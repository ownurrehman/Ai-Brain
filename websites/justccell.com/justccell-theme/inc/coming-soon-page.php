<?php
/**
 * Coming soon spotlight pages (Packaging first).
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return list<string>
 */
function justccell_coming_soon_page_slugs(): array
{
    return ['packaging'];
}

function justccell_page_shows_coming_soon(int $post_id = 0): bool
{
    $post_id = $post_id > 0 ? $post_id : (int) get_queried_object_id();
    if ($post_id < 1) {
        return false;
    }
    $slug = (string) get_post_field('post_name', $post_id);
    if (!in_array($slug, justccell_coming_soon_page_slugs(), true)) {
        return false;
    }
    if (!function_exists('get_field')) {
        return true;
    }
    $enabled = get_field('brand_coming_soon', $post_id);
    if ($enabled === null || $enabled === '') {
        return true;
    }
    return (bool) $enabled;
}

/**
 * @return array<string, mixed>
 */
function justccell_coming_soon_page_defaults(string $slug): array
{
    $defaults = [
        'packaging' => [
            'eyebrow'          => __('Coming soon', 'justccell'),
            'eyebrow_sr'       => __('Packaging page coming soon.', 'justccell'),
            'title'            => __('Packaging is on the way', 'justccell'),
            'lede'             => __('We are building a dedicated packaging brief for sleeves, boxes, and inserts. Search the catalogue or browse hardware while we finish this page.', 'justccell'),
            'primary_label'    => __('Browse hardware', 'justccell'),
            'primary_url'      => '/',
            'secondary_label'  => __('Contact us', 'justccell'),
            'secondary_url'    => '/contact/',
            'show_search'      => true,
            'show_showcase'    => true,
            'shop_heading'     => __('Hardware in the catalogue', 'justccell'),
            'shop_lede'        => __('Live SKUs you can open from the catalogue today.', 'justccell'),
        ],
    ];
    return $defaults[$slug] ?? [];
}

/**
 * @return array<string, mixed>
 */
function justccell_get_coming_soon_spotlight(int $post_id = 0): array
{
    $post_id = $post_id > 0 ? $post_id : (int) get_queried_object_id();
    $slug    = (string) get_post_field('post_name', $post_id);
    $raw     = justccell_coming_soon_page_defaults($slug);

    $text = static function (string $field, string $key) use ($post_id, $raw): string {
        if ($post_id > 0 && function_exists('get_field')) {
            $val = get_field($field, $post_id);
            if (is_string($val) && trim($val) !== '') {
                return trim($val);
            }
        }
        return trim((string) ($raw[$key] ?? ''));
    };

    $bool = static function (string $field, string $key) use ($post_id, $raw): bool {
        if ($post_id > 0 && function_exists('get_field')) {
            $val = get_field($field, $post_id);
            if ($val !== null && $val !== '') {
                return (bool) $val;
            }
        }
        return (bool) ($raw[$key] ?? false);
    };

    return [
        'eyebrow'         => $text('coming_soon_eyebrow', 'eyebrow'),
        'eyebrow_sr'      => $text('coming_soon_eyebrow_sr', 'eyebrow_sr'),
        'title'           => $text('coming_soon_title', 'title'),
        'lede'            => $text('coming_soon_lede', 'lede'),
        'primary_label'   => $text('coming_soon_primary_label', 'primary_label'),
        'primary_url'     => $text('coming_soon_primary_url', 'primary_url'),
        'secondary_label' => $text('coming_soon_secondary_label', 'secondary_label'),
        'secondary_url'   => $text('coming_soon_secondary_url', 'secondary_url'),
        'show_search'     => $bool('coming_soon_show_search', 'show_search'),
        'show_showcase'   => $bool('coming_soon_show_catalog', 'show_showcase'),
        'shop_heading'    => $text('coming_soon_shop_heading', 'shop_heading'),
        'shop_lede'       => $text('coming_soon_shop_lede', 'shop_lede'),
    ];
}

/**
 * @return array<string, mixed>
 */
