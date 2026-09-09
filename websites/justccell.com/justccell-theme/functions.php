<?php
/**
 * Justccell theme bootstrap (presentation layer).
 *
 * Storefront logic, wp-admin hub, WooCommerce, and ACF field behaviour live in the
 * justCCELL Features plugin (Rank Ray). This theme provides templates, assets, and ACF JSON.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('JUSTCCELL_VERSION', '0.9.339');
define('JUSTCCELL_DIR', get_template_directory());
define('JUSTCCELL_URI', get_template_directory_uri());
define('JUSTCCELL_DEVELOPER', 'Rank Ray');
define('JUSTCCELL_DEVELOPER_URL', 'https://rankray.com');

add_action('admin_notices', static function (): void {
    if (defined('JUSTCCELL_FEATURES_LOADED')) {
        return;
    }
    if (!current_user_can('activate_plugins')) {
        return;
    }
    echo '<div class="notice notice-error"><p><strong>Justccell:</strong> ';
    echo esc_html__(
        'Activate the justCCELL Features plugin (Rank Ray). Without it, the storefront and Justccell admin menu will not load.',
        'justccell'
    );
    echo '</p></div>';
});
