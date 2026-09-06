<?php
/**
 * Native 18+ age verification modal — Storefront options + client-side cookie.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_age_gate_is_enabled(): bool
{
    return function_exists('justccell_option_bool')
        && justccell_option_bool('store_age_gate_enabled', false);
}

function justccell_age_gate_should_render(): bool
{
    if (!justccell_age_gate_is_enabled()) {
        return false;
    }
    if (is_admin() || is_customize_preview() || wp_doing_ajax()) {
        return false;
    }
    if (function_exists('justccell_is_storefront_context') && !justccell_is_storefront_context()) {
        return false;
    }
    return true;
}

/**
 * @return array{
 *     title:string,
 *     body_html:string,
 *     confirm_label:string,
 *     decline_label:string,
 *     decline_url:string,
 *     cookie_days:int
 * }
 */
function justccell_age_gate_settings(): array
{
    $title = function_exists('justccell_option_string')
        ? justccell_option_string('store_age_gate_title', __('Age Verification', 'justccell'))
        : __('Age Verification', 'justccell');

    $body = '';
    if (function_exists('get_field')) {
        $raw = get_field('store_age_gate_body', 'option');
        if (is_string($raw) && trim($raw) !== '') {
            $body = $raw;
        }
    }
    if ($body === '') {
        $body = '<p>' . esc_html(
            __('You must be 18 years or older to enter this site. Please confirm your age.', 'justccell')
        ) . '</p>';
    }

    $confirm = function_exists('justccell_option_string')
        ? justccell_option_string('store_age_gate_confirm_label', __('I am 18 or Older', 'justccell'))
        : __('I am 18 or Older', 'justccell');

    $decline = function_exists('justccell_option_string')
        ? justccell_option_string('store_age_gate_decline_label', __('Under 18 / Exit', 'justccell'))
        : __('Under 18 / Exit', 'justccell');

    $decline_url = function_exists('justccell_option_string')
        ? justccell_option_string('store_age_gate_decline_url', 'https://www.google.com')
        : 'https://www.google.com';
    $decline_url = esc_url_raw($decline_url);
    if ($decline_url === '') {
        $decline_url = 'https://www.google.com';
    }

    $days = 30;
    if (function_exists('get_field')) {
        $raw_days = get_field('store_age_gate_cookie_days', 'option');
        if (is_numeric($raw_days)) {
            $days = max(1, min(365, (int) $raw_days));
        }
    }

    return [
        'title'          => $title,
        'body_html'      => $body,
        'confirm_label'  => $confirm,
        'decline_label'  => $decline,
        'decline_url'    => $decline_url,
        'cookie_days'    => $days,
    ];
}

add_action('wp_enqueue_scripts', static function (): void {
    if (is_admin() || !justccell_age_gate_should_render()) {
        return;
    }

    wp_enqueue_script(
        'justccell-age-gate',
        JUSTCCELL_FEATURES_URL . 'assets/js/age-gate.js',
        [],
        JUSTCCELL_FEATURES_VERSION,
        true
    );
}, 25);

add_action('wp_footer', static function (): void {
    if (!justccell_age_gate_should_render()) {
        return;
    }

    get_template_part('template-parts/chrome/age-gate', null, justccell_age_gate_settings());
}, 5);
