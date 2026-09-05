<?php
/**
 * Shared CMS helpers: heading tags, ACF image IDs, brand page slugs.
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
 * @return array<string, string>
 */
function justccell_heading_tag_choices(): array
{
    return [
        'h1' => 'H1',
        'h2' => 'H2',
        'h3' => 'H3',
        'h4' => 'H4',
        'h5' => 'H5',
        'h6' => 'H6',
    ];
}

function justccell_normalize_heading_tag(string $tag, string $default = 'h2'): string
{
    $tag = strtolower(trim($tag));
    return array_key_exists($tag, justccell_heading_tag_choices()) ? $tag : $default;
}

/**
 * Highlight slide overlay text colour (heading + body share one value).
 *
 * @return array<string, string>
 */
function justccell_highlight_text_color_choices(): array
{
    return [
        'black' => 'Black (default)',
        'white' => 'White (dark photos)',
    ];
}

function justccell_normalize_highlight_text_color(string $value): string
{
    $value = strtolower(trim($value));
    return array_key_exists($value, justccell_highlight_text_color_choices()) ? $value : 'black';
}

/**
 * Nav labels use the menu editor title as-is (no automatic rewrites).
 */
function justccell_sanitize_nav_label(string $title): string
{
    return $title;
}

/**
 * ACF select for highlight slide text colour.
 *
 * @return array<string, mixed>
 */
function justccell_acf_highlight_text_color_field(string $key, string $name, string $label = 'Text colour'): array
{
    return [
        'key'           => $key,
        'label'         => $label,
        'name'          => $name,
        'type'          => 'select',
        'choices'       => justccell_highlight_text_color_choices(),
        'default_value' => 'black',
        'return_format' => 'value',
        'allow_null'    => 0,
        'instructions'  => 'Applies to the heading and paragraph on this slide. Choose White when the photo is dark.',
        'wrapper'       => ['width' => '50'],
    ];
}

/**
 * Retired Product page ACF field names (postmeta may remain on published SKUs).
 * Colours, gallery, tiers, and buy-box toggles live on WooCommerce only.
 *
 * @return list<string>
 */
function justccell_acf_legacy_product_clone_field_names(): array
{
    return [
        'clone_colours',
        'clone_gallery',
        'clone_offers',
        'clone_buy_tiers',
        'clone_buy_enabled',
        'clone_buy_note',
        'clone_banner_heading',
        'clone_banner_heading_tag',
        'clone_banner_tag',
        'clone_tagline',
        'clone_laser_video',
        'clone_show_collection',
        'clone_details',
    ];
}

/**
 * Known retired field_jc_prod_* keys still stored on the Product page group.
 *
 * @return array<string, true>
 */
function justccell_acf_legacy_product_clone_field_keys(): array
{
    $keys = [
        'field_jc_prod_colours',
        'field_jc_prod_gallery',
        'field_jc_prod_offers',
        'field_jc_prod_buy_tiers',
        'field_jc_prod_buy_enabled',
        'field_jc_prod_buy_note',
        'field_jc_prod_banner_heading',
        'field_jc_prod_banner_heading_tag',
        'field_jc_prod_banner_tag',
        'field_jc_prod_tagline',
        'field_jc_prod_laser_video',
        'field_jc_prod_show_collection',
        'field_jc_prod_details',
    ];

    return array_fill_keys($keys, true);
}

/**
 * True only when rendering wp-admin field HTML — never during POST save or ACF/Woo AJAX.
 * Hiding fields via acf/prepare_field during validate/save breaks ACF nonce verification.
 */
function justccell_acf_should_hide_field_in_ui(): bool
{
    if (!is_admin()) {
        return false;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return false;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        return false;
    }
    if (wp_doing_ajax()) {
        return false;
    }

    return true;
}

/**
 * One-time destructive ACF maintenance (field deletes) — never during product save POST.
 */
function justccell_acf_is_safe_maintenance_request(): bool
{
    if (wp_doing_ajax() || wp_doing_cron()) {
        return false;
    }
    if (!is_admin() || !current_user_can('manage_options')) {
        return false;
    }
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return false;
    }
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
        return false;
    }

    return true;
}

/**
 * Re-link ACF field keys to product postmeta after a bad full group import (0.9.224).
 * Postmeta values are never deleted — only field registry rows were overwritten.
 */
