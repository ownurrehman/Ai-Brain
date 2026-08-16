<?php
/**
 * Packages page — curated backlink packs + comparison.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$defs = backlinkcrypto_package_defs();
$writing = backlinkcrypto_writing_addon_price();
?>

<section class="bc-packages" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head">
            <p class="bc-eyebrow"><?php esc_html_e('Packages', 'backlinkcrypto'); ?></p>
            <h1><?php esc_html_e('Crypto backlink packages', 'backlinkcrypto'); ?></h1>
            <p><?php esc_html_e('Skip picking every site — buy a curated pack. We match publishers from inventory after checkout. Optional article writing available in cart.', 'backlinkcrypto'); ?></p>
        </div>

        <div class="bc-packages__grid" id="bc-trial">
            <?php foreach ($defs as $pkg) :
                $id = wc_get_product_id_by_sku($pkg['sku']);
                $product = $id ? wc_get_product($id) : null;
                $price_html = $product ? $product->get_price_html() : wc_price($pkg['price']);
                $can = $product && $product->is_purchasable() && $product->is_in_stock();
                $is_trial = ($pkg['sku'] ?? '') === 'BC-PKG-TRIAL';
                ?>
                <article class="bc-packages__card<?php echo $is_trial ? ' bc-packages__card--trial' : ''; ?>">
                    <?php if ($is_trial) : ?>
                        <span class="bc-packages__badge"><?php esc_html_e('Best first buy', 'backlinkcrypto'); ?></span>
                    <?php endif; ?>
                    <h2><?php echo esc_html($pkg['name']); ?></h2>
                    <div class="bc-packages__price"><?php echo wp_kses_post($price_html); ?></div>
                    <p><?php echo esc_html($pkg['desc']); ?></p>
                    <ul>
                        <?php foreach ($pkg['bullets'] as $b) : ?>
                            <li><?php echo esc_html($b); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <?php if ($can) : ?>
                        <a class="bc-btn bc-btn--primary" href="<?php echo esc_url($product->add_to_cart_url()); ?>">
                            <?php echo $is_trial ? esc_html__('Start trial', 'backlinkcrypto') : esc_html__('Add pack to cart', 'backlinkcrypto'); ?>
                        </a>
                    <?php else : ?>
                        <a class="bc-btn bc-btn--ghost" href="<?php echo esc_url(home_url('/contact/')); ?>">
                            <?php esc_html_e('Ask about this pack', 'backlinkcrypto'); ?>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>

        <div class="bc-compare" id="bc-compare">
            <h2><?php esc_html_e('Compare packages', 'backlinkcrypto'); ?></h2>
            <div class="bc-compare__wrap">
                <table class="bc-compare__table">
                    <thead>
                        <tr>
                            <th><?php esc_html_e('Feature', 'backlinkcrypto'); ?></th>
                            <th><?php esc_html_e('Trial', 'backlinkcrypto'); ?></th>
                            <th><?php esc_html_e('Starter', 'backlinkcrypto'); ?></th>
                            <th><?php esc_html_e('Growth', 'backlinkcrypto'); ?></th>
                            <th><?php esc_html_e('Authority', 'backlinkcrypto'); ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th><?php esc_html_e('Placements', 'backlinkcrypto'); ?></th>
                            <td>1</td><td>5</td><td>10</td><td>5</td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('DR focus', 'backlinkcrypto'); ?></th>
                            <td>40–60</td><td>40+</td><td>50+</td><td>70+</td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Typical turnaround', 'backlinkcrypto'); ?></th>
                            <td><?php esc_html_e('3–7 days', 'backlinkcrypto'); ?></td>
                            <td><?php esc_html_e('1–2 weeks', 'backlinkcrypto'); ?></td>
                            <td><?php esc_html_e('2–3 weeks', 'backlinkcrypto'); ?></td>
                            <td><?php esc_html_e('Priority queue', 'backlinkcrypto'); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Writing add-on', 'backlinkcrypto'); ?></th>
                            <td colspan="4"><?php echo esc_html(sprintf(__('Optional +%s per placement in cart', 'backlinkcrypto'), wp_strip_all_tags(wc_price($writing)))); ?></td>
                        </tr>
                        <tr>
                            <th><?php esc_html_e('Best for', 'backlinkcrypto'); ?></th>
                            <td><?php esc_html_e('First test order', 'backlinkcrypto'); ?></td>
                            <td><?php esc_html_e('Small launches', 'backlinkcrypto'); ?></td>
                            <td><?php esc_html_e('Agency client books', 'backlinkcrypto'); ?></td>
                            <td><?php esc_html_e('Competitive keywords', 'backlinkcrypto'); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="bc-packages__note">
            <?php
            printf(
                esc_html__('Need a monthly retainer? Use the agency form on Contact (topic: Bulk). Prefer à la carte? Browse the marketplace. Article writing add-on starts at %s per placement in cart.', 'backlinkcrypto'),
                wp_strip_all_tags(wc_price($writing))
            );
            ?>
        </p>
        <div class="bc-services__cta">
            <a class="bc-btn bc-btn--ghost" href="<?php echo esc_url(backlinkcrypto_marketplace_url()); ?>"><?php esc_html_e('Browse marketplace', 'backlinkcrypto'); ?></a>
            <a class="bc-btn bc-btn--primary" href="<?php echo esc_url(home_url('/contact/?topic=bulk')); ?>"><?php esc_html_e('Request retainer quote', 'backlinkcrypto'); ?></a>
        </div>
    </div>
</section>

<?php
get_footer();
