<?php
/**
 * Header driven by Appearance → Menus (Primary) plus ACF options for the CTA.
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
 * @return list<array<string, mixed>>
 */
function justccell_header_nav(): array
{
    static $nav = null;
    if (is_array($nav)) {
        return $nav;
    }
    $tree = justccell_primary_menu_tree();
    $nav  = $tree !== [] ? justccell_header_nav_from_tree($tree) : justccell_header_nav_fallback();
    return $nav;
}

/**
 * @return array{label:string,url:string}
 */
function justccell_header_cta(): array
{
    if (get_option('justccell_hide_header_cta', '1') === '1') {
        return ['label' => '', 'url' => ''];
    }
    $label = '';
    $url   = '';
    if (function_exists('get_field')) {
        $label = trim((string) get_field('header_cta_label', 'option'));
        $link  = get_field('header_cta_url', 'option');
        if (is_string($link)) {
            $url = $link;
        } elseif (is_array($link) && isset($link['url'])) {
            $url = (string) $link['url'];
        }
    }
    if ($label === '') {
        return ['label' => '', 'url' => ''];
    }
    if ($url === '') {
        $url = function_exists('justccell_contact_page_url') ? justccell_contact_page_url() : home_url('/contact/');
    }
    return ['label' => $label, 'url' => $url];
}

function justccell_header_mega_limit(): int
{
    $limit = 5;
    if (function_exists('get_field')) {
        $raw = (int) get_field('header_mega_limit', 'option');
        if ($raw > 0) {
            $limit = $raw;
        }
    }
    return max(1, min(8, $limit));
}

/**
 * @return list<array{item:WP_Post,children:list<array{item:WP_Post,children:list}>}>
 */
function justccell_primary_menu_tree(): array
{
    $locations = get_nav_menu_locations();
    $menu_id   = (int) ($locations['primary'] ?? 0);
    if ($menu_id < 1) {
        return [];
    }
    $items = wp_get_nav_menu_items($menu_id);
    if (!is_array($items) || $items === []) {
        return [];
    }

    $by_parent = [];
    foreach ($items as $item) {
        if (!$item instanceof WP_Post) {
            continue;
        }
        $by_parent[(int) $item->menu_item_parent][] = $item;
    }

    $build = static function (int $parent) use (&$build, $by_parent): array {
        $out = [];
        foreach ($by_parent[$parent] ?? [] as $item) {
            $out[] = [
                'item'     => $item,
                'children' => $build((int) $item->ID),
            ];
        }
        return $out;
    };

    return $build(0);
}

/**
 * @param list<array{item:WP_Post,children:list}> $tree
 * @return list<array<string, mixed>>
 */
function justccell_header_nav_from_tree(array $tree): array
{
    $out = [];
    foreach ($tree as $node) {
        $item  = $node['item'];
        $kids  = $node['children'];
        $title = (string) $item->title;
        $url   = (string) $item->url;
        $kind  = justccell_header_item_kind($item, $kids);

        if ($kind === 'products_mega') {
            // Same tabs + product cards for Products and CCELL 3.0.
            $out[] = [
                'type'  => 'products',
                'title' => $title,
                'url'   => $url,
                'tabs'  => justccell_header_product_tabs($kids),
            ];
            continue;
        }
        if ($kind === 'dropdown') {
            $links = [];
            foreach ($kids as $child) {
                $links[] = [
                    'title' => (string) $child['item']->title,
                    'url'   => (string) $child['item']->url,
                ];
            }
            $out[] = [
                'type'  => 'dropdown',
                'title' => $title,
                'url'   => $url,
                'links' => $links,
            ];
            continue;
        }
        $out[] = [
            'type'  => 'link',
            'title' => $title,
            'url'   => $url,
        ];
    }
    return $out;
}

/**
 * @param list<array{item:WP_Post,children:list}> $kids
 */
