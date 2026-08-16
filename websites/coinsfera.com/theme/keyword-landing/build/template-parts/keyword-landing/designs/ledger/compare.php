<?php
/**
 * Swiss Ledger - comparison matrix.
 *
 * A strict grid of hairlines. Cells that read as a plain yes or no become a
 * tick or an en dash; anything else prints as written. Our column is marked by
 * a 2px accent rule at the top and a slightly heavier label, never by a fill.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_rows = cfkl_rows( 'compare_rows' );

if ( empty( $cfkl_rows ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'compare_title' );
$cfkl_text  = (string) cfkl_get( 'compare_text' );
$cfkl_us    = (string) cfkl_get( 'compare_col_us', __( 'Coinsfera', 'coinsfera' ) );
$cfkl_col_b = (string) cfkl_get( 'compare_col_b' );
$cfkl_col_c = (string) cfkl_get( 'compare_col_c' );

if ( ! function_exists( 'cfkl_ldg_cell' ) ) {
	/**
	 * Render one matrix cell.
	 *
	 * Editors type either a short phrase or a yes/no marker, and the two need
	 * different treatments: a phrase is set as text, a marker becomes a tick or
	 * an en dash with a spoken equivalent for assistive technology.
	 *
	 * @param string $value Cell value as authored.
	 * @return string HTML, already escaped.
	 */
	function cfkl_ldg_cell( $value ) {

		$value = trim( (string) $value );
		$key   = function_exists( 'mb_strtolower' ) ? mb_strtolower( $value ) : strtolower( $value );

		$yes = array( 'yes', 'y', 'true', '1', '✓', '✔', 'check', 'evet', 'да' );
		$no  = array( '', 'no', 'n', 'false', '0', '-', '–', '—', '×', 'x', 'hayır', 'hayir', 'нет' );

		if ( in_array( $key, $yes, true ) ) {
			return cfkl_icon( 'check', 'cfkl-ldg-tick' )
				. '<span class="cfkl-sr">' . esc_html__( 'Yes', 'coinsfera' ) . '</span>';
		}

		if ( in_array( $key, $no, true ) ) {
			return '<span class="cfkl-ldg-absent" aria-hidden="true">&ndash;</span>'
				. '<span class="cfkl-sr">' . esc_html__( 'No', 'coinsfera' ) . '</span>';
		}

		return esc_html( $value );
	}
}

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-compare-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, $cfkl_text, 'cfkl-ldg-compare-title' ); ?>

		<div class="cfkl-ldg-grid">
			<div class="cfkl-ldg-matrix-wrap" role="region" tabindex="0"
				aria-label="<?php esc_attr_e( 'Comparison of the Istanbul desk with other ways to buy', 'coinsfera' ); ?>">
				<table class="cfkl-ldg-matrix">
					<thead>
						<tr>
							<th scope="col" class="cfkl-ldg-matrix__rowhead">
								<span class="cfkl-ldg-ml"><?php esc_html_e( 'Compared on', 'coinsfera' ); ?></span>
							</th>
							<th scope="col" class="cfkl-ldg-matrix__us">
								<span class="cfkl-ldg-ml cfkl-ldg-ml--strong"><?php echo esc_html( $cfkl_us ); ?></span>
							</th>
							<?php if ( '' !== $cfkl_col_b ) : ?>
								<th scope="col"><span class="cfkl-ldg-ml"><?php echo esc_html( $cfkl_col_b ); ?></span></th>
							<?php endif; ?>
							<?php if ( '' !== $cfkl_col_c ) : ?>
								<th scope="col"><span class="cfkl-ldg-ml"><?php echo esc_html( $cfkl_col_c ); ?></span></th>
							<?php endif; ?>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $cfkl_rows as $cfkl_row ) : ?>
							<?php
							$cfkl_label = isset( $cfkl_row['label'] ) ? (string) $cfkl_row['label'] : '';

							if ( '' === $cfkl_label ) {
								continue;
							}
							?>
							<tr>
								<th scope="row" class="cfkl-ldg-matrix__rowhead"><?php echo esc_html( $cfkl_label ); ?></th>
								<td class="cfkl-ldg-matrix__us">
									<?php echo cfkl_ldg_cell( isset( $cfkl_row['us'] ) ? $cfkl_row['us'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cfkl_ldg_cell. ?>
								</td>
								<?php if ( '' !== $cfkl_col_b ) : ?>
									<td>
										<?php echo cfkl_ldg_cell( isset( $cfkl_row['b'] ) ? $cfkl_row['b'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cfkl_ldg_cell. ?>
									</td>
								<?php endif; ?>
								<?php if ( '' !== $cfkl_col_c ) : ?>
									<td>
										<?php echo cfkl_ldg_cell( isset( $cfkl_row['c'] ) ? $cfkl_row['c'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cfkl_ldg_cell. ?>
									</td>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			</div>
		</div>

	</div>
</section>
