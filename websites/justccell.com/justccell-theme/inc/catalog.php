<?php
/**
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);
if (!defined('ABSPATH')) { exit; }

function justccell_media_id(string $file): int
{
    if ($file === '') {
        return 0;
    }
    $map = get_option('justccell_media_map', []);
    if (!is_array($map)) {
        $map = [];
    }
    $id = (int) ($map[$file] ?? 0);
    if ($id > 0 && get_post_type($id) === 'attachment') {
        return $id;
    }
    $found = get_posts([
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_justccell_ref',
        'meta_value'     => $file,
        'no_found_rows'  => true,
    ]);
    if ($found === []) {
        return 0;
    }
    $id = (int) $found[0];
    $map[$file] = $id;
    update_option('justccell_media_map', $map, false);
    return $id;
}

function justccell_ref_uri(string $file): string
{
    if ($file === '') {
        return '';
    }
    $id = justccell_media_id($file);
    if ($id > 0) {
        $url = wp_get_attachment_url($id);
        if (is_string($url) && $url !== '') {
            return $url;
        }
    }
    return '';
}

function justccell_ensure_media_url(string $file): string
{
    if ($file === '') {
        return '';
    }
    $id = justccell_media_id($file);
    if ($id > 0) {
        $url = wp_get_attachment_url($id);
        if (is_string($url) && $url !== '') {
            return $url;
        }
    }
    return '';
}

/**
 * @param list<string> $files
 */
function justccell_ensure_media_files(array $files): void
{
    foreach ($files as $file) {
        $file = (string) $file;
        if ($file !== '') {
            justccell_ensure_media_url($file);
        }
    }
}

/**
 * Lookup-only. Theme pack sideload was removed after manual Media Library uploads.
 */
function justccell_sideload_media_file(string $key, bool $generate_meta = true): int
{
    unset($generate_meta);
    return justccell_media_id($key);
}

function justccell_media_source_file(string $key): string
{
    return justccell_media_id($key) > 0 ? $key : '';
}

/**
 * @param array<string, string|int|bool> $attrs
 */
function justccell_media_img(string $file, array $attrs = []): string
{
    if ($file === '') {
        return '';
    }
    justccell_ensure_media_url($file);
    $id = justccell_media_id($file);
    if ($id > 0) {
        $size = isset($attrs['size']) ? (string) $attrs['size'] : 'full';
        unset($attrs['size']);
        $clean = [];
        foreach ($attrs as $name => $value) {
            if ($value === null || $value === false || $value === '') {
                continue;
            }
            $clean[$name] = $value;
        }
        $html = wp_get_attachment_image($id, $size, false, $clean);
        return is_string($html) ? $html : '';
    }
    return '';
}

function justccell_catalog(): array
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    $cache = function_exists('justccell_catalog_from_woo') ? justccell_catalog_from_woo() : [];
    if (!is_array($cache)) {
        $cache = [];
    }

    return $cache;
}

function justccell_catalog_by_category(): array {
    $out = ['all-in-ones' => [], 'cartridge' => [], 'pod-system' => [], 'battery' => [], 'equipment' => []];
    foreach (justccell_catalog() as $item) {
        $cat = (string) ($item['category'] ?? '');
        if (!isset($out[$cat])) {
            $out[$cat] = [];
        }
        $out[$cat][] = $item;
    }
    return $out;
}

/**
 * Homepage product rails — live published products per WooCommerce category.
 * Order follows Woo menu_order. Specs come from product clone_specs / catalog.
 *
 * @return array<string, list<array<string, mixed>>>
 */
function justccell_home_rails(): array
{
    $by_cat = justccell_catalog_by_category();
    $out    = [];
    $labels = function_exists('justccell_storefront_category_labels')
        ? justccell_storefront_category_labels()
        : [
            'all-in-ones' => '',
            'cartridge'   => '',
            'pod-system'  => '',
            'battery'     => '',
        ];

    foreach (array_keys($labels) as $cat) {
        $items = [];
        foreach ($by_cat[$cat] ?? [] as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (function_exists('justccell_catalog_card_specs')) {
                $item['specs'] = justccell_catalog_card_specs($item, 3);
            }
            $items[] = $item;
        }
        $out[$cat] = $items;
    }

    return $out;
}

/**
 * @deprecated Kept for older callers; homepage no longer uses curated slug lists.
 *
 * @return array<string, list<string>>
 */
function justccell_home_rail_slugs(): array
{
    $out = [];
    foreach (justccell_home_rails() as $cat => $items) {
        $out[$cat] = array_values(array_filter(array_map(
            static fn ($item): string => is_array($item) ? (string) ($item['slug'] ?? '') : '',
            $items
        )));
    }
    return $out;
}

