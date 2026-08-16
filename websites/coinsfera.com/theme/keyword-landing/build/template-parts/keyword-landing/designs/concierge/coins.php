<?php
/**
 * Concierge coins.
 *
 * Small arch-topped tiles with the ticker in serif, echoing the hero frame at a
 * quarter of the size.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title = (string) cfkl_get( 'coins_title' );
$conc_text  = (string) cfkl_get( 'coins_text' );
$conc_list  = cfkl_rows( 'coins_list' );

if ( empty( $conc_list ) ) {
	return;
}
?>
<section class="cfkl-conc-coins cfkl-reveal">
	<div class="cfkl-container cfkl-conc-coins__inner">

		<header class="cfkl-conc-head cfkl-conc-head--wide">
			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'At the desk', 'coinsfera' ); ?></p>
			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-head__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $conc_text ) : ?>
				<p class="cfkl-conc-head__text"><?php echo esc_html( $conc_text ); ?></p>
			<?php endif; ?>
		</header>

		<ul class="cfkl-conc-coinlist">
			<?php foreach ( $conc_list as $conc_coin ) : ?>
				<?php
				$conc_symbol = isset( $conc_coin['symbol'] ) ? (string) $conc_coin['symbol'] : '';
				$conc_name   = isset( $conc_coin['name'] ) ? (string) $conc_coin['name'] : '';
				$conc_url    = isset( $conc_coin['url'] ) ? (string) $conc_coin['url'] : '';

				if ( '' === $conc_symbol && '' === $conc_name ) {
					continue;
				}
				?>
				<li class="cfkl-conc-coinlist__item">
					<?php if ( '' !== $conc_url ) : ?>
						<a class="cfkl-conc-tile" href="<?php echo esc_url( $conc_url ); ?>">
							<span class="cfkl-conc-tile__sym"><?php echo esc_html( $conc_symbol ); ?></span>
							<?php if ( '' !== $conc_name ) : ?>
								<span class="cfkl-conc-tile__name"><?php echo esc_html( $conc_name ); ?></span>
							<?php endif; ?>
						</a>
					<?php else : ?>
						<span class="cfkl-conc-tile">
							<span class="cfkl-conc-tile__sym"><?php echo esc_html( $conc_symbol ); ?></span>
							<?php if ( '' !== $conc_name ) : ?>
								<span class="cfkl-conc-tile__name"><?php echo esc_html( $conc_name ); ?></span>
							<?php endif; ?>
						</span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>

	</div>
</section>
