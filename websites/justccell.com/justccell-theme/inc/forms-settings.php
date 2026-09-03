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
        'inquiry_recipient'       => '',
        'newsletter_recipient'    => '',
        'inquiry_subject'         => '[Justccell quote]',
        'newsletter_subject'      => '[Justccell newsletter]',
        'success_message'         => __('Request received. We will follow up shortly.', 'justccell'),
        'error_message'           => __('Please complete all required fields.', 'justccell'),
        'first_name_placeholder'  => __('First Name*', 'justccell'),
        'last_name_placeholder'   => __('Last Name*', 'justccell'),
        'email_placeholder'       => __('Email*', 'justccell'),
        'phone_placeholder'       => __('Phone', 'justccell'),
        'country_placeholder'     => __('Country*', 'justccell'),
        'city_placeholder'        => __('City', 'justccell'),
        'company_placeholder'     => __('Company*', 'justccell'),
        'job_placeholder'         => __('Job Title*', 'justccell'),
        'source_placeholder'      => __('How did you hear about us?*', 'justccell'),
        'message_placeholder'     => __('Leave your message*', 'justccell'),
        'contact_submit_label'    => __('SUBMIT', 'justccell'),
        'country_options'         => "United Kingdom\nSpain\nSwitzerland\nOthers",
        'source_options'          => "Industry Events and Trade Shows\nSearch Engines\nIndustry Media\nSocial Media\nAdvertisement\nOthers",
        'quote_submit_label'      => __('Send message', 'justccell'),
        'quote_notes_label'       => __('Project notes', 'justccell'),
        'newsletter_placeholder'  => __('Enter Your E-mail Address', 'justccell'),
        'newsletter_success'      => __('Thanks — we will be in touch.', 'justccell'),
        'newsletter_error'        => __('Enter an email and accept the privacy policy.', 'justccell'),
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

/**
 * @return list<string>
 */
function justccell_form_priority_countries(): array
{
    return [
        __('United Kingdom', 'justccell'),
        __('Spain', 'justccell'),
        __('Switzerland', 'justccell'),
    ];
}

/**
 * @return list<string>
 */
function justccell_form_option_lines(string $name): array
{
    $lines = preg_split('/\R/', justccell_form_setting($name)) ?: [];
    $lines = array_map('trim', $lines);
    $lines = array_values(array_filter($lines, static fn (string $line): bool => $line !== ''));
    if ($name === 'country_options') {
        return justccell_form_country_choices($lines);
    }
    return $lines;
}

/**
 * UK, Spain, and Switzerland always lead; remaining ACF lines follow; Others last.
 *
 * @param list<string> $lines
 * @return list<string>
 */
function justccell_form_country_choices(array $lines): array
{
    $priority = justccell_form_priority_countries();
    $aliases  = [
        'uk'               => 'United Kingdom',
        'united kingdom'   => 'United Kingdom',
        'great britain'    => 'United Kingdom',
        'gb'               => 'United Kingdom',
        'spain'            => 'Spain',
        'es'               => 'Spain',
        'espana'           => 'Spain',
        'españa'           => 'Spain',
        'switzerland'      => 'Switzerland',
        'swiss'            => 'Switzerland',
        'ch'               => 'Switzerland',
        'suisse'           => 'Switzerland',
        'schweiz'          => 'Switzerland',
    ];

    $rest = [];
    $has_others = false;
    foreach ($lines as $line) {
        $key = strtolower($line);
        if ($key === 'others' || $key === 'other') {
            $has_others = true;
            continue;
        }
        $canonical = $aliases[$key] ?? '';
        if ($canonical !== '' && in_array($canonical, $priority, true)) {
            continue;
        }
        $rest[] = $line;
    }

    $out = array_merge($priority, $rest);
    if ($has_others || $lines === []) {
        $out[] = __('Others', 'justccell');
    }

    $seen = [];
    $unique = [];
    foreach ($out as $line) {
        $fold = strtolower($line);
        if (isset($seen[$fold])) {
            continue;
        }
        $seen[$fold] = true;
        $unique[] = $line;
    }
    return $unique;
}

function justccell_form_recipient(string $form): string
{
    $field = $form === 'newsletter' ? 'newsletter_recipient' : 'inquiry_recipient';
    $email = sanitize_email(justccell_form_setting($field));
    if (is_email($email)) {
        return $email;
    }

    $legacy = sanitize_email((string) get_theme_mod('justccell_inquiry_email', ''));
    if (is_email($legacy)) {
        return $legacy;
    }

    $admin = sanitize_email((string) get_option('admin_email'));
    return is_email($admin) ? $admin : '';
}

