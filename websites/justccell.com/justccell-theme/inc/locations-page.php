<?php
/**
 * Locations page data. ACF on Pages → Locations is the source of truth.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_locations_default_kicker(): string
{
    return __('UK headquarters', 'justccell');
}

function justccell_locations_default_lede(): string
{
    return __('Justccell supports licensed extract brands from our UK headquarters in Bolton. Visit by appointment, or send a wholesale inquiry — we respond within one business day.', 'justccell');
}

function justccell_locations_default_cta_copy(): string
{
    return __('Tell us your product line, volumes, and market. A Justccell representative will confirm availability and arrange samples or a meeting at our UK office.', 'justccell');
}

/**
 * @return list<array<string, mixed>>
 */
function justccell_default_location_rows(): array
{
    $uk_address = "112–116 Hamill House\nChorley New Road\nBolton BL1 4DH\nUnited Kingdom";

    return [
        [
            'title'             => __('Bolton — UK headquarters', 'justccell'),
            'title_tag'         => 'h2',
            'country'           => __('United Kingdom', 'justccell'),
            'summary'           => __('Wholesale quotes, samples, and account support for UK and international buyers. Call or visit by appointment.', 'justccell'),
            'address'           => $uk_address,
            'phone_label'       => __('Phone', 'justccell'),
            'phone'             => '01204 565389',
            'email'             => '',
            'hours'             => __('Monday–Friday, 9:00–17:00 GMT', 'justccell'),
            'coming_soon'       => 0,
            'coming_soon_label' => '',
            'gmb_url'           => '',
            'maps_embed_url'    => '',
            'map_url'           => justccell_location_maps_search_url($uk_address),
            'map_label'         => __('Get directions', 'justccell'),
            'image'             => 0,
        ],
    ];
}

/**
 * @param array<string, mixed> $row
 */
function justccell_location_row_is_uk(array $row): bool
{
    $haystack = strtolower(trim(
        (string) ($row['country'] ?? '') . ' ' . (string) ($row['title'] ?? '') . ' ' . (string) ($row['address'] ?? '')
    ));
    if ($haystack === '') {
        return false;
    }
    if (
        str_contains($haystack, 'spain')
        || str_contains($haystack, 'switzerland')
        || str_contains($haystack, 'ecublens')
        || str_contains($haystack, 'swiss')
    ) {
        return false;
    }

    return str_contains($haystack, 'united kingdom')
        || str_contains($haystack, 'bolton')
        || (bool) preg_match('/\buk\b/', $haystack);
}

function justccell_location_address_oneline(string $address): string
{
    $address = trim(str_replace(["\r\n", "\r"], "\n", $address));
    if ($address === '') {
        return '';
    }
    $parts = array_values(array_filter(array_map('trim', explode("\n", $address))));
    return implode(', ', $parts);
}

function justccell_location_maps_search_url(string $address): string
{
    $query = justccell_location_address_oneline($address);
    if ($query === '') {
        return '';
    }
    return 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($query);
}

function justccell_location_maps_embed_url(string $address): string
{
    $query = justccell_location_address_oneline($address);
    if ($query === '') {
        return '';
    }
    return 'https://maps.google.com/maps?q=' . rawurlencode($query) . '&z=15&output=embed';
}

/**
 * @param array<string, mixed> $row
 */
function justccell_location_embed_src(array $row): string
{
    $custom = esc_url_raw((string) ($row['maps_embed_url'] ?? ''));
    if ($custom !== '') {
        return $custom;
    }
    $address = (string) ($row['address'] ?? '');
    return justccell_location_maps_embed_url($address);
}

/**
 * @param array<string, mixed> $row
 */
function justccell_location_directions_url(array $row): string
{
    $url = esc_url_raw((string) ($row['map_url'] ?? ''));
    if ($url !== '') {
        return $url;
    }
    return justccell_location_maps_search_url((string) ($row['address'] ?? ''));
}

/**
 * @param array<string, mixed> $row
 * @return array<string, mixed>
 */
