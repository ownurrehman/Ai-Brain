<?php
/**
 * ACF Local JSON migration — Phase 0 (backup) and Phase 1 (product group dedup).
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_ACF_MIGRATION_PHASE0_OPT = 'justccell_acf_migration_phase0';
const JUSTCCELL_ACF_MIGRATION_PHASE1_OPT = 'justccell_acf_migration_phase1';
const JUSTCCELL_ACF_MIGRATION_PHASE2_OPT = 'justccell_acf_migration_phase2';
const JUSTCCELL_ACF_POSTMETA_BASELINE_OPT = 'justccell_acf_postmeta_baseline';
const JUSTCCELL_ACF_MIGRATION_REPORT_OPT = 'justccell_acf_migration_report';

/**
 * @return list<array{id: int, title: string, status: string, post_name: string, field_count: int, key: string}>
 */
function justccell_acf_find_field_group_posts_by_key(string $group_key): array
{
    if ($group_key === '' || !post_type_exists('acf-field-group') || !function_exists('acf_get_field_group')) {
        return [];
    }

    $posts = get_posts([
        'post_type'              => 'acf-field-group',
        'post_status'            => ['publish', 'acf-disabled', 'draft'],
        'posts_per_page'         => -1,
        'suppress_filters'       => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);

    $matches = [];
    foreach ($posts as $post) {
        if (!$post instanceof WP_Post) {
            continue;
        }
        $group = acf_get_field_group((int) $post->ID);
        if (!is_array($group)) {
            continue;
        }
        $key = (string) ($group['key'] ?? '');
        if ($key !== $group_key && $post->post_name !== $group_key) {
            continue;
        }
        $fields = function_exists('acf_get_fields') ? acf_get_fields((int) $post->ID) : [];
        $matches[] = [
            'id'          => (int) $post->ID,
            'title'       => (string) $post->post_title,
            'status'      => (string) $post->post_status,
            'post_name'   => (string) $post->post_name,
            'field_count' => justccell_acf_count_export_fields(is_array($fields) ? $fields : []),
            'key'         => $key !== '' ? $key : $group_key,
        ];
    }

    return $matches;
}

/**
 * @param list<array<string, mixed>> $fields
 */
function justccell_acf_count_export_fields(array $fields): int
{
    $count = 0;
    foreach ($fields as $field) {
        if (!is_array($field)) {
            continue;
        }
        ++$count;
    }

    return $count;
}

/**
 * @return array<string, mixed>
 */
function justccell_acf_product_postmeta_baseline(): array
{
    global $wpdb;

    $product_count = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->posts} WHERE post_type = 'product' AND post_status IN ('publish','draft','private')"
    );

    $clone_value_rows = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE p.post_type = 'product'
         AND pm.meta_key LIKE 'clone\_%'
         AND pm.meta_key NOT LIKE '\_%'"
    );

    $clone_ref_rows = (int) $wpdb->get_var(
        "SELECT COUNT(*) FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE p.post_type = 'product'
         AND pm.meta_key LIKE '\_clone\_%'"
    );

    $products_with_clone_values = (int) $wpdb->get_var(
        "SELECT COUNT(DISTINCT pm.post_id) FROM {$wpdb->postmeta} pm
         INNER JOIN {$wpdb->posts} p ON p.ID = pm.post_id
         WHERE p.post_type = 'product'
         AND p.post_status IN ('publish','draft','private')
         AND pm.meta_key LIKE 'clone\_%'
         AND pm.meta_key NOT LIKE '\_%'"
    );

    return [
        'recorded_at'                => gmdate('c'),
        'theme_version'              => defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '',
        'product_posts'              => $product_count,
        'clone_meta_value_rows'      => $clone_value_rows,
        'clone_meta_reference_rows'  => $clone_ref_rows,
        'products_with_clone_values' => $products_with_clone_values,
    ];
}

/**
 * @return array{path: string, filename: string, group_count: int}|null
 */
