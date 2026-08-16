<?php
/**
 * Placement / publisher proof gallery.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$entries = function_exists('backlinkcrypto_gallery_entries') ? backlinkcrypto_gallery_entries(9) : [];
if ($entries === []) {
    return;
}
$has_live = false;
foreach ($entries as $e) {
    if (($e['source'] ?? '') === 'live') {
        $has_live = true;
        break;
    }
}
$policies = home_url('/policies/');
$market   = function_exists('backlinkcrypto_marketplace_url') ? backlinkcrypto_marketplace_url() : home_url('/marketplace/');
?>

<section class="bc-gallery" id="bc-gallery" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head">
            <p class="bc-eyebrow"><?php esc_html_e('Proof', 'backlinkcrypto'); ?></p>
            <h2><?php esc_html_e('Live placements & publishers', 'backlinkcrypto'); ?></h2>
            <p>
                <?php
                echo $has_live
                    ? esc_html__('Opted-in live guest posts from fulfilled orders — plus catalog publishers you can buy today.', 'backlinkcrypto')
                    : esc_html__('Publishers from our live catalog. Client live URLs appear here when buyers opt in after delivery.', 'backlinkcrypto');
                ?>
            </p>
        </div>
        <div class="bc-gallery__grid">
            <?php foreach ($entries as $row) :
                $is_live = ($row['source'] ?? '') === 'live';
                ?>
                <article class="bc-gallery__card<?php echo $is_live ? ' is-live' : ''; ?>">
                    <div class="bc-gallery__top">
                        <span class="bc-tag<?php echo $is_live ? ' bc-tag--ok' : ''; ?>">
                            <?php echo esc_html($row['label']); ?>
                        </span>
                        <?php if ($row['dr'] !== '') : ?>
                            <span class="bc-pill">DR <?php echo esc_html($row['dr']); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="bc-gallery__domain"><?php echo esc_html($row['publisher']); ?></h3>
                    <p class="bc-gallery__niche"><?php echo esc_html($row['niche']); ?></p>
                    <?php if (!empty($row['url'])) : ?>
                        <a class="bc-gallery__link" href="<?php echo esc_url($row['url']); ?>" target="_blank" rel="noopener noreferrer nofollow">
                            <?php echo $is_live
                                ? esc_html__('Open live URL', 'backlinkcrypto')
                                : esc_html__('Visit publisher', 'backlinkcrypto'); ?>
                        </a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        </div>
        <p class="bc-gallery__foot">
            <a href="<?php echo esc_url($market); ?>"><?php esc_html_e('Browse marketplace', 'backlinkcrypto'); ?></a>
            <span aria-hidden="true">·</span>
            <a href="<?php echo esc_url($policies); ?>"><?php esc_html_e('Fulfillment & slot reallocation policy', 'backlinkcrypto'); ?></a>
        </p>
    </div>
</section>
