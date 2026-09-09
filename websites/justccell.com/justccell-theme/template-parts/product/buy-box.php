<?php
/**
 * Wholesale qty table + WooCommerce native add-to-cart (attributes / variation_id).
 *
 * Slot API (clone.php shop-grid): open | tiers | purchase | close.
 * Omit slot (or `full`) to render the complete box in one pass.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 *
 * @var array{sku?:string,woo_id?:int,name?:string,slot?:string} $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

if (!function_exists('justccell_buy_box_context')) {
    return;
}

$slot = (string) ($args['slot'] ?? 'full');
$ctx  = justccell_buy_box_context($args);
if ($ctx === null) {
    return;
}

$box          = $ctx['box'];
$woo          = (int) $ctx['woo'];
$tiers        = $ctx['tiers'];
$wc_product   = $ctx['wc_product'];
$has_woo      = (bool) $ctx['has_woo'];
$is_variable  = !empty($ctx['is_variable']);
$show_tiers   = $tiers !== [] || $is_variable;
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

$render_open = static function () use ($woo, $inquiry, $empty_tiers): void {
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
    data-buy-was-label="<?php echo esc_attr__('Was', 'justccell'); ?>"
    data-buy-now-label="<?php echo esc_attr__('Now', 'justccell'); ?>"
    data-buy-select-options="<?php echo esc_attr__('Please choose product options before adding to cart.', 'justccell'); ?>"
>
<div class="p-buy">
    <?php
};

$render_tiers = static function () use ($show_tiers, $box, $tiers): void {
    if (!$show_tiers) {
        return;
    }
    ?>
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
                                <td><?php echo wp_kses(justccell_tier_price_cell_html($tier), justccell_sale_price_allowed_html()); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
    <?php
};

$render_purchase = static function () use ($has_woo, $wc_product, $woo, $active_price, $tiers, $box, $inquiry, $collection): void {
    ?>
            <div class="p-buy__picks p-buy__purchase">
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

                <div class="p-buy__quote" data-buy-quote aria-live="polite" aria-atomic="true">
                    <div class="p-buy__pricing-hero" data-buy-total-row<?php echo $active_price === '' ? ' hidden' : ''; ?>>
                        <p class="p-buy__quote-total">
                            <strong class="p-buy__quote-total-amount" data-buy-total><?php
                            if ($active_price !== '') {
                                $hero_tier = null;
                                foreach ($tiers as $tier) {
                                    if ((int) ($tier['qty_min'] ?? 0) <= 1) {
                                        $hero_tier = $tier;
                                        break;
                                    }
                                }
                                if ($hero_tier === null && $tiers !== []) {
                                    $hero_tier = $tiers[0];
                                }
                                echo wp_kses(
                                    $hero_tier ? justccell_tier_price_cell_html($hero_tier) : esc_html($active_price),
                                    justccell_sale_price_allowed_html()
                                );
                            }
                            ?></strong>
                            <span class="p-buy__quote-vat"><?php esc_html_e('ex VAT', 'justccell'); ?></span>
                        </p>
                        <p class="p-buy__quote-unit-line" data-buy-unit-row hidden>
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
    <?php
};

$render_close = static function () use ($config): void {
    ?>
    <script type="application/json" data-buy-config><?php echo $config !== false ? $config : '{}'; ?></script>
</div>
</div>
    <?php
};

if ($slot === 'open') {
    $render_open();
    return;
}
if ($slot === 'tiers') {
    $render_tiers();
    return;
}
if ($slot === 'purchase') {
    $render_purchase();
    return;
}
if ($slot === 'close') {
    $render_close();
    return;
}

$render_open();
echo '<div class="p-buy__box"><div class="p-buy__grid' . ($show_tiers ? '' : ' p-buy__grid--no-tiers') . '">';
$render_tiers();
$render_purchase();
echo '</div></div>';
$render_close();
