<?php
/**
 * Admin UI for placement fulfillment queue.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('add_meta_boxes', static function (): void {
    add_meta_box(
        'bc_placement_fulfillment',
        __('Fulfillment', 'backlinkcrypto'),
        'backlinkcrypto_render_placement_metabox',
        'bc_placement',
        'normal',
        'high'
    );
});

function backlinkcrypto_render_placement_metabox(WP_Post $post): void
{
    $d = backlinkcrypto_placement_data($post->ID);
    wp_nonce_field('bc_admin_placement_' . $post->ID, 'bc_admin_placement_nonce');

    echo '<div class="bc-admin-placement">';
    printf(
        '<p><strong>%s</strong> %s &nbsp;|&nbsp; <strong>%s</strong> <a href="%s" target="_blank">#%d</a> &nbsp;|&nbsp; <strong>%s</strong> %d/%d</p>',
        esc_html__('Website:', 'backlinkcrypto'),
        esc_html($d['domain']),
        esc_html__('Order:', 'backlinkcrypto'),
        esc_url(admin_url('post.php?post=' . $d['order_id'] . '&action=edit')),
        (int) $d['order_id'],
        esc_html__('Slot:', 'backlinkcrypto'),
        (int) $d['slot'],
        (int) $d['slot_total']
    );

    echo '<p><label><strong>' . esc_html__('Status', 'backlinkcrypto') . '</strong><br />';
    echo '<select name="bc_status" style="min-width:220px">';
    foreach (backlinkcrypto_placement_status_labels() as $key => $label) {
        printf(
            '<option value="%s"%s>%s</option>',
            esc_attr($key),
            selected($d['status'], $key, false),
            esc_html($label)
        );
    }
    echo '</select></label></p>';

    echo '<p><label><strong>' . esc_html__('Live URL', 'backlinkcrypto') . '</strong><br />';
    printf(
        '<input type="url" name="bc_live_url" class="widefat" value="%s" placeholder="https://…" />',
        esc_attr($d['live_url'])
    );
    echo '</label></p>';

    $gallery_on = get_post_meta($post->ID, '_bc_gallery_public', true) === '1';
    echo '<p><label><input type="checkbox" name="bc_gallery_public" value="1"' . checked($gallery_on, true, false) . ' /> ';
    echo esc_html__('Show live URL on public homepage gallery (opt-in proof)', 'backlinkcrypto') . '</label></p>';

    echo '<p><label><strong>' . esc_html__('Revision note (emailed to buyer when status = Needs revision)', 'backlinkcrypto') . '</strong><br />';
    printf(
        '<textarea name="bc_revision_note" class="widefat" rows="3">%s</textarea>',
        esc_textarea($d['revision_note'])
    );
    echo '</label></p>';

    echo '<p><label><strong>' . esc_html__('Admin notes (internal)', 'backlinkcrypto') . '</strong><br />';
    printf(
        '<textarea name="bc_admin_notes" class="widefat" rows="3">%s</textarea>',
        esc_textarea($d['admin_notes'])
    );
    echo '</label></p>';

    echo '<hr /><h3>' . esc_html__('Buyer submission', 'backlinkcrypto') . '</h3>';
    printf('<p><strong>%s</strong> %s</p>', esc_html__('Title:', 'backlinkcrypto'), esc_html($d['article_title'] ?: '—'));
    printf('<p><strong>%s</strong> %s</p>', esc_html__('Target URL:', 'backlinkcrypto'), $d['target_url'] ? '<a href="' . esc_url($d['target_url']) . '" target="_blank">' . esc_html($d['target_url']) . '</a>' : '—');
    printf('<p><strong>%s</strong> %s</p>', esc_html__('Anchors:', 'backlinkcrypto'), esc_html($d['anchors'] ?: '—'));
    if ($d['article_doc_url']) {
        printf('<p><strong>%s</strong> <a href="%s" target="_blank">%s</a></p>', esc_html__('Doc link:', 'backlinkcrypto'), esc_url($d['article_doc_url']), esc_html($d['article_doc_url']));
    }
    if ($d['article_file_id']) {
        $url = wp_get_attachment_url($d['article_file_id']);
        if ($url) {
            printf('<p><strong>%s</strong> <a href="%s" target="_blank">%s</a></p>', esc_html__('File:', 'backlinkcrypto'), esc_url($url), esc_html__('Download', 'backlinkcrypto'));
        }
    }
    if ($d['article_content'] !== '') {
        echo '<p><strong>' . esc_html__('Pasted content:', 'backlinkcrypto') . '</strong></p>';
        echo '<div style="max-height:280px;overflow:auto;background:#f8fafc;border:1px solid #e2e8f0;padding:12px">' . wp_kses_post(wpautop($d['article_content'])) . '</div>';
    }
    if ($d['bio'] !== '') {
        printf('<p><strong>%s</strong> %s</p>', esc_html__('Bio:', 'backlinkcrypto'), esc_html($d['bio']));
    }
    if ($d['buyer_notes'] !== '') {
        printf('<p><strong>%s</strong> %s</p>', esc_html__('Buyer notes:', 'backlinkcrypto'), esc_html($d['buyer_notes']));
    }

    echo '<p class="description">' . esc_html__('Tip: set status to Published and paste the Live URL — the buyer gets an email automatically.', 'backlinkcrypto') . '</p>';
    echo '</div>';
}

add_action('save_post_bc_placement', static function (int $post_id): void {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!isset($_POST['bc_admin_placement_nonce']) || !wp_verify_nonce((string) wp_unslash($_POST['bc_admin_placement_nonce']), 'bc_admin_placement_' . $post_id)) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    $status = sanitize_text_field((string) wp_unslash($_POST['bc_status'] ?? ''));
    $live   = esc_url_raw((string) wp_unslash($_POST['bc_live_url'] ?? ''));
    $rev    = sanitize_textarea_field((string) wp_unslash($_POST['bc_revision_note'] ?? ''));
    $notes  = sanitize_textarea_field((string) wp_unslash($_POST['bc_admin_notes'] ?? ''));

    update_post_meta($post_id, '_bc_live_url', $live);
    update_post_meta($post_id, '_bc_revision_note', $rev);
    update_post_meta($post_id, '_bc_admin_notes', $notes);
    update_post_meta($post_id, '_bc_gallery_public', isset($_POST['bc_gallery_public']) ? '1' : '0');

    if ($status !== '') {
        // Auto-promote to published if live URL set and status still earlier.
        if ($live !== '' && in_array($status, ['ready', 'in_review', 'awaiting_article'], true)) {
            $status = 'published';
        }
        backlinkcrypto_set_placement_status($post_id, $status, true);
    }
}, 20);

add_filter('manage_bc_placement_posts_columns', static function (array $columns): array {
    $new = [];
    $new['cb'] = $columns['cb'] ?? '';
    $new['title'] = __('Placement', 'backlinkcrypto');
    $new['bc_domain'] = __('Website', 'backlinkcrypto');
    $new['bc_order'] = __('Order', 'backlinkcrypto');
    $new['bc_slot'] = __('Slot', 'backlinkcrypto');
    $new['bc_status'] = __('Status', 'backlinkcrypto');
    $new['bc_live'] = __('Live URL', 'backlinkcrypto');
    $new['date'] = $columns['date'] ?? __('Date', 'backlinkcrypto');
    return $new;
});

add_action('manage_bc_placement_posts_custom_column', static function (string $column, int $post_id): void {
    $d = backlinkcrypto_placement_data($post_id);
    switch ($column) {
        case 'bc_domain':
            echo esc_html($d['domain']);
            break;
        case 'bc_order':
            printf(
                '<a href="%s">#%d</a>',
                esc_url(admin_url('post.php?post=' . $d['order_id'] . '&action=edit')),
                (int) $d['order_id']
            );
            break;
        case 'bc_slot':
            printf('%d / %d', (int) $d['slot'], (int) $d['slot_total']);
            break;
        case 'bc_status':
            printf(
                '<span class="bc-status bc-status--%s">%s</span>',
                esc_attr($d['status']),
                esc_html($d['status_label'])
            );
            break;
        case 'bc_live':
            if ($d['live_url'] !== '') {
                printf('<a href="%s" target="_blank">%s</a>', esc_url($d['live_url']), esc_html__('Open', 'backlinkcrypto'));
            } else {
                echo '—';
            }
            break;
        default:
            break;
    }
}, 10, 2);

add_action('restrict_manage_posts', static function (string $post_type): void {
    if ($post_type !== 'bc_placement') {
        return;
    }

    $status = isset($_GET['bc_status_filter']) ? sanitize_text_field((string) wp_unslash($_GET['bc_status_filter'])) : '';
    $domain = isset($_GET['bc_domain_filter']) ? sanitize_text_field((string) wp_unslash($_GET['bc_domain_filter'])) : '';

    echo '<select name="bc_status_filter">';
    echo '<option value="">' . esc_html__('All statuses', 'backlinkcrypto') . '</option>';
    foreach (backlinkcrypto_placement_status_labels() as $key => $label) {
        printf('<option value="%s"%s>%s</option>', esc_attr($key), selected($status, $key, false), esc_html($label));
    }
    echo '</select>';

    printf(
        '<input type="search" name="bc_domain_filter" value="%s" placeholder="%s" />',
        esc_attr($domain),
        esc_attr__('Filter by website…', 'backlinkcrypto')
    );
});

add_action('pre_get_posts', static function (WP_Query $query): void {
    if (!is_admin() || !$query->is_main_query()) {
        return;
    }
    if ($query->get('post_type') !== 'bc_placement') {
        return;
    }

    $meta_query = [];
    $status = isset($_GET['bc_status_filter']) ? sanitize_text_field((string) wp_unslash($_GET['bc_status_filter'])) : '';
    $domain = isset($_GET['bc_domain_filter']) ? sanitize_text_field((string) wp_unslash($_GET['bc_domain_filter'])) : '';

    if ($status !== '') {
        $meta_query[] = ['key' => '_bc_status', 'value' => $status];
    }
    if ($domain !== '') {
        $meta_query[] = ['key' => '_bc_domain', 'value' => $domain, 'compare' => 'LIKE'];
    }
    if ($meta_query !== []) {
        $query->set('meta_query', $meta_query);
    }
});

add_filter('post_row_actions', static function (array $actions, WP_Post $post): array {
    if ($post->post_type !== 'bc_placement') {
        return $actions;
    }
    unset($actions['inline hide-if-no-js']);
    return $actions;
}, 10, 2);

/** Order admin: payment confirm + placement count. */
add_action('woocommerce_admin_order_data_after_order_details', static function ($order): void {
    if (!$order instanceof WC_Order) {
        return;
    }

    $confirmed = backlinkcrypto_order_is_payment_confirmed($order);
    $placements = backlinkcrypto_get_placements_for_order($order->get_id());
    $count = count($placements);

    echo '<div class="form-field" style="clear:both;padding:14px 0 4px;margin-top:8px;border-top:1px solid #dcdcde">';
    echo '<p style="margin:0 0 8px"><strong>' . esc_html__('Crypto payment & placements', 'backlinkcrypto') . '</strong></p>';

    if (!$confirmed) {
        echo '<p style="margin:0 0 10px;color:#996800">' . esc_html__('Awaiting payment confirmation. Do not unlock article uploads until the transfer is verified.', 'backlinkcrypto') . '</p>';
        if (current_user_can('manage_woocommerce')) {
            $url = wp_nonce_url(
                add_query_arg([
                    'bc_confirm_payment' => $order->get_id(),
                ], admin_url('admin.php')),
                'bc_confirm_payment_' . $order->get_id()
            );
            printf(
                '<p style="margin:0 0 12px"><a class="button button-primary" href="%s">%s</a></p>',
                esc_url($url),
                esc_html__('Confirm payment received', 'backlinkcrypto')
            );
        }
    } else {
        echo '<p style="margin:0 0 8px;color:#1e7a3a">' . esc_html__('Payment confirmed — buyer can upload articles.', 'backlinkcrypto') . '</p>';
    }

    echo '<p style="margin:0">';
    echo '<strong>' . esc_html__('Placements:', 'backlinkcrypto') . '</strong> ';
    if ($count === 0) {
        echo esc_html__('none yet', 'backlinkcrypto');
        if ($confirmed && current_user_can('manage_woocommerce')) {
            $url = wp_nonce_url(
                add_query_arg([
                    'bc_create_placements' => $order->get_id(),
                ], admin_url('admin.php')),
                'bc_create_placements_' . $order->get_id()
            );
            printf(' — <a href="%s">%s</a>', esc_url($url), esc_html__('Generate now', 'backlinkcrypto'));
        } elseif (!$confirmed) {
            echo ' — ' . esc_html__('unlocks after payment confirmation', 'backlinkcrypto');
        }
    } else {
        printf(
            '<a href="%s">%s</a>',
            esc_url(admin_url('edit.php?post_type=bc_placement&s=BC-' . $order->get_id())),
            esc_html(sprintf(
                /* translators: %d: count */
                _n('%d ticket', '%d tickets', $count, 'backlinkcrypto'),
                $count
            ))
        );
    }
    echo '</p></div>';
});

