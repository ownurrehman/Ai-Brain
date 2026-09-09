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
const JUSTCCELL_VARIATION_TIER_META = '_justccell_variation_tiers';

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
 * Parse posted admin rows. Empty rows (no min and no price) are skipped.
 * Incomplete rows fail the whole table so we never silently drop prices.
 *
 * @param array<int|string, mixed> $mins
 * @param array<int|string, mixed> $maxes
 * @param array<int|string, mixed> $prices
 * @return array{ok:bool, rows:list<array{min_qty:int,max_qty:int,price:float}>, errors:list<string>}
 */
function justccell_tiered_pricing_parse_admin_post(array $mins, array $maxes, array $prices): array
{
    $keys = array_unique(array_merge(array_keys($mins), array_keys($maxes), array_keys($prices)));
    natsort($keys);

    $rows   = [];
    $errors = [];
    $label  = 0;

    foreach ($keys as $key) {
        $label++;
        $min_raw   = trim((string) ($mins[$key] ?? ''));
        $max_raw   = trim((string) ($maxes[$key] ?? ''));
        $price_raw = trim((string) ($prices[$key] ?? ''));

        if ($price_raw !== '' && function_exists('wc_format_decimal')) {
            $formatted = wc_format_decimal($price_raw);
            $price_raw = is_string($formatted) ? trim($formatted) : trim((string) $formatted);
        }

        if ($min_raw === '' && $price_raw === '') {
            continue;
        }

        $min_ok = $min_raw !== '' && is_numeric($min_raw) && (int) $min_raw >= 1 && (float) $min_raw === (float) (int) $min_raw;
        $min    = (int) $min_raw;
        if (!$min_ok) {
            $errors[] = sprintf(
                /* translators: %d: 1-based row number in the volume-tiers table */
                __('Row %d: enter a starting quantity of 1 or more, or remove the row.', 'justccell'),
                $label
            );
        }

        $price_ok = $price_raw !== '' && is_numeric($price_raw) && (float) $price_raw > 0;
        $price    = $price_ok ? round((float) $price_raw, 4) : 0.0;
        if (!$price_ok) {
            $errors[] = sprintf(
                /* translators: %d: 1-based row number in the volume-tiers table */
                __('Row %d: enter a price per unit greater than 0, or remove the row.', 'justccell'),
                $label
            );
        }

        if (!$min_ok || !$price_ok) {
            continue;
        }

        $max = $max_raw === '' ? 0 : max(0, (int) $max_raw);
        if ($max > 0 && $max < $min) {
            $errors[] = sprintf(
                /* translators: %d: 1-based row number in the volume-tiers table */
                __('Row %d: max quantity cannot be less than the starting quantity. Leave max blank or 0 for unlimited.', 'justccell'),
                $label
            );
            continue;
        }

        $rows[] = [
            'min_qty' => $min,
            'max_qty' => $max,
            'price'   => $price,
        ];
    }

    if ($errors !== []) {
        return ['ok' => false, 'rows' => [], 'errors' => $errors];
    }

    usort(
        $rows,
        static fn (array $a, array $b): int => $a['min_qty'] <=> $b['min_qty']
    );

    return ['ok' => true, 'rows' => $rows, 'errors' => []];
}