add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_options_sub_page') || !function_exists('acf_add_local_field_group')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title'  => __('Forms', 'justccell'),
        'menu_title'  => __('Forms', 'justccell'),
        'parent_slug' => 'justccell',
        'menu_slug'   => 'justccell-forms',
        'capability'  => 'manage_options',
    ]);

    $text = static function (string $key, string $label, string $name, string $default = ''): array {
        return [
            'key'           => $key,
            'label'         => $label,
            'name'          => 'forms_' . $name,
            'type'          => 'text',
            'default_value' => $default,
        ];
    };

    justccell_acf_register_field_group([
        'key'    => 'group_jc_forms_options',
        'title'  => 'Forms',
        'fields' => [
            ['key' => 'field_jc_forms_delivery_tab', 'label' => 'Delivery', 'type' => 'tab'],

            [
                'key'   => 'field_jc_forms_inquiry_recipient',
                'label' => 'Inquiry recipient email',
                'name'  => 'forms_inquiry_recipient',
                'type'  => 'email',
            ],
            [
                'key'   => 'field_jc_forms_newsletter_recipient',
                'label' => 'Newsletter recipient email',
                'name'  => 'forms_newsletter_recipient',
                'type'  => 'email',
            ],
            $text('field_jc_forms_inquiry_subject', 'Inquiry email subject prefix', 'inquiry_subject', '[Justccell quote]'),
            $text('field_jc_forms_newsletter_subject', 'Newsletter email subject prefix', 'newsletter_subject', '[Justccell newsletter]'),
            [
                'key'   => 'field_jc_forms_success_message',
                'label' => 'Inquiry success message',
                'name'  => 'forms_success_message',
                'type'  => 'textarea',
                'rows'  => 2,
            ],
            [
                'key'   => 'field_jc_forms_error_message',
                'label' => 'Inquiry error message',
                'name'  => 'forms_error_message',
                'type'  => 'textarea',
                'rows'  => 2,
            ],
            ['key' => 'field_jc_forms_contact_tab', 'label' => 'Contact form', 'type' => 'tab'],
            $text('field_jc_forms_first_name', 'First name placeholder', 'first_name_placeholder', 'First Name*'),
            $text('field_jc_forms_last_name', 'Last name placeholder', 'last_name_placeholder', 'Last Name*'),
            $text('field_jc_forms_email', 'Email placeholder', 'email_placeholder', 'Email*'),
            $text('field_jc_forms_phone', 'Phone placeholder', 'phone_placeholder', 'Phone'),
            $text('field_jc_forms_country', 'Country placeholder', 'country_placeholder', 'Country*'),
            $text('field_jc_forms_city', 'City placeholder', 'city_placeholder', 'City'),
            $text('field_jc_forms_company', 'Company placeholder', 'company_placeholder', 'Company*'),
            $text('field_jc_forms_job', 'Job title placeholder', 'job_placeholder', 'Job Title*'),
            $text('field_jc_forms_source', 'Referral source placeholder', 'source_placeholder', 'How did you hear about us?*'),
            $text('field_jc_forms_message', 'Message placeholder', 'message_placeholder', 'Leave your message*'),
            $text('field_jc_forms_contact_submit', 'Submit button label', 'contact_submit_label', 'SUBMIT'),
            [
                'key'          => 'field_jc_forms_country_options',
                'label'        => 'Country choices',
                'name'         => 'forms_country_options',
                'type'         => 'textarea',
                'instructions' => 'One choice per line. United Kingdom, Spain, and Switzerland always show first on the contact form, then these extra lines, then Others.',
                'default_value' => "United Kingdom\nSpain\nSwitzerland\nOthers",
                'rows'         => 6,
            ],
            [
                'key'          => 'field_jc_forms_source_options',
                'label'        => 'Referral source choices',
                'name'         => 'forms_source_options',
                'type'         => 'textarea',
                'instructions' => 'One choice per line.',
                'rows'         => 7,
            ],
            ['key' => 'field_jc_forms_quote_tab', 'label' => 'Product inquiry', 'type' => 'tab'],
            $text('field_jc_forms_quote_submit', 'Contact form submit label', 'quote_submit_label', 'Send message'),
            $text('field_jc_forms_quote_notes', 'Project notes label', 'quote_notes_label', 'Project notes'),
            ['key' => 'field_jc_forms_newsletter_tab', 'label' => 'Newsletter', 'type' => 'tab'],
            $text('field_jc_forms_newsletter_placeholder', 'Email placeholder', 'newsletter_placeholder', 'Enter Your E-mail Address'),
            [
                'key'   => 'field_jc_forms_newsletter_success',
                'label' => 'Success message',
                'name'  => 'forms_newsletter_success',
                'type'  => 'textarea',
                'rows'  => 2,
            ],
            [
                'key'   => 'field_jc_forms_newsletter_error',
                'label' => 'Error message',
                'name'  => 'forms_newsletter_error',
                'type'  => 'textarea',
                'rows'  => 2,
            ],
        ],
        'location' => [[[
            'param'    => 'options_page',
            'operator' => '==',
            'value'    => 'justccell-forms',
        ]]],
        'active' => true,
    ]);
});

add_action('init', static function (): void {
    if (wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $current = trim((string) get_field('forms_country_options', 'option'));
    $legacy  = ['United States', 'Canada', 'Others'];
    $lines   = array_values(array_filter(array_map('trim', preg_split('/\R/', $current) ?: [])));
    if ($current !== '' && $lines !== $legacy) {
        return;
    }
    update_field(
        'forms_country_options',
        "United Kingdom\nSpain\nSwitzerland\nOthers",
        'option'
    );
}, 40);
