<?php
/**
 * Content getters: ACF / Woo first, PHP clone arrays as fallback until import.
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
 * @return array<string, mixed>
 */
function justccell_get_brand_page_content(?int $post_id = null): array
{
    $post_id = $post_id ?? (int) get_queried_object_id();
    $slug    = (string) get_post_field('post_name', $post_id);
    $fallback = justccell_static_pages()[$slug] ?? [];

    if (!function_exists('get_field') || $post_id < 1) {
        return justccell_normalize_brand_content($fallback, $slug);
    }

    $title = (string) get_field('brand_title', $post_id);
    if ($title === '' && empty($fallback)) {
        return [];
    }

    $sections = [];
    $rows = get_field('brand_sections', $post_id);
    if (is_array($rows) && $rows !== []) {
        foreach ($rows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $sections[] = [
                'id'        => (string) ($row['id'] ?? ''),
                'title'     => (string) ($row['title'] ?? ''),
                'title_tag' => (string) ($row['title_tag'] ?? 'h2'),
                'copy'      => (string) ($row['copy'] ?? ''),
            ];
        }
    }

    $blocks = [];
    $brows = get_field('brand_blocks', $post_id);
    if (is_array($brows) && $brows !== []) {
        foreach ($brows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $img = $row['image'] ?? '';
            $img_id = justccell_acf_to_attachment_id($img);
            $blocks[] = [
                'title'     => (string) ($row['title'] ?? ''),
                'title_tag' => (string) ($row['title_tag'] ?? 'h2'),
                'kicker'    => (string) ($row['kicker'] ?? ''),
                'copy'      => (string) ($row['copy'] ?? ''),
                'image_id'  => $img_id,
                'image_key' => '',
            ];
        }
    }

    $cards = [];
    $crows = get_field('brand_cards', $post_id);
    if (is_array($crows) && $crows !== []) {
        foreach ($crows as $row) {
            if (!is_array($row)) {
                continue;
            }
            $img = $row['image'] ?? '';
            $cards[] = [
                'title'      => (string) ($row['title'] ?? ''),
                'title_tag'  => (string) ($row['title_tag'] ?? 'h2'),
                'copy'       => (string) ($row['copy'] ?? ''),
                'url'        => (string) ($row['url'] ?? '/'),
                'more_label' => (string) ($row['more_label'] ?? ''),
                'image_id'   => justccell_acf_to_attachment_id($img),
                'image_key'  => '',
            ];
        }
    }

    $timeline = [];
    $timeline_years = [];
    $trows = get_field('brand_timeline', $post_id);
    if (is_array($trows) && $trows !== []) {
        foreach ($trows as $row) {
            if (!is_array($row) || ($row['item'] ?? '') === '') {
                continue;
            }
            $item = (string) $row['item'];
            $year = trim((string) ($row['year'] ?? ''));
            $timeline[] = $item;
            if ($year !== '') {
                if (!isset($timeline_years[$year])) {
                    $timeline_years[$year] = ['year' => $year, 'items' => []];
                }
                foreach (preg_split('/\r\n|\r|\n/', $item) ?: [] as $line) {
                    $line = trim($line);
                    if ($line !== '') {
                        $timeline_years[$year]['items'][] = $line;
                    }
                }
            }
        }
        $timeline_years = array_values($timeline_years);
    }

    $is_why     = function_exists('justccell_is_why_page_slug') && justccell_is_why_page_slug($slug);
    $is_about   = $slug === 'about';
    $is_generic = function_exists('justccell_generic_brand_page_slugs')
        && in_array($slug, justccell_generic_brand_page_slugs(), true);

    $why_rows = [];
    if ($is_why) {
        foreach ((array) get_field('why_rows', $post_id) as $row) {
            if (!is_array($row) || trim((string) ($row['title'] ?? '') . (string) ($row['copy'] ?? '')) === '') {
                continue;
            }
            $img = $row['image'] ?? '';
            $img_id = justccell_acf_to_attachment_id($img);
            $why_rows[] = [
                'title'     => (string) ($row['title'] ?? ''),
                'title_tag' => (string) ($row['title_tag'] ?? 'h3'),
                'kicker'    => (string) ($row['kicker'] ?? ''),
                'copy'      => (string) ($row['copy'] ?? ''),
                'image_id'  => $img_id,
                'image_key' => '',
            ];
        }
        if ($why_rows !== []) {
            $blocks = $why_rows;
        } else {
            $fb_blocks = justccell_normalize_brand_blocks($fallback['blocks'] ?? []);
            $has_images = false;
            foreach ($blocks as $block) {
                if ((int) ($block['image_id'] ?? 0) > 0 || (string) ($block['image_key'] ?? '') !== '') {
                    $has_images = true;
                    break;
                }
            }
            if (!$has_images && $fb_blocks !== []) {
                $blocks = $fb_blocks;
            }
        }
    }

    $left_items = [];
    foreach ((array) get_field('brand_compare_left_items', $post_id) as $row) {
        if (is_array($row) && ($row['item'] ?? '') !== '') {
            $left_items[] = (string) $row['item'];
        }
    }
    $right_items = [];
    foreach ((array) get_field('brand_compare_right_items', $post_id) as $row) {
        if (is_array($row) && ($row['item'] ?? '') !== '') {
            $right_items[] = (string) $row['item'];
        }
    }

    $img_id = justccell_acf_to_attachment_id(get_field('brand_image', $post_id));
    $img_mobile_id = justccell_acf_to_attachment_id(get_field('brand_image_mobile', $post_id));
    $culture = $is_about ? justccell_normalize_brand_media_cards(get_field('brand_culture', $post_id)) : [];
    $customer = $is_about ? justccell_normalize_brand_media_cards(get_field('brand_customer', $post_id)) : [];

    $company_id = $is_about ? justccell_acf_to_attachment_id(get_field('about_company_image', $post_id)) : 0;
    $company_copy = $is_about ? trim((string) get_field('about_company_copy', $post_id)) : '';
    if ($is_about && $company_copy === '') {
        foreach ($sections as $section) {
            if (($section['id'] ?? '') === 'company-introduction') {
                $company_copy = (string) ($section['copy'] ?? '');
                break;
            }
        }
        if ($company_copy === '') {
            foreach (justccell_normalize_brand_sections($fallback['sections'] ?? []) as $section) {
                if (($section['id'] ?? '') === 'company-introduction') {
                    $company_copy = (string) ($section['copy'] ?? '');
                    break;
                }
            }
        }
    }

    $intro_id = $is_why ? justccell_acf_to_attachment_id(get_field('why_intro_image', $post_id)) : 0;
    $layout = $is_why ? (string) get_field('why_layout', $post_id) : '';
    if ($is_why && $layout === '') {
        $layout = (string) ($fallback['layout'] ?? '');
    }
    $meet = $is_why ? trim((string) get_field('why_meet_heading', $post_id)) : '';
    if ($is_why && $meet === '') {
        $meet = (string) ($fallback['meet_heading'] ?? '');
    }
    $stats = [];
    if ($is_why) {
        foreach ((array) get_field('why_stats', $post_id) as $row) {
            if (!is_array($row) || trim((string) ($row['value'] ?? '')) === '') {
                continue;
            }
            $stats[] = [
                'value' => (string) ($row['value'] ?? ''),
                'unit'  => (string) ($row['unit'] ?? ''),
                'label' => (string) ($row['label'] ?? ''),
            ];
        }
        if ($stats === [] && is_array($fallback['stats'] ?? null)) {
            $stats = $fallback['stats'];
        }
    }

    if ($title === '' && $sections === [] && $blocks === [] && $cards === [] && $culture === [] && $why_rows === []) {
        return justccell_normalize_brand_content($fallback, $slug);
    }

    $compare = null;
    $left_title = (string) get_field('brand_compare_left_title', $post_id);
    $right_title = (string) get_field('brand_compare_right_title', $post_id);
    if ($is_why && ($left_title !== '' || $right_title !== '' || $left_items !== [] || $right_items !== [])) {
        $compare = [
            'left'  => [
                'title'     => $left_title,
                'title_tag' => (string) (get_field('brand_compare_left_title_tag', $post_id) ?: 'h3'),
                'items'     => $left_items,
            ],
            'right' => [
                'title'     => $right_title,
                'title_tag' => (string) (get_field('brand_compare_right_title_tag', $post_id) ?: 'h3'),
                'items'     => $right_items,
            ],
        ];
    }

    $cta_title = $is_generic ? trim((string) get_field('brand_cta_title', $post_id)) : '';
    $cta_copy  = $is_generic ? trim((string) get_field('brand_cta_copy', $post_id)) : '';
    $cta_label = $is_generic ? trim((string) get_field('brand_cta_label', $post_id)) : '';
    $cta_url_raw = $is_generic ? trim((string) get_field('brand_cta_url', $post_id)) : '';
    if ($slug === 'laser-engraving') {
        $cta_title = trim((string) get_field('brand_cta_title', $post_id));
        $cta_copy  = trim((string) get_field('brand_cta_copy', $post_id));
        $cta_label = trim((string) get_field('brand_cta_label', $post_id));
        $cta_url_raw = trim((string) get_field('brand_cta_url', $post_id));
    }

    $laser_layout = $slug === 'laser-engraving'
        ? justccell_brand_laser_layout_fields($post_id, $fallback)
        : [];

    return [
        'kicker'            => (string) (get_field('brand_kicker', $post_id) ?: ($fallback['kicker'] ?? '')),
        'title'             => $title !== '' ? $title : (string) ($fallback['title'] ?? get_the_title($post_id)),
        'title_tag'         => (string) (get_field('brand_title_tag', $post_id) ?: 'h1'),
        'lede'              => (string) (get_field('brand_lede', $post_id) ?: ($fallback['lede'] ?? '')),
        'tagline'           => (string) (get_field('brand_tagline', $post_id) ?: ($fallback['tagline'] ?? '')),
        'image_id'          => $img_id,
        'image_key'         => $img_id > 0 ? '' : (string) ($fallback['image'] ?? ''),
        'image_mobile_id'   => $img_mobile_id,
        'image_mobile_key'  => $img_mobile_id > 0 ? '' : (string) ($fallback['image_mobile'] ?? ''),
        'company_image_id'  => $company_id,
        'company_image_key' => $company_id > 0 ? '' : (string) ($fallback['company_image'] ?? ''),
        'company_copy'      => $company_copy,
                'heading_culture'       => $is_about ? (string) (get_field('about_heading_culture', $post_id) ?: __('Corporate Culture', 'justccell')) : '',
        'heading_culture_tag'   => $is_about ? (string) (get_field('about_heading_culture_tag', $post_id) ?: 'h2') : 'h2',
        'heading_company'       => $is_about ? (string) (get_field('about_heading_company', $post_id) ?: __('Company Introduction', 'justccell')) : '',
        'heading_company_tag'   => $is_about ? (string) (get_field('about_heading_company_tag', $post_id) ?: 'h2') : 'h2',
        'heading_history'       => $is_about ? (string) (get_field('about_heading_history', $post_id) ?: __('Brand History', 'justccell')) : '',
        'heading_history_tag'   => $is_about ? (string) (get_field('about_heading_history_tag', $post_id) ?: 'h2') : 'h2',
        'heading_customer'      => $is_about ? (string) (get_field('about_heading_customer', $post_id) ?: __('Customer Centricity', 'justccell')) : '',
        'heading_customer_tag'  => $is_about ? (string) (get_field('about_heading_customer_tag', $post_id) ?: 'h2') : 'h2',
        'intro_image_id'    => $intro_id,
        'intro_image_key'   => $intro_id > 0 ? '' : (string) ($fallback['intro_image'] ?? ''),
        'layout'            => $layout,
        'meet_heading'          => $meet,
        'meet_heading_tag'      => $is_why ? (string) (get_field('why_meet_heading_tag', $post_id) ?: 'h2') : 'h2',
        'stats'             => $stats,
        'sections'          => $is_generic && $sections !== [] ? $sections : ($is_generic ? justccell_normalize_brand_sections($fallback['sections'] ?? []) : $sections),
        'blocks'            => $blocks !== [] ? $blocks : ($is_about ? [] : justccell_normalize_brand_blocks($fallback['blocks'] ?? [])),
        'culture'           => $culture !== [] ? $culture : ($is_about ? justccell_normalize_brand_media_cards($fallback['culture'] ?? []) : []),
        'customer'          => $customer !== [] ? $customer : ($is_about ? justccell_normalize_brand_media_cards($fallback['customer'] ?? []) : []),
        'compare'           => $is_why ? ($compare ?? ($fallback['compare'] ?? null)) : null,
        'compare_heading'       => $is_why ? (string) (get_field('why_compare_heading', $post_id) ?: __('What’s Different', 'justccell')) : '',
        'compare_heading_tag'   => $is_why ? (string) (get_field('why_compare_heading_tag', $post_id) ?: 'h2') : 'h2',
        'cards'             => $is_generic ? ($cards !== [] ? $cards : justccell_normalize_brand_cards($fallback['cards'] ?? [])) : [],
        'timeline'          => $is_about ? ($timeline !== [] ? $timeline : array_values((array) ($fallback['timeline'] ?? []))) : [],
        'timeline_years'    => $is_about ? ($timeline_years !== [] ? $timeline_years : justccell_normalize_brand_timeline_years($fallback['timeline_years'] ?? [])) : [],
        'cta_title'         => $cta_title,
        'cta_title_tag'     => $is_generic ? (string) (get_field('brand_cta_title_tag', $post_id) ?: 'h2') : 'h2',
        'cta_copy'          => $cta_copy,
        'cta_label'         => $cta_label,
        'cta_url'           => $cta_url_raw,
        'video_heading'         => $is_generic ? (string) (get_field('brand_video_heading', $post_id) ?: ($fallback['video_heading'] ?? '')) : '',
        'video_heading_tag'     => $is_generic ? (string) (get_field('brand_video_heading_tag', $post_id) ?: 'h2') : 'h2',
        'video_copy'        => $is_generic ? (string) (get_field('brand_video_copy', $post_id) ?: ($fallback['video_copy'] ?? '')) : '',
        'video_url'         => $is_generic ? justccell_brand_video_url($post_id, $fallback) : '',
    ] + $laser_layout;
}

