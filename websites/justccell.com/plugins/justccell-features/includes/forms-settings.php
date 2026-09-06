<?php
/**
 * Central form settings and editor screen.
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, string>
 */
function justccell_form_defaults(): array
{
    return [
        'inquiry_recipient'         => '',
        'inquiry_recipients_extra'  => '',
        'newsletter_recipient'      => '',
        'inquiry_subject'           => '[Justccell contact]',
        'newsletter_subject'        => '[Justccell newsletter]',
        'success_message'           => __('Request received. We will follow up shortly.', 'justccell'),
        'error_message'             => __('Please complete all required fields.', 'justccell'),
        'first_name_placeholder'    => __('First Name*', 'justccell'),
        'last_name_placeholder'     => __('Last Name*', 'justccell'),
        'email_placeholder'         => __('Email*', 'justccell'),
        'phone_placeholder'         => __('Phone', 'justccell'),
        'country_placeholder'       => __('Country*', 'justccell'),
        'city_placeholder'          => __('City', 'justccell'),
        'company_placeholder'       => __('Company*', 'justccell'),
        'job_placeholder'           => __('Job Title*', 'justccell'),
        'source_placeholder'        => __('How did you hear about us?*', 'justccell'),
        'message_placeholder'       => __('Leave your message*', 'justccell'),
        'contact_submit_label'      => __('SUBMIT', 'justccell'),
        'source_options'            => "Industry Events and Trade Shows\nSearch Engines\nIndustry Media\nSocial Media\nAdvertisement\nOthers",
        'quote_submit_label'        => __('Send message', 'justccell'),
        'quote_notes_label'         => __('Project notes', 'justccell'),
        'newsletter_placeholder'    => __('Enter Your E-mail Address', 'justccell'),
        'newsletter_success'        => __('Thanks — we will be in touch.', 'justccell'),
        'newsletter_error'          => __('Enter an email and accept the privacy policy.', 'justccell'),
    ];
}

function justccell_form_setting(string $name): string
{
    $defaults = justccell_form_defaults();
    $fallback = (string) ($defaults[$name] ?? '');
    if (!function_exists('get_field')) {
        return $fallback;
    }

    $value = get_field('forms_' . $name, 'option');
    return is_string($value) && trim($value) !== '' ? trim($value) : $fallback;
}

function justccell_form_default_country_code(): string
{
    return 'GB';
}

/**
 * ISO country code => label. United Kingdom first, then alphabetical.
 *
 * @return array<string, string>
 */
function justccell_form_world_countries(): array
{
    if (function_exists('WC') && WC()->countries) {
        $all = WC()->countries->get_countries();
    } else {
        $all = [
            'GB' => 'United Kingdom',
            'US' => 'United States',
        ];
    }

    $code = justccell_form_default_country_code();
    $uk   = [];
    if (isset($all[$code])) {
        $uk[$code] = $all[$code];
        unset($all[$code]);
    }

    if ($all !== []) {
        asort($all, SORT_NATURAL | SORT_FLAG_CASE);
    }

    return $uk + $all;
}

function justccell_form_country_from_submission(string $value): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }

    $countries = justccell_form_world_countries();
    if (isset($countries[$value])) {
        return $countries[$value];
    }

    foreach ($countries as $name) {
        if (strcasecmp($name, $value) === 0) {
            return $name;
        }
    }

    return '';
}

/**
 * @return list<string>
 */
function justccell_form_option_lines(string $name): array
{
    $lines = preg_split('/\R/', justccell_form_setting($name)) ?: [];
    $lines = array_map('trim', $lines);
    return array_values(array_filter($lines, static fn (string $line): bool => $line !== ''));
}

/**
 * @return list<string>
 */
function justccell_form_recipients(string $form = 'inquiry'): array
{
    $field   = $form === 'newsletter' ? 'newsletter_recipient' : 'inquiry_recipient';
    $emails  = [];
    $primary = sanitize_email(justccell_form_setting($field));
    if (is_email($primary)) {
        $emails[] = $primary;
    }

    if ($form === 'inquiry') {
        foreach (justccell_form_option_lines('inquiry_recipients_extra') as $line) {
            $extra = sanitize_email($line);
            if (is_email($extra) && !in_array($extra, $emails, true)) {
                $emails[] = $extra;
            }
        }
    }

    if ($emails === []) {
        $legacy = sanitize_email((string) get_theme_mod('justccell_inquiry_email', ''));
        if (is_email($legacy)) {
            $emails[] = $legacy;
        }
    }

    if ($emails === []) {
        $admin = sanitize_email((string) get_option('admin_email'));
        if (is_email($admin)) {
            $emails[] = $admin;
        }
    }

    return $emails;
}

function justccell_form_recipient(string $form): string
{
    $recipients = justccell_form_recipients($form);
    return $recipients[0] ?? '';
}

add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_options_sub_page')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title'  => __('Forms', 'justccell'),
        'menu_title'  => __('Forms', 'justccell'),
        'menu_slug'   => 'justccell-forms',
        'parent_slug' => 'justccell',
        'capability'  => 'manage_options',
    ]);

    // group_jc_forms_options — Local JSON + DB only (Phase 3 Batch 1).
});
