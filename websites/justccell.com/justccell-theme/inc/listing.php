<?php
/**
 * Category listing heroes, ACF page editors, and card copy.
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
 * @return array<string, array{heading:string,lede:string,desktop:string,mobile:string}>
 */
function justccell_listing_defaults(): array
{
    return [
        'all-in-ones' => [
            'heading'  => __('All-In-Ones', 'justccell'),
            'lede'     => __('Discover Justccell’s complete portfolio of disposable all-in-one vaporizers designed to work with different types of cannabis oils, including distillates, live resins, live rosins, liquid diamonds, and more!', 'justccell'),
            'desktop'  => 'public_uploads_images_20240507_0188c7f34e9da8061e66317b7b5fc6e8.jpg',
            'mobile'   => 'public_uploads_images_20240507_acbb0860249690d9cf844ae7e60be197.jpg',
        ],
        'cartridge' => [
            'heading'  => __('Cartridges', 'justccell'),
            'lede'     => __('Explore Justccell’s assortment of proprietary cartridges designed to unlock the full flavor profiles from your cannabis extracts and deliver seamless, uninterrupted cannabis vaporization experiences in every inhale.', 'justccell'),
            'desktop'  => 'public_uploads_images_20240507_7164789476ea15b6391a56997b3b70b5.jpg',
            'mobile'   => 'public_uploads_images_20240507_7164789476ea15b6391a56997b3b70b5.jpg',
        ],
        'pod-system' => [
            'heading'  => __('Pod Systems', 'justccell'),
            'lede'     => __('Stealthier and lighter compared to traditional 510 thread vapes, Justccell pod vapes are irresistible beauties with ergonomic and grip-friendly designs.', 'justccell'),
            'desktop'  => 'public_uploads_images_20240507_99f14a79122c8c2ccd23c8c9357fb9d6.jpg',
            'mobile'   => 'public_uploads_images_20240507_99f14a79122c8c2ccd23c8c9357fb9d6.jpg',
        ],
        'battery' => [
            'heading'  => __('510 Batteries', 'justccell'),
            'lede'     => __('Find out what Justccell batteries have to offer. Explore our assortment of ultra-safe batteries that pass all industry standards. Discover advanced features including temperature adjustability, battery status LED indication, preheating, and more.', 'justccell'),
            'desktop'  => 'public_uploads_images_20240507_61b5e3a568faae185d721499f7b3bb6e.jpg',
            'mobile'   => 'public_uploads_images_20240507_61b5e3a568faae185d721499f7b3bb6e.jpg',
        ],
    ];
}

/**
 * Listing card overlay (name + tagline + cyan capacity).
 * Copy comes from Specs: first non-technical line + Tank volume value.
 *
 * @param array<string, mixed> $item
 * @return array{image:string,image_id:int,tagline:string,capacity:string}
 */
function justccell_catalog_card_meta(array $item): array
{
    $woo_id  = (int) ($item['woo_id'] ?? 0);
    $card_id = (int) ($item['image_id'] ?? 0);
    if ($woo_id > 0 && function_exists('wc_get_product')) {
        $woo_product = wc_get_product($woo_id);
        if ($woo_product instanceof WC_Product) {
            $img = (int) $woo_product->get_image_id();
            if ($img > 0) {
                $card_id = $img;
            }
        }
    }

    $copy = function_exists('justccell_catalog_card_copy_from_specs')
        ? justccell_catalog_card_copy_from_specs(
            function_exists('justccell_product_spec_lines')
                ? justccell_product_spec_lines($item)
                : array_values((array) ($item['specs'] ?? []))
        )
        : ['tagline' => '', 'capacity' => ''];

    return [
        'image'    => (string) ($item['image'] ?? ''),
        'image_id' => $card_id,
        'tagline'  => (string) ($copy['tagline'] ?? ''),
        'capacity' => (string) ($copy['capacity'] ?? ''),
    ];
}

/**
 * @return list<array{q:string,a:string}>
 */
