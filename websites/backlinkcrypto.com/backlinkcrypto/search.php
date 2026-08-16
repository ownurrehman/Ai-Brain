<?php
/**
 * Search results.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();
?>
<main class="bc-main bc-blog">
    <section class="bc-blog-hero bc-blog-hero--compact">
        <div class="bc-container">
            <p class="bc-eyebrow"><?php esc_html_e('Search', 'backlinkcrypto'); ?></p>
            <h1>
                <?php
                printf(
                    /* translators: %s: search query */
                    esc_html__('Results for “%s”', 'backlinkcrypto'),
                    esc_html(get_search_query())
                );
                ?>
            </h1>
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
                <nav class="bc-pagination">
                    <?php the_posts_pagination(); ?>
                </nav>
            <?php else : ?>
                <div class="bc-empty is-visible">
                    <p><?php esc_html_e('No matches. Try a different keyword or browse the marketplace.', 'backlinkcrypto'); ?></p>
                    <p><a class="bc-btn bc-btn--primary" href="<?php echo esc_url(backlinkcrypto_marketplace_url()); ?>"><?php esc_html_e('Marketplace', 'backlinkcrypto'); ?></a></p>
                </div>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php
get_footer();
