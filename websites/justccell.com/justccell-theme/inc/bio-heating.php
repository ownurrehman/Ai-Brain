<?php
/**
 * Justccell 3.0 Bio-Heating landing.
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
function justccell_bio_heating_sections(): array
{
    return [
        [
            'type'          => 'banner',
            'title'         => __('True-to-Source Flavor|— Pure & Smooth Every Hit.', 'justccell'),
            'image_desktop' => 'j3/justccell-j3-flavor-desktop.jpg',
            'image_mobile'  => 'j3/justccell-j3-flavor-mobile.png',
        ],
        [
            'type'          => 'split',
            'reverse'       => false,
            'heading'       => __('Industry First 3D Stomata Ceramic', 'justccell'),
            'copy'          => __('Golden Aperture matrix design ensures unidirectional oil flow, achieving supply-consumption equilibrium to maximize vaping efficiency and help prevent reheated oil and off-flavors, from start to finish.', 'justccell'),
            'image_desktop' => 'j3/justccell-j3-stomata-1-desktop.jpg',
            'image_mobile'  => 'j3/justccell-j3-stomata-1-mobile.png',
        ],
        [
            'type'          => 'split',
            'reverse'       => true,
            'heading'       => __('Industry First Nexus Film', 'justccell'),
            'copy'          => __('Reduces peak atomization temperature by 30% with uniform heating, functioning as an intelligent thermostat to smoothly vaporize every oil droplet—preserving terpenes and flavor profiles.', 'justccell'),
            'image_desktop' => 'j3/justccell-j3-nexus-1-desktop.jpg',
            'image_mobile'  => 'j3/justccell-j3-nexus-1-mobile.png',
        ],
        [
            'type'          => 'banner',
            'title'         => __('Pure Hit|Perfect Lift', 'justccell'),
            'image_desktop' => 'j3/justccell-j3-pure-desktop.jpg',
            'image_mobile'  => 'j3/justccell-j3-pure-mobile.png',
        ],
        [
            'type'          => 'split',
            'reverse'       => false,
            'heading'       => __('Industry First Nexus Film', 'justccell'),
            'copy'          => __('Low-temperature even-heating prevents charring, harshness and harmful substances at the source. Whether a sip or a strong rip, Nexus Film delivers pure clouds that are smooth and refreshing to inhale instantly.', 'justccell'),
            'image_desktop' => 'j3/justccell-j3-nexus-2-desktop.jpg',
            'image_mobile'  => 'j3/justccell-j3-nexus-2-mobile.png',
        ],
        [
            'type'          => 'banner',
            'title'         => __('Reliable Vaping, Every Time|Leak Resistant. Clog Resistant.', 'justccell'),
            'image_desktop' => 'j3/justccell-j3-reliable-desktop.jpg',
            'image_mobile'  => 'j3/justccell-j3-reliable-mobile.png',
        ],
        [
            'type'          => 'split',
            'reverse'       => false,
            'heading'       => __('Industry First 3D Stomata Ceramic', 'justccell'),
            'copy'          => __('Interconnected heating channels create a molecular highway for predictable oil penetration, helping prevent clogging and leakage.', 'justccell'),
            'image_desktop' => 'j3/justccell-j3-stomata-2-desktop.jpg',
            'image_mobile'  => 'j3/justccell-j3-stomata-2-mobile.png',
        ],
        [
            'type'          => 'split',
            'reverse'       => true,
            'heading'       => __('Industry First Nexus Film', 'justccell'),
            'copy'          => __('Pioneering mesh-interlock fusion structure prevents detachment, delivering consistent vaporization performance with industry-leading lifespan.', 'justccell'),
            'image_desktop' => 'j3/justccell-j3-nexus-3-desktop.jpg',
            'image_mobile'  => 'j3/justccell-j3-nexus-3-mobile.png',
        ],
    ];
}

/**
 * Justccell 3.0 catalog — own All-In-Ones / Cartridges / Pod Systems, not the main Products mega.
 *
 * @return list<array{key:string,heading:string,category:string,slugs:list<string>,names:array<string,string>}>
 */
