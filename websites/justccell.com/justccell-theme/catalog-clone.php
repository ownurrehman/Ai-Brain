<?php
/**
 * Category listing at /all-in-ones etc.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$cat = (string) get_query_var('justccell_listing');
if (!array_key_exists($cat, justccell_product_category_labels())) {
    wp_safe_redirect(home_url('/'));
    exit;
}

justccell_ensure_listing_pages();
$defaults = justccell_listing_defaults()[$cat] ?? null;
if (is_array($defaults)) {
    justccell_ensure_media_files([$defaults['desktop'], $defaults['mobile']]);
    justccell_seed_listing_hero_fields($cat);
}
justccell_ensure_media_files(array_column(justccell_catalog(), 'image'));

get_header();
get_template_part('template-parts/catalog/clone');
get_footer();
