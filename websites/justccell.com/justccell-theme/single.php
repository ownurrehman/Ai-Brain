<?php
/**
 * Single post — Discover article layout.
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

if (have_posts()) {
    while (have_posts()) {
        the_post();
        get_template_part('template-parts/discover/single');
    }
}

get_footer();
