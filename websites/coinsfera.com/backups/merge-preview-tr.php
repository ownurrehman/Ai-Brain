<?php
/**
 * Compare widget types/fields between live TR (EN layout) and WPML job 380 translated JSON.
 *
 * Run: wp eval-file merge-preview-tr.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

function cfkl_wpml_decode( $b64 ) {
	$raw = base64_decode( (string) $b64 );
	if ( false === $raw || '' === $raw ) {
		return '';
	}
	foreach ( array( 'gzuncompress', 'gzinflate', 'gzdecode' ) as $fn ) {
		$u = @$fn( $raw );
		if ( is_string( $u ) && '' !== $u ) {
			return $u;
		}
	}
	return $raw;
}

function cfkl_widgets( $nodes, &$out ) {
	if ( ! is_array( $nodes ) ) {
		return;
	}
	foreach ( $nodes as $n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		if ( ( $n['elType'] ?? '' ) === 'widget' ) {
			$w = $n['widgetType'] ?? '?';
			$out[ $w ] = $out[ $w ] ?? array();
			foreach ( (array) ( $n['settings'] ?? array() ) as $k => $v ) {
				if ( is_string( $v ) && '' !== $v && ! is_numeric( $v ) ) {
					$out[ $w ][ $k ] = $v;
				}
			}
		}
		if ( ! empty( $n['elements'] ) ) {
			cfkl_widgets( $n['elements'], $out );
		}
	}
}

global $wpdb;
$row = $wpdb->get_row( "SELECT field_data, field_data_translated FROM {$wpdb->prefix}icl_translate WHERE job_id=380 AND field_type='field-_elementor_data-0'" );
$tr   = json_decode( cfkl_wpml_decode( $row->field_data_translated ), true );
$live = json_decode( (string) get_post_meta( 11226, '_elementor_data', true ), true );

$tw = array();
$lw = array();
cfkl_widgets( $tr, $tw );
cfkl_widgets( $live, $lw );

WP_CLI::log( 'translated widgets: ' . implode( ', ', array_keys( $tw ) ) );
WP_CLI::log( 'live widgets: ' . implode( ', ', array_keys( $lw ) ) );

$same = 0;
$diff = 0;
$missing_widget = 0;
foreach ( $lw as $w => $fields ) {
	if ( ! isset( $tw[ $w ] ) ) {
		WP_CLI::log( "LIVE ONLY widget {$w}" );
		++$missing_widget;
		continue;
	}
	foreach ( $fields as $k => $v ) {
		$tv = $tw[ $w ][ $k ] ?? null;
		if ( null === $tv ) {
			continue;
		}
		if ( $tv === $v ) {
			++$same;
		} else {
			++$diff;
			if ( $diff <= 12 ) {
				WP_CLI::log( "DIFF {$w}.{$k}" );
				WP_CLI::log( '  EN: ' . substr( $v, 0, 90 ) );
				WP_CLI::log( '  TR: ' . substr( $tv, 0, 90 ) );
			}
		}
	}
}
WP_CLI::log( "same={$same} translated_fields={$diff} live_only_widgets={$missing_widget}" );

WP_CLI::log( '==== package strings job 380 ====' );
$pkgs = $wpdb->get_results( "SELECT field_type, field_data, field_data_translated FROM {$wpdb->prefix}icl_translate WHERE job_id=380 AND field_type LIKE 'package-string%'" );
foreach ( $pkgs as $p ) {
	$s = cfkl_wpml_decode( $p->field_data );
	$t = cfkl_wpml_decode( $p->field_data_translated );
	WP_CLI::log( $p->field_type );
	WP_CLI::log( '  src: ' . substr( $s, 0, 100 ) );
	WP_CLI::log( '  tr:  ' . substr( $t, 0, 100 ) );
}