function justccell_listing_faq(string $category): array
{
    $page_id = justccell_listing_page_id($category);
    if ($page_id > 0 && function_exists('get_field')) {
        $rows = get_field('listing_faq', $page_id);
        if (is_array($rows) && $rows !== []) {
            $out = [];
            foreach ($rows as $row) {
                if (is_array($row) && ($row['q'] ?? '') !== '') {
                    $out[] = ['q' => (string) $row['q'], 'a' => (string) ($row['a'] ?? '')];
                }
            }
            if ($out !== []) {
                return $out;
            }
        }
    }

    if ($category !== 'all-in-ones') {
        return [];
    }

    return [
        [
            'q' => __('What is an all-in-one vape?', 'justccell'),
            'a' => __('All-in-one devices offer a simpler vaping experience. Each vaporizer comes with a pre-filled oil tank and a pre-charged internal battery. No need to recharge or refill them; once finished, start another one.', 'justccell'),
        ],
        [
            'q' => __('How to use Justccell all-in-one vapes?', 'justccell'),
            'a' => __('Justccell all-in-one products are activated by inhalation. You can use them directly without additional operations.', 'justccell'),
        ],
        [
            'q' => __('Can I reuse my all-in-one vape?', 'justccell'),
            'a' => __('No. All-in-one devices are filled on automated machines, so the design is not meant for hand filling. Discard the device once the oil is used up.', 'justccell'),
        ],
        [
            'q' => __('How long does an all-in-one vape last?', 'justccell'),
            'a' => __('It depends on draw length and oil type. Oil is vaporized at a steady rate of about 5mg every 3-second draw. A 0.5mL cartridge lasts approximately 100 draws at that rate.', 'justccell'),
        ],
        [
            'q' => __('Does Justccell fill all-in-one vapes?', 'justccell'),
            'a' => __('No. Justccell produces and sells hardware only. We do not produce, distribute, or sell any material filled in cartridges and all-in-one devices.', 'justccell'),
        ],
        [
            'q' => __('Do Justccell all-in-one vapes test for heavy metals?', 'justccell'),
            'a' => __('Yes. All-in-one devices are specified with heavy-metal testing, and the core component material is medical-grade 316L stainless steel (except DS0103 and DS0105).', 'justccell'),
        ],
        [
            'q' => __('Are Justccell all-in-one devices safe?', 'justccell'),
            'a' => __('Products are manufactured with high-quality materials under rigorous safety control and carry FDA, RoHS, FCC, CE, and UL certificates as applicable. Battery-containing products comply with UN38.3, PI967, and SP188 for lithium transport.', 'justccell'),
        ],
    ];
}

function justccell_listing_page_id(string $category): int
{
    $page = get_page_by_path($category);
    return $page instanceof WP_Post ? (int) $page->ID : 0;
}

/**
 * Hub catalog pages use the listing template but are not single-category URLs.
 *
 * @return list<string>
 */
function justccell_catalog_hub_page_slugs(): array
{
    return [];
}

/**
 * True when this page uses the catalog hub layout (listing template, not a category slug URL).
 */
function justccell_is_catalog_hub_page(?int $page_id = null): bool
{
    $page_id = $page_id ?? (int) get_queried_object_id();
    if ($page_id < 1) {
        return false;
    }
    $slug = (string) get_post_field('post_name', $page_id);
    if (array_key_exists($slug, justccell_product_category_labels())) {
        return false;
    }
    return function_exists('justccell_page_layout_kind')
        && justccell_page_layout_kind($page_id) === 'listing';
}

/**
 * Any catalog listing view (category rewrite or hub page with listing template).
 */
function justccell_is_catalog_view(): bool
{
    if (function_exists('justccell_is_catalog_clone') && justccell_is_catalog_clone()) {
        return true;
    }
    if (!is_page() || !function_exists('justccell_page_layout_kind')) {
        return false;
    }
    return justccell_page_layout_kind((int) get_queried_object_id()) === 'listing';
}

/**
 * Selected storefront category slugs from ACF (hub pages only).
 *
 * @return list<string>
 */
