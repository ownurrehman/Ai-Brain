<?php
/**
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);
if (!defined('ABSPATH')) { exit; }

function justccell_media_id(string $file): int
{
    if ($file === '') {
        return 0;
    }
    $map = get_option('justccell_media_map', []);
    if (!is_array($map)) {
        $map = [];
    }
    $id = (int) ($map[$file] ?? 0);
    if ($id > 0 && get_post_type($id) === 'attachment') {
        return $id;
    }
    $found = get_posts([
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_justccell_ref',
        'meta_value'     => $file,
        'no_found_rows'  => true,
    ]);
    if ($found === []) {
        return 0;
    }
    $id = (int) $found[0];
    $map[$file] = $id;
    update_option('justccell_media_map', $map, false);
    return $id;
}

function justccell_ref_uri(string $file): string
{
    if ($file === '') {
        return '';
    }
    $id = justccell_media_id($file);
    if ($id > 0) {
        $url = wp_get_attachment_url($id);
        if (is_string($url) && $url !== '') {
            return $url;
        }
    }
    return '';
}

function justccell_ensure_media_url(string $file): string
{
    if ($file === '') {
        return '';
    }
    $id = justccell_media_id($file);
    if ($id > 0) {
        $url = wp_get_attachment_url($id);
        if (is_string($url) && $url !== '') {
            return $url;
        }
    }
    return '';
}

/**
 * @param list<string> $files
 */
function justccell_ensure_media_files(array $files): void
{
    foreach ($files as $file) {
        $file = (string) $file;
        if ($file !== '') {
            justccell_ensure_media_url($file);
        }
    }
}

/**
 * Lookup-only. Theme pack sideload was removed after manual Media Library uploads.
 */
function justccell_sideload_media_file(string $key, bool $generate_meta = true): int
{
    unset($generate_meta);
    return justccell_media_id($key);
}

function justccell_media_source_file(string $key): string
{
    return justccell_media_id($key) > 0 ? $key : '';
}

/**
 * @param array<string, string|int|bool> $attrs
 */
function justccell_media_img(string $file, array $attrs = []): string
{
    if ($file === '') {
        return '';
    }
    justccell_ensure_media_url($file);
    $id = justccell_media_id($file);
    if ($id > 0) {
        $size = isset($attrs['size']) ? (string) $attrs['size'] : 'full';
        unset($attrs['size']);
        $clean = [];
        foreach ($attrs as $name => $value) {
            if ($value === null || $value === false || $value === '') {
                continue;
            }
            $clean[$name] = $value;
        }
        $html = wp_get_attachment_image($id, $size, false, $clean);
        return is_string($html) ? $html : '';
    }
    return '';
}

function justccell_catalog(): array {
    static $cache = null;
    if (is_array($cache)) {
        return $cache;
    }

    if (function_exists('justccell_catalog_from_woo')) {
        $woo = justccell_catalog_from_woo();
        if ($woo !== [] && get_option('justccell_cms_imported')) {
            $cache = $woo;
            return $cache;
        }
    }

    $cache = justccell_catalog_php();
    return $cache;
}

/**
 * Hardcoded catalog seed (fallback until CMS import).
 *
 * @return list<array{name:string,slug:string,category:string,image:string,specs:list<string>}>
 */
