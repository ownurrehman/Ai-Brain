<?php
/**
 * Product feature grid. Pulls Woo products when catalog exists.
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

$categories = [
    'all-in-ones' => __('All-In-Ones', 'justccell'),
    'cartridge'   => __('Cartridges', 'justccell'),
    'pod-system'  => __('Pod Systems', 'justccell'),
    'battery'     => __('510 Batteries', 'justccell'),
];
?>
<section class="product-grid">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html($heading); ?></h2>
        <ul class="product-grid__cats" role="list">
            <?php foreach ($categories as $slug => $label) : ?>
                <li>
                    <a class="product-grid__cat" href="<?php echo esc_url(justccell_category_url($slug)); ?>">
                        <?php echo esc_html($label); ?>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>

        <?php if (function_exists('wc_get_products')) : ?>
            <?php
            $products = wc_get_products([
                'status' => 'publish',
                'limit'  => 8,
            ]);
            ?>
            <?php if ($products !== []) : ?>
                <ul class="product-grid__list" role="list">
                    <?php foreach ($products as $product) : ?>
                        <?php
                        $permalink = $product->get_permalink();
                        $image_id  = $product->get_image_id();
                        ?>
                        <li class="product-card">
                            <a class="product-card__media" href="<?php echo esc_url($permalink); ?>">
                                <?php
                                echo $image_id
                                    ? wp_get_attachment_image((int) $image_id, 'justccell-card', false, ['loading' => 'lazy'])
                                    : '<span class="product-card__placeholder"></span>';
                                ?>
                            </a>
                            <h3 class="product-card__title">
                                <a href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($product->get_name()); ?></a>
                            </h3>
                            <p class="product-card__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags($product->get_short_description()), 16)); ?></p>
                            <a class="btn btn--primary product-card__cta" href="<?php echo esc_url(justccell_inquiry_url((string) $product->get_sku())); ?>">
                                <?php esc_html_e('Request sample & quote', 'justccell'); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else : ?>
                <p class="product-grid__empty"><?php esc_html_e('Catalog products will appear here after WooCommerce items are added.', 'justccell'); ?></p>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>
