<?php
/**
 * Sample & quote form.
 *
 * @package Justccell
 *
 * @var array{sku?:string} $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$sku = sanitize_text_field((string) ($args['sku'] ?? ''));
?>
<form class="inquiry-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="justccell_inquiry">
    <?php wp_nonce_field('justccell_inquiry', 'justccell_inquiry_nonce'); ?>

    <fieldset class="inquiry-form__field">
        <legend><?php esc_html_e('Account type', 'justccell'); ?> *</legend>
        <div class="inquiry-form__accounts">
            <label class="inquiry-form__account">
                <input type="radio" name="account_type" value="b2c" required>
                <span><?php esc_html_e('Consumer (B2C)', 'justccell'); ?></span>
            </label>
            <label class="inquiry-form__account">
                <input type="radio" name="account_type" value="b2b">
                <span><?php esc_html_e('Business (B2B)', 'justccell'); ?></span>
            </label>
        </div>
    </fieldset>

    <div class="inquiry-form__row">
        <label class="inquiry-form__field">
            <span><?php esc_html_e('Full name', 'justccell'); ?> *</span>
            <input type="text" name="full_name" required autocomplete="name">
        </label>
        <label class="inquiry-form__field">
            <span><?php esc_html_e('Work email', 'justccell'); ?> *</span>
            <input type="email" name="email" required autocomplete="email">
        </label>
    </div>
    <div class="inquiry-form__row">
        <label class="inquiry-form__field">
            <span><?php esc_html_e('Company', 'justccell'); ?></span>
            <input type="text" name="company" autocomplete="organization" data-b2b-company>
        </label>
        <label class="inquiry-form__field">
            <span><?php esc_html_e('Country of delivery', 'justccell'); ?> *</span>
            <input type="text" name="country" required autocomplete="country-name">
        </label>
    </div>
    <label class="inquiry-form__field">
        <span><?php esc_html_e('VAT number (required for B2B)', 'justccell'); ?></span>
        <input type="text" name="vat_number" autocomplete="off" data-b2b-vat>
    </label>
    <div class="inquiry-form__row">
        <label class="inquiry-form__field">
            <span><?php esc_html_e('Product SKU', 'justccell'); ?></span>
            <input type="text" name="sku" value="<?php echo esc_attr($sku); ?>">
        </label>
        <label class="inquiry-form__field">
            <span><?php esc_html_e('How did you hear about us', 'justccell'); ?></span>
            <input type="text" name="source">
        </label>
    </div>
    <label class="inquiry-form__field">
        <span><?php esc_html_e('Project notes', 'justccell'); ?></span>
        <textarea name="message" rows="5"></textarea>
    </label>
    <button class="btn btn--primary" type="submit"><?php esc_html_e('Get samples & quotes', 'justccell'); ?></button>
</form>
