<?php
/**
 * Laser engraving — overlay hero, split film, service cards.
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

$title   = (string) ($page['title'] ?? get_the_title());
$kicker  = (string) ($page['kicker'] ?? '');
$lede    = trim((string) ($page['lede'] ?? ''));
$video   = (string) ($page['video_url'] ?? '');
$vhead   = (string) ($page['video_heading'] ?? '');
$vcopy   = trim((string) ($page['video_copy'] ?? ''));
$blocks  = is_array($page['blocks'] ?? null) ? $page['blocks'] : [];
$steps   = is_array($page['sections'] ?? null) ? $page['sections'] : [];
$cards   = is_array($page['cards'] ?? null) ? $page['cards'] : [];

$href = static function (string $url): string {
    $url = trim($url);
    if ($url === '') {
        return home_url('/');
    }
    if (preg_match('#^https?://#i', $url) === 1) {
        return $url;
    }
    return home_url($url);
};

$cta_url = justccell_brand_cta_url((string) ($page['cta_url'] ?? ''));
$intro_primary_label   = (string) ($page['intro_primary_label'] ?? __('Contact us', 'justccell'));
$intro_primary_url     = $href((string) ($page['intro_primary_url'] ?? '/contact/'));
$intro_secondary_label = trim((string) ($page['intro_secondary_label'] ?? ''));
$intro_secondary_url   = $href((string) ($page['intro_secondary_url'] ?? '/packaging/'));
$steps_heading         = (string) ($page['steps_heading'] ?? __('How to brief us', 'justccell'));
$steps_lede            = trim((string) ($page['steps_lede'] ?? ''));
$hardware_heading      = (string) ($page['hardware_heading'] ?? __('Hardware we mark', 'justccell'));
$hardware_lede         = trim((string) ($page['hardware_lede'] ?? ''));

$clean_blocks = [];
foreach ($blocks as $block) {
    if (!is_array($block)) {
        continue;
    }
    $clean_blocks[] = justccell_laser_public_block($block);
}
$blocks = $clean_blocks;

if ($steps === [] && function_exists('justccell_laser_default_steps')) {
    $steps = justccell_laser_default_steps();
}
if ($cards === [] && function_exists('justccell_laser_default_hardware')) {
    $cards = justccell_laser_default_hardware();
}

$hero_id  = (int) ($page['image_id'] ?? 0);
$hero_key = (string) ($page['image_key'] ?? '');
$hero_mobile_id  = (int) ($page['image_mobile_id'] ?? 0);
$hero_mobile_key = (string) ($page['image_mobile_key'] ?? '');
if ($hero_mobile_id < 1 && $hero_mobile_key === '') {
    $hero_mobile_id  = $hero_id;
    $hero_mobile_key = $hero_key;
}

$echo_img = static function (int $id, string $key, array $attrs): void {
    if ($id > 0) {
        echo wp_get_attachment_image($id, 'full', false, $attrs);
        return;
    }
    if ($key !== '') {
        echo justccell_media_img($key, $attrs);
    }
};

?>
<article class="laser-clone">
    <section class="a-hero laser-hero">
        <div class="a-hero__media">
            <?php if ($video !== '') : ?>
                <video
                    class="laser-hero__video"
                    autoplay
                    muted
                    loop
                    playsinline
                    preload="metadata"
                    aria-hidden="true"
                    tabindex="-1"
                >
                    <source src="<?php echo esc_url($video); ?>" type="video/mp4">
                </video>
            <?php else : ?>
                <span class="a-hero__desktop">
                    <?php $echo_img($hero_id, $hero_key, [
                        'alt'           => $title,
                        'width'         => 1920,
                        'height'        => 860,
                        'decoding'      => 'async',
                        'fetchpriority' => 'high',
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
            <?php endif; ?>
        </div>
        <div class="a-hero__txt">
            <?php if ($kicker !== '') : ?>
                <p class="laser-hero__kicker"><?php echo esc_html($kicker); ?></p>
            <?php endif; ?>
            <?php justccell_echo_heading($title, (string) ($page['title_tag'] ?? 'h1')); ?>
        </div>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero a-hero__crumbs'); ?>
    </section>

    <section class="laser-intro" id="laser-film">
        <div class="container laser-intro__box<?php echo $video === '' ? ' laser-intro__box--copy' : ''; ?>">
            <div class="laser-intro__copy">
                <?php if ($kicker !== '') : ?>
                    <p class="laser-intro__kicker"><?php echo esc_html($kicker); ?></p>
                <?php endif; ?>
                <?php justccell_echo_heading(
                    $vhead !== '' ? $vhead : $title,
                    (string) ($page['video_heading_tag'] ?? 'h2')
                ); ?>
                <?php if ($lede !== '') : ?>
                    <p><?php echo esc_html($lede); ?></p>
                <?php endif; ?>
                <?php if ($vcopy !== '' && $vcopy !== $lede) : ?>
                    <p><?php echo esc_html($vcopy); ?></p>
                <?php endif; ?>
                <div class="laser-intro__actions">
                    <?php if ($intro_primary_label !== '') : ?>
                    <a class="btn btn--primary" href="<?php echo esc_url($intro_primary_url); ?>">
                        <?php echo esc_html($intro_primary_label); ?>
                    </a>
                    <?php endif; ?>
                    <?php if ($intro_secondary_label !== '') : ?>
                    <a class="btn btn--ghost" href="<?php echo esc_url($intro_secondary_url); ?>">
                        <?php echo esc_html($intro_secondary_label); ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>
            <?php if ($video !== '') : ?>
                <div class="laser-intro__media">
                    <video
                        class="laser-intro__player"
                        controls
                        playsinline
                        muted
                        preload="metadata"
                        title="<?php echo esc_attr($vhead !== '' ? $vhead : $title); ?>"
                    >
                        <source src="<?php echo esc_url($video); ?>" type="video/mp4">
                    </video>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <?php if ($blocks !== []) : ?>
        <section class="laser-offer" id="what-we-engrave">
            <div class="container">
                <div class="laser-offer__grid">
                    <?php foreach ($blocks as $i => $block) : ?>
                        <?php if (!is_array($block)) {
                            continue;
                        } ?>
                        <article class="laser-card">
                            <p class="laser-card__num"><?php echo esc_html(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></p>
                            <?php justccell_echo_heading((string) ($block['title'] ?? ''), (string) ($block['title_tag'] ?? 'h3')); ?>
                            <?php if ((string) ($block['copy'] ?? '') !== '') : ?>
                                <p><?php echo esc_html((string) $block['copy']); ?></p>
                            <?php endif; ?>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($steps !== []) : ?>
        <section class="laser-steps" id="how-to-order">
            <div class="container">
                <div class="laser-steps__head">
                    <?php justccell_echo_heading($steps_heading, (string) ($page['steps_heading_tag'] ?? 'h2')); ?>
                    <?php if ($steps_lede !== '') : ?>
                        <p><?php echo esc_html($steps_lede); ?></p>
                    <?php endif; ?>
                </div>
                <ol class="laser-steps__list">
                    <?php foreach ($steps as $i => $step) : ?>
                        <?php if (!is_array($step)) {
                            continue;
                        } ?>
                        <li class="laser-steps__item">
                            <p class="laser-card__num"><?php echo esc_html(str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT)); ?></p>
                            <?php justccell_echo_heading((string) ($step['title'] ?? ''), (string) ($step['title_tag'] ?? 'h3')); ?>
                            <?php if ((string) ($step['copy'] ?? '') !== '') : ?>
                                <p><?php echo esc_html((string) $step['copy']); ?></p>
                            <?php endif; ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($cards !== []) : ?>
        <section class="laser-hardware" id="hardware">
            <div class="container">
                <div class="laser-steps__head">
                    <?php justccell_echo_heading($hardware_heading, (string) ($page['hardware_heading_tag'] ?? 'h2')); ?>
                    <?php if ($hardware_lede !== '') : ?>
                        <p><?php echo esc_html($hardware_lede); ?></p>
                    <?php endif; ?>
                </div>
                <div class="laser-hardware__grid">
                    <?php foreach ($cards as $card) : ?>
                        <?php
                        if (!is_array($card)) {
                            continue;
                        }
                        $card_url = $href((string) ($card['url'] ?? '/'));
                        $more = (string) ($card['more_label'] ?? '');
                        if ($more === '') {
                            $more = __('View hardware', 'justccell');
                        }
                        ?>
                        <a class="laser-hardware__card" href="<?php echo esc_url($card_url); ?>">
                            <?php justccell_echo_heading((string) ($card['title'] ?? ''), (string) ($card['title_tag'] ?? 'h3')); ?>
                            <?php if ((string) ($card['copy'] ?? '') !== '') : ?>
                                <p><?php echo esc_html((string) $card['copy']); ?></p>
                            <?php endif; ?>
                            <span class="h-more"><?php echo esc_html($more); ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ((string) ($page['cta_title'] ?? '') !== '' || (string) ($page['cta_label'] ?? '') !== '') : ?>
        <section class="laser-cta">
            <div class="container">
                <?php if ((string) ($page['cta_title'] ?? '') !== '') : ?>
                    <?php justccell_echo_heading((string) $page['cta_title'], (string) ($page['cta_title_tag'] ?? 'h2')); ?>
                <?php endif; ?>
                <?php if ((string) ($page['cta_copy'] ?? '') !== '') : ?>
                    <p><?php echo esc_html((string) $page['cta_copy']); ?></p>
                <?php endif; ?>
                <?php if ((string) ($page['cta_label'] ?? '') !== '') : ?>
                    <a class="btn btn--primary" href="<?php echo esc_url($cta_url); ?>">
                        <?php echo esc_html((string) $page['cta_label']); ?>
                    </a>
                <?php endif; ?>
            </div>
        </section>
    <?php endif; ?>
</article>
