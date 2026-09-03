<?php
/**
 * Native WooCommerce tiered (volume) pricing — post meta + cart hooks.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_TIER_META = '_justccell_tiered_pricing';

/**
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_normalize_tiered_pricing_rows($raw): array
{
    if (!is_array($raw)) {
        return [];
    }

    $rows = [];
    foreach ($raw as $row) {
        if (!is_array($row)) {
            continue;
        }
        $min = max(1, (int) ($row['min_qty'] ?? $row['qty_min'] ?? 0));
        $max = max(0, (int) ($row['max_qty'] ?? $row['qty_max'] ?? 0));
        $price_raw = $row['price'] ?? '';
        if (is_string($price_raw)) {
            $price_raw = preg_replace('/[^\d.,\-]/', '', $price_raw) ?? '';
            $price_raw = str_replace(',', '', $price_raw);
        }
        $price = is_numeric($price_raw) ? round((float) $price_raw, 4) : 0.0;
        if ($price <= 0) {
            continue;
        }
        if ($max > 0 && $max < $min) {
            continue;
        }
        $rows[] = [
            'min_qty' => $min,
            'max_qty' => $max,
            'price'   => $price,
        ];
    }

    usort(
        $rows,
        static fn (array $a, array $b): int => $a['min_qty'] <=> $b['min_qty']
    );

    return $rows;
}

/**
 * Legacy ACF clone_offers → normalized tier rows.
 *
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_tiered_pricing_legacy_rows(int $product_id): array
{
    if ($product_id < 1 || !function_exists('get_field')) {
        return [];
    }
    $offers = get_field('clone_offers', $product_id);
    if (!is_array($offers) || $offers === []) {
        return [];
    }
    $normalized = function_exists('justccell_normalize_buy_offers')
        ? justccell_normalize_buy_offers($offers)
        : [];
    if ($normalized === []) {
        return [];
    }
    $tiers = $normalized[0]['tiers'] ?? [];
    if (!is_array($tiers) || $tiers === []) {
        return [];
    }
    return justccell_normalize_tiered_pricing_rows($tiers);
}

/**
 * @param list<array{range:string,price:string,qty_min:int,qty_max:int}> $legacy_display
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_tiered_pricing_from_display_rows(array $legacy_display): array
{
    $raw = [];
    foreach ($legacy_display as $row) {
        if (!is_array($row)) {
            continue;
        }
        $raw[] = [
            'min_qty' => (int) ($row['qty_min'] ?? 1),
            'max_qty' => (int) ($row['qty_max'] ?? 0),
            'price'   => (string) ($row['price'] ?? ''),
        ];
    }
    return justccell_normalize_tiered_pricing_rows($raw);
}

/**
 * Meta → legacy ACF → (optional) kit defaults. Persists legacy rows into meta once.
 *
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_tiered_pricing_resolve_rows(int $product_id, bool $use_defaults = false): array
{
    if ($product_id < 1) {
        return [];
    }

    $meta = justccell_normalize_tiered_pricing_rows(get_post_meta($product_id, JUSTCCELL_TIER_META, true));
    if ($meta !== []) {
        return $meta;
    }

    $legacy = justccell_tiered_pricing_legacy_rows($product_id);
    if ($legacy !== []) {
        update_post_meta($product_id, JUSTCCELL_TIER_META, $legacy);
        return $legacy;
    }

    if (!$use_defaults || !function_exists('justccell_default_kit_tiers')) {
        return [];
    }

    $defaults = justccell_tiered_pricing_from_display_rows(justccell_default_kit_tiers());
    if ($defaults === []) {
        return [];
    }

    update_post_meta($product_id, JUSTCCELL_TIER_META, $defaults);
    return $defaults;
}

/**
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_get_product_tiered_pricing(int $product_id): array
{
    return justccell_tiered_pricing_resolve_rows($product_id, false);
}

/**
 * Parent product ID for tier lookup (variation meta overrides parent).
 */
function justccell_tiered_pricing_source_id(int $product_id, int $variation_id = 0): int
{
    if ($variation_id > 0 && justccell_get_product_tiered_pricing($variation_id) !== []) {
        return $variation_id;
    }
    return $product_id;
}

function justccell_tier_range_label(int $min_qty, int $max_qty): string
{
    if ($max_qty > 0) {
        return $min_qty . '-' . $max_qty;
    }
    return $min_qty . '+';
}

