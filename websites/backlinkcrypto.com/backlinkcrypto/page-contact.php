<?php
/**
 * Contact page — structured form + support details.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$support_email = function_exists('backlinkcrypto_public_support_email')
    ? backlinkcrypto_public_support_email()
    : backlinkcrypto_default_support_email();
$market_url = backlinkcrypto_marketplace_url();
$flash = backlinkcrypto_contact_get_flash();
$topics = backlinkcrypto_contact_topics();
$selected_topic = isset($_GET['topic']) ? sanitize_key((string) wp_unslash($_GET['topic'])) : '';
if ($selected_topic === '' || !isset($topics[$selected_topic])) {
    $selected_topic = 'general';
}
?>

<section class="bc-contact" data-bc-reveal>
    <div class="bc-container">
        <div class="bc-contact__grid">
            <div class="bc-contact__intro">
                <p class="bc-eyebrow"><?php esc_html_e('Support', 'backlinkcrypto'); ?></p>
                <h1><?php esc_html_e('Contact Backlink Crypto', 'backlinkcrypto'); ?></h1>
                <p class="bc-contact__lead">
                    <?php
                    if ($selected_topic === 'bulk') {
                        esc_html_e('Tell us your monthly budget, niches, and DR floor — we typically reply with a fixed retainer quote within 1 business day.', 'backlinkcrypto');
                    } else {
                        esc_html_e('Questions about an order, bulk packages, or publisher partnerships? Send a message — we typically reply within 1 business day.', 'backlinkcrypto');
                    }
                    ?>
                </p>

                <ul class="bc-contact__facts">
                    <li>
                        <strong><?php esc_html_e('Email', 'backlinkcrypto'); ?></strong>
                        <a href="mailto:<?php echo esc_attr($support_email); ?>"><?php echo esc_html($support_email); ?></a>
                    </li>
                    <li>
                        <strong><?php esc_html_e('Marketplace', 'backlinkcrypto'); ?></strong>
                        <a href="<?php echo esc_url($market_url); ?>"><?php esc_html_e('Browse all sites', 'backlinkcrypto'); ?></a>
                    </li>
                    <li>
                        <strong><?php esc_html_e('Response time', 'backlinkcrypto'); ?></strong>
                        <span><?php esc_html_e('Within 1 business day', 'backlinkcrypto'); ?></span>
                    </li>
                </ul>

                <div class="bc-contact__tips">
                    <h2><?php esc_html_e('Before you write', 'backlinkcrypto'); ?></h2>
                    <ul>
                        <li><?php esc_html_e('Include your WooCommerce order number for placement or payment issues.', 'backlinkcrypto'); ?></li>
                        <li><?php esc_html_e('For bulk / agency packages, mention monthly volume and target niches.', 'backlinkcrypto'); ?></li>
                        <li><?php esc_html_e('Publishers: tell us your domain, niche, and typical guest-post terms.', 'backlinkcrypto'); ?></li>
                    </ul>
                </div>
            </div>

            <div class="bc-contact__form-wrap">
                <?php if ($flash !== null) : ?>
                    <div class="bc-contact__flash bc-contact__flash--<?php echo esc_attr($flash['type'] === 'ok' ? 'ok' : 'error'); ?>" role="status">
                        <?php echo esc_html($flash['msg']); ?>
                    </div>
                <?php endif; ?>

                <form class="bc-contact__form" method="post" action="<?php echo esc_url(home_url('/contact/')); ?>" novalidate>
                    <?php wp_nonce_field('bc_contact_submit', 'bc_contact_nonce'); ?>
                    <input type="hidden" name="bc_contact_submit" value="1" />

                    <!-- Honeypot -->
                    <p class="bc-contact__hp" aria-hidden="true">
                        <label><?php esc_html_e('Website', 'backlinkcrypto'); ?>
                            <input type="text" name="bc_website" value="" tabindex="-1" autocomplete="off" />
                        </label>
                    </p>

                    <div class="bc-contact__row">
                        <label class="bc-contact__field">
                            <span><?php esc_html_e('Full name', 'backlinkcrypto'); ?> <em>*</em></span>
                            <input type="text" name="bc_name" required maxlength="120" autocomplete="name" />
                        </label>
                        <label class="bc-contact__field">
                            <span><?php esc_html_e('Email', 'backlinkcrypto'); ?> <em>*</em></span>
                            <input type="email" name="bc_email" required maxlength="180" autocomplete="email" />
                        </label>
                    </div>

                    <div class="bc-contact__row">
                        <label class="bc-contact__field">
                            <span><?php esc_html_e('Company / project', 'backlinkcrypto'); ?></span>
                            <input type="text" name="bc_company" maxlength="160" autocomplete="organization" />
                        </label>
                        <label class="bc-contact__field">
                            <span><?php esc_html_e('Order number', 'backlinkcrypto'); ?></span>
                            <input type="text" name="bc_order" maxlength="64" placeholder="#1234" />
                        </label>
                    </div>

                    <label class="bc-contact__field">
                        <span><?php esc_html_e('Topic', 'backlinkcrypto'); ?> <em>*</em></span>
                        <select name="bc_topic" id="bc_topic" required>
                            <?php foreach ($topics as $key => $label) : ?>
                                <option value="<?php echo esc_attr($key); ?>" <?php selected($selected_topic, $key); ?>><?php echo esc_html($label); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </label>

                    <div class="bc-contact__retainer" id="bc-retainer-fields" <?php echo $selected_topic === 'bulk' ? '' : 'hidden'; ?>>
                        <p class="bc-contact__retainer-title"><?php esc_html_e('Retainer details (helps us quote faster)', 'backlinkcrypto'); ?></p>
                        <div class="bc-contact__row">
                            <label class="bc-contact__field">
                                <span><?php esc_html_e('Monthly budget (USD)', 'backlinkcrypto'); ?></span>
                                <input type="text" name="bc_budget" maxlength="64" placeholder="e.g. 2000–5000" />
                            </label>
                            <label class="bc-contact__field">
                                <span><?php esc_html_e('DR floor', 'backlinkcrypto'); ?></span>
                                <input type="number" name="bc_dr_floor" min="0" max="100" placeholder="50" />
                            </label>
                        </div>
                        <div class="bc-contact__row">
                            <label class="bc-contact__field">
                                <span><?php esc_html_e('Target niches', 'backlinkcrypto'); ?></span>
                                <input type="text" name="bc_niches" maxlength="200" placeholder="DeFi, exchange, news…" />
                            </label>
                            <label class="bc-contact__field">
                                <span><?php esc_html_e('Client / site count', 'backlinkcrypto'); ?></span>
                                <input type="text" name="bc_clients" maxlength="64" placeholder="e.g. 3 clients" />
                            </label>
                        </div>
                    </div>

                    <label class="bc-contact__field">
                        <span><?php esc_html_e('Message', 'backlinkcrypto'); ?> <em>*</em></span>
                        <textarea name="bc_message" rows="7" required maxlength="5000" placeholder="<?php echo $selected_topic === 'bulk' ? esc_attr__('Anything else for the quote…', 'backlinkcrypto') : esc_attr__('Tell us what you need…', 'backlinkcrypto'); ?>"></textarea>
                    </label>
                    <script>
                    (function(){
                      var sel=document.getElementById('bc_topic');
                      var box=document.getElementById('bc-retainer-fields');
                      if(!sel||!box) return;
                      function sync(){ box.hidden = sel.value !== 'bulk'; }
                      sel.addEventListener('change', sync);
                      sync();
                    })();
                    </script>

                    <p class="bc-contact__note">
                        <?php esc_html_e('By sending, you agree we may email you about this inquiry. We don’t sell your data.', 'backlinkcrypto'); ?>
                    </p>

                    <button type="submit" class="bc-btn bc-btn--primary bc-contact__submit">
                        <?php esc_html_e('Send message', 'backlinkcrypto'); ?>
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<?php
get_footer();
