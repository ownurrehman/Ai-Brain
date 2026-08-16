<?php
/**
 * DESK - hero.
 *
 * Three things stacked: a full-bleed rate board across the top of the page,
 * the headline and its proof points, and the calculator dressed as a physical
 * dealing ticket.
 *
 * Board and ticket sit inside one [data-cfkl-calc] root on purpose. The
 * calculator script writes each output key into the first element that carries
 * it, so the "live / updated 12s ago" pair belongs to the board and the ticket
 * keeps the keys that describe the quote itself. Every figure below is also
 * rendered server-side, so the ticket reads correctly before - and without -
 * JavaScript.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_heading = (string) cfkl_get( 'banner_heading' );
$cfkl_board   = cfkl_rate_board();

if ( '' === $cfkl_heading && empty( $cfkl_board ) ) {
	return;
}

$cfkl_tagline   = (string) cfkl_get( 'banner_tagline' );
$cfkl_subtext   = (string) cfkl_get( 'banner_subtext' );
$cfkl_cta_label = (string) cfkl_get( 'banner_cta_label' );
$cfkl_cta_url   = (string) cfkl_get( 'banner_cta_url' );
$cfkl_stats     = cfkl_rows( 'banner_stats' );
$cfkl_office    = cfkl_office();
$cfkl_photo     = cfkl_get( 'hero_image', array() );

/*
 * Feed state. The board's status pair is server-rendered from the same
 * transient the localised payload was built from, so the two agree on first
 * paint and JavaScript only has to keep the age counter honest.
 */
$cfkl_rates   = cfkl_get_rates();
$cfkl_stale   = ! empty( $cfkl_rates['stale'] );
$cfkl_updated = isset( $cfkl_rates['updated'] ) ? (int) $cfkl_rates['updated'] : 0;
$cfkl_age     = $cfkl_updated ? max( 0, time() - $cfkl_updated ) : 0;

if ( ! $cfkl_updated ) {
	$cfkl_age_text = '';
} elseif ( $cfkl_age < 10 ) {
	$cfkl_age_text = __( 'updated just now', 'coinsfera' );
} elseif ( $cfkl_age < 90 ) {
	/* translators: %d: age of the quote in seconds. */
	$cfkl_age_text = sprintf( __( 'updated %ds ago', 'coinsfera' ), $cfkl_age );
} else {
	/* translators: %d: age of the quote in minutes. */
	$cfkl_age_text = sprintf( __( 'updated %dm ago', 'coinsfera' ), (int) round( $cfkl_age / 60 ) );
}

/* Ticket state. */
$cfkl_coins      = cfkl_rate_coins();
$cfkl_currencies = cfkl_rate_currencies();

$cfkl_coin = (string) cfkl_get( 'calc_default_coin', 'BTC' );

if ( ! isset( $cfkl_coins[ $cfkl_coin ] ) ) {
	$cfkl_coin = (string) key( $cfkl_coins );
}

$cfkl_currency = (string) cfkl_get( 'calc_default_currency', 'usd' );

if ( ! isset( $cfkl_currencies[ $cfkl_currency ] ) ) {
	$cfkl_currency = 'usd';
}

$cfkl_spread = (float) cfkl_get( 'calc_spread_buy', 1.5 );
$cfkl_market = isset( $cfkl_rates['coins'][ $cfkl_coin ][ $cfkl_currency ] )
	? (float) $cfkl_rates['coins'][ $cfkl_coin ][ $cfkl_currency ]
	: 0.0;
$cfkl_unit   = $cfkl_market * ( 1 + $cfkl_spread / 100 );

/* Lira amounts are two orders of magnitude larger, so the ticket opens on a
   figure a walk-in customer would actually bring. */
$cfkl_opening = 'try' === $cfkl_currency ? 250000 : 10000;
$cfkl_qty     = $cfkl_unit > 0 ? $cfkl_opening / $cfkl_unit : 0;
$cfkl_dp      = isset( $cfkl_coins[ $cfkl_coin ]['dp'] ) ? (int) $cfkl_coins[ $cfkl_coin ]['dp'] : 4;
$cfkl_dash    = '—';

$cfkl_whatsapp = preg_replace( '/\D+/', '', (string) cfkl_get( 'calc_whatsapp' ) );
$cfkl_quote    = $cfkl_whatsapp ? 'https://wa.me/' . $cfkl_whatsapp : $cfkl_cta_url;
$cfkl_quote_cta = (string) cfkl_get( 'calc_cta_label', __( 'Ask the desk for this quote', 'coinsfera' ) );
$cfkl_note     = (string) cfkl_get( 'calc_note' );

