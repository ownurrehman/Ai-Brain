<?php
/**
 * Inspect WPML job blobs vs live Elementor JSON for Buy Bitcoin TR/RU.
 *
 * Run: wp eval-file inspect-tr-wpml.php
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

function cfkl_needles( $hay, $label ) {
	$needles = array(
		'Buy Bitcoin in Istanbul Turkey',
		'Requirements to Buy',
		'Contact Us',
		'Want to spend',
		'how_to_buy_section',
		'buy_sell_section',
		'Bitcoin',
		'İstanbul',
		'nakit',
		'Satın',
		'İletişim',
		'Купить',
		'биткоин',
	);
	WP_CLI::log( "-- {$label} bytes=" . strlen( (string) $hay ) );
	foreach ( $needles as $s ) {
		WP_CLI::log( ( false !== strpos( (string) $hay, $s ) ? 'HIT  ' : 'miss ' ) . $s );
	}
}

global $wpdb;

foreach ( array( 11226 => 380, 6644 => 400 ) as $post_id => $job_id ) {
	WP_CLI::log( "==== post {$post_id} job {$job_id} ====" );
	$row = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT field_data, field_data_translated FROM {$wpdb->prefix}icl_translate WHERE job_id=%d AND field_type=%s",
			$job_id,
			'field-_elementor_data-0'
		)
	);
	if ( ! $row ) {
		WP_CLI::log( 'no elementor field in job' );
		continue;
	}
	$src  = cfkl_wpml_decode( $row->field_data );
	$tr   = cfkl_wpml_decode( $row->field_data_translated );
	$live = (string) get_post_meta( $post_id, '_elementor_data', true );
	WP_CLI::log( 'src==live ' . ( $src === $live ? 'yes' : 'no' ) );
	WP_CLI::log( 'tr==live  ' . ( $tr === $live ? 'yes' : 'no' ) );
	WP_CLI::log( 'tr==src   ' . ( $tr === $src ? 'yes' : 'no' ) );
	cfkl_needles( $src, 'src' );
	cfkl_needles( $tr, 'tr' );
	cfkl_needles( $live, 'live' );

	if ( preg_match( '/"cryptocurrency_inner_banner_title":"([^"]{0,120})"/', $tr, $m ) ) {
		WP_CLI::log( 'tr banner title: ' . $m[1] );
	}
	if ( preg_match( '/"cryptocurrency_inner_banner_title":"([^"]{0,120})"/', $live, $m ) ) {
		WP_CLI::log( 'live banner title: ' . $m[1] );
	}
}

WP_CLI::log( '==== ATE queue ====' );
$q = get_option( 'ATE_RETURNED_JOBS_QUEUE' );
WP_CLI::log( is_array( $q ) || is_string( $q ) ? wp_json_encode( $q ) : var_export( $q, true ) );

WP_CLI::log( '==== translatable field types in job 380 ====' );
$types = $wpdb->get_results( 'SELECT field_type, field_finished, CHAR_LENGTH(field_data_translated) l FROM wp_icl_translate WHERE job_id=380 AND field_translate=1' );
foreach ( $types as $t ) {
	WP_CLI::log( $t->field_type . ' finished=' . $t->field_finished . ' tr_len=' . $t->l );
}
