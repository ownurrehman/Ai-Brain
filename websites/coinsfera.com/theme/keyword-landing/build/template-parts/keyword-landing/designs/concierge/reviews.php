<?php
/**
 * Concierge reviews.
 *
 * Not cards. Each review is a full-width pull quote in serif italic, opened by
 * a hairline rule and signed with a micro-label, and the aggregate rating is
 * kept apart as its own quiet block.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title  = (string) cfkl_get( 'reviews_title' );
$conc_rating = (string) cfkl_get( 'reviews_rating' );
$conc_count  = (string) cfkl_get( 'reviews_count' );
$conc_url    = (string) cfkl_get( 'reviews_url' );
$conc_items  = cfkl_rows( 'reviews_items' );

if ( empty( $conc_items ) && '' === $conc_rating ) {
	return;
}
?>
<section class="cfkl-conc-reviews cfkl-reveal">
	<div class="cfkl-container cfkl-conc-reviews__inner">

		<header class="cfkl-conc-head">
			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'In their words', 'coinsfera' ); ?></p>
			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-head__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>
		</header>

		<?php if ( ! empty( $conc_items ) ) : ?>
			<div class="cfkl-conc-quotes">
				<?php foreach ( $conc_items as $conc_item ) : ?>
					<?php
					$conc_quote = isset( $conc_item['text'] ) ? (string) $conc_item['text'] : '';
					$conc_name  = isset( $conc_item['name'] ) ? (string) $conc_item['name'] : '';
					$conc_meta  = isset( $conc_item['meta'] ) ? (string) $conc_item['meta'] : '';

					if ( '' === $conc_quote ) {
						continue;
					}
					?>
					<figure class="cfkl-conc-quote">
						<blockquote class="cfkl-conc-quote__body">
							<p><?php echo esc_html( $conc_quote ); ?></p>
						</blockquote>
						<?php if ( '' !== $conc_name || '' !== $conc_meta ) : ?>
							<figcaption class="cfkl-conc-quote__by">
								<?php if ( '' !== $conc_name ) : ?>
									<span class="cfkl-conc-quote__name"><?php echo esc_html( $conc_name ); ?></span>
								<?php endif; ?>
								<?php if ( '' !== $conc_meta ) : ?>
									<span class="cfkl-conc-quote__meta"><?php echo esc_html( $conc_meta ); ?></span>
								<?php endif; ?>
							</figcaption>
						<?php endif; ?>
					</figure>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>

		<?php if ( '' !== $conc_rating ) : ?>
			<div class="cfkl-conc-score">

				<p class="cfkl-sr">
					<?php
					if ( '' !== $conc_count ) {
						echo esc_html(
							sprintf(
								/* translators: 1: rating, for example 4.9. 2: number of reviews, for example 1,043. */
								__( 'Rated %1$s out of 5 from %2$s Google reviews.', 'coinsfera' ),
								$conc_rating,
								$conc_count
							)
						);
					} else {
						echo esc_html(
							sprintf(
								/* translators: %s: rating, for example 4.9. */
								__( 'Rated %s out of 5 on Google.', 'coinsfera' ),
								$conc_rating
							)
						);
					}
					?>
				</p>

				<p class="cfkl-conc-score__value" aria-hidden="true"><?php echo esc_html( $conc_rating ); ?></p>

				<p class="cfkl-conc-stars cfkl-conc-score__stars" aria-hidden="true">
					<?php
					for ( $conc_star = 0; $conc_star < 5; $conc_star++ ) {
						echo cfkl_icon( 'star', 'cfkl-conc-star is-filled' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup.
					}
					?>
				</p>

				<?php if ( '' !== $conc_count ) : ?>
					<p class="cfkl-conc-score__count" aria-hidden="true">
						<?php
						echo esc_html(
							sprintf(
								/* translators: %s: number of reviews, for example 1,043. */
								__( '%s Google reviews', 'coinsfera' ),
								$conc_count
							)
						);
						?>
					</p>
				<?php endif; ?>

				<?php if ( '' !== $conc_url ) : ?>
					<p class="cfkl-conc-score__actions">
						<a class="cfkl-conc-link" href="<?php echo esc_url( $conc_url ); ?>"><?php esc_html_e( 'Read the reviews on Google', 'coinsfera' ); ?></a>
					</p>
				<?php endif; ?>

			</div>
		<?php endif; ?>

	</div>
</section>
