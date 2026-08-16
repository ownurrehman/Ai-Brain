<?php
/**
 * Visual product clones at /{category}/{slug}/ (ccell.com URL shape).
 *
 * Store prefix is stripped before WordPress sees the path, so
 * /uk/all-in-ones/tank/ resolves here and home_url() puts /uk back.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Sequential 360 frame keys: tank-360/00.jpg …
 *
 * @return list<string>
 */
function justccell_product_spin_keys(string $folder, int $count): array
{
    $frames = [];
    for ($i = 0; $i < $count; $i++) {
        $frames[] = $folder . '/' . sprintf('%02d.jpg', $i);
    }
    return $frames;
}

require_once JUSTCCELL_DIR . '/inc/product-data.php';

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_product_pages(): array
{
    static $pages = null;
    if (is_array($pages)) {
        return $pages;
    }

    $pages = justccell_product_pages_data();
    if (isset($pages['tank']) && is_array($pages['tank'])) {
        $pages['tank']['spin'] = justccell_product_spin_keys('tank-360', 36);
        $pages['tank']['details'] = [
            'public_uploads_images_20240821_d7272eb683ff4177d1c87687fc02982e.png',
            'public_uploads_images_20240812_abb0f699e525e9234eaa48d19cc0a509.jpg',
            'public_uploads_images_20240812_0aff615625c41aa2abcaa8b850584e2c.jpg',
        ];
        $pages['tank']['features'] = [
            [
                'title' => 'Miniature Design, Extended Experience',
                'copy'  => 'Small enough to easily conceal in the palm of your hand, the Tank keeps consumption as discreet as possible. That said, the oil tank built within is capable of 1ml/2ml/3ml oil capacities, ensuring lasting on-the-go experiences every time.',
                'note'  => '',
                'image' => 'public_uploads_images_20240506_e60d5915f1ab555868ebc36d50a41b6b.jpg',
            ],
            [
                'title' => 'Complete Oil Compatibility',
                'copy'  => 'Designed to be compatible with all cannabis oils (distillate, live resin, live rosin, liquid diamonds, etc.), the Tank brings out the best from your extracts and provides your customers with true-to-strain, clog-free, cloud-filled enjoyment.',
                'note'  => '',
                'image' => 'public_uploads_images_20240506_a498121b67ccdd5c826b3c74bbcb34c9.jpg',
            ],
            [
                'title' => 'No More Leaks or Loud Pockets',
                'copy'  => 'The Tank’s magic switch* takes safety, performance, and discretion to whole new levels. By sliding the switch to the “off” mode, you can not only prevent oil leakage and debris from entering the device, but also seal off scents emanating from the oil within.',
                'note'  => '*Optional aroma seal-off and anti-leakage switch.',
                'image' => 'public_uploads_images_20240506_91f67d4501c135393bf11dd9086d28c9.jpg',
            ],
            [
                'title' => '3 Reprogrammable Voltage Settings',
                'copy'  => 'The Tank gives your customers the option to choose between 3 preset voltage settings (2.4V, 2.8V, 3.2V) for truly customizable levels of flavor and vapor. You can also reprogram these voltages to 3 different selections from 9 available options ranging from 2.0V-3.6V via the Voltage Tuner* to match the device precisely to the oil you are filling it with.',
                'note'  => '*Voltage Tuner sold separately',
                'image' => 'public_uploads_images_20240506_8e00a1242167b6a9fee197a33134980b.jpg',
            ],
            [
                'title' => 'Hassle-Free Batch-Capping',
                'copy'  => 'Streamline your production process with a quick, hassle-free snap, and save valuable time and resources with our snap-fit mouthpiece design. Batch-capped at 15 pieces* at a time, this all-in-one device provides a tight, secure seal in a flash.',
                'note'  => '*Maximum batch-capping quantity may be updated in the future. Contact your sales rep. for the latest info.',
                'image' => 'public_uploads_images_20240506_0f9527478ab30057741eebe91ded759a.jpg',
            ],
        ];
    }

    return $pages;
}

function justccell_has_product_page(string $slug): bool
{
    return array_key_exists($slug, justccell_product_pages());
}

/**
 * @return array<string, mixed>|null
 */
