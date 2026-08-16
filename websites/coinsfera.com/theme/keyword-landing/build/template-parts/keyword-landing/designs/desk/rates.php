<?php
/**
 * DESK - rate sheet.
 *
 * The hero board reads like a ticker; this reads like the sheet pinned beside
 * the counter: every coin against all three settlement currencies at once,
 * which is the comparison a walk-in customer paying lira actually makes.
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
?>
<section class="cfkl-desk-section cfkl-desk-rates cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Rate sheet', 'coinsfera' ), $cfkl_title, $cfkl_text ); ?>

		<div class="cfkl-desk-scroll" role="region" tabindex="0" aria-label="<?php esc_attr_e( 'Rate sheet, scrolls sideways', 'coinsfera' ); ?>">
			<table class="cfkl-desk-table cfkl-desk-table--rates">
				<caption class="cfkl-sr"><?php esc_html_e( 'Reference rates by coin and settlement currency', 'coinsfera' ); ?></caption>
				<thead>
					<tr>
						<th scope="col"><?php esc_html_e( 'Asset', 'coinsfera' ); ?></th>
						<th scope="col" class="cfkl-desk-table__figure"><?php esc_html_e( 'USD', 'coinsfera' ); ?></th>
						<th scope="col" class="cfkl-desk-table__figure"><?php esc_html_e( 'EUR', 'coinsfera' ); ?></th>
						<th scope="col" class="cfkl-desk-table__figure"><?php esc_html_e( 'TRY', 'coinsfera' ); ?></th>
						<th scope="col" class="cfkl-desk-table__figure"><?php esc_html_e( '24h', 'coinsfera' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $cfkl_rows as $cfkl_row ) : ?>
						<?php $cfkl_change = isset( $cfkl_row['change'] ) ? $cfkl_row['change'] : null; ?>
						<tr>
							<th scope="row">
								<span class="cfkl-desk-table__ticker"><?php echo esc_html( $cfkl_row['symbol'] ); ?></span>
								<span class="cfkl-desk-table__name"><?php echo esc_html( $cfkl_row['label'] ); ?></span>
							</th>
							<?php foreach ( array( 'usd', 'eur', 'try' ) as $cfkl_code ) : ?>
								<?php $cfkl_value = isset( $cfkl_row[ $cfkl_code ] ) ? (float) $cfkl_row[ $cfkl_code ] : 0.0; ?>
								<td class="cfkl-desk-table__figure"><?php echo esc_html( $cfkl_value > 0 ? cfkl_money( $cfkl_value, $cfkl_code ) : '—' ); ?></td>
							<?php endforeach; ?>
							<td class="cfkl-desk-table__figure">
								<?php if ( null === $cfkl_change ) : ?>
									<span class="cfkl-desk-table__void">—</span>
								<?php else : ?>
									<span class="cfkl-desk-table__change" data-trend="<?php echo $cfkl_change >= 0 ? 'up' : 'down'; ?>">
										<?php echo esc_html( ( $cfkl_change >= 0 ? '+' : '' ) . number_format_i18n( (float) $cfkl_change, 2 ) . '%' ); ?>
									</span>
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
