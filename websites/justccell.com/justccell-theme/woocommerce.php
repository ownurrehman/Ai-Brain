<?php
/**
 * WooCommerce looks for this filename first on every shop template.
 * Route products and catalog clones back to the storefront we built;
 * cart / checkout / account stay on the commerce shell.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (
    (function_exists('justccell_is_product_clone') && justccell_is_product_clone())
    || (function_exists('is_product') && is_product())
) {
    $slug = function_exists('justccell_current_product_slug') ? justccell_current_product_slug() : '';
    if ($slug !== '') {
        set_query_var('justccell_product', $slug);
    }
    require JUSTCCELL_DIR . '/product-clone.php';
    return;
}

if (
    (function_exists('justccell_is_catalog_clone') && justccell_is_catalog_clone())
    || (function_exists('is_product_taxonomy') && is_product_taxonomy())
) {
    $catalog = JUSTCCELL_DIR . '/catalog-clone.php';
    if (is_readable($catalog)) {
        require $catalog;
        return;
    }
}

require JUSTCCELL_DIR . '/commerce-shell.php';
