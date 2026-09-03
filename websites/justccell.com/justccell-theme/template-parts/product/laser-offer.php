<?php
/**
 * Product-page laser engraving offer. Video from ACF Options / product / Media.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 *
 * @var array{sku?:string,woo_id?:int} $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$sku   = (string) ($args['sku'] ?? '');
$offer = justccell_product_laser_offer($sku, (int) ($args['woo_id'] ?? 0));
if (empty($offer['show'])) {
    return;
}
$has_video = ($offer['video'] ?? '') !== '';
?>
<section class="p-laser" id="laser-engraving">
    <div class="container p-laser__box<?php echo $has_video ? '' : ' p-laser__box--text'; ?>">
        <div class="p-laser__copy">
            <?php justccell_echo_heading((string) $offer['heading'], (string) ($offer['heading_tag'] ?? 'h2')); ?>
            <p><?php echo esc_html((string) $offer['copy']); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url((string) $offer['cta_url']); ?>">
                <?php echo esc_html((string) $offer['cta_label']); ?>
            </a>
        </div>
        <?php if ($has_video) : ?>
        <div class="p-laser__media">
            <video
                class="p-laser__video"
                controls
                muted
                playsinline
                preload="metadata"
                title="<?php echo esc_attr((string) $offer['heading']); ?>"
            >
                <source src="<?php echo esc_url((string) $offer['video']); ?>" type="video/mp4">
            </video>
        </div>
        <?php endif; ?>
    </div>
</section>
