<?php
/**
 * Single blog post — featured image after title + reading progress.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$blog_url = get_permalink((int) get_option('page_for_posts')) ?: home_url('/blog/');
?>
<div class="bc-read-progress" data-bc-read-progress aria-hidden="true">
    <div class="bc-read-progress__bar" data-bc-read-bar></div>
</div>
<main class="bc-main bc-blog bc-blog--single">
    <?php while (have_posts()) : the_post(); ?>
        <article <?php post_class('bc-article'); ?> data-bc-article>
            <header class="bc-article__hero">
                <div class="bc-container bc-narrow">
                    <p class="bc-breadcrumb">
                        <a href="<?php echo esc_url($blog_url); ?>"><?php esc_html_e('← Blog', 'backlinkcrypto'); ?></a>
                    </p>
                    <p class="bc-eyebrow">
                        <?php
                        $cats = get_the_category();
                        echo esc_html($cats ? $cats[0]->name : __('Insights', 'backlinkcrypto'));
                        ?>
                    </p>
                    <h1><?php the_title(); ?></h1>
                    <div class="bc-article__meta">
                        <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
                        <span aria-hidden="true">·</span>
                        <span><?php echo esc_html(sprintf(__('%d min read', 'backlinkcrypto'), max(1, (int) ceil(str_word_count(wp_strip_all_tags(get_the_content())) / 200)))); ?></span>
                    </div>
                </div>
            </header>

            <?php if (has_post_thumbnail()) : ?>
                <div class="bc-container bc-narrow">
                    <figure class="bc-article__thumb">
                        <?php
                        the_post_thumbnail('large', [
                            'class'   => 'bc-article__img',
                            'loading' => 'eager',
                            'alt'     => the_title_attribute(['echo' => false]) ?: '',
                        ]);
                        ?>
                    </figure>
                </div>
            <?php endif; ?>

            <div class="bc-container bc-narrow">
                <div class="bc-prose bc-article__body" data-bc-article-body>
                    <?php the_content(); ?>
                </div>

                <footer class="bc-article__footer">
                    <div class="bc-article__cta">
                        <h2><?php esc_html_e('Ready to buy placements?', 'backlinkcrypto'); ?></h2>
                        <p><?php esc_html_e('Filter verified crypto & finance sites by DA, DR, traffic, and language — then checkout in minutes.', 'backlinkcrypto'); ?></p>
                        <a class="bc-btn bc-btn--primary" href="<?php echo esc_url(backlinkcrypto_marketplace_url()); ?>">
                            <?php esc_html_e('Open marketplace', 'backlinkcrypto'); ?>
                        </a>
                    </div>
                    <?php
                    $tags = get_the_tags();
                    if ($tags) :
                        ?>
                        <ul class="bc-article__tags">
                            <?php foreach ($tags as $tag) : ?>
                                <li><a href="<?php echo esc_url(get_tag_link($tag)); ?>"><?php echo esc_html($tag->name); ?></a></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </footer>
            </div>
        </article>
    <?php endwhile; ?>
</main>
<?php
get_footer();