function justccell_acf_packaging_coming_soon_group(): array
{
    return [
        'key'                   => 'group_jc_packaging_coming_soon',
        'title'                 => 'Coming soon page',
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'menu_order'            => 0,
        'active'                => true,
        'location'              => justccell_acf_location_pages(['packaging']),
        'fields'                => [
            [
                'key'           => 'field_jc_pkg_coming_soon',
                'label'         => 'Show coming soon layout',
                'name'          => 'brand_coming_soon',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
                'instructions'  => 'When on, visitors see the spotlight coming-soon page (like 404). Turn off when the full packaging page is ready.',
            ],
            ['key' => 'field_jc_pkg_cs_hero_tab', 'label' => 'Hero', 'type' => 'tab'],
            [
                'key'           => 'field_jc_pkg_cs_eyebrow',
                'label'         => 'Eyebrow',
                'name'          => 'coming_soon_eyebrow',
                'type'          => 'text',
                'default_value' => __('Coming soon', 'justccell'),
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_pkg_cs_eyebrow_sr',
                'label'         => 'Screen-reader prefix (optional)',
                'name'          => 'coming_soon_eyebrow_sr',
                'type'          => 'text',
                'default_value' => __('Packaging page coming soon.', 'justccell'),
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_pkg_cs_title',
                'label'         => 'Heading',
                'name'          => 'coming_soon_title',
                'type'          => 'text',
                'default_value' => __('Packaging is on the way', 'justccell'),
            ],
            [
                'key'           => 'field_jc_pkg_cs_lede',
                'label'         => 'Intro copy',
                'name'          => 'coming_soon_lede',
                'type'          => 'textarea',
                'rows'          => 3,
                'default_value' => __('We are building a dedicated packaging brief for sleeves, boxes, and inserts. Search the catalogue or browse hardware while we finish this page.', 'justccell'),
            ],
            ['key' => 'field_jc_pkg_cs_actions_tab', 'label' => 'Buttons', 'type' => 'tab'],
            [
                'key'           => 'field_jc_pkg_cs_primary_label',
                'label'         => 'Primary button',
                'name'          => 'coming_soon_primary_label',
                'type'          => 'text',
                'default_value' => __('Browse hardware', 'justccell'),
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'         => 'field_jc_pkg_cs_primary_url',
                'label'       => 'Primary link',
                'name'        => 'coming_soon_primary_url',
                'type'        => 'text',
                'placeholder' => '/',
                'wrapper'     => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_pkg_cs_secondary_label',
                'label'         => 'Secondary button',
                'name'          => 'coming_soon_secondary_label',
                'type'          => 'text',
                'default_value' => __('Contact us', 'justccell'),
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'         => 'field_jc_pkg_cs_secondary_url',
                'label'       => 'Secondary link',
                'name'        => 'coming_soon_secondary_url',
                'type'        => 'text',
                'placeholder' => '/contact/',
                'wrapper'     => ['width' => '50'],
            ],
            ['key' => 'field_jc_pkg_cs_catalog_tab', 'label' => 'Catalog helpers', 'type' => 'tab'],
            [
                'key'           => 'field_jc_pkg_cs_show_search',
                'label'         => 'Show search box',
                'name'          => 'coming_soon_show_search',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
            ],
            [
                'key'           => 'field_jc_pkg_cs_show_catalog',
                'label'         => 'Show category grid and product rail',
                'name'          => 'coming_soon_show_catalog',
                'type'          => 'true_false',
                'default_value' => 1,
                'ui'            => 1,
            ],
            [
                'key'           => 'field_jc_pkg_cs_shop_heading',
                'label'         => 'Product rail heading',
                'name'          => 'coming_soon_shop_heading',
                'type'          => 'text',
                'default_value' => __('Hardware in the catalogue', 'justccell'),
                'wrapper'       => ['width' => '80'],
            ],
            [
                'key'           => 'field_jc_pkg_cs_shop_lede',
                'label'         => 'Product rail intro',
                'name'          => 'coming_soon_shop_lede',
                'type'          => 'textarea',
                'rows'          => 2,
                'default_value' => __('Live SKUs you can open from the catalogue today.', 'justccell'),
            ],
        ],
    ];
}

function justccell_register_acf_packaging_coming_soon(): void
{
    justccell_acf_register_field_group(justccell_acf_packaging_coming_soon_group());
}

function justccell_upgrade_packaging_coming_soon_page(): void
{
    if (get_option('justccell_packaging_coming_soon_0990') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug') || !function_exists('update_field')) {
        return;
    }
    $page = justccell_find_page_by_slug('packaging');
    if (!$page instanceof WP_Post) {
        return;
    }
    $post_id = (int) $page->ID;
    $defaults = justccell_coming_soon_page_defaults('packaging');

    if (function_exists('justccell_acf_seed_text_if_empty')) {
        foreach (
            [
                'coming_soon_eyebrow'         => 'eyebrow',
                'coming_soon_eyebrow_sr'      => 'eyebrow_sr',
                'coming_soon_title'           => 'title',
                'coming_soon_lede'            => 'lede',
                'coming_soon_primary_label'   => 'primary_label',
                'coming_soon_primary_url'     => 'primary_url',
                'coming_soon_secondary_label' => 'secondary_label',
                'coming_soon_secondary_url'   => 'secondary_url',
                'coming_soon_shop_heading'    => 'shop_heading',
                'coming_soon_shop_lede'       => 'shop_lede',
            ] as $field => $key
        ) {
            justccell_acf_seed_text_if_empty($field, (string) ($defaults[$key] ?? ''), $post_id);
        }
    }

    $enabled = function_exists('get_field') ? get_field('brand_coming_soon', $post_id) : null;
    if ($enabled === null || $enabled === '') {
        update_field('brand_coming_soon', 1, $post_id);
    }

    update_option('justccell_packaging_coming_soon_0990', '1', false);
}

add_action('init', 'justccell_upgrade_packaging_coming_soon_page', 76);