function justccell_normalize_location_row(array $row): array
{
    $image_id = 0;
    if (function_exists('justccell_acf_to_attachment_id')) {
        $image_id = justccell_acf_to_attachment_id($row['image'] ?? 0);
    } elseif (isset($row['image_id'])) {
        $image_id = (int) $row['image_id'];
    }

    $soon = !empty($row['coming_soon']);
    $soon_label = trim((string) ($row['coming_soon_label'] ?? ''));
    if ($soon && $soon_label === '') {
        $soon_label = __('Opening soon', 'justccell');
    }

    $normalized = [
        'title'             => trim((string) ($row['title'] ?? '')),
        'title_tag'         => (string) ($row['title_tag'] ?? 'h2'),
        'country'           => trim((string) ($row['country'] ?? '')),
        'summary'           => trim((string) ($row['summary'] ?? '')),
        'address'           => trim((string) ($row['address'] ?? '')),
        'phone_label'       => trim((string) ($row['phone_label'] ?? '')),
        'phone'             => trim((string) ($row['phone'] ?? '')),
        'email'             => sanitize_email((string) ($row['email'] ?? '')),
        'hours'             => trim((string) ($row['hours'] ?? '')),
        'coming_soon'       => $soon,
        'coming_soon_label' => $soon_label,
        'gmb_url'           => esc_url_raw((string) ($row['gmb_url'] ?? '')),
        'maps_embed_url'    => esc_url_raw((string) ($row['maps_embed_url'] ?? '')),
        'map_url'           => esc_url_raw((string) ($row['map_url'] ?? '')),
        'map_label'         => trim((string) ($row['map_label'] ?? '')),
        'image_id'          => $image_id,
    ];

    if ($normalized['map_label'] === '' && $normalized['map_url'] !== '') {
        $normalized['map_label'] = __('Get directions', 'justccell');
    }

    $normalized['embed_src']       = justccell_location_embed_src($normalized);
    $normalized['directions_url']    = justccell_location_directions_url($normalized);
    $normalized['address_oneline']   = justccell_location_address_oneline($normalized['address']);

    return $normalized;
}

/**
 * @return array<string, mixed>
 */
function justccell_get_locations_page_data(): array
{
    $post_id = (int) get_queried_object_id();
    $field = static function (string $name, string $fallback = '') use ($post_id): string {
        if ($post_id < 1 || !function_exists('get_field')) {
            return $fallback;
        }
        $value = get_field($name, $post_id);
        return is_string($value) && trim($value) !== '' ? trim($value) : $fallback;
    };
    $image_id = static function (string $name) use ($post_id): int {
        if ($post_id < 1 || !function_exists('get_field') || !function_exists('justccell_acf_to_attachment_id')) {
            return 0;
        }
        return justccell_acf_to_attachment_id(get_field($name, $post_id));
    };

    $items = [];
    if ($post_id > 0 && function_exists('get_field')) {
        $raw = get_field('locations_items', $post_id);
        if (is_array($raw)) {
            foreach ($raw as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $item = justccell_normalize_location_row($row);
                if ($item['title'] === '' && $item['address'] === '' && !$item['coming_soon']) {
                    continue;
                }
                $items[] = $item;
            }
        }
    }
    if ($items === []) {
        foreach (justccell_default_location_rows() as $row) {
            $items[] = justccell_normalize_location_row($row);
        }
    }

    $cta_url = $field('brand_cta_url', '');
    if ($cta_url === '' && function_exists('justccell_inquiry_url')) {
        $cta_url = justccell_inquiry_url();
    }

    return [
        'kicker'          => $field('brand_kicker', justccell_locations_default_kicker()),
        'title'           => $field('brand_title', __('Location', 'justccell')),
        'title_tag'       => $field('brand_title_tag', 'h1'),
        'lede'            => $field(
            'brand_lede',
            justccell_locations_default_lede()
        ),
        'image_id'        => $image_id('brand_image'),
        'image_mobile_id' => $image_id('brand_image_mobile'),
        'items'           => $items,
        'cta_title'       => $field('brand_cta_title', __('Plan a visit or request samples', 'justccell')),
        'cta_title_tag'   => $field('brand_cta_title_tag', 'h2'),
        'cta_copy'        => $field(
            'brand_cta_copy',
            justccell_locations_default_cta_copy()
        ),
        'cta_label'       => $field('brand_cta_label', __('Contact sales', 'justccell')),
        'cta_url'         => $cta_url,
    ];
}

