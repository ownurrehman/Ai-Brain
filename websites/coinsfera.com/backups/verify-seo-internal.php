<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit( 1 );
}
global $wpdb, $sitepress;

function cf_robots_for( $id ) {
	$idx = $GLOBALS['wpdb']->get_row(
		$GLOBALS['wpdb']->prepare(
			"SELECT is_robots_noindex, is_public FROM {$GLOBALS['wpdb']->prefix}yoast_indexable WHERE object_id=%d AND object_type='post'",
			$id
		)
	);
	$meta = get_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', true );
	$title = '';
	if ( function_exists( 'YoastSEO' ) ) {
		try {
			$m     = YoastSEO()->meta->for_post( $id );
			$title = $m ? (string) $m->title : '';
		} catch ( Exception $e ) {
			$title = 'err ' . $e->getMessage();
		}
	}
	if ( '' === $title ) {
		$title = (string) get_post_meta( $id, '_yoast_wpseo_title', true );
	}
	return array(
		'id'       => $id,
		'meta'     => $meta,
		'idx'      => $idx ? $idx->is_robots_noindex : 'missing',
		'public'   => $idx ? $idx->is_public : 'missing',
		'title'    => $title,
		'link'     => get_permalink( $id ),
	);
}

echo "WP_DEBUG=" . ( defined( 'WP_DEBUG' ) && WP_DEBUG ? '1' : '0' ) . " LOG=" . ( defined( 'WP_DEBUG_LOG' ) && WP_DEBUG_LOG ? '1' : '0' ) . "\n";
echo 'debug.log=' . ( file_exists( WP_CONTENT_DIR . '/debug.log' ) ? size_format( filesize( WP_CONTENT_DIR . '/debug.log' ) ) : 'gone' ) . "\n";
echo 'snippet28093=' . get_post_status( 28093 ) . "\n";
echo 'term73=' . $wpdb->get_var( 'SELECT slug FROM ' . $wpdb->terms . ' WHERE term_id=73' ) . "\n";

echo "NEW 2362 " . wp_json_encode( cf_robots_for( 2362 ), JSON_UNESCAPED_UNICODE ) . "\n";
echo "OLD 2345 " . wp_json_encode( cf_robots_for( 2345 ), JSON_UNESCAPED_UNICODE ) . "\n";
echo "RU_LTC 12885 " . wp_json_encode( cf_robots_for( 12885 ), JSON_UNESCAPED_UNICODE ) . "\n";
echo "RU_HOME 6611 " . wp_json_encode( cf_robots_for( 6611 ), JSON_UNESCAPED_UNICODE ) . "\n";
echo "TR_HOME 11248 " . wp_json_encode( cf_robots_for( 11248 ), JSON_UNESCAPED_UNICODE ) . "\n";

if ( $sitepress ) {
	$sitepress->switch_lang( 'ru' );
}
echo 'ru_term_link=' . get_term_link( 73, 'category' ) . "\n";
echo 'ru_sample_permalink=' . get_permalink( 25511 ) . "\n";

echo "redirects:\n";
foreach ( array( 341, 793, 818, 819, 820, 821 ) as $id ) {
	$row = $wpdb->get_row( $wpdb->prepare( "SELECT id, url, match_url, regex, action_code, action_data, status FROM {$wpdb->prefix}redirection_items WHERE id=%d", $id ), ARRAY_A );
	echo wp_json_encode( $row, JSON_UNESCAPED_UNICODE ) . "\n";
}
$broken = (int) $wpdb->get_var(
	"SELECT COUNT(*) FROM {$wpdb->prefix}redirection_items WHERE status='enabled' AND (action_data LIKE '%h%d0%be%d0%b2%d0%be%d1%81%d1%82%d0%b8%' OR action_data LIKE '%/hовости%')"
);
echo "broken_targets={$broken}\n";

$ihaf = (string) get_option( 'ihaf_insert_header' );
echo 'ihaf_gtm=' . ( false !== strpos( $ihaf, 'GTM-P7ZNP7K' ) ? 'yes' : 'no' ) . "\n";
echo 'ihaf_yandex_tag=' . ( false !== strpos( $ihaf, 'mc.yandex.ru' ) ? 'YES_BAD' : 'no' ) . "\n";
echo 'ihaf_ahrefs=' . ( false !== strpos( $ihaf, 'ahrefs.com' ) ? 'YES_BAD' : 'no' ) . "\n";
echo 'ihaf_trustpilot=' . ( false !== strpos( $ihaf, 'trustpilot' ) ? 'YES_BAD' : 'no' ) . "\n";
echo 'ihaf_verify=' . ( false !== strpos( $ihaf, 'yandex-verification' ) ? 'yes' : 'no' ) . "\n";

$header = file_get_contents( get_template_directory() . '/header.php' );
echo 'header_cdnjs_fa=' . ( false !== strpos( $header, 'cdnjs.cloudflare.com/ajax/libs/font-awesome' ) ? 'YES_BAD' : 'no' ) . "\n";
echo 'header_debug_log=' . ( false !== strpos( $header, 'error_log("Header loaded' ) ? 'YES_BAD' : 'no' ) . "\n";
echo 'theme_perf=' . ( file_exists( get_template_directory() . '/inc/perf.php' ) ? 'yes' : 'no' ) . "\n";
echo 'theme_news=' . ( file_exists( get_template_directory() . '/inc/seo-news-noindex.php' ) ? 'yes' : 'no' ) . "\n";

if ( class_exists( 'Red_Item' ) ) {
	$url = '/ru/news/' . rawurlencode( 'как-пользоваться-trust-wallet' ) . '/';
	$items = Red_Item::get_for_url( $url, 'GET' );
	echo 'match_count=' . ( is_array( $items ) ? count( $items ) : 0 ) . "\n";
	if ( is_array( $items ) ) {
		foreach ( $items as $item ) {
			if ( method_exists( $item, 'get_action_data' ) ) {
				echo 'match_to=' . $item->get_action_data() . " code=" . $item->get_action_code() . "\n";
			} else {
				echo 'item=' . wp_json_encode( $item ) . "\n";
			}
		}
	}
}
