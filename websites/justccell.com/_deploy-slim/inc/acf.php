<?php
/**
 * ACF JSON sync + flexible section renderer.
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_filter('acf/settings/save_json', static function (): string {
    return JUSTCCELL_DIR . '/acf-json';
});

add_filter('acf/settings/load_json', static function (array $paths): array {
    $paths[] = JUSTCCELL_DIR . '/acf-json';
    return $paths;
});

function justccell_render_flexible_sections(?int $post_id = null): void
{
    if (!function_exists('have_rows')) {
        return;
    }

    $post_id = $post_id ?? get_the_ID();
    if (!$post_id || !have_rows('page_sections', $post_id)) {
        return;
    }

    while (have_rows('page_sections', $post_id)) {
        the_row();
        $layout = (string) get_row_layout();
        $file   = JUSTCCELL_DIR . '/template-parts/flexible/' . $layout . '.php';
        if (is_readable($file)) {
            get_template_part('template-parts/flexible/' . $layout);
        }
    }
}
