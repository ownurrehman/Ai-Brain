<?php
/**
 * Visual product pages at /{category}/{slug}/.
 *
 * Store prefix is stripped before WordPress sees the path, so
 * /all-in-ones/tank/ resolves here. Spain/Switzerland keep /es/ and /ch/; UK is the bare domain.
 *
 * Developed by Rank Ray — https://rankray.com
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

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_product_pages(): array
{
    static $pages = null;
    if (is_array($pages)) {
        return $pages;
    }

    if (!function_exists('justccell_product_pages_data')) {
        require_once JUSTCCELL_DIR . '/inc/product-data.php';
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
    if ($slug === '') {
        return false;
    }
    return function_exists('justccell_woo_product_id_by_slug') && justccell_woo_product_id_by_slug($slug) > 0;
}

/**
 * True when /{category}/{slug}/ should load product-clone (Woo product exists and category matches).
 */
function justccell_catalog_product_resolves(string $slug, string $cat): bool
{
    if ($slug === '' || $cat === '' || !array_key_exists($cat, justccell_product_category_labels())) {
        return false;
    }
    if (!function_exists('justccell_woo_product_id_by_slug') || justccell_woo_product_id_by_slug($slug) < 1) {
        return false;
    }
    $page = justccell_product_page($slug);
    return is_array($page) && ($page['category'] ?? '') === $cat;
}

/**
 * Woo/ACF product rows can exist without a clone banner attachment.
 * Fill missing media from the PHP seed so heroes are not a black box.
 *
 * @param array<string, mixed> $from_woo
 * @return array<string, mixed>
 */
function justccell_merge_product_seed(string $slug, array $from_woo): array
{
    $seed = justccell_product_pages()[$slug] ?? null;
    if (!is_array($seed)) {
        return $from_woo;
    }

    $usable_id = static function (int $id): bool {
        if ($id < 1 || !function_exists('wp_get_attachment_url')) {
            return false;
        }
        $url = wp_get_attachment_url($id);
        return is_string($url) && $url !== '';
    };

    if (!$usable_id((int) ($from_woo['banner_id'] ?? 0))) {
        $from_woo['banner_id'] = 0;
        $from_woo['banner'] = (string) ($seed['banner'] ?? '');
    }

    $woo_gallery  = is_array($from_woo['gallery_ids'] ?? null) ? $from_woo['gallery_ids'] : [];
    $seed_gallery = is_array($seed['gallery'] ?? null) ? $seed['gallery'] : [];
    if ($woo_gallery === [] && $seed_gallery !== []) {
        $from_woo['gallery'] = $seed_gallery;
    }

    if (($from_woo['spin_ids'] ?? []) === [] && ($from_woo['spin'] ?? []) === []) {
        $from_woo['spin'] = is_array($seed['spin'] ?? null) ? $seed['spin'] : [];
    }

    $seed_details = is_array($seed['details'] ?? null) ? $seed['details'] : [];
    $woo_details  = is_array($from_woo['details_ids'] ?? null) ? $from_woo['details_ids'] : [];
    if (count($woo_details) < count($seed_details)) {
        $from_woo['details_ids'] = [];
        $from_woo['details']     = $seed_details;
    }

    $seed_copy = trim((string) ($seed['evomax_copy'] ?? ''));
    $woo_copy  = trim((string) ($from_woo['evomax_copy'] ?? ''));
    if ($woo_copy === '') {
        $from_woo['evomax_copy'] = $seed_copy;
    }
    if ($seed_copy === '' && $woo_copy === '') {
        $from_woo['evomax_bg_id'] = 0;
        $from_woo['evomax_bg']    = '';
        $from_woo['evomax_title'] = '';
    } elseif (!$usable_id((int) ($from_woo['evomax_bg_id'] ?? 0))) {
        $from_woo['evomax_bg_id'] = 0;
        $from_woo['evomax_bg']    = (string) ($seed['evomax_bg'] ?? '');
    }

    $woo_features  = is_array($from_woo['features'] ?? null) ? $from_woo['features'] : [];
    $seed_features = is_array($seed['features'] ?? null) ? $seed['features'] : [];
    if (count($woo_features) < count($seed_features)) {
        $from_woo['features'] = $seed_features;
    } else {
        foreach ($woo_features as $i => $feature) {
            if (!is_array($feature)) {
                continue;
            }
            if (!$usable_id((int) ($feature['image_id'] ?? 0))) {
                $from_woo['features'][$i]['image_id'] = 0;
                $from_woo['features'][$i]['image'] = (string) ($seed_features[$i]['image'] ?? '');
            }
        }
    }

    return $from_woo;
}

/**
 * @return array<string, mixed>|null
 */
