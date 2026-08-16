<?php
/**
 * Swiss Ledger - the office.
 *
 * Three columns divided by real vertical hairlines: the address and rating, the
 * opening hours as ruled rows, and how to get here. The map sits below at the
 * full content width, square cornered, framed by a single rule.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_office     = cfkl_office();
$cfkl_hours      = cfkl_rows( 'office_hours' );
$cfkl_directions = cfkl_rows( 'office_directions' );
$cfkl_map        = (string) cfkl_get( 'office_map' );
$cfkl_title      = (string) cfkl_get( 'office_title' );
$cfkl_text       = (string) cfkl_get( 'office_text' );

if ( '' === $cfkl_office['address'] && empty( $cfkl_hours ) && empty( $cfkl_directions ) && '' === $cfkl_map ) {
	return;
}

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-office-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, $cfkl_text, 'cfkl-ldg-office-title' ); ?>

		<div class="cfkl-ldg-grid">
			<div class="cfkl-ldg-office">

				<div class="cfkl-ldg-office__col">
					<p class="cfkl-ldg-ml">
						<?php
						echo '' !== $cfkl_office['label']
							? esc_html( $cfkl_office['label'] )
							: esc_html__( 'Address', 'coinsfera' );
						?>
					</p>
					<?php if ( '' !== $cfkl_office['address'] ) : ?>
						<p class="cfkl-ldg-office__address"><?php echo esc_html( $cfkl_office['address'] ); ?></p>
					<?php endif; ?>

					<?php if ( '' !== $cfkl_office['rating'] ) : ?>
						<p class="cfkl-ldg-office__rating">
							<span class="cfkl-ldg-num cfkl-ldg-num--sm"><?php echo esc_html( $cfkl_office['rating'] ); ?></span>
							<span class="cfkl-ldg-ml"><?php esc_html_e( 'Google rating', 'coinsfera' ); ?></span>
						</p>
					<?php endif; ?>

					<?php if ( '' !== $cfkl_office['url'] && '' !== $cfkl_office['cta'] ) : ?>
						<a class="cfkl-ldg-link" href="<?php echo esc_url( $cfkl_office['url'] ); ?>">
							<?php echo esc_html( $cfkl_office['cta'] ); ?>
						</a>
					<?php endif; ?>
				</div>

				<?php if ( ! empty( $cfkl_hours ) ) : ?>
					<div class="cfkl-ldg-office__col">
						<p class="cfkl-ldg-ml"><?php esc_html_e( 'Opening hours', 'coinsfera' ); ?></p>
						<dl class="cfkl-ldg-hours">
							<?php foreach ( $cfkl_hours as $cfkl_row ) : ?>
								<?php
								$cfkl_days  = isset( $cfkl_row['days'] ) ? (string) $cfkl_row['days'] : '';
								$cfkl_span  = isset( $cfkl_row['hours'] ) ? (string) $cfkl_row['hours'] : '';

								if ( '' === $cfkl_days && '' === $cfkl_span ) {
									continue;
								}
								?>
								<div class="cfkl-ldg-hours__row">
									<dt class="cfkl-ldg-hours__days"><?php echo esc_html( $cfkl_days ); ?></dt>
									<dd class="cfkl-ldg-hours__span"><?php echo esc_html( $cfkl_span ); ?></dd>
								</div>
							<?php endforeach; ?>
						</dl>
					</div>
				<?php endif; ?>

				<?php if ( ! empty( $cfkl_directions ) ) : ?>
					<div class="cfkl-ldg-office__col">
						<p class="cfkl-ldg-ml"><?php esc_html_e( 'Getting here', 'coinsfera' ); ?></p>
						<ul class="cfkl-ldg-directions">
							<?php foreach ( $cfkl_directions as $cfkl_row ) : ?>
								<?php
								$cfkl_mode = isset( $cfkl_row['label'] ) ? (string) $cfkl_row['label'] : '';
								$cfkl_desc = isset( $cfkl_row['desc'] ) ? (string) $cfkl_row['desc'] : '';

								if ( '' === $cfkl_mode && '' === $cfkl_desc ) {
									continue;
								}
								?>
								<li class="cfkl-ldg-directions__row">
									<?php if ( '' !== $cfkl_mode ) : ?>
										<span class="cfkl-ldg-ml"><?php echo esc_html( $cfkl_mode ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== $cfkl_desc ) : ?>
										<span class="cfkl-ldg-body cfkl-ldg-body--sm"><?php echo esc_html( $cfkl_desc ); ?></span>
									<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				<?php endif; ?>

			</div>

			<?php if ( '' !== $cfkl_map ) : ?>
				<?php cfkl_shared( 'map', array( 'class' => 'cfkl-ldg-map' ) ); ?>
			<?php endif; ?>
		</div>

	</div>
</section>
