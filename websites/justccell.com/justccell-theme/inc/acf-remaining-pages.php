<?php
/**
 * ACF sync for About, Why, Discover, Contact, Location, Legal.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, mixed>
 */
function justccell_acf_about_page_group(): array
{
    return [
        'key'                   => 'group_jc_about_page',
        'title'                 => 'About page',
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'hide_on_screen'        => ['the_content'],
        'active'                => true,
        'location'              => justccell_acf_location_pages(['about']),
        'menu_order'            => 0,
        'fields'                => [

            ['key' => 'field_jc_about_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            [
                'key'           => 'field_jc_about_title',
                'label'         => 'Hero title',
                'name'          => 'brand_title',
                'type'          => 'text',
                'default_value' => 'About Justccell',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_about_title_tag', 'brand_title_tag', 'h1', 'Title tag'),
            justccell_acf_image_field('field_jc_about_image', 'brand_image', 'Hero image (desktop)'),
            justccell_acf_image_field('field_jc_about_image_mobile', 'brand_image_mobile', 'Hero image (mobile)'),
            ['key' => 'field_jc_about_tab_culture', 'label' => 'Culture', 'type' => 'tab'],
            [
                'key'           => 'field_jc_about_heading_culture',
                'label'         => 'Culture section heading',
                'name'          => 'about_heading_culture',
                'type'          => 'text',
                'default_value' => 'Corporate Culture',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_about_heading_culture_tag', 'about_heading_culture_tag', 'h2'),
            [
                'key'          => 'field_jc_about_culture',
                'label'        => 'Culture cards',
                'name'         => 'brand_culture',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add culture card',
                'sub_fields'   => [
                    justccell_acf_image_field('field_jc_about_culture_image', 'image', 'Image', '100'),
                    [
                        'key'     => 'field_jc_about_culture_title',
                        'label'   => 'Heading',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '80'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_about_culture_tag', 'title_tag', 'h3'),
                    [
                        'key'  => 'field_jc_about_culture_copy',
                        'label'=> 'Copy',
                        'name' => 'copy',
                        'type' => 'textarea',
                        'rows' => 3,
                    ],
                ],
            ],
            ['key' => 'field_jc_about_tab_company', 'label' => 'Company', 'type' => 'tab'],
            [
                'key'           => 'field_jc_about_heading_company',
                'label'         => 'Company section heading',
                'name'          => 'about_heading_company',
                'type'          => 'text',
                'default_value' => 'Company Introduction',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_about_heading_company_tag', 'about_heading_company_tag', 'h2'),
            [
                'key'           => 'field_jc_about_tagline',
                'label'         => 'Company tagline',
                'name'          => 'brand_tagline',
                'type'          => 'text',
                'default_value' => 'Hardware without the factory',
            ],
            justccell_acf_image_field('field_jc_about_company_image', 'about_company_image', 'Company photo', '50'),
            [
                'key'          => 'field_jc_about_company_copy',
                'label'        => 'Company introduction copy',
                'name'         => 'about_company_copy',
                'type'         => 'textarea',
                'rows'         => 8,
                'instructions' => 'Blank line between paragraphs.',
            ],
            ['key' => 'field_jc_about_tab_history', 'label' => 'History', 'type' => 'tab'],
            [
                'key'           => 'field_jc_about_heading_history',
                'label'         => 'History section heading',
                'name'          => 'about_heading_history',
                'type'          => 'text',
                'default_value' => 'Brand History',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_about_heading_history_tag', 'about_heading_history_tag', 'h2'),
            [
                'key'          => 'field_jc_about_timeline',
                'label'        => 'Brand history',
                'name'         => 'brand_timeline',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add item',
                'sub_fields'   => [
                    [
                        'key'     => 'field_jc_about_tl_year',
                        'label'   => 'Year',
                        'name'    => 'year',
                        'type'    => 'text',
                        'wrapper' => ['width' => '20'],
                    ],
                    [
                        'key'     => 'field_jc_about_tl_item',
                        'label'   => 'Item',
                        'name'    => 'item',
                        'type'    => 'textarea',
                        'rows'    => 2,
                        'wrapper' => ['width' => '80'],
                    ],
                ],
            ],
            ['key' => 'field_jc_about_tab_customer', 'label' => 'Customer', 'type' => 'tab'],
            [
                'key'           => 'field_jc_about_heading_customer',
                'label'         => 'Customer section heading',
                'name'          => 'about_heading_customer',
                'type'          => 'text',
                'default_value' => 'Customer Centricity',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_about_heading_customer_tag', 'about_heading_customer_tag', 'h2'),
            [
                'key'          => 'field_jc_about_customer',
                'label'        => 'Customer cards',
                'name'         => 'brand_customer',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add customer card',
                'sub_fields'   => [
                    justccell_acf_image_field('field_jc_about_customer_image', 'image', 'Image', '100'),
                    [
                        'key'     => 'field_jc_about_customer_title',
                        'label'   => 'Heading',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '80'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_about_customer_tag', 'title_tag', 'h3'),
                    [
                        'key'  => 'field_jc_about_customer_copy',
                        'label'=> 'Copy',
                        'name' => 'copy',
                        'type' => 'textarea',
                        'rows' => 3,
                    ],
                ],
            ],
        ],
    ];
}

/**
 * @return array<string, mixed>
 */
function justccell_acf_why_page_group(): array
{
    return [
        'key'                   => 'group_jc_why_pages',
        'title'                 => 'Why Justccell page',
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'hide_on_screen'        => ['the_content'],
        'active'                => true,
        'location'              => justccell_acf_location_pages(justccell_why_page_slugs()),
        'menu_order'            => 0,
        'fields'                => [

            ['key' => 'field_jc_why_tab_hero', 'label' => 'Hero', 'type' => 'tab'],
            [
                'key'     => 'field_jc_why_title',
                'label'   => 'Hero title',
                'name'    => 'brand_title',
                'type'    => 'text',
                'wrapper' => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_why_title_tag', 'brand_title_tag', 'h1', 'Title tag'),
            justccell_acf_image_field('field_jc_why_image', 'brand_image', 'Hero image (desktop)'),
            justccell_acf_image_field('field_jc_why_image_mobile', 'brand_image_mobile', 'Hero image (mobile)'),
            ['key' => 'field_jc_why_tab_intro', 'label' => 'Intro', 'type' => 'tab'],
            [
                'key'           => 'field_jc_why_layout',
                'label'         => 'Intro layout',
                'name'          => 'why_layout',
                'type'          => 'select',
                'choices'       => [
                    ''      => 'Stacked (copy then image)',
                    'split' => 'Split (image + copy side by side)',
                ],
                'allow_null'    => 0,
                'default_value' => '',
                'return_format' => 'value',
            ],
            [
                'key'  => 'field_jc_why_lede',
                'label'=> 'Intro copy',
                'name' => 'brand_lede',
                'type' => 'textarea',
                'rows' => 5,
            ],
            justccell_acf_image_field('field_jc_why_intro_image', 'why_intro_image', 'Intro image', '50'),
            ['key' => 'field_jc_why_tab_stats', 'label' => 'Stats', 'type' => 'tab'],
            [
                'key'     => 'field_jc_why_meet',
                'label'   => 'Meet / stats heading',
                'name'    => 'why_meet_heading',
                'type'    => 'text',
                'wrapper' => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_why_meet_tag', 'why_meet_heading_tag', 'h2'),
            [
                'key'          => 'field_jc_why_stats',
                'label'        => 'Stats',
                'name'         => 'why_stats',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add stat',
                'instructions' => 'Used on Manufacturing. Leave empty on other Why pages.',
                'sub_fields'   => [
                    [
                        'key'     => 'field_jc_why_stat_value',
                        'label'   => 'Value',
                        'name'    => 'value',
                        'type'    => 'text',
                        'wrapper' => ['width' => '25'],
                    ],
                    [
                        'key'     => 'field_jc_why_stat_unit',
                        'label'   => 'Unit',
                        'name'    => 'unit',
                        'type'    => 'text',
                        'wrapper' => ['width' => '25'],
                    ],
                    [
                        'key'     => 'field_jc_why_stat_label',
                        'label'   => 'Label',
                        'name'    => 'label',
                        'type'    => 'text',
                        'wrapper' => ['width' => '50'],
                    ],
                ],
            ],
            ['key' => 'field_jc_why_tab_rows', 'label' => 'Content rows', 'type' => 'tab'],
            [
                'key'          => 'field_jc_why_rows',
                'label'        => 'Content rows',
                'name'         => 'why_rows',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add row',
                'sub_fields'   => [
                    justccell_acf_image_field('field_jc_why_row_image', 'image', 'Image', '100'),
                    [
                        'key'     => 'field_jc_why_row_title',
                        'label'   => 'Heading',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '80'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_why_row_tag', 'title_tag', 'h3'),
                    [
                        'key'   => 'field_jc_why_row_kicker',
                        'label' => 'Kicker',
                        'name'  => 'kicker',
                        'type'  => 'text',
                    ],
                    [
                        'key'  => 'field_jc_why_row_copy',
                        'label'=> 'Copy',
                        'name' => 'copy',
                        'type' => 'textarea',
                        'rows' => 4,
                    ],
                ],
            ],
            ['key' => 'field_jc_why_tab_compare', 'label' => 'Compare', 'type' => 'tab'],
            [
                'key'           => 'field_jc_why_compare_heading',
                'label'         => 'Compare heading',
                'name'          => 'why_compare_heading',
                'type'          => 'text',
                'default_value' => 'What’s Different',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_why_compare_htag', 'why_compare_heading_tag', 'h2'),
            [
                'key'     => 'field_jc_why_compare_left_title',
                'label'   => 'Left column title',
                'name'    => 'brand_compare_left_title',
                'type'    => 'text',
                'wrapper' => ['width' => '30'],
            ],
            justccell_acf_heading_tag_field('field_jc_why_compare_left_tag', 'brand_compare_left_title_tag', 'h3'),
            [
                'key'     => 'field_jc_why_compare_right_title',
                'label'   => 'Right column title',
                'name'    => 'brand_compare_right_title',
                'type'    => 'text',
                'wrapper' => ['width' => '30'],
            ],
            justccell_acf_heading_tag_field('field_jc_why_compare_right_tag', 'brand_compare_right_title_tag', 'h3'),
            [
                'key'          => 'field_jc_why_compare_left_items',
                'label'        => 'Left column items',
                'name'         => 'brand_compare_left_items',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add item',
                'sub_fields'   => [
                    ['key' => 'field_jc_why_cli', 'label' => 'Item', 'name' => 'item', 'type' => 'text'],
                ],
                'wrapper' => ['width' => '50'],
            ],
            [
                'key'          => 'field_jc_why_compare_right_items',
                'label'        => 'Right column items',
                'name'         => 'brand_compare_right_items',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add item',
                'sub_fields'   => [
                    ['key' => 'field_jc_why_cri', 'label' => 'Item', 'name' => 'item', 'type' => 'text'],
                ],
                'wrapper' => ['width' => '50'],
            ],
            // Legacy orphan — hidden via prepare_field; kept so stored data is not orphaned.
            [
                'key'          => 'field_jc_why_tabs',
                'label'        => 'Tab bar (legacy — not rendered)',
                'name'         => 'why_tabs',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add tab',
                'sub_fields'   => [
                    [
                        'key'     => 'field_jc_why_tab_title',
                        'label'   => 'Label',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '40'],
                    ],
                    [
                        'key'     => 'field_jc_why_tab_url',
                        'label'   => 'URL',
                        'name'    => 'url',
                        'type'    => 'url',
                        'wrapper' => ['width' => '60'],
                    ],
                ],
            ],
        ],
    ];
}

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_about_page_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = justccell_acf_build_field_map(justccell_acf_about_page_group()['fields'] ?? []);
    return $map;
}

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_why_page_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = justccell_acf_build_field_map(justccell_acf_why_page_group()['fields'] ?? []);
    return $map;
}

function justccell_about_seed_page_acf_content(): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $seed_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
    if (get_option('justccell_about_acf_seeded') === $seed_ver) {
        return;
    }

    $page = get_page_by_path('about');
    if (!$page instanceof WP_Post) {
        return;
    }
    $post_id = (int) $page->ID;
    $raw = justccell_static_pages()['about'] ?? [];
    if ($raw === []) {
        return;
    }

    justccell_acf_seed_text_if_empty('brand_title', (string) ($raw['title'] ?? ''), $post_id);
    justccell_acf_seed_text_if_empty('brand_title_tag', 'h1', $post_id);
    justccell_acf_seed_text_if_empty('brand_tagline', (string) ($raw['tagline'] ?? ''), $post_id);
    justccell_acf_seed_text_if_empty('about_heading_culture', __('Corporate Culture', 'justccell'), $post_id);
    justccell_acf_seed_text_if_empty('about_heading_company', __('Company Introduction', 'justccell'), $post_id);
    justccell_acf_seed_text_if_empty('about_heading_history', __('Brand History', 'justccell'), $post_id);
    justccell_acf_seed_text_if_empty('about_heading_customer', __('Customer Centricity', 'justccell'), $post_id);

    justccell_acf_seed_image_if_empty('brand_image', (string) ($raw['image'] ?? ''), $post_id);
    justccell_acf_seed_image_if_empty('brand_image_mobile', (string) ($raw['image_mobile'] ?? ''), $post_id);
    justccell_acf_seed_image_if_empty('about_company_image', (string) ($raw['company_image'] ?? ''), $post_id);

    $company_copy = '';
    foreach ((array) ($raw['sections'] ?? []) as $section) {
        if (is_array($section) && ($section['id'] ?? '') === 'company-introduction') {
            $company_copy = (string) ($section['copy'] ?? '');
            break;
        }
    }
    justccell_acf_seed_text_if_empty('about_company_copy', $company_copy, $post_id);

    $culture = get_field('brand_culture', $post_id);
    if (!is_array($culture) || $culture === []) {
        $rows = [];
        foreach ((array) ($raw['culture'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $img = (string) ($row['image'] ?? '');
            $img_id = 0;
            if ($img !== '') {
                justccell_ensure_media_url($img);
                $img_id = justccell_media_id($img);
            }
            $rows[] = [
                'title'     => (string) ($row['title'] ?? ''),
                'title_tag' => 'h3',
                'copy'      => (string) ($row['copy'] ?? ''),
                'image'     => $img_id > 0 ? $img_id : '',
            ];
        }
        if ($rows !== []) {
            update_field('brand_culture', $rows, $post_id);
        }
    }

    $customer = get_field('brand_customer', $post_id);
    if (!is_array($customer) || $customer === []) {
        $rows = [];
        foreach ((array) ($raw['customer'] ?? []) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $img = (string) ($row['image'] ?? '');
            $img_id = 0;
            if ($img !== '') {
                justccell_ensure_media_url($img);
                $img_id = justccell_media_id($img);
            }
            $rows[] = [
                'title'     => (string) ($row['title'] ?? ''),
                'title_tag' => 'h3',
                'copy'      => (string) ($row['copy'] ?? ''),
                'image'     => $img_id > 0 ? $img_id : '',
            ];
        }
        if ($rows !== []) {
            update_field('brand_customer', $rows, $post_id);
        }
    }

    $timeline = get_field('brand_timeline', $post_id);
    if (!is_array($timeline) || $timeline === []) {
        $rows = [];
        foreach ((array) ($raw['timeline_years'] ?? []) as $year_row) {
            if (!is_array($year_row)) {
                continue;
            }
            $year = (string) ($year_row['year'] ?? '');
            foreach ((array) ($year_row['items'] ?? []) as $item) {
                $rows[] = ['year' => $year, 'item' => (string) $item];
            }
        }
        if ($rows !== []) {
            update_field('brand_timeline', $rows, $post_id);
        }
    }

    update_option('justccell_about_acf_seeded', $seed_ver, false);
}

function justccell_why_seed_pages_acf_content(): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $seed_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
    if (get_option('justccell_why_acf_seeded') === $seed_ver) {
        return;
    }

    $static = justccell_static_pages();
    foreach (justccell_why_page_slugs() as $slug) {
        $page = get_page_by_path($slug);
        if (!$page instanceof WP_Post) {
            continue;
        }
        $raw = $static[$slug] ?? [];
        if ($raw === []) {
            continue;
        }
        $post_id = (int) $page->ID;
        justccell_acf_seed_text_if_empty('brand_title', (string) ($raw['title'] ?? ''), $post_id);
        justccell_acf_seed_text_if_empty('brand_lede', (string) ($raw['lede'] ?? ''), $post_id);
        justccell_acf_seed_text_if_empty('brand_title_tag', 'h1', $post_id);
        justccell_acf_seed_text_if_empty('why_layout', (string) ($raw['layout'] ?? ''), $post_id);
        justccell_acf_seed_text_if_empty('why_meet_heading', (string) ($raw['meet_heading'] ?? ''), $post_id);
        justccell_acf_seed_text_if_empty('why_compare_heading', __('What’s Different', 'justccell'), $post_id);
        justccell_acf_seed_image_if_empty('brand_image', (string) ($raw['image'] ?? ''), $post_id);
        justccell_acf_seed_image_if_empty('brand_image_mobile', (string) ($raw['image_mobile'] ?? ''), $post_id);
        justccell_acf_seed_image_if_empty('why_intro_image', (string) ($raw['intro_image'] ?? ''), $post_id);

        $stats = get_field('why_stats', $post_id);
        if ((!is_array($stats) || $stats === []) && !empty($raw['stats']) && is_array($raw['stats'])) {
            update_field('why_stats', $raw['stats'], $post_id);
        }

        $rows = get_field('why_rows', $post_id);
        if (!is_array($rows) || $rows === []) {
            $block_rows = [];
            foreach ((array) ($raw['blocks'] ?? []) as $block) {
                if (!is_array($block)) {
                    continue;
                }
                $img = (string) ($block['image'] ?? '');
                $img_id = 0;
                if ($img !== '') {
                    justccell_ensure_media_url($img);
                    $img_id = justccell_media_id($img);
                }
                $block_rows[] = [
                    'title'     => (string) ($block['title'] ?? ''),
                    'title_tag' => 'h3',
                    'kicker'    => (string) ($block['kicker'] ?? ''),
                    'copy'      => (string) ($block['copy'] ?? ''),
                    'image'     => $img_id > 0 ? $img_id : '',
                ];
            }
            if ($block_rows !== []) {
                update_field('why_rows', $block_rows, $post_id);
            }
        }

        $compare = is_array($raw['compare'] ?? null) ? $raw['compare'] : null;
        if (is_array($compare)) {
            justccell_acf_seed_text_if_empty('brand_compare_left_title', (string) ($compare['left']['title'] ?? ''), $post_id);
            justccell_acf_seed_text_if_empty('brand_compare_right_title', (string) ($compare['right']['title'] ?? ''), $post_id);
            $left_existing = get_field('brand_compare_left_items', $post_id);
            if (!is_array($left_existing) || $left_existing === []) {
                $left = [];
                foreach ((array) ($compare['left']['items'] ?? []) as $item) {
                    $left[] = ['item' => (string) $item];
                }
                if ($left !== []) {
                    update_field('brand_compare_left_items', $left, $post_id);
                }
            }
            $right_existing = get_field('brand_compare_right_items', $post_id);
            if (!is_array($right_existing) || $right_existing === []) {
                $right = [];
                foreach ((array) ($compare['right']['items'] ?? []) as $item) {
                    $right[] = ['item' => (string) $item];
                }
                if ($right !== []) {
                    update_field('brand_compare_right_items', $right, $post_id);
                }
            }
        }
    }

    update_option('justccell_why_acf_seeded', $seed_ver, false);
}

function justccell_legal_seed_pages_content(): void
{
    $seed_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
    if (get_option('justccell_legal_content_seeded') === $seed_ver) {
        return;
    }

    $static = justccell_static_pages();
    foreach (justccell_legal_page_slugs() as $slug) {
        $page = get_page_by_path($slug);
        if (!$page instanceof WP_Post) {
            continue;
        }
        $content = trim((string) $page->post_content);
        if ($content !== '') {
            continue;
        }
        $raw = $static[$slug] ?? [];
        if ($raw === []) {
            continue;
        }
        $parts = [];
        if ((string) ($raw['lede'] ?? '') !== '') {
            $parts[] = '<p>' . esc_html((string) $raw['lede']) . '</p>';
        }
        foreach ((array) ($raw['blocks'] ?? []) as $block) {
            if (!is_array($block)) {
                continue;
            }
            $title = (string) ($block['title'] ?? '');
            $copy  = (string) ($block['copy'] ?? '');
            if ($title !== '') {
                $parts[] = '<h2>' . esc_html($title) . '</h2>';
            }
            if ($copy !== '') {
                $parts[] = '<p>' . esc_html($copy) . '</p>';
            }
        }
        if ($parts === []) {
            continue;
        }
        wp_update_post([
            'ID'           => (int) $page->ID,
            'post_content' => implode("\n\n", $parts),
        ]);
    }

    update_option('justccell_legal_content_seeded', $seed_ver, false);
}

/**
 * @return list<array{name:string,url:string,image_id:int,image:string}>
 */
function justccell_contact_distributor_rows(int $post_id): array
{
    $rows = [];
    if ($post_id < 1 || !function_exists('get_field')) {
        return $rows;
    }
    foreach ((array) get_field('contact_distributors', $post_id) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $name = trim((string) ($row['name'] ?? ''));
        $url  = esc_url_raw((string) ($row['url'] ?? ''));
        if ($name === '') {
            continue;
        }
        $image_id = function_exists('justccell_acf_to_attachment_id')
            ? justccell_acf_to_attachment_id($row['image'] ?? 0)
            : 0;
        $rows[] = [
            'name'     => $name,
            'url'      => $url !== '' ? $url : '#',
            'image_id' => $image_id,
            'image'    => '',
        ];
    }
    return $rows;
}

add_action('init', 'justccell_about_seed_page_acf_content', 23);
add_action('init', 'justccell_why_seed_pages_acf_content', 23);
add_action('init', 'justccell_legal_seed_pages_content', 24);
