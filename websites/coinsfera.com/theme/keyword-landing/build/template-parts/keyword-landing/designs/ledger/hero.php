<?php
/**
 * Swiss Ledger - hero.
 *
 * Type only. The eyebrow sits above a full width rule, the headline is the one
 * enormous element on the page, and the proof points are a row of figures
 * divided by hairlines. The hero image is deliberately absent here; it appears
 * further down as a full bleed plate.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_tagline = (string) cfkl_get( 'banner_tagline' );
$cfkl_heading = (string) cfkl_get( 'banner_heading' );
$cfkl_subtext = (string) cfkl_get( 'banner_subtext' );
$cfkl_label   = (string) cfkl_get( 'banner_cta_label' );
$cfkl_url     = (string) cfkl_get( 'banner_cta_url' );
$cfkl_stats   = cfkl_rows( 'banner_stats' );

if ( '' === $cfkl_heading ) {
	return;
}

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-hero" aria-labelledby="cfkl-ldg-hero-title">
	<div class="cfkl-container">

		<div class="cfkl-ldg-grid cfkl-ldg-hero__top">
			<?php cfkl_ldg_mark( $cfkl_figure ); ?>
			<?php if ( '' !== $cfkl_tagline ) : ?>
				<p class="cfkl-ldg-ml cfkl-ldg-hero__eyebrow"><?php echo esc_html( $cfkl_tagline ); ?></p>
			<?php endif; ?>
		</div>

		<h1 id="cfkl-ldg-hero-title" class="cfkl-ldg-h1"><?php echo esc_html( $cfkl_heading ); ?></h1>

		<?php if ( '' !== $cfkl_subtext || ( '' !== $cfkl_label && '' !== $cfkl_url ) ) : ?>
			<div class="cfkl-ldg-grid cfkl-ldg-hero__body">
				<div class="cfkl-ldg-hero__lede">
					<?php if ( '' !== $cfkl_subtext ) : ?>
						<p class="cfkl-ldg-body"><?php echo esc_html( $cfkl_subtext ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $cfkl_label && '' !== $cfkl_url ) : ?>
						<a class="cfkl-ldg-btn cfkl-ldg-btn--accent" href="<?php echo esc_url( $cfkl_url ); ?>">
							<?php echo esc_html( $cfkl_label ); ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<?php if ( ! empty( $cfkl_stats ) ) : ?>
			<ul class="cfkl-ldg-stats">
				<?php foreach ( $cfkl_stats as $cfkl_stat ) : ?>
					<?php
					$cfkl_value = isset( $cfkl_stat['value'] ) ? (string) $cfkl_stat['value'] : '';
					$cfkl_note  = isset( $cfkl_stat['label'] ) ? (string) $cfkl_stat['label'] : '';

					if ( '' === $cfkl_value && '' === $cfkl_note ) {
						continue;
					}
					?>
					<li class="cfkl-ldg-stat">
						<?php if ( '' !== $cfkl_value ) : ?>
							<span class="cfkl-ldg-num cfkl-ldg-num--sm"><?php echo esc_html( $cfkl_value ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $cfkl_note ) : ?>
							<span class="cfkl-ldg-ml"><?php echo esc_html( $cfkl_note ); ?></span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
