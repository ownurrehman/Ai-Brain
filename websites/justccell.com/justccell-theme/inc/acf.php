<?php
/**
 * ACF JSON sync + flexible section renderer.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_filter('acf/settings/save_json', static function (): string {
    return JUSTCCELL_DIR . '/acf-json';
});

add_filter('acf/settings/load_json', static function (array $paths): array {
    $paths[] = JUSTCCELL_DIR . '/acf-json';
    return $paths;
});

add_filter('acf/location/rule_types', static function ($choices) {
    if (!is_array($choices)) {
        $choices = [];
    }
    $choices['Justccell']['justccell_page_slug'] = __('Page slug', 'justccell');
    return $choices;
});

add_filter('acf/location/rule_values/justccell_page_slug', static function ($choices) {
    if (!is_array($choices)) {
        $choices = [];
    }
    foreach (justccell_acf_managed_page_slugs() as $slug) {
        $choices[$slug] = $slug;
    }
    return $choices;
});

add_filter('acf/location/rule_match/justccell_page_slug', static function ($result, $rule, $screen) {
    $post_id = isset($screen['post_id']) ? (int) $screen['post_id'] : 0;
    if ($post_id < 1) {
        return false;
    }
    $slug = (string) get_post_field('post_name', $post_id);
    $expected = (string) ($rule['value'] ?? '');
    $equals = function_exists('justccell_page_layout_matches_slug')
        ? justccell_page_layout_matches_slug($post_id, $expected)
        : ($slug !== '' && $slug === $expected);
    $operator = (string) ($rule['operator'] ?? '==');
    return $operator === '!=' ? !$equals : $equals;
}, 10, 3);

/**
 * Slugs shown in the ACF “Page slug” location dropdown.
 *
 * @return list<string>
 */
function justccell_acf_managed_page_slugs(): array
{
    $slugs = ['home', 'contact', 'about', 'discover', 'justccell-3-0'];
    if (function_exists('justccell_location_page_slugs')) {
        $slugs = array_merge($slugs, justccell_location_page_slugs());
    }
    if (function_exists('justccell_why_page_slugs')) {
        $slugs = array_merge($slugs, justccell_why_page_slugs());
    }
    if (function_exists('justccell_generic_brand_page_slugs')) {
        $slugs = array_merge($slugs, justccell_generic_brand_page_slugs());
    }
    if (function_exists('justccell_legal_page_slugs')) {
        $slugs = array_merge($slugs, justccell_legal_page_slugs());
    }
    if (function_exists('justccell_listing_page_slugs')) {
        $slugs = array_merge($slugs, justccell_listing_page_slugs());
    }
    $slugs = array_values(array_unique(array_filter($slugs, static fn ($slug): bool => is_string($slug) && $slug !== '')));
    sort($slugs);
    return $slugs;
}

