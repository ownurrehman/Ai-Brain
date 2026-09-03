<?php
/**
 * AJAX add-to-cart + slide-out cart drawer (no full-page reload).
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
 * WooCommerce POST key for a product attribute taxonomy/name.
 */
function justccell_cart_woo_attribute_request_key(string $taxonomy): string
{
    $taxonomy = trim($taxonomy);
    if ($taxonomy === '') {
        return '';
    }
    if (str_starts_with($taxonomy, 'attribute_')) {
        return sanitize_title($taxonomy);
    }
    return 'attribute_' . sanitize_title($taxonomy);
}

/**
 * Convert a buy-box display value to the slug Woo expects on variations.
 */
function justccell_cart_normalize_attribute_value(string $taxonomy, string $value): string
{
    static $terms_by_taxonomy = [];

    $value = trim($value);
    if ($value === '') {
        return '';
    }
    if (taxonomy_exists($taxonomy)) {
        $term = get_term_by('name', $value, $taxonomy);
        if ($term instanceof WP_Term) {
            return $term->slug;
        }
        $term = get_term_by('slug', $value, $taxonomy);
        if ($term instanceof WP_Term) {
            return $term->slug;
        }
        $needle = strtolower($value);
        if (!isset($terms_by_taxonomy[$taxonomy])) {
            $loaded = get_terms([
                'taxonomy'   => $taxonomy,
                'hide_empty' => false,
            ]);
            $terms_by_taxonomy[$taxonomy] = is_array($loaded) ? $loaded : [];
        }
        $terms = $terms_by_taxonomy[$taxonomy];
        if ($terms !== []) {
            foreach ($terms as $term) {
                if (!$term instanceof WP_Term) {
                    continue;
                }
                if (strcasecmp($term->name, $value) === 0) {
                    return $term->slug;
                }
                if (sanitize_title($term->name) === sanitize_title($value)) {
                    return $term->slug;
                }
                if (strtolower($term->slug) === $needle) {
                    return $term->slug;
                }
            }
        }
        return sanitize_title($value);
    }
    return wc_clean($value);
}

/**
 * Map buy-box attr_* POST fields to Woo attribute_* keys (pa_* taxonomies).
 *
 * @return array<string, string>
 */
function justccell_cart_resolve_variation_attributes(int $product_id): array
{
    if ($product_id < 1 || !function_exists('justccell_product_buy_attributes')) {
        return [];
    }

    $by_public = [];
    foreach (justccell_product_buy_attributes($product_id) as $row) {
        $public = (string) ($row['key'] ?? '');
        $tax    = (string) ($row['taxonomy'] ?? '');
        if ($public === '' || $tax === '') {
            continue;
        }
        $entry = [
            'woo_key'  => justccell_cart_woo_attribute_request_key($tax),
            'taxonomy' => $tax,
        ];
        $by_public[$public] = $entry;
        $by_public[sanitize_title($public)] = $entry;
        if (str_starts_with($tax, 'pa_')) {
            $by_public[substr($tax, 3)] = $entry;
        }
        $by_public[sanitize_title($tax)] = $entry;
    }

    $resolved = [];
    $source   = array_merge($_REQUEST, $_POST);

    foreach ($source as $key => $val) {
        $key = (string) $key;
        if (!str_starts_with($key, 'attr_')) {
            continue;
        }
        $raw_public = substr($key, 5);
        $candidates = array_unique(array_filter([
            $raw_public,
            sanitize_title($raw_public),
            str_replace('_', '-', $raw_public),
            str_replace('-', '_', $raw_public),
        ]));
        $entry = null;
        foreach ($candidates as $candidate) {
            if (isset($by_public[$candidate])) {
                $entry = $by_public[$candidate];
                break;
            }
        }
        if ($entry === null) {
            continue;
        }
        $woo_key = (string) $entry['woo_key'];
        if ($woo_key === '') {
            continue;
        }
        $resolved[$woo_key] = justccell_cart_normalize_attribute_value(
            (string) $entry['taxonomy'],
            (string) wp_unslash($val)
        );
    }

    foreach ($source as $key => $val) {
        $key = (string) $key;
        if (!str_starts_with($key, 'attribute_')) {
            continue;
        }
        if (!isset($resolved[$key])) {
            $resolved[$key] = wc_clean(wp_unslash($val));
        }
    }

    return array_filter($resolved, static fn ($v) => (string) $v !== '');
}

