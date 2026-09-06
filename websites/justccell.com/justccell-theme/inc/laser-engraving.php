<?php
/**
 * Inline laser engraving — Step 1: ACF, config helper, asset enqueues.
 *
 * Cart / checkout / order hooks are added in Step 4.
 * See docs/laser-engraving-system.md.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_LASER_UPLOAD_DIR = 'laser-engravings';
const JUSTCCELL_LASER_MAX_BYTES  = 2621440; // ~2.5 MB decoded artwork

/**
 * Laser engraving options sub-page. Field groups load from Local JSON + DB (Phase 3 Batch 3).
 */
add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_options_sub_page')) {
        return;
    }

    acf_add_options_sub_page([
        'page_title'  => __('Laser Engraving', 'justccell'),
        'menu_title'  => __('Laser Engraving', 'justccell'),
        'parent_slug' => 'justccell',
        'menu_slug'   => 'justccell-laser-settings',
        'capability'  => 'edit_theme_options',
    ]);

    // group_jc_laser_engraving_global — Local JSON + DB only (Phase 3 Batch 3).
    // group_jc_laser_engraving, group_jc_laser_engraving_cat — Local JSON + DB only.
});

/**
 * Global setup fee (ex VAT). Default £25.00 when unset.
 */
function justccell_laser_global_setup_fee(): float
{
    if (!function_exists('get_field')) {
        return 25.0;
    }
    $setup = get_field('laser_global_setup_fee', 'option');
    if ($setup === null || $setup === '') {
        return 25.0;
    }
    return round((float) $setup, 4);
}

/**
 * @return list<array{minQty:int,maxQty:int,pricePerUnit:float}>
 */
function justccell_laser_global_tiers(): array
{
    if (!function_exists('get_field')) {
        return [];
    }
    return justccell_laser_normalize_tiers(get_field('laser_global_tiered_pricing_matrix', 'option'));
}

function justccell_laser_whatsapp_required(): bool
{
    return function_exists('get_field') && (bool) get_field('laser_whatsapp_required', 'option');
}

/**
 * Sanitize customer WhatsApp / phone for cart + order meta.
 */
function justccell_laser_sanitize_whatsapp(string $raw): string
{
    $raw = trim($raw);
    if ($raw === '') {
        return '';
    }
    $clean = preg_replace('/[^\d+\s\-()]/', '', $raw);
    return is_string($clean) ? trim($clean) : '';
}

/**
 * @param mixed $raw
 */
function justccell_laser_acf_image_url($raw): string
{
    if (is_array($raw)) {
        $url = (string) ($raw['url'] ?? '');
        if ($url !== '') {
            return $url;
        }
        $id = (int) ($raw['ID'] ?? $raw['id'] ?? 0);
        return $id > 0 ? (string) wp_get_attachment_image_url($id, 'full') : '';
    }
    if (is_numeric($raw)) {
        $id = (int) $raw;
        return $id > 0 ? (string) wp_get_attachment_image_url($id, 'full') : '';
    }
    if (is_string($raw) && $raw !== '') {
        return esc_url_raw($raw);
    }
    return '';
}

/**
 * @param mixed $rows
 * @return list<array{minQty:int,maxQty:int,pricePerUnit:float}>
 */
function justccell_laser_normalize_tiers($rows): array
{
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $out[] = [
            'minQty'       => max(1, (int) ($row['min_qty'] ?? 1)),
            'maxQty'       => max(0, (int) ($row['max_qty'] ?? 0)),
            'pricePerUnit' => round((float) ($row['price_per_unit'] ?? 0), 4),
        ];
    }
    usort($out, static fn (array $a, array $b): int => $a['minQty'] <=> $b['minQty']);
    return $out;
}

/**
 * @param mixed $rows
 * @return list<array{x:float,y:float,width:float,height:float}>
 */
function justccell_laser_normalize_zones($rows): array
{
    if (!is_array($rows)) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $w = (float) ($row['width'] ?? $row['w'] ?? 0);
        $h = (float) ($row['height'] ?? $row['h'] ?? 0);
        if ($w <= 0 || $h <= 0) {
            continue;
        }
        $out[] = [
            'x'      => (float) ($row['x'] ?? 0),
            'y'      => (float) ($row['y'] ?? 0),
            'width'  => $w,
            'height' => $h,
        ];
    }
    return $out;
}

/**
 * Centered 640×640 fallback when a product has a canvas image but no mapped zone yet.
 *
 * @return list<array{x:float,y:float,width:float,height:float}>
 */
function justccell_laser_default_safe_zones(): array
{
    return [
        [
            'x'      => 120.0,
            'y'      => 120.0,
            'width'  => 400.0,
            'height' => 400.0,
        ],
    ];
}

/**
 * @return list<array{x:float,y:float,width:float,height:float}>
 */
function justccell_laser_zones_from_json(string $json): array
{
    $json = trim($json);
    if ($json === '') {
        return [];
    }
    $decoded = json_decode($json, true);
    return justccell_laser_normalize_zones(is_array($decoded) ? $decoded : []);
}

/**
 * Primary storefront product_cat term ID for category defaults.
 */
function justccell_laser_primary_cat_term_id(int $product_id): int
{
    $terms = get_the_terms($product_id, 'product_cat');
    if (!is_array($terms) || $terms === []) {
        return 0;
    }
    if (function_exists('justccell_product_category_labels')) {
        $allowed = justccell_product_category_labels();
        foreach ($terms as $term) {
            if ($term instanceof WP_Term && array_key_exists($term->slug, $allowed)) {
                return (int) $term->term_id;
            }
        }
    }
    $first = $terms[0];
    return $first instanceof WP_Term ? (int) $first->term_id : 0;
}

/**
 * Woo product ID for the current product clone / single.
 */
function justccell_current_product_woo_id(): int
{
    if (is_singular('product')) {
        return (int) get_the_ID();
    }
    $slug = function_exists('justccell_current_product_slug')
        ? justccell_current_product_slug()
        : (string) get_query_var('justccell_product');
    if ($slug === '') {
        return 0;
    }
    if (function_exists('justccell_product_page')) {
        $page = justccell_product_page($slug);
        if (is_array($page)) {
            return (int) ($page['woo_id'] ?? 0);
        }
    }
    $post = get_page_by_path($slug, OBJECT, 'product');
    return $post instanceof WP_Post ? (int) $post->ID : 0;
}

/**
 * Laser engraving quantity tiers — category defaults, legacy product matrix, then a single open band.
 *
 * @return list<array{minQty:int,maxQty:int,pricePerUnit:float}>
 */
