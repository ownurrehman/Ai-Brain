<?php
/**
 * One-shot: replace remaining Discover featured images. Does not delete posts.
 *
 * Developed by Rank Ray — https://rankray.com
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    if (get_option('justccell_editorial_images_v3') === '1') {
        justccell_editorial_images_v3_cleanup();
        return;
    }
    $dir = WP_CONTENT_DIR . '/justccell-blog-import/images-v2';
    $json = $dir . '/map.json';
    if (!is_readable($json)) {
        return;
    }
    if (get_transient('justccell_editorial_images_v3_lock')) {
        return;
    }
    set_transient('justccell_editorial_images_v3_lock', '1', 15 * MINUTE_IN_SECONDS);
    @set_time_limit(300);

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $rows = json_decode((string) file_get_contents($json), true);
    if (!is_array($rows) || $rows === []) {
        delete_transient('justccell_editorial_images_v3_lock');
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
        $current = (int) get_post_thumbnail_id($post_id);
        $current_file = $current > 0 ? (string) get_attached_file($current) : '';
        if ($current_file !== '' && str_contains($current_file, $file)) {
            $ok++;
            $log[] = 'already:' . $slug;
            continue;
        }
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
        set_post_thumbnail($post_id, $att);
        update_post_meta($att, '_wp_attachment_image_alt', $alt);
        if ($current > 0 && $current !== $att) {
            wp_delete_attachment($current, true);
        }
        $ok++;
        $log[] = 'ok:' . $post_id . ':' . $slug . ':' . $att;
    }

    update_option('justccell_editorial_images_v3_log', implode("\n", $log) . "\nupdated=" . $ok);
    if ($ok >= count($rows)) {
        update_option('justccell_editorial_images_v3', '1');
        delete_transient('justccell_editorial_images_v3_lock');
        justccell_editorial_images_v3_cleanup();
        return;
    }
    delete_transient('justccell_editorial_images_v3_lock');
}, 20);

function justccell_editorial_images_v3_cleanup(): void
{
    $plugin = WP_CONTENT_DIR . '/mu-plugins/justccell-editorial-images-v3.php';
    if (is_file($plugin)) {
        @unlink($plugin);
    }
}
