<?php
/**
 * Neo Fintech - what to bring.
 *
 * Three bordered cards on a peach field, each with its icon in a filled chip.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_items = cfkl_rows( 'req_cards' );

if ( empty( $neo_items ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'req_title' );
$neo_text  = (string) cfkl_get( 'req_text' );
$neo_icons = array( 'shield', 'wallet', 'phone', 'building' );
$neo_index = 0;
?>
<section class="cfkl-neo-section cfkl-neo-req cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, $neo_text ); ?>

		<ul class="cfkl-neo-req__list">
			<?php foreach ( $neo_items as $neo_item ) : ?>
				<?php
				$neo_item_title = isset( $neo_item['title'] ) ? (string) $neo_item['title'] : '';
				$neo_desc       = isset( $neo_item['desc'] ) ? (string) $neo_item['desc'] : '';
				$neo_image      = isset( $neo_item['image'] ) ? $neo_item['image'] : array();

				if ( '' === $neo_item_title && '' === $neo_desc ) {
					continue;
				}

				$neo_has_image = ! empty( $neo_image['ID'] );
				$neo_icon      = $neo_icons[ $neo_index % count( $neo_icons ) ];
				++$neo_index;
				?>
				<li class="cfkl-neo-req__card">
					<span class="cfkl-neo-chip<?php echo $neo_has_image ? ' cfkl-neo-chip--image' : ''; ?>">
						<?php
						if ( $neo_has_image ) {
							echo cfkl_image( $neo_image, 'thumbnail', array( 'class' => 'cfkl-neo-chip__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by wp_get_attachment_image.
						} else {
							echo cfkl_icon( $neo_icon, 'cfkl-neo-chip__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
						}
						?>
					</span>
					<?php if ( '' !== $neo_item_title ) : ?>
						<h3 class="cfkl-neo-req__title"><?php echo esc_html( $neo_item_title ); ?></h3>
					<?php endif; ?>
					<?php if ( '' !== $neo_desc ) : ?>
						<p class="cfkl-neo-req__text"><?php echo esc_html( $neo_desc ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
