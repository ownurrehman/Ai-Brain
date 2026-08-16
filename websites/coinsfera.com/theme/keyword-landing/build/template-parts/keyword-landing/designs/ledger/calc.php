<?php
/**
 * Swiss Ledger - rate calculator.
 *
 * Set as a form on ruled paper. Every control is one row of a ruled table: a
 * micro-label on the left, the value right aligned with no box around it, the
 * row rule doing the work a border would normally do. The total is the one
 * enormous numeral, with the unit rate and spread as small print beneath it.
 *
 * Every figure is rendered server side with cfkl_money() and number_format_i18n
 * so the quote reads correctly before, and without, JavaScript.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_rates = cfkl_get_rates();

if ( empty( $cfkl_rates['coins'] ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'calc_title' );
$cfkl_text  = (string) cfkl_get( 'calc_text' );
$cfkl_label = (string) cfkl_get( 'calc_cta_label', __( 'Request this quote', 'coinsfera' ) );
$cfkl_note  = (string) cfkl_get( 'calc_note' );

$cfkl_coins      = cfkl_rate_coins();
$cfkl_currencies = cfkl_rate_currencies();

/* Only offer coins the feed actually priced; the script works from the same list. */
$cfkl_coins = array_intersect_key( $cfkl_coins, $cfkl_rates['coins'] );

if ( empty( $cfkl_coins ) ) {
	return;
}

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
$cfkl_rate   = $cfkl_market * ( 1 + ( $cfkl_spread / 100 ) );
$cfkl_change = isset( $cfkl_rates['coins'][ $cfkl_coin ]['change'] ) ? $cfkl_rates['coins'][ $cfkl_coin ]['change'] : null;

/* Starting amounts, sized for the currency: lira figures are two orders larger. */
$cfkl_presets = array(
	'usd' => array( 1000, 5000, 25000, 100000 ),
	'eur' => array( 1000, 5000, 25000, 100000 ),
	'try' => array( 50000, 250000, 1000000, 5000000 ),
);

$cfkl_quick  = isset( $cfkl_presets[ $cfkl_currency ] ) ? $cfkl_presets[ $cfkl_currency ] : $cfkl_presets['usd'];
$cfkl_fiat   = (float) $cfkl_quick[1];
$cfkl_dp     = isset( $cfkl_coins[ $cfkl_coin ]['dp'] ) ? (int) $cfkl_coins[ $cfkl_coin ]['dp'] : 6;
$cfkl_crypto = $cfkl_rate > 0 ? $cfkl_fiat / $cfkl_rate : 0.0;

