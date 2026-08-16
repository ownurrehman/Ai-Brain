<?php
/**
 * DESK - what to bring.
 *
 * A checklist, ruled like a form a customer signs rather than a card grid.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_cards = cfkl_rows( 'req_cards' );

if ( empty( $cfkl_cards ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'req_title' );
$cfkl_text  = (string) cfkl_get( 'req_text' );
?>
<section class="cfkl-desk-section cfkl-desk-req cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Before you visit', 'coinsfera' ), $cfkl_title, $cfkl_text ); ?>

		<ul class="cfkl-desk-check">
			<?php foreach ( $cfkl_cards as $cfkl_card ) : ?>
				<?php
				$cfkl_card_title = isset( $cfkl_card['title'] ) ? (string) $cfkl_card['title'] : '';
				$cfkl_card_desc  = isset( $cfkl_card['desc'] ) ? (string) $cfkl_card['desc'] : '';

				if ( '' === $cfkl_card_title ) {
					continue;
				}
				?>
				<li class="cfkl-desk-check__item">
					<span class="cfkl-desk-check__box" aria-hidden="true"><?php echo cfkl_icon( 'check', 'cfkl-desk-check__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?></span>
					<h3 class="cfkl-desk-check__title"><?php echo esc_html( $cfkl_card_title ); ?></h3>
					<?php if ( '' !== $cfkl_card_desc ) : ?>
						<p class="cfkl-desk-check__text"><?php echo esc_html( $cfkl_card_desc ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
