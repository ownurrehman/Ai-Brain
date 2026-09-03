<?php
/**
 * Spain / Switzerland store landing. UK remains the order catalogue.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 *
 * @var array{landing?:array<string,mixed>} $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$args    = is_array($args ?? null) ? $args : [];
$landing = is_array($args['landing'] ?? null) ? $args['landing'] : justccell_current_store_landing();
if (!is_array($landing) || empty($landing['enabled'])) {
    return;
}

$image_id = (int) ($landing['image_id'] ?? 0);
?>
<article class="s-clone s-landing">
    <header class="s-hero">
        <div class="container">
            <?php if ((string) ($landing['kicker'] ?? '') !== '') : ?>
                <p class="s-kicker"><?php echo esc_html((string) $landing['kicker']); ?></p>
            <?php endif; ?>
            <?php justccell_echo_heading((string) ($landing['title'] ?? get_bloginfo('name')), (string) ($landing['title_tag'] ?? 'h1')); ?>
            <?php if ((string) ($landing['lede'] ?? '') !== '') : ?>
                <p class="s-lede"><?php echo esc_html((string) $landing['lede']); ?></p>
            <?php endif; ?>
            <?php if ((string) ($landing['cta_label'] ?? '') !== '') : ?>
            <p class="s-landing__actions">
                <a class="btn btn--primary" href="<?php echo esc_url((string) ($landing['cta_url'] ?? justccell_order_store_home_url())); ?>">
                    <?php echo esc_html((string) $landing['cta_label']); ?>
                </a>
            </p>
            <?php endif; ?>
        </div>
    </header>
    <?php if ($image_id > 0) : ?>
        <figure class="s-figure">
            <?php
            echo wp_get_attachment_image($image_id, 'full', false, [
                'alt'     => (string) ($landing['title'] ?? ''),
                'width'   => 1600,
                'height'  => 900,
                'loading' => 'eager',
            ]);
            ?>
        </figure>
    <?php endif; ?>
    <section class="s-cta">
        <div class="container">
            <?php justccell_echo_heading(
                (string) ($landing['note_heading'] ?? __('Orders run through the UK site', 'justccell')),
                (string) ($landing['note_heading_tag'] ?? 'h2')
            ); ?>
            <p><?php echo esc_html((string) ($landing['note_copy'] ?? __('justccell.com is the catalogue where customers request wholesale. This page is the Spain or Switzerland landing — edit it under Justccell → Storefront.', 'justccell'))); ?></p>
        </div>
    </section>
</article>
