<?php
/**
 * Site breadcrumbs — Rank Math owns the trail + schema.
 *
 * Theme only positions the output and paints the ccell pin / chevron.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Current crumb label for the theme fallback only.
 */
function justccell_breadcrumb_current_label(): string
{
    if (is_front_page()) {
        return __('Home', 'justccell');
    }
    if (is_home()) {
        return __('Discover', 'justccell');
    }
    if (is_search()) {
        return __('Search', 'justccell');
    }
    if (is_404()) {
        return __('Page not found', 'justccell');
    }
    if (is_singular()) {
        $title = trim((string) get_the_title());
        if ($title !== '') {
            return $title;
        }
    }
    return trim(wp_get_document_title());
}

/**
 * Rank Math HTML, if the plugin can render a trail.
 */
function justccell_rank_math_breadcrumb_html(): string
{
    if (function_exists('rank_math_the_breadcrumbs')) {
        ob_start();
        rank_math_the_breadcrumbs();
        $html = trim((string) ob_get_clean());
        if ($html !== '') {
            return $html;
        }
    }

    if (function_exists('rank_math_get_breadcrumbs')) {
        return trim((string) rank_math_get_breadcrumbs());
    }

    return '';
}

/**
 * Strip Rank Math's outer <nav> so the theme wrapper is the landmark.
 */
function justccell_unwrap_rank_math_breadcrumb(string $html): string
{
    $html = trim($html);
    if ($html === '') {
        return '';
    }
    $html = (string) preg_replace('#^<nav\b[^>]*>#i', '', $html, 1);
    $html = (string) preg_replace('#</nav>\s*$#i', '', $html, 1);
    return trim($html);
}

/**
 * Theme-only trail when Rank Math is off or returns empty.
 */
function justccell_fallback_breadcrumb_html(): string
{
    $home = '<a href="' . esc_url(home_url('/')) . '">' . esc_html__('Home', 'justccell') . '</a>';
    $sep  = '<span class="separator" aria-hidden="true">&gt;</span>';
    $now  = '<span class="last">' . esc_html(justccell_breadcrumb_current_label()) . '</span>';
    return '<p>' . $home . $sep . $now . '</p>';
}

/**
 * Print breadcrumbs. Rank Math first; matching fallback otherwise.
 *
 * @param string $class Wrapper class for theme positioning (hero overlay, etc.).
 */
function justccell_the_breadcrumbs(string $class = 'jc-crumbs'): void
{
    if (is_front_page()) {
        return;
    }

    $class = trim($class) !== '' ? trim($class) : 'jc-crumbs';
    $label = __('Breadcrumb', 'justccell');
    $html  = justccell_unwrap_rank_math_breadcrumb(justccell_rank_math_breadcrumb_html());
    if ($html === '') {
        $html = justccell_fallback_breadcrumb_html();
    }

    echo '<nav class="' . esc_attr($class) . '" aria-label="' . esc_attr($label) . '">';
    echo $html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Rank Math / escaped fallback.
    echo '</nav>';
}

add_filter('rank_math/frontend/breadcrumb/settings', static function ($settings) {
    if (!is_array($settings)) {
        return $settings;
    }
    $settings['home']           = true;
    $settings['separator']      = '>';
    $settings['remove_title']   = false;
    $settings['hide_tax_name']  = true;
    $settings['show_ancestors'] = true;
    return $settings;
});

/**
 * Keep the trail as Home > Category > Product — drop Woo Shop / Products roots.
 *
 * @param array<int, mixed> $crumbs
 * @return array<int, mixed>
 */
add_filter('rank_math/frontend/breadcrumb/items', static function ($crumbs) {
    if (!is_array($crumbs) || $crumbs === []) {
        return $crumbs;
    }

    $shop_urls = [];
    if (function_exists('wc_get_page_id')) {
        $shop_id = (int) wc_get_page_id('shop');
        if ($shop_id > 0) {
            $shop_urls[] = untrailingslashit((string) get_permalink($shop_id));
        }
    }

    $skip_labels = ['shop', 'products', 'product', 'store'];
    $kept        = [];
    foreach ($crumbs as $crumb) {
        if (!is_array($crumb)) {
            $kept[] = $crumb;
            continue;
        }
        $label = strtolower(trim(wp_strip_all_tags((string) ($crumb[0] ?? ''))));
        $url   = untrailingslashit((string) ($crumb[1] ?? ''));
        if ($label !== '' && in_array($label, $skip_labels, true)) {
            continue;
        }
        if ($url !== '' && in_array($url, $shop_urls, true)) {
            continue;
        }
        if ($url !== '' && preg_match('#/product-category/([^/]+)/?#', $url, $m)) {
            $crumb[1] = justccell_category_url((string) $m[1]);
        }
        $kept[] = $crumb;
    }

    return $kept !== [] ? $kept : $crumbs;
}, 20);

add_action('init', static function (): void {
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 20);
    remove_action('woocommerce_before_main_content', 'woocommerce_breadcrumb', 10);
});
