<?php
/**
 * Catalog listing hero (banner + heading).
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$hero = $args['hero'] ?? null;
if (!is_array($hero)) {
    return;
}

$panel_key = (string) ($args['panel_key'] ?? '');
$is_panel  = $panel_key !== '';
$is_active = !empty($args['is_active']);

$slides  = $hero['slides'] ?? [];
$heading = (string) ($hero['heading'] ?? '');
$lede    = (string) ($hero['lede'] ?? '');
$page_id = (int) ($hero['page_id'] ?? 0);

if ($heading === '' && $page_id > 0) {
    $heading = (string) get_the_title($page_id);
}

$heading_tag = 'h1';
if ($page_id > 0 && function_exists('get_field')) {
    $heading_tag = (string) (get_field('listing_heading_tag', $page_id) ?: 'h1');
}

$hero_classes = ['jc-hero-banner', 'jc-hero-banner--wide', 'jc-hero-banner--vignette-center', 'c-hero'];
if ($slides === []) {
    $hero_classes[] = 'c-hero--text';
}
foreach ($slides as $slide) {
    if (!is_array($slide)) {
        continue;
    }
    $desk_id = (int) ($slide['desktop_id'] ?? 0);
    $mob_id  = (int) ($slide['mobile_id'] ?? 0);
    if ($mob_id > 0 && $mob_id !== $desk_id) {
        $hero_classes[] = 'jc-hero-banner--split';
        break;
    }
}
if ($is_panel) {
    $hero_classes[] = 'c-hero-panel';
    if ($is_active) {
        $hero_classes[] = 'is-on';
    }
}

$hero_attrs = '';
if ($is_panel) {
    $hero_attrs .= ' data-catalog-hero="' . esc_attr($panel_key) . '"';
    if (!$is_active) {
        $hero_attrs .= ' hidden';
    }
}
if (count($slides) > 1) {
    $hero_attrs .= ' data-banners';
}
?>
<header class="<?php echo esc_attr(implode(' ', $hero_classes)); ?>"<?php echo $hero_attrs; ?>>
    <?php if ($slides !== []) : ?>
        <div class="jc-hero-banner__track c-hero__track" data-banner-track>
            <?php foreach ($slides as $i => $slide) : ?>
                <?php
                if (!is_array($slide)) {
                    continue;
                }
                $tag   = ($slide['url'] ?? '') !== '' ? 'a' : 'div';
                $href  = ($slide['url'] ?? '') !== '' ? ' href="' . esc_url((string) $slide['url']) . '"' : '';
                $class = 'jc-hero-banner__slide c-hero__slide' . ($i === 0 ? ' is-on' : '');
                ?>
                <<?php echo $tag; ?> class="<?php echo esc_attr($class); ?>"<?php echo $href; ?>>
                    <?php
                    echo wp_get_attachment_image((int) $slide['desktop_id'], 'full', false, [
                        'class'         => 'jc-hero-banner__desk c-hero__desk',
                        'alt'           => $heading,
                        'fetchpriority' => $i === 0 && $is_active ? 'high' : 'low',
                    ]);
                    $mob_id = (int) ($slide['mobile_id'] ?? 0);
                    if ($mob_id > 0 && $mob_id !== (int) $slide['desktop_id']) {
                        echo wp_get_attachment_image($mob_id, 'full', false, [
                            'class'   => 'jc-hero-banner__mobile c-hero__mobile',
                            'alt'     => $heading,
                            'loading' => ($i === 0 && $is_active) ? null : 'lazy',
                        ]);
                    }
                    ?>
                </<?php echo $tag; ?>>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    <div class="jc-hero-banner__overlay jc-hero-banner__overlay--center c-hero__txt">
        <?php justccell_echo_heading($heading, $heading_tag); ?>
        <?php if ($lede !== '') : ?>
            <p><?php echo esc_html($lede); ?></p>
        <?php endif; ?>
    </div>
    <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero p-crumbs'); ?>
    <?php if (count($slides) > 1) : ?>
        <div class="h-banner__dots" data-banner-dots></div>
    <?php endif; ?>
</header>
