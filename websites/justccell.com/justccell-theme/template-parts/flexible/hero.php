<?php
/**
 * Hero banner. Works with ACF or static fallback.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$heading = function_exists('get_sub_field') ? (string) get_sub_field('heading') : '';
$lede    = function_exists('get_sub_field') ? (string) get_sub_field('lede') : '';
$cta     = function_exists('get_sub_field') ? trim((string) get_sub_field('cta_label')) : '';
$cta_url = function_exists('get_sub_field') ? trim((string) get_sub_field('cta_url')) : '';
$image   = function_exists('get_sub_field') ? get_sub_field('background') : null;

if ($heading === '') {
    $heading = __('Devices crafted for cannabis', 'justccell');
}
if ($lede === '') {
    $lede = __('All-in-ones, cartridges, pod systems, and 510 batteries engineered for extracts.', 'justccell');
}
if ($cta_url === '') {
    $cta_url = function_exists('justccell_contact_page_url') ? justccell_contact_page_url() : home_url('/contact/');
}

$bg_id  = is_array($image) ? (int) ($image['ID'] ?? $image['id'] ?? 0) : 0;
$bg_alt = is_array($image) ? (string) ($image['alt'] ?? '') : '';
?>
<section class="hero">
    <?php if ($bg_id > 0) : ?>
        <div class="hero__media" aria-hidden="<?php echo $bg_alt === '' ? 'true' : 'false'; ?>">
            <?php echo wp_get_attachment_image($bg_id, 'full', false, [
                'alt'           => $bg_alt,
                'fetchpriority' => 'high',
            ]); ?>
        </div>
    <?php endif; ?>
    <div class="container hero__inner">
        <h1 class="hero__title"><?php echo esc_html($heading); ?></h1>
        <p class="hero__lede"><?php echo esc_html($lede); ?></p>
        <?php if ($cta !== '') : ?>
        <a class="btn btn--primary" href="<?php echo esc_url($cta_url); ?>"><?php echo esc_html($cta); ?></a>
        <?php endif; ?>
    </div>
</section>
