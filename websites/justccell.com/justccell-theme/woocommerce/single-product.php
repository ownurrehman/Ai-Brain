<?php
/**
 * WooCommerce single product — same storefront as /{category}/{slug}/.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$slug = justccell_current_product_slug();
if ($slug === '' && get_the_ID()) {
    $slug = (string) get_post_field('post_name', get_the_ID());
}
if ($slug !== '') {
    set_query_var('justccell_product', $slug);
}
require JUSTCCELL_DIR . '/product-clone.php';
