<?php
/**
 * Wholesale buy box, chat dock, store landings, laser video, collection.
 * ACF Options + per-product fields first; PHP fallbacks until saved.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_order_store(): string
{
    return 'uk';
}

function justccell_order_store_home_url(): string
{
    $store = justccell_order_store();
    $GLOBALS['justccell_skip_home_url'] = true;
    $url = function_exists('justccell_inject_store_prefix')
        ? justccell_inject_store_prefix(home_url('/'), $store)
        : home_url('/');
    $GLOBALS['justccell_skip_home_url'] = false;
    return $url;
}

/**
 * @param mixed $value
 */
function justccell_acf_file_url($value): string
{
    if (is_numeric($value) && (int) $value > 0) {
        $url = wp_get_attachment_url((int) $value);
        return is_string($url) ? $url : '';
    }
    if (is_array($value)) {
        if (isset($value['url']) && is_string($value['url'])) {
            return $value['url'];
        }
        $id = (int) ($value['ID'] ?? $value['id'] ?? 0);
        if ($id > 0) {
            $url = wp_get_attachment_url($id);
            return is_string($url) ? $url : '';
        }
    }
    return '';
}

function justccell_option_string(string $name, string $fallback = ''): string
{
    if (function_exists('get_field')) {
        $value = get_field($name, 'option');
        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }
    }
    return $fallback;
}

function justccell_option_bool(string $name, bool $fallback = false): bool
{
    if (!function_exists('get_field')) {
        return $fallback;
    }
    $value = get_field($name, 'option');
    if ($value === null || $value === '') {
        return $fallback;
    }
    return (bool) $value;
}

/**
 * True when an attribute name/slug is a colour picker.
 */
function justccell_is_colour_attribute_name(string $name): bool
{
    return (bool) preg_match('/colou?r/i', $name);
}

/**
 * True when an attribute name/slug is a combination / kit picker.
 */
function justccell_is_combination_attribute_name(string $name): bool
{
    return (bool) preg_match('/combin|combo|kit/i', $name);
}

/**
 * Stable public key for a Woo attribute (query params + form fields).
 */
function justccell_product_attribute_public_key(string $raw_name): string
{
    $raw = strtolower(trim($raw_name));
    if (str_starts_with($raw, 'pa_')) {
        $raw = substr($raw, 3);
    }
    $key = sanitize_title($raw);
    return $key !== '' ? $key : 'option';
}

/**
 * All selectable WooCommerce attributes on a product (global + custom).
 * One buy-box dropdown per entry — add attributes in Woo and they appear live.
 *
 * @return list<array{key:string,taxonomy:string,label:string,options:list<string>,position:int}>
 */
function justccell_product_buy_attributes(int $product_id): array
{
    if ($product_id < 1 || !function_exists('wc_get_product')) {
        return [];
    }
    $product = wc_get_product($product_id);
    if (!$product instanceof WC_Product) {
        return [];
    }

    $rows = [];
    foreach ($product->get_attributes() as $attr) {
        if (!$attr instanceof WC_Product_Attribute) {
            continue;
        }
        $raw   = (string) $attr->get_name();
        $label = function_exists('wc_attribute_label') ? (string) wc_attribute_label($raw) : $raw;
        if ($label === '') {
            $label = $raw;
        }

        $options = [];
        if ($attr->is_taxonomy()) {
            $terms = wc_get_product_terms($product_id, $raw, ['fields' => 'names']);
            if (is_array($terms)) {
                foreach ($terms as $term) {
                    $term = trim((string) $term);
                    if ($term !== '') {
                        $options[] = $term;
                    }
                }
            }
        } else {
            foreach ($attr->get_options() as $opt) {
                $opt = trim((string) $opt);
                if ($opt !== '') {
                    $options[] = $opt;
                }
            }
        }
        $options = array_values(array_unique($options));
        if ($options === []) {
            continue;
        }

        $rows[] = [
            'key'      => justccell_product_attribute_public_key($raw !== '' ? $raw : $label),
            'taxonomy' => $raw,
            'label'    => $label,
            'options'  => $options,
            'position' => (int) $attr->get_position(),
        ];
    }

    usort(
        $rows,
        static function (array $a, array $b): int {
            if ($a['position'] === $b['position']) {
                return strcmp((string) $a['label'], (string) $b['label']);
            }
            return $a['position'] <=> $b['position'];
        }
    );

    if ($product->is_type('variable') && $product->get_children() !== []) {
        $used_by_tax = [];
        foreach ($product->get_children() as $vid) {
            $variation = wc_get_product((int) $vid);
            if (!$variation instanceof WC_Product_Variation || $variation->get_status() !== 'publish') {
                continue;
            }
            foreach ($variation->get_attributes() as $tax => $val) {
                $val = trim((string) $val);
                if ($val === '' || $val === 'any') {
                    continue;
                }
                $tax = (string) $tax;
                $opt_label = $val;
                if (taxonomy_exists($tax)) {
                    $term = get_term_by('slug', $val, $tax);
                    if ($term instanceof WP_Term) {
                        $opt_label = $term->name;
                    }
                }
                $used_by_tax[$tax][] = $opt_label;
            }
        }
        foreach ($rows as $i => $row) {
            $tax = (string) ($row['taxonomy'] ?? '');
            if ($tax === '' || empty($used_by_tax[$tax])) {
                continue;
            }
            $rows[$i]['options'] = array_values(array_unique($used_by_tax[$tax]));
        }
    }

    return $rows;
}

