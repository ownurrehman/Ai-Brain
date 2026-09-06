<?php
/**
 * Quote leads admin — read/unread, status, list columns, menu alerts.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/** @return list<string> */
function justccell_lead_statuses(): array
{
    return ['new', 'in_progress', 'replied', 'qualified', 'closed', 'spam'];
}

function justccell_lead_status_label(string $status): string
{
    $map = [
        'new'         => __('New', 'justccell'),
        'in_progress' => __('In progress', 'justccell'),
        'replied'     => __('Replied', 'justccell'),
        'qualified'   => __('Qualified', 'justccell'),
        'closed'      => __('Closed', 'justccell'),
        'spam'        => __('Spam', 'justccell'),
    ];
    return $map[$status] ?? ucwords(str_replace('_', ' ', $status));
}

function justccell_leads_unread_count(): int
{
    $query = new WP_Query([
        'post_type'              => 'jc_lead',
        'post_status'            => 'private',
        'posts_per_page'         => 1,
        'fields'                 => 'ids',
        'no_found_rows'          => false,
        'update_post_meta_cache' => false,
        'update_post_term_cache' => false,
        'meta_query'             => [
            'relation' => 'OR',
            [
                'key'     => '_jc_lead_read',
                'compare' => 'NOT EXISTS',
            ],
            [
                'key'     => '_jc_lead_read',
                'value'   => '0',
                'compare' => '=',
            ],
        ],
    ]);
    return (int) $query->found_posts;
}

function justccell_lead_is_read(int $post_id): bool
{
    return get_post_meta($post_id, '_jc_lead_read', true) === '1';
}

function justccell_lead_mark_read(int $post_id, bool $read = true): void
{
    update_post_meta($post_id, '_jc_lead_read', $read ? '1' : '0');
}

function justccell_lead_status(int $post_id): string
{
    $status = sanitize_key((string) get_post_meta($post_id, '_jc_lead_status', true));
    return in_array($status, justccell_lead_statuses(), true) ? $status : 'new';
}

function justccell_lead_set_status(int $post_id, string $status): void
{
    if (!in_array($status, justccell_lead_statuses(), true)) {
        return;
    }
    update_post_meta($post_id, '_jc_lead_status', $status);
}

function justccell_lead_meta(int $post_id, string $key): string
{
    $value = get_post_meta($post_id, '_jc_lead_' . sanitize_key($key), true);
    return is_string($value) ? $value : '';
}

add_filter('manage_jc_lead_posts_columns', static function (array $columns): array {
    $out = [];
    foreach ($columns as $key => $label) {
        if ($key === 'date') {
            $out['jc_lead_read'] = __('Read', 'justccell');
            $out['jc_lead_type'] = __('Type', 'justccell');
            $out['jc_lead_email'] = __('Email', 'justccell');
            $out['jc_lead_company'] = __('Company', 'justccell');
            $out['jc_lead_country'] = __('Country', 'justccell');
            $out['jc_lead_status'] = __('Status', 'justccell');
        }
        $out[$key] = $label;
    }
    return $out;
});

add_action('manage_jc_lead_posts_custom_column', static function (string $column, int $post_id): void {
    switch ($column) {
        case 'jc_lead_read':
            if (justccell_lead_is_read($post_id)) {
                echo '<span class="jc-lead-pill jc-lead-pill--read">' . esc_html__('Read', 'justccell') . '</span>';
            } else {
                echo '<span class="jc-lead-pill jc-lead-pill--unread">' . esc_html__('Unread', 'justccell') . '</span>';
            }
            break;
        case 'jc_lead_type':
            $type = justccell_lead_meta($post_id, 'type');
            echo esc_html($type !== '' ? ucfirst($type) : '—');
            break;
        case 'jc_lead_email':
            $email = justccell_lead_meta($post_id, 'email');
            if ($email !== '' && is_email($email)) {
                echo '<a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            } else {
                echo esc_html($email !== '' ? $email : '—');
            }
            break;
        case 'jc_lead_company':
            echo esc_html(justccell_lead_meta($post_id, 'company') ?: '—');
            break;
        case 'jc_lead_country':
            echo esc_html(justccell_lead_meta($post_id, 'country') ?: '—');
            break;
        case 'jc_lead_status':
            $status = justccell_lead_status($post_id);
            echo '<span class="jc-lead-status jc-lead-status--' . esc_attr($status) . '">' . esc_html(justccell_lead_status_label($status)) . '</span>';
            break;
    }
}, 10, 2);

