<?php
/**
 * About / technology / solution / safety visual clone.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$slug = get_post_field('post_name', get_queried_object_id());
$page = justccell_static_pages()[$slug] ?? null;
if (!is_array($page)) {
    return;
}

$image_key = (string) ($page['image'] ?? '');
if ($image_key !== '') {
    justccell_ensure_media_url($image_key);
}
?>
<article class="s-clone">
    <header class="s-hero">
        <div class="container">
            <p class="s-kicker"><?php echo esc_html((string) ($page['kicker'] ?? get_the_title())); ?></p>
            <h1><?php echo esc_html((string) ($page['title'] ?? get_the_title())); ?></h1>
            <?php if (($page['lede'] ?? '') !== '') : ?>
                <p class="s-lede"><?php echo esc_html((string) $page['lede']); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($image_key !== '') : ?>
        <figure class="s-figure">
            <?php echo justccell_media_img($image_key, [
                'alt'     => (string) ($page['title'] ?? ''),
                'width'   => 1600,
                'height'  => 900,
                'loading' => 'lazy',
            ]); ?>
        </figure>
    <?php endif; ?>

    <?php if (!empty($page['blocks']) && is_array($page['blocks'])) : ?>
        <section class="s-blocks">
            <div class="container s-blocks__grid">
                <?php foreach ($page['blocks'] as $block) : ?>
                    <div class="s-card">
                        <h2><?php echo esc_html((string) ($block['title'] ?? '')); ?></h2>
                        <p><?php echo esc_html((string) ($block['copy'] ?? '')); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($page['timeline']) && is_array($page['timeline'])) : ?>
        <section class="s-timeline">
            <div class="container">
                <h2><?php esc_html_e('Brand history', 'justccell'); ?></h2>
                <ul>
                    <?php foreach ($page['timeline'] as $item) : ?>
                        <li><?php echo esc_html((string) $item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <section class="s-cta">
        <div class="container">
            <h2><?php esc_html_e('Get samples and quotes', 'justccell'); ?></h2>
            <p><?php esc_html_e('Test your extracts with Justccell hardware. Samples typically ship in 3–15 days.', 'justccell'); ?></p>
            <a class="btn btn--primary" href="<?php echo esc_url(justccell_inquiry_url()); ?>">
                <?php esc_html_e('Get samples & quotes', 'justccell'); ?>
            </a>
        </div>
    </section>
</article>
