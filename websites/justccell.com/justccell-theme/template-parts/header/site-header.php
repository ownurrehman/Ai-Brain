<?php
/**
 * Site header. Links come from Appearance → Menus (Primary).
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$nav     = function_exists('justccell_header_nav') ? justccell_header_nav() : [];
$cta     = function_exists('justccell_header_cta') ? justccell_header_cta() : ['label' => '', 'url' => ''];
$logo_id = function_exists('justccell_brand_logo_id') ? justccell_brand_logo_id() : (int) get_theme_mod('custom_logo');
if ($logo_id < 1) {
    $logo_id = (int) get_theme_mod('custom_logo');
}
?>
<a class="skip-link" href="#main"><?php esc_html_e('Skip to content', 'justccell'); ?></a>
<nav class="show_nav" data-header>
    <div class="container2">
        <a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
            <?php
            if ($logo_id > 0) {
                echo wp_get_attachment_image($logo_id, 'full', false, [
                    'class' => 'yc',
                    'alt'   => get_bloginfo('name'),
                ]);
            } else {
                echo '<span class="site-header__wordmark">' . esc_html(get_bloginfo('name')) . '</span>';
            }
            ?>
        </a>

        <div class="nav">
                <i data-mega-dim></i>
                <ul>
                    <?php foreach ($nav as $item) : ?>
                        <?php if (($item['type'] ?? '') === 'products') : ?>
                            <li data-mega>
                                <a href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html((string) $item['title']); ?></a>
                                <div class="pro_nav2">
                                    <div class="pro_nav_tab pro_nav_tab2">
                                        <?php $ti = 0; foreach (($item['tabs'] ?? []) as $tab) : ?>
                                            <a class="<?php echo $ti === 0 ? 'on' : ''; ?>" href="<?php echo esc_url((string) $tab['url']); ?>" data-mega-tab="<?php echo esc_attr((string) $tab['key']); ?>">
                                                <?php echo esc_html((string) $tab['label']); ?>
                                            </a>
                                        <?php $ti++; endforeach; ?>
                                    </div>
                                    <div class="pro_nav_tab2_box">
                                        <?php $ti = 0; foreach (($item['tabs'] ?? []) as $tab) : ?>
                                            <div class="pro_nav_tab2_con container2<?php echo $ti === 0 ? ' on' : ''; ?>" data-mega-panel="<?php echo esc_attr((string) $tab['key']); ?>">
                                                <div class="pro_nav_tab2_nr">
                                                    <?php foreach (($tab['items'] ?? []) as $card) : ?>
                                                        <a class="pro_nav_tab2_hz" href="<?php echo esc_url((string) $card['url']); ?>">
                                                            <div class="pro_nav_tab2_img">
                                                                <?php
                                                                if (function_exists('justccell_echo_catalog_image')) {
                                                                    justccell_echo_catalog_image($card, [
                                                                        'alt' => (string) $card['name'],
                                                                    ]);
                                                                }
                                                                ?>
                                                            </div>
                                                            <p><?php echo esc_html((string) $card['name']); ?></p>
                                                        </a>
                                                    <?php endforeach; ?>
                                                </div>
                                                <a class="nav_all" href="<?php echo esc_url((string) $tab['url']); ?>"><?php esc_html_e('View all >', 'justccell'); ?></a>
                                            </div>
                                        <?php $ti++; endforeach; ?>
                                    </div>
                                </div>
                            </li>
                        <?php elseif (($item['type'] ?? '') === 'dropdown') : ?>
                            <li data-mega>
                                <a href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html((string) $item['title']); ?></a>
                                <div class="pro_nav2">
                                    <div class="pro_nav_tab">
                                        <?php foreach (($item['links'] ?? []) as $link) : ?>
                                            <a href="<?php echo esc_url((string) $link['url']); ?>"><?php echo esc_html((string) $link['title']); ?></a>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </li>
                        <?php else : ?>
                            <li>
                                <a href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html((string) $item['title']); ?></a>
                            </li>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div class="nav_rr_box">
                <?php if (class_exists('WooCommerce')) : ?>
                <button type="button" class="jc-cart-trigger" data-cart-open aria-label="<?php esc_attr_e('Open cart', 'justccell'); ?>">
                    <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path fill="currentColor" d="M7 4h-2l-1 2h2l3.6 7.59-1.35 2.45a1 1 0 0 0 .9 1.41H19v-2H9.42l1.1-2h7.45a1 1 0 0 0 .95-.68L21.22 7H7.21l-.94-2zm-1 16a2 2 0 1 0 0 4 2 2 0 0 0 0-4zm10 0a2 2 0 1 0 .001 3.999A2 2 0 0 0 16 20z"/></svg>
                    <span data-cart-count hidden>0</span>
                </button>
                <?php endif; ?>
                <?php if ($cta['label'] !== '') : ?>
                <div class="nav_rr">
                    <a class="g_a g_a2 font-r" href="<?php echo esc_url((string) $cta['url']); ?>">
                        <p><?php echo esc_html((string) $cta['label']); ?></p>
                    </a>
                </div>
                <?php endif; ?>
            </div>

        <div class="nav_box2" id="c-header">
            <ul class="c-nav2">
                <?php foreach ($nav as $item) : ?>
                    <li>
                        <div class="c-title-box">
                            <a href="<?php echo esc_url((string) $item['url']); ?>"><?php echo esc_html((string) $item['title']); ?></a>
                            <?php if (($item['type'] ?? '') === 'products' && ($item['tabs'] ?? []) !== []) : ?>
                                <button type="button" data-acc-toggle aria-label="<?php echo esc_attr(sprintf(/* translators: %s menu label */ __('Open %s', 'justccell'), (string) $item['title'])); ?>">▾</button>
                            <?php elseif (($item['type'] ?? '') === 'dropdown' && ($item['links'] ?? []) !== []) : ?>
                                <button type="button" data-acc-toggle aria-label="<?php echo esc_attr(sprintf(/* translators: %s menu label */ __('Open %s', 'justccell'), (string) $item['title'])); ?>">▾</button>
                            <?php endif; ?>
                        </div>
                        <?php if (($item['type'] ?? '') === 'products') : ?>
                            <div class="c-title-con">
                                <?php foreach (($item['tabs'] ?? []) as $tab) : ?>
                                    <a href="<?php echo esc_url((string) $tab['url']); ?>"><?php echo esc_html((string) $tab['label']); ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php elseif (($item['type'] ?? '') === 'dropdown') : ?>
                            <div class="c-title-con">
                                <?php foreach (($item['links'] ?? []) as $link) : ?>
                                    <a href="<?php echo esc_url((string) $link['url']); ?>"><?php echo esc_html((string) $link['title']); ?></a>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
                <?php if ($cta['label'] !== '') : ?>
                <li class="c-nav2__cta">
                    <a class="g_a g_a2" href="<?php echo esc_url((string) $cta['url']); ?>">
                        <p><?php echo esc_html((string) $cta['label']); ?></p>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
            <div class="c-switch" data-nav-toggle role="button" tabindex="0" aria-expanded="false" aria-label="<?php esc_attr_e('Menu', 'justccell'); ?>">
                <i></i><i></i><i></i>
            </div>
        </div>
    </div>
</nav>
