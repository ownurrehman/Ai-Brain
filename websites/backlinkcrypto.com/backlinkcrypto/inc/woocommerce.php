<?php
/**
 * WooCommerce hooks and wrappers.
 * Marketplace is table/metric based — no product images anywhere.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', static function (): void {
    // Use theme wrappers instead of default WC wrappers.
    remove_action('woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10);
    remove_action('woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10);
    remove_action('woocommerce_sidebar', 'woocommerce_get_sidebar', 10);
});

add_action('woocommerce_before_main_content', static function (): void {
    echo '<main class="bc-main"><div class="bc-container bc-shop-layout">';
}, 10);

add_action('woocommerce_after_main_content', static function (): void {
    echo '</div></main>';
}, 10);

add_action('init', static function (): void {
    remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5);

    // Loop + single: never render product images / gallery.
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10);
    remove_action('woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10);
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10);
    remove_action('woocommerce_before_single_product_summary', 'woocommerce_show_product_images', 20);
    remove_action('woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20);
});

add_filter('woocommerce_enqueue_styles', static function ($styles) {
    // Keep WooCommerce CSS on cart / checkout / account so forms work.
    if (function_exists('is_cart') && (is_cart() || is_checkout() || is_account_page())) {
        return $styles;
    }
    // Marketplace/homepage uses our own table styles — skip default shop CSS.
    return [];
});

add_filter('body_class', static function (array $classes): array {
    $classes[] = 'backlinkcrypto-theme';
    $classes[] = 'bc-no-product-images';
    return $classes;
});

add_filter('woocommerce_add_to_cart_redirect', static function ($url) {
    // Keep shoppers on the page; AJAX handles feedback.
    if (wp_doing_ajax()) {
        return $url;
    }
    return false;
});

add_filter('woocommerce_cart_redirect_after_error', '__return_false');

/**
 * Never output a product/placeholder image in storefront templates.
 */
add_filter('woocommerce_product_get_image', static function ($image) {
    // Front + AJAX cart: never show product / placeholder images.
    if (is_admin() && !wp_doing_ajax()) {
        return $image;
    }
    return '';
}, 10, 1);

add_filter('woocommerce_placeholder_img', '__return_empty_string');
add_filter('woocommerce_placeholder_img_src', static function (): string {
    return '';
});

/** Cart / mini-cart / checkout: strip thumbnail column. */
add_filter('woocommerce_cart_item_thumbnail', '__return_empty_string', 100);
add_filter('woocommerce_cart_item_name', static function (string $name, $cart_item): string {
    // Prefer domain meta in name if available.
    $product_id = isset($cart_item['product_id']) ? (int) $cart_item['product_id'] : 0;
    if ($product_id > 0) {
        $domain = (string) get_post_meta($product_id, '_bc_domain', true);
        if ($domain !== '') {
            return esc_html($domain);
        }
    }
    return $name;
}, 10, 2);

/** Disable WC image size registration work for this storefront (no thumbnails needed). */
add_filter('woocommerce_get_image_size_thumbnail', static function (): array {
    return ['width' => 1, 'height' => 1, 'crop' => 0];
});
add_filter('woocommerce_get_image_size_single', static function (): array {
    return ['width' => 1, 'height' => 1, 'crop' => 0];
});
add_filter('woocommerce_get_image_size_gallery_thumbnail', static function (): array {
    return ['width' => 1, 'height' => 1, 'crop' => 0];
});

/** Hide Product image + Gallery metaboxes in product editor. */
add_action('do_meta_boxes', static function (string $post_type): void {
    if ($post_type !== 'product') {
        return;
    }
    remove_meta_box('postimagediv', 'product', 'side');
    remove_meta_box('woocommerce-product-images', 'product', 'side');
}, 40);

/** Drop image column from Products list table. */
add_filter('manage_edit-product_columns', static function (array $columns): array {
    unset($columns['thumb']);
    return $columns;
}, 20);

