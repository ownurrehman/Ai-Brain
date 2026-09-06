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
        'get samples and quotes',
        'samples & quotes',
        'samples and quotes',
        'sample & quote',
        'sample and quote',
        'sample tray',
        'request a quote',
        'request sample',
        'request sample & quote',
        'request sample and quote',
        'samples delivered',
        'delivered in 3–15 days',
        'delivered in 3-15 days',
        '3–15 days',
        '3-15 days',
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

function justccell_upgrade_client_copy_policy_v0992(): void
{
    if (get_option('justccell_copy_policy_0992') === '1') {
        return;
    }
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }

    $front = (int) get_option('page_on_front');
    if ($front > 0) {
        foreach (['home_quote_heading', 'home_quote_copy', 'home_quote_heading_tag'] as $field) {
            delete_field($field, $front);
        }
        delete_field('home_quote_bg', $front);
    }

    $bio = function_exists('justccell_bio_page') ? justccell_bio_page() : null;
    if ($bio instanceof WP_Post) {
        foreach (['j3_cta_title', 'j3_cta_copy', 'j3_cta_label'] as $field) {
            justccell_scrub_post_text((int) $bio->ID, $field);
        }
        if (trim((string) get_field('j3_cta_label', (int) $bio->ID)) === '') {
            update_field('j3_cta_label', __('Contact us', 'justccell'), (int) $bio->ID);
        }
        if (trim((string) get_field('j3_cta_url', (int) $bio->ID)) === '') {
            update_field('j3_cta_url', home_url('/contact/'), (int) $bio->ID);
        }
    }

    justccell_scrub_option_text('inquiry_subject', '[Justccell contact]');

    update_option('justccell_copy_policy_0992', '1', false);
}

add_action('init', 'justccell_upgrade_client_copy_policy_v0991', 77);
add_action('init', 'justccell_upgrade_client_copy_policy_v0992', 78);

/**
 * Scrub leftover sample / turnaround copy from Contact + brand ACF defaults.
 */
function justccell_upgrade_client_copy_policy_v0993(): void
{
    if (get_option('justccell_copy_policy_0993') === '1') {
        return;
    }
    if (!function_exists('get_field') || !function_exists('update_field') || !function_exists('justccell_find_page_by_slug')) {
        return;
    }

    $contact = justccell_find_page_by_slug('contact');
    if ($contact instanceof WP_Post) {
        $id = (int) $contact->ID;
        foreach (
            [
                'contact_lede',
                'contact_form_copy',
                'contact_form_title',
                'contact_hero_title',
                'brand_cta_title',
                'brand_cta_copy',
                'brand_cta_label',
            ] as $field
        ) {
            $current = trim((string) get_field($field, $id));
            if ($current === '') {
                continue;
            }
            if (justccell_text_has_banned_cta($current) || preg_match('/\bsamples?\b/i', $current) === 1) {
                $replacement = match ($field) {
                    'contact_lede', 'contact_form_copy' => __('Tell us about your extracts, hardware line, and market. A Justccell representative will follow up within one business day.', 'justccell'),
                    'contact_form_title', 'contact_hero_title' => __('Contact us', 'justccell'),
                    'brand_cta_label' => __('Inquire Now', 'justccell'),
                    'brand_cta_title' => __('Request a wholesale quote', 'justccell'),
                    'brand_cta_copy' => __('Share your oil type, volumes, and timeline — we will route the right hardware path.', 'justccell'),
                    default => '',
                };
                update_field($field, $replacement, $id);
            }
        }
        $faqs = get_field('contact_faq', $id);
        if (is_array($faqs) && function_exists('justccell_contact_faqs_without_samples')) {
            $cleaned = justccell_contact_faqs_without_samples($faqs);
            $cleaned = array_values(array_filter(
                $cleaned,
                static function (array $row): bool {
                    $blob = strtolower(($row['q'] ?? '') . ' ' . ($row['a'] ?? ''));
                    return !str_contains($blob, 'sample');
                }
            ));
            update_field('contact_faq', $cleaned, $id);
        }
        if (preg_match('/get samples|sample/i', (string) $contact->post_title) === 1) {
            wp_update_post([
                'ID'         => $id,
                'post_title' => __('Contact us', 'justccell'),
            ]);
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
        foreach (
            [
                'brand_cta_title',
                'brand_cta_copy',
                'brand_cta_label',
                'coming_soon_primary_label',
                'coming_soon_secondary_label',
                'j3_cta_label',
                'j3_cta_title',
                'j3_cta_copy',
            ] as $field
        ) {
            $current = trim((string) get_field($field, $post_id));
            if ($current !== '' && (justccell_text_has_banned_cta($current) || preg_match('/\bsamples?\b/i', $current) === 1)) {
                $fallback = match ($field) {
                    'brand_cta_label', 'j3_cta_label', 'coming_soon_primary_label' => __('Inquire Now', 'justccell'),
                    'coming_soon_secondary_label' => __('Contact Us', 'justccell'),
                    'brand_cta_title', 'j3_cta_title' => __('Request a wholesale quote', 'justccell'),
                    'brand_cta_copy', 'j3_cta_copy' => __('Share your oil type, volumes, and timeline — we will follow up with wholesale options.', 'justccell'),
                    default => '',
                };
                update_field($field, $fallback, $post_id);
            }
        }
    }

    update_option('justccell_copy_policy_0993', '1', false);
}

add_action('init', 'justccell_upgrade_client_copy_policy_v0993', 79);
