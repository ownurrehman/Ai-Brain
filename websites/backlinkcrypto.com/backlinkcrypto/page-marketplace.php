<?php
/**
 * Marketplace page — full site catalog at /marketplace/.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$query = backlinkcrypto_marketplace_query();
$total_sites = (int) $query->post_count;
?>

<section class="bc-hero bc-hero--pro bc-hero--marketplace" data-bc-reveal>
    <div class="bc-hero__glow" aria-hidden="true"></div>
    <div class="bc-container bc-hero__inner">
        <div class="bc-hero__copy">
            <p class="bc-eyebrow"><?php esc_html_e('Live inventory', 'backlinkcrypto'); ?></p>
            <h1><?php esc_html_e('Crypto backlink marketplace', 'backlinkcrypto'); ?></h1>
            <p class="bc-hero__lead">
                <?php esc_html_e('Filter by DR, DA, traffic, language & niche. Add sites to cart, pay in crypto, then upload one article per placement.', 'backlinkcrypto'); ?>
            </p>
            <div class="bc-hero__cta">
                <a class="bc-btn bc-btn--primary" href="#bc-marketplace"><?php esc_html_e('Browse all sites', 'backlinkcrypto'); ?></a>
                <a class="bc-btn bc-btn--ghost" href="<?php echo esc_url(home_url('/#bc-how')); ?>"><?php esc_html_e('How it works', 'backlinkcrypto'); ?></a>
            </div>
        </div>
        <div class="bc-hero__panel">
            <div class="bc-hero__stats">
                <strong><?php echo esc_html((string) $total_sites); ?></strong>
                <span><?php esc_html_e('sites in marketplace', 'backlinkcrypto'); ?></span>
            </div>
            <div class="bc-ticker" aria-hidden="true">
                <div class="bc-ticker__track" data-bc-ticker>
                    <span>DR 90+ publishers</span>
                    <span>Dofollow placements</span>
                    <span>Crypto · DeFi · NFT</span>
                    <span>1 site = 1 article</span>
                    <span>Crypto pay</span>
                    <span>Verified inventory</span>
                    <span>DR 90+ publishers</span>
                    <span>Dofollow placements</span>
                    <span>Crypto · DeFi · NFT</span>
                    <span>1 site = 1 article</span>
                    <span>Crypto pay</span>
                    <span>Verified inventory</span>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
get_template_part('template-parts/marketplace', 'catalog', ['query' => $query]);
get_footer();
