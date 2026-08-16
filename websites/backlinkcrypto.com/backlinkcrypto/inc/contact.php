<?php
/**
 * Contact form — stores submissions in WP admin + emails support.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const BC_CONTACT_CPT = 'bc_contact';

/**
 * @return array<string,string>
 */
function backlinkcrypto_contact_topics(): array
{
    return [
        'general'      => __('General question', 'backlinkcrypto'),
        'order'        => __('Order / placement support', 'backlinkcrypto'),
        'bulk'         => __('Bulk / retainer quote', 'backlinkcrypto'),
        'partnership'  => __('Publisher / partnership', 'backlinkcrypto'),
        'billing'      => __('Billing / payment', 'backlinkcrypto'),
        'other'        => __('Other', 'backlinkcrypto'),
    ];
}

function backlinkcrypto_contact_notify_email(): string
{
    $settings = function_exists('backlinkcrypto_get_theme_settings')
        ? backlinkcrypto_get_theme_settings()
        : [];
    if (is_array($settings)) {
        $notify = trim((string) ($settings['notify_email'] ?? ''));
        if ($notify !== '' && is_email($notify)) {
            return $notify;
        }
        $email = trim((string) ($settings['support_email'] ?? ''));
        if ($email !== '' && is_email($email)) {
            return $email;
        }
    }
    $admin = get_option('admin_email');
    return is_string($admin) && is_email($admin) ? $admin : backlinkcrypto_default_support_email();
}

add_action('init', static function (): void {
    register_post_type(BC_CONTACT_CPT, [
        'labels' => [
            'name'               => __('Contact Inbox', 'backlinkcrypto'),
            'singular_name'      => __('Contact message', 'backlinkcrypto'),
            'add_new_item'       => __('Add message', 'backlinkcrypto'),
            'edit_item'          => __('View message', 'backlinkcrypto'),
            'search_items'       => __('Search messages', 'backlinkcrypto'),
            'not_found'          => __('No messages yet', 'backlinkcrypto'),
            'menu_name'          => __('Contact Inbox', 'backlinkcrypto'),
        ],
        'public'             => false,
        'show_ui'            => true,
        'show_in_menu'       => true,
        'menu_position'      => 26,
        'menu_icon'          => 'dashicons-email-alt',
        'capability_type'    => 'post',
        'map_meta_cap'       => true,
        'supports'           => ['title', 'editor'],
        'has_archive'        => false,
        'rewrite'            => false,
        'delete_with_user'   => false,
    ]);
});

add_filter('manage_' . BC_CONTACT_CPT . '_posts_columns', static function (array $cols): array {
    return [
        'cb'         => $cols['cb'] ?? '<input type="checkbox" />',
        'title'      => __('Subject', 'backlinkcrypto'),
        'bc_from'    => __('From', 'backlinkcrypto'),
        'bc_topic'   => __('Topic', 'backlinkcrypto'),
        'bc_order'   => __('Order #', 'backlinkcrypto'),
        'bc_status'  => __('Status', 'backlinkcrypto'),
        'date'       => __('Received', 'backlinkcrypto'),
    ];
});

add_action('manage_' . BC_CONTACT_CPT . '_posts_custom_column', static function (string $col, int $post_id): void {
    switch ($col) {
        case 'bc_from':
            $name = (string) get_post_meta($post_id, '_bc_contact_name', true);
            $email = (string) get_post_meta($post_id, '_bc_contact_email', true);
            echo esc_html($name);
            if ($email !== '') {
                echo '<br><a href="mailto:' . esc_attr($email) . '">' . esc_html($email) . '</a>';
            }
            break;
        case 'bc_topic':
            $topic = (string) get_post_meta($post_id, '_bc_contact_topic', true);
            $topics = backlinkcrypto_contact_topics();
            echo esc_html($topics[$topic] ?? $topic);
            break;
        case 'bc_order':
            $order = (string) get_post_meta($post_id, '_bc_contact_order', true);
            echo $order !== '' ? esc_html($order) : '—';
            break;
        case 'bc_status':
            $read = get_post_meta($post_id, '_bc_contact_read', true) === '1';
            echo $read
                ? '<span style="color:#64748b">' . esc_html__('Read', 'backlinkcrypto') . '</span>'
                : '<strong style="color:#7c3aed">' . esc_html__('New', 'backlinkcrypto') . '</strong>';
            break;
        default:
            break;
    }
}, 10, 2);

