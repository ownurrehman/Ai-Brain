<?php
/**
 * Concierge hero.
 *
 * An arch-framed photograph carries the page and the copy sits beside it in the
 * narrower column, which is the opposite of the usual marketing split. There is
 * no calculator here on purpose: this design earns trust before it quotes.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_tagline = (string) cfkl_get( 'banner_tagline' );
$conc_heading = (string) cfkl_get( 'banner_heading' );
$conc_subtext = (string) cfkl_get( 'banner_subtext' );
$conc_label   = (string) cfkl_get( 'banner_cta_label' );
$conc_url     = (string) cfkl_get( 'banner_cta_url' );
$conc_image   = cfkl_get( 'hero_image', array() );
$conc_stats   = cfkl_rows( 'banner_stats' );
$conc_office  = cfkl_office();

/* The address is authored as a textarea, so it arrives with line breaks in it.
   The marker is a single typographic line, so collapse the whitespace. */
$conc_address = trim( preg_replace( '/\s+/', ' ', $conc_office['address'] ) );

if ( '' === $conc_heading ) {
	return;
}
?>
<section class="cfkl-conc-hero">
	<div class="cfkl-container cfkl-conc-hero__inner">

		<div class="cfkl-conc-hero__copy">

			<?php if ( '' !== $conc_tagline ) : ?>
				<p class="cfkl-conc-eyebrow"><?php echo esc_html( $conc_tagline ); ?></p>
			<?php endif; ?>

			<h1 class="cfkl-conc-hero__title"><?php echo esc_html( $conc_heading ); ?></h1>

			<?php if ( '' !== $conc_subtext ) : ?>
				<p class="cfkl-conc-hero__lede"><?php echo esc_html( $conc_subtext ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $conc_address || '' !== $conc_office['rating'] ) : ?>
				<p class="cfkl-conc-marker">

					<?php if ( '' !== $conc_address ) : ?>
						<span class="cfkl-conc-marker__place">
							<?php echo cfkl_icon( 'pin', 'cfkl-conc-marker__pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup. ?>
							<span><?php echo esc_html( $conc_address ); ?></span>
						</span>
					<?php endif; ?>

					<?php if ( '' !== $conc_office['rating'] ) : ?>
						<span class="cfkl-conc-marker__rating">
							<span class="cfkl-sr">
								<?php
								echo esc_html(
									sprintf(
										/* translators: %s: Google rating, for example 4.9. */
										__( 'Rated %s out of 5 on Google.', 'coinsfera' ),
										$conc_office['rating']
									)
								);
								?>
							</span>
							<span class="cfkl-conc-marker__score" aria-hidden="true"><?php echo esc_html( $conc_office['rating'] ); ?></span>
							<span class="cfkl-conc-stars" aria-hidden="true">
								<?php
								for ( $conc_star = 0; $conc_star < 5; $conc_star++ ) {
									echo cfkl_icon( 'star', 'cfkl-conc-star is-filled' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup.
								}
								?>
							</span>
							<span class="cfkl-conc-marker__source" aria-hidden="true"><?php esc_html_e( 'Google rating', 'coinsfera' ); ?></span>
						</span>
					<?php endif; ?>

				</p>
			<?php endif; ?>

			<?php if ( ( '' !== $conc_label && '' !== $conc_url ) || ( '' !== $conc_office['cta'] && '' !== $conc_office['url'] ) ) : ?>
				<p class="cfkl-conc-hero__actions">
					<?php if ( '' !== $conc_label && '' !== $conc_url ) : ?>
						<a class="cfkl-conc-btn" href="<?php echo esc_url( $conc_url ); ?>"><?php echo esc_html( $conc_label ); ?></a>
					<?php endif; ?>

					<?php if ( '' !== $conc_office['cta'] && '' !== $conc_office['url'] ) : ?>
						<a class="cfkl-conc-link" href="<?php echo esc_url( $conc_office['url'] ); ?>"><?php echo esc_html( $conc_office['cta'] ); ?></a>
					<?php endif; ?>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $conc_stats ) ) : ?>
				<ul class="cfkl-conc-figures">
					<?php foreach ( $conc_stats as $conc_stat ) : ?>
						<?php
						$conc_value = isset( $conc_stat['value'] ) ? (string) $conc_stat['value'] : '';
						$conc_note  = isset( $conc_stat['label'] ) ? (string) $conc_stat['label'] : '';

						if ( '' === $conc_value && '' === $conc_note ) {
							continue;
						}
						?>
						<li class="cfkl-conc-figures__item">
							<?php if ( '' !== $conc_value ) : ?>
								<span class="cfkl-conc-figures__value"><?php echo esc_html( $conc_value ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $conc_note ) : ?>
								<span class="cfkl-conc-figures__label"><?php echo esc_html( $conc_note ); ?></span>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>

		<?php if ( ! empty( $conc_image['ID'] ) ) : ?>
			<figure class="cfkl-conc-hero__figure">
				<div class="cfkl-conc-arch cfkl-conc-arch--hero">
					<?php cfkl_hero_image( 'cfkl-conc-hero__img' ); ?>
				</div>
				<?php if ( '' !== $conc_office['label'] ) : ?>
					<figcaption class="cfkl-conc-hero__caption"><?php echo esc_html( $conc_office['label'] ); ?></figcaption>
				<?php endif; ?>
			</figure>
		<?php endif; ?>

	</div>
</section>