function justccell_acf_recover_product_clone_field_refs(): void
{
    if (
        !function_exists('acf_get_field')
        || !function_exists('acf_update_field')
        || !function_exists('justccell_acf_field_group_post_id')
        || !function_exists('justccell_acf_product_clone_field_map')
    ) {
        return;
    }

    $group_id = justccell_acf_field_group_post_id('group_jc_product_clone');
    if ($group_id < 1) {
        return;
    }

    $refs = [];
    $products = get_posts([
        'post_type'              => 'product',
        'post_status'            => 'any',
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ]);
    foreach ($products as $pid) {
        $pid = (int) $pid;
        if ($pid < 1) {
            continue;
        }
        $all = get_post_meta($pid);
        if (!is_array($all)) {
            continue;
        }
        foreach ($all as $meta_key => $vals) {
            if (!is_string($meta_key) || !str_starts_with($meta_key, '_clone_')) {
                continue;
            }
            $field_key = (string) ($vals[0] ?? '');
            if (!str_starts_with($field_key, 'field_')) {
                continue;
            }
            $name = substr($meta_key, 1);
            if ($name !== '') {
                $refs[$field_key] = $name;
            }
        }
    }

    $map = justccell_acf_product_clone_field_map();
    foreach ($map as $field_key => $src) {
        if (!is_array($src) || empty($src['name'])) {
            continue;
        }
        $refs[(string) $field_key] = (string) $src['name'];
    }

    foreach ($refs as $field_key => $name) {
        $src = $map[$field_key] ?? justccell_acf_guess_product_field_def((string) $field_key, (string) $name);
        if (!is_array($src)) {
            continue;
        }
        $existing = acf_get_field($field_key);
        if (is_array($existing) && !empty($existing['ID'])) {
            foreach (['label', 'instructions', 'name', 'type', 'wrapper', 'return_format', 'preview_size', 'rows', 'ui', 'choices', 'default_value', 'conditional_logic', 'layout', 'button_label', 'collapsed', 'message'] as $prop) {
                if (array_key_exists($prop, $src)) {
                    $existing[$prop] = $src[$prop];
                }
            }
            unset($existing['sub_fields']);
            acf_update_field($existing);
            continue;
        }
        $fresh           = $src;
        $fresh['key']    = (string) $field_key;
        $fresh['name']   = (string) $name;
        $fresh['parent'] = $group_id;
        unset($fresh['_ui_order']);
        acf_update_field($fresh);
    }
}

/**
 * @return array<string, mixed>|null
 */
function justccell_acf_guess_product_field_def(string $field_key, string $name): ?array
{
    $defs = [
        'clone_card_tagline'     => ['label' => 'Listing tagline', 'type' => 'text', 'wrapper' => ['width' => '50']],
        'clone_card_capacity'    => ['label' => 'Listing capacity', 'type' => 'text', 'wrapper' => ['width' => '50']],
        'clone_card_image'       => ['label' => 'Card image', 'type' => 'image', 'return_format' => 'id', 'preview_size' => 'thumbnail'],
        'clone_oil_group'        => ['label' => 'Oil group (All-In-Ones mega)', 'type' => 'text', 'wrapper' => ['width' => '50']],
        'clone_mega_featured'    => ['label' => 'Featured in Products mega', 'type' => 'true_false', 'ui' => 1, 'wrapper' => ['width' => '50']],
        'clone_evomax_title_tag' => ['label' => 'Heating heading tag', 'type' => 'select', 'choices' => ['h2' => 'H2', 'h3' => 'H3'], 'default_value' => 'h2', 'wrapper' => ['width' => '50']],
        'clone_j3'               => ['label' => 'Just CCELL 3.0 rail', 'type' => 'true_false', 'ui' => 1],
        'clone_details'          => ['label' => 'Extra detail photos (legacy gallery)', 'type' => 'gallery', 'return_format' => 'array', 'preview_size' => 'thumbnail'],
    ];
    if (isset($defs[$name])) {
        return array_merge(['key' => $field_key, 'name' => $name], $defs[$name]);
    }
    if (str_contains($name, '_image') || $name === 'clone_banner' || $name === 'clone_evomax_bg') {
        return ['key' => $field_key, 'name' => $name, 'label' => ucwords(str_replace('_', ' ', $name)), 'type' => 'image', 'return_format' => 'array', 'preview_size' => 'thumbnail'];
    }
    if (str_contains($name, 'copy') || $name === 'clone_specs') {
        return ['key' => $field_key, 'name' => $name, 'label' => ucwords(str_replace('_', ' ', $name)), 'type' => 'textarea', 'rows' => 3];
    }

    return ['key' => $field_key, 'name' => $name, 'label' => ucwords(str_replace('_', ' ', $name)), 'type' => 'text'];
}