add_action('add_meta_boxes', static function (): void {
    add_meta_box(
        'bc_contact_details',
        __('Contact details', 'backlinkcrypto'),
        static function (WP_Post $post): void {
            $fields = [
                'name'    => __('Name', 'backlinkcrypto'),
                'email'   => __('Email', 'backlinkcrypto'),
                'company' => __('Company', 'backlinkcrypto'),
                'topic'   => __('Topic', 'backlinkcrypto'),
                'order'   => __('Order number', 'backlinkcrypto'),
                'ip'      => __('IP', 'backlinkcrypto'),
            ];
            echo '<table class="form-table"><tbody>';
            foreach ($fields as $key => $label) {
                $val = (string) get_post_meta($post->ID, '_bc_contact_' . $key, true);
                if ($key === 'topic') {
                    $topics = backlinkcrypto_contact_topics();
                    $val = $topics[$val] ?? $val;
                }
                echo '<tr><th>' . esc_html($label) . '</th><td>';
                if ($key === 'email' && $val !== '') {
                    echo '<a href="mailto:' . esc_attr($val) . '">' . esc_html($val) . '</a>';
                } else {
                    echo esc_html($val !== '' ? $val : '—');
                }
                echo '</td></tr>';
            }
            echo '</tbody></table>';
            echo '<p><a class="button button-primary" href="mailto:' . esc_attr((string) get_post_meta($post->ID, '_bc_contact_email', true)) . '?subject=' . rawurlencode('Re: ' . $post->post_title) . '">' . esc_html__('Reply by email', 'backlinkcrypto') . '</a></p>';
        },
        BC_CONTACT_CPT,
        'side',
        'high'
    );
});

add_action('load-post.php', static function (): void {
    // Mark as read when opened in admin.
    // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if ($post_id <= 0) {
        return;
    }
    if (get_post_type($post_id) !== BC_CONTACT_CPT) {
        return;
    }
    update_post_meta($post_id, '_bc_contact_read', '1');
});

add_action('admin_menu', static function (): void {
    global $menu;
    $unread = (int) (new WP_Query([
        'post_type'      => BC_CONTACT_CPT,
        'post_status'    => 'publish',
        'posts_per_page' => 1,
        'fields'         => 'ids',
        'meta_key'       => '_bc_contact_read',
        'meta_value'     => '0',
    ]))->found_posts;

    if ($unread <= 0 || !is_array($menu)) {
        return;
    }
    foreach ($menu as $i => $item) {
        if (!is_array($item) || ($item[2] ?? '') !== 'edit.php?post_type=' . BC_CONTACT_CPT) {
            continue;
        }
        $menu[$i][0] .= ' <span class="awaiting-mod">' . esc_html((string) $unread) . '</span>';
        break;
    }
}, 999);

/**
 * Handle POST from contact form (before headers).
 */