function justccell_laser_resolve_tiers(int $product_id, string $term_key): array
{
    unset($product_id);
    $global = justccell_laser_global_tiers();
    if ($global !== []) {
        return $global;
    }

    if ($term_key !== '') {
        $tiers = justccell_laser_normalize_tiers(get_field('laser_tiered_pricing_matrix', $term_key));
        if ($tiers !== []) {
            return $tiers;
        }
    }

    return [
        [
            'minQty'       => 1,
            'maxQty'       => 0,
            'pricePerUnit' => 0.0,
        ],
    ];
}

/**
 * Resolved engraving config (section 3.3) or null when disabled.
 *
 * @return array<string, mixed>|null
 */
function justccell_laser_config(int $product_id): ?array
{
    if ($product_id < 1 || !function_exists('get_field')) {
        return null;
    }

    if (!(bool) get_field('enable_engraving', $product_id)) {
        return null;
    }

    $term_id  = justccell_laser_primary_cat_term_id($product_id);
    $term_key = $term_id > 0 ? 'product_cat_' . $term_id : '';

    $setup_fee = justccell_laser_global_setup_fee();

    $tiers = justccell_laser_resolve_tiers($product_id, $term_key);

    $bg = justccell_laser_acf_image_url(get_field('canvas_background_image', $product_id));
    if ($bg === '' && $term_key !== '') {
        $bg = justccell_laser_acf_image_url(get_field('laser_canvas_background_image', $term_key));
    }

    $zones = justccell_laser_normalize_zones(get_field('safe_zone_coordinates', $product_id));
    $zones = array_merge($zones, justccell_laser_zones_from_json((string) get_field('safe_zone_json', $product_id)));
    if ($zones === [] && $term_key !== '') {
        $zones = justccell_laser_normalize_zones(get_field('laser_safe_zone_coordinates', $term_key));
        if ($zones === []) {
            $zones = justccell_laser_zones_from_json((string) get_field('laser_safe_zone_json', $term_key));
        }
    }
    // Canvas image alone is enough to run the editor — use a centered default
    // safe zone until the product mapper has been saved in wp-admin.
    if ($zones === [] && $bg !== '') {
        $zones = justccell_laser_default_safe_zones();
    }

    $editor_ready = $bg !== '' && $zones !== [];

    $product = function_exists('wc_get_product') ? wc_get_product($product_id) : null;
    $sku     = $product instanceof WC_Product ? (string) $product->get_sku() : '';
    $cat     = '';
    if ($term_id > 0) {
        $term = get_term($term_id, 'product_cat');
        $cat  = $term instanceof WP_Term ? (string) $term->slug : '';
    }

    return [
        'enabled'      => 1,
        'editorReady'  => $editor_ready ? 1 : 0,
        'productId'    => $product_id,
        'sku'       => $sku,
        'category'  => $cat,
        'currency'  => [
            'code'      => function_exists('justccell_current_currency') ? justccell_current_currency() : (function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'GBP'),
            'symbol'    => function_exists('justccell_currency_symbol') ? justccell_currency_symbol() : '£',
            'precision' => 2,
        ],
        'setupFee'  => $setup_fee,
        'tiers'     => $tiers,
        'canvas'    => [
            'backgroundUrl' => $bg,
            'width'         => 640,
            'height'        => 640,
        ],
        'safeZones' => $zones,
        'fonts'     => [
            ['id' => 'montserrat', 'label' => 'Montserrat', 'family' => 'Montserrat, sans-serif'],
            ['id' => 'editorial', 'label' => 'Editorial Serif', 'family' => "Georgia, 'Times New Roman', serif"],
            ['id' => 'stencil', 'label' => 'Stencil Mono', 'family' => "'Courier New', Courier, monospace"],
            ['id' => 'mark', 'label' => 'Bold Mark', 'family' => "Impact, 'Arial Narrow', sans-serif"],
        ],
        'whatsappRequired' => justccell_laser_whatsapp_required() ? 1 : 0,
        'i18n' => [
            'toggle'     => __('Add on Laser Engraving (Allow 2 days extra for delivery)', 'justccell'),
            'summary'    => __('Engraving estimate', 'justccell'),
            'tiersTitle' => __('Engraving volume pricing', 'justccell'),
            'tiersQty'   => __('Quantity', 'justccell'),
            'tiersPpu'   => __('Per unit', 'justccell'),
            'setup'      => __('Setup fee', 'justccell'),
            'unit'       => __('Per unit', 'justccell'),
            'total'      => __('Engraving total', 'justccell'),
            'addText'    => __('Add text', 'justccell'),
            'upload'     => __('Upload logo', 'justccell'),
            'remove'     => __('Remove selected', 'justccell'),
            'font'       => __('Font', 'justccell'),
            'size'       => __('Size', 'justccell'),
            'spacing'    => __('Spacing', 'justccell'),
            'submit'     => __('Add to cart', 'justccell'),
            'needDesign' => __('Add text or a logo inside the safe zone before adding to cart.', 'justccell'),
            'payloadTooLarge' => __('Engraving file is too large for checkout. Simplify the design or use a smaller logo.', 'justccell'),
            'safeHint'   => __('Drag within the dashed safe zone only.', 'justccell'),
            'uploadHint' => __('For best results, upload hi-res black & white artwork with clean edges. Avoid fine details, gradients, shadows or blur. Accepted formats: .jpg, .jpeg, .png, .ai, .psd, .svg, .eps, .pdf. If your file doesn\'t follow these guidelines, please contact us before ordering.', 'justccell'),
            'whatsappLabel' => __('WhatsApp Phone Number (for artwork proof approval)', 'justccell'),
            'whatsappPlaceholder' => __('e.g. +44 7495 338694', 'justccell'),
            'whatsappRequired' => __('Enter a WhatsApp number so we can send your artwork proof before production.', 'justccell'),
            'uploadFailed' => __('Could not process that image. Try a PNG logo on a transparent background.', 'justccell'),
            'incomplete'   => __('Laser engraving is enabled for this product but the editor is still being configured. You can add to cart without engraving for now.', 'justccell'),
            'save'         => __('Save engraving', 'justccell'),
            'saved'        => __('Engraving saved for this session', 'justccell'),
            'saveHint'     => __('Save keeps your text, logo, and layout until you add to cart or close this tab.', 'justccell'),
        ],
        'ajax' => [
            'url'   => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('justccell_laser'),
        ],
        'cartUrl' => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'),
    ];
}

/**
 * Enqueue Fabric + editor + localize JustccellLaser only when config is valid.
 */
