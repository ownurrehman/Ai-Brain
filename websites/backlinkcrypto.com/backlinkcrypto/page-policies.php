<?php
/**
 * Fulfillment, slot reallocation & indexation policy (no cash refunds).
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$market  = backlinkcrypto_marketplace_url();
$contact = home_url('/contact/');
$email   = function_exists('backlinkcrypto_public_support_email')
    ? backlinkcrypto_public_support_email()
    : backlinkcrypto_default_support_email();
?>

<section class="bc-policy" data-bc-reveal>
    <div class="bc-container bc-policy__hero">
        <p class="bc-eyebrow"><?php esc_html_e('Policies', 'backlinkcrypto'); ?></p>
        <h1><?php esc_html_e('Fulfillment & slot reallocation', 'backlinkcrypto'); ?></h1>
        <p class="bc-policy__lead">
            <?php esc_html_e('All sales are final. If a placement cannot be published as purchased, we reallocate an equal-value slot on another site — we do not issue cash refunds.', 'backlinkcrypto'); ?>
        </p>
        <p class="bc-policy__updated">
            <?php
            printf(
                /* translators: %s: date */
                esc_html__('Last updated: %s', 'backlinkcrypto'),
                esc_html(gmdate('Y-m-d'))
            );
            ?>
        </p>
    </div>

    <div class="bc-container bc-policy__grid">
        <article id="what-you-buy">
            <h2><?php esc_html_e('What you are buying', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Each marketplace purchase is one sponsored guest-post / placement slot on the listed publisher. Packages are bundles of slots fulfilled from matching inventory. You receive the live published URL in My Account → Placements when the post goes live.', 'backlinkcrypto'); ?></p>
        </article>

        <article id="no-refunds">
            <h2><?php esc_html_e('No cash refunds', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Backlink Crypto does not offer cash refunds, chargebacks as a substitute for support, or payment reversals once an order is paid. Purchases convert into placement slots. If fulfillment on the original site is not possible, your remedy is slot reallocation (below) — not a money-back return.', 'backlinkcrypto'); ?></p>
            <ul>
                <li><?php esc_html_e('Paid orders are non-refundable in crypto or fiat.', 'backlinkcrypto'); ?></li>
                <li><?php esc_html_e('Writing add-ons are non-refundable once ordered; unused writing may be applied to a reallocated slot when we reassign the placement.', 'backlinkcrypto'); ?></li>
                <li><?php esc_html_e('Opening a chargeback without contacting us first may pause all open placements on your account.', 'backlinkcrypto'); ?></li>
            </ul>
        </article>

        <article id="reallocation">
            <h2><?php esc_html_e('Equal-value slot reallocation', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('If we cannot publish your approved article on the purchased site (publisher declines the niche, inventory sold out, site policy change, or the listing is otherwise not publishable on our side), we will assign you an equal-value placement slot on another site from our marketplace.', 'backlinkcrypto'); ?></p>
            <ul>
                <li><?php esc_html_e('“Equal value” means comparable listed price and, where inventory allows, similar DR band and niche fit.', 'backlinkcrypto'); ?></li>
                <li><?php esc_html_e('You may suggest preferred alternatives from the catalog; final assignment is from available inventory we can fulfill.', 'backlinkcrypto'); ?></li>
                <li><?php esc_html_e('One purchased slot = one reallocated slot. We do not add free extra placements beyond the paid quantity.', 'backlinkcrypto'); ?></li>
                <li><?php esc_html_e('Reallocation replaces the original site obligation — it is not a cash credit or store wallet you can withdraw.', 'backlinkcrypto'); ?></li>
            </ul>
        </article>

        <article id="turnaround">
            <h2><?php esc_html_e('Turnaround', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Typical review and publishing is about 24–72 hours after we receive an approved article. Higher-DR or editorial sites can take longer. Timelines start after crypto payment is confirmed and usable content is submitted. Reallocated slots follow the same turnaround once the new site is confirmed.', 'backlinkcrypto'); ?></p>
        </article>

        <article id="replacement">
            <h2><?php esc_html_e('Link replacement window (30 days)', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('If a delivered live URL disappears or the agreed link is removed within 30 days of delivery (publisher redesign, accidental delete, or policy change on their side), contact us with your order number and the last known URL. We will republish on the same site when possible, or reallocate an equal-value slot on another listing.', 'backlinkcrypto'); ?></p>
            <p><?php esc_html_e('This does not cover removals caused by your content violating publisher guidelines after go-live, or domains that later change ownership outside our control after the 30-day window. After 30 days, no further replacement is owed.', 'backlinkcrypto'); ?></p>
        </article>

        <article id="indexation">
            <h2><?php esc_html_e('Indexation', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('We deliver a live, publicly reachable URL on the publisher site. We do not guarantee Google (or other engine) indexation timing or ranking outcomes — those depend on the publisher’s site health, your content, and search algorithms.', 'backlinkcrypto'); ?></p>
            <p><?php esc_html_e('A soft “not indexed yet” state is not grounds for reallocation or any payment return. Hard 404s within the 30-day window use the replacement process above.', 'backlinkcrypto'); ?></p>
        </article>

        <article id="metrics">
            <h2><?php esc_html_e('SEO metrics disclaimer', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('DA, DR, traffic, and related figures are buying signals from industry tools. They change over time and are stamped with a “metrics as of” date on each listing. They are not a promise of rankings, traffic to your site, or future metric values after purchase. Metric drift after purchase is not a reason for reallocation.', 'backlinkcrypto'); ?></p>
        </article>

        <article id="content">
            <h2><?php esc_html_e('Content & rejections', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Publishers can reject thin, scraped, or over-optimized articles. We will share revision notes when needed. You are expected to revise to reasonable guidelines. If you refuse to revise and the original site cannot publish, we may reallocate the slot to another site that can take the content (or a revised version) — still with no cash refund.', 'backlinkcrypto'); ?></p>
        </article>

        <article id="contact-policy">
            <h2><?php esc_html_e('How to request reallocation', 'backlinkcrypto'); ?></h2>
            <p><?php esc_html_e('Email or use the contact form with your order ID, placement ID (if any), and a short note on the issue. We aim to respond within one business day and confirm the replacement site.', 'backlinkcrypto'); ?></p>
            <p>
                <a class="bc-btn bc-btn--primary bc-btn--compact" href="<?php echo esc_url($contact); ?>"><?php esc_html_e('Contact support', 'backlinkcrypto'); ?></a>
                <a class="bc-btn bc-btn--ghost bc-btn--compact" href="mailto:<?php echo esc_attr($email); ?>"><?php echo esc_html($email); ?></a>
                <a class="bc-btn bc-btn--ghost bc-btn--compact" href="<?php echo esc_url($market); ?>"><?php esc_html_e('Marketplace', 'backlinkcrypto'); ?></a>
            </p>
        </article>
    </div>
</section>

<?php
get_footer();
