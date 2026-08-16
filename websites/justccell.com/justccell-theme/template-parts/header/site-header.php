<?php
/**
 * Header + PRODUCTS mega menu (ccell.com structure).
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$assets = function_exists('justccell_home_assets') ? justccell_home_assets() : [];
$cats   = function_exists('justccell_catalog_by_category') ? justccell_catalog_by_category() : [];
$labels = function_exists('justccell_product_category_labels') ? justccell_product_category_labels() : [
    'all-in-ones' => __('All-In-Ones', 'justccell'),
    'cartridge'   => __('Cartridges', 'justccell'),
    'pod-system'  => __('Pod Systems', 'justccell'),
    'battery'     => __('510 Batteries', 'justccell'),
];
$logo_id = function_exists('justccell_brand_logo_id') ? justccell_brand_logo_id() : (int) get_theme_mod('custom_logo');
if ($logo_id < 1) {
    $logo_id = (int) get_theme_mod('custom_logo');
}
?>
<header class="site-header" data-header>
    <div class="site-header__bar container">
        <a class="site-header__brand" href="<?php echo esc_url(home_url('/')); ?>">
            <?php
            $logo_id = $logo_id > 0 ? $logo_id : (int) get_theme_mod('custom_logo');
            if ($logo_id > 0) {
                echo wp_get_attachment_image($logo_id, 'full', false, [
                    'class' => 'custom-logo',
                    'alt'   => get_bloginfo('name'),
                ]);
            } elseif (!empty($assets['logo'])) {
                echo '<img src="' . esc_url((string) $assets['logo']) . '" alt="' . esc_attr(get_bloginfo('name')) . '" width="132" height="36">';
            } else {
                echo '<span class="site-header__wordmark">' . esc_html(get_bloginfo('name')) . '</span>';
            }
            ?>
        </a>

        <nav class="site-header__nav" aria-label="<?php esc_attr_e('Primary', 'justccell'); ?>" data-nav>
            <ul class="nav-list">
                <li class="nav-list__item nav-list__item--mega" data-mega>
                    <button class="nav-list__link nav-list__btn" type="button" data-mega-toggle><?php esc_html_e('Products', 'justccell'); ?></button>
                    <div class="mega" data-mega-panel>
                        <div class="mega__grid">
                            <?php foreach ($labels as $key => $label) : ?>
                                <div class="mega__col">
                                    <a class="mega__heading" href="<?php echo esc_url(justccell_category_url($key)); ?>"><?php echo esc_html($label); ?></a>
                                    <ul>
                                        <?php foreach (array_slice($cats[$key] ?? [], 0, 4) as $item) : ?>
                                            <li>
                                                <a class="mega__product" href="<?php echo esc_url(justccell_item_url($item)); ?>">
                                                    <?php echo justccell_media_img((string) $item['image'], [
                                                        'alt'    => '',
                                                        'width'  => 56,
                                                        'height' => 56,
                                                    ]); ?>
                                                    <span><?php echo esc_html($item['name']); ?></span>
                                                </a>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <a class="mega__all" href="<?php echo esc_url(justccell_category_url($key)); ?>"><?php esc_html_e('View all >', 'justccell'); ?></a>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </li>
                <li class="nav-list__item"><a class="nav-list__link" href="<?php echo esc_url(home_url('/technology/')); ?>"><?php esc_html_e('Why Justccell', 'justccell'); ?></a></li>
                <li class="nav-list__item"><a class="nav-list__link" href="<?php echo esc_url(home_url('/solution/')); ?>"><?php esc_html_e('Solution', 'justccell'); ?></a></li>
                <li class="nav-list__item"><a class="nav-list__link" href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About', 'justccell'); ?></a></li>
                <li class="nav-list__item"><a class="nav-list__link" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'justccell'); ?></a></li>
            </ul>
        </nav>

        <a class="btn btn--primary site-header__cta" href="<?php echo esc_url(justccell_inquiry_url()); ?>">
            <?php esc_html_e('Get samples & quotes', 'justccell'); ?>
        </a>

        <?php
        $langs        = justccell_languages();
        $current_lang = justccell_current_lang();
        ?>
        <div class="lang-switch" data-lang-switch>
            <button class="lang-switch__btn" type="button" data-lang-toggle aria-expanded="false" aria-haspopup="listbox">
                <span class="visually-hidden"><?php esc_html_e('Language', 'justccell'); ?></span>
                <span data-lang-current><?php echo esc_html(strtoupper($current_lang)); ?></span>
            </button>
            <ul class="lang-switch__list" hidden data-lang-panel role="listbox" aria-label="<?php esc_attr_e('Website language', 'justccell'); ?>">
                <?php foreach ($langs as $code => $label) : ?>
                    <li>
                        <a
                            class="lang-switch__option<?php echo $code === $current_lang ? ' is-on' : ''; ?>"
                            href="<?php echo esc_url(justccell_lang_url($code)); ?>"
                            lang="<?php echo esc_attr($code); ?>"
                            hreflang="<?php echo esc_attr($code); ?>"
                            dir="<?php echo $code === 'ar' ? 'rtl' : 'ltr'; ?>"
                            <?php echo $code === $current_lang ? 'aria-current="true"' : ''; ?>
                        ><?php echo esc_html($label); ?></a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </div>

        <button class="site-header__toggle" type="button" data-nav-toggle aria-expanded="false">
            <span class="visually-hidden"><?php esc_html_e('Menu', 'justccell'); ?></span>
            <span class="site-header__toggle-bars" aria-hidden="true"></span>
        </button>
    </div>
</header>