function justccell_acf_export_all_field_groups_to_uploads(): ?array
{
    if (!function_exists('acf_get_field_groups') || !function_exists('acf_get_field_group') || !function_exists('acf_get_fields')) {
        return null;
    }

    $export = [];
    foreach (acf_get_field_groups() as $row) {
        if (!is_array($row)) {
            continue;
        }
        $group = acf_get_field_group($row['ID'] ?? $row['key'] ?? '');
        if (!is_array($group)) {
            continue;
        }
        $group['fields'] = acf_get_fields($group);
        if (function_exists('acf_prepare_field_group_for_export')) {
            $group = acf_prepare_field_group_for_export($group);
        }
        $export[] = $group;
    }

    $upload = wp_upload_dir();
    if (!empty($upload['error'])) {
        return null;
    }

    $dir = trailingslashit((string) $upload['basedir']) . 'justccell-acf-backups';
    if (!wp_mkdir_p($dir)) {
        return null;
    }

    $filename = 'acf-field-groups-' . gmdate('Y-m-d-His') . '.json';
    $path     = trailingslashit($dir) . $filename;
    $json     = wp_json_encode($export, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    if (!is_string($json) || $json === '') {
        return null;
    }

    if (file_put_contents($path, $json) === false) {
        return null;
    }

    return [
        'path'        => $path,
        'filename'    => $filename,
        'group_count' => count($export),
    ];
}

/**
 * @param list<array{id: int, title: string, status: string, post_name: string, field_count: int, key: string}> $candidates
 * @return array{winner: array<string, mixed>|null, losers: list<array<string, mixed>>}
 */
function justccell_acf_pick_product_clone_group_winner(array $candidates): array
{
    if ($candidates === []) {
        return ['winner' => null, 'losers' => []];
    }

    usort(
        $candidates,
        static function (array $a, array $b): int {
            $fc = ($b['field_count'] ?? 0) <=> ($a['field_count'] ?? 0);
            if ($fc !== 0) {
                return $fc;
            }
            $a_pub = (($a['status'] ?? '') === 'publish') ? 1 : 0;
            $b_pub = (($b['status'] ?? '') === 'publish') ? 1 : 0;
            if ($b_pub !== $a_pub) {
                return $b_pub <=> $a_pub;
            }
            $a_clone = stripos((string) ($a['title'] ?? ''), 'clone') !== false ? 0 : 1;
            $b_clone = stripos((string) ($b['title'] ?? ''), 'clone') !== false ? 0 : 1;

            return $b_clone <=> $a_clone;
        }
    );

    $winner = $candidates[0];
    $losers = array_slice($candidates, 1);

    return ['winner' => $winner, 'losers' => $losers];
}

/**
 * @return array<string, mixed>
 */
function justccell_acf_run_migration_phase0(): array
{
    $baseline = justccell_acf_product_postmeta_baseline();
    update_option(JUSTCCELL_ACF_POSTMETA_BASELINE_OPT, $baseline, false);

    $export = justccell_acf_export_all_field_groups_to_uploads();
    $report = [
        'phase'    => 0,
        'status'   => $export !== null ? 'ok' : 'export_failed',
        'baseline' => $baseline,
        'export'   => $export,
        'ran_at'   => gmdate('c'),
    ];

    if ($export !== null) {
        $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
        update_option(JUSTCCELL_ACF_MIGRATION_PHASE0_OPT, $ver, false);
    }

    return $report;
}

/**
 * Resolve PHP field-group definition for export structure hints (parent nesting only).
 *
 * @return array<string, mixed>|null
 */
function justccell_acf_export_php_group_definition(string $group_key): ?array
{
    $callables = [
        'group_jc_about_page'       => 'justccell_acf_about_page_group',
        'group_jc_contact_page'     => 'justccell_acf_contact_page_group',
        'group_jc_generic_brand'    => 'justccell_acf_generic_brand_page_group',
        'group_jc_home_full'        => 'justccell_acf_home_page_group',
        'group_jc_j3_page'          => 'justccell_acf_j3_page_group',
        'group_jc_listing_page'     => 'justccell_acf_listing_page_group',
        'group_jc_product_clone'    => 'justccell_acf_product_clone_group',
        'group_jc_why_pages'        => 'justccell_acf_why_page_group',
    ];

    $callable = $callables[$group_key] ?? '';
    if ($callable === '' || !function_exists($callable)) {
        return null;
    }

    $group = $callable();
    return is_array($group) ? $group : null;
}

/**
 * Build field_key => parent_field_key map from a PHP group definition.
 *
 * @param array<string, mixed> $group
 * @return array<string, string>
 */
function justccell_acf_build_field_parent_map_from_group(array $group): array
{
    $map  = [];
    $walk = static function (array $fields, ?string $parent_key) use (&$walk, &$map): void {
        foreach ($fields as $field) {
            if (!is_array($field) || empty($field['key'])) {
                continue;
            }
            $key = (string) $field['key'];
            if ($parent_key !== null && $parent_key !== '') {
                $map[$key] = $parent_key;
            }
            if (!empty($field['sub_fields']) && is_array($field['sub_fields'])) {
                $walk($field['sub_fields'], $key);
            }
            if (($field['type'] ?? '') === 'flexible_content' && is_array($field['layouts'] ?? null)) {
                foreach ($field['layouts'] as $layout) {
                    if (!is_array($layout)) {
                        continue;
                    }
                    $layout_key = (string) ($layout['key'] ?? '');
                    $parent     = $layout_key !== '' ? $layout_key : $key;
                    if (!empty($layout['sub_fields']) && is_array($layout['sub_fields'])) {
                        $walk($layout['sub_fields'], $parent);
                    }
                }
            }
        }
    };

    if (!empty($group['fields']) && is_array($group['fields'])) {
        $walk($group['fields'], null);
    }

    return $map;
}

/**
 * @return array<string, string>
 */
function justccell_acf_export_php_parent_map(string $group_key): array
{
    $group = justccell_acf_export_php_group_definition($group_key);
    if ($group === null) {
        return [];
    }

    return justccell_acf_build_field_parent_map_from_group($group);
}

/**
 * Build nested field tree from flat ACF field list using parent keys.
 *
 * @return list<array<string, mixed>>
 */
function justccell_acf_export_fields_tree($parent, string $group_key = ''): array
{
    if (!function_exists('acf_get_fields')) {
        return [];
    }

    $fields = acf_get_fields($parent);
    if (!is_array($fields) || $fields === []) {
        return [];
    }

    $php_parents = $group_key !== '' ? justccell_acf_export_php_parent_map($group_key) : [];

    $indexed  = [];
    $children = [];
    $roots    = [];

    foreach ($fields as $field) {
        if (!is_array($field) || empty($field['key'])) {
            continue;
        }
        $key = (string) $field['key'];
        $indexed[$key] = $field;
        $parent_ref    = $field['parent'] ?? '';
        if (is_string($parent_ref) && str_starts_with($parent_ref, 'field_')) {
            $children[$parent_ref][] = $key;
        } elseif (isset($php_parents[$key])) {
            $children[$php_parents[$key]][] = $key;
        } else {
            $roots[] = $key;
        }
    }

    $build = static function (string $key) use (&$build, $indexed, $children): array {
        $field = $indexed[$key];
        $subs  = [];
        foreach ($children[$key] ?? [] as $child_key) {
            $subs[] = $build($child_key);
        }
        if (($field['type'] ?? '') === 'flexible_content' && is_array($field['layouts'] ?? null)) {
            foreach ($field['layouts'] as $layout_key => $layout) {
                if (!is_array($layout)) {
                    continue;
                }
                $layout_key_str = (string) ($layout['key'] ?? '');
                $layout_sub     = [];
                foreach ($children[$layout_key_str] ?? [] as $child_key) {
                    $layout_sub[] = $build($child_key);
                }
                $field['layouts'][$layout_key]['sub_fields'] = $layout_sub;
            }
        }
        $field['sub_fields'] = $subs;

        return $field;
    };

    return array_map(static fn (string $key): array => $build($key), $roots);
}

/**
 *
 * @return array<string, array{post: WP_Post, field_count: int}>
 */
function justccell_acf_unique_db_field_group_posts(): array
{
    if (!post_type_exists('acf-field-group') || !function_exists('acf_get_field_group')) {
        return [];
    }

    $posts = get_posts([
        'post_type'              => 'acf-field-group',
        'post_status'            => ['publish', 'acf-disabled', 'draft'],
        'posts_per_page'         => -1,
        'suppress_filters'       => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);

    $by_key = [];
    foreach ($posts as $post) {
        if (!$post instanceof WP_Post) {
            continue;
        }
        $group = acf_get_field_group((int) $post->ID);
        if (!is_array($group)) {
            continue;
        }
        $key = (string) ($group['key'] ?? '');
        if ($key === '' || !str_starts_with($key, 'group_jc_')) {
            continue;
        }
        $fields      = function_exists('acf_get_fields') ? acf_get_fields((int) $post->ID) : [];
        $field_count = justccell_acf_count_export_fields(is_array($fields) ? $fields : []);
        if (!isset($by_key[$key]) || $field_count > $by_key[$key]['field_count']) {
            $by_key[$key] = [
                'post'        => $post,
                'field_count' => $field_count,
            ];
        }
    }

    return $by_key;
}

/**
 * Export each unique DB field group to theme acf-json/{key}.json (read-only schema snapshot).
 *
 * @return array<string, mixed>
 */
function justccell_acf_run_migration_phase2(): array
{
    if (
        !defined('JUSTCCELL_DIR')
        || !function_exists('acf_get_field_group')
        || !function_exists('acf_get_fields')
    ) {
        return ['phase' => 2, 'status' => 'acf_missing', 'ran_at' => gmdate('c')];
    }

    $dir = trailingslashit(JUSTCCELL_DIR) . 'acf-json';
    if (!wp_mkdir_p($dir)) {
        return ['phase' => 2, 'status' => 'mkdir_failed', 'ran_at' => gmdate('c')];
    }

    $phase0 = get_option(JUSTCCELL_ACF_POSTMETA_BASELINE_OPT, []);
    $before_baseline = justccell_acf_product_postmeta_baseline();

    $written = [];
    $errors  = [];

    foreach (justccell_acf_unique_db_field_group_posts() as $key => $row) {
        $post = $row['post'];
        if (!$post instanceof WP_Post) {
            continue;
        }

        $group = acf_get_field_group((int) $post->ID);
        if (!is_array($group)) {
            $errors[] = ['key' => $key, 'error' => 'group_load_failed'];
            continue;
        }

        $group['fields'] = justccell_acf_export_fields_tree((int) $post->ID, $key);
        if (function_exists('acf_prepare_field_group_for_export')) {
            $prepared = acf_prepare_field_group_for_export($group);
            if (is_array($prepared)) {
                $group = $prepared;
                $group['fields'] = justccell_acf_export_fields_tree((int) $post->ID, $key);
            }
        }

        $modified = strtotime((string) $post->post_modified_gmt . ' GMT');
        if ($modified === false || $modified < 1) {
            $modified = time();
        }
        $group['modified'] = $modified;

        unset($group['ID'], $group['local'], $group['local_file']);

        $json = wp_json_encode($group, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if (!is_string($json) || $json === '') {
            $errors[] = ['key' => $key, 'error' => 'encode_failed'];
            continue;
        }

        $path = $dir . '/' . $key . '.json';
        if (file_put_contents($path, $json) === false) {
            $errors[] = ['key' => $key, 'error' => 'write_failed'];
            continue;
        }

        $written[] = [
            'key'         => $key,
            'title'       => (string) ($group['title'] ?? ''),
            'field_count' => (int) $row['field_count'],
            'file'        => $key . '.json',
            'modified'    => $modified,
        ];
    }

    $after_baseline = justccell_acf_product_postmeta_baseline();
    $meta_unchanged = is_array($phase0)
        && (int) ($phase0['clone_meta_value_rows'] ?? -1) === (int) ($after_baseline['clone_meta_value_rows'] ?? -2);

    $status = $errors === [] && $written !== [] ? 'ok' : ($written !== [] ? 'partial' : 'failed');

    $report = [
        'phase'            => 2,
        'status'           => $status,
        'written'          => $written,
        'errors'           => $errors,
        'group_count'      => count($written),
        'baseline_before'  => $before_baseline,
        'baseline_after'   => $after_baseline,
        'postmeta_unchanged' => $meta_unchanged,
        'ran_at'           => gmdate('c'),
    ];

    if ($status === 'ok' || $status === 'partial') {
        $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
        update_option(JUSTCCELL_ACF_MIGRATION_PHASE2_OPT, $ver, false);
    }

    return $report;
}

/**
 * @return array<string, mixed>
 */
function justccell_acf_run_migration_phase1(): array
{
    $candidates = justccell_acf_find_field_group_posts_by_key('group_jc_product_clone');
    $pick       = justccell_acf_pick_product_clone_group_winner($candidates);
    $winner     = is_array($pick['winner'] ?? null) ? $pick['winner'] : null;
    $losers     = is_array($pick['losers'] ?? null) ? $pick['losers'] : [];

    $trashed = [];
    foreach ($losers as $loser) {
        $id = (int) ($loser['id'] ?? 0);
        if ($id < 1) {
            continue;
        }
        $result = wp_trash_post($id);
        $trashed[] = [
            'id'     => $id,
            'title'  => (string) ($loser['title'] ?? ''),
            'status' => $result instanceof WP_Post ? 'trashed' : 'failed',
        ];
    }

    $renamed = false;
    if (is_array($winner) && (int) ($winner['id'] ?? 0) > 0) {
        $winner_id = (int) $winner['id'];
        if ((string) ($winner['title'] ?? '') !== 'Product page') {
            wp_update_post([
                'ID'         => $winner_id,
                'post_title' => 'Product page',
            ]);
            $renamed = true;
        }
        if (function_exists('acf_get_field_group') && function_exists('acf_update_field_group')) {
            $group = acf_get_field_group($winner_id);
            if (is_array($group)) {
                $group['title'] = 'Product page';
                acf_update_field_group($group);
            }
        }
    }

    $recovery = 'skipped';
    if (function_exists('justccell_acf_recover_product_clone_field_refs')) {
        justccell_acf_recover_product_clone_field_refs();
        $recovery = 'ran';
    }

    $after = justccell_acf_find_field_group_posts_by_key('group_jc_product_clone');

    $report = [
        'phase'           => 1,
        'status'          => count($after) === 1 ? 'ok' : (count($after) === 0 ? 'no_group' : 'duplicate_remains'),
        'before'          => $candidates,
        'winner'          => $winner,
        'trashed'         => $trashed,
        'renamed_winner'  => $renamed,
        'field_recovery'  => $recovery,
        'after'           => $after,
        'ran_at'          => gmdate('c'),
    ];

    if ($report['status'] === 'ok' || ($report['status'] === 'no_group' && $candidates === [])) {
        $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
        update_option(JUSTCCELL_ACF_MIGRATION_PHASE1_OPT, $ver, false);
    }

    return $report;
}

add_action('admin_init', static function (): void {
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }

    $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '';
    if ($ver === '') {
        return;
    }

    $report = get_option(JUSTCCELL_ACF_MIGRATION_REPORT_OPT, []);
    if (!is_array($report)) {
        $report = [];
    }

    if (get_option(JUSTCCELL_ACF_MIGRATION_PHASE0_OPT) !== $ver) {
        $report['phase0'] = justccell_acf_run_migration_phase0();
        update_option(JUSTCCELL_ACF_MIGRATION_REPORT_OPT, $report, false);
    }

    if (get_option(JUSTCCELL_ACF_MIGRATION_PHASE1_OPT) !== $ver) {
        $report['phase1'] = justccell_acf_run_migration_phase1();
        update_option(JUSTCCELL_ACF_MIGRATION_REPORT_OPT, $report, false);
    }

    if (get_option(JUSTCCELL_ACF_MIGRATION_PHASE2_OPT) !== $ver) {
        $report['phase2'] = justccell_acf_run_migration_phase2();
        update_option(JUSTCCELL_ACF_MIGRATION_REPORT_OPT, $report, false);
    }
}, 5);

add_action('admin_notices', static function (): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '';
    if ($ver === '' || get_option(JUSTCCELL_ACF_MIGRATION_PHASE2_OPT) !== $ver) {
        return;
    }
    $report = get_option(JUSTCCELL_ACF_MIGRATION_REPORT_OPT, []);
    if (!is_array($report) || empty($report['phase2'])) {
        return;
    }
    $p2 = is_array($report['phase2']) ? $report['phase2'] : [];
    $after = is_array($p2['baseline_after'] ?? null) ? $p2['baseline_after'] : [];
    echo '<div class="notice notice-success is-dismissible"><p><strong>Justccell ACF migration (Phase 2)</strong> — ';
    echo esc_html(sprintf(
        'Wrote %d field groups to acf-json/. clone_* postmeta rows: %d (unchanged: %s).',
        (int) ($p2['group_count'] ?? 0),
        (int) ($after['clone_meta_value_rows'] ?? 0),
        !empty($p2['postmeta_unchanged']) ? 'yes' : 'check'
    ));
    echo '</p></div>';
});