function justccell_header_item_kind(WP_Post $item, array $kids): string
{
    $kind = 'auto';
    if (function_exists('get_field')) {
        $stored = (string) get_field('header_item_kind', $item->ID);
        if (in_array($stored, ['products_mega', 'dropdown', 'link', 'auto'], true)) {
            $kind = $stored;
        }
    }

    // Explicit plain link ignores children.
    if ($kind === 'link') {
        return 'link';
    }
    if ($kids === []) {
        return 'link';
    }

    // Category tabs (All-In-Ones / Cartridges / …) or CCELL 3.0 → product mega
    // even if an older save left Item type as “Text dropdown”.
    if (justccell_nav_kids_are_product_tabs($kids) || justccell_nav_item_is_j3($item)) {
        return 'products_mega';
    }

    if ($kind === 'products_mega') {
        return 'products_mega';
    }
    if ($kind === 'dropdown' || $kind === 'auto') {
        return 'dropdown';
    }
    return $kind;
}

/**
 * @param list<array{item:WP_Post,children:list}> $kids
 */
function justccell_nav_kids_are_product_tabs(array $kids): bool
{
    foreach ($kids as $child) {
        if (!isset($child['item']) || !$child['item'] instanceof WP_Post) {
            continue;
        }
        if (justccell_category_key_from_menu_item($child['item']) !== '') {
            return true;
        }
    }
    return false;
}

function justccell_category_key_from_menu_item(WP_Post $item): string
{
    $labels = justccell_product_category_labels();
    if ($item->type === 'taxonomy' && $item->object === 'product_cat') {
        $term = get_term((int) $item->object_id, 'product_cat');
        if ($term instanceof WP_Term && array_key_exists($term->slug, $labels)) {
            return $term->slug;
        }
    }
    $path = (string) (wp_parse_url((string) $item->url, PHP_URL_PATH) ?: '');
    if (function_exists('justccell_path_without_store')) {
        $path = justccell_path_without_store($path);
    }
    $slug = strtolower(basename(untrailingslashit($path)));
    return array_key_exists($slug, $labels) ? $slug : '';
}

function justccell_nav_item_is_j3(WP_Post $item, string $url = ''): bool
{
    $url   = $url !== '' ? $url : (string) $item->url;
    $title = strtolower(html_entity_decode((string) $item->title, ENT_QUOTES, 'UTF-8'));
    $path  = strtolower((string) (wp_parse_url($url, PHP_URL_PATH) ?: ''));
    return str_contains($path, 'justccell-3-0')
        || str_contains($path, 'ccell-3-0')
        || str_contains($title, 'justccell 3.0')
        || str_contains($title, 'ccell 3.0')
        || str_contains($title, 'justccell 3');
}

function justccell_j3_tab_key_from_menu_item(WP_Post $item): string
{
    $url  = (string) $item->url;
    $frag = (string) (wp_parse_url($url, PHP_URL_FRAGMENT) ?: '');
    $frag = str_starts_with($frag, 'j3-') ? substr($frag, 3) : $frag;
    if (in_array($frag, ['all-in-ones', 'cartridge', 'pod-system'], true)) {
        return $frag;
    }
    $title = strtolower(html_entity_decode((string) $item->title, ENT_QUOTES, 'UTF-8'));
    if (str_contains($title, 'pod')) {
        return 'pod-system';
    }
    if (str_contains($title, 'cartridge') || str_contains($title, '510')) {
        return 'cartridge';
    }
    if (str_contains($title, 'all')) {
        return 'all-in-ones';
    }
    return sanitize_title((string) $item->title);
}

/**
 * @param list<array{item:WP_Post,children:list}> $kids
 * @return list<array{key:string,label:string,url:string,items:list}>
 */
