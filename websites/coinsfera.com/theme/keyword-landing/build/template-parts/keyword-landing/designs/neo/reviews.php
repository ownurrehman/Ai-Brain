<?php
/**
 * Neo Fintech - reviews, led by the number.
 *
 * The rating is set as a display figure, so it is hidden from assistive tech
 * and replaced by a sentence that actually says what it means.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_rating = (string) cfkl_get( 'reviews_rating' );
$neo_count  = (string) cfkl_get( 'reviews_count' );
$neo_url    = (string) cfkl_get( 'reviews_url' );
$neo_items  = cfkl_rows( 'reviews_items' );
$neo_title  = (string) cfkl_get( 'reviews_title' );

if ( '' === $neo_rating && empty( $neo_items ) ) {
	return;
}
?>
<section class="cfkl-neo-section cfkl-neo-reviews cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, '', array( 'align' => 'left' ) ); ?>

		<div class="cfkl-neo-reviews__score">

			<?php if ( '' !== $neo_rating ) : ?>
				<p class="cfkl-neo-reviews__figure" aria-hidden="true"><?php echo esc_html( $neo_rating ); ?></p>
				<p class="cfkl-sr">
					<?php
					printf(
						/* translators: %s: Google rating, for example 4.9. */
						esc_html__( 'Rated %s out of 5 on Google.', 'coinsfera' ),
						esc_html( $neo_rating )
					);
					?>
				</p>
			<?php endif; ?>

			<div class="cfkl-neo-reviews__meta">
				<span class="cfkl-neo-reviews__stars" aria-hidden="true">
					<?php for ( $neo_star = 0; $neo_star < 5; $neo_star++ ) : ?>
						<?php echo cfkl_icon( 'star', 'cfkl-neo-reviews__star is-filled' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
					<?php endfor; ?>
				</span>

				<?php if ( '' !== $neo_count ) : ?>
					<?php
					$neo_count_text = sprintf(
						/* translators: %s: number of Google reviews. */
						__( '%s Google reviews', 'coinsfera' ),
						$neo_count
					);
					?>
					<?php if ( '' !== $neo_url ) : ?>
						<a class="cfkl-neo-pill cfkl-neo-reviews__count" href="<?php echo esc_url( $neo_url ); ?>"><?php echo esc_html( $neo_count_text ); ?></a>
					<?php else : ?>
						<span class="cfkl-neo-pill cfkl-neo-reviews__count"><?php echo esc_html( $neo_count_text ); ?></span>
					<?php endif; ?>
				<?php endif; ?>
			</div>

		</div>

		<?php if ( ! empty( $neo_items ) ) : ?>
			<ul class="cfkl-neo-reviews__list">
				<?php foreach ( $neo_items as $neo_item ) : ?>
					<?php
					$neo_text = isset( $neo_item['text'] ) ? (string) $neo_item['text'] : '';
					$neo_name = isset( $neo_item['name'] ) ? (string) $neo_item['name'] : '';
					$neo_meta = isset( $neo_item['meta'] ) ? (string) $neo_item['meta'] : '';

					if ( '' === $neo_text ) {
						continue;
					}
					?>
					<li class="cfkl-neo-quote">
						<blockquote class="cfkl-neo-quote__text"><p><?php echo esc_html( $neo_text ); ?></p></blockquote>
						<?php if ( '' !== $neo_name || '' !== $neo_meta ) : ?>
							<p class="cfkl-neo-quote__by">
								<?php if ( '' !== $neo_name ) : ?>
									<span class="cfkl-neo-quote__name"><?php echo esc_html( $neo_name ); ?></span>
								<?php endif; ?>
								<?php if ( '' !== $neo_meta ) : ?>
									<span class="cfkl-neo-quote__meta"><?php echo esc_html( $neo_meta ); ?></span>
								<?php endif; ?>
							</p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		<?php endif; ?>

	</div>
</section>