function justccell_tiered_pricing_admin_error(string $message): void
{
    $message = trim($message);
    if ($message === '') {
        return;
    }
    if (class_exists('WC_Admin_Meta_Boxes') && is_callable(['WC_Admin_Meta_Boxes', 'add_error'])) {
        WC_Admin_Meta_Boxes::add_error($message);
        return;
    }
    add_action('admin_notices', static function () use ($message): void {
        echo '<div class="notice notice-error is-dismissible"><p>' . esc_html($message) . '</p></div>';
    });
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
 * Historic kit / battery fallback bands that were wrongly persisted into product meta.
 *
 * @return list<list<array{min_qty:int,max_qty:int,price:float}>>
 */
function justccell_tiered_pricing_forbidden_default_signatures(): array
{
    $qty = [
        [1, 100],
        [101, 1000],
        [1001, 5000],
        [5001, 10000],
        [10001, 20000],
    ];
    $kits = [3.6, 3.48, 3.36, 3.24, 3.12];
    $batteries = [2.77, 2.73, 2.66, 2.6, 2.57];

    $to_rows = static function (array $prices) use ($qty): array {
        $rows = [];
        foreach ($prices as $i => $price) {
            $rows[] = [
                'min_qty' => $qty[$i][0],
                'max_qty' => $qty[$i][1],
                'price'   => (float) $price,
            ];
        }
        return $rows;
    };

    return [
        $to_rows($kits),
        $to_rows($batteries),
    ];
}

/**
 * True when stored rows are the seeded kit/battery fallback (exact or truncated prefix).
 *
 * @param list<array{min_qty:int,max_qty:int,price:float}> $rows
 */
function justccell_tiered_pricing_is_seeded_default(array $rows): bool
{
    if (count($rows) < 2) {
        return false;
    }

    foreach (justccell_tiered_pricing_forbidden_default_signatures() as $signature) {
        if (count($rows) > count($signature)) {
            continue;
        }
        $match = true;
        foreach ($rows as $i => $row) {
            $want = $signature[$i];
            if ((int) ($row['min_qty'] ?? 0) !== (int) $want['min_qty']) {
                $match = false;
                break;
            }
            if ((int) ($row['max_qty'] ?? 0) !== (int) $want['max_qty']) {
                $match = false;
                break;
            }
            if (abs(round((float) ($row['price'] ?? 0), 2) - (float) $want['price']) > 0.001) {
                $match = false;
                break;
            }
        }
        if ($match) {
            return true;
        }
    }

    $price_sigs = [
        [3.6, 3.48, 3.36, 3.24, 3.12],
        [2.77, 2.73, 2.66, 2.6, 2.57],
    ];
    $units = [];
    foreach ($rows as $row) {
        $units[] = round((float) ($row['price'] ?? 0), 2);
    }
    foreach ($price_sigs as $sig) {
        if (count($units) < 3 || count($units) > count($sig)) {
            continue;
        }
        $match = true;
        foreach ($units as $i => $unit) {
            if (abs($unit - (float) $sig[$i]) > 0.001) {
                $match = false;
                break;
            }
        }
        if ($match) {
            return true;
        }
    }

    return false;
}

/**
 * Meta → genuine legacy ACF only. Never invent or persist kit/battery fallback prices.
 *
 * @param bool $use_defaults Deprecated. Ignored — default prices are forbidden.
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_tiered_pricing_resolve_rows(int $product_id, bool $use_defaults = false): array
{
    unset($use_defaults);

    if ($product_id < 1) {
        return [];
    }

    $meta = justccell_normalize_tiered_pricing_rows(get_post_meta($product_id, JUSTCCELL_TIER_META, true));
    if ($meta !== []) {
        if (justccell_tiered_pricing_is_seeded_default($meta)) {
            return [];
        }
        return $meta;
    }

    $legacy = justccell_tiered_pricing_legacy_rows($product_id);
    if ($legacy === [] || justccell_tiered_pricing_is_seeded_default($legacy)) {
        return [];
    }

    update_post_meta($product_id, JUSTCCELL_TIER_META, $legacy);
    return $legacy;
}

/**
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_get_product_tiered_pricing(int $product_id): array
{
    return justccell_tiered_pricing_resolve_rows($product_id, false);
}

/**
 * Typed volume bands saved on a variation. Empty means “use the parent table”.
 *
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_get_variation_manual_tiers(int $variation_id): array
{
    if ($variation_id < 1) {
        return [];
    }

    $own = justccell_normalize_tiered_pricing_rows(
        get_post_meta($variation_id, JUSTCCELL_VARIATION_TIER_META, true)
    );
    if ($own !== []) {
        return justccell_tiered_pricing_is_seeded_default($own) ? [] : $own;
    }

    $legacy = justccell_normalize_tiered_pricing_rows(
        get_post_meta($variation_id, JUSTCCELL_TIER_META, true)
    );
    if ($legacy === [] || justccell_tiered_pricing_is_seeded_default($legacy)) {
        return [];
    }

    return $legacy;
}

/**
 * Parent product ID for tier lookup (variation meta overrides parent).
 */
function justccell_tiered_pricing_source_id(int $product_id, int $variation_id = 0): int
{
    if ($variation_id > 0 && justccell_get_variation_manual_tiers($variation_id) !== []) {
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
 * Display helper keeps Woo sale strikethrough only when a row stores both figures.
 *
 * @param list<array{min_qty:int,max_qty:int,price:float}> $rows
 * @param array{on_sale?:bool,unit?:float,regular?:float} $sale Unused. Typed prices are never rewritten.
 * @return list<array{range:string,price:string,qty_min:int,qty_max:int,unit:float,regular:float,regular_price:string,on_sale:bool}>
 */
function justccell_tiered_pricing_rows_to_display(array $rows, array $sale = []): array
{
    unset($sale);
    $out = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $min  = (int) ($row['min_qty'] ?? 1);
        $max  = (int) ($row['max_qty'] ?? 0);
        $unit = (float) ($row['price'] ?? 0);
        if ($unit <= 0) {
            continue;
        }
        $out[] = [
            'range'         => justccell_tier_range_label($min, $max),
            'price'         => justccell_format_tier_price($unit),
            'qty_min'       => $min,
            'qty_max'       => $max,
            'unit'          => $unit,
            'regular'       => 0.0,
            'regular_price' => '',
            'on_sale'       => false,
        ];
    }

    return $out;
}

/**
 * @return array<string, array<string, bool>>
 */
function justccell_sale_price_allowed_html(): array
{
    return [
        'span' => [
            'class'       => true,
            'aria-hidden' => true,
        ],
        'del'  => [
            'class'       => true,
            'aria-hidden' => true,
        ],
        'ins'  => [
            'class' => true,
        ],
    ];
}

/**
 * Was / now markup for a catalog sale. Empty `$was` returns the current price only.
 */
function justccell_sale_price_markup(string $now, string $was = ''): string
{
    $now = trim($now);
    $was = trim($was);
    if ($now === '') {
        return '';
    }
    if ($was === '' || $was === $now) {
        return esc_html($now);
    }

    $announce = sprintf(
        /* translators: 1: previous price, 2: current sale price */
        __('Was %1$s, now %2$s', 'justccell'),
        $was,
        $now
    );

    return sprintf(
        '<span class="p-buy__pair" aria-hidden="true"><del class="p-buy__was">%1$s</del><ins class="p-buy__now">%2$s</ins></span><span class="p-buy__sr">%3$s</span>',
        esc_html($was),
        esc_html($now),
        esc_html($announce)
    );
}

/**
 * @param array{price?:string,regular_price?:string,on_sale?:bool} $tier
 */
function justccell_tier_price_cell_html(array $tier): string
{
    $now = (string) ($tier['price'] ?? '');
    $was = (string) ($tier['regular_price'] ?? '');
    if (!empty($tier['on_sale']) && $was !== '') {
        return justccell_sale_price_markup($now, $was);
    }

    return esc_html($now);
}

/**
 * Catalog sale compare for strikethrough UI. Empty when not on sale.
 *
 * @return array{on_sale:bool,unit:float,regular:float}
 */
function justccell_product_sale_compare(?WC_Product $product): array
{
    $empty = ['on_sale' => false, 'unit' => 0.0, 'regular' => 0.0];
    if (!$product instanceof WC_Product) {
        return $empty;
    }

    $regular = (float) $product->get_regular_price('edit');
    $sale    = (float) $product->get_sale_price('edit');
    $unit    = (float) $product->get_price('edit');
    if ($unit <= 0) {
        $unit = $sale > 0 ? $sale : $regular;
    }
    if ($regular <= 0 || $unit <= 0 || $regular <= $unit + 0.0001) {
        return $empty;
    }

    return [
        'on_sale'  => true,
        'unit'     => $unit,
        'regular'  => $regular,
    ];
}

/**
 * Typed variation table, else parent table as entered. Never invents or offsets prices.
 *
 * @return list<array{min_qty:int,max_qty:int,price:float}>
 */
function justccell_tiered_pricing_resolve_rows_for_variation(int $variation_id): array
{
    if ($variation_id < 1 || !function_exists('wc_get_product')) {
        return [];
    }

    $variation = wc_get_product($variation_id);
    if (!$variation instanceof WC_Product_Variation) {
        return [];
    }

    $own = justccell_get_variation_manual_tiers($variation_id);
    if ($own !== []) {
        return $own;
    }

    $parent_id = (int) $variation->get_parent_id();
    if ($parent_id < 1) {
        return [];
    }

    return justccell_tiered_pricing_resolve_rows($parent_id, false);
}

function justccell_tiered_pricing_display_rows(int $product_id): array
{
    // Variable parents are not storefront prices — each child resolves via variation_tiers JSON.
    if (!is_admin() && function_exists('wc_get_product')) {
        $product = wc_get_product($product_id);
        if ($product instanceof WC_Product && $product->is_type('variable')) {
            return [];
        }
    }

    return justccell_tiered_pricing_rows_to_display(
        justccell_tiered_pricing_resolve_rows($product_id, false)
    );
}

/**
 * Per-variation buy-box rows: variation typed table, else parent typed table.
 *
 * @return list<array{range:string,price:string,qty_min:int,qty_max:int,unit:float,regular:float,regular_price:string,on_sale:bool}>
 */
function justccell_tiered_pricing_display_rows_for_variation(int $variation_id): array
{
    return justccell_tiered_pricing_rows_to_display(
        justccell_tiered_pricing_resolve_rows_for_variation($variation_id)
    );
}

/**
 * Unit price for cart quantity, or null when no matching band.
 */
function justccell_tier_unit_price_for_qty(int $product_id, int $variation_id, int $qty): ?float
{
    $qty = max(1, $qty);

    if ($variation_id > 0) {
        $tiers = justccell_tiered_pricing_resolve_rows_for_variation($variation_id);
    } else {
        $tiers = justccell_tiered_pricing_resolve_rows($product_id, false);
    }

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
 * One-time purge of seeded kit/battery fallback meta (never re-stamp defaults).
 * ---------------------------------------------------------------------- */

add_action('init', static function (): void {
    if (get_option('justccell_purged_seeded_kit_tiers') === '1.1.22b') {
        return;
    }
    if (!function_exists('wc_get_product')) {
        return;
    }

    $ids = get_posts([
        'post_type'        => ['product', 'product_variation'],
        'post_status'      => 'any',
        'posts_per_page'   => -1,
        'fields'           => 'ids',
        'suppress_filters' => true,
    ]);

    foreach ($ids as $id) {
        $id = (int) $id;
        if ($id < 1) {
            continue;
        }
        $rows = justccell_normalize_tiered_pricing_rows(get_post_meta($id, JUSTCCELL_TIER_META, true));
        if ($rows === [] || !justccell_tiered_pricing_is_seeded_default($rows)) {
            continue;
        }
        delete_post_meta($id, JUSTCCELL_TIER_META);
        if (function_exists('wc_delete_product_transients')) {
            wc_delete_product_transients($id);
        }
        $product = wc_get_product($id);
        if ($product instanceof WC_Product_Variation) {
            $parent_id = (int) $product->get_parent_id();
            if ($parent_id > 0 && function_exists('wc_delete_product_transients')) {
                wc_delete_product_transients($parent_id);
            }
        }
    }

    delete_option('justccell_tier_bulk_migrate');
    update_option('justccell_purged_seeded_kit_tiers', '1.1.22b', false);
}, 20);

/* -------------------------------------------------------------------------
 * Admin — Product data → Tiered pricing tab
 * ---------------------------------------------------------------------- */

function justccell_tiered_pricing_admin_field_name(string $key, int $index): string
{
    return 'justccell_tier_' . $key . '[' . $index . ']';
}

add_filter('woocommerce_product_data_tabs', static function (array $tabs): array {
    global $post;
    $product = ($post instanceof WP_Post && function_exists('wc_get_product'))
        ? wc_get_product((int) $post->ID)
        : null;
    if ($product instanceof WC_Product && $product->is_type('variable')) {
        return $tabs;
    }

    $tabs['justccell_tiered_pricing'] = [
        'label'    => __('Tiered pricing', 'justccell'),
        'target'   => 'justccell_tiered_pricing_data',
        'class'    => ['show_if_simple', 'hide_if_variable', 'hide_if_grouped', 'hide_if_external'],
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
    $product    = function_exists('wc_get_product') ? wc_get_product($product_id) : null;
    if ($product instanceof WC_Product && $product->is_type('variable')) {
        return;
    }

    $rows = justccell_normalize_tiered_pricing_rows(get_post_meta($product_id, JUSTCCELL_TIER_META, true));
    if ($rows === []) {
        $rows = justccell_tiered_pricing_legacy_rows($product_id);
    }
    if ($rows === []) {
        $rows = [['min_qty' => 0, 'max_qty' => 0, 'price' => 0.0]];
    }

    echo '<div id="justccell_tiered_pricing_data" class="panel woocommerce_options_panel hidden">';
    echo '<div class="options_group justccell-tier-pricing-wrap">';
    echo '<p class="description" style="padding:12px 12px 8px;">' . esc_html__(
        'Volume bands for this simple product. Every row you keep needs a starting quantity and a price per unit. Max quantity may be blank or 0 for unlimited. Remove unused rows before saving.',
        'justccell'
    ) . '</p>';
    echo '<p class="justccell-var-tiers__error" id="justccell-tier-pricing-error" hidden></p>';

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
    $min   = (int) ($row['min_qty'] ?? 0);
    $max   = (int) ($row['max_qty'] ?? 0);
    $price = (float) ($row['price'] ?? 0);
    $price_display = $price > 0 && function_exists('wc_format_localized_price')
        ? wc_format_localized_price($price)
        : ($price > 0 ? (string) $price : '');
    $min_display = $min > 0 ? (string) $min : '';
    $max_display = $max > 0 ? (string) $max : '';

    echo '<tr class="justccell-tier-row">';
    echo '<td><input type="number" min="1" step="1" name="' . esc_attr(justccell_tiered_pricing_admin_field_name('min', $index)) . '" value="' . esc_attr($min_display) . '" class="short justccell-tier-min" /></td>';
    echo '<td><input type="number" min="0" step="1" name="' . esc_attr(justccell_tiered_pricing_admin_field_name('max', $index)) . '" value="' . esc_attr($max_display) . '" class="short justccell-tier-max" placeholder="' . esc_attr__('0 = unlimited', 'justccell') . '" /></td>';
    echo '<td><input type="text" name="' . esc_attr(justccell_tiered_pricing_admin_field_name('price', $index)) . '" value="' . esc_attr($price_display) . '" class="short wc_input_price justccell-tier-price" placeholder="' . esc_attr__('0.00', 'justccell') . '" /></td>';
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

    $ver       = defined('JUSTCCELL_FEATURES_VERSION') ? JUSTCCELL_FEATURES_VERSION : '1.0.0';
    $theme_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : $ver;
    $uri       = defined('JUSTCCELL_URI') ? JUSTCCELL_URI : get_template_directory_uri();

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
        $theme_ver,
        true
    );

    wp_enqueue_style(
        'justccell-admin-variation-tiers',
        JUSTCCELL_FEATURES_URL . 'assets/css/admin-variation-tiers.css',
        [],
        $ver
    );

    wp_enqueue_script(
        'justccell-admin-variation-tiers',
        JUSTCCELL_FEATURES_URL . 'assets/js/admin-variation-tiers.js',
        ['jquery'],
        $ver,
        true
    );

    $i18n = [
        'incomplete' => __(
            'Volume tiers: each row you keep needs a starting quantity and a price per unit. Max quantity may be blank or 0 for unlimited. Remove unused rows, then save again.',
            'justccell'
        ),
    ];
    wp_localize_script('justccell-admin-variation-tiers', 'justccellVarTiers', $i18n);
    wp_localize_script('justccell-admin-tiered-pricing', 'justccellVarTiers', $i18n);
});

add_action('woocommerce_process_product_meta', static function (int $product_id): void {
    $product = function_exists('wc_get_product') ? wc_get_product($product_id) : null;
    if ($product instanceof WC_Product_Variable) {
        if (isset($_POST['justccell_var_tier_min']) && is_array($_POST['justccell_var_tier_min'])) {
            foreach (array_keys($_POST['justccell_var_tier_min']) as $vid) {
                justccell_variation_tiers_save_posted((int) $vid, 0);
            }
        }
        return;
    }

    if (
        !isset($_POST['justccell_tiered_pricing_nonce'])
        || !wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['justccell_tiered_pricing_nonce'])), 'justccell_tiered_pricing_save')
    ) {
        return;
    }

    $mins   = isset($_POST['justccell_tier_min']) && is_array($_POST['justccell_tier_min']) ? wp_unslash($_POST['justccell_tier_min']) : [];
    $maxes  = isset($_POST['justccell_tier_max']) && is_array($_POST['justccell_tier_max']) ? wp_unslash($_POST['justccell_tier_max']) : [];
    $prices = isset($_POST['justccell_tier_price']) && is_array($_POST['justccell_tier_price']) ? wp_unslash($_POST['justccell_tier_price']) : [];

    $parsed = justccell_tiered_pricing_parse_admin_post($mins, $maxes, $prices);
    if (!$parsed['ok']) {
        foreach ($parsed['errors'] as $error) {
            justccell_tiered_pricing_admin_error($error);
        }
        return;
    }

    if ($parsed['rows'] === []) {
        delete_post_meta($product_id, JUSTCCELL_TIER_META);
    } else {
        update_post_meta($product_id, JUSTCCELL_TIER_META, $parsed['rows']);
    }

    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients($product_id);
    }
});

