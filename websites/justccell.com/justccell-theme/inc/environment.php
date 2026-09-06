<?php
/**
 * Environment detection — production vs dev.justccell.com staging.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_is_dev_environment(): bool
{
    static $is_dev = null;
    if ($is_dev !== null) {
        return $is_dev;
    }

    if (defined('JUSTCCELL_ENV') && in_array(JUSTCCELL_ENV, ['dev', 'staging'], true)) {
        $is_dev = true;
        return $is_dev;
    }

    if (function_exists('wp_get_environment_type') && wp_get_environment_type() === 'staging') {
        $is_dev = true;
        return $is_dev;
    }

    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $is_dev = $host === 'dev.justccell.com' || str_starts_with($host, 'dev.');
    return $is_dev;
}

function justccell_is_production_environment(): bool
{
    return !justccell_is_dev_environment();
}

function justccell_apply_dev_cache_bypass(): void
{
    if (!justccell_is_dev_environment()) {
        return;
    }

    add_filter('litespeed_can_cache', static fn (): bool => false, PHP_INT_MAX);
    add_filter('litespeed_can_optm', static fn (): bool => false, PHP_INT_MAX);
    add_filter('litespeed_vary_curr_cookies', static fn (array $cookies): array => $cookies, PHP_INT_MAX);

    add_action('send_headers', static function (): void {
        if (headers_sent()) {
            return;
        }
        header('X-LiteSpeed-Cache-Control: no-cache', true);
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0', true);
        header('Pragma: no-cache', true);
        header('X-Justccell-Environment: staging', true);
    }, 0);
}

add_action('plugins_loaded', 'justccell_apply_dev_cache_bypass', 0);

add_action('admin_notices', static function (): void {
    if (!justccell_is_dev_environment() || !current_user_can('manage_options')) {
        return;
    }
    echo '<div class="notice notice-info"><p>';
    echo esc_html__(
        'Staging (dev.justccell.com): LiteSpeed and page caching are disabled so you see changes immediately. Production keeps full caching.',
        'justccell'
    );
    echo '</p></div>';
});
