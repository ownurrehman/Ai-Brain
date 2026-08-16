<?php
/**
 * Concierge calculator - a quote form, not a widget.
 *
 * Underline-only fields, serif labels and one large serif figure, so it reads
 * like a form filled in across a desk at a private bank. Every figure is
 * rendered server side first, which means the quote says something sensible
 * before the script runs and keeps saying it if the script never runs.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title = (string) cfkl_get( 'calc_title' );
$conc_text  = (string) cfkl_get( 'calc_text' );
$conc_label = (string) cfkl_get( 'calc_cta_label' );
$conc_note  = (string) cfkl_get( 'calc_note' );

if ( '' === $conc_title && '' === $conc_text && '' === $conc_label ) {
	return;
}

$conc_coins      = cfkl_rate_coins();
$conc_currencies = cfkl_rate_currencies();

if ( empty( $conc_coins ) || empty( $conc_currencies ) ) {
	return;
}

$conc_coin = (string) cfkl_get( 'calc_default_coin', 'BTC' );

if ( ! isset( $conc_coins[ $conc_coin ] ) ) {
	$conc_coin = (string) key( $conc_coins );
}

$conc_currency = (string) cfkl_get( 'calc_default_currency', 'usd' );

if ( ! isset( $conc_currencies[ $conc_currency ] ) ) {
	$conc_currency = 'usd';
}

/* The starting quote, worked out the same way the script works it out: market
   rate plus the buy spread, because the form opens on the buy side. */
$conc_rates  = cfkl_get_rates();
$conc_market = isset( $conc_rates['coins'][ $conc_coin ][ $conc_currency ] ) ? (float) $conc_rates['coins'][ $conc_coin ][ $conc_currency ] : 0.0;
$conc_spread = (float) cfkl_get( 'calc_spread_buy', 1.5 );
$conc_rate   = $conc_market > 0 ? $conc_market * ( 1 + $conc_spread / 100 ) : 0.0;
$conc_dp     = isset( $conc_coins[ $conc_coin ]['dp'] ) ? (int) $conc_coins[ $conc_coin ]['dp'] : 2;

/* An opening amount a walk-in customer would recognise. Lira needs a bigger
   one than dollars or euro to describe the same trade. */
$conc_seeds   = array(
	'usd' => 10000,
	'eur' => 10000,
	'try' => 250000,
);
$conc_amount  = isset( $conc_seeds[ $conc_currency ] ) ? $conc_seeds[ $conc_currency ] : 10000;
$conc_holding = $conc_rate > 0 ? $conc_amount / $conc_rate : 0.0;

$conc_number   = trim( (string) cfkl_get( 'calc_whatsapp', '' ) );
$conc_whatsapp = preg_replace( '/\D+/', '', $conc_number );
$conc_cta_url  = '' !== $conc_whatsapp ? 'https://wa.me/' . $conc_whatsapp : (string) cfkl_get( 'cta_url', cfkl_get( 'banner_cta_url', '' ) );

