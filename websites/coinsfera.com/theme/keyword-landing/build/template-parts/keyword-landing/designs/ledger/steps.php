<?php
/**
 * Swiss Ledger - how a visit works.
 *
 * An ordered list of ruled rows, each sub-referenced against this section's
 * figure: 07.1, 07.2 and so on. The list element carries the ordering for
 * assistive technology, so the printed references are decorative.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_steps = cfkl_rows( 'steps' );

if ( empty( $cfkl_steps ) ) {
	return;
}

$cfkl_title = (string) cfkl_get( 'steps_title' );

$cfkl_figure = cfkl_ldg_figure();
?>
<section class="cfkl-ldg-sec cfkl-reveal" aria-labelledby="cfkl-ldg-steps-title">
	<div class="cfkl-container">

		<?php cfkl_ldg_head( $cfkl_figure, $cfkl_title, '', 'cfkl-ldg-steps-title' ); ?>

		<div class="cfkl-ldg-grid">
			<ol class="cfkl-ldg-steps">
				<?php foreach ( $cfkl_steps as $cfkl_index => $cfkl_step ) : ?>
					<?php
					$cfkl_label = isset( $cfkl_step['title'] ) ? (string) $cfkl_step['title'] : '';
					$cfkl_desc  = isset( $cfkl_step['desc'] ) ? (string) $cfkl_step['desc'] : '';

					if ( '' === $cfkl_label ) {
						continue;
					}
					?>
					<li class="cfkl-ldg-step">
						<p class="cfkl-ldg-ml cfkl-ldg-step__ref" aria-hidden="true">
							<?php echo esc_html( sprintf( '%1$s.%2$d', $cfkl_figure, $cfkl_index + 1 ) ); ?>
						</p>
						<h3 class="cfkl-ldg-h3 cfkl-ldg-step__title"><?php echo esc_html( $cfkl_label ); ?></h3>
						<?php if ( '' !== $cfkl_desc ) : ?>
							<p class="cfkl-ldg-body cfkl-ldg-body--sm cfkl-ldg-step__text"><?php echo esc_html( $cfkl_desc ); ?></p>
						<?php endif; ?>
					</li>
				<?php endforeach; ?>
			</ol>
		</div>

	</div>
</section>
