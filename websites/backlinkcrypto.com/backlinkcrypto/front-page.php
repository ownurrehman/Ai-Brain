<?php
/**
 * Homepage landing — product-in-hero, use-cases, motion, proof, CTAs.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$market_url = backlinkcrypto_marketplace_url();
$query = backlinkcrypto_marketplace_query();
$total_sites = (int) $query->post_count;
$writing = function_exists('backlinkcrypto_writing_addon_price') ? backlinkcrypto_writing_addon_price() : 149.0;

$featured_products = [];
$preview_rows = [];
if ($query->have_posts() && function_exists('wc_get_product')) {
    foreach ($query->posts as $post_obj) {
        $p = wc_get_product($post_obj->ID);
        if (!$p) {
            continue;
        }
        if ($p->get_featured() && count($featured_products) < 6) {
            $featured_products[] = $p;
        }
        if (count($preview_rows) < 5) {
            $preview_rows[] = $p;
        }
    }
    wp_reset_postdata();
}
if ($preview_rows === [] && $featured_products !== []) {
    $preview_rows = array_slice($featured_products, 0, 5);
}
?>

<section class="bc-hero bc-hero--pro bc-hero--landing" data-bc-reveal>
    <div class="bc-hero__glow" aria-hidden="true"></div>
    <div class="bc-container bc-hero__inner">
        <div class="bc-hero__copy">
            <p class="bc-eyebrow"><?php esc_html_e('Crypto SEO marketplace', 'backlinkcrypto'); ?></p>
            <h1><?php esc_html_e('Verified crypto backlinks for exchanges, DeFi & Web3.', 'backlinkcrypto'); ?></h1>
            <p class="bc-hero__lead">
                <?php esc_html_e('Buy guest-post placements on vetted crypto publishers. Filter by DR, DA, traffic, language & niche — pay in crypto, upload your article, get the live URL.', 'backlinkcrypto'); ?>
            </p>
            <div class="bc-hero__metric-chips" aria-label="<?php esc_attr_e('What you can filter', 'backlinkcrypto'); ?>">
                <span class="bc-chip">DR</span>
                <span class="bc-chip">DA</span>
                <span class="bc-chip"><?php esc_html_e('Traffic', 'backlinkcrypto'); ?></span>
                <span class="bc-chip"><?php esc_html_e('Niche', 'backlinkcrypto'); ?></span>
                <span class="bc-chip"><?php esc_html_e('Language', 'backlinkcrypto'); ?></span>
                <span class="bc-chip"><?php esc_html_e('Dofollow', 'backlinkcrypto'); ?></span>
            </div>
            <div class="bc-hero__cta">
                <a class="bc-btn bc-btn--primary" href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Browse marketplace', 'backlinkcrypto'); ?></a>
                <a class="bc-btn bc-btn--ghost" href="#bc-use-cases"><?php esc_html_e('Choose your path', 'backlinkcrypto'); ?></a>
            </div>
        </div>
        <div class="bc-hero__panel">
            <div class="bc-hero-preview" aria-label="<?php esc_attr_e('Sample marketplace preview', 'backlinkcrypto'); ?>">
                <div class="bc-hero-preview__head">
                    <span><?php esc_html_e('Live catalog preview', 'backlinkcrypto'); ?></span>
                    <a href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Open full table →', 'backlinkcrypto'); ?></a>
                </div>
                <?php if ($preview_rows !== []) : ?>
                    <div class="bc-hero-preview__table-wrap">
                        <table class="bc-hero-preview__table">
                            <thead>
                                <tr>
                                    <th><?php esc_html_e('Domain', 'backlinkcrypto'); ?></th>
                                    <th>DR</th>
                                    <th><?php esc_html_e('Niche', 'backlinkcrypto'); ?></th>
                                    <th><?php esc_html_e('Price', 'backlinkcrypto'); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($preview_rows as $pp) :
                                    $pm = backlinkcrypto_product_metrics($pp->get_id());
                                    $pd = $pm['domain'] !== '' ? $pm['domain'] : $pp->get_name();
                                    $pdr = $pm['dr'] !== '' && $pm['dr'] !== null ? (string) (int) $pm['dr'] : '—';
                                    ?>
                                    <tr>
                                        <td>
                                            <a href="<?php echo esc_url(get_permalink($pp->get_id())); ?>"><?php echo esc_html($pd); ?></a>
                                        </td>
                                        <td><strong><?php echo esc_html($pdr); ?></strong></td>
                                        <td><span class="bc-pill"><?php echo esc_html($pm['niche'] ?: 'Crypto'); ?></span></td>
                                        <td class="bc-hero-preview__price"><?php echo wp_kses_post($pp->get_price_html()); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="bc-hero__stats">
                        <strong data-bc-count-up data-bc-to="<?php echo esc_attr((string) max($total_sites, 1)); ?>"><?php echo esc_html((string) max($total_sites, 1)); ?></strong>
                        <span><?php esc_html_e('curated publisher sites', 'backlinkcrypto'); ?></span>
                    </div>
                <?php endif; ?>
            </div>
            <div class="bc-ticker" aria-hidden="true">
                <div class="bc-ticker__track" data-bc-ticker>
                    <span>Dofollow placements</span>
                    <span>Crypto · DeFi · NFT</span>
                    <span>Pay with USDT</span>
                    <span>1 site = 1 article</span>
                    <span>24–72h typical review</span>
                    <span>Verified inventory</span>
                    <span>Dofollow placements</span>
                    <span>Crypto · DeFi · NFT</span>
                    <span>Pay with USDT</span>
                    <span>1 site = 1 article</span>
                    <span>24–72h typical review</span>
                    <span>Verified inventory</span>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="bc-trust" data-bc-reveal>
    <div class="bc-container bc-trust__grid">
        <div class="bc-trust__item">
            <strong><span data-bc-count-up data-bc-to="<?php echo esc_attr((string) max($total_sites, 50)); ?>" data-bc-suffix="+"><?php echo esc_html((string) max($total_sites, 50)); ?>+</span></strong>
            <span><?php esc_html_e('Curated sites', 'backlinkcrypto'); ?></span>
        </div>
        <div class="bc-trust__item"><strong>EN+</strong><span><?php esc_html_e('Multi-language', 'backlinkcrypto'); ?></span></div>
        <div class="bc-trust__item"><strong><?php esc_html_e('Tracked', 'backlinkcrypto'); ?></strong><span><?php esc_html_e('Live URL delivery', 'backlinkcrypto'); ?></span></div>
        <div class="bc-trust__item"><strong>24–72h</strong><span><?php esc_html_e('Typical review', 'backlinkcrypto'); ?></span></div>
    </div>
</section>

<section class="bc-use-cases" id="bc-use-cases" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head">
            <p class="bc-eyebrow"><?php esc_html_e('Choose a path', 'backlinkcrypto'); ?></p>
            <h2><?php esc_html_e('Built for how crypto SEO teams actually buy', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Pick a goal — we’ll point you at the right inventory or package.', 'backlinkcrypto'); ?></p>
        </div>
        <div class="bc-use-cases__grid">
            <a class="bc-use-case" href="<?php echo esc_url(home_url('/packages/#bc-trial')); ?>">
                <span class="bc-use-case__label"><?php esc_html_e('First order', 'backlinkcrypto'); ?></span>
                <h3><?php esc_html_e('Start with a $249 trial', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('One mid-DR placement to verify delivery — then scale with Starter, Growth, or Authority packs.', 'backlinkcrypto'); ?></p>
                <span class="bc-use-case__cta"><?php esc_html_e('See trial →', 'backlinkcrypto'); ?></span>
            </a>
            <a class="bc-use-case" href="<?php echo esc_url(home_url('/packages/')); ?>">
                <span class="bc-use-case__label"><?php esc_html_e('Token / project launch', 'backlinkcrypto'); ?></span>
                <h3><?php esc_html_e('Launch coverage packs', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Starter to Authority packs for multi-site guest posts when you need speed and coverage.', 'backlinkcrypto'); ?></p>
                <span class="bc-use-case__cta"><?php esc_html_e('View packages →', 'backlinkcrypto'); ?></span>
            </a>
            <a class="bc-use-case" href="<?php echo esc_url(home_url('/defi-backlinks/')); ?>">
                <span class="bc-use-case__label"><?php esc_html_e('DeFi & Web3 SEO', 'backlinkcrypto'); ?></span>
                <h3><?php esc_html_e('DeFi & Web3 placements', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Guest posts on publishers covering DeFi, protocols, and Web3 — filter by niche before you buy.', 'backlinkcrypto'); ?></p>
                <span class="bc-use-case__cta"><?php esc_html_e('Browse DeFi sites →', 'backlinkcrypto'); ?></span>
            </a>
            <a class="bc-use-case" href="<?php echo esc_url(home_url('/contact/?topic=bulk')); ?>">
                <span class="bc-use-case__label"><?php esc_html_e('Agencies', 'backlinkcrypto'); ?></span>
                <h3><?php esc_html_e('Monthly retainer cadence', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Recurring placement quotas for client books — talk to us about volume and niches.', 'backlinkcrypto'); ?></p>
                <span class="bc-use-case__cta"><?php esc_html_e('Request retainer →', 'backlinkcrypto'); ?></span>
            </a>
        </div>
    </div>
</section>

<section class="bc-proof" id="bc-proof" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head">
            <p class="bc-eyebrow"><?php esc_html_e('Why teams buy here', 'backlinkcrypto'); ?></p>
            <h2><?php esc_html_e('Built for agencies and crypto SEO teams', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Transparent listings, crypto checkout, and placement tracking — so campaigns stay accountable.', 'backlinkcrypto'); ?></p>
        </div>
        <div class="bc-proof__grid">
            <div class="bc-proof__card">
                <h3><?php esc_html_e('Metric-first catalog', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Filter DR, DA, traffic, niche, and language in one table before you spend — no mystery “SEO package” bundles.', 'backlinkcrypto'); ?></p>
            </div>
            <div class="bc-proof__card">
                <h3><?php esc_html_e('Pay in USDT, track to live URL', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Checkout in crypto. After payment confirmation, upload articles from My Account → Placements and receive each published URL.', 'backlinkcrypto'); ?></p>
            </div>
            <div class="bc-proof__card bc-proof__card--stats">
                <strong><span data-bc-count-up data-bc-to="<?php echo esc_attr((string) max($total_sites, 50)); ?>" data-bc-suffix="+"><?php echo esc_html((string) max($total_sites, 50)); ?>+</span></strong>
                <span><?php esc_html_e('publisher sites listed', 'backlinkcrypto'); ?></span>
                <strong><?php esc_html_e('From $249', 'backlinkcrypto'); ?></strong>
                <span><?php esc_html_e('Trial placement to test fulfillment', 'backlinkcrypto'); ?></span>
                <a class="bc-btn bc-btn--ghost bc-btn--compact" href="<?php echo esc_url(home_url('/packages/')); ?>"><?php esc_html_e('View packages', 'backlinkcrypto'); ?></a>
            </div>
        </div>
        <div class="bc-proof__niches">
            <span><?php esc_html_e('Browse by niche:', 'backlinkcrypto'); ?></span>
            <a href="<?php echo esc_url(home_url('/crypto-backlinks/')); ?>">Crypto</a>
            <a href="<?php echo esc_url(home_url('/defi-backlinks/')); ?>">DeFi</a>
            <a href="<?php echo esc_url(home_url('/nft-backlinks/')); ?>">NFT</a>
            <a href="<?php echo esc_url(home_url('/exchange-backlinks/')); ?>">Trading</a>
            <a href="<?php echo esc_url(home_url('/finance-backlinks/')); ?>">Finance</a>
            <a href="<?php echo esc_url(home_url('/crypto-news-backlinks/')); ?>">News</a>
        </div>
        <p class="bc-proof__honest">
            <?php esc_html_e('Client live URLs appear in the gallery below when buyers opt in after delivery. Until then we show publishers from the live catalog — not invented testimonials.', 'backlinkcrypto'); ?>
        </p>
    </div>
</section>

<?php get_template_part('template-parts/placement', 'gallery'); ?>

<section class="bc-delivery" id="bc-delivery" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head">
            <p class="bc-eyebrow"><?php esc_html_e('Proof of delivery', 'backlinkcrypto'); ?></p>
            <h2><?php esc_html_e('From payment to live URL — tracked in your account', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Every paid slot becomes a placement ticket. You always know where the order stands.', 'backlinkcrypto'); ?></p>
        </div>
        <ol class="bc-delivery__timeline">
            <li>
                <span class="bc-delivery__dot"></span>
                <strong><?php esc_html_e('Paid', 'backlinkcrypto'); ?></strong>
                <p><?php esc_html_e('Crypto confirmed — tickets unlock in My Account → Placements.', 'backlinkcrypto'); ?></p>
            </li>
            <li>
                <span class="bc-delivery__dot"></span>
                <strong><?php esc_html_e('Article uploaded', 'backlinkcrypto'); ?></strong>
                <p><?php esc_html_e('You submit title, anchors, and content (or add writing at cart).', 'backlinkcrypto'); ?></p>
            </li>
            <li>
                <span class="bc-delivery__dot"></span>
                <strong><?php esc_html_e('In review', 'backlinkcrypto'); ?></strong>
                <p><?php esc_html_e('We coordinate with the publisher — typical 24–72h after approval.', 'backlinkcrypto'); ?></p>
            </li>
            <li>
                <span class="bc-delivery__dot"></span>
                <strong><?php esc_html_e('Live URL', 'backlinkcrypto'); ?></strong>
                <p><?php esc_html_e('Published link lands on the ticket. Optional public gallery when you opt in.', 'backlinkcrypto'); ?></p>
            </li>
        </ol>
    </div>
</section>

<section class="bc-services" id="bc-services" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head">
            <p class="bc-eyebrow"><?php esc_html_e('Services', 'backlinkcrypto'); ?></p>
            <h2><?php esc_html_e('What Backlink Crypto provides', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('A focused marketplace for crypto SEO teams — transparent metrics, crypto checkout, and placement tracking.', 'backlinkcrypto'); ?></p>
        </div>
        <div class="bc-services__grid">
            <article class="bc-services__card">
                <h3><?php esc_html_e('Crypto niche placements', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Guest posts on publishers covering crypto, exchanges, DeFi, NFT, and Web3 finance — not random general blogs.', 'backlinkcrypto'); ?></p>
            </article>
            <article class="bc-services__card">
                <h3><?php esc_html_e('Metric-first browsing', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Compare DA, DR, traffic, niche, and language in one table. Filter verified and dofollow listings before you buy.', 'backlinkcrypto'); ?></p>
            </article>
            <article class="bc-services__card">
                <h3><?php esc_html_e('Crypto checkout', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Pay with USDT and other wallets. Orders stay on hold until payment is confirmed — then you unlock article upload.', 'backlinkcrypto'); ?></p>
            </article>
            <article class="bc-services__card">
                <h3><?php esc_html_e('Placement workspace', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('After payment, submit one article per purchase from My Account → Placements and track status through to the live URL.', 'backlinkcrypto'); ?></p>
            </article>
            <article class="bc-services__card">
                <h3><?php esc_html_e('Bulk & custom packages', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Need a monthly cadence or agency bundle? Contact us for custom packages beyond single marketplace buys.', 'backlinkcrypto'); ?></p>
            </article>
            <article class="bc-services__card">
                <h3><?php esc_html_e('Clear fulfillment', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Typical review and publishing is about 24–72 hours after an approved article. You get the published URL for each placement.', 'backlinkcrypto'); ?></p>
            </article>
        </div>
        <div class="bc-services__cta">
            <a class="bc-btn bc-btn--primary" href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Open full marketplace', 'backlinkcrypto'); ?></a>
            <a class="bc-btn bc-btn--ghost" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Request a custom package', 'backlinkcrypto'); ?></a>
        </div>
    </div>
</section>

<?php if ($featured_products !== []) : ?>
<section class="bc-featured bc-featured--teaser" id="bc-featured" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head bc-featured__head">
            <div>
                <p class="bc-eyebrow"><?php esc_html_e('Sample inventory', 'backlinkcrypto'); ?></p>
                <h2><?php esc_html_e('Featured publisher sites', 'backlinkcrypto'); ?></h2>
                <p><?php esc_html_e('Add from here or open the full filterable marketplace.', 'backlinkcrypto'); ?></p>
            </div>
            <a class="bc-btn bc-btn--ghost bc-btn--compact" href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('See all sites', 'backlinkcrypto'); ?></a>
        </div>
        <div class="bc-featured__grid">
            <?php foreach ($featured_products as $fp) :
                $fm = backlinkcrypto_product_metrics($fp->get_id());
                $fdomain = $fm['domain'] !== '' ? $fm['domain'] : $fp->get_name();
                $fdr = $fm['dr'] !== '' && $fm['dr'] !== null ? (int) $fm['dr'] : null;
                $fda = $fm['da'] !== '' && $fm['da'] !== null ? (int) $fm['da'] : null;
                $flangs = $fm['languages'] ?? ['EN'];
                $can_buy = $fp->is_purchasable() && $fp->is_in_stock();
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
                        <?php if ($can_buy) : ?>
                            <button type="button" class="bc-add" data-product_id="<?php echo esc_attr((string) $fp->get_id()); ?>">
                                <?php esc_html_e('ADD', 'backlinkcrypto'); ?>
                            </button>
                        <?php else : ?>
                            <a class="bc-btn bc-btn--ghost bc-btn--compact" href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('View', 'backlinkcrypto'); ?></a>
                        <?php endif; ?>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="bc-how" id="bc-how" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head">
            <h2><?php esc_html_e('How it works', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('From cart to live URL — clear steps, no guessing.', 'backlinkcrypto'); ?></p>
        </div>
        <ol class="bc-steps" data-bc-steps>
            <li class="bc-steps__item" data-bc-step>
                <span class="bc-steps__num">1</span>
                <h3><?php esc_html_e('Pick sites', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Open the marketplace, filter publishers, and ADD the sites you want. Quantity 2 on the same site = two articles there.', 'backlinkcrypto'); ?></p>
            </li>
            <li class="bc-steps__item" data-bc-step>
                <span class="bc-steps__num">2</span>
                <h3><?php esc_html_e('Checkout', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('Pay with crypto. Your order stays on hold until we confirm payment — then you can upload articles.', 'backlinkcrypto'); ?></p>
            </li>
            <li class="bc-steps__item" data-bc-step>
                <span class="bc-steps__num">3</span>
                <h3><?php esc_html_e('Upload articles', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('After payment is confirmed, open My Account → Placements and submit one article per purchase.', 'backlinkcrypto'); ?></p>
            </li>
            <li class="bc-steps__item" data-bc-step>
                <span class="bc-steps__num">4</span>
                <h3><?php esc_html_e('Go live', 'backlinkcrypto'); ?></h3>
                <p><?php esc_html_e('We publish and send you the live URL for each placement when it’s done.', 'backlinkcrypto'); ?></p>
            </li>
        </ol>
        <p class="bc-how__cta">
            <a class="bc-btn bc-btn--primary" href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Start browsing sites', 'backlinkcrypto'); ?></a>
        </p>
    </div>
</section>

<section class="bc-publisher" id="bc-publisher" data-bc-reveal>
    <div class="bc-container bc-publisher__inner">
        <div>
            <p class="bc-eyebrow"><?php esc_html_e('Publishers', 'backlinkcrypto'); ?></p>
            <h2><?php esc_html_e('List your crypto site on Backlink Crypto', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Run a crypto, DeFi, NFT, or finance publisher? Apply to join the catalog — we review niche fit, metrics, and placement terms.', 'backlinkcrypto'); ?></p>
        </div>
        <a class="bc-btn bc-btn--primary" href="<?php echo esc_url(home_url('/contact/?topic=partnership')); ?>"><?php esc_html_e('Apply as publisher', 'backlinkcrypto'); ?></a>
    </div>
</section>

<section class="bc-faq" id="bc-faq" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-section-head">
            <h2><?php esc_html_e('FAQ', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Straight answers for SEO buyers, agencies, and crypto brands.', 'backlinkcrypto'); ?></p>
        </div>
        <div class="bc-faq__list">
            <details class="bc-faq__item" open>
                <summary><?php esc_html_e('What exactly am I buying?', 'backlinkcrypto'); ?></summary>
                <p><?php esc_html_e('A sponsored guest post placement on a listed publisher. You choose the site from our marketplace, check out, submit your article, and we coordinate publication. When it goes live, you receive the published URL.', 'backlinkcrypto'); ?></p>
            </details>
            <details class="bc-faq__item">
                <summary><?php esc_html_e('What’s the minimum budget?', 'backlinkcrypto'); ?></summary>
                <p><?php esc_html_e('You can buy a single marketplace placement at the listed price (many sites start under a few hundred USD). Packages start from the Starter pack if you want a bundled set. There is no separate platform deposit — you pay per order in crypto.', 'backlinkcrypto'); ?></p>
            </details>
            <details class="bc-faq__item">
                <summary><?php esc_html_e('Do I write the article, or do you?', 'backlinkcrypto'); ?></summary>
                <p><?php
                printf(
                    /* translators: %s: writing price */
                    esc_html__('You can provide the content after payment, or add professional writing at checkout (from %s per placement). Upload title, target URL, and anchors from My Account → Placements.', 'backlinkcrypto'),
                    wp_strip_all_tags(function_exists('wc_price') ? wc_price($writing) : ('$' . (string) (int) $writing))
                );
                ?></p>
            </details>
            <details class="bc-faq__item">
                <summary><?php esc_html_e('Are the backlinks dofollow?', 'backlinkcrypto'); ?></summary>
                <p><?php esc_html_e('Most listings are dofollow and marked clearly in the marketplace. If a site is nofollow, that status is shown on the row before you add it to cart.', 'backlinkcrypto'); ?></p>
            </details>
            <details class="bc-faq__item">
                <summary><?php esc_html_e('How long until my post goes live?', 'backlinkcrypto'); ?></summary>
                <p><?php esc_html_e('Typical review and publishing is about 24–72 hours after we receive an approved article. Higher-authority sites can take longer.', 'backlinkcrypto'); ?></p>
            </details>
            <details class="bc-faq__item">
                <summary><?php esc_html_e('What payment methods do you accept?', 'backlinkcrypto'); ?></summary>
                <p><?php esc_html_e('Checkout uses crypto payment (USDT and other supported wallets). Fulfillment begins once payment is confirmed.', 'backlinkcrypto'); ?></p>
            </details>
            <details class="bc-faq__item">
                <summary><?php esc_html_e('What if a link disappears after go-live?', 'backlinkcrypto'); ?></summary>
                <p><?php
                printf(
                    esc_html__('If a delivered link disappears within 30 days, we republish or reallocate an equal-value slot — no cash refunds. Full rules on our %s.', 'backlinkcrypto'),
                    '<a href="' . esc_url(home_url('/policies/')) . '">' . esc_html__('fulfillment & policies page', 'backlinkcrypto') . '</a>'
                );
                ?></p>
            </details>
            <details class="bc-faq__item">
                <summary><?php esc_html_e('Where do I browse all sites?', 'backlinkcrypto'); ?></summary>
                <p><?php
                printf(
                    esc_html__('The full inventory lives on the %s — filterable by DR, DA, traffic, niche, and language.', 'backlinkcrypto'),
                    '<a href="' . esc_url($market_url) . '">' . esc_html__('marketplace page', 'backlinkcrypto') . '</a>'
                );
                ?></p>
            </details>
        </div>
    </div>
