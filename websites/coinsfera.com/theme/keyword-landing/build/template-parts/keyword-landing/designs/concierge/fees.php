<?php
/**
 * Concierge fees.
 *
 * A definition list rather than a table, so it restacks on a phone without a
 * scrolling region: the item on one side, the figure in serif on the other,
 * separated by gold hairlines like a printed tariff.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$conc_title = (string) cfkl_get( 'fees_title' );
$conc_text  = (string) cfkl_get( 'fees_text' );
$conc_rows  = cfkl_rows( 'fees_rows' );

if ( empty( $conc_rows ) ) {
	return;
}
?>
<section class="cfkl-conc-fees cfkl-reveal">
	<div class="cfkl-container cfkl-conc-fees__inner">

		<header class="cfkl-conc-head">
			<p class="cfkl-conc-eyebrow"><?php esc_html_e( 'What it costs', 'coinsfera' ); ?></p>
			<?php if ( '' !== $conc_title ) : ?>
				<h2 class="cfkl-conc-head__title"><?php echo esc_html( $conc_title ); ?></h2>
			<?php endif; ?>
			<?php if ( '' !== $conc_text ) : ?>
				<p class="cfkl-conc-head__text"><?php echo esc_html( $conc_text ); ?></p>
			<?php endif; ?>
		</header>

		<dl class="cfkl-conc-tariff">
			<?php foreach ( $conc_rows as $conc_row ) : ?>
				<?php
				$conc_item  = isset( $conc_row['label'] ) ? (string) $conc_row['label'] : '';
				$conc_value = isset( $conc_row['value'] ) ? (string) $conc_row['value'] : '';
				$conc_note  = isset( $conc_row['note'] ) ? (string) $conc_row['note'] : '';

				if ( '' === $conc_item && '' === $conc_value ) {
					continue;
				}
				?>
				<div class="cfkl-conc-tariff__row">
					<dt class="cfkl-conc-tariff__item">
						<span class="cfkl-conc-tariff__name"><?php echo esc_html( $conc_item ); ?></span>
						<?php if ( '' !== $conc_note ) : ?>
							<span class="cfkl-conc-tariff__note"><?php echo esc_html( $conc_note ); ?></span>
						<?php endif; ?>
					</dt>
					<dd class="cfkl-conc-tariff__value"><?php echo esc_html( $conc_value ); ?></dd>
				</div>
			<?php endforeach; ?>
		</dl>

	</div>
</section>
