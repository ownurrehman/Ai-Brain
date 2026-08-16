<?php
/**
 * English Elementor pages stay in Elementor. Translations use WPML's own editor.
 *
 * Do not add a custom admin-bar item. WPML already puts "Edit Translation" in
 * the toolbar when the page is set to the WPML Translation Editor.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Language code WPML has stored for a post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function coinsfera_wpml_post_lang( $post_id ) {

	$post_id = (int) $post_id;

	if ( $post_id <= 0 || ! has_filter( 'wpml_element_language_code' ) ) {
		return '';
	}

	$type = 'post_' . get_post_type( $post_id );

	$lang = apply_filters(
		'wpml_element_language_code',
		null,
		array(
			'element_id'   => $post_id,
			'element_type' => $type,
		)
	);

	return is_string( $lang ) ? $lang : '';
}

/**
 * Whether this post is a WPML translation, not the English original.
 *
 * @param int $post_id Post ID.
 * @return bool
 */
function coinsfera_wpml_is_translation( $post_id ) {

	$lang    = coinsfera_wpml_post_lang( $post_id );
	$default = apply_filters( 'wpml_default_language', null );

	return ( '' !== $lang && is_string( $default ) && $lang !== $default );
}

/**
 * Real WPML Advanced Translation Editor URL for a translated post.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function coinsfera_wpml_editor_url( $post_id ) {

	$post_id  = (int) $post_id;
	$fallback = admin_url( 'post.php?post=' . $post_id . '&action=edit' );

	if ( $post_id <= 0 ) {
		return $fallback;
	}

	if ( function_exists( 'wpml_tm_load_status_display_filter' ) ) {
		wpml_tm_load_status_display_filter();
	}

	$type     = 'post_' . get_post_type( $post_id );
	$trid     = apply_filters( 'wpml_element_trid', null, $post_id, $type );
	$lang     = coinsfera_wpml_post_lang( $post_id );
	$default  = apply_filters( 'wpml_default_language', null );
	$original = (int) apply_filters( 'wpml_object_id', $post_id, get_post_type( $post_id ), true, $default );

	if ( $original <= 0 ) {
		$original = $post_id;
	}

	$link = apply_filters( 'wpml_link_to_translation', '', $original, $lang, $trid );

	if ( ! is_string( $link ) || '' === $link || '#' === $link ) {
		return $fallback;
	}

	if ( 0 === strpos( $link, 'http://' ) || 0 === strpos( $link, 'https://' ) ) {
		return $link;
	}

	return admin_url( $link );
}

/**
 * Keep translations on WPML's editor even if a per-post override exists.
 *
 * @param mixed $mode    Current mode.
 * @param mixed $post_id Post ID when provided.
 * @return mixed
 */
function coinsfera_wpml_force_tm_editor( $mode, $post_id = 0 ) {

	if ( $post_id && coinsfera_wpml_is_translation( (int) $post_id ) ) {
		return true;
	}

	return $mode;
}
add_filter( 'wpml_use_tm_editor', 'coinsfera_wpml_force_tm_editor', 20, 2 );

/**
 * Elementor injects "Edit with Elementor" from JS. Strip that config on translations.
 *
 * @param array $settings Admin bar config.
 * @return array
 */
function coinsfera_wpml_strip_elementor_admin_bar( $settings ) {

	$post_id = is_singular() ? (int) get_queried_object_id() : 0;

	if ( $post_id && coinsfera_wpml_is_translation( $post_id ) ) {
		unset( $settings['elementor_edit_page'] );
	}

	return $settings;
}
add_filter( 'elementor/frontend/admin_bar/settings', 'coinsfera_wpml_strip_elementor_admin_bar', 99 );

/**
 * Hide leftover Elementor admin-bar nodes if they were added in PHP.
 *
 * @param WP_Admin_Bar $bar Admin bar.
 * @return void
 */
