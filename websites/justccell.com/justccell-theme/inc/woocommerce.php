<?php
/**
 * WooCommerce: catalog + specs; custom buy box with Add to cart (AJAX drawer). Paid checkout via Viva Smart Checkout when configured.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_filter('use_block_editor_for_post_type', static function ($use, $type) {
    return $type === 'product' ? false : $use;
}, 99, 2);

add_filter('woocommerce_feature_product_block_editor_enabled', '__return_false');

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
    return is_array($styles) ? $styles : [];
});

/**
 * Expose managed stock qty on variation JSON for buy-box live availability.
 *
 * @param array<string,mixed> $data
 * @return array<string,mixed>
 */
add_filter('woocommerce_available_variation', static function (array $data, $product, $variation): array {
    if (!$variation instanceof WC_Product) {
        return $data;
    }
    $stock = justccell_product_buy_box_stock($variation);
    $data['justccell_manage_stock'] = $stock['managed'];
    $data['justccell_stock_qty']    = $stock['quantity'];
    $data['justccell_in_stock']     = $stock['in_stock'];

    $display = (float) ($data['display_price'] ?? 0);
    if ($display <= 0 && function_exists('justccell_tier_unit_price_for_qty')) {
        $parent_id = (int) $variation->get_parent_id();
        $scope_id  = $parent_id > 0 ? $parent_id : (int) $variation->get_id();
        $unit      = justccell_tier_unit_price_for_qty($scope_id, (int) $variation->get_id(), 1);
        if ($unit !== null && $unit > 0) {
            $data['display_price']          = $unit;
            $data['display_regular_price']  = $unit;
            $data['price']                  = $unit;
        }
    }

    return $data;
}, 20, 3);

/**
 * Tier / quote SKUs leave variation Woo prices empty. Woo's default gate treats them as
 * inactive and omits them from data-product_variations. Keep published empty-price
 * children active. Hook woocommerce_variation_is_active with two args only
 * ($active, WC_Product_Variation) — four args fatals on PHP 8.
 */
function justccell_wc_variation_should_skip_price_gate(int $variation_id, $variation = null, int $parent_id = 0): bool
{
    if (!$variation instanceof WC_Product_Variation) {
        $variation = wc_get_product($variation_id);
    }
    if (!$variation instanceof WC_Product_Variation) {
        return false;
    }
    if ($variation->get_status() !== 'publish') {
        return false;
    }
    if ((string) $variation->get_price('edit') !== '') {
        return false;
    }
    $parent_id = $parent_id > 0 ? $parent_id : (int) $variation->get_parent_id();
    if ($parent_id < 1) {
        return false;
    }

    return wc_get_product($parent_id) instanceof WC_Product_Variable;
}

add_filter('woocommerce_variation_is_active', static function ($active, $variation) {
    if (!$variation instanceof WC_Product_Variation) {
        return $active;
    }
    if (justccell_wc_variation_should_skip_price_gate((int) $variation->get_id(), $variation, (int) $variation->get_parent_id())) {
        return true;
    }

    return $active;
}, 10, 2);

/**
 * Woo builds data-product_variations from variation_is_visible(), not variation_is_active().
 * Tier-priced children often have empty catalog prices — keep them in the JSON.
 */
add_filter('woocommerce_variation_is_visible', static function ($visible, $variation_id, $parent_id, $variation) {
    if ($visible) {
        return $visible;
    }
    if (justccell_wc_variation_should_skip_price_gate((int) $variation_id, $variation, (int) $parent_id)) {
        return true;
    }

    return $visible;
}, 20, 4);

/**
 * Clear variable product variation transients (safe — no WC_Product_Variable::sync).
 */
function justccell_wc_clear_variable_product_transients(int $product_id): void
{
    static $busy = [];

    if ($product_id < 1 || isset($busy[$product_id]) || !function_exists('wc_get_product')) {
        return;
    }

    $product = wc_get_product($product_id);
    if (!$product instanceof WC_Product_Variable) {
        return;
    }

    $busy[$product_id] = true;
    wc_delete_product_transients($product_id);
    unset($busy[$product_id]);
}

