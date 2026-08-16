<?php
/**
 * Native B2B sample/quote form. No Contact Form 7.
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_post_nopriv_justccell_inquiry', 'justccell_handle_inquiry');
add_action('admin_post_justccell_inquiry', 'justccell_handle_inquiry');

function justccell_handle_inquiry(): void
{
    if (!isset($_POST['justccell_inquiry_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['justccell_inquiry_nonce'])), 'justccell_inquiry')) {
        wp_safe_redirect(add_query_arg('inquiry', 'invalid', wp_get_referer() ?: home_url('/contact/')));
        exit;
    }

    $account = sanitize_text_field(wp_unslash((string) ($_POST['account_type'] ?? '')));
    $name    = sanitize_text_field(wp_unslash((string) ($_POST['full_name'] ?? '')));
    $email   = sanitize_email(wp_unslash((string) ($_POST['email'] ?? '')));
    $company = sanitize_text_field(wp_unslash((string) ($_POST['company'] ?? '')));
    $country = sanitize_text_field(wp_unslash((string) ($_POST['country'] ?? '')));
    $vat     = sanitize_text_field(wp_unslash((string) ($_POST['vat_number'] ?? '')));
    $sku     = sanitize_text_field(wp_unslash((string) ($_POST['sku'] ?? '')));
    $source  = sanitize_text_field(wp_unslash((string) ($_POST['source'] ?? '')));
    $message = sanitize_textarea_field(wp_unslash((string) ($_POST['message'] ?? '')));

    $account_ok = $account === 'b2c' || $account === 'b2b';
    $b2b_ok     = $account !== 'b2b' || ($company !== '' && $vat !== '');

    if ($name === '' || !is_email($email) || $country === '' || !$account_ok || !$b2b_ok) {
        wp_safe_redirect(add_query_arg('inquiry', 'missing', wp_get_referer() ?: home_url('/contact/')));
        exit;
    }

    $to      = get_option('admin_email');
    $subject = sprintf('[Justccell quote] [%s] %s', strtoupper($account), $company !== '' ? $company : $name);
    $body    = implode("\n", [
        'Account: ' . strtoupper($account),
        'Name: ' . $name,
        'Email: ' . $email,
        'Company: ' . $company,
        'VAT: ' . $vat,
        'Country: ' . $country,
        'SKU: ' . $sku,
        'Source: ' . $source,
        '',
        $message,
    ]);

    wp_mail($to, $subject, $body, ['Reply-To: ' . $email]);

    wp_safe_redirect(add_query_arg('inquiry', 'sent', home_url('/contact/')));
    exit;
}