function justccell_j3_product_groups_defaults(): array
{
    return [
        [
            'key'      => 'all-in-ones',
            'heading'  => __('All-In-Ones', 'justccell'),
            'category' => 'all-in-ones',
            'slugs'    => ['gembar', 'flo', 'airone', 'blade'],
            'names'    => [
                'gembar' => 'GemBar',
                'flo'    => 'Flo',
                'airone' => 'AirOne',
                'blade'  => 'Blade',
            ],
        ],
        [
            'key'      => 'cartridge',
            'heading'  => __('Cartridges', 'justccell'),
            'category' => 'cartridge',
            'slugs'    => ['vita', 'kera'],
            'names'    => [
                'vita' => 'Vita',
                'kera' => 'Kera',
            ],
        ],
        [
            'key'      => 'pod-system',
            'heading'  => __('Pod Systems', 'justccell'),
            'category' => 'pod-system',
            'slugs'    => ['eazie-pro', 'eazie-pod'],
            'names'    => [
                'eazie-pro' => 'Eazie Pro',
                'eazie-pod' => 'Eazie Pod',
            ],
        ],
    ];
}

/**
 * @return array<string, string>
 */
function justccell_j3_product_names(): array
{
    $names = [];
    foreach (justccell_j3_product_groups_defaults() as $group) {
        foreach ($group['names'] as $slug => $name) {
            $names[$slug] = $name;
        }
    }
    return $names;
}

/**
 * @return list<string>
 */
function justccell_j3_product_slugs(): array
{
    return array_keys(justccell_j3_product_names());
}

function justccell_j3_anchor(string $key): string
{
    return 'j3-' . sanitize_title($key);
}

/**
 * ACF text for the Justccell 3.0 page, or a PHP default — never both on output.
 * Templates must read merged values from justccell_get_bio_heating_content() only.
 */
function justccell_j3_acf_string(int $post_id, string $field_name, string $default): string
{
    if ($post_id < 1 || !function_exists('get_field')) {
        return $default;
    }
    $value = get_field($field_name, $post_id);
    if (!is_string($value)) {
        return $default;
    }
    $value = trim($value);
    return $value !== '' ? $value : $default;
}

/**
 * One-time repair: Hermes saved "3.0 CCELL Bio Heating" into j3_cta_title instead of j3_products_title.
 */
function justccell_j3_repair_misplaced_acf_fields(): void
{
    if (get_option('justccell_j3_acf_repair_2026') === '1' || !function_exists('get_field')) {
        return;
    }

    $page = justccell_bio_page();
    if (!$page instanceof WP_Post) {
        return;
    }

    $post_id        = (int) $page->ID;
    $products_title = trim((string) get_field('j3_products_title', $post_id));
    $cta_title      = trim((string) get_field('j3_cta_title', $post_id));
    $target         = '3.0 CCELL Bio Heating';

    if ($products_title === '' && $cta_title === $target) {
        update_field('j3_products_title', $target, $post_id);
        if (function_exists('delete_field')) {
            delete_field('j3_cta_title', $post_id);
        } else {
            update_field('j3_cta_title', '', $post_id);
        }
    }

    update_option('justccell_j3_acf_repair_2026', '1', false);
}

add_action('init', 'justccell_j3_repair_misplaced_acf_fields', 21);

/**
 * Text defaults for the Justccell 3.0 page (matches public frontend).
 *
 * @return array<string, string>
 */
function justccell_j3_page_text_defaults(): array
{
    return [
        'kicker'         => __('Justccell 3.0', 'justccell'),
        'title_line'     => __('Heating Core', 'justccell'),
        'subtitle'       => __('The World’s Premier Ultra-Low|Temperature Heating Solution', 'justccell'),
        'products_title' => __('3.0 CCELL Bio Heating', 'justccell'),
        'cta_title'      => __('Get samples and quotes', 'justccell'),
        'cta_copy'       => __('Test your extracts with Justccell hardware. Samples typically ship in 3–15 days.', 'justccell'),
        'cta_label'      => __('Get samples & quotes', 'justccell'),
    ];
}

