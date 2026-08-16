<?php
/**
 * Dump homepage HTML widget cfxovfl1 and list other pages that have it.
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

function cfkl_find_html( $nodes, &$found ) {
	if ( ! is_array( $nodes ) ) {
		return;
	}
	foreach ( $nodes as $n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		if ( ( $n['id'] ?? '' ) === 'cfxovfl1' || ( ( $n['widgetType'] ?? '' ) === 'html' && isset( $n['settings']['html'] ) && false !== strpos( (string) $n['settings']['html'], 'placeTrustStrip' ) ) ) {
			$found = $n;
			return;
		}
		if ( ! empty( $n['elements'] ) ) {
			cfkl_find_html( $n['elements'], $found );
		}
	}
}

$en = null;
cfkl_find_html( json_decode( (string) get_post_meta( 9, '_elementor_data', true ), true ), $en );
if ( ! $en ) {
	WP_CLI::error( 'EN widget not found' );
}
$html = $en['settings']['html'];
$bak  = WP_CONTENT_DIR . '/uploads/cfkl-backups/homepage-trust-html-en.html';
file_put_contents( $bak, $html );
WP_CLI::log( 'wrote ' . $bak . ' bytes=' . strlen( $html ) );
WP_CLI::log( 'tags style=' . substr_count( $html, '<style' ) . ' /style=' . substr_count( $html, '</style>' ) . ' script=' . substr_count( $html, '<script' ) . ' /script=' . substr_count( $html, '</script>' ) );

global $wpdb;
$ids = $wpdb->get_col( "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key='_elementor_data' AND meta_value LIKE '%placeTrustStrip%'" );
WP_CLI::log( 'posts with placeTrustStrip: ' . implode( ',', $ids ) );
