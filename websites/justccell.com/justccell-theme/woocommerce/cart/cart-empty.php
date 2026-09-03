<?php
/**
 * Empty cart.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 *
 * Developed by Rank Ray — https://rankray.com
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

do_action('woocommerce_cart_is_empty');

$shop = function_exists('wc_get_page_permalink') ? (string) wc_get_page_permalink('shop') : home_url('/');
if ($shop === '' || $shop === '0') {
    $shop = home_url('/all-in-ones/');
}

$suggested = [];
if (function_exists('wc_get_products')) {
    $suggested = wc_get_products([
        'status'   => 'publish',
        'limit'    => 4,
        'orderby'  => 'date',
        'order'    => 'DESC',
        'return'   => 'objects',
        'category' => [],
    ]);
}
?>
<div class="jc-cart-empty">
    <p class="jc-cart-empty__kicker"><?php echo esc_html(justccell_cart_label()); ?></p>
    <h1 class="jc-cart-empty__title"><?php esc_html_e('Your cart is empty', 'justccell'); ?></h1>
    <p class="jc-cart-empty__copy">
        <?php esc_html_e('Add hardware to your cart to build your order.', 'justccell'); ?>
    </p>
    <p class="return-to-shop jc-cart-empty__actions">
        <a class="button wc-backward btn btn--primary" href="<?php echo esc_url(apply_filters('woocommerce_return_to_shop_redirect', $shop)); ?>">
            <?php echo esc_html(apply_filters('woocommerce_return_to_shop_text', __('Continue shopping', 'justccell'))); ?>
        </a>
        <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/all-in-ones/')); ?>"><?php esc_html_e('All-In-Ones', 'justccell'); ?></a>
        <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/cartridge/')); ?>"><?php esc_html_e('Cartridges', 'justccell'); ?></a>
    </p>
</div>

<?php if ($suggested !== []) : ?>
    <section class="jc-cart-suggest" aria-labelledby="jc-cart-suggest-heading">
        <h2 id="jc-cart-suggest-heading" class="jc-cart-suggest__title"><?php esc_html_e('New in store', 'justccell'); ?></h2>
        <ul class="jc-cart-suggest__grid" role="list">
            <?php foreach ($suggested as $product) : ?>
                <?php
                if (!$product instanceof WC_Product) {
                    continue;
                }
                $url = $product->get_permalink();
                ?>
                <li class="jc-cart-suggest__card">
                    <a class="jc-cart-suggest__media" href="<?php echo esc_url($url); ?>">
                        <?php echo $product->get_image('woocommerce_thumbnail', ['loading' => 'lazy']); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
                    </a>
                    <h3 class="jc-cart-suggest__name">
                        <a href="<?php echo esc_url($url); ?>"><?php echo esc_html($product->get_name()); ?></a>
                    </h3>
                    <p class="jc-cart-suggest__price"><?php echo wp_kses_post($product->get_price_html()); ?></p>
                    <a class="btn btn--primary" href="<?php echo esc_url($url); ?>"><?php esc_html_e('View product', 'justccell'); ?></a>
                </li>
            <?php endforeach; ?>
        </ul>
    </section>
<?php endif; ?>
