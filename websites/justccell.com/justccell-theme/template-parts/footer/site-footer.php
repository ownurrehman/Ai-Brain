<?php
/**
 * Site footer.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$columns = function_exists('justccell_footer_columns') ? justccell_footer_columns() : [];
$social  = function_exists('justccell_social_links') ? justccell_social_links() : [];
$legal   = function_exists('justccell_legal_links') ? justccell_legal_links() : [];
$sub     = sanitize_key((string) ($_GET['subscribe'] ?? ''));
$logo_id = function_exists('justccell_brand_logo_id') ? justccell_brand_logo_id() : 0;
$locs    = get_nav_menu_locations();
$footer_menu_id  = (int) ($locs['footer'] ?? 0);
$primary_menu_id = (int) ($locs['primary'] ?? 0);
$use_footer_menu = $footer_menu_id > 0 && $footer_menu_id !== $primary_menu_id;
$use_legal_menu  = !empty($locs['legal']);
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
            <div class="foot_t_r">
                <?php if ($use_footer_menu) : ?>
                    <?php
                    wp_nav_menu([
                        'theme_location' => 'footer',
                        'container'      => false,
                        'menu_class'     => 'foot_ul',
                        'fallback_cb'    => false,
                    ]);
                    ?>
                <?php else : ?>
                    <?php foreach ($columns as $col) : ?>
                        <div class="foot_ul">
                            <a class="font18" href="<?php echo esc_url((string) $col['url']); ?>"><?php echo esc_html((string) $col['title']); ?></a>
                            <ul>
                                <?php foreach ($col['links'] as $link) : ?>
                                    <li><a href="<?php echo esc_url((string) $link['url']); ?>"><?php echo esc_html((string) $link['title']); ?></a></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
        <div class="foot_b">
            <div class="foot_b_l">
                <?php if ($social !== []) : ?>
                    <div class="foot_b_icon">
                        <?php foreach ($social as $item) : ?>
                            <a href="<?php echo esc_url((string) $item['url']); ?>" rel="noopener noreferrer" target="_blank"><?php echo esc_html((string) $item['label']); ?></a>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
                <p>&copy; <?php echo esc_html((string) gmdate('Y')); ?> <?php echo esc_html(justccell_legal_name()); ?> <?php esc_html_e('All Rights Reserved.', 'justccell'); ?></p>
                <nav class="foot_legal" aria-label="<?php esc_attr_e('Legal', 'justccell'); ?>">
                    <?php
                    if ($use_legal_menu) {
                        wp_nav_menu([
                            'theme_location' => 'legal',
                            'container'      => false,
                            'fallback_cb'    => false,
                        ]);
                    } else {
                        foreach ($legal as $item) {
                            echo '<a href="' . esc_url((string) $item['url']) . '">' . esc_html((string) $item['label']) . '</a>';
                        }
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
