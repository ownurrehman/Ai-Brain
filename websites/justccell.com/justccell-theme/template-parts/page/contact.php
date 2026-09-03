<?php
/**
 * Contact and inquiry page.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$status = sanitize_key((string) ($_GET['inquiry'] ?? ''));
$sku    = sanitize_text_field((string) ($_GET['sku'] ?? ''));
$c      = justccell_get_contact_content();
$page   = justccell_get_contact_page_data();
$faqs   = justccell_contact_faqs_without_samples(is_array($c['faqs'] ?? null) ? $c['faqs'] : []);
$social = is_array($page['social'] ?? null) ? $page['social'] : [];
$emails = is_array($page['emails'] ?? null) ? $page['emails'] : [];
$success_message = function_exists('justccell_form_setting')
    ? justccell_form_setting('success_message')
    : __('Request received. We will follow up shortly.', 'justccell');
$error_message = function_exists('justccell_form_setting')
    ? justccell_form_setting('error_message')
    : __('Please complete all required fields.', 'justccell');
?>
<article class="jc-contact">
    <header class="jc-contact__hero">
        <div class="jc-contact__hero-media">
            <?php justccell_contact_echo_img((int) $page['hero_desktop_id'] ?: (string) $page['hero_desktop'], 'jc-contact__hero-img jc-contact__hero-img--desktop', (string) $page['hero_title'], ['fetchpriority' => 'high', 'loading' => 'eager']); ?>
            <?php justccell_contact_echo_img((int) $page['hero_mobile_id'] ?: (string) $page['hero_mobile'], 'jc-contact__hero-img jc-contact__hero-img--mobile', (string) $page['hero_title'], ['fetchpriority' => 'high', 'loading' => 'eager']); ?>
        </div>
        <div class="jc-contact__hero-copy">
            <?php justccell_echo_heading((string) $page['hero_title'], (string) ($page['hero_title_tag'] ?? 'h1')); ?>
        </div>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero jc-contact__crumbs'); ?>
    </header>

    <section class="jc-contact__main">
        <div class="container2 jc-contact__box">
            <div class="jc-contact__main-grid">
                <div class="jc-contact__left">
                    <div class="jc-contact__brand">
                        <?php if ((int) $page['logo_id'] > 0) : ?>
                            <?php justccell_contact_echo_img((int) $page['logo_id'], 'jc-contact__logo', get_bloginfo('name')); ?>
                        <?php else : ?>
                            <span class="jc-contact__logo-text"><?php echo esc_html(get_bloginfo('name')); ?></span>
                        <?php endif; ?>
                    </div>

                    <div class="jc-contact__info">
                        <?php justccell_echo_heading((string) $page['info_heading'], (string) ($page['info_heading_tag'] ?? 'h2')); ?>
                        <div class="jc-contact__info-list">
                            <?php foreach ($emails as $row) : ?>
                                <p class="jc-contact__line">
                                    <?php if ((string) ($row['label'] ?? '') !== '') : ?>
                                        <span class="jc-contact__info-label"><?php echo esc_html((string) $row['label']); ?></span>
                                    <?php endif; ?>
                                    <a class="jc-contact__info-value" href="<?php echo esc_url('mailto:' . $row['email']); ?>"><?php echo esc_html((string) $row['email']); ?></a>
                                </p>
                            <?php endforeach; ?>
                            <?php if ((string) $page['phone'] !== '') : ?>
                                <p class="jc-contact__line">
                                    <span class="jc-contact__info-label"><?php echo esc_html((string) $page['phone_label']); ?></span>
                                    <a class="jc-contact__info-value" href="<?php echo esc_url('tel:' . preg_replace('/[^0-9+]/', '', (string) $page['phone'])); ?>"><?php echo esc_html((string) $page['phone']); ?></a>
                                </p>
                            <?php endif; ?>
                            <?php
                            $address = trim(preg_replace("/\r\n|\r/", "\n", (string) ($page['address'] ?? '')) ?? '');
                            $address = preg_replace("/\n{2,}/", "\n", $address) ?? $address;
                            $address_lines = $address !== '' ? array_values(array_filter(array_map('trim', explode("\n", $address)))) : [];
                            if ($address_lines !== []) :
                                ?>
                                <p class="jc-contact__line jc-contact__line--address">
                                    <span class="jc-contact__info-label"><?php echo esc_html((string) ($page['address_label'] ?? __('Address:', 'justccell'))); ?></span>
                                    <span class="jc-contact__address">
                                        <?php foreach ($address_lines as $line) : ?>
                                            <span class="jc-contact__address-line"><?php echo esc_html($line); ?></span>
                                        <?php endforeach; ?>
                                    </span>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($social !== []) : ?>
                        <div class="jc-contact__social">
                            <?php justccell_echo_heading((string) $page['follow_heading'], (string) ($page['follow_heading_tag'] ?? 'h2')); ?>
                            <div class="jc-contact__social-links">
                                <?php foreach ($social as $item) : ?>
                                    <?php $network = sanitize_key((string) ($item['network'] ?? 'link')); ?>
                                    <a class="jc-contact__social-link jc-contact__social-link--<?php echo esc_attr($network); ?>" href="<?php echo esc_url((string) $item['url']); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr((string) $item['label']); ?>">
                                        <?php justccell_contact_echo_social_icon($network, (int) ($item['icon_id'] ?? 0), (string) ($item['label'] ?? '')); ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="jc-contact__right">
                    <div class="jc-contact__quote-copy">
                        <?php justccell_echo_heading((string) $page['form_title'], (string) ($page['form_title_tag'] ?? 'h2')); ?>
                        <p><?php echo esc_html((string) $page['form_copy']); ?></p>
                    </div>
                    <div class="jc-contact__form-wrap">
                        <?php if ($status === 'sent') : ?>
                            <p class="form-notice form-notice--success" role="status"><?php echo esc_html($success_message); ?></p>
                        <?php elseif ($status === 'missing' || $status === 'invalid') : ?>
                            <p class="form-notice form-notice--error" role="alert"><?php echo esc_html($error_message); ?></p>
                        <?php endif; ?>
                        <?php get_template_part('template-parts/inquiry/form', 'contact', ['sku' => $sku]); ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php if (!empty($page['distributors'])) : ?>
    <section class="jc-contact__distributors">
        <div class="container2">
            <?php justccell_echo_heading((string) $page['distributors_heading'], (string) ($page['distributors_heading_tag'] ?? 'h2')); ?>
            <div class="jc-contact__distributor-grid">
                <?php foreach ((array) $page['distributors'] as $distributor) : ?>
                    <a class="jc-contact__distributor" href="<?php echo esc_url((string) $distributor['url']); ?>" target="_blank" rel="noopener noreferrer">
                        <?php justccell_contact_echo_img((int) ($distributor['image_id'] ?? 0) ?: (string) ($distributor['image'] ?? ''), 'jc-contact__distributor-img', (string) $distributor['name']); ?>
                        <span><?php echo esc_html((string) $distributor['name']); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if ($faqs !== []) : ?>
        <section class="jc-contact__faq">
            <div class="container2">
                <div class="jc-contact__faq-box">
                    <?php justccell_echo_heading((string) $page['faq_heading'], (string) ($page['faq_heading_tag'] ?? 'h2')); ?>
                    <?php foreach ($faqs as $faq) : ?>
                        <details class="jc-contact__faq-item">
                            <summary><?php echo esc_html((string) ($faq['q'] ?? '')); ?></summary>
                            <p><?php echo nl2br(esc_html((string) ($faq['a'] ?? ''))); ?></p>
                        </details>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>