/**
 * @deprecated Prefer product clone_specs via justccell_catalog_card_specs().
 *
 * @return array<string, list<string>>
 */
function justccell_home_card_blurbs(): array
{
    return [];
}

/**
 * Full specification lines for a catalog item (not the 3-line homepage slice).
 *
 * @param array<string, mixed> $item
 * @return list<string>
 */
function justccell_product_spec_lines(array $item): array
{
    $from_all = [];
    foreach (array_values((array) ($item['specs_all'] ?? [])) as $raw) {
        $line = trim((string) $raw);
        if ($line !== '') {
            $from_all[] = $line;
        }
    }
    if ($from_all !== []) {
        return $from_all;
    }

    $woo_id = (int) ($item['woo_id'] ?? 0);
    if ($woo_id > 0 && function_exists('get_field')) {
        $from_acf = [];
        foreach ((array) get_field('clone_specs', $woo_id) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $line = trim((string) ($row['line'] ?? ''));
            if ($line !== '') {
                $from_acf[] = $line;
            }
        }
        if ($from_acf !== []) {
            return $from_acf;
        }
    }

    $fallback = [];
    foreach (array_values((array) ($item['specs'] ?? [])) as $raw) {
        $line = trim((string) $raw);
        if ($line !== '') {
            $fallback[] = $line;
        }
    }
    return $fallback;
}

/**
 * Split a spec row into label + value. Accepts ASCII or fullwidth colon.
 *
 * @return array{label:string,value:string}
 */
function justccell_spec_line_parts(string $line): array
{
    $line = trim($line);
    if ($line === '') {
        return ['label' => '', 'value' => ''];
    }
    if (preg_match('/^([^:]{1,48})[:：]\s*(.+)$/u', $line, $m) === 1) {
        return [
            'label' => strtolower(trim((string) $m[1])),
            'value' => trim((string) $m[2]),
        ];
    }
    return ['label' => '', 'value' => $line];
}

function justccell_spec_line_is_tank_volume(string $line): bool
{
    $label = justccell_spec_line_parts($line)['label'];
    if ($label === '') {
        return false;
    }
    return (bool) preg_match('/^(tank\s*volume|tank\s*capacity|oil\s*(tank|capacity|volume)|volume)$/i', $label);
}

function justccell_spec_line_is_technical(string $line): bool
{
    if (justccell_spec_line_is_tank_volume($line)) {
        return true;
    }
    if (preg_match('/\bdimensions?\s*[:：]/iu', $line) || preg_match('/\bbattery\s*[:：]/iu', $line)) {
        return true;
    }
    $label = justccell_spec_line_parts($line)['label'];
    if ($label === '') {
        return false;
    }
    return (bool) preg_match(
        '/^(battery|dimensions?|resistance|weight|charging|voltage|material|coil|thread|preheat|heating|atomizer|input|output|power|current|wattage|capacity|size|housing|mouthpiece)/i',
        $label
    );
}

/**
 * Catalog / Explore cards: grey marketing line + cyan tank volume from Specs.
 * Product Tagline (clone_subtitle) is PDP H2 only — never used here.
 *
 * @param list<string> $specs
 * @return array{tagline:string,capacity:string}
 */
function justccell_catalog_card_copy_from_specs(array $specs): array
{
    $tagline  = '';
    $capacity = '';
    foreach ($specs as $raw) {
        $line = trim((string) $raw);
        if ($line === '') {
            continue;
        }
        if ($capacity === '' && justccell_spec_line_is_tank_volume($line)) {
            $capacity = justccell_spec_line_parts($line)['value'];
            continue;
        }
        if ($tagline === '' && !justccell_spec_line_is_technical($line)) {
            $tagline = $line;
        }
    }
    return [
        'tagline'  => $tagline,
        'capacity' => $capacity,
    ];
}

/**
 * Catalog listing grid: short marketing line + cyan capacity (not PDP dimensions).
 *
 * @return array{tagline:string,capacity:string}
 */
function justccell_listing_card_copy(string $slug): array
{
    $item = function_exists('justccell_catalog_item') ? justccell_catalog_item($slug) : null;
    if (!is_array($item)) {
        return ['tagline' => '', 'capacity' => ''];
    }
    return justccell_catalog_card_copy_from_specs(justccell_product_spec_lines($item));
}

/**
 * Category tiles + product rail for the 404 page. Uses live catalogue images.
 *
 * @return array{categories:list<array<string, mixed>>,products:list<array<string, mixed>>}
 */
