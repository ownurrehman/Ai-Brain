<?php
/**
 * Fallback index.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();
?>
<main class="bc-main">
    <div class="bc-container">
        <?php if (have_posts()) : ?>
            <?php while (have_posts()) : the_post(); ?>
                <article <?php post_class('bc-page'); ?>>
                    <h1><?php the_title(); ?></h1>
                    <div class="bc-content"><?php the_content(); ?></div>
                </article>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="bc-empty">
                <p><?php esc_html_e('Nothing found.', 'backlinkcrypto'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php
get_footer();
