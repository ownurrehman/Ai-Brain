<?php
/**
 * Neo Fintech - steps as big numbered blocks.
 *
 * The numeral is the whole illustration: an oversized outlined character
 * sitting behind the title. The step icons are deliberately not rendered here,
 * because a small pictogram beside a 9rem outlined numeral reads as clutter.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_items = cfkl_rows( 'steps' );

if ( empty( $neo_items ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'steps_title' );
$neo_count = 0;
?>
<section class="cfkl-neo-section cfkl-neo-steps cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, '', array( 'align' => 'left' ) ); ?>

		<ol class="cfkl-neo-steps__list">
			<?php foreach ( $neo_items as $neo_item ) : ?>
				<?php
				$neo_item_title = isset( $neo_item['title'] ) ? (string) $neo_item['title'] : '';
				$neo_desc       = isset( $neo_item['desc'] ) ? (string) $neo_item['desc'] : '';

				if ( '' === $neo_item_title && '' === $neo_desc ) {
					continue;
				}

				++$neo_count;
				?>
				<li class="cfkl-neo-step">
					<span class="cfkl-neo-step__num" aria-hidden="true"><?php echo esc_html( sprintf( '%02d', $neo_count ) ); ?></span>
					<div class="cfkl-neo-step__body">
						<?php if ( '' !== $neo_item_title ) : ?>
							<h3 class="cfkl-neo-step__title"><?php echo esc_html( $neo_item_title ); ?></h3>
						<?php endif; ?>
						<?php if ( '' !== $neo_desc ) : ?>
							<p class="cfkl-neo-step__text"><?php echo esc_html( $neo_desc ); ?></p>
						<?php endif; ?>
					</div>
				</li>
			<?php endforeach; ?>
		</ol>

	</div>
</section>