add_action('wp_enqueue_scripts', static function (): void {
    if (is_admin()) {
        return;
    }

    $product_id = justccell_current_product_woo_id();
    if ($product_id < 1) {
        return;
    }

    $config = justccell_laser_config($product_id);
    if ($config === null) {
        return;
    }

    $style_deps = ['justccell-globals'];
    if (wp_style_is('justccell-product', 'registered') || wp_style_is('justccell-product', 'enqueued')) {
        $style_deps[] = 'justccell-product';
    }

    wp_enqueue_style(
        'justccell-laser',
        JUSTCCELL_URI . '/assets/css/laser-engraving.css',
        $style_deps,
        JUSTCCELL_VERSION
    );

    $script_deps = ['justccell-cart'];
    if (!empty($config['editorReady'])) {
        wp_enqueue_script(
            'fabric',
            JUSTCCELL_URI . '/assets/js/vendor/fabric.min.js',
            [],
            '5.3.0',
            true
        );
        $script_deps[] = 'fabric';
    }

    wp_enqueue_script(
        'justccell-laser',
        JUSTCCELL_URI . '/assets/js/laser-engraving.js',
        $script_deps,
        JUSTCCELL_VERSION,
        true
    );

    wp_localize_script('justccell-laser', 'JustccellLaser', $config);
}, 30);

/**
 * Render UI once per request (§4.1 / §4.2).
 */
function justccell_laser_render_ui(int $product_id = 0): void
{
    static $rendered = false;
    if ($rendered) {
        return;
    }
    if ($product_id < 1) {
        $product_id = justccell_current_product_woo_id();
    }
    $config = justccell_laser_config($product_id);
    if ($config === null) {
        return;
    }
    $rendered = true;
    get_template_part('template-parts/product/laser-engraving', null, [
        'product_id' => $product_id,
        'config'     => $config,
    ]);
}

/* -------------------------------------------------------------------------
 * Step 4 — Cart, checkout & order (architecture §6 / §7)
 * ---------------------------------------------------------------------- */

/**
 * Server PPU from tiers (§7).
 *
 * @param list<array{minQty:int,maxQty:int,pricePerUnit:float}> $tiers
 */
function justccell_laser_ppu_for_qty(array $tiers, int $qty): float
{
    $qty = max(1, $qty);
    foreach ($tiers as $tier) {
        $min = (int) ($tier['minQty'] ?? 1);
        $max = (int) ($tier['maxQty'] ?? 0);
        if ($qty >= $min && ($max === 0 || $qty <= $max)) {
            return (float) ($tier['pricePerUnit'] ?? 0);
        }
    }
    return 0.0;
}

/**
 * @param list<array{minQty:int,maxQty:int,pricePerUnit:float}> $tiers
 * @return array{0:int,1:int}
 */
function justccell_laser_qty_band_for_qty(array $tiers, int $qty): array
{
    $qty = max(1, $qty);
    foreach ($tiers as $tier) {
        $min = (int) ($tier['minQty'] ?? 1);
        $max = (int) ($tier['maxQty'] ?? 0);
        if ($qty >= $min && ($max === 0 || $qty <= $max)) {
            return [$min, $max];
        }
    }
    return [1, 0];
}

function justccell_laser_validate_data_url(string $data): bool
{
    if ($data === '' || !preg_match('#^data:image/(png|jpeg|jpg);base64,#i', $data)) {
        return false;
    }
    $parts = explode(',', $data, 2);
    if (count($parts) !== 2) {
        return false;
    }
    $raw = base64_decode($parts[1], true);
    if ($raw === false || $raw === '') {
        return false;
    }
    return strlen($raw) <= JUSTCCELL_LASER_MAX_BYTES;
}

/**
 * @param mixed $node
 * @return mixed
 */
function justccell_laser_sanitize_layout_node($node)
{
    if (is_int($node) || is_float($node) || is_bool($node)) {
        return $node;
    }
    if (is_string($node)) {
        return sanitize_text_field($node);
    }
    if (!is_array($node)) {
        return null;
    }

    $out = [];
    foreach ($node as $key => $value) {
        $clean_key = is_string($key) ? sanitize_key($key) : $key;
        $clean     = justccell_laser_sanitize_layout_node($value);
        if ($clean !== null) {
            $out[$clean_key] = $clean;
        }
    }

    return $out;
}

/**
 * Compact canvas metadata for cart / order (no Base64 images).
 *
 * @return array<string, mixed>
 */
function justccell_laser_sanitize_layout(string $raw): array
{
    if ($raw === '' || strlen($raw) > 200000) {
        return [];
    }
    $decoded = json_decode($raw, true);
    if (!is_array($decoded)) {
        return [];
    }
    $clean = justccell_laser_sanitize_layout_node($decoded);

    return is_array($clean) ? $clean : [];
}

/**
 * Persist a data-URL or keep an already-uploaded HTTPS file.
 */
function justccell_laser_resolve_file_url(string $value, int $order_id, string $key): string
{
    $value = trim($value);
    if ($value === '') {
        return '';
    }
    if (preg_match('#^https?://#i', $value) === 1) {
        return esc_url_raw($value);
    }

    return justccell_laser_persist_artwork($value, $order_id, $key);
}

/**
 * Ingest zero double-entry POST into cart item data (§6.1).
 *
 * @param array<string, mixed> $cart_item_data
 * @return array<string, mixed>
 */
