<?php
/**
 * About page — brand story + process + trust.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$market = backlinkcrypto_marketplace_url();
$packages = home_url('/packages/');
$contact = home_url('/contact/');
$email = function_exists('backlinkcrypto_public_support_email')
    ? backlinkcrypto_public_support_email()
    : backlinkcrypto_default_support_email();
?>

<section class="bc-about" data-bc-reveal>
    <div class="bc-container bc-about__hero">
        <p class="bc-eyebrow"><?php esc_html_e('About', 'backlinkcrypto'); ?></p>
        <h1><?php esc_html_e('Crypto backlinks, bought with clear metrics.', 'backlinkcrypto'); ?></h1>
        <p class="bc-about__lead">
            <?php esc_html_e('Backlink Crypto is a marketplace for vetted crypto, DeFi, NFT, and finance guest-post placements. Filter by DA, DR, traffic, niche, and language — then checkout in crypto and track each placement to the live URL.', 'backlinkcrypto'); ?>
        </p>
        <div class="bc-hero__cta">
            <a class="bc-btn bc-btn--primary" href="<?php echo esc_url($market); ?>"><?php esc_html_e('Browse marketplace', 'backlinkcrypto'); ?></a>
            <a class="bc-btn bc-btn--ghost" href="<?php echo esc_url($packages); ?>"><?php esc_html_e('View packages', 'backlinkcrypto'); ?></a>
        </div>
    </div>

    <div class="bc-container bc-about__grid">
        <article>
            <h2><?php esc_html_e('Who we are', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('We operate Backlink Crypto for SEO teams, agencies, and Web3 brands that need publisher placements without opaque “mystery link” packages. Inventory is listed with the metrics we have, prices are public, and fulfillment is tracked in your account.', 'backlinkcrypto'); ?></p>
        </article>
        <article>
            <h2><?php esc_html_e('How buying works', 'backlinkcrypto'); ?></h2>
            <ol class="bc-about__steps">
                <li><?php esc_html_e('Pick sites (or a package) and pay in crypto.', 'backlinkcrypto'); ?></li>
                <li><?php esc_html_e('After payment confirmation, upload one article per placement — or add writing at cart.', 'backlinkcrypto'); ?></li>
                <li><?php esc_html_e('We coordinate publication and deliver the live URL.', 'backlinkcrypto'); ?></li>
            </ol>
        </article>
        <article>
            <h2><?php esc_html_e('Risk & metrics policy', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('DA, DR, and traffic are buying signals from industry tools — they change over time and are not a ranking guarantee. Publishers control their sites; rare removals can happen after redesigns or policy changes.', 'backlinkcrypto'); ?>
                <a href="<?php echo esc_url(home_url('/policies/')); ?>"><?php esc_html_e('Read fulfillment & slot reallocation policy', 'backlinkcrypto'); ?></a>.</p>
        </article>
        <article>
            <h2><?php esc_html_e('Talk to us', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Bulk campaigns, agency retainers, and publisher partnerships are welcome.', 'backlinkcrypto'); ?></p>
            <p><a href="<?php echo esc_url($contact); ?>"><?php esc_html_e('Contact form', 'backlinkcrypto'); ?></a>
                · <a href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a></p>
        </article>
    </div>
</section>

<?php
get_footer();
