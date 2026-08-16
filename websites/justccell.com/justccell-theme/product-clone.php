<?php
/**
 * Visual product clone (Tank first). Inquiry-first — no cart or prices.
 *
 * Do not pass product data as template arg `page`. WordPress extract()s the
 * pagination query var `page` into that name and empties the clone.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$slug    = (string) get_query_var('justccell_product');
$product = justccell_product_page($slug);
if (!is_array($product)) {
    wp_safe_redirect(home_url('/'));
    exit;
}

if (function_exists('set_time_limit')) {
    set_time_limit(180);
}

$media_keys = array_values(array_filter(array_merge(
    [
        (string) ($product['banner'] ?? ''),
        (string) ($product['evomax_bg'] ?? ''),
        'public_static_modules_cms_img_home14.png',
    ],
    is_array($product['gallery'] ?? null) ? $product['gallery'] : [],
    is_array($product['details'] ?? null) ? $product['details'] : [],
    is_array($product['spin'] ?? null) ? $product['spin'] : [],
    array_map(
        static fn (array $feature): string => (string) ($feature['image'] ?? ''),
        is_array($product['features'] ?? null) ? $product['features'] : []
    )
)));
justccell_ensure_media_files($media_keys);

get_header();
get_template_part('template-parts/product/clone');
get_footer();
