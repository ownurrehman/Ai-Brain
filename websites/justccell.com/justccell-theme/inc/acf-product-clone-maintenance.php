<?php
/**
 * One-time Product page ACF group repair: dedupe DB posts, prune orphan fields, sync Local JSON.
 * Does not touch product postmeta (clone_* values on WooCommerce products).
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_ACF_PRODUCT_CLONE_REPAIR_OPT = 'justccell_acf_product_clone_repaired_252';
const JUSTCCELL_ACF_LASER_GLOBAL_REPAIR_OPT    = 'justccell_acf_laser_global_repaired_248';
const JUSTCCELL_ACF_J3_PAGE_REPAIR_OPT         = 'justccell_acf_j3_page_repaired_261';
const JUSTCCELL_ACF_J3_PAGE_TABS_REPAIR_OPT    = 'justccell_acf_j3_page_tabs_263';
const JUSTCCELL_ACF_LOCAL_JSON_REPAIR_OPT      = 'justccell_acf_local_json_repair_262';
const JUSTCCELL_ACF_ORPHAN_PURGE_OPT           = 'justccell_acf_orphan_purge_293';
const JUSTCCELL_ACF_TMPL_LOCATIONS_OPT         = 'justccell_acf_tmpl_locations_297b';
const JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY  = 'group_jc_product_clone';
const JUSTCCELL_ACF_LASER_GLOBAL_GROUP_KEY   = 'group_jc_laser_engraving_global';
const JUSTCCELL_ACF_J3_PAGE_GROUP_KEY        = 'group_jc_j3_page';

/**
 * Repeater sub-field keys that must never sit as direct children of a field group.
 *
 * @return list<string>
 */
function justccell_acf_subfield_only_keys(): array
{
    return [
        'field_jc_prod_spec_line',
        'field_jc_prod_feat_title',
        'field_jc_prod_feat_copy',
        'field_jc_prod_feat_text_color',
        'field_jc_prod_feat_image',
        'field_jc_laser_global_tier_min',
        'field_jc_laser_global_tier_max',
        'field_jc_laser_global_tier_ppu',
        'field_jc_laser_cat_tier_min',
        'field_jc_laser_cat_tier_max',
        'field_jc_laser_cat_tier_ppu',
        'field_jc_j3_sec_type',
        'field_jc_j3_sec_reverse',
        'field_jc_j3_sec_title',
        'field_jc_j3_sec_title_tag',
        'field_jc_j3_sec_heading',
        'field_jc_j3_sec_heading_tag',
        'field_jc_j3_sec_copy',
        'field_jc_j3_sec_desk',
        'field_jc_j3_sec_mob',
        'field_jc_j3_group_heading',
        'field_jc_j3_group_key',
        'field_jc_j3_group_products',
    ];
}

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
        'post_status'            => ['publish', 'acf-disabled', 'draft', 'trash'],
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
        if ($post->post_status === 'trash') {
            continue;
        }
        $group = acf_get_field_group((int) $post->ID);
        if (!is_array($group)) {
            continue;
        }
        $key = (string) ($group['key'] ?? '');
        $slug = (string) $post->post_name;
        $title = (string) $post->post_title;
        $matches_key = $key === $group_key
            || $slug === $group_key
            || str_starts_with($slug, $group_key . '-')
            || ($group_key === JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY && in_array($title, ['Product page', 'Product page clone'], true));
        if (!$matches_key) {
            continue;
        }
        $fields = function_exists('acf_get_fields') ? acf_get_fields((int) $post->ID) : [];
        $matches[] = [
            'id'          => (int) $post->ID,
            'title'       => (string) $post->post_title,
            'status'      => (string) $post->post_status,
            'post_name'   => (string) $post->post_name,
            'field_count' => justccell_acf_count_top_level_fields(is_array($fields) ? $fields : []),
            'key'         => $key !== '' ? $key : $group_key,
        ];
    }

    return $matches;
}

/**
 * @param list<array<string, mixed>> $fields
 */
function justccell_acf_count_top_level_fields(array $fields): int
{
    $count = 0;
    foreach ($fields as $field) {
        if (is_array($field) && !empty($field['key'])) {
            ++$count;
        }
    }

    return $count;
}

/**
 * @param list<array{id: int, title: string, status: string, post_name: string, field_count: int, key: string}> $candidates
 * @return array{id: int, title: string, status: string, post_name: string, field_count: int, key: string}|null
 */
