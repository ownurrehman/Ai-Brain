<?php
/**
 * Product feature grid — live Woo categories + catalog products.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$heading = function_exists('get_sub_field') ? (string) get_sub_field('heading') : '';
if ($heading === '') {
    $heading = __('Hardware catalog', 'justccell');
}

$categories = function_exists('justccell_storefront_category_labels')
    ? justccell_storefront_category_labels()
    : (function_exists('justccell_product_category_labels') ? justccell_product_category_labels() : []);

$cards = [];
if (function_exists('justccell_catalog')) {
    foreach (justccell_catalog() as $item) {
        if (!is_array($item)) {
            continue;
        }
        $cards[] = $item;
        if (count($cards) >= 8) {
            break;
        }
    }
}
?>
<section class="product-grid">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html($heading); ?></h2>
        <?php if ($categories !== []) : ?>
            <ul class="product-grid__cats" role="list">
                <?php foreach ($categories as $slug => $label) : ?>
                    <li>
                        <a class="product-grid__cat" href="<?php echo esc_url(justccell_category_url((string) $slug)); ?>">
                            <?php echo esc_html((string) $label); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <?php if ($cards !== []) : ?>
            <ul class="product-grid__list" role="list">
                <?php foreach ($cards as $item) : ?>
                    <?php
                    $permalink = justccell_item_url($item);
                    $name      = (string) ($item['name'] ?? '');
                    $slug      = (string) ($item['slug'] ?? '');
                    $specs     = function_exists('justccell_catalog_card_specs')
                        ? justccell_catalog_card_specs($item, 2)
                        : array_slice(array_values($item['specs'] ?? []), 0, 2);
                    $excerpt   = implode(' · ', array_map('strval', $specs));
                    ?>
                    <li class="product-card">
                        <a class="product-card__media" href="<?php echo esc_url($permalink); ?>">
                            <?php
                            justccell_echo_catalog_image($item, [
                                'alt'     => $name,
                                'loading' => 'lazy',
                            ]);
                            ?>
                        </a>
                        <h3 class="product-card__title">
                            <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($name); ?></a>
                        </h3>
                        <?php if ($excerpt !== '') : ?>
                            <p class="product-card__excerpt"><?php echo esc_html($excerpt); ?></p>
                        <?php endif; ?>
                        <a class="btn btn--primary product-card__cta" href="<?php echo esc_url($permalink); ?>">
                            <?php esc_html_e('View product', 'justccell'); ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p class="product-grid__empty"><?php esc_html_e('Catalog products will appear here after WooCommerce items are added.', 'justccell'); ?></p>
        <?php endif; ?>
    </div>
</section>