/* -------------------------------------------------------------------------
 * Admin — variation panel repeater (exact prices for this combination)
 * ---------------------------------------------------------------------- */

/**
 * @param array<string, mixed> $variation_data
 */
function justccell_variation_tiers_admin_fields(int $loop, $variation_data, $variation): void
{
    unset($variation_data);

    $variation_id = 0;
    if ($variation instanceof WP_Post) {
        $variation_id = (int) $variation->ID;
    } elseif (is_object($variation) && isset($variation->ID)) {
        $variation_id = (int) $variation->ID;
    }

    $rows = $variation_id > 0 ? justccell_get_variation_manual_tiers($variation_id) : [];
    if ($rows === []) {
        $rows = [['min_qty' => 0, 'max_qty' => 0, 'price' => 0.0]];
    }

    echo '<div class="form-row form-row-full justccell-var-tiers" data-loop="' . esc_attr((string) $loop) . '" data-variation-id="' . esc_attr((string) $variation_id) . '" data-remove-label="' . esc_attr__('Remove tier', 'justccell') . '">';
    echo '<p class="justccell-var-tiers__title"><strong>' . esc_html__('Volume tiers (this variation)', 'justccell') . '</strong></p>';
    echo '<p class="description">' . esc_html__(
        'Type a starting quantity and price per unit on every row you keep. Max quantity may be blank or 0 for unlimited. Remove unused rows. Click Save changes on this Variations tab after editing.',
        'justccell'
    ) . '</p>';
    echo '<p class="justccell-var-tiers__error" hidden></p>';
    echo '<table class="widefat justccell-var-tiers__table"><thead><tr>';
    echo '<th>' . esc_html__('Min qty', 'justccell') . '</th>';
    echo '<th>' . esc_html__('Max qty (blank or 0 = unlimited)', 'justccell') . '</th>';
    echo '<th>' . esc_html__('Price per unit', 'justccell') . '</th>';
    echo '<th></th>';
    echo '</tr></thead><tbody class="justccell-var-tier-rows">';

    foreach ($rows as $i => $row) {
        justccell_variation_tiers_admin_render_row($variation_id, (int) $i, $row);
    }

    echo '</tbody></table>';
    echo '<p><button type="button" class="button justccell-var-tier-add">' . esc_html__('Add tier', 'justccell') . '</button></p>';
    echo '</div>';
}
add_action('woocommerce_product_after_variable_attributes', 'justccell_variation_tiers_admin_fields', 10, 3);