function justccell_product_page(string $slug): ?array
{
    if (!function_exists('justccell_product_page_from_woo')) {
        return null;
    }
    $from_woo = justccell_product_page_from_woo($slug);
    return is_array($from_woo) ? $from_woo : null;
}

function justccell_product_url(string $slug): string
{
    if ($slug === '') {
        return home_url('/');
    }

    if (function_exists('justccell_catalog_item')) {
        $item = justccell_catalog_item($slug);
        if (is_array($item) && ($item['category'] ?? '') !== '') {
            return home_url('/' . trim((string) $item['category'], '/') . '/' . trim($slug, '/') . '/');
        }
    }

    if (function_exists('justccell_woo_product_id_by_slug')) {
        $pid = justccell_woo_product_id_by_slug($slug);
        if ($pid > 0) {
            $cats = wp_get_post_terms($pid, 'product_cat', ['fields' => 'slugs']);
            if (is_array($cats)) {
                foreach ($cats as $cslug) {
                    if (array_key_exists($cslug, justccell_product_category_labels())) {
                        return home_url('/' . $cslug . '/' . trim($slug, '/') . '/');
                    }
                }
            }
        }
    }

    return justccell_inquiry_url($slug);
}

function justccell_product_category_labels(): array
{
    return [
        'all-in-ones' => __('All-In-Ones', 'justccell'),
        'cartridge'   => __('Cartridges', 'justccell'),
        'pod-system'  => __('Pod Systems', 'justccell'),
        'battery'     => __('510 Batteries', 'justccell'),
        'equipment'   => __('Equipment', 'justccell'),
    ];
}

/**
 * Storefront category tabs (homepage / listings) — excludes Equipment.
 *
 * @return array<string, string>
 */
function justccell_storefront_category_labels(): array
{
    $labels = justccell_product_category_labels();
    unset($labels['equipment']);
    return $labels;
}

function justccell_is_product_clone(): bool
{
    if ((string) get_query_var('justccell_product') !== '') {
        return true;
    }
    if (function_exists('is_product') && is_product()) {
        return true;
    }
    [, $slug] = justccell_match_product_path();
    return $slug !== '' && justccell_has_product_page($slug);
}

function justccell_current_product_slug(): string
{
    $slug = (string) get_query_var('justccell_product');
    if ($slug !== '') {
        return $slug;
    }
    if (function_exists('is_product') && is_product()) {
        $id = (int) get_queried_object_id();
        if ($id > 0) {
            return (string) get_post_field('post_name', $id);
        }
    }
    [, $path_slug] = justccell_match_product_path();
    return $path_slug;
}

/**
 * Catalog /{category}/{slug}/ should query the Woo product so Edit Product and ACF attach natively.
 */
function justccell_bind_woo_to_catalog_query(array $qv, string $slug): array
{
    if ($slug === '' || !function_exists('justccell_woo_product_id_by_slug')) {
        return $qv;
    }
    $id = justccell_woo_product_id_by_slug($slug);
    if ($id < 1) {
        $qv['error'] = '404';
        unset($qv['pagename'], $qv['name'], $qv['page_id'], $qv['page'], $qv['p']);
        return $qv;
    }
    $qv['post_type'] = 'product';
    $qv['p']         = $id;
    unset($qv['name'], $qv['pagename'], $qv['error']);
    return $qv;
}

/**
 * Catalog /{category}/ must query the listing Page so Rank Math / Yoast
 * attach to that object — not Discover (page_for_posts) via an empty home query.
 *
 * @param array<string, mixed> $qv
 * @return array<string, mixed>
 */
function justccell_bind_listing_to_page_query(array $qv, string $cat): array
{
    $qv['justccell_listing'] = $cat;
    unset(
        $qv['error'],
        $qv['pagename'],
        $qv['name'],
        $qv['page'],
        $qv['product_cat'],
        $qv['product_tag'],
        $qv['taxonomy'],
        $qv['term'],
        $qv['category_name']
    );
    $id = function_exists('justccell_listing_page_id') ? justccell_listing_page_id($cat) : 0;
    if ($id > 0) {
        $qv['page_id']   = $id;
        $qv['post_type'] = 'page';
    }
    return $qv;
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
    $wp->query_vars = justccell_bind_woo_to_catalog_query($wp->query_vars, $slug);
    unset($wp->query_vars['error'], $wp->query_vars['pagename'], $wp->query_vars['name'], $wp->query_vars['page']);
    if (!empty($wp->query_vars['p'])) {
        unset($wp->query_vars['name']);
    }
});

