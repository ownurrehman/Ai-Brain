<?php
/**
 * Concierge FAQ.
 *
 * Native details/summary rows, separated by hairlines instead of boxed, with the
 * question in serif and the open/close mark drawn in CSS as two thin strokes.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title = (string) cfkl_get( 'faq_title' );
$conc_items = cfkl_rows( 'faq_items' );

if ( empty( $conc_items ) ) {
	return;
}
?>
<section class="cfkl-conc-faq cfkl-reveal">
	<div class="cfkl-container cfkl-conc-faq__inner">

		<header class="cfkl-conc-head">
			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'Before you ask', 'coinsfera' ); ?></p>
			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-head__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>
		</header>

		<div class="cfkl-conc-questions">
			<?php foreach ( $conc_items as $conc_item ) : ?>
				<?php
				$conc_question = isset( $conc_item['title'] ) ? (string) $conc_item['title'] : '';
				$conc_answer   = isset( $conc_item['desc'] ) ? (string) $conc_item['desc'] : '';

				if ( '' === $conc_question || '' === $conc_answer ) {
					continue;
				}
				?>
				<details class="cfkl-conc-question">
					<summary class="cfkl-conc-question__q">
						<span><?php echo esc_html( $conc_question ); ?></span>
					</summary>
					<div class="cfkl-conc-question__a">
						<p><?php echo esc_html( $conc_answer ); ?></p>
					</div>
				</details>
			<?php endforeach; ?>
		</div>

	</div>
</section>
