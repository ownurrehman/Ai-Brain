<?php
/**
 * Category grid matching ccell.com listing groups.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$cat    = (string) get_query_var('justccell_listing');
$labels = justccell_product_category_labels();
$title  = $labels[$cat] ?? $cat;
$groups = justccell_catalog_groups($cat);
$arrow  = justccell_ensure_media_url('public_static_modules_cms_img_home14.png');
?>
<article class="c-clone">
    <header class="c-hero">
        <div class="container">
            <nav class="p-crumbs" aria-label="<?php esc_attr_e('Breadcrumb', 'justccell'); ?>">
                <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'justccell'); ?></a>
                <span aria-hidden="true">›</span>
                <span><?php echo esc_html($title); ?></span>
            </nav>
            <h1><?php echo esc_html($title); ?></h1>
        </div>
    </header>

    <?php foreach ($groups as $group) : ?>
        <section class="c-group">
            <div class="container">
                <?php if (($group['title'] ?? '') !== '') : ?>
                    <h2 class="c-group__title"><?php echo esc_html((string) $group['title']); ?></h2>
                <?php endif; ?>
                <div class="c-grid">
                    <?php foreach ($group['items'] as $item) : ?>
                        <a class="c-card" href="<?php echo esc_url(justccell_item_url($item)); ?>">
                            <div class="c-card__img">
                                <?php echo justccell_media_img((string) $item['image'], [
                                    'alt'     => (string) $item['name'],
                                    'width'   => 420,
                                    'height'  => 420,
                                    'loading' => 'lazy',
                                ]); ?>
                            </div>
                            <h3><?php echo esc_html((string) $item['name']); ?></h3>
                            <ul>
                                <?php foreach (array_slice((array) ($item['specs'] ?? []), 0, 3) as $spec) : ?>
                                    <li><?php echo esc_html((string) $spec); ?></li>
                                <?php endforeach; ?>
                            </ul>
                            <span class="h-more">
                                <?php esc_html_e('More', 'justccell'); ?>
                                <?php if ($arrow !== '') : ?>
                                    <img src="<?php echo esc_url($arrow); ?>" alt="" width="18" height="12">
                                <?php endif; ?>
                            </span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>
</article>
