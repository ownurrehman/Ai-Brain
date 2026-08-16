<?php
/**
 * Diagnose why RU sell-cryptocurrency still shows English after ATE.
 *
 * Run: wp eval-file inspect-sell-ru-wpml.php
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

function cfkl_banner_bits( $json, $label ) {
	$data = json_decode( (string) $json, true );
	WP_CLI::log( "{$label} json=" . ( is_array( $data ) ? 'ok' : 'FAIL' ) . ' bytes=' . strlen( (string) $json ) );
	if ( ! is_array( $data ) ) {
		return;
	}
	$walk = function ( $nodes ) use ( &$walk, $label ) {
		if ( ! is_array( $nodes ) ) {
			return;
		}
		foreach ( $nodes as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			$w = $n['widgetType'] ?? '';
			if ( 'cryptocurrency_inner_banner' === $w ) {
				$s = $n['settings'] ?? array();
				WP_CLI::log( "  banner tag=" . substr( (string) ( $s['cryptocurrency_inner_banner_tag_line'] ?? '' ), 0, 80 ) );
				WP_CLI::log( "  banner title=" . substr( (string) ( $s['cryptocurrency_inner_banner_title'] ?? '' ), 0, 120 ) );
				WP_CLI::log( "  banner btn=" . substr( (string) ( $s['cryptocurrency_inner_banner_btn_lbl'] ?? '' ), 0, 80 ) );
			}
			if ( ! empty( $n['elements'] ) ) {
				$walk( $n['elements'] );
			}
		}
	};
	$walk( $data );
}

global $wpdb;

$slug = 'sell-cryptocurrency-in-istanbul';
$pages = $wpdb->get_results(
	$wpdb->prepare(
		"SELECT p.ID, p.post_title, p.post_name, t.language_code, t.trid, t.source_language_code
		 FROM {$wpdb->posts} p
		 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page'
		 WHERE p.post_name=%s OR p.post_name LIKE %s
		 ORDER BY t.language_code",
		$slug,
		$slug . '%'
	)
);

WP_CLI::log( '==== pages matching sell-cryptocurrency-in-istanbul ====' );
foreach ( $pages as $p ) {
	WP_CLI::log( "id={$p->ID} lang={$p->language_code} src={$p->source_language_code} trid={$p->trid} name={$p->post_name} title={$p->post_title}" );
}

if ( ! $pages ) {
	$pages = $wpdb->get_results(
		"SELECT p.ID, p.post_title, p.post_name, t.language_code, t.trid, t.source_language_code
		 FROM {$wpdb->posts} p
		 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page'
		 WHERE p.post_name LIKE '%sell-cryptocurrency%'
		 ORDER BY t.language_code"
	);
	WP_CLI::log( '==== fallback like sell-cryptocurrency ====' );
	foreach ( $pages as $p ) {
		WP_CLI::log( "id={$p->ID} lang={$p->language_code} src={$p->source_language_code} trid={$p->trid} name={$p->post_name} title={$p->post_title}" );
	}
}

$by_lang = array();
foreach ( $pages as $p ) {
	$by_lang[ $p->language_code ] = $p;
}

$settings = get_option( 'icl_sitepress_settings' );
$cf       = $settings['translation-management']['custom_fields_translation'] ?? array();
WP_CLI::log( '==== WPML field modes ====' );
foreach ( array( '_elementor_data', '_elementor_edit_mode', '_elementor_page_settings', '_elementor_css' ) as $k ) {
	WP_CLI::log( $k . ' => ' . ( $cf[ $k ] ?? 'UNSET' ) . ' (0 ignore, 1 copy, 2 translate)' );
}
WP_CLI::log( 'doc_translation_method=' . ( $settings['translation-management']['doc_translation_method'] ?? '' ) );

foreach ( $by_lang as $lang => $p ) {
	$id   = (int) $p->ID;
	$live = (string) get_post_meta( $id, '_elementor_data', true );
	WP_CLI::log( "==== LIVE {$lang} {$id} ====" );
	WP_CLI::log( 'native=' . (string) get_post_meta( $id, '_wpml_post_translation_editor_native', true ) );
	WP_CLI::log( 'edit_mode=' . (string) get_post_meta( $id, '_elementor_edit_mode', true ) );
	cfkl_banner_bits( $live, "live-{$lang}" );

	$tid = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type='post_page'",
			$id
		)
	);
	$status = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT rid, status, needs_update, md5, translation_service FROM {$wpdb->prefix}icl_translation_status WHERE translation_id=%d",
			$tid
		)
	);
	if ( $status ) {
		WP_CLI::log( "status rid={$status->rid} status={$status->status} needs_update={$status->needs_update} svc={$status->translation_service}" );
	} else {
		WP_CLI::log( 'no icl_translation_status row' );
		continue;
	}

	$jobs = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT job_id, translator_id, translated, revision, translated_date FROM {$wpdb->prefix}icl_translate_job WHERE rid=%d ORDER BY job_id DESC LIMIT 5",
			$status->rid
		)
	);
	foreach ( $jobs as $job ) {
		WP_CLI::log( "job {$job->job_id} translated={$job->translated} rev={$job->revision} date={$job->translated_date}" );
	}

	if ( ! $jobs ) {
		continue;
	}

	$job_id = (int) $jobs[0]->job_id;
	$fields = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT field_type, field_format, LENGTH(field_data) src_len, LENGTH(field_data_translated) tr_len, field_finished
			 FROM {$wpdb->prefix}icl_translate WHERE job_id=%d ORDER BY tid",
			$job_id
		)
	);
	WP_CLI::log( "job {$job_id} fields=" . count( $fields ) );
	foreach ( $fields as $f ) {
		WP_CLI::log( "  {$f->field_type} fmt={$f->field_format} src={$f->src_len} tr={$f->tr_len} done={$f->field_finished}" );
	}

	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT field_data, field_data_translated FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type=%s",
			$job_id,
			'field-_elementor_data-0'
		)
	);
	if ( ! $row ) {
		WP_CLI::log( 'NO field-_elementor_data-0 in latest job' );
		$any = $wpdb->get_col(
			$wpdb->prepare(
				"SELECT DISTINCT field_type FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type LIKE %s",
				$job_id,
				'%elementor%'
			)
		);
		WP_CLI::log( 'elementor-like types: ' . implode( ',', $any ) );
		continue;
	}

	$src = cfkl_decode( $row->field_data );
	$tr  = cfkl_decode( $row->field_data_translated );
	WP_CLI::log( 'src==live ' . ( $src === $live ? 'yes' : 'no' ) );
	WP_CLI::log( 'tr==live  ' . ( $tr === $live ? 'yes' : 'no' ) );
	WP_CLI::log( 'tr==src   ' . ( $tr === $src ? 'yes' : 'no' ) );
	cfkl_banner_bits( $src, "job-src-{$lang}" );
	cfkl_banner_bits( $tr, "job-tr-{$lang}" );

	$needles = array(
		'Sell Cryptocurrency in Istanbul, Turkey',
		'Sell Cryptocurrency in Istanbul For Cash',
		'Contact Us',
		'Продажа',
		'криптовалют',
		'Связаться',
		'Стамбул',
	);
	foreach ( array( 'live' => $live, 'job-tr' => $tr, 'job-src' => $src ) as $lab => $hay ) {
		foreach ( $needles as $s ) {
			WP_CLI::log( ( false !== strpos( (string) $hay, $s ) ? 'HIT  ' : 'miss ' ) . "{$lab} :: {$s}" );
		}
	}
}
