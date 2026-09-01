<?php
/**
 * Plugin Name: Justccell Media Reconnect Repair
 * Description: Safe batched media replacement reconnect for justccell.com.
 * Version: 1.4.4
 * Author: Rank Ray
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JC_MEDIA_REPAIR_PAIRS_OPTION   = 'jc_media_repair_pending_pairs';
const JC_MEDIA_REPAIR_SCAN_OPTION    = 'jc_media_repair_scan_offset';
const JC_MEDIA_REPAIR_DELETE_OPTION  = 'jc_media_repair_delete_offset';
const JC_MEDIA_REPAIR_SCAN_CHUNK     = 40;
const JC_MEDIA_REPAIR_DELETE_CHUNK   = 15;
const JC_MEDIA_REPAIR_INDEX_TRANSIENT = 'jc_media_repair_file_index';
const JC_MEDIA_REPAIR_MARK_CHUNK       = 200;

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

/**
 * @return array<string, list<int>>
 */
function jc_media_repair_get_cached_index(bool $rebuild = false): array
{
    if ($rebuild) {
        delete_transient(JC_MEDIA_REPAIR_INDEX_TRANSIENT);
    }
    $cached = get_transient(JC_MEDIA_REPAIR_INDEX_TRANSIENT);
    if (is_array($cached)) {
        return $cached;
    }
    $index = jc_media_repair_index();
    set_transient(JC_MEDIA_REPAIR_INDEX_TRANSIENT, $index, 30 * MINUTE_IN_SECONDS);

    return $index;
}

function jc_media_repair_is_fresh_upload(int $id, array $index): bool
{
    $file = (string) get_attached_file($id);
    $key  = jc_media_repair_normalized_basename($file);
    if ($key === '' || !str_starts_with($key, 'justccell-')) {
        return false;
    }
    $ids = $index[$key] ?? [];
    if ($ids === []) {
        return false;
    }

    return max($ids) === $id && get_post_meta($id, '_justccell_legacy_media', true) !== '1';
}

function jc_media_repair_find_new_id(string $filename, int $old_id, array $index): int
{
    $target = jc_media_repair_normalized_basename($filename);
    if ($target === '') {
        return 0;
    }
    $ids = $index[$target] ?? [];
    for ($i = count($ids) - 1; $i >= 0; $i--) {
        $id = (int) $ids[$i];
        if ($id <= $old_id) {
            break;
        }
        if (get_post_meta($id, '_justccell_replaced_by', true) !== '') {
            continue;
        }
        if (get_post_meta($id, '_justccell_legacy_media', true) === '1') {
            continue;
        }

        return $id;
    }

    return 0;
}

/**
 * Match old inventory IDs to newest uploads using the bundled CSV map.
 *
 * @return array{done:bool,total_pairs:int,unmatched:int,already:int,error?:string}
 */
function jc_media_repair_scan_from_map(): array
{
    $path = __DIR__ . '/jc-media-id-map.json';
    $raw  = is_readable($path) ? (string) file_get_contents($path) : '';
    $map  = json_decode($raw, true);
    if (!is_array($map) || $map === []) {
        return ['done' => true, 'total_pairs' => 0, 'unmatched' => 0, 'already' => 0, 'error' => 'map missing'];
    }

    $index       = jc_media_repair_get_cached_index(true);
    $pairs       = [];
    $claimed_new = [];
    $unmatched   = 0;
    $already     = 0;

    foreach ($map as $old_raw => $filename) {
        $old_id = (int) $old_raw;
        if ($old_id < 1) {
            continue;
        }
        if (get_post_meta($old_id, '_justccell_replaced_by', true) !== '') {
            $already++;
            continue;
        }
        $new_id = jc_media_repair_find_new_id((string) $filename, $old_id, $index);
        if ($new_id < 1 || isset($claimed_new[$new_id])) {
            $unmatched++;
            continue;
        }
        $claimed_new[$new_id] = true;
        $pairs[] = [
            'old_id'    => $old_id,
            'new_id'    => $new_id,
            'suggested' => (string) $filename,
        ];
    }

    update_option(JC_MEDIA_REPAIR_PAIRS_OPTION, $pairs, false);
    delete_option(JC_MEDIA_REPAIR_SCAN_OPTION);

    return [
        'done'        => true,
        'total_pairs' => count($pairs),
        'unmatched'   => $unmatched,
        'already'     => $already,
    ];
}

