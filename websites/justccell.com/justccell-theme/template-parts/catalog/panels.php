<?php
/**
 * Switchable catalog tab panels (all categories pre-rendered).
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$tabs = $args['tabs'] ?? [];
if (!is_array($tabs) || $tabs === []) {
    return;
}

$card_files = [];
foreach ($tabs as $tab) {
    if (!is_array($tab)) {
        continue;
    }
    $tab_page_id = (int) ($tab['id'] ?? 0);
    if ($tab_page_id < 1 || !function_exists('justccell_listing_catalog_panel_categories')) {
        continue;
    }
    foreach (justccell_listing_catalog_panel_categories($tab_page_id) as $cat_slug) {
        foreach (justccell_catalog_groups($cat_slug) as $group) {
            foreach ($group['items'] as $item) {
                $meta = justccell_catalog_card_meta($item);
                if ($meta['image'] !== '') {
                    $card_files[] = $meta['image'];
                }
            }
        }
    }
}
justccell_ensure_media_files($card_files);
?>
<div class="c-list c-list--panels" data-catalog-panels>
    <?php foreach ($tabs as $tab) : ?>
        <?php
        if (!is_array($tab)) {
            continue;
        }
        $panel_key = (string) ($tab['slug'] ?? '');
        if ($panel_key === '') {
            continue;
        }
        $tab_page_id = (int) ($tab['id'] ?? 0);
        $is_active   = !empty($tab['is_active']);
        $categories  = $tab_page_id > 0 && function_exists('justccell_listing_catalog_panel_categories')
            ? justccell_listing_catalog_panel_categories($tab_page_id)
            : [];
        ?>
        <div
            class="c-catalog-panel<?php echo $is_active ? ' is-on' : ''; ?>"
            data-catalog-panel="<?php echo esc_attr($panel_key); ?>"
            role="tabpanel"
            id="<?php echo esc_attr('catalog-panel-' . $panel_key); ?>"
            aria-labelledby="<?php echo esc_attr('catalog-tab-' . $panel_key); ?>"
            <?php echo $is_active ? '' : ' hidden'; ?>
        >
            <?php foreach ($categories as $cat_slug) : ?>
                <?php
                get_template_part('template-parts/catalog/category-grid', null, [
                    'category' => $cat_slug,
                ]);
                ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>
</div>
