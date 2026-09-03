<?php
/**
 * Homepage — ACF content + catalog rails.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$home = justccell_get_home_content();
$keys = is_array($home['asset_keys'] ?? null) ? $home['asset_keys'] : justccell_home_asset_keys();
$home_files = [];
foreach ($keys as $value) {
    if (is_array($value)) {
        $home_files = array_merge($home_files, $value);
        continue;
    }
    $home_files[] = $value;
}
justccell_ensure_media_files($home_files);
$home_slides = justccell_home_hero_slides();

$groups = justccell_home_rails();
$cat_labels = function_exists('justccell_storefront_category_labels')
    ? justccell_storefront_category_labels()
    : [
        'all-in-ones' => __('All-In-Ones', 'justccell'),
        'cartridge'   => __('Cartridges', 'justccell'),
        'pod-system'  => __('Pod Systems', 'justccell'),
        'battery'     => __('510 Batteries', 'justccell'),
    ];
$acf_tab_map = [
    'all-in-ones' => 'tab_all_in_ones',
    'cartridge'   => 'tab_cartridge',
    'pod-system'  => 'tab_pod_system',
    'battery'     => 'tab_battery',
];
$tabs = [];
foreach ($cat_labels as $key => $default_label) {
    if (($groups[$key] ?? []) === []) {
        continue;
    }
    $acf_key = $acf_tab_map[$key] ?? '';
    $label   = $acf_key !== '' ? trim((string) ($home[$acf_key] ?? '')) : '';
    $tabs[$key] = $label !== '' ? $label : $default_label;
}
if ($tabs === []) {
    foreach ($cat_labels as $key => $default_label) {
        $tabs[$key] = $default_label;
    }
}
$arrow_id  = (int) ($home['arrow_id'] ?? 0);
$arrow_key = (string) ($keys['arrow'] ?? '');
?>
<section class="h-banner" data-banners>
    <div class="h-banner__track" data-banner-track>
        <?php foreach ($home_slides as $i => $slide) : ?>
            <a class="h-banner__slide<?php echo $i === 0 ? ' is-on' : ''; ?>" href="<?php echo esc_url($slide['url'] !== '' ? $slide['url'] : justccell_inquiry_url()); ?>">
                <?php echo wp_get_attachment_image((int) $slide['id'], 'full', false, [
                    'alt'           => (string) $slide['alt'],
                    'width'         => 1920,
                    'height'        => 930,
                    'fetchpriority' => $i === 0 ? 'high' : null,
                    'loading'       => $i === 0 ? null : 'lazy',
                ]); ?>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="h-banner__dots" data-banner-dots></div>
</section>

<section class="h-devices">
    <div class="container">
        <?php justccell_echo_heading((string) ($home['devices_heading'] ?? ''), (string) ($home['devices_heading_tag'] ?? 'h1'), 'h-title'); ?>
        <div class="h-tabs" role="tablist">
            <?php $i = 0; foreach ($tabs as $key => $label) : ?>
                <button class="h-tabs__btn<?php echo $i === 0 ? ' is-on' : ''; ?>" type="button" role="tab" data-tab="<?php echo esc_attr($key); ?>" aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>">
                    <?php echo esc_html($label); ?>
                </button>
            <?php $i++; endforeach; ?>
        </div>
    </div>
    <?php $i = 0; foreach ($tabs as $key => $label) : ?>
        <div class="h-rail<?php echo $i === 0 ? ' is-on' : ''; ?>" data-rail="<?php echo esc_attr($key); ?>">
            <button class="h-rail__prev" type="button" data-rail-prev aria-label="<?php esc_attr_e('Previous', 'justccell'); ?>"></button>
            <div class="h-rail__scroller" data-rail-scroller>
                <?php foreach ($groups[$key] ?? [] as $item) : ?>
                    <a class="h-card" href="<?php echo esc_url(justccell_item_url($item)); ?>">
                        <div class="h-card__img">
                            <?php justccell_echo_catalog_image($item, [
                                'alt'     => (string) $item['name'],
                                'width'   => 340,
                                'height'  => 340,
                                'loading' => 'lazy',
                            ]); ?>
                        </div>
                        <h3 class="h-card__name"><?php echo esc_html($item['name']); ?></h3>
                        <ul class="h-card__specs">
                            <?php foreach ((array) ($item['specs'] ?? []) as $spec) : ?>
                                <li><?php echo esc_html((string) $spec); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <span class="h-more">
                            <?php esc_html_e('More', 'justccell'); ?>
                            <?php
                            if ($arrow_id > 0) {
                                echo wp_get_attachment_image($arrow_id, 'full', false, ['alt' => '', 'width' => 18, 'height' => 12, 'loading' => 'lazy']);
                            } else {
                                echo justccell_media_img($arrow_key, ['alt' => '', 'width' => 18, 'height' => 12, 'loading' => 'lazy']);
                            }
                            ?>
                        </span>
                    </a>
                <?php endforeach; ?>
            </div>
            <button class="h-rail__next" type="button" data-rail-next aria-label="<?php esc_attr_e('Next', 'justccell'); ?>"></button>
        </div>
    <?php $i++; endforeach; ?>
</section>

<section class="h-custom">
    <div class="container">
        <?php justccell_echo_heading((string) ($home['custom_heading'] ?? ''), (string) ($home['custom_heading_tag'] ?? 'h2'), 'h-title', true); ?>
        <div class="h-custom__intro">
            <p class="h-custom__kicker"><?php echo esc_html((string) ($home['custom_kicker'] ?? '')); ?></p>
            <p class="h-custom__copy"><?php echo esc_html((string) ($home['custom_copy'] ?? '')); ?></p>
        </div>
        <div class="h-custom__grid">
            <?php
            $custom_ids = is_array($home['custom_image_ids'] ?? null) ? $home['custom_image_ids'] : [];
            if ($custom_ids !== []) {
                foreach ($custom_ids as $cid) {
                    echo '<div class="h-custom__item">';
                    echo wp_get_attachment_image((int) $cid, 'full', false, ['alt' => '', 'width' => 400, 'height' => 584, 'loading' => 'lazy']);
                    echo '</div>';
                }
            } else {
                foreach ((array) ($home['custom_image_keys'] ?? []) as $slot) {
                    echo '<div class="h-custom__item">';
                    echo justccell_media_img((string) ($keys[$slot] ?? ''), ['alt' => '', 'width' => 400, 'height' => 584, 'loading' => 'lazy']);
                    echo '</div>';
                }
            }
            ?>
        </div>
        <div class="h-custom__premium">
            <div class="h-custom__premium-img">
                <?php justccell_echo_home_image((int) ($home['premium_image_id'] ?? 0), 'premium', $keys, ['alt' => '', 'width' => 820, 'height' => 588, 'loading' => 'lazy']); ?>
            </div>
            <div class="h-custom__premium-txt">
                <?php justccell_echo_heading((string) ($home['premium_heading'] ?? ''), (string) ($home['premium_heading_tag'] ?? 'h3'), 'h-custom__premium-title'); ?>
                <p><?php echo esc_html((string) ($home['premium_copy'] ?? '')); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="h-fill">
    <div class="container h-fill__box">
        <div class="h-fill__txt">
            <?php justccell_echo_heading((string) ($home['fill_heading'] ?? ''), (string) ($home['fill_heading_tag'] ?? 'h2'), 'h-title'); ?>
            <p><?php echo esc_html((string) ($home['fill_copy'] ?? '')); ?></p>
            <a class="h-more" href="<?php echo esc_url((string) ($home['fill_link_url'] ?? home_url('/solution/'))); ?>">
                <?php echo esc_html((string) ($home['fill_link_label'] ?? __('View Details', 'justccell'))); ?>
                <?php
                if ($arrow_id > 0) {
                    echo wp_get_attachment_image($arrow_id, 'full', false, ['alt' => '', 'width' => 18, 'height' => 12, 'loading' => 'lazy']);
                } else {
                    echo justccell_media_img($arrow_key, ['alt' => '', 'width' => 18, 'height' => 12, 'loading' => 'lazy']);
                }
                ?>
            </a>
        </div>
        <div class="h-fill__img">
            <?php justccell_echo_home_image((int) ($home['fill_image_id'] ?? 0), 'fill', $keys, ['alt' => '', 'width' => 780, 'height' => 636, 'loading' => 'lazy']); ?>
        </div>
    </div>
</section>

<section class="h-laser">
    <div class="container h-laser__box">
        <div class="h-laser__copy">
            <?php
            $laser = function_exists('justccell_product_laser_offer')
                ? justccell_product_laser_offer('', 0)
                : [];
            $laser_heading = (string) ($home['trusted_heading'] ?? '');
            if ($laser_heading === '' || strcasecmp($laser_heading, 'Trusted by') === 0) {
                $laser_heading = (string) ($laser['heading'] ?? __('Laser engraving', 'justccell'));
            }
            justccell_echo_heading($laser_heading, (string) ($home['trusted_heading_tag'] ?? 'h2'), 'h-title');
            ?>
            <p><?php echo esc_html((string) ($laser['copy'] ?? __('From beam to brand — laser engraving for your logo, micro text, and finish. Watch the film, then add engraving to your quote.', 'justccell'))); ?></p>
            <a class="h-more" href="<?php echo esc_url((string) ($laser['cta_url'] ?? home_url('/laser-engraving/'))); ?>">
                <?php echo esc_html((string) ($laser['cta_label'] ?? __('See laser engraving', 'justccell'))); ?>
            </a>
        </div>
        <?php if (!empty($laser['video'])) : ?>
        <div class="h-laser__media">
            <video class="h-laser__video" controls muted playsinline preload="metadata" title="<?php echo esc_attr($laser_heading); ?>">
                <source src="<?php echo esc_url((string) $laser['video']); ?>" type="video/mp4">
            </video>
        </div>
        <?php endif; ?>
    </div>
</section>