function justccell_laser_ingest_cart_item_data(array $cart_item_data, int $product_id): array
{
    $enabled = isset($_POST['justccell_laser_enabled'])
        && (string) wp_unslash($_POST['justccell_laser_enabled']) === '1';
    if (!$enabled) {
        return $cart_item_data;
    }

    $config = justccell_laser_config($product_id);
    if ($config === null || empty($config['editorReady'])) {
        throw new \Exception(__('Laser engraving is not available for this product.', 'justccell'));
    }

    $artwork = isset($_POST['justccell_laser_artwork'])
        ? (string) wp_unslash($_POST['justccell_laser_artwork'])
        : '';
    $preview = isset($_POST['justccell_laser_preview'])
        ? (string) wp_unslash($_POST['justccell_laser_preview'])
        : '';
    $text = isset($_POST['justccell_laser_text'])
        ? sanitize_text_field((string) wp_unslash($_POST['justccell_laser_text']))
        : '';
    $layout = justccell_laser_sanitize_layout(
        isset($_POST['justccell_laser_layout'])
            ? (string) wp_unslash($_POST['justccell_laser_layout'])
            : ''
    );
    $whatsapp = justccell_laser_sanitize_whatsapp(
        isset($_POST['justccell_laser_whatsapp'])
            ? (string) wp_unslash($_POST['justccell_laser_whatsapp'])
            : ''
    );
    if (justccell_laser_whatsapp_required() && $whatsapp === '') {
        throw new \Exception(__('Please enter a WhatsApp number for artwork proof approval.', 'justccell'));
    }
    $qty = isset($_POST['quantity']) ? max(1, (int) $_POST['quantity']) : 1;

    if (!justccell_laser_validate_data_url($artwork)) {
        throw new \Exception(__('Engraving artwork is missing or invalid.', 'justccell'));
    }
    if ($preview !== '' && !justccell_laser_validate_data_url($preview)) {
        $preview = '';
    }
    if ($preview === '') {
        $preview = $artwork;
    }

    $ppu  = justccell_laser_ppu_for_qty($config['tiers'], $qty);
    $band = justccell_laser_qty_band_for_qty($config['tiers'], $qty);
    $setup = (float) $config['setupFee'];

    $product = function_exists('wc_get_product') ? wc_get_product($product_id) : null;
    $base    = $product instanceof WC_Product ? (float) $product->get_price('edit') : 0.0;

    $cart_item_data['justccell_laser'] = [
        'enabled'     => true,
        'is_engraved' => true,
        'artwork'     => $artwork,
        'preview'     => $preview,
        'text'        => $text,
        'whatsapp'    => $whatsapp,
        'layout'      => $layout,
        'unit'       => $ppu,
        'setup_fee'  => $setup,
        'qty_band'   => $band,
        'base_price' => $base,
        'product_id' => $product_id,
        'safe_zones' => $config['safeZones'],
        'unique'     => md5($artwork . '|' . $text . '|' . (string) microtime(true)),
    ];

    return $cart_item_data;
}

add_filter('woocommerce_add_cart_item_data', static function ($cart_item_data, $product_id) {
    if (!is_array($cart_item_data)) {
        $cart_item_data = [];
    }
    return justccell_laser_ingest_cart_item_data($cart_item_data, (int) $product_id);
}, 20, 2);

add_filter('woocommerce_add_cart_item', static function ($cart_item) {
    return $cart_item;
}, 20);

add_filter('woocommerce_get_cart_item_from_session', static function ($cart_item, $values) {
    if (is_array($values) && isset($values['justccell_laser']) && is_array($cart_item)) {
        $cart_item['justccell_laser'] = $values['justccell_laser'];
    }
    return $cart_item;
}, 20, 2);

add_filter('woocommerce_product_is_in_stock', static function ($in_stock, $product) {
    if (!$product instanceof WC_Product) {
        return $in_stock;
    }
    return justccell_laser_bypass_catalog_gate($product) ? true : $in_stock;
}, 20, 2);

/**
 * True when the current request is an engraved add-to-cart.
 */
function justccell_laser_request_enabled(): bool
{
    return isset($_REQUEST['justccell_laser_enabled'])
        && (string) wp_unslash($_REQUEST['justccell_laser_enabled']) === '1';
}

/**
 * Cart line with engraving meta matching a product or variation.
 *
 * @return array<string, mixed>|null
 */
function justccell_laser_cart_line_for_product(WC_Product $product): ?array
{
    static $busy = false;
    if ($busy || !function_exists('WC') || !WC()->cart) {
        return null;
    }

    $contents = WC()->cart->cart_contents ?? null;
    if (!is_array($contents) || $contents === []) {
        return null;
    }

    $busy = true;
    $product_id   = (int) $product->get_id();
    $parent_id    = (int) $product->get_parent_id();
    $config_scope = $parent_id > 0 ? $parent_id : $product_id;

    foreach ($contents as $item) {
        if (empty($item['justccell_laser']['enabled'])) {
            continue;
        }

        $line_product_id   = (int) ($item['product_id'] ?? 0);
        $line_variation_id = (int) ($item['variation_id'] ?? 0);

        if ($line_product_id !== $config_scope) {
            continue;
        }
        if ($line_variation_id > 0 && $line_variation_id !== $product_id && $parent_id > 0) {
            continue;
        }
        if ($line_variation_id === 0 && $parent_id > 0) {
            continue;
        }

        $busy = false;
        return is_array($item) ? $item : null;
    }

    $busy = false;
    return null;
}

/**
 * Quote-only catalog SKUs become purchasable only for engraved cart lines (or laser ATC POST).
 */
function justccell_laser_bypass_catalog_gate(WC_Product $product): bool
{
    return justccell_laser_should_bypass_catalog_gate($product);
}

/**
 * @return list<int>
 */
function justccell_laser_session_line_ids(): array
{
    if (!function_exists('WC') || !WC()->session) {
        return [];
    }
    $raw = WC()->session->get('justccell_laser_line_ids');
    if (!is_array($raw)) {
        return [];
    }
    return array_values(array_unique(array_filter(array_map('intval', $raw))));
}

function justccell_laser_register_session_line(int $product_id, int $variation_id = 0): void
{
    if (!function_exists('WC') || !WC()->session || $product_id < 1) {
        return;
    }

    $ids = justccell_laser_session_line_ids();
    $ids[] = $product_id;
    if ($variation_id > 0) {
        $ids[] = $variation_id;
    }
    WC()->session->set('justccell_laser_line_ids', array_values(array_unique($ids)));
}

function justccell_laser_product_in_session(WC_Product $product): bool
{
    $ids = justccell_laser_session_line_ids();
    if ($ids === []) {
        return false;
    }

    $product_id = (int) $product->get_id();
    $parent_id  = (int) $product->get_parent_id();
    return in_array($product_id, $ids, true)
        || ($parent_id > 0 && in_array($parent_id, $ids, true));
}

function justccell_laser_should_bypass_catalog_gate(WC_Product $product): bool
{
    $config_id = (int) ($product->get_parent_id() ?: $product->get_id());
    if (justccell_laser_config($config_id) === null) {
        return false;
    }

    if (justccell_laser_request_enabled()) {
        return true;
    }

    return justccell_laser_product_in_session($product)
        || justccell_laser_cart_line_for_product($product) !== null;
}

/**
 * Resolve variable-product POST data before WooCommerce handles add-to-cart.
 * Maps buy-box attr_* selects → attribute_* and finds variation_id.
 */
function justccell_laser_prepare_add_to_cart_request(): void
{
    if (!justccell_laser_request_enabled() || !isset($_REQUEST['add-to-cart'])) {
        return;
    }
    if (function_exists('justccell_cart_prepare_variable_add_to_cart_request')) {
        justccell_cart_prepare_variable_add_to_cart_request();
    }
}
add_action('wp_loaded', 'justccell_laser_prepare_add_to_cart_request', 19);

/**
 * Quote-only SKUs often have empty Woo prices; laser lines need a numeric price at ATC time.
 */