/**
 * Sync Product page field group UI + purge legacy DB fields (version bump only).
 */
function justccell_acf_maintain_product_clone_field_group(string $ui_ver): void
{
    if (
        $ui_ver === ''
        || !function_exists('acf_get_field')
        || !function_exists('acf_update_field')
        || !function_exists('justccell_acf_field_group_post_id')
        || justccell_acf_field_group_post_id('group_jc_product_clone') < 1
        || !function_exists('justccell_acf_product_clone_field_map')
    ) {
        return;
    }

    $group_id = justccell_acf_field_group_post_id('group_jc_product_clone');
    if (function_exists('acf_get_field_group') && function_exists('acf_update_field_group') && function_exists('justccell_acf_product_clone_group')) {
        $clone = acf_get_field_group('group_jc_product_clone');
        $src_g = justccell_acf_product_clone_group();
        if (is_array($clone) && !empty($clone['ID']) && is_array($src_g)) {
            $clone['title']                 = (string) ($src_g['title'] ?? 'Product page');
            $clone['position']              = (string) ($src_g['position'] ?? 'acf_after_title');
            $clone['label_placement']       = (string) ($src_g['label_placement'] ?? 'top');
            $clone['instruction_placement'] = (string) ($src_g['instruction_placement'] ?? 'label');
            $clone['hide_on_screen']        = $src_g['hide_on_screen'] ?? ['the_content'];
            $clone['style']                 = (string) ($src_g['style'] ?? 'default');
            acf_update_field_group($clone);
            $group_id = (int) $clone['ID'];
        }
    }

    $keep = justccell_acf_product_clone_field_map();

    if ($group_id > 0 && function_exists('acf_get_fields') && function_exists('acf_delete_field')) {
        $stored_fields = acf_get_fields($group_id);
        if (is_array($stored_fields)) {
            $legacy_names = justccell_acf_legacy_product_clone_field_names();
            $legacy_keys  = justccell_acf_legacy_product_clone_field_keys();
            $purge        = static function (array $fields) use (&$purge, $keep, $legacy_names, $legacy_keys): void {
                foreach ($fields as $field) {
                    if (!is_array($field) || empty($field['key'])) {
                        continue;
                    }
                    $key  = (string) $field['key'];
                    $name = (string) ($field['name'] ?? '');
                    $sub  = $field['sub_fields'] ?? null;
                    if (is_array($sub) && $sub !== []) {
                        $purge($sub);
                    }
                    $is_legacy = isset($legacy_keys[$key])
                        || ($name !== '' && in_array($name, $legacy_names, true));
                    if (
                        !empty($field['ID'])
                        && (
                            $is_legacy
                            || (str_starts_with($key, 'field_jc_prod_') && !isset($keep[$key]))
                        )
                    ) {
                        acf_delete_field((int) $field['ID']);
                    }
                }
            };
            $purge($stored_fields);
        }
    }

    foreach ($keep as $key => $src) {
        if (!is_array($src) || ($src['type'] ?? '') === '') {
            continue;
        }
        $existing = acf_get_field($key);
        if (is_array($existing) && !empty($existing['ID'])) {
            foreach (['label', 'instructions', 'button_label', 'placeholder', 'wrapper', 'collapsed', 'placement', 'rows', 'message', 'name', 'type'] as $prop) {
                if (array_key_exists($prop, $src)) {
                    $existing[$prop] = $src[$prop];
                } elseif ($prop === 'instructions') {
                    $existing['instructions'] = '';
                }
            }
            if (array_key_exists('_ui_order', $src)) {
                $existing['menu_order'] = (int) $src['_ui_order'];
            }
            if (isset($src['return_format'])) {
                $existing['return_format'] = $src['return_format'];
            }
            if (isset($src['preview_size'])) {
                $existing['preview_size'] = $src['preview_size'];
            }
            if (isset($src['layout'])) {
                $existing['layout'] = $src['layout'];
            }
            unset($existing['sub_fields']);
            acf_update_field($existing);
            continue;
        }
        if ($group_id > 0) {
            $fresh           = $src;
            $fresh['parent'] = $group_id;
            if (array_key_exists('_ui_order', $src)) {
                $fresh['menu_order'] = (int) $src['_ui_order'];
            }
            unset($fresh['_ui_order']);
            acf_update_field($fresh);
        }
    }

    update_option('justccell_acf_product_clone_ui', $ui_ver, false);
}

