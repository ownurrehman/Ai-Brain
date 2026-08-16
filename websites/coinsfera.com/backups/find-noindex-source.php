<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
global $wpdb;

echo "==== tables ====\n";
print_r( $wpdb->get_col( 'SHOW TABLES LIKE "%wpcode%"' ) );
print_r( $wpdb->get_col( 'SHOW TABLES LIKE "%snippet%"' ) );

echo "==== 28093 meta ====\n";
foreach ( $wpdb->get_results( "SELECT meta_key, LEFT(meta_value,180) v FROM {$wpdb->postmeta} WHERE post_id=28093" ) as $m ) {
	echo $m->meta_key . ' = ' . $m->v . "\n";
}

echo "==== posts containing category 8 noindex ====\n";
foreach ( $wpdb->get_results( "SELECT ID, post_type, post_status, post_title FROM {$wpdb->posts} WHERE post_content LIKE '%update_post_meta%noindex%' OR post_content LIKE '%category__in%'" ) as $r ) {
	echo "{$r->ID} {$r->post_type} {$r->post_status} {$r->post_title}\n";
}

echo "==== options ====\n";
echo 'wpcode opts=' . $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->options} WHERE option_name LIKE '%wpcode%'" ) . "\n";
foreach ( $wpdb->get_results( "SELECT option_name, LENGTH(option_value) l FROM {$wpdb->options} WHERE option_name LIKE '%wpcode%' OR option_value LIKE '%Put news category%'" ) as $o ) {
	echo "{$o->option_name} {$o->l}\n";
}

echo "==== should_noindex 2362 ====\n";
echo 'fn=' . ( function_exists( 'coinsfera_news_should_noindex' ) ? 'yes' : 'no' ) . "\n";
if ( function_exists( 'coinsfera_news_should_noindex' ) ) {
	echo 'should=' . ( coinsfera_news_should_noindex( 2362 ) ? 'yes' : 'no' ) . "\n";
	echo 'orig=' . coinsfera_news_original_date( 2362 ) . "\n";
	echo 'cats=' . implode( ',', coinsfera_news_category_ids() ) . "\n";
}
echo 'cutoff=' . date( 'Y-m-d', strtotime( '-3 years' ) ) . " tz=" . wp_timezone_string() . "\n";
echo 'post_date=' . get_post_field( 'post_date', 2362 ) . ' gmt=' . get_post_field( 'post_date_gmt', 2362 ) . "\n";
