<?php
/**
 * Placement tickets — fulfillment units for guest-post orders.
 *
 * One paid order line unit → one bc_placement post (BC-{order}-{slot}).
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once BACKLINKCRYPTO_DIR . '/inc/placements-admin.php';
require_once BACKLINKCRYPTO_DIR . '/inc/placements-buyer.php';

/** @return list<string> */
function backlinkcrypto_placement_statuses(): array
{
    return [
        'awaiting_article',
        'in_review',
        'needs_revision',
        'ready',
        'published',
        'completed',
    ];
}

/**
 * @return array<string,string>
 */
function backlinkcrypto_placement_status_labels(): array
{
    return [
        'awaiting_article' => __('Awaiting article', 'backlinkcrypto'),
        'in_review'        => __('In review', 'backlinkcrypto'),
        'needs_revision'   => __('Needs revision', 'backlinkcrypto'),
        'ready'            => __('Ready to publish', 'backlinkcrypto'),
        'published'        => __('Published', 'backlinkcrypto'),
        'completed'        => __('Completed', 'backlinkcrypto'),
    ];
}

function backlinkcrypto_placement_status_label(string $status): string
{
    $labels = backlinkcrypto_placement_status_labels();
    return $labels[$status] ?? $status;
}

add_action('init', static function (): void {
    register_post_type('bc_placement', [
        'labels' => [
            'name'               => __('Placements', 'backlinkcrypto'),
            'singular_name'      => __('Placement', 'backlinkcrypto'),
            'add_new_item'       => __('Add Placement', 'backlinkcrypto'),
            'edit_item'          => __('Edit Placement', 'backlinkcrypto'),
            'view_item'          => __('View Placement', 'backlinkcrypto'),
            'search_items'       => __('Search Placements', 'backlinkcrypto'),
            'not_found'          => __('No placements found', 'backlinkcrypto'),
            'not_found_in_trash' => __('No placements in trash', 'backlinkcrypto'),
            'menu_name'          => __('Placements', 'backlinkcrypto'),
        ],
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_icon'          => 'dashicons-media-document',
        'menu_position'      => 56,
        'supports'           => ['title'],
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'has_archive'        => false,
        'rewrite'            => false,
        'exclude_from_search'=> true,
    ]);
});

/**
 * Create placement tickets only after payment is confirmed (not on crypto on-hold).
 */
function backlinkcrypto_order_is_payment_confirmed($order): bool
{
    if (!$order instanceof WC_Order) {
        return false;
    }

    if ($order->get_meta('_bc_payment_confirmed') === '1') {
        return true;
    }

    // WooCommerce "paid" statuses — never treat on-hold as paid for crypto.
    $status = $order->get_status();
    if (in_array($status, ['processing', 'completed'], true)) {
        return true;
    }

    return $order->is_paid() && !in_array($status, ['on-hold', 'pending', 'failed', 'cancelled', 'refunded'], true);
}

/**
 * Create placement tickets when an order becomes paid / processing / completed.
 */