function coinsfera_wpml_admin_bar( $bar ) {

	$post_id = 0;

	if ( is_admin() ) {
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( is_singular() ) {
		$post_id = (int) get_queried_object_id();
	}

	if ( $post_id <= 0 || ! coinsfera_wpml_is_translation( $post_id ) ) {
		return;
	}

	$bar->remove_node( 'elementor_edit_page' );
	$bar->remove_node( 'elementor-inspector' );
	$bar->remove_node( 'elementor_edit_document' );
	$bar->remove_node( 'coinsfera-wpml-editor' );
}
add_action( 'admin_bar_menu', 'coinsfera_wpml_admin_bar', 999 );

/**
 * Remove Elementor row actions on translated pages.
 *
 * @param array   $actions Row actions.
 * @param WP_Post $post    Post.
 * @return array
 */
function coinsfera_wpml_row_actions( $actions, $post ) {

	if ( isset( $post->ID ) && coinsfera_wpml_is_translation( (int) $post->ID ) ) {
		unset( $actions['edit_with_elementor'], $actions['elementor'] );
	}

	return $actions;
}
add_filter( 'page_row_actions', 'coinsfera_wpml_row_actions', 99, 2 );
add_filter( 'post_row_actions', 'coinsfera_wpml_row_actions', 99, 2 );

/**
 * Send Elementor's own edit URL to WPML's editor on translations.
 *
 * @param string $url      Edit URL.
 * @param object $document Elementor document.
 * @return string
 */
function coinsfera_wpml_elementor_edit_url( $url, $document ) {

	if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_id' ) ) {
		return $url;
	}

	$id = (int) $document->get_main_id();

	if ( $id && coinsfera_wpml_is_translation( $id ) ) {
		return coinsfera_wpml_editor_url( $id );
	}

	return $url;
}
add_filter( 'elementor/document/urls/edit', 'coinsfera_wpml_elementor_edit_url', 20, 2 );

/**
 * Hard-block /wp-admin/post.php?action=elementor on translations.
 *
 * @return void
 */