/**
 * @param array<string, mixed> $raw
 * @return array<string, mixed>
 */
function justccell_normalize_brand_content(array $raw, string $slug): array
{
    if ($raw === []) {
        return [];
    }
    $img_key = (string) ($raw['image'] ?? '');
    return [
        'kicker'            => (string) ($raw['kicker'] ?? ''),
        'title'             => (string) ($raw['title'] ?? ''),
        'title_tag'         => 'h1',
        'lede'              => (string) ($raw['lede'] ?? ''),
        'tagline'           => (string) ($raw['tagline'] ?? ''),
        'image_id'          => 0,
        'image_key'         => $img_key,
        'image_mobile_id'   => 0,
        'image_mobile_key'  => (string) ($raw['image_mobile'] ?? ''),
        'company_image_id'  => 0,
        'company_image_key' => (string) ($raw['company_image'] ?? ''),
        'company_copy'      => '',
        'heading_culture'       => __('Corporate Culture', 'justccell'),
        'heading_culture_tag'   => 'h2',
        'heading_company'       => __('Company Introduction', 'justccell'),
        'heading_company_tag'   => 'h2',
        'heading_history'       => __('Brand History', 'justccell'),
        'heading_history_tag'   => 'h2',
        'heading_customer'      => __('Customer Centricity', 'justccell'),
        'heading_customer_tag'  => 'h2',
        'intro_image_id'    => 0,
        'intro_image_key'   => (string) ($raw['intro_image'] ?? ''),
        'layout'            => (string) ($raw['layout'] ?? ''),
        'meet_heading'          => (string) ($raw['meet_heading'] ?? ''),
        'meet_heading_tag'      => 'h2',
        'stats'             => is_array($raw['stats'] ?? null) ? $raw['stats'] : [],
        'sections'          => justccell_normalize_brand_sections($raw['sections'] ?? []),
        'blocks'            => justccell_normalize_brand_blocks($raw['blocks'] ?? []),
        'culture'           => justccell_normalize_brand_media_cards($raw['culture'] ?? []),
        'customer'          => justccell_normalize_brand_media_cards($raw['customer'] ?? []),
        'compare'           => $raw['compare'] ?? null,
        'compare_heading'       => (string) ($raw['compare_heading'] ?? __('What’s Different', 'justccell')),
        'compare_heading_tag'   => 'h2',
        'cards'             => justccell_normalize_brand_cards($raw['cards'] ?? []),
        'timeline'          => array_values((array) ($raw['timeline'] ?? [])),
        'timeline_years'    => justccell_normalize_brand_timeline_years($raw['timeline_years'] ?? []),
        'cta_title'         => '',
        'cta_title_tag'     => 'h2',
        'cta_copy'          => '',
        'cta_label'         => '',
        'cta_url'           => '',
        'video_heading'         => (string) ($raw['video_heading'] ?? ''),
        'video_heading_tag'     => 'h2',
        'video_copy'        => (string) ($raw['video_copy'] ?? ''),
        'video_url'         => justccell_brand_video_url(0, $raw),
    ] + ($slug === 'laser-engraving' ? justccell_brand_laser_layout_fields(0, $raw) : []);
}

