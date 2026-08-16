<?php
/**
 * Remove the homepage HTML widget that WPML stripped into visible CSS/JS.
 *
 * Run: wp eval-file strip-homepage-trust-html.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb;

/**
 * @param array $nodes Elementor tree.
 * @return array
 */
function coinsfera_strip_trust_html( $nodes ) {
	$out = array();
	foreach ( $nodes as $n ) {
		if ( ! is_array( $n ) ) {
			continue;
		}
		$html = '';
		if ( isset( $n['settings']['html'] ) && is_string( $n['settings']['html'] ) ) {
			$html = $n['settings']['html'];
		}
		$drop = ( ( $n['id'] ?? '' ) === 'cfxovfl1' )
			|| (
				( $n['widgetType'] ?? '' ) === 'html'
				&& (
					false !== strpos( $html, 'placeTrustStrip' )
					|| false !== strpos( $html, 'coinsfera mobile banner align' )
					|| false !== strpos( $html, 'cf-trust-strip' )
				)
			);
		if ( $drop ) {
			continue;
		}
		if ( ! empty( $n['elements'] ) && is_array( $n['elements'] ) ) {
			$n['elements'] = coinsfera_strip_trust_html( $n['elements'] );
		}
		$out[] = $n;
	}
	return $out;
}

$bak = WP_CONTENT_DIR . '/uploads/cfkl-backups/strip-trust-html-' . gmdate( 'Ymd-His' );
if ( ! is_dir( $bak ) ) {
	wp_mkdir_p( $bak );
}

foreach ( array( 9, 6611, 11248 ) as $id ) {
	$raw = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s LIMIT 1",
			$id,
			'_elementor_data'
		)
	);
	file_put_contents( $bak . "/{$id}-_elementor_data.json", (string) $raw );

	$data = json_decode( (string) $raw, true );
	if ( ! is_array( $data ) ) {
		$data = json_decode( (string) wp_unslash( $raw ), true );
	}
	if ( ! is_array( $data ) ) {
		WP_CLI::warning( "skip {$id} json fail" );
		continue;
	}

	$new      = coinsfera_strip_trust_html( $data );
	$new_json = wp_json_encode( $new, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE );
	$wpdb->update(
		$wpdb->postmeta,
		array( 'meta_value' => $new_json ),
		array(
			'post_id'  => $id,
			'meta_key' => '_elementor_data',
		)
	);
	delete_post_meta( $id, '_elementor_element_cache' );

	$check = (string) get_post_meta( $id, '_elementor_data', true );
	$gone  = ( false === strpos( $check, 'placeTrustStrip' ) && false === strpos( $check, 'coinsfera mobile banner align' ) );
	WP_CLI::log( "id={$id} json=" . ( is_array( json_decode( $check, true ) ) ? 'ok' : 'FAIL' ) . ' stripped=' . ( $gone ? 'yes' : 'NO' ) . ' bytes=' . strlen( $check ) );
}

WP_CLI::log( 'backup ' . $bak );
