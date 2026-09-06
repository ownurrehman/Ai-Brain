<?php
/**
 * Wholesale qty table + WooCommerce native add-to-cart (attributes / variation_id).
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 *
 * @var array{sku?:string,woo_id?:int,name?:string} $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('justccell_buy_box_context')) {
    return;
}

$ctx = justccell_buy_box_context($args);
if ($ctx === null) {
    return;
}

$box          = $ctx['box'];
$woo          = (int) $ctx['woo'];
$tiers        = $ctx['tiers'];
$wc_product   = $ctx['wc_product'];
$has_woo      = (bool) $ctx['has_woo'];
$active_price = (string) $ctx['active_price'];
$inquiry      = (string) $ctx['inquiry'];
$empty_tiers  = (string) $ctx['empty_tiers'];
$collection   = $ctx['collection'];
$config       = wp_json_encode([
    'tiers'           => $ctx['tiers'],
    'variation_tiers' => $ctx['var_tiers'],
    'tier_overrides'  => [],
    'attributes'      => [],
    'stock'           => $ctx['stock'],
    'variation_stock' => $ctx['var_stock'],
]);
?>
<div
    class="p-buy-wrap"
    data-buy-box
    data-product-id="<?php echo esc_attr((string) $woo); ?>"
    data-inquiry="<?php echo esc_url($inquiry); ?>"
    data-empty-tiers="<?php echo esc_attr($empty_tiers); ?>"
    data-currency="<?php echo esc_attr(function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'GBP'); ?>"
    data-buy-per-item="<?php echo esc_attr__('per item', 'justccell'); ?>"
    data-buy-unit-word="<?php echo esc_attr__('unit', 'justccell'); ?>"
    data-buy-tier-word="<?php echo esc_attr__('tier', 'justccell'); ?>"
    data-buy-total-label="<?php echo esc_attr__('Total', 'justccell'); ?>"
    data-buy-ex-vat="<?php echo esc_attr__('ex VAT', 'justccell'); ?>"
    data-buy-stock-available="<?php echo esc_attr__('%s in stock', 'justccell'); ?>"
    data-buy-stock-remaining="<?php echo esc_attr__('%s remaining', 'justccell'); ?>"
    data-buy-stock-over="<?php echo esc_attr__('Only %s available — reduce quantity to continue', 'justccell'); ?>"
    data-buy-stock-select="<?php echo esc_attr__('Select options to see stock availability', 'justccell'); ?>"
    data-buy-stock-out="<?php echo esc_attr__('Out of stock', 'justccell'); ?>"
>
<div class="p-buy">
    <div class="p-buy__box">
        <div class="p-buy__grid<?php echo $tiers === [] ? ' p-buy__grid--no-tiers' : ''; ?>">
            <?php if ($tiers !== []) : ?>
            <div class="p-buy__prices">
                <table class="p-buy__table" data-buy-table>
                    <thead>
                        <tr>
                            <th scope="col"><?php echo esc_html((string) $box['qty_label']); ?></th>
                            <th scope="col"><?php echo esc_html((string) $box['price_label']); ?></th>
                        </tr>
                    </thead>
                    <tbody data-buy-tiers>
                        <?php foreach ($tiers as $i => $tier) : ?>
                            <tr class="<?php echo $i === 0 ? 'active-tier' : ''; ?>" data-qty-min="<?php echo esc_attr((string) ((int) ($tier['qty_min'] ?? 1))); ?>">
                                <th scope="row"><?php echo esc_html((string) ($tier['range'] ?? '')); ?></th>
                                <td><?php echo esc_html((string) ($tier['price'] ?? '')); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
            <div class="p-buy__picks">
                <?php if ($has_woo) : ?>
                    <?php
                    if ($wc_product instanceof WC_Product) {
                        if ($wc_product->is_type('variable')) {
                            woocommerce_variable_add_to_cart();
                        } else {
                            woocommerce_simple_add_to_cart();
                        }
                    }
                    ?>
                    <label class="p-buy__field p-buy__field--qty">
                        <span><?php esc_html_e('Quantity', 'justccell'); ?></span>
                        <span class="p-buy__stepper">
                            <button type="button" data-buy-qty-down aria-label="<?php esc_attr_e('Decrease quantity', 'justccell'); ?>">−</button>
                            <input type="number" min="1" step="1" value="1" inputmode="numeric" data-buy-qty>
                            <button type="button" data-buy-qty-up aria-label="<?php esc_attr_e('Increase quantity', 'justccell'); ?>">+</button>
                        </span>
                    </label>
                    <p class="p-buy__stock" data-buy-stock hidden role="status" aria-live="polite"></p>
                    <?php
                    if (function_exists('justccell_laser_render_ui')) {
                        justccell_laser_render_ui($woo);
                    }
                    ?>
                <?php endif; ?>

                <div class="p-buy__quote" data-buy-quote aria-live="polite">
                    <div class="p-buy__pricing-hero" data-buy-total-row<?php echo $active_price === '' ? ' hidden' : ''; ?>>
                        <p class="p-buy__quote-total">
                            <strong class="p-buy__quote-total-amount" data-buy-total><?php echo $active_price !== '' ? esc_html($active_price) : esc_html__('Price on request', 'justccell'); ?></strong>
                            <span class="p-buy__quote-vat"><?php esc_html_e('ex VAT', 'justccell'); ?></span>
                        </p>
                        <p class="p-buy__quote-unit-line" data-buy-unit-row<?php echo $tiers === [] ? ' hidden' : ''; ?>>
                            <span data-buy-unit></span>
                        </p>
                    </div>
                    <p class="p-buy__quote-line" data-buy-hardware-row hidden>
                        <span><?php esc_html_e('Hardware', 'justccell'); ?></span>
                        <strong data-buy-hardware></strong>
                    </p>
                    <p class="p-buy__quote-line" data-buy-laser-row hidden>
                        <span><?php esc_html_e('Engraving', 'justccell'); ?></span>
                        <strong data-buy-laser></strong>
                    </p>
                </div>

                <?php if ($has_woo) : ?>
                    <p class="p-buy__laser-notice" data-buy-laser-notice hidden role="alert"></p>
                    <button type="button" class="p-buy__cta" data-buy-submit>
                        <?php echo esc_html((string) $box['cta_label']); ?>
                    </button>
                <?php else : ?>
                    <a class="p-buy__cta" href="<?php echo esc_url($inquiry); ?>" data-buy-submit>
                        <?php echo esc_html((string) $box['cta_label']); ?>
                    </a>
                <?php endif; ?>

                <?php if ((string) $box['note'] !== '') : ?>
                    <p class="p-buy__note"><?php echo esc_html((string) $box['note']); ?></p>
                <?php endif; ?>

                <?php if (!empty($collection['show'])) : ?>
                    <p class="p-buy__collect"><?php echo esc_html((string) $collection['copy']); ?></p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <script type="application/json" data-buy-config><?php echo $config !== false ? $config : '{}'; ?></script>
</div>
</div>
