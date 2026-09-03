<?php
/**
 * Contact page inquiry form.
 *
 * @package Justccell
 *
 * @var array{sku?:string} $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$sku = sanitize_text_field(wp_unslash((string) ($args['sku'] ?? ($_GET['sku'] ?? ''))));
$countries = function_exists('justccell_form_option_lines')
    ? justccell_form_option_lines('country_options')
    : [__('United Kingdom', 'justccell'), __('Spain', 'justccell'), __('Switzerland', 'justccell'), __('Others', 'justccell')];
$sources = function_exists('justccell_form_option_lines')
    ? justccell_form_option_lines('source_options')
    : [__('Industry Events and Trade Shows', 'justccell'), __('Search Engines', 'justccell'), __('Others', 'justccell')];
$setting = static fn (string $name, string $fallback): string => function_exists('justccell_form_setting')
    ? justccell_form_setting($name)
    : $fallback;
?>
<form class="c-form" method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
    <input type="hidden" name="action" value="justccell_inquiry">
    <input type="hidden" name="form_layout" value="contact">
    <input type="hidden" name="account_type" value="b2b">
    <input type="hidden" name="sku" value="<?php echo esc_attr($sku); ?>">
    <?php wp_nonce_field('justccell_inquiry', 'justccell_inquiry_nonce'); ?>
    <div class="visually-hidden" aria-hidden="true">
        <label>
            <input type="text" name="company_website" value="" tabindex="-1" autocomplete="off">
        </label>
    </div>

    <div class="c-form__inp">
        <input type="text" name="first_name" required placeholder="<?php echo esc_attr($setting('first_name_placeholder', __('First Name*', 'justccell'))); ?>" autocomplete="given-name">
    </div>
    <div class="c-form__inp">
        <input type="text" name="last_name" required placeholder="<?php echo esc_attr($setting('last_name_placeholder', __('Last Name*', 'justccell'))); ?>" autocomplete="family-name">
    </div>
    <div class="c-form__inp">
        <input type="email" name="email" required placeholder="<?php echo esc_attr($setting('email_placeholder', __('Email*', 'justccell'))); ?>" autocomplete="email">
    </div>
    <div class="c-form__inp">
        <input type="text" name="phone" placeholder="<?php echo esc_attr($setting('phone_placeholder', __('Phone', 'justccell'))); ?>" autocomplete="tel">
    </div>
    <div class="c-form__inp">
        <select name="country" required>
            <option value=""><?php echo esc_html($setting('country_placeholder', __('Country*', 'justccell'))); ?></option>
            <?php foreach ($countries as $country) : ?>
                <option value="<?php echo esc_attr($country); ?>"><?php echo esc_html($country); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="c-form__inp">
        <input type="text" name="city" placeholder="<?php echo esc_attr($setting('city_placeholder', __('City', 'justccell'))); ?>" autocomplete="address-level2">
    </div>
    <div class="c-form__inp">
        <input type="text" name="company" required placeholder="<?php echo esc_attr($setting('company_placeholder', __('Company*', 'justccell'))); ?>" autocomplete="organization">
    </div>
    <div class="c-form__inp">
        <input type="text" name="job_title" required placeholder="<?php echo esc_attr($setting('job_placeholder', __('Job Title*', 'justccell'))); ?>" autocomplete="organization-title">
    </div>
    <div class="c-form__inp c-form__inp--wide">
        <select name="source" required>
            <option value=""><?php echo esc_html($setting('source_placeholder', __('How did you hear about us?*', 'justccell'))); ?></option>
            <?php foreach ($sources as $source) : ?>
                <option value="<?php echo esc_attr($source); ?>"><?php echo esc_html($source); ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="c-form__inp c-form__inp--wide">
        <textarea name="message" rows="4" required placeholder="<?php echo esc_attr($setting('message_placeholder', __('Leave your message*', 'justccell'))); ?>"></textarea>
    </div>
    <button type="submit" class="c-form__submit">
        <span><?php echo esc_html($setting('contact_submit_label', __('SUBMIT', 'justccell'))); ?></span>
    </button>
</form>
