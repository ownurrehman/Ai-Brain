<?php
/**
 * DESK - dealing procedure.
 *
 * A numbered rail: one hairline runs across the top of every step so the four
 * of them read as a single sequence. The repeater's icons are ignored on
 * purpose; in this design the number is the marker.
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
?>
<section class="cfkl-desk-section cfkl-desk-steps cfkl-reveal">
	<div class="cfkl-container">
		<?php cfkl_desk_spec_head( __( 'Dealing procedure', 'coinsfera' ), $cfkl_title ); ?>

		<ol class="cfkl-desk-rail">
			<?php $cfkl_index = 0; ?>
			<?php foreach ( $cfkl_steps as $cfkl_step ) : ?>
				<?php
				$cfkl_step_title = isset( $cfkl_step['title'] ) ? (string) $cfkl_step['title'] : '';
				$cfkl_step_desc  = isset( $cfkl_step['desc'] ) ? (string) $cfkl_step['desc'] : '';

				if ( '' === $cfkl_step_title ) {
					continue;
				}

				$cfkl_index++;
				?>
				<li class="cfkl-desk-rail__item">
					<span class="cfkl-desk-rail__no"><?php echo esc_html( sprintf( '%02d', $cfkl_index ) ); ?></span>
					<h3 class="cfkl-desk-rail__title"><?php echo esc_html( $cfkl_step_title ); ?></h3>
					<?php if ( '' !== $cfkl_step_desc ) : ?>
						<p class="cfkl-desk-rail__text"><?php echo esc_html( $cfkl_step_desc ); ?></p>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ol>
	</div>
</section>