add_filter('manage_edit-jc_lead_sortable_columns', static function (array $columns): array {
    $columns['jc_lead_status'] = 'jc_lead_status';
    $columns['jc_lead_email'] = 'jc_lead_email';
    return $columns;
});

add_action('pre_get_posts', static function (WP_Query $query): void {
    if (!is_admin() || !$query->is_main_query() || $query->get('post_type') !== 'jc_lead') {
        return;
    }
    $orderby = $query->get('orderby');
    if ($orderby === 'jc_lead_status') {
        $query->set('meta_key', '_jc_lead_status');
        $query->set('orderby', 'meta_value');
    }
    if ($orderby === 'jc_lead_email') {
        $query->set('meta_key', '_jc_lead_email');
        $query->set('orderby', 'meta_value');
    }
});

add_filter('bulk_actions-edit-jc_lead', static function (array $actions): array {
    $actions['jc_mark_read'] = __('Mark as read', 'justccell');
    $actions['jc_mark_unread'] = __('Mark as unread', 'justccell');
    return $actions;
});

add_filter('handle_bulk_actions-edit-jc_lead', static function (string $redirect, string $action, array $post_ids): string {
    if ($action !== 'jc_mark_read' && $action !== 'jc_mark_unread') {
        return $redirect;
    }
    $read = $action === 'jc_mark_read';
    foreach ($post_ids as $post_id) {
        if ((int) $post_id > 0 && get_post_type((int) $post_id) === 'jc_lead') {
            justccell_lead_mark_read((int) $post_id, $read);
        }
    }
    return add_query_arg('jc_leads_updated', count($post_ids), $redirect);
}, 10, 3);

add_action('admin_notices', static function (): void {
    if (!isset($_GET['jc_leads_updated'])) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'jc_lead') {
        return;
    }
    $count = (int) $_GET['jc_leads_updated'];
    if ($count < 1) {
        return;
    }
    echo '<div class="notice notice-success is-dismissible"><p>';
    echo esc_html(sprintf(
        /* translators: %d: number of leads updated */
        _n('%d lead updated.', '%d leads updated.', $count, 'justccell'),
        $count
    ));
    echo '</p></div>';
});

add_action('add_meta_boxes', static function (): void {
    add_meta_box(
        'jc_lead_details',
        __('Lead details', 'justccell'),
        'justccell_render_lead_meta_box',
        'jc_lead',
        'side',
        'high'
    );
});

function justccell_render_lead_meta_box(WP_Post $post): void
{
    wp_nonce_field('justccell_lead_save', 'justccell_lead_nonce');
    $status = justccell_lead_status((int) $post->ID);
    $read   = justccell_lead_is_read((int) $post->ID);
    ?>
    <p>
        <label for="jc_lead_status"><strong><?php esc_html_e('Status', 'justccell'); ?></strong></label><br>
        <select name="jc_lead_status" id="jc_lead_status" style="width:100%;margin-top:4px;">
            <?php foreach (justccell_lead_statuses() as $option) : ?>
                <option value="<?php echo esc_attr($option); ?>" <?php selected($status, $option); ?>>
                    <?php echo esc_html(justccell_lead_status_label($option)); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </p>
    <p>
        <label>
            <input type="checkbox" name="jc_lead_read" value="1" <?php checked($read); ?>>
            <?php esc_html_e('Mark as read', 'justccell'); ?>
        </label>
    </p>
    <?php
    $rows = [
        'type'    => __('Type', 'justccell'),
        'email'   => __('Email', 'justccell'),
        'phone'   => __('Phone', 'justccell'),
        'company' => __('Company', 'justccell'),
        'country' => __('Country', 'justccell'),
        'sku'     => __('SKU', 'justccell'),
    ];
    echo '<dl class="jc-lead-dl">';
    foreach ($rows as $key => $label) {
        $val = justccell_lead_meta((int) $post->ID, $key);
        if ($val === '') {
            continue;
        }
        echo '<dt>' . esc_html($label) . '</dt><dd>';
        if ($key === 'email' && is_email($val)) {
            echo '<a href="mailto:' . esc_attr($val) . '">' . esc_html($val) . '</a>';
        } else {
            echo esc_html($val);
        }
        echo '</dd>';
    }
    echo '</dl>';
}

