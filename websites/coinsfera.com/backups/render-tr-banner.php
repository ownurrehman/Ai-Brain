<?php
/**
 * Render Buy Bitcoin TR through Elementor and see which language comes out.
 *
 * Run: wp eval-file render-tr-banner.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

$post_id = 11226;

if ( function_exists( 'icl_get_languages' ) ) {
	global $sitepress;
	if ( $sitepress ) {
		$sitepress->switch_lang( 'tr' );
	}
}

do_action( 'wp' );

$html = '';
if ( class_exists( '\Elementor\Plugin' ) ) {
	$html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $post_id, true );
}

WP_CLI::log( 'html_bytes=' . strlen( (string) $html ) );
foreach ( array(
	"Istanbul'da Bitcoin al",
	'Buy Bitcoin in Istanbul, Turkey',
	"Istanbul'da nakit",
	'Buy Bitcoin in Istanbul Turkey with cash',
	'Gerekenler',
	'Requirements to Buy Bitcoin in Istanbul',
) as $s ) {
	WP_CLI::log( ( false !== strpos( (string) $html, $s ) ? 'HIT  ' : 'miss ' ) . $s );
}

if ( preg_match( '/<h1[^>]*>(.*?)<\/h1>/is', (string) $html, $m ) ) {
	WP_CLI::log( 'h1=' . wp_strip_all_tags( $m[1] ) );
}
if ( preg_match( '/<h2[^>]*>(.*?)<\/h2>/is', (string) $html, $m ) ) {
	WP_CLI::log( 'h2=' . wp_strip_all_tags( $m[1] ) );
}

WP_CLI::log( 'queried_lang=' . apply_filters( 'wpml_current_language', null ) );
WP_CLI::log( 'post_lang=' . apply_filters( 'wpml_element_language_code', null, array( 'element_id' => $post_id, 'element_type' => 'post_page' ) ) );