function justccell_catalog_php(): array {
    return [
        [
            'name' => 'Mini Tank',
            'slug' => 'mini-tank',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240428_ee2ff11ab19f0deb1c0df81ace03e2b1.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Battery capacity: 200mAh', 'Dimensions: 63H x 36W x 15D (mm)/2.5H x 1.4W x 0.6D (in)'],
        ],
        [
            'name' => 'Voca',
            'slug' => 'voca',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20230619_dcd39929a51284fedaf41b65d55e04a3.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'Battery Capacity: 280mAh', 'Dimensions: 79H x 36W x 13D (mm)/ 3.11H x 1.42W x 0.51D (in)'],
        ],
        [
            'name' => 'Flexcell',
            'slug' => 'flexcell',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20230809_d2bae5526a2bb9d592d0514f5dc86048.png',
            'specs' => ['Tank Volume: 0.5ml/1ml/1.5ml/2ml', 'Battery Capacity: 280mAh/300mAh', 'Dimensions: 109.3-113H x 22W x 10.5-11.6D (mm)/4.3-4.45H x 0.87W x 0.41-0.46D (in)'],
        ],
        [
            'name' => 'DS01 Series',
            'slug' => 'ds0103',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20220411_a61adfb13af0e49c7415e234fbebc2da.png',
            'specs' => ['Tank Volume: 0.3ml/0.5ml/1ml', 'Battery Capacity: 135-330mAh', 'Dimensions: φ0.41-0.42 x 3.67-4.59H (in) / φ10.5-10.6 x 93.3-116.7H (mm)'],
        ],
        [
            'name' => 'Skye II',
            'slug' => 'skye-ii',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20220610_3ce4d5d6ab46467aaf6eb1804ce21ce2.png',
            'specs' => ['Tank Volume: 0.5ml/1.0ml', 'Battery Capacity: 190mAh', 'Dimensions: φ11.0 x 104H (mm)'],
        ],
        [
            'name' => 'Listo',
            'slug' => 'listo',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20211116_bcaa68324cdd46b2a7d7b3ab674f20a6.png',
            'specs' => ['Tank Volume: 1.0ml', 'Battery Capacity: 350mAh', 'Dimensions: 97.8H x 22.3W x 10.8D (mm)'],
        ],
        [
            'name' => 'Rosin Bar',
            'slug' => 'rosin-bar',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240906_e31926981883d3b387b986f6439dbd34.png',
            'specs' => ['Tank Volume: 0.5ml', 'Battery capacity: 280mAh', 'Dimensions: 3.54H x 0.94W x 0.51D (in)/ 90H x 24W x 13D (mm)'],
        ],
        [
            'name' => 'Vision Box Elite',
            'slug' => 'vision-box-elite',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20250224_18df35dc43dd89bc2e223e33f9668c99.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Dimensions: 68.4H x 38W x 19D (mm)/2.69H x 1.5W x 0.75D (in)', 'Battery capacity: 200mAh'],
        ],
        [
            'name' => 'Flexcell Pro',
            'slug' => 'flexcell-pro',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20230608_9a0936d27c230c8ba32caecbc3c2bb71.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'Battery Capacity: 280mAh', 'Dimensions: 108.9H x 23W x 11.3D (mm) / 4.29H x 0.91W x 0.44D (in)'],
        ],
        [
            'name' => 'Voca Pro',
            'slug' => 'voca-pro',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240812_7675561cfaca8299b629ad704641e411.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'Battery Capacity: 280mAh', 'Dimensions: 76H x 36.1W x 13D (mm) / 2.99H x 1.42W x 0.51D (in)'],
        ],
        [
            'name' => 'Blanc',
            'slug' => 'blanc',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240116_725478ae46fbb9c2a2384701be78db7c.png',
            'specs' => ['Tank Volume: 0.3ml/ 0.5ml/ 1ml', 'Battery Capacity: 190mAh', 'Dimensions: φ0.42 x 4.27/4.62/4.93 (in) / φ10.6 x 108.4/117.4/125.23 (mm)'],
        ],
        [
            'name' => 'Slym',
            'slug' => 'slym',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20211117_787224a267f1244dfacf6bf0073971f6.png',
            'specs' => ['Tank Volume: 0.3ml/0.5ml', 'Battery Capacity: 280mAh/ 210mAh/ 280mAh/', '0.3ml & 0.5ml Dimensions：3.9H x 0.75W x 0.26D (in)/ 99H x 19W x 6.7D (mm)'],
        ],
        [
            'name' => 'Flexcell X',
            'slug' => 'flexcell-x',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20241230_e2641b979d7eb386edc0f83be01cbae3.png',
            'specs' => ['Tank Volume: 0.5ml/1ml/2ml', 'Battery Capacity: 280mAh', 'Dimensions: 103H x 22W x 11.6D (mm) / 4.06H x 0.87W x 0.46D (in)'],
        ],
        [
            'name' => 'Tank',
            'slug' => 'tank',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240507_036cfe495c3b090387a77086a1b8dca3.png',
            'specs' => ['Tank volume: 1ml/2ml/3ml', 'Battery capacity: 200mAh/280mAh', '1ml Dimensions: 71.94H x 41.35W x 16.98D (mm)/2.83H x 1.63W x 0.67D (in)'],
        ],
        [
            'name' => 'Eco Star',
            'slug' => 'eco-star',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240412_3c29e45f296ede5be2a07a6d5372b512.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Battery capacity: 180mAh', 'Dimensions: 89H x 22.1W x 16.6D (mm) / 3.5H x 0.87W x 0.65D (in)'],
        ],
        [
            'name' => 'Vision Box',
            'slug' => 'vision-box',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240801_01173010c908454e66cc9b16bebb7fb2.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Dimensions: 68.4H x 38W x 19D (mm)/2.69H x 1.5W x 0.75D (in)', 'Battery capacity: 200 mAh'],
        ],
        [
            'name' => 'Voca Pro Max',
            'slug' => 'voca-pro-max',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240412_d245be9856f594d3023b02948bf74ed9.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Battery capacity: 280mAh', 'Dimensions: 76H x 36.1W x 13D (mm) / 2.99H x 1.42W x 0.51D (in)'],
        ],
        [
            'name' => 'Voca Max',
            'slug' => 'voca-max',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240110_d0c2dd6c5bd9d77bc3da9d8fb23a65b5.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'Battery Capacity: 280mAh', 'Dimensions: 3.11H x 1.42W x 0.51D (in) / 79H x 36W x 13D (mm)'],
        ],
        [
            'name' => 'Ceramic-EVOMAX',
            'slug' => 'ceramic-evomax',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20240724_734bf793dcb89cb674cb5cce16b19238.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Dimensions: Φ0.43 x 2.12H/2.57H (in) / Φ11.0 x 53.9H/65.2H (mm)', 'Resistance: 1.7Ω'],
        ],
        [
            'name' => 'TH2-EVOMAX',
            'slug' => 'th2-evomax',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20240418_b1ade21fd8bd0408fad531233cddf223.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Dimensions: Φ0.41 x 2.04H/2.60H (in) / Φ10.5 x 51.8H/66.1H (mm)', 'Resistance: 1.7Ω'],
        ],
        [
            'name' => 'M6T-EVOMAX',
            'slug' => 'm6t-evomax',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20240418_6c79d92f65b7061065b41545507c6ea8.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Dimensions: Φ0.41 x 2.26H/2.67H (in) / Φ10.5 x 57.3H/67.8H (mm)', 'Resistance: 1.7Ω'],
        ],
        [
            'name' => 'TH2-SE',
            'slug' => 'th2-se',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20230630_c488539e011b14828229be1a0314705b.png',
            'specs' => ['Standard 510 thread', 'Tank Volume: 0.5ml/1ml/1.2ml', 'Dimensions: Φ10.5 x 52/62/66 (mm) / Φ0.41 x 2.05/2.44/2.59 (in)'],
        ],
        [
            'name' => 'M6T-SE',
            'slug' => 'm6t-se',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20230630_2325a47f2ac9556b1be4a5b51540d071.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'Dimensions: Φ10.5 x 57.4/67.9 (mm) / Φ0.41 x 2.26/2.67 (in)', 'Resistance: 1.4Ω'],
        ],
        [
            'name' => 'Luster Pro',
            'slug' => 'luster-pro',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20240401_8a3ca61a604eb24ae696e2c72137ea56.png',
            'specs' => ['Battery capacity: 350mAh', 'Tank volume: 0.5ml/1ml', 'Battery Dimensions: 3.58H x 0.83W x 0.43D (in)/91H x 21W x 11D (mm)'],
        ],
        [
            'name' => 'Dart-X',
            'slug' => 'dart-x',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20211018_5507b894e18a559d796b616d51a7becc.png',
            'specs' => ['Battery Capacity: 480mAh', 'Dimensions: 75.5H × 28.8W× 11.9D (mm)', 'Full Metal Housing with Soft Touch Finish'],
        ],
        [
            'name' => 'Dart',
            'slug' => 'dart',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20211201_1eb9e3590d6aa6205402bc6e9667a45c.png',
            'specs' => ['Battery Capacity: 480mAh', 'Dimensions: 72.5H × 28.7W× 12.5D (mm)', 'Comfortable Grip with Matte Finish'],
        ],
        [
            'name' => 'BELLOS',
            'slug' => 'bellos',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20211203_6b54e1059409c9aaf1f44534ad6f39d6.png',
            'specs' => ['Battery Capacity: 320mAh', 'Dimensions: 67.6H × 30.0W × 12.6D (mm)', 'Constant Power Output'],
        ],
        [
            'name' => 'Luster Pro Pod',
            'slug' => 'luster-pro-pod',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20240401_ef297cb2efe28dd0b270c8d36d9c0b97.png',
            'specs' => ['Tank volume: 0.5ml/1ml', 'Dimensions: 33H/40H × 26.9W × 12.4D (mm)', 'Food-grade thermoplastic mouthpiece'],
        ],
        [
            'name' => 'Dart Series Pod',
            'slug' => 'dart-pod',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20220106_a84f72c8e45d325427f3cd2c5d18b34d.png',
            'specs' => ['Tank Volume: 0.5ml / 1.0ml', 'Dimensions: 32.7 / 37.8H × 28.5W × 11.3D (mm)', 'Plastic Housing'],
        ],
        [
            'name' => 'Bellos Pod',
            'slug' => 'bellos-pod',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20211026_b4f61aff03bdcf647688c52d3f2ca770.png',
            'specs' => ['Tank Volume: 0.5ml / 1.0ml', 'Dimensions: 33.0 / 40.2H × 26.9W × 12.6 / 12.4D (mm)', 'Plastic Housing'],
        ],
        [
            'name' => 'Stylo',
            'slug' => 'stylo',
            'category' => 'battery',
            'image' => 'public_uploads_images_20250407_8b49096ddef040e60e43f740198f0535.png',
            'specs' => ['Battery capacity: 500mAh', 'Dimensions: 57H x 42.4W x 20D (mm)/2.24H x 1.67W x 0.79D (in)', '15-second stable temp heating'],
        ],
        [
            'name' => 'Fino',
            'slug' => 'fino',
            'category' => 'battery',
            'image' => 'public_uploads_images_20231205_31286447c2d9458d16fad562a211279b.png',
            'specs' => ['Battery capacity: 190mAh (Battery) + 1000mAh (Dock)', 'Battery dimensions: 55.4H x 25W x 13.35D (mm) / 2.18H x 0.98W x 0.53D (in)', 'Dock dimensions: 68.9H x 50.6W x 20D (mm) / 2.71H x 1.99W x 0.78D (in)'],
        ],
        [
            'name' => 'Sandwave',
            'slug' => 'sandwave',
            'category' => 'battery',
            'image' => 'public_uploads_images_20230706_0c5f2f7fbc38369b8e5c4f72eb30b78a.png',
            'specs' => ['Battery Capacity: 400mAh', 'Dimensions: 60H x 38.2W x 16.2D (mm)/ 2.36H x 1.5W x 0.64D (in)', '3 temperature settings via slide switch (2.8/3.2/3.6V)'],
        ],
        [
            'name' => 'Go Stik',
            'slug' => 'go-stik',
            'category' => 'battery',
            'image' => 'public_uploads_images_20240701_263818593ec9453f7f3e362cfaffef15.png',
            'specs' => ['Standard 510 thread', 'Battery Capacity: 280mAh', 'Dimensions: 91.5H x 20W x 15.2D (mm)/ 3.6H x 0.79W x 0.6D (in)'],
        ],
        [
            'name' => 'Palm Pro',
            'slug' => 'palm-pro',
            'category' => 'battery',
            'image' => 'public_uploads_images_20240701_304af696635982ced6c7f3206765ddb8.png',
            'specs' => ['Battery Capacity: 500mAh', 'Dimensions: 2.26H x 1.65W x 0.53D (in) / 57.5H x 42W x 13.55D (mm)', 'Standard 510 thread'],
        ],
        [
            'name' => 'M3B Plus',
            'slug' => 'm3b-plus',
            'category' => 'battery',
            'image' => 'public_uploads_images_20240227_779ef15f4f2830ec3f9cc3efdc8096d0.png',
            'specs' => ['Battery capacity: 550mAh', 'Dimensions: φ0.55 x 3.48 (in) / φ14 x 88.3H (mm)', '3 voltage settings (2.8/3.2/3.6V)'],
        ],
        [
            'name' => 'M3 Plus',
            'slug' => 'm3-plus',
            'category' => 'battery',
            'image' => 'public_uploads_images_20221024_be63e17dba69181af1d98932954a4c09.png',
            'specs' => ['Battery capacity: 350mAh', 'Dimensions: Φ12 x 86.4H (mm)', 'Body: Stainless steel'],
        ],
    ];
}

