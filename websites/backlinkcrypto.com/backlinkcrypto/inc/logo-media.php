<?php
/**
 * Sideload brand logo into WordPress Media Library + set Custom Logo / Site Icon.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BC_LOGO_MEDIA_OPTION', 'backlinkcrypto_logo_media_v1');

add_action('init', 'backlinkcrypto_logo_maybe_upload', 60);

function backlinkcrypto_logo_maybe_upload(): void
{
    $state = get_option(BC_LOGO_MEDIA_OPTION);
    if (is_array($state) && ($state['version'] ?? '') === '1.0.0' && !empty($state['attachment_id'])) {
        $id = (int) $state['attachment_id'];
        if ($id > 0 && get_post($id)) {
            return;
        }
    }

    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $files = [
        'logo'            => BACKLINKCRYPTO_DIR . '/assets/img/logo-512.png',
        'logo_icon'       => BACKLINKCRYPTO_DIR . '/assets/img/logo-180.png',
        'logo_horizontal' => BACKLINKCRYPTO_DIR . '/assets/img/logo-horizontal.png',
        'logo_svg'        => BACKLINKCRYPTO_DIR . '/assets/img/logo.svg',
    ];

    $ids = [];
    foreach ($files as $key => $path) {
        if (!is_readable($path)) {
            continue;
        }
        $id = backlinkcrypto_logo_sideload_file($path, backlinkcrypto_logo_title_for($key));
        if ($id > 0) {
            $ids[$key] = $id;
        }
    }

    if ($ids === []) {
        return;
    }

    $primary = (int) ($ids['logo'] ?? reset($ids));
    if ($primary > 0) {
        set_theme_mod('custom_logo', $primary);
        // Site icon (favicon / apple touch) — prefer square 180+.
        $icon = (int) ($ids['logo_icon'] ?? $primary);
        update_option('site_icon', $icon);
    }

    update_option(BC_LOGO_MEDIA_OPTION, [
        'version'       => '1.0.0',
        'uploaded_at'   => gmdate('c'),
        'attachment_id' => $primary,
        'ids'           => $ids,
    ]);
}

function backlinkcrypto_logo_title_for(string $key): string
{
    return match ($key) {
        'logo'            => 'Backlink Crypto Logo',
        'logo_icon'       => 'Backlink Crypto Icon',
        'logo_horizontal' => 'Backlink Crypto Logo Horizontal',
        'logo_svg'        => 'Backlink Crypto Logo SVG',
        default           => 'Backlink Crypto Brand Asset',
    };
}

function backlinkcrypto_logo_sideload_file(string $path, string $title): int
{
    $filename = basename($path);
    $bits = wp_upload_bits($filename, null, (string) file_get_contents($path));
    if (!empty($bits['error']) || empty($bits['file'])) {
        return 0;
    }

    $filetype = wp_check_filetype($filename, null);
    $attachment = [
        'post_mime_type' => $filetype['type'] ?: 'image/png',
        'post_title'     => $title,
        'post_content'   => '',
        'post_status'    => 'inherit',
        'post_excerpt'   => 'Official Backlink Crypto brand logo.',
    ];

    $attach_id = wp_insert_attachment($attachment, $bits['file']);
    if (is_wp_error($attach_id) || !$attach_id) {
        return 0;
    }

    $attach_id = (int) $attach_id;
    $meta = wp_generate_attachment_metadata($attach_id, $bits['file']);
    if (is_array($meta)) {
        wp_update_attachment_metadata($attach_id, $meta);
    }

    update_post_meta($attach_id, '_wp_attachment_image_alt', 'Backlink Crypto');
    update_post_meta($attach_id, '_bc_brand_asset', '1');

    return $attach_id;
}