function justccell_laser_runtime_price($price, $product): string
{
    static $busy = false;
    if ($busy || !$product instanceof WC_Product) {
        return (string) $price;
    }

    if ($price !== '' && $price !== null && is_numeric($price) && (float) $price > 0) {
        return (string) $price;
    }

    $busy = true;

    try {
        $config_id = $product->get_parent_id() > 0 ? (int) $product->get_parent_id() : (int) $product->get_id();
        $config    = justccell_laser_config($config_id);
        if ($config === null) {
            return (string) $price;
        }

        if (
            !justccell_laser_request_enabled()
            && !justccell_laser_product_in_session($product)
            && justccell_laser_cart_line_for_product($product) === null
        ) {
            return (string) $price;
        }

        $qty = 1;
        if (justccell_laser_request_enabled()) {
            $qty = isset($_REQUEST['quantity']) ? max(1, (int) wp_unslash($_REQUEST['quantity'])) : 1;
        } else {
            $line = justccell_laser_cart_line_for_product($product);
            if ($line !== null) {
                $qty = max(1, (int) ($line['quantity'] ?? 1));
            } elseif (function_exists('WC') && WC()->cart) {
                $contents = WC()->cart->cart_contents ?? [];
                if (is_array($contents)) {
                    foreach ($contents as $item) {
                        if (empty($item['justccell_laser']['enabled'])) {
                            continue;
                        }
                        $line_variation_id = (int) ($item['variation_id'] ?? 0);
                        if ($line_variation_id === (int) $product->get_id()) {
                            $qty = max(1, (int) ($item['quantity'] ?? 1));
                            break;
                        }
                    }
                }
            }
        }

        $ppu = justccell_laser_ppu_for_qty($config['tiers'], $qty);
        $base = (float) get_post_meta($config_id, '_price', true);
        if ($base <= 0 && $product->get_parent_id() > 0) {
            $base = (float) get_post_meta((int) $product->get_parent_id(), '_price', true);
        }

        return (string) max(0.01, $base + $ppu);
    } finally {
        $busy = false;
    }
}
add_filter('woocommerce_product_get_price', 'justccell_laser_runtime_price', 20, 2);
add_filter('woocommerce_product_get_regular_price', 'justccell_laser_runtime_price', 20, 2);

/**
 * Variable children with empty catalog prices must still be purchasable for laser ATC.
 */
function justccell_laser_force_purchasable($purchasable, $product): bool
{
    if (!$product instanceof WC_Product) {
        return (bool) $purchasable;
    }
    return justccell_laser_should_bypass_catalog_gate($product) ? true : (bool) $purchasable;
}
add_filter('woocommerce_is_purchasable', 'justccell_laser_force_purchasable', 999, 2);

/**
 * @param mixed $visible
 */
function justccell_laser_force_variation_visible($visible, int $variation_id, int $product_id, $variation): bool
{
    unset($variation);
    $parent_id = $product_id > 0 ? $product_id : (int) wp_get_post_parent_id($variation_id);
    if (justccell_laser_config($parent_id) === null) {
        return (bool) $visible;
    }

    if (justccell_laser_request_enabled()) {
        return true;
    }

    $variation = wc_get_product($variation_id);
    return $variation instanceof WC_Product && justccell_laser_cart_line_for_product($variation)
        ? true
        : (bool) $visible;
}
add_filter('woocommerce_variation_is_visible', 'justccell_laser_force_variation_visible', 999, 4);

/**
 * Cart validation must respect engraved lines after POST (quote-only SKUs).
 * WC 7+ passes: purchasable, cart item key, cart item values, product.
 *
 * @param array<string, mixed> $values
 */
function justccell_laser_cart_item_is_purchasable($purchasable, $cart_item_key, $values, $product): bool
{
    unset($cart_item_key, $product);
    if (!is_array($values) || empty($values['justccell_laser']['enabled'])) {
        return (bool) $purchasable;
    }
    return true;
}
add_filter('woocommerce_cart_item_is_purchasable', 'justccell_laser_cart_item_is_purchasable', 999, 4);

/**
 * Validate laser add-to-cart with actionable notices.
 */
function justccell_laser_validate_add_to_cart(bool $passed, int $product_id, int $quantity, $variation_id = 0, $cart_item_data = []): bool
{
    unset($cart_item_data);
    if (!$passed || !justccell_laser_request_enabled()) {
        return $passed;
    }

    $product = function_exists('wc_get_product') ? wc_get_product($product_id) : null;
    if (!$product instanceof WC_Product) {
        return $passed;
    }

    if ($product->is_type('variable') && (int) $variation_id < 1) {
        wc_add_notice(
            __('Please choose product options (colour, capacity, etc.) before adding an engraved item.', 'justccell'),
            'error'
        );
        return false;
    }

    $artwork = isset($_POST['justccell_laser_artwork'])
        ? (string) wp_unslash($_POST['justccell_laser_artwork'])
        : '';
    if ($artwork === '') {
        wc_add_notice(
            __('Engraving artwork did not upload. Try again with a simpler design or refresh the page.', 'justccell'),
            'error'
        );
        return false;
    }

    if (!justccell_laser_validate_data_url($artwork)) {
        wc_add_notice(
            __('Engraving artwork was too large or invalid. Simplify the design and try again.', 'justccell'),
            'error'
        );
        return false;
    }

    $whatsapp = justccell_laser_sanitize_whatsapp(
        isset($_POST['justccell_laser_whatsapp'])
            ? (string) wp_unslash($_POST['justccell_laser_whatsapp'])
            : ''
    );
    if (justccell_laser_whatsapp_required() && $whatsapp === '') {
        wc_add_notice(
            __('Please enter a WhatsApp number for artwork proof approval.', 'justccell'),
            'error'
        );
        return false;
    }

    return $passed;
}
add_filter('woocommerce_add_to_cart_validation', 'justccell_laser_validate_add_to_cart', 20, 5);

/**
 * WooCommerce core add-to-cart silently fails for some variable / quote-only SKUs.
 * Handle engraved lines explicitly and redirect to basket.
 */
function justccell_laser_bypass_core_add_to_cart(): void
{
    if (!justccell_laser_request_enabled() || !isset($_POST['add-to-cart'])) {
        return;
    }
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }

    remove_action('wp_loaded', ['WC_Form_Handler', 'add_to_cart_action'], 20);
}
add_action('wp_loaded', 'justccell_laser_bypass_core_add_to_cart', 11);