function justccell_listing_hub_categories(int $page_id): array
{
    if ($page_id < 1 || !function_exists('get_field')) {
        return [];
    }
    $terms = get_field('listing_catalog_categories', $page_id);
    if (!is_array($terms) || $terms === []) {
        return [];
    }
    $labels = justccell_product_category_labels();
    $slugs  = [];
    foreach ($terms as $term) {
        $slug = '';
        if ($term instanceof WP_Term) {
            $slug = (string) $term->slug;
        } elseif (is_numeric($term)) {
            $loaded = get_term((int) $term, 'product_cat');
            $slug   = $loaded instanceof WP_Term ? (string) $loaded->slug : '';
        } elseif (is_array($term)) {
            $slug = (string) ($term['slug'] ?? '');
        }
        if ($slug !== '' && array_key_exists($slug, $labels)) {
            $slugs[] = $slug;
        }
    }
    return array_values(array_unique($slugs));
}

/**
 * Categories to render for any catalog listing page.
 * Single-category URLs use the page slug; hub pages use ACF picks, else all storefront tabs.
 *
 * @return list<string>
 */
function justccell_listing_page_categories(int $page_id): array
{
    if ($page_id < 1) {
        return [];
    }
    $slug   = (string) get_post_field('post_name', $page_id);
    $labels = justccell_product_category_labels();
    if (array_key_exists($slug, $labels)) {
        return [$slug];
    }
    $picked = justccell_listing_hub_categories($page_id);
    if ($picked !== []) {
        return $picked;
    }
    return array_keys(justccell_storefront_category_labels());
}

/**
 * @return list<string>
 */
function justccell_listing_catalog_template_paths(): array
{
    return [
        'page-templates/justccell-listing.php',
        'page-templates/template-catalog.php',
    ];
}

/**
 * True when a page uses the Justccell Catalog template.
 */
function justccell_is_listing_catalog_page(int $page_id): bool
{
    if ($page_id < 1) {
        return false;
    }
    $template = (string) get_page_template_slug($page_id);
    if (in_array($template, justccell_listing_catalog_template_paths(), true)) {
        return true;
    }
    return function_exists('justccell_page_layout_kind')
        && justccell_page_layout_kind($page_id) === 'listing';
}

/**
 * All published pages using the catalog listing template.
 *
 * @return list<int>
 */
function justccell_listing_catalog_page_ids(): array
{
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    $clauses = [];
    foreach (justccell_listing_catalog_template_paths() as $template) {
        $clauses[] = [
            'key'   => '_wp_page_template',
            'value' => $template,
        ];
    }

    $ids = get_posts([
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'orderby'        => [
            'menu_order' => 'ASC',
            'title'      => 'ASC',
        ],
        'meta_query'     => [
            'relation' => 'OR',
            ...$clauses,
        ],
    ]);

    $cache = array_values(array_map('intval', $ids));
    return $cache;
}

/**
 * Page IDs for the catalog tab bar (ACF order preserved).
 *
 * @return list<int>
 */
function justccell_listing_catalog_tab_page_ids(int $page_id): array
{
    $fallback = justccell_listing_catalog_page_ids();
    if ($page_id < 1 || !function_exists('get_field')) {
        return $fallback;
    }

    $picked = get_field('listing_catalog_tab_pages', $page_id);
    if (!is_array($picked) || $picked === []) {
        return $fallback;
    }

    $ids = [];
    foreach ($picked as $item) {
        if ($item instanceof WP_Post) {
            $ids[] = (int) $item->ID;
        } elseif (is_numeric($item)) {
            $ids[] = (int) $item;
        }
    }

    $ids = array_values(array_unique(array_filter(
        $ids,
        static fn (int $id): bool => $id > 0 && justccell_is_listing_catalog_page($id)
    )));

    return $ids !== [] ? $ids : $fallback;
}

/**
 * Tab bar rows for catalog listing / hub pages.
 *
 * @param array{page_id?:int,active_slug?:string} $context
 * @return list<array{id:int,label:string,url:string,slug:string,is_active:bool}>
 */
