<?php
/**
 * Footer template.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$market_url = function_exists('backlinkcrypto_marketplace_url') ? backlinkcrypto_marketplace_url() : home_url('/marketplace/');
?>
</div><!-- #bc-content -->

<div class="bc-toast" id="bc-toast" hidden role="status" aria-live="polite"></div>

<div class="bc-drawer-backdrop" id="bc-drawer-backdrop" hidden></div>
<aside class="bc-drawer" id="bc-drawer" aria-hidden="true" aria-label="<?php esc_attr_e('Shopping cart', 'backlinkcrypto'); ?>">
    <div class="bc-drawer__head">
        <h2><?php esc_html_e('Your cart', 'backlinkcrypto'); ?></h2>
        <button type="button" class="bc-drawer__close" data-bc-cart-close aria-label="<?php esc_attr_e('Close cart', 'backlinkcrypto'); ?>">×</button>
    </div>
    <div class="bc-drawer__body" data-bc-cart-items>
        <p class="bc-drawer__empty"><?php esc_html_e('Your cart is empty', 'backlinkcrypto'); ?></p>
    </div>
    <div class="bc-drawer__foot">
        <div class="bc-drawer__subtotal">
            <span><?php esc_html_e('Subtotal', 'backlinkcrypto'); ?></span>
            <strong data-bc-cart-subtotal>—</strong>
        </div>
        <a class="bc-btn bc-btn--primary bc-drawer__checkout" data-bc-checkout href="<?php echo esc_url(function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/')); ?>">
            <?php esc_html_e('Checkout', 'backlinkcrypto'); ?>
        </a>
        <a class="bc-drawer__view" data-bc-view-cart href="<?php echo esc_url(function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/')); ?>">
            <?php esc_html_e('View full cart', 'backlinkcrypto'); ?>
        </a>
    </div>
</aside>

<footer class="bc-footer">
    <div class="bc-container bc-footer__grid">
        <div class="bc-footer__brand">
            <div class="bc-brand__link" style="margin-bottom:.65rem">
                <span class="bc-brand__logo" aria-hidden="true">
                    <svg width="32" height="32" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <defs>
                            <linearGradient id="bcLogoGradFoot" x1="0" y1="0" x2="40" y2="40" gradientUnits="userSpaceOnUse">
                                <stop stop-color="#5596FE"/>
                                <stop offset=".5" stop-color="#7570FE"/>
                                <stop offset="1" stop-color="#9549FF"/>
                            </linearGradient>
                        </defs>
                        <rect width="40" height="40" rx="11" fill="url(#bcLogoGradFoot)"/>
                        <path d="M11 27V13h6.2c3.3 0 5.3 1.7 5.3 4.2 0 1.7-.9 3-2.4 3.6L23.8 27h-3.4l-3.1-5.7H14.2V27H11zm3.2-8.4h2.8c1.6 0 2.5-.8 2.5-2s-.9-2-2.5-2h-2.8v4z" fill="#fff"/>
                        <circle cx="28.5" cy="14.5" r="2.2" fill="#C4B5FD"/>
                        <path d="M26.2 27c2.4-2.1 4.3-4.8 5.5-7.8" stroke="#99F6E4" stroke-width="1.8" stroke-linecap="round"/>
                    </svg>
                </span>
                <strong>Backlink Crypto</strong>
            </div>
            <p><?php esc_html_e('Vetted crypto-niche backlinks for exchanges, DeFi, NFT, and Web3 publishers.', 'backlinkcrypto'); ?></p>
        </div>
        <div>
            <h3><?php esc_html_e('Marketplace', 'backlinkcrypto'); ?></h3>
            <ul>
                <li><a href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Marketplace', 'backlinkcrypto'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/packages/')); ?>"><?php esc_html_e('Packages', 'backlinkcrypto'); ?></a></li>
                <li><a href="<?php echo esc_url(backlinkcrypto_blog_url()); ?>"><?php esc_html_e('Blog', 'backlinkcrypto'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About', 'backlinkcrypto'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/policies/')); ?>"><?php esc_html_e('Policies', 'backlinkcrypto'); ?></a></li>
                <li><a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'backlinkcrypto'); ?></a></li>
            </ul>
        </div>
        <div>
            <h3><?php esc_html_e('Support', 'backlinkcrypto'); ?></h3>
            <ul>
                <?php
                if (has_nav_menu('footer')) {
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'container'      => false,
                        'items_wrap'     => '%3$s',
                        'depth'          => 1,
                    ]);
                } else {
                    ?>
                    <li><a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy Policy', 'backlinkcrypto'); ?></a></li>
                    <li><a href="<?php echo esc_url(home_url('/terms/')); ?>"><?php esc_html_e('Terms', 'backlinkcrypto'); ?></a></li>
                    <li><a href="mailto:<?php echo esc_attr(function_exists('backlinkcrypto_public_support_email') ? backlinkcrypto_public_support_email() : 'contact@backlinkcrypto.com'); ?>"><?php esc_html_e('Email Support', 'backlinkcrypto'); ?></a></li>
                    <?php
                }
                ?>
            </ul>
        </div>
    </div>
    <div class="bc-container bc-footer__bottom">
        <p>&copy; <?php echo esc_html(gmdate('Y')); ?> Backlink Crypto. <?php esc_html_e('All rights reserved.', 'backlinkcrypto'); ?></p>
    </div>
</footer>

<?php wp_footer(); ?>
</body>
</html>
