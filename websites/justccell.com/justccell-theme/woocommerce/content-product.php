<?php
/**
 * Catalog card.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

global $product;
if (!$product instanceof WC_Product) {
    return;
}
?>
<li <?php wc_product_class('product-card', $product); ?>>
    <a class="product-card__media" href="<?php echo esc_url($product->get_permalink()); ?>">
        <?php echo $product->get_image('justccell-card', ['loading' => 'lazy']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
    </a>
    <h2 class="product-card__title">
        <a href="<?php echo esc_url($product->get_permalink()); ?>"><?php echo esc_html($product->get_name()); ?></a>
    </h2>
    <p class="product-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($product->get_short_description()), 16)); ?></p>
    <?php do_action('woocommerce_after_shop_loop_item'); ?>
</li>
