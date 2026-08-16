<?php
/**
 * Concierge trust section.
 *
 * A photograph held in the wide column with the argument set beside it as three
 * numbered notes separated by gold hairlines, rather than as a card grid.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title  = (string) cfkl_get( 'trust_title' );
$conc_text   = (string) cfkl_get( 'trust_text' );
$conc_image  = cfkl_get( 'trust_image', array() );
$conc_points = cfkl_rows( 'trust_points' );

if ( '' === $conc_title && empty( $conc_points ) ) {
	return;
}
?>
<section class="cfkl-conc-trust cfkl-reveal">
	<div class="cfkl-container cfkl-conc-trust__inner">

		<div class="cfkl-conc-trust__copy">

			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'Why a room, not an app', 'coinsfera' ); ?></p>

			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-trust__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>

			<?php if ( '' !== $conc_text ) : ?>
				<p class="cfkl-conc-lede"><?php echo esc_html( $conc_text ); ?></p>
			<?php endif; ?>

			<?php if ( ! empty( $conc_points ) ) : ?>
				<ul class="cfkl-conc-points">
					<?php foreach ( $conc_points as $conc_point ) : ?>
						<?php
						$conc_point_title = isset( $conc_point['title'] ) ? (string) $conc_point['title'] : '';
						$conc_point_text  = isset( $conc_point['desc'] ) ? (string) $conc_point['desc'] : '';

						if ( '' === $conc_point_title && '' === $conc_point_text ) {
							continue;
						}
						?>
						<li class="cfkl-conc-points__item">
							<?php if ( '' !== $conc_point_title ) : ?>
								<h3 class="cfkl-conc-points__title">
									<?php echo cfkl_icon( 'check', 'cfkl-conc-points__mark' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup. ?>
									<span><?php echo esc_html( $conc_point_title ); ?></span>
								</h3>
							<?php endif; ?>
							<?php if ( '' !== $conc_point_text ) : ?>
								<p class="cfkl-conc-points__text"><?php echo esc_html( $conc_point_text ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

		</div>

		<?php if ( ! empty( $conc_image['ID'] ) ) : ?>
			<figure class="cfkl-conc-trust__figure">
				<?php
				echo cfkl_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapes.
					$conc_image,
					'large',
					array( 'class' => 'cfkl-conc-trust__img' )
				);
				?>
			</figure>
		<?php endif; ?>

	</div>
</section>