function justccell_j3_theme_image_attachment_id(string $theme_key): int
{
    if ($theme_key === '' || !function_exists('justccell_media_id')) {
        return 0;
    }
    return justccell_media_id($theme_key);
}

/**
 * Story sections as stored in ACF (same order/content as the live page).
 *
 * @return list<array<string, mixed>>
 */
function justccell_j3_sections_acf_rows(): array
{
    $rows = [];
    foreach (justccell_bio_heating_sections() as $section) {
        if (!is_array($section)) {
            continue;
        }
        $type = (string) ($section['type'] ?? 'split');
        $desk_key = (string) ($section['image_desktop'] ?? '');
        $mob_key  = (string) ($section['image_mobile'] ?? '');
        $rows[] = [
            'type'           => $type,
            'reverse'        => !empty($section['reverse']) ? 1 : 0,
            'title'          => (string) ($section['title'] ?? ''),
            'title_tag'      => 'h2',
            'heading'        => (string) ($section['heading'] ?? ''),
            'heading_tag'    => 'h3',
            'copy'           => (string) ($section['copy'] ?? ''),
            'image_desktop'  => justccell_j3_theme_image_attachment_id($desk_key),
            'image_mobile'   => justccell_j3_theme_image_attachment_id($mob_key),
        ];
    }
    return $rows;
}

/**
 * Populate page 201 ACF so the editor mirrors the public Justccell 3.0 page.
 */
function justccell_j3_seed_page_acf_content(): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }

    $seed_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
    if (get_option('justccell_j3_acf_seeded') === $seed_ver) {
        return;
    }

    $page = justccell_bio_page();
    if (!$page instanceof WP_Post) {
        return;
    }

    $post_id  = (int) $page->ID;
    $defaults = justccell_j3_page_text_defaults();

    $text_map = [
        'j3_kicker'          => 'kicker',
        'j3_title_line'      => 'title_line',
        'j3_subtitle'        => 'subtitle',
        'j3_products_title'  => 'products_title',
        'j3_cta_title'       => 'cta_title',
        'j3_cta_copy'        => 'cta_copy',
        'j3_cta_label'       => 'cta_label',
    ];
    foreach ($text_map as $field => $key) {
        if (trim((string) get_field($field, $post_id)) === '') {
            update_field($field, $defaults[$key], $post_id);
        }
    }

    if (trim((string) get_field('j3_title_tag', $post_id)) === '') {
        update_field('j3_title_tag', 'h1', $post_id);
    }
    if (trim((string) get_field('j3_products_title_tag', $post_id)) === '') {
        update_field('j3_products_title_tag', 'h2', $post_id);
    }
    if (trim((string) get_field('j3_cta_title_tag', $post_id)) === '') {
        update_field('j3_cta_title_tag', 'h2', $post_id);
    }

    if ((int) get_field('j3_hero_desktop', $post_id) < 1) {
        $hero_desk = justccell_j3_theme_image_attachment_id('j3/justccell-j3-hero-desktop.jpg');
        if ($hero_desk > 0) {
            update_field('j3_hero_desktop', $hero_desk, $post_id);
        }
    }
    if ((int) get_field('j3_hero_mobile', $post_id) < 1) {
        $hero_mob = justccell_j3_theme_image_attachment_id('j3/justccell-j3-hero-mobile.jpg');
        if ($hero_mob > 0) {
            update_field('j3_hero_mobile', $hero_mob, $post_id);
        }
    }

    $sections = get_field('j3_sections', $post_id);
    if (!is_array($sections) || $sections === []) {
        update_field('j3_sections', justccell_j3_sections_acf_rows(), $post_id);
    }

    update_field('j3_product_groups', justccell_j3_groups_acf_rows(), $post_id);

    if (function_exists('delete_field')) {
        delete_field('j3_product_slugs', $post_id);
    }

    update_option('justccell_j3_acf_seeded', $seed_ver, false);
}

add_action('init', 'justccell_j3_seed_page_acf_content', 22);

/**
 * @param mixed $rel
 * @return list<int>
 */
