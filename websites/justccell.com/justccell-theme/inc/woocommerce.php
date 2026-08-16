<?php
/**
 * Inquiry-first WooCommerce: catalog + specs, quote CTA instead of cart chrome.
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', static function (): void {
    add_theme_support('woocommerce', [
        'thumbnail_image_width' => 720,
        'single_image_width'    => 960,
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
});

add_filter('woocommerce_enqueue_styles', static function ($styles) {
    if (!is_array($styles)) {
        return [];
    }
    unset($styles['woocommerce-general'], $styles['woocommerce-layout'], $styles['woocommerce-smallscreen']);
    return $styles;
});

add_action('wp_enqueue_scripts', static function (): void {
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('wc-blocks-vendors-style');
    if (!is_checkout() && !is_cart() && !is_account_page()) {
        wp_dequeue_script('wc-cart-fragments');
        wp_dequeue_script('woocommerce');
        wp_dequeue_script('sourcebuster-js');
    }
}, 99);

add_filter('woocommerce_add_to_cart_redirect', static function (): string {
    return justccell_inquiry_url();
});

remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

add_action('woocommerce_after_shop_loop_item', static function (): void {
    global $product;
    if (!$product instanceof WC_Product) {
        return;
    }
    $sku = (string) $product->get_sku();
    echo '<a class="btn btn--primary product-card__cta" href="' . esc_url(justccell_inquiry_url($sku)) . '">';
    esc_html_e('Request sample & quote', 'justccell');
    echo '</a>';
}, 20);

add_action('woocommerce_single_product_summary', static function (): void {
    global $product;
    if (!$product instanceof WC_Product) {
        return;
    }
    $sku = (string) $product->get_sku();
    echo '<p class="product-hero__actions"><a class="btn btn--primary" href="' . esc_url(justccell_inquiry_url($sku)) . '">';
    esc_html_e('Get samples and quotes', 'justccell');
    echo '</a></p>';
}, 30);

add_filter('woocommerce_product_tabs', static function (array $tabs): array {
    unset($tabs['reviews']);
    if (isset($tabs['additional_information'])) {
        $tabs['additional_information']['title'] = __('Technical specs', 'justccell');
    }
    return $tabs;
});
