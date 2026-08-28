<?php
/**
 * One-shot: replace Discover featured images only. Does not delete posts.
 *
 * Developed by Rank Ray — https://rankray.com
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    if (get_option('justccell_editorial_images_v2') === '1') {
        justccell_editorial_images_v2_cleanup();
        return;
    }
    $dir = WP_CONTENT_DIR . '/justccell-blog-import/images-v2';
    $json = $dir . '/map.json';
    if (!is_readable($json)) {
        return;
    }
    if (get_transient('justccell_editorial_images_v2_lock')) {
        return;
    }
    set_transient('justccell_editorial_images_v2_lock', '1', 10 * MINUTE_IN_SECONDS);

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $rows = json_decode((string) file_get_contents($json), true);
    if (!is_array($rows) || $rows === []) {
        update_option('justccell_editorial_images_v2', 'error-json');
        return;
    }

    $log = [];
    $ok = 0;
    foreach ($rows as $row) {
        if (!is_array($row)) {
            continue;
        }
        $slug = sanitize_title((string) ($row['slug'] ?? ''));
        $file = basename((string) ($row['file'] ?? ''));
        $alt = (string) ($row['alt'] ?? '');
        if ($slug === '' || $file === '') {
            continue;
        }
        $posts = get_posts([
            'name'             => $slug,
            'post_type'        => 'post',
            'post_status'      => ['publish', 'draft', 'private'],
            'posts_per_page'   => 1,
            'suppress_filters' => true,
        ]);
        if ($posts === []) {
            $log[] = 'missing-post:' . $slug;
            continue;
        }
        $post_id = (int) $posts[0]->ID;
        $path = $dir . '/' . $file;
        if (!is_readable($path)) {
            $log[] = 'missing-file:' . $file;
            continue;
        }
        $tmp = wp_tempnam($file);
        copy($path, $tmp);
        $att = media_handle_sideload(
            [
                'name'     => $file,
                'tmp_name' => $tmp,
            ],
            $post_id,
            $alt
        );
        if (is_wp_error($att) || (int) $att < 1) {
            $log[] = 'sideload-fail:' . $slug;
            if (is_string($tmp) && is_file($tmp)) {
                @unlink($tmp);
            }
            continue;
        }
        $att = (int) $att;
        $old = (int) get_post_thumbnail_id($post_id);
        set_post_thumbnail($post_id, $att);
        update_post_meta($att, '_wp_attachment_image_alt', $alt);
        if ($old > 0 && $old !== $att) {
            wp_delete_attachment($old, true);
        }
        $ok++;
        $log[] = 'ok:' . $post_id . ':' . $slug . ':' . $att;
    }

    update_option('justccell_editorial_images_v2', '1');
    update_option('justccell_editorial_images_v2_log', implode("\n", $log) . "\nupdated=" . $ok);
    delete_transient('justccell_editorial_images_v2_lock');
    justccell_editorial_images_v2_cleanup();
}, 20);

function justccell_editorial_images_v2_cleanup(): void
{
    $plugin = WP_CONTENT_DIR . '/mu-plugins/justccell-editorial-images-v2.php';
    if (is_file($plugin)) {
        @unlink($plugin);
    }
}
