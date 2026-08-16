<?php
/**
 * Deeper inspect for sell-cryptocurrency RU 15673.
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

function cfkl_widgets( $id ) {
	$raw  = get_post_meta( $id, '_elementor_data', true );
	$data = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
	$out  = array();
	$texts = array();
	$walk = function ( $nodes ) use ( &$walk, &$out, &$texts ) {
		if ( ! is_array( $nodes ) ) {
			return;
		}
		foreach ( $nodes as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			if ( ( $n['elType'] ?? '' ) === 'widget' ) {
				$w         = $n['widgetType'] ?? '?';
				$out[ $w ] = ( $out[ $w ] ?? 0 ) + 1;
				if ( count( $texts ) < 12 ) {
					$s = $n['settings'] ?? array();
					foreach ( $s as $k => $v ) {
						if ( is_string( $v ) && strlen( $v ) > 8 && strlen( $v ) < 180 && false === strpos( $v, 'http' ) && ! preg_match( '/^[a-f0-9]{6,}$/i', $v ) ) {
							$texts[] = $w . '.' . $k . '=' . $v;
							if ( count( $texts ) >= 12 ) {
								break;
							}
						}
					}
				}
			}
			if ( ! empty( $n['elements'] ) ) {
				$walk( $n['elements'] );
			}
		}
	};
	$walk( is_array( $data ) ? $data : array() );
	return array( $out, $texts, is_array( $data ) );
}

global $wpdb;

foreach ( array( 5444 => 'en', 15673 => 'ru', 11236 => 'tr' ) as $id => $lang ) {
	list( $w, $texts, $ok ) = cfkl_widgets( $id );
	WP_CLI::log( "==== {$lang} {$id} widgets=" . array_sum( $w ) . " json=" . ( $ok ? 'ok' : 'fail' ) . ' cache=' . strlen( (string) get_post_meta( $id, '_elementor_element_cache', true ) ) );
	foreach ( $w as $k => $v ) {
		WP_CLI::log( "  {$v} x {$k}" );
	}
	foreach ( $texts as $t ) {
		WP_CLI::log( '  TEXT ' . $t );
	}
}

WP_CLI::log( '==== jobs for rid 2247 (ru) and 698 (tr) ====' );
foreach ( array( 2247, 698, 2248 ) as $rid ) {
	$jobs = $wpdb->get_results( $wpdb->prepare( "SELECT * FROM {$wpdb->prefix}icl_translate_job WHERE rid=%d ORDER BY job_id DESC LIMIT 8", $rid ) );
	WP_CLI::log( "rid {$rid} jobs=" . count( $jobs ) );
	foreach ( $jobs as $j ) {
		WP_CLI::log( json_encode( $j ) );
	}
}

WP_CLI::log( '==== recent jobs mentioning 15673 or sell ====' );
$recent = $wpdb->get_results(
	"SELECT j.job_id, j.rid, j.translated, j.translated_date, s.translation_id, t.element_id, t.language_code, s.status, s.needs_update
	 FROM {$wpdb->prefix}icl_translate_job j
	 JOIN {$wpdb->prefix}icl_translation_status s ON s.rid=j.rid
	 JOIN {$wpdb->prefix}icl_translations t ON t.translation_id=s.translation_id
	 WHERE t.element_id IN (5444,15673,11236)
	 ORDER BY j.job_id DESC LIMIT 20"
);
foreach ( $recent as $r ) {
	WP_CLI::log( json_encode( $r ) );
}

WP_CLI::log( '==== latest 15 jobs any language ====' );
$latest = $wpdb->get_results(
	"SELECT j.job_id, j.rid, j.translated, j.translated_date, t.element_id, t.language_code, s.status
	 FROM {$wpdb->prefix}icl_translate_job j
	 JOIN {$wpdb->prefix}icl_translation_status s ON s.rid=j.rid
	 JOIN {$wpdb->prefix}icl_translations t ON t.translation_id=s.translation_id
	 WHERE t.element_type='post_page'
	 ORDER BY j.job_id DESC LIMIT 15"
);
foreach ( $latest as $r ) {
	WP_CLI::log( json_encode( $r ) );
}

WP_CLI::log( '==== ate original id from screenshot? search icl_translate for Russian banner ====' );
$hit = $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}icl_translate WHERE field_data_translated LIKE '%Продаж%' OR field_data_translated LIKE '%криптовалют%' " );
WP_CLI::log( 'icl_translate rows with Продаж/криптовалют=' . $hit );

$hit2 = $wpdb->get_results( "SELECT job_id, field_type, LEFT(field_data_translated, 80) snippet FROM {$wpdb->prefix}icl_translate WHERE field_data_translated LIKE '%Sell Cryptocurrency in Istanbul For Cash%' LIMIT 10" );
WP_CLI::log( 'rows with English banner title in translated field=' . count( $hit2 ) );
foreach ( $hit2 as $r ) {
	WP_CLI::log( json_encode( $r ) );
}
