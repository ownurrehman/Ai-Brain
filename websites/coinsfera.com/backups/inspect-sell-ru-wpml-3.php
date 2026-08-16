<?php
/**
 * Pull banner title/desc from EN/RU/TR and render Elementor HTML snippets.
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

function cfkl_find_settings( $id, $widget ) {
	$raw  = get_post_meta( $id, '_elementor_data', true );
	$data = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
	$found = null;
	$walk = function ( $nodes ) use ( &$walk, &$found, $widget ) {
		if ( ! is_array( $nodes ) ) {
			return;
		}
		foreach ( $nodes as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			if ( ( $n['widgetType'] ?? '' ) === $widget ) {
				$found = $n['settings'] ?? array();
				return;
			}
			if ( ! empty( $n['elements'] ) ) {
				$walk( $n['elements'] );
			}
		}
	};
	$walk( is_array( $data ) ? $data : array() );
	return $found;
}

$keys = array(
	'cryptocurrency_page_banner_tag_line',
	'cryptocurrency_page_banner_title',
	'cryptocurrency_page_banner_desc',
	'cryptocurrency_page_banner_btn_lbl',
);

foreach ( array( 5444 => 'en', 15673 => 'ru', 11236 => 'tr' ) as $id => $lang ) {
	$s = cfkl_find_settings( $id, 'cryptocurrency_page_banner' );
	WP_CLI::log( "==== {$lang} {$id} banner keys=" . ( is_array( $s ) ? count( $s ) : 0 ) );
	if ( ! is_array( $s ) ) {
		continue;
	}
	foreach ( $keys as $k ) {
		$v = $s[ $k ] ?? '[MISSING]';
		if ( is_array( $v ) ) {
			$v = wp_json_encode( $v );
		}
		WP_CLI::log( $k . '=' . substr( wp_strip_all_tags( (string) $v ), 0, 160 ) );
	}
}

if ( class_exists( '\Elementor\Plugin' ) ) {
	foreach ( array( 5444, 15673 ) as $id ) {
		$html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true );
		$plain = wp_strip_all_tags( (string) $html );
		WP_CLI::log( "==== rendered {$id} bytes=" . strlen( (string) $html ) );
		if ( preg_match( '/Sell Cryptocurrency in Istanbul, Turkey|Продажа криптовалюты в Стамбуле/u', $plain, $m ) ) {
			WP_CLI::log( 'HIT rendered: ' . $m[0] );
		} else {
			WP_CLI::log( 'no banner tag in rendered text; snippet=' . substr( preg_replace( '/\s+/', ' ', $plain ), 0, 220 ) );
		}
	}
}

WP_CLI::log( '==== wpml-config widgets on live ====' );
foreach ( array(
	WP_CONTENT_DIR . '/themes/coinsfera/wpml-config.xml',
	WP_CONTENT_DIR . '/plugins/coinsfera-plugin/wpml-config.xml',
) as $path ) {
	WP_CLI::log( $path . ' exists=' . ( file_exists( $path ) ? 'yes' : 'no' ) );
	if ( file_exists( $path ) ) {
		$xml = file_get_contents( $path );
		WP_CLI::log( '  page_banner=' . ( false !== strpos( $xml, 'cryptocurrency_page_banner' ) ? 'yes' : 'NO' ) );
		WP_CLI::log( '  inner_banner=' . ( false !== strpos( $xml, 'cryptocurrency_inner_banner' ) ? 'yes' : 'NO' ) );
	}
}

WP_CLI::log( '==== job 401 field types (first 40) ====' );
global $wpdb;
$fields = $wpdb->get_results( "SELECT field_type, field_format, LENGTH(field_data) src_len, LENGTH(field_data_translated) tr_len, field_finished FROM {$wpdb->prefix}icl_translate WHERE job_id=401 ORDER BY tid LIMIT 40" );
WP_CLI::log( 'count=' . $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->prefix}icl_translate WHERE job_id=401" ) );
foreach ( $fields as $f ) {
	WP_CLI::log( "  {$f->field_type} fmt={$f->field_format} src={$f->src_len} tr={$f->tr_len} done={$f->field_finished}" );
}

$banner_fields = $wpdb->get_results( "SELECT field_type, field_finished FROM {$wpdb->prefix}icl_translate WHERE job_id=401 AND field_type LIKE '%banner%'" );
WP_CLI::log( 'banner-like fields=' . count( $banner_fields ) );
foreach ( $banner_fields as $f ) {
	WP_CLI::log( '  ' . $f->field_type . ' done=' . $f->field_finished );
}
