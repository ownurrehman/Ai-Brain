<?php
/**
 * Neo Fintech - FAQ.
 *
 * Native disclosure widgets, so every answer is reachable without JavaScript.
 * The plus sign is drawn in CSS from two gradient bars and rotates to a cross
 * when the block opens.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_items = cfkl_rows( 'faq_items' );

if ( empty( $neo_items ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'faq_title' );
?>
<section class="cfkl-neo-section cfkl-neo-faq cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, '', array( 'align' => 'left' ) ); ?>

		<div class="cfkl-neo-faq__list">
			<?php foreach ( $neo_items as $neo_item ) : ?>
				<?php
				$neo_question = isset( $neo_item['title'] ) ? (string) $neo_item['title'] : '';
				$neo_answer   = isset( $neo_item['desc'] ) ? (string) $neo_item['desc'] : '';

				if ( '' === $neo_question || '' === $neo_answer ) {
					continue;
				}
				?>
				<details class="cfkl-neo-faq__item">
					<summary class="cfkl-neo-faq__q">
						<span class="cfkl-neo-faq__q-text"><?php echo esc_html( $neo_question ); ?></span>
					</summary>
					<div class="cfkl-neo-faq__a">
						<p><?php echo esc_html( $neo_answer ); ?></p>
					</div>
				</details>
			<?php endforeach; ?>
		</div>

	</div>
</section>
