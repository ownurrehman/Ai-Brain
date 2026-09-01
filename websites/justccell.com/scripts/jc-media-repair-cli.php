<?php
/**
 * One-shot Media Library metadata + thumbnail repair for justccell.com.
 * Upload to wp-content/mu-plugins/, hit once with the secret query arg, then delete.
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JC_MEDIA_REPAIR_SECRET = 'jc-repair-2026-08-31-a7f3';
const JC_MEDIA_REPAIR_BATCH  = 12;

/**
 * Mark undersized originals as grid-ready so repair queues do not stall.
 */
function jc_media_mark_small_images_ready(): int
{
    global $wpdb;
    $thumb = (int) get_option('thumbnail_size_w', 150);
    $rows  = $wpdb->get_col(
        "SELECT p.ID FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} m
           ON m.post_id = p.ID AND m.meta_key = '_wp_attachment_metadata'
         LEFT JOIN {$wpdb->postmeta} ready
           ON ready.post_id = p.ID AND ready.meta_key = '_justccell_grid_ready'
         WHERE p.post_type = 'attachment'
           AND p.post_status = 'inherit'
           AND p.post_mime_type LIKE 'image/%'
           AND (ready.meta_id IS NULL OR ready.meta_value != '1')"
    );
    if (!is_array($rows)) {
        return 0;
    }
    $marked = 0;
    foreach ($rows as $raw_id) {
        $id = (int) $raw_id;
        if ($id < 1) {
            continue;
        }
        $meta = wp_get_attachment_metadata($id);
        if (!is_array($meta)) {
            continue;
        }
        $width  = (int) ($meta['width'] ?? 0);
        $height = (int) ($meta['height'] ?? 0);
        if ($width < 1 || $height < 1) {
            $file = get_attached_file($id);
            if (is_string($file) && is_readable($file)) {
                $info = @getimagesize($file);
                if (is_array($info)) {
                    $width  = (int) $info[0];
                    $height = (int) $info[1];
                }
            }
        }
        if ($width < 1 || $height < 1 || $width >= $thumb || $height >= $thumb) {
            continue;
        }
        update_post_meta($id, '_justccell_grid_ready', '1', true);
        $marked++;
    }
    return $marked;
}

add_action('init', static function (): void {
    if (!isset($_GET['jc_media_repair']) || (string) $_GET['jc_media_repair'] !== JC_MEDIA_REPAIR_SECRET) {
        return;
    }
    if (!current_user_can('manage_options') && !defined('WP_CLI')) {
        status_header(403);
        header('Content-Type: application/json; charset=utf-8');
        echo wp_json_encode(['ok' => false, 'error' => 'login_required']);
        exit;
    }

    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');

    if (isset($_GET['reset_skips']) && $_GET['reset_skips'] === '1') {
        global $wpdb;
        $wpdb->query(
            "DELETE FROM {$wpdb->postmeta} WHERE meta_key IN ('_justccell_thumb_skip', '_justccell_grid_ready')"
        );
    }

    $report = [
        'ok'      => true,
        'batches' => [],
        'totals'  => ['meta' => 0, 'sanitize' => 0, 'rename' => 0, 'small_ready' => jc_media_mark_small_images_ready()],
    ];

    if (function_exists('wp_raise_memory_limit')) {
        wp_raise_memory_limit('image');
    }
    @set_time_limit(180);

    if (function_exists('justccell_sanitize_media_batch') && isset($_GET['sanitize']) && $_GET['sanitize'] === '1') {
        for ($i = 0; $i < 30; $i++) {
            $batch = justccell_sanitize_media_batch(JC_MEDIA_REPAIR_BATCH);
            $report['batches'][] = ['step' => 'sanitize', 'batch' => $batch];
            $report['totals']['sanitize'] += (int) ($batch['cleaned'] ?? 0);
            if (!empty($batch['done'])) {
                break;
            }
        }
    }

    if (function_exists('justccell_repair_meta_batch')) {
        for ($i = 0; $i < 80; $i++) {
            $batch = justccell_repair_meta_batch(JC_MEDIA_REPAIR_BATCH);
            $report['batches'][] = ['step' => 'meta', 'batch' => $batch];
            $report['totals']['meta'] += (int) ($batch['did'] ?? 0);
            if (!empty($batch['done'])) {
                break;
            }
        }
    }

    if (function_exists('justccell_repair_rename_batch') && isset($_GET['rename']) && $_GET['rename'] === '1') {
        delete_option('justccell_rename_done');
        delete_option('justccell_rename_cursor');
        for ($i = 0; $i < 120; $i++) {
            $batch = justccell_repair_rename_batch(8);
            $report['batches'][] = ['step' => 'rename', 'batch' => $batch];
            $report['totals']['rename'] += (int) ($batch['did'] ?? 0);
            if (!empty($batch['done'])) {
                break;
            }
        }
    }

    $report['thumbs_needed'] = function_exists('justccell_media_thumbs_needed')
        ? justccell_media_thumbs_needed()
        : null;
    $report['cleanup_needed'] = function_exists('justccell_media_cleanup_needed')
        ? justccell_media_cleanup_needed()
        : null;

    echo wp_json_encode($report, JSON_PRETTY_PRINT);
    exit;
}, 0);
