<?php
/**
 * Cart, checkout, account, and shop archive shell.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

get_header();

$shop_class = 'jc-shop';
if (function_exists('justccell_is_order_received_page') && justccell_is_order_received_page()) {
    $shop_class .= ' jc-shop--order-received';
} elseif (function_exists('is_cart') && is_cart()) {
    $shop_class .= ' jc-shop--cart';
} elseif (function_exists('is_account_page') && is_account_page()) {
    $shop_class .= ' jc-shop--account';
} elseif (function_exists('is_checkout') && is_checkout()) {
    $shop_class .= ' jc-shop--checkout';
} elseif (function_exists('is_shop') && (is_shop() || is_product_taxonomy())) {
    $shop_class .= ' jc-shop--archive';
}
?>
<div class="<?php echo esc_attr($shop_class); ?>">
    <div class="jc-shop__inner container">
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--page'); ?>
        <?php
        if (function_exists('is_cart') && is_cart()) {
            echo do_shortcode('[woocommerce_cart]');
        } elseif (function_exists('is_checkout') && is_checkout() && !(function_exists('justccell_is_order_received_page') && justccell_is_order_received_page())) {
            echo do_shortcode('[woocommerce_checkout]');
        } elseif (function_exists('is_account_page') && is_account_page()) {
            echo do_shortcode('[woocommerce_my_account]');
        } elseif (
            (function_exists('is_checkout') && is_checkout())
        ) {
            while (have_posts()) {
                the_post();
                the_content();
            }
        } else {
            woocommerce_content();
        }
        ?>
    </div>
</div>
<?php
get_footer();
