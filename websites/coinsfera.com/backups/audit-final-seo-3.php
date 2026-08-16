<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wpdb;

echo "==== CATEGORY INDEX ====\n";
foreach ( array( 'news', 'blog' ) as $slug ) {
	$t = get_term_by( 'slug', $slug, 'category' );
	if ( ! $t ) {
		echo "missing {$slug}\n";
		continue;
	}
	$link = get_category_link( $t );
	$tm   = get_option( 'wpseo_taxonomy_meta' );
	$meta = $tm['category'][ $t->term_id ] ?? array();
	echo $slug . ' id=' . $t->term_id . ' count=' . $t->count . ' url=' . $link . ' yoast=' . wp_json_encode( $meta ) . "\n";
}

echo "\n==== INDEXABLE POSTS (not noindex) ====\n";
$ids = $wpdb->get_col( "SELECT p.ID FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id=p.ID AND pm.meta_key='_yoast_wpseo_meta-robots-noindex' AND pm.meta_value='1' WHERE p.post_type='post' AND p.post_status='publish' AND pm.post_id IS NULL LIMIT 40" );
echo 'indexable_posts=' . $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON pm.post_id=p.ID AND pm.meta_key='_yoast_wpseo_meta-robots-noindex' AND pm.meta_value='1' WHERE p.post_type='post' AND p.post_status='publish' AND pm.post_id IS NULL" ) . "\n";
foreach ( $ids as $id ) {
	$lang = apply_filters( 'wpml_element_language_code', null, array( 'element_id' => (int) $id, 'element_type' => 'post_post' ) );
	echo $id . ' ' . $lang . ' ' . get_permalink( $id ) . ' ' . get_the_title( $id ) . "\n";
}

echo "\n==== BROKEN RU REDIRECT TARGET CHECK ====\n";
$table = $wpdb->prefix . 'redirection_items';
$bad = $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status='enabled' AND action_data LIKE '%/h%d0%be%d0%b2%d0%be%d1%81%d1%82%d0%b8/%'" );
echo "ru_hовости_targets={$bad}\n";
$sample = $wpdb->get_results( "SELECT url, action_data FROM {$table} WHERE status='enabled' AND action_data LIKE '%h%d0%' LIMIT 5" );
foreach ( $sample as $r ) {
	echo "FROM {$r->url}\n TO   {$r->action_data}\n";
}

echo "\n==== WPCODE 26509 schema snippet ====\n";
$p = get_post( 26509 );
if ( $p ) {
	echo 'status=' . $p->post_status . ' title=' . $p->post_title . ' len=' . strlen( $p->post_content ) . "\n";
	echo substr( wp_strip_all_tags( $p->post_content ), 0, 400 ) . "\n";
}

echo "\n==== AUTHORS ====\n";
$users = $wpdb->get_results( "SELECT u.ID, u.user_login, COUNT(p.ID) c FROM {$wpdb->users} u JOIN {$wpdb->posts} p ON p.post_author=u.ID AND p.post_type='post' AND p.post_status='publish' GROUP BY u.ID ORDER BY c DESC" );
foreach ( $users as $u ) {
	echo "{$u->ID} {$u->user_login} posts={$u->c} url=" . get_author_posts_url( $u->ID ) . "\n";
}

echo "\n==== PRIVACY/TERMS TRANSLATIONS ====\n";
foreach ( array( 3, 6975 ) as $id ) {
	foreach ( array( 'en', 'ru', 'tr' ) as $lang ) {
		$tid = (int) apply_filters( 'wpml_object_id', $id, 'page', true, $lang );
		echo $id . "->{$lang}={$tid} " . ( $tid ? get_permalink( $tid ) : '' ) . "\n";
	}
}
