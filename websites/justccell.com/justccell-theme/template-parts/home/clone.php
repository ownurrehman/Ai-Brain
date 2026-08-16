<?php
/**
 * Homepage visual clone of ccell.com (reference images for design approval).
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$assets = justccell_home_assets();
$groups = justccell_catalog_by_category();
$tabs   = [
    'all-in-ones' => __('All-In-Ones', 'justccell'),
    'cartridge'   => __('Cartridges', 'justccell'),
    'pod-system'  => __('Pod Systems', 'justccell'),
    'battery'     => __('510 Batteries', 'justccell'),
];
?>
<section class="h-banner" data-banners>
    <div class="h-banner__track" data-banner-track>
        <?php foreach ($assets['banners'] as $i => $src) : ?>
            <?php if ($src === '') { continue; } ?>
            <a class="h-banner__slide<?php echo $i === 0 ? ' is-on' : ''; ?>" href="<?php echo esc_url(justccell_inquiry_url()); ?>">
                <img src="<?php echo esc_url($src); ?>" alt="" width="1920" height="930" <?php echo $i === 0 ? 'fetchpriority="high"' : 'loading="lazy"'; ?>>
            </a>
        <?php endforeach; ?>
    </div>
    <div class="h-banner__dots" data-banner-dots></div>
</section>

<section class="h-devices">
    <div class="container">
        <h2 class="h-title"><?php esc_html_e('Devices Crafted for Cannabis', 'justccell'); ?></h2>
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
                            <?php echo justccell_media_img((string) $item['image'], [
                                'alt'     => (string) $item['name'],
                                'width'   => 340,
                                'height'  => 340,
                                'loading' => 'lazy',
                            ]); ?>
                        </div>
                        <h3 class="h-card__name"><?php echo esc_html($item['name']); ?></h3>
                        <ul class="h-card__specs">
                            <?php foreach ($item['specs'] as $spec) : ?>
                                <li><?php echo esc_html($spec); ?></li>
                            <?php endforeach; ?>
                        </ul>
                        <span class="h-more">
                            <?php esc_html_e('More', 'justccell'); ?>
                            <?php if (!empty($assets['arrow'])) : ?>
                                <img src="<?php echo esc_url($assets['arrow']); ?>" alt="" width="18" height="12">
                            <?php endif; ?>
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
        <h2 class="h-title"><?php echo wp_kses_post('Customize<br>Your Own Products'); ?></h2>
        <div class="h-custom__intro">
            <p class="h-custom__kicker"><?php esc_html_e('Classic Customization', 'justccell'); ?></p>
            <p class="h-custom__copy"><?php esc_html_e('Set your brand apart with personalized finishes and distinctive secondary features that make your products truly unique.', 'justccell'); ?></p>
        </div>
        <div class="h-custom__grid">
            <?php foreach (['cust1', 'cust2', 'cust3', 'cust4'] as $key) : ?>
                <?php if (empty($assets[$key])) { continue; } ?>
                <div class="h-custom__item">
                    <img src="<?php echo esc_url($assets[$key]); ?>" alt="" width="400" height="584" loading="lazy">
                </div>
            <?php endforeach; ?>
        </div>
            <div class="h-custom__premium">
            <?php if (!empty($assets['premium'])) : ?>
            <div class="h-custom__premium-img">
                <img src="<?php echo esc_url($assets['premium']); ?>" alt="" width="820" height="588" loading="lazy">
            </div>
            <?php endif; ?>
            <div class="h-custom__premium-txt">
                <h3><?php esc_html_e('Premium Customization', 'justccell'); ?></h3>
                <p><?php esc_html_e('From concept to creation, our expert engineering and design teams are here to transform your vision into a masterpiece from the ground up.', 'justccell'); ?></p>
            </div>
        </div>
    </div>
</section>

<section class="h-fill">
    <div class="container h-fill__box">
        <div class="h-fill__txt">
            <h2 class="h-title"><?php esc_html_e('Make Filling and Capping Effortless', 'justccell'); ?></h2>
            <p><?php esc_html_e('The filling and capping solution delivers unmatched quality, efficiency, and affordability. Streamline production and turn filling and capping your devices into a hassle-free process.', 'justccell'); ?></p>
            <a class="h-more" href="<?php echo esc_url(home_url('/solution/')); ?>">
                <?php esc_html_e('View Details', 'justccell'); ?>
                <?php if (!empty($assets['arrow'])) : ?>
                    <img src="<?php echo esc_url($assets['arrow']); ?>" alt="" width="18" height="12">
                <?php endif; ?>
            </a>
        </div>
        <?php if (!empty($assets['fill'])) : ?>
        <div class="h-fill__img">
            <img src="<?php echo esc_url($assets['fill']); ?>" alt="" width="780" height="636" loading="lazy">
        </div>
        <?php endif; ?>
    </div>
</section>

<section class="h-trusted">
    <div class="container">
        <h2 class="h-title"><?php esc_html_e('Trusted by', 'justccell'); ?></h2>
        <?php if (!empty($assets['trusted'])) : ?>
            <img class="h-trusted__img" src="<?php echo esc_url($assets['trusted']); ?>" alt="<?php esc_attr_e('Trusted by industry brands', 'justccell'); ?>" width="1720" height="220" loading="lazy">
        <?php endif; ?>
    </div>
</section>

<section class="h-quote">
    <div class="container h-quote__box">
        <?php if (!empty($assets['quote_bg'])) : ?>
        <div class="h-quote__bg">
            <img src="<?php echo esc_url($assets['quote_bg']); ?>" alt="" width="1720" height="720" loading="lazy">
        </div>
        <?php endif; ?>
        <div class="h-quote__panel">
            <h2 class="h-title"><?php echo wp_kses_post('Get Samples<br>and Quotes'); ?></h2>
            <p><?php esc_html_e('Test your extracts with Justccell hardware. Samples delivered in 3-15 days.', 'justccell'); ?></p>
            <?php get_template_part('template-parts/inquiry/form'); ?>
        </div>
    </div>
</section>
