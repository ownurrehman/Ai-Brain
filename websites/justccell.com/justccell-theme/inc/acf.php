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

require_once JUSTCCELL_DIR . '/inc/acf-product-clone-maintenance.php';

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
    $slugs = ['home', 'contact', 'about', 'discover', 'ccell-3-0'];
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
    if (function_exists('acf_get_field_group')) {
        $group = acf_get_field_group($key);
        if (is_array($group) && !empty($group['ID'])) {
            return (int) $group['ID'];
        }
    }
    if ($key === JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY && function_exists('justccell_acf_pick_product_clone_group_winner')) {
        $winner = justccell_acf_pick_product_clone_group_winner(
            justccell_acf_find_field_group_posts_by_key($key)
        );
        if (is_array($winner) && (int) ($winner['id'] ?? 0) > 0) {
            return (int) $winner['id'];
        }
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

add_filter('acf/prepare_field/key=field_jc_j3_products', static function ($field) {
    return justccell_acf_should_hide_field_in_ui() ? false : $field;
});
add_filter('acf/prepare_field/key=field_jc_j3_group_products', static function ($field) {
    return false;
});
add_filter('acf/prepare_field/name=j3_product_slugs', static function ($field) {
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