function justccell_acf_field_group_post_id(string $key): int
{
    if ($key === '' || !post_type_exists('acf-field-group')) {
        return 0;
    }
    $found = get_posts([
        'name'                   => $key,
        'post_type'              => 'acf-field-group',
        'post_status'            => ['publish', 'acf-disabled', 'draft'],
        'posts_per_page'         => 1,
        'fields'                 => 'ids',
        'suppress_filters'       => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);
    return isset($found[0]) ? (int) $found[0] : 0;
}

/**
 * @param int|string $parent Field group post ID or parent field key.
 * @return array<string, true>
 */
function justccell_acf_existing_field_keys($parent): array
{
    $keys = [];
    if (!function_exists('acf_get_fields')) {
        return $keys;
    }
    $fields = acf_get_fields($parent);
    if (!is_array($fields)) {
        return $keys;
    }
    foreach ($fields as $field) {
        if (is_array($field) && !empty($field['key'])) {
            $keys[(string) $field['key']] = true;
        }
    }
    return $keys;
}

/**
 * Add PHP-defined fields that are missing from a DB group. Never overwrites existing fields.
 *
 * @param list<array<string, mixed>> $fields
 * @param int|string $parent
 */
function justccell_acf_ensure_missing_fields(array $fields, $parent): void
{
    if (!function_exists('acf_update_field')) {
        return;
    }
    $existing = justccell_acf_existing_field_keys($parent);
    foreach (array_values($fields) as $index => $field) {
        if (!is_array($field) || empty($field['key'])) {
            continue;
        }
        $key = (string) $field['key'];
        $sub = is_array($field['sub_fields'] ?? null) ? $field['sub_fields'] : [];
        if (empty($existing[$key])) {
            $insert = $field;
            $insert['parent'] = $parent;
            $insert['menu_order'] = $index;
            acf_update_field($insert);
            continue;
        }
        if ($sub !== []) {
            justccell_acf_ensure_missing_fields($sub, $key);
        }
    }
}

/**
 * Register a group in PHP until it exists in ACF → Field Groups.
 * After that, only missing field keys are merged in so the UI stays editable.
 *
 * @param array<string, mixed> $group
 */
function justccell_acf_register_field_group(array $group): void
{
    if (wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }
    $key = (string) ($group['key'] ?? '');
    if ($key !== '' && justccell_acf_field_group_post_id($key) > 0) {
        return;
    }
    acf_add_local_field_group($group);
}

/**
 * Flatten product-clone field defs by key for live UI patches.
 *
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_product_clone_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = [];
    if (!function_exists('justccell_acf_product_clone_group')) {
        return $map;
    }
    $walk = static function (array $fields) use (&$walk, &$map): void {
        foreach ($fields as $index => $field) {
            if (!is_array($field) || empty($field['key'])) {
                continue;
            }
            $field['_ui_order'] = (int) $index;
            $map[(string) $field['key']] = $field;
            $sub = $field['sub_fields'] ?? null;
            if (is_array($sub) && $sub !== []) {
                $walk($sub);
            }
        }
    };
    $walk(justccell_acf_product_clone_group()['fields'] ?? []);
    return $map;
}

add_filter('acf/load_field_group', static function ($group) {
    if (!is_array($group) || (string) ($group['key'] ?? '') !== 'group_jc_product_clone') {
        return $group;
    }
    $group['title']           = 'Product page';
    $group['position']        = 'acf_after_title';
    $group['style']           = 'default';
    $group['label_placement'] = 'top';
    $group['active']          = 1;
    $group['location']        = [[
        [
            'param'    => 'post_type',
            'operator' => '==',
            'value'    => 'product',
        ],
    ]];
    // Product description stays on the edit screen for SEO body copy (H2/H3/lists).
    $group['hide_on_screen'] = [];
    return $group;
});

add_filter('acf/load_field', static function ($field) {
    if (!is_array($field)) {
        return $field;
    }
    $key = (string) ($field['key'] ?? '');
    if ($key === '' || !str_starts_with($key, 'field_jc_prod_')) {
        return $field;
    }
    $src = justccell_acf_product_clone_field_map()[$key] ?? null;
    if (!is_array($src)) {
        return $field;
    }
    foreach (['label', 'instructions', 'button_label', 'placeholder', 'wrapper', 'collapsed', 'placement', 'rows', 'message'] as $prop) {
        if (array_key_exists($prop, $src)) {
            $field[$prop] = $src[$prop];
        } elseif ($prop === 'instructions') {
            $field['instructions'] = '';
        }
    }
    if (array_key_exists('_ui_order', $src)) {
        $field['menu_order'] = (int) $src['_ui_order'];
    }
    return $field;
});

// Drop removed Product page fields from the editor (data stays in postmeta). UI render only.
add_filter('acf/prepare_field', static function ($field) {
    if (!is_array($field) || !justccell_acf_should_hide_field_in_ui()) {
        return $field;
    }
    $key = (string) ($field['key'] ?? '');
    if ($key === '' || !str_starts_with($key, 'field_jc_prod_')) {
        return $field;
    }
    if (!isset(justccell_acf_product_clone_field_map()[$key])) {
        return false;
    }
    return $field;
});

add_filter('acf/prepare_field/key=field_jc_j3_products', static function ($field) {
    return justccell_acf_should_hide_field_in_ui() ? false : $field;
});
add_filter('acf/prepare_field/key=field_jc_contact_crumb_home', static function ($field) {
    return justccell_acf_should_hide_field_in_ui() ? false : $field;
});

// Hide every Justccell “How to edit” / *_help message blurb site-wide.
add_filter('acf/prepare_field', static function ($field) {
    if (!is_array($field) || !justccell_acf_should_hide_field_in_ui()) {
        return $field;
    }
    $key   = (string) ($field['key'] ?? '');
    $type  = (string) ($field['type'] ?? '');
    $label = strtolower(trim((string) ($field['label'] ?? '')));
    if ($type !== 'message') {
        return $field;
    }
    if (
        str_ends_with($key, '_help')
        || str_contains($key, '_help')
        || $label === 'how to edit'
    ) {
        return false;
    }
    return $field;
}, 5);

add_filter('acf/prepare_field', static function ($field) {
    if (!is_array($field) || !justccell_acf_should_hide_field_in_ui()) {
        return $field;
    }
    $name = (string) ($field['name'] ?? '');
    if (
        function_exists('justccell_acf_legacy_product_clone_field_names')
        && in_array($name, justccell_acf_legacy_product_clone_field_names(), true)
    ) {
        return false;
    }
    return $field;
}, 6);

foreach (
    array_keys(
        function_exists('justccell_acf_legacy_product_clone_field_keys')
            ? justccell_acf_legacy_product_clone_field_keys()
            : []
    ) as $hidden_key
) {
    add_filter('acf/prepare_field/key=' . $hidden_key, static function ($field) {
        return justccell_acf_should_hide_field_in_ui() ? false : $field;
    });
}

foreach (
    [
        'field_jc_home_trusted_image',
        'field_jc_gbrand_image_mobile',
        'field_jc_why_tabs',
        'field_jc_loc_kicker',
        'field_jc_loc_cta_title',
        'field_jc_loc_cta_tag',
        'field_jc_loc_cta_copy',
        'field_jc_loc_item_soon',
    ] as $hidden_key
) {
    add_filter('acf/prepare_field/key=' . $hidden_key, static function ($field) {
        return justccell_acf_should_hide_field_in_ui() ? false : $field;
    });
}

add_action('admin_init', static function (): void {
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }
    $ui_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '';
    if ($ui_ver !== '' && get_option('justccell_acf_product_json_revert') !== $ui_ver) {
        delete_option('justccell_acf_product_clone_ui');
        if (function_exists('justccell_acf_recover_product_clone_field_refs')) {
            justccell_acf_recover_product_clone_field_refs();
        }
        update_option('justccell_acf_product_json_revert', $ui_ver, false);
    }
    if ($ui_ver === '' || get_option('justccell_acf_product_clone_ui') === $ui_ver) {
        return;
    }
    if (function_exists('justccell_acf_maintain_product_clone_field_group')) {
        justccell_acf_maintain_product_clone_field_group($ui_ver);
    }
}, 20);

add_filter('acf/load_field_group', static function ($group) {
    if (!is_array($group)) {
        return $group;
    }
    $map = [
        'group_jc_home_full'     => 'justccell_acf_home_page_group',
        'group_jc_listing_page'  => 'justccell_acf_listing_page_group',
        'group_jc_generic_brand' => 'justccell_acf_generic_brand_page_group',
        'group_jc_about_page'    => 'justccell_acf_about_page_group',
        'group_jc_why_pages'     => 'justccell_acf_why_page_group',
        'group_jc_contact_page'  => 'justccell_acf_contact_page_group',
    ];
    $key = (string) ($group['key'] ?? '');
    if ($key === 'group_jc_j3_page') {
        $src = function_exists('justccell_acf_j3_page_group') ? justccell_acf_j3_page_group() : [];
        if ($src === []) {
            return $group;
        }
        $group['title']           = (string) ($src['title'] ?? $group['title']);
        $group['position']        = (string) ($src['position'] ?? 'acf_after_title');
        $group['style']           = (string) ($src['style'] ?? 'default');
        $group['label_placement'] = (string) ($src['label_placement'] ?? 'top');
        $group['hide_on_screen']  = $src['hide_on_screen'] ?? ['the_content'];
        $group['active']          = 1;
        if (!empty($src['location'])) {
            $group['location'] = $src['location'];
        }
        return $group;
    }
    $fn = $map[$key] ?? null;
    if ($fn === null || !function_exists($fn)) {
        return $group;
    }
    $src = $fn();
    if ($src === []) {
        return $group;
    }
    $group['title']           = (string) ($src['title'] ?? $group['title']);
    $group['position']        = (string) ($src['position'] ?? 'acf_after_title');
    $group['style']           = (string) ($src['style'] ?? 'default');
    $group['label_placement'] = (string) ($src['label_placement'] ?? 'top');
    $group['hide_on_screen']  = $src['hide_on_screen'] ?? ['the_content'];
    $group['active']          = 1;
    if (!empty($src['location'])) {
        $group['location'] = $src['location'];
    }
    return $group;
});

add_filter('acf/load_field', static function ($field) {
    if (!is_array($field)) {
        return $field;
    }
    $key = (string) ($field['key'] ?? '');
    if ($key === '') {
        return $field;
    }

    $prefix_map = [
        'field_jc_j3_'      => 'justccell_acf_j3_page_field_map',
        'field_jc_home_'    => 'justccell_acf_home_page_field_map',
        'field_jc_list_'    => 'justccell_acf_listing_page_field_map',
        'field_jc_listing_' => 'justccell_acf_listing_page_field_map',
        'field_jc_gbrand_'  => 'justccell_acf_generic_brand_page_field_map',
        'field_jc_about_'   => 'justccell_acf_about_page_field_map',
        'field_jc_why_'     => 'justccell_acf_why_page_field_map',
        'field_jc_contact_' => 'justccell_acf_contact_page_field_map',
    ];

    $map_fn = null;
    foreach ($prefix_map as $prefix => $fn) {
        if (str_starts_with($key, $prefix) && function_exists($fn)) {
            $map_fn = $fn;
            break;
        }
    }
    if ($map_fn === null) {
        return $field;
    }

    $src = $map_fn()[$key] ?? null;
    if (!is_array($src)) {
        return $field;
    }
    foreach (['label', 'instructions', 'button_label', 'placeholder', 'wrapper', 'collapsed', 'placement', 'rows', 'message', 'default_value', 'conditional_logic', 'min', 'max'] as $prop) {
        if (array_key_exists($prop, $src)) {
            $field[$prop] = $src[$prop];
        }
    }
    if (array_key_exists('_ui_order', $src)) {
        $field['menu_order'] = (int) $src['_ui_order'];
    }
    return $field;
});


add_action('acf/init', static function (): void {
    if (wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    if (!is_admin() || !current_user_can('manage_options')) {
        return;
    }
    if (get_option('justccell_acf_ui_ready') !== '1' && function_exists('acf_get_local_field_groups')) {
        foreach (acf_get_local_field_groups() as $group) {
            $key = (string) ($group['key'] ?? '');
            if ($key === '' || !str_starts_with($key, 'group_jc_')) {
                continue;
            }
            if (justccell_acf_field_group_post_id($key) > 0) {
                continue;
            }
            $full = $group;
            if (function_exists('acf_get_field_group')) {
                $loaded = acf_get_field_group($key);
                if (is_array($loaded)) {
                    $full = $loaded;
                }
            }
            if (empty($full['fields']) && function_exists('acf_get_fields')) {
                $fields = acf_get_fields($key);
                if (is_array($fields)) {
                    $full['fields'] = $fields;
                }
            }
            unset($full['ID'], $full['local'], $full['local_file'], $full['modified']);
            if (function_exists('acf_import_field_group')) {
                acf_import_field_group($full);
            } elseif (function_exists('acf_update_field_group')) {
                $saved = acf_update_field_group($full);
                $parent = is_array($saved) && !empty($saved['ID']) ? (int) $saved['ID'] : $key;
                if (!empty($full['fields']) && is_array($full['fields']) && function_exists('acf_update_field')) {
                    foreach ($full['fields'] as $field) {
                        if (!is_array($field)) {
                            continue;
                        }
                        $field['parent'] = $parent;
                        acf_update_field($field);
                    }
                }
            }
        }
        update_option('justccell_acf_ui_ready', '1');
        delete_transient('justccell_acf_groups_imported');
    }

    if (!get_option('justccell_acf_page_sections_scoped') && function_exists('acf_get_field_group') && function_exists('acf_update_field_group')) {
        $builder = acf_get_field_group('group_jc_page_sections');
        if (is_array($builder) && !empty($builder['ID'])) {
            $builder['location'] = [[
                [
                    'param'    => 'page_template',
                    'operator' => '==',
                    'value'    => 'page-templates/template-flexible.php',
                ],
            ]];
            acf_update_field_group($builder);
            update_option('justccell_acf_page_sections_scoped', '1');
        }
    }

    $front = (int) get_option('page_on_front');
    if ($front > 0 && get_option('justccell_home_devices_h1') !== '1' && function_exists('get_field') && function_exists('update_field')) {
        $tag = (string) get_field('home_devices_heading_tag', $front);
        if ($tag === '' || $tag === 'h2') {
            update_field('home_devices_heading_tag', 'h1', $front);
        }
        update_option('justccell_home_devices_h1', '1');
    }

    $ui_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '';

    if (
        $ui_ver !== ''
        && get_option('justccell_acf_j3_page_ui') !== $ui_ver
        && function_exists('acf_get_field')
        && function_exists('acf_update_field')
        && justccell_acf_field_group_post_id('group_jc_j3_page') > 0
        && function_exists('justccell_acf_j3_page_field_map')
    ) {
        if (function_exists('acf_get_field_group') && function_exists('acf_update_field_group')) {
            $j3 = acf_get_field_group('group_jc_j3_page');
            $src = justccell_acf_j3_page_group();
            if (is_array($j3) && !empty($j3['ID']) && is_array($src)) {
                $j3['title']            = (string) ($src['title'] ?? $j3['title']);
                $j3['position']         = (string) ($src['position'] ?? 'acf_after_title');
                $j3['label_placement']  = (string) ($src['label_placement'] ?? 'top');
                $j3['hide_on_screen']   = $src['hide_on_screen'] ?? ['the_content'];
                acf_update_field_group($j3);
            }
        }
        foreach (justccell_acf_j3_page_field_map() as $key => $src) {
            $existing = acf_get_field($key);
            if (!is_array($existing) || empty($existing['ID'])) {
                continue;
            }
            foreach (['label', 'instructions', 'button_label', 'placeholder', 'wrapper', 'collapsed', 'placement', 'rows', 'message', 'default_value', 'conditional_logic', 'min', 'max'] as $prop) {
                if (array_key_exists($prop, $src)) {
                    $existing[$prop] = $src[$prop];
                }
            }
            if (array_key_exists('_ui_order', $src)) {
                $existing['menu_order'] = (int) $src['_ui_order'];
            }
            unset($existing['sub_fields']);
            acf_update_field($existing);
        }
        update_option('justccell_acf_j3_page_ui', $ui_ver, false);
    }

    if (function_exists('justccell_acf_sync_group_field_ui')) {
        justccell_acf_sync_group_field_ui(
            'justccell_acf_home_page_ui',
            'group_jc_home_full',
            static fn () => justccell_acf_home_page_group(),
            static fn () => justccell_acf_home_page_field_map()
        );
        justccell_acf_sync_group_field_ui(
            'justccell_acf_listing_page_ui',
            'group_jc_listing_page',
            static fn () => justccell_acf_listing_page_group(),
            static fn () => justccell_acf_listing_page_field_map()
        );
        justccell_acf_sync_group_field_ui(
            'justccell_acf_gbrand_page_ui',
            'group_jc_generic_brand',
            static fn () => justccell_acf_generic_brand_page_group(),
            static fn () => justccell_acf_generic_brand_page_field_map()
        );
        justccell_acf_sync_group_field_ui(
            'justccell_acf_laser_page_ui',
            'group_jc_laser_page',
            static fn () => justccell_acf_laser_page_group(),
            static fn () => justccell_acf_laser_page_field_map()
        );
        justccell_acf_sync_group_field_ui(
            'justccell_acf_about_page_ui',
            'group_jc_about_page',
            static fn () => justccell_acf_about_page_group(),
            static fn () => justccell_acf_about_page_field_map()
        );
        justccell_acf_sync_group_field_ui(
            'justccell_acf_why_page_ui',
            'group_jc_why_pages',
            static fn () => justccell_acf_why_page_group(),
            static fn () => justccell_acf_why_page_field_map()
        );
        justccell_acf_sync_group_field_ui(
            'justccell_acf_contact_page_ui',
            'group_jc_contact_page',
            static fn () => justccell_acf_contact_page_group(),
            static fn () => justccell_acf_contact_page_field_map()
        );
    }
}, 99);

function justccell_render_flexible_sections(?int $post_id = null): void
{
    if (!function_exists('have_rows')) {
        return;
    }

    $post_id = $post_id ?? get_the_ID();
    if (!$post_id || !have_rows('page_sections', $post_id)) {
        return;
    }

    while (have_rows('page_sections', $post_id)) {
        the_row();
        $layout = (string) get_row_layout();
        $file   = JUSTCCELL_DIR . '/template-parts/flexible/' . $layout . '.php';
        if (is_readable($file)) {
            get_template_part('template-parts/flexible/' . $layout);
        }
    }
}
