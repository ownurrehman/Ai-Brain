<?php
/**
 * Contact page data.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_contact_public_heading(string $stored): string
{
    $stored = trim($stored);
    return $stored !== '' ? $stored : __('Contact us', 'justccell');
}

function justccell_contact_form_heading(string $stored): string
{
    $stored = trim($stored);
    return $stored !== '' ? $stored : __('Contact us', 'justccell');
}

/**
 * @param list<array{q?:string,a?:string}> $faqs
 * @return list<array{q:string,a:string}>
 */
function justccell_contact_faqs_without_samples(array $faqs): array
{
    $kept = [];
    foreach ($faqs as $row) {
        if (!is_array($row)) {
            continue;
        }
        $q = trim((string) ($row['q'] ?? ''));
        $a = (string) ($row['a'] ?? '');
        if ($q === '') {
            continue;
        }
        $blob = strtolower($q . ' ' . $a);
        if (
            preg_match('/how fast do samples ship/i', $q) === 1
            || str_contains($blob, 'sample')
            || str_contains($blob, '3-15 days')
            || str_contains($blob, '3–15 days')
        ) {
            continue;
        }
        $kept[] = [
            'q' => $q,
            'a' => $a,
        ];
    }
    return $kept;
}

/**
 * @return array<string, mixed>
 */
function justccell_get_contact_page_data(): array
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

    $sales = sanitize_email($field('contact_sales_email', (string) get_theme_mod('justccell_inquiry_email', '')));
    if (!is_email($sales)) {
        $sales = '';
    }
    $support = sanitize_email($field('contact_support_email', (string) get_theme_mod('justccell_support_email', '')));
    if (!is_email($support)) {
        $support = '';
    }

    $logo_id = $image_id('contact_logo');
    if ($logo_id > 0 && function_exists('justccell_filename_is_leaky')) {
        $attached = basename((string) get_post_meta($logo_id, '_wp_attached_file', true));
        if (justccell_filename_is_leaky($attached)) {
            $logo_id = 0;
        }
    }
    if ($logo_id < 1 && function_exists('justccell_brand_logo_id')) {
        $logo_id = justccell_brand_logo_id();
    }

    return [
        'hero_title'                => justccell_contact_public_heading($field('contact_hero_title', __('Contact us', 'justccell'))),
        'hero_title_tag'            => $field('contact_hero_title_tag', 'h1'),
        'hero_desktop_id'           => $image_id('contact_hero_desktop'),
        'hero_desktop'              => 'contact/justccell-contact-hero-desktop.jpg',
        'hero_mobile_id'            => $image_id('contact_hero_mobile'),
        'hero_mobile'               => 'contact/justccell-contact-hero-mobile.jpg',
        'logo_id'                   => $logo_id,
        'info_heading'              => $field('contact_info_heading', __('Contact Information', 'justccell')),
        'info_heading_tag'          => $field('contact_info_heading_tag', 'h2'),
        'emails'               => justccell_contact_email_rows(
            $post_id,
            $sales,
            $support,
            $field('contact_sales_label', __('Purchase Inquiry:', 'justccell')),
            $field('contact_support_label', __('Support', 'justccell'))
        ),
        'phone_label'          => $field('contact_phone_label', __('Tel:', 'justccell')),
        'phone'                => $field(
            'contact_phone',
            function_exists('justccell_public_phone')
                ? justccell_public_phone()
                : (string) get_theme_mod('justccell_contact_phone', '+447495338694')
        ),
        'address_label'        => $field('contact_address_label', __('Address:', 'justccell')),
        'address'              => $field(
            'contact_address',
            "112 - 116 Hamill House\nChorley New Road,\nBolton,\nBL1 4DH"
        ),
        'follow_heading'            => $field('contact_follow_heading', __('Follow Us', 'justccell')),
        'follow_heading_tag'        => $field('contact_follow_heading_tag', 'h2'),
        'social'                    => justccell_contact_social_rows($post_id),
        'distributors_heading'      => $field('contact_distributors_heading', __('Our Distributors', 'justccell')),
        'distributors_heading_tag'  => $field('contact_distributors_heading_tag', 'h2'),
        'distributors'              => justccell_contact_distributor_rows($post_id),
        'faq_heading'               => $field('contact_faq_heading', __('FAQ', 'justccell')),
        'faq_heading_tag'           => $field('contact_faq_heading_tag', 'h2'),
        'form_title'                => justccell_contact_form_heading($field('contact_form_title', __('Contact us', 'justccell'))),
        'form_title_tag'            => $field('contact_form_title_tag', 'h2'),
        'form_copy'            => $field('contact_form_copy', __('Please fill the form below to submit your inquiry, and a Justccell sales representative will contact you promptly.', 'justccell')),
    ];
}

