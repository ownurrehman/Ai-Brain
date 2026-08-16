<?php
/**
 * DESK - fee ledger.
 *
 * Dot leaders and right-aligned figures: a price list, not a pricing section.
 * The leaders are drawn by the label's ::after so the definition list keeps a
 * valid dt/dd structure.
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
?>
<section class="cfkl-desk-section cfkl-desk-fees cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Schedule of charges', 'coinsfera' ), $cfkl_title, $cfkl_text ); ?>

		<dl class="cfkl-desk-ledger">
			<?php foreach ( $cfkl_rows as $cfkl_row ) : ?>
				<?php
				$cfkl_label = isset( $cfkl_row['label'] ) ? (string) $cfkl_row['label'] : '';
				$cfkl_value = isset( $cfkl_row['value'] ) ? (string) $cfkl_row['value'] : '';
				$cfkl_note  = isset( $cfkl_row['note'] ) ? (string) $cfkl_row['note'] : '';

				if ( '' === $cfkl_label ) {
					continue;
				}
				?>
				<div class="cfkl-desk-ledger__row">
					<dt class="cfkl-desk-ledger__label"><span class="cfkl-desk-ledger__item"><?php echo esc_html( $cfkl_label ); ?></span></dt>
					<dd class="cfkl-desk-ledger__value">
						<span class="cfkl-desk-ledger__amount"><?php echo esc_html( '' !== $cfkl_value ? $cfkl_value : '—' ); ?></span>
						<?php if ( '' !== $cfkl_note ) : ?>
							<span class="cfkl-desk-ledger__note"><?php echo esc_html( $cfkl_note ); ?></span>
						<?php endif; ?>
					</dd>
				</div>
			<?php endforeach; ?>
		</dl>
	</div>
</section>
