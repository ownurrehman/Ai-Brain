<?php
/**
 * Plugin Name: JC Price Diagnostic (temporary, read-only)
 * Description: Detects products whose _justccell_tiered_pricing meta matches the £3.60 default_kit_tiers signature (i.e. seeded defaults masking real variation prices). Writes wp-content/uploads/jc-price-diag-sig.json. READ-ONLY. Delete after use.
 * Version: 0.0.3
 * Author: Rank Ray
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    if (get_option('jc_price_diag_v3') === '1') {
        return;
    }
    if (!function_exists('wc_get_product')) {
        return;
    }

    // The £3.60 default_kit_tiers signature (unit prices, in band order).
    $default_sig = [3.6, 3.48, 3.36, 3.24, 3.12];

    $q = new WP_Query([
        'post_type'        => 'product',
        'post_status'      => 'publish',
        'posts_per_page'   => -1,
        'fields'           => 'ids',
        'no_found_rows'    => true,
        'suppress_filters' => true,
    ]);

    $seeded = [];   // products whose tier meta == default signature (regression victims)
    $custom = [];   // products with genuine custom tiers
    $summary = ['total' => 0, 'seeded_default_3_60' => 0, 'custom_tiers' => 0];

    foreach ($q->posts as $pid) {
        $pid = (int) $pid;
        $p   = wc_get_product($pid);
        if (!$p instanceof WC_Product) {
            continue;
        }
        $summary['total']++;

        $meta = get_post_meta($pid, '_justccell_tiered_pricing', true);
        $units = [];
        if (is_array($meta)) {
            foreach ($meta as $row) {
                if (is_array($row) && isset($row['price'])) {
                    $units[] = round((float) $row['price'], 2);
                }
            }
        }

        // Real variation catalog prices (the true intended prices).
        $vprices = [];
        foreach ($p->get_children() as $cid) {
            $c = wc_get_product((int) $cid);
            if ($c instanceof WC_Product) {
                $vp = $c->get_price('edit');
                if ($vp !== '' && (float) $vp > 0) {
                    $vprices[] = round((float) $vp, 2);
                }
            }
        }

        $is_seeded = ($units === $default_sig);

        $record = [
            'id'   => $pid,
            'name' => $p->get_name(),
            'tier_units' => $units,
            'variation_prices' => $vprices,
        ];

        if ($is_seeded) {
            $summary['seeded_default_3_60']++;
            $seeded[] = $record;
        } else {
            $summary['custom_tiers']++;
            $custom[] = $record;
        }
    }

    $out = [
        'generated'   => gmdate('c'),
        'default_signature' => $default_sig,
        'summary'     => $summary,
        'seeded_default_products' => $seeded,
        'custom_tier_products'    => $custom,
    ];

    $uploads = wp_upload_dir();
    $file    = trailingslashit($uploads['basedir']) . 'jc-price-diag-sig.json';
    file_put_contents($file, wp_json_encode($out, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

    update_option('jc_price_diag_v3', '1', false);
}, 999);
