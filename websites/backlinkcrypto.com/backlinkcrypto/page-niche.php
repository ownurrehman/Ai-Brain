<?php
/**
 * Template Name: Niche Landing
 * Niche SEO landing with filtered marketplace teaser.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$niche = (string) get_post_meta(get_the_ID(), '_bc_niche_filter', true);
if ($niche === '') {
    $niche = 'Crypto';
}
$query = backlinkcrypto_marketplace_query($niche);
$count = (int) $query->post_count;
$market = add_query_arg('niche', $niche, backlinkcrypto_marketplace_url());
?>

<section class="bc-niche-hero" data-bc-reveal>
    <div class="bc-container">
        <p class="bc-eyebrow"><?php echo esc_html($niche); ?></p>
        <h1><?php the_title(); ?></h1>
        <div class="bc-prose">
            <?php
            while (have_posts()) {
                the_post();
                the_content();
            }
            ?>
        </div>
        <p class="bc-niche-hero__count">
            <?php
            printf(
                /* translators: 1: count 2: niche */
                esc_html__('%1$d live %2$s listings in the marketplace.', 'backlinkcrypto'),
                $count,
                esc_html($niche)
            );
            ?>
        </p>
        <div class="bc-hero__cta">
            <a class="bc-btn bc-btn--primary" href="<?php echo esc_url($market); ?>"><?php esc_html_e('Open filtered marketplace', 'backlinkcrypto'); ?></a>
            <a class="bc-btn bc-btn--ghost" href="<?php echo esc_url(home_url('/packages/')); ?>"><?php esc_html_e('See packages', 'backlinkcrypto'); ?></a>
        </div>
    </div>
</section>

<?php
get_template_part('template-parts/marketplace', 'catalog', [
    'query'         => $query,
    'hide_featured' => $count < 3,
]);
get_footer();
