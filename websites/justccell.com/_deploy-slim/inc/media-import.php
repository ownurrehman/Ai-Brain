<?php
/**
 * Copy clone images into the WordPress Media Library and reuse those attachments.
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_MEDIA_BATCH = 6;

function justccell_media_map(): array
{
    $map = get_option('justccell_media_map', []);
    return is_array($map) ? $map : [];
}

function justccell_media_extract_packs(): void
{
    $root = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
    foreach (glob($root . '/justccell-media*', GLOB_ONLYDIR) ?: [] as $plugin) {
        $dir = $plugin . '/ref';
        $zip = $plugin . '/ref.zip';
        $ready = is_dir($dir) && count(glob($dir . '/*') ?: []) > 5;
        if ($ready || !is_readable($zip) || !class_exists('ZipArchive')) {
            continue;
        }
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            continue;
        }
        $archive = new ZipArchive();
        if ($archive->open($zip) !== true) {
            continue;
        }
        $archive->extractTo($dir);
        $archive->close();
    }
}

/**
 * @return list<string>
 */
function justccell_media_source_dirs(): array
{
    justccell_media_extract_packs();
    $dirs = [];
    $plugin_root = defined('WP_PLUGIN_DIR') ? WP_PLUGIN_DIR : WP_CONTENT_DIR . '/plugins';
    foreach (glob($plugin_root . '/justccell-media*', GLOB_ONLYDIR) ?: [] as $plugin) {
        $dirs[] = $plugin . '/ref';
    }
    $theme_root = function_exists('get_theme_root') ? get_theme_root() : WP_CONTENT_DIR . '/themes';
    foreach (glob($theme_root . '/justccell-theme*', GLOB_ONLYDIR) ?: [] as $theme) {
        $dirs[] = $theme . '/assets/img/ref';
        $dirs[] = $theme . '/assets/img/product';
    }
    if (defined('JUSTCCELL_DIR')) {
        $dirs[] = JUSTCCELL_DIR . '/assets/img/ref';
        $dirs[] = JUSTCCELL_DIR . '/assets/img/product';
    }
    return array_values(array_unique(array_filter($dirs)));
}

function justccell_media_source_file(string $key): string
{
    foreach (justccell_media_source_dirs() as $dir) {
        $path = rtrim($dir, '/') . '/' . ltrim(str_replace('\\', '/', $key), '/');
        if (is_readable($path)) {
            return $path;
        }
    }
    return '';
}

function justccell_media_source_dir(): string
{
    foreach (justccell_media_source_dirs() as $dir) {
        if (is_dir($dir . '/tank-360') || is_readable($dir . '/public_static_modules_cms_img_newlogo.png')) {
            return $dir;
        }
    }
    $dirs = justccell_media_source_dirs();
    return $dirs[0] ?? '';
}

/**
 * @return list<string>
 */
function justccell_media_file_keys(): array
{
    $dir = justccell_media_source_dir();
    if ($dir === '' || !is_dir($dir)) {
        return [];
    }

    $keys = [];
    $iterator = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($dir, FilesystemIterator::SKIP_DOTS)
    );
    foreach ($iterator as $file) {
        if (!$file instanceof SplFileInfo || !$file->isFile()) {
            continue;
        }
        $ext = strtolower($file->getExtension());
        if (!in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'], true)) {
            continue;
        }
        $relative = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($dir))), '/');
        if ($relative !== '') {
            $keys[] = $relative;
        }
    }
    sort($keys);
    return $keys;
}