/**
 * @return list<string>
 */
function justccell_product_colour_options(int $product_id): array
{
    foreach (justccell_product_buy_attributes($product_id) as $row) {
        $tax   = (string) ($row['taxonomy'] ?? '');
        $label = (string) ($row['label'] ?? '');
        if (justccell_is_colour_attribute_name($tax) || justccell_is_colour_attribute_name($label)) {
            return is_array($row['options'] ?? null) ? $row['options'] : [];
        }
    }
    return [];
}

/**
 * @return list<string>
 */
function justccell_product_combination_options(int $product_id): array
{
    foreach (justccell_product_buy_attributes($product_id) as $row) {
        $tax   = (string) ($row['taxonomy'] ?? '');
        $label = (string) ($row['label'] ?? '');
        if (justccell_is_combination_attribute_name($tax) || justccell_is_combination_attribute_name($label)) {
            return is_array($row['options'] ?? null) ? $row['options'] : [];
        }
    }
    return [];
}

/**
 * @return list<array{range:string,price:string,qty_min:int,qty_max:int}>
 */
function justccell_default_kit_tiers(): array
{
    return [
        ['range' => '1-100', 'price' => '£3.60', 'qty_min' => 1, 'qty_max' => 100],
        ['range' => '101-1000', 'price' => '£3.48', 'qty_min' => 101, 'qty_max' => 1000],
        ['range' => '1001-5000', 'price' => '£3.36', 'qty_min' => 1001, 'qty_max' => 5000],
        ['range' => '5001-10000', 'price' => '£3.24', 'qty_min' => 5001, 'qty_max' => 10000],
        ['range' => '10001-20000', 'price' => '£3.12', 'qty_min' => 10001, 'qty_max' => 20000],
    ];
}

/**
 * @return list<array{range:string,price:string,qty_min:int,qty_max:int}>
 */
function justccell_default_battery_tiers(): array
{
    return [
        ['range' => '1-100', 'price' => '£2.77', 'qty_min' => 1, 'qty_max' => 100],
        ['range' => '101-1000', 'price' => '£2.73', 'qty_min' => 101, 'qty_max' => 1000],
        ['range' => '1001-5000', 'price' => '£2.66', 'qty_min' => 1001, 'qty_max' => 5000],
        ['range' => '5001-10000', 'price' => '£2.60', 'qty_min' => 5001, 'qty_max' => 10000],
        ['range' => '10001-20000', 'price' => '£2.57', 'qty_min' => 10001, 'qty_max' => 20000],
    ];
}

/**
 * @return list<array{key:string,label:string,variants:list<string>,tiers:list<array{range:string,price:string,qty_min:int,qty_max:int}>}>
 */