function justccell_404_showcase(): array
{
    $labels = function_exists('justccell_storefront_category_labels')
        ? justccell_storefront_category_labels()
        : justccell_product_category_labels();
    $blurbs = [
        'all-in-ones' => __('All-oil disposables for distillate through live rosin.', 'justccell'),
        'cartridge'   => __('510 ceramic cartridges specified for filling lines.', 'justccell'),
        'pod-system'  => __('Closed pods — lighter than a 510 pen.', 'justccell'),
        'battery'     => __('510-thread batteries and boxes.', 'justccell'),
    ];
    $by_cat = justccell_catalog_by_category();

    $categories = [];
    $products   = [];
    $seen       = [];

    foreach ($labels as $slug => $label) {
        $items = array_values(array_filter(
            $by_cat[$slug] ?? [],
            static fn ($item): bool => is_array($item)
        ));
        $lead = null;
        foreach ($items as $item) {
            if ((int) ($item['image_id'] ?? 0) > 0 || (string) ($item['image'] ?? '') !== '') {
                $lead = $item;
                break;
            }
        }
        if ($lead === null && $items !== []) {
            $lead = $items[0];
        }
        $categories[] = [
            'slug'  => $slug,
            'label' => $label,
            'blurb' => $blurbs[$slug] ?? '',
            'url'   => justccell_category_url($slug),
            'item'  => is_array($lead) ? $lead : null,
        ];
        foreach (array_slice($items, 0, 3) as $item) {
            $key = (string) ($item['slug'] ?? '');
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $products[] = $item;
        }
    }

    return [
        'categories' => $categories,
        'products'   => $products,
    ];
}

/**
 * Catalog listing groups. Only slugs we actually sell.
 *
 * @return list<array{title:string,items:list<array<string, mixed>>}>
 */
function justccell_catalog_groups(string $category): array
{
    $items = justccell_catalog_by_category()[$category] ?? [];
    if ($items === []) {
        return [];
    }

    return [
        [
            'title' => '',
            'copy'  => '',
            'items' => $items,
        ],
    ];
}

/**
 * Explore More cards: one description line + a short cyan capacity.
 *
 * @param array{specs?:list<string>} $item
 * @return array{blurb:string,capacity:string}
 */
function justccell_catalog_explore_meta(array $item): array
{
    $copy = justccell_catalog_card_copy_from_specs(justccell_product_spec_lines($item));
    return [
        'blurb'    => $copy['tagline'],
        'capacity' => $copy['capacity'],
    ];
}

/**
 * @return array{name:string,slug:string,category:string,image:string,specs:list<string>}|null
 */
function justccell_catalog_item(string $slug): ?array
{
    foreach (justccell_catalog() as $item) {
        if ($item['slug'] === $slug) {
            return $item;
        }
    }
    return null;
}

/**
 * Print catalog/product card image from attachment id or media key.
 *
 * @param array<string, mixed> $attrs
 */
function justccell_echo_catalog_image(array $item, array $attrs = []): void
{
    $id = (int) ($item['image_id'] ?? 0);
    if ($id > 0) {
        echo wp_get_attachment_image($id, isset($attrs['size']) ? (string) $attrs['size'] : 'full', false, $attrs);
        return;
    }
    echo justccell_media_img((string) ($item['image'] ?? ''), $attrs);
}

/**
 * Product clone if it exists, otherwise the inquiry form with SKU.
 *
 * @param array{name:string,slug:string,category:string,image:string,specs:list<string>} $item
 */
function justccell_item_url(array $item): string
{
    if (function_exists('justccell_has_product_page') && justccell_has_product_page($item['slug'])) {
        return justccell_product_url($item['slug']);
    }
    return justccell_inquiry_url($item['slug']);
}

function justccell_category_url(string $slug): string
{
    return home_url('/' . trim($slug, '/') . '/');
}

/**
 * @return array<string, string|list<string>>
 */
function justccell_home_asset_keys(): array
{
    return [
        'arrow'    => 'public_static_modules_cms_img_home14.png',
        'quote_bg' => 'public_static_modules_cms_img_home18.jpg',
        'fill'     => 'public_uploads_images_20250225_08b6cc13898889e8407ea3790ae31cad.png',
        'trusted'  => 'public_uploads_images_20250225_2be1257b82984d06383bd05570e5a8be.png',
        'premium'  => 'public_uploads_images_20250225_e9e2853eb498b95706a72f332df0a1a1.png',
        'cust1'    => 'public_uploads_images_20250225_4520819030305c0ed4bde75255a8d6ad.png',
        'cust2'    => 'public_uploads_images_20250225_03e34b11c8bd28052b8a1a5d877ebbe9.png',
        'cust3'    => 'public_uploads_images_20250225_64e6456cdd881d697853f502f353d4a8.png',
        'cust4'    => 'public_uploads_images_20250225_6c0bc8408fa97536916c3f93d7b4cb21.png',
        'banners'  => [
            'public_uploads_images_20250926_6d26d199e7d5f7c457ad85f05c69f8e4.jpg',
            'public_uploads_images_20250409_47d88dbb6d565e229709aa76a51fc82f.jpg',
            'public_uploads_images_20250228_35607022bf9c0261440de779466b67df.jpg',
            'public_uploads_images_20250624_586896b2422c482af3eb027b9c112ad5.jpg',
        ],
        'banners_mobile' => [
            'justccell-home-hero-mobile-1.jpg',
            'justccell-home-hero-mobile-2.png',
            'justccell-home-hero-mobile-3.jpg',
            'justccell-home-hero-mobile-4.jpg',
        ],
    ];
}

