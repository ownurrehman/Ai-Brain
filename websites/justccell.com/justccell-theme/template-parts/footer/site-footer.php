<?php
/**
 * Site footer.
 *
 * Footer link columns: Appearance → Menus → Footer Top (parent = column title, children = links).
 * Bottom social row: Footer Bottom. Legal strip: Footer Last.
 * Logo: Justccell → Storefront → Footer logo, or Appearance → Customize → Site Identity.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$sub = sanitize_key((string) ($_GET['subscribe'] ?? ''));
$logo_id = function_exists('justccell_footer_logo_id') ? justccell_footer_logo_id() : 0;
$newsletter_placeholder = function_exists('justccell_form_setting')
    ? justccell_form_setting('newsletter_placeholder')
    : __('Enter Your E-mail Address', 'justccell');
$newsletter_success = function_exists('justccell_form_setting')
    ? justccell_form_setting('newsletter_success')
    : __('Thanks — we will be in touch.', 'justccell');
$newsletter_error = function_exists('justccell_form_setting')
    ? justccell_form_setting('newsletter_error')
    : __('Enter an email and accept the privacy policy.', 'justccell');
?>
<footer class="jc-foot">
    <div class="foot container2">
        <div class="foot_t">
            <div class="foot_t_l">
                <a href="<?php echo esc_url(home_url('/')); ?>">
                    <?php
                    if ($logo_id > 0) {
                        echo wp_get_attachment_image($logo_id, 'full', false, [
                            'alt' => get_bloginfo('name'),
                        ]);
                    } else {
                        echo '<span class="site-footer__wordmark">' . esc_html(get_bloginfo('name')) . '</span>';
                    }
                    ?>
                </a>
                <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
                    <input type="hidden" name="action" value="justccell_subscribe">
                    <?php wp_nonce_field('justccell_subscribe', 'justccell_subscribe_nonce'); ?>
                    <div class="foot_form">
                        <label class="visually-hidden" for="footer-email"><?php esc_html_e('Email address', 'justccell'); ?></label>
                        <input id="footer-email" type="email" name="email" required placeholder="<?php echo esc_attr($newsletter_placeholder); ?>" autocomplete="email">
                        <button type="submit" aria-label="<?php esc_attr_e('Subscribe', 'justccell'); ?>">→</button>
                    </div>
                    <div class="foot_form_radio">
                        <label>
                            <input type="checkbox" name="privacy_ok" value="1" required>
                            <p>
                                <?php
                                echo wp_kses(
                                    sprintf(
                                        __('By subscribing, you are agreeing to our <a href="%s">Privacy Policy.</a>', 'justccell'),
                                        esc_url(home_url('/privacy-policy/'))
                                    ),
                                    ['a' => ['href' => []]]
                                );
                                ?>
                            </p>
                        </label>
                    </div>
                    <?php if ($sub === 'sent') : ?>
                        <p class="site-footer__note" role="status"><?php echo esc_html($newsletter_success); ?></p>
                    <?php elseif ($sub === 'missing') : ?>
                        <p class="site-footer__note" role="alert"><?php echo esc_html($newsletter_error); ?></p>
                    <?php endif; ?>
                </form>
            </div>
            <div class="foot_t_r" aria-label="<?php esc_attr_e('Footer navigation', 'justccell'); ?>">
                <?php
                if (function_exists('justccell_render_footer_column_menu')) {
                    justccell_render_footer_column_menu();
                }
                ?>
            </div>
        </div>
        <div class="foot_b">
            <div class="foot_b_l">
                <?php
                if (function_exists('justccell_render_footer_bottom_menu')) {
                    justccell_render_footer_bottom_menu();
                }
                ?>
                <p>&copy; <?php echo esc_html((string) gmdate('Y')); ?> <?php echo esc_html(justccell_legal_name()); ?> <?php esc_html_e('All Rights Reserved.', 'justccell'); ?></p>
                <nav class="foot_legal" aria-label="<?php esc_attr_e('Legal', 'justccell'); ?>">
                    <?php
                    if (function_exists('justccell_render_footer_last_menu')) {
                        justccell_render_footer_last_menu();
                    }
                    ?>
                </nav>
            </div>
            <p><?php echo esc_html(justccell_footer_disclaimer()); ?></p>
            <?php if (function_exists('justccell_show_developer_credit') && justccell_show_developer_credit()) : ?>
                <p class="foot_by">
                    <a href="<?php echo esc_url(justccell_developer_url()); ?>" rel="noopener noreferrer" target="_blank">
                        <?php
                        echo esc_html(
                            sprintf(
                                /* translators: %s: developer name */
                                __('Website by %s', 'justccell'),
                                justccell_developer_name()
                            )
                        );
                        ?>
                    </a>
                </p>
            <?php endif; ?>
        </div>
    </div>
</footer>
