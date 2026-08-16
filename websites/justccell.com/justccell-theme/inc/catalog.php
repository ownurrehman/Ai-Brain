<?php
declare(strict_types=1);
if (!defined('ABSPATH')) { exit; }

function justccell_media_id(string $file): int
{
    $map = get_option('justccell_media_map', []);
    if (!is_array($map)) {
        return 0;
    }
    $id = (int) ($map[$file] ?? 0);
    if ($id > 0 && get_post_type($id) === 'attachment') {
        return $id;
    }
    return 0;
}

function justccell_media_pack_url(string $file): string
{
    static $base = null;
    if ($base === null) {
        $base = '';
        $root = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
        foreach (glob($root . '/justccell-media*', GLOB_ONLYDIR) ?: [] as $dir) {
            if (is_dir($dir . '/ref/tank-360') || is_readable($dir . '/ref/public_static_modules_cms_img_newlogo.png')) {
                $base = trailingslashit(plugins_url('ref', $dir . '/justccell-media.php'));
                break;
            }
        }
    }
    if ($base === '') {
        return '';
    }
    return $base . ltrim(str_replace('\\', '/', $file), '/');
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
    if ($id < 1 && function_exists('justccell_sideload_media_file')) {
        $id = justccell_sideload_media_file($file, false);
    }
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
        $html = wp_get_attachment_image($id, $size, false, $attrs);
        return is_string($html) ? $html : '';
    }
    return '';
}

function justccell_catalog(): array {
    return [
        [
            'name' => 'Tank',
            'slug' => 'tank',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240507_622e6cebbbb7055185e806fd2b593268.png',
            'specs' => ['Tank Volume: 1ml/2ml/3ml', 'Compatible With All Types of Cannabis Oils', 'Child-Resistant Lock'],
        ],
        [
            'name' => 'Eco Star',
            'slug' => 'eco-star',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20250207_effe61ef54aebd0e7fc85ebcc86ee2cd.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'Easily Removable, Recyclable Battery', 'Clog-Free Dual Air Vents'],
        ],
        [
            'name' => 'Mini Tank',
            'slug' => 'mini-tank',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240507_80564d119e791271bb317cc91dd74828.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'Ultra Compact All-In-One Vaporizer', 'Clog-Free Dual Air Vents'],
        ],
        [
            'name' => 'Rosin Bar',
            'slug' => 'rosin-bar',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20250102_5409e46e60179e2e1054c72da4423a8a.png',
            'specs' => ['Tank Volume: 0.5ml', '100% Rosin-Ready', 'THC and Terpene Partitioned Atomization'],
        ],
        [
            'name' => 'Voca Pro',
            'slug' => 'voca-pro',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240812_61c733c4d0c3397a6faf5017f5a3a21b.png',
            'specs' => ['0.5ml and 1ml options available at the same size', 'Preheat and child-resistant button'],
        ],
        [
            'name' => 'Blanc',
            'slug' => 'blanc',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20240116_a60dfad82bbff7ac268915d20bd4c163.png',
            'specs' => ['Tank Volume: 0.3ml/0.5ml/1ml', 'Full Ceramic All-In-One Device'],
        ],
        [
            'name' => 'Slym',
            'slug' => 'slym',
            'category' => 'all-in-ones',
            'image' => 'public_uploads_images_20230213_3933986251799de2a685e4063737e2cb.png',
            'specs' => ['Tank Volume: 0.3ml/0.5ml', 'Ultra-Slim Lightweight Design'],
        ],
        [
            'name' => 'Ceramic-EVOMAX',
            'slug' => 'ceramic-evomax',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20240724_08bd6433734bad9d99b5b9cbff5646fd.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'Ceramic Center Post', 'Borosilicate Glass Body'],
        ],
        [
            'name' => 'TH2-EVOMAX',
            'slug' => 'th2-evomax',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20240418_5d48c79c07af862e9cfe7781f14bee14.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'All-Oil-Capable Borosilicate Glass Cartridge'],
        ],
        [
            'name' => 'M6T-EVOMAX',
            'slug' => 'm6t-evomax',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20240423_3cd1b054149dcdac21ea0842969824c2.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'All-Oil-Capable, BPA-Free Thermoplastic Cartridge'],
        ],
        [
            'name' => 'TH2-SE',
            'slug' => 'th2-se',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20240522_3ab3d30d467a97f213945125b358b654.png',
            'specs' => ['Tank Volume: 0.5ml/1ml/1.2ml', 'Borosilicate Glass Cartridge'],
        ],
        [
            'name' => 'M6T-SE',
            'slug' => 'm6t-se',
            'category' => 'cartridge',
            'image' => 'public_uploads_images_20230630_efca403a99e32ee7c4526e6eb00a1595.png',
            'specs' => ['Tank Volume: 0.5ml/1ml', 'BPA-Free Thermoplastic Cartridge'],
        ],
        [
            'name' => 'Luster Pro',
            'slug' => 'luster-pro',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20240401_5330ba5336acfab3cdd25f260af2dcd2.png',
            'specs' => ['Sleek Pod System With Variable Wattage'],
        ],
        [
            'name' => 'Dart-X',
            'slug' => 'dart-x',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20250305_d3c596b799f72238d81a78d16ee53966.png',
            'specs' => ['3 Unique Temperature Settings'],
        ],
        [
            'name' => 'Dart',
            'slug' => 'dart',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20211018_bde1c43c00b90f8a4898e34fa0376bc1.png',
            'specs' => ['Comfortable Grip with Matte Finish'],
        ],
        [
            'name' => 'Bellos',
            'slug' => 'bellos',
            'category' => 'pod-system',
            'image' => 'public_uploads_images_20211018_f63b7837d8d2557e1573f356e364adf0.png',
            'specs' => ['Easy on Custom Designs', 'Haptic Feedback'],
        ],
        [
            'name' => 'Stylo',
            'slug' => 'stylo',
            'category' => 'battery',
            'image' => 'public_uploads_images_20250407_8b49096ddef040e60e43f740198f0535.png',
            'specs' => ['500mAh', '3 voltage settings (2.4V/2.8V/3.2V)'],
        ],
        [
            'name' => 'Fino',
            'slug' => 'fino',
            'category' => 'battery',
            'image' => 'public_uploads_images_20231205_1544a0ee45c6725d20e885f965a4fb57.png',
            'specs' => ['190mAh (Battery) + 1000mAh (Dock)', '8 Voltage Settings (2.2V-3.6V)'],
        ],
        [
            'name' => 'Sandwave',
            'slug' => 'sandwave',
            'category' => 'battery',
            'image' => 'public_uploads_images_20230706_0c5f2f7fbc38369b8e5c4f72eb30b78a.png',
            'specs' => ['Battery Capacity: 400mAh', 'Inhale Activated', '3 Temperature Settings via Slide Switch (2.8/3.2/3.6V)'],
        ],
        [
            'name' => 'Go Stik',
            'slug' => 'go-stik',
            'category' => 'battery',
            'image' => 'public_uploads_images_20230817_6f917d3506895deda262311ba114014a.png',
            'specs' => ['2 Temperature Settings via Slide Switch (2.8/3.2V)', 'Compatible With 510 Cartridges of Any Size'],
        ],
        [
            'name' => 'Palm Pro',
            'slug' => 'palm-pro',
            'category' => 'battery',
            'image' => 'public_uploads_images_20230811_097aa8efa3e032454546c9cc20e18992.png',
            'specs' => ['Battery Capacity: 500mAh', '3 Voltage Settings (2.8/3.2/3.6V)', 'Adjustable Airflow'],
        ],
        [
            'name' => 'M3B Plus',
            'slug' => 'm3b-plus',
            'category' => 'battery',
            'image' => 'public_uploads_images_20240227_89397ff3de880aca770571a81e29c4d2.png',
            'specs' => ['3 Voltage Settings (2.8/3.2/3.6V)', 'Compatible With 510 Cartridges of Any Size'],
        ],
    ];
}

