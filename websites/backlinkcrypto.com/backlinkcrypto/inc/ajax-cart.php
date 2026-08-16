<?php
/**
 * AJAX cart + drawer endpoints.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('wp_ajax_bc_add_to_cart', 'backlinkcrypto_ajax_add_to_cart');
add_action('wp_ajax_nopriv_bc_add_to_cart', 'backlinkcrypto_ajax_add_to_cart');
add_action('wp_ajax_bc_add_to_cart_bulk', 'backlinkcrypto_ajax_add_to_cart_bulk');
add_action('wp_ajax_nopriv_bc_add_to_cart_bulk', 'backlinkcrypto_ajax_add_to_cart_bulk');
add_action('wp_ajax_bc_cart_drawer', 'backlinkcrypto_ajax_cart_drawer');
add_action('wp_ajax_nopriv_bc_cart_drawer', 'backlinkcrypto_ajax_cart_drawer');
add_action('wp_ajax_bc_update_cart_qty', 'backlinkcrypto_ajax_update_cart_qty');
add_action('wp_ajax_nopriv_bc_update_cart_qty', 'backlinkcrypto_ajax_update_cart_qty');
add_action('wp_ajax_bc_remove_cart_item', 'backlinkcrypto_ajax_remove_cart_item');
add_action('wp_ajax_nopriv_bc_remove_cart_item', 'backlinkcrypto_ajax_remove_cart_item');

function backlinkcrypto_ajax_add_to_cart(): void
{
    if (!function_exists('WC')) {
        wp_send_json_error(['message' => 'WooCommerce unavailable'], 500);
    }
    if (is_null(WC()->cart)) {
        wc_load_cart();
    }

    $product_id = absint($_POST['product_id'] ?? 0);
    $qty = max(1, absint($_POST['quantity'] ?? 1));

    if (!$product_id) {
        wp_send_json_error(['message' => 'Missing product'], 400);
    }

    $product = wc_get_product($product_id);
    if (!$product || !$product->is_purchasable() || !$product->is_in_stock()) {
        wp_send_json_error(['message' => 'Product unavailable'], 400);
    }

    $cart_item_key = WC()->cart->add_to_cart($product_id, $qty);
    if (!$cart_item_key) {
        wp_send_json_error(['message' => 'Could not add to cart'], 400);
    }

    wp_send_json_success(backlinkcrypto_cart_payload([
        'added' => $product->get_name(),
    ]));
}

function backlinkcrypto_ajax_add_to_cart_bulk(): void
{
    if (!function_exists('WC')) {
        wp_send_json_error(['message' => 'WooCommerce unavailable'], 500);
    }
    if (is_null(WC()->cart)) {
        wc_load_cart();
    }

    $raw = $_POST['product_ids'] ?? [];
    if (is_string($raw)) {
        $raw = array_filter(array_map('trim', explode(',', $raw)));
    }
    if (!is_array($raw)) {
        wp_send_json_error(['message' => 'Missing products'], 400);
    }

    $ids = array_values(array_unique(array_filter(array_map('absint', $raw))));
    if ($ids === []) {
        wp_send_json_error(['message' => 'No sites selected'], 400);
    }
    if (count($ids) > 40) {
        wp_send_json_error(['message' => 'Select up to 40 sites at once'], 400);
    }

    $added_names = [];
    $failed = 0;
    foreach ($ids as $product_id) {
        $product = wc_get_product($product_id);
        if (!$product || !$product->is_purchasable() || !$product->is_in_stock()) {
            $failed++;
            continue;
        }
        $key = WC()->cart->add_to_cart($product_id, 1);
        if ($key) {
            $domain = (string) get_post_meta($product_id, '_bc_domain', true);
            $added_names[] = $domain !== '' ? $domain : $product->get_name();
        } else {
            $failed++;
        }
    }

    if ($added_names === []) {
        wp_send_json_error(['message' => 'Could not add selected sites'], 400);
    }

    $label = count($added_names) === 1
        ? $added_names[0]
        : sprintf('%d sites', count($added_names));

    wp_send_json_success(backlinkcrypto_cart_payload([
        'added'       => $label,
        'added_count' => count($added_names),
        'failed'      => $failed,
    ]));
}

function backlinkcrypto_ajax_cart_drawer(): void
{
    if (!function_exists('WC')) {
        wp_send_json_error(['message' => 'WooCommerce unavailable'], 500);
    }
    if (is_null(WC()->cart)) {
        wc_load_cart();
    }

    wp_send_json_success(backlinkcrypto_cart_payload());
}

function backlinkcrypto_ajax_update_cart_qty(): void
{
    if (!function_exists('WC')) {
        wp_send_json_error(['message' => 'WooCommerce unavailable'], 500);
    }
    if (is_null(WC()->cart)) {
        wc_load_cart();
    }

    $key = sanitize_text_field((string) ($_POST['key'] ?? ''));
    $qty = absint($_POST['quantity'] ?? 0);

    if ($key === '') {
        wp_send_json_error(['message' => 'Missing item'], 400);
    }

    if ($qty <= 0) {
        WC()->cart->remove_cart_item($key);
    } else {
        WC()->cart->set_quantity($key, $qty, true);
    }

    wp_send_json_success(backlinkcrypto_cart_payload());
}

function backlinkcrypto_ajax_remove_cart_item(): void
{
    if (!function_exists('WC')) {
        wp_send_json_error(['message' => 'WooCommerce unavailable'], 500);
    }
    if (is_null(WC()->cart)) {
        wc_load_cart();
    }

    $key = sanitize_text_field((string) ($_POST['key'] ?? ''));
    if ($key === '') {
        wp_send_json_error(['message' => 'Missing item'], 400);
    }

    WC()->cart->remove_cart_item($key);
    wp_send_json_success(backlinkcrypto_cart_payload());
}

/**
 * @param array<string,mixed> $extra
 * @return array<string,mixed>
 */
function backlinkcrypto_cart_payload(array $extra = []): array
{
    $items = [];
    foreach (WC()->cart->get_cart() as $key => $item) {
        /** @var WC_Product $product */
        $product = $item['data'];
        $domain = (string) get_post_meta($product->get_id(), '_bc_domain', true);
        $items[] = [
            'key'      => $key,
            'id'       => $product->get_id(),
            'name'     => $domain !== '' ? $domain : $product->get_name(),
            'qty'      => (int) $item['quantity'],
            'price'    => WC()->cart->get_product_price($product),
            'subtotal' => WC()->cart->get_product_subtotal($product, (int) $item['quantity']),
            'remove'   => wc_get_cart_remove_url($key),
        ];
    }

    return array_merge([
        'count'     => (int) WC()->cart->get_cart_contents_count(),
        'subtotal'  => WC()->cart->get_cart_subtotal(),
        'total'     => WC()->cart->get_total(),
        'cart_url'  => wc_get_cart_url(),
        'checkout'  => wc_get_checkout_url(),
        'is_empty'  => WC()->cart->is_empty(),
        'items'     => $items,
        'fragments' => apply_filters('woocommerce_add_to_cart_fragments', []),
    ], $extra);
}
