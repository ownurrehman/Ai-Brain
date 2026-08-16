<?php
/**
 * Live crypto rate feed for the Keyword Landing calculator.
 *
 * The site's older `getSingleCoinPrice` / `getCurrencyData` actions used to
 * proxy CryptoCompare on every keystroke with a free key, which is why they
 * answered "over your rate limit". This feed makes one upstream call per TTL
 * for every coin and currency at once, keeps a week-old copy so a failed
 * upstream shows a slightly stale price instead of a broken calculator, and
 * serves those two legacy admin-ajax actions from the cache so the homepage
 * ticker and the coin-page calculators stop calling CryptoCompare.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CFKL_RATES_TRANSIENT = 'cfkl_rates_v2';
const CFKL_RATES_BACKUP    = 'cfkl_rates_backup_v2';
const CFKL_RATES_LOCK      = 'cfkl_rates_lock_v2';
const CFKL_RATES_TTL       = 180;
const CFKL_RATES_TIMEOUT   = 6;

/**
 * Coins the calculator can quote.
 *
 * `gecko` is the CoinGecko id; `dp` is how many decimals the crypto amount
 * shows, which differs by an order of magnitude between BTC and XRP.
 *
 * @return array<string, array{label: string, gecko: string, dp: int}>
 */
function cfkl_rate_coins() {

	$coins = array(
		'BTC'  => array( 'label' => 'Bitcoin',  'gecko' => 'bitcoin',     'dp' => 6 ),
		'USDT' => array( 'label' => 'Tether',   'gecko' => 'tether',      'dp' => 2 ),
		'ETH'  => array( 'label' => 'Ethereum', 'gecko' => 'ethereum',    'dp' => 5 ),
		'USDC' => array( 'label' => 'USD Coin', 'gecko' => 'usd-coin',    'dp' => 2 ),
		'XRP'  => array( 'label' => 'XRP',      'gecko' => 'ripple',      'dp' => 2 ),
		'SOL'  => array( 'label' => 'Solana',   'gecko' => 'solana',      'dp' => 4 ),
		'BNB'  => array( 'label' => 'BNB',      'gecko' => 'binancecoin', 'dp' => 4 ),
		'TRX'  => array( 'label' => 'Tron',     'gecko' => 'tron',        'dp' => 2 ),
	);

	/**
	 * Filter the coins offered by the landing page calculator.
	 *
	 * @param array $coins Coin definitions keyed by ticker.
	 */
	return apply_filters( 'cfkl_rate_coins', $coins );
}

/**
 * Every ticker this feed will fetch.
 *
 * The landing-page calculator still offers `cfkl_rate_coins()` only. The extra
 * rows exist so the homepage widget (BCH) and the older buy/sell calculators
 * (LTC and friends) can read the same cache.
 *
 * @return array<string, array{label: string, gecko: string, dp: int}>
 */
function cfkl_feed_coins() {

	$extra = array(
		'BCH'  => array( 'label' => 'Bitcoin Cash', 'gecko' => 'bitcoin-cash',        'dp' => 4 ),
		'LTC'  => array( 'label' => 'Litecoin',     'gecko' => 'litecoin',            'dp' => 4 ),
		'ADA'  => array( 'label' => 'Cardano',      'gecko' => 'cardano',             'dp' => 2 ),
		'DOGE' => array( 'label' => 'Dogecoin',     'gecko' => 'dogecoin',            'dp' => 2 ),
		'AVAX' => array( 'label' => 'Avalanche',    'gecko' => 'avalanche-2',         'dp' => 4 ),
		'DOT'  => array( 'label' => 'Polkadot',     'gecko' => 'polkadot',            'dp' => 4 ),
		'LINK' => array( 'label' => 'Chainlink',    'gecko' => 'chainlink',           'dp' => 4 ),
		'ATOM' => array( 'label' => 'Cosmos',       'gecko' => 'cosmos',              'dp' => 4 ),
		'XLM'  => array( 'label' => 'Stellar',      'gecko' => 'stellar',             'dp' => 2 ),
		'TON'  => array( 'label' => 'Toncoin',      'gecko' => 'the-open-network',    'dp' => 4 ),
	);

	$coins = array_merge( cfkl_rate_coins(), $extra );

	/**
	 * Filter the coins fetched into the shared rate cache.
	 *
	 * @param array $coins Coin definitions keyed by ticker.
	 */
	return apply_filters( 'cfkl_feed_coins', $coins );
}

