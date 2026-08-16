<?php
/**
 * Reconnect every translated page to its English Elementor layout.
 *
 * English stays in Elementor. RU/TR get the same JSON so WPML's
 * "Edit Translation" button owns the strings.
 *
 * Run: wp eval-file reconnect-all-pages.php
 */

if ( ! defined( 'ABSPATH' ) ) {
	fwrite( STDERR, "Run with wp eval-file\n" );
	exit( 1 );
}

global $wpdb;

$stamp = gmdate( 'Ymd-His' );
$bak   = WP_CONTENT_DIR . '/uploads/cfkl-backups/all-pages-wpml-' . $stamp;

if ( ! is_dir( $bak ) ) {
	wp_mkdir_p( $bak );
}

file_put_contents(
	$bak . '/.htaccess',
	"Require all denied\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n"
);

$keys = array(
	'_elementor_data',
	'_elementor_page_settings',
	'_elementor_edit_mode',
	'_elementor_version',
	'_elementor_template_type',
	'_wpml_post_translation_editor_native',
);

$en_pages = $wpdb->get_results(
	"SELECT p.ID, p.post_name, p.post_title
	 FROM {$wpdb->posts} p
	 JOIN {$wpdb->prefix}icl_translations t
	   ON t.element_id = p.ID AND t.element_type = 'post_page'
	 WHERE p.post_type = 'page'
	   AND p.post_status = 'publish'
	   AND t.language_code = 'en'
	   AND t.source_language_code IS NULL
	 ORDER BY p.ID"
);

$copied   = 0;
$skipped  = 0;
$flagged  = 0;
$cleared  = 0;

/**
 * Count Elementor widgets in a post.
 *
 * @param int $id Post ID.
 * @return int
 */
function coinsfera_widget_count( $id ) {
	$data = json_decode( (string) get_post_meta( $id, '_elementor_data', true ), true );
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

/**
 * Backup one post's Elementor/WPML meta.
 *
 * @param string $dir  Backup directory.
 * @param string $lang Language code.
 * @param int    $id   Post ID.
 * @param array  $keys Meta keys.
 * @return void
 */
function coinsfera_backup_meta( $dir, $lang, $id, $keys ) {
	foreach ( $keys as $key ) {
		$val  = get_post_meta( $id, $key, true );
		$file = $dir . '/' . $lang . '-' . $id . '-' . $key . '.json';
		file_put_contents( $file, is_string( $val ) ? $val : wp_json_encode( $val ) );
	}
}

foreach ( $en_pages as $en ) {
	$en_id   = (int) $en->ID;
	$en_data = get_post_meta( $en_id, '_elementor_data', true );
	$ru_id   = (int) apply_filters( 'wpml_object_id', $en_id, 'page', false, 'ru' );
	$tr_id   = (int) apply_filters( 'wpml_object_id', $en_id, 'page', false, 'tr' );

	$targets = array();
	if ( $ru_id > 0 && $ru_id !== $en_id && 'publish' === get_post_status( $ru_id ) ) {
		$targets['ru'] = $ru_id;
	}
	if ( $tr_id > 0 && $tr_id !== $en_id && 'publish' === get_post_status( $tr_id ) ) {
		$targets['tr'] = $tr_id;
	}

	if ( ! $targets ) {
		++$skipped;
		WP_CLI::log( "SKIP {$en_id} {$en->post_name} (no RU/TR)" );
		continue;
	}

	delete_post_meta( $en_id, '_wpml_post_translation_editor_native' );
	++$cleared;

	if ( ! is_string( $en_data ) || strlen( $en_data ) < 50 ) {
		++$skipped;
		WP_CLI::log( "SKIP {$en_id} {$en->post_name} (no Elementor JSON)" );
		foreach ( $targets as $lang => $id ) {
			delete_post_meta( $id, '_wpml_post_translation_editor_native' );
		}
		continue;
	}

	coinsfera_backup_meta( $bak, 'en', $en_id, $keys );
	$en_widgets = coinsfera_widget_count( $en_id );
	$en_settings = get_post_meta( $en_id, '_elementor_page_settings', true );
	$en_mode     = get_post_meta( $en_id, '_elementor_edit_mode', true );
	$en_ver      = get_post_meta( $en_id, '_elementor_version', true );
	$en_type     = get_post_meta( $en_id, '_elementor_template_type', true );

	$line = "EN {$en_id} {$en->post_name} widgets={$en_widgets}";

	foreach ( $targets as $lang => $id ) {
		$before = coinsfera_widget_count( $id );
		coinsfera_backup_meta( $bak, $lang, $id, $keys );

		update_post_meta( $id, '_elementor_data', $en_data );
		if ( '' !== $en_settings && false !== $en_settings ) {
			update_post_meta( $id, '_elementor_page_settings', $en_settings );
		}
		if ( '' !== $en_mode ) {
			update_post_meta( $id, '_elementor_edit_mode', $en_mode );
		}
		if ( '' !== $en_ver ) {
			update_post_meta( $id, '_elementor_version', $en_ver );
		}
		if ( '' !== $en_type ) {
			update_post_meta( $id, '_elementor_template_type', $en_type );
		}
		delete_post_meta( $id, '_wpml_post_translation_editor_native' );
		delete_post_meta( $id, '_elementor_css' );
		delete_post_meta( $id, '_elementor_element_cache' );
		delete_post_meta( $id, '_elementor_page_assets' );

		$after = coinsfera_widget_count( $id );
		$match = get_post_meta( $id, '_elementor_data', true ) === $en_data ? 'yes' : 'NO';
		$line .= " {$lang}={$id} {$before}->{$after} match={$match}";
		++$copied;
	}

	$trid  = $wpdb->get_var(
		$wpdb->prepare(
			"SELECT trid FROM {$wpdb->prefix}icl_translations WHERE element_id=%d AND element_type='post_page'",
			$en_id
		)
	);
	$trans = $wpdb->get_results(
		$wpdb->prepare(
			"SELECT translation_id, language_code FROM {$wpdb->prefix}icl_translations WHERE trid=%d AND element_type='post_page' AND source_language_code IS NOT NULL",
			$trid
		)
	);
	foreach ( $trans as $row ) {
		$updated = $wpdb->update(
			$wpdb->prefix . 'icl_translation_status',
			array( 'needs_update' => 1 ),
			array( 'translation_id' => (int) $row->translation_id )
		);
		if ( false !== $updated ) {
			++$flagged;
		}
	}

	WP_CLI::log( $line );
}

delete_option( 'wpml_config_files_arr' );

if ( class_exists( '\Elementor\Plugin' ) && isset( \Elementor\Plugin::$instance->files_manager ) ) {
	\Elementor\Plugin::$instance->files_manager->clear_cache();
	WP_CLI::log( 'elementor css cache cleared' );
}

file_put_contents(
	$bak . '/summary.json',
	wp_json_encode(
		array(
			'copied'  => $copied,
			'skipped' => $skipped,
			'flagged' => $flagged,
			'cleared' => $cleared,
			'bak'     => $bak,
		),
		JSON_PRETTY_PRINT
	)
);

WP_CLI::log( "copied={$copied} skipped={$skipped} flagged={$flagged} backup={$bak}" );