function justccell_catalog_by_category(): array {
    $out = ['all-in-ones' => [], 'cartridge' => [], 'pod-system' => [], 'battery' => [], 'equipment' => []];
    foreach (justccell_catalog() as $item) {
        $cat = (string) ($item['category'] ?? '');
        if (!isset($out[$cat])) {
            $out[$cat] = [];
        }
        $out[$cat][] = $item;
    }
    return $out;
}

/**
 * Homepage product rails — live published products per WooCommerce category.
 * Order follows Woo menu_order. Specs come from product clone_specs / catalog.
 *
 * @return array<string, list<array<string, mixed>>>
 */
function justccell_home_rails(): array
{
    $by_cat = justccell_catalog_by_category();
    $out    = [];
    $labels = function_exists('justccell_storefront_category_labels')
        ? justccell_storefront_category_labels()
        : [
            'all-in-ones' => '',
            'cartridge'   => '',
            'pod-system'  => '',
            'battery'     => '',
        ];

    foreach (array_keys($labels) as $cat) {
        $items = [];
        foreach ($by_cat[$cat] ?? [] as $item) {
            if (!is_array($item)) {
                continue;
            }
            if (function_exists('justccell_catalog_card_specs')) {
                $item['specs'] = justccell_catalog_card_specs($item, 3);
            }
            $items[] = $item;
        }
        $out[$cat] = $items;
    }

    return $out;
}

