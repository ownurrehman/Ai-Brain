<?php
/**
 * Product card partial (text-only — no images).
 *
 * @package BacklinkCrypto
 *
 * @var array $args
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/** @var WC_Product|null $product */
$product = $args['product'] ?? null;
if (!$product instanceof WC_Product) {
    return;
}

$permalink = get_permalink($product->get_id());
$meta      = backlinkcrypto_product_metrics($product->get_id());
$domain    = $meta['domain'] !== '' ? $meta['domain'] : $product->get_name();
$dr        = $meta['dr'] !== '' && $meta['dr'] !== null ? (string) $meta['dr'] : '—';
$traffic   = backlinkcrypto_format_traffic($meta['traffic']);
$niche     = $meta['niche'] !== '' ? $meta['niche'] : 'Crypto';
?>
<article class="bc-card bc-card--text">
    <div class="bc-card__body">
        <h3 class="bc-card__title">
            <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($domain); ?></a>
        </h3>
        <p class="bc-card__meta">
            <?php
            printf(
                /* translators: 1: DR, 2: traffic, 3: niche */
                esc_html__('DR %1$s · Traffic %2$s · %3$s', 'backlinkcrypto'),
                esc_html($dr),
                esc_html($traffic),
                esc_html($niche)
            );
            ?>
        </p>
        <div class="bc-card__footer">
            <span class="bc-card__price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
            <?php if ($product->is_purchasable() && $product->is_in_stock()) : ?>
                <a
                    class="bc-btn bc-btn--small bc-btn--primary bc-add"
                    href="<?php echo esc_url($product->add_to_cart_url()); ?>"
                    data-quantity="1"
                    data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>"
                >
                    <?php esc_html_e('ADD', 'backlinkcrypto'); ?>
                </a>
            <?php else : ?>
                <a class="bc-btn bc-btn--small bc-btn--ghost" href="<?php echo esc_url($permalink); ?>">
                    <?php esc_html_e('View', 'backlinkcrypto'); ?>
                </a>
            <?php endif; ?>
        </div>
    </div>
</article>