function justccell_default_buy_offers(string $category = ''): array
{
    $kit     = justccell_default_kit_tiers();
    $battery = justccell_default_battery_tiers();

    if ($category === 'cartridge') {
        return [
            ['key' => '0-5ml', 'label' => '0.5ml', 'variants' => [], 'tiers' => $kit],
            ['key' => '1-0ml', 'label' => '1.0ml', 'variants' => [], 'tiers' => $kit],
        ];
    }
    if ($category === 'battery') {
        return [
            ['key' => 'battery-only', 'label' => 'Battery only', 'variants' => [], 'tiers' => $battery],
            ['key' => 'with-pod', 'label' => 'Battery with pod', 'variants' => [], 'tiers' => $kit],
        ];
    }

    return [
        ['key' => 'pod-battery', 'label' => 'Pod and battery', 'variants' => [], 'tiers' => $kit],
        ['key' => 'pod-only', 'label' => 'Pod only', 'variants' => [], 'tiers' => $kit],
        ['key' => 'battery-only', 'label' => 'Battery only', 'variants' => [], 'tiers' => $battery],
    ];
}

/**
 * @param mixed $raw
 * @return list<string>
 */
function justccell_parse_variant_lines($raw): array
{
    if (is_array($raw)) {
        $out = [];
        foreach ($raw as $line) {
            $line = trim((string) $line);
            if ($line !== '') {
                $out[] = $line;
            }
        }
        return $out;
    }
    $text = is_string($raw) ? $raw : '';
    if ($text === '') {
        return [];
    }
    $lines = preg_split('/\r\n|\r|\n/', $text) ?: [];
    $out   = [];
    foreach ($lines as $line) {
        $line = trim((string) $line);
        if ($line !== '') {
            $out[] = $line;
        }
    }
    return $out;
}

/**
 * @param mixed $rows
 * @return list<array{range:string,price:string,qty_min:int,qty_max:int}>
 */
function justccell_normalize_tiers($rows): array
{
    $out = [];
    if (!is_array($rows)) {
        return $out;
    }
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $range = trim((string) ($row['range'] ?? ''));
        $price = trim((string) ($row['price'] ?? ''));
        if ($range === '' && $price === '') {
            continue;
        }
        $out[] = [
            'range'   => $range !== '' ? $range : (string) ($row['qty_label'] ?? ''),
            'price'   => $price,
            'qty_min' => (int) ($row['qty_min'] ?? 0),
            'qty_max' => (int) ($row['qty_max'] ?? 0),
        ];
    }
    return $out;
}

/**
 * @param mixed $rows
 * @return list<array{key:string,label:string,variants:list<string>,tiers:list<array{range:string,price:string,qty_min:int,qty_max:int}>}>
 */
function justccell_normalize_buy_offers($rows): array
{
    $out = [];
    if (!is_array($rows)) {
        return $out;
    }
    foreach ($rows as $i => $row) {
        if (!is_array($row)) {
            continue;
        }
        $label = trim((string) ($row['label'] ?? ''));
        if ($label === '') {
            continue;
        }
        $key = sanitize_title((string) ($row['key'] ?? $label));
        if ($key === '') {
            $key = 'offer-' . (string) $i;
        }
        $out[] = [
            'key'      => $key,
            'label'    => $label,
            'variants' => justccell_parse_variant_lines($row['variants'] ?? ''),
            'tiers'    => justccell_normalize_tiers($row['tiers'] ?? []),
        ];
    }
    return $out;
}

function justccell_buy_box_category(string $slug, int $product_id): string
{
    if ($product_id > 0 && taxonomy_exists('product_cat')) {
        $terms = wp_get_post_terms($product_id, 'product_cat', ['fields' => 'slugs']);
        if (is_array($terms)) {
            foreach (['pod-system', 'all-in-ones', 'cartridge', 'battery'] as $want) {
                if (in_array($want, $terms, true)) {
                    return $want;
                }
            }
        }
    }
    if ($slug !== '' && function_exists('justccell_catalog_item')) {
        $item = justccell_catalog_item($slug);
        if (is_array($item) && isset($item['category']) && is_string($item['category'])) {
            return $item['category'];
        }
    }
    return '';
}

/**
 * Buy box: every WooCommerce product attribute becomes a dropdown.
 *
 * @return array{
 *   enabled:bool,
 *   qty_label:string,
 *   price_label:string,
 *   note:string,
 *   cta_label:string,
 *   attributes:list<array{key:string,label:string,options:list<string>}>,
 *   tiers:list<array{range:string,price:string,qty_min:int,qty_max:int}>,
 *   tier_overrides:array<string,array<string,list<array{range:string,price:string,qty_min:int,qty_max:int>}>>,
 *   offers:list<array{key:string,label:string,variants:list<string>,tiers:list<array{range:string,price:string,qty_min:int,qty_max:int}>}>
 * }
 */