/**
 * @return array{done:bool,scanned:int,pairs:int,total_pairs:int,offset?:int,phase:string,total_attachments:int}
 */
function jc_media_repair_scan_chunk(): array
{
    global $wpdb;

    if (function_exists('set_time_limit')) {
        set_time_limit(120);
    }

    $total_attachments = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_status = 'inherit'"
    );

    $offset = (int) get_option(JC_MEDIA_REPAIR_SCAN_OPTION, 0);
    $phase  = 'scanning';

    $ids = $wpdb->get_col($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts}
         WHERE post_type = 'attachment' AND post_status = 'inherit'
         ORDER BY ID ASC
         LIMIT %d OFFSET %d",
        JC_MEDIA_REPAIR_SCAN_CHUNK,
        $offset
    ));
    if (!is_array($ids) || $ids === []) {
        delete_option(JC_MEDIA_REPAIR_SCAN_OPTION);
        $pairs = get_option(JC_MEDIA_REPAIR_PAIRS_OPTION, []);
        return [
            'done'              => true,
            'scanned'           => 0,
            'pairs'             => 0,
            'total_pairs'       => is_array($pairs) ? count($pairs) : 0,
            'phase'             => 'complete',
            'total_attachments' => $total_attachments,
        ];
    }

    $index       = jc_media_repair_get_cached_index();
    $pairs       = get_option(JC_MEDIA_REPAIR_PAIRS_OPTION, []);
    $pairs       = is_array($pairs) ? $pairs : [];
    $claimed_new = [];
    foreach ($pairs as $pair) {
        if (is_array($pair) && isset($pair['new_id'])) {
            $claimed_new[(int) $pair['new_id']] = true;
        }
    }

    $found = 0;
    foreach ($ids as $raw_id) {
        $old_id = (int) $raw_id;
        if ($old_id < 1) {
            continue;
        }
        if (get_post_meta($old_id, '_justccell_replaced_by', true) !== '') {
            continue;
        }
        if (jc_media_repair_is_fresh_upload($old_id, $index)) {
            continue;
        }
        $suggested = '';
        if (function_exists('justccell_desired_pretty_basename')) {
            $suggested = strtolower(justccell_desired_pretty_basename($old_id));
        }
        if ($suggested === '') {
            $suggested = jc_media_repair_normalized_basename((string) get_attached_file($old_id));
        }
        if ($suggested === '') {
            continue;
        }
        $new_id = jc_media_repair_find_new_id($suggested, $old_id, $index);
        if ($new_id < 1 || $new_id <= $old_id || isset($claimed_new[$new_id])) {
            continue;
        }
        $claimed_new[$new_id] = true;
        $pairs[] = [
            'old_id'    => $old_id,
            'new_id'    => $new_id,
            'suggested' => $suggested,
        ];
        $found++;
    }

    update_option(JC_MEDIA_REPAIR_PAIRS_OPTION, $pairs, false);
    $next_offset = $offset + count($ids);
    update_option(JC_MEDIA_REPAIR_SCAN_OPTION, $next_offset, false);

    return [
        'done'              => count($ids) < JC_MEDIA_REPAIR_SCAN_CHUNK,
        'scanned'           => count($ids),
        'pairs'             => $found,
        'total_pairs'       => count($pairs),
        'offset'            => $next_offset,
        'phase'             => $phase,
        'total_attachments' => $total_attachments,
    ];
}

function jc_media_repair_reset_scan(): void
{
    delete_option(JC_MEDIA_REPAIR_SCAN_OPTION);
    delete_option(JC_MEDIA_REPAIR_PAIRS_OPTION);
    delete_transient(JC_MEDIA_REPAIR_INDEX_TRANSIENT);
}

/**
 * @param mixed $value
 * @return mixed
 */
function jc_media_repair_replace_id_in_value($value, int $old_id, int $new_id)
{
    if (is_numeric($value) && (int) $value === $old_id) {
        return $new_id;
    }
    if (is_string($value) && $value === (string) $old_id) {
        return (string) $new_id;
    }
    if (is_array($value)) {
        if (isset($value['ID']) && (int) $value['ID'] === $old_id) {
            $value['ID'] = $new_id;
        }
        if (isset($value['id']) && (int) $value['id'] === $old_id) {
            $value['id'] = $new_id;
        }
        foreach ($value as $key => $item) {
            $value[$key] = jc_media_repair_replace_id_in_value($item, $old_id, $new_id);
        }
    }

    return $value;
}

