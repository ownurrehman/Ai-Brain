<?php
/**
 * Product card grid for one storefront category.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$category = (string) ($args['category'] ?? '');
if ($category === '') {
    return;
}

$labels = justccell_product_category_labels();
$label  = $labels[$category] ?? $category;
$groups = justccell_catalog_groups($category);

foreach ($groups as $group) : ?>
    <section class="c-group">
        <div class="container">
            <?php if (($group['title'] ?? '') !== '') : ?>
                <div class="c-group__head">
                    <h2><?php echo esc_html((string) $group['title']); ?></h2>
                    <i></i>
                    <?php if (($group['copy'] ?? '') !== '') : ?>
                        <p><?php echo esc_html((string) $group['copy']); ?></p>
                    <?php endif; ?>
                </div>
            <?php elseif (count($groups) > 1) : ?>
                <div class="c-group__head">
                    <h2><?php echo esc_html($label); ?></h2>
                    <i></i>
                </div>
            <?php endif; ?>
            <div class="c-grid">
                <?php foreach ($group['items'] as $item) : ?>
                    <?php $meta = justccell_catalog_card_meta($item); ?>
                    <a class="c-card" href="<?php echo esc_url(justccell_item_url($item)); ?>">
                        <div class="c-card__img">
                            <?php
                            if ((int) ($meta['image_id'] ?? 0) > 0) {
                                echo wp_get_attachment_image((int) $meta['image_id'], 'full', false, [
                                    'alt'     => (string) $item['name'],
                                    'width'   => 420,
                                    'height'  => 420,
                                    'loading' => 'lazy',
                                ]);
                            } else {
                                echo justccell_media_img($meta['image'], [
                                    'alt'     => (string) $item['name'],
                                    'width'   => 420,
                                    'height'  => 420,
                                    'loading' => 'lazy',
                                ]);
                            }
                            ?>
                        </div>
                        <div class="c-card__copy">
                            <h3><?php echo esc_html((string) $item['name']); ?></h3>
                            <?php if ($meta['tagline'] !== '') : ?>
                                <p><?php echo esc_html($meta['tagline']); ?></p>
                            <?php endif; ?>
                            <?php if ($meta['capacity'] !== '') : ?>
                                <span><?php echo esc_html($meta['capacity']); ?></span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
<?php endforeach; ?>
