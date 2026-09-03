<?php
/**
 * Admin laser engraving UI — safe-zone mapper + ACF layout CSS.
 *
 * Product edit: visual mapper syncs safe_zone_coordinates (640×640 Fabric space).
 * ACF visibility: product group only on product edit; category defaults are programmatic-only.
 *
 * Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Whether the current admin screen is a WooCommerce product editor.
 */
function justccell_admin_laser_zone_is_product_screen(): bool
{
    if (!is_admin() || !function_exists('get_current_screen')) {
        return false;
    }

    $screen = get_current_screen();
    if (!$screen instanceof WP_Screen) {
        return false;
    }

    return $screen->base === 'post' && $screen->post_type === 'product';
}

/**
 * Hide category laser defaults from all admin screens.
 * Term meta remains readable via get_field() for product-level fallbacks (see justccell_laser_config).
 */
add_filter('acf/load_field_group', static function ($field_group) {
    if (!is_array($field_group)) {
        return $field_group;
    }

    $key = $field_group['key'] ?? '';

    if ($key === 'group_jc_laser_engraving_cat') {
        return false;
    }

    if ($key === 'group_jc_laser_engraving' && !justccell_admin_laser_zone_is_product_screen()) {
        return false;
    }

    return $field_group;
}, 20);

/**
 * Enqueue mapper + product ACF admin layout assets (product edit only).
 */
function justccell_admin_laser_zone_enqueue(string $hook_suffix): void
{
    unset($hook_suffix);

    if (!justccell_admin_laser_zone_is_product_screen()) {
        return;
    }

    $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1.0.0';
    $uri = defined('JUSTCCELL_URI') ? JUSTCCELL_URI : get_template_directory_uri();

    wp_enqueue_style(
        'justccell-admin-laser-acf',
        $uri . '/assets/css/admin-laser-acf.css',
        [],
        $ver
    );

    wp_enqueue_style(
        'justccell-admin-product-acf',
        $uri . '/assets/css/admin-product-acf.css',
        [],
        $ver
    );

    if (function_exists('wp_enqueue_media')) {
        wp_enqueue_media();
    }

    wp_enqueue_script('jquery-ui-draggable');
    wp_enqueue_script('jquery-ui-resizable');

    wp_enqueue_style(
        'justccell-admin-laser-zone',
        $uri . '/assets/css/admin-laser-zone.css',
        ['justccell-admin-laser-acf'],
        $ver
    );

    wp_enqueue_script(
        'justccell-admin-laser-zone',
        $uri . '/assets/js/admin-laser-zone.js',
        [
            'jquery',
            'jquery-ui-draggable',
            'jquery-ui-resizable',
        ],
        $ver,
        true
    );

    wp_localize_script(
        'justccell-admin-laser-zone',
        'JustccellLaserZoneAdmin',
        [
            'canvasWidth'  => 640,
            'canvasHeight' => 640,
            'fieldKeys'    => [
                'enable'   => 'field_jc_laser_enable',
                'canvasBg' => 'field_jc_laser_canvas_bg',
                'zones'    => 'field_jc_laser_zones',
                'zoneX'    => 'field_jc_laser_zone_x',
                'zoneY'    => 'field_jc_laser_zone_y',
                'zoneW'    => 'field_jc_laser_zone_w',
                'zoneH'    => 'field_jc_laser_zone_h',
            ],
            'fieldNames'   => [
                'canvasBg' => 'canvas_background_image',
                'zones'    => 'safe_zone_coordinates',
            ],
            'i18n'         => [
                'title'       => __('Safe zone mapper', 'justccell'),
                'hint'        => __('Drag and resize the red box on the plate. Coordinates sync to the Fabric canvas (640×640) used on the product page.', 'justccell'),
                'noImage'     => __('Select a canvas background image to map the safe zone.', 'justccell'),
                'readout'     => __('Canvas coords', 'justccell'),
                'nativeHint'  => __('Plate (native)', 'justccell'),
                'ensureRow'   => __('Add safe zone', 'justccell'),
            ],
        ]
    );
}
add_action('admin_enqueue_scripts', 'justccell_admin_laser_zone_enqueue', 40);
