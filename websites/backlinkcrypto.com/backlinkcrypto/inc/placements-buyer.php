<?php
/**
 * Buyer-facing placement upload UI (My Account).
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    add_rewrite_endpoint('placements', EP_ROOT | EP_PAGES);
});

add_filter('woocommerce_account_menu_items', static function (array $items): array {
    $new = [];
    foreach ($items as $key => $label) {
        $new[$key] = $label;
        if ($key === 'orders') {
            $new['placements'] = __('Placements', 'backlinkcrypto');
        }
    }
    if (!isset($new['placements'])) {
        $new['placements'] = __('Placements', 'backlinkcrypto');
    }
    return $new;
});

add_action('woocommerce_account_placements_endpoint', 'backlinkcrypto_render_account_placements');

add_action('woocommerce_order_details_after_order_table', static function ($order): void {
    if (!$order instanceof WC_Order) {
        return;
    }

    if (!backlinkcrypto_order_is_payment_confirmed($order)) {
        echo '<section class="bc-order-placements bc-order-placements--pending">';
        echo '<h2>' . esc_html__('Payment confirmation', 'backlinkcrypto') . '</h2>';
        echo '<p class="bc-muted">' . esc_html__('Your order is on hold while we verify your crypto payment. After confirmation, placement tickets unlock here and in My Account → Placements so you can upload articles.', 'backlinkcrypto') . '</p>';
        echo '</section>';
        return;
    }

    $placements = backlinkcrypto_get_placements_for_order($order->get_id());
    if ($placements === []) {
        backlinkcrypto_maybe_create_placements_for_order($order->get_id());
        $placements = backlinkcrypto_get_placements_for_order($order->get_id());
    }
    if ($placements === []) {
        return;
    }

    echo '<section class="bc-order-placements">';
    echo '<h2>' . esc_html__('Placement tickets', 'backlinkcrypto') . '</h2>';
    echo '<p class="bc-muted">' . esc_html__('Each purchased slot has its own ticket — upload one article per ticket.', 'backlinkcrypto') . '</p>';
    echo '<ul class="bc-placement-mini-list">';
    foreach ($placements as $post) {
        $d = backlinkcrypto_placement_data($post->ID);
        printf(
            '<li><strong>%s</strong> · %s · <span class="bc-status bc-status--%s">%s</span> · slot %d/%d</li>',
            esc_html($d['code']),
            esc_html($d['domain']),
            esc_attr($d['status']),
            esc_html($d['status_label']),
            (int) $d['slot'],
            (int) $d['slot_total']
        );
    }
    echo '</ul>';
    printf(
        '<p><a class="button" href="%s">%s</a></p>',
        esc_url(wc_get_account_endpoint_url('placements')),
        esc_html__('Manage article uploads', 'backlinkcrypto')
    );
    echo '</section>';
}, 20);

add_action('template_redirect', static function (): void {
    if (!isset($_POST['bc_placement_action'])) {
        return;
    }
    if (!is_user_logged_in()) {
        return;
    }

    $action = sanitize_text_field((string) wp_unslash($_POST['bc_placement_action']));
    $placement_id = isset($_POST['placement_id']) ? (int) $_POST['placement_id'] : 0;
    if ($placement_id <= 0) {
        return;
    }

    if (!isset($_POST['_wpnonce']) || !wp_verify_nonce((string) wp_unslash($_POST['_wpnonce']), 'bc_placement_' . $placement_id)) {
        wc_add_notice(__('Security check failed. Please try again.', 'backlinkcrypto'), 'error');
        return;
    }

    $data = backlinkcrypto_placement_data($placement_id);
    $user_id = get_current_user_id();
    if ((int) $data['customer_id'] !== $user_id && !current_user_can('manage_woocommerce')) {
        wc_add_notice(__('You cannot edit this placement.', 'backlinkcrypto'), 'error');
        return;
    }

    if ($action === 'submit_article') {
        if (!backlinkcrypto_customer_can_edit_placement($data)) {
            wc_add_notice(__('This placement is locked while we process it.', 'backlinkcrypto'), 'error');
            wp_safe_redirect(wc_get_account_endpoint_url('placements'));
            exit;
        }

        $title   = sanitize_text_field((string) wp_unslash($_POST['article_title'] ?? ''));
        $content = wp_kses_post((string) wp_unslash($_POST['article_content'] ?? ''));
        $doc_url = esc_url_raw((string) wp_unslash($_POST['article_doc_url'] ?? ''));
        $target  = esc_url_raw((string) wp_unslash($_POST['target_url'] ?? ''));
        $anchors = sanitize_textarea_field((string) wp_unslash($_POST['anchors'] ?? ''));
        $bio     = sanitize_textarea_field((string) wp_unslash($_POST['bio'] ?? ''));
        $notes   = sanitize_textarea_field((string) wp_unslash($_POST['buyer_notes'] ?? ''));

        $has_body = trim(wp_strip_all_tags($content)) !== '';
        $has_doc  = $doc_url !== '';
        $has_file = !empty($_FILES['article_file']['name']);

        if ($title === '' || (!$has_body && !$has_doc && !$has_file)) {
            wc_add_notice(__('Please add a title and either paste content, a Google Doc link, or upload a file.', 'backlinkcrypto'), 'error');
            wp_safe_redirect(add_query_arg('placement', $placement_id, wc_get_account_endpoint_url('placements')));
            exit;
        }

        update_post_meta($placement_id, '_bc_article_title', $title);
        update_post_meta($placement_id, '_bc_article_content', $content);
        update_post_meta($placement_id, '_bc_article_doc_url', $doc_url);
        update_post_meta($placement_id, '_bc_target_url', $target);
        update_post_meta($placement_id, '_bc_anchors', $anchors);
        update_post_meta($placement_id, '_bc_bio', $bio);
        update_post_meta($placement_id, '_bc_buyer_notes', $notes);

        if ($has_file && isset($_FILES['article_file']) && is_array($_FILES['article_file'])) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
            require_once ABSPATH . 'wp-admin/includes/media.php';
            require_once ABSPATH . 'wp-admin/includes/image.php';

            $file = $_FILES['article_file'];
            $allowed = ['doc', 'docx', 'pdf', 'txt', 'rtf', 'odt'];
            $ext = strtolower(pathinfo((string) $file['name'], PATHINFO_EXTENSION));
            if (!in_array($ext, $allowed, true)) {
                wc_add_notice(__('Upload a .docx, .doc, .pdf, .txt, .rtf, or .odt file.', 'backlinkcrypto'), 'error');
                wp_safe_redirect(add_query_arg('placement', $placement_id, wc_get_account_endpoint_url('placements')));
                exit;
            }

            $upload = wp_handle_upload($file, ['test_form' => false]);
            if (!isset($upload['error']) && isset($upload['file'], $upload['url'], $upload['type'])) {
                $attachment = [
                    'post_mime_type' => $upload['type'],
                    'post_title'     => sanitize_file_name(basename($upload['file'])),
                    'post_content'   => '',
                    'post_status'    => 'inherit',
                ];
                $attach_id = wp_insert_attachment($attachment, $upload['file']);
                if (!is_wp_error($attach_id) && $attach_id) {
                    update_post_meta($placement_id, '_bc_article_file_id', (string) $attach_id);
                }
            } else {
                wc_add_notice(__('File upload failed. You can still paste content or use a Doc link.', 'backlinkcrypto'), 'error');
            }
        }

        backlinkcrypto_set_placement_status($placement_id, 'in_review', false);
        update_post_meta($placement_id, '_bc_submitted_at', gmdate('c'));

        // Notify shop manager.
        $admin_email = get_option('admin_email');
        if ($admin_email) {
            wp_mail(
                $admin_email,
                sprintf('[Backlink Crypto] Article submitted — %s (%s)', $data['code'], $data['domain']),
                sprintf(
                    "Buyer submitted placement %s for %s.\n\nTitle: %s\nTarget: %s\nReview in WP Admin → Placements.\n",
                    $data['code'],
                    $data['domain'],
                    $title,
                    $target
                )
            );
        }

        wc_add_notice(__('Article submitted for review. You can still revise it if we request changes.', 'backlinkcrypto'), 'success');
        wp_safe_redirect(wc_get_account_endpoint_url('placements'));
        exit;
    }
});

function backlinkcrypto_render_account_placements(): void
{
    $user_id = get_current_user_id();
    $placements = backlinkcrypto_get_placements_for_customer($user_id);
    $focus = isset($_GET['placement']) ? (int) $_GET['placement'] : 0;

    echo '<div class="bc-placements">';
    echo '<header class="bc-placements__header">';
    echo '<h2>' . esc_html__('Your placements', 'backlinkcrypto') . '</h2>';
    echo '<p>' . esc_html__('Each ticket is one guest post slot. Tickets unlock after we confirm your crypto payment.', 'backlinkcrypto') . '</p>';
    echo '</header>';

    // Show on-hold orders waiting for payment confirmation.
    $pending_orders = [];
    if (function_exists('wc_get_orders') && $user_id > 0) {
        $pending_orders = wc_get_orders([
            'customer_id' => $user_id,
            'status'      => ['on-hold', 'pending'],
            'limit'       => 20,
            'orderby'     => 'date',
            'order'       => 'DESC',
        ]);
    }

    if ($pending_orders !== []) {
        echo '<section class="bc-awaiting-payment">';
        echo '<h3>' . esc_html__('Awaiting payment confirmation', 'backlinkcrypto') . '</h3>';
        echo '<p class="bc-muted">' . esc_html__('Send crypto using the wallet instructions on your order, then wait for our team to verify the transfer. Article upload unlocks after confirmation.', 'backlinkcrypto') . '</p>';
        echo '<ul class="bc-awaiting-payment__list">';
        foreach ($pending_orders as $po) {
            if (!$po instanceof WC_Order) {
                continue;
            }
            printf(
                '<li><a href="%s">%s #%d</a> · %s · <span>%s</span></li>',
                esc_url($po->get_view_order_url()),
                esc_html__('Order', 'backlinkcrypto'),
                (int) $po->get_id(),
                wp_kses_post($po->get_formatted_order_total()),
                esc_html(wc_get_order_status_name($po->get_status()))
            );
        }
        echo '</ul></section>';
    }

    if ($placements === []) {
        echo '<div class="bc-empty"><p>' . esc_html__('No placement tickets yet. After we confirm payment, tickets appear here automatically and we email you to upload articles.', 'backlinkcrypto') . '</p>';
        printf(
            '<p><a class="button" href="%s">%s</a></p>',
            esc_url(wc_get_account_endpoint_url('orders')),
            esc_html__('View orders', 'backlinkcrypto')
        );
        echo '</div></div>';
        return;
    }

    // Group by order.
    $by_order = [];
    foreach ($placements as $post) {
        $d = backlinkcrypto_placement_data($post->ID);
        $oid = (int) $d['order_id'];
        $by_order[$oid][] = $d;
    }

    foreach ($by_order as $order_id => $rows) {
        echo '<section class="bc-placement-order">';
        printf(
            '<h3>%s <a href="%s">#%d</a></h3>',
            esc_html__('Order', 'backlinkcrypto'),
            esc_url(wc_get_account_endpoint_url('view-order/' . $order_id)),
            (int) $order_id
        );

        foreach ($rows as $d) {
            $open = $focus === (int) $d['id'] || backlinkcrypto_customer_can_edit_placement($d);
            $can_edit = backlinkcrypto_customer_can_edit_placement($d);
            $file_url = $d['article_file_id'] ? wp_get_attachment_url($d['article_file_id']) : '';

            printf('<article class="bc-ticket" id="placement-%d" data-status="%s">', (int) $d['id'], esc_attr($d['status']));
            echo '<div class="bc-ticket__top">';
            printf('<div><strong class="bc-ticket__code">%s</strong>', esc_html($d['code']));
            printf(
                '<div class="bc-ticket__meta">%s · slot %d of %d</div></div>',
                esc_html($d['domain']),
                (int) $d['slot'],
                (int) $d['slot_total']
            );
            printf(
                '<span class="bc-status bc-status--%s">%s</span>',
                esc_attr($d['status']),
                esc_html($d['status_label'])
            );
            echo '</div>';

            if ($d['status'] === 'needs_revision' && $d['revision_note'] !== '') {
                echo '<div class="bc-ticket__revision"><strong>' . esc_html__('Revision requested:', 'backlinkcrypto') . '</strong> ';
                echo esc_html($d['revision_note']);
                echo '</div>';
            }

            if (in_array($d['status'], ['published', 'completed'], true) && $d['live_url'] !== '') {
                printf(
                    '<p class="bc-ticket__live">%s <a href="%s" target="_blank" rel="noopener noreferrer">%s</a></p>',
                    esc_html__('Live URL:', 'backlinkcrypto'),
                    esc_url($d['live_url']),
                    esc_html($d['live_url'])
                );
            }

            if ($can_edit) {
                echo '<details class="bc-ticket__form" ' . ($open ? 'open' : '') . '>';
                echo '<summary>' . esc_html__('Upload / edit article', 'backlinkcrypto') . '</summary>';
                echo '<form method="post" enctype="multipart/form-data" class="bc-form">';
                wp_nonce_field('bc_placement_' . $d['id']);
                echo '<input type="hidden" name="bc_placement_action" value="submit_article" />';
                printf('<input type="hidden" name="placement_id" value="%d" />', (int) $d['id']);

                echo '<label>' . esc_html__('Article title', 'backlinkcrypto');
                printf('<input type="text" name="article_title" required value="%s" />', esc_attr($d['article_title']));
                echo '</label>';

                echo '<label>' . esc_html__('Target URL (page you want linked)', 'backlinkcrypto');
                printf('<input type="url" name="target_url" placeholder="https://" value="%s" />', esc_attr($d['target_url']));
                echo '</label>';

                echo '<label>' . esc_html__('Anchor text(s)', 'backlinkcrypto');
                printf('<textarea name="anchors" rows="2" placeholder="Primary anchor, secondary…">%s</textarea>', esc_textarea($d['anchors']));
                echo '</label>';

                echo '<label>' . esc_html__('Paste article (optional if uploading a file or Doc link)', 'backlinkcrypto');
                printf('<textarea name="article_content" rows="8">%s</textarea>', esc_textarea($d['article_content']));
                echo '</label>';

                echo '<label>' . esc_html__('Google Doc / Drive link', 'backlinkcrypto');
                printf('<input type="url" name="article_doc_url" placeholder="https://docs.google.com/…" value="%s" />', esc_attr($d['article_doc_url']));
                echo '</label>';

                echo '<label>' . esc_html__('Upload file (.docx / .pdf / .txt)', 'backlinkcrypto');
                echo '<input type="file" name="article_file" accept=".doc,.docx,.pdf,.txt,.rtf,.odt" />';
                if ($file_url) {
                    printf(' <a href="%s" target="_blank" rel="noopener">%s</a>', esc_url($file_url), esc_html__('Current file', 'backlinkcrypto'));
                }
                echo '</label>';

                echo '<label>' . esc_html__('Author bio (optional)', 'backlinkcrypto');
                printf('<textarea name="bio" rows="2">%s</textarea>', esc_textarea($d['bio']));
                echo '</label>';

                echo '<label>' . esc_html__('Notes for publisher (optional)', 'backlinkcrypto');
                printf('<textarea name="buyer_notes" rows="2">%s</textarea>', esc_textarea($d['buyer_notes']));
                echo '</label>';

                echo '<button type="submit" class="button">' . esc_html__('Submit article for review', 'backlinkcrypto') . '</button>';
                echo '</form></details>';
            } else {
                echo '<div class="bc-ticket__summary">';
                if ($d['article_title'] !== '') {
                    printf('<p><strong>%s</strong> %s</p>', esc_html__('Title:', 'backlinkcrypto'), esc_html($d['article_title']));
                }
                if ($d['target_url'] !== '') {
                    printf('<p><strong>%s</strong> <a href="%s" target="_blank" rel="noopener">%s</a></p>', esc_html__('Target:', 'backlinkcrypto'), esc_url($d['target_url']), esc_html($d['target_url']));
                }
                if ($d['anchors'] !== '') {
                    printf('<p><strong>%s</strong> %s</p>', esc_html__('Anchors:', 'backlinkcrypto'), esc_html($d['anchors']));
                }
                echo '<p class="bc-muted">' . esc_html__('This ticket is locked while we fulfill it.', 'backlinkcrypto') . '</p>';
                echo '</div>';
            }

            echo '</article>';
        }

        echo '</section>';
    }

    echo '</div>';
}

/**
 * Flush rewrite rules once after theme update so /my-account/placements/ works.
 */
add_action('after_switch_theme', static function (): void {
    flush_rewrite_rules();
});

add_action('init', static function (): void {
    if (get_option('bc_placements_rewrite_flushed') === BACKLINKCRYPTO_VERSION) {
        return;
    }
    flush_rewrite_rules(false);
    update_option('bc_placements_rewrite_flushed', BACKLINKCRYPTO_VERSION);
}, 99);
