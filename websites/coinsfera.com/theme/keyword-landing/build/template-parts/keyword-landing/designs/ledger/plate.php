<?php
/**
 * Swiss Ledger - the plate.
 *
 * The only image on the page, run full bleed between two sections and captioned
 * beneath like a plate in a report. It is still the LCP candidate, so it is
 * rendered through cfkl_hero_image() and stays eager even this far down; the
 * preload in the bootstrap asks for the large size, so the plate does too
 * rather than paying for a second download of the full file.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_image = cfkl_get( 'hero_image', array() );

if ( empty( $cfkl_image['ID'] ) ) {
	return;
}

$cfkl_office  = cfkl_office();
$cfkl_caption = isset( $cfkl_image['caption'] ) ? trim( (string) $cfkl_image['caption'] ) : '';

if ( '' === $cfkl_caption ) {
	$cfkl_caption = trim( (string) $cfkl_office['address'] );
}

if ( '' === $cfkl_caption && isset( $cfkl_image['alt'] ) ) {
	$cfkl_caption = trim( (string) $cfkl_image['alt'] );
}

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-plate" aria-label="<?php esc_attr_e( 'Our Istanbul office', 'coinsfera' ); ?>">
	<figure class="cfkl-ldg-plate__figure">
		<?php cfkl_hero_image( 'cfkl-ldg-plate__img' ); ?>
		<figcaption class="cfkl-container">
			<div class="cfkl-ldg-grid cfkl-ldg-plate__caption">
				<?php cfkl_ldg_mark( $cfkl_figure ); ?>
				<p class="cfkl-ldg-ml cfkl-ldg-plate__text">
					<?php if ( '' !== $cfkl_caption ) : ?>
						<?php echo esc_html( $cfkl_caption ); ?>
					<?php else : ?>
						<?php esc_html_e( 'The trading floor at our Istanbul office', 'coinsfera' ); ?>
					<?php endif; ?>
				</p>
			</div>
		</figcaption>
	</figure>
</section>
