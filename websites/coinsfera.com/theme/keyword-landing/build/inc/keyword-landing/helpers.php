<?php
/**
 * Rendering helpers for the Keyword Landing template.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * The available designs.
 *
 * Lives here rather than in bootstrap.php because the field group is
 * registered while bootstrap.php is still executing its requires, and the
 * design selector needs these labels at that moment.
 *
 * @return array<string, array{label: string, note: string}>
 */
function cfkl_designs() {

	return array(
		'desk'      => array(
			'label' => __( 'A - OTC Desk (trading desk, dense, tabular)', 'coinsfera' ),
			'note'  => __( 'Ink on warm white, monospaced figures, hairline rules, no shadows.', 'coinsfera' ),
		),
		'concierge' => array(
			'label' => __( 'B - Istanbul Concierge (editorial, warm, spacious)', 'coinsfera' ),
			'note'  => __( 'Serif display, sand palette, photography led, asymmetric grid.', 'coinsfera' ),
		),
		'neo'       => array(
			'label' => __( 'C - Neo Fintech (bold blocks, oversized type)', 'coinsfera' ),
			'note'  => __( 'Orange as a field colour, large radii, solid offset shadows.', 'coinsfera' ),
		),
		'ledger'    => array(
			'label' => __( 'D - Swiss Ledger (strict grid, minimal, numeric)', 'coinsfera' ),
			'note'  => __( 'Square corners, visible grid, micro caps, numbers as the hero.', 'coinsfera' ),
		),
	);
}

/**
 * The design this page renders with.
 *
 * @param int|false $post_id Page to read. Defaults to the current page.
 * @return string Design slug, always one that exists.
 */
function cfkl_design( $post_id = false ) {

	$design  = (string) cfkl_get( 'design', 'desk', $post_id );
	$designs = cfkl_designs();

	return isset( $designs[ $design ] ) ? $design : 'desk';
}

/**
 * Read a cfkl field.
 *
 * @param string    $name    Field name, without the cfkl_ prefix.
 * @param mixed     $default Value returned when the field is empty or ACF is inactive.
 * @param int|false $post_id Post to read from. Defaults to the current post.
 * @return mixed
 */
function cfkl_get( $name, $default = '', $post_id = false ) {

	if ( ! function_exists( 'get_field' ) ) {
		return $default;
	}

	$value = get_field( 'cfkl_' . $name, $post_id );

	return ( null === $value || '' === $value || array() === $value ) ? $default : $value;
}

/**
 * Read a cfkl repeater as a plain array.
 *
 * @param string    $name    Field name, without the cfkl_ prefix.
 * @param int|false $post_id Post to read from. Defaults to the current post.
 * @return array
 */
function cfkl_rows( $name, $post_id = false ) {

	$rows = cfkl_get( $name, array(), $post_id );

	return is_array( $rows ) ? $rows : array();
}

/**
 * Render an image from an ACF image array with dimensions attached.
 *
 * Explicit width and height come from the attachment metadata so the browser
 * can reserve space before the image loads.
 *
 * @param array  $image ACF image array.
 * @param string $size  Registered image size.
 * @param array  $attrs Extra HTML attributes.
 * @return string
 */
function cfkl_image( $image, $size = 'large', array $attrs = array() ) {

	if ( empty( $image['ID'] ) ) {
		return '';
	}

	$attrs = wp_parse_args( $attrs, array(
		'loading'  => 'lazy',
		'decoding' => 'async',
		'alt'      => isset( $image['alt'] ) ? $image['alt'] : '',
	) );

	return wp_get_attachment_image( (int) $image['ID'], $size, false, $attrs );
}

/**
 * Echo a section heading block.
 *
 * Every section routes its title and intro line through here so the vertical
 * rhythm and measure are identical across the page instead of each partial
 * spacing its own heading.
 *
 * @param string $title Heading text.
 * @param string $text  Optional supporting line below the heading.
 * @param array  $args  align: center|left. level: h2|h3. id: heading id.
 *                      tight: true to drop the bottom margin when the section
 *                      body supplies its own top spacing.
 * @return void
 */
