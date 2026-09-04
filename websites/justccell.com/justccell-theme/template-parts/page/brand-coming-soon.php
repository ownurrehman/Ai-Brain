<?php
/**
 * Coming soon spotlight — title/excerpt from the page, shared 404-style layout.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$spotlight = function_exists('justccell_get_coming_soon_spotlight')
    ? justccell_get_coming_soon_spotlight((int) get_queried_object_id())
    : [];

get_template_part('template-parts/page/spotlight', null, $spotlight);
