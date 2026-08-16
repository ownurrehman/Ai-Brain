<?php
/**
 * Neo Fintech - hero.
 *
 * A full-bleed orange field. The headline is the loudest thing on the page,
 * the stat strip is three pills, and the calculator card straddles the bottom
 * edge of the field so the first scroll reveals it sitting half on orange and
 * half on paper.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_tagline   = (string) cfkl_get( 'banner_tagline' );
$neo_heading   = (string) cfkl_get( 'banner_heading' );
$neo_subtext   = (string) cfkl_get( 'banner_subtext' );
$neo_cta_label = (string) cfkl_get( 'banner_cta_label' );
$neo_cta_url   = (string) cfkl_get( 'banner_cta_url' );
$neo_image     = cfkl_get( 'hero_image', array() );
$neo_office    = cfkl_office();

/* Three pills, as the design calls for, even when a fourth stat is authored. */
$neo_stats = array_slice( cfkl_rows( 'banner_stats' ), 0, 3 );

if ( '' === $neo_heading ) {
	return;
}

/*
 * The calculator is buffered rather than printed in place. Its own guard can
 * decide there is no usable rate feed, and the paper apron under the orange
 * field only earns its space when there is a card to straddle it.
 */
ob_start();
cfkl_part( 'calc' );
$neo_calc = trim( ob_get_clean() );
?>
<section class="cfkl-neo-hero<?php echo '' !== $neo_calc ? ' cfkl-neo-hero--has-calc' : ''; ?>">

	<div class="cfkl-container cfkl-neo-hero__inner">

		<div class="cfkl-neo-hero__copy">

			<?php if ( '' !== $neo_tagline ) : ?>
				<p class="cfkl-neo-pill cfkl-neo-hero__eyebrow"><?php echo esc_html( $neo_tagline ); ?></p>
			<?php endif; ?>

			<h1 class="cfkl-neo-hero__title"><?php echo esc_html( $neo_heading ); ?></h1>

			<?php if ( '' !== $neo_subtext ) : ?>
				<p class="cfkl-neo-hero__text"><?php echo esc_html( $neo_subtext ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $neo_cta_label && '' !== $neo_cta_url ) : ?>
				<p class="cfkl-neo-hero__actions">
					<a class="cfkl-neo-btn cfkl-neo-btn--ink cfkl-neo-btn--lg" href="<?php echo esc_url( $neo_cta_url ); ?>">
						<span><?php echo esc_html( $neo_cta_label ); ?></span>
						<?php echo cfkl_icon( 'arrow', 'cfkl-neo-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					</a>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $neo_stats ) ) : ?>
				<ul class="cfkl-neo-hero__stats">
					<?php foreach ( $neo_stats as $neo_stat ) : ?>
						<?php
						$neo_value = isset( $neo_stat['value'] ) ? (string) $neo_stat['value'] : '';
						$neo_label = isset( $neo_stat['label'] ) ? (string) $neo_stat['label'] : '';

						if ( '' === $neo_value && '' === $neo_label ) {
							continue;
						}
						?>
						<li class="cfkl-neo-hero__stat">
							<?php if ( '' !== $neo_value ) : ?>
								<span class="cfkl-neo-hero__stat-value"><?php echo esc_html( $neo_value ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $neo_label ) : ?>
								<span class="cfkl-neo-hero__stat-label"><?php echo esc_html( $neo_label ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>

		<div class="cfkl-neo-hero__side">

			<?php if ( ! empty( $neo_image ) ) : ?>
				<div class="cfkl-neo-hero__panel">
					<?php cfkl_hero_image( 'cfkl-neo-hero__img' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( '' !== $neo_office['address'] ) : ?>
				<div class="cfkl-neo-hero__office">
					<?php echo cfkl_icon( 'pin', 'cfkl-neo-hero__office-icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<div class="cfkl-neo-hero__office-body">
						<?php if ( '' !== $neo_office['label'] ) : ?>
							<p class="cfkl-neo-hero__office-label"><?php echo esc_html( $neo_office['label'] ); ?></p>
						<?php endif; ?>
						<p class="cfkl-neo-hero__office-address"><?php echo nl2br( esc_html( $neo_office['address'] ) ); ?></p>
						<?php if ( '' !== $neo_office['cta'] && '' !== $neo_office['url'] ) : ?>
							<a class="cfkl-neo-hero__office-link" href="<?php echo esc_url( $neo_office['url'] ); ?>">
								<?php echo esc_html( $neo_office['cta'] ); ?>
							</a>
						<?php endif; ?>
					</div>
					<?php if ( '' !== $neo_office['rating'] ) : ?>
						<p class="cfkl-neo-hero__office-rating">
							<?php echo cfkl_icon( 'star', 'cfkl-neo-hero__office-star is-filled' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							<span><?php echo esc_html( $neo_office['rating'] ); ?></span>
						</p>
					<?php endif; ?>
				</div>
			<?php endif; ?>

		</div>

	</div>

	<?php if ( '' !== $neo_calc ) : ?>
		<div class="cfkl-container cfkl-neo-hero__calc">
			<?php echo $neo_calc; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- buffered template part, escaped at source. ?>
		</div>
	<?php endif; ?>

</section>