/**
 * @deprecated Kept for older callers; homepage no longer uses curated slug lists.
 *
 * @return array<string, list<string>>
 */
function justccell_home_rail_slugs(): array
{
    $out = [];
    foreach (justccell_home_rails() as $cat => $items) {
        $out[$cat] = array_values(array_filter(array_map(
            static fn ($item): string => is_array($item) ? (string) ($item['slug'] ?? '') : '',
            $items
        )));
    }
    return $out;
}

/**
 * @deprecated Prefer product clone_specs via justccell_catalog_card_specs().
 *
 * @return array<string, list<string>>
 */
function justccell_home_card_blurbs(): array
{
    return [];
}

/**
 * Catalog listing grid: short marketing line + cyan capacity (not PDP dimensions).
 *
 * @return array{tagline:string,capacity:string}
 */
function justccell_listing_card_copy(string $slug): array
{
    $item = function_exists('justccell_catalog_item') ? justccell_catalog_item($slug) : null;
    if (!is_array($item)) {
        return ['tagline' => '', 'capacity' => ''];
    }
    $specs = function_exists('justccell_catalog_card_specs')
        ? justccell_catalog_card_specs($item, 2)
        : array_slice(array_values($item['specs'] ?? []), 0, 2);
    $first  = trim((string) ($specs[0] ?? ''));
    $second = trim((string) ($specs[1] ?? ''));
    if ($first === '') {
        return ['tagline' => '', 'capacity' => ''];
    }
    if (stripos($first, 'Tank Volume:') === 0) {
        return [
            'capacity' => trim(substr($first, strlen('Tank Volume:'))),
            'tagline'  => $second,
        ];
    }
    return [
        'tagline'  => $first,
        'capacity' => $second,
    ];
}