/* The desk itself is the alternative to the form, so it is named next to it. */
$conc_office = cfkl_office();
?>
<section class="cfkl-conc-calc cfkl-reveal">
	<div class="cfkl-container cfkl-conc-calc__inner">

		<div class="cfkl-conc-calc__intro">
			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'Ask for a quote', 'coinsfera' ); ?></p>

			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-calc__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>

			<?php if ( '' !== $conc_text ) : ?>
				<p class="cfkl-conc-lede"><?php echo esc_html( $conc_text ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $conc_number || '' !== $conc_office['address'] ) : ?>
				<div class="cfkl-conc-calc__aside">
					<p class="cfkl-conc-eyebrow cfkl-conc-eyebrow--quiet"><?php esc_html_e( 'Or simply ask at the desk', 'coinsfera' ); ?></p>

					<?php if ( '' !== $conc_office['address'] ) : ?>
						<p class="cfkl-conc-calc__where">
							<?php echo cfkl_icon( 'pin', 'cfkl-conc-calc__pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup. ?>
							<span><?php echo esc_html( $conc_office['address'] ); ?></span>
						</p>
					<?php endif; ?>

					<?php if ( '' !== $conc_number && '' !== $conc_whatsapp ) : ?>
						<p class="cfkl-conc-calc__phone">
							<a class="cfkl-conc-link" href="<?php echo esc_url( 'https://wa.me/' . $conc_whatsapp ); ?>"><?php echo esc_html( $conc_number ); ?></a>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>

		<form class="cfkl-conc-form"
			data-cfkl-calc
			data-calc-default-coin="<?php echo esc_attr( $conc_coin ); ?>"
			data-calc-default-currency="<?php echo esc_attr( $conc_currency ); ?>"
			data-calc-default-mode="buy"
			data-calc-state="<?php echo esc_attr( empty( $conc_rates['stale'] ) ? 'live' : 'stale' ); ?>"
			data-calc-active-mode="buy">

			<div class="cfkl-conc-form__tabs" role="group" aria-label="<?php esc_attr_e( 'Direction of the trade', 'coinsfera' ); ?>">
				<button type="button" class="cfkl-conc-tab is-active" data-calc-mode="buy" aria-pressed="true">
					<?php esc_html_e( 'I would like to buy', 'coinsfera' ); ?>
				</button>
				<button type="button" class="cfkl-conc-tab" data-calc-mode="sell" aria-pressed="false">
					<?php esc_html_e( 'I would like to sell', 'coinsfera' ); ?>
				</button>
			</div>

			<div class="cfkl-conc-form__grid">

				<p class="cfkl-conc-field">
					<label class="cfkl-conc-field__label" for="cfkl-conc-coin"><?php esc_html_e( 'Which coin', 'coinsfera' ); ?></label>
					<span class="cfkl-conc-field__control cfkl-conc-field__control--select">
						<select class="cfkl-conc-field__input" id="cfkl-conc-coin" data-calc-coin>
							<?php foreach ( $conc_coins as $conc_symbol => $conc_meta ) : ?>
								<option value="<?php echo esc_attr( $conc_symbol ); ?>" <?php selected( $conc_symbol, $conc_coin ); ?>>
									<?php
									echo esc_html(
										sprintf(
											/* translators: 1: coin name, for example Bitcoin. 2: ticker, for example BTC. */
											__( '%1$s (%2$s)', 'coinsfera' ),
											isset( $conc_meta['label'] ) ? $conc_meta['label'] : $conc_symbol,
											$conc_symbol
										)
									);
									?>
								</option>
							<?php endforeach; ?>
						</select>
					</span>
				</p>

				<p class="cfkl-conc-field">
					<label class="cfkl-conc-field__label" for="cfkl-conc-currency"><?php esc_html_e( 'Settling in', 'coinsfera' ); ?></label>
					<span class="cfkl-conc-field__control cfkl-conc-field__control--select">
						<select class="cfkl-conc-field__input" id="cfkl-conc-currency" data-calc-currency>
							<?php foreach ( $conc_currencies as $conc_code => $conc_money ) : ?>
								<option value="<?php echo esc_attr( $conc_code ); ?>" <?php selected( $conc_code, $conc_currency ); ?>>
									<?php echo esc_html( isset( $conc_money['label'] ) ? $conc_money['label'] : strtoupper( $conc_code ) ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</span>
				</p>

				<p class="cfkl-conc-field">
					<label class="cfkl-conc-field__label" for="cfkl-conc-fiat"><?php esc_html_e( 'Amount you have in mind', 'coinsfera' ); ?></label>
					<span class="cfkl-conc-field__control">
						<span class="cfkl-conc-field__affix cfkl-conc-field__affix--figure" data-calc-out="currency-symbol" aria-hidden="true"><?php echo esc_html( isset( $conc_currencies[ $conc_currency ]['symbol'] ) ? $conc_currencies[ $conc_currency ]['symbol'] : '' ); ?></span>
						<input class="cfkl-conc-field__input cfkl-conc-field__input--figure"
							id="cfkl-conc-fiat"
							type="number"
							inputmode="decimal"
							min="0"
							step="any"
							autocomplete="off"
							value="<?php echo esc_attr( $conc_amount ); ?>"
							data-calc-fiat>
					</span>
				</p>

				<p class="cfkl-conc-field">
					<label class="cfkl-conc-field__label" for="cfkl-conc-crypto"><?php esc_html_e( 'Or an amount in coin', 'coinsfera' ); ?></label>
					<span class="cfkl-conc-field__control">
						<input class="cfkl-conc-field__input cfkl-conc-field__input--figure"
							id="cfkl-conc-crypto"
							type="number"
							inputmode="decimal"
							min="0"
							step="any"
							autocomplete="off"
							value="<?php echo esc_attr( $conc_holding > 0 ? number_format( $conc_holding, $conc_dp, '.', '' ) : '' ); ?>"
							data-calc-crypto>
						<span class="cfkl-conc-field__affix cfkl-conc-field__affix--figure" data-calc-out="coin" aria-hidden="true"><?php echo esc_html( $conc_coin ); ?></span>
					</span>
				</p>

			</div>

			<div class="cfkl-conc-form__quote">

				<p class="cfkl-conc-form__direction" data-calc-out="direction"><?php esc_html_e( 'You pay', 'coinsfera' ); ?></p>

				<p class="cfkl-conc-form__figure" data-calc-out="total"><?php echo esc_html( cfkl_money( $conc_amount, $conc_currency ) ); ?></p>

				<p class="cfkl-conc-form__reading">
					<span class="cfkl-conc-form__unit" data-calc-out="unit">
						<?php
						if ( $conc_rate > 0 ) {
							echo esc_html(
								sprintf(
									/* translators: 1: ticker, for example BTC. 2: formatted price, for example $64,000. */
									__( '1 %1$s = %2$s', 'coinsfera' ),
									$conc_coin,
									cfkl_money( $conc_rate, $conc_currency )
								)
							);
						} else {
							esc_html_e( 'Rate confirmed by the desk', 'coinsfera' );
						}
						?>
					</span>
					<span class="cfkl-conc-form__state">
						<span data-calc-out="status">
							<?php
							if ( empty( $conc_rates['stale'] ) ) {
								esc_html_e( 'Live rate', 'coinsfera' );
							} else {
								esc_html_e( 'Last known rate', 'coinsfera' );
							}
							?>
						</span>
						<span class="cfkl-conc-form__age" data-calc-out="updated"></span>
					</span>
				</p>

			</div>

			<?php if ( '' !== $conc_label && '' !== $conc_cta_url ) : ?>
				<a class="cfkl-conc-btn cfkl-conc-btn--terra cfkl-conc-form__cta"
					href="<?php echo esc_url( $conc_cta_url ); ?>"
					data-calc-cta
					data-calc-message="<?php esc_attr_e( 'Hello Coinsfera, I would like to {mode} {crypto} {coin}, around {fiat} {currency}. Could you confirm the rate and a good time to come to the office?', 'coinsfera' ); ?>">
					<?php echo esc_html( $conc_label ); ?>
				</a>
			<?php endif; ?>

			<?php if ( '' !== $conc_note ) : ?>
				<p class="cfkl-conc-form__note"><?php echo esc_html( $conc_note ); ?></p>
			<?php endif; ?>

		</form>

	</div>
</section>
