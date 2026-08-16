<?php
/**
 * Swiss Ledger - fees, set as a typeset ledger.
 *
 * Item on the left, dot leaders across the gap, the figure right aligned and
 * tabular. The last line carries a rule above it, the way a total does. The
 * section standfirst moves into a marginal column divided by a vertical rule,
 * so the ledger itself stays a clean two column reading.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_rows = cfkl_rows( 'fees_rows' );

if ( empty( $cfkl_rows ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'fees_title' );
$cfkl_text  = (string) cfkl_get( 'fees_text' );
$cfkl_last  = count( $cfkl_rows ) - 1;

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-fees-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, '', 'cfkl-ldg-fees-title' ); ?>

		<div class="cfkl-ldg-grid">
			<dl class="cfkl-ldg-ledger">
				<?php foreach ( $cfkl_rows as $cfkl_index => $cfkl_row ) : ?>
					<?php
					$cfkl_label = isset( $cfkl_row['label'] ) ? (string) $cfkl_row['label'] : '';
					$cfkl_value = isset( $cfkl_row['value'] ) ? (string) $cfkl_row['value'] : '';
					$cfkl_note  = isset( $cfkl_row['note'] ) ? (string) $cfkl_row['note'] : '';

					if ( '' === $cfkl_label ) {
						continue;
					}

					$cfkl_class = 'cfkl-ldg-ledger__row';

					if ( $cfkl_index === $cfkl_last ) {
						$cfkl_class .= ' cfkl-ldg-ledger__row--total';
					}
					?>
					<div class="<?php echo esc_attr( $cfkl_class ); ?>">
						<dt class="cfkl-ldg-ledger__item">
							<span class="cfkl-ldg-ledger__label"><?php echo esc_html( $cfkl_label ); ?></span>
							<span class="cfkl-ldg-ledger__leader" aria-hidden="true"></span>
							<?php if ( '' !== $cfkl_note ) : ?>
								<span class="cfkl-ldg-ledger__note"><?php echo esc_html( $cfkl_note ); ?></span>
							<?php endif; ?>
						</dt>
						<dd class="cfkl-ldg-ledger__value"><?php echo esc_html( '' !== $cfkl_value ? $cfkl_value : '—' ); ?></dd>
					</div>
				<?php endforeach; ?>
			</dl>

			<?php if ( '' !== $cfkl_text ) : ?>
				<aside class="cfkl-ldg-margin-note">
					<p class="cfkl-ldg-ml"><?php esc_html_e( 'Note', 'coinsfera' ); ?></p>
					<p class="cfkl-ldg-smallprint"><?php echo esc_html( $cfkl_text ); ?></p>
				</aside>
			<?php endif; ?>
		</div>

	</div>
</section>
