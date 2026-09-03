<?php
/**
 * Fallback index.
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

if (function_exists('justccell_is_discover_view') && justccell_is_discover_view()) {
    get_template_part('template-parts/discover/archive');
    get_footer();
    return;
}
?>
<section class="container archive-feed">
    <?php if (have_posts()) : ?>
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('archive-feed__item'); ?>>
                <h2 class="archive-feed__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h2>
                <?php the_excerpt(); ?>
            </article>
        <?php endwhile; ?>
        <?php the_posts_pagination(); ?>
    <?php else : ?>
        <p><?php esc_html_e('Nothing found.', 'justccell'); ?></p>
    <?php endif; ?>
</section>
<?php
get_footer();