function justccell_laser_handle_add_to_cart(): void
{
    if (!justccell_laser_request_enabled() || !isset($_POST['add-to-cart'])) {
        return;
    }
    if (isset($_POST['justccell_cart_ajax']) && (string) wp_unslash($_POST['justccell_cart_ajax']) === '1') {
        return;
    }

    justccell_laser_prepare_add_to_cart_request();

    if (!function_exists('justccell_process_add_to_cart')) {
        return;
    }

    $result = justccell_process_add_to_cart();
    if (!$result['success']) {
        wc_add_notice((string) $result['message'], 'error');
        return;
    }

    $product_id = absint(wp_unslash($_POST['add-to-cart']));
    $quantity   = max(1, absint(wp_unslash($_POST['quantity'] ?? 1)));
    wc_add_to_cart_message([$product_id => $quantity], true);
    wp_safe_redirect(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'));
    exit;
}
add_action('wp_loaded', 'justccell_laser_handle_add_to_cart', 20);

/**
 * Server-authoritative line price: base + engraving PPU (§7).
 */
add_action('woocommerce_before_calculate_totals', static function ($cart): void {
    if (!is_object($cart) || !method_exists($cart, 'get_cart')) {
        return;
    }
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }

    foreach ($cart->get_cart() as $item) {
        if (empty($item['justccell_laser']['enabled']) || !isset($item['data']) || !is_object($item['data'])) {
            continue;
        }
        if (!method_exists($item['data'], 'set_price')) {
            continue;
        }

        $product_id = (int) ($item['product_id'] ?? 0);
        $config     = justccell_laser_config($product_id);
        $qty        = max(1, (int) ($item['quantity'] ?? 1));
        $ppu        = $config
            ? justccell_laser_ppu_for_qty($config['tiers'], $qty)
            : (float) ($item['justccell_laser']['unit'] ?? 0);

        $base = (float) $item['data']->get_price();
        if ($base <= 0) {
            $base = isset($item['justccell_laser']['base_price'])
                ? (float) $item['justccell_laser']['base_price']
                : 0.0;
        }
        if ($base <= 0) {
            $meta_price = get_post_meta($product_id, '_price', true);
            $base = is_numeric($meta_price) ? (float) $meta_price : 0.0;
        }

        $setup = (float) ($item['justccell_laser']['setup_fee'] ?? 0);
        if ($setup <= 0 && $config) {
            $setup = (float) $config['setupFee'];
        }
        if ($setup > 0 && $qty > 0) {
            $setup = $setup / $qty;
        }

        $item['data']->set_price(max(0.01, $base + $ppu + $setup));
    }
}, 20);

/**
 * One setup fee per engraved cart line.
 */
add_action('woocommerce_cart_calculate_fees', static function ($cart): void {
    if (!is_object($cart) || !method_exists($cart, 'get_cart') || !method_exists($cart, 'add_fee')) {
        return;
    }
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    // Block cart Store API totals are fragile with extra fees on quote-only SKUs.
    // Setup fee is rolled into the engraved line price server-side for now.
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return;
    }

    $n = 0;
    foreach ($cart->get_cart() as $item) {
        if (empty($item['justccell_laser']['enabled'])) {
            continue;
        }
        $fee = (float) ($item['justccell_laser']['setup_fee'] ?? 0);
        if ($fee <= 0) {
            $pid = (int) ($item['product_id'] ?? 0);
            $cfg = justccell_laser_config($pid);
            $fee = $cfg ? (float) $cfg['setupFee'] : 0.0;
        }
        if ($fee <= 0) {
            continue;
        }
        $n++;
        $label = $n === 1
            ? __('Laser engraving setup', 'justccell')
            : sprintf(__('Laser engraving setup (%d)', 'justccell'), $n);
        $cart->add_fee($label, $fee, false);
    }
}, 20);

/**
 * Cart / checkout item data: preview thumb + text.
 *
 * @param list<array<string, mixed>> $item_data
 * @param array<string, mixed>       $cart_item
 * @return list<array<string, mixed>>
 */
add_filter('woocommerce_get_item_data', static function ($item_data, $cart_item) {
    if (!is_array($item_data)) {
        $item_data = [];
    }
    if (empty($cart_item['justccell_laser']['enabled']) || !is_array($cart_item['justccell_laser'])) {
        return $item_data;
    }

    $laser   = $cart_item['justccell_laser'];
    $preview = (string) ($laser['preview'] ?? '');
    if ($preview !== '') {
        $label = __('Custom engraving', 'justccell');
        $row   = [
            'key'   => __('Engraving artwork', 'justccell'),
            'value' => $label,
        ];
        if (str_starts_with($preview, 'http')) {
            $row['display'] = sprintf(
                '<span class="jc-cart-laser"><img class="jc-cart-laser__img" src="%s" alt="%s" width="64" height="64" loading="lazy"></span>',
                esc_url($preview),
                esc_attr($label)
            );
        } elseif (str_starts_with($preview, 'data:')) {
            $row['value'] = __('Custom engraving attached', 'justccell');
        }
        $item_data[] = $row;
    }

    $text = trim((string) ($laser['text'] ?? ''));
    if ($text !== '') {
        $item_data[] = [
            'key'   => __('Engraving text', 'justccell'),
            'value' => $text,
        ];
    }

    $whatsapp = trim((string) ($laser['whatsapp'] ?? ''));
    if ($whatsapp !== '') {
        $item_data[] = [
            'key'   => __('WhatsApp (proof)', 'justccell'),
            'value' => $whatsapp,
        ];
    }

    $setup = (float) ($laser['setup_fee'] ?? 0);
    if ($setup > 0) {
        $item_data[] = [
            'key'   => __('Engraving setup fee', 'justccell'),
            'value' => function_exists('justccell_format_money')
                ? justccell_format_money($setup)
                : (string) $setup,
        ];
    }

    $item_data[] = [
        'key'   => __('Engraving (per unit)', 'justccell'),
        'value' => function_exists('justccell_format_money')
            ? justccell_format_money(max(0, (float) ($laser['unit'] ?? 0)))
            : (string) ($laser['unit'] ?? '0'),
    ];

    return $item_data;
}, 20, 2);

/**
 * Persist Base64 artwork to uploads/laser-engravings/ — never store Base64 on the order.
 */
