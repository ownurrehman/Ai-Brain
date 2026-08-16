<?php
/**
 * Find existing Turkish copy for leftover Buy Bitcoin widgets.
 *
 * Run: wp eval-file find-tr-sections.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb;

$needles = array(
	'buy_sell_section_buy_title',
	'buy_sell_section_title',
	'how_to_buy_section_title',
	'Follow Us on Social Media',
	'Nakit ile Bitcoin',
	'Bitcoin Alım Satım',
);

foreach ( $needles as $n ) {
	$like = '%' . $wpdb->esc_like( $n ) . '%';
	$rows = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT post_id, CHAR_LENGTH(meta_value) l FROM {$wpdb->postmeta} pm
			 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=pm.post_id AND t.element_type='post_page'
			 WHERE pm.meta_key='_elementor_data' AND t.language_code='tr' AND pm.meta_value LIKE %s
			 LIMIT 15",
			$like
		)
	);
	WP_CLI::log( 'needle ' . $n . ' hits=' . count( $rows ) );
	foreach ( $rows as $r ) {
		WP_CLI::log( '  ' . $r->post_id . ' ' . get_the_title( $r->post_id ) . ' bytes=' . $r->l );
	}
}

function cfkl_widget_settings( $post_id, $widget ) {
	$j = json_decode( (string) get_post_meta( $post_id, '_elementor_data', true ), true );
	$found = array();
	$walk  = function ( $nodes ) use ( &$walk, &$found, $widget ) {
		if ( ! is_array( $nodes ) ) {
			return;
		}
		foreach ( $nodes as $n ) {
			if ( ! is_array( $n ) ) {
				continue;
			}
			if ( ( $n['widgetType'] ?? '' ) === $widget ) {
				$found[] = $n['settings'] ?? array();
			}
			if ( ! empty( $n['elements'] ) ) {
				$walk( $n['elements'] );
			}
		}
	};
	$walk( $j );
	return $found;
}

$keys = array(
	'buy_sell_section_title',
	'buy_sell_section_buy_title',
	'buy_sell_section_buy_desc',
	'buy_sell_section_buy_btn_title',
	'buy_sell_section_sell_title',
	'buy_sell_section_sell_desc',
	'buy_sell_section_sell_btn_title',
	'buy_sell_section_consultancy_title',
	'buy_sell_section_consultancy_desc',
	'buy_sell_section_consultancy_btn_title',
);

WP_CLI::log( '==== sample TR buy_sell from other pages ====' );
$ids = $wpdb->get_col( "SELECT element_id FROM {$wpdb->prefix}icl_translations WHERE language_code='tr' AND element_type='post_page'" );
foreach ( $ids as $id ) {
	$sets = cfkl_widget_settings( (int) $id, 'buy_sell_section' );
	if ( ! $sets ) {
		continue;
	}
	$s = $sets[0];
	$title = (string) ( $s['buy_sell_section_buy_title'] ?? '' );
	if ( '' === $title || false !== stripos( $title, 'Buy Bitcoin with Cash' ) ) {
		continue;
	}
	WP_CLI::log( 'PAGE ' . $id . ' ' . get_the_title( $id ) );
	foreach ( $keys as $k ) {
		if ( ! empty( $s[ $k ] ) && is_string( $s[ $k ] ) ) {
			WP_CLI::log( '  ' . $k . '=' . substr( $s[ $k ], 0, 110 ) );
		}
	}
	if ( ! empty( $s['buy_sell_section_items'] ) && is_array( $s['buy_sell_section_items'] ) ) {
		foreach ( $s['buy_sell_section_items'] as $i => $item ) {
			WP_CLI::log( '  item' . $i . '=' . ( $item['title'] ?? '' ) . ' / ' . ( $item['counter'] ?? '' ) );
		}
	}
	break;
}

WP_CLI::log( '==== sample TR how_to_buy ====' );
foreach ( $ids as $id ) {
	$sets = cfkl_widget_settings( (int) $id, 'how_to_buy_section' );
	if ( ! $sets ) {
		continue;
	}
	$s = $sets[0];
	$title = (string) ( $s['how_to_buy_section_title'] ?? '' );
	if ( '' === $title || false !== stripos( $title, 'Benefits of Coinsfera' ) ) {
		continue;
	}
	WP_CLI::log( 'PAGE ' . $id . ' ' . get_the_title( $id ) );
	WP_CLI::log( '  title=' . $title );
	WP_CLI::log( '  btn=' . ( $s['how_to_buy_section_btn_lbl'] ?? '' ) );
	if ( ! empty( $s['how_to_buy_section_items'] ) && is_array( $s['how_to_buy_section_items'] ) ) {
		foreach ( $s['how_to_buy_section_items'] as $i => $item ) {
			WP_CLI::log( '  item' . $i . ' ' . ( $item['title'] ?? '' ) . ' | ' . substr( (string) ( $item['desc'] ?? '' ), 0, 90 ) );
		}
	}
	break;
}

WP_CLI::log( '==== current TR 11226 leftover widgets ====' );
foreach ( array( 'buy_sell_section', 'how_to_buy_section', 'heading', 'text-editor' ) as $w ) {
	$sets = cfkl_widget_settings( 11226, $w );
	foreach ( $sets as $i => $s ) {
		WP_CLI::log( $w . '#' . $i );
		foreach ( $s as $k => $v ) {
			if ( is_string( $v ) && strlen( $v ) > 8 && preg_match( '/title|desc|lbl|editor|text/i', $k ) ) {
				WP_CLI::log( '  ' . $k . '=' . substr( $v, 0, 100 ) );
			}
			if ( is_array( $v ) && preg_match( '/items/', (string) $k ) ) {
				foreach ( $v as $item ) {
					if ( is_array( $item ) ) {
						WP_CLI::log( '  item title=' . ( $item['title'] ?? $item['title_text'] ?? '' ) . ' desc=' . substr( (string) ( $item['desc'] ?? '' ), 0, 70 ) );
					}
				}
			}
		}
	}
}
