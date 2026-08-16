<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
global $wpdb;

$ids = array( 12885, 6611, 11248, 6644, 11226, 2362 );
foreach ( $ids as $id ) {
	$post  = $wpdb->get_row( $wpdb->prepare( "SELECT ID, post_title, post_name FROM {$wpdb->posts} WHERE ID=%d", $id ) );
	$yt    = $wpdb->get_var( $wpdb->prepare( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=%d AND meta_key='_yoast_wpseo_title'", $id ) );
	$lang  = $wpdb->get_var( $wpdb->prepare( "SELECT language_code FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type='post_page'", $id ) );
	$idx   = $wpdb->get_row( $wpdb->prepare( "SELECT object_id, title, permalink, language, is_robots_noindex FROM {$wpdb->prefix}yoast_indexable WHERE object_id=%d", $id ), ARRAY_A );
	echo "ID={$id} lang={$lang} name={$post->post_name}\n";
	echo "  post_title={$post->post_title}\n";
	echo "  yoast_meta={$yt}\n";
	echo '  indexable=' . wp_json_encode( $idx, JSON_UNESCAPED_UNICODE ) . "\n";
}

echo "yoast_indexable cols with title:\n";
$cols = $wpdb->get_results( "SHOW COLUMNS FROM {$wpdb->prefix}yoast_indexable LIKE '%title%'" );
foreach ( $cols as $c ) {
	echo "  {$c->Field}\n";
}
echo "lang col:\n";
print_r( $wpdb->get_results( "SHOW COLUMNS FROM {$wpdb->prefix}yoast_indexable LIKE '%lang%'" ) );

echo "2362 meta now=" . $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=2362 AND meta_key='_yoast_wpseo_meta-robots-noindex'" ) . "\n";
