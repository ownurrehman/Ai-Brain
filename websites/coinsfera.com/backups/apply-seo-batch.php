<?php
/**
 * One-shot: news noindex by age, RU news slug + redirects, RU/TR titles, slim IHAF header.
 *
 * wp eval-file apply-seo-batch.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb, $sitepress;

$backup_dir = WP_CONTENT_DIR . '/uploads/cfkl-backups';
if ( ! is_dir( $backup_dir ) ) {
	wp_mkdir_p( $backup_dir );
}
$stamp = gmdate( 'Ymd-His' );

echo "==== 1. Disable WPCode 28093 (bulk news noindex) ====\n";
$snippet = get_post( 28093 );
if ( $snippet ) {
	echo "found post_type={$snippet->post_type} status={$snippet->post_status} title={$snippet->post_title}\n";
	file_put_contents( "{$backup_dir}/wpcode-28093-{$stamp}.php", (string) $snippet->post_content );
	$wpdb->update(
		$wpdb->posts,
		array(
			'post_status'  => 'draft',
			'post_content' => "// Disabled {$stamp}: news older than 3 years is noindexed in the theme (inc/seo-news-noindex.php).\n",
		),
		array( 'ID' => 28093 ),
		array( '%s', '%s' ),
		array( '%d' )
	);
	clean_post_cache( 28093 );
	echo "drafted 28093\n";
} else {
	$maybe = $wpdb->get_row( "SELECT ID, post_title, post_status FROM {$wpdb->posts} WHERE post_title LIKE '%no index%' AND post_status='publish' LIMIT 5", ARRAY_A );
	echo '28093 missing; similar=' . wp_json_encode( $maybe ) . "\n";
}

echo "\n==== 2. RU news category slug → novosti ====\n";
if ( $sitepress ) {
	$sitepress->switch_lang( 'ru' );
}
$term = get_term( 73, 'category' );
echo 'before slug=' . ( $term->slug ?? '' ) . ' name=' . ( $term->name ?? '' ) . "\n";
$updated = wp_update_term( 73, 'category', array( 'slug' => 'novosti' ) );
echo 'update=' . wp_json_encode( $updated ) . "\n";
clean_term_cache( 73, 'category' );
$term = get_term( 73, 'category' );
echo 'after slug=' . ( $term->slug ?? '' ) . ' link=' . get_term_link( 73, 'category' ) . "\n";

$icl_strings = $wpdb->get_results(
	"SELECT s.id, s.name, s.value, st.language, st.value AS trans
	 FROM {$wpdb->prefix}icl_strings s
	 LEFT JOIN {$wpdb->prefix}icl_string_translations st ON st.string_id=s.id AND st.language='ru'
	 WHERE s.value LIKE '%убрик%' OR s.value LIKE '%новост%' OR st.value LIKE '%убрик%' OR st.value LIKE '%h%d0%'
	 LIMIT 30"
);
echo "wpml slug strings:\n";
foreach ( $icl_strings as $row ) {
	echo "  {$row->id} {$row->name} src={$row->value} ru={$row->trans}\n";
	$ru = (string) $row->trans;
	if ( $ru && ( false !== strpos( $ru, 'h%d0' ) || preg_match( '/^hовости/u', $ru ) ) ) {
		$wpdb->update(
			$wpdb->prefix . 'icl_string_translations',
			array( 'value' => 'novosti' ),
			array( 'string_id' => $row->id, 'language' => 'ru' )
		);
		echo "    -> set ru trans to novosti\n";
	}
	if ( $ru && preg_match( '/^p[уy]брик/u', $ru ) ) {
		$wpdb->update(
			$wpdb->prefix . 'icl_string_translations',
			array( 'value' => 'рубрика' ),
			array( 'string_id' => $row->id, 'language' => 'ru' )
		);
		echo "    -> set ru trans to рубрика\n";
	}
}

flush_rewrite_rules( false );
if ( $sitepress ) {
	$sitepress->switch_lang( 'ru' );
}

echo "\n==== 3. Repair broken RU news redirects ====\n";
$broken = $wpdb->get_results(
	"SELECT id, url, match_url, action_data, regex, action_code, group_id, match_type, status
	 FROM {$wpdb->prefix}redirection_items
	 WHERE status='enabled'
	   AND (action_data LIKE '%h%d0%be%d0%b2%d0%be%d1%81%d1%82%d0%b8%'
	     OR action_data LIKE '%/hовости%'
	     OR action_data LIKE '%hовости%')"
);
echo 'broken_rows=' . count( $broken ) . "\n";
$fixed = 0;
$miss  = 0;
foreach ( $broken as $row ) {
	$path = (string) wp_parse_url( $row->url, PHP_URL_PATH );
	$slug = urldecode( trim( basename( $path ), '/' ) );
	$post = $wpdb->get_row(
		$wpdb->prepare(
			"SELECT ID, post_name FROM {$wpdb->posts} WHERE post_name=%s AND post_type='post' LIMIT 1",
			sanitize_title( $slug )
		)
	);
	if ( ! $post ) {
		$post = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT ID, post_name FROM {$wpdb->posts} WHERE post_name=%s AND post_type='post' LIMIT 1",
				$slug
			)
		);
	}
	if ( ! $post && $slug ) {
		$like = $wpdb->esc_like( mb_substr( $slug, 0, 24 ) );
		$post = $wpdb->get_row(
			$wpdb->prepare(
				"SELECT p.ID, p.post_name
				 FROM {$wpdb->posts} p
				 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_post'
				 WHERE p.post_type='post' AND p.post_status='publish' AND t.language_code='ru'
				   AND (p.post_name LIKE %s OR p.post_title LIKE %s)
				 LIMIT 1",
				$like . '%',
				'%' . $like . '%'
			)
		);
	}
	$ru_id = $post ? (int) apply_filters( 'wpml_object_id', (int) $post->ID, 'post', true, 'ru' ) : 0;
	$link  = $ru_id ? get_permalink( $ru_id ) : '';
	$path_to = $link ? (string) wp_parse_url( $link, PHP_URL_PATH ) : '';
	if ( $path_to ) {
		$wpdb->update(
			$wpdb->prefix . 'redirection_items',
			array( 'action_data' => $path_to ),
			array( 'id' => (int) $row->id ),
			array( '%s' ),
			array( '%d' )
		);
		echo "ok id={$row->id} {$row->url} -> {$path_to}\n";
		++$fixed;
	} else {
		echo "MISS id={$row->id} from={$row->url} slug={$slug}\n";
		++$miss;
	}
}
echo "redirects_fixed={$fixed} miss={$miss}\n";

$group_id = (int) $wpdb->get_var( "SELECT group_id FROM {$wpdb->prefix}redirection_items WHERE status='enabled' ORDER BY id DESC LIMIT 1" );
if ( $group_id <= 0 ) {
	$group_id = 1;
}
$catchalls = array(
	array( '/ru/news/(.*)', '/ru/novosti/$1' ),
	array( '/ru/hовости/(.*)', '/ru/novosti/$1' ),
	array( '/ru/h%d0%be%d0%b2%d0%be%d1%81%d1%82%d0%b8/(.*)', '/ru/novosti/$1' ),
);
foreach ( $catchalls as $pair ) {
	list( $from, $to ) = $pair;
	$exists = (int) $wpdb->get_var(
		$wpdb->prepare(
			"SELECT id FROM {$wpdb->prefix}redirection_items WHERE url=%s LIMIT 1",
			$from
		)
	);
	if ( $exists ) {
		$wpdb->update(
			$wpdb->prefix . 'redirection_items',
			array(
				'action_data' => $to,
				'regex'       => 1,
				'status'      => 'enabled',
				'action_code' => 301,
			),
			array( 'id' => $exists )
		);
		echo "catchall update id={$exists} {$from} -> {$to}\n";
		continue;
	}
	$wpdb->insert(
		$wpdb->prefix . 'redirection_items',
		array(
			'url'         => $from,
			'match_url'   => 'regex',
			'match_data'  => '',
			'action_type' => 'url',
			'action_code' => 301,
			'action_data' => $to,
			'match_type'  => 'url',
			'title'       => 'RU news slug repair',
			'regex'       => 1,
			'group_id'    => $group_id,
			'status'      => 'enabled',
			'position'    => 0,
		)
	);
	echo 'catchall insert id=' . $wpdb->insert_id . " {$from} -> {$to}\n";
}

echo "\n==== 4. News noindex by age (3 years, original EN date) ====\n";
if ( function_exists( 'coinsfera_news_cron_refresh' ) ) {
	coinsfera_news_cron_refresh();
} else {
	echo "theme function missing — applying inline\n";
	$cat_ids = array( 8, 73, 99 );
	$ids     = get_posts(
		array(
			'category__in'     => $cat_ids,
			'numberposts'      => -1,
			'post_status'      => 'publish',
			'post_type'        => 'post',
			'fields'           => 'ids',
			'suppress_filters' => true,
		)
	);
	$cutoff = strtotime( '-3 years' );
	foreach ( $ids as $id ) {
		$orig = (int) apply_filters( 'wpml_object_id', (int) $id, 'post', true, 'en' );
		$date = (string) get_post_field( 'post_date', $orig ?: $id );
		if ( strtotime( $date ) < $cutoff ) {
			update_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', '1' );
		} else {
			delete_post_meta( $id, '_yoast_wpseo_meta-robots-noindex' );
		}
	}
}

$news_ids = get_posts(
	array(
		'category__in'     => array( 8, 73, 99 ),
		'numberposts'      => -1,
		'post_status'      => 'publish',
		'post_type'        => 'post',
		'fields'           => 'ids',
		'suppress_filters' => true,
	)
);
$noindex = 0;
$index   = 0;
$cutoff  = strtotime( '-3 years' );
$sample_new = array();
$sample_old = array();
foreach ( $news_ids as $id ) {
	$orig = (int) apply_filters( 'wpml_object_id', (int) $id, 'post', true, 'en' );
	$date = (string) get_post_field( 'post_date', $orig ?: $id );
	$meta = get_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', true );
	if ( strtotime( $date ) < $cutoff ) {
		++$noindex;
		if ( count( $sample_old ) < 3 ) {
			$sample_old[] = $id . ' ' . $date . ' meta=' . $meta;
		}
	} else {
		++$index;
		if ( count( $sample_new ) < 3 ) {
			$sample_new[] = $id . ' ' . $date . ' meta=' . var_export( $meta, true ) . ' ' . get_permalink( $id );
		}
	}
}
echo 'news_posts=' . count( $news_ids ) . " old_noindex={$noindex} recent_indexable={$index}\n";
echo 'sample_old=' . implode( ' | ', $sample_old ) . "\n";
echo 'sample_new=' . implode( ' | ', $sample_new ) . "\n";

echo "\n==== 5. Shorten RU/TR money-page titles ====\n";
$coins = array(
	'bitcoin'        => 'Bitcoin',
	'ethereum'       => 'Ethereum',
	'tether'         => 'USDT',
	'usdt'           => 'USDT',
	'litecoin'       => 'Litecoin',
	'ripple'         => 'XRP',
	'xrp'            => 'XRP',
	'binance-coin'   => 'BNB',
	'bnb'            => 'BNB',
	'bitcoin-cash'   => 'Bitcoin Cash',
	'stellar'        => 'Stellar',
	'dash'           => 'Dash',
	'monero'         => 'Monero',
	'zcash'          => 'Zcash',
	'tron'           => 'TRON',
	'cardano'        => 'Cardano',
	'dogecoin'       => 'Dogecoin',
	'solana'         => 'Solana',
	'usdc'           => 'USDC',
	'polygon'        => 'Polygon',
	'shiba'          => 'SHIB',
	'avalanche'      => 'Avalanche',
	'polkadot'       => 'Polkadot',
	'cryptocurrency' => array( 'en' => 'Crypto', 'ru' => 'криптовалюту', 'tr' => 'kripto' ),
);

$forced = array(
	6611  => 'Coinsfera — криптобиржа в Стамбуле',
	11248 => "Coinsfera: İstanbul kripto borsası",
	12885 => 'Купить Litecoin в Стамбуле | Coinsfera',
);

$pages = $wpdb->get_results(
	"SELECT p.ID, p.post_title, p.post_name, t.language_code
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page'
	 WHERE p.post_type='page' AND p.post_status='publish' AND t.language_code IN ('ru','tr')"
);
$title_changed = 0;
foreach ( $pages as $p ) {
	$new = '';
	if ( isset( $forced[ (int) $p->ID ] ) ) {
		$new = $forced[ (int) $p->ID ];
	} else {
		$en_id   = (int) apply_filters( 'wpml_object_id', (int) $p->ID, 'page', true, 'en' );
		$en_slug = $en_id ? (string) get_post_field( 'post_name', $en_id ) : (string) $p->post_name;
		if ( preg_match( '/^(buy|sell)-(.+)-in-istanbul$/', $en_slug, $m ) ) {
			$action = $m[1];
			$key    = $m[2];
			$coin   = $coins[ $key ] ?? null;
			if ( is_array( $coin ) ) {
				$coin = $coin[ $p->language_code ] ?? $coin['en'];
			}
			if ( $coin ) {
				if ( 'ru' === $p->language_code ) {
					$verb = ( 'buy' === $action ) ? 'Купить' : 'Продать';
					$new  = "{$verb} {$coin} в Стамбуле | Coinsfera";
				} else {
					$verb = ( 'buy' === $action ) ? 'alın' : 'satın';
					$new  = "İstanbul'da {$coin} {$verb} | Coinsfera";
				}
			}
		}
	}
	if ( '' === $new ) {
		continue;
	}
	$old = (string) get_post_meta( $p->ID, '_yoast_wpseo_title', true );
	if ( $old === $new ) {
		continue;
	}
	update_post_meta( $p->ID, '_yoast_wpseo_title', $new );
	echo strlen( $new ) . "\t{$p->language_code}\t{$p->ID}\t{$p->post_name}\t{$old}  =>  {$new}\n";
	++$title_changed;
}
echo "titles_updated={$title_changed}\n";

echo "\n==== 6. Slim Insert Headers (keep GTM + verifications) ====\n";
$ihaf = (string) get_option( 'ihaf_insert_header' );
file_put_contents( "{$backup_dir}/ihaf-insert-header-{$stamp}.html", $ihaf );
$orig_len = strlen( $ihaf );
$ihaf     = preg_replace( '#<!--\s*Trustpilot[\s\S]*?<script[^>]*trustpilot[^>]*>\s*</script>#i', '', $ihaf );
$ihaf     = preg_replace( '#<script[^>]*trustpilot[^>]*>\s*</script>#i', '', $ihaf );
$ihaf     = preg_replace( '#<!--\s*/?Yandex[\s\S]*?<!--\s*/Yandex[^\n]*-->#i', '', $ihaf );
$ihaf     = preg_replace( '#<script[^>]*>[\s\S]*?mc\.yandex\.ru[\s\S]*?</script>#i', '', $ihaf );
$ihaf     = preg_replace( '#<noscript>[\s\S]*?mc\.yandex\.ru[\s\S]*?</noscript>#i', '', $ihaf );
$ihaf     = preg_replace( '#<!--\s*Ahrefs[\s\S]*?<script[^>]*>[\s\S]*?ahrefs[\s\S]*?</script>#i', '', $ihaf );
$ihaf     = preg_replace( '#<script[^>]*ahrefs[^>]*>[\s\S]*?</script>#i', '', $ihaf );
$ihaf     = preg_replace( '#<!--\s*Active Menu[\s\S]*?</script>#i', '', $ihaf );
$ihaf     = preg_replace( "/\n{3,}/", "\n\n", $ihaf );
update_option( 'ihaf_insert_header', $ihaf );
echo "ihaf {$orig_len} -> " . strlen( $ihaf ) . "\n";
echo 'kept_gtm=' . ( false !== stripos( $ihaf, 'GTM-P7ZNP7K' ) ? 'yes' : 'NO' ) . "\n";
echo 'kept_verify=' . ( false !== stripos( $ihaf, 'yandex-verification' ) ? 'yes' : 'NO' ) . "\n";
echo 'stripped_yandex_tag=' . ( false === stripos( $ihaf, 'mc.yandex.ru' ) ? 'yes' : 'NO' ) . "\n";
echo 'stripped_ahrefs=' . ( false === stripos( $ihaf, 'ahrefs.com' ) ? 'yes' : 'NO' ) . "\n";
echo 'stripped_trustpilot=' . ( false === stripos( $ihaf, 'trustpilot' ) ? 'yes' : 'NO' ) . "\n";

if ( $sitepress ) {
	$sitepress->switch_lang( 'en' );
}

echo "\nDONE {$stamp}\n";
