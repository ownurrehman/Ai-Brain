<?php
/**
 * Single product — specs + inquiry, no cart chrome.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) {
    the_post();
    global $product;
    ?>
    <article <?php wc_product_class('product-hero container', $product); ?>>
        <div class="product-hero__gallery">
            <?php do_action('woocommerce_before_single_product_summary'); ?>
        </div>
        <div class="product-hero__summary">
            <?php do_action('woocommerce_single_product_summary'); ?>
        </div>
    </article>
    <section class="container product-tabs">
        <?php do_action('woocommerce_after_single_product_summary'); ?>
    </section>
    <?php
}

get_footer();
