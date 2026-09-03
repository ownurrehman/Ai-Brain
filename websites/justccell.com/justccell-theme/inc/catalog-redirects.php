<?php
/**
 * Catalog-cut 301 map (2026-09-02). Hermes trashed 36 SKUs; Cursor owns these redirects.
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
function justccell_catalog_cut_redirects(): array
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
            '/justccell-3-0'           => '/ccell-3-0/',
        ] as $from => $to
    ) {
        $add($from, $to);
    }

    // Trashed SKU -> category or replacement page (never to a different live SKU).
    $trashed = [
        'bellos'              => '/all-in-ones/',
        'bellos-pod'          => '/all-in-ones/',
        'blanc'               => '/all-in-ones/',
        'ceramic-evomax'      => '/all-in-ones/',
        'dart'                => '/all-in-ones/',
        'dart-pod'            => '/all-in-ones/',
        'dart-x'              => '/all-in-ones/',
        'diama'               => '/cartridge/',
        'ds0103'              => '/all-in-ones/',
        'eazie-pod-only-3-0'  => '/pod-system/eazie-pro/',
        'fino'                => '/all-in-ones/',
        'flexcell'            => '/all-in-ones/',
        'flexcell-pro'        => '/all-in-ones/',
        'flexcell-x'          => '/all-in-ones/',
        'go-stik'             => '/all-in-ones/',
        'listo'               => '/all-in-ones/',
        'luster-pro'          => '/all-in-ones/',
        'luster-pro-pod'      => '/all-in-ones/',
        'm3-plus'             => '/battery/',
        'm3b-plus'            => '/battery/',
        'mini-tank'           => '/all-in-ones/',
        'mixjoy'              => '/all-in-ones/',
        'palm-pro'            => '/battery/',
        'rosin-bar'           => '/all-in-ones/',
        'sandwave'            => '/all-in-ones/',
        'skye-ii'             => '/all-in-ones/',
        'slym'                => '/all-in-ones/',
        'stylo'               => '/all-in-ones/',
        'tank'                => '/all-in-ones/',
        'th2-se'              => '/cartridge/th2-evo/',
        'm6t-se'              => '/cartridge/m6t-evo/',
        'vision-box'          => '/all-in-ones/',
        'vision-box-elite'    => '/all-in-ones/',
        'voca'                => '/all-in-ones/voca-pro-max/',
        'voca-max'            => '/all-in-ones/voca-pro-max/',
        'voca-pro'            => '/all-in-ones/voca-pro-max/',
    ];

    // Original public paths before trash (from clone catalogue).
    $origins = [
        'bellos'              => ['pod-system'],
        'bellos-pod'          => ['pod-system'],
        'blanc'               => ['all-in-ones'],
        'ceramic-evomax'      => ['cartridge'],
        'dart'                => ['pod-system'],
        'dart-pod'            => ['pod-system'],
        'dart-x'              => ['pod-system'],
        'diama'               => ['cartridge'],
        'ds0103'              => ['all-in-ones'],
        'eazie-pod-only-3-0'  => ['pod-system'],
        'fino'                => ['battery'],
        'flexcell'            => ['all-in-ones'],
        'flexcell-pro'        => ['all-in-ones'],
        'flexcell-x'          => ['all-in-ones'],
        'go-stik'             => ['battery'],
        'listo'               => ['all-in-ones'],
        'luster-pro'          => ['pod-system'],
        'luster-pro-pod'      => ['pod-system'],
        'm3-plus'             => ['battery'],
        'm3b-plus'            => ['battery'],
        'mini-tank'           => ['all-in-ones'],
        'mixjoy'              => ['all-in-ones'],
        'palm-pro'            => ['battery'],
        'rosin-bar'           => ['all-in-ones'],
        'sandwave'            => ['battery'],
        'skye-ii'             => ['all-in-ones'],
        'slym'                => ['all-in-ones'],
        'stylo'               => ['battery'],
        'tank'                => ['all-in-ones'],
        'th2-se'              => ['cartridge'],
        'm6t-se'              => ['cartridge'],
        'vision-box'          => ['all-in-ones'],
        'vision-box-elite'    => ['all-in-ones'],
        'voca'                => ['all-in-ones'],
        'voca-max'            => ['all-in-ones'],
        'voca-pro'            => ['all-in-ones'],
    ];

    foreach ($trashed as $slug => $dest) {
        foreach ($origins[$slug] ?? ['all-in-ones'] as $prefix) {
            $add('/' . $prefix . '/' . $slug, $dest);
        }
    }

    // Clone-era disposable paths.
    $add('/disposable/blanc', '/all-in-ones/');
    $add('/disposable/slym', '/all-in-ones/');

    // Old clone aliases that must not steal the new Flex / M4 / Palm SE pages.
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
    $dest = justccell_catalog_cut_redirects()[$path] ?? '';
    if ($dest === '') {
        return;
    }

    // Never 301 away from a live product at this exact permalink slug (e.g. Diama restored).
    // Match post_name only — SKU lookup would wrongly skip rename redirects (th2-evomax → th2-evo).
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

add_action('after_setup_theme', static function (): void {
    if (get_option('justccell_catalog_cut_2026') === '1') {
        return;
    }
    update_option('justccell_catalog_cut_2026', '1', false);
    delete_option('justccell_rewrite_ver');
}, 1);
