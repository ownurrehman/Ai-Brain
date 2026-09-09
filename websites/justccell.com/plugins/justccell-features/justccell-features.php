<?php
/**
 * Plugin Name: justCCELL Features
 * Plugin URI: https://rankray.com
 * Description: Justccell storefront features plus a portable WooCommerce Quick Stock editor (Products list). Full storefront requires the Justccell theme; Quick Stock works on any WooCommerce site.
 * Version: 1.1.44
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Requires Plugins: woocommerce
 * WC requires at least: 8.0
 * Author: Rank Ray
 * Author URI: https://rankray.com
 * Text Domain: justccell
 * Domain Path: /languages
 *
 * @package Justccell_Features
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('JUSTCCELL_FEATURES_VERSION', '1.1.44');
define('JUSTCCELL_FEATURES_FILE', __FILE__);
define('JUSTCCELL_FEATURES_DIR', plugin_dir_path(__FILE__));
define('JUSTCCELL_FEATURES_URL', plugin_dir_url(__FILE__));
define('JUSTCCELL_FEATURES_BASENAME', plugin_basename(__FILE__));

require_once JUSTCCELL_FEATURES_DIR . 'includes/acfml-safety.php';

add_action('plugins_loaded', 'justccell_features_bootstrap_theme_constants', 0);

/**
 * Ensure theme path constants exist before modules load.
 */
function justccell_features_bootstrap_theme_constants(): void
{
    if (!defined('JUSTCCELL_DIR')) {
        define('JUSTCCELL_DIR', get_template_directory());
    }
    if (!defined('JUSTCCELL_URI')) {
        define('JUSTCCELL_URI', get_template_directory_uri());
    }
    if (!defined('JUSTCCELL_VERSION')) {
        $theme = wp_get_theme(get_template());
        $ver   = $theme->get('Version');
        define(
            'JUSTCCELL_VERSION',
            is_string($ver) && $ver !== '' ? $ver : JUSTCCELL_FEATURES_VERSION
        );
    }
    if (!defined('JUSTCCELL_DEVELOPER')) {
        define('JUSTCCELL_DEVELOPER', 'Rank Ray');
    }
    if (!defined('JUSTCCELL_DEVELOPER_URL')) {
        define('JUSTCCELL_DEVELOPER_URL', 'https://rankray.com');
    }
}

/**
 * WooCommerce is available (Quick Stock needs CRUD, not a specific theme).
 */
function justccell_features_woocommerce_active(): bool
{
    return class_exists('WooCommerce', false) || defined('WC_PLUGIN_FILE') || function_exists('wc_get_product');
}

/**
 * Products-list Quick Stock — works on any theme as long as WooCommerce is active.
 *
 * @return list<string>
 */
function justccell_features_portable_woo_admin_modules(): array
{
    return [
        'class-jc-quick-stock.php',
        'admin-stock-quick-edit.php',
    ];
}

/**
 * Load portable Woo admin modules (idempotent via require_once).
 */
function justccell_features_load_portable_woo_admin(): void
{
    if (!justccell_features_woocommerce_active()) {
        return;
    }

    $dir = JUSTCCELL_FEATURES_DIR . 'includes/';
    foreach (justccell_features_portable_woo_admin_modules() as $file) {
        $path = $dir . $file;
        if (is_readable($path)) {
            require_once $path;
        }
    }
}

/**
 * Load all feature modules (mirrors legacy theme functions.php boot order).
 */
function justccell_features_load_modules(): void
{
    if (defined('JUSTCCELL_FEATURES_LOADED')) {
        return;
    }

    justccell_features_bootstrap_theme_constants();

    $theme_ok = ((string) get_template() === 'justccell-theme');

    if (!$theme_ok) {
        justccell_features_load_portable_woo_admin();
        define('JUSTCCELL_FEATURES_LOADED', true);
        define('JUSTCCELL_FEATURES_PORTABLE_ONLY', true);

        return;
    }

    $dir = JUSTCCELL_FEATURES_DIR . 'includes/';

    $modules = [
        'environment.php',
        'setup.php',
        'storefront.php',
        'tiered-pricing.php',
        'commerce.php',
        'commerce-pages.php',
        'checkout-modernization.php',
        'cart-ajax.php',
        'laser-engraving.php',
        'admin-laser-zone.php',
        'forms-settings.php',
        'wpml-lock.php',
        'cms-helpers.php',
        'breadcrumbs.php',
        'page-layouts.php',
        'catalog.php',
        'listing.php',
        'product-pages.php',
        'catalog-redirects.php',
        'static-pages.php',
        'coming-soon-page.php',
        'rest-privacy.php',
        'copy-policy.php',
        'bio-heating.php',
        'contact-page.php',
        'locations-page.php',
        'cms-content.php',
        'admin-menu.php',
        'class-jc-quick-stock.php',
        'admin-stock-quick-edit.php',
        'nav-fallback.php',
        'assets.php',
        'acf.php',
        'acf-catalog-pages.php',
        'acf-remaining-pages.php',
        'acf-fields.php',
        'cms-import.php',
        'woocommerce.php',
        'elite-cross-sell.php',
        'inquiry.php',
        'leads-admin.php',
        'chrome.php',
        'age-gate.php',
        'header-menu.php',
        'footer-menus.php',
        'blog.php',
    ];

    if (is_readable($dir . 'acf-page-groups.php')) {
        $modules[] = 'acf-page-groups.php';
    }

    foreach ($modules as $file) {
        $path = $dir . $file;
        if (is_readable($path)) {
            require_once $path;
        }
    }

    define('JUSTCCELL_FEATURES_LOADED', true);
}

add_action('after_setup_theme', 'justccell_features_load_modules', 1);

add_action('admin_notices', static function (): void {
    if (!current_user_can('activate_plugins')) {
        return;
    }
    if (!function_exists('is_plugin_active') && is_readable(ABSPATH . 'wp-admin/includes/plugin.php')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    if (function_exists('is_plugin_active') && is_plugin_active('jc-acfml-safety/jc-acfml-safety.php')) {
        echo '<div class="notice notice-warning"><p>';
        echo esc_html__(
            'justCCELL Features now includes the ACFML safety net. You can deactivate the legacy Justccell ACFML Safety Net plugin.',
            'justccell'
        );
        echo '</p></div>';
    }
    if ((string) get_template() === 'justccell-theme') {
        return;
    }
    if (defined('JUSTCCELL_FEATURES_PORTABLE_ONLY') && JUSTCCELL_FEATURES_PORTABLE_ONLY) {
        return;
    }
    if (!function_exists('is_plugin_active') && is_readable(ABSPATH . 'wp-admin/includes/plugin.php')) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }
    if (!function_exists('is_plugin_active') || !is_plugin_active(JUSTCCELL_FEATURES_BASENAME)) {
        return;
    }
    echo '<div class="notice notice-warning"><p>';
    echo esc_html__(
        'justCCELL Features is active but the Justccell theme is not. Activate wp-content/themes/justccell-theme/ for full storefront support.',
        'justccell'
    );
    echo '</p></div>';
});

register_activation_hook(__FILE__, static function (): void {
    if (!function_exists('flush_rewrite_rules')) {
        return;
    }
    flush_rewrite_rules();
});

register_deactivation_hook(__FILE__, static function (): void {
    if (!function_exists('flush_rewrite_rules')) {
        return;
    }
    flush_rewrite_rules();
});
