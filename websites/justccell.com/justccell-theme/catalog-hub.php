<?php
/**
 * Multi-category catalog hub (e.g. /products/).
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$page_id = (int) get_queried_object_id();
if ($page_id < 1) {
    $page_id = (int) get_the_ID();
}

justccell_ensure_media_files(array_column(justccell_catalog(), 'image'));

get_header();
get_template_part('template-parts/catalog/hub', null, ['page_id' => $page_id]);
get_footer();
