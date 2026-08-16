<?php
/**
 * Site footer.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}
?>
<footer class="site-footer">
    <div class="container site-footer__grid">
        <div class="site-footer__brand">
            <?php
            $footer_logo = function_exists('justccell_brand_logo_id') ? justccell_brand_logo_id() : 0;
            if ($footer_logo > 0) {
                echo wp_get_attachment_image($footer_logo, 'full', false, [
                    'class' => 'site-footer__logo',
                    'alt'   => get_bloginfo('name'),
                ]);
            } else {
                echo '<p class="site-footer__wordmark">' . esc_html(get_bloginfo('name')) . '</p>';
            }
            ?>
            <p class="site-footer__tagline"><?php esc_html_e('Precision hardware for cannabis extracts.', 'justccell'); ?></p>
            <nav class="site-footer__links" aria-label="<?php esc_attr_e('Company', 'justccell'); ?>">
                <a href="<?php echo esc_url(home_url('/about/')); ?>"><?php esc_html_e('About', 'justccell'); ?></a>
                <a href="<?php echo esc_url(home_url('/technology/')); ?>"><?php esc_html_e('Why Justccell', 'justccell'); ?></a>
                <a href="<?php echo esc_url(home_url('/solution/')); ?>"><?php esc_html_e('Solution', 'justccell'); ?></a>
                <a href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact', 'justccell'); ?></a>
                <a href="<?php echo esc_url(home_url('/privacy-policy/')); ?>"><?php esc_html_e('Privacy', 'justccell'); ?></a>
            </nav>
        </div>
        <nav class="site-footer__nav" aria-label="<?php esc_attr_e('Footer', 'justccell'); ?>">
            <?php
            wp_nav_menu([
                'theme_location' => 'footer',
                'container'      => false,
                'menu_class'     => 'footer-list',
                'fallback_cb'    => false,
            ]);
            ?>
        </nav>
        <div class="site-footer__cta">
            <p><?php esc_html_e('Test your extracts with Justccell hardware. Samples typically ship in 3–15 days.', 'justccell'); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url(justccell_inquiry_url()); ?>">
                <?php esc_html_e('Get samples & quotes', 'justccell'); ?>
            </a>
        </div>
    </div>
    <div class="site-footer__legal container">
        <p>&copy; <?php echo esc_html((string) gmdate('Y')); ?> <?php bloginfo('name'); ?>
            <span class="site-footer__context"><?php echo esc_html(strtoupper(justccell_current_store()) . ' · ' . justccell_current_currency() . ' · ' . strtoupper(justccell_current_lang())); ?></span>
        </p>
        <?php
        wp_nav_menu([
            'theme_location' => 'legal',
            'container'      => false,
            'menu_class'     => 'footer-list footer-list--inline',
            'fallback_cb'    => false,
        ]);
        ?>
    </div>
</footer>
