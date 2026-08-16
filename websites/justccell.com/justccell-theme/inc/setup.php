<?php
/**
 * Theme supports, menus, permalinks.
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 48,
        'width'       => 180,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', [
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('editor-styles');
    add_editor_style('assets/css/globals.css');

    register_nav_menus([
        'primary' => __('Primary', 'justccell'),
        'footer'  => __('Footer', 'justccell'),
        'legal'   => __('Legal', 'justccell'),
    ]);

    add_image_size('justccell-card', 720, 720, true);
    add_image_size('justccell-hero', 1920, 1080, true);
});

add_action('after_switch_theme', 'justccell_seed_site');
add_action('init', static function (): void {
    if (get_option('justccell_seeded') === '1') {
        justccell_ensure_core_pages();
        return;
    }
    justccell_seed_site();
}, 30);

function justccell_seed_site(): void
{
    if (get_option('permalink_structure') !== '/%postname%/') {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%postname%/');
        $wp_rewrite->flush_rules(true);
    }

    if (get_option('blogname') === 'justccell.com') {
        update_option('blogname', 'Justccell');
    }

    $cats = [
        'all-in-ones' => __('All-In-Ones', 'justccell'),
        'cartridge'   => __('Cartridges', 'justccell'),
        'pod-system'  => __('Pod Systems', 'justccell'),
        'battery'     => __('510 Batteries', 'justccell'),
    ];

    if (taxonomy_exists('product_cat')) {
        foreach ($cats as $slug => $name) {
            if (!term_exists($slug, 'product_cat')) {
                wp_insert_term($name, 'product_cat', ['slug' => $slug]);
            }
        }
    }

    $created_ids = justccell_ensure_core_pages();

    justccell_seed_menus($created_ids);
    update_option('justccell_seeded', '1');
}

/**
 * @return array<string, int>
 */
function justccell_ensure_core_pages(): array
{
    $pages = [
        'contact'     => __('Get samples and quotes', 'justccell'),
        'about'       => __('About Justccell', 'justccell'),
        'technology'  => __('Why Justccell', 'justccell'),
        'solution'    => __('Solution', 'justccell'),
        'safety'      => __('Safety', 'justccell'),
        'research'    => __('Research', 'justccell'),
        'manufacture' => __('Manufacture', 'justccell'),
    ];

    $created_ids = [];
    foreach ($pages as $slug => $title) {
        $existing = get_page_by_path($slug);
        if ($existing instanceof WP_Post) {
            $created_ids[$slug] = (int) $existing->ID;
            continue;
        }
        $id = wp_insert_post([
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ]);
        if (is_int($id) && $id > 0) {
            $created_ids[$slug] = $id;
        }
    }

    return $created_ids;
}

/**
 * @param array<string, int> $page_ids
 */
function justccell_seed_menus(array $page_ids): void
{
    $primary_id = wp_create_nav_menu('Primary');
    if (is_wp_error($primary_id)) {
        $primary_id = 0;
        $menus = wp_get_nav_menus();
        foreach ($menus as $menu) {
            if ($menu->name === 'Primary') {
                $primary_id = (int) $menu->term_id;
                break;
            }
        }
    }

    if ($primary_id > 0 && !wp_get_nav_menu_items($primary_id)) {
        $order = 1;
        foreach (['all-in-ones', 'cartridge', 'pod-system', 'battery'] as $slug) {
            $term = get_term_by('slug', $slug, 'product_cat');
            if (!$term instanceof WP_Term) {
                continue;
            }
            wp_update_nav_menu_item($primary_id, 0, [
                'menu-item-title'  => $term->name,
                'menu-item-url'    => get_term_link($term),
                'menu-item-status' => 'publish',
                'menu-item-type'   => 'custom',
                'menu-item-position' => $order++,
            ]);
        }
        foreach (['technology', 'about', 'contact'] as $slug) {
            if (empty($page_ids[$slug])) {
                continue;
            }
            wp_update_nav_menu_item($primary_id, 0, [
                'menu-item-title'     => get_the_title($page_ids[$slug]),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_ids[$slug],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
                'menu-item-position'  => $order++,
            ]);
        }
        $locations = get_theme_mod('nav_menu_locations', []);
        if (!is_array($locations)) {
            $locations = [];
        }
        $locations['primary'] = $primary_id;
        $locations['footer']  = $primary_id;
        set_theme_mod('nav_menu_locations', $locations);
    }
}

add_filter('excerpt_length', static function (): int {
    return 24;
});

/**
 * Inquiry landing URL, optionally prefilled with a product SKU.
 */
function justccell_inquiry_url(string $sku = ''): string
{
    $url = home_url('/contact/');
    if ($sku !== '') {
        $url = add_query_arg('sku', rawurlencode($sku), $url);
    }
    return $url;
}