function backlinkcrypto_maybe_create_placements_for_order(int $order_id): void
{
    if (!function_exists('wc_get_order')) {
        return;
    }

    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }

    if ($order->get_meta('_bc_placements_created') === '1') {
        return;
    }

    // Crypto pay: wait for admin confirmation (processing/completed).
    if (!backlinkcrypto_order_is_payment_confirmed($order)) {
        return;
    }

    $customer_id = (int) $order->get_user_id();
    $created     = 0;

    foreach ($order->get_items() as $item_id => $item) {
        if (!$item instanceof WC_Order_Item_Product) {
            continue;
        }

        $product_id = (int) $item->get_product_id();
        $qty        = max(1, (int) $item->get_quantity());
        $domain     = (string) get_post_meta($product_id, '_bc_domain', true);
        if ($domain === '') {
            $domain = $item->get_name();
        }

        for ($slot = 1; $slot <= $qty; $slot++) {
            $code = sprintf('BC-%d-%02d', $order_id, $created + 1);

            // Idempotent: skip if this exact slot already exists for this order item.
            $existing = get_posts([
                'post_type'      => 'bc_placement',
                'post_status'    => 'any',
                'posts_per_page' => 1,
                'fields'         => 'ids',
                'meta_query'     => [
                    'relation' => 'AND',
                    ['key' => '_bc_order_id', 'value' => (string) $order_id],
                    ['key' => '_bc_order_item_id', 'value' => (string) $item_id],
                    ['key' => '_bc_slot', 'value' => (string) $slot],
                ],
            ]);
            if ($existing) {
                $created++;
                continue;
            }

            $placement_id = wp_insert_post([
                'post_type'   => 'bc_placement',
                'post_status' => 'publish',
                'post_title'  => $code,
                'post_author' => $customer_id > 0 ? $customer_id : 1,
            ], true);

            if (is_wp_error($placement_id) || !$placement_id) {
                continue;
            }

            update_post_meta($placement_id, '_bc_code', $code);
            update_post_meta($placement_id, '_bc_order_id', (string) $order_id);
            update_post_meta($placement_id, '_bc_order_item_id', (string) $item_id);
            update_post_meta($placement_id, '_bc_product_id', (string) $product_id);
            update_post_meta($placement_id, '_bc_domain', $domain);
            update_post_meta($placement_id, '_bc_slot', (string) $slot);
            update_post_meta($placement_id, '_bc_slot_total', (string) $qty);
            update_post_meta($placement_id, '_bc_status', 'awaiting_article');
            update_post_meta($placement_id, '_bc_customer_id', (string) $customer_id);
            update_post_meta($placement_id, '_bc_customer_email', $order->get_billing_email());

            $created++;
        }
    }

    if ($created > 0) {
        $order->update_meta_data('_bc_placements_created', '1');
        $order->update_meta_data('_bc_placements_count', (string) $created);
        $order->add_order_note(
            sprintf(
                /* translators: %d: number of placement tickets */
                __('Created %d placement ticket(s) for article fulfillment.', 'backlinkcrypto'),
                $created
            )
        );
        $order->save();

        backlinkcrypto_notify_buyer_placements_ready($order, $created);
    }
}

/**
 * @param WC_Order $order
 */
function backlinkcrypto_notify_buyer_placements_ready($order, int $count): void
{
    $email = $order->get_billing_email();
    if (!$email) {
        return;
    }

    $account_url = wc_get_account_endpoint_url('placements');
    $subject     = sprintf(
        /* translators: %s: site name */
        __('[%s] Upload your guest post articles', 'backlinkcrypto'),
        wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)
    );
    $message = sprintf(
        "Hi %s,\n\nGood news — we confirmed payment for order #%d.\n\nYour placement tickets are ready. Upload one guest-post article per ticket here:\n%s\n\nWe created %d ticket(s). Each is tied to a specific website, even if you bought the same site multiple times.\n\nThanks,\n%s",
        $order->get_billing_first_name() ?: 'there',
        $order->get_id(),
        $account_url,
        $count,
        wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)
    );

    wp_mail($email, $subject, $message);
}

/**
 * Admin confirms crypto payment → move to processing + unlock tickets.
 */
function backlinkcrypto_confirm_order_payment(int $order_id, string $note = ''): bool
{
    $order = wc_get_order($order_id);
    if (!$order) {
        return false;
    }

    $order->update_meta_data('_bc_payment_confirmed', '1');
    $order->update_meta_data('_bc_payment_confirmed_at', gmdate('c'));
    $order->update_meta_data('_bc_payment_confirmed_by', (string) get_current_user_id());
    $order->add_order_note(
        $note !== ''
            ? $note
            : __('Crypto payment confirmed by admin. Placement tickets will unlock for the buyer.', 'backlinkcrypto')
    );
    $order->save();

    if ($order->has_status(['on-hold', 'pending'])) {
        $order->update_status('processing', __('Payment confirmed — fulfillment started.', 'backlinkcrypto'));
    } else {
        // Already processing/completed: still create tickets if missing.
        backlinkcrypto_maybe_create_placements_for_order($order_id);
    }

    return true;
}

