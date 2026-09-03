<?php
/**
 * Native B2B sample/quote form. No Contact Form 7.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'justccell_register_leads');
add_action('admin_post_nopriv_justccell_inquiry', 'justccell_handle_inquiry');
add_action('admin_post_justccell_inquiry', 'justccell_handle_inquiry');
add_action('admin_post_nopriv_justccell_subscribe', 'justccell_handle_subscribe');
add_action('admin_post_justccell_subscribe', 'justccell_handle_subscribe');

function justccell_register_leads(): void
{
    register_post_type('jc_lead', [
        'labels' => [
            'name'          => __('Quote leads', 'justccell'),
            'singular_name' => __('Quote lead', 'justccell'),
            'menu_name'     => __('Quote leads', 'justccell'),
        ],
        'public'              => false,
        'show_ui'             => true,
        'show_in_menu'        => 'justccell',
        'menu_icon'           => 'dashicons-email-alt',
        'supports'            => ['title', 'editor', 'custom-fields'],
        'capability_type'     => 'post',
        'exclude_from_search' => true,
        'rewrite'             => false,
    ]);
}

/**
 * @param array<string, string> $fields
 */
function justccell_store_lead(string $type, string $title, array $fields): int
{
    $body = [];
    foreach ($fields as $key => $value) {
        if ($key === 'VAT') {
            continue;
        }
        $body[] = $key . ': ' . $value;
    }
    $id = wp_insert_post([
        'post_type'    => 'jc_lead',
        'post_status'  => 'private',
        'post_title'   => $title,
        'post_content' => implode("\n", $body),
        'meta_input'   => array_merge($fields, ['_jc_lead_type' => $type]),
    ], true);

    return is_int($id) ? $id : 0;
}