function justccell_acf_pick_product_clone_group_winner(array $candidates): ?array
{
    if ($candidates === []) {
        return null;
    }

    usort(
        $candidates,
        static function (array $a, array $b): int {
            $bloated_a = ((int) ($a['field_count'] ?? 0)) > 40 ? 1 : 0;
            $bloated_b = ((int) ($b['field_count'] ?? 0)) > 40 ? 1 : 0;
            if ($bloated_a !== $bloated_b) {
                return $bloated_a <=> $bloated_b;
            }
            $slug_a = (($a['post_name'] ?? '') === JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY) ? 1 : 0;
            $slug_b = (($b['post_name'] ?? '') === JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY) ? 1 : 0;
            if ($slug_b !== $slug_a) {
                return $slug_b <=> $slug_a;
            }
            $pub_a = (($a['status'] ?? '') === 'publish') ? 1 : 0;
            $pub_b = (($b['status'] ?? '') === 'publish') ? 1 : 0;
            if ($pub_b !== $pub_a) {
                return $pub_b <=> $pub_a;
            }

            return ($a['id'] ?? PHP_INT_MAX) <=> ($b['id'] ?? PHP_INT_MAX);
        }
    );

    return $candidates[0];
}

/**
 * @param list<array<string, mixed>> $fields
 * @return list<string>
 */
function justccell_acf_collect_field_keys_from_defs(array $fields): array
{
    $keys = [];
    foreach ($fields as $field) {
        if (!is_array($field)) {
            continue;
        }
        if (!empty($field['key']) && is_string($field['key'])) {
            $keys[] = $field['key'];
        }
        if (!empty($field['sub_fields']) && is_array($field['sub_fields'])) {
            $keys = array_merge($keys, justccell_acf_collect_field_keys_from_defs($field['sub_fields']));
        }
        if (!empty($field['layouts']) && is_array($field['layouts'])) {
            foreach ($field['layouts'] as $layout) {
                if (is_array($layout) && !empty($layout['sub_fields']) && is_array($layout['sub_fields'])) {
                    $keys = array_merge($keys, justccell_acf_collect_field_keys_from_defs($layout['sub_fields']));
                }
            }
        }
    }

    return array_values(array_unique($keys));
}

/**
 * @return list<WP_Post>
 */
function justccell_acf_collect_field_posts_for_group(int $group_id): array
{
    if ($group_id < 1 || !post_type_exists('acf-field')) {
        return [];
    }

    $found = [];
    $queue = [$group_id];

    while ($queue !== []) {
        $parent_id = (int) array_shift($queue);
        $children  = get_posts([
            'post_type'              => 'acf-field',
            'post_status'            => 'any',
            'posts_per_page'         => -1,
            'post_parent'            => $parent_id,
            'suppress_filters'       => true,
            'no_found_rows'          => true,
            'update_post_meta_cache' => false,
            'update_post_term_cache' => false,
        ]);
        foreach ($children as $child) {
            if (!$child instanceof WP_Post) {
                continue;
            }
            $id = (int) $child->ID;
            if (isset($found[$id])) {
                continue;
            }
            $found[$id] = $child;
            $queue[]    = $id;
        }
    }

    return array_values($found);
}

/**
 * Remove repeater sub-fields wrongly attached to the group root (shows as orphan "Line", "Heading", etc.).
 */
function justccell_acf_delete_orphan_subfields_at_group_root(int $group_id): void
{
    if ($group_id < 1 || !post_type_exists('acf-field')) {
        return;
    }

    $sub_only = array_fill_keys(justccell_acf_subfield_only_keys(), true);
    $children = get_posts([
        'post_type'              => 'acf-field',
        'post_status'            => 'any',
        'posts_per_page'         => -1,
        'post_parent'            => $group_id,
        'suppress_filters'       => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ]);

    foreach ($children as $post) {
        if (!$post instanceof WP_Post) {
            continue;
        }
        $key = (string) $post->post_name;
        if (!isset($sub_only[$key])) {
            continue;
        }
        if (function_exists('acf_delete_field')) {
            acf_delete_field((int) $post->ID);
        } else {
            wp_delete_post((int) $post->ID, true);
        }
    }
}

/**
 * Delete every acf-field registry post under a group (postmeta values are untouched).
 */
