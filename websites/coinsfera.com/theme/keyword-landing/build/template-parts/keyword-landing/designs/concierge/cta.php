<?php
/**
 * Concierge closing invitation.
 *
 * The one espresso-dark block on the page, so the page closes on a warm chord
 * rather than trailing off. The address is repeated here because the whole
 * argument of this design is that there is somewhere to go.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title   = (string) cfkl_get( 'cta_title' );
$conc_label   = (string) cfkl_get( 'cta_label' );
$conc_url     = (string) cfkl_get( 'cta_url' );
$conc_office  = cfkl_office();
$conc_address = trim( preg_replace( '/\s+/', ' ', $conc_office['address'] ) );

if ( '' === $conc_title ) {
	return;
}
?>
<section class="cfkl-conc-cta cfkl-reveal">
	<div class="cfkl-container cfkl-conc-cta__inner">

		<p class="cfkl-conc-eyebrow cfkl-conc-eyebrow--light"><?php esc_html_e( 'We look forward to it', 'coinsfera' ); ?></p>

		<h2 class="cfkl-conc-cta__title"><?php echo esc_html( $conc_title ); ?></h2>

		<?php if ( '' !== $conc_address ) : ?>
			<p class="cfkl-conc-cta__address">
				<?php echo cfkl_icon( 'pin', 'cfkl-conc-cta__pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup. ?>
				<span><?php echo esc_html( $conc_address ); ?></span>
			</p>
		<?php endif; ?>

		<?php if ( '' !== $conc_label && '' !== $conc_url ) : ?>
			<p class="cfkl-conc-cta__actions">
				<a class="cfkl-conc-btn" href="<?php echo esc_url( $conc_url ); ?>"><?php echo esc_html( $conc_label ); ?></a>
			</p>
		<?php endif; ?>

	</div>
</section>
