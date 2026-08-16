<?php
/**
 * Swiss Ledger - rate board.
 *
 * A plain data list, one ruled row per coin. Ticker as a micro-label, the dollar
 * price as a large tabular figure, lira and euro as secondary figures, and the
 * 24h move as a small signed number. Positive moves take the one accent colour;
 * negative moves go muted, because this design has no second colour to spend.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_rows = cfkl_rate_board();

if ( empty( $cfkl_rows ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'rates_title' );
$cfkl_text  = (string) cfkl_get( 'rates_text' );
$cfkl_feed  = cfkl_get_rates();
$cfkl_stamp = ! empty( $cfkl_feed['updated'] )
	? date_i18n( get_option( 'time_format' ), (int) $cfkl_feed['updated'] )
	: '';

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-rates-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, $cfkl_text, 'cfkl-ldg-rates-title' ); ?>

		<div class="cfkl-ldg-grid">
			<div class="cfkl-ldg-board">

				<div class="cfkl-ldg-board__legend" aria-hidden="true">
					<span class="cfkl-ldg-ml"><?php esc_html_e( 'Asset', 'coinsfera' ); ?></span>
					<span class="cfkl-ldg-ml"><?php esc_html_e( 'USD', 'coinsfera' ); ?></span>
					<span class="cfkl-ldg-ml"><?php esc_html_e( 'TRY', 'coinsfera' ); ?></span>
					<span class="cfkl-ldg-ml"><?php esc_html_e( 'EUR', 'coinsfera' ); ?></span>
					<span class="cfkl-ldg-ml"><?php esc_html_e( '24h', 'coinsfera' ); ?></span>
				</div>

				<ul class="cfkl-ldg-board__list">
					<?php foreach ( $cfkl_rows as $cfkl_row ) : ?>
						<?php
						$cfkl_symbol = isset( $cfkl_row['symbol'] ) ? (string) $cfkl_row['symbol'] : '';
						$cfkl_label  = isset( $cfkl_row['label'] ) ? (string) $cfkl_row['label'] : $cfkl_symbol;
						$cfkl_change = isset( $cfkl_row['change'] ) ? $cfkl_row['change'] : null;

						if ( '' === $cfkl_symbol ) {
							continue;
						}
						?>
						<li class="cfkl-ldg-board__row">
							<span class="cfkl-ldg-board__coin">
								<span class="cfkl-ldg-ml cfkl-ldg-board__ticker"><?php echo esc_html( $cfkl_symbol ); ?></span>
								<span class="cfkl-ldg-board__name"><?php echo esc_html( $cfkl_label ); ?></span>
							</span>
							<span class="cfkl-ldg-board__price cfkl-ldg-num cfkl-ldg-num--sm">
								<?php echo esc_html( cfkl_money( isset( $cfkl_row['usd'] ) ? $cfkl_row['usd'] : 0, 'usd' ) ); ?>
							</span>
							<span class="cfkl-ldg-board__alt">
								<span class="cfkl-ldg-ml cfkl-ldg-board__key"><?php esc_html_e( 'TRY', 'coinsfera' ); ?></span>
								<?php echo esc_html( cfkl_money( isset( $cfkl_row['try'] ) ? $cfkl_row['try'] : 0, 'try' ) ); ?>
							</span>
							<span class="cfkl-ldg-board__alt">
								<span class="cfkl-ldg-ml cfkl-ldg-board__key"><?php esc_html_e( 'EUR', 'coinsfera' ); ?></span>
								<?php echo esc_html( cfkl_money( isset( $cfkl_row['eur'] ) ? $cfkl_row['eur'] : 0, 'eur' ) ); ?>
							</span>
							<span class="cfkl-ldg-board__move">
								<?php if ( null === $cfkl_change ) : ?>
									<span aria-hidden="true">&ndash;</span>
								<?php else : ?>
									<span class="cfkl-ldg-delta" data-trend="<?php echo esc_attr( $cfkl_change >= 0 ? 'up' : 'down' ); ?>">
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
								<?php endif; ?>
							</span>
						</li>
					<?php endforeach; ?>
				</ul>

				<?php if ( '' !== $cfkl_stamp ) : ?>
					<p class="cfkl-ldg-ml cfkl-ldg-board__stamp">
						<?php
						printf(
							/* translators: %s: time the rate feed was last refreshed. */
							esc_html__( 'Indicative mid-market rates, %s', 'coinsfera' ),
							esc_html( $cfkl_stamp )
						);
						?>
					</p>
				<?php endif; ?>

			</div>
		</div>

	</div>
</section>
