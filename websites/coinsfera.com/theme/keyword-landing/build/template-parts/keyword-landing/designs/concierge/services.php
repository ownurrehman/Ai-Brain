<?php
/**
 * Concierge services.
 *
 * An index of the other things handled at the same desk, set as hairline-ruled
 * rows with a serif title and a small terracotta arrow. No cards, no icons: a
 * contents page, not a product grid.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title = (string) cfkl_get( 'services_title' );
$conc_items = cfkl_rows( 'services' );

if ( empty( $conc_items ) ) {
	return;
}
?>
<section class="cfkl-conc-services cfkl-reveal">
	<div class="cfkl-container cfkl-conc-services__inner">

		<header class="cfkl-conc-head">
			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'Also arranged here', 'coinsfera' ); ?></p>
			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-head__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>
		</header>

		<ul class="cfkl-conc-index">
			<?php foreach ( $conc_items as $conc_item ) : ?>
				<?php
				$conc_item_title = isset( $conc_item['title'] ) ? (string) $conc_item['title'] : '';
				$conc_item_text  = isset( $conc_item['desc'] ) ? (string) $conc_item['desc'] : '';
				$conc_item_url   = isset( $conc_item['url'] ) ? (string) $conc_item['url'] : '';

				if ( '' === $conc_item_title ) {
					continue;
				}
				?>
				<li class="cfkl-conc-index__item">
					<?php if ( '' !== $conc_item_url ) : ?>
						<a class="cfkl-conc-index__link" href="<?php echo esc_url( $conc_item_url ); ?>">
							<span class="cfkl-conc-index__body">
								<span class="cfkl-conc-index__title"><?php echo esc_html( $conc_item_title ); ?></span>
								<?php if ( '' !== $conc_item_text ) : ?>
									<span class="cfkl-conc-index__text"><?php echo esc_html( $conc_item_text ); ?></span>
								<?php endif; ?>
							</span>
							<?php echo cfkl_icon( 'arrow', 'cfkl-conc-index__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup. ?>
						</a>
					<?php else : ?>
						<span class="cfkl-conc-index__link">
							<span class="cfkl-conc-index__body">
								<span class="cfkl-conc-index__title"><?php echo esc_html( $conc_item_title ); ?></span>
								<?php if ( '' !== $conc_item_text ) : ?>
									<span class="cfkl-conc-index__text"><?php echo esc_html( $conc_item_text ); ?></span>
								<?php endif; ?>
							</span>
						</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
