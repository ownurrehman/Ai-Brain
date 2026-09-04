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
 *
 * @param array<string, mixed> $item
 * @return array{image:string,tagline:string,capacity:string}
 */
function justccell_catalog_card_meta(array $item): array
{
    $slug   = (string) ($item['slug'] ?? '');
    $woo_id = (int) ($item['woo_id'] ?? 0);
    if ($woo_id > 0 && function_exists('get_field')) {
        $tagline  = (string) get_field('clone_card_tagline', $woo_id);
        $capacity = (string) get_field('clone_card_capacity', $woo_id);
        $card_id  = 0;
        if (function_exists('wc_get_product')) {
            $woo_product = wc_get_product($woo_id);
            if ($woo_product instanceof WC_Product) {
                $card_id = (int) $woo_product->get_image_id();
            }
        }
        $listing  = justccell_listing_card_copy($slug);
        if ($tagline === '') {
            $tagline = $listing['tagline'];
        }
        if ($capacity === '') {
            $capacity = $listing['capacity'];
        }
        if ($tagline === '' || $capacity === '') {
            $explore = justccell_catalog_explore_meta($item);
            if ($tagline === '') {
                $tagline = $explore['blurb'];
            }
            if ($capacity === '') {
                $capacity = $explore['capacity'];
            }
        }
        return [
            'image'     => '',
            'image_id'  => $card_id > 0 ? $card_id : (int) ($item['image_id'] ?? 0),
            'tagline'   => $tagline,
            'capacity'  => $capacity,
        ];
    }

    $map = [
        'mini-tank'         => ['image' => 'public_uploads_images_20240507_80564d119e791271bb317cc91dd74828.png', 'tagline' => 'Ultra Compact All-In-One Vaporizer', 'capacity' => '0.5ml/1ml'],
        'voca'              => ['image' => 'public_uploads_images_20230619_2220afff8c98c5d8aff0e4c8de7231ca.png', 'tagline' => 'Dual Air-Vent All-In-One Device', 'capacity' => '0.5ml/1ml'],
        'flexcell'          => ['image' => 'public_uploads_images_20230427_d5eeaac89ee9cd98d1553c812aafafa9.png', 'tagline' => 'Highly Customizable Dual Air-Vent All-In-One Device', 'capacity' => '0.5ml/1ml/1.5ml/2ml'],
        'ds0103'            => ['image' => 'public_uploads_images_20220411_02a7ac3130ea94305d68e695e6f15a05.png', 'tagline' => 'Tiny but Mighty Snap-fit All-In-One Devices', 'capacity' => '0.3ml/0.5ml/1.0ml'],
        'skye-ii'           => ['image' => 'public_uploads_images_20220610_925639bf0942124107cf47abea46e7e8.png', 'tagline' => 'Bringing Classic to the Next Level', 'capacity' => '0.5ml/1.0ml'],
        'listo'             => ['image' => 'public_uploads_images_20211021_bac5746b86f414971619a28f9abb839f.png', 'tagline' => '1ml Large Oil Tank.', 'capacity' => '1.0 ml'],
        'rosin-bar'         => ['image' => 'public_uploads_images_20250102_5409e46e60179e2e1054c72da4423a8a.png', 'tagline' => '100% Rosin-Ready', 'capacity' => '0.5ml'],
        'vision-box-elite'  => ['image' => 'public_uploads_images_20250224_18df35dc43dd89bc2e223e33f9668c99.png', 'tagline' => 'Engineered with Solventless-Specific HeRo Heating Technology', 'capacity' => '0.5ml/1ml'],
        'flexcell-pro'      => ['image' => 'public_uploads_images_20230608_7d6c447ae5d01f54c5d215742277e72f.png', 'tagline' => 'Clog-Free Dual Air-Vent All-In-One Device', 'capacity' => '0.5ml/1ml'],
        'voca-pro'          => ['image' => 'public_uploads_images_20240812_61c733c4d0c3397a6faf5017f5a3a21b.png', 'tagline' => 'Clog-Free Dual Air-Vent All-In-One Device', 'capacity' => '0.5ml/1ml'],
        'blanc'             => ['image' => 'public_uploads_images_20240116_a60dfad82bbff7ac268915d20bd4c163.png', 'tagline' => 'Full Ceramic All-In-One Device', 'capacity' => '0.3ml/0.5ml/1ml'],
        'slym'              => ['image' => 'public_uploads_images_20230213_3933986251799de2a685e4063737e2cb.png', 'tagline' => 'Thinnest Yet to Date at 6.7mm', 'capacity' => '0.3ml/0.5ml'],
        'flexcell-x'        => ['image' => 'public_uploads_images_20241230_c0ffa7353cd0b19ce7e90745444078ab.png', 'tagline' => 'Clog-Free, All-Oil-Capable All-In-One Device', 'capacity' => '0.5ml/1ml/2ml'],
        'tank'              => ['image' => 'public_uploads_images_20240507_622e6cebbbb7055185e806fd2b593268.png', 'tagline' => 'Large Oil Capacity All-In-One Vaporizer', 'capacity' => '1ml/2ml/3ml'],
        'eco-star'          => ['image' => 'public_uploads_images_20250207_effe61ef54aebd0e7fc85ebcc86ee2cd.png', 'tagline' => 'Earth-Conscious All-In-One Vaporizer', 'capacity' => '0.5ml/1ml'],
        'vision-box'        => ['image' => 'public_uploads_images_20250619_35f2b2bbb34411585f5c2a25c6d07dc7.png', 'tagline' => 'Smart AIO Vaporizer', 'capacity' => '0.5ml/1ml'],
        'voca-pro-max'      => ['image' => 'public_uploads_images_20240411_48dfb8f5f3fe776c66eb24357e275171.png', 'tagline' => 'All-In-One Vaporizer with All Oil Compatibility', 'capacity' => '0.5ml/1ml'],
        'voca-max'          => ['image' => 'public_uploads_images_20240110_d0c2dd6c5bd9d77bc3da9d8fb23a65b5.png', 'tagline' => 'Dual Air-Vent All-In-One Device', 'capacity' => '0.5ml/1ml'],
    ];

    $extra   = $map[$slug] ?? [];
    $listing = justccell_listing_card_copy($slug);

    $image = (string) ($extra['image'] ?? $item['image'] ?? '');
    if ($image !== '' && justccell_media_id($image) < 1) {
        $image = (string) ($item['image'] ?? '');
    }

    $tagline = (string) ($extra['tagline'] ?? '');
    if ($tagline === '') {
        $tagline = $listing['tagline'];
    }
    if ($tagline === '') {
        $explore = justccell_catalog_explore_meta($item);
        $tagline = $explore['blurb'];
    }

    $capacity = (string) ($extra['capacity'] ?? '');
    if ($capacity === '') {
        $capacity = $listing['capacity'];
    }
    if ($capacity === '') {
        $capacity = justccell_catalog_explore_meta($item)['capacity'];
    }

    return [
        'image'    => $image,
        'image_id' => 0,
        'tagline'  => $tagline,
        'capacity' => $capacity,
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
        $desk_id = is_array($desk) ? (int) ($desk['ID'] ?? $desk['id'] ?? 0) : (int) $desk;
        $mob_id  = is_array($mob) ? (int) ($mob['ID'] ?? $mob['id'] ?? 0) : (int) $mob;
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
 * @return list<array{id:int,url:string,alt:string,key:string}>
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
            foreach ($rows as $row) {
                if (!is_array($row)) {
                    continue;
                }
                $img = $row['image'] ?? null;
                $id  = is_array($img) ? (int) ($img['ID'] ?? $img['id'] ?? 0) : (int) $img;
                if ($id < 1) {
                    continue;
                }
                $slides[] = [
                    'id'  => $id,
                    'url' => (string) ($row['url'] ?? ''),
                    'alt' => (string) ($row['alt'] ?? ''),
                    'key' => '',
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
        (function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : home_url('/justccell-3-0/')),
        justccell_inquiry_url(),
    ];
    foreach ((array) ($keys['banners'] ?? []) as $i => $file) {
        justccell_ensure_media_url((string) $file);
        $id = justccell_media_id((string) $file);
        if ($id < 1) {
            continue;
        }
        $slides[] = [
            'id'  => $id,
            'url' => (string) ($links[$i] ?? justccell_inquiry_url()),
            'alt' => '',
            'key' => (string) $file,
        ];
    }
    return $slides;
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
        (function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : home_url('/justccell-3-0/')),
        justccell_inquiry_url(),
    ];
    foreach ((array) ($keys['banners'] ?? []) as $i => $file) {
        justccell_ensure_media_url((string) $file);
        $id = justccell_media_id((string) $file);
        if ($id < 1) {
            continue;
        }
        $slides[] = [
            'image' => $id,
            'url'   => (string) ($links[$i] ?? ''),
            'alt'   => sprintf(__('Homepage banner %d', 'justccell'), $i + 1),
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
}, 70);