/**
 * Print a heading. $html true allows a safe subset (br, em, strong) for multi-line titles.
 */
function justccell_echo_heading(string $text, string $tag = 'h2', string $class = '', bool $html = false): void
{
    $text = trim($text);
    if ($text === '') {
        return;
    }
    // ACF often stores multi-line titles with <br> or real newlines.
    if (!$html && (str_contains($text, '<br') || str_contains($text, "\n"))) {
        $html = true;
    }
    $el = justccell_normalize_heading_tag($tag);
    $attr = $class !== '' ? ' class="' . esc_attr($class) . '"' : '';
    if ($html) {
        $text  = preg_replace("/\r\n|\r|\n/", '<br>', $text) ?? $text;
        $inner = wp_kses($text, ['br' => [], 'em' => [], 'strong' => [], 'span' => ['class' => true]]);
    } else {
        $inner = esc_html($text);
    }
    echo '<' . $el . $attr . '>' . $inner . '</' . $el . '>';
}

/**
 * ACF image field → attachment ID.
 *
 * @param mixed $value
 */
function justccell_acf_to_attachment_id($value): int
{
    if (is_numeric($value)) {
        return (int) $value;
    }
    if (is_array($value)) {
        return (int) ($value['ID'] ?? $value['id'] ?? 0);
    }
    return 0;
}

/**
 * Legacy gallery meta `clone_details` → ordered attachment IDs (max 3).
 *
 * @return list<int>
 */
function justccell_legacy_clone_detail_photo_ids(int $post_id): array
{
    if ($post_id < 1) {
        return [];
    }
    $legacy = get_post_meta($post_id, 'clone_details', true);
    if (!is_array($legacy) || $legacy === []) {
        return [];
    }
    $ids = [];
    foreach ($legacy as $img) {
        $id = justccell_acf_to_attachment_id($img);
        if ($id > 0) {
            $ids[] = $id;
        }
        if (count($ids) >= 3) {
            break;
        }
    }

    return $ids;
}

/**
 * Extra detail strip under heating — new single-image fields, legacy gallery fallback.
 *
 * @return list<int>
 */
function justccell_product_detail_photo_ids(int $post_id = 0): array
{
    if ($post_id < 1) {
        $post_id = (int) get_the_ID();
    }
    if ($post_id < 1) {
        return [];
    }

    $ids = [];
    foreach (['clone_detail_1', 'clone_detail_2', 'clone_detail_3'] as $key) {
        $raw = function_exists('get_field') ? get_field($key, $post_id) : get_post_meta($post_id, $key, true);
        $id  = justccell_acf_to_attachment_id($raw);
        if ($id > 0) {
            $ids[] = $id;
        }
    }

    if ($ids !== []) {
        return $ids;
    }

    return justccell_legacy_clone_detail_photo_ids($post_id);
}

/**
 * Show legacy gallery picks in the new image fields until the product is re-saved.
 *
 * @param mixed $value
 * @return mixed
 */
function justccell_acf_load_product_detail_photo($value, $post_id, $field)
{
    if (justccell_acf_to_attachment_id($value) > 0) {
        return $value;
    }
    if (!is_array($field) || $post_id < 1) {
        return $value;
    }
    $name = (string) ($field['name'] ?? '');
    if (!preg_match('/^clone_detail_(\d)$/', $name, $m)) {
        return $value;
    }
    $index  = (int) $m[1] - 1;
    $legacy = justccell_legacy_clone_detail_photo_ids((int) $post_id);

    return $legacy[$index] ?? $value;
}

add_filter('acf/load_value/name=clone_detail_1', 'justccell_acf_load_product_detail_photo', 10, 3);
add_filter('acf/load_value/name=clone_detail_2', 'justccell_acf_load_product_detail_photo', 10, 3);
add_filter('acf/load_value/name=clone_detail_3', 'justccell_acf_load_product_detail_photo', 10, 3);