add_action('woocommerce_save_product_variation', static function (int $variation_id, int $i = 0): void {
    unset($i);
    $variation = wc_get_product($variation_id);
    if (!$variation instanceof WC_Product_Variation) {
        return;
    }
    $parent_id = (int) $variation->get_parent_id();
    if ($parent_id > 0) {
        justccell_wc_clear_variable_product_transients($parent_id);
    }
}, 99, 2);

/**
 * Woo 11 prints order status as plain text. Re-wrap with <mark> via the
 * column action (replaces the template elseif — not a custom orders.php).
 */
add_action('woocommerce_my_account_my_orders_column_order-status', static function ($order): void {
    if (!$order instanceof WC_Order) {
        return;
    }
    echo '<mark>' . esc_html(wc_get_order_status_name($order->get_status())) . '</mark>';
});

/**
 * View-order line rows: product thumbnail + stacked title / attributes / engraving meta.
 */
function justccell_wc_should_wrap_order_item_row(): bool
{
    if (is_admin() && !wp_doing_ajax()) {
        return false;
    }

    // Custom thank-you template (thankyou.php) already renders product thumbs.
    if (function_exists('justccell_is_order_received_page') && justccell_is_order_received_page()) {
        return false;
    }

    return function_exists('is_account_page') && is_account_page();
}

add_filter('woocommerce_order_item_name', static function (string $html, $item, bool $is_visible): string {
    if (!justccell_wc_should_wrap_order_item_row() || !$item instanceof WC_Order_Item_Product) {
        return $html;
    }

    $product = $item->get_product();
    $thumb   = '';
    if ($product) {
        $thumb = $product->get_image(
            [128, 128],
            [
                'class' => 'jc-wc-order-item__thumb',
                'alt'   => esc_attr($product->get_name()),
            ]
        );
    }
    if ($thumb === '' && function_exists('wc_placeholder_img')) {
        $thumb = wc_placeholder_img(
            [128, 128],
            ['class' => 'jc-wc-order-item__thumb']
        );
    }

    $media = $thumb !== ''
        ? '<div class="jc-wc-order-item__media">' . $thumb . '</div>'
        : '';

    return '<div class="jc-wc-order-item">' . $media . '<div class="jc-wc-order-item__content">' . $html;
}, 20, 3);

add_action('woocommerce_order_item_meta_end', static function ($item_id, $item, $order, $plain_text): void {
    unset($item_id, $order);
    if ($plain_text || !justccell_wc_should_wrap_order_item_row() || !$item instanceof WC_Order_Item_Product) {
        return;
    }
    echo '</div></div>';
}, 99, 4);

add_action('wp_enqueue_scripts', static function (): void {
    wp_dequeue_style('wc-blocks-style');
    wp_dequeue_style('wc-blocks-vendors-style');
    if (!is_checkout() && !is_cart() && !is_account_page()) {
        wp_dequeue_script('wc-cart-fragments');
        wp_dequeue_script('woocommerce');
        wp_dequeue_script('sourcebuster-js');
    }
}, 99);

add_filter('woocommerce_add_to_cart_redirect', static function ($url) {
    if (isset($_REQUEST['justccell_cart_ajax']) && (string) wp_unslash($_REQUEST['justccell_cart_ajax']) === '1') {
        return false;
    }
    if (isset($_REQUEST['add-to-cart'])) {
        return function_exists('wc_get_cart_url') ? wc_get_cart_url() : $url;
    }
    $laser = isset($_REQUEST['justccell_laser_enabled']) && (string) wp_unslash($_REQUEST['justccell_laser_enabled']) === '1';
    if ($laser && function_exists('wc_get_cart_url')) {
        return wc_get_cart_url();
    }
    return $url;
});

