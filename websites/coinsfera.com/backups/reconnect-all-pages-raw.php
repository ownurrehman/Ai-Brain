<?php
/**
 * Copy English Elementor JSON onto RU/TR using raw SQL so WPML/wp_slash
 * cannot corrupt the blob.
 *
 * Run: wp eval-file reconnect-all-pages-raw.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb;

$keys = array(
	'_elementor_data',
	'_elementor_page_settings',
	'_elementor_edit_mode',
	'_elementor_version',
	'_elementor_template_type',
);

/**
 * Raw post meta value as stored in the database.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @return string|null
 */
function coinsfera_raw_meta( $post_id, $key ) {
	global $wpdb;

	$val = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT meta_value FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s LIMIT 1",
			$post_id,
			$key
		)
	);

	return is_string( $val ) ? $val : null;
}

/**
 * Write a raw meta blob, bypassing KSES/WPML filters.
 *
 * @param int    $post_id Post ID.
 * @param string $key     Meta key.
 * @param string $value   Raw DB value.
 * @return void
 */
function coinsfera_write_raw_meta( $post_id, $key, $value ) {
	global $wpdb;

	$meta_id = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT meta_id FROM {$wpdb->postmeta} WHERE post_id = %d AND meta_key = %s LIMIT 1",
			$post_id,
			$key
		)
	);

	if ( $meta_id ) {
		$wpdb->update(
			$wpdb->postmeta,
			array( 'meta_value' => $value ),
			array( 'meta_id' => (int) $meta_id )
		);
	} else {
		$wpdb->insert(
			$wpdb->postmeta,
			array(
				'post_id'    => $post_id,
				'meta_key'   => $key,
				'meta_value' => $value,
			)
		);
	}

	wp_cache_delete( $post_id, 'post_meta' );
	clean_post_cache( $post_id );
}

/**
 * Count widgets from a JSON string (slashed or not).
 *
 * @param string $raw Meta value.
 * @return int
 */
function coinsfera_count_widgets_raw( $raw ) {
	$json = json_decode( $raw, true );
	if ( ! is_array( $json ) ) {
		$json = json_decode( stripslashes( $raw ), true );
	}
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
	$walk( is_array( $json ) ? $json : array() );
	return $count;
}

$en_pages = $wpdb->get_results(
	"SELECT p.ID, p.post_name
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t
	   ON t.element_id = p.ID AND t.element_type = 'post_page'
	 WHERE p.post_type = 'page'
	   AND p.post_status = 'publish'
	   AND t.language_code = 'en'
	   AND t.source_language_code IS NULL
	 ORDER BY p.ID"
);

$copied  = 0;
$skipped = 0;

foreach ( $en_pages as $en ) {
	$en_id   = (int) $en->ID;
	$en_blob = coinsfera_raw_meta( $en_id, '_elementor_data' );

	$ru_id = (int) apply_filters( 'wpml_object_id', $en_id, 'page', false, 'ru' );
	$tr_id = (int) apply_filters( 'wpml_object_id', $en_id, 'page', false, 'tr' );
	$targets = array();
	if ( $ru_id > 0 && $ru_id !== $en_id && 'publish' === get_post_status( $ru_id ) ) {
		$targets['ru'] = $ru_id;
	}
	if ( $tr_id > 0 && $tr_id !== $en_id && 'publish' === get_post_status( $tr_id ) ) {
		$targets['tr'] = $tr_id;
	}

	delete_post_meta( $en_id, '_wpml_post_translation_editor_native' );

	if ( ! $targets ) {
		++$skipped;
		continue;
	}

	if ( ! is_string( $en_blob ) || strlen( $en_blob ) < 50 ) {
		foreach ( $targets as $id ) {
			delete_post_meta( $id, '_wpml_post_translation_editor_native' );
		}
		++$skipped;
		WP_CLI::log( "SKIP {$en_id} {$en->post_name} (no Elementor JSON)" );
		continue;
	}

	$en_widgets = coinsfera_count_widgets_raw( $en_blob );
	$line       = "EN {$en_id} {$en->post_name} widgets={$en_widgets}";

	foreach ( $targets as $lang => $id ) {
		foreach ( $keys as $key ) {
			$blob = coinsfera_raw_meta( $en_id, $key );
			if ( null === $blob ) {
				continue;
			}
			coinsfera_write_raw_meta( $id, $key, $blob );
		}
		delete_post_meta( $id, '_wpml_post_translation_editor_native' );
		delete_post_meta( $id, '_elementor_css' );
		delete_post_meta( $id, '_elementor_element_cache' );
		delete_post_meta( $id, '_elementor_page_assets' );

		$after_blob = coinsfera_raw_meta( $id, '_elementor_data' );
		$after      = coinsfera_count_widgets_raw( (string) $after_blob );
		$match      = ( $after_blob === $en_blob ) ? 'yes' : 'NO';
		$json_ok    = is_array( json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true ) ) ? 'json-ok' : 'json-FAIL';
		$line      .= " {$lang}={$id} {$after} match={$match} {$json_ok}";
		++$copied;
	}

	$trid = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type='post_page'",
			$en_id
		)
	);
	$trans = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND element_type='post_page' AND source_language_code IS NOT NULL",
			$trid
		)
	);
	foreach ( $trans as $row ) {
		$wpdb->update(
			$wpdb->prefix . 'icl_translation_status',
			array( 'needs_update' => 1 ),
			array( 'translation_id' => (int) $row->translation_id )
		);
	}

	WP_CLI::log( $line );
}

if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
}

WP_CLI::log( "copied={$copied} skipped={$skipped}" );
