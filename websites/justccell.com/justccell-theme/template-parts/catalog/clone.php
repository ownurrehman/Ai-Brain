<?php
/**
 * Category grid listing groups.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$cat     = (string) get_query_var('justccell_listing');
$labels  = justccell_product_category_labels();
$title   = $labels[$cat] ?? $cat;
$groups  = justccell_catalog_groups($cat);
$hero    = justccell_listing_hero($cat);
$faq     = justccell_listing_faq($cat);
$faq_heading = __('FAQ', 'justccell');
$faq_heading_tag = 'h2';
if ((int) ($hero['page_id'] ?? 0) > 0 && function_exists('get_field')) {
    $saved_faq = trim((string) get_field('listing_faq_heading', (int) $hero['page_id']));
    if ($saved_faq !== '') {
        $faq_heading = $saved_faq;
    }
    $faq_heading_tag = (string) (get_field('listing_faq_heading_tag', (int) $hero['page_id']) ?: 'h2');
}
$slides  = $hero['slides'];
$heading = $hero['heading'] !== '' ? $hero['heading'] : $title;
$lede    = $hero['lede'];

$card_files = [];
foreach ($groups as $group) {
    foreach ($group['items'] as $item) {
        $meta = justccell_catalog_card_meta($item);
        if ($meta['image'] !== '') {
            $card_files[] = $meta['image'];
        }
    }
}
justccell_ensure_media_files($card_files);

$tabs = function_exists('justccell_storefront_category_labels')
    ? justccell_storefront_category_labels()
    : [
        'all-in-ones' => __('All-In-Ones', 'justccell'),
        'cartridge'   => __('Cartridges', 'justccell'),
        'pod-system'  => __('Pod Systems', 'justccell'),
        'battery'     => __('510 Batteries', 'justccell'),
    ];
?>
<article class="c-clone">
    <header class="c-hero"<?php echo count($slides) > 1 ? ' data-banners' : ''; ?>>
        <div class="c-hero__track" data-banner-track>
            <?php foreach ($slides as $i => $slide) : ?>
                <?php
                $tag   = $slide['url'] !== '' ? 'a' : 'div';
                $href  = $slide['url'] !== '' ? ' href="' . esc_url($slide['url']) . '"' : '';
                $class = 'c-hero__slide' . ($i === 0 ? ' is-on' : '');
                ?>
                <<?php echo $tag; ?> class="<?php echo esc_attr($class); ?>"<?php echo $href; ?>>
                    <?php
                    echo wp_get_attachment_image((int) $slide['desktop_id'], 'full', false, [
                        'class'         => 'c-hero__desk',
                        'alt'           => $heading,
                        'fetchpriority' => $i === 0 ? 'high' : 'low',
                    ]);
                    echo wp_get_attachment_image((int) $slide['mobile_id'], 'full', false, [
                        'class'   => 'c-hero__mobile',
                        'alt'     => $heading,
                        'loading' => $i === 0 ? null : 'lazy',
                    ]);
                    ?>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
        <div class="c-hero__txt">
            <?php justccell_echo_heading($heading, (string) (function_exists('get_field') && $hero['page_id'] > 0 ? (get_field('listing_heading_tag', $hero['page_id']) ?: 'h1') : 'h1')); ?>
            <?php if ($lede !== '') : ?>
                <p><?php echo esc_html($lede); ?></p>
            <?php endif; ?>
        </div>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero p-crumbs'); ?>
        <?php if (count($slides) > 1) : ?>
            <div class="h-banner__dots" data-banner-dots></div>
        <?php endif; ?>
    </header>

    <nav class="c-tabs" aria-label="<?php esc_attr_e('Product categories', 'justccell'); ?>">
        <?php foreach ($tabs as $slug => $label) : ?>
            <a class="<?php echo $slug === $cat ? 'is-on' : ''; ?>" href="<?php echo esc_url(justccell_category_url($slug)); ?>">
                <?php echo esc_html($label); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <div class="c-list">
        <?php foreach ($groups as $group) : ?>
            <section class="c-group"<?php echo ($group['title'] ?? '') !== '' ? ' id="' . esc_attr(justccell_group_anchor((string) $group['title'])) . '"' : ''; ?>>
                <div class="container">
                    <?php if (($group['title'] ?? '') !== '') : ?>
                        <div class="c-group__head">
                            <h2><?php echo esc_html((string) $group['title']); ?></h2>
                            <i></i>
                            <?php if (($group['copy'] ?? '') !== '') : ?>
                                <p><?php echo esc_html((string) $group['copy']); ?></p>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    <div class="c-grid">
                        <?php foreach ($group['items'] as $item) : ?>
                            <?php $meta = justccell_catalog_card_meta($item); ?>
                            <a class="c-card" href="<?php echo esc_url(justccell_item_url($item)); ?>">
                                <div class="c-card__img">
                                    <?php
                                    if ((int) ($meta['image_id'] ?? 0) > 0) {
                                        echo wp_get_attachment_image((int) $meta['image_id'], 'full', false, [
                                            'alt'     => (string) $item['name'],
                                            'width'   => 420,
                                            'height'  => 420,
                                            'loading' => 'lazy',
                                        ]);
                                    } else {
                                        echo justccell_media_img($meta['image'], [
                                            'alt'     => (string) $item['name'],
                                            'width'   => 420,
                                            'height'  => 420,
                                            'loading' => 'lazy',
                                        ]);
                                    }
                                    ?>
                                </div>
                                <div class="c-card__copy">
                                    <h3><?php echo esc_html((string) $item['name']); ?></h3>
                                    <?php if ($meta['tagline'] !== '') : ?>
                                        <p><?php echo esc_html($meta['tagline']); ?></p>
                                    <?php endif; ?>
                                    <?php if ($meta['capacity'] !== '') : ?>
                                        <span><?php echo esc_html($meta['capacity']); ?></span>
                                    <?php endif; ?>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>

    <?php if ($faq !== []) : ?>
        <section class="c-faq">
            <div class="container">
                <?php justccell_echo_heading($faq_heading, $faq_heading_tag); ?>
                <?php foreach ($faq as $i => $row) : ?>
                    <details class="c-faq__item"<?php echo $i === 0 ? ' open' : ''; ?>>
                        <summary><?php echo esc_html($row['q']); ?></summary>
                        <p><?php echo esc_html($row['a']); ?></p>
                    </details>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</article>
