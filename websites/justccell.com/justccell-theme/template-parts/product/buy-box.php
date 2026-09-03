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

$sku  = (string) ($args['sku'] ?? '');
$woo  = (int) ($args['woo_id'] ?? 0);
$name = (string) ($args['name'] ?? '');
$box  = justccell_product_buy_box($sku, $woo);
if (empty($box['enabled'])) {
    return;
}

$wc_product = ($woo > 0 && function_exists('wc_get_product')) ? wc_get_product($woo) : null;
if ($wc_product instanceof WC_Product) {
    $GLOBALS['product'] = $wc_product;
}

$tiers     = is_array($box['tiers'] ?? null) ? $box['tiers'] : [];
$var_tiers = is_array($box['variation_tiers'] ?? null) ? $box['variation_tiers'] : [];
$has_woo   = $wc_product instanceof WC_Product && $wc_product->is_purchasable();

$active_price = '';
foreach ($tiers as $tier) {
    $min = (int) ($tier['qty_min'] ?? 0);
    if ($min <= 1) {
        $active_price = (string) ($tier['price'] ?? '');
        break;
    }
}
if ($active_price === '' && $tiers !== []) {
    $active_price = (string) ($tiers[0]['price'] ?? '');
}

$inquiry     = function_exists('justccell_contact_page_url') ? justccell_contact_page_url() : home_url('/contact/');
$empty_tiers = function_exists('justccell_option_string')
    ? justccell_option_string('store_buy_empty_tiers', __('Select options to see pricing for this combination.', 'justccell'))
    : __('Select options to see pricing for this combination.', 'justccell');
$collection  = justccell_product_collection($woo);
$config     = wp_json_encode([
    'tiers'            => $tiers,
    'variation_tiers'  => $var_tiers,
    'tier_overrides'   => [],
    'attributes'       => [],
]);
unset($name);
?>
<div
    class="p-buy-wrap"
    data-buy-box
    data-product-id="<?php echo esc_attr((string) $woo); ?>"
    data-inquiry="<?php echo esc_url($inquiry); ?>"
    data-empty-tiers="<?php echo esc_attr($empty_tiers); ?>"
    data-currency="<?php echo esc_attr(function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'GBP'); ?>"
    data-buy-per-item="<?php echo esc_attr__('per item', 'justccell'); ?>"
    data-buy-total-label="<?php echo esc_attr__('Total', 'justccell'); ?>"
    data-buy-ex-vat="<?php echo esc_attr__('ex VAT', 'justccell'); ?>"
>
<?php if (function_exists('woocommerce_output_all_notices')) : ?>
    <div class="p-buy-notices"><?php woocommerce_output_all_notices(); ?></div>
<?php endif; ?>
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
                            <tr class="<?php echo $i === 0 ? 'is-on' : ''; ?>" data-qty-min="<?php echo esc_attr((string) ((int) ($tier['qty_min'] ?? 1))); ?>">
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
                    if ($wc_product->is_type('variable')) {
                        woocommerce_variable_add_to_cart();
                    } else {
                        woocommerce_simple_add_to_cart();
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
                    <?php
                    if (function_exists('justccell_laser_render_ui')) {
                        justccell_laser_render_ui($woo);
                    }
                    ?>
                <?php endif; ?>

                <div class="p-buy__quote" data-buy-quote aria-live="polite">
                    <p class="p-buy__quote-kicker"><?php esc_html_e('Your price', 'justccell'); ?></p>
                    <p class="p-buy__quote-unit" data-buy-unit><?php echo $active_price !== '' ? esc_html($active_price) : esc_html__('Price on request', 'justccell'); ?></p>
                    <p class="p-buy__quote-meta">
                        <span data-buy-unit-label><?php esc_html_e('per item', 'justccell'); ?></span>
                        <?php if ($tiers !== []) : ?>
                            <span aria-hidden="true" data-buy-band-sep> · </span>
                            <span data-buy-band><?php echo esc_html((string) ($tiers[0]['range'] ?? '')); ?></span>
                        <?php endif; ?>
                    </p>
                    <p class="p-buy__quote-line" data-buy-hardware-row hidden>
                        <span><?php esc_html_e('Hardware', 'justccell'); ?></span>
                        <strong data-buy-hardware></strong>
                    </p>
                    <p class="p-buy__quote-line" data-buy-laser-row hidden>
                        <span><?php esc_html_e('Engraving', 'justccell'); ?></span>
                        <strong data-buy-laser></strong>
                    </p>
                    <p class="p-buy__quote-total" data-buy-total-row<?php echo $active_price === '' ? ' hidden' : ''; ?>>
                        <span><?php esc_html_e('Total', 'justccell'); ?></span>
                        <strong data-buy-total><?php echo $active_price !== '' ? esc_html($active_price) : ''; ?></strong>
                        <span class="p-buy__quote-vat"><?php esc_html_e('ex VAT', 'justccell'); ?></span>
                    </p>
                </div>

                <?php if ($has_woo) : ?>
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
<div class="p-buy-sticky" data-buy-sticky hidden>
    <span data-buy-sticky-price aria-live="polite"><?php esc_html_e('Price on request', 'justccell'); ?></span>
    <?php if ($has_woo) : ?>
    <button type="button" class="p-buy__cta" data-buy-submit><?php echo esc_html((string) $box['cta_label']); ?></button>
    <?php else : ?>
    <a class="p-buy__cta" href="<?php echo esc_url($inquiry); ?>" data-buy-submit><?php echo esc_html((string) $box['cta_label']); ?></a>
    <?php endif; ?>
</div>
</div>