/**
 * Category tiles + product rail for the 404 page. Uses live catalogue images.
 *
 * @return array{categories:list<array<string, mixed>>,products:list<array<string, mixed>>}
 */
function justccell_404_showcase(): array
{
    $labels = function_exists('justccell_storefront_category_labels')
        ? justccell_storefront_category_labels()
        : justccell_product_category_labels();
    $blurbs = [
        'all-in-ones' => __('All-oil disposables for distillate through live rosin.', 'justccell'),
        'cartridge'   => __('510 ceramic cartridges specified for filling lines.', 'justccell'),
        'pod-system'  => __('Closed pods — lighter than a 510 pen.', 'justccell'),
        'battery'     => __('510-thread batteries and boxes.', 'justccell'),
    ];
    $by_cat = justccell_catalog_by_category();

    $categories = [];
    $products   = [];
    $seen       = [];

    foreach ($labels as $slug => $label) {
        $items = array_values(array_filter(
            $by_cat[$slug] ?? [],
            static fn ($item): bool => is_array($item)
        ));
        $lead = null;
        foreach ($items as $item) {
            if ((int) ($item['image_id'] ?? 0) > 0 || (string) ($item['image'] ?? '') !== '') {
                $lead = $item;
                break;
            }
        }
        if ($lead === null && $items !== []) {
            $lead = $items[0];
        }
        $categories[] = [
            'slug'  => $slug,
            'label' => $label,
            'blurb' => $blurbs[$slug] ?? '',
            'url'   => justccell_category_url($slug),
            'item'  => is_array($lead) ? $lead : null,
        ];
        foreach (array_slice($items, 0, 3) as $item) {
            $key = (string) ($item['slug'] ?? '');
            if ($key === '' || isset($seen[$key])) {
                continue;
            }
            $seen[$key] = true;
            $products[] = $item;
        }
    }

    return [
        'categories' => $categories,
        'products'   => $products,
    ];
}