function justccell_j3_relationship_ids($rel): array
{
    $ids = [];
    if (!is_array($rel)) {
        return $ids;
    }
    foreach ($rel as $row) {
        if (is_numeric($row)) {
            $ids[] = (int) $row;
        } elseif ($row instanceof WP_Post) {
            $ids[] = (int) $row->ID;
        } elseif (is_array($row) && isset($row['ID'])) {
            $ids[] = (int) $row['ID'];
        }
    }
    return array_values(array_filter($ids));
}

/**
 * @return array{name:string,slug:string,category:string,image:string,image_id:int,woo_id:int,specs:list<string>}
 */
function justccell_j3_item_from_defaults(string $slug, string $category = 'all-in-ones'): array
{
    $names = justccell_j3_product_names();
    return [
        'name'     => $names[$slug] ?? ucwords(str_replace('-', ' ', $slug)),
        'slug'     => $slug,
        'category' => $category,
        'image'    => '',
        'image_id' => 0,
        'woo_id'   => 0,
        'specs'    => [],
    ];
}

/**
 * Resolve a 3.0 SKU from Woo (including 3.0-only products) or the default name list.
 *
 * @return array{name:string,slug:string,category:string,image:string,image_id:int,woo_id:int,specs:list<string>}
 */
function justccell_j3_item(string $slug, string $category = 'all-in-ones'): array
{
    $item = justccell_j3_item_from_defaults($slug, $category);
    if ($slug === '') {
        return $item;
    }

    if (function_exists('justccell_catalog_item')) {
        $catalog = justccell_catalog_item($slug);
        if (is_array($catalog)) {
            $item = array_merge($item, $catalog);
        }
    }

    if (!function_exists('justccell_woo_product_id_by_slug')) {
        return $item;
    }
    $id = justccell_woo_product_id_by_slug($slug);
    if ($id < 1 || !function_exists('wc_get_product')) {
        return $item;
    }
    $product = wc_get_product($id);
    if (!$product instanceof WC_Product) {
        return $item;
    }
    $thumb = (int) $product->get_image_id();
    $item['name']     = $product->get_name() !== '' ? $product->get_name() : $item['name'];
    $item['slug']     = $product->get_slug() !== '' ? $product->get_slug() : $slug;
    $item['image_id'] = $thumb;
    $item['woo_id']   = $id;
    return $item;
}

/**
 * @param list<int> $ids
 * @return list<array<string, mixed>>
 */
function justccell_j3_items_from_ids(array $ids, string $category): array
{
    $items = [];
    foreach ($ids as $pid) {
        $pid = (int) $pid;
        if ($pid < 1 || !function_exists('wc_get_product')) {
            continue;
        }
        if ($category !== '' && function_exists('justccell_product_in_storefront_category')
            && !justccell_product_in_storefront_category($pid, $category)
        ) {
            continue;
        }
        $product = wc_get_product($pid);
        if (!$product instanceof WC_Product) {
            continue;
        }
        $slug = $product->get_slug() !== '' ? $product->get_slug() : $product->get_sku();
        $items[] = justccell_j3_item($slug, $category);
    }
    return $items;
}

/**
 * Live published products in a Woo category (menu_order), for J3 tabs when ACF picks are empty.
 *
 * @return list<array<string, mixed>>
 */
function justccell_j3_items_from_category(string $category): array
{
    $items = [];
    if (!function_exists('justccell_catalog_by_category')) {
        return $items;
    }
    foreach (justccell_catalog_by_category()[$category] ?? [] as $row) {
        if (!is_array($row)) {
            continue;
        }
        $slug = (string) ($row['slug'] ?? '');
        if ($slug === '') {
            continue;
        }
        $item = justccell_j3_item($slug, $category);
        if ((int) ($item['woo_id'] ?? 0) < 1 && (string) ($item['name'] ?? '') === '') {
            continue;
        }
        $items[] = $item;
    }
    return $items;
}

/**
 * @return list<array{key:string,heading:string,anchor:string,items:list<array<string,mixed>>}>
 */
