<?php
/**
 * Neo Fintech - rate calculator.
 *
 * A large off-white card with a two pixel ink border and a solid offset
 * shadow, sized to straddle the bottom edge of the hero's orange field.
 *
 * Every value on screen is rendered by PHP first, from the same cached feed
 * the script is handed, so the card quotes a real price before JavaScript runs
 * and keeps quoting one if JavaScript never arrives.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_feed = cfkl_get_rates();

if ( empty( $neo_feed['coins'] ) ) {
	return;
}

$neo_catalogue  = cfkl_rate_coins();
$neo_currencies = cfkl_rate_currencies();

/* Only offer coins the feed actually priced this cycle. */
$neo_options = array_intersect_key( $neo_catalogue, $neo_feed['coins'] );

if ( empty( $neo_options ) ) {
	return;
}

$neo_coin     = (string) cfkl_get( 'calc_default_coin', 'BTC' );
$neo_currency = (string) cfkl_get( 'calc_default_currency', 'usd' );

if ( ! isset( $neo_options[ $neo_coin ] ) ) {
	$neo_coin = (string) key( $neo_options );
}

if ( ! isset( $neo_currencies[ $neo_currency ] ) ) {
	$neo_currency = 'usd';
}

/* The Coinbase fallback carries no euro or lira leg; quote dollars instead. */
if ( empty( $neo_feed['coins'][ $neo_coin ][ $neo_currency ] ) ) {
	$neo_currency = 'usd';
}

$neo_market = isset( $neo_feed['coins'][ $neo_coin ][ $neo_currency ] ) ? (float) $neo_feed['coins'][ $neo_coin ][ $neo_currency ] : 0.0;

if ( $neo_market <= 0 ) {
	return;
}

$neo_spread = (float) cfkl_get( 'calc_spread_buy', 1.5 );
$neo_rate   = $neo_market * ( 1 + $neo_spread / 100 );
$neo_dp     = isset( $neo_catalogue[ $neo_coin ]['dp'] ) ? (int) $neo_catalogue[ $neo_coin ]['dp'] : 6;
$neo_label  = isset( $neo_catalogue[ $neo_coin ]['label'] ) ? (string) $neo_catalogue[ $neo_coin ]['label'] : $neo_coin;
$neo_symbol = isset( $neo_currencies[ $neo_currency ]['symbol'] ) ? (string) $neo_currencies[ $neo_currency ]['symbol'] : '';
$neo_money  = isset( $neo_currencies[ $neo_currency ]['label'] ) ? (string) $neo_currencies[ $neo_currency ]['label'] : strtoupper( $neo_currency );

/*
 * A thousand lira is pocket change at this desk, so the starting amount and the
 * preset buttons are scaled when the page quotes in TRY.
 */
$neo_step   = 'try' === $neo_currency ? 25000 : 1000;
$neo_fiat   = $neo_step;
$neo_crypto = $neo_fiat / $neo_rate;
$neo_change = isset( $neo_feed['coins'][ $neo_coin ]['change'] ) ? $neo_feed['coins'][ $neo_coin ]['change'] : null;
$neo_state  = empty( $neo_feed['stale'] ) ? 'live' : 'stale';

$neo_title   = (string) cfkl_get( 'calc_title' );
$neo_text    = (string) cfkl_get( 'calc_text' );
$neo_note    = (string) cfkl_get( 'calc_note' );
$neo_cta     = (string) cfkl_get( 'calc_cta_label' );
$neo_wa      = preg_replace( '/\D+/', '', (string) cfkl_get( 'calc_whatsapp', '' ) );
$neo_cta_url = (string) cfkl_get( 'banner_cta_url', cfkl_get( 'cta_url', '' ) );

/*
 * One template, used twice: PHP fills it for the starting quote so the button
 * already carries a real message, and the script reuses it through
 * data-calc-message as the visitor changes the amount.
 */
$neo_template = __( 'Hi Coinsfera, I would like to {mode} {crypto} {coin} for about {fiat} {currency}. Is the rate available today?', 'coinsfera' );

