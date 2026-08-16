<?php
/**
 * Concierge office section - "your visit".
 *
 * The map is framed as a shallow arch on one side; on the other the practical
 * details a visitor actually needs, with the opening hours set as a definition
 * list with dot leaders the way a printed timetable would set them.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title      = (string) cfkl_get( 'office_title' );
$conc_text       = (string) cfkl_get( 'office_text' );
$conc_hours      = cfkl_rows( 'office_hours' );
$conc_directions = cfkl_rows( 'office_directions' );
$conc_map        = (string) cfkl_get( 'office_map' );
$conc_office     = cfkl_office();
$conc_address    = trim( preg_replace( '/\s+/', ' ', $conc_office['address'] ) );

if ( '' === $conc_title && '' === $conc_map && empty( $conc_hours ) && empty( $conc_directions ) ) {
	return;
}
?>
<section class="cfkl-conc-office cfkl-reveal">
	<div class="cfkl-container cfkl-conc-office__inner">

		<?php if ( '' !== $conc_map ) : ?>
			<div class="cfkl-conc-office__visual">
				<div class="cfkl-conc-arch cfkl-conc-arch--map">
					<?php cfkl_shared( 'map', array( 'class' => 'cfkl-conc-map' ) ); ?>
				</div>
			</div>
		<?php endif; ?>

		<div class="cfkl-conc-office__detail">

			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'Your visit', 'coinsfera' ); ?></p>

			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-office__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>

			<?php if ( '' !== $conc_text ) : ?>
				<p class="cfkl-conc-lede"><?php echo esc_html( $conc_text ); ?></p>
			<?php endif; ?>

			<?php if ( '' !== $conc_address ) : ?>
				<p class="cfkl-conc-office__address">
					<?php echo cfkl_icon( 'pin', 'cfkl-conc-office__pin' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- cfkl_icon returns escaped markup. ?>
					<span><?php echo esc_html( $conc_address ); ?></span>
				</p>
			<?php endif; ?>

			<?php if ( ! empty( $conc_hours ) ) : ?>
				<h3 class="cfkl-conc-office__sub"><?php esc_html_e( 'Opening hours', 'coinsfera' ); ?></h3>
				<dl class="cfkl-conc-hours">
					<?php foreach ( $conc_hours as $conc_row ) : ?>
						<?php
						$conc_days  = isset( $conc_row['days'] ) ? (string) $conc_row['days'] : '';
						$conc_range = isset( $conc_row['hours'] ) ? (string) $conc_row['hours'] : '';

						if ( '' === $conc_days || '' === $conc_range ) {
							continue;
						}
						?>
						<div class="cfkl-conc-hours__row">
							<dt class="cfkl-conc-hours__days"><?php echo esc_html( $conc_days ); ?></dt>
							<dd class="cfkl-conc-hours__time"><?php echo esc_html( $conc_range ); ?></dd>
						</div>
					<?php endforeach; ?>
				</dl>
			<?php endif; ?>

			<?php if ( ! empty( $conc_directions ) ) : ?>
				<h3 class="cfkl-conc-office__sub"><?php esc_html_e( 'Getting here', 'coinsfera' ); ?></h3>
				<ul class="cfkl-conc-directions">
					<?php foreach ( $conc_directions as $conc_way ) : ?>
						<?php
						$conc_way_label = isset( $conc_way['label'] ) ? (string) $conc_way['label'] : '';
						$conc_way_text  = isset( $conc_way['desc'] ) ? (string) $conc_way['desc'] : '';

						if ( '' === $conc_way_label && '' === $conc_way_text ) {
							continue;
						}
						?>
						<li class="cfkl-conc-directions__item">
							<?php if ( '' !== $conc_way_label ) : ?>
								<span class="cfkl-conc-eyebrow cfkl-conc-directions__label"><?php echo esc_html( $conc_way_label ); ?></span>
							<?php endif; ?>
							<?php if ( '' !== $conc_way_text ) : ?>
								<p class="cfkl-conc-directions__text"><?php echo esc_html( $conc_way_text ); ?></p>
							<?php endif; ?>
						</li>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>

			<?php if ( '' !== $conc_office['cta'] && '' !== $conc_office['url'] ) : ?>
				<p class="cfkl-conc-office__actions">
					<a class="cfkl-conc-link" href="<?php echo esc_url( $conc_office['url'] ); ?>"><?php echo esc_html( $conc_office['cta'] ); ?></a>
				</p>
			<?php endif; ?>

		</div>

	</div>
</section>