/**
 * Laser engraving layout fields (intro buttons + section headings).
 *
 * @param array<string, mixed> $fallback
 * @return array<string, string>
 */
function justccell_brand_laser_layout_fields(int $post_id, array $fallback): array
{
    $text = static function (string $field, string $fallback_key, string $default) use ($post_id, $fallback): string {
        if ($post_id > 0 && function_exists('get_field')) {
            $val = get_field($field, $post_id);
            if (is_string($val) && trim($val) !== '') {
                return trim($val);
            }
        }
        $from_fallback = trim((string) ($fallback[$fallback_key] ?? ''));
        if ($from_fallback !== '') {
            return $from_fallback;
        }
        return $default;
    };

    return [
        'intro_primary_label'   => $text('brand_intro_primary_label', 'intro_primary_label', __('Contact us', 'justccell')),
        'intro_primary_url'     => $text('brand_intro_primary_url', 'intro_primary_url', '/contact/'),
        'intro_secondary_label' => $text('brand_intro_secondary_label', 'intro_secondary_label', __('Packaging', 'justccell')),
        'intro_secondary_url'   => $text('brand_intro_secondary_url', 'intro_secondary_url', '/packaging/'),
        'steps_heading'         => $text('brand_steps_heading', 'steps_heading', __('How to brief us', 'justccell')),
        'steps_heading_tag'     => $text('brand_steps_heading_tag', 'steps_heading_tag', 'h2'),
        'steps_lede'            => $text('brand_steps_lede', 'steps_lede', __('Artwork, colourway, and quantity sit on the same enquiry as the hardware. We proof a small batch before a production run.', 'justccell')),
        'hardware_heading'      => $text('brand_hardware_heading', 'hardware_heading', __('Hardware we mark', 'justccell')),
        'hardware_heading_tag'  => $text('brand_hardware_heading_tag', 'hardware_heading_tag', 'h2'),
        'hardware_lede'         => $text('brand_hardware_lede', 'hardware_lede', __('Logos and micro text go on batteries, pods, and selected all-in-ones. Open a product to add engraving to your order.', 'justccell')),
    ];
}

