<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
global $sitepress, $wpdb;

foreach ( array( 'tr' => 99, 'ru' => 73 ) as $lang => $id ) {
	$sitepress->switch_lang( $lang );
	$t = get_term( $id, 'category' );
	echo "$lang id=$id slug={$t->slug} name={$t->name} link=" . get_term_link( $id, 'category' ) . "\n";
}

echo "==== long page titles ====\n";
$pages = $wpdb->get_results(
	"SELECT p.ID, p.post_title, p.post_name, t.language_code
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page'
	 WHERE p.post_type='page' AND p.post_status='publish'"
);
foreach ( $pages as $p ) {
	$yt       = (string) get_post_meta( $p->ID, '_yoast_wpseo_title', true );
	$raw      = $yt ?: $p->post_title;
	$rendered = trim( strtr( $raw, array( '%%title%%' => $p->post_title, '%%page%%' => '', '%%sep%%' => '|', '%%sitename%%' => 'Coinsfera' ) ) );
	if ( strlen( $rendered ) > 60 || ( 'ru' === $p->language_code && preg_match( '/[A-Za-z]{8,}/', $rendered ) && ! preg_match( '/[А-Яа-я]/u', $rendered ) ) ) {
		echo strlen( $rendered ) . "\t{$p->language_code}\t{$p->ID}\t{$p->post_name}\t{$rendered}\n";
	}
}
