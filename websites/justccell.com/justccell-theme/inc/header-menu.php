<?php
/**
 * Primary header menu — standard Appearance → Menus tree, plus product-card mega for category children.
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

        if ($kids === []) {
            $out[] = [
                'type'  => 'link',
                'title' => $title,
                'url'   => $url,
            ];
            continue;
        }

        if (justccell_nav_kids_are_product_tabs($kids)) {
            $tabs = justccell_nav_item_is_j3($item)
                ? justccell_header_j3_tabs($kids)
                : justccell_header_product_tabs($kids);
            $out[] = [
                'type'  => 'products',
                'title' => $title,
                'url'   => $url,
                'tabs'  => $tabs,
            ];
            continue;
        }

        $out[] = [
            'type'  => 'dropdown',
            'title' => $title,
            'url'   => $url,
            'links' => justccell_header_dropdown_links($kids),
        ];
    }

    return $out;
}

/**
 * @param list<array{item:WP_Post,children:list}> $kids
 * @return list<array{title:string,url:string,children:list}>
 */
function justccell_header_dropdown_links(array $kids): array
{
    $links = [];
    foreach ($kids as $child) {
        if (!isset($child['item']) || !$child['item'] instanceof WP_Post) {
            continue;
        }
        $item     = $child['item'];
        $children = isset($child['children']) && is_array($child['children'])
            ? justccell_header_dropdown_links($child['children'])
            : [];
        $links[]  = [
            'title'    => (string) $item->title,
            'url'      => (string) $item->url,
            'children' => $children,
        ];
    }

    return $links;
}

function justccell_header_dropdown_has_nested_links(array $links): bool
{
    foreach ($links as $link) {
        if (($link['children'] ?? []) !== []) {
            return true;
        }
    }

    return false;
}

/**
 * @param list<array{title:string,url:string,children?:list}> $links
 */
function justccell_render_mobile_dropdown_links(array $links, int $depth = 0): void
{
    foreach ($links as $link) {
        $class = $depth > 0 ? ' class="c-title-con__depth-' . (int) $depth . '"' : '';
        echo '<a' . $class . ' href="' . esc_url((string) $link['url']) . '">' . esc_html((string) $link['title']) . '</a>';
        if (!empty($link['children']) && is_array($link['children'])) {
            justccell_render_mobile_dropdown_links($link['children'], $depth + 1);
        }
    }
}

/**
 * @param list<array{item:WP_Post,children:list}> $kids
 */
function justccell_nav_kids_are_product_tabs(array $kids): bool
{
    foreach ($kids as $child) {
        $item = $child['item'];
        if ($item instanceof WP_Post && justccell_menu_item_is_product_tab($item)) {
            return true;
        }
    }

    return false;
}

