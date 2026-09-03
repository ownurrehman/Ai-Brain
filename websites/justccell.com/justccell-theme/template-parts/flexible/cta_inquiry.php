<?php
/**
 * Closing contact CTA (flexible sections).
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$heading = function_exists('get_sub_field') ? trim((string) get_sub_field('heading')) : '';
$lede    = function_exists('get_sub_field') ? trim((string) get_sub_field('lede')) : '';
$label   = function_exists('get_sub_field') ? trim((string) get_sub_field('cta_label')) : '';
$url     = function_exists('get_sub_field') ? trim((string) get_sub_field('cta_url')) : '';

if ($heading === '' && $lede === '' && $label === '') {
    return;
}

if ($url === '') {
    $url = function_exists('justccell_contact_page_url') ? justccell_contact_page_url() : home_url('/contact/');
}
?>
<section class="cta-inquiry">
    <div class="container cta-inquiry__inner">
        <?php if ($heading !== '') : ?>
            <h2 class="cta-inquiry__title"><?php echo esc_html($heading); ?></h2>
        <?php endif; ?>
        <?php if ($lede !== '') : ?>
            <p class="cta-inquiry__lede"><?php echo esc_html($lede); ?></p>
        <?php endif; ?>
        <?php if ($label !== '') : ?>
            <a class="btn btn--primary" href="<?php echo esc_url($url); ?>">
                <?php echo esc_html($label); ?>
            </a>
        <?php endif; ?>
    </div>
</section>