remove_action('woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10);
remove_action('woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30);
remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

add_filter('woocommerce_product_tabs', static function (array $tabs): array {
    unset($tabs['reviews']);
    if (isset($tabs['additional_information'])) {
        $tabs['additional_information']['title'] = __('Technical specs', 'justccell');
    }
    return $tabs;
});

/**
 * Rank Math, sitemaps, and Woo “View” emit /{category}/{slug}/ — never a second public /product/ URL.
 * Leave Woo’s rewrite base as /product/; leftover hits 301 into the canonical.
 */
add_action('init', static function (): void {
    if (get_option('justccell_wc_keep_product_base') === 'product') {
        return;
    }
    $permalinks = (array) get_option('woocommerce_permalinks', []);
    $current    = trim((string) ($permalinks['product_base'] ?? ''), '/');
    if ($current === '%product_cat%') {
        $permalinks['product_base'] = 'product';
        update_option('woocommerce_permalinks', $permalinks);
        delete_option('justccell_rewrite_ver');
    }
    update_option('justccell_wc_keep_product_base', 'product');
}, 5);

add_filter('post_type_link', static function (string $post_link, $post): string {
    static $busy = false;
    if ($busy || !$post instanceof WP_Post || $post->post_type !== 'product' || $post->post_name === '') {
        return $post_link;
    }
    $busy = true;
    $cat  = '';
    $terms = get_the_terms($post, 'product_cat');
    if (is_array($terms) && function_exists('justccell_product_category_labels')) {
        $allowed = justccell_product_category_labels();
        foreach ($terms as $term) {
            if ($term instanceof WP_Term && $term->slug !== '' && array_key_exists($term->slug, $allowed)) {
                $cat = $term->slug;
                break;
            }
        }
    }
    $busy = false;
    if ($cat === '') {
        return $post_link;
    }
    return home_url('/' . $cat . '/' . $post->post_name . '/');
}, 20, 2);

/**
 * Native Woo attributes used by the product buy box (Edit Product → Attributes).
 */
add_action('init', static function (): void {
    if (!function_exists('wc_create_attribute')) {
        return;
    }

    $ensure = static function (string $slug, string $name, string $option_key): void {
        if (get_option($option_key) === '1') {
            return;
        }
        $id = function_exists('wc_attribute_taxonomy_id_by_name')
            ? (int) wc_attribute_taxonomy_id_by_name($slug)
            : 0;
        if ($id < 1) {
            wc_create_attribute([
                'name'         => $name,
                'slug'         => $slug,
                'type'         => 'select',
                'order_by'     => 'menu_order',
                'has_archives' => false,
            ]);
            delete_transient('wc_attribute_taxonomies');
        }
        update_option($option_key, '1');
    };

    $ensure('colour', __('Colour', 'justccell'), 'justccell_attr_colour');
    $ensure('combination', __('Combination', 'justccell'), 'justccell_attr_combination');
}, 8);

/**
 * Core WP renders the built-in "name" column via WP_Terms_List_Table::column_name(),
 * not manage_product_cat_custom_column. Swap the key so the custom-column filter runs.
 *
 * @param array<string, string> $columns Taxonomy list-table columns.
 * @return array<string, string>
 */
function justccell_wc_product_cat_list_columns(array $columns): array
{
    if (!isset($columns['name'])) {
        return $columns;
    }

    $name_label = $columns['name'];
    unset($columns['name']);

    $out = [];
    foreach ($columns as $key => $label) {
        $out[$key] = $label;
        if ($key === 'thumb') {
            $out['jc_cat_name'] = $name_label;
        }
    }

    if (!isset($out['jc_cat_name'])) {
        $out = ['jc_cat_name' => $name_label] + $out;
    }

    return $out;
}
add_filter('manage_edit-product_cat_columns', 'justccell_wc_product_cat_list_columns', 100);

/**
 * @param array<string, string> $columns Sortable columns.
 * @return array<string, string>
 */
function justccell_wc_product_cat_sortable_columns(array $columns): array
{
    if (isset($columns['name'])) {
        $columns['jc_cat_name'] = $columns['name'];
        unset($columns['name']);
    }

    return $columns;
}
add_filter('manage_edit-product_cat_sortable_columns', 'justccell_wc_product_cat_sortable_columns', 100);

/**
 * @param string $column    Default primary column key.
 * @param string $screen_id Admin list-table screen ID (e.g. edit-product_cat).
 */
function justccell_wc_product_cat_primary_column(string $column, string $screen_id): string
{
    if ($screen_id === 'edit-product_cat') {
        return 'jc_cat_name';
    }

    return $column;
}
add_filter('list_table_primary_column', 'justccell_wc_product_cat_primary_column', 10, 2);

/**
 * Render product_cat Name column from raw term data (manage_*_custom_column path).
 *
 * @param mixed  $content     Existing column HTML.
 * @param string $column_name Column key.
 * @param mixed  $term_id     Term ID (WP may pass string).
 */
function justccell_wc_product_cat_custom_column($content, string $column_name, $term_id): string
{
    $term_id = (int) $term_id;
    if ($column_name !== 'jc_cat_name' || $term_id < 1) {
        return is_string($content) ? $content : '';
    }

    clean_term_cache($term_id, 'product_cat');
    $term = get_term($term_id, 'product_cat');
    $name = ($term instanceof WP_Term && !is_wp_error($term) && $term->name !== '')
        ? $term->name
        : '';

    if ($name === '') {
        global $wpdb;
        $raw = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT name FROM {$wpdb->terms} WHERE term_id = %d LIMIT 1",
                $term_id
            )
        );
        $name = is_string($raw) ? $raw : '';
    }

    if ($name === '') {
        return $content;
    }

    $default_id = (int) get_option('default_product_cat');
    $default_tag = ($default_id > 0 && $default_id === $term_id)
        ? ' <span class="jc-cat-default-tag">(Default)</span>'
        : '';

    $edit_link = get_edit_term_link($term_id, 'product_cat', 'product');
    if (is_string($edit_link) && $edit_link !== '') {
        return sprintf(
            '<strong><a class="row-title" href="%s">%s</a>%s</strong>',
            esc_url($edit_link),
            esc_html($name),
            $default_tag
        );
    }

    return sprintf('<strong>%s%s</strong>', esc_html($name), $default_tag);
}
add_filter('manage_product_cat_custom_column', 'justccell_wc_product_cat_custom_column', 99, 3);