add_action('admin_init', static function (): void {
    if (isset($_GET['bc_confirm_payment'])) {
        $order_id = (int) $_GET['bc_confirm_payment'];
        if ($order_id > 0 && current_user_can('manage_woocommerce')) {
            if (isset($_GET['_wpnonce']) && wp_verify_nonce((string) wp_unslash($_GET['_wpnonce']), 'bc_confirm_payment_' . $order_id)) {
                backlinkcrypto_confirm_order_payment($order_id);
            }
        }
        wp_safe_redirect(admin_url('post.php?post=' . $order_id . '&action=edit'));
        exit;
    }

    if (!isset($_GET['bc_create_placements'])) {
        return;
    }
    $order_id = (int) $_GET['bc_create_placements'];
    if ($order_id <= 0 || !current_user_can('manage_woocommerce')) {
        return;
    }
    if (!isset($_GET['_wpnonce']) || !wp_verify_nonce((string) wp_unslash($_GET['_wpnonce']), 'bc_create_placements_' . $order_id)) {
        return;
    }

    $order = wc_get_order($order_id);
    if ($order && !backlinkcrypto_order_is_payment_confirmed($order)) {
        wp_die(esc_html__('Confirm crypto payment first, then generate placement tickets.', 'backlinkcrypto'));
    }

    // Allow manual regenerate by clearing flag first if empty.
    if ($order && count(backlinkcrypto_get_placements_for_order($order_id)) === 0) {
        $order->delete_meta_data('_bc_placements_created');
        $order->save();
    }
    backlinkcrypto_maybe_create_placements_for_order($order_id);

    wp_safe_redirect(admin_url('post.php?post=' . $order_id . '&action=edit'));
    exit;
});