/**
 * @return list<array{label:string,email:string}>
 */
function justccell_contact_email_rows(
    int $post_id,
    string $sales,
    string $support,
    string $sales_label,
    string $support_label
): array {
    $rows = [];
    if ($post_id > 0 && function_exists('get_field')) {
        foreach ((array) get_field('contact_emails', $post_id) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $email = sanitize_email((string) ($row['email'] ?? ''));
            if (!is_email($email)) {
                continue;
            }
            $rows[] = [
                'label' => trim((string) ($row['label'] ?? '')),
                'email' => $email,
            ];
        }
    }
    if ($rows !== []) {
        return $rows;
    }
    if (is_email($sales)) {
        $rows[] = ['label' => $sales_label, 'email' => $sales];
    }
    if (is_email($support)) {
        $rows[] = ['label' => $support_label, 'email' => $support];
    }
    return $rows;
}

/**
 * @return list<array{label:string,url:string,network:string,icon_id:int}>
 */
function justccell_contact_social_rows(int $post_id): array
{
    $rows = [];
    if ($post_id > 0 && function_exists('get_field')) {
        foreach ((array) get_field('contact_social', $post_id) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $url = esc_url_raw((string) ($row['url'] ?? ''));
            if ($url === '') {
                continue;
            }
            $label = trim((string) ($row['label'] ?? ''));
            $network = sanitize_key((string) ($row['network'] ?? ''));
            if ($network === '') {
                $network = justccell_contact_social_network_from_url($url);
            }
            $rows[] = [
                'label'    => $label !== '' ? $label : ucfirst($network),
                'url'      => $url,
                'network'  => $network,
                'icon_id'  => function_exists('justccell_acf_to_attachment_id')
                    ? justccell_acf_to_attachment_id($row['icon'] ?? 0)
                    : 0,
            ];
        }
    }
    if ($rows !== []) {
        return $rows;
    }
    if (!function_exists('justccell_social_links')) {
        return [];
    }
    foreach (justccell_social_links() as $item) {
        $rows[] = [
            'label'   => (string) ($item['label'] ?? ''),
            'url'     => (string) ($item['url'] ?? ''),
            'network' => sanitize_key((string) ($item['network'] ?? '')),
            'icon_id' => 0,
        ];
    }
    return $rows;
}

function justccell_contact_social_network_from_url(string $url): string
{
    $host = strtolower((string) wp_parse_url($url, PHP_URL_HOST));
    if (str_contains($host, 'instagram')) {
        return 'instagram';
    }
    if (str_contains($host, 'youtube') || str_contains($host, 'youtu.be')) {
        return 'youtube';
    }
    if (str_contains($host, 'linkedin')) {
        return 'linkedin';
    }
    if (str_contains($host, 'facebook') || str_contains($host, 'fb.com')) {
        return 'facebook';
    }
    if (str_contains($host, 'twitter') || $host === 'x.com' || str_ends_with($host, '.x.com')) {
        return 'x';
    }
    return 'link';
}

