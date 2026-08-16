<?php
/**
 * Single product — richer placement details.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

get_header();

while (have_posts()) {
    the_post();
    /** @var WC_Product|null $product */
    $product = wc_get_product(get_the_ID());
    if (!$product) {
        continue;
    }
    $meta   = backlinkcrypto_product_metrics($product->get_id());
    $domain = $meta['domain'] !== '' ? $meta['domain'] : $product->get_name();
    $da     = $meta['da'] !== '' && $meta['da'] !== null ? (string) (int) $meta['da'] : '—';
    $dr     = $meta['dr'] !== '' && $meta['dr'] !== null ? (string) (int) $meta['dr'] : '—';
    $can_buy = $product->is_purchasable() && $product->is_in_stock();
    $as_of  = $meta['as_of'] !== '' ? $meta['as_of'] : gmdate('Y-m-d');
    $writing = function_exists('backlinkcrypto_writing_addon_price') ? backlinkcrypto_writing_addon_price() : 149.0;
    ?>
    <main class="bc-main bc-product-page">
        <div class="bc-container bc-product bc-product--no-image">
            <p class="bc-breadcrumb">
                <a href="<?php echo esc_url(backlinkcrypto_marketplace_url()); ?>"><?php esc_html_e('← Marketplace', 'backlinkcrypto'); ?></a>
            </p>

            <div class="bc-product__layout">
                <div class="bc-product__main">
                    <header class="bc-product__head">
                        <p class="bc-eyebrow"><?php esc_html_e('Guest post placement', 'backlinkcrypto'); ?></p>
                        <h1><?php echo esc_html($domain); ?></h1>
                        <div class="bc-product__badges">
                            <?php if (!empty($meta['verified'])) : ?>
                                <span class="bc-pill"><?php esc_html_e('Verified', 'backlinkcrypto'); ?></span>
                            <?php endif; ?>
                            <?php if (!empty($meta['dofollow'])) : ?>
                                <span class="bc-pill"><?php esc_html_e('Dofollow', 'backlinkcrypto'); ?></span>
                            <?php else : ?>
                                <span class="bc-pill bc-pill--muted"><?php esc_html_e('Nofollow', 'backlinkcrypto'); ?></span>
                            <?php endif; ?>
                            <span class="bc-pill bc-pill--muted"><?php echo esc_html($meta['niche'] ?: 'Crypto'); ?></span>
                        </div>
                    </header>

                    <div class="bc-product__metrics" aria-label="<?php esc_attr_e('Site metrics', 'backlinkcrypto'); ?>">
                        <div>
                            <span>DA</span>
                            <strong><?php echo esc_html($da); ?></strong>
                        </div>
                        <div>
                            <span>DR</span>
                            <strong><?php echo esc_html($dr); ?></strong>
                        </div>
                        <div>
                            <span><?php esc_html_e('Traffic', 'backlinkcrypto'); ?></span>
                            <strong><?php echo esc_html(backlinkcrypto_format_traffic($meta['traffic'])); ?></strong>
                        </div>
                        <div>
                            <span><?php esc_html_e('Language', 'backlinkcrypto'); ?></span>
                            <div class="bc-product__lang"><?php backlinkcrypto_render_language_badges($meta['languages']); ?></div>
                        </div>
                    </div>
                    <p class="bc-product__asof">
                        <?php
                        printf(
                            esc_html__('Metrics as of %s — sourced from industry SEO tools and shown for comparison.', 'backlinkcrypto'),
                            esc_html($as_of)
                        );
                        ?>
                    </p>

                    <section class="bc-product__section">
                        <h2><?php esc_html_e('What you get', 'backlinkcrypto'); ?></h2>
                        <ul class="bc-product__points">
                            <li><?php esc_html_e('One sponsored guest post / placement on this publisher at the listed price.', 'backlinkcrypto'); ?></li>
                            <li><?php echo !empty($meta['dofollow'])
                                ? esc_html__('Link type: dofollow (as marked). Publisher policies can still apply to anchors and commercial content.', 'backlinkcrypto')
                                : esc_html__('Link type: nofollow (as marked on this listing).', 'backlinkcrypto'); ?></li>
                            <li><?php esc_html_e('Typical turnaround: 24–72 hours after we receive an approved article (higher-DR sites may take longer).', 'backlinkcrypto'); ?></li>
                            <li><?php esc_html_e('You receive the live URL in My Account → Placements when published.', 'backlinkcrypto'); ?></li>
                        </ul>
                    </section>

                    <section class="bc-product__section">
                        <h2><?php esc_html_e('Content guidelines', 'backlinkcrypto'); ?></h2>
                        <ul class="bc-product__points">
                            <li><?php esc_html_e('Original article, usually 800–1,500+ words unless the publisher specifies otherwise.', 'backlinkcrypto'); ?></li>
                            <li><?php esc_html_e('Include preferred title, target URL, and 1–2 natural anchor suggestions.', 'backlinkcrypto'); ?></li>
                            <li><?php esc_html_e('No thin spam, scraped content, or aggressive over-optimization — publishers can reject.', 'backlinkcrypto'); ?></li>
                            <li><?php esc_html_e('Upload after crypto payment is confirmed (file, Doc link, or paste).', 'backlinkcrypto'); ?></li>
                        </ul>
                    </section>

                    <section class="bc-product__section">
                        <h2><?php esc_html_e('Sample anchor ideas', 'backlinkcrypto'); ?></h2>
                        <p class="bc-product__hint"><?php esc_html_e('Natural examples — final anchors should match the article context:', 'backlinkcrypto'); ?></p>
                        <ul class="bc-product__anchors">
                            <li><code><?php echo esc_html($domain); ?></code></li>
                            <li><code>crypto exchange review</code></li>
                            <li><code>learn more</code></li>
                            <li><code>official site</code></li>
                        </ul>
                    </section>

                    <?php if ($product->get_description() !== '' && trim(wp_strip_all_tags($product->get_description())) !== $domain) : ?>
                        <div class="bc-product__desc">
                            <?php echo wp_kses_post(wpautop($product->get_description())); ?>
                        </div>
                    <?php endif; ?>
                </div>

                <aside class="bc-product__buy-card" aria-label="<?php esc_attr_e('Buy placement', 'backlinkcrypto'); ?>">
                    <div class="bc-product__buy-label"><?php esc_html_e('Price', 'backlinkcrypto'); ?></div>
                    <div class="bc-product__price"><?php echo wp_kses_post($product->get_price_html()); ?></div>

                    <?php if ($can_buy) : ?>
                        <form class="bc-product__cart cart" action="<?php echo esc_url(apply_filters('woocommerce_add_to_cart_form_action', $product->get_permalink())); ?>" method="post" enctype="multipart/form-data">
                            <div class="bc-product__qty">
                                <span class="bc-product__qty-label"><?php esc_html_e('Quantity', 'backlinkcrypto'); ?></span>
                                <div class="bc-qty" data-bc-qty>
                                    <button type="button" class="bc-qty__btn" data-bc-qty-minus aria-label="<?php esc_attr_e('Decrease quantity', 'backlinkcrypto'); ?>">−</button>
                                    <?php
                                    woocommerce_quantity_input([
                                        'min_value'   => 1,
                                        'max_value'   => $product->get_max_purchase_quantity() > 0 ? $product->get_max_purchase_quantity() : '',
                                        'input_value' => 1,
                                    ], $product);
                                    ?>
                                    <button type="button" class="bc-qty__btn" data-bc-qty-plus aria-label="<?php esc_attr_e('Increase quantity', 'backlinkcrypto'); ?>">+</button>
                                </div>
                            </div>

                            <button
                                type="submit"
                                name="add-to-cart"
                                value="<?php echo esc_attr((string) $product->get_id()); ?>"
                                class="bc-btn bc-btn--primary bc-product__atc single_add_to_cart_button button alt"
                            >
                                <?php esc_html_e('Add to cart', 'backlinkcrypto'); ?>
                            </button>
                        </form>
                        <p class="bc-product__buy-note">
                            <?php
                            printf(
                                /* translators: %s: price */
                                esc_html__('Optional at cart: professional article writing from %s per placement.', 'backlinkcrypto'),
                                wp_strip_all_tags(wc_price($writing))
                            );
                            ?>
                        </p>
                        <p class="bc-product__buy-note"><?php esc_html_e('Crypto checkout · upload your article after payment is confirmed', 'backlinkcrypto'); ?></p>
                        <p class="bc-product__buy-note">
                            <a href="<?php echo esc_url(home_url('/policies/')); ?>"><?php esc_html_e('Fulfillment & slot reallocation policy', 'backlinkcrypto'); ?></a>
                        </p>
                    <?php else : ?>
                        <p class="bc-product__unavailable"><?php esc_html_e('This placement is currently unavailable.', 'backlinkcrypto'); ?></p>
                        <a class="bc-btn bc-btn--ghost bc-product__atc" href="<?php echo esc_url(backlinkcrypto_marketplace_url()); ?>">
                            <?php esc_html_e('Browse marketplace', 'backlinkcrypto'); ?>
                        </a>
                    <?php endif; ?>
                </aside>
            </div>
        </div>
    </main>
    <?php
}

get_footer();