function justccell_product_page(string $slug): ?array
{
    $pages = justccell_product_pages();
    $page  = $pages[$slug] ?? null;
    if (!is_array($page)) {
        return null;
    }
    $usable = static function (string $key): bool {
        if ($key === '') {
            return false;
        }
        if (justccell_media_id($key) > 0) {
            return true;
        }
        return function_exists('justccell_media_source_file') && justccell_media_source_file($key) !== '';
    };

    $item  = function_exists('justccell_catalog_item') ? justccell_catalog_item($slug) : null;
    $thumb = is_array($item) ? (string) ($item['image'] ?? '') : '';
    if ($thumb !== '' && !$usable($thumb)) {
        $thumb = '';
    }
    if (!$usable((string) ($page['banner'] ?? ''))) {
        $page['banner'] = $thumb;
    }
    $gallery = is_array($page['gallery'] ?? null) ? $page['gallery'] : [];
    $page['gallery'] = array_values(array_filter($gallery, $usable));
    if ($page['gallery'] === [] && $thumb !== '') {
        $page['gallery'] = [$thumb];
    }
    if (isset($page['features']) && is_array($page['features'])) {
        $page['features'] = array_values(array_filter(
            $page['features'],
            static fn ($feature): bool => is_array($feature) && $usable((string) ($feature['image'] ?? ''))
        ));
    }
    $details = is_array($page['details'] ?? null) ? $page['details'] : [];
    $page['details'] = array_values(array_filter($details, $usable));
    if (!$usable((string) ($page['evomax_bg'] ?? ''))) {
        $page['evomax_bg'] = '';
    }
    return $page;
}

function justccell_product_url(string $slug): string
{
    $page = justccell_product_page($slug);
    if ($page === null) {
        return justccell_inquiry_url($slug);
    }
    return home_url('/' . $page['category'] . '/' . $slug . '/');
}

function justccell_product_category_labels(): array
{
    return [
        'all-in-ones' => __('All-In-Ones', 'justccell'),
        'cartridge'   => __('Cartridges', 'justccell'),
        'pod-system'  => __('Pod Systems', 'justccell'),
        'battery'     => __('510 Batteries', 'justccell'),
    ];
}

function justccell_is_product_clone(): bool
{
    if ((string) get_query_var('justccell_product') !== '') {
        return true;
    }
    [, $slug] = justccell_match_product_path();
    return $slug !== '' && justccell_has_product_page($slug);
}

add_filter('query_vars', static function (array $vars): array {
    $vars[] = 'justccell_product';
    $vars[] = 'justccell_product_cat';
    $vars[] = 'justccell_listing';
    return $vars;
});

/**
 * Resolve /{category}/{slug}/ even if permalinks have not flushed.
 */
function justccell_match_product_path(?string $path = null): array
{
    $path = $path ?? justccell_request_path();
    if (function_exists('justccell_path_without_store')) {
        $path = justccell_path_without_store($path);
    }
    $cats = implode('|', array_map('preg_quote', array_keys(justccell_product_category_labels())));
    if (preg_match('#^/(' . $cats . ')/([^/]+)/?$#', $path, $match) !== 1) {
        return ['', ''];
    }
    return [$match[1], $match[2]];
}

function justccell_match_listing_path(?string $path = null): string
{
    $path = $path ?? justccell_request_path();
    if (function_exists('justccell_path_without_store')) {
        $path = justccell_path_without_store($path);
    }
    $cats = implode('|', array_map('preg_quote', array_keys(justccell_product_category_labels())));
    if (preg_match('#^/(' . $cats . ')/?$#', $path, $match) !== 1) {
        return '';
    }
    return $match[1];
}

function justccell_is_catalog_clone(): bool
{
    $cat = (string) get_query_var('justccell_listing');
    return $cat !== '' && array_key_exists($cat, justccell_product_category_labels());
}

add_action('parse_request', static function (WP $wp): void {
    if (!empty($wp->query_vars['justccell_product'])) {
        return;
    }
    [$cat, $slug] = justccell_match_product_path();
    if ($slug === '' || !justccell_has_product_page($slug)) {
        return;
    }
    $page = justccell_product_page($slug);
    if (!is_array($page) || $page['category'] !== $cat) {
        return;
    }
    $wp->query_vars['justccell_product_cat'] = $cat;
    $wp->query_vars['justccell_product']     = $slug;
    unset($wp->query_vars['error'], $wp->query_vars['pagename'], $wp->query_vars['name'], $wp->query_vars['page']);
});