function jc_media_repair_replace_id_in_meta(int $old_id, int $new_id): int
{
    global $wpdb;
    $changed = 0;

    $wpdb->update(
        $wpdb->postmeta,
        ['meta_value' => (string) $new_id],
        ['meta_key' => '_thumbnail_id', 'meta_value' => (string) $old_id],
        ['%s'],
        ['%s', '%s']
    );
    $changed += (int) $wpdb->rows_affected;

    $meta_rows = $wpdb->get_results($wpdb->prepare(
        "SELECT post_id, meta_key, meta_value FROM {$wpdb->postmeta}
         WHERE meta_value = %s
            OR meta_value LIKE %s
            OR meta_value LIKE %s",
        (string) $old_id,
        '%;i:' . (int) $old_id . ';%',
        '%"' . (int) $old_id . '"%'
    ));
    if (!is_array($meta_rows)) {
        return $changed;
    }

    foreach ($meta_rows as $row) {
        $pid = (int) ($row->post_id ?? 0);
        $key = (string) ($row->meta_key ?? '');
        if ($pid < 1 || $key === '' || $key === '_justccell_legacy_media') {
            continue;
        }
        $raw     = (string) ($row->meta_value ?? '');
        $parsed  = maybe_unserialize($raw);
        $updated = jc_media_repair_replace_id_in_value($parsed, $old_id, $new_id);
        if ($updated === $parsed && $raw !== (string) $new_id) {
            if ($key === '_product_image_gallery' && str_contains($raw, (string) $old_id)) {
                $updated = str_replace((string) $old_id, (string) $new_id, $raw);
            } else {
                continue;
            }
        }
        if ($updated !== $parsed || (is_string($updated) && $updated !== $raw)) {
            update_post_meta($pid, $key, $updated);
            $changed++;
        }
    }

    return $changed;
}

/**
 * @return array{total:int,legacy:int,replaced:int,pending:int,fresh:int}
 */
function jc_media_repair_summary(): array
{
    global $wpdb;
    $stored = get_option(JC_MEDIA_REPAIR_PAIRS_OPTION, []);

    return [
        'total'    => (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'attachment' AND post_status = 'inherit'"
        ),
        'legacy'   => (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
             WHERE meta_key = '_justccell_legacy_media' AND meta_value = '1'"
        ),
        'replaced' => (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT post_id) FROM {$wpdb->postmeta}
             WHERE meta_key = '_justccell_replaced_by' AND meta_value != ''"
        ),
        'pending'  => is_array($stored) ? count($stored) : 0,
        'fresh'    => (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file'
             LEFT JOIN {$wpdb->postmeta} legacy ON legacy.post_id = p.ID
               AND legacy.meta_key = '_justccell_legacy_media' AND legacy.meta_value = '1'
             WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
               AND m.meta_value LIKE '%justccell-%' AND legacy.meta_id IS NULL"
        ),
    ];
}

function jc_media_repair_apply_pair(int $old_id, int $new_id): bool
{
    if ($old_id < 1 || $new_id < 1) {
        return false;
    }

    jc_media_repair_replace_id_in_meta($old_id, $new_id);
    update_post_meta($old_id, '_justccell_replaced_by', (string) $new_id, true);
    return true;
}

/**
 * @return array{done:int,remaining:int,error:string}
 */
function jc_media_repair_apply_chunk(): array
{
    if (function_exists('set_time_limit')) {
        set_time_limit(60);
    }

    $limit = 15;
    $pairs = get_option(JC_MEDIA_REPAIR_PAIRS_OPTION, []);
    if (!is_array($pairs) || $pairs === []) {
        return ['done' => 0, 'remaining' => 0, 'error' => ''];
    }

    $done  = 0;
    $error = '';
    for ($i = 0; $i < $limit && $pairs !== []; $i++) {
        $pair = array_shift($pairs);
        if (!is_array($pair)) {
            continue;
        }
        try {
            if (jc_media_repair_apply_pair((int) ($pair['old_id'] ?? 0), (int) ($pair['new_id'] ?? 0))) {
                $done++;
            }
        } catch (Throwable $e) {
            $error = $e->getMessage();
            array_unshift($pairs, $pair);
            break;
        }
    }
    update_option(JC_MEDIA_REPAIR_PAIRS_OPTION, $pairs, false);

    return [
        'done'      => $done,
        'remaining' => count($pairs),
        'error'     => $error,
    ];
}

