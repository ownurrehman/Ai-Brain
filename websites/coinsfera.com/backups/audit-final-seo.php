<?php
/**
 * Coinsfera SEO / on-page / speed audit snapshot.
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb, $sitepress;

echo "==== CORE ====\n";
echo 'wp=' . get_bloginfo( 'version' ) . ' php=' . PHP_VERSION . "\n";
echo 'home=' . home_url( '/' ) . "\n";
echo 'blog_public=' . get_option( 'blog_public' ) . "\n";
echo 'permalink=' . get_option( 'permalink_structure' ) . "\n";
echo 'timezone=' . wp_timezone_string() . "\n";
echo 'WP_DEBUG=' . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? '1' : '0' ) . ' LOG=' . ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ? '1' : '0' ) . "\n";
$log = WP_CONTENT_DIR . '/debug.log';
echo 'debug.log=' . ( file_exists( $log ) ? size_format( filesize( $log ) ) : 'missing' ) . "\n";

$wpseo = get_option( 'wpseo' );
echo 'yoast_sitemap=' . wp_json_encode( $wpseo['enable_xml_sitemap'] ?? null ) . "\n";
echo 'yoast_content_analysis=' . wp_json_encode( $wpseo['content_analysis_active'] ?? null ) . "\n";

$icl = get_option( 'icl_sitepress_settings' );
echo 'wpml_default=' . ( $icl['default_language'] ?? '' ) . "\n";
echo 'wpml_negotiation=' . ( $icl['language_negotiation_type'] ?? '' ) . "\n";
echo 'wpml_head_langs=' . wp_json_encode( $icl['seo']['head_langs'] ?? null ) . "\n";
echo 'wpml_canonical_dupes=' . wp_json_encode( $icl['seo']['canonicalization_duplicates'] ?? null ) . "\n";
echo 'wpml_browser_redirect=' . wp_json_encode( $icl['automatic_redirect'] ?? null ) . "\n";

echo "\n==== PLUGINS ====\n";
if ( ! function_exists( 'get_plugins' ) ) {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
}
foreach ( get_option( 'active_plugins', array() ) as $p ) {
	echo $p . "\n";
}

echo "\n==== COUNTS ====\n";
foreach ( array( 'page', 'post', 'elementor_library' ) as $pt ) {
	$n = wp_count_posts( $pt );
	echo $pt . ' publish=' . ( $n->publish ?? 0 ) . ' draft=' . ( $n->draft ?? 0 ) . "\n";
}

echo "\n==== NOINDEX PAGES ====\n";
$rows = $wpdb->get_results(
	"SELECT p.ID, p.post_title, p.post_name, p.post_type, t.language_code, pm.meta_value
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->postmeta} pm ON pm.post_id=p.ID AND pm.meta_key='_yoast_wpseo_meta-robots-noindex'
	 LEFT JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type=CONCAT('post_', p.post_type)
	 WHERE p.post_status='publish' AND pm.meta_value IN ('1','2')
	 ORDER BY p.post_type, t.language_code, p.ID
	 LIMIT 80"
);
echo 'count=' . count( $rows ) . "\n";
foreach ( $rows as $r ) {
	echo "{$r->post_type} {$r->ID} {$r->language_code} {$r->post_name} robots={$r->meta_value}\n";
}

echo "\n==== EMPTY YOAST DESC (published pages) ====\n";
$pages = $wpdb->get_results(
	"SELECT p.ID, p.post_title, p.post_name, t.language_code
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page'
	 WHERE p.post_type='page' AND p.post_status='publish'
	 ORDER BY t.language_code, p.ID"
);
$empty = 0;
$long_title = 0;
$dup_titles = array();
foreach ( $pages as $p ) {
	$desc  = (string) get_post_meta( $p->ID, '_yoast_wpseo_metadesc', true );
	$title = (string) get_post_meta( $p->ID, '_yoast_wpseo_title', true );
	if ( '' === $title ) {
		$title = $p->post_title;
	}
	$dup_titles[ $title ][] = $p->language_code . ':' . $p->ID . ':' . $p->post_name;
	if ( '' === trim( $desc ) ) {
		++$empty;
		if ( $empty <= 25 ) {
			echo "empty_desc {$p->language_code} {$p->ID} {$p->post_name}\n";
		}
	}
	if ( strlen( wp_strip_all_tags( $title ) ) > 65 ) {
		++$long_title;
	}
}
echo "pages=" . count( $pages ) . " empty_desc={$empty} yoast_title_gt_65={$long_title}\n";
echo "duplicate yoast/post titles:\n";
$dups = 0;
foreach ( $dup_titles as $t => $ids ) {
	if ( count( $ids ) > 1 ) {
		++$dups;
		if ( $dups <= 12 ) {
			echo '  [' . count( $ids ) . '] ' . substr( $t, 0, 80 ) . ' => ' . implode( ',', $ids ) . "\n";
		}
	}
}
echo "dup_title_groups={$dups}\n";

echo "\n==== PAGES MISSING RU/TR ====\n";
$missing = 0;
foreach ( $pages as $p ) {
	if ( 'en' !== $p->language_code ) {
		continue;
	}
	foreach ( array( 'ru', 'tr' ) as $lang ) {
		$tid = (int) apply_filters( 'wpml_object_id', (int) $p->ID, 'page', false, $lang );
		if ( $tid <= 0 || 'publish' !== get_post_status( $tid ) ) {
			++$missing;
			echo "NO {$lang} EN {$p->ID} {$p->post_name}\n";
		}
	}
}
echo "missing_lang_pairs={$missing}\n";

echo "\n==== CUSTOM CANONICALS ====\n";
$canons = $wpdb->get_results( "SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key='_yoast_wpseo_canonical' AND meta_value<>''" );
echo 'count=' . count( $canons ) . "\n";
foreach ( $canons as $row ) {
	$post = get_post( (int) $row->post_id );
	if ( ! $post || 'publish' !== $post->post_status ) {
		continue;
	}
	echo $row->post_id . ' ' . $post->post_name . ' => ' . $row->meta_value . "\n";
}

echo "\n==== REDIRECTS (yoast / rank) ====\n";
$redir = $wpdb->get_var( "SHOW TABLES LIKE '{$wpdb->prefix}yoast_seo_links'" );
echo 'yoast_seo_links=' . ( $redir ? 'yes' : 'no' ) . "\n";
$opt = get_option( 'wpseo-premium-redirects-base' );
if ( is_array( $opt ) ) {
	echo 'yoast_premium_redirects=' . count( $opt ) . "\n";
}

echo "\n==== WPCODE SNIPPETS ====\n";
$snips = $wpdb->get_results( "SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type='wpcode' ORDER BY ID DESC LIMIT 40" );
if ( ! $snips ) {
	$snips = $wpdb->get_results( "SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_type LIKE '%wpcode%' OR post_type LIKE '%snippet%' ORDER BY ID DESC LIMIT 40" );
}
foreach ( $snips as $s ) {
	echo "{$s->ID} {$s->post_status} {$s->post_title}\n";
}

echo "\n==== HOMEPAGE / KEY PAGES YOAST ====\n";
$ids = array( 9, 6611, 11248, 5444, 15673, 11236, 3, 6975, 2152 );
foreach ( $ids as $id ) {
	$p = get_post( $id );
	if ( ! $p ) {
		echo "missing {$id}\n";
		continue;
	}
	$lang = apply_filters( 'wpml_element_language_code', null, array( 'element_id' => $id, 'element_type' => 'post_' . $p->post_type ) );
	if ( $sitepress && is_string( $lang ) ) {
		$sitepress->switch_lang( $lang );
	}
	$title = get_post_meta( $id, '_yoast_wpseo_title', true );
	$desc  = get_post_meta( $id, '_yoast_wpseo_metadesc', true );
	$noix  = get_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', true );
	echo "{$id} {$lang} {$p->post_name} noindex={$noix} title_len=" . strlen( (string) $title ) . ' desc_len=' . strlen( (string) $desc ) . ' title=' . substr( (string) $title, 0, 70 ) . "\n";
}

echo "\n==== AUTOPTIMIZE / SG ====\n";
$sg = get_option( 'siteground_optimizer_options' );
if ( ! is_array( $sg ) ) {
	$sg = array();
	foreach ( array(
		'siteground_optimizer_enable_cache',
		'siteground_optimizer_file_caching',
		'siteground_optimizer_combine_css',
		'siteground_optimizer_combine_javascript',
		'siteground_optimizer_minify_html',
		'siteground_optimizer_minify_javascript',
		'siteground_optimizer_minify_css',
		'siteground_optimizer_optimize_javascript_async',
		'siteground_optimizer_lazyload_images',
		'siteground_optimizer_webp_support',
	) as $k ) {
		$sg[ $k ] = get_option( $k );
	}
}
foreach ( $sg as $k => $v ) {
	if ( is_scalar( $v ) && ( false !== strpos( $k, 'cache' ) || false !== strpos( $k, 'minify' ) || false !== strpos( $k, 'combine' ) || false !== strpos( $k, 'lazy' ) || false !== strpos( $k, 'webp' ) || false !== strpos( $k, 'async' ) ) ) {
		echo $k . '=' . wp_json_encode( $v ) . "\n";
	}
}
