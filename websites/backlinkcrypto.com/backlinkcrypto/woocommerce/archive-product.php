<?php
/**
 * WooCommerce archive / shop.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

get_header();
?>

<main class="bc-main">
    <div class="bc-container">
        <header class="bc-section-head">
            <?php if (apply_filters('woocommerce_show_page_title', true)) : ?>
                <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
            <?php endif; ?>
            <?php do_action('woocommerce_archive_description'); ?>
        </header>

        <?php
        $terms = get_terms([
            'taxonomy'   => 'product_cat',
            'hide_empty' => true,
            'exclude'    => [get_option('default_product_cat')],
        ]);
        if (!is_wp_error($terms) && !empty($terms)) :
            ?>
            <div class="bc-chips bc-chips--shop">
                <a class="bc-chip<?php echo is_shop() ? ' is-active' : ''; ?>" href="<?php echo esc_url(wc_get_page_permalink('shop')); ?>">
                    <?php esc_html_e('All', 'backlinkcrypto'); ?>
                </a>
                <?php foreach ($terms as $term) : ?>
                    <a class="bc-chip<?php echo is_product_category($term->slug) ? ' is-active' : ''; ?>" href="<?php echo esc_url(get_term_link($term)); ?>">
                        <?php echo esc_html($term->name); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>

        <?php if (woocommerce_product_loop()) : ?>
            <?php do_action('woocommerce_before_shop_loop'); ?>
            <div class="bc-grid">
                <?php
                while (have_posts()) {
                    the_post();
                    $product = wc_get_product(get_the_ID());
                    if ($product) {
                        get_template_part('template-parts/product', 'card', ['product' => $product]);
                    }
                }
                ?>
            </div>
            <?php do_action('woocommerce_after_shop_loop'); ?>
        <?php else : ?>
            <?php do_action('woocommerce_no_products_found'); ?>
        <?php endif; ?>
    </div>
</main>

<?php
get_footer();
