<?php
/**
 * Keyword Landing template bootstrap.
 *
 * Loaded from functions.php. Everything here is scoped to pages using the
 * Keyword Landing template, so no other template's output changes.
 *
 * A page picks one of four designs. A design owns its own section order,
 * markup and stylesheet; the only things shared between them are the field
 * data, the rate feed, the calculator behaviour and a small base stylesheet.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'CFKL_TEMPLATE' ) ) {
	define( 'CFKL_TEMPLATE', 'page-templates/template-keyword-landing.php' );
}

require_once COINSFERA_PATH . '/inc/keyword-landing/helpers.php';
require_once COINSFERA_PATH . '/inc/keyword-landing/rates.php';
require_once COINSFERA_PATH . '/inc/keyword-landing/acf-fields.php';

/**
 * Whether the current request renders the Keyword Landing template.
 *
 * @return bool
 */
function cfkl_is_active() {

	return is_page() && is_page_template( CFKL_TEMPLATE );
}

/**
 * Cache-busting version string for an asset in the theme.
 *
 * @param string $relative_path Path relative to the theme root.
 * @return string
 */
function cfkl_asset_version( $relative_path ) {

	$file = COINSFERA_PATH . $relative_path;

	return file_exists( $file ) ? (string) filemtime( $file ) : COINSFERA_VER;
}

/**
 * Enqueue the base stylesheet, the active design's stylesheet and the scripts.
 *
 * @return void
 */
function cfkl_enqueue_assets() {

	if ( ! cfkl_is_active() ) {
		return;
	}

	$design  = cfkl_design( get_queried_object_id() );
	$base    = '/assets/css/keyword-landing.css';
	$skin    = '/assets/css/design-' . $design . '.css';

	wp_enqueue_style(
		'coinsfera-keyword-landing',
		COINSFERA_URI . $base,
		array( 'coinsfera-custom-style' ),
		cfkl_asset_version( $base )
	);

	wp_enqueue_style(
		'coinsfera-keyword-design',
		COINSFERA_URI . $skin,
		array( 'coinsfera-keyword-landing' ),
		cfkl_asset_version( $skin )
	);

	wp_enqueue_script(
		'coinsfera-keyword-landing',
		COINSFERA_URI . '/assets/js/keyword-landing.js',
		array(),
		cfkl_asset_version( '/assets/js/keyword-landing.js' ),
		true
	);

	wp_enqueue_script(
		'coinsfera-keyword-calc',
		COINSFERA_URI . '/assets/js/keyword-landing-calc.js',
		array(),
		cfkl_asset_version( '/assets/js/keyword-landing-calc.js' ),
		true
	);

	wp_localize_script( 'coinsfera-keyword-calc', 'CFKL_CALC', cfkl_calc_payload( get_queried_object_id() ) );
}
add_action( 'wp_enqueue_scripts', 'cfkl_enqueue_assets' );

/**
 * Homepage ticker and the older coin-page calculators.
 *
 * Always loaded: those pages do not use the Keyword Landing template, but they
 * share this feed. Depends on the theme's custom.js so we can unbind its
 * per-keystroke CryptoCompare calls after it has bound them.
 *
 * @return void
 */
function cfkl_enqueue_legacy_rates() {

	if ( is_admin() ) {
		return;
	}

	$path = '/assets/js/legacy-rates.js';
	$deps = array( 'jquery' );

	if ( wp_script_is( 'coinsfera-custom-script', 'registered' ) ) {
		$deps[] = 'coinsfera-custom-script';
	}

	wp_enqueue_script(
		'coinsfera-legacy-rates',
		COINSFERA_URI . $path,
		$deps,
		cfkl_asset_version( $path ),
		true
	);

	wp_localize_script(
		'coinsfera-legacy-rates',
		'CFKL_LIVE',
		array(
			'ajax'  => admin_url( 'admin-ajax.php' ),
			'rates' => rest_url( 'cfkl/v1/rates' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', 'cfkl_enqueue_legacy_rates', 20 );

/**
 * Add body classes so stylesheets can scope safely.
 *
 * @param array $classes Body classes.
 * @return array
 */
function cfkl_body_class( $classes ) {

	if ( cfkl_is_active() ) {
		$classes[] = 'cfkl-page';
		$classes[] = 'cfkl-design-' . cfkl_design( get_queried_object_id() );
	}

	return $classes;
}
add_filter( 'body_class', 'cfkl_body_class' );

/**
 * Output FAQPage JSON-LD in the document head.
 *
 * @return void
 */
function cfkl_print_schema() {

	if ( ! cfkl_is_active() ) {
		return;
	}

	echo cfkl_faq_jsonld( get_queried_object_id() ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD built with wp_json_encode.
}
add_action( 'wp_head', 'cfkl_print_schema', 20 );

/**
 * Flag JavaScript availability before first paint.
 *
 * Scroll-reveal start states live behind this class, so a visitor without
 * JavaScript sees every section instead of a blank page.
 *
 * @return void
 */
function cfkl_print_js_flag() {

	if ( ! cfkl_is_active() ) {
		return;
	}

	echo '<script>document.documentElement.classList.add("cfkl-js");</script>' . "\n";
}
add_action( 'wp_head', 'cfkl_print_js_flag', 2 );

/**
 * Warm the connection to the rate API before the calculator asks for it.
 *
 * @return void
 */
function cfkl_preconnect_rates() {

	if ( ! cfkl_is_active() ) {
		return;
	}

	echo '<link rel="preconnect" href="https://api.coingecko.com" crossorigin>' . "\n";
}
add_action( 'wp_head', 'cfkl_preconnect_rates', 3 );

/**
 * Preload the hero image so the LCP candidate starts downloading early.
 *
 * @return void
 */
function cfkl_preload_hero() {

	if ( ! cfkl_is_active() ) {
		return;
	}

	$hero = cfkl_get( 'hero_image', array(), get_queried_object_id() );

	if ( empty( $hero['ID'] ) ) {
		return;
	}

	$src    = wp_get_attachment_image_url( (int) $hero['ID'], 'large' );
	$srcset = wp_get_attachment_image_srcset( (int) $hero['ID'], 'large' );

	if ( ! $src ) {
		return;
	}

	printf(
		'<link rel="preload" as="image" href="%s"%s fetchpriority="high">' . "\n",
		esc_url( $src ),
		$srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '"' : ''
	);
}
add_action( 'wp_head', 'cfkl_preload_hero', 1 );