function justccell_product_buy_box(string $slug, int $product_id = 0): array
{
    $enabled = true;
    $qty     = __('Quantity', 'justccell');
    $price   = __('Per Item Price', 'justccell');
    $note    = __('Prices exclude VAT. Add to cart to build your order — checkout opens when VAT and payments are ready.', 'justccell');
    $cta     = __('Add to cart', 'justccell');

    $acf_tiers = [];
    if ($product_id > 0 && function_exists('get_field')) {
        $raw = get_post_meta($product_id, 'clone_buy_enabled', true);
        if ($raw === '0' || $raw === 0) {
            $enabled = false;
        }
        $custom_note = (string) get_field('clone_buy_note', $product_id);
        if ($custom_note !== '') {
            $note = $custom_note;
        }
    }

    $attributes = [];
    foreach (justccell_product_buy_attributes($product_id) as $row) {
        $attributes[] = [
            'key'     => (string) $row['key'],
            'label'   => (string) $row['label'],
            'options' => is_array($row['options'] ?? null) ? $row['options'] : [],
        ];
    }

    $tiers = function_exists('justccell_tiered_pricing_display_rows')
        ? justccell_tiered_pricing_display_rows($product_id)
        : [];

    $variation_tiers = [];
    if ($product_id > 0 && function_exists('wc_get_product') && function_exists('justccell_tiered_pricing_display_rows')) {
        $wc_product = wc_get_product($product_id);
        if ($wc_product instanceof WC_Product && $wc_product->is_type('variable')) {
            foreach ($wc_product->get_children() as $child_id) {
                $child_id = (int) $child_id;
                $child_rows = justccell_tiered_pricing_display_rows($child_id);
                if ($child_rows !== []) {
                    $variation_tiers[(string) $child_id] = $child_rows;
                }
            }
        }
    }

    $offers = [[
        'key'      => 'default',
        'label'    => '',
        'variants' => [],
        'tiers'    => $tiers,
    ]];

    unset($slug);

    return [
        'enabled'          => $enabled,
        'qty_label'        => $qty,
        'price_label'      => $price,
        'note'             => $note,
        'cta_label'        => $cta,
        'attributes'       => $attributes,
        'tiers'            => $tiers,
        'variation_tiers'  => $variation_tiers,
        'tier_overrides'   => [],
        'offers'           => $offers,
    ];
}

function justccell_laser_video_url(int $product_id = 0): string
{
    if ($product_id > 0 && function_exists('get_field')) {
        $url = justccell_acf_file_url(get_field('clone_laser_video', $product_id));
        if ($url !== '') {
            return $url;
        }
    }
    if (function_exists('get_field')) {
        $url = justccell_acf_file_url(get_field('store_laser_video', 'option'));
        if ($url !== '') {
            return $url;
        }
    }
    $id = function_exists('justccell_media_id') ? justccell_media_id('laser-engraving.mp4') : 0;
    if ($id > 0) {
        $url = wp_get_attachment_url($id);
        if (is_string($url) && $url !== '') {
            return $url;
        }
    }
    if (function_exists('justccell_ensure_media_url')) {
        $sideloaded = justccell_ensure_media_url('laser-engraving.mp4');
        if ($sideloaded !== '') {
            return $sideloaded;
        }
    }
    return '';
}

/**
 * @return array{show:bool,heading:string,copy:string,cta_label:string,cta_url:string,video:string}
 */