</section>

<section class="bc-cta-band" data-bc-reveal>
    <div class="bc-container bc-cta-band__inner">
        <div>
            <h2><?php esc_html_e('Ready to build crypto SEO authority?', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Browse verified publishers, add placements to cart, and checkout in crypto.', 'backlinkcrypto'); ?></p>
        </div>
        <div class="bc-cta-band__actions">
            <a class="bc-btn bc-btn--primary" href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Browse marketplace', 'backlinkcrypto'); ?></a>
            <a class="bc-btn bc-btn--ghost" href="<?php echo esc_url(home_url('/contact/')); ?>"><?php esc_html_e('Contact us', 'backlinkcrypto'); ?></a>
        </div>
    </div>
</section>

<script type="application/ld+json">
<?php
echo wp_json_encode([
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => [
        [
            '@type'          => 'Question',
            'name'           => 'What exactly am I buying?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'A sponsored guest post placement on a listed publisher. You choose the site, check out, submit your article, and receive the published URL when live.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'What is the minimum budget?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'You can buy a single marketplace placement at the listed price. Packages are available for bundled coverage. Pay per order in crypto — no separate platform deposit.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Do you write the article?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'You can provide content after payment, or add professional writing at checkout. Upload from My Account → Placements.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'Are the backlinks dofollow?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'Most listings are dofollow and marked clearly. Nofollow listings show that status before you add to cart.',
            ],
        ],
        [
            '@type'          => 'Question',
            'name'           => 'What payment methods do you accept?',
            'acceptedAnswer' => [
                '@type' => 'Answer',
                'text'  => 'Checkout uses crypto payment such as USDT and other supported wallets. Fulfillment begins once payment is confirmed.',
            ],
        ],
    ],
], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>
</script>

<?php
get_footer();