/**
 * Catalog listing groups. Only slugs we actually sell.
 *
 * @return list<array{title:string,items:list<array<string, mixed>>}>
 */
function justccell_catalog_groups(string $category): array
{
    $items = justccell_catalog_by_category()[$category] ?? [];
    if ($items === []) {
        return [];
    }

    return [
        [
            'title' => '',
            'copy'  => '',
            'items' => $items,
        ],
    ];
}

/**
 * Explore More cards: one description line + a short cyan capacity.
 *
 * @param array{specs?:list<string>} $item
 * @return array{blurb:string,capacity:string}
 */
function justccell_catalog_explore_meta(array $item): array
{
    $specs    = array_values($item['specs'] ?? []);
    $first    = (string) ($specs[0] ?? '');
    $second   = (string) ($specs[1] ?? '');
    $capacity = $first;
    $blurb    = $second;
    if (stripos($first, 'Tank Volume:') === 0) {
        $capacity = trim(substr($first, strlen('Tank Volume:')));
    }
    if ($blurb === '') {
        $blurb    = $first;
        $capacity = '';
    }
    return [
        'blurb'    => $blurb,
        'capacity' => $capacity,
    ];
}

/**
 * @return array{name:string,slug:string,category:string,image:string,specs:list<string>}|null
 */
function justccell_catalog_item(string $slug): ?array
{
    foreach (justccell_catalog() as $item) {
        if ($item['slug'] === $slug) {
            return $item;
        }
    }
    return null;
}

/**
 * Print catalog/product card image from attachment id or media key.
 *
 * @param array<string, mixed> $attrs
 */
function justccell_echo_catalog_image(array $item, array $attrs = []): void
{
    $id = (int) ($item['image_id'] ?? 0);
    if ($id > 0) {
        echo wp_get_attachment_image($id, isset($attrs['size']) ? (string) $attrs['size'] : 'full', false, $attrs);
        return;
    }
    echo justccell_media_img((string) ($item['image'] ?? ''), $attrs);
}

/**
 * Product clone if it exists, otherwise the inquiry form with SKU.
 *
 * @param array{name:string,slug:string,category:string,image:string,specs:list<string>} $item
 */
function justccell_item_url(array $item): string
{
    if (function_exists('justccell_has_product_page') && justccell_has_product_page($item['slug'])) {
        return justccell_product_url($item['slug']);
    }
    return justccell_inquiry_url($item['slug']);
}

function justccell_category_url(string $slug): string
{
    return home_url('/' . trim($slug, '/') . '/');
}

/**
 * @return array<string, string|list<string>>
 */
function justccell_home_asset_keys(): array
{
    return [
        'arrow'    => 'public_static_modules_cms_img_home14.png',
        'quote_bg' => 'public_static_modules_cms_img_home18.jpg',
        'fill'     => 'public_uploads_images_20250225_08b6cc13898889e8407ea3790ae31cad.png',
        'trusted'  => 'public_uploads_images_20250225_2be1257b82984d06383bd05570e5a8be.png',
        'premium'  => 'public_uploads_images_20250225_e9e2853eb498b95706a72f332df0a1a1.png',
        'cust1'    => 'public_uploads_images_20250225_4520819030305c0ed4bde75255a8d6ad.png',
        'cust2'    => 'public_uploads_images_20250225_03e34b11c8bd28052b8a1a5d877ebbe9.png',
        'cust3'    => 'public_uploads_images_20250225_64e6456cdd881d697853f502f353d4a8.png',
        'cust4'    => 'public_uploads_images_20250225_6c0bc8408fa97536916c3f93d7b4cb21.png',
        'banners'  => [
            'public_uploads_images_20250926_6d26d199e7d5f7c457ad85f05c69f8e4.jpg',
            'public_uploads_images_20250409_47d88dbb6d565e229709aa76a51fc82f.jpg',
            'public_uploads_images_20250228_35607022bf9c0261440de779466b67df.jpg',
            'public_uploads_images_20250624_586896b2422c482af3eb027b9c112ad5.jpg',
        ],
    ];
}