function justccell_product_laser_offer(string $slug, int $product_id = 0): array
{
    $show = justccell_option_bool('store_laser_on_products', true);
    if ($product_id > 0) {
        // Explicit ACF switch: empty meta = on (default); 0/false = off; 1/true = on.
        if (function_exists('get_field')) {
            $acf_show = get_field('clone_show_laser', $product_id);
            if ($acf_show === false || $acf_show === 0 || $acf_show === '0') {
                $show = false;
            } elseif ($acf_show === true || $acf_show === 1 || $acf_show === '1') {
                $show = true;
            }
        } else {
            $raw = get_post_meta($product_id, 'clone_show_laser', true);
            if ($raw === '0' || $raw === 0 || $raw === false) {
                $show = false;
            }
        }
    }

    $heading = justccell_option_string('store_laser_heading', __('OEM laser engraving', 'justccell'));
    $heading_tag = justccell_option_string('store_laser_heading_tag', 'h2');
    if ($heading_tag === '') {
        $heading_tag = 'h2';
    }
    $copy = justccell_option_string(
        'store_laser_copy',
        __('Mouthpiece: injection molding. Battery case: injection molding, silkscreen printing, pad printing, digital printing, laser engraving, spray painting. Bottom cap: injection molding, silkscreen printing, laser engraving, spray painting.', 'justccell')
    );
    $cta = justccell_option_string('store_laser_cta_label', __('See laser engraving', 'justccell'));
    $url = justccell_option_string('store_laser_cta_url', home_url('/laser-engraving/'));

    if ($product_id > 0 && function_exists('get_field')) {
        $h = trim((string) get_field('clone_laser_heading', $product_id));
        if ($h !== '') {
            $heading = $h;
        }
        $c = trim((string) get_field('clone_laser_copy', $product_id));
        if ($c !== '') {
            $copy = $c;
        }
    }

    unset($slug);

    $video = justccell_laser_video_url($product_id);

    return [
        'show'        => $show,
        'heading'     => $heading,
        'heading_tag' => $heading_tag,
        'copy'        => $copy,
        'cta_label'   => $cta,
        'cta_url'     => $url !== '' ? $url : home_url('/laser-engraving/'),
        'video'       => $video,
    ];
}

function justccell_collection_copy(): string
{
    return justccell_option_string(
        'store_collection_copy',
        __('Collection from our UK warehouse is available. Mention collection on your enquiry and we will confirm a slot.', 'justccell')
    );
}

function justccell_collection_enabled(): bool
{
    return justccell_option_bool('store_collection_enabled', true);
}

/**
 * @return array{show:bool,copy:string}
 */
function justccell_product_collection(int $product_id = 0): array
{
    $show = justccell_collection_enabled();
    if ($product_id > 0) {
        $raw = get_post_meta($product_id, 'clone_show_collection', true);
        if ($raw === '0' || $raw === 0) {
            $show = false;
        }
    }
    $copy = justccell_collection_copy();
    return [
        'show' => $show && $copy !== '',
        'copy' => $copy,
    ];
}

function justccell_social_option_url(string $key): string
{
    $map = [
        'instagram' => 'store_instagram',
        'whatsapp'  => 'store_whatsapp',
        'telegram'  => 'store_telegram',
        'youtube'   => 'store_youtube',
        'linkedin'  => 'store_linkedin',
        'facebook'  => 'store_facebook',
        'x'         => 'store_x',
    ];
    $field = $map[$key] ?? '';
    if ($field === '') {
        return '';
    }
    return esc_url_raw(justccell_option_string($field, ''));
}

function justccell_public_phone(): string
{
    return '+447495338694';
}

function justccell_chat_phone_digits(): string
{
    return '447495338694';
}

function justccell_default_whatsapp_url(): string
{
    return 'https://wa.me/' . justccell_chat_phone_digits();
}

function justccell_default_telegram_url(): string
{
    return 'https://t.me/+' . justccell_chat_phone_digits();
}

function justccell_chat_fallback_url(string $via): string
{
    return home_url('/contact/?via=' . rawurlencode($via));
}

function justccell_whatsapp_url(): string
{
    $url = justccell_social_option_url('whatsapp');
    if ($url !== '') {
        return $url;
    }
    $phone = preg_replace('/\D+/', '', (string) get_theme_mod('justccell_contact_phone', justccell_public_phone())) ?? '';
    if (strlen($phone) >= 8) {
        return 'https://wa.me/' . $phone;
    }
    return justccell_default_whatsapp_url();
}

function justccell_telegram_url(): string
{
    $url = justccell_social_option_url('telegram');
    return $url !== '' ? $url : justccell_default_telegram_url();
}

/**
 * @return list<array{network:string,url:string,label:string}>
 */
function justccell_chat_dock_links(): array
{
    return [
        [
            'network' => 'whatsapp',
            'url'     => justccell_whatsapp_url(),
            'label'   => justccell_option_string('store_whatsapp_label', __('WhatsApp', 'justccell')),
        ],
        [
            'network' => 'telegram',
            'url'     => justccell_telegram_url(),
            'label'   => justccell_option_string('store_telegram_label', __('Telegram', 'justccell')),
        ],
    ];
}

/**
 * @return array<string, array{enabled:bool,kicker:string,title:string,lede:string,cta_label:string,cta_url:string}>
 */