function justccell_j3_product_groups_for_page(int $post_id): array
{
    $defaults = justccell_j3_product_groups_defaults();
    $rows     = [];
    if ($post_id > 0 && function_exists('get_field')) {
        foreach ((array) get_field('j3_product_groups', $post_id) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $key = sanitize_title((string) ($row['key'] ?? ''));
            if ($key === '') {
                continue;
            }
            $heading = trim((string) ($row['heading'] ?? ''));
            $cat     = $key;
            foreach ($defaults as $def) {
                if ($def['key'] === $key) {
                    $cat = $def['category'];
                    if ($heading === '') {
                        $heading = $def['heading'];
                    }
                    break;
                }
            }
            $ids   = justccell_j3_relationship_ids($row['products'] ?? []);
            $items = $ids !== [] ? justccell_j3_items_from_ids($ids, $cat) : [];
            if ($items === []) {
                $items = justccell_j3_items_from_category($cat);
            }
            $rows[] = [
                'key'     => $key,
                'heading' => $heading !== '' ? $heading : $key,
                'anchor'  => justccell_j3_anchor($key),
                'items'   => $items,
            ];
        }
    }
    if ($rows !== []) {
        return $rows;
    }

    foreach ($defaults as $def) {
        $items = justccell_j3_items_from_category((string) $def['category']);
        if ($items === []) {
            foreach ($def['slugs'] as $slug) {
                $row = justccell_j3_item($slug, $def['category']);
                if ((int) ($row['woo_id'] ?? 0) < 1) {
                    continue;
                }
                $items[] = $row;
            }
        }
        $rows[] = [
            'key'     => $def['key'],
            'heading' => $def['heading'],
            'anchor'  => justccell_j3_anchor($def['key']),
            'items'   => $items,
        ];
    }
    return $rows;
}

function justccell_ensure_j3_woo_products(): void
{
    if (!function_exists('wp_insert_post')) {
        return;
    }
    foreach (justccell_j3_product_groups_defaults() as $group) {
        $order = 1;
        foreach ($group['slugs'] as $slug) {
            $name = $group['names'][$slug] ?? $slug;
            $id   = function_exists('justccell_woo_product_id_by_slug')
                ? justccell_woo_product_id_by_slug($slug)
                : 0;
            if ($id < 1) {
                $post_id = wp_insert_post([
                    'post_title'  => $name,
                    'post_name'   => $slug,
                    'post_status' => 'publish',
                    'post_type'   => 'product',
                    'menu_order'  => $order,
                ]);
                if (!is_int($post_id) || $post_id < 1) {
                    $order++;
                    continue;
                }
                $id = $post_id;
                wp_set_object_terms($id, 'simple', 'product_type');
                update_post_meta($id, '_sku', $slug);
                update_post_meta($id, '_virtual', 'yes');
                update_post_meta($id, '_sold_individually', 'yes');
                update_post_meta($id, '_manage_stock', 'no');
                update_post_meta($id, '_price', '');
                update_post_meta($id, '_regular_price', '');
                update_post_meta($id, '_catalog_visibility', 'visible');
            }
            update_post_meta($id, '_justccell_j3', '1');
            if (function_exists('update_field')) {
                update_field('clone_j3', 1, $id);
                update_field('clone_mega_featured', 0, $id);
            }
            if (taxonomy_exists('product_cat')) {
                $term = get_term_by('slug', $group['category'], 'product_cat');
                if ($term instanceof WP_Term) {
                    wp_set_object_terms($id, [(int) $term->term_id], 'product_cat', false);
                }
            }
            $order++;
        }
    }
}

/**
 * @return list<array<string, mixed>>
 */
function justccell_j3_groups_acf_rows(): array
{
    $rows = [];
    foreach (justccell_j3_product_groups_defaults() as $group) {
        $ids = [];
        foreach ($group['slugs'] as $slug) {
            $id = function_exists('justccell_woo_product_id_by_slug')
                ? justccell_woo_product_id_by_slug($slug)
                : 0;
            if ($id > 0) {
                $ids[] = $id;
            }
        }
        $rows[] = [
            'heading'  => $group['heading'],
            'key'      => $group['key'],
            'products' => $ids,
        ];
    }
    return $rows;
}

