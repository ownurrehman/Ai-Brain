<?php
/**
 * DESK - counterparty feedback.
 *
 * The rating is treated as a figure, not a badge: a mono score block sits at
 * the head of a compact quote grid.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_items = cfkl_rows( 'reviews_items' );

if ( empty( $cfkl_items ) ) {
	return;
}

$cfkl_title  = (string) cfkl_get( 'reviews_title' );
$cfkl_rating = (string) cfkl_get( 'reviews_rating' );
$cfkl_count  = (string) cfkl_get( 'reviews_count' );
$cfkl_url    = (string) cfkl_get( 'reviews_url' );
?>
<section class="cfkl-desk-section cfkl-desk-reviews cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Counterparty feedback', 'coinsfera' ), $cfkl_title ); ?>

		<?php if ( '' !== $cfkl_rating || '' !== $cfkl_count ) : ?>
			<div class="cfkl-desk-score">
				<?php if ( '' !== $cfkl_rating ) : ?>
					<p class="cfkl-desk-score__value"><?php echo esc_html( $cfkl_rating ); ?></p>
				<?php endif; ?>
				<div class="cfkl-desk-score__meta">
					<p class="cfkl-desk-score__stars">
						<?php for ( $cfkl_star = 0; $cfkl_star < 5; $cfkl_star++ ) : ?>
							<?php echo cfkl_icon( 'star', 'cfkl-desk-score__star is-filled' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<?php endfor; ?>
						<span class="cfkl-sr"><?php esc_html_e( 'Rated five stars by customers on Google', 'coinsfera' ); ?></span>
					</p>
					<?php if ( '' !== $cfkl_count ) : ?>
						<p class="cfkl-desk-score__count">
							<?php
							/* translators: %s: number of Google reviews. */
							echo esc_html( sprintf( __( '%s Google reviews', 'coinsfera' ), $cfkl_count ) );
							?>
						</p>
					<?php endif; ?>
					<?php if ( '' !== $cfkl_url ) : ?>
						<a class="cfkl-desk-link" href="<?php echo esc_url( $cfkl_url ); ?>" rel="noopener" target="_blank">
							<?php esc_html_e( 'Read them on Google', 'coinsfera' ); ?>
							<?php echo cfkl_icon( 'arrow', 'cfkl-desk-link__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</a>
					<?php endif; ?>
				</div>
			</div>
		<?php endif; ?>

		<ul class="cfkl-desk-quotes">
			<?php foreach ( $cfkl_items as $cfkl_item ) : ?>
				<?php
				$cfkl_quote = isset( $cfkl_item['text'] ) ? (string) $cfkl_item['text'] : '';
				$cfkl_name  = isset( $cfkl_item['name'] ) ? (string) $cfkl_item['name'] : '';
				$cfkl_meta  = isset( $cfkl_item['meta'] ) ? (string) $cfkl_item['meta'] : '';

				if ( '' === $cfkl_quote ) {
					continue;
				}
				?>
				<li class="cfkl-desk-quotes__item">
					<figure class="cfkl-desk-quotes__figure">
						<blockquote class="cfkl-desk-quotes__text"><?php echo esc_html( $cfkl_quote ); ?></blockquote>
						<figcaption class="cfkl-desk-quotes__by">
							<?php if ( '' !== $cfkl_name ) : ?>
								<span class="cfkl-desk-quotes__name"><?php echo esc_html( $cfkl_name ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $cfkl_meta ) : ?>
								<span class="cfkl-desk-quotes__meta"><?php echo esc_html( $cfkl_meta ); ?></span>
							<?php endif; ?>
						</figcaption>
					</figure>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