function justccell_acf_delete_group_field_tree(int $group_id): void
{
    $posts = justccell_acf_collect_field_posts_for_group($group_id);
    if ($posts === []) {
        return;
    }

    usort(
        $posts,
        static function (WP_Post $a, WP_Post $b): int {
            return $b->ID <=> $a->ID;
        }
    );

    foreach ($posts as $post) {
        if (function_exists('acf_delete_field')) {
            acf_delete_field((int) $post->ID);
        } else {
            wp_delete_post((int) $post->ID, true);
        }
    }
}

/**
 * @return array<string, mixed>|null
 */
function justccell_acf_load_json_field_group(string $group_key): ?array
{
    if (!defined('JUSTCCELL_DIR') || $group_key === '') {
        return null;
    }
    $path = JUSTCCELL_DIR . '/acf-json/' . $group_key . '.json';
    if (!is_readable($path)) {
        return null;
    }
    $group = json_decode((string) file_get_contents($path), true);

    return is_array($group) && (string) ($group['key'] ?? '') === $group_key ? $group : null;
}

/**
 * Import a field group JSON onto an existing group post, rebuilding the field tree from scratch.
 */
function justccell_acf_reimport_field_group_from_json(int $group_id, array $json_group): void
{
    if ($group_id < 1 || !function_exists('acf_import_field_group')) {
        return;
    }

    justccell_acf_delete_orphan_subfields_at_group_root($group_id);
    justccell_acf_delete_group_field_tree($group_id);

    $json_group['ID'] = $group_id;
    unset($json_group['local'], $json_group['local_file']);
    acf_import_field_group($json_group);
}

/**
 * Delete duplicate and orphan acf-field registry posts for one group.
 * Product clone_* postmeta is never touched.
 *
 * @param list<string> $allowed_keys
 */
function justccell_acf_prune_product_clone_field_registry(int $group_id, array $allowed_keys): void
{
    if ($group_id < 1 || !post_type_exists('acf-field')) {
        return;
    }

    $allowed = array_fill_keys($allowed_keys, true);
    if (function_exists('justccell_acf_legacy_product_clone_field_keys')) {
        foreach (array_keys(justccell_acf_legacy_product_clone_field_keys()) as $legacy_key) {
            unset($allowed[$legacy_key]);
        }
    }

    $posts = justccell_acf_collect_field_posts_for_group($group_id);

    /** @var array<string, list<int>> $ids_by_key */
    $ids_by_key = [];

    foreach ($posts as $post) {
        $field_key = (string) $post->post_name;
        if ($field_key === '' && function_exists('acf_get_field')) {
            $field = acf_get_field((int) $post->ID);
            if (is_array($field) && !empty($field['key'])) {
                $field_key = (string) $field['key'];
            }
        }
        if ($field_key === '') {
            wp_delete_post((int) $post->ID, true);
            continue;
        }
        $ids_by_key[$field_key][] = (int) $post->ID;
    }

    foreach ($ids_by_key as $field_key => $ids) {
        sort($ids);
        if (!isset($allowed[$field_key])) {
            foreach ($ids as $id) {
                if (function_exists('acf_delete_field')) {
                    acf_delete_field($id);
                } else {
                    wp_delete_post($id, true);
                }
            }
            continue;
        }
        $keep = array_shift($ids);
        foreach ($ids as $id) {
            if ($id === $keep) {
                continue;
            }
            if (function_exists('acf_delete_field')) {
                acf_delete_field($id);
            } else {
                wp_delete_post($id, true);
            }
        }
    }
}

/**
 * @return array<string, mixed>|null
 */
function justccell_acf_load_product_clone_json_group(): ?array
{
    return justccell_acf_load_json_field_group(JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY);
}

/**
 * Dedupe duplicate Product page groups, rebuild field tree from Local JSON.
 * Product clone_* postmeta on WooCommerce products is never deleted.
 */
