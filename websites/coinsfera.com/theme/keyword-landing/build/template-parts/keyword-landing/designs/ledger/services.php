<?php
/**
 * Swiss Ledger - cross references.
 *
 * The other services read as a see-also list at the end of a chapter: ruled
 * rows, sub-referenced against this section's figure, with a hairline arrow at
 * the right edge instead of a card.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_items = cfkl_rows( 'services' );

if ( empty( $cfkl_items ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'services_title' );

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-services-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, '', 'cfkl-ldg-services-title' ); ?>

		<div class="cfkl-ldg-grid">
			<ul class="cfkl-ldg-refs">
				<?php foreach ( $cfkl_items as $cfkl_index => $cfkl_item ) : ?>
					<?php
					$cfkl_label = isset( $cfkl_item['title'] ) ? (string) $cfkl_item['title'] : '';
					$cfkl_desc  = isset( $cfkl_item['desc'] ) ? (string) $cfkl_item['desc'] : '';
					$cfkl_url   = isset( $cfkl_item['url'] ) ? (string) $cfkl_item['url'] : '';

					if ( '' === $cfkl_label ) {
						continue;
					}
					?>
					<li class="cfkl-ldg-refs__row">
						<?php if ( '' !== $cfkl_url ) : ?>
							<a class="cfkl-ldg-refs__link" href="<?php echo esc_url( $cfkl_url ); ?>">
								<span class="cfkl-ldg-ml cfkl-ldg-refs__ref" aria-hidden="true">
									<?php echo esc_html( sprintf( '%1$s.%2$d', $cfkl_figure, $cfkl_index + 1 ) ); ?>
								</span>
								<span class="cfkl-ldg-refs__body">
									<span class="cfkl-ldg-refs__title"><?php echo esc_html( $cfkl_label ); ?></span>
									<?php if ( '' !== $cfkl_desc ) : ?>
										<span class="cfkl-ldg-body cfkl-ldg-body--sm"><?php echo esc_html( $cfkl_desc ); ?></span>
									<?php endif; ?>
								</span>
								<?php echo cfkl_icon( 'arrow', 'cfkl-ldg-refs__arrow' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup. ?>
							</a>
						<?php else : ?>
							<span class="cfkl-ldg-refs__link">
								<span class="cfkl-ldg-ml cfkl-ldg-refs__ref" aria-hidden="true">
									<?php echo esc_html( sprintf( '%1$s.%2$d', $cfkl_figure, $cfkl_index + 1 ) ); ?>
								</span>
								<span class="cfkl-ldg-refs__body">
									<span class="cfkl-ldg-refs__title"><?php echo esc_html( $cfkl_label ); ?></span>
									<?php if ( '' !== $cfkl_desc ) : ?>
										<span class="cfkl-ldg-body cfkl-ldg-body--sm"><?php echo esc_html( $cfkl_desc ); ?></span>
									<?php endif; ?>
								</span>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>
</section>