function justccell_listing_catalog_tabs(int $page_id, array $context = []): array
{
    if ($page_id < 1) {
        return [];
    }

    $active_id   = (int) ($context['page_id'] ?? $page_id);
    $active_slug = (string) ($context['active_slug'] ?? '');
    if ($active_slug !== '' && function_exists('justccell_listing_page_id')) {
        $slug_page_id = justccell_listing_page_id($active_slug);
        if ($slug_page_id > 0) {
            $active_id = $slug_page_id;
        }
    }

    $tabs = [];
    foreach (justccell_listing_catalog_tab_page_ids($page_id) as $tab_page_id) {
        $tab_page_id = (int) $tab_page_id;
        $post        = get_post($tab_page_id);
        if (!$post instanceof WP_Post || $post->post_status !== 'publish') {
            continue;
        }

        $label = '';
        if (function_exists('get_field')) {
            $label = trim((string) get_field('listing_heading', $tab_page_id));
        }
        if ($label === '') {
            $label = (string) get_the_title($tab_page_id);
        }

        $url = get_permalink($tab_page_id);
        if (!is_string($url) || $url === '') {
            continue;
        }

        $tabs[] = [
            'id'        => $tab_page_id,
            'label'     => $label,
            'url'       => $url,
            'slug'      => (string) $post->post_name,
            'is_active' => $tab_page_id === $active_id,
        ];
    }

    return $tabs;
}

/**
 * Product category slugs shown when a catalog tab (page) is selected.
 *
 * @return list<string>
 */
function justccell_listing_catalog_panel_categories(int $tab_page_id): array
{
    if ($tab_page_id < 1) {
        return [];
    }

    $slug   = (string) get_post_field('post_name', $tab_page_id);
    $labels = justccell_product_category_labels();
    if (array_key_exists($slug, $labels)) {
        return [$slug];
    }

    return justccell_listing_page_categories($tab_page_id);
}

/**
 * Limit catalog tab picker to Justccell Catalog template pages.
 *
 * @param array<string, mixed> $args
 * @param array<string, mixed> $field
 * @return array<string, mixed>
 */
function justccell_listing_catalog_tab_pages_relationship_query(array $args, $field, $post_id): array
{
    unset($field, $post_id);

    $clauses = [];
    foreach (justccell_listing_catalog_template_paths() as $template) {
        $clauses[] = [
            'key'   => '_wp_page_template',
            'value' => $template,
        ];
    }

    $args['post_type']   = ['page'];
    $args['post_status'] = ['publish'];
    $args['meta_query']  = [
        'relation' => 'OR',
        ...$clauses,
    ];
    $args['orderby'] = [
        'menu_order' => 'ASC',
        'title'      => 'ASC',
    ];

    return $args;
}

add_filter(
    'acf/fields/relationship/query/name=listing_catalog_tab_pages',
    'justccell_listing_catalog_tab_pages_relationship_query',
    10,
    3
);
add_filter(
    'acf/fields/relationship/query/key=field_jc_listing_catalog_tab_pages',
    'justccell_listing_catalog_tab_pages_relationship_query',
    10,
    3
);

/**
 * Hero block for a catalog hub page (reads ACF from that page, not a category slug).
 *
 * @return array{heading:string,lede:string,page_id:int,slides:list<array{desktop_id:int,mobile_id:int,desktop_key:string,mobile_key:string,url:string}>}
 */
function justccell_listing_hero_for_page(int $page_id): array
{
    $heading = '';
    $lede    = '';
    if ($page_id > 0 && function_exists('get_field')) {
        $heading = (string) get_field('listing_heading', $page_id);
        $lede    = (string) get_field('listing_lede', $page_id);
    }
    if ($heading === '' && $page_id > 0) {
        $heading = (string) get_the_title($page_id);
    }
    return [
        'heading' => $heading,
        'lede'    => $lede,
        'page_id' => $page_id,
        'slides'  => justccell_listing_hero_slides_from_acf($page_id),
    ];
}

/**
 * FAQ rows for any listing or hub page.
 *
 * @return list<array{q:string,a:string}>
 */