function justccell_acf_repair_product_clone_field_group(): void
{
    if (get_option(JUSTCCELL_ACF_PRODUCT_CLONE_REPAIR_OPT) === '1') {
        return;
    }
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }
    if (!function_exists('acf_import_field_group') || !function_exists('acf_get_field_group')) {
        return;
    }

    $json_group = justccell_acf_load_product_clone_json_group();
    if ($json_group === null) {
        return;
    }

    $allowed_keys = justccell_acf_collect_field_keys_from_defs(
        is_array($json_group['fields'] ?? null) ? $json_group['fields'] : []
    );

    $candidates = justccell_acf_find_field_group_posts_by_key(JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY);
    $winner     = justccell_acf_pick_product_clone_group_winner($candidates);
    $winner_id  = $winner !== null ? (int) $winner['id'] : 0;

    if ($winner_id > 0) {
        foreach ($candidates as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id < 1 || $id === $winner_id) {
                continue;
            }
            wp_trash_post($id);
        }
        if (($winner['post_name'] ?? '') !== JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY) {
            wp_update_post([
                'ID'        => $winner_id,
                'post_name' => JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY,
            ]);
        }
        justccell_acf_reimport_field_group_from_json($winner_id, $json_group);
        justccell_acf_prune_product_clone_field_registry($winner_id, $allowed_keys);
    } else {
        unset($json_group['local'], $json_group['local_file']);
        acf_import_field_group($json_group);
    }

    update_option(JUSTCCELL_ACF_PRODUCT_CLONE_REPAIR_OPT, '1', false);
}

/**
 * Rebuild Laser Engraving global options field tree (tier matrix sub-fields).
 */
function justccell_acf_repair_laser_global_field_group(): void
{
    if (get_option(JUSTCCELL_ACF_LASER_GLOBAL_REPAIR_OPT) === '1') {
        return;
    }
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }
    if (!function_exists('acf_import_field_group') || !function_exists('acf_get_field_group')) {
        return;
    }

    $json_group = justccell_acf_load_json_field_group(JUSTCCELL_ACF_LASER_GLOBAL_GROUP_KEY);
    if ($json_group === null) {
        return;
    }

    $group = acf_get_field_group(JUSTCCELL_ACF_LASER_GLOBAL_GROUP_KEY);
    $group_id = is_array($group) && !empty($group['ID']) ? (int) $group['ID'] : 0;
    if ($group_id < 1) {
        unset($json_group['local'], $json_group['local_file']);
        acf_import_field_group($json_group);
        update_option(JUSTCCELL_ACF_LASER_GLOBAL_REPAIR_OPT, '1', false);
        return;
    }

    justccell_acf_reimport_field_group_from_json($group_id, $json_group);
    update_option(JUSTCCELL_ACF_LASER_GLOBAL_REPAIR_OPT, '1', false);
}

/**
 * Rebuild Just CCELL 3.0 page field tree (story + product repeater sub-fields were stripped from Local JSON).
 */
function justccell_acf_repair_j3_page_field_group(): void
{
    if (get_option(JUSTCCELL_ACF_J3_PAGE_REPAIR_OPT) === '1') {
        return;
    }
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }
    if (!function_exists('acf_import_field_group') || !function_exists('acf_get_field_group')) {
        return;
    }

    $json_group = justccell_acf_load_json_field_group(JUSTCCELL_ACF_J3_PAGE_GROUP_KEY);
    if ($json_group === null) {
        return;
    }

    $allowed_keys = justccell_acf_collect_field_keys_from_defs(
        is_array($json_group['fields'] ?? null) ? $json_group['fields'] : []
    );

    $candidates = justccell_acf_find_field_group_posts_by_key(JUSTCCELL_ACF_J3_PAGE_GROUP_KEY);
    $winner     = $candidates !== [] ? $candidates[0] : null;
    $winner_id  = $winner !== null ? (int) $winner['id'] : 0;

    if ($winner_id > 0) {
        foreach ($candidates as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id < 1 || $id === $winner_id) {
                continue;
            }
            wp_trash_post($id);
        }
        if (($winner['post_name'] ?? '') !== JUSTCCELL_ACF_J3_PAGE_GROUP_KEY) {
            wp_update_post([
                'ID'        => $winner_id,
                'post_name' => JUSTCCELL_ACF_J3_PAGE_GROUP_KEY,
            ]);
        }
        justccell_acf_reimport_field_group_from_json($winner_id, $json_group);
        justccell_acf_prune_product_clone_field_registry($winner_id, $allowed_keys);
    } else {
        unset($json_group['local'], $json_group['local_file']);
        acf_import_field_group($json_group);
    }

    update_option(JUSTCCELL_ACF_J3_PAGE_REPAIR_OPT, '1', false);
}

/**
 * Remove legacy per-tab product relationship picks; tabs are label + category only (0.9.263).
 */
