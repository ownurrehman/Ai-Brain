<?php
/**
 * Diagnose TR sell-ethereum and sell-tether vs EN and WPML jobs.
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

function cfkl_decode( $b64 ) {
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

function cfkl_banner( $id ) {
	$raw  = get_post_meta( $id, '_elementor_data', true );
	$data = is_array( $raw ) ? $raw : json_decode( (string) $raw, true );
	$out  = array(
		'json'    => is_array( $data ) ? 'ok' : 'FAIL',
		'bytes'   => strlen( is_string( $raw ) ? $raw : wp_json_encode( $raw ) ),
		'widgets' => 0,
		'texts'   => array(),
	);
	$walk = function ( $nodes ) use ( &$walk, &$out ) {
		if ( ! is_array( $nodes ) ) {
			return;
		}
		foreach ( $nodes as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			if ( ( $n['elType'] ?? '' ) === 'widget' ) {
				$out['widgets']++;
				$w = $n['widgetType'] ?? '?';
				$s = $n['settings'] ?? array();
				foreach ( $s as $k => $v ) {
					if ( ! is_string( $v ) || strlen( $v ) < 12 || strlen( $v ) > 160 ) {
						continue;
					}
					if ( preg_match( '/^(https?:|#|rgb)/i', $v ) ) {
						continue;
					}
					if ( count( $out['texts'] ) < 8 ) {
						$out['texts'][] = $w . '.' . $k . '=' . $v;
					}
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

global $wpdb;

$slugs = array( 'sell-ethereum-in-istanbul', 'sell-tether-in-istanbul' );

foreach ( $slugs as $slug ) {
	WP_CLI::log( "======== {$slug} ========" );
	$pages = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT p.ID, p.post_title, p.post_modified, t.language_code, t.trid, t.source_language_code
			 FROM {$wpdb->posts} p
			 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page'
			 WHERE p.post_name=%s
			 ORDER BY t.language_code",
			$slug
		)
	);
	if ( ! $pages ) {
		$pages = $wpdb->get_results(
			$wpdb->prepare(
				"SELECT p.ID, p.post_title, p.post_modified, t.language_code, t.trid, t.source_language_code
				 FROM {$wpdb->posts} p
				 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page'
				 WHERE p.post_name LIKE %s
				 ORDER BY t.language_code",
				'%' . $wpdb->esc_like( $slug ) . '%'
			)
		);
	}
	foreach ( $pages as $p ) {
		WP_CLI::log( "id={$p->ID} lang={$p->language_code} src={$p->source_language_code} title={$p->post_title} modified={$p->post_modified}" );
		$info = cfkl_banner( (int) $p->ID );
		WP_CLI::log( "  json={$info['json']} bytes={$info['bytes']} widgets={$info['widgets']} native=" . (string) get_post_meta( $p->ID, '_wpml_post_translation_editor_native', true ) );
		foreach ( $info['texts'] as $t ) {
			WP_CLI::log( '  ' . $t );
		}

		$tid = $wpdb->get_var(
			$wpdb->prepare(
				"SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type='post_page'",
				$p->ID
			)
		);
		$status = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT rid, status, needs_update FROM {$wpdb->prefix}icl_translation_status WHERE translation_id=%d",
				$tid
			)
		);
		if ( $status ) {
			WP_CLI::log( "  status rid={$status->rid} status={$status->status} needs_update={$status->needs_update}" );
			$job = $wpdb->get_row(
				$wpdb->prepare(
					"SELECT job_id, translated, editor, editor_job_id, completed_date, ate_sync_count FROM {$wpdb->prefix}icl_translate_job WHERE rid=%d ORDER BY job_id DESC LIMIT 1",
					$status->rid
				)
			);
			if ( $job ) {
				WP_CLI::log( "  job {$job->job_id} translated={$job->translated} editor={$job->editor} ate={$job->editor_job_id} completed={$job->completed_date} sync={$job->ate_sync_count}" );
				$row = $wpdb->get_row(
					$wpdb->prepare(
						"SELECT LENGTH(field_data) src_len, LENGTH(field_data_translated) tr_len, field_finished FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type=%s",
						$job->job_id,
						'field-_elementor_data-0'
					)
				);
				if ( $row ) {
					WP_CLI::log( "  elementor field src={$row->src_len} tr={$row->tr_len} done={$row->field_finished}" );
				} else {
					WP_CLI::log( '  NO field-_elementor_data-0' );
				}
				$pkg = (int) $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type LIKE 'package-string%'",
						$job->job_id
					)
				);
				$pkg_done = (int) $wpdb->get_var(
					$wpdb->prepare(
						"SELECT COUNT(*) FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type LIKE 'package-string%' AND field_finished=1",
						$job->job_id
					)
				);
				WP_CLI::log( "  package-strings {$pkg_done}/{$pkg}" );
			} else {
				WP_CLI::log( '  no job' );
			}
		} else {
			WP_CLI::log( '  no translation status' );
		}
	}
}
