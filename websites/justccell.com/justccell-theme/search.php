<?php
/**
 * Search results — catalogue + editorial.
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

$query = get_search_query();
$count = (int) $wp_query->found_posts;
?>
<section class="jc-search">
    <div class="container jc-search__inner">
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--page'); ?>
        <header class="jc-search__hero">
            <p class="jc-search__kicker"><?php esc_html_e('Search', 'justccell'); ?></p>
            <h1 class="jc-search__title">
                <?php
                if ($query !== '') {
                    printf(
                        /* translators: %s: search query */
                        esc_html__('Results for “%s”', 'justccell'),
                        esc_html($query)
                    );
                } else {
                    esc_html_e('Search Justccell', 'justccell');
                }
                ?>
            </h1>
            <p class="jc-search__lede">
                <?php
                if ($count > 0) {
                    printf(
                        /* translators: %d: result count */
                        esc_html(_n('%d match', '%d matches', $count, 'justccell')),
                        $count
                    );
                } else {
                    esc_html_e('Nothing matched that search. Try a product name, or browse the catalogue.', 'justccell');
                }
                ?>
            </p>
            <form class="jc-search__form" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                <label class="visually-hidden" for="jc-search-q"><?php esc_html_e('Search', 'justccell'); ?></label>
                <input id="jc-search-q" type="search" name="s" value="<?php echo esc_attr($query); ?>" placeholder="<?php echo esc_attr__('What hardware are you filling?', 'justccell'); ?>">
                <button class="btn btn--primary" type="submit"><?php esc_html_e('Search', 'justccell'); ?></button>
            </form>
        </header>

        <?php if (have_posts()) : ?>
            <ul class="jc-search__list" role="list">
                <?php
                while (have_posts()) {
                    the_post();
                    $is_product = get_post_type() === 'product';
                    ?>
                    <li class="jc-search__item">
                        <a class="jc-search__card" href="<?php the_permalink(); ?>">
                            <span class="jc-search__thumb">
                                <?php
                                if (has_post_thumbnail()) {
                                    the_post_thumbnail('woocommerce_thumbnail');
                                }
                                ?>
                            </span>
                            <span class="jc-search__body">
                                <?php
                                $type_obj = get_post_type_object(get_post_type());
                                $type_lbl = $is_product
                                    ? __('Product', 'justccell')
                                    : (($type_obj && isset($type_obj->labels->singular_name))
                                        ? (string) $type_obj->labels->singular_name
                                        : __('Page', 'justccell'));
                                ?>
                                <span class="jc-search__type"><?php echo esc_html($type_lbl); ?></span>
                                <strong><?php the_title(); ?></strong>
                                <span class="jc-search__excerpt"><?php echo esc_html(wp_trim_words(wp_strip_all_tags(get_the_excerpt()), 22)); ?></span>
                            </span>
                        </a>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <?php the_posts_pagination(['mid_size' => 1]); ?>
        <?php else : ?>
            <div class="jc-search__empty">
                <p><?php esc_html_e('No hardware or articles matched. Browse a category instead.', 'justccell'); ?></p>
                <p class="jc-search__actions">
                    <a class="btn btn--primary" href="<?php echo esc_url(home_url('/all-in-ones/')); ?>"><?php esc_html_e('All-In-Ones', 'justccell'); ?></a>
                    <a class="btn btn--ghost" href="<?php echo esc_url(home_url('/cartridge/')); ?>"><?php esc_html_e('Cartridges', 'justccell'); ?></a>
                </p>
            </div>
        <?php endif; ?>
    </div>
</section>
<?php
get_footer();
