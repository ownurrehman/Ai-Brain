<?php
/**
 * Neo Fintech - the office.
 *
 * Address, hours and directions on the left as a peach card; the lazy Maps
 * embed on the right inside the same bordered, offset-shadow panel language.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_office     = cfkl_office();
$neo_hours      = cfkl_rows( 'office_hours' );
$neo_directions = cfkl_rows( 'office_directions' );
$neo_map        = (string) cfkl_get( 'office_map' );
$neo_title      = (string) cfkl_get( 'office_title' );
$neo_text       = (string) cfkl_get( 'office_text' );

if ( '' === $neo_office['address'] && empty( $neo_hours ) && '' === $neo_map ) {
	return;
}
?>
<section class="cfkl-neo-section cfkl-neo-office cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, $neo_text, array( 'align' => 'left' ) ); ?>

		<div class="cfkl-neo-office__grid">

			<div class="cfkl-neo-office__card">

				<?php if ( '' !== $neo_office['address'] ) : ?>
					<p class="cfkl-neo-office__address">
						<?php echo cfkl_icon( 'pin', 'cfkl-neo-office__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						<span><?php echo nl2br( esc_html( $neo_office['address'] ) ); ?></span>
					</p>
				<?php endif; ?>

				<?php if ( ! empty( $neo_hours ) ) : ?>
					<ul class="cfkl-neo-office__hours">
						<?php foreach ( $neo_hours as $neo_row ) : ?>
							<?php
							$neo_days  = isset( $neo_row['days'] ) ? (string) $neo_row['days'] : '';
							$neo_shift = isset( $neo_row['hours'] ) ? (string) $neo_row['hours'] : '';

							if ( '' === $neo_days && '' === $neo_shift ) {
								continue;
							}
							?>
							<li class="cfkl-neo-office__hour">
								<?php echo cfkl_icon( 'clock', 'cfkl-neo-office__icon cfkl-neo-office__icon--small' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
								<span class="cfkl-neo-office__days"><?php echo esc_html( $neo_days ); ?></span>
								<span class="cfkl-neo-office__time"><?php echo esc_html( $neo_shift ); ?></span>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( ! empty( $neo_directions ) ) : ?>
					<ul class="cfkl-neo-office__ways">
						<?php foreach ( $neo_directions as $neo_row ) : ?>
							<?php
							$neo_label = isset( $neo_row['label'] ) ? (string) $neo_row['label'] : '';
							$neo_desc  = isset( $neo_row['desc'] ) ? (string) $neo_row['desc'] : '';

							if ( '' === $neo_label && '' === $neo_desc ) {
								continue;
							}
							?>
							<li class="cfkl-neo-office__way">
								<?php if ( '' !== $neo_label ) : ?>
									<span class="cfkl-neo-pill cfkl-neo-office__way-label"><?php echo esc_html( $neo_label ); ?></span>
								<?php endif; ?>
								<?php if ( '' !== $neo_desc ) : ?>
									<span class="cfkl-neo-office__way-text"><?php echo esc_html( $neo_desc ); ?></span>
								<?php endif; ?>
							</li>
						<?php endforeach; ?>
					</ul>
				<?php endif; ?>

				<?php if ( '' !== $neo_office['cta'] && '' !== $neo_office['url'] ) : ?>
					<p class="cfkl-neo-office__actions">
						<a class="cfkl-neo-btn cfkl-neo-btn--ink" href="<?php echo esc_url( $neo_office['url'] ); ?>">
							<span><?php echo esc_html( $neo_office['cta'] ); ?></span>
							<?php echo cfkl_icon( 'arrow', 'cfkl-neo-btn__icon' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG. ?>
						</a>
					</p>
				<?php endif; ?>

			</div>

			<?php cfkl_shared( 'map', array( 'class' => 'cfkl-neo-office__map' ) ); ?>

		</div>

	</div>
</section>
