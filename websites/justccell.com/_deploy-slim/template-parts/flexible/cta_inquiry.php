<?php
/**
 * Closing inquiry CTA.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$heading = function_exists('get_sub_field') ? (string) get_sub_field('heading') : '';
$lede    = function_exists('get_sub_field') ? (string) get_sub_field('lede') : '';
if ($heading === '') {
    $heading = __('Get samples and quotes', 'justccell');
}
if ($lede === '') {
    $lede = __('Test your extracts with Justccell hardware. Samples delivered in 3–15 days.', 'justccell');
}
?>
<section class="cta-inquiry">
    <div class="container cta-inquiry__inner">
        <h2 class="cta-inquiry__title"><?php echo esc_html($heading); ?></h2>
        <p class="cta-inquiry__lede"><?php echo esc_html($lede); ?></p>
        <a class="btn btn--primary" href="<?php echo esc_url(justccell_inquiry_url()); ?>">
            <?php esc_html_e('Start an inquiry', 'justccell'); ?>
        </a>
    </div>
</section>
