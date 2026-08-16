<?php
/**
 * Neo Fintech - fees.
 *
 * A description list inside one big bordered card. Each amount is set as a
 * figure so the numbers, not the labels, are what the eye lands on.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_rows = cfkl_rows( 'fees_rows' );

if ( empty( $neo_rows ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'fees_title' );
$neo_text  = (string) cfkl_get( 'fees_text' );
?>
<section class="cfkl-neo-section cfkl-neo-fees cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, $neo_text, array( 'align' => 'left' ) ); ?>

		<dl class="cfkl-neo-fees__list">
			<?php foreach ( $neo_rows as $neo_row ) : ?>
				<?php
				$neo_label = isset( $neo_row['label'] ) ? (string) $neo_row['label'] : '';
				$neo_value = isset( $neo_row['value'] ) ? (string) $neo_row['value'] : '';
				$neo_note  = isset( $neo_row['note'] ) ? (string) $neo_row['note'] : '';

				if ( '' === $neo_label && '' === $neo_value ) {
					continue;
				}
				?>
				<div class="cfkl-neo-fees__row">
					<dt class="cfkl-neo-fees__label">
						<?php echo esc_html( $neo_label ); ?>
						<?php if ( '' !== $neo_note ) : ?>
							<span class="cfkl-neo-fees__note"><?php echo esc_html( $neo_note ); ?></span>
						<?php endif; ?>
					</dt>
					<dd class="cfkl-neo-fees__value"><?php echo esc_html( $neo_value ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>

	</div>
</section>