/**
 * Fiat currencies the calculator can quote.
 *
 * @return array<string, array{label: string, symbol: string, locale: string}>
 */
function cfkl_rate_currencies() {

	return array(
		'usd' => array( 'label' => 'USD', 'symbol' => '$', 'locale' => 'en-US' ),
		'eur' => array( 'label' => 'EUR', 'symbol' => '€', 'locale' => 'de-DE' ),
		'try' => array( 'label' => 'TRY', 'symbol' => '₺', 'locale' => 'tr-TR' ),
	);
}

/**
 * Current rates, read from cache.
 *
 * This never calls an upstream API, because it runs while a page is being
 * rendered. CoinGecko rate-limits this server's shared IP from time to time,
 * and a visitor must never wait on that. Refreshing is the job of cron and of
 * the REST route, both of which are off the critical path.
 *
 * @return array{updated: int, source: string, stale: bool, coins: array}
 */
function cfkl_get_rates() {

	$cached = get_transient( CFKL_RATES_TRANSIENT );

	if ( is_array( $cached ) && ! empty( $cached['coins'] ) ) {
		return $cached;
	}

	$backup = get_transient( CFKL_RATES_BACKUP );

	if ( is_array( $backup ) && ! empty( $backup['coins'] ) ) {
		$backup['stale'] = true;

		/*
		 * Hand the refresh to cron so this request returns immediately. WP
		 * spawns cron in a non-blocking loopback at shutdown, so the next
		 * visitor gets fresh numbers without this one paying for them. The
		 * lock keeps concurrent stale reads from queueing the same job over
		 * and over.
		 */
		if ( ! get_transient( CFKL_RATES_LOCK ) ) {
			set_transient( CFKL_RATES_LOCK, 1, 60 );
			wp_schedule_single_event( time(), 'cfkl_refresh_rates_event' );
		}

		return $backup;
	}

	/*
	 * Nothing cached at all, which happens once after deployment or after the
	 * object cache is flushed. Fetch inline just this once, behind a lock so
	 * concurrent requests do not all call upstream together.
	 */
	if ( ! get_transient( CFKL_RATES_LOCK ) ) {
		set_transient( CFKL_RATES_LOCK, 1, 30 );

		$fresh = cfkl_refresh_rates();

		if ( ! empty( $fresh['coins'] ) ) {
			return $fresh;
		}
	}

	return array(
		'updated' => 0,
		'source'  => 'none',
		'stale'   => true,
		'coins'   => array(),
	);
}

/**
 * Fetch from upstream and update the cache.
 *
 * Only cron and the REST route call this. CoinGecko is preferred because it is
 * the only source here that carries a 24h change figure; Coinbase covers the
 * intervals where CoinGecko throttles us.
 *
 * @return array Fresh rates, or an empty array when every source failed.
 */
function cfkl_refresh_rates() {

	$rates = cfkl_fetch_rates_coingecko();

	if ( empty( $rates['coins'] ) ) {
		$rates = cfkl_fetch_rates_coinbase();
	}

	if ( empty( $rates['coins'] ) ) {
		return array();
	}

	set_transient( CFKL_RATES_TRANSIENT, $rates, CFKL_RATES_TTL );
	set_transient( CFKL_RATES_BACKUP, $rates, WEEK_IN_SECONDS );

	return $rates;
}

/**
 * Whether the cached copy is old enough to be worth refreshing.
 *
 * @return bool
 */
function cfkl_rates_need_refresh() {

	$cached = get_transient( CFKL_RATES_TRANSIENT );

	if ( ! is_array( $cached ) || empty( $cached['coins'] ) ) {
		return true;
	}

	return ( time() - (int) $cached['updated'] ) >= CFKL_RATES_TTL;
}

