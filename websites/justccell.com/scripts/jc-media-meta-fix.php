<?php
/**
 * Plugin Name: Justccell One-Shot Media Metadata Repair
 * Description: Regenerates attachment metadata for the 36 broken tank-360 uploads (IDs 115-150). Trigger once with ?jcm_repair=SECRET then deactivate + delete this plugin.
 * Version: 1.0.0
 * Author: Hermes (Rank Ray)
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', function (): void {
    $secret = 'jcm-repair-2026-09-01-k8d2';
    // Trigger: /?jcm_repair=SECRET  (you must be logged in as admin)
    // Optional: &batch=18 limits per hit. Re-hit until "remaining": 0
    if (!isset($_GET['jcm_repair']) || $_GET['jcm_repair'] !== $secret) {
        return;
    }

    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');

    if (!current_user_can('manage_options')) {
        status_header(403);
        echo wp_json_encode(['ok' => false, 'error' => 'login_required']);
        exit;
    }

    if (function_exists('wp_raise_memory_limit')) {
        wp_raise_memory_limit('image');
    }
    @set_time_limit(600);

    $ids = range(115, 150); // the 36 tank-360-*.jpg attachments with empty metadata
    $batch = isset($_GET['batch']) ? max(1, (int) $_GET['batch']) : 12;

    $fixed   = [];
    $failed  = [];
    $healthy = 0;
    $done = 0;

    require_once ABSPATH . 'wp-admin/includes/image.php';

    foreach ($ids as $id) {
        if ($done >= $batch) {
            break;
        }
        $meta = wp_get_attachment_metadata($id);
        if (is_array($meta) && !empty($meta['sizes']['thumbnail'])) {
            $healthy++;
            continue;
        }
        $file = get_attached_file($id);
        if (!is_string($file) || !file_exists($file)) {
            // try resolving by GUID if file path is stale
            $att = get_post($id);
            $url = is_object($att) ? $att->guid : '';
            if (is_string($url) && $url !== '') {
                $up = wp_upload_dir();
                $file = str_replace($up['baseurl'], $up['basedir'], $url);
            }
        }
        if (!is_string($file) || !file_exists($file)) {
            $failed[] = ['id' => $id, 'why' => 'file_missing', 'path' => is_string($file) ? $file : ''];
            $done++;
            continue;
        }
        $new = wp_generate_attachment_metadata($id, $file);
        if (is_array($new) && !empty($new['sizes'])) {
            wp_update_attachment_metadata($id, $new);
            $fixed[] = $id;
 $done++;
        }
    }

    // count remaining
    $remaining = 0;
    foreach ($ids as $id) {
        $m = wp_get_attachment_metadata($id);
        if (!is_array($m) || empty($m['sizes']['thumbnail'])) {
            $remaining++;
        }
    }

    echo wp_json_encode([
        'ok'        => true,
        'fixed'     => $fixed,
        'healthy'   => $healthy,
        'failed'    => $failed,
        'remaining' => $remaining,
        'note'      => 'Refresh Media Library grid. When remaining=0, deactivate and delete this plugin. Also delete mu-plugins/jc-media-repair-cli.php.',
    ], JSON_PRETTY_PRINT);
    exit;
}, 0);