function coinsfera_wpml_block_elementor_on_translations() {

	if ( ! is_admin() ) {
		return;
	}

	$action = isset( $_GET['action'] ) ? sanitize_key( wp_unslash( $_GET['action'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( 'elementor' !== $action ) {
		return;
	}

	$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended

	if ( $post_id && coinsfera_wpml_is_translation( $post_id ) ) {
		wp_safe_redirect( coinsfera_wpml_editor_url( $post_id ) );
		exit;
	}
}
add_action( 'admin_init', 'coinsfera_wpml_block_elementor_on_translations', 1 );

/**
 * Hide the Elementor "Edit with Elementor" button on translation edit screens.
 *
 * @return void
 */
function coinsfera_wpml_hide_elementor_button() {

	$post_id = 0;

	if ( is_admin() ) {
		$post_id = isset( $_GET['post'] ) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	} elseif ( is_singular() ) {
		$post_id = (int) get_queried_object_id();
	}

	if ( ! $post_id || ! coinsfera_wpml_is_translation( $post_id ) ) {
		return;
	}

	echo '<style id="coinsfera-wpml-hide-elementor">
		#elementor-switch-mode,
		#elementor-editor,
		.elementor-switch-mode,
		#elementor-go-to-edit-page-link,
		#wp-admin-bar-elementor_edit_page,
		#wp-admin-bar-coinsfera-wpml-editor { display: none !important; }
	</style>';
}
add_action( 'admin_head', 'coinsfera_wpml_hide_elementor_button' );
add_action( 'wp_head', 'coinsfera_wpml_hide_elementor_button' );

/**
 * Swap Elementor global widget template IDs to the current language.
 *
 * Global widgets are separate Elementor library posts. Without this, a Turkish
 * page keeps loading the English template and the heading never changes.
 *
 * @param array $data    Elementor document data.
 * @param int   $post_id Post being rendered.
 * @return array
 */
function coinsfera_wpml_swap_global_template_ids( $data, $post_id = 0 ) {

	if ( ! is_array( $data ) || ! has_filter( 'wpml_object_id' ) ) {
		return $data;
	}

	// Use the page being viewed, not WPML's current language. Global widgets
	// can render after WPML has already mapped template IDs to the wrong
	// translation (Russian pages were loading the Turkish copies).
	$lang = '';
	$page_id = is_singular() ? (int) get_queried_object_id() : (int) $post_id;

	if ( $page_id > 0 ) {
		$lang = coinsfera_wpml_post_lang( $page_id );
	}

	if ( '' === $lang ) {
		$lang = apply_filters( 'wpml_current_language', null );
	}

	$default = apply_filters( 'wpml_default_language', null );

	if ( ! is_string( $lang ) || ! is_string( $default ) || $lang === $default ) {
		return $data;
	}

	foreach ( $data as &$el ) {
		if ( ! is_array( $el ) ) {
			continue;
		}

		if ( 'global' === ( $el['widgetType'] ?? '' ) && ! empty( $el['templateID'] ) ) {
			$translated = (int) apply_filters( 'wpml_object_id', (int) $el['templateID'], 'elementor_library', true, $lang );
			if ( $translated > 0 ) {
				$el['templateID'] = $translated;
			}
		}

		if ( ! empty( $el['elements'] ) && is_array( $el['elements'] ) ) {
			$el['elements'] = coinsfera_wpml_swap_global_template_ids( $el['elements'], $post_id );
		}
	}

	return $data;
}
add_filter( 'elementor/frontend/builder_content_data', 'coinsfera_wpml_swap_global_template_ids', 99, 2 );

/**
 * When an English Elementor document is saved, mark WPML translations stale
 * so new widgets appear in the translation editor.
 *
 * @param object $document Elementor document.
 * @return void
 */
function coinsfera_wpml_flag_after_elementor_save( $document ) {

	if ( ! is_object( $document ) || ! method_exists( $document, 'get_main_id' ) ) {
		return;
	}

	coinsfera_wpml_flag_translations_need_update( (int) $document->get_main_id() );
}
add_action( 'elementor/document/after_save', 'coinsfera_wpml_flag_after_elementor_save', 20 );

/**
 * Set needs_update on every WPML translation of a post.
 *
 * @param int $post_id Original post ID.
 * @return void
 */
function coinsfera_wpml_flag_translations_need_update( $post_id ) {

	global $wpdb;

	$post_id = (int) $post_id;

	if ( $post_id <= 0 || coinsfera_wpml_is_translation( $post_id ) || ! has_filter( 'wpml_element_trid' ) ) {
		return;
	}

	$type = 'post_' . get_post_type( $post_id );
	$trid = apply_filters( 'wpml_element_trid', null, $post_id, $type );

	if ( ! $trid ) {
		return;
	}

	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT translation_id FROM {$wpdb->prefix}icl_translations WHERE trid = %d AND source_language_code IS NOT NULL",
			$trid
		)
	);

	foreach ( $ids as $translation_id ) {
		$wpdb->update(
			$wpdb->prefix . 'icl_translation_status',
			array( 'needs_update' => 1 ),
			array( 'translation_id' => (int) $translation_id )
		);
	}
}

/**
 * Keep Elementor internals out of the WPML editor.
 *
 * _elementor_page_assets stores CSS/JS handles such as widget-heading.
 * Those are not visitor-facing strings and should not use translation credits.
 *
 * @param array $fields Translation job fields.
 * @return array
 */
function coinsfera_wpml_drop_asset_job_fields( $fields ) {

	if ( ! is_array( $fields ) ) {
		return $fields;
	}

	$skip = array(
		'elementor_page_assets',
		'elementor_controls_usage',
	);

	$out = array();

	foreach ( $fields as $field ) {
		$type = '';
		if ( is_array( $field ) ) {
			$type = (string) ( $field['field_type'] ?? $field['type'] ?? '' );
		}
		$drop = false;
		foreach ( $skip as $needle ) {
			if ( $type && false !== strpos( $type, $needle ) ) {
				$drop = true;
				break;
			}
		}
		if ( ! $drop ) {
			$out[] = $field;
		}
	}

	return $out;
}
add_filter( 'wpml_tm_adjust_translation_fields', 'coinsfera_wpml_drop_asset_job_fields' );

/**
 * True when a staff browser session is present.
 *
 * locale filters run before WordPress finishes authenticating the user, so
 * is_user_logged_in() is often still false. The logged-in cookie is enough
 * to keep the admin bar in English without changing visitor pages.
 *
 * @return bool
 */
function coinsfera_staff_session_present() {

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return false;
	}

	if ( defined( 'LOGGED_IN_COOKIE' ) && ! empty( $_COOKIE[ LOGGED_IN_COOKIE ] ) ) {
		return true;
	}

	return function_exists( 'is_user_logged_in' ) && is_user_logged_in();
}

/**
 * Keep the WordPress admin bar and wp-admin in English.
 *
 * WPML maps locale to the page language on /ru/ and /tr/, which translates
 * "Edit Page" and the rest of the black bar. Visitors still see translated
 * menus and page copy. Logged-in staff get English labels.
 *
 * @param string|null $locale Current locale.
 * @return string|null
 */
function coinsfera_keep_wp_admin_english( $locale ) {

	if ( coinsfera_staff_session_present() ) {
		return 'en_US';
	}

	return $locale;
}
add_filter( 'locale', 'coinsfera_keep_wp_admin_english', 999 );
add_filter( 'plugin_locale', 'coinsfera_keep_wp_admin_english', 999 );
add_filter( 'determine_locale', 'coinsfera_keep_wp_admin_english', 999 );
add_filter( 'pre_determine_locale', 'coinsfera_keep_wp_admin_english', 999 );

/**
 * Whether a translation file is Russian or Turkish.
 *
 * @param string $path File path.
 * @return bool
 */