/**
 * Resolve a media key or attachment ID to an attachment ID.
 */
function justccell_resolve_media_id(string|int $key_or_id): int
{
    if (is_int($key_or_id) || ctype_digit((string) $key_or_id)) {
        $id = (int) $key_or_id;
        return $id > 0 && get_post_type($id) === 'attachment' ? $id : 0;
    }
    $key = (string) $key_or_id;
    if ($key === '') {
        return 0;
    }
    justccell_ensure_media_url($key);
    return justccell_media_id($key);
}

/**
 * @return list<string>
 */
function justccell_legal_page_slugs(): array
{
    return ['privacy-policy', 'terms', 'cookies'];
}

function justccell_is_legal_page_slug(string $slug): bool
{
    return in_array($slug, justccell_legal_page_slugs(), true);
}

/**
 * Marketing clone pages that still use the generic brand template.
 *
 * @return list<string>
 */
function justccell_generic_brand_page_slugs(): array
{
    // Packaging + Elite Terpenes use Coming Soon — not this brand ACF group.
    return [
        'solution',
        'choose-hardware',
        'oil-types',
        '510-thread',
        'laser-engraving',
    ];
}

/**
 * Canonical Location page slug plus the retired plural.
 *
 * @return list<string>
 */
function justccell_location_page_slugs(): array
{
    return ['location', 'locations'];
}

function justccell_is_location_page_slug(string $slug): bool
{
    return in_array($slug, justccell_location_page_slugs(), true);
}

function justccell_find_location_page(): ?WP_Post
{
    if (!function_exists('justccell_find_page_by_slug')) {
        return null;
    }
    foreach (justccell_location_page_slugs() as $slug) {
        $page = justccell_find_page_by_slug($slug);
        if ($page instanceof WP_Post) {
            return $page;
        }
    }
    return null;
}

function justccell_location_page_url(): string
{
    $page = justccell_find_location_page();
    if ($page instanceof WP_Post) {
        $link = get_permalink($page);
        if (is_string($link) && $link !== '') {
            return $link;
        }
    }
    return home_url('/location/');
}

function justccell_brand_page_slugs(): array
{
    return array_merge(
        ['about', 'justccell-3-0', 'ccell-3-0'],
        justccell_location_page_slugs(),
        justccell_why_page_slugs(),
        justccell_generic_brand_page_slugs()
    );
}

function justccell_is_brand_page_slug(string $slug): bool
{
    return in_array($slug, justccell_brand_page_slugs(), true);
}

function justccell_page_hides_block_editor(int $post_id): bool
{
    if ($post_id < 1) {
        return false;
    }
    if (function_exists('justccell_page_layout_kind')) {
        $kind = justccell_page_layout_kind($post_id);
        if ($kind === 'legal' || $kind === 'flexible' || $kind === '') {
            if ($kind === 'legal') {
                return false;
            }
        } else {
            return true;
        }
    }
    if ($post_id === (int) get_option('page_on_front')) {
        return true;
    }
    $slug = (string) get_post_field('post_name', $post_id);
    if ($slug === 'contact' || $slug === 'discover') {
        return true;
    }
    if (justccell_is_legal_page_slug($slug)) {
        return false;
    }
    if (justccell_is_brand_page_slug($slug)) {
        return true;
    }
    if (function_exists('justccell_product_category_labels') && array_key_exists($slug, justccell_product_category_labels())) {
        return true;
    }
    $discover_id = function_exists('justccell_discover_page_id') ? justccell_discover_page_id() : 0;
    return $discover_id > 0 && $post_id === $discover_id;
}

/**
 * @return list<string>
 */
function justccell_why_page_slugs(): array
{
    return ['technology', 'safety', 'research', 'manufacture'];
}

function justccell_is_why_page_slug(string $slug): bool
{
    return in_array($slug, justccell_why_page_slugs(), true);
}

/**
 * Four Why Justccell tabs under the hero (ccell .pro_tab). Packaging / laser stay in the header mega only.
 *
 * @return list<array{title:string,slug:string,url:string}>
 */