/* A dealing reference: stable for the page and the day, like a real ticket. */
$cfkl_ref = 'CF-' . gmdate( 'ymd' ) . '-' . strtoupper( substr( md5( (string) get_the_ID() ), 0, 4 ) );
?>
<section class="cfkl-desk-hero">
	<div class="cfkl-desk-quote"
		data-cfkl-calc
		data-calc-default-coin="<?php echo esc_attr( $cfkl_coin ); ?>"
		data-calc-default-currency="<?php echo esc_attr( $cfkl_currency ); ?>"
		data-calc-default-mode="buy"
		data-calc-active-mode="buy"
		data-calc-state="<?php echo $cfkl_stale ? 'stale' : 'live'; ?>">

		<?php if ( ! empty( $cfkl_board ) ) : ?>
			<div class="cfkl-desk-board" role="region" tabindex="0" aria-label="<?php esc_attr_e( 'Reference rates board', 'coinsfera' ); ?>">
				<div class="cfkl-desk-board__track">
					<?php foreach ( $cfkl_board as $cfkl_row ) : ?>
						<?php
						$cfkl_row_rate   = isset( $cfkl_row[ $cfkl_currency ] ) ? (float) $cfkl_row[ $cfkl_currency ] : 0.0;
						$cfkl_row_change = isset( $cfkl_row['change'] ) ? $cfkl_row['change'] : null;
						?>
						<div class="cfkl-desk-board__cell">
							<span class="cfkl-desk-board__ticker"><?php echo esc_html( $cfkl_row['symbol'] ); ?></span>
							<span class="cfkl-desk-board__price"><?php echo esc_html( $cfkl_row_rate > 0 ? cfkl_money( $cfkl_row_rate, $cfkl_currency ) : $cfkl_dash ); ?></span>
							<?php if ( null !== $cfkl_row_change ) : ?>
								<span class="cfkl-desk-board__change" data-trend="<?php echo $cfkl_row_change >= 0 ? 'up' : 'down'; ?>">
									<?php echo esc_html( ( $cfkl_row_change >= 0 ? '+' : '' ) . number_format_i18n( (float) $cfkl_row_change, 2 ) . '%' ); ?>
								</span>
							<?php endif; ?>
						</div>
					<?php endforeach; ?>

					<div class="cfkl-desk-board__cell cfkl-desk-board__cell--state">
						<span class="cfkl-desk-board__dot" aria-hidden="true"></span>
						<span class="cfkl-desk-board__status" data-calc-out="status"><?php echo esc_html( $cfkl_stale ? __( 'Last known rate', 'coinsfera' ) : __( 'Live rate', 'coinsfera' ) ); ?></span>
						<span class="cfkl-desk-board__age" data-calc-out="updated"><?php echo esc_html( $cfkl_age_text ); ?></span>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<div class="cfkl-container">
			<div class="cfkl-desk-hero__grid">

				<div class="cfkl-desk-hero__lead">
					<?php if ( '' !== $cfkl_tagline ) : ?>
						<p class="cfkl-desk-hero__eyebrow"><?php echo esc_html( $cfkl_tagline ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $cfkl_heading ) : ?>
						<h1 class="cfkl-desk-hero__title"><?php echo esc_html( $cfkl_heading ); ?></h1>
					<?php endif; ?>

					<?php if ( '' !== $cfkl_subtext ) : ?>
						<p class="cfkl-desk-hero__text"><?php echo esc_html( $cfkl_subtext ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $cfkl_cta_label && '' !== $cfkl_cta_url ) : ?>
						<p class="cfkl-desk-hero__actions">
							<a class="cfkl-desk-link" href="<?php echo esc_url( $cfkl_cta_url ); ?>">
								<?php echo esc_html( $cfkl_cta_label ); ?>
								<?php echo cfkl_icon( 'arrow', 'cfkl-desk-link__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							</a>
						</p>
					<?php endif; ?>

					<?php if ( ! empty( $cfkl_stats ) ) : ?>
						<dl class="cfkl-desk-hero__stats">
							<?php foreach ( $cfkl_stats as $cfkl_stat ) : ?>
								<?php if ( empty( $cfkl_stat['value'] ) ) { continue; } ?>
								<div class="cfkl-desk-hero__stat">
									<dt class="cfkl-desk-hero__stat-label"><?php echo esc_html( isset( $cfkl_stat['label'] ) ? $cfkl_stat['label'] : '' ); ?></dt>
									<dd class="cfkl-desk-hero__stat-value"><?php echo esc_html( $cfkl_stat['value'] ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					<?php endif; ?>

					<?php if ( '' !== $cfkl_office['address'] ) : ?>
						<div class="cfkl-desk-hero__desk">
							<span class="cfkl-desk-hero__desk-label">
								<?php echo cfkl_icon( 'pin', 'cfkl-desk-hero__desk-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								<?php echo esc_html( '' !== $cfkl_office['label'] ? $cfkl_office['label'] : __( 'Counter', 'coinsfera' ) ); ?>
							</span>
							<p class="cfkl-desk-hero__desk-address"><?php echo esc_html( $cfkl_office['address'] ); ?></p>
							<?php if ( '' !== $cfkl_office['url'] && '' !== $cfkl_office['cta'] ) : ?>
								<a class="cfkl-desk-hero__desk-link" href="<?php echo esc_url( $cfkl_office['url'] ); ?>" rel="noopener" target="_blank">
									<?php echo esc_html( $cfkl_office['cta'] ); ?>
									<?php if ( '' !== $cfkl_office['rating'] ) : ?>
										<span class="cfkl-desk-hero__desk-rating"><?php echo esc_html( $cfkl_office['rating'] ); ?></span>
									<?php endif; ?>
								</a>
							<?php endif; ?>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $cfkl_photo['ID'] ) ) : ?>
						<figure class="cfkl-desk-hero__plate">
							<?php cfkl_hero_image( 'cfkl-desk-hero__img' ); ?>
							<figcaption class="cfkl-desk-hero__caption"><?php esc_html_e( 'The counter, Istanbul', 'coinsfera' ); ?></figcaption>
						</figure>
					<?php endif; ?>
				</div>

				<div class="cfkl-desk-ticket" role="group" aria-labelledby="cfkl-desk-ticket-title">

					<div class="cfkl-desk-ticket__head">
						<span class="cfkl-desk-ticket__kind" id="cfkl-desk-ticket-title"><?php esc_html_e( 'Order ticket', 'coinsfera' ); ?></span>
						<span class="cfkl-desk-ticket__ref"><?php echo esc_html( $cfkl_ref ); ?></span>
					</div>

					<div class="cfkl-desk-ticket__tabs" role="group" aria-label="<?php esc_attr_e( 'Direction of trade', 'coinsfera' ); ?>">
						<button type="button" class="cfkl-desk-ticket__tab is-active" data-calc-mode="buy" aria-pressed="true"><?php esc_html_e( 'Buy', 'coinsfera' ); ?></button>
						<button type="button" class="cfkl-desk-ticket__tab" data-calc-mode="sell" aria-pressed="false"><?php esc_html_e( 'Sell', 'coinsfera' ); ?></button>
					</div>

					<div class="cfkl-desk-ticket__rows">

						<div class="cfkl-desk-ticket__row">
							<label class="cfkl-desk-ticket__label" for="cfkl-desk-asset"><?php esc_html_e( 'Asset', 'coinsfera' ); ?></label>
							<span class="cfkl-desk-ticket__control">
								<select class="cfkl-desk-ticket__select" id="cfkl-desk-asset" data-calc-coin>
									<?php foreach ( $cfkl_coins as $cfkl_symbol => $cfkl_meta ) : ?>
										<option value="<?php echo esc_attr( $cfkl_symbol ); ?>" <?php selected( $cfkl_symbol, $cfkl_coin ); ?>>
											<?php echo esc_html( $cfkl_symbol . ' · ' . $cfkl_meta['label'] ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</span>
						</div>

						<div class="cfkl-desk-ticket__row">
							<label class="cfkl-desk-ticket__label" for="cfkl-desk-settlement"><?php esc_html_e( 'Settlement', 'coinsfera' ); ?></label>
							<span class="cfkl-desk-ticket__control">
								<select class="cfkl-desk-ticket__select" id="cfkl-desk-settlement" data-calc-currency>
									<?php foreach ( $cfkl_currencies as $cfkl_code => $cfkl_money_meta ) : ?>
										<option value="<?php echo esc_attr( $cfkl_code ); ?>" <?php selected( $cfkl_code, $cfkl_currency ); ?>>
											<?php echo esc_html( $cfkl_money_meta['label'] ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</span>
						</div>

						<div class="cfkl-desk-ticket__row">
							<label class="cfkl-desk-ticket__label" for="cfkl-desk-fiat"><?php esc_html_e( 'Cash amount', 'coinsfera' ); ?></label>
							<span class="cfkl-desk-ticket__control">
								<input class="cfkl-desk-ticket__input" id="cfkl-desk-fiat" type="number" inputmode="decimal" min="0" step="any" value="<?php echo esc_attr( $cfkl_opening ); ?>" data-calc-fiat>
								<span class="cfkl-desk-ticket__unit" data-calc-out="currency"><?php echo esc_html( $cfkl_currencies[ $cfkl_currency ]['label'] ); ?></span>
							</span>
						</div>

						<div class="cfkl-desk-ticket__row">
							<label class="cfkl-desk-ticket__label" for="cfkl-desk-crypto"><?php esc_html_e( 'Quantity', 'coinsfera' ); ?></label>
							<span class="cfkl-desk-ticket__control">
								<input class="cfkl-desk-ticket__input" id="cfkl-desk-crypto" type="number" inputmode="decimal" min="0" step="any" value="<?php echo esc_attr( $cfkl_qty > 0 ? number_format( $cfkl_qty, $cfkl_dp, '.', '' ) : '' ); ?>" data-calc-crypto>
								<span class="cfkl-desk-ticket__unit" data-calc-out="coin"><?php echo esc_html( $cfkl_coin ); ?></span>
							</span>
						</div>
					</div>

					<div class="cfkl-desk-ticket__perf" aria-hidden="true"></div>

					<div class="cfkl-desk-ticket__summary">
						<p class="cfkl-desk-ticket__direction" data-calc-out="direction"><?php esc_html_e( 'You pay', 'coinsfera' ); ?></p>
						<p class="cfkl-desk-ticket__total" data-calc-out="total"><?php echo esc_html( $cfkl_unit > 0 ? cfkl_money( $cfkl_opening, $cfkl_currency ) : $cfkl_dash ); ?></p>

						<dl class="cfkl-desk-ticket__terms">
							<div class="cfkl-desk-ticket__term">
								<dt><?php esc_html_e( 'Quantity', 'coinsfera' ); ?></dt>
								<dd>
									<span data-calc-out="crypto"><?php echo esc_html( $cfkl_qty > 0 ? number_format_i18n( $cfkl_qty, $cfkl_dp ) : $cfkl_dash ); ?></span>
									<span data-calc-out="coin-label"><?php echo esc_html( $cfkl_coins[ $cfkl_coin ]['label'] ); ?></span>
								</dd>
							</div>
							<div class="cfkl-desk-ticket__term">
								<dt><?php esc_html_e( 'Unit rate', 'coinsfera' ); ?></dt>
								<dd data-calc-out="unit">
									<?php
									echo esc_html(
										$cfkl_unit > 0
											/* translators: 1: coin ticker, 2: formatted unit price. */
											? sprintf( __( '1 %1$s = %2$s', 'coinsfera' ), $cfkl_coin, cfkl_money( $cfkl_unit, $cfkl_currency ) )
											: $cfkl_dash
									);
									?>
								</dd>
							</div>
							<div class="cfkl-desk-ticket__term">
								<dt><?php esc_html_e( 'Desk spread', 'coinsfera' ); ?></dt>
								<dd data-calc-out="spread"><?php echo esc_html( number_format_i18n( $cfkl_spread, 1 ) . '%' ); ?></dd>
							</div>
						</dl>

						<?php if ( '' !== $cfkl_quote && '' !== $cfkl_quote_cta ) : ?>
							<a class="cfkl-desk-ticket__cta" href="<?php echo esc_url( $cfkl_quote ); ?>" data-calc-cta
								data-calc-message="<?php esc_attr_e( 'Hi Coinsfera, I would like to {mode} {crypto} {coin} for about {fiat} {currency}. Is the rate available today?', 'coinsfera' ); ?>">
								<?php echo esc_html( $cfkl_quote_cta ); ?>
							</a>
						<?php endif; ?>

						<?php if ( '' !== $cfkl_note ) : ?>
							<p class="cfkl-desk-ticket__note"><?php echo esc_html( $cfkl_note ); ?></p>
						<?php endif; ?>
					</div>
				</div>

			</div>
		</div>
	</div>
</section>
