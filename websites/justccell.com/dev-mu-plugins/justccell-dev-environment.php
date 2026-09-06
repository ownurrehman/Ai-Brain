<?php
/**
 * Staging/dev cache bypass — deploy to dev.justccell.com ONLY:
 * public_html/dev/wp-content/mu-plugins/justccell-dev-environment.php
 *
 * Do not upload to production wp-content/mu-plugins/.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Runs before plugins; uses host + wp-config constants only.
 */
function justccell_dev_environment_active(): bool
{
    static $active = null;
    if ($active !== null) {
        return $active;
    }

    if (defined('JUSTCCELL_ENV') && in_array(JUSTCCELL_ENV, ['dev', 'staging'], true)) {
        $active = true;
        return $active;
    }

    if (defined('WP_ENVIRONMENT_TYPE') && WP_ENVIRONMENT_TYPE === 'staging') {
        $active = true;
        return $active;
    }

    $host = strtolower((string) ($_SERVER['HTTP_HOST'] ?? ''));
    $active = $host === 'dev.justccell.com' || str_starts_with($host, 'dev.');
    return $active;
}

if (!justccell_dev_environment_active()) {
    return;
}

if (!defined('LSCACHE_NO_CACHE')) {
    define('LSCACHE_NO_CACHE', true);
}

if (!defined('DONOTCACHEPAGE')) {
    define('DONOTCACHEPAGE', true);
}

add_filter('litespeed_can_cache', static fn (): bool => false, PHP_INT_MAX);
add_filter('litespeed_can_optm', static fn (): bool => false, PHP_INT_MAX);

add_action('send_headers', static function (): void {
    if (headers_sent()) {
        return;
    }
    header('X-LiteSpeed-Cache-Control: no-cache', true);
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0', true);
    header('Pragma: no-cache', true);
    header('X-Justccell-Environment: staging', true);
}, 0);
