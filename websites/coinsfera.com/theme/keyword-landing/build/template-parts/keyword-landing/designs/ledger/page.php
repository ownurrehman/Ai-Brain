<?php
/**
 * Design D - Swiss Ledger. Section order and the shared figure numbering.
 *
 * The page reads like a set financial document: every section carries a figure
 * reference in the left margin column, and those references have to run 01, 02,
 * 03 without gaps no matter which sections the editor left empty. A section can
 * only know whether it renders after it has read its own fields, so the number
 * is claimed by the section itself, once its guard has passed, through
 * cfkl_ldg_figure(). page.php never assigns numbers, which is why an empty
 * section costs nothing in the sequence.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cfkl_ldg_figure' ) ) {
	/**
	 * Claim the next figure reference.
	 *
	 * Called by a section only after it has decided it has something to print,
	 * so skipped sections never advance the counter.
	 *
	 * @return string Two digit reference, e.g. "04".
	 */
	function cfkl_ldg_figure() {

		$count = isset( $GLOBALS['cfkl_ldg_count'] ) ? (int) $GLOBALS['cfkl_ldg_count'] : 0;
		$count++;

		$GLOBALS['cfkl_ldg_count'] = $count;

		return str_pad( (string) $count, 2, '0', STR_PAD_LEFT );
	}
}

if ( ! function_exists( 'cfkl_ldg_mark' ) ) {
	/**
	 * Echo a figure reference for the left margin column.
	 *
	 * Hidden from assistive technology: it is a visual cross-reference, and
	 * hearing "zero four" before every heading only adds noise.
	 *
	 * @param string $figure Reference produced by cfkl_ldg_figure().
	 * @param string $class  Extra class on the wrapper.
	 * @return void
	 */
	function cfkl_ldg_mark( $figure, $class = '' ) {

		printf(
			'<p class="cfkl-ldg-fig%1$s" aria-hidden="true"><span class="cfkl-ldg-fig__mark"></span>%2$s</p>',
			'' !== $class ? ' ' . esc_attr( $class ) : '',
			esc_html( $figure )
		);
	}
}

if ( ! function_exists( 'cfkl_ldg_head' ) ) {
	/**
	 * Echo the standard head row: figure in the margin, title and standfirst
	 * in the content columns.
	 *
	 * @param string $figure Figure reference.
	 * @param string $title  Section title.
	 * @param string $text   Optional standfirst.
	 * @param string $id     Heading id, for the section's aria-labelledby.
	 * @return void
	 */
	function cfkl_ldg_head( $figure, $title, $text = '', $id = '' ) {

		echo '<div class="cfkl-ldg-grid cfkl-ldg-head">';

		cfkl_ldg_mark( $figure );

		echo '<div class="cfkl-ldg-head__body">';

		if ( '' !== $title ) {
			printf(
				'<h2 class="cfkl-ldg-h2"%1$s>%2$s</h2>',
				'' !== $id ? ' id="' . esc_attr( $id ) . '"' : '',
				esc_html( $title )
			);
		}

		if ( '' !== $text ) {
			printf( '<p class="cfkl-ldg-lede">%s</p>', esc_html( $text ) );
		}

		echo '</div></div>';
	}
}

cfkl_part( 'hero' );
cfkl_part( 'calc' );
cfkl_part( 'rates' );
cfkl_part( 'compare' );
cfkl_part( 'fees' );
cfkl_part( 'requirements' );
cfkl_part( 'steps' );
cfkl_part( 'plate' );
cfkl_part( 'coins' );
cfkl_part( 'trust' );
cfkl_part( 'office' );
cfkl_part( 'reviews' );
cfkl_part( 'services' );
cfkl_part( 'faq' );
cfkl_part( 'cta' );