function cfkl_heading( $title, $text = '', array $args = array() ) {

	if ( '' === $title && '' === $text ) {
		return;
	}

	$args = wp_parse_args( $args, array(
		'align' => 'center',
		'level' => 'h2',
		'id'    => '',
		'tight' => false,
	) );

	$level = in_array( $args['level'], array( 'h2', 'h3' ), true ) ? $args['level'] : 'h2';

	$classes = array( 'cfkl-section__head' );

	if ( 'left' === $args['align'] ) {
		$classes[] = 'cfkl-section__head--left';
	}

	if ( $args['tight'] ) {
		$classes[] = 'cfkl-section__head--tight';
	}

	printf( '<header class="%s">', esc_attr( implode( ' ', $classes ) ) );

	if ( '' !== $title ) {
		printf(
			'<%1$s class="cfkl-section__title"%2$s>%3$s</%1$s>',
			$level,
			'' !== $args['id'] ? ' id="' . esc_attr( $args['id'] ) . '"' : '',
			esc_html( $title )
		);
	}

	if ( '' !== $text ) {
		printf( '<p class="cfkl-section__text">%s</p>', esc_html( $text ) );
	}

	echo '</header>';
}

/**
 * Load a section partial belonging to the active design.
 *
 * @param string $slug Partial name inside the design's folder.
 * @param array  $args Variables passed to the partial as $args.
 * @return void
 */
function cfkl_part( $slug, array $args = array() ) {

	get_template_part( 'template-parts/keyword-landing/designs/' . cfkl_design() . '/' . $slug, null, $args );
}

/**
 * Load a partial shared by every design.
 *
 * @param string $slug Partial name inside template-parts/keyword-landing/shared/.
 * @param array  $args Variables passed to the partial as $args.
 * @return void
 */
function cfkl_shared( $slug, array $args = array() ) {

	get_template_part( 'template-parts/keyword-landing/shared/' . $slug, null, $args );
}

/**
 * Render the hero image as the page's LCP candidate.
 *
 * @param string $class Class for the <img> itself.
 * @param string $size  Registered image size.
 * @return void
 */
function cfkl_hero_image( $class = 'cfkl-hero__img', $size = 'large' ) {

	$image = cfkl_get( 'hero_image', array() );

	if ( empty( $image ) ) {
		return;
	}

	echo cfkl_image( $image, $size, array( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_get_attachment_image escapes.
		'loading'       => 'eager',
		'fetchpriority' => 'high',
		'class'         => $class,
	) );
}

/**
 * Inline icon set.
 *
 * Inline so a design needs no icon font and no extra request. Each path is
 * drawn on a 24x24 grid and inherits currentColor.
 *
 * @param string $name  Icon name.
 * @param string $class Class for the <svg>.
 * @return string SVG markup, or an empty string for an unknown name.
 */
function cfkl_icon( $name, $class = 'cfkl-icon' ) {

	$paths = array(
		'pin'      => '<path d="M12 21s7-5.6 7-11a7 7 0 1 0-14 0c0 5.4 7 11 7 11Z"/><circle cx="12" cy="10" r="2.6"/>',
		'check'    => '<path d="m4 12.5 5.2 5.2L20 7"/>',
		'cross'    => '<path d="M6 6l12 12M18 6 6 18"/>',
		'star'     => '<path d="m12 3.5 2.6 5.6 6 .8-4.4 4.2 1.1 6-5.3-2.9L6.7 20l1.1-6-4.4-4.2 6-.8Z"/>',
		'clock'    => '<circle cx="12" cy="12" r="8.5"/><path d="M12 7.2V12l3.2 2"/>',
		'shield'   => '<path d="M12 3.2 19 6v5.4c0 4.3-2.9 7.6-7 9.4-4.1-1.8-7-5.1-7-9.4V6Z"/><path d="m8.8 12 2.2 2.2 4.2-4.4"/>',
		'wallet'   => '<path d="M4 7.5A2.5 2.5 0 0 1 6.5 5H17a2 2 0 0 1 2 2v1"/><rect x="4" y="8" width="16" height="11" rx="2.2"/><circle cx="16" cy="13.5" r="1.2"/>',
		'arrow'    => '<path d="M5 12h13m-5.5-5.5L19 12l-6.5 5.5"/>',
		'swap'     => '<path d="M7 8h11l-3-3m3 11H7l3 3"/>',
		'bolt'     => '<path d="M13 3 5.5 13.4H11l-.8 7.6L18.5 10H13Z"/>',
		'phone'    => '<path d="M6.5 3.8h3l1.5 3.7-2 1.4a11 11 0 0 0 5.1 5.1l1.4-2 3.7 1.5v3a2 2 0 0 1-2.2 2A16.5 16.5 0 0 1 4.5 6a2 2 0 0 1 2-2.2Z"/>',
		'building' => '<path d="M4.5 20.5V6l7-2.5V20.5"/><path d="M11.5 9h8v11.5"/><path d="M3 20.5h18"/><path d="M7.5 9v.01M7.5 12.5v.01M7.5 16v.01M15.5 12.5v.01M15.5 16v.01"/>',
	);

	if ( empty( $paths[ $name ] ) ) {
		return '';
	}

	return sprintf(
		'<svg class="%s" viewBox="0 0 24 24" aria-hidden="true" focusable="false">%s</svg>',
		esc_attr( $class ),
		$paths[ $name ] // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- static path data.
	);
}

