<?php
/**
 * Product clone layout matching ccell.com Tank:
 * banner overlay, specs + 360 spin, sticky scroll features.
 *
 * Loads product data from the rewrite query var. Never use $page — WordPress
 * already binds that name to pagination.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$slug = (string) get_query_var('justccell_product');
$product = justccell_product_page($slug);
if (!is_array($product)) {
    return;
}

if (function_exists('set_time_limit')) {
    set_time_limit(180);
}

$labels   = justccell_product_category_labels();
$cat      = (string) ($product['category'] ?? 'all-in-ones');
$cat_name = $labels[$cat] ?? $cat;
$gallery  = is_array($product['gallery'] ?? null) ? $product['gallery'] : [];
$spin_keys = is_array($product['spin'] ?? null) ? $product['spin'] : [];
$features = is_array($product['features'] ?? null) ? $product['features'] : [];
$details  = is_array($product['details'] ?? null) ? $product['details'] : [];
$related  = array_values(array_filter(
    justccell_catalog_by_category()[$cat] ?? [],
    static fn (array $item): bool => $item['slug'] !== $slug
));
$name       = (string) ($product['name'] ?? '');
$banner_key = (string) ($product['banner'] ?? '');
$evomax_bg  = (string) ($product['evomax_bg'] ?? '');
$feature_count = count($features);

$ensure = array_values(array_filter(array_merge(
    [$banner_key, $evomax_bg],
    $gallery,
    $details,
    $spin_keys,
    array_map(
        static fn (array $feature): string => (string) ($feature['image'] ?? ''),
        $features
    )
)));
justccell_ensure_media_files($ensure);

$spin = array_values(array_filter(array_map('justccell_ensure_media_url', $spin_keys)));
?>
<article class="p-clone">
    <section class="p-banner">
        <div class="p-banner__img">
            <?php echo justccell_media_img($banner_key, [
                'alt'           => $name,
                'width'         => 1920,
                'height'        => 780,
                'fetchpriority' => 'high',
                'class'         => 'p-banner__photo',
            ]); ?>
        </div>
        <div class="p-banner__txt">
            <h1><?php echo esc_html($name); ?></h1>
            <p><?php echo esc_html((string) ($product['tagline'] ?? '')); ?></p>
        </div>
        <nav class="p-crumbs" aria-label="<?php esc_attr_e('Breadcrumb', 'justccell'); ?>">
            <a href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Home', 'justccell'); ?></a>
            <span aria-hidden="true">›</span>
            <a href="<?php echo esc_url(justccell_category_url($cat)); ?>"><?php echo esc_html($cat_name); ?></a>
            <span aria-hidden="true">›</span>
            <span><?php echo esc_html($name); ?></span>
        </nav>
    </section>

    <section class="p-dart">
        <div class="container p-dart__box">
            <div class="p-dart__copy">
                <h2><?php echo esc_html($name); ?></h2>
                <p class="p-dart__sub"><?php echo esc_html((string) ($product['subtitle'] ?? '')); ?></p>
                <i class="p-dart__rule" aria-hidden="true"></i>
                <ul class="p-specs">
                    <?php foreach ((array) ($product['specs'] ?? []) as $spec) : ?>
                        <li><?php echo esc_html((string) $spec); ?></li>
                    <?php endforeach; ?>
                </ul>
                <?php if ($gallery !== []) : ?>
                    <div class="p-thumbs" data-product-thumbs>
                        <?php foreach ($gallery as $i => $src_key) : ?>
                            <?php $thumb_url = justccell_ensure_media_url((string) $src_key); ?>
                            <button
                                class="p-thumbs__btn<?php echo $i === 0 ? ' is-on' : ''; ?>"
                                type="button"
                                data-thumb
                                data-view="<?php echo $i === 0 && $spin !== [] ? 'spin' : 'still'; ?>"
                                data-src="<?php echo esc_url($thumb_url); ?>"
                                aria-label="<?php echo esc_attr(sprintf(__('View image %d', 'justccell'), $i + 1)); ?>"
                            >
                                <?php echo justccell_media_img((string) $src_key, [
                                    'alt'    => '',
                                    'width'  => 88,
                                    'height' => 88,
                                ]); ?>
                            </button>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="p-dart__stage" data-product-stage>
                <?php if ($spin !== []) : ?>
                    <div
                        class="p-spin is-on"
                        data-spin
                        data-spin-frames="<?php echo esc_attr((string) wp_json_encode(array_values($spin))); ?>"
                    >
                        <img
                            class="p-spin__view"
                            data-spin-view
                            src="<?php echo esc_url((string) $spin[0]); ?>"
                            alt="<?php echo esc_attr($name); ?>"
                            width="1000"
                            height="1000"
                            fetchpriority="high"
                        >
                        <div class="p-spin__mask" data-spin-mask></div>
                    </div>
                <?php endif; ?>
                <div class="p-still<?php echo $spin === [] ? ' is-on' : ''; ?>" data-still>
                    <?php
                    $still_key = (string) ($gallery[0] ?? '');
                    echo justccell_media_img($still_key, [
                        'alt'    => $name,
                        'width'  => 720,
                        'height' => 720,
                        'class'  => 'p-still__img',
                    ]);
                    ?>
                </div>
            </div>
        </div>
    </section>

    <?php if ($features !== []) : ?>
        <section class="p-high" data-sticky-features style="height: <?php echo esc_attr((string) ($feature_count * 70)); ?>vh">
            <div class="p-high__pin" data-sticky-pin>
                <?php foreach ($features as $i => $feature) : ?>
                    <?php $feature_url = justccell_ensure_media_url((string) ($feature['image'] ?? '')); ?>
                    <div class="p-high__panel<?php echo $i === 0 ? ' is-on' : ''; ?>" data-feature-panel>
                        <div class="p-high__img"<?php echo $feature_url !== '' ? ' style="background-image: url(\'' . esc_url($feature_url) . '\')"' : ''; ?>></div>
                        <div class="p-high__txt">
                            <h2><?php echo esc_html((string) ($feature['title'] ?? '')); ?></h2>
                            <p><?php echo esc_html((string) ($feature['copy'] ?? '')); ?></p>
                            <?php if (($feature['note'] ?? '') !== '') : ?>
                                <p class="p-high__note"><?php echo esc_html((string) $feature['note']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="p-high__dots" role="tablist" aria-label="<?php esc_attr_e('Product highlights', 'justccell'); ?>">
                    <?php foreach ($features as $i => $feature) : ?>
                        <button class="<?php echo $i === 0 ? 'is-on' : ''; ?>" type="button" data-feature-dot aria-label="<?php echo esc_attr((string) ($feature['title'] ?? '')); ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php
    $evomax_url = justccell_ensure_media_url($evomax_bg);
    ?>
    <?php if ($evomax_url !== '' || (string) ($product['evomax_copy'] ?? '') !== '') : ?>
    <section class="p-evomax"<?php echo $evomax_url !== '' ? ' style="background-image: url(\'' . esc_url($evomax_url) . '\')"' : ''; ?>>
        <div class="p-evomax__box">
            <h2><?php echo esc_html((string) ($product['evomax_title'] ?? '')); ?></h2>
            <i class="p-dart__rule p-dart__rule--center" aria-hidden="true"></i>
            <p><?php echo nl2br(esc_html((string) ($product['evomax_copy'] ?? ''))); ?></p>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($details !== []) : ?>
        <section class="p-details">
            <div class="p-details__box">
                <div class="p-details__wide">
                    <?php echo justccell_media_img((string) $details[0], [
                        'alt'     => '',
                        'width'   => 1400,
                        'height'  => 460,
                        'loading' => 'lazy',
                    ]); ?>
                </div>
                <?php if (count($details) > 1) : ?>
                    <div class="p-details__pair">
                        <?php foreach (array_slice($details, 1) as $src_key) : ?>
                            <div class="p-details__cell">
                                <?php echo justccell_media_img((string) $src_key, [
                                    'alt'     => '',
                                    'width'   => 680,
                                    'height'  => 460,
                                    'loading' => 'lazy',
                                ]); ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($related !== []) : ?>
        <section class="p-explore">
            <div class="p-explore__head">
                <h2><?php esc_html_e('Explore More', 'justccell'); ?></h2>
                <p><?php esc_html_e('Products you may also like', 'justccell'); ?></p>
            </div>
            <div class="p-explore__rail" data-rail="related">
                <button class="p-explore__prev" type="button" data-rail-prev aria-label="<?php esc_attr_e('Previous', 'justccell'); ?>"></button>
                <div class="p-explore__scroller" data-rail-scroller>
                    <?php foreach ($related as $item) : ?>
                        <?php $meta = justccell_catalog_explore_meta($item); ?>
                        <a class="p-explore__card" href="<?php echo esc_url(justccell_item_url($item)); ?>">
                            <div class="p-explore__card-img">
                                <?php echo justccell_media_img((string) $item['image'], [
                                    'alt'     => (string) $item['name'],
                                    'width'   => 340,
                                    'height'  => 340,
                                    'loading' => 'lazy',
                                ]); ?>
                            </div>
                            <h3><?php echo esc_html($item['name']); ?></h3>
                            <?php if ($meta['blurb'] !== '') : ?>
                                <p><?php echo esc_html($meta['blurb']); ?></p>
                            <?php endif; ?>
                            <?php if ($meta['capacity'] !== '') : ?>
                                <span><?php echo esc_html($meta['capacity']); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <button class="p-explore__next" type="button" data-rail-next aria-label="<?php esc_attr_e('Next', 'justccell'); ?>"></button>
            </div>
        </section>
    <?php endif; ?>
</article>