$cfkl_whatsapp = preg_replace( '/\D+/', '', (string) cfkl_get( 'calc_whatsapp' ) );
$cfkl_cta_url  = '' !== $cfkl_whatsapp
	? 'https://wa.me/' . $cfkl_whatsapp
	: (string) cfkl_get( 'banner_cta_url', (string) cfkl_get( 'cta_url' ) );

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-ldg-sec--band" aria-labelledby="cfkl-ldg-calc-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, $cfkl_text, 'cfkl-ldg-calc-title' ); ?>

		<div class="cfkl-ldg-grid">
			<form class="cfkl-ldg-calc"
				data-cfkl-calc
				data-calc-default-coin="<?php echo esc_attr( $cfkl_coin ); ?>"
				data-calc-default-currency="<?php echo esc_attr( $cfkl_currency ); ?>"
				data-calc-default-mode="buy"
				data-calc-active-mode="buy">

				<div class="cfkl-ldg-row cfkl-ldg-row--modes">
					<span class="cfkl-ldg-ml" id="cfkl-ldg-calc-mode"><?php esc_html_e( 'Direction', 'coinsfera' ); ?></span>
					<div class="cfkl-ldg-modes" role="group" aria-labelledby="cfkl-ldg-calc-mode">
						<button type="button" class="cfkl-ldg-mode is-active" data-calc-mode="buy" aria-pressed="true">
							<?php esc_html_e( 'Buy', 'coinsfera' ); ?>
						</button>
						<button type="button" class="cfkl-ldg-mode" data-calc-mode="sell" aria-pressed="false">
							<?php esc_html_e( 'Sell', 'coinsfera' ); ?>
						</button>
					</div>
				</div>

				<div class="cfkl-ldg-row">
					<label class="cfkl-ldg-ml" for="cfkl-ldg-calc-coin"><?php esc_html_e( 'Asset', 'coinsfera' ); ?></label>
					<select class="cfkl-ldg-select" id="cfkl-ldg-calc-coin" data-calc-coin>
						<?php foreach ( $cfkl_coins as $cfkl_symbol => $cfkl_meta ) : ?>
							<option value="<?php echo esc_attr( $cfkl_symbol ); ?>" <?php selected( $cfkl_symbol, $cfkl_coin ); ?>>
								<?php echo esc_html( $cfkl_symbol . ' / ' . $cfkl_meta['label'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="cfkl-ldg-row">
					<label class="cfkl-ldg-ml" for="cfkl-ldg-calc-currency"><?php esc_html_e( 'Settlement currency', 'coinsfera' ); ?></label>
					<select class="cfkl-ldg-select" id="cfkl-ldg-calc-currency" data-calc-currency>
						<?php foreach ( $cfkl_currencies as $cfkl_code => $cfkl_meta ) : ?>
							<option value="<?php echo esc_attr( $cfkl_code ); ?>" <?php selected( $cfkl_code, $cfkl_currency ); ?>>
								<?php echo esc_html( $cfkl_meta['label'] ); ?>
							</option>
						<?php endforeach; ?>
					</select>
				</div>

				<div class="cfkl-ldg-row">
					<label class="cfkl-ldg-ml" for="cfkl-ldg-calc-fiat"><?php esc_html_e( 'Cash amount', 'coinsfera' ); ?></label>
					<span class="cfkl-ldg-field">
						<span class="cfkl-ldg-field__unit" data-calc-out="currency-symbol"><?php echo esc_html( $cfkl_currencies[ $cfkl_currency ]['symbol'] ); ?></span>
						<input class="cfkl-ldg-input" id="cfkl-ldg-calc-fiat" type="number" inputmode="decimal" min="0" step="any"
							value="<?php echo esc_attr( (string) $cfkl_fiat ); ?>" data-calc-fiat>
					</span>
				</div>

				<div class="cfkl-ldg-row">
					<label class="cfkl-ldg-ml" for="cfkl-ldg-calc-crypto"><?php esc_html_e( 'Coin amount', 'coinsfera' ); ?></label>
					<span class="cfkl-ldg-field">
						<input class="cfkl-ldg-input" id="cfkl-ldg-calc-crypto" type="number" inputmode="decimal" min="0" step="any"
							value="<?php echo esc_attr( number_format( $cfkl_crypto, $cfkl_dp, '.', '' ) ); ?>" data-calc-crypto>
						<span class="cfkl-ldg-field__unit" data-calc-out="coin"><?php echo esc_html( $cfkl_coin ); ?></span>
					</span>
				</div>

				<div class="cfkl-ldg-row cfkl-ldg-row--quick">
					<span class="cfkl-ldg-ml" id="cfkl-ldg-calc-quick"><?php esc_html_e( 'Common sizes', 'coinsfera' ); ?></span>
					<div class="cfkl-ldg-quick" role="group" aria-labelledby="cfkl-ldg-calc-quick">
						<?php foreach ( $cfkl_quick as $cfkl_amount ) : ?>
							<button type="button" class="cfkl-ldg-quick__btn" data-calc-quick data-amount="<?php echo esc_attr( (string) $cfkl_amount ); ?>">
								<?php echo esc_html( cfkl_money( $cfkl_amount, $cfkl_currency ) ); ?>
							</button>
						<?php endforeach; ?>
					</div>
				</div>

				<div class="cfkl-ldg-row cfkl-ldg-row--total">
					<span class="cfkl-ldg-ml" data-calc-out="direction"><?php esc_html_e( 'You pay', 'coinsfera' ); ?></span>
					<span class="cfkl-ldg-num" data-calc-out="total"><?php echo esc_html( cfkl_money( $cfkl_fiat, $cfkl_currency ) ); ?></span>
				</div>

				<dl class="cfkl-ldg-fine">
					<div class="cfkl-ldg-fine__pair">
						<dt class="cfkl-ldg-ml"><?php esc_html_e( 'Unit rate', 'coinsfera' ); ?></dt>
						<dd class="cfkl-ldg-fine__v" data-calc-out="unit">
							<?php
							printf(
								/* translators: 1: coin ticker, 2: formatted unit price. */
								esc_html__( '1 %1$s = %2$s', 'coinsfera' ),
								esc_html( $cfkl_coin ),
								esc_html( cfkl_money( $cfkl_rate, $cfkl_currency ) )
							);
							?>
						</dd>
					</div>
					<div class="cfkl-ldg-fine__pair">
						<dt class="cfkl-ldg-ml"><?php esc_html_e( 'Desk spread', 'coinsfera' ); ?></dt>
						<dd class="cfkl-ldg-fine__v" data-calc-out="spread"><?php echo esc_html( sprintf( '%s%%', number_format_i18n( $cfkl_spread, 1 ) ) ); ?></dd>
					</div>
					<div class="cfkl-ldg-fine__pair">
						<dt class="cfkl-ldg-ml"><?php esc_html_e( 'Feed', 'coinsfera' ); ?></dt>
						<dd class="cfkl-ldg-fine__v">
							<span data-calc-out="status">
								<?php
								echo empty( $cfkl_rates['stale'] )
									? esc_html__( 'Live rate', 'coinsfera' )
									: esc_html__( 'Last known rate', 'coinsfera' );
								?>
							</span>
							<?php if ( null !== $cfkl_change ) : ?>
								<span class="cfkl-ldg-delta" data-calc-out="change" data-trend="<?php echo esc_attr( $cfkl_change >= 0 ? 'up' : 'down' ); ?>">
									<?php
									echo esc_html(
										sprintf(
											'%1$s%2$s%%',
											$cfkl_change >= 0 ? '+' : '',
											number_format_i18n( (float) $cfkl_change, 2 )
										)
									);
									?>
								</span>
							<?php else : ?>
								<span class="cfkl-ldg-delta" data-calc-out="change" hidden></span>
							<?php endif; ?>
						</dd>
					</div>
				</dl>

				<?php if ( '' !== $cfkl_label && '' !== $cfkl_cta_url ) : ?>
					<a class="cfkl-ldg-btn cfkl-ldg-btn--ink" href="<?php echo esc_url( $cfkl_cta_url ); ?>" data-calc-cta>
						<?php echo esc_html( $cfkl_label ); ?>
					</a>
				<?php endif; ?>

				<?php if ( '' !== $cfkl_note ) : ?>
					<p class="cfkl-ldg-smallprint"><?php echo esc_html( $cfkl_note ); ?></p>
				<?php endif; ?>
			</form>
		</div>

	</div>
</section>