function coinsfera_is_ru_or_tr_mofile( $path ) {

	if ( ! is_string( $path ) || '' === $path ) {
		return false;
	}

	return (bool) preg_match( '/(?:^|[\/\\\\._-])(ru_RU|tr_TR|ru_ru|tr_tr)(?:[\/\\\\._-]|$)/', $path );
}

/**
 * Do not load ru_RU / tr_TR gettext files for logged-in staff.
 *
 * switch_to_locale( 'en_US' ) is a no-op when the locale filter already
 * returns en_US, so WPML's earlier ru/tr .mo files stay in memory. Skipping
 * those files (and wiping $l10n below) keeps the admin bar in English.
 *
 * @param bool   $override Whether to short-circuit loading.
 * @param string $domain   Text domain.
 * @param string $mofile   File path.
 * @return bool
 */
function coinsfera_staff_skip_ru_tr_textdomain( $override, $domain, $mofile ) {

	if ( coinsfera_staff_session_present() && coinsfera_is_ru_or_tr_mofile( $mofile ) ) {
		return true;
	}

	return $override;
}
add_filter( 'override_load_textdomain', 'coinsfera_staff_skip_ru_tr_textdomain', 0, 3 );

/**
 * Point staff locale files at English when a path still contains ru/tr.
 *
 * @param string $mofile File path.
 * @param string $domain Text domain.
 * @return string
 */
function coinsfera_staff_english_mofile( $mofile, $domain ) {

	if ( ! coinsfera_staff_session_present() || ! coinsfera_is_ru_or_tr_mofile( $mofile ) ) {
		return $mofile;
	}

	$english = preg_replace( '/(ru_RU|tr_TR|ru_ru|tr_tr)/', 'en_US', $mofile );

	return ( is_string( $english ) && $english !== $mofile && file_exists( $english ) ) ? $english : $mofile;
}
add_filter( 'load_textdomain_mofile', 'coinsfera_staff_english_mofile', 0, 2 );

/**
 * HTML lang must follow the page language (en-US / ru-RU / tr-TR).
 *
 * Staff locale is forced to English for the admin bar. Crawlers still need
 * the URL language on <html lang>, matching hreflang and og:locale.
 *
 * @return string
 */
function coinsfera_page_html_lang() {

	$code = 'en';

	if ( has_filter( 'wpml_current_language' ) ) {
		$wpml = apply_filters( 'wpml_current_language', null );
		if ( is_string( $wpml ) && '' !== $wpml ) {
			$code = $wpml;
		}
	} elseif ( defined( 'ICL_LANGUAGE_CODE' ) && ICL_LANGUAGE_CODE ) {
		$code = ICL_LANGUAGE_CODE;
	}

	$map = array(
		'en' => 'en-US',
		'ru' => 'ru-RU',
		'tr' => 'tr-TR',
	);

	return $map[ $code ] ?? $code;
}

/**
 * Keep language_attributes() on the page language, not the staff locale.
 *
 * @param string $output Existing attributes string.
 * @return string
 */
function coinsfera_filter_language_attributes( $output ) {

	$lang = coinsfera_page_html_lang();

	if ( preg_match( '/lang=(["\'])[^"\']*\1/', $output ) ) {
		$output = preg_replace( '/lang=(["\'])[^"\']*\1/', 'lang="' . esc_attr( $lang ) . '"', $output, 1 );
	} else {
		$output = trim( $output . ' lang="' . esc_attr( $lang ) . '"' );
	}

	return $output;
}
add_filter( 'language_attributes', 'coinsfera_filter_language_attributes', 20 );

/**
 * Page language for visitor-facing plugin strings (not staff locale).
 *
 * @return string
 */
function coinsfera_frontend_lang_code() {

	$page_id = is_singular() ? (int) get_queried_object_id() : 0;
	if ( $page_id > 0 ) {
		$lang = coinsfera_wpml_post_lang( $page_id );
		if ( is_string( $lang ) && '' !== $lang ) {
			return $lang;
		}
	}

	if ( has_filter( 'wpml_current_language' ) ) {
		$wpml = apply_filters( 'wpml_current_language', null );
		if ( is_string( $wpml ) && '' !== $wpml ) {
			return $wpml;
		}
	}

	return 'en';
}

/**
 * Calculator labels in coinsfera-plugin are gettext. Staff locale is English
 * and the plugin has no TR/RU .mo files, so "Want to spend" stayed English
 * on translated pages. Map those few frontend strings to the page language.
 *
 * @param string $translation Current translation.
 * @param string $text        Original string.
 * @param string $domain      Text domain.
 * @return string
 */
