<?php
/**
 * Hero banner. Works with ACF or static fallback.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$heading = function_exists('get_sub_field') ? (string) get_sub_field('heading') : '';
$lede    = function_exists('get_sub_field') ? (string) get_sub_field('lede') : '';
$cta     = function_exists('get_sub_field') ? (string) get_sub_field('cta_label') : '';
$image   = function_exists('get_sub_field') ? get_sub_field('background') : null;

if ($heading === '') {
    $heading = __('Devices crafted for cannabis', 'justccell');
}
if ($lede === '') {
    $lede = __('All-in-ones, cartridges, pod systems, and 510 batteries engineered for extracts.', 'justccell');
}
if ($cta === '') {
    $cta = __('Get samples & quotes', 'justccell');
}

$bg_url    = is_array($image) ? (string) ($image['url'] ?? '') : '';
$bg_width  = is_array($image) ? (int) ($image['width'] ?? 1920) : 1920;
$bg_height = is_array($image) ? (int) ($image['height'] ?? 1080) : 1080;
$bg_alt    = is_array($image) ? (string) ($image['alt'] ?? '') : '';
?>
<section class="hero">
    <?php if ($bg_url !== '') : ?>
        <div class="hero__media" aria-hidden="<?php echo $bg_alt === '' ? 'true' : 'false'; ?>">
            <img src="<?php echo esc_url($bg_url); ?>" alt="<?php echo esc_attr($bg_alt); ?>" width="<?php echo esc_attr((string) $bg_width); ?>" height="<?php echo esc_attr((string) $bg_height); ?>" fetchpriority="high">
        </div>
    <?php endif; ?>
    <div class="container hero__inner">
        <h1 class="hero__title"><?php echo esc_html($heading); ?></h1>
        <p class="hero__lede"><?php echo esc_html($lede); ?></p>
        <a class="btn btn--primary" href="<?php echo esc_url(justccell_inquiry_url()); ?>"><?php echo esc_html($cta); ?></a>
    </div>
</section>
