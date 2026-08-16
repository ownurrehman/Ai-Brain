<?php
/**
 * Swiss Ledger - why a physical desk.
 *
 * A ruled definition list: the claim on the left in the heading size, the
 * argument on the right at reading measure, divided by a vertical hairline. No
 * cards, no icons, no image - the rules carry the structure.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_points = cfkl_rows( 'trust_points' );

if ( empty( $cfkl_points ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'trust_title' );
$cfkl_text  = (string) cfkl_get( 'trust_text' );

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-trust-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, $cfkl_text, 'cfkl-ldg-trust-title' ); ?>

		<div class="cfkl-ldg-grid">
			<dl class="cfkl-ldg-defs">
				<?php foreach ( $cfkl_points as $cfkl_point ) : ?>
					<?php
					$cfkl_label = isset( $cfkl_point['title'] ) ? (string) $cfkl_point['title'] : '';
					$cfkl_desc  = isset( $cfkl_point['desc'] ) ? (string) $cfkl_point['desc'] : '';

					if ( '' === $cfkl_label ) {
						continue;
					}
					?>
					<div class="cfkl-ldg-defs__row">
						<dt class="cfkl-ldg-defs__term"><?php echo esc_html( $cfkl_label ); ?></dt>
						<dd class="cfkl-ldg-defs__desc">
							<?php if ( '' !== $cfkl_desc ) : ?>
								<span class="cfkl-ldg-body cfkl-ldg-body--sm"><?php echo esc_html( $cfkl_desc ); ?></span>
							<?php endif; ?>
						</dd>
					</div>
				<?php endforeach; ?>
			</dl>
		</div>

	</div>
</section>
