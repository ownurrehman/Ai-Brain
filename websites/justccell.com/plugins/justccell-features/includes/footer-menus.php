<?php
/**
 * Footer menu locations, column walker, logo helper, and default menu seeding.
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
 * Footer logo: Storefront override → brand asset scan → Site Identity (same fallback chain as header).
 */
function justccell_footer_logo_id(): int
{
    if (function_exists('get_field')) {
        $acf = get_field('store_footer_logo', 'option');
        if (is_array($acf) && !empty($acf['ID'])) {
            return (int) $acf['ID'];
        }
        if (is_numeric($acf) && (int) $acf > 0) {
            return (int) $acf;
        }
    }

    $logo_id = function_exists('justccell_brand_logo_id') ? justccell_brand_logo_id() : 0;
    if ($logo_id < 1) {
        $logo_id = (int) get_theme_mod('custom_logo');
    }

    return $logo_id;
}

/**
 * Resolve a footer location, including legacy `footer` / `legal` slugs.
 */
function justccell_footer_menu_location(string $location): string
{
    $locs = get_nav_menu_locations();
    if (!is_array($locs)) {
        return $location;
    }

    if (!empty($locs[$location])) {
        return $location;
    }

    if ($location === 'footer_top' && !empty($locs['footer'])) {
        return 'footer';
    }

    if ($location === 'footer_last' && !empty($locs['legal'])) {
        return 'legal';
    }

    return $location;
}

function justccell_has_assigned_footer_menu(string $location): bool
{
    $locs = get_nav_menu_locations();
    if (!is_array($locs)) {
        return false;
    }

    $resolved = justccell_footer_menu_location($location);
    $menu_id  = (int) ($locs[$resolved] ?? 0);
    $primary  = (int) ($locs['primary'] ?? 0);

    if ($menu_id < 1) {
        return false;
    }

    if ($location === 'footer_top' && $menu_id === $primary) {
        return false;
    }

    $items = wp_get_nav_menu_items($menu_id);

    return is_array($items) && $items !== [];
}

/**
 * Flat footer links (social row, legal strip) — outputs bare <a> tags.
 */
class Justccell_Footer_Inline_Walker extends Walker_Nav_Menu
{
  /**
   * @param WP_Post $data_object
   */
    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0): void
    {
        $item   = $data_object;
        $url    = !empty($item->url) ? (string) $item->url : '#';
        $target = !empty($item->target) ? (string) $item->target : '';
        $rel    = trim((string) $item->xfn);

        if ($target === '_blank' && stripos($rel, 'noopener') === false) {
            $rel = trim($rel . ' noopener noreferrer');
        }

        $output .= '<a href="' . esc_url($url) . '"';
        if ($target !== '') {
            $output .= ' target="' . esc_attr($target) . '"';
        }
        if ($rel !== '') {
            $output .= ' rel="' . esc_attr($rel) . '"';
        }
        $output .= '>' . esc_html((string) $item->title) . '</a>';
    }
}

/**
 * Column-style footer menu: parent item = column heading, children = links.
 */
class Justccell_Footer_Column_Walker extends Walker_Nav_Menu
{
    public function start_lvl(&$output, $depth = 0, $args = null): void
    {
        if ((int) $depth === 0) {
            $output .= '<ul>';
        }
    }

    public function end_lvl(&$output, $depth = 0, $args = null): void
    {
        if ((int) $depth === 0) {
            $output .= '</ul>';
        }
    }

  /**
   * @param WP_Post $data_object
   */
    public function start_el(&$output, $data_object, $depth = 0, $args = null, $current_object_id = 0): void
    {
        $item = $data_object;
        $url  = !empty($item->url) ? (string) $item->url : '#';

        if ((int) $depth === 0) {
            $output .= '<div class="foot_ul"><a class="font18" href="' . esc_url($url) . '">' . esc_html((string) $item->title) . '</a>';
            return;
        }

        $output .= '<li><a href="' . esc_url($url) . '">' . esc_html((string) $item->title) . '</a></li>';
    }