function justccell_laser_persist_artwork(string $data_url, int $order_id, string $cart_key): string
{
    if (!justccell_laser_validate_data_url($data_url)) {
        return '';
    }
    $parts = explode(',', $data_url, 2);
    $raw   = base64_decode($parts[1], true);
    if ($raw === false || $raw === '') {
        return '';
    }

    $upload = wp_upload_dir();
    if (!empty($upload['error'])) {
        return '';
    }

    $dir = trailingslashit($upload['basedir']) . JUSTCCELL_LASER_UPLOAD_DIR;
    if (!wp_mkdir_p($dir)) {
        return '';
    }

    $index = $dir . '/index.php';
    if (!file_exists($index)) {
        file_put_contents($index, "<?php\n// Silence is golden.\n");
    }

    $header = strtolower($parts[0]);
    $ext    = (str_contains($header, 'jpeg') || str_contains($header, 'jpg')) ? 'jpg' : 'png';
    $hash   = substr(hash('sha256', $raw), 0, 16);
    $name   = sprintf(
        'order-%d-%s-%s.%s',
        max(0, $order_id),
        sanitize_title(substr($cart_key, 0, 12)) ?: 'item',
        $hash,
        $ext
    );
    $path = $dir . '/' . $name;
    if (file_put_contents($path, $raw) === false) {
        return '';
    }

    return trailingslashit($upload['baseurl']) . JUSTCCELL_LASER_UPLOAD_DIR . '/' . $name;
}

/**
 * Keep laser cart session small: persist artwork files and drop heavy meta before save.
 *
 * @param array<string, mixed> $cart_item
 * @return array<string, mixed>
 */
function justccell_laser_prepare_cart_item_for_session(array $cart_item): array
{
    if (empty($cart_item['justccell_laser']['enabled']) || !is_array($cart_item['justccell_laser'])) {
        return $cart_item;
    }

    $session_key = isset($cart_item['key']) ? (string) $cart_item['key'] : wp_unique_id('laser-');
    $laser       = $cart_item['justccell_laser'];

    foreach (['artwork' => '', 'preview' => '-preview'] as $field => $suffix) {
        $value = (string) ($laser[$field] ?? '');
        if ($value === '' || !str_starts_with($value, 'data:')) {
            continue;
        }
        $url = justccell_laser_persist_artwork($value, 0, $session_key . $suffix);
        if ($url !== '') {
            $laser[$field] = $url;
        }
    }

    unset($laser['safe_zones']);
    $cart_item['justccell_laser'] = $laser;

    return $cart_item;
}

add_filter('woocommerce_add_cart_item', static function ($cart_item) {
    if (!is_array($cart_item)) {
        return $cart_item;
    }
    return justccell_laser_prepare_cart_item_for_session($cart_item);
}, 25);

/**
 * HPOS: order line meta via $item->add_meta_data() only (§6.2).
 * Artwork may already be an HTTPS upload from the cart session — never drop the rest of the meta.
 */
add_action('woocommerce_checkout_create_order_line_item', static function ($item, $cart_item_key, $values): void {
    if (!is_object($item) || !method_exists($item, 'add_meta_data')) {
        return;
    }
    if (empty($values['justccell_laser']['enabled']) || !is_array($values['justccell_laser'])) {
        return;
    }

    $laser    = $values['justccell_laser'];
    $order_id = method_exists($item, 'get_order_id') ? (int) $item->get_order_id() : 0;
    $artwork  = justccell_laser_resolve_file_url(
        (string) ($laser['artwork'] ?? ''),
        $order_id,
        (string) $cart_item_key
    );
    $preview = justccell_laser_resolve_file_url(
        (string) ($laser['preview'] ?? ''),
        $order_id,
        (string) $cart_item_key . '-preview'
    );
    if ($preview === '') {
        $preview = $artwork;
    }

    $ppu  = (float) ($laser['unit'] ?? 0);
    $qty  = method_exists($item, 'get_quantity') ? max(1, (int) $item->get_quantity()) : 1;
    $pid  = (int) ($values['product_id'] ?? 0);
    $cfg  = justccell_laser_config($pid);
    if ($cfg) {
        $ppu = justccell_laser_ppu_for_qty($cfg['tiers'], $qty);
    }
    $setup = (float) ($laser['setup_fee'] ?? 0);
    if ($setup <= 0 && $cfg) {
        $setup = (float) $cfg['setupFee'];
    }
    $text   = trim((string) ($laser['text'] ?? ''));
    $whatsapp = trim((string) ($laser['whatsapp'] ?? ''));
    $layout = is_array($laser['layout'] ?? null) ? $laser['layout'] : [];
    $layout_json = $layout !== [] ? wp_json_encode($layout) : '';

    $item->add_meta_data('_justccell_laser', 'yes', true);
    if ($artwork !== '') {
        $item->add_meta_data('_justccell_laser_artwork_url', $artwork, true);
        $item->add_meta_data(__('Engraving artwork', 'justccell'), $artwork, true);
    }
    if ($preview !== '') {
        $item->add_meta_data('_justccell_laser_preview_url', $preview, true);
    }
    $item->add_meta_data('_justccell_laser_text', $text, true);
    $item->add_meta_data('_justccell_laser_whatsapp', $whatsapp, true);
    $item->add_meta_data('_justccell_laser_unit', (string) $ppu, true);
    $item->add_meta_data('_justccell_laser_setup_fee', (string) $setup, true);
    if ($layout_json !== '' && $layout_json !== false) {
        $item->add_meta_data('_justccell_laser_layout', $layout_json, true);
    }
    $item->add_meta_data(
        '_justccell_laser_safe_zones',
        wp_json_encode($laser['safe_zones'] ?? []),
        true
    );
    if ($text !== '') {
        $item->add_meta_data(__('Engraving text', 'justccell'), $text, true);
    }
    if ($whatsapp !== '') {
        $item->add_meta_data(__('WhatsApp (proof)', 'justccell'), $whatsapp, true);
    }
    if ($setup > 0 && function_exists('justccell_format_money')) {
        $item->add_meta_data(__('Engraving setup fee', 'justccell'), justccell_format_money($setup), true);
    }
    if ($ppu > 0 && function_exists('justccell_format_money')) {
        $item->add_meta_data(__('Engraving (per unit)', 'justccell'), justccell_format_money($ppu), true);
    }
}, 20, 3);

/**
 * Underscore-prefixed laser storage keys — never show on cart, receipts, emails, or account.
 */
function justccell_laser_is_internal_meta_key(string $key): bool
{
    return str_starts_with($key, '_justccell_laser');
}

add_filter('woocommerce_order_item_get_formatted_meta_data', static function ($formatted_meta, $item) {
    unset($item);
    if (!is_array($formatted_meta)) {
        return $formatted_meta;
    }

    return array_values(array_filter($formatted_meta, static function ($meta) {
        if (!is_object($meta) || !isset($meta->key)) {
            return true;
        }

        return !justccell_laser_is_internal_meta_key((string) $meta->key);
    }));
}, 10, 2);

