<?php
/**
 * Why Justccell pages — overlay hero, four tabs, quote + rows (ccell layout).
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$page = justccell_get_brand_page_content();
if ($page === []) {
    return;
}

$slug = (string) get_post_field('post_name', get_queried_object_id());
$tabs = function_exists('justccell_why_page_tabs') ? justccell_why_page_tabs() : [];
$layout = (string) ($page['layout'] ?? '');
$lede = trim((string) ($page['lede'] ?? ''));
$meet = trim((string) ($page['meet_heading'] ?? ''));
$blocks = is_array($page['blocks'] ?? null) ? $page['blocks'] : [];
$stats = is_array($page['stats'] ?? null) ? $page['stats'] : [];
$compare = is_array($page['compare'] ?? null) ? $page['compare'] : [];
$title = (string) ($page['title'] ?? get_the_title());

$files = array_filter([
    (string) ($page['image_key'] ?? ''),
    (string) ($page['image_mobile_key'] ?? ''),
    (string) ($page['intro_image_key'] ?? ''),
]);
foreach ($blocks as $block) {
    if (!is_array($block)) {
        continue;
    }
    $key = (string) ($block['image_key'] ?? '');
    if ($key !== '') {
        $files[] = $key;
    }
}
justccell_ensure_media_files($files);

$echo_img = static function (int $id, string $key, array $attrs): void {
    if ($id > 0) {
        echo wp_get_attachment_image($id, 'full', false, $attrs);
        return;
    }
    echo justccell_media_img($key, $attrs);
};

$hero_mobile_id  = (int) ($page['image_mobile_id'] ?? 0);
$hero_mobile_key = (string) ($page['image_mobile_key'] ?? '');
if ($hero_mobile_id < 1 && $hero_mobile_key === '') {
    $hero_mobile_id  = (int) ($page['image_id'] ?? 0);
    $hero_mobile_key = (string) ($page['image_key'] ?? '');
}
?>
<article class="why-clone">
    <section class="a-hero why-hero">
        <div class="a-hero__media">
            <span class="a-hero__desktop">
                <?php $echo_img((int) ($page['image_id'] ?? 0), (string) ($page['image_key'] ?? ''), [
                    'alt'      => $title,
                    'width'    => 1920,
                    'height'   => 860,
                    'decoding' => 'async',
                ]); ?>
            </span>
            <span class="a-hero__mobile">
                <?php $echo_img($hero_mobile_id, $hero_mobile_key, [
                    'alt'      => $title,
                    'width'    => 750,
                    'height'   => 700,
                    'decoding' => 'async',
                ]); ?>
            </span>
        </div>
        <div class="a-hero__txt">
            <?php justccell_echo_heading($title, (string) ($page['title_tag'] ?? 'h1')); ?>
        </div>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero a-hero__crumbs'); ?>
    </section>

    <?php if ($tabs !== []) : ?>
        <nav class="why-tab" aria-label="<?php esc_attr_e('Why Justccell', 'justccell'); ?>">
            <?php foreach ($tabs as $tab) : ?>
                <?php
                $tab_slug = (string) ($tab['slug'] ?? '');
                $on = $tab_slug !== '' && $tab_slug === $slug;
                ?>
                <a href="<?php echo esc_url((string) ($tab['url'] ?? '')); ?>"<?php echo $on ? ' class="on" aria-current="page"' : ''; ?>>
                    <?php echo esc_html((string) ($tab['title'] ?? '')); ?>
                </a>
            <?php endforeach; ?>
        </nav>
    <?php endif; ?>

    <?php if ($layout === 'split') : ?>
        <section class="why-split">
            <div class="container why-split__box">
                <div class="why-split__media js-reveal">
                    <?php $echo_img((int) ($page['intro_image_id'] ?? 0), (string) ($page['intro_image_key'] ?? ''), [
                        'alt'     => $meet !== '' ? $meet : $title,
                        'width'   => 900,
                        'height'  => 700,
                        'loading' => 'lazy',
                    ]); ?>
                </div>
                <div class="why-split__copy js-reveal">
                    <?php if ($meet !== '') : ?>
                        <?php justccell_echo_heading($meet, (string) ($page['meet_heading_tag'] ?? 'h2')); ?>
                    <?php endif; ?>
                    <?php if ($lede !== '') : ?>
                        <?php echo wp_kses_post(wpautop($lede)); ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php else : ?>
        <section class="why-intro">
            <div class="container">
                <?php if ($lede !== '') : ?>
                    <div class="why-intro__copy js-reveal">
                        <?php echo wp_kses_post(wpautop($lede)); ?>
                    </div>
                <?php endif; ?>
                <?php if ((int) ($page['intro_image_id'] ?? 0) > 0 || (string) ($page['intro_image_key'] ?? '') !== '') : ?>
                    <figure class="why-intro__media js-reveal">
                        <?php $echo_img((int) ($page['intro_image_id'] ?? 0), (string) ($page['intro_image_key'] ?? ''), [
                            'alt'     => $meet !== '' ? $meet : $title,
                            'width'   => 1400,
                            'height'  => 800,
                            'loading' => 'lazy',
                        ]); ?>
                    </figure>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($stats !== []) : ?>
        <section class="why-stats">
            <div class="container">
                <?php if ($meet !== '') : ?>
                    <div class="a-subh">
                        <?php justccell_echo_heading($meet, (string) ($page['meet_heading_tag'] ?? 'h2')); ?>
                    </div>
                <?php endif; ?>
                <div class="why-stats__grid">
                    <?php foreach ($stats as $i => $stat) : ?>
                        <?php if (!is_array($stat)) { continue; } ?>
                        <div class="why-stats__item js-reveal" style="--reveal-delay: <?php echo esc_attr((string) (0.08 * $i)); ?>s">
                            <p class="why-stats__value">
                                <span><?php echo esc_html((string) ($stat['value'] ?? '')); ?></span>
                                <?php if ((string) ($stat['unit'] ?? '') !== '') : ?>
                                    <small><?php echo esc_html((string) $stat['unit']); ?></small>
                                <?php endif; ?>
                            </p>
                            <p class="why-stats__label"><?php echo esc_html((string) ($stat['label'] ?? '')); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php elseif ($meet !== '' && $layout !== 'split') : ?>
        <div class="a-subh why-meet">
            <?php justccell_echo_heading($meet, (string) ($page['meet_heading_tag'] ?? 'h2')); ?>
        </div>
    <?php endif; ?>

    <?php if ($blocks !== []) : ?>
        <section class="why-rows">
            <div class="container">
                <?php foreach ($blocks as $i => $block) : ?>
                    <?php
                    if (!is_array($block)) {
                        continue;
                    }
                    $has_img = (int) ($block['image_id'] ?? 0) > 0 || (string) ($block['image_key'] ?? '') !== '';
                    ?>
                    <div class="why-row<?php echo $has_img ? '' : ' why-row--text'; ?>">
                        <?php if ($has_img) : ?>
                            <div class="why-row__media js-reveal">
                                <?php $echo_img((int) ($block['image_id'] ?? 0), (string) ($block['image_key'] ?? ''), [
                                    'alt'     => (string) ($block['title'] ?? ''),
                                    'width'   => 640,
                                    'height'  => 690,
                                    'loading' => 'lazy',
                                ]); ?>
                            </div>
                        <?php endif; ?>
                        <div class="why-row__copy js-reveal">
                            <?php justccell_echo_heading((string) ($block['title'] ?? ''), (string) ($block['title_tag'] ?? 'h3')); ?>
                            <?php if ((string) ($block['kicker'] ?? '') !== '') : ?>
                                <p class="why-row__kicker"><?php echo esc_html((string) $block['kicker']); ?></p>
                            <?php endif; ?>
                            <?php if ((string) ($block['copy'] ?? '') !== '') : ?>
                                <p><?php echo esc_html((string) $block['copy']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($compare !== [] && (isset($compare['left']) || isset($compare['right']))) : ?>
        <section class="s-compare why-compare js-reveal">
            <div class="container">
                <div class="a-subh">
                    <?php justccell_echo_heading((string) ($page['compare_heading'] ?? __('What’s Different', 'justccell')), (string) ($page['compare_heading_tag'] ?? 'h2')); ?>
                </div>
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
</article>