/**
 * Resolve a variable product variation ID from buy-box / AJAX attribute payload.
 */
function justccell_cart_find_variation_id(WC_Product_Variable $product, array $attrs): int
{
    if ($attrs === []) {
        return 0;
    }

    $data_store   = WC_Data_Store::load('product');
    $variation_id = (int) $data_store->find_matching_product_variation($product, $attrs);
    if ($variation_id > 0) {
        return $variation_id;
    }

    foreach ($product->get_children() as $child_id) {
        $variation = wc_get_product((int) $child_id);
        if (!$variation instanceof WC_Product_Variation || $variation->get_status() !== 'publish') {
            continue;
        }
        if ($variation->get_stock_status() === 'outofstock') {
            continue;
        }

        $matches = true;
        foreach ($attrs as $woo_key => $wanted) {
            $tax = str_starts_with($woo_key, 'attribute_') ? substr($woo_key, 10) : $woo_key;
            $have = (string) $variation->get_attribute($tax);
            if ($have === '' || $have === 'any') {
                continue;
            }
            if ($have === $wanted) {
                continue;
            }
            if (sanitize_title($have) === sanitize_title((string) $wanted)) {
                continue;
            }
            if (taxonomy_exists($tax)) {
                $term = get_term_by('slug', (string) $wanted, $tax);
                if ($term instanceof WP_Term && strcasecmp($term->name, $have) === 0) {
                    continue;
                }
            }
            if (strcasecmp($have, (string) $wanted) === 0) {
                continue;
            }
            $matches = false;
            break;
        }
        if ($matches) {
            return (int) $child_id;
        }
    }

    return 0;
}

/**
 * Map buy-box attr_* POST keys → attribute_* and resolve variation_id.
 */
function justccell_cart_prepare_variable_add_to_cart_request(): void
{
    if (!isset($_REQUEST['add-to-cart'])) {
        return;
    }

    $product_id = absint(wp_unslash($_REQUEST['add-to-cart']));
    if ($product_id < 1 || !function_exists('wc_get_product')) {
        return;
    }

    $product = wc_get_product($product_id);
    if (!$product instanceof WC_Product || !$product->is_type('variable')) {
        return;
    }

    $attrs = justccell_cart_resolve_variation_attributes($product_id);

    if ($attrs === []) {
        foreach ($product->get_children() as $child_id) {
            $variation = wc_get_product((int) $child_id);
            if (!$variation instanceof WC_Product_Variation || $variation->get_status() !== 'publish') {
                continue;
            }
            if ($variation->get_stock_status() === 'outofstock') {
                continue;
            }
            foreach ($variation->get_attributes() as $tax => $value) {
                $tax   = (string) $tax;
                $value = (string) $value;
                if ($value === '') {
                    continue;
                }
                $attrs['attribute_' . sanitize_title($tax)] = $value;
            }
            $_POST['variation_id']    = (string) $variation->get_id();
            $_REQUEST['variation_id'] = (string) $variation->get_id();
            break;
        }
    } else {
        $variation_id = justccell_cart_find_variation_id($product, $attrs);
        if ($variation_id > 0) {
            $_POST['variation_id']    = (string) $variation_id;
            $_REQUEST['variation_id'] = (string) $variation_id;
        }
    }

    foreach ($attrs as $key => $value) {
        $_POST[$key]    = $value;
        $_REQUEST[$key] = $value;
    }
}

/**
 * @return array<string, string>
 */
