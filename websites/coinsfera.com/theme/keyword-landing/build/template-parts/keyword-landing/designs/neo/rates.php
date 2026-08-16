<?php
/**
 * Neo Fintech - rate board as a marquee ticker.
 *
 * The row is printed once for the reader and three more times for the eye. The
 * duplicates are aria-hidden, so a screen reader hears the prices once while
 * the CSS animation slides the track by exactly one copy and loops seamlessly.
 * With no animation - reduced motion - the duplicates are removed and the
 * single row becomes a horizontally scrollable region.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_rows = cfkl_rate_board();

if ( empty( $neo_rows ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'rates_title' );
$neo_text  = (string) cfkl_get( 'rates_text' );

/* Build the row once, then repeat the same markup for the moving copies. */
ob_start();
?>
<ul class="cfkl-neo-ticker__row">
	<?php foreach ( $neo_rows as $neo_row ) : ?>
		<?php
		$neo_symbol = isset( $neo_row['symbol'] ) ? (string) $neo_row['symbol'] : '';
		$neo_name   = isset( $neo_row['label'] ) ? (string) $neo_row['label'] : '';
		$neo_usd    = isset( $neo_row['usd'] ) ? (float) $neo_row['usd'] : 0.0;
		$neo_try    = isset( $neo_row['try'] ) ? (float) $neo_row['try'] : 0.0;
		$neo_change = isset( $neo_row['change'] ) ? $neo_row['change'] : null;

		if ( '' === $neo_symbol || $neo_usd <= 0 ) {
			continue;
		}
		?>
		<li class="cfkl-neo-tick">
			<span class="cfkl-neo-tick__sym"><?php echo esc_html( $neo_symbol ); ?></span>
			<?php if ( '' !== $neo_name ) : ?>
				<span class="cfkl-neo-tick__name"><?php echo esc_html( $neo_name ); ?></span>
			<?php endif; ?>
			<span class="cfkl-neo-tick__price"><?php echo esc_html( cfkl_money( $neo_usd, 'usd' ) ); ?></span>
			<?php if ( $neo_try > 0 ) : ?>
				<span class="cfkl-neo-tick__price cfkl-neo-tick__price--try"><?php echo esc_html( cfkl_money( $neo_try, 'try' ) ); ?></span>
			<?php endif; ?>
			<?php if ( null !== $neo_change ) : ?>
				<span class="cfkl-neo-tick__change" data-trend="<?php echo esc_attr( $neo_change >= 0 ? 'up' : 'down' ); ?>">
					<?php echo esc_html( ( $neo_change >= 0 ? '+' : '' ) . number_format_i18n( (float) $neo_change, 2 ) . '%' ); ?>
				</span>
			<?php endif; ?>
		</li>
	<?php endforeach; ?>
</ul>
<?php
$neo_row_markup = trim( ob_get_clean() );

if ( '' === $neo_row_markup ) {
	return;
}

$neo_ghost = str_replace( 'class="cfkl-neo-ticker__row"', 'class="cfkl-neo-ticker__row" aria-hidden="true"', $neo_row_markup );
?>
<section class="cfkl-neo-section cfkl-neo-rates">

	<?php if ( '' !== $neo_title || '' !== $neo_text ) : ?>
		<div class="cfkl-container">
			<?php cfkl_heading( $neo_title, $neo_text, array( 'align' => 'left' ) ); ?>
		</div>
	<?php endif; ?>

	<div class="cfkl-neo-ticker">
		<div class="cfkl-neo-ticker__viewport"
			tabindex="0"
			role="group"
			aria-label="<?php esc_attr_e( 'Indicative desk rates', 'coinsfera' ); ?>">
			<div class="cfkl-neo-ticker__track">
				<?php
				echo $neo_row_markup; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built above, every value escaped.
				echo $neo_ghost;      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- same markup, hidden from assistive tech.
				echo $neo_ghost;      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- same markup, hidden from assistive tech.
				echo $neo_ghost;      // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- same markup, hidden from assistive tech.
				?>
			</div>
		</div>
	</div>

</section>