/**
 * One-shot: unmark the newest justccell-* file per basename if it was tagged legacy.
 *
 * @return array{cleared:int,done:bool,remaining:int,offset:int}
 */
function jc_media_repair_unmark_chunk(int $offset = 0): array
{
    global $wpdb;

    $rows = $wpdb->get_results(
        "SELECT p.ID, m.meta_value AS attached_file
         FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file'
         WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
           AND m.meta_value LIKE '%justccell-%'",
        ARRAY_A
    );

    $newest_by_key = [];
    if (is_array($rows)) {
        foreach ($rows as $row) {
            $id  = (int) ($row['ID'] ?? 0);
            $key = jc_media_repair_normalized_basename((string) ($row['attached_file'] ?? ''));
            if ($id < 1 || $key === '' || !str_starts_with($key, 'justccell-')) {
                continue;
            }
            if (!isset($newest_by_key[$key]) || $id > $newest_by_key[$key]) {
                $newest_by_key[$key] = $id;
            }
        }
    }

    $cleared = 0;
    foreach ($newest_by_key as $id) {
        if (get_post_meta($id, '_justccell_legacy_media', true) === '1') {
            delete_post_meta($id, '_justccell_legacy_media');
            $cleared++;
        }
    }

    return [
        'cleared'   => $cleared,
        'done'      => true,
        'remaining' => 0,
        'offset'    => 0,
    ];
}

/**
 * Fast legacy tagging — no full inventory scan.
 *
 * @return array{marked:int,done:bool,remaining:int,phase:string}
 */
function jc_media_repair_mark_legacy_chunk(string $phase = 'non_seo'): array
{
    global $wpdb;

    if (function_exists('set_time_limit')) {
        set_time_limit(120);
    }

    if ($phase === 'non_seo') {
        $ids = $wpdb->get_col(
            "SELECT p.ID FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file'
             LEFT JOIN {$wpdb->postmeta} leg ON leg.post_id = p.ID
               AND leg.meta_key = '_justccell_legacy_media' AND leg.meta_value = '1'
             WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
               AND (m.meta_value NOT LIKE '%justccell-%' OR m.meta_value IS NULL OR m.meta_value = '')
               AND leg.meta_id IS NULL
             ORDER BY p.ID ASC
             LIMIT " . (int) JC_MEDIA_REPAIR_MARK_CHUNK
        );
        $marked = 0;
        if (is_array($ids)) {
            foreach ($ids as $raw_id) {
                $id = (int) $raw_id;
                if ($id > 0) {
                    update_post_meta($id, '_justccell_legacy_media', '1', true);
                    $marked++;
                }
            }
        }
        $remaining = (int) $wpdb->get_var(
            "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
             INNER JOIN {$wpdb->postmeta} m ON m.post_id = p.ID AND m.meta_key = '_wp_attached_file'
             LEFT JOIN {$wpdb->postmeta} leg ON leg.post_id = p.ID
               AND leg.meta_key = '_justccell_legacy_media' AND leg.meta_value = '1'
             WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
               AND (m.meta_value NOT LIKE '%justccell-%' OR m.meta_value IS NULL OR m.meta_value = '')
               AND leg.meta_id IS NULL"
        );

        return [
            'marked'    => $marked,
            'done'      => $remaining === 0,
            'remaining' => $remaining,
            'phase'     => $remaining === 0 ? 'dupes' : 'non_seo',
        ];
    }

    // Mark older duplicate justccell-* files (keep newest per basename).
    $index  = jc_media_repair_get_cached_index();
    $marked = 0;
    foreach ($index as $key => $ids) {
        if (!str_starts_with($key, 'justccell-') || count($ids) < 2) {
            continue;
        }
        $newest = max($ids);
        foreach ($ids as $id) {
            if ($id >= $newest) {
                continue;
            }
            if (get_post_meta($id, '_justccell_legacy_media', true) !== '1') {
                update_post_meta($id, '_justccell_legacy_media', '1', true);
                $marked++;
            }
        }
    }

    return [
        'marked'    => $marked,
        'done'      => true,
        'remaining' => 0,
        'phase'     => 'complete',
    ];
}

/**
 * @return array{deleted:int,skipped:int,done:bool,remaining:int}
 */