/**
 * Keep the cache warm so no visitor ever waits on the upstream.
 *
 * @return void
 */
function cfkl_schedule_rates() {

	if ( ! wp_next_scheduled( 'cfkl_refresh_rates_event' ) ) {
		wp_schedule_event( time() + 60, 'cfkl_five_minutes', 'cfkl_refresh_rates_event' );
	}
}
add_action( 'init', 'cfkl_schedule_rates' );

/**
 * Add the five minute interval the refresh event runs on.
 *
 * @param array $schedules Cron schedules.
 * @return array
 */
function cfkl_cron_schedule( $schedules ) {

	$schedules['cfkl_five_minutes'] = array(
		'interval' => 300,
		'display'  => __( 'Every five minutes', 'coinsfera' ),
	);

	return $schedules;
}
add_filter( 'cron_schedules', 'cfkl_cron_schedule' ); // phpcs:ignore WordPress.WP.CronInterval.ChangeDetected -- the feed is only useful while fresh.

add_action( 'cfkl_refresh_rates_event', 'cfkl_refresh_rates' );

/**
 * Fetch every coin and currency from CoinGecko in one request.
 *
 * @return array
 */
function cfkl_fetch_rates_coingecko() {

	$coins = cfkl_feed_coins();
	$ids   = wp_list_pluck( $coins, 'gecko' );

	$url = add_query_arg(
		array(
			'ids'                => implode( ',', $ids ),
			'vs_currencies'      => 'usd,eur,try',
			'include_24hr_change' => 'true',
		),
		'https://api.coingecko.com/api/v3/simple/price'
	);

	$body = cfkl_rates_request( $url );

	if ( empty( $body ) ) {
		return array();
	}

	$out = array();

	foreach ( $coins as $symbol => $coin ) {

		$row = isset( $body[ $coin['gecko'] ] ) ? $body[ $coin['gecko'] ] : null;

		if ( empty( $row['usd'] ) ) {
			continue;
		}

		$out[ $symbol ] = array(
			'usd'    => cfkl_round_rate( $row['usd'] ),
			'eur'    => isset( $row['eur'] ) ? cfkl_round_rate( $row['eur'] ) : 0.0,
			'try'    => isset( $row['try'] ) ? cfkl_round_rate( $row['try'] ) : 0.0,
			'change' => isset( $row['usd_24h_change'] ) ? round( (float) $row['usd_24h_change'], 2 ) : 0.0,
		);
	}

	if ( empty( $out ) ) {
		return array();
	}

	return array(
		'updated' => time(),
		'source'  => 'coingecko',
		'stale'   => false,
		'coins'   => $out,
	);
}

/**
 * Fallback feed: Coinbase returns every rate against USD in one request.
 *
 * Coinbase quotes how much of each currency one USD buys, so a coin price is
 * the reciprocal. It carries no 24h change, which the calculator treats as
 * simply not showing a change figure.
 *
 * @return array
 */
function cfkl_fetch_rates_coinbase() {

	$body = cfkl_rates_request( 'https://api.coinbase.com/v2/exchange-rates?currency=USD' );

	if ( empty( $body['data']['rates'] ) ) {
		return array();
	}

	$rates = $body['data']['rates'];
	$eur   = isset( $rates['EUR'] ) ? (float) $rates['EUR'] : 0.0;
	$try   = isset( $rates['TRY'] ) ? (float) $rates['TRY'] : 0.0;
	$out   = array();

	foreach ( array_keys( cfkl_feed_coins() ) as $symbol ) {

		$per_usd = isset( $rates[ $symbol ] ) ? (float) $rates[ $symbol ] : 0.0;

		if ( $per_usd <= 0 ) {
			continue;
		}

		$usd = 1 / $per_usd;

		$out[ $symbol ] = array(
			'usd'    => cfkl_round_rate( $usd ),
			'eur'    => $eur > 0 ? cfkl_round_rate( $usd * $eur ) : 0.0,
			'try'    => $try > 0 ? cfkl_round_rate( $usd * $try ) : 0.0,
			'change' => null,
		);
	}

	if ( empty( $out ) ) {
		return array();
	}

	return array(
		'updated' => time(),
		'source'  => 'coinbase',
		'stale'   => false,
		'coins'   => $out,
	);
}