function justccell_format_tier_price(float $price): string
{
    if (function_exists('justccell_format_money')) {
        return justccell_format_money($price);
    }
    if (!function_exists('wc_price')) {
        return '£' . number_format($price, 2, '.', '');
    }
    return html_entity_decode(wp_strip_all_tags(wc_price($price)), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Buy-box / frontend table rows.
 *
 * @return list<array{range:string,price:string,qty_min:int,qty_max:int,unit:float}>
 */
function justccell_tiered_pricing_display_rows(int $product_id): array
{
    $rows = justccell_tiered_pricing_resolve_rows($product_id, true);
    $out  = [];
    foreach ($rows as $row) {
        $min  = (int) $row['min_qty'];
        $max  = (int) $row['max_qty'];
        $unit = (float) $row['price'];
        $out[] = [
            'range'   => justccell_tier_range_label($min, $max),
            'price'   => justccell_format_tier_price($unit),
            'qty_min' => $min,
            'qty_max' => $max,
            'unit'    => $unit,
        ];
    }
    return $out;
}

/**
 * Unit price for cart quantity, or null when no matching band.
 */
function justccell_tier_unit_price_for_qty(int $product_id, int $variation_id, int $qty): ?float
{
    $qty    = max(1, $qty);
    $source = justccell_tiered_pricing_source_id($product_id, $variation_id);
    $tiers  = justccell_tiered_pricing_resolve_rows($source, true);
    if ($tiers === []) {
        return null;
    }
    foreach ($tiers as $tier) {
        $min = (int) $tier['min_qty'];
        $max = (int) $tier['max_qty'];
        if ($qty >= $min && ($max === 0 || $qty <= $max)) {
            return (float) $tier['price'];
        }
    }
    return null;
}

/* -------------------------------------------------------------------------
 * One-time migration for all published products (legacy ACF → meta).
 * ---------------------------------------------------------------------- */

add_action('admin_init', static function (): void {
    if (!is_admin() || !current_user_can('manage_woocommerce')) {
        return;
    }
    $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
    if (get_option('justccell_tier_bulk_migrate') === $ver) {
        return;
    }

    $ids = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
    ]);

    foreach ($ids as $id) {
        $id = (int) $id;
        if ($id < 1) {
            continue;
        }
        if (justccell_normalize_tiered_pricing_rows(get_post_meta($id, JUSTCCELL_TIER_META, true)) !== []) {
            continue;
        }
        justccell_tiered_pricing_resolve_rows($id, true);
    }

    update_option('justccell_tier_bulk_migrate', $ver, false);
}, 50);

/* -------------------------------------------------------------------------
 * Admin — Product data → Tiered pricing tab
 * ---------------------------------------------------------------------- */

function justccell_tiered_pricing_admin_field_name(string $key, int $index): string
{
    return 'justccell_tier_' . $key . '[' . $index . ']';
}

add_filter('woocommerce_product_data_tabs', static function (array $tabs): array {
    $tabs['justccell_tiered_pricing'] = [
        'label'    => __('Tiered pricing', 'justccell'),
        'target'   => 'justccell_tiered_pricing_data',
        'class'    => ['show_if_simple', 'show_if_variable', 'show_if_grouped', 'show_if_external'],
        'priority' => 25,
    ];
    return $tabs;
});

add_action('woocommerce_product_data_panels', static function (): void {
    global $post;
    if (!$post instanceof WP_Post) {
        return;
    }

    $product_id = (int) $post->ID;
    $rows       = justccell_normalize_tiered_pricing_rows(get_post_meta($product_id, JUSTCCELL_TIER_META, true));
    if ($rows === []) {
        $rows = justccell_tiered_pricing_legacy_rows($product_id);
    }
    if ($rows === []) {
        $rows = [['min_qty' => 1, 'max_qty' => 100, 'price' => 0.0]];
    }

    echo '<div id="justccell_tiered_pricing_data" class="panel woocommerce_options_panel hidden">';
    echo '<div class="options_group justccell-tier-pricing-wrap">';
    echo '<p class="description" style="padding:12px 12px 8px;">' . esc_html__(
        'Volume bands for the buy box and cart. Max qty 0 = open-ended. Save the product after editing.',
        'justccell'
    ) . '</p>';

    wp_nonce_field('justccell_tiered_pricing_save', 'justccell_tiered_pricing_nonce');

    echo '<table class="widefat justccell-tier-pricing-table"><thead><tr>';
    echo '<th>' . esc_html__('Min qty', 'justccell') . '</th>';
    echo '<th>' . esc_html__('Max qty (0 = open)', 'justccell') . '</th>';
    echo '<th>' . esc_html__('Price per unit', 'justccell') . '</th>';
    echo '<th></th>';
    echo '</tr></thead><tbody id="justccell-tier-rows">';

    foreach ($rows as $i => $row) {
        justccell_tiered_pricing_admin_render_row($i, $row);
    }

    echo '</tbody></table>';
    echo '<p style="padding:0 12px;"><button type="button" class="button" id="justccell-tier-add">' . esc_html__('Add tier', 'justccell') . '</button></p>';
    echo '</div></div>';
});

