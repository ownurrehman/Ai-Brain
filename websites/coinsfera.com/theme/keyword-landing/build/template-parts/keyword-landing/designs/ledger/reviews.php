<?php
/**
 * Swiss Ledger - reviews as citations.
 *
 * The rating is one large numeral with the count set as small print beneath it.
 * The quotes themselves are footnote sized, in quotation marks supplied by CSS,
 * attributed with an em dash. Nothing is boxed and nothing is starred.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_items  = cfkl_rows( 'reviews_items' );
$cfkl_rating = (string) cfkl_get( 'reviews_rating' );
$cfkl_count  = (string) cfkl_get( 'reviews_count' );
$cfkl_url    = (string) cfkl_get( 'reviews_url' );
$cfkl_title  = (string) cfkl_get( 'reviews_title' );

if ( empty( $cfkl_items ) && '' === $cfkl_rating ) {
	return;
}

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-reviews-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, '', 'cfkl-ldg-reviews-title' ); ?>

		<div class="cfkl-ldg-grid">

			<?php if ( '' !== $cfkl_rating ) : ?>
				<div class="cfkl-ldg-rating">
					<p class="cfkl-ldg-num"><?php echo esc_html( $cfkl_rating ); ?></p>
					<?php if ( '' !== $cfkl_count ) : ?>
						<p class="cfkl-ldg-smallprint">
							<?php
							printf(
								/* translators: %s: number of Google reviews. */
								esc_html__( 'from %s Google reviews', 'coinsfera' ),
								esc_html( $cfkl_count )
							);
							?>
						</p>
					<?php endif; ?>
					<?php if ( '' !== $cfkl_url ) : ?>
						<a class="cfkl-ldg-link" href="<?php echo esc_url( $cfkl_url ); ?>">
							<?php esc_html_e( 'Read the reviews', 'coinsfera' ); ?>
						</a>
					<?php endif; ?>
				</div>
			<?php endif; ?>

			<?php if ( ! empty( $cfkl_items ) ) : ?>
				<div class="cfkl-ldg-citations">
					<?php foreach ( $cfkl_items as $cfkl_item ) : ?>
						<?php
						$cfkl_text = isset( $cfkl_item['text'] ) ? (string) $cfkl_item['text'] : '';
						$cfkl_name = isset( $cfkl_item['name'] ) ? (string) $cfkl_item['name'] : '';
						$cfkl_meta = isset( $cfkl_item['meta'] ) ? (string) $cfkl_item['meta'] : '';

						if ( '' === $cfkl_text ) {
							continue;
						}
						?>
						<figure class="cfkl-ldg-citation">
							<blockquote class="cfkl-ldg-citation__quote">
								<p><?php echo esc_html( $cfkl_text ); ?></p>
							</blockquote>
							<?php if ( '' !== $cfkl_name || '' !== $cfkl_meta ) : ?>
								<figcaption class="cfkl-ldg-citation__by">
									<?php if ( '' !== $cfkl_name ) : ?>
										<cite class="cfkl-ldg-citation__name"><?php echo esc_html( $cfkl_name ); ?></cite>
									<?php endif; ?>
									<?php if ( '' !== $cfkl_meta ) : ?>
										<span class="cfkl-ldg-ml"><?php echo esc_html( $cfkl_meta ); ?></span>
									<?php endif; ?>
								</figcaption>
							<?php endif; ?>
						</figure>
					<?php endforeach; ?>
				</div>
			<?php endif; ?>

		</div>

	</div>
</section>