/**
 * Round a rate to a sensible number of decimals for its size.
 *
 * A Bitcoin price needs no decimals; a Tether price needs several. Rounding
 * here also keeps the JSON small, since this host prints floats at full
 * precision and an unrounded rate serialises as seventeen digits.
 *
 * @param mixed $value Raw rate.
 * @return float
 */
function cfkl_round_rate( $value ) {

	$value = (float) $value;

	if ( $value >= 1000 ) {
		return round( $value );
	}

	return round( $value, $value >= 1 ? 4 : 6 );
}

/**
 * GET a JSON endpoint and decode it.
 *
 * @param string $url Endpoint.
 * @return array Decoded body, or an empty array on any failure.
 */
function cfkl_rates_request( $url ) {

	$response = wp_remote_get(
		$url,
		array(
			'timeout'    => CFKL_RATES_TIMEOUT,
			'user-agent' => 'Coinsfera/1.0 (+https://www.coinsfera.com)',
			'headers'    => array( 'Accept' => 'application/json' ),
		)
	);

	if ( is_wp_error( $response ) || 200 !== (int) wp_remote_retrieve_response_code( $response ) ) {
		return array();
	}

	$body = json_decode( wp_remote_retrieve_body( $response ), true );

	return is_array( $body ) ? $body : array();
}

/**
 * Rates plus the presentation data the calculator needs, ready to hand to JS.
 *
 * @param int|false $post_id Landing page to read spread settings from.
 * @return array
 */
function cfkl_calc_payload( $post_id = false ) {

	$rates = cfkl_get_rates();

	$coins = array();

	foreach ( cfkl_rate_coins() as $symbol => $coin ) {

		if ( empty( $rates['coins'][ $symbol ] ) ) {
			continue;
		}

		$coins[ $symbol ] = array_merge(
			$rates['coins'][ $symbol ],
			array(
				'label' => $coin['label'],
				'dp'    => $coin['dp'],
			)
		);
	}

	return array(
		'updated'    => isset( $rates['updated'] ) ? (int) $rates['updated'] : 0,
		'stale'      => ! empty( $rates['stale'] ),
		'source'     => isset( $rates['source'] ) ? $rates['source'] : 'none',
		'coins'      => $coins,
		'currencies' => cfkl_rate_currencies(),
		'spread'     => array(
			'buy'  => (float) cfkl_get( 'calc_spread_buy', 1.5, $post_id ),
			'sell' => (float) cfkl_get( 'calc_spread_sell', 1.5, $post_id ),
		),
		'endpoint'   => rest_url( 'cfkl/v1/rates' ),
		'locale'     => cfkl_calc_locale(),
		'whatsapp'   => preg_replace( '/\D+/', '', (string) cfkl_get( 'calc_whatsapp', '', $post_id ) ),
		'i18n'       => array(
			'live'      => __( 'Live rate', 'coinsfera' ),
			'stale'     => __( 'Last known rate', 'coinsfera' ),
			'justNow'   => __( 'updated just now', 'coinsfera' ),
			'secondsAgo' => __( 'updated %ds ago', 'coinsfera' ),
			'minutesAgo' => __( 'updated %dm ago', 'coinsfera' ),
			'youPay'    => __( 'You pay', 'coinsfera' ),
			'youGet'    => __( 'You receive', 'coinsfera' ),
		),
	);
}

/**
 * Number formatting locale for the active WPML language.
 *
 * @return string
 */
function cfkl_calc_locale() {

	$map = array(
		'tr' => 'tr-TR',
		'ru' => 'ru-RU',
		'en' => 'en-US',
	);

	$lang = defined( 'ICL_LANGUAGE_CODE' ) ? ICL_LANGUAGE_CODE : 'en';

	return isset( $map[ $lang ] ) ? $map[ $lang ] : 'en-US';
}

