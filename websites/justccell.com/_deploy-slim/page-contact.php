<?php
/**
 * Contact / B2B inquiry — ccell.com Get Samples layout.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();

$status = sanitize_key((string) ($_GET['inquiry'] ?? ''));
$sku    = sanitize_text_field((string) ($_GET['sku'] ?? ''));
?>
<article class="contact-clone">
    <div class="container contact-clone__grid">
        <aside class="contact-clone__aside">
            <p class="s-kicker"><?php esc_html_e('Contact', 'justccell'); ?></p>
            <h1><?php esc_html_e('Get samples and quotes', 'justccell'); ?></h1>
            <p class="contact-clone__lede"><?php esc_html_e('Tell us about your extracts. Samples typically ship in 3–15 days. Use this form for sample hardware, custom finishes, and distributor introductions.', 'justccell'); ?></p>
            <h2><?php esc_html_e('Distributors', 'justccell'); ?></h2>
            <p class="contact-clone__lede"><?php esc_html_e('Looking to carry Justccell hardware? Include your region and channel in the message and we will route you to the right team.', 'justccell'); ?></p>
            <h2><?php esc_html_e('FAQ', 'justccell'); ?></h2>
            <p class="contact-clone__lede"><?php esc_html_e('Justccell devices are inquiry-first. Public storefronts do not show prices or add-to-cart. Quotes follow the extract, volume, and market you are filling for.', 'justccell'); ?></p>
        </aside>
        <div>
            <?php if ($status === 'sent') : ?>
                <p class="form-notice form-notice--success" role="status"><?php esc_html_e('Request received. We will follow up shortly.', 'justccell'); ?></p>
            <?php elseif ($status === 'missing' || $status === 'invalid') : ?>
                <p class="form-notice form-notice--error" role="alert"><?php esc_html_e('Please complete name, email, and country.', 'justccell'); ?></p>
            <?php endif; ?>
            <?php get_template_part('template-parts/inquiry/form', null, ['sku' => $sku]); ?>
        </div>
    </div>
</article>
<?php
get_footer();