add_action('parse_request', static function (WP $wp): void {
    if (!empty($wp->query_vars['justccell_product']) || !empty($wp->query_vars['justccell_listing'])) {
        return;
    }
    $cat = justccell_match_listing_path();
    if ($cat === '') {
        return;
    }
    $wp->query_vars['justccell_listing'] = $cat;
    unset($wp->query_vars['error'], $wp->query_vars['pagename'], $wp->query_vars['name'], $wp->query_vars['page']);
});

add_action('init', static function (): void {
    $cats = implode('|', array_map('preg_quote', array_keys(justccell_product_category_labels())));
    add_rewrite_rule(
        '^(' . $cats . ')/([^/]+)/?$',
        'index.php?justccell_product_cat=$matches[1]&justccell_product=$matches[2]',
        'top'
    );
    add_rewrite_rule(
        '^(' . $cats . ')/?$',
        'index.php?justccell_listing=$matches[1]',
        'top'
    );

    if (get_option('justccell_rewrite_ver') !== JUSTCCELL_VERSION) {
        flush_rewrite_rules(false);
        update_option('justccell_rewrite_ver', JUSTCCELL_VERSION);
    }
}, 20);

add_filter('pre_handle_404', static function (bool $preempt, WP_Query $query): bool {
    unset($query);
    $slug = (string) get_query_var('justccell_product');
    $page = $slug !== '' ? justccell_product_page($slug) : null;
    $cat  = (string) get_query_var('justccell_product_cat');
    if (is_array($page) && ($cat === '' || $cat === $page['category'])) {
        return true;
    }
    $listing = (string) get_query_var('justccell_listing');
    if ($listing !== '' && array_key_exists($listing, justccell_product_category_labels())) {
        return true;
    }
    return $preempt;
}, 10, 2);

add_filter('redirect_canonical', static function ($redirect) {
    if ((string) get_query_var('justccell_product') !== '' || (string) get_query_var('justccell_listing') !== '') {
        return false;
    }
    return $redirect;
}, 5);

add_filter('template_include', static function (string $template): string {
    $listing = (string) get_query_var('justccell_listing');
    if ($listing !== '' && array_key_exists($listing, justccell_product_category_labels())) {
        status_header(200);
        return JUSTCCELL_DIR . '/catalog-clone.php';
    }
    $slug = (string) get_query_var('justccell_product');
    $page = $slug !== '' ? justccell_product_page($slug) : null;
    $cat  = (string) get_query_var('justccell_product_cat');
    if (!is_array($page) || ($cat !== '' && $cat !== $page['category'])) {
        return $template;
    }
    status_header(200);
    return JUSTCCELL_DIR . '/product-clone.php';
});

add_filter('document_title_parts', static function (array $parts): array {
    $slug = (string) get_query_var('justccell_product');
    $page = $slug !== '' ? justccell_product_page($slug) : null;
    if (is_array($page)) {
        $parts['title'] = $page['name'] . ' — ' . $page['subtitle'];
        return $parts;
    }
    $listing = (string) get_query_var('justccell_listing');
    $labels  = justccell_product_category_labels();
    if ($listing !== '' && isset($labels[$listing])) {
        $parts['title'] = $labels[$listing];
    }
    return $parts;
});

add_filter('body_class', static function (array $classes): array {
    if ((string) get_query_var('justccell_product') !== '') {
        $classes[] = 'is-product-clone';
    }
    if ((string) get_query_var('justccell_listing') !== '') {
        $classes[] = 'is-catalog-clone';
    }
    return $classes;
});

add_action('template_redirect', static function (): void {
    if (!function_exists('is_product_category') || !is_product_category()) {
        return;
    }
    $term = get_queried_object();
    if (!$term instanceof WP_Term) {
        return;
    }
    if (!array_key_exists($term->slug, justccell_product_category_labels())) {
        return;
    }
    wp_safe_redirect(justccell_category_url($term->slug), 301);
    exit;
});
