<?php
/**
 * About page layout.
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

$files = array_filter([
    (string) ($page['image_key'] ?? ''),
    (string) ($page['image_mobile_key'] ?? ''),
    (string) ($page['company_image_key'] ?? ''),
]);
foreach (array_merge((array) ($page['culture'] ?? []), (array) ($page['customer'] ?? [])) as $card) {
    if (!is_array($card)) {
        continue;
    }
    $key = (string) ($card['image_key'] ?? '');
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

$company_copy = trim((string) ($page['company_copy'] ?? ''));
if ($company_copy === '') {
    foreach ((array) ($page['sections'] ?? []) as $section) {
        if (is_array($section) && ($section['id'] ?? '') === 'company-introduction') {
            $company_copy = (string) ($section['copy'] ?? '');
            break;
        }
    }
}
$company_paras = array_values(array_filter(array_map('trim', preg_split('/\n\s*\n/', $company_copy) ?: [])));
$years = is_array($page['timeline_years'] ?? null) ? $page['timeline_years'] : [];
$culture = is_array($page['culture'] ?? null) ? $page['culture'] : [];
$customer = is_array($page['customer'] ?? null) ? $page['customer'] : [];
?>
<article class="a-clone">
    <section class="a-hero">
        <div class="a-hero__media">
            <span class="a-hero__desktop">
                <?php $echo_img((int) ($page['image_id'] ?? 0), (string) ($page['image_key'] ?? ''), [
                    'alt'     => (string) ($page['title'] ?? ''),
                    'width'   => 1920,
                    'height'  => 860,
                ]); ?>
            </span>
            <span class="a-hero__mobile">
                <?php $echo_img((int) ($page['image_mobile_id'] ?? 0), (string) ($page['image_mobile_key'] ?? ''), [
                    'alt'     => (string) ($page['title'] ?? ''),
                    'width'   => 750,
                    'height'  => 700,
                ]); ?>
            </span>
        </div>
        <div class="a-hero__txt">
            <?php justccell_echo_heading((string) ($page['title'] ?? ''), (string) ($page['title_tag'] ?? 'h1')); ?>
        </div>
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--hero a-hero__crumbs'); ?>
    </section>

    <?php if ($culture !== []) : ?>
        <section class="a-culture" id="corporate-culture">
            <div class="container">
                <div class="a-subh">
                    <?php justccell_echo_heading((string) ($page['heading_culture'] ?? __('Corporate Culture', 'justccell')), (string) ($page['heading_culture_tag'] ?? 'h2')); ?>
                </div>
                <div class="a-culture__box" data-culture>
                    <?php foreach ($culture as $i => $card) : ?>
                        <button
                            class="a-culture__card<?php echo $i === 0 ? ' is-on' : ''; ?>"
                            type="button"
                            data-culture-card
                            aria-pressed="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                        >
                            <?php $echo_img((int) ($card['image_id'] ?? 0), (string) ($card['image_key'] ?? ''), [
                                'alt'     => (string) ($card['title'] ?? ''),
                                'width'   => 800,
                                'height'  => 578,
                            ]); ?>
                            <span class="a-culture__txt">
                                <?php justccell_echo_heading((string) ($card['title'] ?? ''), (string) ($card['title_tag'] ?? 'h3')); ?>
                                <span class="a-culture__copy"><?php echo esc_html((string) ($card['copy'] ?? '')); ?></span>
                            </span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <section class="a-company" id="company-introduction">
        <div class="container a-company__box">
            <div class="a-company__img">
                <?php $echo_img((int) ($page['company_image_id'] ?? 0), (string) ($page['company_image_key'] ?? ''), [
                    'alt'     => (string) ($page['heading_company'] ?? __('Company Introduction', 'justccell')),
                    'width'   => 740,
                    'height'  => 680,
                ]); ?>
            </div>
            <div class="a-company__txt">
                <?php justccell_echo_heading((string) ($page['heading_company'] ?? __('Company Introduction', 'justccell')), (string) ($page['heading_company_tag'] ?? 'h2')); ?>
                <?php if (($page['tagline'] ?? '') !== '') : ?>
                    <p class="a-company__tag"><?php echo esc_html((string) $page['tagline']); ?></p>
                <?php endif; ?>
                <?php foreach ($company_paras as $para) : ?>
                    <p><?php echo esc_html($para); ?></p>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <?php if ($years !== []) : ?>
        <section class="a-history" id="brand-history" data-history>
            <div class="container">
                <div class="a-subh a-subh--left">
                    <?php justccell_echo_heading((string) ($page['heading_history'] ?? __('Brand History', 'justccell')), (string) ($page['heading_history_tag'] ?? 'h2')); ?>
                </div>
                <div class="a-history__stage">
                    <?php foreach ($years as $i => $row) : ?>
                        <div class="a-history__slide<?php echo $i === 0 ? ' is-on' : ''; ?>" data-history-slide>
                            <div class="a-history__copy">
                                <?php foreach ((array) ($row['items'] ?? []) as $item) : ?>
                                    <p>· <?php echo esc_html((string) $item); ?></p>
                                <?php endforeach; ?>
                            </div>
                            <span class="a-history__year-lg" aria-hidden="true"><?php echo esc_html((string) ($row['year'] ?? '')); ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="a-history__nav">
                    <button class="a-history__arrow a-history__arrow--prev" type="button" data-history-prev aria-label="<?php esc_attr_e('Previous year', 'justccell'); ?>"></button>
                    <div class="a-history__years" role="tablist">
                        <?php foreach ($years as $i => $row) : ?>
                            <button
                                class="a-history__year<?php echo $i === 0 ? ' is-on' : ''; ?>"
                                type="button"
                                role="tab"
                                data-history-year
                                aria-selected="<?php echo $i === 0 ? 'true' : 'false'; ?>"
                            ><?php echo esc_html((string) ($row['year'] ?? '')); ?></button>
                        <?php endforeach; ?>
                    </div>
                    <button class="a-history__arrow a-history__arrow--next" type="button" data-history-next aria-label="<?php esc_attr_e('Next year', 'justccell'); ?>"></button>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($customer !== []) : ?>
        <section class="a-listen" id="customer-centricity">
            <div class="container">
                <div class="a-subh">
                    <?php justccell_echo_heading((string) ($page['heading_customer'] ?? __('Customer Centricity', 'justccell')), (string) ($page['heading_customer_tag'] ?? 'h2')); ?>
                </div>
                <div class="a-listen__grid">
                    <?php foreach ($customer as $card) : ?>
                        <article class="a-listen__card">
                            <div class="a-listen__img">
                                <?php $echo_img((int) ($card['image_id'] ?? 0), (string) ($card['image_key'] ?? ''), [
                                    'alt'     => (string) ($card['title'] ?? ''),
                                    'width'   => 800,
                                    'height'  => 380,
                                ]); ?>
                            </div>
                            <div class="a-listen__txt">
                                <?php justccell_echo_heading((string) ($card['title'] ?? ''), (string) ($card['title_tag'] ?? 'h3')); ?>
                                <p><?php echo esc_html((string) ($card['copy'] ?? '')); ?></p>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endif; ?>
</article>
