<?php
/**
 * Find must-fix SEO defects on Coinsfera pages.
 *
 * Run: wp eval-file audit-seo-defects.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb, $sitepress;

$s = get_option( 'icl_sitepress_settings' );
echo "=== WPML SEO SETTINGS ===\n";
echo 'seo=' . wp_json_encode( $s['seo'] ?? null ) . "\n";
echo 'negotiation=' . ( $s['language_negotiation_type'] ?? '' ) . "\n";
echo 'auto_redirect=' . ( $s['automatic_redirect'] ?? '' ) . "\n";
echo 'hide_untranslated=' . wp_json_encode( $s['hide_untranslated'] ?? null ) . "\n";

$wpseo = get_option( 'wpseo' );
echo "\n=== YOAST ===\n";
echo 'enable_xml_sitemap=' . wp_json_encode( $wpseo['enable_xml_sitemap'] ?? null ) . "\n";

echo "\n=== PAGE SEO ISSUES ===\n";
$rows = $wpdb->get_results(
	"SELECT p.ID, p.post_title, p.post_name, t.language_code, t.trid, t.source_language_code
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t
	   ON t.element_id = p.ID AND t.element_type = 'post_page'
	 WHERE p.post_type = 'page' AND p.post_status = 'publish'
	 ORDER BY t.trid, t.language_code"
);

$bad = 0;
foreach ( $rows as $r ) {
	$title = (string) get_post_meta( $r->ID, '_yoast_wpseo_title', true );
	$desc  = (string) get_post_meta( $r->ID, '_yoast_wpseo_metadesc', true );
	$canon = (string) get_post_meta( $r->ID, '_yoast_wpseo_canonical', true );
	$noix  = (string) get_post_meta( $r->ID, '_yoast_wpseo_meta-robots-noindex', true );
	$issues = array();
	if ( '' === $title ) {
		$issues[] = 'empty_title';
	}
	if ( '' === $desc ) {
		$issues[] = 'empty_desc';
	}
	if ( '1' === $noix ) {
		$issues[] = 'noindex';
	}
	if ( '' !== $canon ) {
		$issues[] = 'custom_canonical=' . $canon;
	}
	if ( $issues ) {
		++$bad;
		echo $r->ID . ' ' . $r->language_code . ' ' . $r->post_name . ' ' . implode( ',', $issues ) . "\n";
	}
}
echo 'issue_rows=' . $bad . ' total_pages=' . count( $rows ) . "\n";

echo "\n=== POST SEO ISSUES (published, translated) ===\n";
$posts = $wpdb->get_results(
	"SELECT p.ID, p.post_name, t.language_code
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t
	   ON t.element_id = p.ID AND t.element_type = 'post_post'
	 WHERE p.post_type = 'post' AND p.post_status = 'publish'
	 ORDER BY p.ID DESC
	 LIMIT 80"
);
$pbad = 0;
$pempty_desc = 0;
$pnoindex = 0;
foreach ( $posts as $r ) {
	$desc = (string) get_post_meta( $r->ID, '_yoast_wpseo_metadesc', true );
	$noix = (string) get_post_meta( $r->ID, '_yoast_wpseo_meta-robots-noindex', true );
	$canon = (string) get_post_meta( $r->ID, '_yoast_wpseo_canonical', true );
	if ( '1' === $noix || '' !== $canon ) {
		++$pbad;
		echo $r->ID . ' ' . $r->language_code . ' ' . $r->post_name . ( '1' === $noix ? ' noindex' : '' ) . ( $canon ? ' canon=' . $canon : '' ) . "\n";
	}
	if ( '' === $desc ) {
		++$pempty_desc;
	}
	if ( '1' === $noix ) {
		++$pnoindex;
	}
}
echo "post_sample=" . count( $posts ) . " empty_desc={$pempty_desc} noindex={$pnoindex} flagged={$pbad}\n";
