<?php
/**
 * Swiss Ledger - questions.
 *
 * A numbered list of ruled rows. Each row is a native details/summary so it
 * opens without JavaScript; the marker is a thin plus drawn in CSS at the right
 * edge that loses its upright stroke when the row is open. The ordered list
 * carries the numbering for assistive technology, so the printed figures are
 * decorative.
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

$cfkl_figure = cfkl_ldg_figure();
$cfkl_n      = 0;
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-faq-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, '', 'cfkl-ldg-faq-title' ); ?>

		<div class="cfkl-ldg-grid">
			<ol class="cfkl-ldg-faq">
				<?php foreach ( $cfkl_items as $cfkl_item ) : ?>
					<?php
					$cfkl_q = isset( $cfkl_item['title'] ) ? (string) $cfkl_item['title'] : '';
					$cfkl_a = isset( $cfkl_item['desc'] ) ? (string) $cfkl_item['desc'] : '';

					if ( '' === $cfkl_q ) {
						continue;
					}

					$cfkl_n++;
					?>
					<li class="cfkl-ldg-faq__item">
						<details class="cfkl-ldg-faq__details">
							<summary class="cfkl-ldg-faq__summary">
								<span class="cfkl-ldg-ml cfkl-ldg-faq__ref" aria-hidden="true">
									<?php echo esc_html( str_pad( (string) $cfkl_n, 2, '0', STR_PAD_LEFT ) ); ?>
								</span>
								<span class="cfkl-ldg-faq__q"><?php echo esc_html( $cfkl_q ); ?></span>
								<span class="cfkl-ldg-faq__mark" aria-hidden="true"></span>
							</summary>
							<?php if ( '' !== $cfkl_a ) : ?>
								<div class="cfkl-ldg-faq__a">
									<p class="cfkl-ldg-body cfkl-ldg-body--sm"><?php echo esc_html( $cfkl_a ); ?></p>
								</div>
							<?php endif; ?>
						</details>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>

	</div>
</section>
