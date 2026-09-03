<?php
/**
 * Default fallback when no primary menu is assigned.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

function justccell_nav_fallback(): void
{
    $items = [
        '/all-in-ones/' => __('All-In-Ones', 'justccell'),
        '/cartridge/'   => __('Cartridges', 'justccell'),
        '/pod-system/'  => __('Pod Systems', 'justccell'),
        '/battery/'     => __('510 Batteries', 'justccell'),
        '/technology/'  => __('Why Justccell', 'justccell'),
        '/ccell-3-0/' => __('Justccell 3.0', 'justccell'),
        '/solution/'    => __('Solution', 'justccell'),
        '/about/'       => __('About', 'justccell'),
        '/discover/'    => __('Discover', 'justccell'),
        '/contact/'     => __('Contact', 'justccell'),
    ];

    echo '<ul class="nav-list">';
    foreach ($items as $path => $label) {
        echo '<li class="nav-list__item"><a class="nav-list__link" href="' . esc_url(home_url($path)) . '">' . esc_html($label) . '</a></li>';
    }
    echo '</ul>';
}