/**
 * @param array{min_qty:int,max_qty:int,price:float} $row
 */
function justccell_variation_tiers_admin_render_row(int $variation_id, int $index, array $row): void
{
    $min   = (int) ($row['min_qty'] ?? 0);
    $max   = (int) ($row['max_qty'] ?? 0);
    $price = (float) ($row['price'] ?? 0);
    $price_display = $price > 0 && function_exists('wc_format_localized_price')
        ? wc_format_localized_price($price)
        : ($price > 0 ? (string) $price : '');
    $min_display = $min > 0 ? (string) $min : '';
    $max_display = $max > 0 ? (string) $max : '';

    $prefix = 'justccell_var_tier_';
    $key    = (string) max(0, $variation_id);
    echo '<tr class="justccell-var-tier-row">';
    echo '<td><input type="number" min="1" step="1" name="' . esc_attr($prefix . 'min[' . $key . '][' . $index . ']') . '" value="' . esc_attr($min_display) . '" class="short justccell-var-tier-min" /></td>';
    echo '<td><input type="number" min="0" step="1" name="' . esc_attr($prefix . 'max[' . $key . '][' . $index . ']') . '" value="' . esc_attr($max_display) . '" class="short justccell-var-tier-max" placeholder="' . esc_attr__('0 = unlimited', 'justccell') . '" /></td>';
    echo '<td><input type="text" name="' . esc_attr($prefix . 'price[' . $key . '][' . $index . ']') . '" value="' . esc_attr($price_display) . '" class="short wc_input_price justccell-var-tier-price" placeholder="' . esc_attr__('0.00', 'justccell') . '" /></td>';
    echo '<td><button type="button" class="button-link-delete justccell-var-tier-remove" aria-label="' . esc_attr__('Remove tier', 'justccell') . '">&times;</button></td>';
    echo '</tr>';
}