/**
 * Resolve brand footer CTA link (path or absolute URL).
 */
function justccell_brand_cta_url(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') {
        return function_exists('justccell_contact_page_url') ? justccell_contact_page_url() : home_url('/contact/');
    }
    if (preg_match('#^https?://#i', $raw) === 1) {
        return $raw;
    }
    return home_url($raw);
}

/**
 * @param array<string, mixed> $fallback
 */
function justccell_brand_video_url(int $post_id, array $fallback = []): string
{
    if ($post_id > 0 && function_exists('justccell_acf_file_url') && function_exists('get_field')) {
        $url = justccell_acf_file_url(get_field('brand_video', $post_id));
        if ($url !== '') {
            return $url;
        }
    }
    $key = (string) ($fallback['video'] ?? '');
    if ($key !== '' && function_exists('justccell_ensure_media_url')) {
        $sideloaded = justccell_ensure_media_url($key);
        if ($sideloaded !== '') {
            return $sideloaded;
        }
    }
    if ($key === 'laser-engraving.mp4' && function_exists('justccell_laser_video_url')) {
        return justccell_laser_video_url();
    }
    return '';
}

/**
 * @param mixed $sections
 * @return list<array{id:string,title:string,title_tag:string,copy:string}>
 */
function justccell_normalize_brand_sections($sections): array
{
    $out = [];
    if (!is_array($sections)) {
        return $out;
    }
    foreach ($sections as $section) {
        if (!is_array($section)) {
            continue;
        }
        $out[] = [
            'id'        => (string) ($section['id'] ?? ''),
            'title'     => (string) ($section['title'] ?? ''),
            'title_tag' => (string) ($section['title_tag'] ?? 'h2'),
            'copy'      => (string) ($section['copy'] ?? ''),
        ];
    }
    return $out;
}

/**
 * @param mixed $blocks
 * @return list<array{title:string,title_tag:string,copy:string}>
 */
function justccell_normalize_brand_blocks($blocks): array
{
    $out = [];
    if (!is_array($blocks)) {
        return $out;
    }
    foreach ($blocks as $block) {
        if (!is_array($block)) {
            continue;
        }
        $img = $block['image'] ?? '';
        $img_id = justccell_acf_to_attachment_id($img);
        $img_key = ($img_id > 0 || is_array($img) || is_numeric($img)) ? '' : (string) $img;
        $out[] = [
            'title'     => (string) ($block['title'] ?? ''),
            'title_tag' => (string) ($block['title_tag'] ?? 'h2'),
            'kicker'    => (string) ($block['kicker'] ?? ''),
            'copy'      => (string) ($block['copy'] ?? ''),
            'image_id'  => $img_id,
            'image_key' => $img_key,
        ];
    }
    return $out;
}

/**
 * @param mixed $cards
 * @return list<array{title:string,title_tag:string,copy:string,image_id:int,image_key:string}>
 */
function justccell_normalize_brand_media_cards($cards): array
{
    $out = [];
    if (!is_array($cards)) {
        return $out;
    }
    foreach ($cards as $card) {
        if (!is_array($card)) {
            continue;
        }
        $img = $card['image'] ?? '';
        $img_id = justccell_acf_to_attachment_id($img);
        $img_key = ($img_id > 0 || is_array($img) || is_numeric($img)) ? '' : (string) $img;
        $out[] = [
            'title'     => (string) ($card['title'] ?? ''),
            'title_tag' => (string) ($card['title_tag'] ?? 'h3'),
            'copy'      => (string) ($card['copy'] ?? ''),
            'image_id'  => $img_id,
            'image_key' => $img_key,
        ];
    }
    return $out;
}