function justccell_header_j3_tabs(array $kids): array
{
    $defaults = function_exists('justccell_j3_product_groups_defaults')
        ? justccell_j3_product_groups_defaults()
        : [];
    $by_key = [];
    foreach ($defaults as $group) {
        $by_key[$group['key']] = $group;
    }

    $tabs = [];
    foreach ($kids as $child) {
        $item    = $child['item'];
        $key     = justccell_j3_tab_key_from_menu_item($item);
        $ids     = [];
        if (function_exists('get_field')) {
            $ids = function_exists('justccell_j3_relationship_ids')
                ? justccell_j3_relationship_ids(get_field('mega_products', $item->ID))
                : [];
        }
        $items = [];
        if ($ids !== [] && function_exists('justccell_j3_items_from_ids')) {
            $items = justccell_j3_items_from_ids($ids, $key);
        }
        if ($items === [] && isset($by_key[$key]) && function_exists('justccell_j3_item')) {
            foreach ($by_key[$key]['slugs'] as $slug) {
                $items[] = justccell_j3_item($slug, $by_key[$key]['category']);
            }
        }
        $cards = [];
        foreach ($items as $row) {
            $cards[] = function_exists('justccell_mega_card_from_catalog_item')
                ? justccell_mega_card_from_catalog_item($row)
                : [
                    'name'     => (string) ($row['name'] ?? ''),
                    'url'      => justccell_item_url($row),
                    'image'    => (string) ($row['image'] ?? ''),
                    'image_id' => (int) ($row['image_id'] ?? 0),
                ];
        }
        $anchor = function_exists('justccell_j3_anchor') ? justccell_j3_anchor($key) : 'j3-' . $key;
        $tabs[] = [
            'key'   => $anchor,
            'label' => (string) $item->title,
            'url'   => (string) $item->url !== '' ? (string) $item->url : (function_exists('justccell_bio_page_url') ? justccell_bio_page_url($anchor) : home_url('/ccell-3-0/#' . $anchor)),
            'items' => $cards,
        ];
    }

    if ($tabs !== []) {
        return $tabs;
    }

    foreach ($defaults as $group) {
        $cards  = [];
        $anchor = function_exists('justccell_j3_anchor') ? justccell_j3_anchor($group['key']) : 'j3-' . $group['key'];
        if (function_exists('justccell_j3_item')) {
            foreach ($group['slugs'] as $slug) {
                $row     = justccell_j3_item($slug, $group['category']);
                $cards[] = function_exists('justccell_mega_card_from_catalog_item')
                    ? justccell_mega_card_from_catalog_item($row)
                    : [
                        'name'     => (string) $row['name'],
                        'url'      => justccell_item_url($row),
                        'image'    => '',
                        'image_id' => (int) ($row['image_id'] ?? 0),
                    ];
            }
        }
        $tabs[] = [
            'key'   => $anchor,
            'label' => $group['heading'],
            'url'   => function_exists('justccell_bio_page_url') ? justccell_bio_page_url($anchor) : home_url('/ccell-3-0/#' . $anchor),
            'items' => $cards,
        ];
    }
    return $tabs;
}

/**
 * @param list<array{item:WP_Post,children:list}> $kids
 * @return list<array{key:string,label:string,url:string,items:list}>
 */
function justccell_header_product_tabs(array $kids): array
{
    $limit = justccell_header_mega_limit();
    $tabs  = [];
    foreach ($kids as $child) {
        $item = $child['item'];
        $key  = justccell_category_key_from_menu_item($item);
        if ($key === '') {
            $key = sanitize_title((string) $item->title);
        }
        $ids = [];
        if (function_exists('get_field')) {
            $rel = get_field('mega_products', $item->ID);
            if (is_array($rel)) {
                foreach ($rel as $row) {
                    if (is_numeric($row)) {
                        $ids[] = (int) $row;
                    } elseif ($row instanceof WP_Post) {
                        $ids[] = (int) $row->ID;
                    } elseif (is_array($row) && isset($row['ID'])) {
                        $ids[] = (int) $row['ID'];
                    }
                }
            }
        }
        $tabs[] = [
            'key'   => $key,
            'label' => (string) $item->title,
            'url'   => (string) $item->url,
            'items' => justccell_mega_cards_for_category($key, $ids, $limit),
        ];
    }
    return $tabs;
}

/**
 * @return list<array<string, mixed>>
 */
function justccell_header_nav_fallback(): array
{
    $tabs = [];
    foreach (justccell_mega_columns() as $key => $col) {
        $tabs[] = [
            'key'   => $key,
            'label' => (string) $col['label'],
            'url'   => (string) $col['url'],
            'items' => $col['items'],
        ];
    }

    return [
        [
            'type'  => 'products',
            'title' => __('Products', 'justccell'),
            'url'   => justccell_category_url('all-in-ones'),
            'tabs'  => $tabs,
        ],
        [
            'type'  => 'link',
            'title' => __('Justccell 3.0', 'justccell'),
            'url'   => function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : home_url('/ccell-3-0/'),
        ],
        [
            'type'  => 'dropdown',
            'title' => __('Why Justccell', 'justccell'),
            'url'   => home_url('/technology/'),
            'links' => justccell_why_links(),
        ],
        [
            'type'  => 'link',
            'title' => __('Solution', 'justccell'),
            'url'   => home_url('/solution/'),
        ],
        [
            'type'  => 'link',
            'title' => __('About', 'justccell'),
            'url'   => home_url('/about/'),
        ],
        [
            'type'  => 'link',
            'title' => __('Discover', 'justccell'),
            'url'   => home_url('/discover/'),
        ],
        [
            'type'  => 'link',
            'title' => __('Contact', 'justccell'),
            'url'   => home_url('/contact/'),
        ],
    ];
}

