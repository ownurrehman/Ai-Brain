<?php
/**
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();

get_template_part('template-parts/page/spotlight', null, [
    'eyebrow'         => '404',
    'eyebrow_sr'      => __('Error 404.', 'justccell'),
    'title'           => __('This page isn’t on Justccell', 'justccell'),
    'lede'            => __('That URL may have moved, or it isn’t public yet. Search the catalogue or browse the hardware below.', 'justccell'),
    'primary_label'   => __('Back home', 'justccell'),
    'primary_url'     => '/',
    'secondary_label' => __('Contact us', 'justccell'),
    'secondary_url'   => '/contact/',
    'show_search'     => true,
    'show_showcase'   => true,
]);

get_footer();