function justccell_catalog_by_category(): array {
    $out = ['all-in-ones' => [], 'cartridge' => [], 'pod-system' => [], 'battery' => []];
    foreach (justccell_catalog() as $item) {
        $out[$item['category']][] = $item;
    }
    return $out;
}

/**
 * ccell.com listing groups. Only slugs we actually sell.
 *
 * @return list<array{title:string,items:list<array<string, mixed>>}>
 */
function justccell_catalog_groups(string $category): array
{
    $groups = [
        'all-in-ones' => [
            ['title' => 'Best For Distillates', 'slugs' => ['mini-tank']],
            ['title' => 'Best For Live Rosins', 'slugs' => ['rosin-bar']],
            ['title' => 'Best For Live Resins', 'slugs' => ['voca-pro', 'blanc', 'slym']],
            ['title' => 'All-Oil-Capable', 'slugs' => ['tank', 'eco-star']],
        ],
        'cartridge' => [
            ['title' => '', 'slugs' => ['ceramic-evomax', 'th2-evomax', 'm6t-evomax', 'th2-se', 'm6t-se']],
        ],
        'pod-system' => [
            ['title' => '', 'slugs' => ['luster-pro', 'dart-x', 'dart', 'bellos']],
        ],
        'battery' => [
            ['title' => '', 'slugs' => ['stylo', 'fino', 'sandwave', 'go-stik', 'palm-pro', 'm3b-plus']],
        ],
    ];

    $items = [];
    foreach (justccell_catalog() as $item) {
        $items[$item['slug']] = $item;
    }

    $out  = [];
    $used = [];
    foreach ($groups[$category] ?? [] as $group) {
        $rows = [];
        foreach ($group['slugs'] as $slug) {
            if (isset($items[$slug])) {
                $rows[] = $items[$slug];
                $used[$slug] = true;
            }
        }
        if ($rows !== []) {
            $out[] = [
                'title' => (string) $group['title'],
                'items' => $rows,
            ];
        }
    }

    $leftover = [];
    foreach (justccell_catalog_by_category()[$category] ?? [] as $item) {
        if (empty($used[$item['slug']])) {
            $leftover[] = $item;
        }
    }
    if ($leftover !== []) {
        $out[] = ['title' => '', 'items' => $leftover];
    }

    return $out;
}

/**
 * ccell Explore More cards: one description line + a short cyan capacity.
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
 * Horizontal PNG first (transparent, header-ready). JPEG fallback. Square is favicon only.
 *
 * @return list<string>
 */
function justccell_brand_logo_basenames(): array
{
    return [
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

function justccell_brand_icon_id(): int
{
    return justccell_attachment_id_by_basename('Just-CCELL-round.png');
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
        if ($current !== $logo_id && !in_array($current_file, justccell_brand_logo_basenames(), true)) {
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
