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

        if (justccell_nav_item_is_j3($item)) {
            $tabs = justccell_nav_kids_are_product_tabs($kids)
                ? justccell_header_j3_tabs($kids)
                : justccell_header_j3_default_tabs();
            $out[] = [
                'type'  => 'products',
                'title' => $title,
                'url'   => $url,
                'tabs'  => $tabs,
            ];
            continue;
        }

        if (justccell_nav_kids_are_product_tabs($kids)) {
            $tabs = justccell_header_product_tabs($kids);
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
 * @return list<int>
 */
function justccell_header_menu_category_term_ids(WP_Post $item, string $field_name): array
{
    if (!function_exists('get_field') || $field_name === '') {
        return [];
    }

    $raw = get_field($field_name, (int) $item->ID);
    if (!is_array($raw) || $raw === []) {
        return [];
    }

    $ids = [];
    foreach ($raw as $row) {
        if ($row instanceof WP_Term) {
            $ids[] = (int) $row->term_id;
            continue;
        }
        if (is_numeric($row)) {
            $ids[] = (int) $row;
            continue;
        }
        if (is_array($row) && isset($row['term_id'])) {
            $ids[] = (int) $row['term_id'];
        }
    }

    return array_values(array_unique(array_filter($ids)));
}

/**
 * @return list<string>
 */
function justccell_header_menu_category_slugs(WP_Post $item, string $field_name): array
{
    $slugs = [];
    foreach (justccell_header_menu_category_term_ids($item, $field_name) as $term_id) {
        $term = get_term($term_id, 'product_cat');
        if ($term instanceof WP_Term && $term->slug !== '') {
            $slugs[] = (string) $term->slug;
        }
    }

    return array_values(array_unique($slugs));
}

/**
 * WooCommerce tax_query for mega menu auto-fill on one submenu row.
 *
 * @return list<array<string, mixed>>
 */
function justccell_mega_menu_tax_query(?WP_Post $menu_item, string $storefront_key): array
{
    if (!taxonomy_exists('product_cat')) {
        return [];
    }

    if (!$menu_item instanceof WP_Post) {
        if ($storefront_key === '') {
            return [];
        }

        return [[
            'taxonomy'         => 'product_cat',
            'field'            => 'slug',
            'terms'            => [$storefront_key],
            'include_children' => false,
        ]];
    }

    $include_slugs = justccell_header_menu_category_slugs($menu_item, 'mega_include_categories');
    $exclude_slugs = justccell_header_menu_category_slugs($menu_item, 'mega_exclude_categories');

    $tax_query = ['relation' => 'AND'];

    if ($include_slugs !== []) {
        foreach ($include_slugs as $slug) {
            $tax_query[] = [
                'taxonomy'         => 'product_cat',
                'field'            => 'slug',
                'terms'            => [$slug],
                'include_children' => false,
            ];
        }
    } elseif ($storefront_key !== '') {
        $tax_query[] = [
            'taxonomy'         => 'product_cat',
            'field'            => 'slug',
            'terms'            => [$storefront_key],
            'include_children' => false,
        ];
    }

    if ($exclude_slugs !== []) {
        $tax_query[] = [
            'taxonomy'         => 'product_cat',
            'field'            => 'slug',
            'terms'            => $exclude_slugs,
            'operator'         => 'NOT IN',
            'include_children' => false,
        ];
    }

    return count($tax_query) > 1 ? $tax_query : [];
}

/**
 * True when a published product belongs in this mega menu tab.
 */
function justccell_product_matches_mega_menu_filters(int $product_id, ?WP_Post $menu_item, string $storefront_key = ''): bool
{
    if ($product_id < 1 || get_post_type($product_id) !== 'product' || !taxonomy_exists('product_cat')) {
        return false;
    }

    if (!$menu_item instanceof WP_Post) {
        return $storefront_key === ''
            || !function_exists('justccell_product_in_storefront_category')
            || justccell_product_in_storefront_category($product_id, $storefront_key);
    }

    foreach (justccell_header_menu_category_slugs($menu_item, 'mega_include_categories') as $slug) {
        if (!has_term($slug, 'product_cat', $product_id)) {
            return false;
        }
    }

    if (justccell_header_menu_category_slugs($menu_item, 'mega_include_categories') === [] && $storefront_key !== '') {
        if (!function_exists('justccell_product_in_storefront_category')
            || !justccell_product_in_storefront_category($product_id, $storefront_key)
        ) {
            return false;
        }
    }

    foreach (justccell_header_menu_category_slugs($menu_item, 'mega_exclude_categories') as $slug) {
        if (has_term($slug, 'product_cat', $product_id)) {
            return false;
        }
    }

    return true;
}

/**
 * Featured → curated slugs → menu_order.
 *
 * @param list<int>           $product_ids
 * @param array<int, true>    $seen_pids
 * @return list<int>
 */
function justccell_mega_prioritize_product_ids(array $product_ids, string $storefront_key, int $limit, array $seen_pids = []): array
{
    $featured = [];
    $curated  = [];
    $rest     = [];

    $curated_slugs = function_exists('justccell_mega_featured')
        ? (justccell_mega_featured()[$storefront_key] ?? [])
        : [];

    foreach ($product_ids as $product_id) {
        $product_id = (int) $product_id;
        if ($product_id < 1 || isset($seen_pids[$product_id])) {
            continue;
        }
        if (function_exists('get_field') && (bool) get_field('clone_mega_featured', $product_id)) {
            $featured[] = $product_id;
            continue;
        }
        $slug = (string) get_post_field('post_name', $product_id);
        if ($slug !== '' && in_array($slug, $curated_slugs, true)) {
            $curated[] = $product_id;
            continue;
        }
        $rest[] = $product_id;
    }

    return array_slice(array_merge($featured, $curated, $rest), 0, $limit);
}

/**
 * @param list<int> $seen_pids
 * @return list<int>
 */
function justccell_mega_menu_auto_product_ids(?WP_Post $menu_item, string $storefront_key, int $limit, array $seen_pids = []): array
{
    $limit = max(1, min(8, $limit));
    $seen  = [];
    foreach ($seen_pids as $pid) {
        $seen[(int) $pid] = true;
    }

    if (function_exists('wc_get_products')) {
        $tax_query = justccell_mega_menu_tax_query($menu_item, $storefront_key);
        if ($tax_query !== []) {
            $ids = wc_get_products([
                'status'    => 'publish',
                'limit'     => -1,
                'return'    => 'ids',
                'orderby'   => 'menu_order',
                'order'     => 'ASC',
                'tax_query' => $tax_query,
            ]);
            if (is_array($ids) && $ids !== []) {
                return justccell_mega_prioritize_product_ids($ids, $storefront_key, $limit, $seen);
            }
        }
    }

    $pool = justccell_catalog_by_category()[$storefront_key] ?? [];
    usort($pool, static function (array $a, array $b): int {
        return ((int) ($a['menu_order'] ?? 0)) <=> ((int) ($b['menu_order'] ?? 0));
    });

    $eligible = [];
    foreach ($pool as $item) {
        if (!is_array($item)) {
            continue;
        }
        $woo_id = (int) ($item['woo_id'] ?? 0);
        if ($woo_id < 1 || isset($seen[$woo_id])) {
            continue;
        }
        if (!justccell_product_matches_mega_menu_filters($woo_id, $menu_item, $storefront_key)) {
            continue;
        }
        $eligible[] = $woo_id;
    }

    return justccell_mega_prioritize_product_ids($eligible, $storefront_key, $limit, $seen);
}

/**
 * True when this nav row sits under the Products top-level item (not CCELL 3.0).
 */
function justccell_menu_item_under_products(WP_Post $item): bool
{
    if (justccell_menu_item_under_j3($item)) {
        return false;
    }

    $menu_id = (int) $item->menu_item_parent;
    $guard   = 0;
    while ($menu_id > 0 && $guard < 12) {
        ++$guard;
        $parent = get_post($menu_id);
        if (!$parent instanceof WP_Post) {
            break;
        }
        $parent_item = wp_setup_nav_menu_item($parent);
        if (!$parent_item instanceof WP_Post) {
            break;
        }
        if ((int) $parent_item->menu_item_parent === 0) {
            $title = strtolower(trim((string) $parent_item->title));

            return str_contains($title, 'product');
        }
        $menu_id = (int) $parent_item->menu_item_parent;
    }

    return false;
}

/**
 * One-time defaults: Products tabs exclude CCELL 3.0; CCELL 3.0 tabs require storefront + 3.0.
 */
function justccell_seed_header_menu_mega_category_filters(): void
{
    if (!function_exists('get_field') || !function_exists('update_field') || !taxonomy_exists('product_cat')) {
        return;
    }
    if (get_option('justccell_mega_menu_filters_seeded_v1') === '1') {
        return;
    }

    $locations = get_nav_menu_locations();
    $menu_id   = (int) ($locations['primary'] ?? 0);
    if ($menu_id < 1) {
        return;
    }

    $items = wp_get_nav_menu_items($menu_id);
    if (!is_array($items) || $items === []) {
        return;
    }

    $j3_term_ids = [];
    if (function_exists('justccell_j3_product_cat_slugs')) {
        foreach (justccell_j3_product_cat_slugs() as $slug) {
            $term = get_term_by('slug', $slug, 'product_cat');
            if ($term instanceof WP_Term) {
                $j3_term_ids[] = (int) $term->term_id;
            }
        }
    }
    $j3_term_ids = array_values(array_unique(array_filter($j3_term_ids)));

    foreach ($items as $raw) {
        if (!$raw instanceof WP_Post) {
            continue;
        }
        $item = wp_setup_nav_menu_item($raw);
        if (!$item instanceof WP_Post || !justccell_menu_item_is_product_tab($item)) {
            continue;
        }

        $storefront = justccell_category_key_from_menu_item($item);
        if ($storefront === '') {
            continue;
        }

        if (justccell_menu_item_under_j3($item)) {
            if (justccell_header_menu_category_term_ids($item, 'mega_include_categories') !== []) {
                continue;
            }
            $include = $j3_term_ids;
            $term    = get_term_by('slug', $storefront, 'product_cat');
            if ($term instanceof WP_Term) {
                $include[] = (int) $term->term_id;
            }
            $include = array_values(array_unique(array_filter($include)));
            if ($include !== []) {
                update_field('mega_include_categories', $include, (int) $item->ID);
            }
            continue;
        }

        if (justccell_menu_item_under_products($item)
            && justccell_header_menu_category_term_ids($item, 'mega_exclude_categories') === []
            && $j3_term_ids !== []
        ) {
            update_field('mega_exclude_categories', $j3_term_ids, (int) $item->ID);
        }
    }

    update_option('justccell_mega_menu_filters_seeded_v1', '1', false);
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

        $picked_ids = justccell_header_menu_product_ids($item);
        $items      = justccell_mega_cards_for_category($key, $picked_ids, $limit, $item);

        $tabs[] = [
            'key'   => $key,
            'label' => (string) $item->title,
            'url'   => (string) $item->url,
            'items' => $items,
        ];
    }

    return $tabs;
}