/**
 * @param mixed $years
 * @return list<array{year:string,items:list<string>}>
 */
function justccell_normalize_brand_timeline_years($years): array
{
    $out = [];
    if (!is_array($years)) {
        return $out;
    }
    foreach ($years as $row) {
        if (!is_array($row)) {
            continue;
        }
        $items = [];
        foreach ((array) ($row['items'] ?? []) as $item) {
            $item = trim((string) $item);
            if ($item !== '') {
                $items[] = $item;
            }
        }
        $year = trim((string) ($row['year'] ?? ''));
        if ($year === '' && $items === []) {
            continue;
        }
        $out[] = [
            'year'  => $year,
            'items' => $items,
        ];
    }
    return $out;
}

/**
 * @param mixed $cards
 * @return list<array{title:string,title_tag:string,copy:string,url:string}>
 */
function justccell_normalize_brand_cards($cards): array
{
    $out = [];
    if (!is_array($cards)) {
        return $out;
    }
    foreach ($cards as $card) {
        if (!is_array($card)) {
            continue;
        }
        $img = $card['image'] ?? '';
        $img_id = justccell_acf_to_attachment_id($img);
        $img_key = ($img_id > 0 || is_array($img) || is_numeric($img)) ? '' : (string) $img;
        $out[] = [
            'title'      => (string) ($card['title'] ?? ''),
            'title_tag'  => (string) ($card['title_tag'] ?? 'h2'),
            'copy'       => (string) ($card['copy'] ?? ''),
            'url'        => (string) ($card['url'] ?? '/'),
            'more_label' => (string) ($card['more_label'] ?? ''),
            'image_id'   => $img_id,
            'image_key'  => $img_key,
        ];
    }
    return $out;
}

/**
 * Homepage editable content.
 *
 * @return array<string, mixed>
 */
function justccell_get_home_content(): array
{
    $front = function_exists('justccell_home_content_page_id')
        ? justccell_home_content_page_id()
        : (int) get_option('page_on_front');
    $keys  = justccell_home_asset_keys();

    $defaults = justccell_home_page_text_defaults();

    if ($front < 1 || !function_exists('get_field')) {
        return justccell_home_content_from_keys($defaults, $keys);
    }

    $gallery = get_field('home_custom_images', $front);
    $custom_ids = [];
    if (is_array($gallery)) {
        foreach ($gallery as $img) {
            $id = justccell_acf_to_attachment_id($img);
            if ($id > 0) {
                $custom_ids[] = $id;
            }
        }
    }

    return [
        'devices_heading'     => (string) (get_field('home_devices_heading', $front) ?: $defaults['devices_heading']),
        'devices_heading_tag' => (string) (get_field('home_devices_heading_tag', $front) ?: 'h1'),
        'custom_heading'      => (string) (get_field('home_custom_heading', $front) ?: $defaults['custom_heading']),
        'custom_heading_tag'  => (string) (get_field('home_custom_heading_tag', $front) ?: 'h2'),
        'custom_kicker'       => (string) (get_field('home_custom_kicker', $front) ?: $defaults['custom_kicker']),
        'custom_copy'         => (string) (get_field('home_custom_copy', $front) ?: $defaults['custom_copy']),
        'custom_image_ids'    => $custom_ids,
        'custom_image_keys'   => $custom_ids === [] ? ['cust1', 'cust2', 'cust3', 'cust4'] : [],
        'premium_image_id'    => justccell_acf_to_attachment_id(get_field('home_premium_image', $front)),
        'premium_image_key'   => 'premium',
        'premium_heading'     => (string) (get_field('home_premium_heading', $front) ?: $defaults['premium_heading']),
        'premium_heading_tag' => (string) (get_field('home_premium_heading_tag', $front) ?: 'h3'),
        'premium_copy'        => (string) (get_field('home_premium_copy', $front) ?: $defaults['premium_copy']),
        'fill_heading'        => (string) (get_field('home_fill_heading', $front) ?: $defaults['fill_heading']),
        'fill_heading_tag'    => (string) (get_field('home_fill_heading_tag', $front) ?: 'h2'),
        'fill_copy'           => (string) (get_field('home_fill_copy', $front) ?: $defaults['fill_copy']),
        'fill_image_id'       => justccell_acf_to_attachment_id(get_field('home_fill_image', $front)),
        'fill_image_key'      => 'fill',
        'fill_link_label'     => (string) (get_field('home_fill_link_label', $front) ?: $defaults['fill_link_label']),
        'fill_link_url'       => (string) (get_field('home_fill_link_url', $front) ?: $defaults['fill_link_url']),
        'trusted_heading'     => (string) (get_field('home_trusted_heading', $front) ?: $defaults['trusted_heading']),
        'trusted_heading_tag' => (string) (get_field('home_trusted_heading_tag', $front) ?: 'h2'),
        'trusted_image_id'    => justccell_acf_to_attachment_id(get_field('home_trusted_image', $front)),
        'trusted_image_key'   => 'trusted',
        'arrow_id'            => justccell_acf_to_attachment_id(get_field('home_arrow_image', $front)),
        'arrow_key'           => 'arrow',
        'tab_all_in_ones'     => (string) (get_field('home_tab_all_in_ones', $front) ?: $defaults['tab_all_in_ones']),
        'tab_cartridge'       => (string) (get_field('home_tab_cartridge', $front) ?: $defaults['tab_cartridge']),
        'tab_pod_system'      => (string) (get_field('home_tab_pod_system', $front) ?: $defaults['tab_pod_system']),
        'tab_battery'         => (string) (get_field('home_tab_battery', $front) ?: $defaults['tab_battery']),
        'asset_keys'          => $keys,
    ];
}