/**
 * Persist one variation table from POST. No-op when that variation was not in the payload.
 */
function justccell_variation_tiers_save_posted(int $variation_id, int $loop = 0): void
{
    if ($variation_id < 1) {
        return;
    }
    $parent_id = (int) wp_get_post_parent_id($variation_id);
    if (!current_user_can('edit_product', $variation_id)
        && ($parent_id < 1 || !current_user_can('edit_product', $parent_id))
    ) {
        return;
    }

    $has_id   = isset($_POST['justccell_var_tier_min'][$variation_id]) && is_array($_POST['justccell_var_tier_min'][$variation_id]);
    $has_loop = isset($_POST['justccell_var_tier_min'][$loop]) && is_array($_POST['justccell_var_tier_min'][$loop]);
    if ($has_id) {
        $bucket = $variation_id;
    } elseif ($has_loop) {
        $bucket = $loop;
    } else {
        return;
    }

    $mins   = wp_unslash($_POST['justccell_var_tier_min'][$bucket]);
    $maxes  = isset($_POST['justccell_var_tier_max'][$bucket]) && is_array($_POST['justccell_var_tier_max'][$bucket])
        ? wp_unslash($_POST['justccell_var_tier_max'][$bucket])
        : [];
    $prices = isset($_POST['justccell_var_tier_price'][$bucket]) && is_array($_POST['justccell_var_tier_price'][$bucket])
        ? wp_unslash($_POST['justccell_var_tier_price'][$bucket])
        : [];

    $parsed = justccell_tiered_pricing_parse_admin_post($mins, $maxes, $prices);
    if (!$parsed['ok']) {
        foreach ($parsed['errors'] as $error) {
            justccell_tiered_pricing_admin_error(
                sprintf(
                    /* translators: 1: variation ID, 2: validation message */
                    __('Variation #%1$d — %2$s', 'justccell'),
                    $variation_id,
                    $error
                )
            );
        }
        return;
    }

    if ($parsed['rows'] === []) {
        delete_post_meta($variation_id, JUSTCCELL_VARIATION_TIER_META);
        delete_post_meta($variation_id, JUSTCCELL_TIER_META);
    } else {
        update_post_meta($variation_id, JUSTCCELL_VARIATION_TIER_META, $parsed['rows']);
        delete_post_meta($variation_id, JUSTCCELL_TIER_META);
    }

    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients($variation_id);
        $parent_id = (int) wp_get_post_parent_id($variation_id);
        if ($parent_id > 0) {
            wc_delete_product_transients($parent_id);
            if (function_exists('justccell_wc_clear_variable_product_transients')) {
                justccell_wc_clear_variable_product_transients($parent_id);
            }
        }
    }
}

add_action('woocommerce_save_product_variation', static function (int $variation_id, $loop): void {
    justccell_variation_tiers_save_posted($variation_id, (int) $loop);
}, 20, 2);

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

        if ($unit === null || $unit <= 0) {
            $unit = justccell_tier_unit_price_for_qty($product_id, 0, $qty);
        }

        if ($unit === null || $unit <= 0) {
            continue;
        }

        $item['data']->set_price($unit);
    }
}, 10);
