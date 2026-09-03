<?php
/**
 * Generic page — brand/contact via ACF, else Gutenberg + flexible.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$slug = (string) get_post_field('post_name', get_queried_object_id());
$kind = function_exists('justccell_page_layout_kind')
    ? justccell_page_layout_kind((int) get_queried_object_id())
    : '';

if ($kind !== '' && function_exists('justccell_render_page_layout')) {
    justccell_render_page_layout($kind);
    return;
}

if (
    class_exists('WooCommerce')
    && (
        (function_exists('is_cart') && is_cart())
        || (function_exists('is_checkout') && is_checkout())
        || (function_exists('is_account_page') && is_account_page())
    )
) {
    require JUSTCCELL_DIR . '/commerce-shell.php';
    return;
}

get_header();

if ($slug === 'discover') {
    get_template_part('template-parts/discover/archive');
    get_footer();
    return;
}

if ($slug === 'contact') {
    get_template_part('template-parts/page/contact');
    get_footer();
    return;
}

if (function_exists('justccell_is_brand_page_slug') && justccell_is_brand_page_slug($slug)) {
    get_template_part('template-parts/page/brand');
    get_footer();
    return;
}

while (have_posts()) {
    the_post();
    $is_legal = function_exists('justccell_is_legal_page_slug') && justccell_is_legal_page_slug($slug);
    ?>
    <article <?php post_class('page-article container' . ($is_legal ? ' page-article--legal' : '')); ?>>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--page'); ?>
        <header class="page-article__header">
            <h1 class="page-article__title"><?php the_title(); ?></h1>
        </header>
        <div class="page-article__content entry-content">
            <?php the_content(); ?>
        </div>
        <?php
        if (!$is_legal) {
            justccell_render_flexible_sections();
        }
        ?>
    </article>
    <?php
}

get_footer();