function justccell_default_store_landings(): array
{
    $uk = justccell_order_store_home_url();
    return [
        'es' => [
            'enabled'   => true,
            'kicker'    => __('Spain', 'justccell'),
            'title'     => __('Justccell Spain', 'justccell'),
            'lede'      => __('Hardware for licensed extract businesses in Spain and the EU. Browse and request wholesale from the UK catalogue — the order site for Justccell.', 'justccell'),
            'cta_label' => __('Open the UK catalogue', 'justccell'),
            'cta_url'   => $uk,
        ],
        'ch' => [
            'enabled'   => true,
            'kicker'    => __('Switzerland', 'justccell'),
            'title'     => __('Justccell Switzerland', 'justccell'),
            'lede'      => __('Swiss landing for Justccell hardware. Orders and wholesale quotes run through the UK justccell.com catalogue.', 'justccell'),
            'cta_label' => __('Open the UK catalogue', 'justccell'),
            'cta_url'   => $uk,
        ],
    ];
}

/**
 * @return array{enabled:bool,kicker:string,title:string,lede:string,cta_label:string,cta_url:string,image_id:int}|null
 */
function justccell_current_store_landing(): ?array
{
    $store = function_exists('justccell_current_store') ? justccell_current_store() : '';
    if ($store === '' || $store === justccell_order_store()) {
        return null;
    }

    $fallback = justccell_default_store_landings()[$store] ?? null;
    $row      = null;

    if (function_exists('get_field')) {
        foreach ((array) get_field('store_landings', 'option') as $item) {
            if (!is_array($item) || (string) ($item['store'] ?? '') !== $store) {
                continue;
            }
            $row = [
                'enabled'          => (bool) ($item['enabled'] ?? false),
                'kicker'           => (string) ($item['kicker'] ?? ''),
                'title'            => (string) ($item['title'] ?? ''),
                'title_tag'        => (string) ($item['title_tag'] ?? 'h1'),
                'lede'             => (string) ($item['lede'] ?? ''),
                'note_heading'     => (string) ($item['note_heading'] ?? ''),
                'note_heading_tag' => (string) ($item['note_heading_tag'] ?? 'h2'),
                'note_copy'        => (string) ($item['note_copy'] ?? ''),
                'cta_label'        => (string) ($item['cta_label'] ?? ''),
                'cta_url'          => (string) ($item['cta_url'] ?? ''),
                'image_id'         => justccell_acf_to_attachment_id($item['image'] ?? 0),
            ];
            break;
        }
    }

    if (is_array($row)) {
        if (empty($row['enabled'])) {
            return null;
        }
        if ($row['title'] === '' && is_array($fallback)) {
            $row['title'] = $fallback['title'];
            $row['lede'] = $fallback['lede'];
            $row['kicker'] = $fallback['kicker'];
            $row['cta_label'] = $fallback['cta_label'];
            $row['cta_url'] = $fallback['cta_url'];
        }
        if ($row['cta_url'] === '') {
            $row['cta_url'] = justccell_order_store_home_url();
        }
        if (($row['title_tag'] ?? '') === '') {
            $row['title_tag'] = 'h1';
        }
        if (($row['note_heading'] ?? '') === '') {
            $row['note_heading'] = __('Orders run through the UK site', 'justccell');
        }
        if (($row['note_heading_tag'] ?? '') === '') {
            $row['note_heading_tag'] = 'h2';
        }
        if (($row['note_copy'] ?? '') === '') {
            $row['note_copy'] = __('justccell.com is the catalogue where customers request wholesale. This page is the Spain or Switzerland landing — edit it under Justccell → Storefront.', 'justccell');
        }
        return $row;
    }

    if (!is_array($fallback) || empty($fallback['enabled'])) {
        return null;
    }

    return [
        'enabled'          => true,
        'kicker'           => $fallback['kicker'],
        'title'            => $fallback['title'],
        'title_tag'        => 'h1',
        'lede'             => $fallback['lede'],
        'note_heading'     => __('Orders run through the UK site', 'justccell'),
        'note_heading_tag' => 'h2',
        'note_copy'        => __('justccell.com is the catalogue where customers request wholesale. This page is the Spain or Switzerland landing — edit it under Justccell → Storefront.', 'justccell'),
        'cta_label'        => $fallback['cta_label'],
        'cta_url'          => $fallback['cta_url'],
        'image_id'         => 0,
    ];
}