/**
 * @return list<int>
 */
function justccell_header_menu_product_ids(WP_Post $item): array
{
    if (!function_exists('get_field')) {
        return [];
    }

    $rel = get_field('mega_products', $item->ID);
    if (!is_array($rel) || $rel === []) {
        return [];
    }

    $ids = [];
    foreach ($rel as $row) {
        if (is_numeric($row)) {
            $ids[] = (int) $row;
        } elseif ($row instanceof WP_Post) {
            $ids[] = (int) $row->ID;
        } elseif (is_array($row) && isset($row['ID'])) {
            $ids[] = (int) $row['ID'];
        }
    }

    return array_values(array_unique(array_filter($ids)));
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
        $ids = justccell_header_menu_product_ids($item);
        $tabs[] = [
            'key'   => $key,
            'label' => (string) $item->title,
            'url'   => (string) $item->url,
            'items' => justccell_mega_cards_for_category($key, $ids, $limit, $item),
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
            'url'   => function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : home_url('/ccell-3-0/'),
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
 * True when this nav row sits under the CCELL 3.0 top-level item.
 */
function justccell_menu_item_under_j3(WP_Post $item): bool
{
    $menu_id = (int) $item->menu_item_parent;
    $guard   = 0;
    while ($menu_id > 0 && $guard < 12) {
        ++$guard;
        $parent = get_post($menu_id);
        if (!$parent instanceof WP_Post) {
            break;
        }
        $parent_item = wp_setup_nav_menu_item($parent);
        if ($parent_item instanceof WP_Post && justccell_nav_item_is_j3($parent_item)) {
            return true;
        }
        $menu_id = (int) $parent_item->menu_item_parent;
    }

    return false;
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

    $include_slugs = justccell_header_menu_category_slugs($item, 'mega_include_categories');
    $exclude_slugs = justccell_header_menu_category_slugs($item, 'mega_exclude_categories');

    $tax_query = ['relation' => 'AND'];

    if ($include_slugs !== []) {
        foreach ($include_slugs as $slug) {
            $tax_query[] = [
                'taxonomy'         => 'product_cat',
                'field'            => 'slug',
                'terms'            => [$slug],
                'include_children' => false,
            ];
        }
    } else {
        $tax_query[] = [
            'taxonomy'         => 'product_cat',
            'field'            => 'slug',
            'terms'            => [$key],
            'include_children' => false,
        ];
    }

    if ($exclude_slugs !== []) {
        $tax_query[] = [
            'taxonomy'         => 'product_cat',
            'field'            => 'slug',
            'terms'            => $exclude_slugs,
            'operator'         => 'NOT IN',
            'include_children' => false,
        ];
    }

    $args['tax_query'] = $tax_query;

    return $args;
}

/**
 * Only show mega menu fields on submenu rows that map to a WooCommerce category.
 *
 * @param array<string, mixed>|false $field
 * @return array<string, mixed>|false
 */
function justccell_prepare_mega_menu_item_acf_field($field)
{
    if (!is_array($field)) {
        return $field;
    }

    if (function_exists('justccell_acf_should_hide_field_in_ui') && !justccell_acf_should_hide_field_in_ui()) {
        return $field;
    }

    $item_id = function_exists('justccell_acf_nav_menu_item_id_from_field')
        ? justccell_acf_nav_menu_item_id_from_field($field)
        : (int) ($field['post_id'] ?? 0);

    if ($item_id < 1) {
        return $field;
    }

    $raw = get_post($item_id);
    if (!$raw instanceof WP_Post || $raw->post_type !== 'nav_menu_item') {
        return false;
    }

    if (function_exists('justccell_nav_menu_item_in_primary_menu')
        && !justccell_nav_menu_item_in_primary_menu($item_id)
    ) {
        return false;
    }

    $item = wp_setup_nav_menu_item($raw);
    if (!$item instanceof WP_Post || !justccell_menu_item_is_product_tab($item)) {
        return false;
    }

    return $field;
}

add_filter('acf/fields/relationship/query/name=mega_products', 'justccell_mega_products_relationship_query', 10, 3);
add_filter('acf/prepare_field/key=field_jc_header_mega_products', 'justccell_prepare_mega_menu_item_acf_field');
add_filter('acf/prepare_field/key=field_jc_header_mega_include_categories', 'justccell_prepare_mega_menu_item_acf_field');
add_filter('acf/prepare_field/key=field_jc_header_mega_exclude_categories', 'justccell_prepare_mega_menu_item_acf_field');
add_action('init', 'justccell_seed_header_menu_mega_category_filters', 41);

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