/**
 * @param array<string, mixed> $defaults
 * @param array<string, string|list<string>> $keys
 * @return array<string, mixed>
 */
function justccell_home_content_from_keys(array $defaults, array $keys): array
{
    return array_merge($defaults, [
        'custom_image_ids'  => [],
        'custom_image_keys' => ['cust1', 'cust2', 'cust3', 'cust4'],
        'premium_image_id'  => 0,
        'premium_image_key' => 'premium',
        'fill_image_id'     => 0,
        'fill_image_key'    => 'fill',
        'trusted_image_id'  => 0,
        'trusted_image_key' => 'trusted',
        'arrow_id'          => 0,
        'arrow_key'         => 'arrow',
        'asset_keys'        => $keys,
    ]);
}

/**
 * Echo an image from attachment ID, else media key via asset map.
 *
 * @param array<string, string|list<string>> $keys
 * @param array<string, mixed> $attrs
 */
function justccell_echo_home_image(int $id, string $key_slot, array $keys, array $attrs = []): void
{
    if ($id > 0) {
        echo wp_get_attachment_image($id, 'full', false, $attrs);
        return;
    }
    $file = (string) ($keys[$key_slot] ?? '');
    echo justccell_media_img($file, $attrs);
}

/**
 * Contact page content.
 *
 * @return array<string, mixed>
 */
function justccell_get_contact_content(?int $post_id = null): array
{
    $post_id = $post_id ?? (int) get_queried_object_id();
    $faqs_fallback = justccell_contact_faqs();

    $defaults = [
        'kicker'                 => __('Contact', 'justccell'),
        'title'                  => __('Contact us', 'justccell'),
        'title_tag'              => 'h1',
        'lede'                   => __('Tell us about your extracts, hardware line, and market. A Justccell representative will follow up within one business day.', 'justccell'),
        'distributors_heading'   => __('Distributors', 'justccell'),
        'distributors_copy'      => __('Looking to carry Justccell hardware? Include your region and channel in the message and we will route you to the right team.', 'justccell'),
        'faqs'                   => $faqs_fallback,
    ];

    if ($post_id < 1 || !function_exists('get_field')) {
        return $defaults;
    }

    $title = (string) get_field('contact_title', $post_id);
    $faqs  = [];
    foreach ((array) get_field('contact_faq', $post_id) as $row) {
        if (is_array($row) && ($row['q'] ?? '') !== '') {
            $faqs[] = ['q' => (string) $row['q'], 'a' => (string) ($row['a'] ?? '')];
        }
    }
    if (function_exists('justccell_contact_faqs_without_samples')) {
        $faqs = justccell_contact_faqs_without_samples($faqs);
        $faqs_fallback = justccell_contact_faqs_without_samples($faqs_fallback);
    }

    if ($title === '' && $faqs === []) {
        return $defaults;
    }

    return [
        'kicker'               => (string) (get_field('contact_kicker', $post_id) ?: $defaults['kicker']),
        'title'                => $title !== '' ? $title : $defaults['title'],
        'title_tag'            => (string) (get_field('contact_title_tag', $post_id) ?: 'h1'),
        'lede'                 => (string) (get_field('contact_lede', $post_id) ?: $defaults['lede']),
        'distributors_heading' => (string) (get_field('contact_distributors_heading', $post_id) ?: $defaults['distributors_heading']),
        'distributors_copy'    => (string) (get_field('contact_distributors_copy', $post_id) ?: $defaults['distributors_copy']),
        'faqs'                 => $faqs !== [] ? $faqs : $faqs_fallback,
    ];
}

/**
 * Woo product ID by catalog slug.
 */
function justccell_woo_product_id_by_slug(string $slug): int
{
    if ($slug === '' || !function_exists('wc_get_product_id_by_sku')) {
        return 0;
    }
    $by_sku = (int) wc_get_product_id_by_sku($slug);
    if ($by_sku > 0) {
        return $by_sku;
    }
    $posts = get_posts([
        'name'           => $slug,
        'post_type'      => 'product',
        'post_status'    => ['publish', 'draft', 'private'],
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ]);
    return isset($posts[0]) ? (int) $posts[0] : 0;
}

/**
 * Build product clone array from Woo + ACF when available.
 * Colour/combination pickers use WooCommerce attributes only — legacy `clone_colours` postmeta is ignored.
 *
 * @return array<string, mixed>|null
 */
