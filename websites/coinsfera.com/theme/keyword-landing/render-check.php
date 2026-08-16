<?php
/**
 * Render the draft landing page in CLI and report on the output.
 *
 * Run with: wp eval-file render-check.php
 * Read-only: renders into a buffer, writes nothing.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit( "Run through WP-CLI.\n" );
}

$page_id = 28486;

WP_CLI::log( '--- field values ---' );
foreach ( array( 'cfkl_banner_heading', 'cfkl_banner_cta_url', 'cfkl_faq_schema' ) as $f ) {
	$v = get_field( $f, $page_id );
	WP_CLI::log( sprintf( '%-24s %s', $f, is_scalar( $v ) ? $v : gettype( $v ) ) );
}
foreach ( array( 'cfkl_banner_stats', 'cfkl_trust_points', 'cfkl_steps', 'cfkl_req_cards', 'cfkl_features', 'cfkl_services', 'cfkl_faq_items' ) as $f ) {
	$v = get_field( $f, $page_id );
	WP_CLI::log( sprintf( '%-24s %d rows', $f, is_array( $v ) ? count( $v ) : 0 ) );
}

WP_CLI::log( '--- rendering ---' );

// Simulate a front-end request for the draft page.
global $wp_query, $post;
$wp_query = new WP_Query( array(
	'page_id'     => $page_id,
	'post_type'   => 'page',
	'post_status' => 'draft',
) );
$GLOBALS['wp_the_query'] = $wp_query;
$post                    = $wp_query->posts[0];
setup_postdata( $post );

WP_CLI::log( 'is_page: ' . ( is_page() ? 'yes' : 'no' ) );
WP_CLI::log( 'is_page_template: ' . ( is_page_template( 'page-templates/template-keyword-landing.php' ) ? 'yes' : 'no' ) );
WP_CLI::log( 'cfkl_is_active: ' . ( cfkl_is_active() ? 'yes' : 'no' ) );

$errors = array();
set_error_handler( function ( $no, $str, $file, $line ) use ( &$errors ) {
	if ( false !== strpos( $file, '/keyword-landing' ) || false !== strpos( $file, 'template-keyword-landing' ) ) {
		$errors[] = "$str in " . basename( $file ) . ":$line";
	}
	return true;
} );

ob_start();
include get_template_directory() . '/page-templates/template-keyword-landing.php';
$html = ob_get_clean();

restore_error_handler();

file_put_contents( '/tmp/cfkl-render.html', $html );

WP_CLI::log( '--- results ---' );
WP_CLI::log( 'output bytes: ' . strlen( $html ) );
WP_CLI::log( 'errors in landing code: ' . ( $errors ? implode( ' | ', $errors ) : 'none' ) );

WP_CLI::log( '--- asset diagnostics ---' );
$styles = wp_styles();
WP_CLI::log( 'style registered: ' . ( isset( $styles->registered['coinsfera-keyword-landing'] ) ? 'yes' : 'NO' ) );
WP_CLI::log( 'style in queue: ' . ( in_array( 'coinsfera-keyword-landing', $styles->queue, true ) ? 'yes' : 'no' ) );
WP_CLI::log( 'style done: ' . ( in_array( 'coinsfera-keyword-landing', $styles->done, true ) ? 'yes' : 'no' ) );
WP_CLI::log( 'dep coinsfera-custom-style registered: ' . ( isset( $styles->registered['coinsfera-custom-style'] ) ? 'yes' : 'NO' ) );
$scripts = wp_scripts();
WP_CLI::log( 'script registered: ' . ( isset( $scripts->registered['coinsfera-keyword-landing'] ) ? 'yes' : 'NO' ) );
WP_CLI::log( 'script done: ' . ( in_array( 'coinsfera-keyword-landing', $scripts->done, true ) ? 'yes' : 'no' ) );
WP_CLI::log( 'css file exists: ' . ( file_exists( get_template_directory() . '/assets/css/keyword-landing.css' ) ? 'yes' : 'NO' ) );
WP_CLI::log( 'js file exists: ' . ( file_exists( get_template_directory() . '/assets/js/keyword-landing.js' ) ? 'yes' : 'NO' ) );
WP_CLI::log( 'did wp_enqueue_scripts fire: ' . did_action( 'wp_enqueue_scripts' ) );

$checks = array(
	'hero section'      => 'cfkl-hero',
	'h1'                => '<h1',
	'stat strip'        => 'cfkl-stats__value',
	'intro prose'       => 'cfkl-prose',
	'trust points'      => 'cfkl-points__item',
	'steps'             => 'cfkl-steps__item',
	'requirement tiles' => 'cfkl-tile',
	'features'          => 'cfkl-feature__title',
	'service links'     => 'cfkl-service__inner',
	'faq details'       => '<details',
	'closing cta'       => 'cfkl-cta__title',
	'sticky cta'        => 'data-cfkl-sticky',
	'stylesheet'        => 'keyword-landing.css',
	'script'            => 'keyword-landing.js',
	'js flag'           => 'cfkl-js',
	'hero preload'      => 'rel="preload" as="image"',
	'faq schema'        => 'FAQPage',
	'site header'       => 'header-wrapper',
	'site footer'       => 'footer-wrapper',
);

foreach ( $checks as $label => $needle ) {
	WP_CLI::log( sprintf( '%-18s %s', $label, false !== strpos( $html, $needle ) ? 'present' : 'MISSING' ) );
}

WP_CLI::log( '--- counts ---' );
WP_CLI::log( 'h1 tags: ' . substr_count( $html, '<h1' ) );
WP_CLI::log( 'h2 tags: ' . substr_count( $html, '<h2' ) );
WP_CLI::log( 'details: ' . substr_count( $html, '<details' ) );
WP_CLI::log( 'FAQPage occurrences: ' . substr_count( $html, 'FAQPage' ) );
WP_CLI::log( 'img tags: ' . substr_count( $html, '<img' ) );
WP_CLI::log( 'imgs missing width: ' . ( substr_count( $html, '<img' ) - substr_count( $html, 'width=' ) ) );
WP_CLI::log( 'body words: ' . str_word_count( wp_strip_all_tags( $html ) ) );

// Surface the wrapper we deliberately avoided.
WP_CLI::log( 'theme title band present: ' . ( false !== strpos( $html, 'coinsfera_seo_subtitle' ) || false !== strpos( $html, 'breadcrumb' ) ? 'check manually' : 'no' ) );
