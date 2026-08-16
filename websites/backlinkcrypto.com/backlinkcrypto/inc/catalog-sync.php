<?php
/**
 * Catalog sync — rename typos, niches, metrics date (one-shot per version).
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', 'backlinkcrypto_catalog_sync_maybe', 45);

function backlinkcrypto_catalog_sync_maybe(): void
{
    if (!class_exists('WooCommerce')) {
        return;
    }
    $flag = 'bc_catalog_sync_' . BACKLINKCRYPTO_VERSION;
    if (get_option($flag) === '1') {
        return;
    }
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    $path = BACKLINKCRYPTO_DIR . '/inc/inventory.json';
    if (!is_readable($path)) {
        return;
    }
    $rows = json_decode((string) file_get_contents($path), true);
    if (!is_array($rows)) {
        return;
    }

    $as_of = gmdate('Y-m-d');
    $renames = [
        'Crpto Wisser'     => 'Crypto Wisser',
        'The Crpto Basic'  => 'The Crypto Basic',
        'Nft Newtoday'     => 'NFT News Today',
    ];

    foreach ($rows as $row) {
        $sku = (string) ($row['sku'] ?? '');
        if ($sku === '') {
            continue;
        }
        $id = wc_get_product_id_by_sku($sku);
        if (!$id) {
            continue;
        }
        $product = wc_get_product($id);
        if (!$product) {
            continue;
        }

        $domain = (string) ($row['domain'] ?? '');
        $name = (string) ($row['name'] ?? '');
        if (isset($renames[$name])) {
            $name = $renames[$name];
        }
        $title = $domain !== '' ? $domain : $name;
        if ($title !== '') {
            $product->set_name($title);
        }
        $niche = (string) ($row['niche'] ?? 'Crypto');
        update_post_meta($id, '_bc_niche', $niche);
        update_post_meta($id, '_bc_metrics_as_of', $as_of);
        if ($domain !== '') {
            update_post_meta($id, '_bc_domain', $domain);
        }
        $product->save();
    }

    // Fix live products by old title if SKU path missed.
    global $wpdb;
    foreach ($renames as $old => $new) {
        $ids = $wpdb->get_col($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'product' AND post_title = %s LIMIT 5",
            $old
        ));
        foreach ($ids as $pid) {
            wp_update_post(['ID' => (int) $pid, 'post_title' => $new]);
            update_post_meta((int) $pid, '_bc_metrics_as_of', $as_of);
        }
    }

    update_option($flag, '1');
}
