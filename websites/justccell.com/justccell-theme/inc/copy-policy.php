<?php
/**
 * Client copy policy — no samples / no “request a quote” CTAs sitewide.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_contact_page_url(): string
{
    return home_url('/contact/');
}

/**
 * Phrases that must not appear in visitor-facing CTA labels or buttons.
 */
function justccell_banned_cta_phrases(): array
{
    return [
        'get samples',
        'samples & quotes',
        'samples and quotes',
        'sample & quote',
        'sample and quote',
        'request a quote',
        'request sample',
        'request sample & quote',
        'request sample and quote',
    ];
}

function justccell_text_has_banned_cta(string $text): bool
{
    $hay = strtolower(html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    if ($hay === '') {
        return false;
    }
    foreach (justccell_banned_cta_phrases() as $phrase) {
        if (str_contains($hay, $phrase)) {
            return true;
        }
    }
    return false;
}

function justccell_scrub_option_text(string $option_name, string $replacement = ''): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $current = trim((string) get_field($option_name, 'option'));
    if ($current !== '' && justccell_text_has_banned_cta($current)) {
        update_field($option_name, $replacement, 'option');
    }
}

function justccell_scrub_post_text(int $post_id, string $field, string $replacement = ''): void
{
    if (!function_exists('get_field') || !function_exists('update_field') || $post_id < 1) {
        return;
    }
    $current = trim((string) get_field($field, $post_id));
    if ($current !== '' && justccell_text_has_banned_cta($current)) {
        update_field($field, $replacement, $post_id);
    }
}

function justccell_upgrade_client_copy_policy_v0991(): void
{
    if (get_option('justccell_copy_policy_0991') === '1') {
        return;
    }
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }

    justccell_scrub_option_text('header_cta_label');
    justccell_scrub_option_text('quote_submit_label', __('Send message', 'justccell'));
    justccell_scrub_option_text('store_laser_cta_label');

    foreach (['home', 'contact', 'packaging', 'about', 'solution', 'laser-engraving', 'location', 'locations'] as $slug) {
        if (!function_exists('justccell_find_page_by_slug')) {
            continue;
        }
        $page = justccell_find_page_by_slug($slug);
        if (!$page instanceof WP_Post) {
            continue;
        }
        $id = (int) $page->ID;
        foreach (
            [
                'brand_cta_title',
                'brand_cta_copy',
                'brand_cta_label',
                'coming_soon_secondary_label',
                'coming_soon_primary_label',
                'coming_soon_title',
                'coming_soon_lede',
                'coming_soon_shop_heading',
                'coming_soon_shop_lede',
                'j3_cta_label',
                'contact_lede',
                'contact_form_copy',
            ] as $field
        ) {
            justccell_scrub_post_text($id, $field);
        }
        if ($slug === 'contact') {
            $lede = trim((string) get_field('contact_lede', $id));
            if ($lede === '' || stripos($lede, 'sample') !== false) {
                update_field(
                    'contact_lede',
                    __('Tell us about your extracts, hardware line, and market. A Justccell representative will follow up within one business day.', 'justccell'),
                    $id
                );
            }
        }
    }

    $pages = get_posts([
        'post_type'      => 'page',
        'post_status'    => ['publish', 'draft', 'private'],
        'posts_per_page' => 200,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);
    foreach ($pages as $post_id) {
        $post_id = (int) $post_id;
        foreach (['brand_cta_title', 'brand_cta_copy', 'brand_cta_label'] as $field) {
            justccell_scrub_post_text($post_id, $field);
        }
    }

    update_option('justccell_hide_header_cta', '1', false);
    update_option('justccell_copy_policy_0991', '1', false);
}

add_action('init', 'justccell_upgrade_client_copy_policy_v0991', 77);