function justccell_apply_j3_categories_054(): void
{
    if (get_option('justccell_j3_categories_054') === '1') {
        return;
    }
    if (!post_type_exists('product')) {
        return;
    }
    if (get_option('justccell_catalog_cut_2026') === '1') {
        update_option('justccell_j3_categories_054', '1', false);
        return;
    }
    justccell_ensure_j3_woo_products();
    $ready = function_exists('justccell_woo_product_id_by_slug')
        && justccell_woo_product_id_by_slug('mixjoy') > 0;
    if (!$ready) {
        return;
    }
    if (function_exists('justccell_find_page_by_slug') && function_exists('update_field')) {
        $page = justccell_bio_page();
        if ($page instanceof WP_Post) {
            update_field('j3_product_groups', justccell_j3_groups_acf_rows(), (int) $page->ID);
            update_field('j3_product_slugs', [], (int) $page->ID);
        }
    }
    if (function_exists('justccell_flatten_j3_header_link')) {
        justccell_flatten_j3_header_link();
    }
    update_option('justccell_j3_categories_054', '1', false);
}

add_action('init', 'justccell_apply_j3_categories_054', 73);

/**
 * @param array<string, mixed> $item
 */
function justccell_j3_echo_product_card(array $item): void
{
    $meta = function_exists('justccell_catalog_card_meta')
        ? justccell_catalog_card_meta($item)
        : ['image_id' => (int) ($item['image_id'] ?? 0), 'image' => (string) ($item['image'] ?? '')];
    $url  = function_exists('justccell_item_url')
        ? justccell_item_url($item)
        : justccell_inquiry_url((string) ($item['slug'] ?? ''));
    ?>
    <a class="j3-products__card" href="<?php echo esc_url($url); ?>">
        <div class="j3-products__img">
            <?php
            if ((int) ($meta['image_id'] ?? 0) > 0) {
                echo wp_get_attachment_image((int) $meta['image_id'], 'medium', false, [
                    'alt'     => (string) ($item['name'] ?? ''),
                    'loading' => 'lazy',
                ]);
            } elseif (function_exists('justccell_media_img') && ($meta['image'] ?? $item['image'] ?? '') !== '') {
                echo justccell_media_img((string) ($meta['image'] ?? $item['image'] ?? ''), [
                    'alt'     => (string) ($item['name'] ?? ''),
                    'loading' => 'lazy',
                ]);
            }
            ?>
        </div>
        <p class="j3-products__name"><?php echo esc_html((string) ($item['name'] ?? '')); ?></p>
    </a>
    <?php
}

/**
 * @return array<string, mixed>
 */
