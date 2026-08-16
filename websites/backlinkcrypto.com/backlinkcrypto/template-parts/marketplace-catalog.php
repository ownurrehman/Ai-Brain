<?php
/**
 * Marketplace catalog — featured + filters + table + mobile cards.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/** @var array{query?:WP_Query,hide_featured?:bool} $args */
$args = is_array($args ?? null) ? $args : [];
$query = $args['query'] ?? backlinkcrypto_marketplace_query();
$total_sites = (int) $query->post_count;
$niches = [];
$languages = [];
$featured_products = [];
$show_featured = empty($args['hide_featured']);
$metrics_as_of = gmdate('Y-m-d');

if ($query->have_posts() && function_exists('wc_get_product')) {
    foreach ($query->posts as $post_obj) {
        $p = wc_get_product($post_obj->ID);
        if ($p && $p->get_featured()) {
            $featured_products[] = $p;
        }
        $as = (string) get_post_meta($post_obj->ID, '_bc_metrics_as_of', true);
        if ($as !== '') {
            $metrics_as_of = $as;
        }
    }
}
?>

<?php if ($show_featured && $featured_products !== []) : ?>
<section class="bc-featured" id="bc-featured" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head bc-featured__head">
            <div>
                <p class="bc-eyebrow"><?php esc_html_e('Hand-picked', 'backlinkcrypto'); ?></p>
                <h2><?php esc_html_e('Featured sites', 'backlinkcrypto'); ?></h2>
                <p><?php esc_html_e('Top picks — pinned above the full marketplace.', 'backlinkcrypto'); ?></p>
            </div>
            <a class="bc-btn bc-btn--ghost bc-btn--compact" href="#bc-marketplace"><?php esc_html_e('See all sites', 'backlinkcrypto'); ?></a>
        </div>
        <div class="bc-featured__grid">
            <?php foreach ($featured_products as $fp) :
                $fm = backlinkcrypto_product_metrics($fp->get_id());
                $fdomain = $fm['domain'] !== '' ? $fm['domain'] : $fp->get_name();
                $fdr = $fm['dr'] !== '' && $fm['dr'] !== null ? (int) $fm['dr'] : null;
                $fda = $fm['da'] !== '' && $fm['da'] !== null ? (int) $fm['da'] : null;
                $flangs = $fm['languages'] ?? ['EN'];
                $flink = get_permalink($fp->get_id());
                ?>
                <article class="bc-featured__card">
                    <div class="bc-featured__top">
                        <span class="bc-featured__badge"><?php esc_html_e('Featured', 'backlinkcrypto'); ?></span>
                        <?php if ($fm['verified']) : ?>
                            <span class="bc-tag bc-tag--ok"><?php esc_html_e('Verified', 'backlinkcrypto'); ?></span>
                        <?php endif; ?>
                    </div>
                    <h3 class="bc-featured__domain">
                        <a href="<?php echo esc_url($flink); ?>"><?php echo esc_html($fdomain); ?></a>
                    </h3>
                    <div class="bc-featured__metrics">
                        <div><span>DR</span><strong><?php echo $fdr === null ? '—' : esc_html((string) $fdr); ?></strong></div>
                        <div><span>DA</span><strong><?php echo $fda === null ? '—' : esc_html((string) $fda); ?></strong></div>
                        <div><span><?php esc_html_e('Traffic', 'backlinkcrypto'); ?></span><strong><?php echo esc_html(backlinkcrypto_format_traffic($fm['traffic'] !== '' ? $fm['traffic'] : null)); ?></strong></div>
                    </div>
                    <div class="bc-featured__meta">
                        <span class="bc-pill"><?php echo esc_html($fm['niche']); ?></span>
                        <?php backlinkcrypto_render_language_badges($flangs); ?>
                    </div>
                    <div class="bc-featured__footer">
                        <span class="bc-featured__price"><?php echo wp_kses_post($fp->get_price_html()); ?></span>
                        <?php if ($fp->is_purchasable() && $fp->is_in_stock()) : ?>
                            <button type="button" class="bc-add" data-product_id="<?php echo esc_attr((string) $fp->get_id()); ?>">
                                <?php esc_html_e('ADD', 'backlinkcrypto'); ?>
                            </button>
                        <?php else : ?>
                            <span class="bc-add is-disabled"><?php esc_html_e('ADD', 'backlinkcrypto'); ?></span>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="bc-marketplace" id="bc-marketplace">
    <div class="bc-container">
        <div class="bc-section-head" data-bc-reveal>
            <h2><?php esc_html_e('All sites', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Sortable metric table — filter by DR, price, traffic, niche & language.', 'backlinkcrypto'); ?></p>
            <p class="bc-marketplace__count" id="bc-result-count" aria-live="polite">
                <strong data-bc-count><?php echo esc_html((string) $total_sites); ?></strong>
                <span><?php esc_html_e('sites listed', 'backlinkcrypto'); ?></span>
            </p>
            <p class="bc-marketplace__asof">
                <?php
                printf(
                    /* translators: %s: date Y-m-d */
                    esc_html__('SEO metrics as of %s (industry tools; values can change).', 'backlinkcrypto'),
                    esc_html($metrics_as_of)
                );
                ?>
            </p>
            <p class="bc-marketplace__bulk-hint">
                <?php esc_html_e('Tip: select multiple sites, then use “Add selected” to cart in one step.', 'backlinkcrypto'); ?>
            </p>
        </div>

        <form class="bc-filters" id="bc-filters" autocomplete="off" data-bc-reveal>
            <label class="bc-filter">
                <span><?php esc_html_e('Search', 'backlinkcrypto'); ?></span>
                <input type="search" name="q" id="bc-q" placeholder="domain or name…" />
            </label>
            <label class="bc-filter">
                <span><?php esc_html_e('Min DR', 'backlinkcrypto'); ?></span>
                <input type="number" name="min_dr" id="bc-min-dr" min="0" max="100" placeholder="0" />
            </label>
            <label class="bc-filter">
                <span><?php esc_html_e('Max DR', 'backlinkcrypto'); ?></span>
                <input type="number" name="max_dr" id="bc-max-dr" min="0" max="100" placeholder="100" />
            </label>
            <label class="bc-filter">
                <span><?php esc_html_e('Min price', 'backlinkcrypto'); ?></span>
                <input type="number" name="min_price" id="bc-min-price" min="0" placeholder="0" />
            </label>
            <label class="bc-filter">
                <span><?php esc_html_e('Max price', 'backlinkcrypto'); ?></span>
                <input type="number" name="max_price" id="bc-max-price" min="0" placeholder="∞" />
            </label>
            <label class="bc-filter">
                <span><?php esc_html_e('Min traffic', 'backlinkcrypto'); ?></span>
                <input type="number" name="min_traffic" id="bc-min-traffic" min="0" placeholder="0" />
            </label>
            <label class="bc-filter">
                <span><?php esc_html_e('Niche', 'backlinkcrypto'); ?></span>
                <select name="niche" id="bc-niche">
                    <option value=""><?php esc_html_e('All', 'backlinkcrypto'); ?></option>
                </select>
            </label>
            <label class="bc-filter">
                <span><?php esc_html_e('Language', 'backlinkcrypto'); ?></span>
                <select name="language" id="bc-language">
                    <option value=""><?php esc_html_e('All', 'backlinkcrypto'); ?></option>
                </select>
            </label>
            <label class="bc-filter bc-filter--check">
                <span><?php esc_html_e('Verified only', 'backlinkcrypto'); ?></span>
                <input type="checkbox" name="verified" id="bc-verified" />
            </label>
            <button type="button" class="bc-btn bc-btn--ghost" id="bc-reset"><?php esc_html_e('Reset', 'backlinkcrypto'); ?></button>
        </form>

        <?php if ($query->have_posts() && function_exists('wc_get_product')) : ?>
            <div class="bc-table-wrap bc-table-wrap--desktop">
                <table class="bc-table" id="bc-table">
                    <thead>
                        <tr>
                            <th class="bc-col-select">
                                <label class="bc-check">
                                    <input type="checkbox" id="bc-select-all" aria-label="<?php esc_attr_e('Select all visible sites', 'backlinkcrypto'); ?>" />
                                </label>
                            </th>
                            <th>
                                <button type="button" class="bc-sort" data-sort="domain" data-type="string">
                                    <?php esc_html_e('Domain', 'backlinkcrypto'); ?>
                                    <span class="bc-sort__arrows" aria-hidden="true"><i></i><i></i></span>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="bc-sort" data-sort="languages" data-type="string">
                                    <?php esc_html_e('Language', 'backlinkcrypto'); ?>
                                    <span class="bc-sort__arrows" aria-hidden="true"><i></i><i></i></span>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="bc-sort" data-sort="da" data-type="number">
                                    <?php esc_html_e('DA', 'backlinkcrypto'); ?>
                                    <span class="bc-sort__arrows" aria-hidden="true"><i></i><i></i></span>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="bc-sort is-desc" data-sort="dr" data-type="number" aria-sort="descending">
                                    <?php esc_html_e('DR', 'backlinkcrypto'); ?>
                                    <span class="bc-sort__arrows" aria-hidden="true"><i></i><i></i></span>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="bc-sort" data-sort="traffic" data-type="number">
                                    <?php esc_html_e('Traffic', 'backlinkcrypto'); ?>
                                    <span class="bc-sort__arrows" aria-hidden="true"><i></i><i></i></span>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="bc-sort" data-sort="niche" data-type="string">
                                    <?php esc_html_e('Niche', 'backlinkcrypto'); ?>
                                    <span class="bc-sort__arrows" aria-hidden="true"><i></i><i></i></span>
                                </button>
                            </th>
                            <th>
                                <button type="button" class="bc-sort" data-sort="price" data-type="number">
                                    <?php esc_html_e('Price', 'backlinkcrypto'); ?>
                                    <span class="bc-sort__arrows" aria-hidden="true"><i></i><i></i></span>
                                </button>
                            </th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $row_i = 0;
                        $card_rows = [];
                        while ($query->have_posts()) {
                            $query->the_post();
                            $product = wc_get_product(get_the_ID());
                            if (!$product) {
                                continue;
                            }
                            $m = backlinkcrypto_product_metrics($product->get_id());
                            $domain_label = $m['domain'] !== '' ? $m['domain'] : $product->get_name();
                            $permalink = get_permalink($product->get_id());
                            $niches[$m['niche']] = true;
                            $lang_codes = $m['languages'] ?? ['EN'];
                            foreach ($lang_codes as $lc) {
                                $languages[$lc] = true;
                            }
                            $dr = $m['dr'] !== '' && $m['dr'] !== null ? (int) $m['dr'] : '';
                            $da = $m['da'] !== '' && $m['da'] !== null ? (int) $m['da'] : '';
                            $traffic = $m['traffic'] !== '' && $m['traffic'] !== null ? (int) $m['traffic'] : 0;
                            $price_num = (float) $product->get_price();
                            $langs_attr = strtolower(implode(',', $lang_codes));
                            $delay = min($row_i, 24) * 18;
                            $is_featured = $product->get_featured();
                            $row_asof = (string) ($m['as_of'] ?? $metrics_as_of);
                            $row_i++;
                            $card_rows[] = compact('product', 'm', 'domain_label', 'permalink', 'lang_codes', 'dr', 'da', 'traffic', 'price_num', 'is_featured', 'delay', 'row_asof');
                            $can_buy = $product->is_purchasable() && $product->is_in_stock();
                            ?>
                            <tr
                                class="bc-row bc-row--anim<?php echo $is_featured ? ' is-featured' : ''; ?>"
                                style="--bc-delay: <?php echo esc_attr((string) $delay); ?>ms"
                                data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>"
                                data-domain="<?php echo esc_attr(strtolower($domain_label)); ?>"
                                data-name="<?php echo esc_attr(strtolower($product->get_name())); ?>"
                                data-dr="<?php echo esc_attr((string) ($dr === '' ? -1 : $dr)); ?>"
                                data-da="<?php echo esc_attr((string) ($da === '' ? -1 : $da)); ?>"
                                data-traffic="<?php echo esc_attr((string) $traffic); ?>"
                                data-price="<?php echo esc_attr((string) $price_num); ?>"
                                data-niche="<?php echo esc_attr($m['niche']); ?>"
                                data-language="<?php echo esc_attr($langs_attr); ?>"
                                data-languages="<?php echo esc_attr($langs_attr); ?>"
                                data-verified="<?php echo $m['verified'] ? '1' : '0'; ?>"
                                data-featured="<?php echo $is_featured ? '1' : '0'; ?>"
                                data-asof="<?php echo esc_attr($row_asof); ?>"
                            >
                                <td class="bc-col-select">
                                    <?php if ($can_buy) : ?>
                                        <label class="bc-check">
                                            <input type="checkbox" class="bc-row-check" value="<?php echo esc_attr((string) $product->get_id()); ?>" aria-label="<?php echo esc_attr(sprintf(/* translators: %s: domain */ __('Select %s', 'backlinkcrypto'), $domain_label)); ?>" />
                                        </label>
                                    <?php endif; ?>
                                </td>
                                <td class="bc-col-domain">
                                    <strong>
                                        <?php if ($is_featured) : ?>
                                            <span class="bc-featured-mark" title="<?php esc_attr_e('Featured', 'backlinkcrypto'); ?>">★</span>
                                        <?php endif; ?>
                                        <a class="bc-domain-link" href="<?php echo esc_url($permalink); ?>"><?php echo esc_html($domain_label); ?></a>
                                    </strong>
                                    <?php if ($row_asof !== '') : ?>
                                        <div class="bc-row-asof" title="<?php echo esc_attr(sprintf(/* translators: %s: date */ __('Metrics as of %s', 'backlinkcrypto'), $row_asof)); ?>">
                                            <?php echo esc_html($row_asof); ?>
                                        </div>
                                    <?php endif; ?>
                                    <div class="bc-tags">
                                        <?php if ($m['verified']) : ?>
                                            <span class="bc-tag bc-tag--ok"><?php esc_html_e('Verified', 'backlinkcrypto'); ?></span>
                                        <?php endif; ?>
                                        <?php if ($m['dofollow']) : ?>
                                            <span class="bc-tag"><?php esc_html_e('Dofollow', 'backlinkcrypto'); ?></span>
                                        <?php else : ?>
                                            <span class="bc-tag bc-tag--warn"><?php esc_html_e('Nofollow', 'backlinkcrypto'); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <td class="bc-col-lang"><?php backlinkcrypto_render_language_badges($lang_codes); ?></td>
                                <td><?php echo $da === '' ? '—' : esc_html((string) $da); ?></td>
                                <td>
                                    <?php if ($dr === '') : ?>
                                        —
                                    <?php else : ?>
                                        <span class="bc-badge <?php echo esc_attr(backlinkcrypto_dr_class($dr)); ?>" title="<?php echo esc_attr(sprintf(/* translators: %s: date */ __('Metrics as of %s', 'backlinkcrypto'), $row_asof)); ?>"><?php echo esc_html((string) $dr); ?></span>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo esc_html(backlinkcrypto_format_traffic($m['traffic'] !== '' ? $m['traffic'] : null)); ?></td>
                                <td><span class="bc-pill"><?php echo esc_html($m['niche']); ?></span></td>
                                <td class="bc-col-price"><?php echo wp_kses_post($product->get_price_html()); ?></td>
                                <td class="bc-col-action">
                                    <?php if ($can_buy) : ?>
                                        <button type="button" class="bc-add" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>">
                                            <?php esc_html_e('ADD', 'backlinkcrypto'); ?>
                                        </button>
                                    <?php else : ?>
                                        <span class="bc-add is-disabled"><?php esc_html_e('ADD', 'backlinkcrypto'); ?></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                        }
                        wp_reset_postdata();
                        ?>
                    </tbody>
                </table>
            </div>

            <div class="bc-market-cards" id="bc-market-cards" aria-label="<?php esc_attr_e('Site list', 'backlinkcrypto'); ?>">
                <?php foreach ($card_rows as $cr) :
                    /** @var WC_Product $product */
                    $product = $cr['product'];
                    $m = $cr['m'];
                    $domain_label = $cr['domain_label'];
                    $permalink = $cr['permalink'];
                    $lang_codes = $cr['lang_codes'];
                    $dr = $cr['dr'];
                    $da = $cr['da'];
                    $is_featured = $cr['is_featured'];
                    $langs_attr = strtolower(implode(',', $lang_codes));
                    $row_asof = (string) ($cr['row_asof'] ?? '');
                    $can_buy = $product->is_purchasable() && $product->is_in_stock();
                    ?>
                    <article
                        class="bc-market-card bc-row<?php echo $is_featured ? ' is-featured' : ''; ?>"
                        data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>"
                        data-domain="<?php echo esc_attr(strtolower($domain_label)); ?>"
                        data-name="<?php echo esc_attr(strtolower($product->get_name())); ?>"
                        data-dr="<?php echo esc_attr((string) ($dr === '' ? -1 : $dr)); ?>"
                        data-da="<?php echo esc_attr((string) ($da === '' ? -1 : $da)); ?>"
                        data-traffic="<?php echo esc_attr((string) $cr['traffic']); ?>"
                        data-price="<?php echo esc_attr((string) $cr['price_num']); ?>"
                        data-niche="<?php echo esc_attr($m['niche']); ?>"
                        data-language="<?php echo esc_attr($langs_attr); ?>"
                        data-languages="<?php echo esc_attr($langs_attr); ?>"
                        data-verified="<?php echo $m['verified'] ? '1' : '0'; ?>"
                        data-featured="<?php echo $is_featured ? '1' : '0'; ?>"
                        data-asof="<?php echo esc_attr($row_asof); ?>"
                    >
                        <div class="bc-market-card__top">
                            <label class="bc-check bc-market-card__check">
                                <?php if ($can_buy) : ?>
                                    <input type="checkbox" class="bc-row-check" value="<?php echo esc_attr((string) $product->get_id()); ?>" aria-label="<?php echo esc_attr(sprintf(/* translators: %s: domain */ __('Select %s', 'backlinkcrypto'), $domain_label)); ?>" />
                                <?php endif; ?>
                            </label>
                            <a class="bc-market-card__domain" href="<?php echo esc_url($permalink); ?>">
                                <?php if ($is_featured) : ?><span class="bc-featured-mark">★</span><?php endif; ?>
                                <?php echo esc_html($domain_label); ?>
                            </a>
                            <span class="bc-market-card__price"><?php echo wp_kses_post($product->get_price_html()); ?></span>
                        </div>
                        <?php if ($row_asof !== '') : ?>
                            <p class="bc-row-asof"><?php printf(esc_html__('Metrics as of %s', 'backlinkcrypto'), esc_html($row_asof)); ?></p>
                        <?php endif; ?>
                        <div class="bc-market-card__metrics">
                            <span>DR <strong><?php echo $dr === '' ? '—' : esc_html((string) $dr); ?></strong></span>
                            <span>DA <strong><?php echo $da === '' ? '—' : esc_html((string) $da); ?></strong></span>
                            <span><?php esc_html_e('Traffic', 'backlinkcrypto'); ?> <strong><?php echo esc_html(backlinkcrypto_format_traffic($m['traffic'] !== '' ? $m['traffic'] : null)); ?></strong></span>
                        </div>
                        <div class="bc-market-card__meta">
                            <span class="bc-pill"><?php echo esc_html($m['niche']); ?></span>
                            <?php backlinkcrypto_render_language_badges($lang_codes); ?>
                            <?php if ($m['verified']) : ?><span class="bc-tag bc-tag--ok"><?php esc_html_e('Verified', 'backlinkcrypto'); ?></span><?php endif; ?>
                            <?php if ($m['dofollow']) : ?><span class="bc-tag"><?php esc_html_e('Dofollow', 'backlinkcrypto'); ?></span><?php endif; ?>
                        </div>
                        <div class="bc-market-card__actions">
                            <a class="bc-btn bc-btn--ghost bc-btn--compact" href="<?php echo esc_url($permalink); ?>"><?php esc_html_e('Details', 'backlinkcrypto'); ?></a>
                            <?php if ($can_buy) : ?>
                                <button type="button" class="bc-add" data-product_id="<?php echo esc_attr((string) $product->get_id()); ?>"><?php esc_html_e('ADD', 'backlinkcrypto'); ?></button>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>

            <div class="bc-bulk-bar" id="bc-bulk-bar" hidden>
                <div class="bc-bulk-bar__inner">
                    <span class="bc-bulk-bar__count">
                        <strong data-bc-selected-count>0</strong>
                        <?php esc_html_e('selected', 'backlinkcrypto'); ?>
                    </span>
                    <button type="button" class="bc-btn bc-btn--ghost bc-btn--compact" id="bc-clear-selected"><?php esc_html_e('Clear', 'backlinkcrypto'); ?></button>
                    <button type="button" class="bc-btn bc-btn--primary bc-btn--compact" id="bc-add-selected" data-bc-add-selected>
                        <?php esc_html_e('Add selected', 'backlinkcrypto'); ?>
                    </button>
                </div>
            </div>

            <p class="bc-empty" id="bc-empty" hidden><?php esc_html_e('No sites match these filters.', 'backlinkcrypto'); ?></p>
        <?php else : ?>
            <div class="bc-empty is-visible">
                <p><?php esc_html_e('Marketplace is seeding… refresh in a moment.', 'backlinkcrypto'); ?></p>
            </div>
        <?php endif; ?>
    </div>
</section>

<script type="application/json" id="bc-filter-options">
<?php
echo wp_json_encode([
    'niches'    => array_keys($niches),
    'languages' => array_keys($languages),
]);
?>
</script>
