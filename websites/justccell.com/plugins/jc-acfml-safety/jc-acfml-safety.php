<?php
/**
 * Plugin Name: Justccell ACFML Safety Net (deprecated)
 * Description: DEPRECATED — merged into justCCELL Features 1.1.0. Deactivate this plugin; safety net now loads from wp-content/plugins/justccell-features/includes/acfml-safety.php.
 * Version: 1.0.1
 * Author: Rank Ray
 * Author URI: https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_notices', static function (): void {
    if (!current_user_can('activate_plugins')) {
        return;
    }
    echo '<div class="notice notice-warning"><p><strong>Justccell ACFML Safety Net</strong> — ';
    echo esc_html__('This plugin is deprecated. Deactivate it; justCCELL Features (Rank Ray) now includes the same guard.', 'justccell');
    echo '</p></div>';
});
