<?php
/**
 * Template Name: Catalog listing
 * Description: Edit hero slider images here. The public URL still uses the catalog clone.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$slug = (string) get_post_field('post_name', get_the_ID());
if (array_key_exists($slug, justccell_product_category_labels())) {
    set_query_var('justccell_listing', $slug);
    include JUSTCCELL_DIR . '/catalog-clone.php';
    return;
}

get_header();
echo '<main class="container"><p>';
esc_html_e('This catalog template is only for All-In-Ones, Cartridges, Pod Systems, and 510 Batteries pages.', 'justccell');
echo '</p></main>';
get_footer();
