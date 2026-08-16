<?php
/**
 * Technology / innovation showcase.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$heading = function_exists('get_sub_field') ? (string) get_sub_field('heading') : '';
$body    = function_exists('get_sub_field') ? (string) get_sub_field('body') : '';
$items   = function_exists('get_sub_field') ? get_sub_field('items') : [];

if ($heading === '') {
    $heading = __('Technology & manufacturing', 'justccell');
}
if ($body === '') {
    $body = __('Ceramic heating, clog-resistant airflow, and filling systems designed for commercial production.', 'justccell');
}
if (!is_array($items) || $items === []) {
    $items = [
        ['title' => __('Ceramic heating', 'justccell'), 'text' => __('Stable temperature delivery for live resin, distillate, and rosin.', 'justccell')],
        ['title' => __('Filling & capping', 'justccell'), 'text' => __('Production-ready filling paths that keep hardware consistent at scale.', 'justccell')],
        ['title' => __('Safety research', 'justccell'), 'text' => __('Materials and lock systems built for regulated cannabis markets.', 'justccell')],
    ];
}
?>
<section class="tech-showcase">
    <div class="container">
        <h2 class="section-title"><?php echo esc_html($heading); ?></h2>
        <p class="section-lede"><?php echo esc_html($body); ?></p>
        <ul class="tech-showcase__grid" role="list">
            <?php foreach ($items as $item) : ?>
                <li class="tech-card">
                    <h3 class="tech-card__title"><?php echo esc_html((string) ($item['title'] ?? '')); ?></h3>
                    <p class="tech-card__text"><?php echo esc_html((string) ($item['text'] ?? '')); ?></p>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>
