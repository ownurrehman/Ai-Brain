<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
global $wpdb;
$t = $wpdb->prefix . 'yoast_indexable';
$n = $wpdb->query( "UPDATE $t SET is_public=1 WHERE object_type='post' AND is_robots_noindex=0 AND object_id IN (SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (8,73,99))" );
$o = $wpdb->query( "UPDATE $t SET is_public=0 WHERE object_type='post' AND is_robots_noindex=1 AND object_id IN (SELECT object_id FROM {$wpdb->term_relationships} WHERE term_taxonomy_id IN (8,73,99))" );
echo "public_on={$n} public_off={$o}\n";
echo '2362 public=' . $wpdb->get_var( "SELECT is_public FROM $t WHERE object_id=2362 AND object_type='post'" ) . ' noindex=' . $wpdb->get_var( "SELECT is_robots_noindex FROM $t WHERE object_id=2362 AND object_type='post'" ) . "\n";
echo '2345 public=' . $wpdb->get_var( "SELECT is_public FROM $t WHERE object_id=2345 AND object_type='post'" ) . ' noindex=' . $wpdb->get_var( "SELECT is_robots_noindex FROM $t WHERE object_id=2345 AND object_type='post'" ) . "\n";