/**
 * The office details, gathered from the fields the hero and office sections share.
 *
 * @param int|false $post_id Page to read. Defaults to the current page.
 * @return array{label: string, address: string, url: string, cta: string, rating: string}
 */
function cfkl_office( $post_id = false ) {

	return array(
		'label'   => (string) cfkl_get( 'banner_office_label', '', $post_id ),
		'address' => (string) cfkl_get( 'banner_card_title', '', $post_id ),
		'url'     => (string) cfkl_get( 'banner_card_btn_url', '', $post_id ),
		'cta'     => (string) cfkl_get( 'banner_card_btn_label', '', $post_id ),
		'rating'  => (string) cfkl_get( 'banner_office_rating', '', $post_id ),
	);
}

/**
 * Rows for the live rate board, in the order the editor chose.
 *
 * Returns an empty array when the feed is unavailable, which every design
 * treats as a reason to skip the board rather than print empty cells.
 *
 * @param int|false $post_id Page to read. Defaults to the current page.
 * @return array<int, array{symbol: string, label: string, usd: float, try: float, eur: float, change: float|null}>
 */
function cfkl_rate_board( $post_id = false ) {

	$wanted = cfkl_get( 'rates_coins', array(), $post_id );
	$wanted = is_array( $wanted ) ? $wanted : array();

	if ( empty( $wanted ) ) {
		return array();
	}

	$rates = cfkl_get_rates();
	$coins = cfkl_rate_coins();
	$rows  = array();

	foreach ( $wanted as $symbol ) {

		if ( empty( $rates['coins'][ $symbol ] ) ) {
			continue;
		}

		$rows[] = array_merge(
			$rates['coins'][ $symbol ],
			array(
				'symbol' => $symbol,
				'label'  => isset( $coins[ $symbol ]['label'] ) ? $coins[ $symbol ]['label'] : $symbol,
			)
		);
	}

	return $rows;
}

/**
 * Format a rate for display in the markup, before JavaScript takes over.
 *
 * @param float  $value    Amount.
 * @param string $currency usd|eur|try.
 * @return string
 */
function cfkl_money( $value, $currency = 'usd' ) {

	$symbols = array(
		'usd' => '$',
		'eur' => '€',
		'try' => '₺',
	);

	$symbol   = isset( $symbols[ $currency ] ) ? $symbols[ $currency ] : '';
	$decimals = $value >= 100 ? 0 : 4;

	return $symbol . number_format_i18n( (float) $value, $decimals );
}

/**
 * Build FAQPage JSON-LD from the FAQ repeater.
 *
 * Returns an empty string when the toggle is off or no questions are answered,
 * so the page never emits an empty FAQPage node.
 *
 * @param int|false $post_id Post to read from. Defaults to the current post.
 * @return string
 */
function cfkl_faq_jsonld( $post_id = false ) {

	if ( ! cfkl_get( 'faq_schema', false, $post_id ) ) {
		return '';
	}

	$entities = array();

	foreach ( cfkl_rows( 'faq_items', $post_id ) as $row ) {

		$question = isset( $row['title'] ) ? trim( wp_strip_all_tags( $row['title'] ) ) : '';
		$answer   = isset( $row['desc'] ) ? trim( wp_strip_all_tags( $row['desc'] ) ) : '';

		if ( '' === $question || '' === $answer ) {
			continue;
		}

		$entities[] = array(
			'@type'          => 'Question',
			'name'           => $question,
			'acceptedAnswer' => array(
				'@type' => 'Answer',
				'text'  => $answer,
			),
		);
	}

	if ( empty( $entities ) ) {
		return '';
	}

	$schema = array(
		'@context'   => 'https://schema.org',
		'@type'      => 'FAQPage',
		'mainEntity' => $entities,
	);

	return '<script type="application/ld+json">'
		. wp_json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE )
		. '</script>';
}