  /**
   * @param WP_Post $data_object
   */
    public function end_el(&$output, $data_object, $depth = 0, $args = null): void
    {
        if ((int) $depth !== 0) {
            return;
        }

        $classes = is_array($data_object->classes ?? null) ? $data_object->classes : [];
        if (!in_array('menu-item-has-children', $classes, true)) {
            $output .= '<ul></ul>';
        }

        $output .= '</div>';
    }
}

function justccell_render_footer_column_menu(): void
{
    if (justccell_has_assigned_footer_menu('footer_top')) {
        wp_nav_menu([
            'theme_location' => justccell_footer_menu_location('footer_top'),
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 2,
            'fallback_cb'    => false,
            'walker'         => new Justccell_Footer_Column_Walker(),
        ]);
        return;
    }

    $columns = function_exists('justccell_footer_columns') ? justccell_footer_columns() : [];
    foreach ($columns as $col) {
        echo '<div class="foot_ul">';
        echo '<a class="font18" href="' . esc_url((string) $col['url']) . '">' . esc_html((string) $col['title']) . '</a>';
        echo '<ul>';
        foreach ($col['links'] as $link) {
            echo '<li><a href="' . esc_url((string) $link['url']) . '">' . esc_html((string) $link['title']) . '</a></li>';
        }
        echo '</ul></div>';
    }
}

function justccell_render_footer_bottom_menu(): void
{
    if (justccell_has_assigned_footer_menu('footer_bottom')) {
        echo '<div class="foot_b_icon">';
        wp_nav_menu([
            'theme_location' => justccell_footer_menu_location('footer_bottom'),
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 1,
            'fallback_cb'    => false,
            'walker'         => new Justccell_Footer_Inline_Walker(),
        ]);
        echo '</div>';
        return;
    }

    $social = function_exists('justccell_social_links') ? justccell_social_links() : [];
    if ($social === []) {
        return;
    }

    echo '<div class="foot_b_icon">';
    foreach ($social as $item) {
        echo '<a href="' . esc_url((string) $item['url']) . '" rel="noopener noreferrer" target="_blank">' . esc_html((string) $item['label']) . '</a>';
    }
    echo '</div>';
}

function justccell_render_footer_last_menu(): void
{
    if (justccell_has_assigned_footer_menu('footer_last')) {
        wp_nav_menu([
            'theme_location' => justccell_footer_menu_location('footer_last'),
            'container'      => false,
            'items_wrap'     => '%3$s',
            'depth'          => 1,
            'fallback_cb'    => false,
            'walker'         => new Justccell_Footer_Inline_Walker(),
        ]);
        return;
    }

    $legal = function_exists('justccell_legal_links') ? justccell_legal_links() : [];
    foreach ($legal as $item) {
        echo '<a href="' . esc_url((string) $item['url']) . '">' . esc_html((string) $item['label']) . '</a>';
    }
}

function justccell_get_or_create_nav_menu(string $name): int
{
    $menu = wp_get_nav_menu_object($name);
    if ($menu instanceof WP_Term) {
        return (int) $menu->term_id;
    }

    $id = wp_create_nav_menu($name);

    return is_wp_error($id) ? 0 : (int) $id;
}

/**
 * Copy legacy footer / legal assignments into the new locations once.
 */
function justccell_migrate_footer_menu_locations(): void
{
    $locs = get_theme_mod('nav_menu_locations', []);
    if (!is_array($locs)) {
        $locs = [];
    }

    $changed = false;

    if (!empty($locs['footer']) && empty($locs['footer_top'])) {
        $primary_id = (int) ($locs['primary'] ?? 0);
        if ((int) $locs['footer'] !== $primary_id) {
            $locs['footer_top'] = (int) $locs['footer'];
            $changed            = true;
        }
    }

    if (!empty($locs['legal']) && empty($locs['footer_last'])) {
        $locs['footer_last'] = (int) $locs['legal'];
        $changed             = true;
    }

    if ($changed) {
        set_theme_mod('nav_menu_locations', $locs);
    }
}

