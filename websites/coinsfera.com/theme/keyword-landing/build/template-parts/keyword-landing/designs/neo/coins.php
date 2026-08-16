<?php
/**
 * Neo Fintech - coins we trade.
 *
 * Chunky ticker pills. Each one links out when the editor supplied a URL and
 * stays a plain pill when they did not.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_items = cfkl_rows( 'coins_list' );

if ( empty( $neo_items ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'coins_title' );
$neo_text  = (string) cfkl_get( 'coins_text' );
?>
<section class="cfkl-neo-section cfkl-neo-coins cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, $neo_text, array( 'align' => 'left' ) ); ?>

		<ul class="cfkl-neo-coins__list">
			<?php foreach ( $neo_items as $neo_item ) : ?>
				<?php
				$neo_symbol = isset( $neo_item['symbol'] ) ? (string) $neo_item['symbol'] : '';
				$neo_name   = isset( $neo_item['name'] ) ? (string) $neo_item['name'] : '';
				$neo_url    = isset( $neo_item['url'] ) ? (string) $neo_item['url'] : '';

				if ( '' === $neo_symbol && '' === $neo_name ) {
					continue;
				}

				$neo_tag = '' !== $neo_url ? 'a' : 'span';
				?>
				<li class="cfkl-neo-coins__item">
					<<?php echo esc_attr( $neo_tag ); ?> class="cfkl-neo-coin"<?php echo '' !== $neo_url ? ' href="' . esc_url( $neo_url ) . '"' : ''; ?>>
						<?php if ( '' !== $neo_symbol ) : ?>
							<span class="cfkl-neo-coin__sym"><?php echo esc_html( $neo_symbol ); ?></span>
						<?php endif; ?>
						<?php if ( '' !== $neo_name ) : ?>
							<span class="cfkl-neo-coin__name"><?php echo esc_html( $neo_name ); ?></span>
						<?php endif; ?>
					</<?php echo esc_attr( $neo_tag ); ?>>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
