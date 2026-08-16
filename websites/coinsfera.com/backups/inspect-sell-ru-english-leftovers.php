<?php
/**
 * List leftover English-looking strings in RU sell page Elementor JSON.
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

$raw  = get_post_meta( 15673, '_elementor_data', true );
$data = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
$out  = array();
$walk = function ( $nodes ) use ( &$walk, &$out ) {
	if ( ! is_array( $nodes ) ) {
		return;
	}
	foreach ( $nodes as $n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		$w = $n['widgetType'] ?? ( $n['elType'] ?? '' );
		$s = $n['settings'] ?? array();
		foreach ( $s as $k => $v ) {
			if ( ! is_string( $v ) || strlen( $v ) < 8 || strlen( $v ) > 220 ) {
				continue;
			}
			if ( preg_match( '/[А-Яа-яЁё]/u', $v ) ) {
				continue;
			}
			if ( preg_match( '/[A-Za-z]{4,}/', $v ) && ! preg_match( '/^(https?:|#[a-f0-9]|rgb|rgba|solid|none|left|right|center|px|em)/i', $v ) ) {
				$out[] = $w . '.' . $k . '=' . $v;
			}
		}
		if ( ! empty( $n['elements'] ) ) {
			$walk( $n['elements'] );
		}
	}
};
$walk( is_array( $data ) ? $data : array() );
WP_CLI::log( 'english-looking=' . count( $out ) );
foreach ( $out as $line ) {
	WP_CLI::log( $line );
}