function justccell_listing_faq_for_page(int $page_id): array
{
    if ($page_id < 1 || !function_exists('get_field')) {
        return [];
    }
    $rows = get_field('listing_faq', $page_id);
    if (!is_array($rows) || $rows === []) {
        return [];
    }
    $out = [];
    foreach ($rows as $row) {
        if (is_array($row) && ($row['q'] ?? '') !== '') {
            $out[] = ['q' => (string) $row['q'], 'a' => (string) ($row['a'] ?? '')];
        }
    }
    return $out;
}

/**
 * @return array<string, int>
 */
function justccell_ensure_listing_pages(): array
{
    $created = [];
    foreach (justccell_listing_defaults() as $slug => $row) {
        $page = get_page_by_path($slug);
        if (!$page instanceof WP_Post) {
            $id = wp_insert_post([
                'post_title'   => $row['heading'],
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_content' => '',
            ]);
            if (!is_int($id) || $id < 1) {
                continue;
            }
            $page = get_post($id);
        }
        if (!$page instanceof WP_Post) {
            continue;
        }
        $created[$slug] = (int) $page->ID;
        if (function_exists('justccell_assign_page_layout')) {
            justccell_assign_page_layout((int) $page->ID, 'listing');
        }
    }
    return $created;
}

/**
 * @return list<array{desktop_id:int,mobile_id:int,desktop_key:string,mobile_key:string,url:string}>
 */
function justccell_listing_hero_slides_from_acf(int $page_id): array
{
    if ($page_id < 1 || !function_exists('get_field')) {
        return [];
    }
    $rows = get_field('listing_hero_slides', $page_id);
    if (!is_array($rows)) {
        return [];
    }
    $slides = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $desk = $row['desktop'] ?? null;
        $mob  = $row['mobile'] ?? $desk;
        $desk_id = function_exists('justccell_acf_to_attachment_id')
            ? justccell_acf_to_attachment_id($desk)
            : (is_array($desk) ? (int) ($desk['ID'] ?? $desk['id'] ?? 0) : (int) $desk);
        $mob_id  = function_exists('justccell_acf_to_attachment_id')
            ? justccell_acf_to_attachment_id($mob)
            : (is_array($mob) ? (int) ($mob['ID'] ?? $mob['id'] ?? 0) : (int) $mob);
        if ($desk_id < 1) {
            continue;
        }
        $slides[] = [
            'desktop_id'  => $desk_id,
            'mobile_id'   => $mob_id > 0 ? $mob_id : $desk_id,
            'desktop_key' => '',
            'mobile_key'  => '',
            'url'         => (string) ($row['url'] ?? ''),
        ];
    }
    return $slides;
}

/**
 * @return array{heading:string,lede:string,page_id:int,slides:list<array{desktop_id:int,mobile_id:int,desktop_key:string,mobile_key:string,url:string}>}
 */
function justccell_listing_hero(string $category): array
{
    $defaults = justccell_listing_defaults()[$category] ?? [
        'heading' => $category,
        'lede'    => '',
        'desktop' => '',
        'mobile'  => '',
    ];
    $page_id = justccell_listing_page_id($category);
    $heading = $defaults['heading'];
    $lede    = $defaults['lede'];
    if ($page_id > 0 && function_exists('get_field')) {
        $custom_heading = (string) get_field('listing_heading', $page_id);
        $custom_lede    = (string) get_field('listing_lede', $page_id);
        if ($custom_heading !== '') {
            $heading = $custom_heading;
        }
        if ($custom_lede !== '') {
            $lede = $custom_lede;
        }
    }

    $slides = justccell_listing_hero_slides_from_acf($page_id);
    if ($slides === [] && $page_id > 0 && function_exists('justccell_seed_listing_hero_fields')) {
        justccell_seed_listing_hero_fields($category);
        $slides = justccell_listing_hero_slides_from_acf($page_id);
    }
    if ($slides === []) {
        $desk_key = (string) $defaults['desktop'];
        $mob_key  = (string) $defaults['mobile'];
        justccell_ensure_media_files(array_values(array_filter([$desk_key, $mob_key])));
        $desk_id = justccell_media_id($desk_key);
        $mob_id  = justccell_media_id($mob_key);
        if ($desk_id > 0) {
            $slides[] = [
                'desktop_id'  => $desk_id,
                'mobile_id'   => $mob_id > 0 ? $mob_id : $desk_id,
                'desktop_key' => $desk_key,
                'mobile_key'  => $mob_key,
                'url'         => '',
            ];
        }
    }

    return [
        'heading' => $heading,
        'lede'    => $lede,
        'page_id' => $page_id,
        'slides'  => $slides,
    ];
}

