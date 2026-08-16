<?php
/**
 * Swiss Ledger - coins traded, set as an index.
 *
 * Ruled rows in a multi-column index: ticker as a micro-label, name as the
 * entry. Rows with a link become one large hit area; rows without stay plain
 * text rather than pretending to be interactive.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_list = cfkl_rows( 'coins_list' );

if ( empty( $cfkl_list ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'coins_title' );
$cfkl_text  = (string) cfkl_get( 'coins_text' );

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-coins-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, $cfkl_text, 'cfkl-ldg-coins-title' ); ?>

		<div class="cfkl-ldg-grid">
			<ul class="cfkl-ldg-index">
				<?php foreach ( $cfkl_list as $cfkl_coin ) : ?>
					<?php
					$cfkl_symbol = isset( $cfkl_coin['symbol'] ) ? (string) $cfkl_coin['symbol'] : '';
					$cfkl_name   = isset( $cfkl_coin['name'] ) ? (string) $cfkl_coin['name'] : '';
					$cfkl_url    = isset( $cfkl_coin['url'] ) ? (string) $cfkl_coin['url'] : '';

					if ( '' === $cfkl_symbol && '' === $cfkl_name ) {
						continue;
					}
					?>
					<li class="cfkl-ldg-index__row">
						<?php if ( '' !== $cfkl_url ) : ?>
							<a class="cfkl-ldg-index__link" href="<?php echo esc_url( $cfkl_url ); ?>">
								<span class="cfkl-ldg-ml cfkl-ldg-index__ticker"><?php echo esc_html( $cfkl_symbol ); ?></span>
								<span class="cfkl-ldg-index__name"><?php echo esc_html( $cfkl_name ); ?></span>
							</a>
						<?php else : ?>
							<span class="cfkl-ldg-index__link">
								<span class="cfkl-ldg-ml cfkl-ldg-index__ticker"><?php echo esc_html( $cfkl_symbol ); ?></span>
								<span class="cfkl-ldg-index__name"><?php echo esc_html( $cfkl_name ); ?></span>
							</span>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>
</section>