function backlinkcrypto_notify_admin_awaiting_payment($order): void
{
    if (!$order instanceof WC_Order) {
        return;
    }
    if ($order->get_meta('_bc_admin_awaiting_payment_emailed') === '1') {
        return;
    }

    $admin = (string) get_option('admin_email');
    if ($admin === '') {
        return;
    }

    // Prefer internal notify inbox when set (admin-only); never put this on customer emails.
    if (function_exists('backlinkcrypto_get_theme_settings')) {
        $notify = trim((string) (backlinkcrypto_get_theme_settings()['notify_email'] ?? ''));
        if ($notify !== '' && is_email($notify)) {
            $admin = $notify;
        }
    }

    $total_plain = html_entity_decode(
        wp_strip_all_tags($order->get_formatted_order_total()),
        ENT_QUOTES | ENT_HTML5,
        'UTF-8'
    );
    $total_plain = preg_replace('/\s+/u', ' ', trim((string) $total_plain)) ?: (string) $order->get_total();

    $edit = admin_url('post.php?post=' . $order->get_id() . '&action=edit');
    $subject = sprintf(
        '[%s] Order #%d awaiting crypto payment confirmation',
        wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES),
        $order->get_id()
    );
    $message = sprintf(
        "New order #%d is on hold awaiting crypto payment confirmation.\n\nCustomer: %s <%s>\nTotal: %s %s\n\nConfirm payment in WP Admin (button on the order), then the buyer gets an email to upload articles.\n\n%s\n",
        $order->get_id(),
        $order->get_formatted_billing_full_name(),
        $order->get_billing_email(),
        $total_plain,
        $order->get_currency(),
        $edit
    );
    wp_mail($admin, $subject, $message, [
        'Content-Type: text/plain; charset=UTF-8',
    ]);
    $order->update_meta_data('_bc_admin_awaiting_payment_emailed', '1');
    $order->save();
}

function backlinkcrypto_notify_buyer_awaiting_payment($order): void
{
    if (!$order instanceof WC_Order) {
        return;
    }
    if ($order->get_meta('_bc_buyer_awaiting_payment_emailed') === '1') {
        return;
    }
    $email = $order->get_billing_email();
    if (!$email) {
        return;
    }

    $subject = sprintf(
        /* translators: %s: site name */
        __('[%s] Order received — awaiting payment confirmation', 'backlinkcrypto'),
        wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)
    );
    $message = sprintf(
        "Hi %s,\n\nThanks for order #%d.\n\nBecause we accept Crypto payment, your order stays on hold until we verify the transfer.\n\nPlease send payment using the wallet instructions on your order email, then reply with the transaction hash.\n\nAfter we confirm payment, you'll get another email with links to upload your guest-post articles in My Account → Placements.\n\nOrder: %s\n\nThanks,\n%s",
        $order->get_billing_first_name() ?: 'there',
        $order->get_id(),
        $order->get_view_order_url(),
        wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES)
    );
    wp_mail($email, $subject, $message, [
        'Content-Type: text/plain; charset=UTF-8',
    ]);
    $order->update_meta_data('_bc_buyer_awaiting_payment_emailed', '1');
    $order->save();
}

add_action('woocommerce_order_status_processing', 'backlinkcrypto_maybe_create_placements_for_order', 20);
add_action('woocommerce_order_status_completed', 'backlinkcrypto_maybe_create_placements_for_order', 20);
add_action('woocommerce_payment_complete', 'backlinkcrypto_maybe_create_placements_for_order', 20);

// On-hold = waiting for crypto confirmation — notify only, do NOT create tickets.
add_action('woocommerce_order_status_on-hold', static function (int $order_id): void {
    $order = wc_get_order($order_id);
    if (!$order) {
        return;
    }
    backlinkcrypto_notify_admin_awaiting_payment($order);
    backlinkcrypto_notify_buyer_awaiting_payment($order);
}, 20);

/**
 * @return list<WP_Post>
 */
function backlinkcrypto_get_placements_for_order(int $order_id): array
{
    return get_posts([
        'post_type'      => 'bc_placement',
        'post_status'    => 'publish',
        'posts_per_page' => 200,
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_bc_slot',
        'order'          => 'ASC',
        'meta_query'     => [
            ['key' => '_bc_order_id', 'value' => (string) $order_id],
        ],
    ]);
}

/**
 * @return list<WP_Post>
 */
function backlinkcrypto_get_placements_for_customer(int $user_id): array
{
    if ($user_id <= 0) {
        return [];
    }

    return get_posts([
        'post_type'      => 'bc_placement',
        'post_status'    => 'publish',
        'posts_per_page' => 200,
        'orderby'        => 'date',
        'order'          => 'DESC',
        'meta_query'     => [
            ['key' => '_bc_customer_id', 'value' => (string) $user_id],
        ],
    ]);
}