function justccell_cart_variation_attributes_from_request(): array
{
    $product_id = absint(wp_unslash($_POST['add-to-cart'] ?? $_REQUEST['add-to-cart'] ?? 0));
    if ($product_id > 0) {
        return justccell_cart_resolve_variation_attributes($product_id);
    }

    $attrs = [];
    foreach ($_POST as $key => $val) {
        $key = (string) $key;
        if (str_starts_with($key, 'attribute_')) {
            $attrs[$key] = wc_clean(wp_unslash($val));
        }
    }
    return $attrs;
}

/**
 * Quote-only SKUs with tier bands are still addable to cart.
 */
function justccell_cart_product_has_tier_pricing(WC_Product $product): bool
{
    $config_id = $product->get_parent_id() > 0 ? (int) $product->get_parent_id() : (int) $product->get_id();
    if (!function_exists('justccell_get_product_tiered_pricing')) {
        return false;
    }
    return justccell_get_product_tiered_pricing($config_id) !== [];
}

add_filter('woocommerce_is_purchasable', static function ($purchasable, $product): bool {
    if ($purchasable || !$product instanceof WC_Product) {
        return (bool) $purchasable;
    }
    if (function_exists('justccell_laser_should_bypass_catalog_gate')
        && justccell_laser_should_bypass_catalog_gate($product)) {
        return true;
    }
    return justccell_cart_product_has_tier_pricing($product) ? true : (bool) $purchasable;
}, 998, 2);

/**
 * Verify add-to-cart nonce (cart drawer or legacy laser form).
 */
function justccell_cart_verify_add_nonce(): bool
{
    $nonce = isset($_POST['justccell_cart_nonce'])
        ? sanitize_key(wp_unslash((string) $_POST['justccell_cart_nonce']))
        : '';
    if ($nonce !== '' && wp_verify_nonce($nonce, 'justccell_cart')) {
        return true;
    }

    $wc_nonce = isset($_POST['woocommerce-add-to-cart-nonce'])
        ? sanitize_key(wp_unslash((string) $_POST['woocommerce-add-to-cart-nonce']))
        : '';
    return $wc_nonce !== '' && wp_verify_nonce($wc_nonce, 'woocommerce-add-to-cart');
}

/**
 * @return array{success:bool,message:string,data:array<string,mixed>}
 */
function justccell_process_add_to_cart(): array
{
    if (function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    if (!function_exists('WC') || !WC()->cart) {
        return [
            'success' => false,
            'message' => __('Cart is unavailable. Refresh and try again.', 'justccell'),
            'data'    => [],
        ];
    }

    if (!justccell_cart_verify_add_nonce()) {
        return [
            'success' => false,
            'message' => __('Your session expired. Refresh the page and try again.', 'justccell'),
            'data'    => [],
        ];
    }

    if (!isset($_POST['add-to-cart'])) {
        return [
            'success' => false,
            'message' => __('Product not found.', 'justccell'),
            'data'    => [],
        ];
    }

    justccell_cart_prepare_variable_add_to_cart_request();

    $product_id   = absint(wp_unslash($_POST['add-to-cart']));
    $quantity     = max(1, absint(wp_unslash($_POST['quantity'] ?? 1)));
    $variation_id = absint(wp_unslash($_POST['variation_id'] ?? 0));
    $product      = wc_get_product($product_id);

    if (!$product instanceof WC_Product) {
        return [
            'success' => false,
            'message' => __('Product not found.', 'justccell'),
            'data'    => [],
        ];
    }

    if ($product->is_type('variable') && $variation_id < 1) {
        return [
            'success' => false,
            'message' => __('Please choose product options before adding to cart.', 'justccell'),
            'data'    => [],
        ];
    }

    $cart_item_data = [];
    $laser          = function_exists('justccell_laser_request_enabled') && justccell_laser_request_enabled();

    if ($laser) {
        try {
            $cart_item_data = justccell_laser_ingest_cart_item_data([], $product_id);
        } catch (\Throwable $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => [],
            ];
        }

        if (empty($cart_item_data['justccell_laser']['enabled'])) {
            return [
                'success' => false,
                'message' => __('Engraving data was missing. Add your design and try again.', 'justccell'),
                'data'    => [],
            ];
        }
    }

    $passed = apply_filters(
        'woocommerce_add_to_cart_validation',
        true,
        $product_id,
        $quantity,
        $variation_id,
        $cart_item_data
    );

    if (!$passed) {
        $errors = wc_get_notices('error');
        wc_clear_notices();
        $message = __('Could not add this item to your cart.', 'justccell');
        if (is_array($errors) && $errors !== []) {
            $first = reset($errors);
            if (is_array($first) && isset($first['notice']) && is_string($first['notice'])) {
                $message = wp_strip_all_tags($first['notice']);
            }
        }
        return [
            'success' => false,
            'message' => $message,
            'data'    => [],
        ];
    }

    $added = WC()->cart->add_to_cart(
        $product_id,
        $quantity,
        $variation_id,
        justccell_cart_variation_attributes_from_request(),
        $cart_item_data
    );

    if (!$added) {
        $errors = wc_get_notices('error');
        wc_clear_notices();
        $message = __('Could not add this item to your cart.', 'justccell');
        if (is_array($errors) && $errors !== []) {
            $first = reset($errors);
            if (is_array($first) && isset($first['notice']) && is_string($first['notice'])) {
                $message = wp_strip_all_tags($first['notice']);
            }
        }
        return [
            'success' => false,
            'message' => $message,
            'data'    => [],
        ];
    }

    if ($laser && function_exists('justccell_laser_register_session_line')) {
        justccell_laser_register_session_line($product_id, $variation_id);
    }

    wc_clear_notices();

    return [
        'success' => true,
        'message' => $laser
            ? __('Engraved item added to your cart.', 'justccell')
            : __('Added to your cart.', 'justccell'),
        'data'    => justccell_cart_drawer_payload(),
    ];
}

