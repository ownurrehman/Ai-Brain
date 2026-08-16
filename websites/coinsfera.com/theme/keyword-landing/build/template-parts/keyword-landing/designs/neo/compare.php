<?php
/**
 * Neo Fintech - comparison as three stacked cards.
 *
 * No table. Each option gets its own card carrying every row label, and ours
 * is lifted out of the line: orange fill, a Best pill, a deeper offset shadow
 * and a slight scale on desktop. Our card is first in the markup so the phone
 * layout leads with it; CSS moves it to the middle on wide screens.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$neo_rows = cfkl_rows( 'compare_rows' );

if ( empty( $neo_rows ) ) {
	return;
}

$neo_columns = array(
	array(
		'key'   => 'us',
		'field' => 'us',
		'title' => (string) cfkl_get( 'compare_col_us' ),
		'best'  => true,
	),
	array(
		'key'   => 'b',
		'field' => 'b',
		'title' => (string) cfkl_get( 'compare_col_b' ),
		'best'  => false,
	),
	array(
		'key'   => 'c',
		'field' => 'c',
		'title' => (string) cfkl_get( 'compare_col_c' ),
		'best'  => false,
	),
);

$neo_columns = array_values(
	array_filter(
		$neo_columns,
		static function ( $column ) {
			return '' !== $column['title'];
		}
	)
);

if ( empty( $neo_columns ) ) {
	return;
}

$neo_title = (string) cfkl_get( 'compare_title' );
$neo_text  = (string) cfkl_get( 'compare_text' );
?>
<section class="cfkl-neo-section cfkl-neo-compare cfkl-reveal">
	<div class="cfkl-container">

		<?php cfkl_heading( $neo_title, $neo_text ); ?>

		<div class="cfkl-neo-compare__cards">
			<?php foreach ( $neo_columns as $neo_column ) : ?>
				<article class="cfkl-neo-vs cfkl-neo-vs--<?php echo esc_attr( $neo_column['key'] ); ?>">

					<header class="cfkl-neo-vs__head">
						<?php if ( $neo_column['best'] ) : ?>
							<p class="cfkl-neo-pill cfkl-neo-vs__flag"><?php esc_html_e( 'Best', 'coinsfera' ); ?></p>
						<?php endif; ?>
						<h3 class="cfkl-neo-vs__title"><?php echo esc_html( $neo_column['title'] ); ?></h3>
					</header>

					<ul class="cfkl-neo-vs__list">
						<?php foreach ( $neo_rows as $neo_row ) : ?>
							<?php
							$neo_label = isset( $neo_row['label'] ) ? (string) $neo_row['label'] : '';
							$neo_value = isset( $neo_row[ $neo_column['field'] ] ) ? (string) $neo_row[ $neo_column['field'] ] : '';

							if ( '' === $neo_label && '' === $neo_value ) {
								continue;
							}
							?>
							<li class="cfkl-neo-vs__row">
								<?php
								echo cfkl_icon( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static SVG.
									$neo_column['best'] ? 'check' : 'cross',
									'cfkl-neo-vs__mark cfkl-neo-vs__mark--' . ( $neo_column['best'] ? 'yes' : 'no' )
								);
								?>
								<span class="cfkl-neo-vs__cell">
									<?php if ( '' !== $neo_label ) : ?>
										<span class="cfkl-neo-vs__label"><?php echo esc_html( $neo_label ); ?></span>
									<?php endif; ?>
									<?php if ( '' !== $neo_value ) : ?>
										<span class="cfkl-neo-vs__value"><?php echo esc_html( $neo_value ); ?></span>
									<?php endif; ?>
								</span>
							</li>
						<?php endforeach; ?>
					</ul>

				</article>
			<?php endforeach; ?>
		</div>

	</div>
</section>