/**
 * Create default footer menus when no menu is assigned to a footer location.
 */
function justccell_seed_footer_menus(): void
{
    $locs = get_theme_mod('nav_menu_locations', []);
    if (!is_array($locs)) {
        $locs = [];
    }

    $primary_id = (int) ($locs['primary'] ?? 0);
    $top_id     = (int) ($locs['footer_top'] ?? 0);

    if ($top_id < 1 || $top_id === $primary_id) {
        $top_id = justccell_get_or_create_nav_menu('Footer Top');
        if ($top_id > 0 && !wp_get_nav_menu_items($top_id)) {
            $order   = 1;
            $columns = function_exists('justccell_footer_columns') ? justccell_footer_columns() : [];
            foreach ($columns as $col) {
                $parent_id = wp_update_nav_menu_item($top_id, 0, [
                    'menu-item-title'    => (string) $col['title'],
                    'menu-item-url'      => (string) $col['url'],
                    'menu-item-status'   => 'publish',
                    'menu-item-type'     => 'custom',
                    'menu-item-position' => $order++,
                ]);
                if (is_wp_error($parent_id) || (int) $parent_id < 1) {
                    continue;
                }
                $child_order = 1;
                foreach ($col['links'] as $link) {
                    wp_update_nav_menu_item($top_id, 0, [
                        'menu-item-title'     => (string) $link['title'],
                        'menu-item-url'       => (string) $link['url'],
                        'menu-item-status'    => 'publish',
                        'menu-item-type'      => 'custom',
                        'menu-item-parent-id' => (int) $parent_id,
                        'menu-item-position'  => $child_order++,
                    ]);
                }
            }
        }
        if ($top_id > 0) {
            $locs['footer_top'] = $top_id;
        }
    }

    if (empty($locs['footer_bottom'])) {
        $bottom_id = justccell_get_or_create_nav_menu('Footer Bottom');
        if ($bottom_id > 0 && !wp_get_nav_menu_items($bottom_id)) {
            $social = function_exists('justccell_social_links') ? justccell_social_links() : [];
            $order  = 1;
            foreach ($social as $item) {
                wp_update_nav_menu_item($bottom_id, 0, [
                    'menu-item-title'    => (string) $item['label'],
                    'menu-item-url'      => (string) $item['url'],
                    'menu-item-status'   => 'publish',
                    'menu-item-type'     => 'custom',
                    'menu-item-position' => $order++,
                ]);
            }
        }
        if ($bottom_id > 0) {
            $locs['footer_bottom'] = $bottom_id;
        }
    }

    if (empty($locs['footer_last'])) {
        $last_id = justccell_get_or_create_nav_menu('Footer Last');
        if ($last_id > 0 && !wp_get_nav_menu_items($last_id)) {
            $legal = function_exists('justccell_legal_links') ? justccell_legal_links() : [];
            $order = 1;
            foreach ($legal as $item) {
                wp_update_nav_menu_item($last_id, 0, [
                    'menu-item-title'    => (string) $item['label'],
                    'menu-item-url'      => (string) $item['url'],
                    'menu-item-status'   => 'publish',
                    'menu-item-type'     => 'custom',
                    'menu-item-position' => $order++,
                ]);
            }
        }
        if ($last_id > 0) {
            $locs['footer_last'] = $last_id;
        }
    }

    set_theme_mod('nav_menu_locations', $locs);
}

add_action('init', static function (): void {
    if ((int) get_option('justccell_footer_menus_v', 0) >= 1) {
        return;
    }

    justccell_migrate_footer_menu_locations();
    justccell_seed_footer_menus();
    update_option('justccell_footer_menus_v', 1, false);
}, 20);
