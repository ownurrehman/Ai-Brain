<?php
/**
 * Concierge steps.
 *
 * A vertical timeline: one gold hairline running down the left, large serif
 * numerals sitting on it as markers, and the copy in a narrow measure beside
 * them. The step icons are deliberately unused; numerals read as an itinerary,
 * icons would read as a product tour.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title = (string) cfkl_get( 'steps_title' );
$conc_steps = cfkl_rows( 'steps' );

if ( empty( $conc_steps ) ) {
	return;
}

$conc_index = 0;
?>
<section class="cfkl-conc-steps cfkl-reveal">
	<div class="cfkl-container cfkl-conc-steps__inner">

		<header class="cfkl-conc-head">
			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'Your itinerary', 'coinsfera' ); ?></p>
			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-head__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>
		</header>

		<ol class="cfkl-conc-timeline">
			<?php foreach ( $conc_steps as $conc_step ) : ?>
				<?php
				$conc_step_title = isset( $conc_step['title'] ) ? (string) $conc_step['title'] : '';
				$conc_step_text  = isset( $conc_step['desc'] ) ? (string) $conc_step['desc'] : '';

				if ( '' === $conc_step_title && '' === $conc_step_text ) {
					continue;
				}

				$conc_index++;
				?>
				<li class="cfkl-conc-timeline__item">
					<span class="cfkl-conc-timeline__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $conc_index ) ); ?></span>
					<div class="cfkl-conc-timeline__body">
						<?php if ( '' !== $conc_step_title ) : ?>
							<h3 class="cfkl-conc-timeline__title"><?php echo esc_html( $conc_step_title ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $conc_step_text ) : ?>
							<p class="cfkl-conc-timeline__text"><?php echo esc_html( $conc_step_text ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>

	</div>
</section>
