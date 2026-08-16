<?php
/**
 * Template Name: Flexible sections
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();
justccell_render_flexible_sections();
get_footer();
