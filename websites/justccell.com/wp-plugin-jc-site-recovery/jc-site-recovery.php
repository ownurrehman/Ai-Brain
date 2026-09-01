<?php
/**
 * Plugin Name: JC Site Recovery
 * Description: Emergency recovery endpoints for justccell.com (remove after site is stable).
 * Version: 1.0.0
 * Author: Rank Ray
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JC_RECOVERY_TOKEN = 'jc-recover-2026-09-01-k8m2';

register_shutdown_function(static function (): void {
    $error = error_get_last();
    if ($error === null || !is_array($error)) {
        return;
    }
    $fatal_types = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array((int) ($error['type'] ?? 0), $fatal_types, true)) {
        return;
    }
    $line = gmdate('c') . ' ' . wp_json_encode($error) . "\n";
    @file_put_contents(WP_CONTENT_DIR . '/jc-last-fatal.log', $line, FILE_APPEND);
});

/**
 * @return never
 */
function jc_recovery_respond(array $payload, int $code = 200): void
{
    if (!headers_sent()) {
        status_header($code);
        header('Content-Type: application/json; charset=utf-8');
        header('Cache-Control: no-store');
    }
    echo wp_json_encode($payload, JSON_PRETTY_PRINT);
    exit;
}

add_action('init', static function (): void {
    if (!isset($_GET['jc_recovery']) || (string) $_GET['jc_recovery'] !== JC_RECOVERY_TOKEN) {
        return;
    }

    $action = isset($_GET['action']) ? sanitize_key((string) wp_unslash($_GET['action'])) : 'ping';

    if ($action === 'ping') {
        jc_recovery_respond(['ok' => true, 'message' => 'recovery plugin loaded']);
    }

    if ($action === 'status') {
        global $wpdb;
        $pending = get_option('jc_media_repair_pending_pairs', []);
        jc_recovery_respond([
            'ok'                 => true,
            'theme'              => (string) get_option('stylesheet'),
            'template'           => (string) get_option('template'),
            'repair_plugin'      => is_plugin_active('jc-media-reconnect-repair/jc-media-reconnect-repair.php'),
            'replaced_count'     => (int) $wpdb->get_var(
                "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
                 WHERE meta_key = '_justccell_replaced_by' AND meta_value != ''"
            ),
            'legacy_count'       => (int) $wpdb->get_var(
                "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
                 WHERE meta_key = '_justccell_legacy_media' AND meta_value = '1'"
            ),
            'pending_pair_count' => is_array($pending) ? count($pending) : 0,
            'php_version'        => PHP_VERSION,
        ]);
    }

    if ($action === 'cleanup_repair_options') {
        delete_option('jc_media_repair_pending_pairs');
        delete_option('jc_media_repair_scan_offset');
        jc_recovery_respond(['ok' => true, 'cleaned' => true]);
    }

    if ($action === 'enable_media_repair') {
        $path = get_template_directory() . '/inc/media-repair.php';
        $off  = $path . '.off';
        $ok   = is_file($off) && !is_file($path) && rename($off, $path);
        jc_recovery_respond(['ok' => $ok, 'path' => $path]);
    }

    if ($action === 'disable_media_repair') {
        $path = get_template_directory() . '/inc/media-repair.php';
        $off  = $path . '.off';
        $ok   = is_file($path) && !is_file($off) && rename($path, $off);
        jc_recovery_respond(['ok' => $ok, 'path' => $path, 'renamed_to' => $off]);
    }

    if ($action === 'restore_justccell_theme') {
        switch_theme('justccell-theme');
        jc_recovery_respond(['ok' => true, 'theme' => 'justccell-theme']);
    }

    if ($action === 'disable_media_migration') {
        $path = get_template_directory() . '/inc/media-migration.php';
        $off  = $path . '.off';
        $ok   = is_file($path) && !is_file($off) && rename($path, $off);
        jc_recovery_respond(['ok' => $ok, 'path' => $path, 'renamed_to' => $off]);
    }

    if ($action === 'patch_product_url') {
        $path    = get_template_directory() . '/inc/product-pages.php';
        $content = is_readable($path) ? (string) file_get_contents($path) : '';
        if ($content === '') {
            jc_recovery_respond(['ok' => false, 'error' => 'missing product-pages.php'], 500);
        }
        if (str_contains($content, '$page = justccell_product_page($slug);')) {
            // continue to patch below
        } elseif (str_contains($content, 'justccell_catalog_item($slug)')) {
            jc_recovery_respond(['ok' => true, 'patched' => false, 'message' => 'already patched']);
        }
        $old = "function justccell_product_url(string \$slug): string\n{\n    \$page = justccell_product_page(\$slug);\n    if (\$page === null) {\n        return justccell_inquiry_url(\$slug);\n    }\n    return home_url('/' . \$page['category'] . '/' . \$slug . '/');\n}";
        $new = "function justccell_product_url(string \$slug): string\n{\n    if (\$slug === '') {\n        return home_url('/');\n    }\n\n    if (function_exists('justccell_catalog_item')) {\n        \$item = justccell_catalog_item(\$slug);\n        if (is_array(\$item) && (\$item['category'] ?? '') !== '') {\n            return home_url('/' . trim((string) \$item['category'], '/') . '/' . trim(\$slug, '/') . '/');\n        }\n    }\n\n    \$seed = justccell_product_pages()[\$slug] ?? null;\n    if (is_array(\$seed) && (\$seed['category'] ?? '') !== '') {\n        return home_url('/' . trim((string) \$seed['category'], '/') . '/' . trim(\$slug, '/') . '/');\n    }\n\n    if (function_exists('justccell_woo_product_id_by_slug')) {\n        \$pid = justccell_woo_product_id_by_slug(\$slug);\n        if (\$pid > 0) {\n            \$cats = wp_get_post_terms(\$pid, 'product_cat', ['fields' => 'slugs']);\n            if (is_array(\$cats)) {\n                foreach (\$cats as \$cslug) {\n                    if (array_key_exists(\$cslug, justccell_product_category_labels())) {\n                        return home_url('/' . \$cslug . '/' . trim(\$slug, '/') . '/');\n                    }\n                }\n            }\n        }\n    }\n\n    return justccell_inquiry_url(\$slug);\n}";
        if (!str_contains($content, $old)) {
            jc_recovery_respond(['ok' => false, 'error' => 'pattern not found'], 500);
        }
        $written = file_put_contents($path, str_replace($old, $new, $content, $count));
        jc_recovery_respond(['ok' => $written !== false, 'replacements' => $count]);
    }

    if ($action === 'enable_media_migration') {
        $path = get_template_directory() . '/inc/media-migration.php';
        $off  = $path . '.off';
        $ok   = is_file($off) && !is_file($path) && rename($off, $path);
        jc_recovery_respond(['ok' => $ok, 'path' => $path]);
    }

    if ($action === 'last_errors') {
        $paths = [
            WP_CONTENT_DIR . '/jc-last-fatal.log',
            WP_CONTENT_DIR . '/debug.log',
            ABSPATH . 'error_log',
            WP_CONTENT_DIR . '/uploads/debug-log-' . gmdate('Y-m-d') . '.log',
        ];
        $out = [];
        foreach ($paths as $path) {
            if (!is_readable($path)) {
                continue;
            }
            $lines = file($path);
            $out[$path] = is_array($lines) ? array_slice($lines, -40) : [];
        }
        jc_recovery_respond(['ok' => true, 'logs' => $out]);
    }

    if ($action === 'grid_stats') {
        global $wpdb;
        $total = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_status = 'inherit'"
        );
        $legacy = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
             WHERE meta_key = '_justccell_legacy_media' AND meta_value = '1'"
        );
        $missing_meta = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attachment_metadata'
             WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
               AND (m.meta_id IS NULL OR m.meta_value = '' OR m.meta_value = 'a:0:{}')"
        );
        $no_thumb = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attachment_metadata'
             WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
               AND m.meta_value NOT LIKE '%s:9:\"thumbnail\"%'
               AND m.meta_value NOT LIKE '%\"thumbnail\"%'"
        );
        $path_counts = [];
        foreach (['2026/09', '2026/08', '2025/09', '2025/08', '2024/'] as $prefix) {
            $like = $wpdb->esc_like($prefix) . '%';
            $path_counts[$prefix] = (int) $wpdb->get_var($wpdb->prepare(
                "SELECT COUNT(*) FROM {$wpdb->posts} p
                 INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file'
                 WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
                   AND m.meta_value LIKE %s",
                $like
            ));
        }
        $by_month = $wpdb->get_results(
            "SELECT DATE_FORMAT(post_date, '%Y-%m') AS ym, COUNT(*) AS n
             FROM {$wpdb->posts}
             WHERE post_type = 'attachment' AND post_status = 'inherit'
             GROUP BY ym
             ORDER BY ym DESC
             LIMIT 12",
            ARRAY_A
        );
        $sample = $wpdb->get_results(
            "SELECT p.ID, p.post_date, p.post_mime_type,
                    file.meta_value AS attached_file,
                    CASE WHEN leg.meta_id IS NULL THEN 0 ELSE 1 END AS legacy
             FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} file ON file.post_id = p.ID AND file.meta_key = '_wp_attached_file'
             LEFT JOIN {$wpdb->postmeta} leg ON leg.post_id = p.ID
               AND leg.meta_key = '_justccell_legacy_media' AND leg.meta_value = '1'
             WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
             ORDER BY p.post_date DESC
             LIMIT 20",
            ARRAY_A
        );
        jc_recovery_respond([
            'ok'           => true,
            'total'        => $total,
            'legacy'       => $legacy,
            'missing_meta' => $missing_meta,
            'no_thumb'     => $no_thumb,
            'path_counts'  => $path_counts,
            'by_month'     => is_array($by_month) ? $by_month : [],
            'newest'       => is_array($sample) ? $sample : [],
            'visible_est'  => max(0, $total - $legacy),
        ]);
    }

    if ($action === 'grid_query_bench') {
        $start = microtime(true);
        $query = new WP_Query([
            'post_type'             => 'attachment',
            'post_status'           => 'inherit',
            'posts_per_page'        => 40,
            'paged'                 => 1,
            'orderby'               => 'date',
            'order'                 => 'DESC',
            'suppress_filters'      => true,
            'cache_results'         => true,
            'update_post_meta_cache'=> true,
            'update_post_term_cache'=> false,
        ]);
        $items = [];
        foreach ($query->posts as $post) {
            $js = wp_prepare_attachment_for_js($post);
            if (!is_array($js)) {
                continue;
            }
            $thumb = '';
            if (!empty($js['sizes']['thumbnail']['url'])) {
                $thumb = (string) $js['sizes']['thumbnail']['url'];
            } elseif (!empty($js['sizes']['medium']['url'])) {
                $thumb = (string) $js['sizes']['medium']['url'];
            } else {
                $thumb = (string) ($js['url'] ?? '');
            }
            $items[] = [
                'id'       => (int) ($js['id'] ?? 0),
                'filename' => (string) ($js['filename'] ?? ''),
                'thumb'    => $thumb,
            ];
            if (count($items) >= 8) {
                break;
            }
        }
        jc_recovery_respond([
            'ok'      => true,
            'found'   => (int) $query->found_posts,
            'got'     => count($query->posts),
            'ms'      => (int) round((microtime(true) - $start) * 1000),
            'sample'  => $items,
        ]);
    }

    if ($action === 'gen_grid_thumbs') {
        if (function_exists('wp_raise_memory_limit')) {
            wp_raise_memory_limit('image');
        }
        require_once ABSPATH . 'wp-admin/includes/file.php';
        require_once ABSPATH . 'wp-admin/includes/image.php';

        $limit  = isset($_GET['limit']) ? max(1, min(8, (int) $_GET['limit'])) : 4;
        $cursor = (int) get_option('jc_grid_thumb_cursor', 0);
        if ($cursor < 1) {
            $cursor = 2147483647;
        }

        global $wpdb;
        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts}
             WHERE post_type = 'attachment'
               AND post_status = 'inherit'
               AND post_mime_type LIKE %s
               AND ID < %d
             ORDER BY ID DESC
             LIMIT 80",
            'image/%',
            $cursor
        ));

        $did     = 0;
        $scanned = 0;
        $errors  = [];
        $last    = '';
        $next    = $cursor;

        foreach (is_array($ids) ? $ids : [] as $raw) {
            $id   = (int) $raw;
            $next = $id;
            $scanned++;

            $file = get_attached_file($id);
            if (!is_string($file) || $file === '' || !is_readable($file)) {
                continue;
            }

            $ext      = strtolower((string) pathinfo($file, PATHINFO_EXTENSION));
            $expected = dirname($file) . '/' . pathinfo($file, PATHINFO_FILENAME) . '-150x150.' . $ext;
            $meta     = wp_get_attachment_metadata($id);
            $meta     = is_array($meta) ? $meta : [];
            $named    = isset($meta['sizes']['thumbnail']['file']) ? (string) $meta['sizes']['thumbnail']['file'] : '';
            $named_path = $named !== '' ? dirname($file) . '/' . $named : '';
            $has_thumb  = ($named_path !== '' && $named_path !== $file && is_file($named_path))
                || (is_file($expected) && $expected !== $file);
            if ($has_thumb) {
                continue;
            }

            $info = @getimagesize($file);
            if (!is_array($info)) {
                continue;
            }
            if ((int) $info[0] <= 150 && (int) $info[1] <= 150) {
                continue;
            }

            $editor = wp_get_image_editor($file);
            if (is_wp_error($editor)) {
                $errors[] = $id . ':' . $editor->get_error_message();
                continue;
            }

            $resized = $editor->multi_resize([
                'thumbnail' => ['width' => 150, 'height' => 150, 'crop' => true],
                'medium'    => ['width' => 300, 'height' => 300, 'crop' => false],
            ]);
            if (!is_array($resized) || $resized === []) {
                $errors[] = $id . ':resize-empty';
                continue;
            }

            if (!isset($meta['sizes']) || !is_array($meta['sizes'])) {
                $meta['sizes'] = [];
            }
            foreach ($resized as $size => $data) {
                if (is_array($data) && !empty($data['file'])) {
                    $meta['sizes'][$size] = $data;
                }
            }
            if (empty($meta['width'])) {
                $meta['width']  = (int) $info[0];
                $meta['height'] = (int) $info[1];
                $meta['file']   = _wp_relative_upload_path($file);
            }
            wp_update_attachment_metadata($id, $meta);
            $did++;
            $last = basename($file);
            if ($did >= $limit) {
                break;
            }
        }

        $done = !is_array($ids) || $ids === [];
        update_option('jc_grid_thumb_cursor', $done ? 0 : $next, false);

        jc_recovery_respond([
            'ok'      => true,
            'did'     => $did,
            'scanned' => $scanned,
            'last'    => $last,
            'cursor'  => $done ? 0 : $next,
            'done'    => $done,
            'errors'  => array_slice($errors, 0, 6),
        ]);
    }

    if ($action === 'mark_slow_grid_legacy') {
        global $wpdb;
        $ids = $wpdb->get_col(
            "SELECT p.ID FROM {$wpdb->posts} p
             LEFT JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attachment_metadata'
             LEFT JOIN {$wpdb->postmeta} leg ON leg.post_id = p.ID
               AND leg.meta_key = '_justccell_legacy_media' AND leg.meta_value = '1'
             WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
               AND leg.meta_id IS NULL
               AND (
                    m.meta_id IS NULL OR m.meta_value = '' OR m.meta_value = 'a:0:{}'
                    OR (
                        m.meta_value NOT LIKE '%s:9:\"thumbnail\"%'
                        AND m.meta_value NOT LIKE '%\"thumbnail\"%'
                    )
               )
             LIMIT 400"
        );
        $marked = 0;
        if (is_array($ids)) {
            foreach ($ids as $raw) {
                $id = (int) $raw;
                if ($id > 0) {
                    update_post_meta($id, '_justccell_legacy_media', '1', true);
                    $marked++;
                }
            }
        }
        jc_recovery_respond(['ok' => true, 'marked' => $marked, 'batch' => is_array($ids) ? count($ids) : 0]);
    }

    if ($action === 'bloated_meta') {
        global $wpdb;
        $rows = $wpdb->get_results(
            "SELECT post_id, meta_key, LENGTH(meta_value) AS len
             FROM {$wpdb->postmeta}
             WHERE meta_key NOT LIKE '\\_%'
             ORDER BY len DESC
             LIMIT 40",
            ARRAY_A
        );
        jc_recovery_respond(['ok' => true, 'rows' => is_array($rows) ? $rows : []]);
    }

    if ($action === 'trim_clone_features') {
        global $wpdb;
        $max_rows = isset($_GET['max']) ? max(1, (int) $_GET['max']) : 24;
        $fixed    = 0;
        $rows     = $wpdb->get_results(
            "SELECT post_id, meta_value FROM {$wpdb->postmeta}
             WHERE meta_key = 'clone_features' AND LENGTH(meta_value) > 50000",
            ARRAY_A
        );
        if (is_array($rows)) {
            foreach ($rows as $row) {
                $pid   = (int) ($row['post_id'] ?? 0);
                $value = maybe_unserialize((string) ($row['meta_value'] ?? ''));
                if (!is_array($value) || count($value) <= $max_rows) {
                    continue;
                }
                $trimmed = array_slice($value, 0, $max_rows);
                update_post_meta($pid, 'clone_features', $trimmed);
                $fixed++;
            }
        }
        jc_recovery_respond(['ok' => true, 'fixed' => $fixed, 'max_rows' => $max_rows]);
    }

    if ($action === 'run_scan_dupes') {
        if (!function_exists('jc_media_repair_get_cached_index')) {
            jc_recovery_respond(['ok' => false, 'error' => 'index missing'], 500);
        }
        $index = jc_media_repair_get_cached_index();
        $pairs = get_option('jc_media_repair_pending_pairs', []);
        $pairs = is_array($pairs) ? $pairs : [];
        $claimed_old = [];
        foreach ($pairs as $pair) {
            if (is_array($pair) && isset($pair['old_id'])) {
                $claimed_old[(int) $pair['old_id']] = true;
            }
        }
        $found = 0;
        foreach ($index as $key => $ids) {
            if (!is_array($ids) || count($ids) < 2) {
                continue;
            }
            $newest = (int) max($ids);
            if ($newest < 1 || get_post_meta($newest, '_justccell_legacy_media', true) === '1') {
                continue;
            }
            foreach ($ids as $raw_old) {
                $old_id = (int) $raw_old;
                if ($old_id >= $newest || isset($claimed_old[$old_id])) {
                    continue;
                }
                if (get_post_meta($old_id, '_justccell_replaced_by', true) !== '') {
                    continue;
                }
                $claimed_old[$old_id] = true;
                $pairs[] = [
                    'old_id'    => $old_id,
                    'new_id'    => $newest,
                    'suggested' => (string) $key,
                ];
                $found++;
            }
        }
        update_option('jc_media_repair_pending_pairs', $pairs, false);
        jc_recovery_respond(['ok' => true, 'added' => $found, 'total_pairs' => count($pairs)]);
    }

    if ($action === 'run_scan_map') {
        if (!function_exists('jc_media_repair_scan_from_map')) {
            jc_recovery_respond(['ok' => false, 'error' => 'scan_from_map missing — deploy reconnect 1.4.2'], 500);
        }
        $r = jc_media_repair_scan_from_map();
        jc_recovery_respond(['ok' => true] + $r);
    }

    if ($action === 'run_apply_batch') {
        if (!function_exists('jc_media_repair_apply_chunk')) {
            jc_recovery_respond(['ok' => false, 'error' => 'repair plugin inactive'], 500);
        }
        $r = jc_media_repair_apply_chunk();
        jc_recovery_respond(['ok' => empty($r['error'])] + $r);
    }

    if ($action === 'run_delete_batch') {
        if (!function_exists('jc_media_repair_delete_legacy_chunk')) {
            jc_recovery_respond(['ok' => false, 'error' => 'repair plugin inactive'], 500);
        }
        delete_option('jc_media_repair_delete_offset');
        $r = jc_media_repair_delete_legacy_chunk();
        jc_recovery_respond(['ok' => true] + $r);
    }

    if ($action === 'run_unmark_fresh') {
        if (!function_exists('jc_media_repair_unmark_chunk')) {
            jc_recovery_respond(['ok' => false, 'error' => 'repair plugin inactive'], 500);
        }
        $r = jc_media_repair_unmark_chunk(0);
        jc_recovery_respond([
            'ok'      => true,
            'cleared' => (int) ($r['cleared'] ?? 0),
            'summary' => function_exists('jc_media_repair_summary') ? jc_media_repair_summary() : [],
        ]);
    }

    if ($action === 'run_mark_legacy_all') {
        if (!function_exists('jc_media_repair_mark_legacy_chunk')) {
            jc_recovery_respond(['ok' => false, 'error' => 'repair plugin inactive'], 500);
        }
        $total = 0;
        $phase = 'non_seo';
        for ($i = 0; $i < 80; $i++) {
            $r = jc_media_repair_mark_legacy_chunk($phase);
            $total += (int) ($r['marked'] ?? 0);
            if (!empty($r['done']) && ($r['phase'] ?? '') === 'complete') {
                break;
            }
            if (!empty($r['done']) && ($r['phase'] ?? '') === 'dupes') {
                $phase = 'dupes';
            }
        }
        jc_recovery_respond(['ok' => true, 'marked' => $total, 'summary' => function_exists('jc_media_repair_summary') ? jc_media_repair_summary() : []]);
    }

    if ($action === 'patch_theme_migration_stub') {
        $path    = get_template_directory() . '/inc/media-migration.php';
        $content = is_readable($path) ? (string) file_get_contents($path) : '';
        $needle  = "function justccell_render_media_migration_page(): void\n{\n    if (!current_user_can('upload_files')) {";
        $replace = "function justccell_render_media_migration_page(): void\n{\n    if (function_exists('jc_media_repair_render_page')) {\n        jc_media_repair_render_page();\n        return;\n    }\n\n    if (!current_user_can('upload_files')) {";
        if (str_contains($content, "function_exists('jc_media_repair_render_page')")) {
            jc_recovery_respond(['ok' => true, 'patched' => false, 'message' => 'already patched']);
        }
        if (!str_contains($content, $needle)) {
            jc_recovery_respond(['ok' => false, 'error' => 'pattern not found'], 500);
        }
        $written = file_put_contents($path, str_replace($needle, $replace, $content, $count));
        jc_recovery_respond(['ok' => $written !== false, 'replacements' => $count]);
    }

    if ($action === 'switch_theme') {
        $theme = sanitize_key((string) ($_GET['theme'] ?? 'twentytwentyfive'));
        if ($theme === '') {
            jc_recovery_respond(['ok' => false, 'error' => 'invalid theme'], 400);
        }
        switch_theme($theme);
        jc_recovery_respond(['ok' => true, 'theme' => $theme]);
    }

    jc_recovery_respond(['ok' => false, 'error' => 'unknown action'], 400);
}, 0);
