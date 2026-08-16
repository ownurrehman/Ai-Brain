<?php
/**
 * DESK - instruments traded.
 *
 * A hairline matrix of tickers. Cells that carry a link are the only thing in
 * the grid that changes on hover.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_coins = cfkl_rows( 'coins_list' );

if ( empty( $cfkl_coins ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'coins_title' );
$cfkl_text  = (string) cfkl_get( 'coins_text' );
?>
<section class="cfkl-desk-section cfkl-desk-coins cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Instruments traded', 'coinsfera' ), $cfkl_title, $cfkl_text ); ?>

		<ul class="cfkl-desk-matrix">
			<?php foreach ( $cfkl_coins as $cfkl_coin ) : ?>
				<?php
				$cfkl_symbol = isset( $cfkl_coin['symbol'] ) ? (string) $cfkl_coin['symbol'] : '';
				$cfkl_name   = isset( $cfkl_coin['name'] ) ? (string) $cfkl_coin['name'] : '';
				$cfkl_url    = isset( $cfkl_coin['url'] ) ? (string) $cfkl_coin['url'] : '';

				if ( '' === $cfkl_symbol && '' === $cfkl_name ) {
					continue;
				}
				?>
				<li class="cfkl-desk-matrix__cell">
					<?php if ( '' !== $cfkl_url ) : ?>
						<a class="cfkl-desk-matrix__link" href="<?php echo esc_url( $cfkl_url ); ?>">
							<span class="cfkl-desk-matrix__symbol"><?php echo esc_html( $cfkl_symbol ); ?></span>
							<span class="cfkl-desk-matrix__name"><?php echo esc_html( $cfkl_name ); ?></span>
						</a>
					<?php else : ?>
						<span class="cfkl-desk-matrix__symbol"><?php echo esc_html( $cfkl_symbol ); ?></span>
						<span class="cfkl-desk-matrix__name"><?php echo esc_html( $cfkl_name ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</section>