add_filter('woocommerce_get_item_data', static function ($item_data, $cart_item) {
    unset($cart_item);
    if (!is_array($item_data) || $item_data === []) {
        return is_array($item_data) ? $item_data : [];
    }

    return array_values(array_filter($item_data, static function ($row) {
        if (!is_array($row)) {
            return true;
        }
        $key = (string) ($row['key'] ?? $row['name'] ?? '');

        return $key === '' || !justccell_laser_is_internal_meta_key($key);
    }));
}, 999, 2);

add_filter('woocommerce_order_item_display_meta_key', static function ($display_key, $meta, $item) {
    unset($item);
    if (is_object($meta) && isset($meta->key) && justccell_laser_is_internal_meta_key((string) $meta->key)) {
        return '';
    }

    return $display_key;
}, 10, 3);

add_filter('woocommerce_order_item_display_meta_value', static function ($display_value, $meta, $item) {
    unset($item);
    if (is_object($meta) && isset($meta->key) && justccell_laser_is_internal_meta_key((string) $meta->key)) {
        return '';
    }

    return $display_value;
}, 5, 3);

add_filter('woocommerce_hidden_order_itemmeta', static function ($hidden) {
    if (!is_array($hidden)) {
        $hidden = [];
    }
    return array_merge($hidden, [
        '_justccell_laser',
        '_justccell_laser_artwork_url',
        '_justccell_laser_preview_url',
        '_justccell_laser_text',
        '_justccell_laser_whatsapp',
        '_justccell_laser_unit',
        '_justccell_laser_setup_fee',
        '_justccell_laser_layout',
        '_justccell_laser_safe_zones',
    ]);
});

add_filter('woocommerce_order_item_display_meta_value', static function ($display, $meta) {
    if (!is_object($meta)) {
        return $display;
    }
    $key = (string) ($meta->key ?? '');
    $val = (string) ($meta->value ?? '');
    if ($key !== __('Engraving artwork', 'justccell') || preg_match('#^https?://#i', $val) !== 1) {
        return $display;
    }
    return sprintf(
        '<span class="jc-order-meta-art"><a class="jc-order-laser__link" href="%1$s" target="_blank" rel="noopener"><img class="jc-order-meta-art__img jc-order-laser__img" src="%1$s" alt="%2$s" width="48" height="48" loading="lazy"></a></span>',
        esc_url($val),
        esc_attr__('Engraving artwork', 'justccell')
    );
}, 10, 2);

add_action('woocommerce_after_order_itemmeta', static function ($item_id, $item): void {
    if (!is_admin()) {
        return;
    }
    if (!$item instanceof WC_Order_Item_Product) {
        return;
    }
    if ((string) $item->get_meta('_justccell_laser') !== 'yes') {
        return;
    }
    $url  = (string) $item->get_meta('_justccell_laser_preview_url');
    if ($url === '') {
        $url = (string) $item->get_meta('_justccell_laser_artwork_url');
    }
    $text = (string) $item->get_meta('_justccell_laser_text');
    $whatsapp = (string) $item->get_meta('_justccell_laser_whatsapp');
    echo '<div class="jc-order-laser" style="margin:0.5rem 0 0">';
    echo '<p style="margin:0 0 0.35rem"><strong>' . esc_html__('Laser engraving', 'justccell') . '</strong></p>';
    if ($url !== '') {
        echo '<p style="margin:0 0 0.35rem"><img src="' . esc_url($url) . '" alt="" width="96" height="96" style="max-width:96px;height:auto;border:1px solid #dcdcde;background:#fff"></p>';
    }
    if ($text !== '') {
        echo '<p style="margin:0">' . esc_html($text) . '</p>';
    }
    if ($whatsapp !== '') {
        echo '<p style="margin:0.35rem 0 0"><strong>' . esc_html__('WhatsApp (proof)', 'justccell') . ':</strong> ' . esc_html($whatsapp) . '</p>';
    }
    echo '</div>';
}, 10, 2);

add_action('wpo_wcpdf_after_item_meta', static function ($type, $item): void {
    if (!$item instanceof WC_Order_Item) {
        return;
    }
    if ((string) $item->get_meta('_justccell_laser') !== 'yes') {
        return;
    }
    $url   = (string) $item->get_meta('_justccell_laser_artwork_url');
    $text  = (string) $item->get_meta('_justccell_laser_text');
    $whatsapp = (string) $item->get_meta('_justccell_laser_whatsapp');
    $setup = (string) $item->get_meta('_justccell_laser_setup_fee');
    $unit  = (string) $item->get_meta('_justccell_laser_unit');
    echo '<div class="jc-pdf-laser" style="margin-top:4px;font-size:11px">';
    echo '<div><strong>' . esc_html__('Laser engraving', 'justccell') . '</strong></div>';
    if ($text !== '') {
        echo '<div>' . esc_html__('Engraving text', 'justccell') . ': ' . esc_html($text) . '</div>';
    }
    if ($whatsapp !== '') {
        echo '<div>' . esc_html__('WhatsApp (proof)', 'justccell') . ': ' . esc_html($whatsapp) . '</div>';
    }
    if (is_numeric($setup) && (float) $setup > 0 && function_exists('justccell_format_money')) {
        echo '<div>' . esc_html__('Setup fee', 'justccell') . ': ' . esc_html(justccell_format_money((float) $setup)) . '</div>';
    }
    if (is_numeric($unit) && (float) $unit > 0 && function_exists('justccell_format_money')) {
        echo '<div>' . esc_html__('Per unit', 'justccell') . ': ' . esc_html(justccell_format_money((float) $unit)) . '</div>';
    }
    if ($url !== '') {
        echo '<div><img src="' . esc_url($url) . '" width="72" height="72" style="max-width:72px;height:auto" alt=""></div>';
    }
    echo '</div>';
    unset($type);
}, 10, 2);

/**
 * Laser ATC → cart; otherwise leave inquiry redirect from woocommerce.php.
 */
add_filter('woocommerce_add_to_cart_redirect', static function ($url) {
    $laser = isset($_REQUEST['justccell_laser_enabled'])
        && (string) wp_unslash($_REQUEST['justccell_laser_enabled']) === '1';
    if ($laser && function_exists('wc_get_cart_url')) {
        return wc_get_cart_url();
    }
    return $url;
}, 50);

/**
 * Ensure cart scripts load when an engraved line is present.
 */
add_action('wp_enqueue_scripts', static function (): void {
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }
    foreach (WC()->cart->get_cart() as $item) {
        if (!empty($item['justccell_laser']['enabled'])) {
            wp_enqueue_script('woocommerce');
            wp_enqueue_script('wc-cart-fragments');
            break;
        }
    }
}, 100);
