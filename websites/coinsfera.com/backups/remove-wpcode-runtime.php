<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
global $wpdb;

$snips = get_option( 'wpcode_snippets' );
if ( ! is_array( $snips ) ) {
	echo "no option\n";
	exit;
}
$removed = 0;
foreach ( $snips as $loc => $items ) {
	if ( ! is_array( $items ) ) {
		continue;
	}
	$keep = array();
	foreach ( $items as $item ) {
		$id = is_object( $item ) ? (int) ( $item->id ?? 0 ) : (int) ( $item['id'] ?? 0 );
		if ( 28093 === $id ) {
			++$removed;
			continue;
		}
		$keep[] = $item;
	}
	$snips[ $loc ] = $keep;
}
update_option( 'wpcode_snippets', $snips, false );
update_post_meta( 28093, '_wpcode_auto_insert', '0' );
wp_update_post(
	array(
		'ID'          => 28093,
		'post_status' => 'draft',
	)
);
echo "removed_from_runtime={$removed}\n";

$snips2 = get_option( 'wpcode_snippets' );
$still  = 0;
foreach ( $snips2['everywhere'] ?? array() as $item ) {
	$id = is_object( $item ) ? (int) ( $item->id ?? 0 ) : (int) ( $item['id'] ?? 0 );
	if ( 28093 === $id ) {
		++$still;
	}
}
echo "still_in_everywhere={$still} everywhere_count=" . count( $snips2['everywhere'] ?? array() ) . "\n";

$ttids    = $wpdb->get_col( "SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy} WHERE taxonomy='category' AND term_id IN (8,73,99)" );
$news_ids = $wpdb->get_col( 'SELECT DISTINCT object_id FROM ' . $wpdb->term_relationships . ' WHERE term_taxonomy_id IN (' . implode( ',', array_map( 'intval', $ttids ) ) . ')' );
$in       = implode( ',', array_map( 'intval', $news_ids ) );
$trids    = $wpdb->get_col( "SELECT DISTINCT trid FROM {$wpdb->prefix}icl_translations WHERE element_type='post_post' AND element_id IN ($in)" );
$all_rows = $wpdb->get_results( 'SELECT element_id, language_code, trid FROM ' . $wpdb->prefix . 'icl_translations WHERE element_type="post_post" AND trid IN (' . implode( ',', array_map( 'intval', $trids ) ) . ')' );
$by_trid  = array();
foreach ( $all_rows as $row ) {
	$by_trid[ $row->trid ][ $row->language_code ] = (int) $row->element_id;
}
$cutoff  = strtotime( '-3 years' );
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
$table   = $wpdb->prefix . 'yoast_indexable';
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
	$wpdb->query( "UPDATE {$table} SET is_robots_noindex=1, is_public=0 WHERE object_type='post' AND object_id IN ($old_in)" );
}
if ( $new_ids ) {
	$new_in = implode( ',', $new_ids );
	$wpdb->query( "DELETE FROM {$wpdb->postmeta} WHERE meta_key='_yoast_wpseo_meta-robots-noindex' AND post_id IN ($new_in)" );
	$wpdb->query( "UPDATE {$table} SET is_robots_noindex=0, is_public=1 WHERE object_type='post' AND object_id IN ($new_in)" );
}
echo 'old=' . count( $old_ids ) . ' new=' . count( $new_ids ) . "\n";
echo '2362 meta=' . var_export( $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=2362 AND meta_key='_yoast_wpseo_meta-robots-noindex'" ), true )
	. ' idx=' . $wpdb->get_var( "SELECT is_robots_noindex FROM {$table} WHERE object_id=2362 AND object_type='post'" ) . "\n";
echo '2345 meta=' . $wpdb->get_var( "SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id=2345 AND meta_key='_yoast_wpseo_meta-robots-noindex'" )
	. ' idx=' . $wpdb->get_var( "SELECT is_robots_noindex FROM {$table} WHERE object_id=2345 AND object_type='post'" ) . "\n";
echo "DONE\n";
