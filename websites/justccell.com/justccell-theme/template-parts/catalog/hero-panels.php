<?php
/**
 * Switchable catalog hero panels (one hero per tab, pre-rendered from ACF).
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
?>
<div class="c-hero-stack" data-catalog-heroes>
    <?php foreach ($tabs as $tab) : ?>
        <?php
        if (!is_array($tab)) {
            continue;
        }
        $panel_key = (string) ($tab['slug'] ?? '');
        $tab_page_id = (int) ($tab['id'] ?? 0);
        if ($panel_key === '' || $tab_page_id < 1) {
            continue;
        }
        $hero = function_exists('justccell_listing_hero_for_page')
            ? justccell_listing_hero_for_page($tab_page_id)
            : ['heading' => '', 'lede' => '', 'page_id' => $tab_page_id, 'slides' => []];
        get_template_part('template-parts/catalog/hero', null, [
            'hero'      => $hero,
            'panel_key' => $panel_key,
            'is_active' => !empty($tab['is_active']),
        ]);
        ?>
    <?php endforeach; ?>
</div>
