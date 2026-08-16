<?php
/**
 * DESK - counterparty comparison.
 *
 * A genuine table with hairline borders. Our column carries the only wash of
 * orange on the page and a 2px rule above it, so the eye lands there without
 * any of the copy having to claim anything.
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
$cfkl_b     = (string) cfkl_get( 'compare_col_b' );
$cfkl_c     = (string) cfkl_get( 'compare_col_c' );
?>
<section class="cfkl-desk-section cfkl-desk-compare cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Counterparty comparison', 'coinsfera' ), $cfkl_title, $cfkl_text ); ?>

		<div class="cfkl-desk-scroll" role="region" tabindex="0" aria-label="<?php esc_attr_e( 'Comparison table, scrolls sideways', 'coinsfera' ); ?>">
			<table class="cfkl-desk-table cfkl-desk-table--compare">
				<caption class="cfkl-sr"><?php esc_html_e( 'How the desk compares with other ways to trade', 'coinsfera' ); ?></caption>
				<thead>
					<tr>
						<th scope="col"><span class="cfkl-desk-table__head"><?php esc_html_e( 'Criterion', 'coinsfera' ); ?></span></th>
						<th scope="col" class="cfkl-desk-table__us"><span class="cfkl-desk-table__head"><?php echo esc_html( $cfkl_us ); ?></span></th>
						<?php if ( '' !== $cfkl_b ) : ?>
							<th scope="col"><span class="cfkl-desk-table__head"><?php echo esc_html( $cfkl_b ); ?></span></th>
						<?php endif; ?>
						<?php if ( '' !== $cfkl_c ) : ?>
							<th scope="col"><span class="cfkl-desk-table__head"><?php echo esc_html( $cfkl_c ); ?></span></th>
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
							<th scope="row"><?php echo esc_html( $cfkl_label ); ?></th>
							<td class="cfkl-desk-table__us"><?php echo cfkl_desk_mark( isset( $cfkl_row['us'] ) ? $cfkl_row['us'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cfkl_desk_mark(). ?></td>
							<?php if ( '' !== $cfkl_b ) : ?>
								<td><?php echo cfkl_desk_mark( isset( $cfkl_row['b'] ) ? $cfkl_row['b'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cfkl_desk_mark(). ?></td>
							<?php endif; ?>
							<?php if ( '' !== $cfkl_c ) : ?>
								<td><?php echo cfkl_desk_mark( isset( $cfkl_row['c'] ) ? $cfkl_row['c'] : '' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in cfkl_desk_mark(). ?></td>
							<?php endif; ?>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
	</div>
</section>
