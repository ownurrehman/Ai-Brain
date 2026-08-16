<?php
/**
 * Blog index (Posts page).
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();
?>
<main class="bc-main bc-blog">
    <section class="bc-blog-hero">
        <div class="bc-container">
            <p class="bc-eyebrow"><?php esc_html_e('Insights', 'backlinkcrypto'); ?></p>
            <h1><?php esc_html_e('Crypto backlinks blog', 'backlinkcrypto'); ?></h1>
            <p class="bc-blog-hero__lead">
                <?php esc_html_e('Practical guides on guest posts, DA/DR, link building strategy, and buying placements the smart way.', 'backlinkcrypto'); ?>
            </p>
            <a class="bc-btn bc-btn--primary bc-btn--compact" href="<?php echo esc_url(backlinkcrypto_marketplace_url()); ?>">
                <?php esc_html_e('Browse marketplace', 'backlinkcrypto'); ?>
            </a>
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
                <nav class="bc-pagination" aria-label="<?php esc_attr_e('Blog pagination', 'backlinkcrypto'); ?>">
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
                    <p><?php esc_html_e('No articles yet — check back soon.', 'backlinkcrypto'); ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
