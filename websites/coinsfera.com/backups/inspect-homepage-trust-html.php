<?php
/**
 * Find homepage HTML widgets containing the trust-strip / banner CSS.
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

function cfkl_walk( $nodes, $page, &$hits ) {
	if ( ! is_array( $nodes ) ) {
		return;
	}
	foreach ( $nodes as $n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		$w = $n['widgetType'] ?? '';
		$s = $n['settings'] ?? array();
		$blob = wp_json_encode( $s );
		if ( false !== strpos( (string) $blob, 'placeTrustStrip' ) || false !== strpos( (string) $blob, 'mobile banner align' ) || false !== strpos( (string) $blob, 'cf-trust-strip' ) ) {
			$html = '';
			foreach ( array( 'html', 'editor', 'content', 'custom_html' ) as $k ) {
				if ( ! empty( $s[ $k ] ) && is_string( $s[ $k ] ) ) {
					$html = $s[ $k ];
					break;
				}
			}
			if ( '' === $html ) {
				$html = $blob;
			}
			$hits[] = array(
				'page'   => $page,
				'id'     => $n['id'] ?? '',
				'widget' => $w,
				'keys'   => implode( ',', array_keys( $s ) ),
				'has_style'  => ( false !== strpos( $html, '<style' ) || false !== strpos( $html, '&lt;style' ) ),
				'has_script' => ( false !== strpos( $html, '<script' ) || false !== strpos( $html, '&lt;script' ) ),
				'has_lt'     => ( false !== strpos( $html, '&lt;' ) ),
				'start'  => substr( $html, 0, 220 ),
				'len'    => strlen( $html ),
			);
		}
		if ( ! empty( $n['elements'] ) ) {
			cfkl_walk( $n['elements'], $page, $hits );
		}
	}
}

foreach ( array( 9 => 'en', 6611 => 'ru', 11248 => 'tr' ) as $id => $lang ) {
	$raw  = get_post_meta( $id, '_elementor_data', true );
	$data = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
	$hits = array();
	cfkl_walk( is_array( $data ) ? $data : array(), $lang . ':' . $id, $hits );
	WP_CLI::log( "==== {$lang} {$id} hits=" . count( $hits ) );
	foreach ( $hits as $h ) {
		WP_CLI::log( wp_json_encode( $h, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) );
	}
}