/**
 * Expose the feed so the calculator can refresh without a page load.
 *
 * @return void
 */
function cfkl_register_rates_route() {

	register_rest_route(
		'cfkl/v1',
		'/rates',
		array(
			'methods'             => 'GET',
			'permission_callback' => '__return_true',
			'callback'            => 'cfkl_rates_route',
		)
	);
}
add_action( 'rest_api_init', 'cfkl_register_rates_route' );

/**
 * REST handler for the rate feed.
 *
 * @return WP_REST_Response
 */
function cfkl_rates_route() {

	/*
	 * This runs after the page has loaded, so it is the right place to absorb
	 * the cost of an upstream call. If it fails, cfkl_get_rates() still returns
	 * the last good copy.
	 */
	if ( cfkl_rates_need_refresh() ) {
		cfkl_refresh_rates();
	}

	$rates = cfkl_get_rates();

	$response = rest_ensure_response(
		array(
			'updated' => isset( $rates['updated'] ) ? (int) $rates['updated'] : 0,
			'stale'   => ! empty( $rates['stale'] ),
			'coins'   => isset( $rates['coins'] ) ? $rates['coins'] : array(),
		)
	);

	// Let any page cache hold this for the same window the transient uses.
	$response->header( 'Cache-Control', 'public, max-age=' . CFKL_RATES_TTL );

	/*
	 * This host sets serialize_precision to 17, so a rate of 0.999003 would be
	 * written out as 0.99900299999999997. Switching to -1 for the encode gives
	 * the shortest representation that still round-trips exactly. The request
	 * ends immediately afterwards, so nothing else is affected.
	 */
	add_filter( 'rest_pre_echo_response', 'cfkl_shorten_float_output' );

	return $response;
}

/**
 * Ask PHP for the shortest accurate float representation before encoding.
 *
 * @param mixed $result Response data, returned untouched.
 * @return mixed
 */
function cfkl_shorten_float_output( $result ) {

	// phpcs:ignore WordPress.PHP.IniSet.Risky -- scoped to this response, see caller.
	@ini_set( 'serialize_precision', '-1' );

	return $result;
}

/**
 * Take over the theme's old CryptoCompare admin-ajax actions.
 *
 * WPCode snippet 27609 still registers the same hooks. Running late on init
 * and wp_loaded strips those callbacks so a visitor never trips the paid
 * quota, then we serve the cached feed in the shapes the existing JS expects.
 *
 * @return void
 */
function cfkl_replace_legacy_price_ajax() {

	foreach ( array( 'wp_ajax_getSingleCoinPrice', 'wp_ajax_nopriv_getSingleCoinPrice', 'wp_ajax_getCurrencyData', 'wp_ajax_nopriv_getCurrencyData' ) as $hook ) {
		remove_all_actions( $hook );
	}

	add_action( 'wp_ajax_getSingleCoinPrice', 'cfkl_ajax_single_coin_price' );
	add_action( 'wp_ajax_nopriv_getSingleCoinPrice', 'cfkl_ajax_single_coin_price' );
	add_action( 'wp_ajax_getCurrencyData', 'cfkl_ajax_currency_data' );
	add_action( 'wp_ajax_nopriv_getCurrencyData', 'cfkl_ajax_currency_data' );
}
add_action( 'init', 'cfkl_replace_legacy_price_ajax', 99 );
add_action( 'wp_loaded', 'cfkl_replace_legacy_price_ajax', 99 );

/**
 * Map a calculator `data-coin` value to a cache ticker.
 *
 * @param string $raw Incoming symbol or name.
 * @return string Uppercase ticker, or empty string.
 */
