<?php
/**
 * Blog card for index / archive / search.
 * Layout: category → title → featured image → excerpt → meta.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

$cats = get_the_category();
$cat_label = $cats ? $cats[0]->name : __('Insights', 'backlinkcrypto');
$has_thumb = has_post_thumbnail();
?>
<article <?php post_class('bc-blog-card'); ?>>
    <a class="bc-blog-card__link" href="<?php the_permalink(); ?>">
        <div class="bc-blog-card__body">
            <span class="bc-blog-card__cat"><?php echo esc_html($cat_label); ?></span>
            <h2 class="bc-blog-card__title"><?php the_title(); ?></h2>
        </div>
        <?php if ($has_thumb) : ?>
            <div class="bc-blog-card__media">
                <?php
                the_post_thumbnail('medium_large', [
                    'class'   => 'bc-blog-card__img',
                    'loading' => 'lazy',
                    'alt'     => the_title_attribute(['echo' => false]) ?: '',
                ]);
                ?>
            </div>
        <?php else : ?>
            <div class="bc-blog-card__media bc-blog-card__media--placeholder" aria-hidden="true">
                <span></span>
            </div>
        <?php endif; ?>
        <div class="bc-blog-card__body bc-blog-card__body--foot">
            <p class="bc-blog-card__excerpt"><?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?></p>
            <div class="bc-blog-card__meta">
                <time datetime="<?php echo esc_attr(get_the_date(DATE_W3C)); ?>"><?php echo esc_html(get_the_date()); ?></time>
                <span class="bc-blog-card__more"><?php esc_html_e('Read →', 'backlinkcrypto'); ?></span>
            </div>
        </div>
    </a>
</article>
