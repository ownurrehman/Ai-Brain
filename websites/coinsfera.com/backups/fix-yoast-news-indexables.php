<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
global $wpdb;

echo "yoast_indexable 2362:\n";
print_r(
	$wpdb->get_row(
		"SELECT object_id, is_robots_noindex, is_public, permalink
		 FROM {$wpdb->prefix}yoast_indexable
		 WHERE object_id=2362 AND object_type='post'",
		ARRAY_A
	)
);
echo "postmeta 2362:\n";
print_r(
	$wpdb->get_results(
		"SELECT meta_id, meta_key, meta_value FROM {$wpdb->postmeta}
		 WHERE post_id=2362 AND meta_key LIKE '%noindex%'"
	)
);

$table = $wpdb->prefix . 'yoast_indexable';
$cols  = $wpdb->get_col( "DESC {$table}", 0 );
echo 'has_is_robots_noindex=' . ( in_array( 'is_robots_noindex', $cols, true ) ? 'yes' : 'no' ) . "\n";

$ttids    = $wpdb->get_col( "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy='category' AND term_id IN (8,73,99)" );
$news_ids = $wpdb->get_col( 'SELECT DISTINCT object_id FROM ' . $wpdb->term_relationships . ' WHERE term_taxonomy_id IN (' . implode( ',', array_map( 'intval', $ttids ) ) . ')' );
$in       = implode( ',', array_map( 'intval', $news_ids ) );
$trids    = $wpdb->get_col( "SELECT DISTINCT trid FROM {$wpdb->prefix}icl_translations WHERE element_type='post_post' AND element_id IN ($in)" );
$all_rows = $wpdb->get_results( 'SELECT element_id, language_code, trid FROM ' . $wpdb->prefix . 'icl_translations WHERE element_type="post_post" AND trid IN (' . implode( ',', array_map( 'intval', $trids ) ) . ')' );
$by_trid  = array();
foreach ( $all_rows as $row ) {
	$by_trid[ $row->trid ][ $row->language_code ] = (int) $row->element_id;
}

$cutoff = strtotime( '-3 years' );
$old_ids = array();
$new_ids = array();
foreach ( $by_trid as $langs ) {
	$en_id = $langs['en'] ?? reset( $langs );
	$date  = (string) $wpdb->get_var( $wpdb->prepare( "SELECT post_date FROM {$wpdb->posts} WHERE ID=%d", $en_id ) );
	$old   = $date && strtotime( $date ) < $cutoff;
	foreach ( $langs as $id ) {
		if ( $old ) {
			$old_ids[] = (int) $id;
		} else {
			$new_ids[] = (int) $id;
		}
	}
}

$old_ids = array_values( array_unique( $old_ids ) );
$new_ids = array_values( array_unique( $new_ids ) );
echo 'old_ids=' . count( $old_ids ) . ' new_ids=' . count( $new_ids ) . "\n";

if ( $old_ids ) {
	$old_in = implode( ',', $old_ids );
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key='_yoast_wpseo_meta-robots-noindex' AND post_id IN ($old_in)" );
	foreach ( $old_ids as $id ) {
		$wpdb->insert(
			$wpdb->postmeta,
			array(
				'post_id'    => $id,
				'meta_key'   => '_yoast_wpseo_meta-robots-noindex',
				'meta_value' => '1',
			)
		);
	}
	$wpdb->query( "UPDATE {$table} SET is_robots_noindex=1 WHERE object_type='post' AND object_id IN ($old_in)" );
}
if ( $new_ids ) {
	$new_in = implode( ',', $new_ids );
	$del    = $wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key='_yoast_wpseo_meta-robots-noindex' AND post_id IN ($new_in)" );
	$upd    = $wpdb->query( "UPDATE {$table} SET is_robots_noindex=0 WHERE object_type='post' AND object_id IN ($new_in)" );
	echo "cleared_postmeta={$del} yoast_indexable_new={$upd}\n";
}

echo 'meta2362=' . $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=2362 AND meta_key='_yoast_wpseo_meta-robots-noindex'" ) . "\n";
echo 'idx2362=' . $wpdb->get_var( "SELECT is_robots_noindex FROM {$table} WHERE object_id=2362 AND object_type='post'" ) . "\n";
echo 'meta2345=' . $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=2345 AND meta_key='_yoast_wpseo_meta-robots-noindex'" ) . "\n";
echo 'idx2345=' . $wpdb->get_var( "SELECT is_robots_noindex FROM {$table} WHERE object_id=2345 AND object_type='post'" ) . "\n";
echo "DONE\n";