/**
 * UK WooCommerce / Cart block ships "Basket" — site standard is Cart everywhere.
 */
function justccell_cart_label(): string
{
    return __('Cart', 'justccell');
}

/**
 * Preserve sentence case: Basket → Cart, basket → cart.
 */
function justccell_replace_basket_with_cart(string $text): string
{
    if ($text === '' || stripos($text, 'basket') === false) {
        return $text;
    }

    $text = (string) preg_replace('/\bBASKET\b/', 'CART', $text);
    $text = (string) preg_replace('/\bBasket\b/', 'Cart', $text);
    $text = (string) preg_replace('/\bbasket\b/', 'cart', $text);

    return $text;
}

add_filter('gettext', static function (string $translated): string {
    return justccell_replace_basket_with_cart($translated);
}, 99, 1);

add_filter('gettext_with_context', static function (string $translated): string {
    return justccell_replace_basket_with_cart($translated);
}, 99, 1);

add_filter('ngettext', static function (string $translated): string {
    return justccell_replace_basket_with_cart($translated);
}, 99, 1);

add_filter('ngettext_with_context', static function (string $translated): string {
    return justccell_replace_basket_with_cart($translated);
}, 99, 1);

add_filter('woocommerce_empty_cart_message', 'justccell_replace_basket_with_cart', 99);
add_filter('woocommerce_return_to_shop_text', static function (): string {
    return __('Continue shopping', 'justccell');
});

add_filter('load_script_translations', static function ($json) {
    if (!is_string($json) || stripos($json, 'basket') === false) {
        return $json;
    }
    return justccell_replace_basket_with_cart($json);
}, 99);

add_filter('woocommerce_page_title', static function (string $title): string {
    $title = justccell_replace_basket_with_cart($title);
    return function_exists('is_cart') && is_cart() ? justccell_cart_label() : $title;
}, 99);

add_filter('the_title', static function (string $title, $post_id): string {
    $title = justccell_replace_basket_with_cart($title);
    if (!function_exists('wc_get_page_id')) {
        return $title;
    }
    if ((int) $post_id === (int) wc_get_page_id('cart')) {
        return justccell_cart_label();
    }
    return $title;
}, 99, 2);