function justccell_product_page_from_woo(string $slug): ?array
{
    $id = justccell_woo_product_id_by_slug($slug);
    if ($id < 1) {
        return null;
    }

    $product = wc_get_product($id);
    if (!$product instanceof WC_Product) {
        return null;
    }

    $acf = static function (string $name) use ($id) {
        return function_exists('get_field') ? get_field($name, $id) : null;
    };

    $thumb_id  = (int) $product->get_image_id();
    $banner_id = justccell_acf_to_attachment_id($acf('clone_banner'));
    if ($banner_id < 1) {
        $banner_id = $thumb_id;
    }

    $gallery_ids = [];
    if ($thumb_id > 0) {
        $gallery_ids[] = $thumb_id;
    }
    foreach ($product->get_gallery_image_ids() as $gid) {
        $gid = (int) $gid;
        if ($gid > 0 && !in_array($gid, $gallery_ids, true)) {
            $gallery_ids[] = $gid;
        }
    }

    $spin_ids = [];
    foreach ((array) $acf('clone_spin') as $img) {
        $sid = justccell_acf_to_attachment_id($img);
        if ($sid > 0) {
            $spin_ids[] = $sid;
        }
    }
    $detail_ids = [];
    foreach ((array) $acf('clone_details') as $img) {
        $did = justccell_acf_to_attachment_id($img);
        if ($did > 0) {
            $detail_ids[] = $did;
        }
    }

    $specs = [];
    foreach ((array) $acf('clone_specs') as $row) {
        if (is_array($row) && ($row['line'] ?? '') !== '') {
            $specs[] = (string) $row['line'];
        }
    }

    $features = [];
    foreach ((array) $acf('clone_features') as $row) {
        if (!is_array($row)) {
            continue;
        }
        $image_id = justccell_acf_to_attachment_id($row['image'] ?? 0);
        $title    = (string) ($row['title'] ?? '');
        $copy     = (string) ($row['copy'] ?? '');
        if ($image_id < 1 && trim($title . $copy) === '') {
            continue;
        }
        $features[] = [
            'title'      => $title,
            'title_tag'  => (string) ($row['title_tag'] ?? 'h2'),
            'copy'       => $copy,
            'note'       => (string) ($row['note'] ?? ''),
            'text_color' => justccell_normalize_highlight_text_color((string) ($row['text_color'] ?? 'black')),
            'image_id'   => $image_id,
            'image'      => '',
        ];
    }

    $subtitle = justccell_product_hero_line((string) $acf('clone_subtitle'));
    // Migrate retired Banner text → Product Tagline when subtitle is empty.
    if ($subtitle === '') {
        $subtitle = justccell_product_hero_line((string) $acf('clone_tagline'));
    }

    $product_heading = trim((string) $acf('clone_product_heading'));
    // Migrate retired Banner heading → Product heading when empty.
    if ($product_heading === '') {
        $product_heading = trim((string) $acf('clone_banner_heading'));
    }
    $woo_name        = $product->get_name();
    $specs_heading   = trim((string) $acf('clone_specs_heading'));
    if ($specs_heading === '' && $specs !== []) {
        $specs_heading = __('Specifications', 'justccell');
    }
    $description     = trim((string) $product->get_description());
    if ($description === '') {
        $description = trim((string) $product->get_short_description());
    }

    $card_id = $thumb_id > 0 ? $thumb_id : $banner_id;

    $cats     = wp_get_post_terms($id, 'product_cat', ['fields' => 'slugs']);
    $category = 'all-in-ones';
    if (is_array($cats)) {
        foreach ($cats as $cslug) {
            if (array_key_exists($cslug, justccell_product_category_labels())) {
                $category = $cslug;
                break;
            }
        }
    }

    $page = [
        'woo_id'           => $id,
        'slug'             => $slug,
        'name'             => $woo_name,
        'product_heading'  => $product_heading !== '' ? $product_heading : $woo_name,
        'category'         => $category,
        'subtitle'         => $subtitle,
        'banner_id'        => $banner_id,
        'banner'           => '',
        'gallery_ids'      => $gallery_ids,
        'gallery'          => [],
        'spin_ids'         => $spin_ids,
        'spin'             => [],
        'specs_heading'    => $specs_heading,
        'specs'            => $specs,
        'features'         => $features,
        'evomax_title'     => (string) $acf('clone_evomax_title'),
        'evomax_title_tag' => (string) ($acf('clone_evomax_title_tag') ?: 'h2'),
        'evomax_copy'      => (string) $acf('clone_evomax_copy'),
        'evomax_bg_id'     => justccell_acf_to_attachment_id($acf('clone_evomax_bg')),
        'evomax_bg'        => '',
        'details_ids'      => $detail_ids,
        'details'          => [],
        'card_tagline'     => (string) $acf('clone_card_tagline'),
        'card_capacity'    => (string) $acf('clone_card_capacity'),
        'card_image_id'    => $card_id,
        'oil_group'        => (string) $acf('clone_oil_group'),
        'mega_featured'    => (bool) $acf('clone_mega_featured'),
        'description'      => $description,
        'from_cms'         => true,
    ];

    return justccell_product_page_merge_pack_fallbacks($slug, $page);
}

/**
 * Fill missing feature / detail / heating media from the design pack when ACF
 * rows exist without images (common on imported SKUs with sparse Product page fields).
 *
 * @param array<string, mixed> $page
 * @return array<string, mixed>
 */
