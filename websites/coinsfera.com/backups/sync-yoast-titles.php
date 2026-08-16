<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
global $wpdb;

echo "==== leftover noindex snippet search ====\n";
$rows = $wpdb->get_results(
	"SELECT ID, post_type, post_status, post_title
	 FROM {$wpdb->posts}
	 WHERE post_content LIKE '%category__in%' OR post_content LIKE '%news category to no index%' OR post_content LIKE '%_yoast_wpseo_meta-robots-noindex%'"
);
foreach ( $rows as $r ) {
	echo "{$r->ID} {$r->post_type} {$r->post_status} {$r->post_title}\n";
}

echo "\n==== sync indexable titles from postmeta (RU/TR pages) ====\n";
$diff = $wpdb->get_results(
	"SELECT yi.object_id, t.language_code, pm.meta_value AS new_title, yi.title AS old_title
	 FROM {$wpdb->prefix}yoast_indexable yi
	 JOIN {$wpdb->postmeta} pm ON pm.post_id=yi.object_id AND pm.meta_key='_yoast_wpseo_title'
	 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=yi.object_id AND t.element_type='post_page'
	 WHERE t.language_code IN ('ru','tr') AND yi.object_type='post' AND pm.meta_value <> yi.title"
);
echo 'diff=' . count( $diff ) . "\n";
$n = 0;
foreach ( $diff as $row ) {
	$wpdb->update(
		$wpdb->prefix . 'yoast_indexable',
		array(
			'title'             => $row->new_title,
			'open_graph_title'  => $row->new_title,
			'twitter_title'     => $row->new_title,
		),
		array(
			'object_id'   => (int) $row->object_id,
			'object_type' => 'post',
		)
	);
	echo "{$row->language_code} {$row->object_id} {$row->old_title} => {$row->new_title}\n";
	++$n;
}
echo "updated_indexables={$n}\n";

$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE post_id=2362 AND meta_key='_yoast_wpseo_meta-robots-noindex'" );
echo '2362 meta after delete=' . var_export( $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=2362 AND meta_key='_yoast_wpseo_meta-robots-noindex'" ), true ) . "\n";
echo "12885 idx=" . $wpdb->get_var( "SELECT title FROM {$wpdb->prefix}yoast_indexable WHERE object_id=12885 AND object_type='post'" ) . "\n";
echo "6611 idx=" . $wpdb->get_var( "SELECT title FROM {$wpdb->prefix}yoast_indexable WHERE object_id=6611 AND object_type='post'" ) . "\n";
