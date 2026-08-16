<?php
/**
 * Neo Fintech - closing CTA.
 *
 * A full-bleed violet block with one orange pill button. The page opens on
 * orange and closes on violet on purpose, so the end does not read as a
 * repeat of the beginning.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_title = (string) cfkl_get( 'cta_title' );
$neo_label = (string) cfkl_get( 'cta_label' );
$neo_url   = (string) cfkl_get( 'cta_url' );

if ( '' === $neo_title && '' === $neo_label ) {
	return;
}

$neo_office = cfkl_office();
?>
<section class="cfkl-neo-cta cfkl-reveal">
	<div class="cfkl-container cfkl-neo-cta__inner">

		<?php if ( '' !== $neo_title ) : ?>
			<h2 class="cfkl-neo-cta__title"><?php echo esc_html( $neo_title ); ?></h2>
		<?php endif; ?>

		<div class="cfkl-neo-cta__actions">
			<?php if ( '' !== $neo_label && '' !== $neo_url ) : ?>
				<a class="cfkl-neo-btn cfkl-neo-btn--orange cfkl-neo-btn--lg" href="<?php echo esc_url( $neo_url ); ?>">
					<span><?php echo esc_html( $neo_label ); ?></span>
					<?php echo cfkl_icon( 'arrow', 'cfkl-neo-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
				</a>
			<?php endif; ?>

			<?php if ( '' !== $neo_office['address'] ) : ?>
				<p class="cfkl-neo-cta__address">
					<?php echo cfkl_icon( 'pin', 'cfkl-neo-cta__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<span><?php echo esc_html( str_replace( array( "\r\n", "\n", "\r" ), ' ', $neo_office['address'] ) ); ?></span>
				</p>
			<?php endif; ?>
		</div>

	</div>
</section>
