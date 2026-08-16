<?php
/**
 * Generic page. Gutenberg content + optional ACF sections.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();

while (have_posts()) {
    the_post();
    ?>
    <article <?php post_class('page-article container'); ?>>
        <header class="page-article__header">
            <h1 class="page-article__title"><?php the_title(); ?></h1>
        </header>
        <div class="page-article__content entry-content">
            <?php the_content(); ?>
        </div>
        <?php justccell_render_flexible_sections(); ?>
    </article>
    <?php
}

get_footer();
