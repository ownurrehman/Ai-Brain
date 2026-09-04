<?php
/**
 * Justccell 3.0 Bio-Heating page.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

$page = justccell_get_bio_heating_content();
if ($page === []) {
    return;
}

// All j3_* ACF values are merged in justccell_get_bio_heating_content() — do not call get_field() here.

$alt = (string) ($page['kicker'] ?? __('Justccell 3.0', 'justccell'));
$groups = (array) ($page['product_groups'] ?? []);
?>
<article class="j3">
    <header class="j3-hero">
        <div class="j3-hero__media">
            <?php
            justccell_j3_echo_img_pair(
                (int) ($page['hero_desktop_id'] ?? 0),
                (string) ($page['hero_desktop'] ?? ''),
                (int) ($page['hero_mobile_id'] ?? 0),
                (string) ($page['hero_mobile'] ?? ''),
                'j3-hero__img',
                $alt,
                ['fetchpriority' => 'high']
            );
            ?>
        </div>
        <div class="j3-hero__box">
            <div class="j3-hero__txt">
                <?php
                $hero_title = trim($alt . "\n" . (string) ($page['title_line'] ?? ''));
                justccell_echo_heading($hero_title, (string) ($page['title_tag'] ?? 'h1'), 'j3-hero__title', true);
                ?>
                <p class="j3-hero__sub"><?php justccell_j3_echo_lines((string) ($page['subtitle'] ?? '')); ?></p>
            </div>
        </div>
    </header>

    <?php foreach ((array) ($page['sections'] ?? []) as $section) : ?>
        <?php
        if (!is_array($section)) {
            continue;
        }
        $type = (string) ($section['type'] ?? '');
        if ($type === 'banner') :
            ?>
            <section class="j3-band">
                <div class="container2">
                    <div class="j3-band__tit">
                        <div class="j3-band__gl">
                            <?php justccell_echo_heading(str_replace('|', "\n", (string) ($section['title'] ?? '')), (string) ($section['title_tag'] ?? 'h2'), '', true); ?>
                        </div>
                        <div class="j3-band__gr"></div>
                    </div>
                    <div class="j3-band__media">
                        <?php
                        justccell_j3_echo_img_pair(
                            (int) ($section['image_desktop_id'] ?? 0),
                            (string) ($section['image_desktop'] ?? ''),
                            (int) ($section['image_mobile_id'] ?? 0),
                            (string) ($section['image_mobile'] ?? ''),
                            'j3-band__img',
                            (string) ($section['title'] ?? $alt)
                        );
                        ?>
                    </div>
                </div>
            </section>
        <?php elseif ($type === 'split') : ?>
            <section class="j3-split<?php echo !empty($section['reverse']) ? ' j3-split--reverse' : ''; ?>">
                <div class="container2">
                    <div class="j3-split__row">
                        <div class="j3-split__media">
                            <?php
                            justccell_j3_echo_img_pair(
                                (int) ($section['image_desktop_id'] ?? 0),
                                (string) ($section['image_desktop'] ?? ''),
                                (int) ($section['image_mobile_id'] ?? 0),
                                (string) ($section['image_mobile'] ?? ''),
                                'j3-split__img',
                                (string) ($section['heading'] ?? $alt)
                            );
                            ?>
                        </div>
                        <div class="j3-split__txt">
                            <?php justccell_echo_heading((string) ($section['heading'] ?? ''), (string) ($section['heading_tag'] ?? 'h3')); ?>
                            <?php if (($section['copy'] ?? '') !== '') : ?>
                                <p><?php echo esc_html((string) $section['copy']); ?></p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endif; ?>
    <?php endforeach; ?>

    <?php if ($groups !== []) : ?>
        <section class="j3-products" data-j3-tabs>
            <div class="container2">
                <?php if ((string) ($page['products_title'] ?? '') !== '') : ?>
                    <?php justccell_echo_heading((string) $page['products_title'], (string) ($page['products_title_tag'] ?? 'h2'), 'j3-products__title'); ?>
                <?php endif; ?>
                <div class="j3-tabs" role="tablist">
                    <?php foreach ($groups as $gi => $group) : ?>
                        <button
                            class="j3-tabs__btn<?php echo $gi === 0 ? ' is-on' : ''; ?>"
                            type="button"
                            role="tab"
                            data-j3-tab="<?php echo esc_attr((string) ($group['anchor'] ?? '')); ?>"
                            aria-selected="<?php echo $gi === 0 ? 'true' : 'false'; ?>"
                        >
                            <?php echo esc_html((string) ($group['heading'] ?? '')); ?>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php foreach ($groups as $gi => $group) : ?>
                <div
                    class="j3-products__rail<?php echo $gi === 0 ? ' is-on' : ''; ?>"
                    id="<?php echo esc_attr((string) ($group['anchor'] ?? '')); ?>"
                    data-j3-panel="<?php echo esc_attr((string) ($group['anchor'] ?? '')); ?>"
                >
                    <div class="container2 j3-products__grid">
                        <?php foreach ((array) ($group['items'] ?? []) as $item) : ?>
                            <?php
                            if (!is_array($item)) {
                                continue;
                            }
                            justccell_j3_echo_product_card($item);
                            ?>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <section class="s-cta">
        <div class="container">
            <?php if ((string) ($page['cta_title'] ?? '') !== '' || (string) ($page['cta_copy'] ?? '') !== '' || (string) ($page['cta_label'] ?? '') !== '') : ?>
            <?php justccell_echo_heading((string) ($page['cta_title'] ?? ''), (string) ($page['cta_title_tag'] ?? 'h2')); ?>
            <?php if ((string) ($page['cta_copy'] ?? '') !== '') : ?>
            <p><?php echo esc_html((string) ($page['cta_copy'] ?? '')); ?></p>
            <?php endif; ?>
            <?php if ((string) ($page['cta_label'] ?? '') !== '') : ?>
            <?php
            $cta_url = trim((string) ($page['cta_url'] ?? ''));
            if ($cta_url === '') {
                $cta_url = function_exists('justccell_contact_page_url') ? justccell_contact_page_url() : home_url('/contact/');
            }
            ?>
            <a class="btn btn--primary" href="<?php echo esc_url($cta_url); ?>">
                <?php echo esc_html((string) ($page['cta_label'] ?? '')); ?>
            </a>
            <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
</article>
