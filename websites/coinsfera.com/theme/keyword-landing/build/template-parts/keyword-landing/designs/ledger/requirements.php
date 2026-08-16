<?php
/**
 * Swiss Ledger - what to bring.
 *
 * Three columns divided by real vertical hairlines, each sub-referenced against
 * this section's figure, so the page reads 06.a, 06.b, 06.c like clauses. The
 * card icons the field set offers are not rendered: this design carries exactly
 * one image, the plate, and coloured glyphs would break a monochrome page.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_cards = cfkl_rows( 'req_cards' );

if ( empty( $cfkl_cards ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'req_title' );
$cfkl_text  = (string) cfkl_get( 'req_text' );

$cfkl_figure  = cfkl_ldg_figure();
$cfkl_letters = array( 'a', 'b', 'c', 'd', 'e', 'f' );
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-req-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, $cfkl_text, 'cfkl-ldg-req-title' ); ?>

		<div class="cfkl-ldg-grid">
			<ul class="cfkl-ldg-cols">
				<?php foreach ( $cfkl_cards as $cfkl_index => $cfkl_card ) : ?>
					<?php
					$cfkl_label = isset( $cfkl_card['title'] ) ? (string) $cfkl_card['title'] : '';
					$cfkl_desc  = isset( $cfkl_card['desc'] ) ? (string) $cfkl_card['desc'] : '';

					if ( '' === $cfkl_label ) {
						continue;
					}

					$cfkl_letter = isset( $cfkl_letters[ $cfkl_index ] ) ? $cfkl_letters[ $cfkl_index ] : (string) ( $cfkl_index + 1 );
					?>
					<li class="cfkl-ldg-col">
						<p class="cfkl-ldg-ml cfkl-ldg-col__ref" aria-hidden="true">
							<?php echo esc_html( sprintf( '%1$s.%2$s', $cfkl_figure, $cfkl_letter ) ); ?>
						</p>
						<h3 class="cfkl-ldg-h3"><?php echo esc_html( $cfkl_label ); ?></h3>
						<?php if ( '' !== $cfkl_desc ) : ?>
							<p class="cfkl-ldg-body cfkl-ldg-body--sm"><?php echo esc_html( $cfkl_desc ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ul>
		</div>

	</div>
</section>
