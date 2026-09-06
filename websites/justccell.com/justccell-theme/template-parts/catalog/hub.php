<?php
/**
 * Catalog hub — hero + selected category tabs + product grids.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$page_id = (int) ($args['page_id'] ?? get_queried_object_id());
if ($page_id < 1) {
    $page_id = (int) get_the_ID();
}

$hero       = justccell_listing_hero_for_page($page_id);
$faq        = justccell_listing_faq_for_page($page_id);
$faq_heading = __('FAQ', 'justccell');
$faq_heading_tag = 'h2';
if ($page_id > 0 && function_exists('get_field')) {
    $saved_faq = trim((string) get_field('listing_faq_heading', $page_id));
    if ($saved_faq !== '') {
        $faq_heading = $saved_faq;
    }
    $faq_heading_tag = (string) (get_field('listing_faq_heading_tag', $page_id) ?: 'h2');
}

$tabs = function_exists('justccell_listing_catalog_tabs')
    ? justccell_listing_catalog_tabs($page_id, ['page_id' => $page_id])
    : [];

?>
<article class="c-clone c-clone--hub">
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
            foreach (justccell_listing_page_categories($page_id) as $cat_slug) {
                get_template_part('template-parts/catalog/category-grid', null, [
                    'category' => $cat_slug,
                ]);
            }
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