/**
 * @return array<string, mixed>
 */
function justccell_cart_drawer_payload(): array
{
    $cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');
    $payload  = [
        'count'         => 0,
        'subtotal_html' => '',
        'cart_url'      => $cart_url,
        'items'         => [],
    ];

    if (!function_exists('WC') || !WC()->cart) {
        return $payload;
    }

    $cart = WC()->cart;
    $cart->calculate_totals();

    $items = [];
    foreach ($cart->get_cart() as $cart_key => $item) {
        if (!is_array($item) || !isset($item['data']) || !$item['data'] instanceof WC_Product) {
            continue;
        }

        $product = $item['data'];
        $qty     = max(1, (int) ($item['quantity'] ?? 1));
        $name    = $product->get_name();
        $thumb   = $product->get_image('woocommerce_thumbnail', ['class' => 'jc-cart-item__thumb']);
        $price   = function_exists('justccell_format_money')
            ? justccell_format_money((float) $product->get_price())
            : (function_exists('wc_price') ? justccell_decode_money_text(wc_price((float) $product->get_price())) : '');

        $meta_lines = [];
        if (!empty($item['justccell_laser']['enabled']) && is_array($item['justccell_laser'])) {
            $laser = $item['justccell_laser'];
            $text  = trim((string) ($laser['text'] ?? ''));
            if ($text !== '') {
                $meta_lines[] = $text;
            }
            $whatsapp = trim((string) ($laser['whatsapp'] ?? ''));
            if ($whatsapp !== '') {
                $meta_lines[] = sprintf(
                    /* translators: %s: WhatsApp number */
                    __('WhatsApp %s', 'justccell'),
                    $whatsapp
                );
            }
            $meta_lines[] = __('Laser engraving', 'justccell');
            $setup = (float) ($laser['setup_fee'] ?? 0);
            if ($setup > 0 && function_exists('justccell_format_money')) {
                $meta_lines[] = sprintf(
                    /* translators: %s: formatted setup fee */
                    __('Setup %s', 'justccell'),
                    justccell_format_money($setup)
                );
            }
            $preview      = (string) ($laser['preview'] ?? '');
            if ($preview !== '' && str_starts_with($preview, 'http')) {
                $thumb = sprintf(
                    '<img class="jc-cart-item__thumb jc-laser-cart-thumb" src="%s" alt="" width="72" height="72" loading="lazy">',
                    esc_url($preview)
                );
            }
        }

        $variation = '';
        if ($product->is_type('variation')) {
            $bits = [];
            foreach ($product->get_attributes() as $label => $value) {
                if ((string) $value !== '') {
                    $bits[] = wc_attribute_label((string) $label) . ': ' . $value;
                }
            }
            $variation = implode(' · ', $bits);
        }

        $items[] = [
            'key'       => (string) $cart_key,
            'name'      => $name,
            'qty'       => $qty,
            'price'     => $price,
            'thumb'     => $thumb,
            'variation' => $variation,
            'meta'      => $meta_lines,
        ];
    }

    $payload['count']         = $cart->get_cart_contents_count();
    $payload['subtotal_html'] = function_exists('justccell_format_money_html')
        ? justccell_format_money_html((float) $cart->get_subtotal())
        : (function_exists('wc_price') ? wp_kses_post(wc_price((float) $cart->get_subtotal())) : '');
    $payload['items']         = $items;

    return $payload;
}