function justccell_home_assets(): array
{
    $out = [];
    foreach (justccell_home_asset_keys() as $key => $value) {
        if (is_array($value)) {
            $out[$key] = array_values(array_filter(array_map('justccell_ensure_media_url', $value)));
            continue;
        }
        $out[$key] = justccell_ensure_media_url($value);
    }
    $logo = justccell_brand_logo_url();
    if ($logo !== '') {
        $out['logo'] = $logo;
    }
    return $out;
}

function justccell_attachment_id_by_basename(string $basename): int
{
    global $wpdb;
    $basename = basename(str_replace('\\', '/', $basename));
    if ($basename === '') {
        return 0;
    }
    $id = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta}
         WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s
         ORDER BY post_id DESC LIMIT 1",
        '%' . $wpdb->esc_like($basename)
    ));
    return $id > 0 && get_post_type($id) === 'attachment' ? $id : 0;
}

/**
 * Transparent horizontal PNGs first. The older Just-CCELL-logo-line.png and the
 * JPEGs carry a baked white background, so they are fallbacks only.
 *
 * @return list<string>
 */
function justccell_brand_logo_basenames(): array
{
    return [
        'Justccell.com-logo-horizontal.png',
        'Justccell.com-logo-horizontal-small.png',
        'Just-CCELL-logo-line.png',
        'JustCCELL_horizontal_by_3Devices.jpg',
        'JustCCELL_by_3Devices.jpg',
    ];
}

function justccell_brand_logo_id(): int
{
    foreach (justccell_brand_logo_basenames() as $file) {
        $id = justccell_attachment_id_by_basename($file);
        if ($id > 0) {
            return $id;
        }
    }
    return 0;
}

/**
 * Square/circle mark, favicon and schema use only.
 *
 * @return list<string>
 */
function justccell_brand_icon_basenames(): array
{
    return [
        'Justccell.com-circle-logo.png',
        'Justccell.com-logo-square.png',
        'Just-CCELL-round.png',
    ];
}

function justccell_brand_icon_id(): int
{
    foreach (justccell_brand_icon_basenames() as $file) {
        $id = justccell_attachment_id_by_basename($file);
        if ($id > 0) {
            return $id;
        }
    }
    return 0;
}

function justccell_brand_logo_url(): string
{
    $id = justccell_brand_logo_id();
    if ($id < 1) {
        return '';
    }
    $url = wp_get_attachment_url($id);
    return is_string($url) ? $url : '';
}

function justccell_assign_brand_assets(): void
{
    $logo_id = justccell_brand_logo_id();
    if ($logo_id > 0) {
        $current = (int) get_theme_mod('custom_logo');
        $current_file = $current > 0 ? basename((string) get_post_meta($current, '_wp_attached_file', true)) : '';
        // Promote to the preferred asset when the current one is also ours, so a
        // newer transparent upload supersedes an older white-background file.
        // A logo we do not ship was chosen by hand, so leave it alone.
        $is_ours = $current_file === '' || in_array($current_file, justccell_brand_logo_basenames(), true);
        if ($current !== $logo_id && $is_ours) {
            set_theme_mod('custom_logo', $logo_id);
        }
    }

    $icon_id = justccell_brand_icon_id();
    if ($icon_id > 0 && (int) get_option('site_icon') !== $icon_id) {
        update_option('site_icon', $icon_id);
    }
}

function justccell_ensure_front_media(): void
{
    $keys = array_column(justccell_catalog(), 'image');
    foreach (justccell_home_asset_keys() as $value) {
        if (is_array($value)) {
            $keys = array_merge($keys, $value);
            continue;
        }
        $keys[] = $value;
    }
    justccell_ensure_media_files(array_values(array_filter($keys)));
}

add_action('wp', static function (): void {
    if (is_admin()) {
        return;
    }
    justccell_assign_brand_assets();
    if (is_front_page()) {
        justccell_ensure_front_media();
        return;
    }
    justccell_ensure_media_files(array_column(justccell_catalog(), 'image'));
}, 4);
