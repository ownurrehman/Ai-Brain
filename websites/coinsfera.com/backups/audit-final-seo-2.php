<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $wpdb;

echo "==== NOINDEX TOTALS ====\n";
echo 'noindex_meta_1=' . $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->postmeta} pm JOIN {$wpdb->posts} p ON p.ID=pm.post_id WHERE pm.meta_key='_yoast_wpseo_meta-robots-noindex' AND pm.meta_value='1' AND p.post_status='publish'" ) . "\n";
echo 'published_posts=' . $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish'" ) . "\n";
echo 'published_pages=' . $wpdb->get_var( "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type='page' AND post_status='publish'" ) . "\n";

echo "\n==== NEWS CATEGORY ====\n";
$news = get_term_by( 'slug', 'news', 'category' );
if ( $news ) {
	echo "news id={$news->term_id} count={$news->count}\n";
	$noix = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} p JOIN {$wpdb->term_relationships} tr ON tr.object_id=p.ID JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id=tr.term_taxonomy_id JOIN {$wpdb->postmeta} pm ON pm.post_id=p.ID AND pm.meta_key='_yoast_wpseo_meta-robots-noindex' AND pm.meta_value='1' WHERE p.post_status='publish' AND tt.term_id=%d", $news->term_id ) );
	$all = $wpdb->get_var( $wpdb->prepare( "SELECT COUNT(*) FROM {$wpdb->posts} p JOIN {$wpdb->term_relationships} tr ON tr.object_id=p.ID JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id=tr.term_taxonomy_id WHERE p.post_status='publish' AND p.post_type='post' AND tt.term_id=%d", $news->term_id ) );
	echo "news_posts={$all} news_noindex={$noix}\n";
}
$cats = get_categories( array( 'hide_empty' => false ) );
foreach ( $cats as $c ) {
	$no = (int) get_term_meta( $c->term_id, '_yoast_wpseo_meta-robots-noindex', true ) || (int) get_option( 'wpseo_taxonomy_meta' )['category'][ $c->term_id ]['noindex'] ?? 0;
	$tm = get_option( 'wpseo_taxonomy_meta' );
	$flag = $tm['category'][ $c->term_id ]['wpseo_noindex'] ?? ( $tm['category'][ $c->term_id ]['noindex'] ?? '' );
	echo "cat {$c->slug} count={$c->count} yoast_noindex=" . wp_json_encode( $flag ) . "\n";
}

echo "\n==== AUTHOR / DATE ARCHIVES ====\n";
$wpseo = get_option( 'wpseo_titles' );
foreach ( array( 'noindex-author-wpseo', 'noindex-author-noposts-wpseo', 'noindex-archive-wpseo', 'noindex-post', 'noindex-page', 'disable-author', 'disable-date', 'noindex-tax-category' ) as $k ) {
	echo $k . '=' . wp_json_encode( $wpseo[ $k ] ?? null ) . "\n";
}

echo "\n==== REDIRECTION PLUGIN ====\n";
$table = $wpdb->prefix . 'redirection_items';
$exists = $wpdb->get_var( $wpdb->prepare( 'SHOW TABLES LIKE %s', $table ) );
if ( $exists ) {
	echo 'redirects=' . $wpdb->get_var( "SELECT COUNT(*) FROM {$table} WHERE status='enabled'" ) . "\n";
	$rows = $wpdb->get_results( "SELECT url, action_data, action_code, match_type FROM {$table} WHERE status='enabled' ORDER BY id DESC LIMIT 25" );
	foreach ( $rows as $r ) {
		echo "{$r->action_code} {$r->url} => {$r->action_data}\n";
	}
} else {
	echo "no redirection table\n";
}

echo "\n==== PERMALINK CATEGORY RISK ====\n";
echo 'permalink=' . get_option( 'permalink_structure' ) . "\n";
$posts_in_cat = $wpdb->get_results( "SELECT p.ID, p.post_name, t.slug FROM {$wpdb->posts} p JOIN {$wpdb->term_relationships} tr ON tr.object_id=p.ID JOIN {$wpdb->term_taxonomy} tt ON tt.term_taxonomy_id=tr.term_taxonomy_id JOIN {$wpdb->terms} t ON t.term_id=tt.term_id WHERE p.post_type='post' AND p.post_status='publish' AND tt.taxonomy='category' LIMIT 8" );
foreach ( $posts_in_cat as $r ) {
	echo get_permalink( $r->ID ) . " cat={$r->slug}\n";
}

echo "\n==== INSERT HEADERS / WPCODE HEADER ====\n";
$ihaf = get_option( 'ihaf_insert_header' );
echo 'ihaf_header_len=' . strlen( (string) $ihaf ) . "\n";
if ( $ihaf ) {
	foreach ( array( 'GTM', 'googletagmanager', 'yandex', 'ahrefs', 'trustpilot', 'domain_verify', 'fbq', 'hotjar' ) as $n ) {
		echo $n . '=' . ( false !== stripos( (string) $ihaf, $n ) ? 'yes' : 'no' ) . "\n";
	}
}

echo "\n==== SCHEMA PLUGIN ====\n";
$saswp = get_option( 'saswp_settings' );
echo 'saswp=' . ( is_array( $saswp ) ? 'yes keys=' . count( $saswp ) : wp_json_encode( $saswp ) ) . "\n";
$yoast_schema = get_option( 'wpseo_titles' );
echo 'company_or_person=' . wp_json_encode( $yoast_schema['company_or_person'] ?? null ) . "\n";
echo 'company_name=' . wp_json_encode( $yoast_schema['company_name'] ?? null ) . "\n";

echo "\n==== ELEMENTOR / ASSETS ====\n";
echo 'elementor_css_print=' . get_option( 'elementor_css_print_method' ) . "\n";
echo 'elementor_google_font=' . get_option( 'elementor_google_font' ) . "\n";
echo 'elementor_experiment_e_font_icon_svg=' . get_option( 'elementor_experiment-e_font_icon_svg' ) . "\n";

echo "\n==== POST FRESHNESS ====\n";
$latest = $wpdb->get_results( "SELECT ID, post_date, post_title FROM {$wpdb->posts} WHERE post_type='post' AND post_status='publish' ORDER BY post_date DESC LIMIT 8" );
foreach ( $latest as $r ) {
	echo "{$r->post_date} {$r->ID} {$r->post_title}\n";
}

echo "\n==== INDEXABLE PAGES WITH LONG TITLES (rendered estimate) ====\n";
$n = 0;
$pages = $wpdb->get_results( "SELECT p.ID, p.post_title, p.post_name, t.language_code FROM {$wpdb->posts} p JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page' WHERE p.post_type='page' AND p.post_status='publish'" );
foreach ( $pages as $p ) {
	$yt = (string) get_post_meta( $p->ID, '_yoast_wpseo_title', true );
	$raw = $yt ?: $p->post_title;
	$rendered = strtr( $raw, array( '%%title%%' => $p->post_title, '%%page%%' => '', '%%sep%%' => '|', '%%sitename%%' => 'Coinsfera' ) );
	$rendered = trim( preg_replace( '/\s+/', ' ', $rendered ) );
	if ( strlen( $rendered ) > 70 ) {
		++$n;
		if ( $n <= 15 ) {
			echo strlen( $rendered ) . " {$p->language_code} {$p->post_name} :: {$rendered}\n";
		}
	}
}
echo "pages_title_gt_70={$n}\n";
