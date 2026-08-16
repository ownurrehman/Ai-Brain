<?php
/**
 * DESK - page layout.
 *
 * The OTC dealing desk. Sections run in the order a trader would audit a
 * counterparty: the live board and a dealing ticket first, then the numbers
 * that justify them, then the people and the address behind the numbers.
 *
 * Intro and features are deliberately absent. This design has no soft-sell
 * prose; anything it cannot express as a figure, a rule or a table it does
 * not say.
 *
 * The two render helpers below live here because page.php is the one file in
 * the design that is included exactly once per request. Section partials call
 * them, so the spec-sheet header and the comparison glyphs are written in a
 * single place instead of thirteen.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! function_exists( 'cfkl_desk_spec_head' ) ) {
	/**
	 * Echo a section header styled as a spec-sheet row.
	 *
	 * The section number counts renders rather than declarations, so a page
	 * with no fee lines is numbered 01 02 03 rather than 01 03 04.
	 *
	 * @param string $micro Micro-label printed beside the number.
	 * @param string $title Section heading.
	 * @param string $text  Optional supporting line.
	 * @param string $id    Optional id for the heading.
	 * @return void
	 */
	function cfkl_desk_spec_head( $micro, $title, $text = '', $id = '' ) {

		static $count = 0;

		if ( '' === $title && '' === $micro ) {
			return;
		}

		$count++;

		echo '<header class="cfkl-desk-spec">';
		echo '<div class="cfkl-desk-spec__row">';

		printf(
			'<span class="cfkl-desk-spec__no">%s</span>',
			esc_html( sprintf( '%02d', $count ) )
		);

		if ( '' !== $micro ) {
			printf( '<span class="cfkl-desk-spec__micro">%s</span>', esc_html( $micro ) );
		}

		echo '<span class="cfkl-desk-spec__rule" aria-hidden="true"></span>';
		echo '</div>';

		if ( '' !== $title ) {
			printf(
				'<h2 class="cfkl-desk-spec__title"%1$s>%2$s</h2>',
				'' !== $id ? ' id="' . esc_attr( $id ) . '"' : '',
				esc_html( $title )
			);
		}

		if ( '' !== $text ) {
			printf( '<p class="cfkl-desk-spec__text">%s</p>', esc_html( $text ) );
		}

		echo '</header>';
	}
}

if ( ! function_exists( 'cfkl_desk_mark' ) ) {
	/**
	 * Render one comparison cell.
	 *
	 * Editors type "Yes" or "No" in most rows and a short phrase in the rest,
	 * so a recognised word becomes a glyph with a screen-reader word behind it
	 * and everything else prints as written.
	 *
	 * @param string $value Cell value as typed.
	 * @return string Escaped markup.
	 */
	function cfkl_desk_mark( $value ) {

		$raw = trim( (string) $value );

		if ( '' === $raw ) {
			return '<span class="cfkl-desk-mark cfkl-desk-mark--void" aria-hidden="true">&mdash;</span>'
				. '<span class="cfkl-sr">' . esc_html__( 'Not stated', 'coinsfera' ) . '</span>';
		}

		$key = function_exists( 'mb_strtolower' ) ? mb_strtolower( $raw ) : strtolower( $raw );

		$yes = array( 'yes', 'y', 'true', '1', '✓', 'evet', 'var', 'да' );
		$no  = array( 'no', 'n', 'false', '0', '—', '–', '-', '✗', '×', 'x', 'hayır', 'yok', 'нет' );

		if ( in_array( $key, $yes, true ) ) {
			return '<span class="cfkl-desk-mark cfkl-desk-mark--yes">'
				. cfkl_icon( 'check', 'cfkl-desk-mark__icon' )
				. '<span class="cfkl-sr">' . esc_html__( 'Yes', 'coinsfera' ) . '</span></span>';
		}

		if ( in_array( $key, $no, true ) ) {
			return '<span class="cfkl-desk-mark cfkl-desk-mark--no">'
				. cfkl_icon( 'cross', 'cfkl-desk-mark__icon' )
				. '<span class="cfkl-sr">' . esc_html__( 'No', 'coinsfera' ) . '</span></span>';
		}

		return esc_html( $raw );
	}
}

cfkl_part( 'hero' );
cfkl_part( 'rates' );
cfkl_part( 'fees' );
cfkl_part( 'compare' );
cfkl_part( 'steps' );
cfkl_part( 'requirements' );
cfkl_part( 'trust' );
cfkl_part( 'coins' );
cfkl_part( 'office' );
cfkl_part( 'reviews' );
cfkl_part( 'services' );
cfkl_part( 'faq' );
cfkl_part( 'cta' );
