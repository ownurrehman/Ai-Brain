<?php
/**
 * Brand / legal / discover — ACF first, PHP fallback.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$slug = (string) get_post_field('post_name', get_queried_object_id());
$kind = function_exists('justccell_page_layout_kind')
    ? justccell_page_layout_kind((int) get_queried_object_id())
    : '';

if ($kind === 'bio' || (function_exists('justccell_is_bio_page') && justccell_is_bio_page((int) get_queried_object_id()))) {
    get_template_part('template-parts/page/brand', 'bio-heating');
    return;
}

if ($kind === 'location' || (function_exists('justccell_is_location_page_slug') ? justccell_is_location_page_slug($slug) : ($slug === 'location' || $slug === 'locations'))) {
    get_template_part('template-parts/page/brand', 'locations');
    return;
}

if ($kind === 'coming-soon'
    || (function_exists('justccell_page_shows_coming_soon') && justccell_page_shows_coming_soon((int) get_queried_object_id()))
) {
    get_template_part('template-parts/page/brand', 'coming-soon');
    return;
}

$page = justccell_get_brand_page_content();
if ($page === []) {
    return;
}

if ($kind === 'about' || $slug === 'about') {
    get_template_part('template-parts/page/brand', 'about');
    return;
}

if ($kind === 'why' || (function_exists('justccell_is_why_page_slug') && justccell_is_why_page_slug($slug))) {
    get_template_part('template-parts/page/brand', 'why');
    return;
}

if ($slug === 'laser-engraving') {
    get_template_part('template-parts/page/brand', 'laser');
    return;
}

$image_id  = (int) ($page['image_id'] ?? 0);
$image_key = (string) ($page['image_key'] ?? '');
if ($image_key !== '' && $image_id < 1) {
    justccell_ensure_media_url($image_key);
}
?>
<article class="s-clone">
    <header class="s-hero">
        <div class="container">
            <?php if ((string) ($page['kicker'] ?? '') !== '') : ?>
            <p class="s-kicker"><?php echo esc_html((string) $page['kicker']); ?></p>
            <?php endif; ?>
            <?php justccell_echo_heading((string) ($page['title'] ?? get_the_title()), (string) ($page['title_tag'] ?? 'h1')); ?>
            <?php if (($page['lede'] ?? '') !== '') : ?>
                <p class="s-lede"><?php echo esc_html((string) $page['lede']); ?></p>
            <?php endif; ?>
        </div>
    </header>

    <?php if ($image_id > 0 || $image_key !== '') : ?>
        <figure class="s-figure">
            <?php
            if ($image_id > 0) {
                echo wp_get_attachment_image($image_id, 'full', false, [
                    'alt'     => (string) ($page['title'] ?? ''),
                    'width'   => 1600,
                    'height'  => 900,
                    'loading' => 'lazy',
                ]);
            } else {
                echo justccell_media_img($image_key, [
                    'alt'     => (string) ($page['title'] ?? ''),
                    'width'   => 1600,
                    'height'  => 900,
                    'loading' => 'lazy',
                ]);
            }
            ?>
        </figure>
    <?php endif; ?>

    <?php if ((string) ($page['video_url'] ?? '') !== '') : ?>
        <section class="s-video">
            <div class="container s-video__box">
                <?php if ((string) ($page['video_heading'] ?? '') !== '') : ?>
                    <?php justccell_echo_heading((string) $page['video_heading'], (string) ($page['video_heading_tag'] ?? 'h2')); ?>
                <?php endif; ?>
                <?php if ((string) ($page['video_copy'] ?? '') !== '') : ?>
                    <p><?php echo esc_html((string) $page['video_copy']); ?></p>
                <?php endif; ?>
                <video class="s-video__player" controls playsinline preload="metadata"<?php echo is_page('laser-engraving') ? ' muted' : ''; ?>>
                    <source src="<?php echo esc_url((string) $page['video_url']); ?>" type="video/mp4">
                </video>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($page['sections']) && is_array($page['sections'])) : ?>
        <div class="s-sections">
            <?php foreach ($page['sections'] as $section) : ?>
                <?php
                if (!is_array($section)) {
                    continue;
                }
                $sid = (string) ($section['id'] ?? '');
                ?>
                <section class="s-section"<?php echo $sid !== '' ? ' id="' . esc_attr($sid) . '"' : ''; ?>>
                    <div class="container">
                        <?php justccell_echo_heading((string) ($section['title'] ?? ''), (string) ($section['title_tag'] ?? 'h2')); ?>
                        <?php if (($section['copy'] ?? '') !== '') : ?>
                            <p><?php echo esc_html((string) $section['copy']); ?></p>
                        <?php endif; ?>
                    </div>
                </section>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($page['blocks']) && is_array($page['blocks'])) : ?>
        <section class="s-blocks">
            <div class="container s-blocks__grid">
                <?php foreach ($page['blocks'] as $block) : ?>
                    <div class="s-card">
                        <?php
                        $bid = (int) ($block['image_id'] ?? 0);
                        $bkey = (string) ($block['image_key'] ?? '');
                        if ($bid > 0) {
                            echo wp_get_attachment_image($bid, 'medium', false, [
                                'alt'     => (string) ($block['title'] ?? ''),
                                'loading' => 'lazy',
                            ]);
                        } elseif ($bkey !== '') {
                            echo justccell_media_img($bkey, [
                                'alt'     => (string) ($block['title'] ?? ''),
                                'loading' => 'lazy',
                            ]);
                        }
                        ?>
                        <?php if ((string) ($block['kicker'] ?? '') !== '') : ?>
                            <p class="s-kicker"><?php echo esc_html((string) $block['kicker']); ?></p>
                        <?php endif; ?>
                        <?php justccell_echo_heading((string) ($block['title'] ?? ''), (string) ($block['title_tag'] ?? 'h2')); ?>
                        <p><?php echo esc_html((string) ($block['copy'] ?? '')); ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($page['compare']) && is_array($page['compare'])) : ?>
        <?php $compare = $page['compare']; ?>
        <section class="s-compare">
            <div class="container">
                <?php justccell_echo_heading((string) ($page['compare_heading'] ?? __('What’s different', 'justccell')), (string) ($page['compare_heading_tag'] ?? 'h2')); ?>
                <div class="s-compare__grid">
                    <?php foreach (['left', 'right'] as $side) : ?>
                        <?php $col = is_array($compare[$side] ?? null) ? $compare[$side] : []; ?>
                        <div class="s-compare__col s-compare__col--<?php echo esc_attr($side); ?>">
                            <?php justccell_echo_heading((string) ($col['title'] ?? ''), (string) ($col['title_tag'] ?? 'h3')); ?>
                            <ul>
                                <?php foreach ((array) ($col['items'] ?? []) as $item) : ?>
                                    <li><?php echo esc_html((string) $item); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($page['cards']) && is_array($page['cards'])) : ?>
        <section class="s-cards">
            <div class="container s-cards__grid">
                <?php foreach ($page['cards'] as $card) : ?>
                    <a class="s-hub" href="<?php echo esc_url(home_url((string) ($card['url'] ?? '/'))); ?>">
                        <?php
                        $card_id  = (int) ($card['image_id'] ?? 0);
                        $card_key = (string) ($card['image_key'] ?? '');
                        if ($card_id > 0) {
                            echo wp_get_attachment_image($card_id, 'medium', false, [
                                'alt'     => (string) ($card['title'] ?? ''),
                                'loading' => 'lazy',
                            ]);
                        } elseif ($card_key !== '') {
                            echo justccell_media_img($card_key, [
                                'alt'     => (string) ($card['title'] ?? ''),
                                'loading' => 'lazy',
                            ]);
                        }
                        ?>
                        <?php justccell_echo_heading((string) ($card['title'] ?? ''), (string) ($card['title_tag'] ?? 'h2')); ?>
                        <p><?php echo esc_html((string) ($card['copy'] ?? '')); ?></p>
                        <span class="h-more"><?php echo esc_html((string) (($card['more_label'] ?? '') !== '' ? $card['more_label'] : __('View details', 'justccell'))); ?></span>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if (!empty($page['timeline']) && is_array($page['timeline'])) : ?>
        <section class="s-timeline" id="brand-history">
            <div class="container">
                <?php justccell_echo_heading((string) ($page['heading_history'] ?? __('Brand history', 'justccell')), (string) ($page['heading_history_tag'] ?? 'h2')); ?>
                <ul>
                    <?php foreach ($page['timeline'] as $item) : ?>
                        <li><?php echo esc_html((string) $item); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <?php if ((string) ($page['cta_title'] ?? '') !== '' || (string) ($page['cta_label'] ?? '') !== '') : ?>
    <section class="s-cta">
        <div class="container">
            <?php justccell_echo_heading((string) ($page['cta_title'] ?? ''), (string) ($page['cta_title_tag'] ?? 'h2')); ?>
            <?php if ((string) ($page['cta_copy'] ?? '') !== '') : ?>
                <p><?php echo esc_html((string) $page['cta_copy']); ?></p>
            <?php endif; ?>
            <?php if ((string) ($page['cta_label'] ?? '') !== '') : ?>
            <a class="btn btn--primary" href="<?php echo esc_url(justccell_brand_cta_url((string) ($page['cta_url'] ?? ''))); ?>">
                <?php echo esc_html((string) $page['cta_label']); ?>
            </a>
            <?php endif; ?>
        </div>
    </section>
    <?php endif; ?>
</article>
