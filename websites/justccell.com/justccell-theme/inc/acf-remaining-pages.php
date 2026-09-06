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