function backlinkcrypto_contact_handle_submit(): void
{
    if (!isset($_POST['bc_contact_submit'])) {
        return;
    }
    if (!isset($_POST['bc_contact_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash((string) $_POST['bc_contact_nonce'])), 'bc_contact_submit')) {
        set_transient('bc_contact_flash_' . backlinkcrypto_contact_flash_key(), [
            'type' => 'error',
            'msg'  => __('Security check failed. Please try again.', 'backlinkcrypto'),
        ], 60);
        return;
    }

    // Honeypot — bots fill this.
    $honey = isset($_POST['bc_website']) ? trim((string) wp_unslash($_POST['bc_website'])) : '';
    if ($honey !== '') {
        set_transient('bc_contact_flash_' . backlinkcrypto_contact_flash_key(), [
            'type' => 'ok',
            'msg'  => __('Thanks — your message was sent.', 'backlinkcrypto'),
        ], 60);
        return;
    }

    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash((string) $_SERVER['REMOTE_ADDR'])) : '';
    $rate_key = 'bc_contact_rate_' . md5($ip !== '' ? $ip : 'unknown');
    if (get_transient($rate_key)) {
        set_transient('bc_contact_flash_' . backlinkcrypto_contact_flash_key(), [
            'type' => 'error',
            'msg'  => __('Please wait a minute before sending another message.', 'backlinkcrypto'),
        ], 60);
        return;
    }

    $name = isset($_POST['bc_name']) ? sanitize_text_field(wp_unslash((string) $_POST['bc_name'])) : '';
    $email = isset($_POST['bc_email']) ? sanitize_email(wp_unslash((string) $_POST['bc_email'])) : '';
    $company = isset($_POST['bc_company']) ? sanitize_text_field(wp_unslash((string) $_POST['bc_company'])) : '';
    $topic = isset($_POST['bc_topic']) ? sanitize_key(wp_unslash((string) $_POST['bc_topic'])) : 'general';
    $order = isset($_POST['bc_order']) ? sanitize_text_field(wp_unslash((string) $_POST['bc_order'])) : '';
    $message = isset($_POST['bc_message']) ? sanitize_textarea_field(wp_unslash((string) $_POST['bc_message'])) : '';
    $budget = isset($_POST['bc_budget']) ? sanitize_text_field(wp_unslash((string) $_POST['bc_budget'])) : '';
    $dr_floor = isset($_POST['bc_dr_floor']) ? sanitize_text_field(wp_unslash((string) $_POST['bc_dr_floor'])) : '';
    $niches = isset($_POST['bc_niches']) ? sanitize_text_field(wp_unslash((string) $_POST['bc_niches'])) : '';
    $clients = isset($_POST['bc_clients']) ? sanitize_text_field(wp_unslash((string) $_POST['bc_clients'])) : '';

    $topics = backlinkcrypto_contact_topics();
    if (!isset($topics[$topic])) {
        $topic = 'general';
    }

    $errors = [];
    if ($name === '' || strlen($name) < 2) {
        $errors[] = __('Please enter your name.', 'backlinkcrypto');
    }
    if ($email === '' || !is_email($email)) {
        $errors[] = __('Please enter a valid email.', 'backlinkcrypto');
    }
    if ($message === '' || strlen($message) < 10) {
        $errors[] = __('Please write a message (at least a short paragraph).', 'backlinkcrypto');
    }

    if ($errors !== []) {
        set_transient('bc_contact_flash_' . backlinkcrypto_contact_flash_key(), [
            'type' => 'error',
            'msg'  => implode(' ', $errors),
        ], 60);
        return;
    }

    $subject = sprintf(
        /* translators: 1: topic label, 2: name */
        __('[%1$s] Message from %2$s', 'backlinkcrypto'),
        $topics[$topic],
        $name
    );

    $post_id = wp_insert_post([
        'post_type'    => BC_CONTACT_CPT,
        'post_status'  => 'publish',
        'post_title'   => $subject,
        'post_content' => $message,
    ], true);

    if (is_wp_error($post_id)) {
        set_transient('bc_contact_flash_' . backlinkcrypto_contact_flash_key(), [
            'type' => 'error',
            'msg'  => __('Could not save your message. Please email us directly.', 'backlinkcrypto'),
        ], 60);
        return;
    }

    update_post_meta($post_id, '_bc_contact_name', $name);
    update_post_meta($post_id, '_bc_contact_email', $email);
    update_post_meta($post_id, '_bc_contact_company', $company);
    update_post_meta($post_id, '_bc_contact_topic', $topic);
    update_post_meta($post_id, '_bc_contact_order', $order);
    update_post_meta($post_id, '_bc_contact_ip', $ip);
    update_post_meta($post_id, '_bc_contact_read', '0');
    update_post_meta($post_id, '_bc_contact_budget', $budget);
    update_post_meta($post_id, '_bc_contact_dr_floor', $dr_floor);
    update_post_meta($post_id, '_bc_contact_niches', $niches);
    update_post_meta($post_id, '_bc_contact_clients', $clients);

    set_transient($rate_key, '1', 60);

    $to = backlinkcrypto_contact_notify_email();
    $admin_body = "New contact form submission on Backlink Crypto\n\n"
        . "Name: {$name}\n"
        . "Email: {$email}\n"
        . "Company: " . ($company !== '' ? $company : '—') . "\n"
        . "Topic: {$topics[$topic]}\n"
        . "Order #: " . ($order !== '' ? $order : '—') . "\n"
        . "Budget: " . ($budget !== '' ? $budget : '—') . "\n"
        . "DR floor: " . ($dr_floor !== '' ? $dr_floor : '—') . "\n"
        . "Niches: " . ($niches !== '' ? $niches : '—') . "\n"
        . "Clients: " . ($clients !== '' ? $clients : '—') . "\n"
        . "IP: " . ($ip !== '' ? $ip : '—') . "\n\n"
        . "Message:\n{$message}\n\n"
        . "Manage in WP Admin → Contact Inbox\n"
        . admin_url('post.php?post=' . $post_id . '&action=edit') . "\n";

    $headers = [
        'Content-Type: text/plain; charset=UTF-8',
        'Reply-To: ' . $name . ' <' . $email . '>',
    ];

    wp_mail($to, '[Backlink Crypto] ' . $subject, $admin_body, $headers);

    // Optional confirmation to sender.
    $confirm = "Hi {$name},\n\n"
        . "We received your message and will reply within 1 business day.\n\n"
        . "Topic: {$topics[$topic]}\n"
        . "— Backlink Crypto\n"
        . home_url('/');
    wp_mail($email, __('We received your message — Backlink Crypto', 'backlinkcrypto'), $confirm, [
        'Content-Type: text/plain; charset=UTF-8',
        'From: Backlink Crypto <' . backlinkcrypto_public_support_email() . '>',
        'Reply-To: Backlink Crypto <' . backlinkcrypto_public_support_email() . '>',
    ]);

    set_transient('bc_contact_flash_' . backlinkcrypto_contact_flash_key(), [
        'type' => 'ok',
        'msg'  => __('Thanks — your message was sent. We’ll reply within 1 business day.', 'backlinkcrypto'),
    ], 60);

    wp_safe_redirect(add_query_arg('sent', '1', home_url('/contact/')));
    exit;
}

function backlinkcrypto_contact_flash_key(): string
{
    $ip = isset($_SERVER['REMOTE_ADDR']) ? sanitize_text_field(wp_unslash((string) $_SERVER['REMOTE_ADDR'])) : 'x';
    return md5($ip . '|' . (isset($_SERVER['HTTP_USER_AGENT']) ? (string) $_SERVER['HTTP_USER_AGENT'] : ''));
}

/**
 * @return array{type:string,msg:string}|null
 */
function backlinkcrypto_contact_get_flash(): ?array
{
    $key = 'bc_contact_flash_' . backlinkcrypto_contact_flash_key();
    $flash = get_transient($key);
    if (is_array($flash) && isset($flash['type'], $flash['msg'])) {
        delete_transient($key);
        return [
            'type' => (string) $flash['type'],
            'msg'  => (string) $flash['msg'],
        ];
    }
    if (isset($_GET['sent']) && (string) $_GET['sent'] === '1') {
        return [
            'type' => 'ok',
            'msg'  => __('Thanks — your message was sent. We’ll reply within 1 business day.', 'backlinkcrypto'),
        ];
    }
    return null;
}

add_action('template_redirect', 'backlinkcrypto_contact_handle_submit', 5);
