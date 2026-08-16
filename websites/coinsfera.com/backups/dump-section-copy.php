<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}

function cfkl_widget_settings( $post_id, $widget ) {
	$j = json_decode( (string) get_post_meta( $post_id, '_elementor_data', true ), true );
	$found = array();
	$walk  = function ( $nodes ) use ( &$walk, &$found, $widget ) {
		if ( ! is_array( $nodes ) ) {
			return;
		}
		foreach ( $nodes as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			if ( ( $n['widgetType'] ?? '' ) === $widget ) {
				$found[] = $n['settings'] ?? array();
			}
			if ( ! empty( $n['elements'] ) ) {
				$walk( $n['elements'] );
			}
		}
	};
	$walk( $j );
	return $found;
}

function cfkl_dump_widget( $id, $widget ) {
	WP_CLI::log( "== {$id} " . get_the_title( $id ) . " {$widget} ==" );
	foreach ( cfkl_widget_settings( $id, $widget ) as $s ) {
		foreach ( $s as $k => $v ) {
			if ( is_string( $v ) && preg_match( '/title|desc|lbl|note|text/i', $k ) && strlen( $v ) > 1 ) {
				WP_CLI::log( "$k: $v" );
			}
			if ( is_array( $v ) && preg_match( '/items/', (string) $k ) ) {
				foreach ( $v as $i => $item ) {
					if ( ! is_array( $item ) ) {
						continue;
					}
					WP_CLI::log( "item$i title=" . ( $item['title'] ?? '' ) );
					WP_CLI::log( "item$i desc=" . ( $item['desc'] ?? '' ) );
					WP_CLI::log( "item$i counter=" . ( $item['counter'] ?? '' ) );
				}
			}
		}
	}
}

foreach ( array( 2036, 11226, 11248, 14459, 11220 ) as $id ) {
	cfkl_dump_widget( $id, 'buy_sell_section' );
	cfkl_dump_widget( $id, 'how_to_buy_section' );
}

WP_CLI::log( '==== global 26211 ====' );
$g = get_post( 26211 );
if ( $g ) {
	WP_CLI::log( $g->post_title . ' type=' . $g->post_type . ' lang=' . apply_filters( 'wpml_element_language_code', null, array( 'element_id' => 26211, 'element_type' => 'post_' . $g->post_type ) ) );
	$tr = apply_filters( 'wpml_object_id', 26211, $g->post_type, false, 'tr' );
	WP_CLI::log( 'tr object=' . $tr );
	if ( $tr ) {
		$gs = cfkl_widget_settings( (int) $tr, 'homepage_community_section' );
		if ( ! $gs ) {
			$d = (string) get_post_meta( $tr, '_elementor_data', true );
			WP_CLI::log( 'tr global bytes=' . strlen( $d ) );
			if ( preg_match( '/homepage_community_section_title":"([^"]+)"/', $d, $m ) ) {
				WP_CLI::log( 'tr community title=' . $m[1] );
			}
		} else {
			WP_CLI::log( 'tr title=' . ( $gs[0]['homepage_community_section_title'] ?? '' ) );
		}
	}
}

WP_CLI::log( '==== EN 2036 buy_sell buttons html? ====' );
$en = cfkl_widget_settings( 2036, 'buy_sell_section' );
if ( $en ) {
	foreach ( array( 'buy_sell_section_buy_btn_title', 'buy_sell_section_sell_btn_title', 'buy_sell_section_consultancy_btn_title' ) as $k ) {
		WP_CLI::log( $k . '=' . ( $en[0][ $k ] ?? '' ) );
	}
}
