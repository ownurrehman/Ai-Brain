<?php
/**
 * Concierge requirements - what to bring.
 *
 * Three notes in a wide, airy row, each opened by a gold hairline and a small
 * icon. Read as a packing list rather than as feature cards.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title = (string) cfkl_get( 'req_title' );
$conc_text  = (string) cfkl_get( 'req_text' );
$conc_cards = cfkl_rows( 'req_cards' );

if ( empty( $conc_cards ) && '' === $conc_title ) {
	return;
}
?>
<section class="cfkl-conc-req cfkl-reveal">
	<div class="cfkl-container cfkl-conc-req__inner">

		<header class="cfkl-conc-head cfkl-conc-head--wide">
			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'Before you come', 'coinsfera' ); ?></p>
			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-head__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $conc_text ) : ?>
				<p class="cfkl-conc-head__text"><?php echo esc_html( $conc_text ); ?></p>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $conc_cards ) ) : ?>
			<ul class="cfkl-conc-bring">
				<?php foreach ( $conc_cards as $conc_card ) : ?>
					<?php
					$conc_card_title = isset( $conc_card['title'] ) ? (string) $conc_card['title'] : '';
					$conc_card_text  = isset( $conc_card['desc'] ) ? (string) $conc_card['desc'] : '';
					$conc_card_image = isset( $conc_card['image'] ) ? $conc_card['image'] : array();

					if ( '' === $conc_card_title && '' === $conc_card_text ) {
						continue;
					}
					?>
					<li class="cfkl-conc-bring__item">
						<?php if ( ! empty( $conc_card_image['ID'] ) ) : ?>
							<span class="cfkl-conc-bring__icon">
								<?php
								echo cfkl_image( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapes.
									$conc_card_image,
									'thumbnail',
									array( 'class' => 'cfkl-conc-bring__img' )
								);
								?>
							</span>
						<?php endif; ?>
						<?php if ( '' !== $conc_card_title ) : ?>
							<h3 class="cfkl-conc-bring__title"><?php echo esc_html( $conc_card_title ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $conc_card_text ) : ?>
							<p class="cfkl-conc-bring__text"><?php echo esc_html( $conc_card_text ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