function coinsfera_page_lang_plugin_strings( $translation, $text, $domain ) {

	if ( 'coinsfera-plugin' !== $domain || is_admin() ) {
		return $translation;
	}

	$map = array(
		'Want to spend' => array(
			'tr' => 'Harcamak istediğiniz',
			'ru' => 'Хотите потратить',
		),
		'Get'           => array(
			'tr' => 'Alın',
			'ru' => 'Получить',
		),
	);

	if ( ! isset( $map[ $text ] ) ) {
		return $translation;
	}

	$lang = coinsfera_frontend_lang_code();
	return $map[ $text ][ $lang ] ?? $translation;
}
add_filter( 'gettext', 'coinsfera_page_lang_plugin_strings', 20, 3 );

/**
 * Reload English translations after WPML has already loaded ru_RU / tr_TR.
 *
 * @return void
 */
function coinsfera_switch_staff_locale_to_english() {

	if ( ! coinsfera_staff_session_present() ) {
		return;
	}

	global $l10n;

	if ( is_array( $l10n ) ) {
		foreach ( array_keys( $l10n ) as $domain ) {
			if ( function_exists( 'unload_textdomain' ) ) {
				unload_textdomain( $domain, true );
			}
		}
	}

	if ( function_exists( 'load_default_textdomain' ) ) {
		load_default_textdomain( 'en_US' );
	}

	if ( function_exists( 'switch_to_locale' ) ) {
		$switched = switch_to_locale( 'en_US' );
		if ( ! $switched && function_exists( 'load_default_textdomain' ) ) {
			load_default_textdomain( 'en_US' );
		}
	}
}
add_action( 'plugins_loaded', 'coinsfera_switch_staff_locale_to_english', 99 );
add_action( 'init', 'coinsfera_switch_staff_locale_to_english', 99 );
add_action( 'wp', 'coinsfera_switch_staff_locale_to_english', 0 );
add_action( 'admin_bar_init', 'coinsfera_switch_staff_locale_to_english', 0 );

/**
 * True on the public site (including the admin bar on RU/TR pages).
 *
 * @return bool
 */
function coinsfera_is_frontend_view() {

	if ( defined( 'WP_CLI' ) && WP_CLI ) {
		return false;
	}

	if ( is_admin() && ! wp_doing_ajax() ) {
		return false;
	}

	return true;
}

/**
 * Show English menu titles to logged-in staff. URLs stay on the page language
 * so /ru/ and /tr/ links still open the translation being edited.
 *
 * @param array $items Menu items.
 * @return array
 */
function coinsfera_staff_english_nav_menu_items( $items ) {

	if ( ! coinsfera_staff_session_present() || ! coinsfera_is_frontend_view() || ! is_array( $items ) ) {
		return $items;
	}

	$default = apply_filters( 'wpml_default_language', 'en' );
	if ( ! is_string( $default ) || '' === $default ) {
		$default = 'en';
	}

	foreach ( $items as $item ) {
		if ( ! is_object( $item ) || empty( $item->ID ) ) {
			continue;
		}

		$en_id = (int) apply_filters( 'wpml_object_id', (int) $item->ID, 'nav_menu_item', true, $default );
		if ( $en_id <= 0 ) {
			$en_id = (int) $item->ID;
		}

		$source = get_post( $en_id );
		if ( $source && ! empty( $source->post_title ) ) {
			$item->title      = $source->post_title;
			$item->post_title = $source->post_title;
		}
	}

	return $items;
}
add_filter( 'wp_get_nav_menu_items', 'coinsfera_staff_english_nav_menu_items', 99 );

/**
 * Keep WPML string-translated theme copy in English for staff (footer titles).
 *
 * @param string      $translated Current value.
 * @param string      $context    String domain.
 * @param string      $name       String name.
 * @param string|null $language   Target language.
 * @return string
 */
function coinsfera_staff_keep_english_strings( $translated, $context, $name, $language = null ) {

	if ( ! coinsfera_staff_session_present() || ! coinsfera_is_frontend_view() ) {
		return $translated;
	}

	if ( is_string( $language ) && 'en' === $language ) {
		return $translated;
	}

	remove_filter( 'wpml_translate_single_string', 'coinsfera_staff_keep_english_strings', 99 );
	$english = apply_filters( 'wpml_translate_single_string', $translated, $context, $name, 'en' );
	add_filter( 'wpml_translate_single_string', 'coinsfera_staff_keep_english_strings', 99, 4 );

	return is_string( $english ) && '' !== $english ? $english : $translated;
}
add_filter( 'wpml_translate_single_string', 'coinsfera_staff_keep_english_strings', 99, 4 );
