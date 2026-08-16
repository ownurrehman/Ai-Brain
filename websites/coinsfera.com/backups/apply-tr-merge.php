<?php
/**
 * Copy Turkish strings from WPML job 380 onto the current Buy Bitcoin TR layout.
 *
 * The job is 100% translated against the previous Elementor document. The live
 * page uses the newer English layout, so WPML never wrote those strings back.
 *
 * Run: wp eval-file apply-tr-merge.php
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

function cfkl_skip_setting_key( $key ) {
	$key = (string) $key;
	return (bool) preg_match(
		'/(_id$|css|margin|padding|width|height|gap|align|background|border|shadow|animation|hover|overlay|opacity|zindex|z_index|__dynamic|__globals|_element_id)/i',
		$key
	);
}

function cfkl_merge_settings( $en, $tr ) {
	if ( ! is_array( $en ) || ! is_array( $tr ) ) {
		return $en;
	}

	foreach ( $en as $k => $v ) {
		if ( ! array_key_exists( $k, $tr ) || cfkl_skip_setting_key( $k ) ) {
			continue;
		}
		$tv = $tr[ $k ];
		if ( is_string( $v ) && is_string( $tv ) && '' !== $tv ) {
			$en[ $k ] = $tv;
		} elseif ( is_array( $v ) && is_array( $tv ) ) {
			$is_list = array_keys( $v ) === range( 0, count( $v ) - 1 );
			if ( $is_list ) {
				foreach ( $v as $i => $item ) {
					if ( isset( $tv[ $i ] ) && is_array( $item ) && is_array( $tv[ $i ] ) ) {
						$en[ $k ][ $i ] = cfkl_merge_settings( $item, $tv[ $i ] );
					} elseif ( isset( $tv[ $i ] ) && is_string( $item ) && is_string( $tv[ $i ] ) && '' !== $tv[ $i ] ) {
						$en[ $k ][ $i ] = $tv[ $i ];
					}
				}
			} else {
				$en[ $k ] = cfkl_merge_settings( $v, $tv );
			}
		}
	}

	return $en;
}

function cfkl_collect_widgets_by_type( $nodes, &$bucket ) {
	if ( ! is_array( $nodes ) ) {
		return;
	}
	foreach ( $nodes as $n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		if ( ( $n['elType'] ?? '' ) === 'widget' ) {
			$w = $n['widgetType'] ?? '?';
			$bucket[ $w ][] = $n;
		}
		if ( ! empty( $n['elements'] ) ) {
			cfkl_collect_widgets_by_type( $n['elements'], $bucket );
		}
	}
}

function cfkl_apply_widgets( $nodes, &$bucket, &$stats ) {
	if ( ! is_array( $nodes ) ) {
		return $nodes;
	}
	foreach ( $nodes as $i => $n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		if ( ( $n['elType'] ?? '' ) === 'widget' ) {
			$w = $n['widgetType'] ?? '?';
			if ( ! empty( $bucket[ $w ] ) ) {
				$src = array_shift( $bucket[ $w ] );
				$before = wp_json_encode( $n['settings'] ?? array() );
				$n['settings'] = cfkl_merge_settings( $n['settings'] ?? array(), $src['settings'] ?? array() );
				$after = wp_json_encode( $n['settings'] ?? array() );
				if ( $before !== $after ) {
					++$stats['updated'];
					$stats['widgets'][] = $w;
				} else {
					++$stats['unchanged'];
				}
			} else {
				++$stats['no_source'];
				$stats['missing'][] = $w;
			}
		}
		if ( ! empty( $n['elements'] ) ) {
			$n['elements'] = cfkl_apply_widgets( $n['elements'], $bucket, $stats );
		}
		$nodes[ $i ] = $n;
	}
	return $nodes;
}

$tr_id  = 11226;
$en_id  = 2036;
$job_id = 380;
$bak    = WP_CONTENT_DIR . '/uploads/cfkl-backups/buy-bitcoin-wpml-pilot-20260816';

global $wpdb;

$row = $wpdb->get_row(
	$wpdb->prepare(
		"SELECT field_data_translated FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type=%s",
		$job_id,
		'field-_elementor_data-0'
	)
);

if ( ! $row ) {
	WP_CLI::error( 'WPML job 380 has no _elementor_data' );
}

$translated = json_decode( cfkl_wpml_decode( $row->field_data_translated ), true );
$live       = json_decode( (string) get_post_meta( $tr_id, '_elementor_data', true ), true );
$en         = json_decode( (string) get_post_meta( $en_id, '_elementor_data', true ), true );

if ( ! is_array( $translated ) || ! is_array( $live ) ) {
	WP_CLI::error( 'Could not decode Elementor JSON' );
}

if ( ! is_dir( $bak ) ) {
	wp_mkdir_p( $bak );
}
file_put_contents( $bak . '/tr-11226-_elementor_data-before-merge.json', get_post_meta( $tr_id, '_elementor_data', true ) );

$bucket = array();
cfkl_collect_widgets_by_type( $translated, $bucket );

$stats = array(
	'updated'  => 0,
	'unchanged'=> 0,
	'no_source'=> 0,
	'widgets'  => array(),
	'missing'  => array(),
);
$merged = cfkl_apply_widgets( $live, $bucket, $stats );
$json   = wp_json_encode( $merged );

update_metadata( 'post', $tr_id, '_elementor_data', wp_slash( $json ) );
delete_post_meta( $tr_id, '_elementor_css' );

$title_row = $wpdb->get_row(
	$wpdb->prepare(
		"SELECT field_data_translated FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type=%s",
		$job_id,
		'title'
	)
);
if ( $title_row ) {
	$title = cfkl_wpml_decode( $title_row->field_data_translated );
	if ( is_string( $title ) && '' !== trim( $title ) ) {
		wp_update_post(
			array(
				'ID'         => $tr_id,
				'post_title' => $title,
			)
		);
		WP_CLI::log( 'title=' . $title );
	}
}

$en_json = is_array( $en ) ? wp_json_encode( $en ) : (string) get_post_meta( $en_id, '_elementor_data', true );
$wpdb->update(
	$wpdb->prefix . 'icl_translate',
	array(
		'field_data'             => base64_encode( gzcompress( $en_json, 9 ) ),
		'field_data_translated'  => base64_encode( gzcompress( $json, 9 ) ),
		'field_finished'         => 1,
	),
	array(
		'job_id'     => $job_id,
		'field_type' => 'field-_elementor_data-0',
	)
);

$wpdb->update(
	$wpdb->prefix . 'icl_translation_status',
	array( 'needs_update' => 1 ),
	array( 'rid' => 684 )
);

if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

$live2 = (string) get_post_meta( $tr_id, '_elementor_data', true );
WP_CLI::log( 'updated_widgets=' . $stats['updated'] . ' unchanged=' . $stats['unchanged'] . ' no_source=' . $stats['no_source'] );
WP_CLI::log( 'copied: ' . implode( ', ', array_unique( $stats['widgets'] ) ) );
WP_CLI::log( 'left_english: ' . implode( ', ', array_unique( $stats['missing'] ) ) );
WP_CLI::log( 'live has Istanbul\'da: ' . ( false !== strpos( $live2, "Istanbul'da" ) ? 'yes' : 'no' ) );
WP_CLI::log( 'live has Buy Bitcoin in Istanbul Turkey: ' . ( false !== strpos( $live2, 'Buy Bitcoin in Istanbul Turkey with cash' ) ? 'yes' : 'no' ) );
WP_CLI::log( 'live has how_to_buy_section: ' . ( false !== strpos( $live2, 'how_to_buy_section' ) ? 'yes' : 'no' ) );
