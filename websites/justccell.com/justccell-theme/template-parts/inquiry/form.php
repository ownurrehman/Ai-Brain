<?php
/**
 * Contact form.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 *
 * @var array{sku?:string} $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$sku     = sanitize_text_field(wp_unslash((string) ($args['sku'] ?? ($_GET['sku'] ?? ''))));
$combo   = sanitize_text_field(wp_unslash((string) ($_GET['combo'] ?? '')));
$variant = sanitize_text_field(wp_unslash((string) ($_GET['variant'] ?? '')));
$qty     = sanitize_text_field(wp_unslash((string) ($_GET['qty'] ?? '')));
$attr_fields = [];
foreach ($_GET as $key => $value) {
    $key = sanitize_key((string) $key);
    if (!str_starts_with($key, 'attr_')) {
        continue;
    }
    $slug = substr($key, 5);
    if ($slug === '') {
        continue;
    }
    $val = sanitize_text_field(wp_unslash(is_array($value) ? (string) ($value[0] ?? '') : (string) $value));
    if ($val === '') {
        continue;
    }
    $attr_fields[$slug] = $val;
    if ($combo === '' && (justccell_is_combination_attribute_name($slug))) {
        $combo = $val;
    }
    if ($variant === '' && justccell_is_colour_attribute_name($slug)) {
        $variant = $val;
    }
}
$notes_label = function_exists('justccell_form_setting')
    ? justccell_form_setting('quote_notes_label')
    : __('Project notes', 'justccell');
$submit_label = function_exists('justccell_form_setting')
    ? justccell_form_setting('quote_submit_label')
    : __('Send message', 'justccell');
?>
<form class="inquiry-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="justccell_inquiry">
    <?php wp_nonce_field('justccell_inquiry', 'justccell_inquiry_nonce'); ?>
    <div class="visually-hidden" aria-hidden="true">
        <label><?php esc_html_e('Company website', 'justccell'); ?>
            <input type="text" name="company_website" value="" tabindex="-1" autocomplete="off">
        </label>
    </div>

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
    <?php if ($attr_fields !== []) : ?>
        <div class="inquiry-form__row">
            <?php foreach ($attr_fields as $slug => $val) : ?>
                <label class="inquiry-form__field">
                    <span><?php echo esc_html(ucwords(str_replace(['-', '_'], ' ', $slug))); ?></span>
                    <input type="text" name="attr[<?php echo esc_attr($slug); ?>]" value="<?php echo esc_attr($val); ?>">
                </label>
            <?php endforeach; ?>
        </div>
        <input type="hidden" name="combo" value="<?php echo esc_attr($combo); ?>">
        <input type="hidden" name="variant" value="<?php echo esc_attr($variant); ?>">
    <?php else : ?>
        <div class="inquiry-form__row">
            <label class="inquiry-form__field">
                <span><?php esc_html_e('Combination', 'justccell'); ?></span>
                <input type="text" name="combo" value="<?php echo esc_attr($combo); ?>">
            </label>
            <label class="inquiry-form__field">
                <span><?php esc_html_e('Colour / option', 'justccell'); ?></span>
                <input type="text" name="variant" value="<?php echo esc_attr($variant); ?>">
            </label>
        </div>
    <?php endif; ?>
    <label class="inquiry-form__field">
        <span><?php esc_html_e('Quantity', 'justccell'); ?></span>
        <input type="text" name="qty" value="<?php echo esc_attr($qty); ?>">
    </label>
    <label class="inquiry-form__field">
        <span><?php echo esc_html($notes_label); ?></span>
        <textarea name="message" rows="5"></textarea>
    </label>
    <button class="btn btn--primary" type="submit"><?php echo esc_html($submit_label); ?></button>
</form>
