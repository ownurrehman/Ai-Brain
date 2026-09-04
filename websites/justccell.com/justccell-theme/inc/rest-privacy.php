<?php
/**
 * REST API privacy while the site is in pre-launch / coming-soon mode.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * True when anonymous visitors should not read catalog/product REST payloads.
 */
function justccell_rest_prelaunch_gated(): bool
{
    if (is_user_logged_in() && current_user_can('read')) {
        return false;
    }

    if (defined('WP_CLI') && WP_CLI) {
        return false;
    }

    if (function_exists('wp_doing_cron') && wp_doing_cron()) {
        return false;
    }

    $opts = get_option('signals_csmm_options');
    if (is_array($opts) && !empty($opts['status'])) {
        return true;
    }

    if (get_option('csmm_status') === '1') {
        return true;
    }

    if (get_option('woocommerce_coming_soon') === 'yes') {
        return true;
    }

    return (bool) apply_filters('justccell_rest_prelaunch_gated', false);
}

/**
 * @return list<string>
 */
function justccell_rest_blocked_route_prefixes(): array
{
    return [
        '/wp/v2/product',
        '/wp/v2/products',
        '/wc/v3/products',
        '/wc/store/v1/products',
    ];
}

function justccell_rest_route_is_blocked(string $route): bool
{
    $route = '/' . ltrim($route, '/');
    foreach (justccell_rest_blocked_route_prefixes() as $prefix) {
        if ($route === $prefix || str_starts_with($route, $prefix . '/')) {
            return true;
        }
    }
    return false;
}

add_filter('rest_endpoints', static function (array $endpoints): array {
    if (!justccell_rest_prelaunch_gated()) {
        return $endpoints;
    }

    foreach (array_keys($endpoints) as $route) {
        if (justccell_rest_route_is_blocked((string) $route)) {
            unset($endpoints[$route]);
        }
    }

    return $endpoints;
}, 20);

add_filter('rest_pre_dispatch', static function ($result, $server, $request) {
    unset($server);
    if (!justccell_rest_prelaunch_gated() || !($request instanceof WP_REST_Request)) {
        return $result;
    }

    if (!justccell_rest_route_is_blocked($request->get_route())) {
        return $result;
    }

    return new WP_Error(
        'justccell_rest_prelaunch',
        __('Catalog data is not public while the site is in coming-soon mode.', 'justccell'),
        ['status' => 401]
    );
}, 10, 3);