function justccell_seed_locations_page_fields(int $post_id, bool $force = false): void
{
    if ($post_id < 1 || !function_exists('justccell_acf_set_if_empty')) {
        return;
    }
    justccell_acf_set_if_empty('brand_kicker', justccell_locations_default_kicker(), $post_id, $force);
    justccell_acf_set_if_empty('brand_title', __('Location', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('brand_title_tag', 'h1', $post_id, $force);
    justccell_acf_set_if_empty(
        'brand_lede',
        justccell_locations_default_lede(),
        $post_id,
        $force
    );
    justccell_acf_set_if_empty('locations_items', justccell_default_location_rows(), $post_id, $force);
    justccell_acf_set_if_empty('brand_cta_title', __('Plan a visit or request samples', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('brand_cta_title_tag', 'h2', $post_id, $force);
    justccell_acf_set_if_empty(
        'brand_cta_copy',
        justccell_locations_default_cta_copy(),
        $post_id,
        $force
    );
    justccell_acf_set_if_empty('brand_cta_label', __('Contact sales', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('brand_cta_url', home_url('/contact/'), $post_id, $force);
}

function justccell_upgrade_locations_page_fields(): void
{
    if (get_option('justccell_locations_upgrade_069') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug') || !function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $page = function_exists('justccell_find_location_page') ? justccell_find_location_page() : justccell_find_page_by_slug('location');
    if (!$page instanceof WP_Post) {
        return;
    }

    $post_id = (int) $page->ID;
    $defaults = justccell_default_location_rows();

    $lede = get_field('brand_lede', $post_id);
    if (!is_string($lede) || trim($lede) === '') {
        update_field(
            'brand_lede',
            justccell_locations_default_lede(),
            $post_id
        );
    }

    $patch_strings = [
        'brand_kicker'    => justccell_locations_default_kicker(),
        'brand_lede'      => justccell_locations_default_lede(),
        'brand_cta_title' => __('Plan a visit or request samples', 'justccell'),
        'brand_cta_copy'  => justccell_locations_default_cta_copy(),
        'brand_cta_label' => __('Contact sales', 'justccell'),
    ];
    foreach ($patch_strings as $key => $value) {
        $current = get_field($key, $post_id);
        if (!is_string($current) || trim($current) === '' || $current === __('Contact us', 'justccell')) {
            update_field($key, $value, $post_id);
        }
    }

    $legacy_titles = [
        'uk location'        => 0,
        'spain location'     => 1,
        'switzerland location' => 2,
    ];

    $items = get_field('locations_items', $post_id);
    if (!is_array($items)) {
        $items = [];
    }

    $changed = false;
    foreach ($items as $index => $row) {
        if (!is_array($row)) {
            continue;
        }
        $def_index = $index;
        $title_key = strtolower(trim((string) ($row['title'] ?? '')));
        if (isset($legacy_titles[$title_key])) {
            $def_index = $legacy_titles[$title_key];
        }
        $def = $defaults[$def_index] ?? null;
        if (!is_array($def)) {
            continue;
        }

        if (isset($legacy_titles[$title_key])) {
            $row['title'] = $def['title'];
            $changed = true;
        }

        foreach (['country', 'summary', 'hours', 'map_url', 'map_label', 'coming_soon_label'] as $field_key) {
            if (trim((string) ($row[$field_key] ?? '')) === '' && trim((string) ($def[$field_key] ?? '')) !== '') {
                $row[$field_key] = $def[$field_key];
                $changed = true;
            }
        }

        if (empty($row['coming_soon']) && trim((string) ($row['map_url'] ?? '')) === '' && trim((string) ($def['map_url'] ?? '')) !== '') {
            $row['map_url'] = $def['map_url'];
            $changed = true;
        }

        if (trim((string) ($row['phone_label'] ?? '')) === 'Tel:') {
            $row['phone_label'] = __('Phone', 'justccell');
            $changed = true;
        }

        $items[$index] = $row;
    }

    if ($changed) {
        update_field('locations_items', $items, $post_id);
    }

    update_option('justccell_locations_upgrade_069', '1', false);
}

function justccell_apply_locations_copy_053(): void
{
    if (get_option('justccell_locations_copy_053') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug')) {
        return;
    }
    $page = function_exists('justccell_find_location_page') ? justccell_find_location_page() : justccell_find_page_by_slug('location');
    if (!$page instanceof WP_Post) {
        return;
    }
    justccell_seed_locations_page_fields((int) $page->ID, false);
    update_option('justccell_locations_copy_053', '1', false);
}

function justccell_locations_copy_mentions_other_markets(string $value): bool
{
    $hay = strtolower($value);
    return str_contains($hay, 'across europe')
        || str_contains($hay, 'global presence')
        || str_contains($hay, 'office nearest you')
        || str_contains($hay, 'spain')
        || str_contains($hay, 'switzerland');
}

/**
 * Client 2026-09-01: keep Locations to the UK only. Spain/EU moves to a later domain.
 */
function justccell_apply_locations_uk_only_20260901(): void
{
    if (get_option('justccell_locations_uk_only_20260901') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug') || !function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $page = function_exists('justccell_find_location_page') ? justccell_find_location_page() : justccell_find_page_by_slug('location');
    if (!$page instanceof WP_Post) {
        return;
    }

    $post_id = (int) $page->ID;
    $items = get_field('locations_items', $post_id);
    $kept = [];
    if (is_array($items)) {
        foreach ($items as $row) {
            if (!is_array($row)) {
                continue;
            }
            if (justccell_location_row_is_uk($row)) {
                $kept[] = $row;
            }
        }
    }
    if ($kept === []) {
        $kept = justccell_default_location_rows();
    }
    update_field('locations_items', $kept, $post_id);

    $copy = [
        'brand_kicker'   => justccell_locations_default_kicker(),
        'brand_lede'     => justccell_locations_default_lede(),
        'brand_cta_copy' => justccell_locations_default_cta_copy(),
    ];
    foreach ($copy as $key => $value) {
        $current = get_field($key, $post_id);
        if (!is_string($current) || trim($current) === '' || justccell_locations_copy_mentions_other_markets($current)) {
            update_field($key, $value, $post_id);
        }
    }

    update_option('justccell_locations_uk_only_20260901', '1', false);
}

add_action('init', 'justccell_apply_locations_copy_053', 72);
add_action('init', 'justccell_upgrade_locations_page_fields', 73);
add_action('init', 'justccell_apply_locations_uk_only_20260901', 74);
add_action('init', 'justccell_apply_location_slug_20260901', 75);

/**
 * Keep ACF + page title on the canonical /location/ slug.
 */
function justccell_apply_location_slug_20260901(): void
{
    if (get_option('justccell_location_slug_20260901') === '1') {
        return;
    }
    $page = function_exists('justccell_find_location_page') ? justccell_find_location_page() : (function_exists('justccell_find_page_by_slug') ? justccell_find_page_by_slug('location') : null);
    if (!$page instanceof WP_Post) {
        return;
    }

    $post_id = (int) $page->ID;
    if ($page->post_name === 'locations') {
        wp_update_post([
            'ID'        => $post_id,
            'post_name' => 'location',
        ]);
    }
    if (strcasecmp((string) $page->post_title, 'Locations') === 0) {
        wp_update_post([
            'ID'         => $post_id,
            'post_title' => __('Location', 'justccell'),
        ]);
    }
    if (function_exists('get_field') && function_exists('update_field')) {
        $title = get_field('brand_title', $post_id);
        if (!is_string($title) || trim($title) === '' || strcasecmp(trim($title), 'Locations') === 0) {
            update_field('brand_title', __('Location', 'justccell'), $post_id);
        }
    }

    update_option('justccell_location_slug_20260901', '1', false);
}
