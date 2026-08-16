<?php
/**
 * Neo Fintech - features as a bento grid.
 *
 * The first tile takes double the width and the tint cycles peach, violet,
 * paper, so no two neighbours share a fill.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_items = cfkl_rows( 'features' );

if ( empty( $neo_items ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'features_title' );
$neo_tints = array( 'peach', 'violet', 'paper' );
$neo_icons = array( 'bolt', 'shield', 'clock', 'wallet', 'swap', 'building' );
$neo_index = 0;
?>
<section class="cfkl-neo-section cfkl-neo-features cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, '', array( 'align' => 'left' ) ); ?>

		<ul class="cfkl-neo-bento">
			<?php foreach ( $neo_items as $neo_item ) : ?>
				<?php
				$neo_item_title = isset( $neo_item['title'] ) ? (string) $neo_item['title'] : '';
				$neo_desc       = isset( $neo_item['desc'] ) ? (string) $neo_item['desc'] : '';
				$neo_image      = isset( $neo_item['image'] ) ? $neo_item['image'] : array();

				if ( '' === $neo_item_title && '' === $neo_desc ) {
					continue;
				}

				$neo_classes = array(
					'cfkl-neo-tile',
					'cfkl-neo-tile--' . $neo_tints[ $neo_index % count( $neo_tints ) ],
				);

				if ( 0 === $neo_index ) {
					$neo_classes[] = 'cfkl-neo-tile--wide';
				}

				$neo_has_image = ! empty( $neo_image['ID'] );
				$neo_icon      = $neo_icons[ $neo_index % count( $neo_icons ) ];
				++$neo_index;
				?>
				<li class="<?php echo esc_attr( implode( ' ', $neo_classes ) ); ?>">

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
						<h3 class="cfkl-neo-tile__title"><?php echo esc_html( $neo_item_title ); ?></h3>
					<?php endif; ?>

					<?php if ( '' !== $neo_desc ) : ?>
						<p class="cfkl-neo-tile__text"><?php echo esc_html( $neo_desc ); ?></p>
					<?php endif; ?>

				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
