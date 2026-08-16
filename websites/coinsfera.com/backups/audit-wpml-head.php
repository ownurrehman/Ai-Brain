<?php
/**
 * Dump Yoast/WPML head tags for Buy Bitcoin, homepage, and Sell Bitcoin.
 *
 * Run: wp eval-file audit-wpml-head.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $sitepress, $wp_query, $post, $wp_the_query;

/**
 * Print canonical/hreflang/open graph from wp_head.
 *
 * @param int    $id   Post ID.
 * @param string $lang Language code.
 * @return void
 */
function coinsfera_dump_head( $id, $lang ) {
	global $sitepress, $wp_query, $post, $wp_the_query;

	$id = (int) $id;
	if ( $id <= 0 ) {
		echo "SKIP empty id for {$lang}\n";
		return;
	}

	$sitepress->switch_lang( $lang );
	$post                = get_post( $id );
	$wp_query            = new WP_Query( array( 'page_id' => $id ) );
	$wp_the_query        = $wp_query;
	$GLOBALS['post']     = $post;
	setup_postdata( $post );

	ob_start();
	do_action( 'wp_head' );
	$head = ob_get_clean();

	echo "\n######## {$lang} post {$id} " . get_permalink( $id ) . " ########\n";
	if ( preg_match( '/<title>(.*?)<\/title>/is', $head, $m ) ) {
		echo 'title=' . trim( preg_replace( '/\s+/', ' ', $m[1] ) ) . "\n";
	}

	foreach ( array( 'canonical', 'alternate' ) as $rel ) {
		$pattern = '/<link[^>]+rel=["\']' . $rel . '["\'][^>]*>/i';
		if ( preg_match_all( $pattern, $head, $ms ) ) {
			foreach ( $ms[0] as $tag ) {
				echo $tag . "\n";
			}
		} else {
			echo "NO {$rel} tags\n";
		}
	}

	foreach ( array( 'og:locale', 'og:locale:alternate', 'og:url', 'og:title', 'description', 'robots' ) as $prop ) {
		$pattern = '/<meta[^>]+(?:property|name)=["\']' . preg_quote( $prop, '/' ) . '["\'][^>]*>/i';
		if ( preg_match_all( $pattern, $head, $ms ) ) {
			foreach ( $ms[0] as $tag ) {
				echo $tag . "\n";
			}
		}
	}
}

$front = (int) get_option( 'page_on_front' );
echo 'page_on_front=' . $front . ' title=' . get_the_title( $front ) . "\n";
foreach ( array( 'en', 'ru', 'tr' ) as $lang ) {
	$tid = (int) apply_filters( 'wpml_object_id', $front, 'page', true, $lang );
	$sitepress->switch_lang( $lang );
	echo "front {$lang}={$tid} permalink=" . get_permalink( $tid ) . "\n";
}

coinsfera_dump_head( 2036, 'en' );
coinsfera_dump_head( 6644, 'ru' );
coinsfera_dump_head( 11226, 'tr' );

$front_en = (int) apply_filters( 'wpml_object_id', $front, 'page', true, 'en' );
$front_ru = (int) apply_filters( 'wpml_object_id', $front, 'page', true, 'ru' );
$front_tr = (int) apply_filters( 'wpml_object_id', $front, 'page', true, 'tr' );
coinsfera_dump_head( $front_en, 'en' );
coinsfera_dump_head( $front_ru, 'ru' );
coinsfera_dump_head( $front_tr, 'tr' );

$sell_en = 0;
$pages   = get_posts(
	array(
		'name'        => 'sell-bitcoin-in-istanbul',
		'post_type'   => 'page',
		'post_status' => 'publish',
		'numberposts' => 20,
	)
);
echo "\n===== sell bitcoin pages =====\n";
foreach ( $pages as $p ) {
	$lang = apply_filters(
		'wpml_element_language_code',
		null,
		array(
			'element_id'   => $p->ID,
			'element_type' => 'post_page',
		)
	);
	echo "{$p->ID} lang={$lang} title={$p->post_title}\n";
	if ( 'en' === $lang ) {
		$sell_en = (int) $p->ID;
	}
}
if ( $sell_en ) {
	foreach ( array( 'en', 'ru', 'tr' ) as $lang ) {
		$sid = (int) apply_filters( 'wpml_object_id', $sell_en, 'page', true, $lang );
		echo "sell {$lang}={$sid}\n";
		coinsfera_dump_head( $sid, $lang );
	}
}

echo "\n===== sitemap rewrite =====\n";
echo 'permalink_structure=' . get_option( 'permalink_structure' ) . "\n";
$index = function_exists( 'wpseo_sitemaps_dir' ) ? 'yoast' : 'yoast-or-other';
echo "seo-sitemap={$index}\n";
if ( class_exists( 'WPSEO_Sitemaps_Router' ) ) {
	echo "yoast sitemap router present\n";
}
