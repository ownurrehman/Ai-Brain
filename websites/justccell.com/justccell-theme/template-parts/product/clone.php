<?php
/**
 * Product clone layout — Woo/ACF or PHP fallback.
 *
 * Developed by Rank Ray — https://rankray.com
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

$from_cms = !empty($product['from_cms']);
$labels   = justccell_product_category_labels();
$cat      = (string) ($product['category'] ?? 'all-in-ones');
$cat_name = $labels[$cat] ?? $cat;
$name     = (string) ($product['name'] ?? '');
$product_heading = trim((string) ($product['product_heading'] ?? ''));
if ($product_heading === '') {
    $product_heading = $name;
}
$product_tagline = trim((string) ($product['subtitle'] ?? ''));
$short_description = function_exists('justccell_product_short_description_html')
    ? justccell_product_short_description_html((string) ($product['short_description'] ?? ''))
    : '';
$specs_heading   = trim((string) ($product['specs_heading'] ?? ''));
$specs_list      = array_values(array_filter(array_map('strval', (array) ($product['specs'] ?? []))));
if ($specs_heading === '' && $specs_list !== []) {
    $specs_heading = __('Specifications', 'justccell');
}
$banner_id  = (int) ($product['banner_id'] ?? 0);
$banner_key = (string) ($product['banner'] ?? '');
$banner_mobile_id  = (int) ($product['banner_mobile_id'] ?? 0);
$banner_mobile_key = (string) ($product['banner_mobile'] ?? '');
$evomax_bg_id  = (int) ($product['evomax_bg_id'] ?? 0);
$evomax_bg_key = (string) ($product['evomax_bg'] ?? '');

$gallery_ids = is_array($product['gallery_ids'] ?? null) ? $product['gallery_ids'] : [];
$gallery_keys = is_array($product['gallery'] ?? null) ? $product['gallery'] : [];
$spin_ids = is_array($product['spin_ids'] ?? null) ? $product['spin_ids'] : [];
$spin_keys = is_array($product['spin'] ?? null) ? $product['spin'] : [];
$details_ids = is_array($product['details_ids'] ?? null) ? $product['details_ids'] : [];
$details_keys = is_array($product['details'] ?? null) ? $product['details'] : [];
$features = is_array($product['features'] ?? null) ? $product['features'] : [];

$ensure = array_values(array_filter(array_merge(
    [$banner_key, $banner_mobile_key, $evomax_bg_key],
    $gallery_keys,
    $details_keys,
    $spin_keys,
    array_map(
        static fn (array $feature): string => (string) ($feature['image'] ?? ''),
        $features
    )
)));
if ($ensure !== []) {
    justccell_ensure_media_files($ensure);
}

$spin_urls = [];
if ($spin_ids !== []) {
    foreach ($spin_ids as $sid) {
        $u = justccell_product_media_url((int) $sid, '');
        if ($u !== '') {
            $spin_urls[] = $u;
        }
    }
} else {
    $spin_urls = array_values(array_filter(array_map('justccell_ensure_media_url', $spin_keys)));
}

$related = array_values(array_filter(
    justccell_catalog_by_category()[$cat] ?? [],
    static fn (array $item): bool => $item['slug'] !== $slug
));

$usable_pair = static function (int $id, string $key): bool {
    return justccell_product_media_url($id, $key) !== '';
};
$kept_gallery_ids  = [];
$kept_gallery_keys = [];
$gallery_n = max(count($gallery_ids), count($gallery_keys));
for ($i = 0; $i < $gallery_n; $i++) {
    $gid  = (int) ($gallery_ids[$i] ?? 0);
    $gkey = (string) ($gallery_keys[$i] ?? '');
    if ($usable_pair($gid, $gkey)) {
        $kept_gallery_ids[]  = $gid;
        $kept_gallery_keys[] = $gkey;
    }
}
$gallery_ids  = $kept_gallery_ids;
$gallery_keys = $kept_gallery_keys;

$kept_details_ids  = [];
$kept_details_keys = [];
$details_n = max(count($details_ids), count($details_keys));
for ($i = 0; $i < $details_n; $i++) {
    $did  = (int) ($details_ids[$i] ?? 0);
    $dkey = (string) ($details_keys[$i] ?? '');
    if ($usable_pair($did, $dkey)) {
        $kept_details_ids[]  = $did;
        $kept_details_keys[] = $dkey;
    }
}
$details_ids  = $kept_details_ids;
$details_keys = $kept_details_keys;

$gallery_count = $gallery_ids !== [] ? count($gallery_ids) : count($gallery_keys);
$details_count = $details_ids !== [] ? count($details_ids) : count($details_keys);
$default_image_id  = (int) ($gallery_ids[0] ?? 0);
$default_image_url = justccell_product_media_url($default_image_id, (string) ($gallery_keys[0] ?? ''));
$has_stage_media   = $gallery_count > 0 || $spin_urls !== [];

$features = array_values(array_filter(
    $features,
    static function (array $feature): bool {
        $id  = (int) ($feature['image_id'] ?? 0);
        $key = (string) ($feature['image'] ?? '');
        return justccell_product_media_url($id, $key) !== '';
    }
));
$feature_count = count($features);
$evomax_copy   = trim((string) ($product['evomax_copy'] ?? ''));
$show_evomax   = $evomax_copy !== '' && ($evomax_bg_id > 0 || $evomax_bg_key !== '');
$banner_empty  = justccell_product_media_url($banner_id, $banner_key) === '';
$has_mobile_banner = $banner_mobile_id > 0
    ? $banner_mobile_id !== $banner_id
    : ($banner_mobile_key !== '' && $banner_mobile_key !== $banner_key && justccell_product_media_url(0, $banner_mobile_key) !== '');
?>
<article class="p-clone product">
    <section class="jc-hero-banner jc-hero-banner--bleed p-banner<?php echo $banner_empty ? ' is-empty' : ''; ?><?php echo $has_mobile_banner ? ' jc-hero-banner--split p-banner--split' : ''; ?>">
        <div class="jc-hero-banner__media p-banner__img">
            <?php justccell_echo_product_media($banner_id, $banner_key, [
                'alt'           => $name,
                'width'         => 1920,
                'height'        => 780,
                'fetchpriority' => 'high',
                'class'         => 'jc-hero-banner__desk p-banner__photo p-banner__desk',
            ]); ?>
            <?php if ($has_mobile_banner) : ?>
                <?php justccell_echo_product_media($banner_mobile_id, $banner_mobile_key, [
                    'alt'           => $name,
                    'width'         => 750,
                    'height'        => 1334,
                    'fetchpriority' => 'high',
                    'class'         => 'jc-hero-banner__mobile p-banner__photo p-banner__mobile',
                ]); ?>
            <?php endif; ?>
        </div>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero p-crumbs'); ?>
    </section>

    <?php
    if (function_exists('justccell_render_product_page_notices')) {
        justccell_render_product_page_notices();
    }
    ?>

    <?php
    $buy_args = [
        'sku'    => $slug,
        'woo_id' => (int) ($product['woo_id'] ?? 0),
        'name'   => $name,
    ];
    ?>
    <section class="p-dart" aria-label="<?php echo esc_attr(sprintf(__('Product details for %s', 'justccell'), $name)); ?>">
        <div class="container">
            <?php
            get_template_part('template-parts/product/buy-box', null, $buy_args + ['slot' => 'open']);
            ?>
            <div class="p-dart__shop-grid<?php echo $has_stage_media ? '' : ' p-dart__shop-grid--no-stage'; ?>">
                <div class="p-dart__shop-left">
                    <div class="p-dart__copy">
                        <h1><?php echo esc_html($product_heading); ?></h1>
                        <?php if ($product_tagline !== '') : ?>
                            <h2 class="p-dart__sub"><?php echo esc_html($product_tagline); ?></h2>
                        <?php endif; ?>
                        <?php if ($short_description !== '') : ?>
                            <div class="p-dart__intro">
                                <?php echo $short_description; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- wp_kses_post in helper ?>
                            </div>
                        <?php endif; ?>
                        <i class="p-dart__rule" aria-hidden="true"></i>
                        <?php if ($specs_list !== []) : ?>
                            <?php if ($specs_heading !== '') : ?>
                                <h3 class="p-specs__title"><?php echo esc_html($specs_heading); ?></h3>
                            <?php endif; ?>
                            <ul class="p-specs">
                                <?php foreach ($specs_list as $spec) : ?>
                                    <li><?php echo esc_html((string) $spec); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php endif; ?>
                    </div>
                    <?php
                    get_template_part('template-parts/product/buy-box', null, $buy_args + ['slot' => 'tiers']);
                    ?>
                </div>
                <div class="p-dart__shop-right">
                    <?php if ($has_stage_media) : ?>
                    <div
                        class="p-dart__stage"
                        data-product-stage
                        data-has-spin="<?php echo $spin_urls !== [] ? '1' : '0'; ?>"
                        data-default-image-id="<?php echo esc_attr((string) $default_image_id); ?>"
                        data-default-image-url="<?php echo esc_url($default_image_url); ?>"
                    >
                        <?php if ($spin_urls !== []) : ?>
                            <div class="p-spin is-on" data-spin>
                                <div class="p-spin__frames">
                                    <?php foreach (array_values($spin_urls) as $i => $spin_url) : ?>
                                        <img
                                            class="p-spin__view<?php echo $i === 0 ? ' is-on' : ''; ?>"
                                            src="<?php echo esc_url($spin_url); ?>"
                                            alt="<?php echo $i === 0 ? esc_attr($name) : ''; ?>"
                                            width="1000"
                                            height="1000"
                                            draggable="false"
                                            decoding="async"
                                            <?php echo $i === 0 ? 'fetchpriority="high"' : ''; ?>
                                        >
                                    <?php endforeach; ?>
                                    <div class="p-spin__hint" aria-hidden="true">
                                        <svg class="p-spin__orbit" viewBox="0 0 240 48" focusable="false">
                                            <ellipse cx="120" cy="24" rx="110" ry="16" />
                                        </svg>
                                        <span class="p-spin__badge">
                                            <span class="p-spin__deg">360°</span>
                                            <svg class="p-spin__arrows" viewBox="0 0 72 18" focusable="false">
                                                <path d="M10 12c14-10 38-10 52 0" />
                                                <path d="M8 12 3 8.2M8 12l4.2-4.6" />
                                                <path d="M64 12l5-3.8M64 12l-4.2-4.6" />
                                            </svg>
                                        </span>
                                    </div>
                                </div>
                                <div class="p-spin__mask" data-spin-mask aria-hidden="true"></div>
                            </div>
                        <?php endif; ?>
                        <div class="p-still<?php echo $spin_urls === [] ? ' is-on' : ''; ?>" data-still>
                            <div class="p-stage-viewport">
                                <?php
                                justccell_echo_product_media(
                                    (int) ($gallery_ids[0] ?? 0),
                                    (string) ($gallery_keys[0] ?? ''),
                                    ['alt' => $name, 'width' => 720, 'height' => 720, 'class' => 'p-still__img wp-post-image p-stage-slide p-stage-slide--current']
                                );
                                ?>
                                <img
                                    class="p-still__img p-stage-slide p-stage-slide--incoming"
                                    data-stage-incoming
                                    alt=""
                                    width="720"
                                    height="720"
                                    hidden
                                    decoding="async"
                                    aria-hidden="true"
                                >
                            </div>
                        </div>
                        <?php if ($gallery_count > 1) : ?>
                            <button
                                type="button"
                                class="p-stage-nav p-stage-nav--prev"
                                data-stage-prev
                                aria-label="<?php esc_attr_e('Previous image', 'justccell'); ?>"
                            >
                                <svg viewBox="0 0 16 16" width="18" height="18" aria-hidden="true" focusable="false">
                                    <path d="M10.2 2.4 4.6 8l5.6 5.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <button
                                type="button"
                                class="p-stage-nav p-stage-nav--next"
                                data-stage-next
                                aria-label="<?php esc_attr_e('Next image', 'justccell'); ?>"
                            >
                                <svg viewBox="0 0 16 16" width="18" height="18" aria-hidden="true" focusable="false">
                                    <path d="M5.8 2.4 11.4 8 5.8 13.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                    <?php if ($gallery_count > 0) : ?>
                        <div class="p-thumbs-rail" data-thumbs-rail>
                            <button
                                type="button"
                                class="p-thumbs-rail__btn p-thumbs-rail__btn--prev"
                                data-thumbs-prev
                                hidden
                                aria-label="<?php esc_attr_e('Previous images', 'justccell'); ?>"
                            >
                                <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false">
                                    <path d="M10.2 2.4 4.6 8l5.6 5.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <div class="p-thumbs p-thumbs--stage" data-product-thumbs>
                            <?php for ($i = 0; $i < $gallery_count; $i++) :
                                $gid = (int) ($gallery_ids[$i] ?? 0);
                                $gkey = (string) ($gallery_keys[$i] ?? '');
                                $src = justccell_product_media_url($gid, $gkey);
                                ?>
                                <button
                                    class="p-thumbs__btn<?php echo $i === 0 ? ' is-on' : ''; ?>"
                                    type="button"
                                    data-thumb
                                    data-view="<?php echo $i === 0 && $spin_urls !== [] ? 'spin' : 'still'; ?>"
                                    data-src="<?php echo esc_url($src); ?>"
                                    aria-label="<?php echo esc_attr(sprintf(__('View image %d', 'justccell'), $i + 1)); ?>"
                                >
                                    <?php justccell_echo_product_media($gid, $gkey, ['alt' => '', 'width' => 88, 'height' => 88]); ?>
                                </button>
                            <?php endfor; ?>
                            </div>
                            <button
                                type="button"
                                class="p-thumbs-rail__btn p-thumbs-rail__btn--next"
                                data-thumbs-next
                                hidden
                                aria-label="<?php esc_attr_e('Next images', 'justccell'); ?>"
                            >
                                <svg viewBox="0 0 16 16" width="16" height="16" aria-hidden="true" focusable="false">
                                    <path d="M5.8 2.4 11.4 8 5.8 13.6" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                                </svg>
                            </button>
                            <span class="p-thumbs-rail__fade" aria-hidden="true"></span>
                        </div>
                    <?php endif; ?>
                    <?php
                    get_template_part('template-parts/product/buy-box', null, $buy_args + ['slot' => 'purchase']);
                    ?>
                </div>
            </div>
            <?php
            get_template_part('template-parts/product/buy-box', null, $buy_args + ['slot' => 'close']);
            ?>
        </div>
    </section>

    <?php
    get_template_part('template-parts/product/laser-offer', null, [
        'sku'    => $slug,
        'woo_id' => (int) ($product['woo_id'] ?? 0),
    ]);
    ?>

    <?php if ($features !== []) : ?>
        <section class="jc-vertical-scroll p-high" data-sticky-features style="--jc-vertical-scroll-h: <?php echo esc_attr((string) (100 + ($feature_count - 1) * 110)); ?>vh">
            <div class="jc-vertical-scroll__pin p-high__pin" data-sticky-pin>
                <?php foreach ($features as $i => $feature) :
                    $feat_title = trim((string) ($feature['title'] ?? ''));
                    $feat_copy  = trim((string) ($feature['copy'] ?? ''));
                    $feat_note  = trim((string) ($feature['note'] ?? ''));
                    $feat_art   = $feat_title === '' && $feat_copy === '' && $feat_note === '';
                    $feat_text_color = function_exists('justccell_normalize_highlight_text_color')
                        ? justccell_normalize_highlight_text_color((string) ($feature['text_color'] ?? 'black'))
                        : 'black';
                    $feat_txt_class = 'p-high__txt' . ($feat_text_color === 'white' ? ' p-high__txt--white' : '');
                    ?>
                    <div class="jc-vertical-scroll-slide p-high__panel vertical-slider<?php echo $i === 0 ? ' is-on' : ''; ?><?php echo $feat_art ? ' p-high__panel--art' : ''; ?>" data-feature-panel>
                        <div class="jc-vertical-scroll-slide__media p-high__img">
                            <?php justccell_echo_product_media(
                                (int) ($feature['image_id'] ?? 0),
                                (string) ($feature['image'] ?? ''),
                                ['alt' => $feat_title !== '' ? $feat_title : $name, 'loading' => 'lazy']
                            ); ?>
                        </div>
                        <?php if (!$feat_art) : ?>
                        <div class="<?php echo esc_attr($feat_txt_class); ?>">
                            <?php justccell_echo_heading($feat_title, (string) ($feature['title_tag'] ?? 'h2')); ?>
                            <?php if ($feat_copy !== '') : ?>
                                <p><?php echo nl2br(esc_html($feat_copy)); ?></p>
                            <?php endif; ?>
                            <?php if ($feat_note !== '') : ?>
                                <p class="p-high__note"><?php echo esc_html($feat_note); ?></p>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
                <div class="p-high__dots" role="tablist" aria-label="<?php esc_attr_e('Product highlights', 'justccell'); ?>">
                    <?php foreach ($features as $i => $feature) :
                        $dot_label = trim((string) ($feature['title'] ?? ''));
                        if ($dot_label === '') {
                            $dot_label = sprintf(__('Highlight %d', 'justccell'), $i + 1);
                        }
                        ?>
                        <button class="<?php echo $i === 0 ? 'is-on' : ''; ?>" type="button" data-feature-dot aria-label="<?php echo esc_attr($dot_label); ?>"></button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($show_evomax) : ?>
    <section class="p-evomax">
        <?php justccell_echo_product_media($evomax_bg_id, $evomax_bg_key, [
            'class'   => 'p-evomax__bg',
            'alt'     => '',
            'loading' => 'lazy',
        ]); ?>
        <div class="p-evomax__box">
            <?php justccell_echo_heading(
                (string) ($product['evomax_title'] ?? ''),
                (string) ($product['evomax_title_tag'] ?? 'h2'),
                'p-evomax__title',
                false,
                (string) ($product['evomax_title_color'] ?? '#ffffff')
            ); ?>
            <i class="p-dart__rule p-dart__rule--center" aria-hidden="true"></i>
            <p><?php echo nl2br(esc_html((string) ($product['evomax_copy'] ?? ''))); ?></p>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($details_count > 0) : ?>
        <section class="p-details">
            <div class="p-details__box">
                <div class="p-details__wide">
                    <?php justccell_echo_product_media(
                        (int) ($details_ids[0] ?? 0),
                        (string) ($details_keys[0] ?? ''),
                        ['alt' => '', 'width' => 1400, 'height' => 460, 'loading' => 'lazy']
                    ); ?>
                </div>
                <?php if ($details_count > 1) : ?>
                    <div class="p-details__pair">
                        <?php for ($i = 1; $i < $details_count; $i++) : ?>
                            <div class="p-details__cell">
                                <?php justccell_echo_product_media(
                                    (int) ($details_ids[$i] ?? 0),
                                    (string) ($details_keys[$i] ?? ''),
                                    ['alt' => '', 'width' => 680, 'height' => 460, 'loading' => 'lazy']
                                ); ?>
                            </div>
                        <?php endfor; ?>
                    </div>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php
    $story = function_exists('justccell_product_description_parts')
        ? justccell_product_description_parts((string) ($product['description'] ?? ''))
        : ['show' => false];
    if (!empty($story['show'])) :
        ?>
        <section class="p-story" data-product-story>
            <div class="container p-story__box">
                <h2 class="p-story__title"><?php echo esc_html(sprintf(__('About %s', 'justccell'), $name)); ?></h2>
                <div class="p-story__body" data-story-body>
                    <div class="p-story__teaser">
                        <?php echo wp_kses_post((string) ($story['full'] ?? $story['teaser'] ?? '')); ?>
                    </div>
                </div>
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
                                <?php justccell_echo_catalog_image($item, [
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