function justccell_product_page_merge_pack_fallbacks(string $slug, array $page): array
{
    if ($slug === '' || !function_exists('justccell_product_pages_data')) {
        return $page;
    }
    $pack = justccell_product_pages_data()[$slug] ?? null;
    if (!is_array($pack)) {
        return $page;
    }

    $pack_features = is_array($pack['features'] ?? null) ? $pack['features'] : [];
    $features      = is_array($page['features'] ?? null) ? $page['features'] : [];

    $missing_all_images = $features !== [];
    foreach ($features as $row) {
        if (!is_array($row)) {
            continue;
        }
        if ((int) ($row['image_id'] ?? 0) > 0 || (string) ($row['image'] ?? '') !== '') {
            $missing_all_images = false;
            break;
        }
    }

    // No slides, or slides with no photos → use the design-pack highlights (ccell parity).
    if (($features === [] || $missing_all_images) && $pack_features !== []) {
        $features = [];
        foreach ($pack_features as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = (string) ($row['image'] ?? '');
            $features[] = [
                'title'      => (string) ($row['title'] ?? ''),
                'title_tag'  => 'h2',
                'copy'       => (string) ($row['copy'] ?? ''),
                'note'       => (string) ($row['note'] ?? ''),
                'text_color' => justccell_normalize_highlight_text_color((string) ($row['text_color'] ?? 'black')),
                'image_id'   => $key !== '' && function_exists('justccell_media_id') ? justccell_media_id($key) : 0,
                'image'      => $key,
            ];
        }
    } else {
        foreach ($features as $i => $row) {
            if (!is_array($row)) {
                continue;
            }
            $id  = (int) ($row['image_id'] ?? 0);
            $key = (string) ($row['image'] ?? '');
            if ($id > 0 || $key !== '') {
                continue;
            }
            $pack_row = is_array($pack_features[$i] ?? null) ? $pack_features[$i] : null;
            if (!is_array($pack_row)) {
                continue;
            }
            $pkey = (string) ($pack_row['image'] ?? '');
            if ($pkey === '') {
                continue;
            }
            $features[$i]['image']    = $pkey;
            $features[$i]['image_id'] = function_exists('justccell_media_id') ? justccell_media_id($pkey) : 0;
        }
    }
    $page['features'] = $features;

    if ((string) ($page['banner'] ?? '') === '' && (int) ($page['banner_id'] ?? 0) < 1 && ($pack['banner'] ?? '') !== '') {
        $page['banner'] = (string) $pack['banner'];
    }

    if (($page['gallery_ids'] ?? []) === [] && ($page['gallery'] ?? []) === [] && is_array($pack['gallery'] ?? null)) {
        $page['gallery'] = array_values(array_map('strval', $pack['gallery']));
    }

    if ((string) ($page['evomax_bg'] ?? '') === '' && (int) ($page['evomax_bg_id'] ?? 0) < 1 && ($pack['evomax_bg'] ?? '') !== '') {
        $page['evomax_bg'] = (string) $pack['evomax_bg'];
    }
    if (trim((string) ($page['evomax_copy'] ?? '')) === '' && ($pack['evomax_copy'] ?? '') !== '') {
        $page['evomax_copy'] = (string) $pack['evomax_copy'];
    }
    if (trim((string) ($page['evomax_title'] ?? '')) === '' && ($pack['evomax_title'] ?? '') !== '') {
        $page['evomax_title'] = (string) $pack['evomax_title'];
    }

    if (($page['details_ids'] ?? []) === [] && ($page['details'] ?? []) === [] && is_array($pack['details'] ?? null)) {
        $page['details'] = array_values(array_map('strval', $pack['details']));
    }

    if (justccell_product_hero_line((string) ($page['subtitle'] ?? '')) === '') {
        if (($pack['subtitle'] ?? '') !== '') {
            $page['subtitle'] = justccell_product_hero_line((string) $pack['subtitle']);
        }
    }
    if (trim((string) ($page['specs_heading'] ?? '')) === '' && !empty($page['specs'])) {
        $page['specs_heading'] = __('Specifications', 'justccell');
    }

    return $page;
}

/**
 * Hero / accent lines only — never the Woo product description.
 */
function justccell_product_hero_line(string $text): string
{
    $text = trim(wp_strip_all_tags($text));
    if ($text === '') {
        return '';
    }
    $words = preg_split('/\s+/u', $text, -1, PREG_SPLIT_NO_EMPTY);
    if (!is_array($words) || $words === [] || count($words) > 24 || mb_strlen($text) > 180) {
        return '';
    }
    return $text;
}

/**
 * WooCommerce product description for the PDP story block (SEO).
 *
 * @return array{show:bool,needs_more:bool,teaser:string,full:string}
 */
function justccell_product_description_parts(string $html, int $word_limit = 300): array
{
    unset($word_limit);
    $html = trim($html);
    if ($html === '') {
        return ['show' => false, 'needs_more' => false, 'teaser' => '', 'full' => ''];
    }

    $full = wp_kses_post($html);
    $text = trim(preg_replace('/\s+/u', ' ', wp_strip_all_tags($full)) ?? '');
    if ($text === '') {
        return ['show' => false, 'needs_more' => false, 'teaser' => '', 'full' => ''];
    }

    return [
        'show'       => true,
        'needs_more' => false,
        'teaser'     => $full,
        'full'       => $full,
    ];
}

/**
 * Short specs for homepage / catalog cards (~2–3 lines, not full PDP lists).
 *
 * @param array{slug?:string,specs?:list<string>} $item
 * @return list<string>
 */
function justccell_catalog_card_specs(array $item, int $limit = 3): array
{
    $limit = max(1, $limit);
    $specs = array_values(array_filter(array_map('strval', $item['specs'] ?? [])));
    return array_slice($specs, 0, $limit);
}

/**
 * Catalog item list: Woo products when imported, else PHP.
 *
 * @return list<array{name:string,slug:string,category:string,image:string,image_id:int,specs:list<string>}>
 */
function justccell_catalog_from_woo(): array
{
    if (!taxonomy_exists('product_cat') || !function_exists('wc_get_products')) {
        return [];
    }
    $out = [];
    foreach (justccell_product_category_labels() as $cat => $_label) {
        $ids = wc_get_products([
            'status'   => 'publish',
            'limit'    => -1,
            'category' => [$cat],
            'return'   => 'ids',
            'orderby'  => 'menu_order',
            'order'    => 'ASC',
        ]);
        foreach ($ids as $pid) {
            $pid = (int) $pid;
            if (function_exists('justccell_product_in_storefront_category')
                && !justccell_product_in_storefront_category($pid, $cat)
            ) {
                continue;
            }
            $product = wc_get_product($pid);
            if (!$product instanceof WC_Product) {
                continue;
            }
            $slug = $product->get_slug() !== '' ? $product->get_slug() : $product->get_sku();
            $specs = [];
            if (function_exists('get_field')) {
                foreach ((array) get_field('clone_specs', (int) $pid) as $row) {
                    if (is_array($row) && ($row['line'] ?? '') !== '') {
                        $specs[] = (string) $row['line'];
                    }
                }
            }
            $thumb = (int) $product->get_image_id();
            $out[] = [
                'name'          => $product->get_name(),
                'slug'          => $slug,
                'category'      => $cat,
                'image'         => '',
                'image_id'      => $thumb,
                'specs'         => justccell_catalog_card_specs(['slug' => $slug, 'specs' => $specs], 3),
                'woo_id'        => (int) $pid,
                'mega_featured' => function_exists('get_field') && (bool) get_field('clone_mega_featured', (int) $pid),
                'menu_order'    => $product->get_menu_order(),
            ];
        }
    }
    return $out;
}
