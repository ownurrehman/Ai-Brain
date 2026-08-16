<?php
/**
 * Public placement gallery — opted-in live URLs + catalog publisher samples.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return list<array{publisher:string,niche:string,dr:string,url:string,label:string,source:string}>
 */
function backlinkcrypto_gallery_entries(int $limit = 12): array
{
    $out = [];

    $q = new WP_Query([
        'post_type'      => 'bc_placement',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'meta_query'     => [
            'relation' => 'AND',
            [
                'key'   => '_bc_gallery_public',
                'value' => '1',
            ],
            [
                'key'     => '_bc_live_url',
                'value'   => 'http',
                'compare' => 'LIKE',
            ],
        ],
        'orderby'        => 'date',
        'order'          => 'DESC',
    ]);

    foreach ($q->posts as $post) {
        $id = (int) $post->ID;
        $d = function_exists('backlinkcrypto_placement_data') ? backlinkcrypto_placement_data($id) : [];
        $url = (string) ($d['live_url'] ?? get_post_meta($id, '_bc_live_url', true));
        $domain = (string) ($d['domain'] ?? get_post_meta($id, '_bc_domain', true));
        if ($url === '' || $domain === '') {
            continue;
        }
        $product_id = (int) get_post_meta($id, '_bc_product_id', true);
        $niche = 'Crypto';
        $dr = '';
        if ($product_id > 0 && function_exists('backlinkcrypto_product_metrics')) {
            $m = backlinkcrypto_product_metrics($product_id);
            $niche = (string) ($m['niche'] ?: $niche);
            $dr = $m['dr'] !== '' && $m['dr'] !== null ? (string) (int) $m['dr'] : '';
        }
        $out[] = [
            'publisher' => $domain,
            'niche'     => $niche,
            'dr'        => $dr,
            'url'       => $url,
            'label'     => __('Live placement', 'backlinkcrypto'),
            'source'    => 'live',
        ];
    }
    wp_reset_postdata();

    if (count($out) >= $limit) {
        return array_slice($out, 0, $limit);
    }

    // Honest fallback: featured catalog publishers (site home), not fake article URLs.
    if (function_exists('wc_get_products')) {
        $products = wc_get_products([
            'status'   => 'publish',
            'limit'    => 8,
            'featured' => true,
            'orderby'  => 'meta_value_num',
            'meta_key' => '_bc_dr',
            'order'    => 'DESC',
        ]);
        foreach ($products as $product) {
            if (count($out) >= $limit) {
                break;
            }
            if (get_post_meta($product->get_id(), '_bc_is_package', true) === '1') {
                continue;
            }
            $m = backlinkcrypto_product_metrics($product->get_id());
            $domain = $m['domain'] !== '' ? $m['domain'] : $product->get_name();
            if ($domain === '') {
                continue;
            }
            $host = preg_replace('#^https?://#', '', $domain);
            $host = rtrim((string) $host, '/');
            $out[] = [
                'publisher' => $domain,
                'niche'     => (string) ($m['niche'] ?: 'Crypto'),
                'dr'        => $m['dr'] !== '' && $m['dr'] !== null ? (string) (int) $m['dr'] : '',
                'url'       => 'https://' . $host,
                'label'     => __('Publisher in catalog', 'backlinkcrypto'),
                'source'    => 'catalog',
            ];
        }
    }

    return array_slice($out, 0, $limit);
}

/**
 * Whether gallery has at least one opted-in live URL.
 */
function backlinkcrypto_gallery_has_live(): bool
{
    foreach (backlinkcrypto_gallery_entries(3) as $row) {
        if (($row['source'] ?? '') === 'live') {
            return true;
        }
    }

    return false;
}