/**
 * @return list<array{id:int,desktop_id:int,mobile_id:int,url:string,alt:string,key:string}>
 */
function justccell_home_hero_slides(): array
{
    $front = function_exists('justccell_home_content_page_id')
        ? justccell_home_content_page_id()
        : (int) get_option('page_on_front');
    $slides = [];
    if ($front > 0 && function_exists('get_field')) {
        $rows = get_field('home_hero_slides', $front);
        if (is_array($rows)) {
            foreach ($rows as $i => $row) {
                if (!is_array($row)) {
                    continue;
                }
                $desk_id = function_exists('justccell_acf_to_attachment_id')
                    ? justccell_acf_to_attachment_id($row['image'] ?? 0)
                    : justccell_slide_image_id($row['image'] ?? 0);
                if ($desk_id < 1) {
                    continue;
                }
                $mob_id = function_exists('justccell_acf_to_attachment_id')
                    ? justccell_acf_to_attachment_id($row['mobile'] ?? 0)
                    : justccell_slide_image_id($row['mobile'] ?? 0);
                if ($mob_id < 1) {
                    $mob_file = (string) (justccell_home_mobile_banner_files()[$i] ?? '');
                    if ($mob_file !== '') {
                        $mob_id = justccell_import_theme_home_image($mob_file);
                    }
                }
                $slides[] = [
                    'id'         => $desk_id,
                    'desktop_id' => $desk_id,
                    'mobile_id'  => $mob_id > 0 ? $mob_id : $desk_id,
                    'url'        => (string) ($row['url'] ?? ''),
                    'alt'        => (string) ($row['alt'] ?? ''),
                    'key'        => '',
                ];
            }
        }
    }

    if ($slides !== []) {
        return $slides;
    }

    $keys = justccell_home_asset_keys();
    $links = [
        function_exists('justccell_product_url') ? justccell_product_url('tank') : justccell_inquiry_url(),
        justccell_category_url('all-in-ones'),
        (function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : home_url('/cell-3-0/')),
        justccell_inquiry_url(),
    ];
    $mobile_keys = (array) ($keys['banners_mobile'] ?? []);
    foreach ((array) ($keys['banners'] ?? []) as $i => $file) {
        justccell_ensure_media_url((string) $file);
        $id = justccell_media_id((string) $file);
        if ($id < 1) {
            continue;
        }
        $mob_key = (string) ($mobile_keys[$i] ?? '');
        if ($mob_key !== '') {
            justccell_ensure_media_url($mob_key);
        }
        $mob_id = $mob_key !== '' ? justccell_media_id($mob_key) : 0;
        $slides[] = [
            'id'         => $id,
            'desktop_id' => $id,
            'mobile_id'  => $mob_id > 0 ? $mob_id : $id,
            'url'        => (string) ($links[$i] ?? justccell_inquiry_url()),
            'alt'        => '',
            'key'        => (string) $file,
        ];
    }
    return $slides;
}

/**
 * @return list<string>
 */
function justccell_home_mobile_banner_files(): array
{
    $keys = justccell_home_asset_keys();
    $files = [];
    foreach ((array) ($keys['banners_mobile'] ?? []) as $file) {
        $files[] = (string) $file;
    }
    return $files;
}

/**
 * Import a homepage mobile crop from the theme pack into Media Library (once per file).
 */