function justccell_acf_repair_j3_page_tabs_field_group(): void
{
    if (get_option(JUSTCCELL_ACF_J3_PAGE_TABS_REPAIR_OPT) === '1') {
        return;
    }
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }
    if (!function_exists('justccell_acf_repair_field_group_from_local_json')) {
        return;
    }

    if (justccell_acf_repair_field_group_from_local_json(JUSTCCELL_ACF_J3_PAGE_GROUP_KEY)) {
        update_option(JUSTCCELL_ACF_J3_PAGE_TABS_REPAIR_OPT, '1', false);
    }
}

/**
 * Re-import one ACF field group from theme Local JSON (repair stripped repeater sub-fields).
 */
function justccell_acf_repair_field_group_from_local_json(string $group_key): bool
{
    if ($group_key === '' || !function_exists('acf_import_field_group') || !function_exists('acf_get_field_group')) {
        return false;
    }

    $json_group = justccell_acf_load_json_field_group($group_key);
    if ($json_group === null) {
        return false;
    }

    $allowed_keys = justccell_acf_collect_field_keys_from_defs(
        is_array($json_group['fields'] ?? null) ? $json_group['fields'] : []
    );

    $candidates = justccell_acf_find_field_group_posts_by_key($group_key);
    $winner     = $group_key === JUSTCCELL_ACF_PRODUCT_CLONE_GROUP_KEY
        ? justccell_acf_pick_product_clone_group_winner($candidates)
        : ($candidates[0] ?? null);
    $winner_id  = $winner !== null ? (int) $winner['id'] : 0;

    if ($winner_id > 0) {
        foreach ($candidates as $row) {
            $id = (int) ($row['id'] ?? 0);
            if ($id < 1 || $id === $winner_id) {
                continue;
            }
            wp_trash_post($id);
        }
        if (($winner['post_name'] ?? '') !== $group_key) {
            wp_update_post([
                'ID'        => $winner_id,
                'post_name' => $group_key,
            ]);
        }
        justccell_acf_reimport_field_group_from_json($winner_id, $json_group);
        justccell_acf_prune_product_clone_field_registry($winner_id, $allowed_keys);
        return true;
    }

    unset($json_group['local'], $json_group['local_file']);
    acf_import_field_group($json_group);

    return true;
}

/**
 * Rebuild every Justccell page/options field group from acf-json/ (Sept 2026 migration stripped repeater children).
 */
function justccell_acf_repair_all_local_json_field_groups(): void
{
    if (get_option(JUSTCCELL_ACF_LOCAL_JSON_REPAIR_OPT) === '1') {
        return;
    }
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }
    if (!defined('JUSTCCELL_DIR')) {
        return;
    }

    $paths = glob(JUSTCCELL_DIR . '/acf-json/group_jc_*.json') ?: [];
    sort($paths);
    foreach ($paths as $path) {
        $group_key = basename($path, '.json');
        justccell_acf_repair_field_group_from_local_json($group_key);
    }

    update_option(JUSTCCELL_ACF_LOCAL_JSON_REPAIR_OPT, '1', false);
}

/**
 * One-time: empty trashed ACF field groups and delete orphan / duplicate acf-field registry
 * posts left behind by historical re-imports (825 rows, dozens of duplicate keys as of 0.9.292).
 *
 * Conservative by design:
 *  - Only deletes field groups already in the trash, plus their field trees.
 *  - Only deletes acf-field posts whose top-most ancestor is NOT a live field group.
 *  - Never touches fields under live (publish/disabled/draft) groups.
 *  - Never touches product postmeta (clone_* values on WooCommerce products).
 */