function justccell_menu_item_is_product_tab(WP_Post $item): bool
{
    if ($item->type === 'taxonomy' && $item->object === 'product_cat') {
        return true;
    }

    return justccell_category_key_from_menu_item($item) !== '';
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

/**
 * Top-level Appearance → Menus row for the CCELL 3.0 bio page (product mega shows J3 SKUs only).
 */
function justccell_nav_item_is_j3(WP_Post $item): bool
{
    if ($item->type === 'post_type' && $item->object === 'page') {
        $page_id = (int) $item->object_id;
        if ($page_id > 0 && function_exists('justccell_is_bio_page') && justccell_is_bio_page($page_id)) {
            return true;
        }
    }

    $bio_url = function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : '';
    if ($bio_url !== '') {
        $item_path = (string) (wp_parse_url((string) $item->url, PHP_URL_PATH) ?: '');
        $bio_path  = (string) (wp_parse_url($bio_url, PHP_URL_PATH) ?: '');
        if ($item_path !== '' && $bio_path !== ''
            && untrailingslashit(strtolower($item_path)) === untrailingslashit(strtolower($bio_path))
        ) {
            return true;
        }
    }

    $title = strtolower(trim(preg_replace('/\s+/u', ' ', html_entity_decode((string) $item->title, ENT_QUOTES | ENT_HTML5, 'UTF-8')) ?? ''));
    if ($title === '') {
        return false;
    }

    return preg_match('/\bccell\s*3(?:\.0)?\b/u', $title) === 1
        || preg_match('/\bjust\s*ccell\s*3(?:\.0)?\b/u', $title) === 1;
}

/**
 * Map a CCELL 3.0 submenu row to a storefront category key (510 Batteries ≠ cartridges).
 */
function justccell_j3_tab_key_from_menu_item(WP_Post $item): string
{
    $title = strtolower(trim((string) $item->title));
    $path  = (string) (wp_parse_url((string) $item->url, PHP_URL_PATH) ?: '');
    if (function_exists('justccell_path_without_store')) {
        $path = justccell_path_without_store($path);
    }
    $slug = strtolower(basename(untrailingslashit($path)));
    if (
        str_contains($title, '510')
        || str_contains($title, 'batter')
        || $slug === 'battery'
    ) {
        return 'battery';
    }

    return justccell_category_key_from_menu_item($item);
}

/**
 * @param list<array{item:WP_Post,children:list}> $kids
 * @return list<array{key:string,label:string,url:string,items:list}>
 */
function justccell_header_j3_tabs(array $kids): array
{
    $limit = justccell_header_mega_limit();
    $tabs  = [];
    foreach ($kids as $child) {
        $item = $child['item'];
        if (!$item instanceof WP_Post) {
            continue;
        }
        $key = justccell_j3_tab_key_from_menu_item($item);
        if ($key === '') {
            continue;
        }
        $tabs[] = [
            'key'   => $key,
            'label' => (string) $item->title,
            'url'   => (string) $item->url,
            'items' => function_exists('justccell_j3_mega_cards_for_category')
                ? justccell_j3_mega_cards_for_category($key, $limit)
                : [],
        ];
    }

    return $tabs;
}

/**
 * Default CCELL 3.0 header tabs when the menu tree is not configured.
 *
 * @return list<array{key:string,label:string,url:string,items:list}>
 */
function justccell_header_j3_default_tabs(): array
{
    if (!function_exists('justccell_j3_product_groups_defaults')) {
        return [];
    }
    $limit = justccell_header_mega_limit();
    $tabs  = [];
    foreach (justccell_j3_product_groups_defaults() as $group) {
        $key = (string) ($group['key'] ?? '');
        if ($key === '') {
            continue;
        }
        $tabs[] = [
            'key'   => $key,
            'label' => (string) ($group['heading'] ?? $key),
            'url'   => function_exists('justccell_category_url') ? justccell_category_url($key) : home_url('/' . $key . '/'),
            'items' => function_exists('justccell_j3_mega_cards_for_category')
                ? justccell_j3_mega_cards_for_category($key, $limit)
                : [],
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
        if (!$item instanceof WP_Post || !justccell_menu_item_is_product_tab($item)) {
            continue;
        }
        $key = justccell_category_key_from_menu_item($item);
        if ($key === '') {
            continue;
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
            'type'  => 'products',
            'title' => __('CCELL 3.0', 'justccell'),
            'url'   => function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : home_url('/cell-3-0/'),
            'tabs'  => justccell_header_j3_default_tabs(),
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

/**
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
    $existing[]           = $clause;
    $existing['relation']  = 'AND';
    $args['tax_query']     = $existing;

    return $args;
}

/**
 * Only show optional product picks on submenu rows that map to a WooCommerce category.
 *
 * @param array<string, mixed>|false $field
 * @return array<string, mixed>|false
 */
function justccell_prepare_mega_products_menu_field($field)
{
    if (!is_array($field) || !function_exists('get_post')) {
        return $field;
    }

    $post_id = (int) ($field['post_id'] ?? 0);
    if ($post_id < 1) {
        return false;
    }

    $raw = get_post($post_id);
    if (!$raw instanceof WP_Post || $raw->post_type !== 'nav_menu_item') {
        return false;
    }

    $item = wp_setup_nav_menu_item($raw);
    if (!$item instanceof WP_Post || !justccell_menu_item_is_product_tab($item)) {
        return false;
    }

    return $field;
}

add_filter('acf/fields/relationship/query/name=mega_products', 'justccell_mega_products_relationship_query', 10, 3);
add_filter('acf/prepare_field/key=field_jc_header_mega_products', 'justccell_prepare_mega_products_menu_field');

add_action('admin_head-nav-menus.php', static function (): void {
    ?>
    <style>
        /* Optional theme field — keep it below core WordPress menu item controls. */
        .menu-item-settings .acf-fields {
            margin-top: 12px;
            padding-top: 12px;
            border-top: 1px solid #dcdcde;
        }
    </style>
    <?php
});

add_action('admin_footer-nav-menus.php', static function (): void {
    if (!current_user_can('edit_theme_options')) {
        return;
    }
    echo '<div class="notice notice-info" style="margin:12px 0 0;"><p>';
    echo esc_html__(
        'Tip: drag a menu item slightly right to nest it under a parent. Product-card mega menus appear automatically when submenu items are Product categories (add them from the Product categories panel on the left).',
        'justccell'
    );
    echo '</p></div>';
});
