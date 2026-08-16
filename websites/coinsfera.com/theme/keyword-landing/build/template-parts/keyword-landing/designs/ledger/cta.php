<?php
/**
 * Swiss Ledger - closing line.
 *
 * The last entry in the document: figure reference, one modest heading, one ink
 * button, and the address repeated as small print so the page ends on a fact.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_title = (string) cfkl_get( 'cta_title' );
$cfkl_label = (string) cfkl_get( 'cta_label' );
$cfkl_url   = (string) cfkl_get( 'cta_url' );

if ( '' === $cfkl_title && ( '' === $cfkl_label || '' === $cfkl_url ) ) {
	return;
}

$cfkl_office = cfkl_office();

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-ldg-sec--band cfkl-ldg-close" aria-labelledby="cfkl-ldg-cta-title">
	<div class="cfkl-container">
		<div class="cfkl-ldg-grid cfkl-ldg-head">
			<?php cfkl_ldg_mark( $cfkl_figure ); ?>
			<div class="cfkl-ldg-head__body">
				<?php if ( '' !== $cfkl_title ) : ?>
					<h2 id="cfkl-ldg-cta-title" class="cfkl-ldg-h2 cfkl-ldg-close__title"><?php echo esc_html( $cfkl_title ); ?></h2>
				<?php endif; ?>

				<?php if ( '' !== $cfkl_label && '' !== $cfkl_url ) : ?>
					<a class="cfkl-ldg-btn cfkl-ldg-btn--ink" href="<?php echo esc_url( $cfkl_url ); ?>">
						<?php echo esc_html( $cfkl_label ); ?>
					</a>
				<?php endif; ?>

				<?php if ( '' !== $cfkl_office['address'] ) : ?>
					<p class="cfkl-ldg-ml cfkl-ldg-close__address"><?php echo esc_html( $cfkl_office['address'] ); ?></p>
				<?php endif; ?>
			</div>
		</div>
	</div>
</section>
