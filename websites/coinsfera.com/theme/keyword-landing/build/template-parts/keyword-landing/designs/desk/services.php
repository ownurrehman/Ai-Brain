<?php
/**
 * DESK - other desks.
 *
 * Internal cross-links as an index: mono number, name, one line, arrow. The
 * repeater's icons are skipped; an index does not carry pictures.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_services = cfkl_rows( 'services' );

if ( empty( $cfkl_services ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'services_title' );
?>
<section class="cfkl-desk-section cfkl-desk-services cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Other desks', 'coinsfera' ), $cfkl_title ); ?>

		<ul class="cfkl-desk-index">
			<?php $cfkl_index = 0; ?>
			<?php foreach ( $cfkl_services as $cfkl_service ) : ?>
				<?php
				$cfkl_name = isset( $cfkl_service['title'] ) ? (string) $cfkl_service['title'] : '';
				$cfkl_desc = isset( $cfkl_service['desc'] ) ? (string) $cfkl_service['desc'] : '';
				$cfkl_url  = isset( $cfkl_service['url'] ) ? (string) $cfkl_service['url'] : '';

				if ( '' === $cfkl_name ) {
					continue;
				}

				$cfkl_index++;
				?>
				<li class="cfkl-desk-index__item">
					<?php if ( '' !== $cfkl_url ) : ?>
						<a class="cfkl-desk-index__link" href="<?php echo esc_url( $cfkl_url ); ?>">
							<span class="cfkl-desk-index__no"><?php echo esc_html( sprintf( '%02d', $cfkl_index ) ); ?></span>
							<span class="cfkl-desk-index__body">
								<span class="cfkl-desk-index__title"><?php echo esc_html( $cfkl_name ); ?></span>
								<?php if ( '' !== $cfkl_desc ) : ?>
									<span class="cfkl-desk-index__text"><?php echo esc_html( $cfkl_desc ); ?></span>
								<?php endif; ?>
							</span>
							<?php echo cfkl_icon( 'arrow', 'cfkl-desk-index__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</a>
					<?php else : ?>
						<span class="cfkl-desk-index__link">
							<span class="cfkl-desk-index__no"><?php echo esc_html( sprintf( '%02d', $cfkl_index ) ); ?></span>
							<span class="cfkl-desk-index__body">
								<span class="cfkl-desk-index__title"><?php echo esc_html( $cfkl_name ); ?></span>
								<?php if ( '' !== $cfkl_desc ) : ?>
									<span class="cfkl-desk-index__text"><?php echo esc_html( $cfkl_desc ); ?></span>
								<?php endif; ?>
							</span>
						</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