function jc_media_repair_delete_legacy_chunk(): array
{
    global $wpdb;

    if (function_exists('set_time_limit')) {
        set_time_limit(120);
    }

    $offset = (int) get_option(JC_MEDIA_REPAIR_DELETE_OPTION, 0);
    $ids    = $wpdb->get_col($wpdb->prepare(
        "SELECT p.ID FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} legacy
           ON legacy.post_id = p.ID AND legacy.meta_key = '_justccell_legacy_media' AND legacy.meta_value = '1'
         INNER JOIN {$wpdb->postmeta} replaced
           ON replaced.post_id = p.ID AND replaced.meta_key = '_justccell_replaced_by' AND replaced.meta_value != ''
         WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'
         ORDER BY p.ID ASC
         LIMIT %d OFFSET %d",
        JC_MEDIA_REPAIR_DELETE_CHUNK,
        $offset
    ));

    $deleted = 0;
    $skipped = 0;
    if (!is_array($ids) || $ids === []) {
        delete_option(JC_MEDIA_REPAIR_DELETE_OPTION);
        return ['deleted' => 0, 'skipped' => 0, 'done' => true, 'remaining' => 0];
    }

    foreach ($ids as $raw_id) {
        $id = (int) $raw_id;
        if ($id < 1 || get_post_type($id) !== 'attachment') {
            $skipped++;
            continue;
        }
        if (wp_delete_attachment($id, true)) {
            $deleted++;
        } else {
            $skipped++;
        }
    }

    $next = $offset + count($ids);
    update_option(JC_MEDIA_REPAIR_DELETE_OPTION, $next, false);

    $remaining = (int) $wpdb->get_var(
        "SELECT COUNT(DISTINCT p.ID) FROM {$wpdb->posts} p
         INNER JOIN {$wpdb->postmeta} legacy
           ON legacy.post_id = p.ID AND legacy.meta_key = '_justccell_legacy_media' AND legacy.meta_value = '1'
         INNER JOIN {$wpdb->postmeta} replaced
           ON replaced.post_id = p.ID AND replaced.meta_key = '_justccell_replaced_by' AND replaced.meta_value != ''
         WHERE p.post_type = 'attachment' AND p.post_status = 'inherit'"
    );

    return [
        'deleted'   => $deleted,
        'skipped'   => $skipped,
        'done'      => count($ids) < JC_MEDIA_REPAIR_DELETE_CHUNK,
        'remaining' => max(0, $remaining - $deleted),
    ];
}

function jc_media_repair_is_target_page(): bool
{
    $page = isset($_GET['page']) ? sanitize_key((string) wp_unslash($_GET['page'])) : '';
    return $page === 'justccell-media-replace' && current_user_can('upload_files');
}

add_action('wp_ajax_jc_media_repair_scan', static function (): void {
    check_ajax_referer('jc_media_repair', 'nonce');
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    if (isset($_POST['reset']) && (string) wp_unslash($_POST['reset']) === '1') {
        jc_media_repair_reset_scan();
    }
    wp_send_json_success(jc_media_repair_scan_chunk());
});

add_action('wp_ajax_jc_media_repair_apply', static function (): void {
    check_ajax_referer('jc_media_repair', 'nonce');
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    wp_send_json_success(jc_media_repair_apply_chunk());
});

add_action('wp_ajax_jc_media_repair_unmark', static function (): void {
    check_ajax_referer('jc_media_repair', 'nonce');
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    $offset = isset($_POST['offset']) ? (int) wp_unslash($_POST['offset']) : 0;
    wp_send_json_success(jc_media_repair_unmark_chunk($offset));
});

add_action('wp_ajax_jc_media_repair_warm', static function (): void {
    check_ajax_referer('jc_media_repair', 'nonce');
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    if (function_exists('set_time_limit')) {
        set_time_limit(300);
    }
    if (function_exists('justccell_attachment_usage_map')) {
        justccell_attachment_usage_map();
    }
    jc_media_repair_get_cached_index(true);
    wp_send_json_success(['ok' => true]);
});

add_action('wp_ajax_jc_media_repair_mark_legacy', static function (): void {
    check_ajax_referer('jc_media_repair', 'nonce');
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    $phase = isset($_POST['phase']) ? sanitize_key((string) wp_unslash($_POST['phase'])) : 'non_seo';
    if ($phase === '' || $phase === 'start') {
        $phase = 'non_seo';
    }
    wp_send_json_success(jc_media_repair_mark_legacy_chunk($phase));
});