function justccell_home_assets(): array
{
    $out = [];
    foreach (justccell_home_asset_keys() as $key => $value) {
        if (is_array($value)) {
            $out[$key] = array_values(array_filter(array_map('justccell_ensure_media_url', $value)));
            continue;
        }
        $out[$key] = justccell_ensure_media_url($value);
    }
    $logo = justccell_brand_logo_url();
    if ($logo !== '') {
        $out['logo'] = $logo;
    }
    return $out;
}

function justccell_attachment_id_by_basename(string $basename): int
{
    global $wpdb;
    $basename = basename(str_replace('\\', '/', $basename));
    if ($basename === '') {
        return 0;
    }
    $id = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT post_id FROM {$wpdb->postmeta}
         WHERE meta_key = '_wp_attached_file' AND meta_value LIKE %s
         ORDER BY post_id DESC LIMIT 1",
        '%' . $wpdb->esc_like($basename)
    ));
    return $id > 0 && get_post_type($id) === 'attachment' ? $id : 0;
}

/**
 * Transparent horizontal PNGs first. The older Just-CCELL-logo-line.png and the
 * JPEGs carry a baked white background, so they are fallbacks only.
 *
 * @return list<string>
 */
function justccell_brand_logo_basenames(): array
{
    return [
        'Justccell.com-logo-horizontal.png',
        'Justccell.com-logo-horizontal-small.png',
        'Just-CCELL-logo-line.png',
        'JustCCELL_horizontal_by_3Devices.jpg',
        'JustCCELL_by_3Devices.jpg',
    ];
}

function justccell_brand_logo_id(): int
{
    foreach (justccell_brand_logo_basenames() as $file) {
        $id = justccell_attachment_id_by_basename($file);
        if ($id > 0) {
            return $id;
        }
    }
    return 0;
}

/**
 * Square/circle mark, favicon and schema use only.
 *
 * @return list<string>
 */
function justccell_brand_icon_basenames(): array
{
    return [
        'Justccell.com-circle-logo.png',
        'Justccell.com-logo-square.png',
        'Just-CCELL-round.png',
    ];
}

function justccell_brand_icon_id(): int
{
    foreach (justccell_brand_icon_basenames() as $file) {
        $id = justccell_attachment_id_by_basename($file);
        if ($id > 0) {
            return $id;
        }
    }
    return 0;
}

function justccell_brand_logo_url(): string
{
    $id = justccell_brand_logo_id();
    if ($id < 1) {
        return '';
    }
    $url = wp_get_attachment_url($id);
    return is_string($url) ? $url : '';
}

function justccell_assign_brand_assets(): void
{
    $logo_id = justccell_brand_logo_id();
    if ($logo_id > 0) {
        $current = (int) get_theme_mod('custom_logo');
        $current_file = $current > 0 ? basename((string) get_post_meta($current, '_wp_attached_file', true)) : '';
        // Promote to the preferred asset when the current one is also ours, so a
        // newer transparent upload supersedes an older white-background file.
        // A logo we do not ship was chosen by hand, so leave it alone.
        $is_ours = $current_file === '' || in_array($current_file, justccell_brand_logo_basenames(), true);
        if ($current !== $logo_id && $is_ours) {
            set_theme_mod('custom_logo', $logo_id);
        }
    }

    $icon_id = justccell_brand_icon_id();
    if ($icon_id > 0 && (int) get_option('site_icon') !== $icon_id) {
        update_option('site_icon', $icon_id);
    }
}

function justccell_ensure_front_media(): void
{
    $keys = array_column(justccell_catalog(), 'image');
    foreach (justccell_home_asset_keys() as $value) {
        if (is_array($value)) {
            $keys = array_merge($keys, $value);
            continue;
        }
        $keys[] = $value;
    }
    justccell_ensure_media_files(array_values(array_filter($keys)));
}

add_action('wp', static function (): void {
    if (is_admin()) {
        return;
    }
    justccell_assign_brand_assets();
    if (is_front_page()) {
        justccell_ensure_front_media();
        return;
    }
    justccell_ensure_media_files(array_column(justccell_catalog(), 'image'));
}, 4);