function justccell_media_title(string $key): string
{
    $titles = [
        'public_static_modules_cms_img_newlogo.png' => 'Justccell logo',
        'public_uploads_images_20241128_4972526923761444af2d1d76aa5f512a.png' => 'Justccell logo (dark)',
        'public_static_modules_cms_img_home14.png' => 'Homepage arrow',
        'public_static_modules_cms_img_home18.jpg' => 'Quote section background',
        'public_uploads_images_20250225_08b6cc13898889e8407ea3790ae31cad.png' => 'Fill technology',
        'public_uploads_images_20250225_2be1257b82984d06383bd05570e5a8be.png' => 'Trusted technology',
        'public_uploads_images_20250225_e9e2853eb498b95706a72f332df0a1a1.png' => 'Premium technology',
        'public_uploads_images_20250225_4520819030305c0ed4bde75255a8d6ad.png' => 'Customer logo 1',
        'public_uploads_images_20250225_03e34b11c8bd28052b8a1a5d877ebbe9.png' => 'Customer logo 2',
        'public_uploads_images_20250225_64e6456cdd881d697853f502f353d4a8.png' => 'Customer logo 3',
        'public_uploads_images_20250225_6c0bc8408fa97536916c3f93d7b4cb21.png' => 'Customer logo 4',
        'public_uploads_images_20250926_6d26d199e7d5f7c457ad85f05c69f8e4.jpg' => 'Homepage banner 1',
        'public_uploads_images_20250409_47d88dbb6d565e229709aa76a51fc82f.jpg' => 'Homepage banner 2',
        'public_uploads_images_20250228_35607022bf9c0261440de779466b67df.jpg' => 'Homepage banner 3',
        'public_uploads_images_20250624_586896b2422c482af3eb027b9c112ad5.jpg' => 'Homepage banner 4',
        'public_uploads_images_20240507_622e6cebbbb7055185e806fd2b593268.png' => 'Tank — catalog',
        'public_uploads_images_20250207_effe61ef54aebd0e7fc85ebcc86ee2cd.png' => 'Eco Star — catalog',
        'public_uploads_images_20240507_80564d119e791271bb317cc91dd74828.png' => 'Mini Tank — catalog',
        'public_uploads_images_20250102_5409e46e60179e2e1054c72da4423a8a.png' => 'Rosin Bar — catalog',
        'public_uploads_images_20240812_61c733c4d0c3397a6faf5017f5a3a21b.png' => 'Voca Pro — catalog',
        'public_uploads_images_20240116_a60dfad82bbff7ac268915d20bd4c163.png' => 'Blanc — catalog',
        'public_uploads_images_20230213_3933986251799de2a685e4063737e2cb.png' => 'Slym — catalog',
        'public_uploads_images_20240724_08bd6433734bad9d99b5b9cbff5646fd.png' => 'Ceramic-EVOMAX — catalog',
        'public_uploads_images_20240418_5d48c79c07af862e9cfe7781f14bee14.png' => 'TH2-EVOMAX — catalog',
        'public_uploads_images_20240423_3cd1b054149dcdac21ea0842969824c2.png' => 'M6T-EVOMAX — catalog',
        'public_uploads_images_20240522_3ab3d30d467a97f213945125b358b654.png' => 'TH2-SE — catalog',
        'public_uploads_images_20230630_efca403a99e32ee7c4526e6eb00a1595.png' => 'M6T-SE — catalog',
        'public_uploads_images_20240401_5330ba5336acfab3cdd25f260af2dcd2.png' => 'Luster Pro — catalog',
        'public_uploads_images_20250305_d3c596b799f72238d81a78d16ee53966.png' => 'Dart-X — catalog',
        'public_uploads_images_20211018_bde1c43c00b90f8a4898e34fa0376bc1.png' => 'Dart — catalog',
        'public_uploads_images_20211018_f63b7837d8d2557e1573f356e364adf0.png' => 'Bellos — catalog',
        'public_uploads_images_20250407_8b49096ddef040e60e43f740198f0535.png' => 'Stylo — catalog',
        'public_uploads_images_20231205_1544a0ee45c6725d20e885f965a4fb57.png' => 'Fino — catalog',
        'public_uploads_images_20230706_0c5f2f7fbc38369b8e5c4f72eb30b78a.png' => 'Sandwave — catalog',
        'public_uploads_images_20230817_6f917d3506895deda262311ba114014a.png' => 'Go Stik — catalog',
        'public_uploads_images_20230811_097aa8efa3e032454546c9cc20e18992.png' => 'Palm Pro — catalog',
        'public_uploads_images_20240227_89397ff3de880aca770571a81e29c4d2.png' => 'M3B Plus — catalog',
        'public_uploads_images_20240812_92d893bc1556b73225bdd65a97795d5b.jpg' => 'Tank — hero banner',
        'public_uploads_images_20240507_036cfe495c3b090387a77086a1b8dca3.png' => 'Tank — gallery 1',
        'public_uploads_images_20240812_ecd6251ca0c25e0cc0670e258a1105b6.png' => 'Tank — gallery 2',
        'public_uploads_images_20240812_5c0ed5cfa8be11eaf1973ca747aebee7.png' => 'Tank — gallery 3',
        'public_uploads_images_20240812_c8aacaa36440a355b98d9b4e6e3b9d55.png' => 'Tank — gallery 4',
        'public_uploads_images_20240812_4b77ce35f4f058ad21387cfc324a2fa5.png' => 'Tank — gallery 5',
        'public_uploads_images_20240812_26d00c337b9b28df435ff6fc6a8abde0.png' => 'Tank — gallery 6',
        'public_uploads_images_20240812_63f2fb1ddeacbb6cfc2b46c8c522c205.png' => 'Tank — gallery 7',
        'public_uploads_images_20240812_1f1d5c5a7ea7a4179bdf04f67fbeb47e.png' => 'Tank — gallery 8',
        'public_uploads_images_20240506_e60d5915f1ab555868ebc36d50a41b6b.jpg' => 'Tank — miniature design',
        'public_uploads_images_20240506_a498121b67ccdd5c826b3c74bbcb34c9.jpg' => 'Tank — oil compatibility',
        'public_uploads_images_20240506_91f67d4501c135393bf11dd9086d28c9.jpg' => 'Tank — leak switch',
        'public_uploads_images_20240506_8e00a1242167b6a9fee197a33134980b.jpg' => 'Tank — voltage settings',
        'public_uploads_images_20240506_0f9527478ab30057741eebe91ded759a.jpg' => 'Tank — batch-capping',
        'public_uploads_images_20240821_d7272eb683ff4177d1c87687fc02982e.png' => 'Tank — detail 1',
        'public_uploads_images_20240812_abb0f699e525e9234eaa48d19cc0a509.jpg' => 'Tank — detail 2',
        'public_uploads_images_20240812_0aff615625c41aa2abcaa8b850584e2c.jpg' => 'Tank — detail 3',
    ];

    if (isset($titles[$key])) {
        return $titles[$key];
    }
    if (preg_match('#^tank-360/(\d+)\.jpg$#', $key, $match) === 1) {
        return sprintf('Tank 360 — frame %s', $match[1]);
    }
    return 'Justccell — ' . basename($key);
}

