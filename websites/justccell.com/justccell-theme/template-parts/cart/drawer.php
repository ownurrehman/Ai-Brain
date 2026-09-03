<?php
/**
 * Slide-out cart drawer (AJAX-populated).
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH') || !class_exists('WooCommerce')) {
    return;
}

$cart_url = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');
$zero     = function_exists('justccell_format_money_html')
    ? justccell_format_money_html(0)
    : (function_exists('wc_price') ? wp_kses_post(wc_price(0)) : '£0.00');
?>
<div class="jc-cart" data-cart-drawer aria-hidden="true">
    <div class="jc-cart__backdrop" data-cart-backdrop></div>
    <aside class="jc-cart__panel" data-cart-panel tabindex="-1" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e('Your cart', 'justccell'); ?>">
        <header class="jc-cart__head">
            <h2><?php esc_html_e('Your cart', 'justccell'); ?></h2>
            <div class="jc-cart__head-actions">
                <button type="button" class="jc-cart__icon-btn" data-cart-close aria-label="<?php esc_attr_e('Close cart', 'justccell'); ?>">×</button>
            </div>
        </header>
        <div class="jc-cart__body" data-cart-items>
            <p class="jc-cart__empty"><?php esc_html_e('Your cart is empty.', 'justccell'); ?></p>
        </div>
        <footer class="jc-cart__foot">
            <div class="jc-cart__subtotal">
                <span><?php esc_html_e('Subtotal', 'justccell'); ?></span>
                <span data-cart-subtotal><?php echo $zero; ?></span>
            </div>
            <div class="jc-cart__actions">
                <a class="jc-cart__btn jc-cart__btn--primary" href="<?php echo esc_url($cart_url); ?>" data-cart-view><?php esc_html_e('View cart', 'justccell'); ?></a>
                <button type="button" class="jc-cart__btn jc-cart__btn--ghost" data-cart-close><?php esc_html_e('Continue shopping', 'justccell'); ?></button>
            </div>
        </footer>
    </aside>
    <p class="jc-cart__toast" data-cart-toast hidden role="status" aria-live="polite"></p>
</div>