function justccell_acf_purge_trashed_and_orphan_fields(): void
{
    if (get_option(JUSTCCELL_ACF_ORPHAN_PURGE_OPT) === '1') {
        return;
    }
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }
    if (!post_type_exists('acf-field-group') || !post_type_exists('acf-field')) {
        return;
    }

    $query_args = [
        'posts_per_page'         => -1,
        'suppress_filters'       => true,
        'no_found_rows'          => true,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
    ];

    // 1. Delete trashed field groups and their entire field trees.
    $trashed_groups = get_posts(array_merge($query_args, [
        'post_type'   => 'acf-field-group',
        'post_status' => 'trash',
        'fields'      => 'ids',
    ]));
    foreach ($trashed_groups as $gid) {
        justccell_acf_delete_group_field_tree((int) $gid);
        wp_delete_post((int) $gid, true);
    }

    // 2. Collect the IDs of every live field group.
    $live_groups = get_posts(array_merge($query_args, [
        'post_type'   => 'acf-field-group',
        'post_status' => ['publish', 'acf-disabled', 'draft'],
        'fields'      => 'ids',
    ]));
    $valid_group_ids = array_fill_keys(array_map('intval', (array) $live_groups), true);

    // 3. Walk every acf-field to its top-most ancestor; delete those not rooted in a live group.
    $fields = get_posts(array_merge($query_args, [
        'post_type'   => 'acf-field',
        'post_status' => 'any',
    ]));

    $parent_of = [];
    foreach ($fields as $field_post) {
        if ($field_post instanceof WP_Post) {
            $parent_of[(int) $field_post->ID] = (int) $field_post->post_parent;
        }
    }

    foreach ($fields as $field_post) {
        if (!$field_post instanceof WP_Post) {
            continue;
        }

        // Purge trashed field rows outright.
        if ($field_post->post_status === 'trash') {
            justccell_acf_force_delete_field_post((int) $field_post->ID);
            continue;
        }

        // Climb parent chain (sub-fields point at parent fields) up to the root ancestor.
        $cursor = (int) $field_post->post_parent;
        $guard  = 0;
        while ($cursor > 0 && isset($parent_of[$cursor]) && $guard < 100) {
            $cursor = $parent_of[$cursor];
            ++$guard;
        }

        // $cursor is now a field-group ID, 0, or a missing/trashed ancestor.
        if ($cursor < 1 || !isset($valid_group_ids[$cursor])) {
            justccell_acf_force_delete_field_post((int) $field_post->ID);
        }
    }

    update_option(JUSTCCELL_ACF_ORPHAN_PURGE_OPT, '1', false);
}

/**
 * Delete a single acf-field registry post (postmeta values are untouched).
 */
function justccell_acf_force_delete_field_post(int $field_id): void
{
    if ($field_id < 1) {
        return;
    }
    if (function_exists('acf_delete_field')) {
        acf_delete_field($field_id);
    } else {
        wp_delete_post($field_id, true);
    }
}

/**
 * One-time (0.9.297): retarget the page-type field groups from the custom "Page slug" location rule
 * to native "Post Template" (page_template) rules, so binding is portable across cloned sites and
 * ACF refreshes fields instantly when the Template dropdown changes in the editor.
 *
 * Re-imports each group from Local JSON (which now carries the page_template location) into its real
 * DB post via the proven repair helper — acf_get_field_group() alone returns the local (ID-less)
 * copy when Local JSON is active, so a plain acf_update_field_group() would not persist. This also
 * clears the "Sync available" notice for these groups (DB is brought level with JSON). Every affected
 * page already has its native template assigned on live (verified 2026-09-06), so no fields disappear.
 *
 * The Justccell-only laser page (group_jc_laser_page) stays on the slug rule by design — it shares
 * the brand template with 4 sibling pages but must appear on the laser-engraving page only.
 */
function justccell_acf_retarget_page_groups_to_templates(): void
{
    if (get_option(JUSTCCELL_ACF_TMPL_LOCATIONS_OPT) === '1') {
        return;
    }
    if (!function_exists('justccell_acf_is_safe_maintenance_request') || !justccell_acf_is_safe_maintenance_request()) {
        return;
    }
    if (!function_exists('justccell_acf_repair_field_group_from_local_json')) {
        return;
    }

    foreach ([
        'group_jc_about_page',
        'group_jc_why_pages',
        'group_jc_legal_pages',
        'group_jc_locations_page',
        'group_jc_generic_brand',
        'group_jc_j3_page',
        'group_jc_discover_hub',
    ] as $key) {
        justccell_acf_repair_field_group_from_local_json($key);
    }

    update_option(JUSTCCELL_ACF_TMPL_LOCATIONS_OPT, '1', false);
}

add_action('admin_init', 'justccell_acf_repair_product_clone_field_group', 20);
add_action('admin_init', 'justccell_acf_repair_laser_global_field_group', 21);
add_action('admin_init', 'justccell_acf_repair_j3_page_field_group', 22);
add_action('admin_init', 'justccell_acf_repair_j3_page_tabs_field_group', 22);
add_action('admin_init', 'justccell_acf_repair_all_local_json_field_groups', 23);
add_action('admin_init', 'justccell_acf_purge_trashed_and_orphan_fields', 24);
add_action('admin_init', 'justccell_acf_retarget_page_groups_to_templates', 25);
