<?php
/**
 * Marketplace helpers — catalog URL, page ensure, product query.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Canonical marketplace URL (/marketplace/).
 */
function backlinkcrypto_marketplace_url(): string
{
    $page = get_page_by_path('marketplace');
    if ($page instanceof WP_Post) {
        $url = get_permalink($page);
        if (is_string($url) && $url !== '') {
            return $url;
        }
    }

    return home_url('/marketplace/');
}

/**
 * @return WP_Query
 */
function backlinkcrypto_marketplace_query(?string $niche = null): WP_Query
{
    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 500,
        'orderby'        => 'meta_value_num',
        'meta_key'       => '_bc_dr',
        'order'          => 'DESC',
        'tax_query'      => [
            [
                'taxonomy' => 'product_cat',
                'field'    => 'slug',
                'terms'    => ['packages'],
                'operator' => 'NOT IN',
            ],
        ],
        'meta_query'     => [
            'relation' => 'AND',
            [
                'relation' => 'OR',
                [
                    'key'     => '_bc_is_package',
                    'compare' => 'NOT EXISTS',
                ],
                [
                    'key'     => '_bc_is_package',
                    'value'   => '1',
                    'compare' => '!=',
                ],
            ],
        ],
    ];

    if ($niche !== null && $niche !== '') {
        $args['meta_query'][] = [
            'key'   => '_bc_niche',
            'value' => $niche,
        ];
    }

    return new WP_Query($args);
}

/**
 * Ensure /marketplace/ page exists with SEO meta (idempotent per theme version).
 */
function backlinkcrypto_ensure_marketplace_page(): void
{
    $flag = 'bc_marketplace_page_' . BACKLINKCRYPTO_VERSION;
    if (get_option($flag) === '1') {
        return;
    }

    $existing = get_page_by_path('marketplace');
    $seo_title = 'Crypto Backlink Marketplace | Browse Verified Sites';
    $seo_desc  = 'Browse vetted crypto, DeFi, NFT & Web3 publishers. Filter by DR, DA, traffic, niche & language — then add placements to cart.';

    if ($existing instanceof WP_Post) {
        $id = (int) $existing->ID;
        wp_update_post([
            'ID'           => $id,
            'post_title'   => 'Marketplace',
            'post_status'  => 'publish',
            'post_content' => '',
        ]);
    } else {
        $id = wp_insert_post([
            'post_type'    => 'page',
            'post_name'    => 'marketplace',
            'post_title'   => 'Marketplace',
            'post_content' => '',
            'post_status'  => 'publish',
        ], true);
        if (is_wp_error($id)) {
            return;
        }
        $id = (int) $id;
    }

    update_post_meta($id, '_bc_seo_title', $seo_title);
    update_post_meta($id, '_bc_seo_description', $seo_desc);
    update_option($flag, '1');
}

add_action('init', 'backlinkcrypto_ensure_marketplace_page', 40);

/**
 * Ensure /policies/ page exists (idempotent per theme version).
 */
function backlinkcrypto_ensure_policies_page(): void
{
    $flag = 'bc_policies_page_' . BACKLINKCRYPTO_VERSION;
    if (get_option($flag) === '1') {
        return;
    }

    $existing = get_page_by_path('policies');
    $seo_title = 'Fulfillment & Slot Reallocation Policy | Backlink Crypto';
    $seo_desc  = 'No cash refunds. If a placement cannot be published, we reallocate an equal-value slot on another site. Turnaround, replacement window & indexation rules.';

    if ($existing instanceof WP_Post) {
        $id = (int) $existing->ID;
        wp_update_post([
            'ID'          => $id,
            'post_title'  => 'Fulfillment & Policies',
            'post_status' => 'publish',
        ]);
    } else {
        $id = wp_insert_post([
            'post_type'    => 'page',
            'post_name'    => 'policies',
            'post_title'   => 'Fulfillment & Policies',
            'post_content' => '',
            'post_status'  => 'publish',
        ], true);
        if (is_wp_error($id)) {
            return;
        }
        $id = (int) $id;
    }

    update_post_meta($id, '_bc_seo_title', $seo_title);
    update_post_meta($id, '_bc_seo_description', $seo_desc);
    // Force SEO meta refresh when policy wording changes with theme version.
    update_option($flag, '1');

    // Keep /terms/ aligned with no-cash-refund policy.
    $terms = get_page_by_path('terms');
    if ($terms instanceof WP_Post) {
        wp_update_post([
            'ID'           => (int) $terms->ID,
            'post_content' => "By purchasing on Backlink Crypto you agree that listings display estimated SEO metrics (DA/DR/traffic) for guidance only, and fulfillment begins after payment confirmation.\n\nAll sales are final. We do not issue cash refunds. If a placement cannot be published on the purchased site, we reallocate an equal-value slot on another site from inventory. See /policies/ for full fulfillment and reallocation rules.\n\nMetrics can change over time. Contact support for order issues.",
        ]);
        update_post_meta((int) $terms->ID, '_bc_seo_title', 'Terms of Service | Backlink Crypto');
        update_post_meta((int) $terms->ID, '_bc_seo_description', 'Terms for buying crypto backlinks on Backlink Crypto: no cash refunds, equal-value slot reallocation, metrics guidance, and fulfillment.');
    }
}

add_action('init', 'backlinkcrypto_ensure_policies_page', 41);
