<?php
/**
 * One-shot media reconnect repair for justccell.com.
 * Upload to: wp-content/mu-plugins/jc-media-reconnect-repair.php
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function jc_media_repair_normalized_basename(string $filename): string
{
    $filename = strtolower(trim(basename($filename)));
    if ($filename === '') {
        return '';
    }
    $name = pathinfo($filename, PATHINFO_FILENAME);
    $ext  = strtolower((string) pathinfo($filename, PATHINFO_EXTENSION));
    if (is_string($name) && $name !== '') {
        $name = (string) preg_replace('/-\d+$/', '', $name);
    }
    return $ext !== '' ? $name . '.' . $ext : (string) $name;
}

/**
 * @return array<string, list<int>>
 */
function jc_media_repair_index(): array
{
    static $index = null;
    if (is_array($index)) {
        return $index;
    }

    global $wpdb;
    $rows = $wpdb->get_results(
        "SELECT p.ID, m.meta_value AS attached_file
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file'
         WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'",
        ARRAY_A
    );
    $index = [];
    if (!is_array($rows)) {
        return $index;
    }
    foreach ($rows as $row) {
        $id  = (int) ($row['ID'] ?? 0);
        $key = jc_media_repair_normalized_basename((string) ($row['attached_file'] ?? ''));
        if ($id < 1 || $key === '') {
            continue;
        }
        $index[$key][] = $id;
    }
    foreach ($index as &$ids) {
        sort($ids, SORT_NUMERIC);
    }
    unset($ids);

    return $index;
}

function jc_media_repair_unmark_fresh_uploads(): int
{
    $index   = jc_media_repair_index();
    $cleared = 0;
    foreach ($index as $ids) {
        if ($ids === []) {
            continue;
        }
        $newest = max($ids);
        $base   = jc_media_repair_normalized_basename((string) get_attached_file($newest));
        if ($base === '' || !str_starts_with($base, 'justccell-')) {
            continue;
        }
        if (get_post_meta($newest, '_justccell_legacy_media', true) === '1') {
            delete_post_meta($newest, '_justccell_legacy_media');
            $cleared++;
        }
    }
    return $cleared;
}

function jc_media_repair_find_new_id(string $filename, int $old_id): int
{
    $target = jc_media_repair_normalized_basename($filename);
    if ($target === '') {
        return 0;
    }
    $ids = jc_media_repair_index()[$target] ?? [];
    for ($i = count($ids) - 1; $i >= 0; $i--) {
        $id = (int) $ids[$i];
        if ($id <= $old_id) {
            break;
        }
        if (get_post_meta($id, '_justccell_replaced_by', true) !== '') {
            continue;
        }
        return $id;
    }
    return 0;
}

/**
 * @return list<array{old_id:int,new_id:int,suggested:string}>
 */
function jc_media_repair_match_pairs(): array
{
    if (!function_exists('justccell_media_inventory_rows')) {
        return [];
    }

    $pairs       = [];
    $claimed_new = [];
    foreach (justccell_media_inventory_rows() as $row) {
        $old_id = (int) ($row['id'] ?? 0);
        if ($old_id < 1) {
            continue;
        }
        if (get_post_meta($old_id, '_justccell_replaced_by', true) !== '') {
            continue;
        }
        $suggested = strtolower((string) ($row['suggested_filename'] ?? ''));
        if ($suggested === '') {
            continue;
        }
        $new_id = jc_media_repair_find_new_id($suggested, $old_id);
        if ($new_id < 1 || $new_id <= $old_id) {
            continue;
        }
        if (isset($claimed_new[$new_id])) {
            continue;
        }
        $claimed_new[$new_id] = true;
        $pairs[] = [
            'old_id'    => $old_id,
            'new_id'    => $new_id,
            'suggested' => $suggested,
        ];
    }
    return $pairs;
}

add_action('admin_init', static function (): void {
    $page = isset($_GET['page']) ? sanitize_key((string) wp_unslash($_GET['page'])) : '';
    if ($page !== 'justccell-media-replace' || !current_user_can('upload_files')) {
        return;
    }

    if (!isset($_POST['jc_media_repair_action'])) {
        return;
    }
    check_admin_referer('jc_media_repair');

    $action = sanitize_key((string) wp_unslash($_POST['jc_media_repair_action']));
    if ($action === 'unmark_fresh') {
        $n = jc_media_repair_unmark_fresh_uploads();
        add_action('admin_notices', static function () use ($n): void {
            echo '<div class="notice notice-success"><p>'
                . esc_html(sprintf('Cleared legacy tag from %d fresh upload(s). Refresh this page.', $n))
                . '</p></div>';
        });
        return;
    }

    if ($action === 'preview_pairs') {
        $pairs = jc_media_repair_match_pairs();
        add_action('admin_notices', static function () use ($pairs): void {
            echo '<div class="notice notice-info"><p>'
                . esc_html(sprintf('Repair matcher found %d pair(s) ready to reconnect.', count($pairs)))
                . '</p></div>';
        });
        return;
    }

    if ($action === 'apply_pairs' && function_exists('justccell_media_remap_attachment')) {
        $pairs = jc_media_repair_match_pairs();
        $done  = 0;
        foreach ($pairs as $pair) {
            if (justccell_media_remap_attachment((int) $pair['old_id'], (int) $pair['new_id'])) {
                $done++;
            }
        }
        add_action('admin_notices', static function () use ($done): void {
            echo '<div class="notice notice-success"><p>'
                . esc_html(sprintf('Reconnected %d image(s) using repair matcher.', $done))
                . '</p></div>';
        });
    }
});

add_action('admin_notices', static function (): void {
    $page = isset($_GET['page']) ? sanitize_key((string) wp_unslash($_GET['page'])) : '';
    if ($page !== 'justccell-media-replace' || !current_user_can('upload_files')) {
        return;
    }

    $pairs = jc_media_repair_match_pairs();
    echo '<div class="notice notice-warning" style="padding:12px 16px"><p><strong>Media reconnect repair (0.9.71)</strong> — '
        . esc_html(sprintf('Improved matcher sees %d pair(s). Theme button not deployed yet; use these instead:', count($pairs)))
        . '</p><form method="post" style="margin-top:8px;display:flex;gap:8px;flex-wrap:wrap">'
        . wp_nonce_field('jc_media_repair', '_wpnonce', true, false)
        . '<button class="button" name="jc_media_repair_action" value="unmark_fresh" type="submit">1. Unmark fresh SEO uploads</button>'
        . '<button class="button" name="jc_media_repair_action" value="preview_pairs" type="submit">2. Refresh match count</button>'
        . '<button class="button button-primary" name="jc_media_repair_action" value="apply_pairs" type="submit" onclick="return confirm(\'Reconnect all matched images to site content?\');">3. Reconnect all (' . count($pairs) . ')</button>'
        . '</form></div>';
});
