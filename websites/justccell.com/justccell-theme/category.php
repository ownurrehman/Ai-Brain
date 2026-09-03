<?php
/**
 * Post category archive — Guides, News, Blogs.
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
get_template_part('template-parts/discover/archive');
get_footer();
