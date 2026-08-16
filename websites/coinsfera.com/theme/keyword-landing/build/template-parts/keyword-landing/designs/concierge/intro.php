<?php
/**
 * Concierge intro.
 *
 * The editorial voice of the design lives here: one narrow column of long-form
 * prose opened by a serif drop cap, the way a magazine opens a feature.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title = (string) cfkl_get( 'intro_title' );
$conc_text  = (string) cfkl_get( 'intro_text' );

if ( '' === $conc_title && '' === $conc_text ) {
	return;
}
?>
<section class="cfkl-conc-intro cfkl-reveal">
	<div class="cfkl-container cfkl-conc-intro__inner">

		<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'The short version', 'coinsfera' ); ?></p>

		<?php if ( '' !== $conc_title ) : ?>
			<h2 class="cfkl-conc-intro__title"><?php echo esc_html( $conc_title ); ?></h2>
		<?php endif; ?>

		<?php if ( '' !== $conc_text ) : ?>
			<div class="cfkl-conc-prose cfkl-conc-prose--opening">
				<?php echo wp_kses_post( $conc_text ); ?>
			</div>
		<?php endif; ?>

	</div>
</section>
