<?php
/**
 * DESK - notes and queries.
 *
 * <details> rows separated by hairlines, each numbered like a clause. The
 * open/closed marker is drawn in CSS so nothing here needs JavaScript.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_items = cfkl_rows( 'faq_items' );

if ( empty( $cfkl_items ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'faq_title' );
?>
<section class="cfkl-desk-section cfkl-desk-faq cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Notes and queries', 'coinsfera' ), $cfkl_title ); ?>

		<div class="cfkl-desk-qa">
			<?php $cfkl_index = 0; ?>
			<?php foreach ( $cfkl_items as $cfkl_item ) : ?>
				<?php
				$cfkl_question = isset( $cfkl_item['title'] ) ? (string) $cfkl_item['title'] : '';
				$cfkl_answer   = isset( $cfkl_item['desc'] ) ? (string) $cfkl_item['desc'] : '';

				if ( '' === $cfkl_question || '' === $cfkl_answer ) {
					continue;
				}

				$cfkl_index++;
				?>
				<details class="cfkl-desk-qa__item">
					<summary class="cfkl-desk-qa__q">
						<span class="cfkl-desk-qa__no"><?php echo esc_html( sprintf( 'Q%02d', $cfkl_index ) ); ?></span>
						<span class="cfkl-desk-qa__text"><?php echo esc_html( $cfkl_question ); ?></span>
						<span class="cfkl-desk-qa__mark" aria-hidden="true"></span>
					</summary>
					<div class="cfkl-desk-qa__a">
						<p><?php echo esc_html( $cfkl_answer ); ?></p>
					</div>
				</details>
			<?php endforeach; ?>
		</div>
	</div>
</section>