add_action('wp_ajax_jc_media_repair_delete_legacy', static function (): void {
    check_ajax_referer('jc_media_repair', 'nonce');
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    if (isset($_POST['reset']) && (string) wp_unslash($_POST['reset']) === '1') {
        delete_option(JC_MEDIA_REPAIR_DELETE_OPTION);
    }
    wp_send_json_success(jc_media_repair_delete_legacy_chunk());
});

add_action('wp_ajax_jc_media_repair_status', static function (): void {
    check_ajax_referer('jc_media_repair', 'nonce');
    if (!current_user_can('upload_files')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    wp_send_json_success(jc_media_repair_summary());
});

add_action('admin_enqueue_scripts', static function (string $hook): void {
    if (!jc_media_repair_is_target_page()) {
        return;
    }

    wp_enqueue_script('jquery');
    $nonce = wp_create_nonce('jc_media_repair');
    $inline = <<<JS
jQuery(function ($) {
  const box = $('#jc-media-repair');
  if (!box.length) return;
  const ajax = (action, data) => $.ajax({
    url: ajaxurl,
    method: 'POST',
    timeout: 45000,
    data: Object.assign({ action, nonce: '{$nonce}' }, data || {})
  }).then((res) => res).catch((err) => {
    const msg = (err && err.statusText) ? err.statusText : 'request failed';
    return { success: false, data: { error: msg } };
  });
  const log = (msg) => {
    const ts = new Date().toLocaleTimeString();
    box.find('.jc-media-repair__log').prepend('<div>[' + ts + '] ' + msg + '</div>');
  };
  const setStatus = (msg) => { box.find('.jc-media-repair__status').text(msg); log(msg); };
  const sleep = (ms) => new Promise((r) => setTimeout(r, ms));
  const refreshSummary = async () => {
    const res = await ajax('jc_media_repair_status', {});
    if (!res.success) return;
    const s = res.data;
    box.find('.jc-media-repair__summary').text(
      'Library: ' + s.total + ' attachments · ' + s.fresh + ' fresh uploads · ' +
      s.legacy + ' legacy · ' + s.replaced + ' reconnected · ' + s.pending + ' queued'
    );
  };

  async function runMarkLegacy() {
    box.find('button').prop('disabled', true);
    let total = 0;
    let phase = 'non_seo';
    setStatus('Step 0: Tagging old non-SEO files as legacy (fixes slow grid)…');
    for (;;) {
      const res = await ajax('jc_media_repair_mark_legacy', { phase });
      if (!res.success) { setStatus('Step 0 failed.'); break; }
      total += res.data.marked || 0;
      const rem = res.data.remaining || 0;
      setStatus('Step 0: Marked ' + total + ' — ' + rem + ' non-SEO left (phase ' + phase + ')');
      if (res.data.done && res.data.phase === 'complete') {
        setStatus('Step 0 complete — ' + total + ' legacy tagged. Open Media → Grid to see your uploads.');
        break;
      }
      if (res.data.done && res.data.phase === 'dupes') {
        phase = 'dupes';
        setStatus('Step 0: Tagging older duplicate SEO files…');
        continue;
      }
      if (!res.data.done) continue;
      phase = 'dupes';
    }
    box.find('button').prop('disabled', false);
    refreshSummary();
  }

  async function runUnmark() {
    box.find('button').prop('disabled', true);
    setStatus('Step 1: Unmarking newest SEO uploads (one quick query)…');
    const res = await ajax('jc_media_repair_unmark', { offset: 0 });
    if (!res.success) {
      setStatus('Step 1 failed: ' + ((res.data && res.data.error) || 'timeout. Refresh and skip to Step 2.'));
    } else {
      setStatus('Step 1 complete — cleared ' + (res.data.cleared || 0) + ' upload(s). Continue to Step 2.');
    }
    box.find('button').prop('disabled', false);
    refreshSummary();
  }

  async function runScan() {
    box.find('button').prop('disabled', true);
    const t0 = Date.now();
    setStatus('Step 2a: Building usage index (1–3 min, please wait — not frozen)…');
    const warm = await ajax('jc_media_repair_warm', {});
    if (!warm.success) { setStatus('Step 2a failed building index.'); box.find('button').prop('disabled', false); return; }
    setStatus('Step 2b: Index ready in ' + Math.round((Date.now() - t0) / 1000) + 's. Scanning…');
    await ajax('jc_media_repair_scan', { reset: '1' });
    for (;;) {
      const res = await ajax('jc_media_repair_scan', {});
      if (!res.success) { setStatus('Scan failed — check log.'); break; }
      const elapsed = Math.round((Date.now() - t0) / 1000);
      const d = res.data;
      const pct = d.total_attachments ? Math.min(99, Math.round((d.offset || 0) / d.total_attachments * 100)) : 0;
      let phaseMsg = 'scanning';
      if (d.phase === 'warming_usage') phaseMsg = 'building usage map (slow, one-time)';
      if (d.phase === 'building_index') phaseMsg = 'indexing filenames';
      setStatus('Step 2: ' + phaseMsg + ' — ' + (d.total_pairs || 0) + ' matches, ~' + pct + '%, ' + elapsed + 's');
      if (d.done) {
        setStatus('Step 2 complete — ' + (d.total_pairs || 0) + ' pair(s) in ' + elapsed + 's.');
        break;
      }
    }
    box.find('button').prop('disabled', false);
    refreshSummary();
  }

  async function runApply() {
    if (!confirm('Reconnect matched images to pages/products? Runs safely one pair at a time.')) return;
    box.find('button').prop('disabled', true);
    let total = 0;
    setStatus('Step 3: Reconnecting…');
    for (;;) {
      const res = await ajax('jc_media_repair_apply', {});
      if (!res.success) { setStatus('Reconnect failed.'); break; }
      total += res.data.done || 0;
      const left = res.data.remaining || 0;
      if (res.data.error) { setStatus('Error: ' + res.data.error); box.find('button').prop('disabled', false); return; }
      setStatus('Step 3: Reconnected ' + total + ' — ' + left + ' remaining');
      if (left === 0) { setStatus('Step 3 complete — ' + total + ' image(s) reconnected. Check the live site, then run Step 4.'); break; }
    }
    box.find('button').prop('disabled', false);
    refreshSummary();
  }

  async function runDelete() {
    if (!confirm('Permanently delete old legacy files that were already reconnected?')) return;
    box.find('button').prop('disabled', true);
    await ajax('jc_media_repair_delete_legacy', { reset: '1' });
    let total = 0;
    setStatus('Step 4: Deleting legacy files…');
    for (;;) {
      const res = await ajax('jc_media_repair_delete_legacy', {});
      if (!res.success) { setStatus('Delete failed.'); break; }
      total += res.data.deleted || 0;
      setStatus('Step 4: Deleted ' + total + ' — ~' + (res.data.remaining || 0) + ' left');
      if (res.data.done) {
        setStatus('Step 4 complete — deleted ' + total + ' legacy file(s). Try Media Library grid view.');
        break;
      }
    }
    box.find('button').prop('disabled', false);
    refreshSummary();
  }

  box.on('click', '[data-jc-action="mark-legacy"]', runMarkLegacy);
  box.on('click', '[data-jc-action="unmark"]', runUnmark);
  box.on('click', '[data-jc-action="scan"]', runScan);
  box.on('click', '[data-jc-action="apply"]', runApply);
  box.on('click', '[data-jc-action="delete"]', runDelete);
});
JS;
    wp_add_inline_script('jquery', $inline);
});

add_action('admin_notices', static function (): void {
    if (!jc_media_repair_is_target_page()) {
        return;
    }

    $summary = jc_media_repair_summary();

    echo '<div id="jc-media-repair" class="notice notice-warning" style="padding:12px 16px;max-width:56rem">'
        . '<p><strong>Media reconnect (v1.4.1)</strong> — '
        . esc_html__('Run steps 0→4 in order. Live log below shows progress — do not close the tab during scan.', 'justccell')
        . '</p>'
        . '<p class="jc-media-repair__summary">' . esc_html(sprintf(
            'Library: %d attachments · %d fresh uploads · %d legacy · %d reconnected · %d queued',
            $summary['total'],
            $summary['fresh'],
            $summary['legacy'],
            $summary['replaced'],
            $summary['pending']
        )) . '</p>'
        . '<p class="jc-media-repair__status">' . esc_html__('Ready.', 'justccell') . '</p>'
        . '<div class="jc-media-repair__log" style="max-height:140px;overflow:auto;font-size:12px;margin:8px 0;background:#fff;border:1px solid #ddd;padding:8px"></div>'
        . '<p style="display:flex;gap:8px;flex-wrap:wrap;margin-top:8px">'
        . '<button type="button" class="button button-primary" data-jc-action="mark-legacy">0. Mark old files legacy (fast)</button>'
        . '<button type="button" class="button" data-jc-action="unmark">1. Unmark fresh uploads</button>'
        . '<button type="button" class="button" data-jc-action="scan">2. Scan for matches</button>'
        . '<button type="button" class="button button-primary" data-jc-action="apply">3. Reconnect (batched)</button>'
        . '<button type="button" class="button" data-jc-action="delete">4. Delete legacy files</button>'
        . '</p>'
        . '<p class="description">' . esc_html__(
            'After Step 3, spot-check homepage + a few product pages. Step 4 removes old broken files so the Media Library grid loads fast.',
            'justccell'
        ) . '</p>'
        . '</div>';
});

function jc_media_repair_render_page(): void
{
    if (!current_user_can('upload_files')) {
        wp_die(esc_html__('Forbidden', 'justccell'));
    }

    $summary = jc_media_repair_summary();

    echo '<div class="wrap"><h1>' . esc_html__('Media replacement', 'justccell') . '</h1>';
    echo '<p>' . esc_html__(
        'Your uploads are in the library. Use the yellow box above to reconnect them to pages/products, then delete the old broken files.',
        'justccell'
    ) . '</p>';

    echo '<h2>' . esc_html__('Status', 'justccell') . '</h2><ul>';
    echo '<li>' . esc_html(sprintf(__('Total attachments: %d', 'justccell'), $summary['total'])) . '</li>';
    echo '<li>' . esc_html(sprintf(__('Fresh SEO uploads (not legacy): %d', 'justccell'), $summary['fresh'])) . '</li>';
    echo '<li>' . esc_html(sprintf(__('Marked legacy: %d', 'justccell'), $summary['legacy'])) . '</li>';
    echo '<li>' . esc_html(sprintf(__('Already reconnected: %d', 'justccell'), $summary['replaced'])) . '</li>';
    echo '<li>' . esc_html(sprintf(__('Queued to reconnect: %d', 'justccell'), $summary['pending'])) . '</li>';
    echo '</ul>';

    echo '<h2>' . esc_html__('After reconnect', 'justccell') . '</h2>';
    echo '<p>' . esc_html__(
        'Open Media → Library → Grid view. It should load quickly once legacy files are deleted (Step 4).',
        'justccell'
    ) . '</p>';
    echo '</div>';
}

add_action('admin_menu', static function (): void {
    $hook = 'justccell_page_justccell-media-replace';
    remove_action($hook, 'justccell_render_media_migration_page');
    remove_submenu_page('justccell', 'justccell-media-replace');
    add_submenu_page(
        'justccell',
        __('Media replacement', 'justccell'),
        __('Media replace', 'justccell'),
        'upload_files',
        'justccell-media-replace',
        'jc_media_repair_render_page'
    );
}, 999);

add_action('init', static function (): void {
    remove_filter('ajax_query_attachments_args', 'justccell_media_library_query_args', 10);
}, 30);

/**
 * Keep Media Library grid AJAX cheap: skip WPML SQL, avoid unindexed
 * meta_query, and cap the page size. Legacy rows are excluded by ID.
 *
 * @param array<string,mixed> $args
 * @return array<string,mixed>
 */
function jc_media_repair_fast_grid_args(array $args): array
{
    $action = isset($_REQUEST['action']) ? (string) wp_unslash($_REQUEST['action']) : '';
    if ($action !== 'query-attachments') {
        return $args;
    }

    unset($args['meta_query'], $args['date_query']);

    $args['suppress_filters']       = true;
    $args['cache_results']          = true;
    $args['update_post_meta_cache'] = true;
    $args['update_post_term_cache'] = false;

    $per_page = isset($args['posts_per_page']) ? (int) $args['posts_per_page'] : 40;
    if ($per_page < 1 || $per_page > 40) {
        $args['posts_per_page'] = 40;
    }

    global $wpdb;
    $legacy_ids = $wpdb->get_col(
        "SELECT DISTINCT post_id FROM {$wpdb->postmeta}
         WHERE meta_key = '_justccell_legacy_media' AND meta_value = '1'"
    );
    if (is_array($legacy_ids) && $legacy_ids !== []) {
        $not_in = array_map('intval', $legacy_ids);
        $existing = $args['post__not_in'] ?? [];
        if (!is_array($existing)) {
            $existing = [];
        }
        $args['post__not_in'] = array_values(array_unique(array_merge($existing, $not_in)));
    }

    return $args;
}

add_filter('ajax_query_attachments_args', 'jc_media_repair_fast_grid_args', 20);
