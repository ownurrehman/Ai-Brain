<?php
/**
 * Category / tag / date archives.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();
?>
<main class="bc-main bc-blog">
    <section class="bc-blog-hero bc-blog-hero--compact">
        <div class="bc-container">
            <p class="bc-eyebrow"><?php esc_html_e('Archive', 'backlinkcrypto'); ?></p>
            <h1><?php the_archive_title(); ?></h1>
            <?php
            $desc = get_the_archive_description();
            if ($desc) {
                echo '<div class="bc-blog-hero__lead">' . wp_kses_post($desc) . '</div>';
            }
            ?>
        </div>
    </section>

    <section class="bc-blog-list">
        <div class="bc-container">
            <?php if (have_posts()) : ?>
                <div class="bc-blog-grid">
                    <?php
                    while (have_posts()) {
                        the_post();
                        get_template_part('template-parts/content', 'blog-card');
                    }
                    ?>
                </div>
                <nav class="bc-pagination" aria-label="<?php esc_attr_e('Archive pagination', 'backlinkcrypto'); ?>">
                    <?php
                    the_posts_pagination([
                        'mid_size'  => 1,
                        'prev_text' => __('← Newer', 'backlinkcrypto'),
                        'next_text' => __('Older →', 'backlinkcrypto'),
                    ]);
                    ?>
                </nav>
            <?php else : ?>
                <div class="bc-empty is-visible">
                    <p><?php esc_html_e('No posts in this archive.', 'backlinkcrypto'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
