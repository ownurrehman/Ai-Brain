<?php
/**
 * Seed WooCommerce from inventory.json (runs once per version).
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BC_THEME_SEED_OPTION', 'backlinkcrypto_seed_v4');

add_action('init', 'backlinkcrypto_theme_maybe_seed', 40);

function backlinkcrypto_theme_maybe_seed(): void
{
    if (!class_exists('WooCommerce')) {
        return;
    }
    if (get_option(BC_THEME_SEED_OPTION)) {
        return;
    }
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    backlinkcrypto_theme_seed_run();
}

add_action('admin_init', static function (): void {
    if (!current_user_can('manage_woocommerce')) {
        return;
    }
    if (!isset($_GET['bc_theme_seed']) || !wp_verify_nonce((string) ($_GET['_wpnonce'] ?? ''), 'bc_theme_seed')) {
        return;
    }
    delete_option(BC_THEME_SEED_OPTION);
    backlinkcrypto_theme_seed_run();
    wp_safe_redirect(admin_url('edit.php?post_type=product&bc_seeded=1'));
    exit;
});

function backlinkcrypto_theme_seed_run(): void
{
    if (get_option(BC_THEME_SEED_OPTION)) {
        return;
    }

    $path = BACKLINKCRYPTO_DIR . '/inc/inventory.json';
    if (!is_readable($path)) {
        return;
    }

    $raw = file_get_contents($path);
    $rows = json_decode((string) $raw, true);
    if (!is_array($rows) || $rows === []) {
        return;
    }

    $keep_skus = [];

    update_option('blogname', 'Backlink Crypto');
    update_option('blogdescription', 'Crypto backlinks marketplace');
    update_option('woocommerce_currency', 'USD');
    update_option('woocommerce_price_thousand_sep', ',');
    update_option('woocommerce_price_decimal_sep', '.');
    update_option('woocommerce_price_num_decimals', '0');
    update_option('woocommerce_enable_guest_checkout', 'yes');
    update_option('show_on_front', 'posts');

    $cat_map = [];
    foreach (['crypto' => 'Crypto', 'general' => 'General', 'news' => 'News', 'defi' => 'DeFi', 'nft' => 'NFT'] as $slug => $label) {
        $existing = get_term_by('slug', $slug, 'product_cat');
        if ($existing && !is_wp_error($existing)) {
            $cat_map[$slug] = (int) $existing->term_id;
            continue;
        }
        $result = wp_insert_term($label, 'product_cat', ['slug' => $slug]);
        if (!is_wp_error($result)) {
            $cat_map[$slug] = (int) $result['term_id'];
        }
    }

    foreach ($rows as $row) {
        $sku = (string) ($row['sku'] ?? '');
        if ($sku === '') {
            continue;
        }
        $keep_skus[$sku] = true;

        $existing_id = wc_get_product_id_by_sku($sku);
        $product = $existing_id ? wc_get_product($existing_id) : new WC_Product_Simple();
        if (!$product) {
            $product = new WC_Product_Simple();
        }

        $domain = (string) ($row['domain'] ?? '');
        $name = $domain !== '' ? $domain : (string) ($row['name'] ?? $sku);
        $price = (string) round((float) ($row['price'] ?? 0));

        $product->set_name($name);
        $product->set_sku($sku);
        $product->set_regular_price($price);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');
        // Table marketplace — never attach product images.
        $product->set_image_id(0);
        $product->set_gallery_image_ids([]);

        $dr = $row['dr'] ?? null;
        $da = $row['da'] ?? null;
        $traffic = $row['traffic'] ?? null;
        $short = sprintf(
            'DA %s · DR %s · Traffic %s',
            $da !== null ? (string) $da : '—',
            $dr !== null ? (string) $dr : '—',
            $traffic !== null ? number_format((int) $traffic) : '—'
        );
        $product->set_short_description($short);

        $desc = $name;
        if (!empty($row['extras'])) {
            $desc .= "\n\n" . $row['extras'];
        }
        $product->set_description($desc);

        $niche = strtolower((string) ($row['niche'] ?? 'crypto'));
        $cat_slug = 'crypto';
        if (str_contains($niche, 'nft')) {
            $cat_slug = 'nft';
        } elseif (str_contains($niche, 'defi')) {
            $cat_slug = 'defi';
        } elseif ($niche === 'general' || $niche === 'news') {
            $cat_slug = $niche === 'news' ? 'news' : 'general';
        }
        if (!empty($cat_map[$cat_slug])) {
            $product->set_category_ids([$cat_map[$cat_slug]]);
        }

        $id = $product->save();
        if (!$id) {
            continue;
        }

        update_post_meta($id, '_bc_domain', $domain);
        update_post_meta($id, '_bc_da', $da !== null ? (string) (int) $da : '');
        update_post_meta($id, '_bc_dr', $dr !== null ? (string) (int) $dr : '');
        update_post_meta($id, '_bc_pa', isset($row['pa']) && $row['pa'] !== null ? (string) (int) $row['pa'] : '');
        update_post_meta($id, '_bc_traffic', $traffic !== null ? (string) (int) $traffic : '');
        $langs = $row['languages'] ?? $row['language'] ?? ['EN'];
        if (is_string($langs)) {
            $langs = preg_split('/[\s,|\/]+/', $langs) ?: ['EN'];
        }
        $langs = array_values(array_unique(array_map(static function ($c) {
            return strtoupper(substr(trim((string) $c), 0, 2));
        }, (array) $langs)));
        $langs = array_values(array_filter($langs));
        if ($langs === []) {
            $langs = ['EN'];
        }
        update_post_meta($id, '_bc_languages', implode(',', $langs));
        update_post_meta($id, '_bc_language', $langs[0]); // legacy
        update_post_meta($id, '_bc_niche', (string) ($row['niche'] ?? 'Crypto'));
        update_post_meta($id, '_bc_verified', !empty($row['verified']) ? '1' : '0');
        update_post_meta($id, '_bc_dofollow', !empty($row['dofollow']) ? '1' : '0');
        update_post_meta($id, '_bc_extras', (string) ($row['extras'] ?? ''));
        update_post_meta($id, '_bc_country', (string) ($row['country'] ?? ''));
    }

    // Draft products not in the inventory seed.
    $legacy = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 500,
        'fields'         => 'ids',
    ]);
    foreach ($legacy as $pid) {
        $sku = (string) get_post_meta((int) $pid, '_sku', true);
        if ($sku !== '' && isset($keep_skus[$sku])) {
            continue;
        }
        $product = wc_get_product((int) $pid);
        if ($product) {
            $product->set_status('draft');
            $product->save();
        }
    }

    $gateways = (array) get_option('woocommerce_bacs_settings', []);
    $gateways['enabled'] = 'yes';
    $gateways['title'] = 'Crypto';
    $gateways['description'] = 'Pay with USDT or other crypto. Order stays on hold until we confirm payment.';
    $gateways['instructions'] = "Pay with crypto, then reply to your order email with the tx hash.\n\nAdd wallet addresses in WP Admin → Backlink Crypto → Theme Settings.";
    update_option('woocommerce_bacs_settings', $gateways);
    update_option('woocommerce_bacs_accounts', []);

    $shop_id = (int) get_option('woocommerce_shop_page_id');
    if ($shop_id) {
        wp_update_post(['ID' => $shop_id, 'post_title' => 'Marketplace']);
    }

    update_option(BC_THEME_SEED_OPTION, [
        'version'   => '4.0.0',
        'count'     => count($rows),
        'seeded_at' => gmdate('c'),
    ]);

    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients();
    }
    flush_rewrite_rules(false);
}