function justccell_get_bio_heating_content(): array
{
    $post_id = (int) get_queried_object_id();
    $fallback_sections = justccell_bio_heating_sections();
    $defaults = justccell_j3_page_text_defaults();
    $defaults['hero_desktop'] = 'j3/justccell-j3-hero-desktop.jpg';
    $defaults['hero_mobile']  = 'j3/justccell-j3-hero-mobile.jpg';

    $field = static function (string $name) use ($post_id, $defaults): string {
        $key = $name;
        if (str_starts_with($name, 'j3_')) {
            $key = substr($name, 3);
        }
        $default = (string) ($defaults[$key] ?? '');
        return justccell_j3_acf_string($post_id, $name, $default);
    };
    $field_tag = static function (string $name, string $default) use ($post_id): string {
        return justccell_j3_acf_string($post_id, $name, $default);
    };
    $image_id = static function (string $name) use ($post_id): int {
        if ($post_id < 1 || !function_exists('get_field')) {
            return 0;
        }
        return justccell_acf_to_attachment_id(get_field($name, $post_id));
    };

    $sections = [];
    if ($post_id > 0 && function_exists('get_field')) {
        foreach ((array) get_field('j3_sections', $post_id) as $row) {
            if (!is_array($row)) {
                continue;
            }
            $type = (string) ($row['type'] ?? 'split');
            if ($type !== 'banner' && $type !== 'split') {
                continue;
            }
            $desk = justccell_acf_to_attachment_id($row['image_desktop'] ?? 0);
            $mob  = justccell_acf_to_attachment_id($row['image_mobile'] ?? 0);
            $sections[] = [
                'type'             => $type,
                'reverse'          => !empty($row['reverse']),
                'title'            => (string) ($row['title'] ?? ''),
                'title_tag'        => (string) ($row['title_tag'] ?? 'h2'),
                'heading'          => (string) ($row['heading'] ?? ''),
                'heading_tag'      => (string) ($row['heading_tag'] ?? 'h3'),
                'copy'             => (string) ($row['copy'] ?? ''),
                'image_desktop_id' => $desk,
                'image_mobile_id'  => $mob,
                'image_desktop'    => '',
                'image_mobile'     => '',
            ];
        }
    }
    if ($sections === []) {
        foreach ($fallback_sections as $row) {
            $sections[] = [
                'type'             => (string) ($row['type'] ?? 'split'),
                'reverse'          => !empty($row['reverse']),
                'title'            => (string) ($row['title'] ?? ''),
                'title_tag'        => 'h2',
                'heading'          => (string) ($row['heading'] ?? ''),
                'heading_tag'      => 'h3',
                'copy'             => (string) ($row['copy'] ?? ''),
                'image_desktop_id' => 0,
                'image_mobile_id'  => 0,
                'image_desktop'    => (string) ($row['image_desktop'] ?? ''),
                'image_mobile'     => (string) ($row['image_mobile'] ?? ''),
            ];
        }
    }

    $hero_desk = $image_id('j3_hero_desktop');
    $hero_mob  = $image_id('j3_hero_mobile');

    return [
        'kicker'             => $field('j3_kicker'),
        'title_line'         => $field('j3_title_line'),
        'title_tag'          => $field_tag('j3_title_tag', 'h1'),
        'subtitle'           => $field('j3_subtitle'),
        'hero_desktop_id'    => $hero_desk,
        'hero_mobile_id'     => $hero_mob,
        'hero_desktop'       => $hero_desk > 0 ? '' : $defaults['hero_desktop'],
        'hero_mobile'        => $hero_mob > 0 ? '' : $defaults['hero_mobile'],
        'sections'           => $sections,
        'products_title'     => $field('j3_products_title'),
        'products_title_tag' => $field_tag('j3_products_title_tag', 'h2'),
        'product_groups'     => justccell_j3_product_groups_for_page($post_id),
        'cta_title'          => $field('j3_cta_title'),
        'cta_title_tag'      => $field_tag('j3_cta_title_tag', 'h2'),
        'cta_copy'           => $field('j3_cta_copy'),
        'cta_label'          => $field('j3_cta_label'),
    ];
}

/**
 * @param array<string, string|int|bool|null> $attrs
 */
function justccell_j3_echo_lines(string $text, string $separator = '|'): void
{
    if ($text === '') {
        return;
    }

    $parts = array_values(array_filter(array_map('trim', explode($separator, $text))));
    if ($parts === []) {
        return;
    }

    $html = implode('<br>', array_map('esc_html', $parts));
    echo wp_kses($html, ['br' => []]);
}

/**
 * @param array<string, string|int|bool|null> $attrs
 */
function justccell_j3_echo_img_pair(
    int $desktop_id,
    string $desktop_key,
    int $mobile_id,
    string $mobile_key,
    string $class_base,
    string $alt,
    array $attrs = []
): void {
    justccell_j3_echo_img($desktop_id, $desktop_key, $class_base . ' j3-desk', $alt, $attrs);
    justccell_j3_echo_img($mobile_id, $mobile_key, $class_base . ' j3-mob', $alt, $attrs);
}

/**
 * @param array<string, string|int|bool|null> $attrs
 */
function justccell_j3_echo_img(int $id, string $key, string $class, string $alt, array $attrs = []): void
{
    if ($id < 1 && $key !== '') {
        justccell_ensure_media_url($key);
        $id = justccell_media_id($key);
    }
    if ($id < 1) {
        return;
    }

    echo wp_get_attachment_image($id, 'full', false, array_merge([
        'alt'     => $alt,
        'loading' => 'lazy',
        'class'   => $class,
    ], $attrs));
}
