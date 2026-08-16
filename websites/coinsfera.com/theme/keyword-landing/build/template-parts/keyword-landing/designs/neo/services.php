<?php
/**
 * Neo Fintech - other services at the desk.
 *
 * Violet-tinted link cards that press into their own shadow on hover.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_items = cfkl_rows( 'services' );

if ( empty( $neo_items ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'services_title' );
?>
<section class="cfkl-neo-section cfkl-neo-services cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, '', array( 'align' => 'left' ) ); ?>

		<ul class="cfkl-neo-services__list">
			<?php foreach ( $neo_items as $neo_item ) : ?>
				<?php
				$neo_item_title = isset( $neo_item['title'] ) ? (string) $neo_item['title'] : '';
				$neo_desc       = isset( $neo_item['desc'] ) ? (string) $neo_item['desc'] : '';
				$neo_url        = isset( $neo_item['url'] ) ? (string) $neo_item['url'] : '';
				$neo_icon       = isset( $neo_item['icon'] ) ? $neo_item['icon'] : array();

				if ( '' === $neo_item_title ) {
					continue;
				}

				$neo_tag = '' !== $neo_url ? 'a' : 'div';
				?>
				<li class="cfkl-neo-services__item">
					<<?php echo esc_attr( $neo_tag ); ?> class="cfkl-neo-service"<?php echo '' !== $neo_url ? ' href="' . esc_url( $neo_url ) . '"' : ''; ?>>

						<?php if ( ! empty( $neo_icon['ID'] ) ) : ?>
							<span class="cfkl-neo-chip cfkl-neo-chip--image">
								<?php echo cfkl_image( $neo_icon, 'thumbnail', array( 'class' => 'cfkl-neo-chip__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped by wp_get_attachment_image. ?>
							</span>
						<?php endif; ?>

						<h3 class="cfkl-neo-service__title"><?php echo esc_html( $neo_item_title ); ?></h3>

						<?php if ( '' !== $neo_desc ) : ?>
							<p class="cfkl-neo-service__text"><?php echo esc_html( $neo_desc ); ?></p>
						<?php endif; ?>

						<?php if ( '' !== $neo_url ) : ?>
							<span class="cfkl-neo-service__more" aria-hidden="true">
								<?php echo cfkl_icon( 'arrow', 'cfkl-neo-service__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							</span>
						<?php endif; ?>

					</<?php echo esc_attr( $neo_tag ); ?>>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