function justccell_seed_header_menu(): void
{
    if (get_option('justccell_header_menu_seeded') === '1') {
        return;
    }
    if (!is_admin() && !wp_doing_cron()) {
        // Still seed on front so logged-in QA gets a real menu after deploy.
    }

    $menu_id = wp_create_nav_menu('Justccell header');
    if (is_wp_error($menu_id)) {
        $existing = wp_get_nav_menu_object('Justccell header');
        $menu_id  = $existing instanceof WP_Term ? (int) $existing->term_id : 0;
    }
    $menu_id = (int) $menu_id;
    if ($menu_id < 1) {
        return;
    }
    $already = wp_get_nav_menu_items($menu_id);
    if (is_array($already) && $already !== []) {
        $locations = get_theme_mod('nav_menu_locations', []);
        if (!is_array($locations)) {
            $locations = [];
        }
        $locations['primary'] = $menu_id;
        set_theme_mod('nav_menu_locations', $locations);
        update_option('justccell_header_menu_seeded', '1');
        return;
    }

    $page_id = static function (string $slug): int {
        $page = get_page_by_path($slug);
        return $page instanceof WP_Post ? (int) $page->ID : 0;
    };

    $add_page = static function (int $menu, string $slug, string $title, int $parent, int $order) use ($page_id): int {
        $pid = $page_id($slug);
        $args = [
            'menu-item-title'    => $title,
            'menu-item-status'   => 'publish',
            'menu-item-position' => $order,
            'menu-item-parent-id'=> $parent,
        ];
        if ($pid > 0) {
            $args['menu-item-type']      = 'post_type';
            $args['menu-item-object']    = 'page';
            $args['menu-item-object-id'] = $pid;
        } else {
            $args['menu-item-type'] = 'custom';
            $args['menu-item-url']  = home_url('/' . $slug . '/');
        }
        $id = wp_update_nav_menu_item($menu, 0, $args);
        return is_int($id) ? $id : 0;
    };

    $add_custom = static function (int $menu, string $title, string $url, int $parent, int $order): int {
        $id = wp_update_nav_menu_item($menu, 0, [
            'menu-item-title'     => $title,
            'menu-item-url'       => $url,
            'menu-item-status'    => 'publish',
            'menu-item-type'      => 'custom',
            'menu-item-position'  => $order,
            'menu-item-parent-id' => $parent,
        ]);
        return is_int($id) ? $id : 0;
    };

    $order = 1;
    $products = $add_custom($menu_id, __('Products', 'justccell'), justccell_category_url('all-in-ones'), 0, $order++);
    $labels   = justccell_product_category_labels();
    $child_n  = 1;
    foreach ($labels as $slug => $label) {
        $add_custom($menu_id, $label, justccell_category_url($slug), $products, $child_n++);
    }
    if ($products > 0 && function_exists('update_field')) {
        update_field('header_item_kind', 'products_mega', $products);
    }

    $j3_slug = 'ccell-3-0';
    if (function_exists('justccell_bio_page')) {
        $bio = justccell_bio_page();
        if ($bio instanceof WP_Post && $bio->post_name !== '') {
            $j3_slug = (string) $bio->post_name;
        }
    }
    $j3 = $add_page($menu_id, $j3_slug, __('Justccell 3.0', 'justccell'), 0, $order++);
    if ($j3 > 0 && function_exists('update_field')) {
        // Plain link until children are added; Auto becomes products mega with kids.
        update_field('header_item_kind', 'auto', $j3);
    }

    $why = $add_page($menu_id, 'technology', __('Why Justccell', 'justccell'), 0, $order++);
    $why_n = 1;
    $add_page($menu_id, 'technology', __('All-New Technology', 'justccell'), $why, $why_n++);
    $add_page($menu_id, 'safety', __('Safety', 'justccell'), $why, $why_n++);
    $add_page($menu_id, 'research', __('R&D Capability', 'justccell'), $why, $why_n++);
    $add_page($menu_id, 'manufacture', __('Manufacturing Capability', 'justccell'), $why, $why_n++);
    $add_page($menu_id, 'location', __('Location', 'justccell'), $why, $why_n++);
    if ($why > 0 && function_exists('update_field')) {
        update_field('header_item_kind', 'dropdown', $why);
    }

    $add_page($menu_id, 'solution', __('Solution', 'justccell'), 0, $order++);
    $add_page($menu_id, 'about', __('About', 'justccell'), 0, $order++);
    $add_page($menu_id, 'discover', __('Discover', 'justccell'), 0, $order++);
    $add_page($menu_id, 'contact', __('Contact', 'justccell'), 0, $order++);

    $locations = get_theme_mod('nav_menu_locations', []);
    if (!is_array($locations)) {
        $locations = [];
    }
    $locations['primary'] = $menu_id;
    set_theme_mod('nav_menu_locations', $locations);
    update_option('justccell_header_menu_seeded', '1');
}

