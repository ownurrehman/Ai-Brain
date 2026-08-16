<?php
/**
 * Report how WPML sees the cfkl fields.
 *
 * Run with: wp eval-file wpml-check.php
 * Read-only.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit( "Run through WP-CLI.\n" );
}

$labels = array( 0 => 'ignore', 1 => 'copy', 2 => 'translate', 3 => 'copy once' );

$settings = get_option( 'icl_sitepress_settings' );
$prefs    = array();

if ( isset( $settings['translation-management']['custom_fields_translation'] ) ) {
	foreach ( $settings['translation-management']['custom_fields_translation'] as $key => $pref ) {
		if ( 0 === strpos( $key, 'cfkl_' ) ) {
			$prefs[ $key ] = $pref;
		}
	}
}

WP_CLI::log( 'cfkl meta keys known to WPML: ' . count( $prefs ) );

$tally = array();
foreach ( $prefs as $pref ) {
	$tally[ $pref ] = isset( $tally[ $pref ] ) ? $tally[ $pref ] + 1 : 1;
}
ksort( $tally );
foreach ( $tally as $pref => $count ) {
	$label = isset( $labels[ $pref ] ) ? $labels[ $pref ] : 'unknown';
	WP_CLI::log( sprintf( '  %d (%-9s) : %d fields', $pref, $label, $count ) );
}

WP_CLI::log( '--- spot checks (expected in brackets) ---' );
$expect = array(
	'cfkl_banner_heading' => 2,
	'cfkl_banner_subtext' => 2,
	'cfkl_intro_text'     => 2,
	'cfkl_hero_image'     => 1,
	'cfkl_steps'          => 1,
	'cfkl_banner_cta_url' => 3,
	'cfkl_faq_schema'     => 1,
);
foreach ( $expect as $key => $want ) {
	$have = isset( $prefs[ $key ] ) ? $prefs[ $key ] : 'not registered';
	$mark = ( (string) $have === (string) $want ) ? 'ok' : 'CHECK';
	WP_CLI::log( sprintf( '  %-22s got %-14s want %d  %s', $key, $have, $want, $mark ) );
}

WP_CLI::log( '--- what the field group declares in code ---' );
$fields = acf_get_fields( 'group_cfkl_keyword_landing' );
$tally2 = array();
foreach ( $fields as $field ) {
	if ( empty( $field['name'] ) ) {
		continue;
	}
	$pref            = isset( $field['wpml_cf_preferences'] ) ? $field['wpml_cf_preferences'] : 'unset';
	$tally2[ $pref ] = isset( $tally2[ $pref ] ) ? $tally2[ $pref ] + 1 : 1;
}
ksort( $tally2 );
foreach ( $tally2 as $pref => $count ) {
	$label = isset( $labels[ $pref ] ) ? $labels[ $pref ] : 'unknown';
	WP_CLI::log( sprintf( '  %s (%-9s) : %d fields', $pref, $label, $count ) );
}

WP_CLI::log( '--- sub field preferences (repeater children) ---' );
foreach ( $fields as $field ) {
	if ( 'repeater' !== $field['type'] || empty( $field['sub_fields'] ) ) {
		continue;
	}
	$parts = array();
	foreach ( $field['sub_fields'] as $sub ) {
		$pref    = isset( $sub['wpml_cf_preferences'] ) ? $sub['wpml_cf_preferences'] : '?';
		$parts[] = $sub['name'] . '=' . $pref;
	}
	WP_CLI::log( sprintf( '  %-20s [%s] -> %s', $field['name'], $field['wpml_cf_preferences'], implode( ', ', $parts ) ) );
}

WP_CLI::log( '--- translations of the draft ---' );
$page_id = 28486;
if ( function_exists( 'wpml_get_language_information' ) ) {
	$info = apply_filters( 'wpml_post_language_details', null, $page_id );
	WP_CLI::log( '  draft language: ' . ( is_array( $info ) && isset( $info['language_code'] ) ? $info['language_code'] : 'unknown' ) );
}
foreach ( array( 'en', 'ru', 'tr' ) as $lang ) {
	$tid = apply_filters( 'wpml_object_id', $page_id, 'page', false, $lang );
	WP_CLI::log( sprintf( '  %s: %s', $lang, $tid ? $tid : 'no translation yet' ) );
}
