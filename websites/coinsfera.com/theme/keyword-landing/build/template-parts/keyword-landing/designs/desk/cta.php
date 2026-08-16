<?php
/**
 * DESK - closing line.
 *
 * A ruled band and the page's single filled button. Everything else on the
 * page is ink on paper, so this is the only element that can be orange.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_title = (string) cfkl_get( 'cta_title' );
$cfkl_label = (string) cfkl_get( 'cta_label' );
$cfkl_url   = (string) cfkl_get( 'cta_url' );

if ( '' === $cfkl_title && '' === $cfkl_label ) {
	return;
}

$cfkl_office = cfkl_office();
?>
<section class="cfkl-desk-section cfkl-desk-cta cfkl-reveal">
	<div class="cfkl-container">
		<div class="cfkl-desk-cta__band">
			<div class="cfkl-desk-cta__body">
				<p class="cfkl-desk-cta__micro"><?php esc_html_e( 'Dealing line open', 'coinsfera' ); ?></p>
				<?php if ( '' !== $cfkl_title ) : ?>
					<h2 class="cfkl-desk-cta__title"><?php echo esc_html( $cfkl_title ); ?></h2>
				<?php endif; ?>
				<?php if ( '' !== $cfkl_office['address'] ) : ?>
					<p class="cfkl-desk-cta__address"><?php echo esc_html( $cfkl_office['address'] ); ?></p>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $cfkl_label && '' !== $cfkl_url ) : ?>
				<a class="cfkl-desk-btn" href="<?php echo esc_url( $cfkl_url ); ?>">
					<?php echo esc_html( $cfkl_label ); ?>
					<?php echo cfkl_icon( 'arrow', 'cfkl-desk-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</a>
			<?php endif; ?>
		</div>
	</div>
</section>
