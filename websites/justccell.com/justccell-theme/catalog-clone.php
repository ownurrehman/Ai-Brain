<?php
/**
 * Category listing clone of ccell.com /all-in-ones etc.
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

justccell_ensure_media_files(array_column(justccell_catalog(), 'image'));

get_header();
get_template_part('template-parts/catalog/clone');
get_footer();
