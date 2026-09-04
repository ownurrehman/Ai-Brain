<?php
/**
 * Canonical product / page 301 map (slug renames + legacy paths only).
 * All 57 published SKUs are permanent inventory — no catalog-cut trash redirects.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, string> Normalized request path (no trailing slash) => destination path (with trailing slash).
 */
function justccell_catalog_redirects(): array
{
    $map = [];

    $add = static function (string $from, string $to) use (&$map): void {
        $from = '/' . trim(strtolower($from), '/');
        $to   = '/' . trim($to, '/');
        if ($from === '' || $from === '/') {
            return;
        }
        if ($to !== '/') {
            $to .= '/';
        }
        $map[$from] = $to;
    };

    // Slug renames (old permalink -> new).
    foreach (
        [
            '/pod-system/eazie-pro-3-0' => '/pod-system/eazie-pro/',
            '/pod-system/eazie-pod-3-0' => '/pod-system/eazie-pod/',
            '/cartridge/th2-evomax'     => '/cartridge/th2-evo/',
            '/cartridge/m6t-evomax'     => '/cartridge/m6t-evo/',
            // Legacy bio slug → canonical Just CCELL 3.0 permalink (never reverse).
            '/ccell-3-0'               => '/justccell-3-0/',
            '/ccell-3.0'               => '/justccell-3-0/',
            '/justccell-3.0'           => '/justccell-3-0/',
        ] as $from => $to
    ) {
        $add($from, $to);
    }

    // Legacy category paths from the reference storefront (not live product slugs).
    $add('/disposable/blanc', '/all-in-ones/');
    $add('/disposable/slym', '/all-in-ones/');

    // Old public aliases that must not steal newer canonical product pages.
    $add('/all-in-ones/flex-pro', '/all-in-ones/');
    $add('/battery/m3', '/battery/');
    $add('/battery/m3b', '/battery/');
    $add('/battery/palm', '/battery/');
    $add('/pod-system/bellos-battery', '/pod-system/');
    $add('/pod-system/dart-battery', '/pod-system/');
    $add('/pod-system/dart-x-battery', '/pod-system/');
    $add('/cartridge/th2-se-2ml', '/cartridge/th2-evo/');
    $add('/locations', '/location/');

    return $map;
}

/** @deprecated 0.9.206 Use justccell_catalog_redirects(). */
function justccell_catalog_cut_redirects(): array
{
    return justccell_catalog_redirects();
}

add_action('template_redirect', static function (): void {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    $path = justccell_request_path();
    if (function_exists('justccell_path_without_store')) {
        $path = justccell_path_without_store($path);
    }
    $path = '/' . trim(strtolower($path), '/');
    if ($path === '/') {
        return;
    }
    $dest = justccell_catalog_redirects()[$path] ?? '';
    if ($dest === '') {
        return;
    }

    // Never 301 away from a live product at this exact permalink slug.
    $slug = basename($path);
    if ($slug !== '') {
        $live = get_posts([
            'name'           => $slug,
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 1,
            'fields'         => 'ids',
            'no_found_rows'  => true,
        ]);
        if ($live !== []) {
            return;
        }
    }

    wp_safe_redirect(home_url($dest), 301);
    exit;
}, 7);
