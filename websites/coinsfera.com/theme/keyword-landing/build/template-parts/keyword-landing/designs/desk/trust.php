<?php
/**
 * DESK - counterparty notes.
 *
 * Numbered clauses down the left, one photograph plated on the right with a
 * mono caption. Nothing here is a card.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_points = cfkl_rows( 'trust_points' );
$cfkl_title  = (string) cfkl_get( 'trust_title' );
$cfkl_text   = (string) cfkl_get( 'trust_text' );
$cfkl_image  = cfkl_get( 'trust_image', array() );

if ( empty( $cfkl_points ) && '' === $cfkl_title ) {
	return;
}
?>
<section class="cfkl-desk-section cfkl-desk-trust cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Counterparty notes', 'coinsfera' ), $cfkl_title, $cfkl_text ); ?>

		<div class="cfkl-desk-trust__grid">
			<?php if ( ! empty( $cfkl_points ) ) : ?>
				<ol class="cfkl-desk-clauses">
					<?php $cfkl_index = 0; ?>
					<?php foreach ( $cfkl_points as $cfkl_point ) : ?>
						<?php
						$cfkl_point_title = isset( $cfkl_point['title'] ) ? (string) $cfkl_point['title'] : '';
						$cfkl_point_desc  = isset( $cfkl_point['desc'] ) ? (string) $cfkl_point['desc'] : '';

						if ( '' === $cfkl_point_title ) {
							continue;
						}

						$cfkl_index++;
						?>
						<li class="cfkl-desk-clauses__item">
							<span class="cfkl-desk-clauses__no"><?php echo esc_html( sprintf( '%02d', $cfkl_index ) ); ?></span>
							<h3 class="cfkl-desk-clauses__title"><?php echo esc_html( $cfkl_point_title ); ?></h3>
							<?php if ( '' !== $cfkl_point_desc ) : ?>
								<p class="cfkl-desk-clauses__text"><?php echo esc_html( $cfkl_point_desc ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ol>
			<?php endif; ?>

			<?php if ( ! empty( $cfkl_image['ID'] ) ) : ?>
				<figure class="cfkl-desk-plate">
					<?php echo cfkl_image( $cfkl_image, 'medium_large', array( 'class' => 'cfkl-desk-plate__img' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapes. ?>
					<figcaption class="cfkl-desk-plate__caption"><?php esc_html_e( 'Trading floor, Beyoğlu', 'coinsfera' ); ?></figcaption>
				</figure>
			<?php endif; ?>
		</div>
	</div>
</section>
