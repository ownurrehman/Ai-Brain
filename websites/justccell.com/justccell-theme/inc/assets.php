<?php
/**
 * Frontend asset pipeline. One CSS file, one JS file. No jQuery.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_enqueue_scripts', static function (): void {
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('classic-theme-styles');
    wp_dequeue_style('global-styles');

    wp_enqueue_style(
        'justccell-globals',
        JUSTCCELL_URI . '/assets/css/globals.css',
        [],
        JUSTCCELL_VERSION
    );
    wp_enqueue_style(
        'justccell-chrome',
        JUSTCCELL_URI . '/assets/css/chrome.css',
        ['justccell-globals'],
        JUSTCCELL_VERSION
    );

    if (is_admin_bar_showing()) {
        wp_enqueue_style(
            'justccell-admin-bar',
            JUSTCCELL_URI . '/assets/css/admin-bar.css',
            ['justccell-chrome'],
            JUSTCCELL_VERSION
        );
    }

    if (is_front_page() || justccell_is_product_clone() || justccell_is_catalog_clone()) {
        wp_enqueue_style(
            'justccell-home',
            JUSTCCELL_URI . '/assets/css/home.css',
            ['justccell-globals'],
            JUSTCCELL_VERSION
        );
    }

    if (justccell_is_catalog_clone()) {
        wp_enqueue_style(
            'justccell-catalog',
            JUSTCCELL_URI . '/assets/css/catalog.css',
            ['justccell-globals', 'justccell-home'],
            JUSTCCELL_VERSION
        );
    }

    $brand = is_page() && function_exists('justccell_static_pages')
        && isset(justccell_static_pages()[(string) get_post_field('post_name')]);
    $coming_soon = is_page()
        && function_exists('justccell_page_shows_coming_soon')
        && justccell_page_shows_coming_soon((int) get_queried_object_id());
    $landing = is_front_page()
        && function_exists('justccell_current_store_landing')
        && is_array(justccell_current_store_landing());
    $bio_heating = function_exists('justccell_is_bio_page') && justccell_is_bio_page();
    if ($bio_heating) {
        wp_enqueue_style(
            'justccell-bio-heating',
            JUSTCCELL_URI . '/assets/css/bio-heating.css',
            ['justccell-globals', 'justccell-chrome'],
            JUSTCCELL_VERSION
        );
    }

    $catalog = function_exists('justccell_is_catalog_clone') && justccell_is_catalog_clone();
    $discover = !$catalog && (
        is_home()
        || is_singular('post')
        || is_page('discover')
        || is_category()
        || (function_exists('justccell_is_discover_view') && justccell_is_discover_view())
    );
    if ($brand || $coming_soon || is_page('contact') || is_404() || is_search() || $landing || $discover) {
        wp_enqueue_style(
            'justccell-pages',
            JUSTCCELL_URI . '/assets/css/pages.css',
            ['justccell-globals', 'justccell-chrome'],
            JUSTCCELL_VERSION
        );
    }

    if ($discover) {
        wp_enqueue_style(
            'justccell-discover',
            JUSTCCELL_URI . '/assets/css/discover.css',
            ['justccell-globals', 'justccell-chrome', 'justccell-pages'],
            JUSTCCELL_VERSION
        );
    }

    if (justccell_is_product_clone()) {
        wp_enqueue_style(
            'justccell-product',
            JUSTCCELL_URI . '/assets/css/product.css',
            ['justccell-globals', 'justccell-home'],
            JUSTCCELL_VERSION
        );
        wp_enqueue_script(
            'justccell-product-high-scroll',
            JUSTCCELL_URI . '/assets/js/product-high-scroll.js',
            [],
            JUSTCCELL_VERSION,
            true
        );
        if (class_exists('WooCommerce')) {
            wp_enqueue_script('wc-add-to-cart-variation');
        }
        wp_enqueue_script(
            'justccell-product',
            JUSTCCELL_URI . '/assets/js/product.js',
            class_exists('WooCommerce') ? ['justccell-cart', 'jquery', 'wc-add-to-cart-variation'] : [],
            JUSTCCELL_VERSION,
            true
        );
        wp_dequeue_script('selectWoo');
        wp_dequeue_script('select2');
        wp_dequeue_style('select2');
        wp_dequeue_style('woocommerce_select2');
    }

    wp_enqueue_script(
        'justccell-main',
        JUSTCCELL_URI . '/assets/js/main.js',
        [],
        JUSTCCELL_VERSION,
        true
    );
}, 20);

add_action('wp_enqueue_scripts', static function (): void {
    if (!class_exists('WooCommerce')) {
        return;
    }

    $commerce = (function_exists('is_cart') && is_cart())
        || (function_exists('is_checkout') && is_checkout())
        || (function_exists('is_account_page') && is_account_page());

    if (!$commerce) {
        return;
    }

    $deps = ['justccell-globals', 'justccell-chrome'];
    if (wp_style_is('justccell-commerce', 'enqueued') || wp_style_is('justccell-commerce', 'registered')) {
        $deps[] = 'justccell-commerce';
    }

    wp_enqueue_style(
        'justccell-woocommerce',
        JUSTCCELL_URI . '/assets/css/woocommerce.css',
        $deps,
        JUSTCCELL_VERSION
    );
}, 30);

add_filter('style_loader_tag', static function (string $html, string $handle): string {
    if ($handle !== 'justccell-globals') {
        return $html;
    }

    $href = JUSTCCELL_URI . '/assets/css/globals.css?ver=' . JUSTCCELL_VERSION;
    return '<link rel="preload" href="' . esc_url($href) . '" as="style">' . $html;
}, 10, 2);

add_action('wp_head', static function (): void {
    $font_400 = JUSTCCELL_URI . '/assets/fonts/montserrat-400.woff2';
    echo '<link rel="preload" href="' . esc_url($font_400) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}, 1);

add_filter('script_loader_tag', static function (string $tag, string $handle): string {
    if ($handle === 'justccell-main') {
        return str_replace(' src', ' defer src', $tag);
    }
    return $tag;
}, 10, 2);
