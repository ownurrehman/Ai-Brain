<?php
/**
 * Homepage clone.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();
get_template_part('template-parts/home/clone');
get_footer();
