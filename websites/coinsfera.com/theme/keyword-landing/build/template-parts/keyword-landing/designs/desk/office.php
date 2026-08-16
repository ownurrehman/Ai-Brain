<?php
/**
 * DESK - the counter.
 *
 * Address, hours and directions kept as a facts panel beside the map, with
 * opening hours in the same ledger rhythm as the fee schedule.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_title      = (string) cfkl_get( 'office_title' );
$cfkl_text       = (string) cfkl_get( 'office_text' );
$cfkl_hours      = cfkl_rows( 'office_hours' );
$cfkl_directions = cfkl_rows( 'office_directions' );
$cfkl_map        = (string) cfkl_get( 'office_map' );
$cfkl_office     = cfkl_office();

if ( '' === $cfkl_title && empty( $cfkl_hours ) && empty( $cfkl_directions ) && '' === $cfkl_map ) {
	return;
}
?>
<section class="cfkl-desk-section cfkl-desk-office cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'The counter', 'coinsfera' ), $cfkl_title, $cfkl_text ); ?>

		<div class="cfkl-desk-office__grid">
			<div class="cfkl-desk-office__facts">

				<?php if ( '' !== $cfkl_office['address'] ) : ?>
					<div class="cfkl-desk-office__block">
						<h3 class="cfkl-desk-office__label"><?php esc_html_e( 'Address', 'coinsfera' ); ?></h3>
						<p class="cfkl-desk-office__address"><?php echo esc_html( $cfkl_office['address'] ); ?></p>
						<?php if ( '' !== $cfkl_office['url'] && '' !== $cfkl_office['cta'] ) : ?>
							<a class="cfkl-desk-link" href="<?php echo esc_url( $cfkl_office['url'] ); ?>" rel="noopener" target="_blank">
								<?php echo esc_html( $cfkl_office['cta'] ); ?>
								<?php echo cfkl_icon( 'arrow', 'cfkl-desk-link__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							</a>
						<?php endif; ?>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $cfkl_hours ) ) : ?>
					<div class="cfkl-desk-office__block">
						<h3 class="cfkl-desk-office__label">
							<?php echo cfkl_icon( 'clock', 'cfkl-desk-office__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
							<?php esc_html_e( 'Trading hours', 'coinsfera' ); ?>
						</h3>
						<dl class="cfkl-desk-ledger cfkl-desk-ledger--tight">
							<?php foreach ( $cfkl_hours as $cfkl_row ) : ?>
								<?php
								$cfkl_days  = isset( $cfkl_row['days'] ) ? (string) $cfkl_row['days'] : '';
								$cfkl_hour  = isset( $cfkl_row['hours'] ) ? (string) $cfkl_row['hours'] : '';

								if ( '' === $cfkl_days ) {
									continue;
								}
								?>
								<div class="cfkl-desk-ledger__row">
									<dt class="cfkl-desk-ledger__label"><span class="cfkl-desk-ledger__item"><?php echo esc_html( $cfkl_days ); ?></span></dt>
									<dd class="cfkl-desk-ledger__value">
										<span class="cfkl-desk-ledger__amount"><?php echo esc_html( '' !== $cfkl_hour ? $cfkl_hour : '—' ); ?></span>
									</dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $cfkl_directions ) ) : ?>
					<div class="cfkl-desk-office__block">
						<h3 class="cfkl-desk-office__label"><?php esc_html_e( 'Getting here', 'coinsfera' ); ?></h3>
						<dl class="cfkl-desk-office__routes">
							<?php foreach ( $cfkl_directions as $cfkl_route ) : ?>
								<?php
								$cfkl_route_label = isset( $cfkl_route['label'] ) ? (string) $cfkl_route['label'] : '';
								$cfkl_route_desc  = isset( $cfkl_route['desc'] ) ? (string) $cfkl_route['desc'] : '';

								if ( '' === $cfkl_route_label ) {
									continue;
								}
								?>
								<div class="cfkl-desk-office__route">
									<dt><?php echo esc_html( $cfkl_route_label ); ?></dt>
									<dd><?php echo esc_html( $cfkl_route_desc ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</div>
				<?php endif; ?>
			</div>

			<?php if ( '' !== $cfkl_map ) : ?>
				<?php cfkl_shared( 'map', array( 'class' => 'cfkl-desk-map' ) ); ?>
			<?php endif; ?>
		</div>
	</div>
</section>
