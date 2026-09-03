<?php
/**
 * Location — ACF on Pages → Locations. Single UK office, About/Contact layout.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$page = function_exists('justccell_get_locations_page_data') ? justccell_get_locations_page_data() : [];
if ($page === []) {
    return;
}

$title = (string) ($page['title'] ?? __('Location', 'justccell'));
$hero_id = (int) ($page['image_id'] ?? 0);
$hero_mobile_id = (int) ($page['image_mobile_id'] ?? 0);
if ($hero_mobile_id < 1) {
    $hero_mobile_id = $hero_id;
}
$items = is_array($page['items'] ?? null) ? $page['items'] : [];
$office = [];
foreach ($items as $row) {
    if (is_array($row)) {
        $office = $row;
        break;
    }
}
$cta_url = (string) ($page['cta_url'] ?? '');
$cta_label = (string) ($page['cta_label'] ?? '');

$heading        = (string) ($office['title'] ?? '');
$country        = (string) ($office['country'] ?? '');
$summary        = (string) ($office['summary'] ?? '');
$lede           = (string) ($page['lede'] ?? '');
$address        = (string) ($office['address'] ?? '');
$phone          = (string) ($office['phone'] ?? '');
$phone_label    = (string) ($office['phone_label'] ?? '');
$email          = (string) ($office['email'] ?? '');
$hours          = (string) ($office['hours'] ?? '');
$directions     = (string) ($office['directions_url'] ?? '');
$directions_lbl = (string) ($office['map_label'] ?? __('Get directions', 'justccell'));
$gmb            = (string) ($office['gmb_url'] ?? '');
$embed_src      = (string) ($office['embed_src'] ?? '');
$img_id         = (int) ($office['image_id'] ?? 0);
$tel            = preg_replace('/[^0-9+]/', '', $phone) ?? '';
$map_label      = $country !== '' ? $country : ($heading !== '' ? $heading : $title);
?>
<article class="jc-location">
    <section class="a-hero jc-location__hero">
        <?php if ($hero_id > 0) : ?>
            <div class="a-hero__media">
                <span class="a-hero__desktop">
                    <?php
                    echo wp_get_attachment_image($hero_id, 'full', false, [
                        'alt'           => $title,
                        'class'         => 'jc-location__hero-img',
                        'width'         => 1920,
                        'height'        => 860,
                        'decoding'      => 'async',
                        'fetchpriority' => 'high',
                    ]);
                    ?>
                </span>
                <?php if ($hero_mobile_id > 0) : ?>
                    <span class="a-hero__mobile">
                        <?php
                        echo wp_get_attachment_image($hero_mobile_id, 'full', false, [
                            'alt'      => $title,
                            'class'    => 'jc-location__hero-img',
                            'width'    => 750,
                            'height'   => 700,
                            'decoding' => 'async',
                        ]);
                        ?>
                    </span>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <div class="a-hero__txt">
            <?php justccell_echo_heading($title, (string) ($page['title_tag'] ?? 'h1')); ?>
        </div>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero a-hero__crumbs'); ?>
    </section>

    <?php if ($office !== []) : ?>
        <section class="jc-location__office" aria-label="<?php echo esc_attr($heading !== '' ? $heading : $title); ?>">
            <div class="container jc-location__box">
                <div class="jc-location__copy">
                    <?php if ($country !== '') : ?>
                        <p class="jc-location__country"><?php echo esc_html($country); ?></p>
                    <?php endif; ?>
                    <?php if ($heading !== '') : ?>
                        <?php justccell_echo_heading($heading, (string) ($office['title_tag'] ?? 'h2')); ?>
                    <?php endif; ?>
                    <?php if ($lede !== '') : ?>
                        <p class="jc-location__lede"><?php echo esc_html($lede); ?></p>
                    <?php elseif ($summary !== '') : ?>
                        <p class="jc-location__lede"><?php echo esc_html($summary); ?></p>
                    <?php endif; ?>

                    <dl class="jc-location__meta">
                        <?php if ($address !== '') : ?>
                            <div class="jc-location__meta-row">
                                <dt><?php esc_html_e('Address', 'justccell'); ?></dt>
                                <dd><?php echo nl2br(esc_html($address)); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if ($hours !== '') : ?>
                            <div class="jc-location__meta-row">
                                <dt><?php esc_html_e('Hours', 'justccell'); ?></dt>
                                <dd><?php echo esc_html($hours); ?></dd>
                            </div>
                        <?php endif; ?>
                        <?php if ($phone !== '') : ?>
                            <div class="jc-location__meta-row">
                                <dt><?php echo esc_html($phone_label !== '' ? $phone_label : __('Phone', 'justccell')); ?></dt>
                                <dd>
                                    <?php if ($tel !== '') : ?>
                                        <a href="<?php echo esc_url('tel:' . $tel); ?>"><?php echo esc_html($phone); ?></a>
                                    <?php else : ?>
                                        <?php echo esc_html($phone); ?>
                                    <?php endif; ?>
                                </dd>
                            </div>
                        <?php endif; ?>
                        <?php if ($email !== '') : ?>
                            <div class="jc-location__meta-row">
                                <dt><?php esc_html_e('Email', 'justccell'); ?></dt>
                                <dd><a href="<?php echo esc_url('mailto:' . $email); ?>"><?php echo esc_html($email); ?></a></dd>
                            </div>
                        <?php endif; ?>
                    </dl>

                    <?php if ($directions !== '' || $gmb !== '' || ($cta_label !== '' && $cta_url !== '')) : ?>
                        <div class="jc-location__actions">
                            <?php if ($directions !== '') : ?>
                                <a class="btn btn--primary" href="<?php echo esc_url($directions); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php echo esc_html($directions_lbl); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($cta_label !== '' && $cta_url !== '') : ?>
                                <a class="btn btn--ghost" href="<?php echo esc_url($cta_url); ?>">
                                    <?php echo esc_html($cta_label); ?>
                                </a>
                            <?php endif; ?>
                            <?php if ($gmb !== '') : ?>
                                <a class="btn btn--ghost" href="<?php echo esc_url($gmb); ?>" target="_blank" rel="noopener noreferrer">
                                    <?php esc_html_e('Google Business Profile', 'justccell'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="jc-location__map">
                    <?php if ($embed_src !== '') : ?>
                        <iframe
                            class="jc-location__map-frame"
                            title="<?php echo esc_attr(sprintf(
                                /* translators: %s: location name */
                                __('Map of %s', 'justccell'),
                                $map_label
                            )); ?>"
                            src="<?php echo esc_url($embed_src); ?>"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            allowfullscreen
                        ></iframe>
                    <?php elseif ($img_id > 0) : ?>
                        <?php
                        echo wp_get_attachment_image($img_id, 'full', false, [
                            'alt'     => $heading !== '' ? $heading : $map_label,
                            'class'   => 'jc-location__photo',
                            'loading' => 'lazy',
                        ]);
                        ?>
                    <?php endif; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>
