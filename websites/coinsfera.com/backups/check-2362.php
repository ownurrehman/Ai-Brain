<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
global $wpdb;
echo 'meta=' . $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=2362 AND meta_key='_yoast_wpseo_meta-robots-noindex'" ) . "\n";
echo 'idx=' . $wpdb->get_var( "SELECT is_robots_noindex FROM {$wpdb->prefix}yoast_indexable WHERE object_id=2362 AND object_type='post'" ) . "\n";
echo 'public=' . $wpdb->get_var( "SELECT is_public FROM {$wpdb->prefix}yoast_indexable WHERE object_id=2362 AND object_type='post'" ) . "\n";