function cfkl_legacy_ticker( $raw ) {

	$raw = strtoupper( preg_replace( '/[^A-Za-z0-9]/', '', (string) $raw ) );

	$aliases = array(
		'BITCOIN'      => 'BTC',
		'ETHEREUM'     => 'ETH',
		'TETHER'       => 'USDT',
		'USDCOIN'      => 'USDC',
		'RIPPLE'       => 'XRP',
		'SOLANA'       => 'SOL',
		'BINANCECOIN'  => 'BNB',
		'BINANCE'      => 'BNB',
		'TRON'         => 'TRX',
		'BITCOINCASH'  => 'BCH',
		'LITECOIN'     => 'LTC',
		'CARDANO'      => 'ADA',
		'DOGECOIN'     => 'DOGE',
		'AVALANCHE'    => 'AVAX',
		'POLKADOT'     => 'DOT',
		'CHAINLINK'    => 'LINK',
		'COSMOS'       => 'ATOM',
		'STELLAR'      => 'XLM',
		'TONCOIN'      => 'TON',
	);

	if ( isset( $aliases[ $raw ] ) ) {
		return $aliases[ $raw ];
	}

	return $raw;
}

/**
 * Format a USD figure the way the homepage ticker paints it.
 *
 * @param float $usd Price in dollars.
 * @return string
 */
function cfkl_widget_price_label( $usd ) {

	$usd = (float) $usd;

	if ( $usd >= 1000 ) {
		return '$' . number_format( $usd, 0 );
	}

	if ( $usd >= 1 ) {
		return '$' . number_format( $usd, 2 );
	}

	return '$' . number_format( $usd, 4 );
}

/**
 * Legacy calculator: `{ "USD": 63091 }` like CryptoCompare's price endpoint.
 *
 * @return void
 */
function cfkl_ajax_single_coin_price() {

	$symbol = cfkl_legacy_ticker( isset( $_REQUEST['fsym'] ) ? wp_unslash( $_REQUEST['fsym'] ) : '' ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended,WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- public market data, sanitised by cfkl_legacy_ticker.
	$tsyms  = isset( $_REQUEST['tsyms'] ) ? strtoupper( sanitize_text_field( wp_unslash( $_REQUEST['tsyms'] ) ) ) : 'USD'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( '' === $symbol ) {
		wp_send_json( array() );
	}

	$rates = cfkl_get_rates();
	$row   = isset( $rates['coins'][ $symbol ] ) ? $rates['coins'][ $symbol ] : array();
	$out   = array();

	foreach ( array_filter( array_map( 'trim', explode( ',', $tsyms ) ) ) as $fiat ) {

		$key = strtolower( $fiat );

		if ( 'usdt' === $key ) {
			$key = 'usd';
		}

		if ( empty( $row[ $key ] ) ) {
			continue;
		}

		$out[ $fiat ] = $row[ $key ];
	}

	wp_send_json( $out );
}

/**
 * Legacy homepage ticker: `[ { currency, price, change, class }, … ]`.
 *
 * @return void
 */
function cfkl_ajax_currency_data() {

	$requested = array();

	if ( isset( $_REQUEST['cryptocurrency'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- public market data.
		$raw = wp_unslash( $_REQUEST['cryptocurrency'] ); // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- sanitised per item below.

		if ( is_string( $raw ) ) {
			$raw = explode( ',', $raw );
		}

		if ( is_array( $raw ) ) {
			foreach ( $raw as $item ) {
				$ticker = cfkl_legacy_ticker( $item );

				if ( '' !== $ticker ) {
					$requested[] = $ticker;
				}
			}
		}
	}

	$rates = cfkl_get_rates();
	$coins = isset( $rates['coins'] ) && is_array( $rates['coins'] ) ? $rates['coins'] : array();

	if ( empty( $requested ) ) {
		$requested = array_keys( $coins );
	}

	$out = array();

	foreach ( $requested as $symbol ) {

		if ( empty( $coins[ $symbol ]['usd'] ) ) {
			continue;
		}

		$change = isset( $coins[ $symbol ]['change'] ) ? (float) $coins[ $symbol ]['change'] : 0.0;
		$label  = ( $change > 0 ? '+' : '' ) . number_format( $change, 2, '.', '' );

		$out[] = array(
			'currency' => $symbol,
			'price'    => cfkl_widget_price_label( $coins[ $symbol ]['usd'] ),
			'change'   => $label,
			'class'    => $change < 0 ? 'low-rate' : 'high-rate',
		);
	}

	wp_send_json( $out );
}
