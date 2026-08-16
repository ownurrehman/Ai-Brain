<?php
/**
 * WooCommerce product archive.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="container archive-products">
    <header class="page-article__header">
        <h1 class="page-article__title"><?php woocommerce_page_title(); ?></h1>
        <?php do_action('woocommerce_archive_description'); ?>
    </header>
    <?php if (woocommerce_product_loop()) : ?>
        <ul class="product-grid__list" role="list">
            <?php
            while (have_posts()) {
                the_post();
                wc_get_template_part('content', 'product');
            }
            ?>
        </ul>
        <?php woocommerce_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('No hardware in this category yet.', 'justccell'); ?></p>
    <?php endif; ?>
</section>
<?php
get_footer();