add_action('init', 'justccell_seed_header_menu', 45);
add_action('init', 'justccell_rewrite_location_nav_urls', 76);

/**
 * Add Location as the last Why Justccell child. Skip if it is already in the primary menu.
 */
function justccell_ensure_locations_nav(): void
{
    $locations = get_nav_menu_locations();
    $menu_id   = (int) ($locations['primary'] ?? 0);
    if ($menu_id < 1) {
        return;
    }
    $items = wp_get_nav_menu_items($menu_id);
    if (!is_array($items) || $items === []) {
        return;
    }

    $why_id         = 0;
    $has_locations  = false;
    $last_child_pos = 0;
    foreach ($items as $item) {
        if (!$item instanceof WP_Post) {
            continue;
        }
        $title = strtolower(html_entity_decode((string) $item->title, ENT_QUOTES, 'UTF-8'));
        $path  = strtolower((string) (wp_parse_url((string) $item->url, PHP_URL_PATH) ?: ''));
        if ((int) $item->menu_item_parent === 0 && (str_contains($title, 'why justccell') || str_contains($title, 'why ccell'))) {
            $why_id = (int) $item->ID;
        }
        if ($title === 'locations' || $title === 'location' || str_contains($path, '/location')) {
            $has_locations = true;
        }
    }
    if ($has_locations || $why_id < 1) {
        return;
    }
    foreach ($items as $item) {
        if ($item instanceof WP_Post && (int) $item->menu_item_parent === $why_id) {
            $last_child_pos = max($last_child_pos, (int) $item->menu_order);
        }
    }

    $page = function_exists('justccell_find_location_page') ? justccell_find_location_page() : (function_exists('justccell_find_page_by_slug') ? justccell_find_page_by_slug('location') : null);
    $args = [
        'menu-item-title'     => __('Location', 'justccell'),
        'menu-item-status'    => 'publish',
        'menu-item-parent-id' => $why_id,
        'menu-item-position'  => $last_child_pos + 1,
    ];
    if ($page instanceof WP_Post) {
        $args['menu-item-type']      = 'post_type';
        $args['menu-item-object']    = 'page';
        $args['menu-item-object-id'] = (int) $page->ID;
    } else {
        $args['menu-item-type'] = 'custom';
        $args['menu-item-url']  = home_url('/location/');
    }
    wp_update_nav_menu_item($menu_id, 0, $args);
}

/**
 * Point leftover custom menu URLs at /location/ after the page slug change.
 */
function justccell_rewrite_location_nav_urls(): void
{
    if (get_option('justccell_location_nav_url_20260901') === '1') {
        return;
    }
    $locations = get_nav_menu_locations();
    $menu_id   = (int) ($locations['primary'] ?? 0);
    if ($menu_id < 1) {
        update_option('justccell_location_nav_url_20260901', '1', false);
        return;
    }
    $items = wp_get_nav_menu_items($menu_id);
    if (!is_array($items)) {
        return;
    }
    $page = function_exists('justccell_find_location_page') ? justccell_find_location_page() : null;
    foreach ($items as $item) {
        if (!$item instanceof WP_Post) {
            continue;
        }
        if ((string) ($item->type ?? '') === 'post_type') {
            continue;
        }
        $path = strtolower((string) (wp_parse_url((string) $item->url, PHP_URL_PATH) ?: ''));
        if (preg_match('#/locations(/|$)#', $path) !== 1) {
            continue;
        }
        $args = [
            'menu-item-title'     => (string) $item->title,
            'menu-item-status'    => 'publish',
            'menu-item-parent-id' => (int) $item->menu_item_parent,
            'menu-item-position' => (int) $item->menu_order,
        ];
        if ($page instanceof WP_Post) {
            $args['menu-item-type']      = 'post_type';
            $args['menu-item-object']    = 'page';
            $args['menu-item-object-id'] = (int) $page->ID;
        } else {
            $args['menu-item-type'] = 'custom';
            $args['menu-item-url']  = home_url('/location/');
        }
        wp_update_nav_menu_item($menu_id, (int) $item->ID, $args);
    }
    update_option('justccell_location_nav_url_20260901', '1', false);
}