function justccell_why_page_tabs(): array
{
    return [
        ['title' => __('All-New Technology', 'justccell'), 'slug' => 'technology', 'url' => home_url('/technology/')],
        ['title' => __('Safety', 'justccell'), 'slug' => 'safety', 'url' => home_url('/safety/')],
        ['title' => __('R&D Capability', 'justccell'), 'slug' => 'research', 'url' => home_url('/research/')],
        ['title' => __('Manufacturing Capability', 'justccell'), 'slug' => 'manufacture', 'url' => home_url('/manufacture/')],
    ];
}

/**
 * @return list<string>
 */
function justccell_listing_page_slugs(): array
{
    return array_keys(justccell_product_category_labels());
}

/**
 * ACF field group location: page is one of these slugs.
 *
 * @param list<string> $slugs
 * @return list<list<array{param:string,operator:string,value:string}>>
 */
function justccell_acf_location_pages(array $slugs): array
{
    $rules = [];
    foreach ($slugs as $slug) {
        $slug = sanitize_title((string) $slug);
        if ($slug === '') {
            continue;
        }
        $rules[] = [
            [
                'param'    => 'justccell_page_slug',
                'operator' => '==',
                'value'    => $slug,
            ],
        ];
    }
    return $rules;
}

/**
 * Shared ACF select field for heading tags.
 *
 * @return array<string, mixed>
 */
function justccell_acf_heading_tag_field(string $key, string $name, string $default = 'h2', string $label = 'Heading tag'): array
{
    return [
        'key'           => $key,
        'label'         => $label,
        'name'          => $name,
        'type'          => 'select',
        'choices'       => justccell_heading_tag_choices(),
        'default_value' => $default,
        'return_format' => 'value',
        'allow_null'    => 0,
        'wrapper'       => ['width' => '20'],
    ];
}

function justccell_acf_page_help(string $what): string
{
    return $what
        . ' Headings: the H1–H6 dropdown sits next to each heading. Keep one H1 per page (usually the hero title).'
        . ' SEO title, meta description, canonical, and social share are the Rank Math box on this screen.'
        . ' Image alt text is edited in Media Library.';
}

/**
 * @return array<string, mixed>
 */
function justccell_acf_image_field(string $key, string $name, string $label, string $width = '50'): array
{
    $field = [
        'key'           => $key,
        'label'         => $label,
        'name'          => $name,
        'type'          => 'image',
        'return_format' => 'id',
        'preview_size'  => 'medium',
        'library'       => 'all',
    ];
    if ($width !== '') {
        $field['wrapper'] = ['width' => $width];
    }
    return $field;
}

/**
 * Print product clone image from attachment ID or media key.
 *
 * @param array<string, mixed> $attrs
 */
function justccell_echo_product_media(int $id, string $key, array $attrs = []): void
{
    if ($id > 0) {
        unset($attrs['size']);
        echo wp_get_attachment_image($id, 'full', false, $attrs);
        return;
    }
    if ($key !== '') {
        echo justccell_media_img($key, $attrs);
    }
}

/**
 * Attachment URL for thumbs / spin JSON.
 */
function justccell_product_media_url(int $id, string $key): string
{
    if ($id > 0) {
        $url = wp_get_attachment_url($id);
        return is_string($url) ? $url : '';
    }
    return $key !== '' ? justccell_ensure_media_url($key) : '';
}

/**
 * True when the product is assigned to this storefront product_cat slug
 * (All-In-Ones, Cartridges, …). Child terms are not treated as a match.
 */
function justccell_product_in_storefront_category(int $product_id, string $category): bool
{
    if ($product_id < 1 || $category === '' || !taxonomy_exists('product_cat')) {
        return false;
    }
    if (function_exists('justccell_product_category_labels')
        && !array_key_exists($category, justccell_product_category_labels())
    ) {
        return false;
    }
    return has_term($category, 'product_cat', $product_id);
}

add_filter('use_block_editor_for_post', static function ($use, $post) {
    if (!$post instanceof WP_Post || $post->post_type !== 'page') {
        return $use;
    }
    if (justccell_page_hides_block_editor((int) $post->ID)) {
        return false;
    }
    return $use;
}, 20, 2);

add_action('load-post.php', static function (): void {
    $id = (int) ($_GET['post'] ?? 0);
    if ($id > 0 && justccell_page_hides_block_editor($id)) {
        remove_post_type_support('page', 'editor');
    }
});
