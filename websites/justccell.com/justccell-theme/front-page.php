<?php
/**
 * Homepage clone.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();
$landing = function_exists('justccell_current_store_landing') ? justccell_current_store_landing() : null;
if (is_array($landing) && !empty($landing['enabled'])) {
    get_template_part('template-parts/home/store-landing', null, ['landing' => $landing]);
} else {
    get_template_part('template-parts/home/clone');
}
get_footer();
