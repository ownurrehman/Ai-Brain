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

$page_id = (int) ($hero['page_id'] ?? 0);
$tabs    = function_exists('justccell_listing_catalog_tabs')
    ? justccell_listing_catalog_tabs($page_id > 0 ? $page_id : (int) get_queried_object_id(), [
        'page_id'     => $page_id > 0 ? $page_id : (int) get_queried_object_id(),
        'active_slug' => $cat,
    ])
    : [];
?>
<article class="c-clone">
    <?php if ($tabs !== []) : ?>
        <?php get_template_part('template-parts/catalog/hero-panels', null, ['tabs' => $tabs]); ?>
    <?php else : ?>
        <?php get_template_part('template-parts/catalog/hero', null, ['hero' => $hero]); ?>
    <?php endif; ?>

    <?php if ($tabs !== []) : ?>
        <?php get_template_part('template-parts/catalog/tabs', null, ['tabs' => $tabs]); ?>
        <?php get_template_part('template-parts/catalog/panels', null, ['tabs' => $tabs]); ?>
    <?php else : ?>
        <div class="c-list">
            <?php
            get_template_part('template-parts/catalog/category-grid', null, [
                'category' => $cat,
            ]);
            ?>
        </div>
    <?php endif; ?>

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