function justccell_import_theme_home_image(string $filename): int
{
    $filename = basename(str_replace('\\', '/', $filename));
    if ($filename === '' || !preg_match('/^justccell-home-hero-mobile-[1-4]\.(jpg|png)$/', $filename)) {
        return 0;
    }
    $existing = justccell_media_id($filename);
    if ($existing > 0) {
        return $existing;
    }
    if (function_exists('justccell_attachment_id_by_basename')) {
        $by_name = justccell_attachment_id_by_basename($filename);
        if ($by_name > 0) {
            update_post_meta($by_name, '_justccell_ref', $filename);
            $map = get_option('justccell_media_map', []);
            if (!is_array($map)) {
                $map = [];
            }
            $map[$filename] = $by_name;
            update_option('justccell_media_map', $map, false);
            return $by_name;
        }
    }
    $source = JUSTCCELL_DIR . '/assets/img/home/' . $filename;
    if (!is_readable($source)) {
        return 0;
    }
    if (!function_exists('media_handle_sideload')) {
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/media.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';
    }
    $tmp = wp_tempnam($filename);
    if (!is_string($tmp) || $tmp === '' || !copy($source, $tmp)) {
        return 0;
    }
    $id = media_handle_sideload(
        [
            'name'     => $filename,
            'tmp_name' => $tmp,
        ],
        0
    );
    if (is_wp_error($id)) {
        if (is_string($tmp) && $tmp !== '' && is_file($tmp)) {
            wp_delete_file($tmp);
        }
        return 0;
    }
    $id = (int) $id;
    if ($id > 0) {
        update_post_meta($id, '_justccell_ref', $filename);
        $map = get_option('justccell_media_map', []);
        if (!is_array($map)) {
            $map = [];
        }
        $map[$filename] = $id;
        update_option('justccell_media_map', $map, false);
    }
    return $id;
}

function justccell_seed_home_hero_mobile_271(): void
{
    if (get_option('justccell_home_hero_mobile_271') === '1') {
        return;
    }
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    $front = function_exists('justccell_home_content_page_id')
        ? justccell_home_content_page_id()
        : (int) get_option('page_on_front');
    if ($front < 1) {
        return;
    }
    $rows = get_field('home_hero_slides', $front);
    if (!is_array($rows) || $rows === []) {
        return;
    }
    $files = justccell_home_mobile_banner_files();
    $changed = false;
    $complete = true;
    foreach ($rows as $i => $row) {
        if (!is_array($row)) {
            continue;
        }
        $mob = function_exists('justccell_acf_to_attachment_id')
            ? justccell_acf_to_attachment_id($row['mobile'] ?? 0)
            : justccell_slide_image_id($row['mobile'] ?? 0);
        if ($mob > 0) {
            continue;
        }
        $file = (string) ($files[$i] ?? '');
        if ($file === '') {
            continue;
        }
        $id = justccell_import_theme_home_image($file);
        if ($id < 1) {
            $complete = false;
            continue;
        }
        $rows[$i]['mobile'] = $id;
        $changed = true;
    }
    if ($changed) {
        update_field('home_hero_slides', $rows, $front);
    }
    if ($complete) {
        update_option('justccell_home_hero_mobile_271', '1', false);
    }
}

function justccell_seed_listing_hero_fields(string $category): void
{
    if (!function_exists('update_field')) {
        return;
    }
    $page_id = justccell_listing_page_id($category);
    $defaults = justccell_listing_defaults()[$category] ?? null;
    if ($page_id < 1 || $defaults === null) {
        return;
    }
    if ((string) get_field('listing_heading', $page_id) === '') {
        update_field('listing_heading', $defaults['heading'], $page_id);
    }
    if ((string) get_field('listing_lede', $page_id) === '') {
        update_field('listing_lede', $defaults['lede'], $page_id);
    }
    $existing = get_field('listing_hero_slides', $page_id);
    if (is_array($existing) && $existing !== []) {
        return;
    }
    justccell_ensure_media_files([$defaults['desktop'], $defaults['mobile']]);
    $desk = justccell_media_id($defaults['desktop']);
    $mob  = justccell_media_id($defaults['mobile']);
    if ($desk < 1) {
        return;
    }
    update_field('listing_hero_slides', [
        [
            'desktop' => $desk,
            'mobile'  => $mob > 0 ? $mob : $desk,
            'url'     => '',
        ],
    ], $page_id);
}

