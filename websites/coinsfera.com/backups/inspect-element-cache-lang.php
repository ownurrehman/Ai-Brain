<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

foreach ( array( 11237, 11570 ) as $id ) {
	$cache = get_post_meta( $id, '_elementor_element_cache', true );
	$hay   = is_string( $cache ) ? $cache : wp_json_encode( $cache );
	WP_CLI::log( "id={$id} cache_type=" . gettype( $cache ) . ' bytes=' . strlen( (string) $hay ) );
	foreach ( array( 'Sell Ethereum', 'Sell Tether', 'Ethereum satmak', 'Tether USDT Satmak', 'Want to spend', 'Harcamak', 'Contact Us', 'Bize Ulaşın' ) as $s ) {
		WP_CLI::log( ( false !== strpos( (string) $hay, $s ) ? 'HIT  ' : 'miss ' ) . $s );
	}
	if ( is_array( $cache ) ) {
		WP_CLI::log( 'keys=' . implode( ',', array_keys( $cache ) ) );
		$blob = wp_json_encode( $cache );
		WP_CLI::log( 'json has Sell Ethereum ' . ( false !== strpos( $blob, 'Sell Ethereum' ) ? 'yes' : 'no' ) );
		WP_CLI::log( 'json has satmak ' . ( false !== strpos( $blob, 'satmak' ) ? 'yes' : 'no' ) );
	}
}
