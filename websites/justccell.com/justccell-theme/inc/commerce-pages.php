<?php
/**
 * WooCommerce cart, checkout, and order-received presentation.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * True on checkout order-received endpoint.
 */
function justccell_is_order_received_page(): bool
{
    return function_exists('is_checkout')
        && is_checkout()
        && function_exists('is_wc_endpoint_url')
        && is_wc_endpoint_url('order-received');
}

/**
 * @return list<array{label: string, value: string}>
 */
function justccell_order_received_meta_rows(WC_Order $order): array
{
    $rows = [
        [
            'label' => __('Order number', 'justccell'),
            'value' => $order->get_order_number(),
        ],
        [
            'label' => __('Date', 'justccell'),
            'value' => wc_format_datetime($order->get_date_created()),
        ],
    ];

    if ($order->get_billing_email() !== '') {
        $rows[] = [
            'label' => __('Email', 'justccell'),
            'value' => $order->get_billing_email(),
        ];
    }

    $rows[] = [
        'label' => __('Total', 'justccell'),
        'value' => function_exists('justccell_decode_money_text')
            ? justccell_decode_money_text($order->get_formatted_order_total())
            : wp_strip_all_tags($order->get_formatted_order_total()),
    ];

    if ($order->get_payment_method_title() !== '') {
        $rows[] = [
            'label' => __('Payment method', 'justccell'),
            'value' => $order->get_payment_method_title(),
        ];
    }

    return $rows;
}

/**
 * @return list<array{key: string, label: string, value: string}>
 */
function justccell_order_item_meta_lines(WC_Order_Item_Product $item): array
{
    $lines = [];
    foreach ($item->get_formatted_meta_data('') as $meta) {
        $display_key = function_exists('justccell_decode_money_text')
            ? justccell_decode_money_text((string) $meta->display_key)
            : wp_strip_all_tags((string) $meta->display_key);
        $display_val = function_exists('justccell_decode_money_text')
            ? justccell_decode_money_text((string) $meta->display_value)
            : wp_strip_all_tags((string) $meta->display_value);
        if ($display_key === '' && $display_val === '') {
            continue;
        }
        $lines[] = [
            'key'   => (string) $meta->key,
            'label' => $display_key,
            'value' => $display_val,
        ];
    }

    return $lines;
}

add_action('after_setup_theme', static function (): void {
    if (!class_exists('WooCommerce')) {
        return;
    }

    remove_action('woocommerce_thankyou', 'woocommerce_order_details_table', 10);
}, 20);

add_action('wp_enqueue_scripts', static function (): void {
    if (!class_exists('WooCommerce')) {
        return;
    }

    $load = function_exists('is_cart') && (
        is_cart()
        || is_checkout()
        || is_account_page()
        || is_shop()
        || is_product_taxonomy()
        || is_search()
        || justccell_is_order_received_page()
    );

    if (!$load) {
        return;
    }

    wp_enqueue_style(
        'justccell-commerce',
        JUSTCCELL_URI . '/assets/css/commerce.css',
        ['justccell-globals', 'justccell-chrome'],
        JUSTCCELL_VERSION
    );

    if (is_cart() || is_checkout() || is_account_page()) {
        wp_enqueue_script(
            'justccell-cart-wording',
            JUSTCCELL_URI . '/assets/js/cart-wording.js',
            [],
            JUSTCCELL_VERSION,
            true
        );
    }
}, 25);

/**
 * Branded heading above the classic WooCommerce cart form.
 */
function justccell_cart_page_header(): void
{
    if (!function_exists('WC') || !WC()->cart || WC()->cart->is_empty()) {
        return;
    }

    $count = (int) WC()->cart->get_cart_contents_count();
    $shop  = function_exists('wc_get_page_permalink') ? (string) wc_get_page_permalink('shop') : home_url('/');
    if ($shop === '' || $shop === '0') {
        $shop = home_url('/all-in-ones/');
    }
    ?>
    <header class="jc-cart-head">
        <div class="jc-cart-head__copy">
            <p class="jc-cart-head__kicker"><?php echo esc_html(justccell_cart_label()); ?></p>
            <h1 class="jc-cart-head__title"><?php esc_html_e('Your cart', 'justccell'); ?></h1>
            <p class="jc-cart-head__count">
                <?php
                echo esc_html(
                    sprintf(
                        /* translators: %d: number of items in the cart */
                        _n('%d item', '%d items', $count, 'justccell'),
                        $count
                    )
                );
                ?>
            </p>
        </div>
        <a class="jc-cart-head__shop" href="<?php echo esc_url($shop); ?>">
            <?php esc_html_e('Continue shopping', 'justccell'); ?>
        </a>
    </header>
    <?php
}
add_action('woocommerce_before_cart', 'justccell_cart_page_header', 5);

add_filter('body_class', static function (array $classes): array {
    if (justccell_is_order_received_page()) {
        $classes[] = 'jc-order-received-page';
    }
    if (function_exists('is_cart') && is_cart()) {
        $classes[] = 'jc-cart-page';
    }
    if (function_exists('is_checkout') && is_checkout() && !justccell_is_order_received_page()) {
        $classes[] = 'jc-checkout-page';
    }
    if (function_exists('is_account_page') && is_account_page()) {
        $classes[] = 'jc-account-page';
        if (!is_user_logged_in()) {
            $classes[] = 'jc-account-page--auth';
        }
    }
    return $classes;
});

add_filter('woocommerce_page_title', static function (string $title): string {
    if (justccell_is_order_received_page() || (function_exists('is_account_page') && is_account_page())) {
        return '';
    }
    if (function_exists('is_cart') && is_cart()) {
        return justccell_cart_label();
    }
    return $title;
}, 30);

add_filter('document_title_parts', static function (array $parts): array {
    if (justccell_is_order_received_page()) {
        $parts['title'] = __('Order confirmed', 'justccell');
    }
    return $parts;
}, 30);

add_filter('pre_get_document_title', static function (string $title): string {
    if (justccell_is_order_received_page()) {
        return __('Order confirmed', 'justccell') . ' | ' . get_bloginfo('name');
    }
    return $title;
}, 30);

add_filter('rank_math/frontend/title', static function (string $title): string {
    if (justccell_is_order_received_page()) {
        return __('Order confirmed', 'justccell') . ' | ' . get_bloginfo('name');
    }
    return $title;
}, 30);