add_action('parse_request', static function (WP $wp): void {
    if (!empty($wp->query_vars['justccell_product'])) {
        return;
    }
    $cat = (string) ($wp->query_vars['justccell_listing'] ?? '');
    if ($cat === '') {
        $cat = justccell_match_listing_path();
    }
    if ($cat === '' || !array_key_exists($cat, justccell_product_category_labels())) {
        return;
    }
    $wp->query_vars = justccell_bind_listing_to_page_query($wp->query_vars, $cat);
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

    if (!wp_doing_ajax() && get_option('justccell_rewrite_ver') !== JUSTCCELL_VERSION) {
        flush_rewrite_rules(false);
        update_option('justccell_rewrite_ver', JUSTCCELL_VERSION);
    }
}, 20);

add_filter('pre_handle_404', static function (bool $preempt, WP_Query $query): bool {
    unset($query);
    $slug = (string) get_query_var('justccell_product');
    $cat  = (string) get_query_var('justccell_product_cat');
    if ($slug !== '' && $cat !== '') {
        return justccell_catalog_product_resolves($slug, $cat);
    }
    $listing = (string) get_query_var('justccell_listing');
    if ($listing !== '' && array_key_exists($listing, justccell_product_category_labels())) {
        return true;
    }
    return $preempt;
}, 10, 2);

add_filter('request', static function (array $qv): array {
    $slug = (string) ($qv['justccell_product'] ?? '');
    if ($slug !== '') {
        $cat = (string) ($qv['justccell_product_cat'] ?? '');
        if (!justccell_catalog_product_resolves($slug, $cat)) {
            $qv['error'] = '404';
            unset($qv['pagename'], $qv['name'], $qv['page_id'], $qv['page'], $qv['p']);
            return $qv;
        }
        return justccell_bind_woo_to_catalog_query($qv, $slug);
    }
    $cat = (string) ($qv['justccell_listing'] ?? '');
    if ($cat === '') {
        $cat = justccell_match_listing_path();
    }
    if ($cat !== '' && array_key_exists($cat, justccell_product_category_labels())) {
        return justccell_bind_listing_to_page_query($qv, $cat);
    }
    return $qv;
});

add_action('pre_get_posts', static function (WP_Query $query): void {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    $cat = (string) $query->get('justccell_listing');
    if ($cat === '' || !array_key_exists($cat, justccell_product_category_labels())) {
        return;
    }
    $id = function_exists('justccell_listing_page_id') ? justccell_listing_page_id($cat) : 0;
    if ($id < 1) {
        return;
    }
    $query->set('page_id', $id);
    $query->set('post_type', 'page');
    $query->set('justccell_listing', $cat);
    $query->is_home              = false;
    $query->is_posts_page        = false;
    $query->is_page              = true;
    $query->is_singular          = true;
    $query->is_archive           = false;
    $query->is_category          = false;
    $query->is_tax               = false;
    $query->is_post_type_archive = false;
}, 0);

add_action('pre_get_posts', static function (WP_Query $query): void {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    $slug = (string) $query->get('justccell_product');
    $cat  = (string) $query->get('justccell_product_cat');
    if ($slug === '' || $cat === '') {
        return;
    }
    if (justccell_catalog_product_resolves($slug, $cat)) {
        return;
    }
    $query->set_404();
    $query->is_home              = false;
    $query->is_posts_page        = false;
    $query->is_page              = false;
    $query->is_singular          = false;
    $query->is_archive           = false;
    $query->is_category          = false;
    $query->is_tax               = false;
    $query->is_post_type_archive = false;
}, 0);

add_action('template_redirect', static function (): void {
    if (is_preview()) {
        return;
    }
    $slug = (string) get_query_var('justccell_product');
    $cat  = (string) get_query_var('justccell_product_cat');
    if ($slug === '' || $cat === '' || justccell_catalog_product_resolves($slug, $cat)) {
        return;
    }
    global $wp_query;
    if ($wp_query instanceof WP_Query) {
        $wp_query->set_404();
    }
    status_header(404);
}, 0);

add_action('template_redirect', static function (): void {
    if (is_preview()) {
        return;
    }
    $path = justccell_request_path();
    if (function_exists('justccell_path_without_store')) {
        $path = justccell_path_without_store($path);
    }
    if (preg_match('#^/product/([^/]+)/?$#', $path, $match) !== 1) {
        return;
    }
    $slug = $match[1];
    $page = justccell_product_page($slug);
    if (!is_array($page)) {
        return;
    }
    wp_safe_redirect(justccell_product_url($slug), 301);
    exit;
}, 1);

add_filter('redirect_canonical', static function ($redirect) {
    if ((string) get_query_var('justccell_product') !== '' || (string) get_query_var('justccell_listing') !== '') {
        return false;
    }
    return $redirect;
}, 5);

