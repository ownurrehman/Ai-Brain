<?php
/**
 * Single Discover post — article + related list (ccell discover_show).
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$post_id = get_the_ID();
$cats    = get_the_category();
$primary = isset($cats[0]) && $cats[0] instanceof WP_Term ? $cats[0] : null;
$related = new WP_Query([
    'post_type'           => 'post',
    'post_status'         => 'publish',
    'posts_per_page'      => 6,
    'post__not_in'        => $post_id ? [(int) $post_id] : [],
    'category__in'        => $primary instanceof WP_Term ? [(int) $primary->term_id] : [],
    'ignore_sticky_posts' => true,
    'no_found_rows'       => true,
]);
if (!$related->have_posts()) {
    $related = new WP_Query([
        'post_type'           => 'post',
        'post_status'         => 'publish',
        'posts_per_page'      => 6,
        'post__not_in'        => $post_id ? [(int) $post_id] : [],
        'ignore_sticky_posts' => true,
        'no_found_rows'       => true,
    ]);
}
?>
<article <?php post_class('d-clone d-single'); ?>>
    <div class="container">
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--page'); ?>
    </div>

    <div class="d-show container">
        <div class="d-show__main">
            <h1><?php the_title(); ?></h1>
            <span class="d-show__date"><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
            <div class="d-show__body entry-content">
                <?php the_content(); ?>
            </div>
        </div>
        <aside class="d-show__side">
            <h2><?php esc_html_e('Related', 'justccell'); ?></h2>
            <?php if ($related->have_posts()) : ?>
                <?php while ($related->have_posts()) : $related->the_post(); ?>
                    <a class="d-related" href="<?php the_permalink(); ?>">
                        <p><?php the_title(); ?></p>
                    </a>
                <?php endwhile; ?>
                <?php wp_reset_postdata(); ?>
            <?php endif; ?>
            <a class="d-more" href="<?php echo esc_url($primary instanceof WP_Term ? get_term_link($primary) : justccell_discover_url()); ?>">
                <?php esc_html_e('View more', 'justccell'); ?>
            </a>
        </aside>
    </div>
</article>