if ( '' !== $neo_wa ) {
	$neo_cta_url = 'https://wa.me/' . $neo_wa . '?text=' . rawurlencode(
		str_replace(
			array( '{mode}', '{crypto}', '{coin}', '{fiat}', '{currency}' ),
			array( 'buy', number_format( $neo_crypto, $neo_dp, '.', '' ), $neo_coin, number_format( $neo_fiat, 2, '.', '' ), $neo_money ),
			$neo_template
		)
	);
}

$neo_quick = array( $neo_step, $neo_step * 5, $neo_step * 25 );
$neo_id    = 'cfkl-neo-calc';
?>
<form class="cfkl-neo-calc"
	data-cfkl-calc
	data-calc-default-coin="<?php echo esc_attr( $neo_coin ); ?>"
	data-calc-default-currency="<?php echo esc_attr( $neo_currency ); ?>"
	data-calc-default-mode="buy"
	data-calc-state="<?php echo esc_attr( $neo_state ); ?>"
	data-calc-active-mode="buy"
	<?php if ( '' !== $neo_title ) : ?>
		aria-labelledby="<?php echo esc_attr( $neo_id . '-title' ); ?>"
	<?php endif; ?>>

	<div class="cfkl-neo-calc__head">

		<div class="cfkl-neo-calc__intro">
			<?php if ( '' !== $neo_title ) : ?>
				<h2 class="cfkl-neo-calc__title" id="<?php echo esc_attr( $neo_id . '-title' ); ?>"><?php echo esc_html( $neo_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $neo_text ) : ?>
				<p class="cfkl-neo-calc__text"><?php echo esc_html( $neo_text ); ?></p>
			<?php endif; ?>
		</div>

		<p class="cfkl-neo-calc__state">
			<span class="cfkl-neo-calc__dot" aria-hidden="true"></span>
			<span data-calc-out="status"><?php echo esc_html( 'live' === $neo_state ? __( 'Live rate', 'coinsfera' ) : __( 'Last known rate', 'coinsfera' ) ); ?></span>
			<span class="cfkl-neo-calc__age" data-calc-out="updated"><?php esc_html_e( 'updated just now', 'coinsfera' ); ?></span>
		</p>

	</div>

	<div class="cfkl-neo-calc__modes" role="group" aria-label="<?php esc_attr_e( 'Choose buy or sell', 'coinsfera' ); ?>">
		<button type="button" class="cfkl-neo-mode is-active" data-calc-mode="buy" aria-pressed="true"><?php esc_html_e( 'I am buying', 'coinsfera' ); ?></button>
		<button type="button" class="cfkl-neo-mode" data-calc-mode="sell" aria-pressed="false"><?php esc_html_e( 'I am selling', 'coinsfera' ); ?></button>
	</div>

	<div class="cfkl-neo-calc__grid">

		<div class="cfkl-neo-calc__field">
			<label class="cfkl-neo-calc__label" for="<?php echo esc_attr( $neo_id . '-fiat' ); ?>">
				<span data-calc-out="direction"><?php esc_html_e( 'You pay', 'coinsfera' ); ?></span>
			</label>
			<div class="cfkl-neo-calc__control">
				<input class="cfkl-neo-calc__input"
					id="<?php echo esc_attr( $neo_id . '-fiat' ); ?>"
					type="number"
					inputmode="decimal"
					min="0"
					step="any"
					value="<?php echo esc_attr( (string) $neo_fiat ); ?>"
					data-calc-fiat>
				<select class="cfkl-neo-calc__select"
					id="<?php echo esc_attr( $neo_id . '-currency' ); ?>"
					aria-label="<?php esc_attr_e( 'Currency', 'coinsfera' ); ?>"
					data-calc-currency>
					<?php foreach ( $neo_currencies as $neo_code => $neo_row ) : ?>
						<option value="<?php echo esc_attr( $neo_code ); ?>"<?php echo $neo_code === $neo_currency ? ' selected' : ''; ?>>
							<?php echo esc_html( $neo_row['label'] ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

		<span class="cfkl-neo-calc__swap" aria-hidden="true">
			<?php echo cfkl_icon( 'swap', 'cfkl-neo-calc__swap-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</span>

		<div class="cfkl-neo-calc__field">
			<label class="cfkl-neo-calc__label" for="<?php echo esc_attr( $neo_id . '-crypto' ); ?>">
				<?php esc_html_e( 'Crypto amount', 'coinsfera' ); ?>
			</label>
			<div class="cfkl-neo-calc__control">
				<input class="cfkl-neo-calc__input"
					id="<?php echo esc_attr( $neo_id . '-crypto' ); ?>"
					type="number"
					inputmode="decimal"
					min="0"
					step="any"
					value="<?php echo esc_attr( number_format( $neo_crypto, $neo_dp, '.', '' ) ); ?>"
					data-calc-crypto>
				<select class="cfkl-neo-calc__select"
					id="<?php echo esc_attr( $neo_id . '-coin' ); ?>"
					aria-label="<?php esc_attr_e( 'Coin', 'coinsfera' ); ?>"
					data-calc-coin>
					<?php foreach ( $neo_options as $neo_code => $neo_row ) : ?>
						<option value="<?php echo esc_attr( $neo_code ); ?>"<?php echo $neo_code === $neo_coin ? ' selected' : ''; ?>>
							<?php echo esc_html( $neo_code ); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>

	</div>

	<div class="cfkl-neo-calc__quick" role="group" aria-label="<?php esc_attr_e( 'Common amounts', 'coinsfera' ); ?>">
		<?php foreach ( $neo_quick as $neo_amount ) : ?>
			<button type="button" class="cfkl-neo-quick" data-calc-quick data-amount="<?php echo esc_attr( (string) $neo_amount ); ?>">
				<?php echo esc_html( cfkl_money( $neo_amount, $neo_currency ) ); ?>
			</button>
		<?php endforeach; ?>
	</div>

	<div class="cfkl-neo-calc__readout">

		<p class="cfkl-neo-calc__unit">
			<span data-calc-out="unit">
				<?php
				printf(
					/* translators: 1: coin ticker, 2: formatted price of one coin. */
					esc_html__( '1 %1$s = %2$s', 'coinsfera' ),
					esc_html( $neo_coin ),
					esc_html( cfkl_money( $neo_rate, $neo_currency ) )
				);
				?>
			</span>
			<span class="cfkl-neo-calc__change"
				data-calc-out="change"
				<?php if ( null === $neo_change ) : ?>
					hidden
				<?php else : ?>
					data-trend="<?php echo esc_attr( $neo_change >= 0 ? 'up' : 'down' ); ?>"
				<?php endif; ?>>
				<?php
				if ( null !== $neo_change ) {
					echo esc_html( ( $neo_change >= 0 ? '+' : '' ) . number_format_i18n( (float) $neo_change, 2 ) . '%' );
				}
				?>
			</span>
		</p>

		<p class="cfkl-neo-calc__total">
			<span class="cfkl-neo-calc__total-label"><?php esc_html_e( 'Total', 'coinsfera' ); ?></span>
			<span class="cfkl-neo-calc__total-value" data-calc-out="total"><?php echo esc_html( cfkl_money( $neo_fiat, $neo_currency ) ); ?></span>
			<span class="cfkl-neo-calc__spread">
				<?php
				printf(
					/* translators: %s: the desk's margin, already a percentage. */
					esc_html__( 'margin %s', 'coinsfera' ),
					'<span data-calc-out="spread">' . esc_html( number_format_i18n( $neo_spread, 1 ) . '%' ) . '</span>'
				);
				?>
			</span>
		</p>

	</div>

	<?php if ( '' !== $neo_cta && '' !== $neo_cta_url ) : ?>
		<a class="cfkl-neo-btn cfkl-neo-btn--orange cfkl-neo-btn--block"
			href="<?php echo esc_url( $neo_cta_url ); ?>"
			data-calc-cta
			data-calc-message="<?php echo esc_attr( $neo_template ); ?>">
			<span><?php echo esc_html( $neo_cta ); ?></span>
			<?php echo cfkl_icon( 'phone', 'cfkl-neo-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
		</a>
	<?php endif; ?>

	<?php if ( '' !== $neo_note ) : ?>
		<p class="cfkl-neo-calc__note"><?php echo esc_html( $neo_note ); ?></p>
	<?php endif; ?>

</form>