add_action('save_post_jc_lead', static function (int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST['justccell_lead_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['justccell_lead_nonce'])), 'justccell_lead_save')) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    if (isset($_POST['jc_lead_status'])) {
        justccell_lead_set_status($post_id, sanitize_key(wp_unslash((string) $_POST['jc_lead_status'])));
    }
    justccell_lead_mark_read($post_id, !empty($_POST['jc_lead_read']));
}, 10, 1);

add_action('load-post.php', static function (): void {
    if (!isset($_GET['post'])) {
        return;
    }
    $post_id = (int) $_GET['post'];
    if ($post_id < 1 || get_post_type($post_id) !== 'jc_lead') {
        return;
    }
    justccell_lead_mark_read($post_id, true);
});

add_action('admin_menu', static function (): void {
    $count = justccell_leads_unread_count();
    if ($count < 1) {
        return;
    }

    global $menu, $submenu;
    $badge = ' <span class="awaiting-mod update-plugins count-' . (int) $count . '"><span class="plugin-count">' . (int) $count . '</span></span>';

    if (is_array($menu)) {
        foreach ($menu as &$item) {
            if (is_array($item) && ($item[2] ?? '') === 'justccell') {
                $item[0] .= $badge;
                break;
            }
        }
        unset($item);
    }

    if (is_array($submenu['justccell'] ?? null)) {
        foreach ($submenu['justccell'] as &$item) {
            if (is_array($item) && ($item[2] ?? '') === 'edit.php?post_type=jc_lead') {
                $item[0] .= $badge;
                break;
            }
        }
        unset($item);
    }
}, 999);

add_action('admin_head', static function (): void {
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || $screen->post_type !== 'jc_lead') {
        return;
    }
    ?>
    <style>
        .jc-lead-pill { display:inline-block;padding:2px 8px;border-radius:999px;font-size:11px;font-weight:600;line-height:1.4; }
        .jc-lead-pill--unread { background:#2271b1;color:#fff; }
        .jc-lead-pill--read { background:#dcdcde;color:#50575e; }
        .jc-lead-status { display:inline-block;padding:2px 8px;border-radius:4px;font-size:11px;font-weight:600;background:#f0f0f1;color:#1d2327; }
        .jc-lead-status--new { background:#dbeafe;color:#1e40af; }
        .jc-lead-status--in_progress { background:#fef3c7;color:#92400e; }
        .jc-lead-status--replied { background:#e0e7ff;color:#3730a3; }
        .jc-lead-status--qualified { background:#dcfce7;color:#166534; }
        .jc-lead-status--closed { background:#e5e7eb;color:#374151; }
        .jc-lead-status--spam { background:#fee2e2;color:#991b1b; }
        tr.type-jc_lead:has(.jc-lead-pill--unread) td.column-title strong { font-weight:700; }
        tr.type-jc_lead:has(.jc-lead-pill--unread) { background:#f0f6fc; }
        .jc-lead-dl { margin:12px 0 0; }
        .jc-lead-dl dt { font-weight:600;margin-top:8px; }
        .jc-lead-dl dd { margin:2px 0 0; }
    </style>
    <?php
});
