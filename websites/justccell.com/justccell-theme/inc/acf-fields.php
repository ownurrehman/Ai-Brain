<?php
/**
 * ACF Pro field groups — 1:1 with front-end sections.
 * Location: real Pages / Products. Template dropdown stays Default.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    foreach (
        [
            'justccell_register_acf_header_menu',
            'justccell_register_acf_storefront',
        ] as $register
    ) {
        if (function_exists($register)) {
            $register();
        }
    }
});

// group_jc_product_clone — Local JSON + DB only (Phase 3 Batch 4).

function justccell_register_acf_header_menu(): void
{
    if (function_exists('acf_add_options_sub_page')) {
        acf_add_options_sub_page([
            'page_title'  => __('Header', 'justccell'),
            'menu_title'  => __('Header', 'justccell'),
            'parent_slug' => 'justccell',
            'menu_slug'   => 'justccell-header',
            'capability'  => 'edit_theme_options',
        ]);
    }

    // group_jc_header_options — Local JSON + DB only (Phase 3 Batch 1).
    // group_jc_header_menu_item — Local JSON + DB only (Phase 3 Batch 2).
}

function justccell_register_acf_storefront(): void
{
    if (function_exists('acf_add_options_sub_page')) {
        acf_add_options_sub_page([
            'page_title'  => __('Storefront', 'justccell'),
            'menu_title'  => __('Storefront', 'justccell'),
            'parent_slug' => 'justccell',
            'menu_slug'   => 'justccell-storefront',
            'capability'  => 'edit_theme_options',
        ]);
    }

    // group_jc_storefront — Local JSON + DB only (Phase 3 Batch 1).
}
