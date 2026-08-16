<?php
/**
 * One-shot: backup Buy Bitcoin RU/TR Elementor JSON, then copy the English
 * layout onto those translations so WPML editor owns the strings.
 *
 * Run: wp eval-file wp-content/uploads/cfkl-backups/reconnect-buy-bitcoin.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

$en = 2036;
$ru = 6644;
$tr = 11226;
$bak = WP_CONTENT_DIR . '/uploads/cfkl-backups/buy-bitcoin-wpml-pilot-20260816';

if ( ! is_dir( $bak ) ) {
	wp_mkdir_p( $bak );
}

$keys = array(
	'_elementor_data',
	'_elementor_page_settings',
	'_elementor_edit_mode',
	'_elementor_version',
	'_wpml_post_translation_editor_native',
);

foreach ( array( 'en' => $en, 'ru' => $ru, 'tr' => $tr ) as $lang => $id ) {
	foreach ( $keys as $key ) {
		$val = get_post_meta( $id, $key, true );
		file_put_contents(
			"$bak/{$lang}-{$id}-{$key}.json",
			is_string( $val ) ? $val : wp_json_encode( $val )
		);
	}
	file_put_contents(
		"$bak/{$lang}-{$id}-post.json",
		wp_json_encode(
			array(
				'ID'       => $id,
				'title'    => get_the_title( $id ),
				'status'   => get_post_status( $id ),
				'modified' => get_post_field( 'post_modified_gmt', $id ),
			)
		)
	);
}

WP_CLI::log( "backed up to $bak" );

$en_data     = get_post_meta( $en, '_elementor_data', true );
$en_settings = get_post_meta( $en, '_elementor_page_settings', true );
$en_mode     = get_post_meta( $en, '_elementor_edit_mode', true );
$en_ver      = get_post_meta( $en, '_elementor_version', true );

if ( ! is_string( $en_data ) || strlen( $en_data ) < 100 ) {
	WP_CLI::error( 'English Elementor data missing' );
}

foreach ( array( $ru, $tr ) as $id ) {
	update_post_meta( $id, '_elementor_data', $en_data );
	if ( '' !== $en_settings ) {
		update_post_meta( $id, '_elementor_page_settings', $en_settings );
	}
	if ( '' !== $en_mode ) {
		update_post_meta( $id, '_elementor_edit_mode', $en_mode );
	}
	if ( '' !== $en_ver ) {
		update_post_meta( $id, '_elementor_version', $en_ver );
	}
	delete_post_meta( $id, '_wpml_post_translation_editor_native' );
	delete_post_meta( $id, '_elementor_css' );
	WP_CLI::log( 'copied EN layout to ' . $id . ' (' . get_the_title( $id ) . ') bytes=' . strlen( (string) get_post_meta( $id, '_elementor_data', true ) ) );
}

delete_post_meta( $en, '_wpml_post_translation_editor_native' );

global $wpdb;
$trid  = $wpdb->get_var( $wpdb->prepare( "SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type='post_page'", $en ) );
$trans = $wpdb->get_results( $wpdb->prepare( "SELECT translation_id, element_id, language_code FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND element_type='post_page'", $trid ) );

foreach ( $trans as $t ) {
	if ( 'en' === $t->language_code ) {
		continue;
	}
	$status = $wpdb->get_row( $wpdb->prepare( "SELECT rid, status, needs_update FROM {$wpdb->prefix}icl_translation_status WHERE translation_id=%d", $t->translation_id ) );
	WP_CLI::log( sprintf( 'lang=%s translation_id=%s %s', $t->language_code, $t->translation_id, $status ? "rid={$status->rid} st={$status->status} needs={$status->needs_update}" : 'no status row' ) );
	if ( $status ) {
		$wpdb->update( $wpdb->prefix . 'icl_translation_status', array( 'needs_update' => 1 ), array( 'rid' => $status->rid ) );
	}
}

if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
	WP_CLI::log( 'elementor css cache cleared' );
}

delete_option( 'wpml_config_files_arr' );

$same_ru = get_post_meta( $ru, '_elementor_data', true ) === $en_data;
$same_tr = get_post_meta( $tr, '_elementor_data', true ) === $en_data;
WP_CLI::log( 'RU matches EN: ' . ( $same_ru ? 'yes' : 'no' ) );
WP_CLI::log( 'TR matches EN: ' . ( $same_tr ? 'yes' : 'no' ) );
WP_CLI::log( 'native EN=' . get_post_meta( $en, '_wpml_post_translation_editor_native', true ) . ' RU=' . get_post_meta( $ru, '_wpml_post_translation_editor_native', true ) . ' TR=' . get_post_meta( $tr, '_wpml_post_translation_editor_native', true ) );
