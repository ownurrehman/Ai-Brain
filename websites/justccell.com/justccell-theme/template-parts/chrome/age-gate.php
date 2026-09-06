<?php
/**
 * 18+ age verification modal — markup only; visibility handled in age-gate.js.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 *
 * @var array $args
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$settings = is_array($args ?? null) ? $args : [];
if ($settings === [] && function_exists('justccell_age_gate_settings')) {
    $settings = justccell_age_gate_settings();
}

$title         = (string) ($settings['title'] ?? '');
$body_html     = (string) ($settings['body_html'] ?? '');
$confirm_label = (string) ($settings['confirm_label'] ?? '');
$decline_label = (string) ($settings['decline_label'] ?? '');
$decline_url   = (string) ($settings['decline_url'] ?? 'https://www.google.com');
$cookie_days   = max(1, (int) ($settings['cookie_days'] ?? 30));
?>
<div
    id="jc-age-gate"
    class="jc-age-gate"
    hidden
    aria-hidden="true"
    data-cookie-days="<?php echo esc_attr((string) $cookie_days); ?>"
    data-decline-url="<?php echo esc_url($decline_url); ?>"
>
    <div class="jc-age-gate__overlay" aria-hidden="true"></div>
    <div
        class="jc-age-gate__dialog"
        role="dialog"
        aria-modal="true"
        aria-labelledby="jc-age-gate-title"
    >
        <?php if ($title !== '') : ?>
            <h2 id="jc-age-gate-title" class="jc-age-gate__title"><?php echo esc_html($title); ?></h2>
        <?php endif; ?>
        <?php if ($body_html !== '') : ?>
            <div class="jc-age-gate__body"><?php echo wp_kses_post($body_html); ?></div>
        <?php endif; ?>
        <div class="jc-age-gate__actions">
            <button type="button" class="btn btn--primary jc-age-gate__confirm">
                <?php echo esc_html($confirm_label); ?>
            </button>
            <button type="button" class="btn jc-age-gate__decline">
                <?php echo esc_html($decline_label); ?>
            </button>
        </div>
    </div>
</div>
