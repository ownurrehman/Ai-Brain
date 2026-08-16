<?php
/**
 * Force WPML to use ?lang= so /es /de /us /ae /ch stay country stores.
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
}

add_action('init', 'justccell_ensure_wpml_languages', 20);

function justccell_ensure_wpml_languages(): void
{
    if (!justccell_is_wpml_active() || !is_admin()) {
        return;
    }

    global $sitepress;
    if (!is_object($sitepress) || !method_exists($sitepress, 'set_active_languages')) {
        return;
    }

    $required = array_keys(justccell_languages());
    $active   = [];
    if (method_exists($sitepress, 'get_active_languages')) {
        $current = $sitepress->get_active_languages();
        if (is_array($current)) {
            $active = array_keys($current);
        }
    }

    $missing = array_diff($required, $active);
    if ($missing === []) {
        return;
    }

    $sitepress->set_active_languages(array_values(array_unique(array_merge($active, $required))));
    if (method_exists($sitepress, 'set_default_language')) {
        $sitepress->set_default_language('en');
    }
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
