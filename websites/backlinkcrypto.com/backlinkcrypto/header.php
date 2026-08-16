<?php
/**
 * Header template — Marketplace Pro.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$cart_count = function_exists('backlinkcrypto_cart_count') ? backlinkcrypto_cart_count() : 0;
$cart_url   = function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/');
$account    = function_exists('wc_get_page_permalink') ? wc_get_page_permalink('myaccount') : home_url('/my-account/');
$home       = home_url('/');
$market_url = function_exists('backlinkcrypto_marketplace_url') ? backlinkcrypto_marketplace_url() : home_url('/marketplace/');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<a class="bc-skip" href="#bc-content"><?php esc_html_e('Skip to content', 'backlinkcrypto'); ?></a>

<header class="bc-header" data-bc-header>
    <div class="bc-container">
        <div class="bc-header__shell">
            <div class="bc-header__inner">
                <div class="bc-brand">
                    <a class="bc-brand__link" href="<?php echo esc_url($home); ?>" aria-label="Backlink Crypto">
                        <span class="bc-brand__logo-plate" aria-hidden="true">
                            <?php
                            $bc_logo_id = (int) get_theme_mod('custom_logo');
                            if ($bc_logo_id > 0) {
                                echo wp_get_attachment_image(
                                    $bc_logo_id,
                                    'full',
                                    false,
                                    [
                                        'class'   => 'custom-logo',
                                        'alt'     => get_bloginfo('name'),
                                        'loading' => 'eager',
                                    ]
                                );
                            } else {
                                ?>
                                <svg width="36" height="36" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M11 27V13h6.2c3.3 0 5.3 1.7 5.3 4.2 0 1.7-.9 3-2.4 3.6L23.8 27h-3.4l-3.1-5.7H14.2V27H11zm3.2-8.4h2.8c1.6 0 2.5-.8 2.5-2s-.9-2-2.5-2h-2.8v4z" fill="#fff"/>
                                    <circle cx="28.5" cy="14.5" r="2.2" fill="#C4B5FD"/>
                                    <path d="M26.2 27c2.4-2.1 4.3-4.8 5.5-7.8" stroke="#E9D5FF" stroke-width="1.8" stroke-linecap="round"/>
                                </svg>
                                <?php
                            }
                            ?>
                        </span>
                        <span class="bc-brand__text">
                            <strong>Backlink Crypto</strong>
                            <small><?php esc_html_e('Verified crypto placements', 'backlinkcrypto'); ?></small>
                        </span>
                    </a>
                </div>

                <nav class="bc-nav" aria-label="<?php esc_attr_e('Primary', 'backlinkcrypto'); ?>">
                    <ul class="bc-nav__list">
                        <li><a href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Marketplace', 'backlinkcrypto'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/packages/')); ?>"><?php esc_html_e('Packages', 'backlinkcrypto'); ?></a></li>
                        <li><a href="<?php echo esc_url(backlinkcrypto_blog_url()); ?>"><?php esc_html_e('Blog', 'backlinkcrypto'); ?></a></li>
                        <li><a href="<?php echo esc_url($home); ?>#bc-how"><?php esc_html_e('How it works', 'backlinkcrypto'); ?></a></li>
                        <li><a href="<?php echo esc_url($home); ?>#bc-faq"><?php esc_html_e('FAQ', 'backlinkcrypto'); ?></a></li>
                        <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'backlinkcrypto'); ?></a></li>
                        <li><a href="<?php echo esc_url($account); ?>"><?php esc_html_e('Account', 'backlinkcrypto'); ?></a></li>
                    </ul>
                </nav>

                <div class="bc-header__actions">
                    <a class="bc-btn bc-btn--primary bc-btn--compact" href="<?php echo esc_url($market_url); ?>">
                        <?php esc_html_e('Browse sites', 'backlinkcrypto'); ?>
                    </a>
                    <a class="bc-cart" href="<?php echo esc_url($cart_url); ?>" data-bc-cart-open aria-label="<?php esc_attr_e('View cart', 'backlinkcrypto'); ?>">
                        <span class="bc-cart__icon" aria-hidden="true">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M6 6h15l-1.5 9h-12z"/>
                                <circle cx="9" cy="20" r="1.5"/>
                                <circle cx="18" cy="20" r="1.5"/>
                                <path d="M6 6L5 3H2"/>
                            </svg>
                        </span>
                        <span class="bc-cart__count<?php echo $cart_count > 0 ? '' : ' is-empty'; ?>" data-bc-cart-count><?php echo esc_html((string) $cart_count); ?></span>
                    </a>
                    <button type="button" class="bc-menu-toggle" aria-expanded="false" aria-controls="bc-mobile-nav" data-bc-menu>
                        <span class="bc-menu-toggle__bar"></span>
                        <span class="bc-menu-toggle__bar"></span>
                        <span class="bc-menu-toggle__bar"></span>
                        <span class="screen-reader-text"><?php esc_html_e('Menu', 'backlinkcrypto'); ?></span>
                    </button>
                </div>
            </div>

            <div id="bc-mobile-nav" class="bc-mobile-nav" hidden>
                <ul class="bc-nav__list">
                    <li><a href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Marketplace', 'backlinkcrypto'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/packages/')); ?>"><?php esc_html_e('Packages', 'backlinkcrypto'); ?></a></li>
                    <li><a href="<?php echo esc_url(backlinkcrypto_blog_url()); ?>"><?php esc_html_e('Blog', 'backlinkcrypto'); ?></a></li>
                    <li><a href="<?php echo esc_url($home); ?>#bc-how"><?php esc_html_e('How it works', 'backlinkcrypto'); ?></a></li>
                    <li><a href="<?php echo esc_url($home); ?>#bc-faq"><?php esc_html_e('FAQ', 'backlinkcrypto'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'backlinkcrypto'); ?></a></li>
                    <li><a href="<?php echo esc_url($account); ?>"><?php esc_html_e('Account', 'backlinkcrypto'); ?></a></li>
                    <li><a href="<?php echo esc_url($cart_url); ?>"><?php esc_html_e('Cart', 'backlinkcrypto'); ?></a></li>
                </ul>
            </div>
        </div>
    </div>
</header>

<div id="bc-content">
