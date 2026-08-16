<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( array( 11237 => 'tr-eth', 11570 => 'tr-usdt', 5297 => 'en-eth', 5404 => 'en-usdt' ) as $id => $lab ) {
	$cache = get_post_meta( $id, '_elementor_element_cache', true );
	$css   = get_post_meta( $id, '_elementor_css', true );
	$clen  = is_string( $cache ) ? strlen( $cache ) : ( is_array( $cache ) ? strlen( wp_json_encode( $cache ) ) : 0 );
	WP_CLI::log( "{$lab} {$id} cache_bytes={$clen} css_type=" . gettype( $css ) );
	$hay = is_string( $cache ) ? $cache : wp_json_encode( $cache );
	foreach ( array(
		'Sell Ethereum in Istanbul, Turkey',
		'Türkiye\'nin İstanbul şehrinde Ethereum satmak',
		'Sell Tether USDT in Istanbul Turkey',
		'İstanbul, Türkiye\'de Tether USDT Satmak',
		'Want to spend',
	) as $s ) {
		WP_CLI::log( ( false !== strpos( (string) $hay, $s ) ? 'HIT  ' : 'miss ' ) . "cache {$s}" );
	}

	if ( class_exists( '\Elementor\Plugin' ) ) {
		$html = \Elementor\Plugin::$instance->frontend->get_builder_content_for_display( $id, true );
		$plain = wp_strip_all_tags( (string) $html );
		WP_CLI::log( "  rendered bytes=" . strlen( (string) $html ) );
		foreach ( array(
			'Sell Ethereum in Istanbul, Turkey',
			'Türkiye\'nin İstanbul şehrinde Ethereum satmak',
			'Sell Tether USDT in Istanbul Turkey',
			'İstanbul, Türkiye\'de Tether USDT Satmak',
			'Want to spend',
		) as $s ) {
			WP_CLI::log( ( false !== strpos( $plain, $s ) ? 'HIT  ' : 'miss ' ) . "render {$s}" );
		}
	}
}