/** Admin notice so editors know images are intentionally unused. */
add_action('admin_notices', static function (): void {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'product') {
        return;
    }
    echo '<div class="notice notice-info is-dismissible"><p>';
    echo esc_html__('Backlink Crypto uses a metric table marketplace — product images are disabled. You do not need to upload or optimize any product photos.', 'backlinkcrypto');
    echo '</p></div>';
});

/** CSS: hide leftover product image UI only (never blog featured images). */
add_action('wp_head', static function (): void {
    echo '<style id="bc-no-product-images">
.bc-no-product-images .woocommerce-product-gallery,
.bc-no-product-images .product .images,
.bc-no-product-images .product .attachment-woocommerce_thumbnail,
.bc-no-product-images .product .wp-post-image,
.bc-no-product-images .bc-card__media,
.bc-no-product-images td.product-thumbnail,
.bc-no-product-images .product-thumbnail,
.bc-no-product-images .cart_item img,
.bc-no-product-images .woocommerce-cart-form img,
.bc-no-product-images .mini_cart_item img,
.bc-no-product-images.single-product .wp-post-image,
.bc-no-product-images.woocommerce-cart .wp-post-image,
.bc-no-product-images.woocommerce-checkout .wp-post-image { display:none !important; }
.bc-no-product-images .woocommerce div.product div.summary { width:100% !important; float:none !important; margin:0 !important; }
/* Blog / post featured images must remain visible */
.bc-blog .wp-post-image,
.bc-blog-card__media img,
.bc-article__thumb img { display:block !important; }
</style>';
}, 99);

add_action('admin_head', static function (): void {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'product') {
        return;
    }
    echo '<style id="bc-admin-no-product-images">
#postimagediv, #woocommerce-product-images, .column-thumb { display:none !important; }
</style>';
});

/** Shop/category archives: send shoppers to the marketplace catalog. */
add_action('template_redirect', static function (): void {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    if (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) {
        $dest = function_exists('backlinkcrypto_marketplace_url')
            ? backlinkcrypto_marketplace_url()
            : home_url('/marketplace/');
        wp_safe_redirect($dest);
        exit;
    }
});

/**
 * Ensure Cart / Checkout / My Account pages contain classic WC shortcodes.
 * (Empty page content = blank cart UI even when the session has items.)
 */
add_action('init', static function (): void {
    if (get_option('bc_commerce_pages_fixed') === BACKLINKCRYPTO_VERSION) {
        return;
    }
    if (!function_exists('wc_get_page_id')) {
        return;
    }

    $map = [
        'cart'      => ['shortcode' => 'woocommerce_cart', 'block' => 'woocommerce/cart'],
        'checkout'  => ['shortcode' => 'woocommerce_checkout', 'block' => 'woocommerce/checkout'],
        'myaccount' => ['shortcode' => 'woocommerce_my_account', 'block' => 'woocommerce/my-account'],
    ];

    foreach ($map as $key => $meta) {
        $page_id = (int) wc_get_page_id($key);
        if ($page_id <= 0) {
            continue;
        }
        $post = get_post($page_id);
        if (!$post) {
            continue;
        }
        $raw = (string) $post->post_content;
        if (has_shortcode($raw, $meta['shortcode']) || has_block($meta['block'], $post)) {
            continue;
        }
        // Empty or missing WC markup — restore classic shortcode.
        if (trim(wp_strip_all_tags($raw)) === '') {
            wp_update_post([
                'ID'           => $page_id,
                'post_content' => '[' . $meta['shortcode'] . ']',
            ]);
        }
    }

    update_option('bc_commerce_pages_fixed', BACKLINKCRYPTO_VERSION);

    // Keep WooCommerce shop page title aligned with marketplace branding.
    $shop_id = (int) wc_get_page_id('shop');
    if ($shop_id > 0) {
        $shop = get_post($shop_id);
        if ($shop && $shop->post_title !== 'Marketplace') {
            wp_update_post(['ID' => $shop_id, 'post_title' => 'Marketplace']);
        }
    }
}, 50);