function justccell_handle_inquiry(): void
{
    $back = wp_get_referer() ?: home_url('/contact/');
    if (!isset($_POST['justccell_inquiry_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['justccell_inquiry_nonce'])), 'justccell_inquiry')) {
        wp_safe_redirect(add_query_arg('inquiry', 'invalid', $back));
        exit;
    }

    $honeypot = sanitize_text_field(wp_unslash((string) ($_POST['company_website'] ?? '')));
    if ($honeypot !== '') {
        wp_safe_redirect(add_query_arg('inquiry', 'sent', $back));
        exit;
    }

    $ip = (string) ($_SERVER['REMOTE_ADDR'] ?? '');
    if ($ip !== '') {
        $key   = 'jc_inq_' . md5($ip);
        $hits  = (int) get_transient($key);
        if ($hits >= 8) {
            wp_safe_redirect(add_query_arg('inquiry', 'invalid', $back));
            exit;
        }
        set_transient($key, $hits + 1, 10 * MINUTE_IN_SECONDS);
    }

    $account = sanitize_text_field(wp_unslash((string) ($_POST['account_type'] ?? '')));
    $name    = sanitize_text_field(wp_unslash((string) ($_POST['full_name'] ?? '')));
    $first   = sanitize_text_field(wp_unslash((string) ($_POST['first_name'] ?? '')));
    $last    = sanitize_text_field(wp_unslash((string) ($_POST['last_name'] ?? '')));
    if ($name === '') {
        $name = trim($first . ' ' . $last);
    }
    $email   = sanitize_email(wp_unslash((string) ($_POST['email'] ?? '')));
    $company = sanitize_text_field(wp_unslash((string) ($_POST['company'] ?? '')));
    $country = sanitize_text_field(wp_unslash((string) ($_POST['country'] ?? '')));
    $vat     = sanitize_text_field(wp_unslash((string) ($_POST['vat_number'] ?? '')));
    $phone   = sanitize_text_field(wp_unslash((string) ($_POST['phone'] ?? '')));
    $city    = sanitize_text_field(wp_unslash((string) ($_POST['city'] ?? '')));
    $job     = sanitize_text_field(wp_unslash((string) ($_POST['job_title'] ?? '')));
    $layout  = sanitize_key(wp_unslash((string) ($_POST['form_layout'] ?? '')));
    $sku     = sanitize_text_field(wp_unslash((string) ($_POST['sku'] ?? '')));
    $combo   = sanitize_text_field(wp_unslash((string) ($_POST['combo'] ?? '')));
    $variant = sanitize_text_field(wp_unslash((string) ($_POST['variant'] ?? '')));
    $qty     = sanitize_text_field(wp_unslash((string) ($_POST['qty'] ?? '')));
    $source  = sanitize_text_field(wp_unslash((string) ($_POST['source'] ?? '')));
    $message = sanitize_textarea_field(wp_unslash((string) ($_POST['message'] ?? '')));

    $attr_lines = [];
    $posted_attrs = $_POST['attr'] ?? [];
    if (is_array($posted_attrs)) {
        foreach ($posted_attrs as $attr_key => $attr_val) {
            $attr_key = sanitize_key((string) $attr_key);
            $attr_val = sanitize_text_field(wp_unslash(is_array($attr_val) ? (string) ($attr_val[0] ?? '') : (string) $attr_val));
            if ($attr_key === '' || $attr_val === '') {
                continue;
            }
            $attr_lines[ucwords(str_replace(['-', '_'], ' ', $attr_key))] = $attr_val;
            if ($combo === '' && justccell_is_combination_attribute_name($attr_key)) {
                $combo = $attr_val;
            }
            if ($variant === '' && justccell_is_colour_attribute_name($attr_key)) {
                $variant = $attr_val;
            }
        }
    }

    $account_ok = $account === 'b2c' || $account === 'b2b';
    $b2b_ok     = $account !== 'b2b' || ($company !== '' && ($layout === 'contact' || $vat !== ''));

    if ($name === '' || !is_email($email) || $country === '' || !$account_ok || !$b2b_ok) {
        wp_safe_redirect(add_query_arg('inquiry', 'missing', $back));
        exit;
    }

    $fields = [
        'Account' => strtoupper($account),
        'Name'    => $name,
        'Email'   => $email,
        'Phone'   => $phone,
        'Company' => $company,
        'Job'     => $job,
        'VAT'     => $vat,
        'Country' => $country,
        'City'    => $city,
        'SKU'     => $sku,
        'Combo'   => $combo,
        'Variant' => $variant,
        'Qty'     => $qty,
        'Source'  => $source,
        'Store'   => function_exists('justccell_current_store') ? justccell_current_store() : '',
        'Lang'    => function_exists('justccell_current_lang') ? justccell_current_lang() : '',
        'Message' => $message,
    ];
    foreach ($attr_lines as $attr_label => $attr_val) {
        $fields['Attr: ' . $attr_label] = $attr_val;
    }
    justccell_store_lead('inquiry', sprintf('[quote] [%s] %s', strtoupper($account), $company !== '' ? $company : $name), $fields);

    $to = function_exists('justccell_form_recipient')
        ? justccell_form_recipient('inquiry')
        : (function_exists('justccell_inquiry_recipient') ? justccell_inquiry_recipient() : (string) get_option('admin_email'));
    $prefix  = function_exists('justccell_form_setting') ? justccell_form_setting('inquiry_subject') : '[Justccell quote]';
    $subject = sprintf('%s [%s] %s', $prefix, strtoupper($account), $company !== '' ? $company : $name);
    $body    = implode("\n", array_map(
        static fn (string $key, string $value): string => $key . ': ' . $value,
        array_keys($fields),
        array_values($fields)
    ));

    if (is_email($to)) {
        wp_mail($to, $subject, $body, ['Reply-To: ' . $email]);
    }

    wp_safe_redirect(add_query_arg('inquiry', 'sent', $back));
    exit;
}

function justccell_handle_subscribe(): void
{
    $back = wp_get_referer() ?: home_url('/');
    if (!isset($_POST['justccell_subscribe_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['justccell_subscribe_nonce'])), 'justccell_subscribe')) {
        wp_safe_redirect(add_query_arg('subscribe', 'missing', $back));
        exit;
    }

    $email = sanitize_email(wp_unslash((string) ($_POST['email'] ?? '')));
    $ok    = (string) ($_POST['privacy_ok'] ?? '') === '1';
    if (!is_email($email) || !$ok) {
        wp_safe_redirect(add_query_arg('subscribe', 'missing', $back));
        exit;
    }

    justccell_store_lead('newsletter', '[newsletter] ' . $email, [
        'Email' => $email,
        'Store' => function_exists('justccell_current_store') ? justccell_current_store() : '',
        'Lang'  => function_exists('justccell_current_lang') ? justccell_current_lang() : '',
    ]);

    $to = function_exists('justccell_form_recipient')
        ? justccell_form_recipient('newsletter')
        : (function_exists('justccell_inquiry_recipient') ? justccell_inquiry_recipient() : (string) get_option('admin_email'));
    $subject = function_exists('justccell_form_setting') ? justccell_form_setting('newsletter_subject') : '[Justccell newsletter]';
    if (is_email($to)) {
        wp_mail($to, $subject . ' ' . $email, 'New footer signup: ' . $email, ['Reply-To: ' . $email]);
    }

    wp_safe_redirect(add_query_arg('subscribe', 'sent', $back));
    exit;
}
