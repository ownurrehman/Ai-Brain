<?php
/**
 * Order received — invoice-style confirmation (ccell.com commerce UX).
 *
 * @see https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 8.1.0
 *
 * Developed by Rank Ray — https://rankray.com
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

/**
 * @var WC_Order|false $order
 */
?>
<div class="jc-order-received woocommerce-order">
    <?php if ($order instanceof WC_Order) : ?>
        <?php if ($order->has_status('failed')) : ?>
            <header class="jc-order-received__hero jc-order-received__hero--failed">
                <p class="jc-order-received__kicker"><?php esc_html_e('Payment issue', 'justccell'); ?></p>
                <h1 class="jc-order-received__title"><?php esc_html_e('Order not completed', 'justccell'); ?></h1>
                <p class="jc-order-received__lede">
                    <?php esc_html_e('Unfortunately your order could not be processed. Please try again or use a different payment method.', 'justccell'); ?>
                </p>
            </header>
            <div class="jc-order-received__actions">
                <a class="btn btn--primary" href="<?php echo esc_url($order->get_checkout_payment_url()); ?>">
                    <?php esc_html_e('Pay again', 'justccell'); ?>
                </a>
            </div>
        <?php else : ?>
            <?php
            $received = apply_filters(
                'woocommerce_thankyou_order_received_text',
                esc_html__('Thank you. Your order has been received.', 'woocommerce'),
                $order
            );
            $meta_rows = function_exists('justccell_order_received_meta_rows')
                ? justccell_order_received_meta_rows($order)
                : [];
            ?>
            <header class="jc-order-received__hero">
                <div class="jc-order-received__badge" aria-hidden="true">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 6L9 17l-5-5" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/>
                    </svg>
                </div>
                <p class="jc-order-received__kicker"><?php esc_html_e('Order confirmed', 'justccell'); ?></p>
                <h1 class="jc-order-received__title"><?php esc_html_e('Order received', 'justccell'); ?></h1>
                <p class="jc-order-received__lede woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
                    <?php echo wp_kses_post($received); ?>
                </p>
            </header>

            <?php if ($meta_rows !== []) : ?>
                <dl class="jc-order-received__meta">
                    <?php foreach ($meta_rows as $row) : ?>
                        <div class="jc-order-received__meta-row">
                            <dt><?php echo esc_html($row['label']); ?></dt>
                            <dd><?php echo esc_html($row['value']); ?></dd>
                        </div>
                    <?php endforeach; ?>
                </dl>
            <?php endif; ?>

            <?php
            if (function_exists('justccell_elite_thankyou_card')) {
                justccell_elite_thankyou_card($order);
            }
            ?>

            <div class="jc-order-received__layout">
                <section class="jc-order-received__panel jc-order-received__panel--items" aria-labelledby="jc-order-items-heading">
                    <h2 id="jc-order-items-heading" class="jc-order-received__panel-title">
                        <?php esc_html_e('Order details', 'justccell'); ?>
                    </h2>

                    <div class="jc-order-table-wrap">
                        <table class="jc-order-table shop_table order_details">
                            <thead>
                                <tr>
                                    <th scope="col" class="jc-order-table__product"><?php esc_html_e('Product', 'justccell'); ?></th>
                                    <th scope="col" class="jc-order-table__total"><?php esc_html_e('Total', 'justccell'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach ($order->get_items() as $item_id => $item) :
                                    if (!$item instanceof WC_Order_Item_Product) {
                                        continue;
                                    }
                                    $product = $item->get_product();
                                    $meta_lines = function_exists('justccell_order_item_meta_lines')
                                        ? justccell_order_item_meta_lines($item)
                                        : [];
                                    ?>
                                    <tr class="jc-order-table__row">
                                        <td class="jc-order-table__product" data-title="<?php esc_attr_e('Product', 'justccell'); ?>">
                                            <div class="jc-order-item">
                                                <?php if ($product instanceof WC_Product) : ?>
                                                    <div class="jc-order-item__thumb">
                                                        <?php echo wp_kses_post($product->get_image('woocommerce_thumbnail')); ?>
                                                    </div>
                                                <?php endif; ?>
                                                <div class="jc-order-item__body">
                                                    <p class="jc-order-item__name">
                                                        <?php echo esc_html($item->get_name()); ?>
                                                        <span class="jc-order-item__qty">&times;&nbsp;<?php echo esc_html((string) $item->get_quantity()); ?></span>
                                                    </p>
                                                    <?php if ($meta_lines !== []) : ?>
                                                        <ul class="jc-order-item__meta">
                                                            <?php foreach ($meta_lines as $meta_line) : ?>
                                                                <li>
                                                                    <span class="jc-order-item__meta-label"><?php echo esc_html($meta_line['label']); ?>:</span>
                                                                    <?php
                                                                    $meta_value = (string) ($meta_line['value'] ?? '');
                                                                    $meta_key   = (string) ($meta_line['key'] ?? '');
                                                                    $is_artwork = $meta_key === __('Engraving artwork', 'justccell')
                                                                        || stripos($meta_key, 'engraving artwork') !== false;
                                                                    if ($is_artwork && preg_match('#^https?://#i', $meta_value) === 1) {
                                                                        echo wp_kses_post(
                                                                            sprintf(
                                                                                '<span class="jc-order-item__meta-art"><img src="%1$s" alt="%2$s" width="48" height="48" loading="lazy" class="jc-order-item__meta-art-img"></span>',
                                                                                esc_url($meta_value),
                                                                                esc_attr__('Engraving artwork', 'justccell')
                                                                            )
                                                                        );
                                                                    } else {
                                                                        echo esc_html($meta_value);
                                                                    }
                                                                    ?>
                                                                </li>
                                                            <?php endforeach; ?>
                                                        </ul>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="jc-order-table__total" data-title="<?php esc_attr_e('Total', 'justccell'); ?>">
                                            <?php echo wp_kses_post($order->get_formatted_line_subtotal($item)); ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <?php foreach ($order->get_order_item_totals() as $key => $total) : ?>
                                    <tr class="jc-order-table__foot jc-order-table__foot--<?php echo esc_attr(sanitize_html_class((string) $key)); ?>">
                                        <th scope="row"><?php echo esc_html((string) $total['label']); ?></th>
                                        <td><?php echo wp_kses_post((string) $total['value']); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tfoot>
                        </table>
                    </div>

                    <?php if ($order->get_customer_note() !== '') : ?>
                        <div class="jc-order-received__note">
                            <h3 class="jc-order-received__note-title"><?php esc_html_e('Order note', 'justccell'); ?></h3>
                            <p><?php echo wp_kses_post(nl2br(wc_clean($order->get_customer_note()))); ?></p>
                        </div>
                    <?php endif; ?>
                </section>

                <aside class="jc-order-received__panel jc-order-received__panel--addresses">
                    <?php if ($order->has_billing_address()) : ?>
                        <section class="jc-order-address" aria-labelledby="jc-billing-heading">
                            <h2 id="jc-billing-heading" class="jc-order-received__panel-title">
                                <?php esc_html_e('Billing address', 'justccell'); ?>
                            </h2>
                            <address class="jc-order-address__body">
                                <?php echo wp_kses_post($order->get_formatted_billing_address(esc_html__('N/A', 'woocommerce'))); ?>
                            </address>
                            <?php if ($order->get_billing_phone() !== '') : ?>
                                <p class="jc-order-address__phone">
                                    <span><?php esc_html_e('Phone', 'justccell'); ?>:</span>
                                    <?php echo esc_html($order->get_billing_phone()); ?>
                                </p>
                            <?php endif; ?>
                        </section>
                    <?php endif; ?>

                    <?php if ($order->needs_shipping_address() && $order->has_shipping_address()) : ?>
                        <section class="jc-order-address" aria-labelledby="jc-shipping-heading">
                            <h2 id="jc-shipping-heading" class="jc-order-received__panel-title">
                                <?php esc_html_e('Shipping address', 'justccell'); ?>
                            </h2>
                            <address class="jc-order-address__body">
                                <?php echo wp_kses_post($order->get_formatted_shipping_address(esc_html__('N/A', 'woocommerce'))); ?>
                            </address>
                        </section>
                    <?php endif; ?>
                </aside>
            </div>

            <footer class="jc-order-received__actions">
                <a class="btn btn--primary" href="<?php echo esc_url(function_exists('justccell_order_store_home_url') ? justccell_order_store_home_url() : home_url('/')); ?>">
                    <?php esc_html_e('Continue shopping', 'justccell'); ?>
                </a>
                <?php if (is_user_logged_in()) : ?>
                    <a class="btn btn--ghost" href="<?php echo esc_url(wc_get_account_endpoint_url('orders')); ?>">
                        <?php esc_html_e('View my orders', 'justccell'); ?>
                    </a>
                <?php endif; ?>
            </footer>
        <?php endif; ?>

        <?php do_action('woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id()); ?>
        <?php do_action('woocommerce_thankyou', $order->get_id()); ?>
    <?php else : ?>
        <header class="jc-order-received__hero">
            <p class="jc-order-received__kicker"><?php esc_html_e('Order confirmed', 'justccell'); ?></p>
            <h1 class="jc-order-received__title"><?php esc_html_e('Order received', 'justccell'); ?></h1>
            <p class="woocommerce-notice woocommerce-notice--success woocommerce-thankyou-order-received">
                <?php
                echo wp_kses_post(
                    apply_filters(
                        'woocommerce_thankyou_order_received_text',
                        esc_html__('Thank you. Your order has been received.', 'woocommerce'),
                        false
                    )
                );
                ?>
            </p>
        </header>
    <?php endif; ?>
</div>
