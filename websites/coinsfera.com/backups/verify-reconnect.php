<?php
/**
 * Verify reconnect: native meta gone, widget counts match.
 *
 * Run: wp eval-file verify-reconnect.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb, $sitepress;

$native = $wpdb->get_results(
	"SELECT post_id, meta_value FROM {$wpdb->postmeta} WHERE meta_key = '_wpml_post_translation_editor_native' AND meta_value <> ''"
);
echo "=== native meta ===\n";
if ( ! $native ) {
	echo "none\n";
} else {
	foreach ( $native as $row ) {
		echo $row->post_id . ' =' . $row->meta_value . "\n";
	}
}

$s  = get_option( 'icl_sitepress_settings' );
$tm = $s['translation-management'] ?? array();
echo "\n=== WPML method ===\n";
echo 'doc_translation_method=' . ( $tm['doc_translation_method'] ?? '' ) . "\n";
echo '_elementor_data=' . ( $tm['custom_fields_translation']['_elementor_data'] ?? '' ) . "\n";

echo "\n=== widget alignment ===\n";
$ens = $wpdb->get_results(
	"SELECT p.ID, p.post_name FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t ON t.element_id=p.ID AND t.element_type='post_page'
	 WHERE p.post_type='page' AND p.post_status='publish' AND t.language_code='en' AND t.source_language_code IS NULL
	 ORDER BY p.ID"
);

/**
 * Count widgets.
 *
 * @param int $id Post ID.
 * @return int
 */
function coinsfera_wcount( $id ) {
	$data  = json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true );
	$count = 0;
	$walk  = function ( $nodes ) use ( &$walk, &$count ) {
		if ( ! is_array( $nodes ) ) {
			return;
		}
		foreach ( $nodes as $node ) {
			if ( ! is_array( $node ) ) {
				continue;
			}
			if ( 'widget' === ( $node['elType'] ?? '' ) ) {
				++$count;
			}
			if ( ! empty( $node['elements'] ) ) {
				$walk( $node['elements'] );
			}
		}
	};
	$walk( $data );
	return $count;
}

$bad = 0;
foreach ( $ens as $en ) {
	$ru = (int) apply_filters( 'wpml_object_id', (int) $en->ID, 'page', false, 'ru' );
	$tr = (int) apply_filters( 'wpml_object_id', (int) $en->ID, 'page', false, 'tr' );
	if ( ! $ru && ! $tr ) {
		continue;
	}
	$ew = coinsfera_wcount( $en->ID );
	if ( $ew <= 0 ) {
		continue;
	}
	$rw = $ru ? coinsfera_wcount( $ru ) : 0;
	$tw = $tr ? coinsfera_wcount( $tr ) : 0;
	$ok = ( ! $ru || $rw === $ew ) && ( ! $tr || $tw === $ew );
	if ( ! $ok ) {
		++$bad;
	}
	echo ( $ok ? 'OK  ' : 'BAD ' ) . $en->post_name . " en={$ew} ru={$rw} tr={$tw}\n";
}
echo "mismatched={$bad}\n";

$sitepress->switch_lang( 'ru' );
echo "\nhome ru=" . get_permalink( 6611 ) . "\n";
$sitepress->switch_lang( 'tr' );
echo 'sell tr=' . get_permalink( 11235 ) . "\n";
