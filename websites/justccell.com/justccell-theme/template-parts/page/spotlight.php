<?php
/**
 * Shared spotlight layout — 404, coming soon, and similar utility pages.
 *
 * @package Justccell
 *
 * @var array<string, mixed> $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$args = is_array($args ?? null) ? $args : [];
$eyebrow          = trim((string) ($args['eyebrow'] ?? ''));
$eyebrow_sr       = trim((string) ($args['eyebrow_sr'] ?? ''));
$title            = trim((string) ($args['title'] ?? ''));
$lede             = trim((string) ($args['lede'] ?? ''));
$primary_label    = trim((string) ($args['primary_label'] ?? ''));
$primary_url      = trim((string) ($args['primary_url'] ?? ''));
$secondary_label  = trim((string) ($args['secondary_label'] ?? ''));
$secondary_url    = trim((string) ($args['secondary_url'] ?? ''));
$show_search      = !empty($args['show_search']);
$show_showcase    = !empty($args['show_showcase']);
$shop_heading     = trim((string) ($args['shop_heading'] ?? __('Hardware in the catalogue', 'justccell')));
$shop_lede        = trim((string) ($args['shop_lede'] ?? __('Live SKUs you can open from the catalogue today.', 'justccell')));
$more             = is_array($args['more_links'] ?? null) ? $args['more_links'] : [];

$showcase = $show_showcase && function_exists('justccell_404_showcase')
    ? justccell_404_showcase()
    : ['categories' => [], 'products' => []];
$categories = is_array($showcase['categories'] ?? null) ? $showcase['categories'] : [];
$products   = is_array($showcase['products'] ?? null) ? $showcase['products'] : [];

if ($more === []) {
    $more = [
        '/discover/'   => __('Discover', 'justccell'),
        '/about/'      => __('About', 'justccell'),
        '/technology/' => __('Why Justccell', 'justccell'),
        '/ccell-3-0/'  => __('Justccell 3.0', 'justccell'),
        '/location/'   => __('Location', 'justccell'),
    ];
}

$link = static function (string $url): string {
    $url = trim($url);
    if ($url === '') {
        return home_url('/');
    }
    if (preg_match('#^https?://#i', $url) === 1) {
        return $url;
    }
    return home_url($url);
};
?>
<div class="s-404">
    <div class="container s-404__wrap">
        <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--page'); ?>

        <header class="s-404__hero">
            <?php if ($eyebrow !== '') : ?>
                <?php
                $eyebrow_class = preg_match('/^\d+$/', $eyebrow) === 1
                    ? 's-404__code'
                    : 's-404__code s-404__code--text';
                ?>
                <p class="<?php echo esc_attr($eyebrow_class); ?>" aria-hidden="true"><?php echo esc_html($eyebrow); ?></p>
            <?php endif; ?>
            <?php if ($title !== '') : ?>
                <h1 class="s-404__title">
                    <?php if ($eyebrow_sr !== '') : ?>
                        <span class="visually-hidden"><?php echo esc_html($eyebrow_sr); ?></span>
                    <?php endif; ?>
                    <?php echo esc_html($title); ?>
                </h1>
            <?php endif; ?>
            <?php if ($lede !== '') : ?>
                <p class="s-404__lede"><?php echo esc_html($lede); ?></p>
            <?php endif; ?>
            <?php if ($primary_label !== '' || $secondary_label !== '') : ?>
                <p class="s-404__actions">
                    <?php if ($primary_label !== '') : ?>
                        <a class="btn btn--primary" href="<?php echo esc_url($link($primary_url !== '' ? $primary_url : '/')); ?>">
                            <?php echo esc_html($primary_label); ?>
                        </a>
                    <?php endif; ?>
                    <?php if ($secondary_label !== '') : ?>
                        <a class="btn s-404__ghost" href="<?php echo esc_url($link($secondary_url !== '' ? $secondary_url : '/contact/')); ?>">
                            <?php echo esc_html($secondary_label); ?>
                        </a>
                    <?php endif; ?>
                </p>
            <?php endif; ?>
        </header>

        <?php if ($show_search || ($show_showcase && $categories !== [])) : ?>
            <div class="s-404__card">
                <?php if ($show_search) : ?>
                    <form class="s-404__search" role="search" method="get" action="<?php echo esc_url(home_url('/')); ?>">
                        <label for="s-spotlight-q"><?php esc_html_e('Search Justccell', 'justccell'); ?></label>
                        <div class="s-404__search-row">
                            <input id="s-spotlight-q" type="search" name="s" placeholder="<?php echo esc_attr__('What hardware are you filling?', 'justccell'); ?>" value="">
                            <button type="submit" class="btn btn--primary"><?php esc_html_e('Search', 'justccell'); ?></button>
                        </div>
                    </form>
                <?php endif; ?>
                <?php if ($show_showcase && $categories !== []) : ?>
                    <p class="s-404__browse-label"><?php esc_html_e('Browse categories', 'justccell'); ?></p>
                    <ul class="s-404__cats">
                        <?php foreach ($categories as $cat) : ?>
                            <li>
                                <a class="s-404__cat" href="<?php echo esc_url((string) $cat['url']); ?>">
                                    <span class="s-404__cat-img">
                                        <?php
                                        if (is_array($cat['item'] ?? null) && function_exists('justccell_echo_catalog_image')) {
                                            justccell_echo_catalog_image($cat['item'], [
                                                'alt'     => (string) $cat['label'],
                                                'width'   => 300,
                                                'height'  => 300,
                                                'size'    => 'medium',
                                                'loading' => 'lazy',
                                            ]);
                                        }
                                        ?>
                                    </span>
                                    <span class="s-404__cat-copy">
                                        <strong><?php echo esc_html((string) $cat['label']); ?></strong>
                                        <span><?php echo esc_html((string) $cat['blurb']); ?></span>
                                    </span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <?php if ($show_showcase && $products !== []) : ?>
        <section class="s-404__shop" aria-labelledby="s-spotlight-shop">
            <div class="container s-404__shop-head">
                <h2 id="s-spotlight-shop"><?php echo esc_html($shop_heading); ?></h2>
                <?php if ($shop_lede !== '') : ?>
                    <p><?php echo esc_html($shop_lede); ?></p>
                <?php endif; ?>
            </div>
            <div class="container s-404__rail" data-rail="spotlight">
                <button class="s-404__rail-btn s-404__rail-btn--prev" type="button" data-rail-prev aria-label="<?php esc_attr_e('Previous products', 'justccell'); ?>"></button>
                <div class="s-404__scroller" data-rail-scroller>
                    <?php foreach ($products as $item) : ?>
                        <?php
                        $meta  = function_exists('justccell_catalog_explore_meta') ? justccell_catalog_explore_meta($item) : ['blurb' => '', 'capacity' => ''];
                        $blurb = trim((string) ($meta['blurb'] ?? ''));
                        if ($blurb === '' && isset($item['specs'][0])) {
                            $blurb = (string) $item['specs'][0];
                        }
                        ?>
                        <a class="s-404__sku" href="<?php echo esc_url(justccell_item_url($item)); ?>">
                            <span class="s-404__sku-img">
                                <?php
                                if (function_exists('justccell_echo_catalog_image')) {
                                    justccell_echo_catalog_image($item, [
                                        'alt'     => (string) ($item['name'] ?? ''),
                                        'width'   => 360,
                                        'height'  => 360,
                                        'loading' => 'lazy',
                                    ]);
                                }
                                ?>
                            </span>
                            <strong><?php echo esc_html((string) ($item['name'] ?? '')); ?></strong>
                            <?php if ($blurb !== '') : ?>
                                <span><?php echo esc_html($blurb); ?></span>
                            <?php endif; ?>
                        </a>
                    <?php endforeach; ?>
                </div>
                <button class="s-404__rail-btn s-404__rail-btn--next" type="button" data-rail-next aria-label="<?php esc_attr_e('Next products', 'justccell'); ?>"></button>
            </div>
        </section>
    <?php endif; ?>

    <?php if ($more !== []) : ?>
        <nav class="s-404__more" aria-label="<?php echo esc_attr__('More on Justccell', 'justccell'); ?>">
            <div class="container">
                <ul>
                    <?php foreach ($more as $path => $label) : ?>
                        <li><a href="<?php echo esc_url(home_url($path)); ?>"><?php echo esc_html($label); ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </nav>
    <?php endif; ?>
</div>