/**
 * @deprecated 0.9.106 No longer flattens CCELL 3.0 — nested mega tabs are supported.
 */
function justccell_ensure_j3_header_mega(): void
{
    // Intentionally empty.
}

/**
 * Legacy no-op. Older versions forced CCELL 3.0 to a plain link and deleted children.
 * Nested Products-style mega under CCELL 3.0 is allowed again (0.9.106+).
 */
function justccell_flatten_j3_header_link(): void
{
    // Intentionally empty — do not strip submenu items under CCELL 3.0.
}

/**
 * Force CCELL 3.0 parent Item type to Products mega when it has children.
 */
function justccell_unlock_j3_header_mega(): void
{
    if (wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    if (get_option('justccell_j3_header_mega_unlocked_0107') === '1') {
        return;
    }

    $locations = get_nav_menu_locations();
    $menu_id   = (int) ($locations['primary'] ?? 0);
    if ($menu_id < 1) {
        update_option('justccell_j3_header_mega_unlocked_0107', '1', false);
        return;
    }
    $items = wp_get_nav_menu_items($menu_id);
    if (!is_array($items)) {
        update_option('justccell_j3_header_mega_unlocked_0107', '1', false);
        return;
    }

    $j3_id = 0;
    foreach ($items as $item) {
        if (!$item instanceof WP_Post) {
            continue;
        }
        if ((int) $item->menu_item_parent === 0 && justccell_nav_item_is_j3($item)) {
            $j3_id = (int) $item->ID;
            break;
        }
    }

    if ($j3_id > 0 && function_exists('update_field')) {
        $has_kids = false;
        foreach ($items as $item) {
            if ($item instanceof WP_Post && (int) $item->menu_item_parent === $j3_id) {
                $has_kids = true;
                break;
            }
        }
        if ($has_kids) {
            update_field('header_item_kind', 'products_mega', $j3_id);
        }
    }

    update_option('justccell_j3_header_mega_unlocked_0107', '1', false);
}

add_action('init', 'justccell_unlock_j3_header_mega', 74);

/**
 * Menu “Mega product cards” picker: only products in this tab’s category.
 *
 * @param array<string, mixed> $args
 * @param array<string, mixed> $field
 * @param int|string           $post_id
 * @return array<string, mixed>
 */
function justccell_mega_products_relationship_query(array $args, $field, $post_id): array
{
    unset($field);
    $id = (int) preg_replace('/\D+/', '', (string) $post_id);
    if ($id < 1) {
        return $args;
    }
    $raw = get_post($id);
    if (!$raw instanceof WP_Post) {
        return $args;
    }
    $item = wp_setup_nav_menu_item($raw);
    if (!$item instanceof WP_Post) {
        return $args;
    }
    $key = justccell_category_key_from_menu_item($item);
    if ($key === '') {
        $key = sanitize_title((string) $item->title);
    }
    if ($key === '' || !taxonomy_exists('product_cat')) {
        return $args;
    }
    $clause = [
        'taxonomy'         => 'product_cat',
        'field'            => 'slug',
        'terms'            => [$key],
        'include_children' => false,
    ];
    $existing = $args['tax_query'] ?? [];
    if (!is_array($existing) || $existing === []) {
        $args['tax_query'] = [$clause];
        return $args;
    }
    $existing[] = $clause;
    $existing['relation'] = 'AND';
    $args['tax_query']    = $existing;
    return $args;
}

add_filter('acf/fields/relationship/query/name=mega_products', 'justccell_mega_products_relationship_query', 10, 3);