function justccell_media_import_needed(): bool
{
    $keys = justccell_media_file_keys();
    if ($keys === []) {
        return false;
    }
    $map = justccell_media_map();
    foreach ($keys as $key) {
        $id = (int) ($map[$key] ?? 0);
        if ($id < 1 || get_post_type($id) !== 'attachment') {
            return true;
        }
    }
    return false;
}

function justccell_sideload_media_file(string $key, bool $generate_meta = true): int
{
    $map = justccell_media_map();
    $existing = (int) ($map[$key] ?? 0);
    if ($existing > 0 && get_post_type($existing) === 'attachment') {
        return $existing;
    }

    $found = get_posts([
        'post_type'      => 'attachment',
        'post_status'    => 'inherit',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_justccell_ref',
        'meta_value'     => $key,
    ]);
    if ($found !== []) {
        $id = (int) $found[0];
        $map[$key] = $id;
        update_option('justccell_media_map', $map, false);
        return $id;
    }

    $src = justccell_media_source_file($key);
    if ($src === '') {
        return 0;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';

    $uploads = wp_upload_dir();
    if (!empty($uploads['error']) || !is_string($uploads['path'])) {
        return 0;
    }

    $dest_name = wp_unique_filename($uploads['path'], str_replace('/', '-', $key));
    $dest      = $uploads['path'] . '/' . $dest_name;
    if (!copy($src, $dest)) {
        return 0;
    }

    $filetype = wp_check_filetype($dest_name, null);
    $id       = wp_insert_attachment(
        [
            'post_mime_type' => $filetype['type'] ?: 'image/jpeg',
            'post_title'     => justccell_media_title($key),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'guid'           => $uploads['url'] . '/' . $dest_name,
        ],
        $dest
    );
    if (!is_int($id) || $id < 1) {
        return 0;
    }

    update_post_meta($id, '_justccell_ref', $key);

    if ($generate_meta && !str_starts_with($key, 'tank-360/')) {
        $meta = wp_generate_attachment_metadata($id, $dest);
        if (is_array($meta)) {
            wp_update_attachment_metadata($id, $meta);
        }
    }

    $map[$key] = $id;
    update_option('justccell_media_map', $map, false);
    return $id;
}

/**
 * @return array{imported:int,total:int,done:bool,last:string}
 */
function justccell_import_media_batch(int $limit = JUSTCCELL_MEDIA_BATCH): array
{
    $keys     = justccell_media_file_keys();
    $map      = justccell_media_map();
    $imported = 0;
    $last     = '';
    $pending  = 0;

    foreach ($keys as $key) {
        $id = (int) ($map[$key] ?? 0);
        if ($id > 0 && get_post_type($id) === 'attachment') {
            $imported++;
            continue;
        }
        if ($pending >= $limit) {
            break;
        }
        $new = justccell_sideload_media_file($key);
        if ($new > 0) {
            $imported++;
            $last = $key;
        }
        $pending++;
        $map = justccell_media_map();
    }

    $total = count($keys);
    $done  = $total > 0 && $imported >= $total;
    if ($done) {
        justccell_media_attach_site();
        update_option('justccell_media_imported', '1');
    }

    return [
        'imported' => $imported,
        'total'    => $total,
        'done'     => $done,
        'last'     => $last,
    ];
}

function justccell_media_attach_site(): void
{
    if (function_exists('justccell_assign_brand_assets')) {
        justccell_assign_brand_assets();
    }

    $map = justccell_media_map();
    $catalog_files = [
        'tank'            => 'public_uploads_images_20240507_622e6cebbbb7055185e806fd2b593268.png',
        'eco-star'        => 'public_uploads_images_20250207_effe61ef54aebd0e7fc85ebcc86ee2cd.png',
        'mini-tank'       => 'public_uploads_images_20240507_80564d119e791271bb317cc91dd74828.png',
        'rosin-bar'       => 'public_uploads_images_20250102_5409e46e60179e2e1054c72da4423a8a.png',
        'voca-pro'        => 'public_uploads_images_20240812_61c733c4d0c3397a6faf5017f5a3a21b.png',
        'blanc'           => 'public_uploads_images_20240116_a60dfad82bbff7ac268915d20bd4c163.png',
        'slym'            => 'public_uploads_images_20230213_3933986251799de2a685e4063737e2cb.png',
        'ceramic-evomax'  => 'public_uploads_images_20240724_08bd6433734bad9d99b5b9cbff5646fd.png',
        'th2-evomax'      => 'public_uploads_images_20240418_5d48c79c07af862e9cfe7781f14bee14.png',
        'm6t-evomax'      => 'public_uploads_images_20240423_3cd1b054149dcdac21ea0842969824c2.png',
        'th2-se'          => 'public_uploads_images_20240522_3ab3d30d467a97f213945125b358b654.png',
        'm6t-se'          => 'public_uploads_images_20230630_efca403a99e32ee7c4526e6eb00a1595.png',
        'luster-pro'      => 'public_uploads_images_20240401_5330ba5336acfab3cdd25f260af2dcd2.png',
        'dart-x'          => 'public_uploads_images_20250305_d3c596b799f72238d81a78d16ee53966.png',
        'dart'            => 'public_uploads_images_20211018_bde1c43c00b90f8a4898e34fa0376bc1.png',
        'bellos'          => 'public_uploads_images_20211018_f63b7837d8d2557e1573f356e364adf0.png',
        'stylo'           => 'public_uploads_images_20250407_8b49096ddef040e60e43f740198f0535.png',
        'fino'            => 'public_uploads_images_20231205_1544a0ee45c6725d20e885f965a4fb57.png',
        'sandwave'        => 'public_uploads_images_20230706_0c5f2f7fbc38369b8e5c4f72eb30b78a.png',
        'go-stik'         => 'public_uploads_images_20230817_6f917d3506895deda262311ba114014a.png',
        'palm-pro'        => 'public_uploads_images_20230811_097aa8efa3e032454546c9cc20e18992.png',
        'm3b-plus'        => 'public_uploads_images_20240227_89397ff3de880aca770571a81e29c4d2.png',
    ];

    foreach ($catalog_files as $slug => $file) {
        $image_id = (int) ($map[$file] ?? 0);
        if ($image_id < 1) {
            continue;
        }
        $product_id = 0;
        if (function_exists('wc_get_product_id_by_sku')) {
            $product_id = (int) wc_get_product_id_by_sku($slug);
        }
        if ($product_id < 1) {
            $found = get_posts([
                'name'           => $slug,
                'post_type'      => 'product',
                'post_status'    => 'any',
                'posts_per_page' => 1,
                'fields'         => 'ids',
            ]);
            $product_id = $found !== [] ? (int) $found[0] : 0;
        }
        if ($product_id > 0) {
            set_post_thumbnail($product_id, $image_id);
        }
    }

    $front = (int) get_option('page_on_front');
    $hero  = (int) ($map['public_uploads_images_20250926_6d26d199e7d5f7c457ad85f05c69f8e4.jpg'] ?? 0);
    if ($front > 0 && $hero > 0 && (int) get_post_thumbnail_id($front) === 0) {
        set_post_thumbnail($front, $hero);
    }
}

add_action('admin_menu', static function (): void {
    add_management_page(
        __('Justccell Media', 'justccell'),
        __('Justccell Media', 'justccell'),
        'upload_files',
        'justccell-media',
        'justccell_media_tools_page'
    );
});

function justccell_media_tools_page(): void
{
    if (!current_user_can('upload_files')) {
        return;
    }
    $keys   = justccell_media_file_keys();
    $status = [
        'imported' => count(justccell_media_map()),
        'total'    => count($keys),
        'done'     => $keys !== [] && !justccell_media_import_needed(),
        'source'   => justccell_media_source_dir(),
    ];
    echo '<div class="wrap"><h1>' . esc_html__('Justccell Media', 'justccell') . '</h1>';
    echo '<p>' . esc_html__('Clone photos are copied into Media → Library, then the site uses those attachments.', 'justccell') . '</p>';
    echo '<p id="justccell-media-import-status">';
    if ($status['total'] === 0) {
        echo esc_html__('Photo pack is not on the server yet. Install/activate the Justccell Media Pack plugin, then refresh this page.', 'justccell');
    } elseif ($status['done']) {
        echo esc_html(sprintf(__('Done. %d images are in the Media Library.', 'justccell'), $status['imported']));
    } else {
        echo esc_html(sprintf(__('Imported %d of %d. Keep this page open.', 'justccell'), $status['imported'], $status['total']));
    }
    echo '</p></div>';
}

add_action('admin_notices', static function (): void {
    if (!current_user_can('upload_files') || !justccell_media_import_needed()) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    echo '<div class="notice notice-info"><p id="justccell-media-import-status">';
    esc_html_e('Justccell is copying site images into the WordPress Media Library. Keep this admin page open for a minute.', 'justccell');
    if ($screen && $screen->id !== 'tools_page_justccell-media') {
        echo ' <a href="' . esc_url(admin_url('tools.php?page=justccell-media')) . '">';
        esc_html_e('Open progress', 'justccell');
        echo '</a>';
    }
    echo '</p></div>';
});

add_action('admin_enqueue_scripts', static function (): void {
    if (!current_user_can('upload_files') || !justccell_media_import_needed()) {
        return;
    }
    wp_enqueue_script(
        'justccell-media-import',
        JUSTCCELL_URI . '/assets/js/admin-media-import.js',
        [],
        JUSTCCELL_VERSION,
        true
    );
    wp_localize_script('justccell-media-import', 'justccellMediaImport', [
        'ajax'  => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('justccell_media_import'),
    ]);
});

add_action('wp_ajax_justccell_import_media', static function (): void {
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    check_ajax_referer('justccell_media_import', 'nonce');
    wp_send_json_success(justccell_import_media_batch());
});
