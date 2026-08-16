<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $sitepress, $wpdb;

$sitepress->switch_lang( 'ru' );
echo "==== RU category 73 ====\n";
$t = get_term( 73, 'category' );
echo 'slug=' . ( $t->slug ?? '' ) . ' name=' . ( $t->name ?? '' ) . ' url=' . get_term_link( 73, 'category' ) . "\n";

echo "==== resolve redirect targets ====\n";
$rows = $wpdb->get_results( "SELECT id, url, action_data FROM {$wpdb->prefix}redirection_items WHERE status='enabled' AND (action_data LIKE '%h%d0%be%d0%b2%d0%be%d1%81%d1%82%d0%b8%' OR action_data LIKE '%/hовости%')" );
echo 'broken=' . count( $rows ) . "\n";
foreach ( $rows as $r ) {
	$path = wp_parse_url( $r->url, PHP_URL_PATH );
	$slug = trim( basename( (string) $path ), '/' );
	$slug = urldecode( $slug );
	$post = get_page_by_path( $slug, OBJECT, 'post' );
	if ( ! $post ) {
		$found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_name=%s AND post_type='post' LIMIT 1", sanitize_title( $slug ) ) );
		if ( ! $found ) {
			$found = $wpdb->get_var( $wpdb->prepare( "SELECT ID FROM {$wpdb->posts} WHERE post_title LIKE %s AND post_type='post' AND post_status='publish' LIMIT 1", '%' . $wpdb->esc_like( mb_substr( $slug, 0, 20 ) ) . '%' ) );
		}
		$post = $found ? get_post( $found ) : null;
	}
	$ru_id = $post ? (int) apply_filters( 'wpml_object_id', $post->ID, 'post', false, 'ru' ) : 0;
	$en_id = $post ? (int) apply_filters( 'wpml_object_id', $post->ID, 'post', true, 'en' ) : 0;
	$link  = $ru_id ? get_permalink( $ru_id ) : '';
	echo "id={$r->id}\n  from={$r->url}\n  to={$r->action_data}\n  slug={$slug} ru={$ru_id} en={$en_id} permalink={$link}\n";
}

echo "==== sample RU posts in news ====\n";
$ids = get_posts(
	array(
		'category'    => 73,
		'numberposts' => 5,
		'post_status' => 'publish',
		'fields'      => 'ids',
	)
);
foreach ( (array) $ids as $id ) {
	echo $id . ' ' . get_permalink( $id ) . ' ' . get_the_title( $id ) . "\n";
}
