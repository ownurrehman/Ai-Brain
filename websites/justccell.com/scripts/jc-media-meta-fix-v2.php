<?php
/**
 * Plugin Name: Justccell Media Repair + Cleanup
 * Description: One-shot repair (done) + removes the stuck Cursor repair mu-plugin. Trigger: /?jcm_cleanup=SECRET while logged in as admin. Deactivate + delete this plugin from Plugins page afterwards.
 * Version: 1.1.0
 * Author: Hermes (Rank Ray)
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

// PART 1 (kept for reference/verification): metadata repair — already complete (36/36 healthy).
add_action('init', function (): void {
    $secret = 'jcm-repair-2026-09-01-k8d2';
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
    $ids = range(115, 150);
    $remaining = 0;
    foreach ($ids as $id) {
        $m = wp_get_attachment_metadata($id);
        if (!is_array($m) || empty($m['sizes']['thumbnail'])) {
            $remaining++;
        }
    }
    echo wp_json_encode(['ok' => true, 'remaining' => $remaining], JSON_PRETTY_PRINT);
    exit;
}, 0);

// PART 2: DELETE the stuck Cursor mu-plugin (only with secret arg + admin user).
add_action('init', function (): void {
    $secret = 'jcm-cleanup-2026-09-01-x7q9';
    if (!isset($_GET['jcm_cleanup']) || $_GET['jcm_cleanup'] !== $secret) {
        return;
    }
    nocache_headers();
    header('Content-Type: application/json; charset=utf-8');
    if (!current_user_can('manage_options')) {
        status_header(403);
        echo wp_json_encode(['ok' => false, 'error' => 'login_required']);
        exit;
    }

    $target = WPMU_PLUGIN_DIR . '/jc-media-repair-cli.php';
    $result = ['ok' => true, 'mu_plugins_dir' => WPMU_PLUGIN_DIR];

    if (!file_exists($target)) {
        // try variations
        foreach (['jc-media-repair-cli.php', 'jc_repair.php'] as $candidate) {
            $p = WPMU_PLUGIN_DIR . '/' . $candidate;
            if (file_exists($p)) { $target = $p; break; }
        }
    }
    // Also scan mu-plugins dir listing for visibility in the report
    $result['mu_plugins_found'] = is_dir(WPMU_PLUGIN_DIR) ? scandir(WPMU_PLUGIN_DIR) : null;

    $file = is_string($target) ? $target : '';
    if ($file !== '' && file_exists($file)) {
        $result['target'] = basename($file);
        $result['deleted'] = @unlink($file);
    } else {
        $result['deleted'] = false;
        $result['note'] = 'mu-plugin file not found under WPMU_PLUGIN_DIR';
    }
    echo wp_json_encode($result, JSON_PRETTY_PRINT);
    exit;
}, 0);