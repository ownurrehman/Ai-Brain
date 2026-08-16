<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function cfkl_en_leftovers( $id ) {
	$raw  = get_post_meta( $id, '_elementor_data', true );
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
				if ( ! is_string( $v ) || strlen( $v ) < 8 || strlen( $v ) > 180 ) {
					continue;
				}
				if ( preg_match( '/[İıŞşĞğÜüÖöÇçА-Яа-яЁё]/u', $v ) ) {
					continue;
				}
				if ( preg_match( '/[A-Za-z]{4,}/', $v ) && ! preg_match( '/^(https?:|#[a-f0-9]|rgb|rgba|solid|none|left|right|center)/i', $v ) ) {
					$out[] = $w . '.' . $k . '=' . $v;
				}
			}
			if ( ! empty( $n['elements'] ) ) {
				$walk( $n['elements'] );
			}
		}
	};
	$walk( is_array( $data ) ? $data : array() );
	return $out;
}

foreach ( array( 11237 => 'tr-eth', 11570 => 'tr-usdt' ) as $id => $lab ) {
	WP_CLI::log( "==== {$lab} leftovers ====" );
	foreach ( cfkl_en_leftovers( $id ) as $line ) {
		WP_CLI::log( $line );
	}
}
