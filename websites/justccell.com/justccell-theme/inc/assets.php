<?php
/**
 * Frontend asset pipeline. One CSS file, one JS file. No jQuery.
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

    if (is_page(['about', 'technology', 'solution', 'safety', 'research', 'manufacture', 'contact'])) {
        wp_enqueue_style(
            'justccell-pages',
            JUSTCCELL_URI . '/assets/css/pages.css',
            ['justccell-globals'],
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
            'justccell-product',
            JUSTCCELL_URI . '/assets/js/product.js',
            [],
            JUSTCCELL_VERSION,
            true
        );
    }

    wp_enqueue_script(
        'justccell-main',
        JUSTCCELL_URI . '/assets/js/main.js',
        [],
        JUSTCCELL_VERSION,
        true
    );
}, 20);

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
