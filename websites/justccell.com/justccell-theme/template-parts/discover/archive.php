<?php
/**
 * Discover listing — overlay hero, All / Guides / News / Blogs, 3-col post grid.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

justccell_ensure_media_files([justccell_discover_hero_key()]);
$hub   = justccell_discover_hub_content();
$query = justccell_discover_listing_query();
$crumb = justccell_discover_crumb_label();
$tabs  = justccell_discover_tabs();
$title = (string) ($hub['title'] ?? __('Discover', 'justccell'));
$lede  = (string) ($hub['lede'] ?? '');
$intro = (string) ($hub['intro'] ?? '');
$desk_id  = (int) ($hub['image_id'] ?? 0);
$desk_key = (string) ($hub['image_key'] ?? justccell_discover_hero_key());
$mob_id   = (int) ($hub['image_mobile_id'] ?? 0);
$mob_key  = (string) ($hub['image_mobile_key'] ?? $desk_key);
if ($mob_id < 1 && $desk_id > 0) {
    $mob_id = $desk_id;
}

$echo_hero = static function (int $id, string $key, array $attrs): void {
    if ($id > 0) {
        echo wp_get_attachment_image($id, 'full', false, $attrs);
        return;
    }
    echo justccell_media_img($key, $attrs);
};
?>
<article class="d-clone">
    <section class="a-hero why-hero">
        <div class="a-hero__media">
            <span class="a-hero__desktop">
                <?php $echo_hero($desk_id, $desk_key, [
                    'alt'      => $title,
                    'width'    => 1920,
                    'height'   => 860,
                    'decoding' => 'async',
                ]); ?>
            </span>
            <span class="a-hero__mobile">
                <?php $echo_hero($mob_id, $mob_key, [
                    'alt'      => $title,
                    'width'    => 750,
                    'height'   => 700,
                    'decoding' => 'async',
                ]); ?>
            </span>
        </div>
        <div class="a-hero__txt">
            <?php justccell_echo_heading($title, (string) ($hub['title_tag'] ?? 'h1')); ?>
            <?php if ($lede !== '') : ?>
                <p><?php echo esc_html($lede); ?></p>
            <?php endif; ?>
        </div>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero a-hero__crumbs'); ?>
    </section>

    <nav class="d-tab" aria-label="<?php esc_attr_e('Discover categories', 'justccell'); ?>">
        <?php foreach ($tabs as $tab) : ?>
            <a href="<?php echo esc_url((string) $tab['url']); ?>"<?php echo !empty($tab['on']) ? ' class="on" aria-current="page"' : ''; ?>>
                <?php echo esc_html((string) $tab['title']); ?>
            </a>
        <?php endforeach; ?>
    </nav>

    <section class="d-grid-wrap">
        <?php if (trim(wp_strip_all_tags($intro)) !== '') : ?>
            <div class="d-intro container entry-content">
                <?php echo wp_kses_post($intro); ?>
            </div>
        <?php endif; ?>
        <div class="d-grid container">
            <?php if ($query->have_posts()) : ?>
                <?php
                while ($query->have_posts()) {
                    $query->the_post();
                    get_template_part('template-parts/discover/card');
                }
                if (is_page('discover') && !is_home()) {
                    wp_reset_postdata();
                }
                ?>
            <?php else : ?>
                <p class="d-empty"><?php esc_html_e('No posts in this category yet. Add a WordPress post and assign Guides, News, or Blogs.', 'justccell'); ?></p>
            <?php endif; ?>
        </div>
        <?php justccell_discover_pagination($query); ?>
    </section>
</article>