function justccell_seed_home_hero_fields(): void
{
    if (!function_exists('update_field')) {
        return;
    }
    $front = (int) get_option('page_on_front');
    if ($front < 1) {
        return;
    }
    $existing = get_field('home_hero_slides', $front);
    if (is_array($existing) && $existing !== []) {
        return;
    }
    $slides = [];
    $keys = justccell_home_asset_keys();
    $links = [
        function_exists('justccell_product_url') ? justccell_product_url('tank') : justccell_inquiry_url(),
        justccell_category_url('all-in-ones'),
        (function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : home_url('/cell-3-0/')),
        justccell_inquiry_url(),
    ];
    foreach ((array) ($keys['banners'] ?? []) as $i => $file) {
        justccell_ensure_media_url((string) $file);
        $id = justccell_media_id((string) $file);
        if ($id < 1) {
            continue;
        }
        $mob_file = (string) (($keys['banners_mobile'] ?? [])[$i] ?? '');
        $mob_id = 0;
        if ($mob_file !== '') {
            $mob_id = justccell_import_theme_home_image($mob_file);
        }
        $slides[] = [
            'image'  => $id,
            'mobile' => $mob_id > 0 ? $mob_id : $id,
            'url'    => (string) ($links[$i] ?? ''),
            'alt'    => sprintf(__('Homepage banner %d', 'justccell'), $i + 1),
        ];
    }
    if ($slides !== []) {
        update_field('home_hero_slides', $slides, $front);
    }
}

add_action('init', static function (): void {
    if (wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    justccell_ensure_listing_pages();
}, 40);

add_action('wp', static function (): void {
    if (is_admin()) {
        return;
    }
    $cat = (string) get_query_var('justccell_listing');
    if ($cat !== '' && isset(justccell_listing_defaults()[$cat])) {
        justccell_seed_listing_hero_fields($cat);
        return;
    }
    if (is_front_page()) {
        justccell_seed_home_hero_fields();
        justccell_seed_home_hero_mobile_271();
    }
}, 6);

function justccell_slide_image_id($img): int
{
    if (is_array($img)) {
        return (int) ($img['ID'] ?? $img['id'] ?? 0);
    }
    return (int) $img;
}

function justccell_apply_client_home_047(): void
{
    if (get_option('justccell_client_home_047') === '1') {
        return;
    }
    if (!function_exists('update_field') || !function_exists('justccell_sideload_media_file')) {
        return;
    }

    update_option('justccell_hide_header_cta', '1', false);

    $front = (int) get_option('page_on_front');
    if ($front < 1) {
        return;
    }

    update_field('home_devices_heading', __('Devices crafted for flavour preservation', 'justccell'), $front);
    update_field('home_trusted_heading', __('Laser engraving', 'justccell'), $front);

    $eazie = justccell_sideload_media_file('justccell-home-banner-eazie-pro.jpg', false);
    $diama = justccell_sideload_media_file('justccell-home-banner-diama.jpg', false);
    if ($eazie < 1 || $diama < 1) {
        return;
    }

    $rows = get_field('home_hero_slides', $front);
    $rows = is_array($rows) ? $rows : [];
    $keep = [];
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $id = justccell_slide_image_id($row['image'] ?? 0);
        if ($id < 1) {
            continue;
        }
        $keep[] = [
            'image' => $id,
            'url'   => (string) ($row['url'] ?? ''),
            'alt'   => (string) ($row['alt'] ?? ''),
        ];
        if (count($keep) >= 2) {
            break;
        }
    }
    $keep[] = [
        'image' => $eazie,
        'url'   => justccell_inquiry_url('eazie-pro'),
        'alt'   => __('Eazie Pro', 'justccell'),
    ];
    $keep[] = [
        'image' => $diama,
        'url'   => justccell_inquiry_url('diama'),
        'alt'   => __('Diama', 'justccell'),
    ];
    update_field('home_hero_slides', $keep, $front);
    update_option('justccell_client_home_047', '1', false);
}

add_action('init', static function (): void {
    if (wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    justccell_apply_client_home_047();
    if (is_admin()) {
        justccell_seed_home_hero_mobile_271();
    }
}, 70);