function justccell_cart_ajax_add_to_cart(): void
{
    $result = justccell_process_add_to_cart();
    if ($result['success']) {
        wp_send_json_success($result);
    }
    wp_send_json_error($result);
}
add_action('wp_ajax_justccell_add_to_cart', 'justccell_cart_ajax_add_to_cart');
add_action('wp_ajax_nopriv_justccell_add_to_cart', 'justccell_cart_ajax_add_to_cart');

function justccell_cart_ajax_get_drawer(): void
{
    if (function_exists('wc_load_cart')) {
        wc_load_cart();
    }

    $nonce = isset($_REQUEST['nonce']) ? sanitize_key(wp_unslash((string) $_REQUEST['nonce'])) : '';
    if ($nonce === '' || !wp_verify_nonce($nonce, 'justccell_cart')) {
        wp_send_json_error(['message' => __('Invalid request.', 'justccell')], 403);
    }

    wp_send_json_success([
        'message' => '',
        'data'    => justccell_cart_drawer_payload(),
    ]);
}
add_action('wp_ajax_justccell_cart_drawer', 'justccell_cart_ajax_get_drawer');
add_action('wp_ajax_nopriv_justccell_cart_drawer', 'justccell_cart_ajax_get_drawer');

add_action('wp_enqueue_scripts', static function (): void {
    if (!class_exists('WooCommerce') || is_admin()) {
        return;
    }

    wp_enqueue_style(
        'justccell-cart',
        JUSTCCELL_URI . '/assets/css/cart-drawer.css',
        ['justccell-chrome'],
        JUSTCCELL_VERSION
    );

    wp_enqueue_script(
        'justccell-cart',
        JUSTCCELL_URI . '/assets/js/cart-drawer.js',
        [],
        JUSTCCELL_VERSION,
        true
    );

    wp_localize_script('justccell-cart', 'JustccellCart', [
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('justccell_cart'),
        'cartUrl'  => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'),
        'currency' => [
            'code'   => function_exists('justccell_current_currency') ? justccell_current_currency() : 'GBP',
            'symbol' => function_exists('justccell_currency_symbol') ? justccell_currency_symbol() : '£',
        ],
        'i18n'     => [
            'title'       => __('Your cart', 'justccell'),
            'empty'       => __('Your cart is empty.', 'justccell'),
            'subtotal'    => __('Subtotal', 'justccell'),
            'viewCart'    => __('View cart', 'justccell'),
            'continue'    => __('Continue shopping', 'justccell'),
            'close'       => __('Close cart', 'justccell'),
            'minimize'    => __('Minimize', 'justccell'),
            'adding'      => __('Adding…', 'justccell'),
            'added'       => __('Added to your cart.', 'justccell'),
            'error'       => __('Could not add to cart. Try again.', 'justccell'),
            'openCart'    => __('Open cart', 'justccell'),
        ],
    ]);
}, 25);