add_filter('single_post_title', static function (string $title): string {
    return justccell_replace_basket_with_cart($title);
}, 99);

add_filter('document_title_parts', static function (array $parts): array {
    if (isset($parts['title'])) {
        $parts['title'] = justccell_replace_basket_with_cart((string) $parts['title']);
    }
    if (function_exists('is_cart') && is_cart()) {
        $parts['title'] = justccell_cart_label();
    }
    return $parts;
}, 99);

add_filter('pre_get_document_title', static function (string $title): string {
    $title = justccell_replace_basket_with_cart($title);
    if (function_exists('is_cart') && is_cart()) {
        return justccell_cart_label() . ' | ' . get_bloginfo('name');
    }
    return $title;
}, 99);

add_filter('rank_math/frontend/title', static function (string $title): string {
    if (function_exists('is_cart') && is_cart()) {
        return justccell_cart_label() . ' | ' . get_bloginfo('name');
    }
    return justccell_replace_basket_with_cart($title);
}, 99);

add_filter('rank_math/frontend/description', static function (string $description): string {
    return justccell_replace_basket_with_cart($description);
}, 99);

add_filter('rank_math/frontend/breadcrumb/html', static function (string $html): string {
    return justccell_replace_basket_with_cart($html);
}, 99);

/**
 * Keep Woo cart / checkout / account page records free of "basket".
 *
 * @param 'cart'|'checkout'|'myaccount' $which
 */
function justccell_sanitize_woo_page_copy(string $which): void
{
    if (!function_exists('wc_get_page_id')) {
        return;
    }
    $page_id = (int) wc_get_page_id($which);
    if ($page_id < 1) {
        return;
    }
    $page = get_post($page_id);
    if (!$page instanceof WP_Post) {
        return;
    }

    $updates = ['ID' => $page_id];
    if ($which === 'cart' && (stripos($page->post_title, 'basket') !== false || strcasecmp($page->post_title, 'Basket') === 0)) {
        $updates['post_title'] = justccell_cart_label();
    } elseif (stripos($page->post_title, 'basket') !== false) {
        $updates['post_title'] = justccell_replace_basket_with_cart($page->post_title);
    }
    if ($which === 'cart' && $page->post_name === 'basket') {
        $updates['post_name'] = 'cart';
    }
    if (stripos($page->post_content, 'basket') !== false) {
        $updates['post_content'] = justccell_replace_basket_with_cart($page->post_content);
    }
    if (count($updates) > 1) {
        wp_update_post($updates);
        delete_option('justccell_rewrite_ver');
    }

    foreach (['rank_math_title', 'rank_math_description', 'rank_math_focus_keyword', '_yoast_wpseo_title', '_yoast_wpseo_metadesc'] as $meta_key) {
        $val = (string) get_post_meta($page_id, $meta_key, true);
        if ($val !== '' && stripos($val, 'basket') !== false) {
            update_post_meta($page_id, $meta_key, justccell_replace_basket_with_cart($val));
        }
    }
}

add_action('init', static function (): void {
    if (!function_exists('wc_get_page_id') || is_admin()) {
        return;
    }
    justccell_sanitize_woo_page_copy('cart');
    justccell_sanitize_woo_page_copy('checkout');
    justccell_sanitize_woo_page_copy('myaccount');
}, 30);

add_filter('template_include', static function (string $template): string {
    if (!class_exists('WooCommerce')) {
        return $template;
    }
    $woo_shell = JUSTCCELL_DIR . '/commerce-shell.php';
    if (!is_readable($woo_shell)) {
        return $template;
    }
    if (
        (function_exists('is_cart') && is_cart())
        || (function_exists('is_checkout') && is_checkout())
        || (function_exists('is_account_page') && is_account_page())
    ) {
        return $woo_shell;
    }
    return $template;
}, 99);

add_action('template_redirect', static function (): void {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    $path = isset($_SERVER['REQUEST_URI']) ? (string) wp_parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) : '';
    if (preg_match('#/basket/?$#i', $path) === 1) {
        wp_safe_redirect(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'), 301);
        exit;
    }
}, 5);