/**
 * Product/listing pages are virtual routes (custom query vars, is_singular=false), so Rank Math
 * cannot derive a canonical from a queried object and emits none. Feed it the pretty self URL —
 * same integration pattern the theme uses for rank_math/frontend/title + description.
 */
function justccell_rank_math_view_canonical($canonical)
{
    $listing = (string) get_query_var('justccell_listing');
    if (
        $listing !== ''
        && function_exists('justccell_product_category_labels')
        && array_key_exists($listing, justccell_product_category_labels())
        && function_exists('justccell_category_url')
    ) {
        return justccell_category_url($listing);
    }

    $slug = function_exists('justccell_current_product_slug') ? justccell_current_product_slug() : '';
    if ($slug !== '' && function_exists('justccell_product_url')) {
        $cat = (string) get_query_var('justccell_product_cat');
        if ($cat === '' || justccell_catalog_product_resolves($slug, $cat)) {
            return justccell_product_url($slug);
        }
    }

    return $canonical;
}
add_filter('rank_math/frontend/canonical', 'justccell_rank_math_view_canonical', 20);
add_filter('wpseo_canonical', 'justccell_rank_math_view_canonical', 20);

add_filter('template_include', static function (string $template): string {
    $listing = (string) get_query_var('justccell_listing');
    if ($listing !== '' && array_key_exists($listing, justccell_product_category_labels())) {
        status_header(200);
        return JUSTCCELL_DIR . '/catalog-clone.php';
    }
    $slug = justccell_current_product_slug();
    if ($slug === '') {
        return $template;
    }
    $cat = (string) get_query_var('justccell_product_cat');
    if (!justccell_catalog_product_resolves($slug, $cat)) {
        return $template;
    }
    set_query_var('justccell_product', $slug);
    status_header(200);
    return JUSTCCELL_DIR . '/product-clone.php';
}, 20);

add_filter('document_title_parts', static function (array $parts): array {
    $slug = (string) get_query_var('justccell_product');
    $page = $slug !== '' ? justccell_product_page($slug) : null;
    if (is_array($page)) {
        $sub = trim((string) ($page['subtitle'] ?? ''));
        $parts['title'] = $sub !== '' ? $page['name'] . ' — ' . $sub : (string) $page['name'];
        return $parts;
    }
    $listing = (string) get_query_var('justccell_listing');
    $labels  = justccell_product_category_labels();
    if ($listing !== '' && isset($labels[$listing])) {
        $obj = get_queried_object();
        if ($obj instanceof WP_Post && $obj->post_type === 'page' && $obj->post_title !== '') {
            return $parts;
        }
        $parts['title'] = $labels[$listing];
    }
    return $parts;
});

add_filter('body_class', static function (array $classes): array {
    $slug = (string) get_query_var('justccell_product');
    $cat  = (string) get_query_var('justccell_product_cat');
    if ($slug !== '' && $cat !== '' && justccell_catalog_product_resolves($slug, $cat)) {
        $classes[] = 'is-product-clone';
    }
    if ((string) get_query_var('justccell_listing') !== '') {
        $classes[] = 'is-catalog-clone';
    } elseif (is_page() && function_exists('justccell_is_catalog_view') && justccell_is_catalog_view()) {
        $classes[] = 'is-catalog-clone';
    }
    return $classes;
});

add_action('admin_bar_menu', static function (WP_Admin_Bar $bar): void {
    if (!is_admin_bar_showing()) {
        return;
    }
    $listing = (string) get_query_var('justccell_listing');
    if ($listing !== '' && function_exists('justccell_listing_page_id')) {
        $page_id = justccell_listing_page_id($listing);
        if ($page_id > 0 && current_user_can('edit_post', $page_id)) {
            $href = get_edit_post_link($page_id, 'raw');
            if (is_string($href) && $href !== '') {
                $bar->add_node([
                    'id'    => 'edit',
                    'title' => __('Edit Page', 'justccell'),
                    'href'  => $href,
                ]);
            }
        }
        return;
    }
    $slug = justccell_current_product_slug();
    if ($slug === '') {
        return;
    }
    $page = justccell_product_page($slug);
    $id   = is_array($page) ? (int) ($page['woo_id'] ?? 0) : 0;
    if ($id < 1 && function_exists('justccell_woo_product_id_by_slug')) {
        $id = justccell_woo_product_id_by_slug($slug);
    }
    if ($id < 1 || get_post_type($id) !== 'product' || !current_user_can('edit_post', $id)) {
        return;
    }
    $href = get_edit_post_link($id, 'raw');
    if (!is_string($href) || $href === '') {
        return;
    }
    $bar->add_node([
        'id'    => 'edit',
        'title' => __('Edit Product', 'justccell'),
        'href'  => $href,
    ]);
}, 80);

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
