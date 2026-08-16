<?php
/**
 * Follow-up: news noindex across all languages, leftover RU redirect, rewrite flush.
 *
 * wp eval-file apply-seo-batch-2.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb, $sitepress;

if ( $sitepress ) {
	$sitepress->switch_lang( 'all' );
}

echo "==== News noindex by original EN date ====\n";
$ttids = $wpdb->get_col(
	"SELECT term_taxonomy_id FROM {$wpdb->term_taxonomy}
	 WHERE taxonomy='category' AND term_id IN (8,73,99)"
);
$news_ids = $wpdb->get_col(
	'SELECT DISTINCT object_id FROM ' . $wpdb->term_relationships . ' WHERE term_taxonomy_id IN (' . implode( ',', array_map( 'intval', $ttids ) ) . ')'
);
$in        = implode( ',', array_map( 'intval', $news_ids ) );
$trids     = $wpdb->get_col( "SELECT DISTINCT trid FROM {$wpdb->prefix}icl_translations WHERE element_type='post_post' AND element_id IN ($in)" );
$all_rows  = $wpdb->get_results( 'SELECT element_id, language_code, trid FROM ' . $wpdb->prefix . 'icl_translations WHERE element_type="post_post" AND trid IN (' . implode( ',', array_map( 'intval', $trids ) ) . ')' );
$by_trid   = array();
foreach ( $all_rows as $row ) {
	$by_trid[ $row->trid ][ $row->language_code ] = (int) $row->element_id;
}

$cutoff  = strtotime( '-3 years' );
$set     = 0;
$cleared = 0;
$old_n   = 0;
$new_n   = 0;
$samples = array( 'old' => array(), 'new' => array() );

foreach ( $by_trid as $trid => $langs ) {
	$en_id = $langs['en'] ?? reset( $langs );
	$date  = (string) get_post_field( 'post_date', $en_id );
	$old   = $date && strtotime( $date ) < $cutoff;
	if ( $old ) {
		++$old_n;
	} else {
		++$new_n;
	}
	foreach ( $langs as $lang => $id ) {
		if ( 'post' !== get_post_type( $id ) || 'publish' !== get_post_status( $id ) ) {
			continue;
		}
		if ( $old ) {
			update_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', '1' );
			++$set;
			if ( count( $samples['old'] ) < 3 && 'en' === $lang ) {
				$samples['old'][] = $id . ' ' . $date . ' ' . get_permalink( $id );
			}
		} else {
			delete_post_meta( $id, '_yoast_wpseo_meta-robots-noindex' );
			++$cleared;
			if ( count( $samples['new'] ) < 5 && 'en' === $lang ) {
				$samples['new'][] = $id . ' ' . $date . ' ' . get_permalink( $id );
			}
		}
	}
}

echo "groups=" . count( $by_trid ) . " old_groups={$old_n} new_groups={$new_n} set_noindex={$set} cleared={$cleared}\n";
echo 'sample_old=' . implode( ' | ', $samples['old'] ) . "\n";
echo 'sample_new=' . implode( ' | ', $samples['new'] ) . "\n";
echo 'published_posts_noindex=' . $wpdb->get_var(
	"SELECT COUNT(*) FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID=pm.post_id
	 WHERE pm.meta_key='_yoast_wpseo_meta-robots-noindex' AND pm.meta_value='1'
	   AND p.post_type='post' AND p.post_status='publish'"
) . "\n";

echo "\n==== Fix leftover redirect 341 ====\n";
$target = '/ru/novosti/ey-zapuskaet-instrument-blockchain-chtoby-pomoch/';
$wpdb->update(
	$wpdb->prefix . 'redirection_items',
	array( 'action_data' => $target ),
	array( 'id' => 341 ),
	array( '%s' ),
	array( '%d' )
);
echo $wpdb->get_var( "SELECT CONCAT(url,' => ',action_data) FROM {$wpdb->prefix}redirection_items WHERE id=341" ) . "\n";

echo "\n==== Catch-all match_url + leftover broken targets ====\n";
$left = $wpdb->get_results(
	"SELECT id, url, action_data FROM {$wpdb->prefix}redirection_items
	 WHERE status='enabled' AND (action_data LIKE '%h%d0%be%d0%b2%d0%be%d1%81%d1%82%d0%b8%' OR action_data LIKE '%/hовости%')"
);
echo 'still_broken=' . count( $left ) . "\n";
foreach ( $left as $row ) {
	echo "  {$row->id} {$row->url} => {$row->action_data}\n";
}

$wpdb->query( "UPDATE {$wpdb->prefix}redirection_items SET match_url='regex' WHERE id IN (818,819,820)" );
$wpdb->query( "UPDATE {$wpdb->prefix}redirection_items SET regex=1, match_type='url', action_type='url', action_code=301, status='enabled' WHERE id IN (818,819,820)" );

$p_base = '/ru/pубрика/';
$r_base = '/ru/рубрика/';
$exists = (int) $wpdb->get_var( $wpdb->prepare( "SELECT id FROM {$wpdb->prefix}redirection_items WHERE url=%s LIMIT 1", $p_base . '(.*)' ) );
if ( ! $exists ) {
	$group_id = (int) $wpdb->get_var( "SELECT group_id FROM {$wpdb->prefix}redirection_items WHERE id=818" );
	$wpdb->insert(
		$wpdb->prefix . 'redirection_items',
		array(
			'url'         => $p_base . '(.*)',
			'match_url'   => 'regex',
			'match_data'  => '',
			'action_type' => 'url',
			'action_code' => 301,
			'action_data' => $r_base . '$1',
			'match_type'  => 'url',
			'title'       => 'RU category base latin-p repair',
			'regex'       => 1,
			'group_id'    => $group_id ?: 1,
			'status'      => 'enabled',
			'position'    => 0,
		)
	);
	echo 'inserted category-base redirect id=' . $wpdb->insert_id . "\n";
}

if ( class_exists( 'Red_Item' ) ) {
	echo "Red_Item class present\n";
}

flush_rewrite_rules( false );
if ( $sitepress ) {
	$sitepress->switch_lang( 'en' );
}

echo "snippet 28093 status=" . get_post_status( 28093 ) . "\n";
echo "term73=" . $wpdb->get_var( "SELECT slug FROM {$wpdb->terms} WHERE term_id=73" ) . "\n";
echo "DONE\n";