/**
 * @param array{min_qty:int,max_qty:int,price:float} $row
 */
function justccell_tiered_pricing_admin_render_row(int $index, array $row): void
{
    $min   = (int) ($row['min_qty'] ?? 1);
    $max   = (int) ($row['max_qty'] ?? 0);
    $price = (float) ($row['price'] ?? 0);
    $price_display = $price > 0 && function_exists('wc_format_localized_price')
        ? wc_format_localized_price($price)
        : ($price > 0 ? (string) $price : '');

    echo '<tr class="justccell-tier-row">';
    echo '<td><input type="number" min="1" step="1" name="' . esc_attr(justccell_tiered_pricing_admin_field_name('min', $index)) . '" value="' . esc_attr((string) $min) . '" class="short" /></td>';
    echo '<td><input type="number" min="0" step="1" name="' . esc_attr(justccell_tiered_pricing_admin_field_name('max', $index)) . '" value="' . esc_attr((string) $max) . '" class="short" /></td>';
    echo '<td><input type="text" name="' . esc_attr(justccell_tiered_pricing_admin_field_name('price', $index)) . '" value="' . esc_attr($price_display) . '" class="short wc_input_price" placeholder="' . esc_attr__('0.00', 'justccell') . '" /></td>';
    echo '<td><button type="button" class="button-link-delete justccell-tier-remove" aria-label="' . esc_attr__('Remove tier', 'justccell') . '">&times;</button></td>';
    echo '</tr>';
}

add_action('admin_enqueue_scripts', static function (string $hook): void {
    if (!in_array($hook, ['post.php', 'post-new.php'], true)) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen instanceof WP_Screen || $screen->post_type !== 'product') {
        return;
    }

    $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1.0.0';
    $uri = defined('JUSTCCELL_URI') ? JUSTCCELL_URI : get_template_directory_uri();

    wp_enqueue_style(
        'justccell-admin-product-acf',
        $uri . '/assets/css/admin-product-acf.css',
        [],
        $ver
    );

    wp_enqueue_script(
        'justccell-admin-product-acf',
        $uri . '/assets/js/admin-product-acf.js',
        [],
        $ver,
        true
    );

    wp_enqueue_script(
        'justccell-admin-tiered-pricing',
        $uri . '/assets/js/admin-tiered-pricing.js',
        ['jquery'],
        $ver,
        true
    );
});

add_action('woocommerce_process_product_meta', static function (int $product_id): void {
    if (
        !isset($_POST['justccell_tiered_pricing_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['justccell_tiered_pricing_nonce'])), 'justccell_tiered_pricing_save')
    ) {
        return;
    }

    $mins   = isset($_POST['justccell_tier_min']) && is_array($_POST['justccell_tier_min']) ? wp_unslash($_POST['justccell_tier_min']) : [];
    $maxes  = isset($_POST['justccell_tier_max']) && is_array($_POST['justccell_tier_max']) ? wp_unslash($_POST['justccell_tier_max']) : [];
    $prices = isset($_POST['justccell_tier_price']) && is_array($_POST['justccell_tier_price']) ? wp_unslash($_POST['justccell_tier_price']) : [];

    $raw = [];
    $n   = max(count($mins), count($maxes), count($prices));
    for ($i = 0; $i < $n; $i++) {
        $price_raw = $prices[$i] ?? '';
        if (function_exists('wc_format_decimal')) {
            $price_raw = wc_format_decimal(wp_unslash((string) $price_raw));
        }
        $raw[] = [
            'min_qty' => $mins[$i] ?? 1,
            'max_qty' => $maxes[$i] ?? 0,
            'price'   => $price_raw,
        ];
    }

    $normalized = justccell_normalize_tiered_pricing_rows($raw);
    if ($normalized === []) {
        delete_post_meta($product_id, JUSTCCELL_TIER_META);
        return;
    }
    update_post_meta($product_id, JUSTCCELL_TIER_META, $normalized);
});

/* -------------------------------------------------------------------------
 * Cart — tier unit price before laser engraving add-on (priority 10)
 * ---------------------------------------------------------------------- */

add_action('woocommerce_before_calculate_totals', static function ($cart): void {
    if (!is_object($cart) || !method_exists($cart, 'get_cart')) {
        return;
    }
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $item) {
        if (!isset($item['data']) || !is_object($item['data']) || !method_exists($item['data'], 'set_price')) {
            continue;
        }

        $product_id   = (int) ($item['product_id'] ?? 0);
        $variation_id = (int) ($item['variation_id'] ?? 0);
        $qty          = max(1, (int) ($item['quantity'] ?? 1));
        $unit         = justccell_tier_unit_price_for_qty($product_id, $variation_id, $qty);

        if ($unit === null) {
            continue;
        }

        $item['data']->set_price($unit);
    }
}, 10);