function justccell_contact_echo_social_icon(string $network, int $icon_id, string $label): void
{
    if ($icon_id > 0) {
        echo wp_get_attachment_image($icon_id, 'thumbnail', false, [
            'alt'     => $label,
            'class'   => 'jc-contact__social-img',
            'loading' => 'lazy',
        ]);
        return;
    }

    $svgs = [
        'instagram' => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 3h10a4 4 0 0 1 4 4v10a4 4 0 0 1-4 4H7a4 4 0 0 1-4-4V7a4 4 0 0 1 4-4zm5 4.5A4.5 4.5 0 1 0 16.5 12 4.5 4.5 0 0 0 12 7.5zm0 7.4A2.9 2.9 0 1 1 14.9 12 2.9 2.9 0 0 1 12 14.9zm5.2-8.7a1.05 1.05 0 1 0 1.05 1.05 1.05 1.05 0 0 0-1.05-1.05z"/></svg>',
        'youtube'   => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M23 12.2s0-3.3-.4-4.7a3 3 0 0 0-2.1-2.1C18.9 5 12 5 12 5s-6.9 0-8.5.4a3 3 0 0 0-2.1 2.1C1 8.9 1 12.2 1 12.2s0 3.3.4 4.7a3 3 0 0 0 2.1 2.1C5.1 19.4 12 19.4 12 19.4s6.9 0 8.5-.4a3 3 0 0 0 2.1-2.1c.4-1.4.4-4.7.4-4.7zM9.8 15.6V8.8l6.2 3.4z"/></svg>',
        'linkedin'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M6.5 9.5H3.8V20h2.7zm.2-4.1A1.6 1.6 0 1 0 5.1 7a1.6 1.6 0 0 0 1.6-1.6zM20.2 13.4c0-3.2-1.7-4.7-4-4.7a3.4 3.4 0 0 0-3 1.6h-.1V9.5H10.5V20h2.7v-5.2c0-1.4.3-2.7 2-2.7s1.7 1.2 1.7 2.8V20h2.7z"/></svg>',
        'facebook'  => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14.5 20v-7.2h2.4l.4-2.8h-2.8V8.3c0-.8.2-1.4 1.4-1.4h1.5V4.4A19 19 0 0 0 15 4.2c-2.2 0-3.7 1.4-3.7 3.8v2h-2.5v2.8h2.5V20z"/></svg>',
        'x'         => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M14.7 10.5 21 3h-1.7l-5.5 6.5L9.5 3H4l6.7 9.7L4 21h1.7l5.8-6.8L14.7 21H20zm-2 2.4-.7-1-5.5-7.8h2.4l4.4 6.3.7 1 5.8 8.2h-2.4z"/></svg>',
        'link'      => '<svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M10.6 13.4a4 4 0 0 1 0-5.6l2.8-2.8a4 4 0 0 1 5.6 5.6l-1.3 1.3-1.1-1.1 1.3-1.3a2.4 2.4 0 1 0-3.4-3.4l-2.8 2.8a2.4 2.4 0 0 0 0 3.4zm2.8-2.8a4 4 0 0 1 0 5.6l-2.8 2.8a4 4 0 1 1-5.6-5.6l1.3-1.3 1.1 1.1-1.3 1.3a2.4 2.4 0 1 0 3.4 3.4l2.8-2.8a2.4 2.4 0 0 0 0-3.4z"/></svg>',
    ];
    echo $svgs[$network] ?? $svgs['link']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * @return list<array{name:string,url:string,image_id:int,image:string}>
 */
function justccell_contact_distributors(): array
{
    return [];
}

/**
 * @param array<string, string|int|bool|null> $attrs
 */
function justccell_contact_echo_img(int|string $image, string $class, string $alt, array $attrs = []): void
{
    if ($image === '' || $image === 0) {
        return;
    }

    $key = is_string($image) ? $image : '';
    $id  = is_int($image) ? $image : 0;
    if ($key !== '' && str_starts_with($key, 'public_')) {
        return;
    }
    if (str_starts_with($key, 'contact/') && defined('JUSTCCELL_DIR')) {
        $path = JUSTCCELL_DIR . '/assets/img/' . $key;
        if (is_readable($path)) {
            justccell_ensure_media_url($key);
        }
    } elseif ($key !== '') {
        justccell_ensure_media_url($key);
    }

    if ($id < 1) {
        $id = justccell_media_id($key);
    }
    $base_attrs = array_merge([
        'alt'     => $alt,
        'loading' => 'lazy',
        'class'   => $class,
    ], $attrs);

    if ($id > 0) {
        echo wp_get_attachment_image($id, 'full', false, $base_attrs);
    }
}

function justccell_apply_contact_copy_051(): void
{
    if (get_option('justccell_contact_copy_051') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug') || !function_exists('update_field')) {
        return;
    }
    $page = justccell_find_page_by_slug('contact');
    if (!$page instanceof WP_Post) {
        return;
    }
    $id         = (int) $page->ID;
    $contact_us = __('Contact us', 'justccell');
    foreach (['contact_hero_title', 'contact_form_title', 'contact_title'] as $name) {
        $current = trim((string) get_field($name, $id));
        if ($current === '') {
            update_field($name, $contact_us, $id);
        }
    }
    $faqs = get_field('contact_faq', $id);
    if (is_array($faqs)) {
        $cleaned = justccell_contact_faqs_without_samples($faqs);
        if (count($cleaned) !== count($faqs)) {
            update_field('contact_faq', $cleaned, $id);
        }
    }
    if (preg_match('/get samples/i', (string) $page->post_title) === 1) {
        wp_update_post([
            'ID'         => $id,
            'post_title' => $contact_us,
        ]);
    }
    update_option('justccell_contact_copy_051', '1', false);
}

add_action('init', 'justccell_apply_contact_copy_051', 70);

function justccell_apply_site_copy_052(): void
{
    if (get_option('justccell_site_copy_052') === '1') {
        return;
    }

    $phone   = function_exists('justccell_public_phone') ? justccell_public_phone() : '+447495338694';
    $wa      = function_exists('justccell_default_whatsapp_url') ? justccell_default_whatsapp_url() : 'https://wa.me/447495338694';
    $tg      = function_exists('justccell_default_telegram_url') ? justccell_default_telegram_url() : 'https://t.me/+447495338694';

    if (function_exists('update_field')) {
        $current_wa = function_exists('justccell_social_option_url') ? justccell_social_option_url('whatsapp') : '';
        $current_tg = function_exists('justccell_social_option_url') ? justccell_social_option_url('telegram') : '';
        if ($current_wa !== $wa) {
            update_field('store_whatsapp', $wa, 'option');
        }
        if ($current_tg !== $tg) {
            update_field('store_telegram', $tg, 'option');
        }
    }
    set_theme_mod('justccell_contact_phone', $phone);

    if (function_exists('justccell_find_page_by_slug') && function_exists('update_field')) {
        $page = justccell_find_page_by_slug('contact');
        if ($page instanceof WP_Post) {
            $id = (int) $page->ID;
            update_field('contact_distributors', [], $id);
            $stored_phone = trim((string) get_field('contact_phone', $id));
            if ($stored_phone === '' || $stored_phone !== $phone) {
                update_field('contact_phone', $phone, $id);
            }
        }
    }

    if (function_exists('justccell_ensure_locations_nav')) {
        justccell_ensure_locations_nav();
    }

    update_option('justccell_site_copy_052', '1', false);
}

add_action('init', 'justccell_apply_site_copy_052', 71);

function justccell_seed_contact_address_field(): void
{
    if (get_option('justccell_contact_address_seeded') === '1') {
        return;
    }
    if (!function_exists('update_field') || !function_exists('justccell_find_page_by_slug')) {
        return;
    }
    $page = justccell_find_page_by_slug('contact');
    if (!$page instanceof WP_Post) {
        return;
    }
    $id = (int) $page->ID;
    $address = "112 - 116 Hamill House\nChorley New Road,\nBolton,\nBL1 4DH";
    if (trim((string) get_field('contact_address', $id)) === '') {
        update_field('contact_address', $address, $id);
    }
    update_option('justccell_contact_address_seeded', '1', false);
}

add_action('init', 'justccell_seed_contact_address_field', 72);
