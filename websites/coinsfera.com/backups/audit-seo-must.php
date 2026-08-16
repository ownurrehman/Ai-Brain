<?php
/**
 * Find broken hreflang targets, wrong-language canonicals, and sitemap gaps.
 *
 * Run: wp eval-file audit-seo-must.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb, $sitepress;

echo "=== WRONG CUSTOM CANONICALS ===\n";
$canons = $wpdb->get_results(
	"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_yoast_wpseo_canonical' AND meta_value <> ''"
);
foreach ( $canons as $row ) {
	$id   = (int) $row->post_id;
	$post = get_post( $id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		continue;
	}
	$type = 'post_' . $post->post_type;
	$lang = apply_filters(
		'wpml_element_language_code',
		null,
		array(
			'element_id'   => $id,
			'element_type' => $type,
		)
	);
	$sitepress->switch_lang( is_string( $lang ) ? $lang : 'en' );
	$permalink = get_permalink( $id );
	$ok        = untrailingslashit( $row->meta_value ) === untrailingslashit( $permalink );
	echo $id . ' lang=' . $lang . ' custom=' . $row->meta_value . ' permalink=' . $permalink . ' ' . ( $ok ? 'OK' : 'MISMATCH' ) . "\n";
}

echo "\n=== HREFLANG 404 / MISSING TRANSLATION TARGETS (pages) ===\n";
$pages = $wpdb->get_results(
	"SELECT p.ID, p.post_name, p.post_status, t.language_code, t.trid
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t
	   ON t.element_id = p.ID AND t.element_type = 'post_page'
	 WHERE p.post_type = 'page' AND p.post_status = 'publish' AND t.source_language_code IS NULL AND t.language_code = 'en'"
);

$missing = 0;
$ok      = 0;
foreach ( $pages as $en ) {
	foreach ( array( 'ru', 'tr' ) as $lang ) {
		$tid = (int) apply_filters( 'wpml_object_id', (int) $en->ID, 'page', false, $lang );
		if ( $tid <= 0 ) {
			++$missing;
			echo 'NO ' . $lang . ' for EN ' . $en->ID . ' ' . $en->post_name . "\n";
			continue;
		}
		$st = get_post_status( $tid );
		if ( 'publish' !== $st ) {
			++$missing;
			echo 'UNPUBLISHED ' . $lang . ' ' . $tid . ' status=' . $st . ' from EN ' . $en->post_name . "\n";
			continue;
		}
		++$ok;
	}
}
echo "en_pages_checked=" . count( $pages ) . " published_targets={$ok} missing_or_unpublished={$missing}\n";

echo "\n=== HTML LANG / LANGUAGE ATTRIBUTES ===\n";
$header = COINSFERA_PATH . '/header.php';
if ( defined( 'COINSFERA_PATH' ) && file_exists( $header ) ) {
	$src = file_get_contents( $header );
	if ( false !== strpos( $src, 'language_attributes' ) ) {
		echo "header.php uses language_attributes OK\n";
	} else {
		echo "header.php MISSING language_attributes\n";
	}
} else {
	$theme_header = get_template_directory() . '/header.php';
	$src          = file_exists( $theme_header ) ? file_get_contents( $theme_header ) : '';
	echo 'header=' . $theme_header . "\n";
	echo ( false !== strpos( $src, 'language_attributes' ) ) ? "language_attributes OK\n" : "MISSING language_attributes\n";
}

echo "\n=== ROBOTS / SITEMAP OPTIONS ===\n";
echo 'blog_public=' . get_option( 'blog_public' ) . "\n";
$wpseo = get_option( 'wpseo' );
echo 'enable_xml_sitemap=' . wp_json_encode( $wpseo['enable_xml_sitemap'] ?? null ) . "\n";
echo 'home=' . home_url( '/' ) . "\n";
echo 'sitemap_index=' . home_url( '/sitemap_index.xml' ) . "\n";

echo "\n=== PRIVACY / TERMS TITLES ===\n";
foreach ( array( 3, 6975 ) as $id ) {
	$lang = apply_filters(
		'wpml_element_language_code',
		null,
		array(
			'element_id'   => $id,
			'element_type' => 'post_page',
		)
	);
	echo $id . ' lang=' . $lang . ' post_title=' . get_the_title( $id ) . ' yoast_title=' . get_post_meta( $id, '_yoast_wpseo_title', true ) . ' desc_len=' . strlen( (string) get_post_meta( $id, '_yoast_wpseo_metadesc', true ) ) . "\n";
	foreach ( array( 'ru', 'tr' ) as $l ) {
		$tid = (int) apply_filters( 'wpml_object_id', $id, 'page', false, $l );
		echo '  ' . $l . '=' . $tid;
		if ( $tid ) {
			echo ' title=' . get_the_title( $tid ) . ' yoast_title_len=' . strlen( (string) get_post_meta( $tid, '_yoast_wpseo_title', true ) ) . ' desc_len=' . strlen( (string) get_post_meta( $tid, '_yoast_wpseo_metadesc', true ) );
		}
		echo "\n";
	}
}