/**
 * @return array<string,mixed>
 */
function backlinkcrypto_placement_data(int $placement_id): array
{
    $status = (string) get_post_meta($placement_id, '_bc_status', true);
    if ($status === '') {
        $status = 'awaiting_article';
    }

    return [
        'id'              => $placement_id,
        'code'            => (string) (get_post_meta($placement_id, '_bc_code', true) ?: get_the_title($placement_id)),
        'order_id'        => (int) get_post_meta($placement_id, '_bc_order_id', true),
        'product_id'      => (int) get_post_meta($placement_id, '_bc_product_id', true),
        'domain'          => (string) get_post_meta($placement_id, '_bc_domain', true),
        'slot'            => (int) get_post_meta($placement_id, '_bc_slot', true),
        'slot_total'      => (int) get_post_meta($placement_id, '_bc_slot_total', true),
        'status'          => $status,
        'status_label'    => backlinkcrypto_placement_status_label($status),
        'article_title'   => (string) get_post_meta($placement_id, '_bc_article_title', true),
        'article_content' => (string) get_post_meta($placement_id, '_bc_article_content', true),
        'article_doc_url' => (string) get_post_meta($placement_id, '_bc_article_doc_url', true),
        'article_file_id' => (int) get_post_meta($placement_id, '_bc_article_file_id', true),
        'target_url'      => (string) get_post_meta($placement_id, '_bc_target_url', true),
        'anchors'         => (string) get_post_meta($placement_id, '_bc_anchors', true),
        'bio'             => (string) get_post_meta($placement_id, '_bc_bio', true),
        'buyer_notes'     => (string) get_post_meta($placement_id, '_bc_buyer_notes', true),
        'admin_notes'     => (string) get_post_meta($placement_id, '_bc_admin_notes', true),
        'live_url'        => (string) get_post_meta($placement_id, '_bc_live_url', true),
        'revision_note'   => (string) get_post_meta($placement_id, '_bc_revision_note', true),
        'customer_id'     => (int) get_post_meta($placement_id, '_bc_customer_id', true),
        'customer_email'  => (string) get_post_meta($placement_id, '_bc_customer_email', true),
    ];
}

function backlinkcrypto_customer_can_edit_placement(array $data): bool
{
    return in_array($data['status'], ['awaiting_article', 'needs_revision'], true);
}

/**
 * Update status + optional notification.
 */
function backlinkcrypto_set_placement_status(int $placement_id, string $status, bool $notify = true): bool
{
    if (!in_array($status, backlinkcrypto_placement_statuses(), true)) {
        return false;
    }

    $prev = (string) get_post_meta($placement_id, '_bc_status', true);
    update_post_meta($placement_id, '_bc_status', $status);

    if ($notify && $prev !== $status) {
        backlinkcrypto_notify_placement_status_change($placement_id, $status);
    }

    return true;
}

function backlinkcrypto_notify_placement_status_change(int $placement_id, string $status): void
{
    $data = backlinkcrypto_placement_data($placement_id);
    $email = $data['customer_email'];
    if ($email === '' && $data['customer_id'] > 0) {
        $user = get_user_by('id', $data['customer_id']);
        $email = $user ? (string) $user->user_email : '';
    }
    if ($email === '') {
        return;
    }

    // Only ping buyers for actionable statuses.
    if (!in_array($status, ['needs_revision', 'published', 'completed'], true)) {
        return;
    }

    $account_url = wc_get_account_endpoint_url('placements');
    $subject     = sprintf(
        '[%s] Placement %s — %s',
        wp_specialchars_decode(get_bloginfo('name'), ENT_QUOTES),
        $data['code'],
        backlinkcrypto_placement_status_label($status)
    );

    $extra = '';
    if ($status === 'needs_revision' && $data['revision_note'] !== '') {
        $extra = "\n\nRevision notes:\n" . $data['revision_note'];
    }
    if (in_array($status, ['published', 'completed'], true) && $data['live_url'] !== '') {
        $extra = "\n\nLive URL:\n" . $data['live_url'];
    }

    $message = sprintf(
        "Placement %s for %s is now: %s.%s\n\nManage your placements:\n%s\n",
        $data['code'],
        $data['domain'],
        backlinkcrypto_placement_status_label($status),
        $extra,
        $account_url
    );

    wp_mail($email, $subject, $message);
}
