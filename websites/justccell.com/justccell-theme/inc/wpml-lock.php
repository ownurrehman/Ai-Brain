<?php
/**
 * Force WPML to use ?lang= so /es and /ch stay country stores (UK has no prefix).
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_WPML_NEGOTIATION_PARAMETER = 3;

/**
 * @param mixed $value
 * @return mixed
 */
function justccell_lock_wpml_settings_array($value)
{
    if (!is_array($value)) {
        return $value;
    }

    $value['language_negotiation_type'] = JUSTCCELL_WPML_NEGOTIATION_PARAMETER;
    $value['automatic_redirect']        = 0;
    $value['remember_language']         = 1;

    if (!isset($value['default_language']) || $value['default_language'] === '') {
        $value['default_language'] = 'en';
    }

    // Contact and other English pages 404 at ?lang=es unless WPML falls back.
    if (!isset($value['custom_posts_display_as_translated']) || !is_array($value['custom_posts_display_as_translated'])) {
        $value['custom_posts_display_as_translated'] = [];
    }
    foreach (['post', 'page', 'product'] as $type) {
        $value['custom_posts_display_as_translated'][$type] = 1;
    }

    return $value;
}

add_filter('pre_update_option_icl_sitepress_settings', 'justccell_lock_wpml_settings_array');
add_filter('option_icl_sitepress_settings', 'justccell_lock_wpml_settings_array');

add_action('wpml_loaded', 'justccell_lock_wpml_runtime', 1);

function justccell_lock_wpml_runtime(): void
{
    global $sitepress;
    if (!is_object($sitepress) || !method_exists($sitepress, 'set_setting')) {
        return;
    }

    $sitepress->set_setting('language_negotiation_type', JUSTCCELL_WPML_NEGOTIATION_PARAMETER, true);
    $sitepress->set_setting('automatic_redirect', 0, true);

    $display = $sitepress->get_setting('custom_posts_display_as_translated', []);
    if (!is_array($display)) {
        $display = [];
    }
    foreach (['post', 'page', 'product'] as $type) {
        $display[$type] = 1;
    }
    $sitepress->set_setting('custom_posts_display_as_translated', $display, true);
}

/**
 * WCML "currency per language" (1) would make Spain+English leave EUR.
 * Independent (2) or off (0) only. Theme still sets currency from the store URL.
 *
 * @param mixed $value
 * @return mixed
 */
function justccell_lock_wcml_settings($value)
{
    if (!is_array($value)) {
        return $value;
    }

    if (isset($value['enable_multi_currency']) && (int) $value['enable_multi_currency'] === 1) {
        $value['enable_multi_currency'] = 2;
    }

    return $value;
}

add_filter('pre_update_option__wcml_settings', 'justccell_lock_wcml_settings');
add_filter('option__wcml_settings', 'justccell_lock_wcml_settings');

add_filter('wcml_client_currency', 'justccell_wcml_client_currency', 100);

function justccell_wcml_client_currency($currency): string
{
    unset($currency);
    return justccell_current_currency();
}

/**
 * WPML 404s /contact/?lang=es when page 12 is English-only. Rebind the published page.
 */
function justccell_recover_untranslated_page(): void
{
    if (is_admin() || wp_doing_ajax() || !is_404()) {
        return;
    }

    $path = function_exists('justccell_request_path') ? justccell_request_path() : '';
    if (function_exists('justccell_path_without_store')) {
        $path = justccell_path_without_store($path);
    }
    if (preg_match('#^/([a-z0-9][a-z0-9-]{0,190})/?$#', $path, $match) !== 1) {
        return;
    }

    $slug = $match[1];
    $skip = [
        'cart'        => true,
        'checkout'    => true,
        'my-account'  => true,
        'shop'        => true,
        'feed'        => true,
        'wp-json'     => true,
        'wp-admin'    => true,
        'xmlrpc.php'  => true,
    ];
    if (isset($skip[$slug])) {
        return;
    }

    $page = null;
    if (function_exists('justccell_find_page_by_slug')) {
        $found = justccell_find_page_by_slug($slug);
        if ($found instanceof WP_Post && $found->post_status === 'publish') {
            $page = $found;
        }
    }
    if (!$page instanceof WP_Post) {
        $found = get_posts([
            'name'             => $slug,
            'post_type'        => 'page',
            'post_status'      => 'publish',
            'posts_per_page'   => 1,
            'suppress_filters' => true,
            'no_found_rows'    => true,
        ]);
        $page = isset($found[0]) && $found[0] instanceof WP_Post ? $found[0] : null;
    }
    if (!$page instanceof WP_Post) {
        return;
    }

    global $wp_query, $post;
    $wp_query->init();
    $wp_query->is_404            = false;
    $wp_query->is_page           = true;
    $wp_query->is_singular       = true;
    $wp_query->is_home           = false;
    $wp_query->is_posts_page     = false;
    $wp_query->queried_object    = $page;
    $wp_query->queried_object_id = (int) $page->ID;
    $wp_query->posts             = [$page];
    $wp_query->post              = $page;
    $wp_query->post_count        = 1;
    $wp_query->found_posts       = 1;
    $wp_query->max_num_pages     = 1;
    $wp_query->set('page_id', (int) $page->ID);
    $wp_query->set('pagename', $slug);
    $wp_query->set('error', '');
    $post = $page;
    setup_postdata($page);
    status_header(200);

    add_filter('pre_get_document_title', static function (string $title) use ($page): string {
        unset($title);
        $rm = (string) get_post_meta((int) $page->ID, 'rank_math_title', true);
        if (trim($rm) !== '') {
            return trim($rm);
        }
        $name = get_bloginfo('name');
        $page_title = get_the_title($page);
        return $page_title !== '' ? $page_title . ' | ' . $name : $name;
    }, 100);
}

add_action('wp', 'justccell_recover_untranslated_page', 1);
add_action('template_redirect', 'justccell_recover_untranslated_page', 1);

