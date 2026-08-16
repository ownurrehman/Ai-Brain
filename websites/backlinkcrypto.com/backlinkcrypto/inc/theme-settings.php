<?php
/**
 * Theme Settings (WP Admin) — crypto wallets & brand ops without code edits.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BC_THEME_SETTINGS_OPTION', 'backlinkcrypto_theme_settings');

/**
 * @return array{
 *   usdt_trc20:string,
 *   usdt_erc20:string,
 *   btc:string,
 *   eth:string,
 *   other_wallets:string,
 *   payment_note:string,
 *   support_email:string,
 *   notify_email:string
 * }
 */
function backlinkcrypto_get_theme_settings(): array
{
    $defaults = [
        'usdt_trc20'    => '',
        'usdt_erc20'    => '',
        'btc'           => '',
        'eth'           => '',
        'other_wallets' => '',
        'payment_note'  => 'Send the exact order total, then reply to your order email with the transaction hash. Orders stay on hold until we confirm payment.',
        'support_email' => backlinkcrypto_default_support_email(),
        'notify_email'  => '',
        'telegram_url'  => '',
        'ga4_id'        => '',
    ];
    $saved = get_option(BC_THEME_SETTINGS_OPTION, []);
    if (!is_array($saved)) {
        $saved = [];
    }
    $merged = array_merge($defaults, array_intersect_key($saved, $defaults));

    // Never expose internal parent-brand addresses as public support.
    if (backlinkcrypto_is_internal_brand_email((string) $merged['support_email'])) {
        $merged['support_email'] = backlinkcrypto_default_support_email();
    }
    if ($merged['support_email'] === '' || !is_email($merged['support_email'])) {
        $merged['support_email'] = backlinkcrypto_default_support_email();
    }

    return $merged;
}

function backlinkcrypto_build_payment_instructions(array $settings): string
{
    $public_support = backlinkcrypto_public_support_email();
    $note = trim((string) ($settings['payment_note'] ?? ''));
    $note = backlinkcrypto_scrub_public_text($note);

    $lines = [
        'Pay with crypto, then reply to your order email with the transaction hash.',
        '',
    ];

    if (($settings['usdt_trc20'] ?? '') !== '') {
        $lines[] = 'USDT (TRC20): ' . $settings['usdt_trc20'];
    }
    if (($settings['usdt_erc20'] ?? '') !== '') {
        $lines[] = 'USDT (ERC20): ' . $settings['usdt_erc20'];
    }
    if (($settings['btc'] ?? '') !== '') {
        $lines[] = 'BTC: ' . $settings['btc'];
    }
    if (($settings['eth'] ?? '') !== '') {
        $lines[] = 'ETH: ' . $settings['eth'];
    }
    if (trim((string) ($settings['other_wallets'] ?? '')) !== '') {
        $lines[] = '';
        $lines[] = backlinkcrypto_scrub_public_text(trim((string) $settings['other_wallets']));
    }
    if ($note !== '') {
        $lines[] = '';
        $lines[] = $note;
    }
    $lines[] = '';
    $lines[] = 'Support: ' . $public_support;

    $has_wallet = backlinkcrypto_theme_settings_have_wallets($settings);

    if (!$has_wallet) {
        return "Pay with crypto, then reply to your order email with the transaction hash.\n\n"
            . 'Support: ' . $public_support;
    }

    return implode("\n", $lines);
}

function backlinkcrypto_theme_settings_have_wallets(array $settings): bool
{
    return $settings['usdt_trc20'] !== ''
        || $settings['usdt_erc20'] !== ''
        || $settings['btc'] !== ''
        || $settings['eth'] !== ''
        || trim($settings['other_wallets']) !== '';
}

/**
 * Build WooCommerce BACS "accounts" rows from wallets (shown as crypto details, not bank).
 *
 * @return list<array<string,string>>
 */
function backlinkcrypto_build_bacs_accounts(array $settings): array
{
    $accounts = [];
    $map = [
        'usdt_trc20' => ['USDT (TRC20)', 'TRC20'],
        'usdt_erc20' => ['USDT (ERC20)', 'ERC20'],
        'btc'        => ['Bitcoin (BTC)', 'BTC'],
        'eth'        => ['Ethereum (ETH)', 'ETH'],
    ];

    foreach ($map as $key => [$name, $network]) {
        $addr = trim((string) ($settings[$key] ?? ''));
        if ($addr === '' || str_contains(strtoupper($addr), 'REPLACE_')) {
            continue;
        }
        $accounts[] = [
            'account_name'   => $name,
            'account_number' => $addr,
            'bank_name'      => $network,
            'sort_code'      => '',
            'iban'           => '',
            'bic'            => '',
        ];
    }

    return $accounts;
}

/**
 * Push Theme Settings wallets into WooCommerce Crypto (BACS) gateway + account rows.
 */
function backlinkcrypto_sync_payment_gateway_instructions(array $settings): void
{
    $gateways = (array) get_option('woocommerce_bacs_settings', []);
    $gateways['enabled'] = 'yes';
    $gateways['title'] = 'Crypto';
    $gateways['description'] = backlinkcrypto_crypto_gateway_description($settings);
    $gateways['instructions'] = backlinkcrypto_build_payment_instructions($settings);
    update_option('woocommerce_bacs_settings', $gateways);

    // This is what prints "Our bank details / Account number" — must mirror Theme Settings wallets.
    $accounts = backlinkcrypto_build_bacs_accounts($settings);
    update_option('woocommerce_bacs_accounts', $accounts);
}

/**
 * Checkout-visible payment blurb (Woo shows description before Place order).
 */
function backlinkcrypto_crypto_gateway_description(?array $settings = null): string
{
    $settings = $settings ?? backlinkcrypto_get_theme_settings();
    $trc = trim((string) ($settings['usdt_trc20'] ?? ''));
    if ($trc !== '' && !str_contains(strtoupper($trc), 'REPLACE_')) {
        return 'Pay with USDT (TRC20). Send the exact order total to ' . $trc
            . ' — then reply to your order email with the tx hash. Order stays on hold until we confirm payment.';
    }
    $erc = trim((string) ($settings['usdt_erc20'] ?? ''));
    if ($erc !== '' && !str_contains(strtoupper($erc), 'REPLACE_')) {
        return 'Pay with USDT (ERC20). Send the exact order total to ' . $erc
            . ' — then reply to your order email with the tx hash. Order stays on hold until we confirm payment.';
    }

    return 'Pay with USDT or other crypto. Your order stays on hold until we confirm payment. Wallet details appear in your order email after you place the order.';
}

/** Always serve scrubbed crypto instructions (never stale RankRay support lines). */
add_filter('option_woocommerce_bacs_settings', static function ($value) {
    if (!is_array($value)) {
        return $value;
    }
    if (function_exists('backlinkcrypto_get_theme_settings') && function_exists('backlinkcrypto_build_payment_instructions')) {
        $settings = backlinkcrypto_get_theme_settings();
        $value['title'] = 'Crypto';
        $value['description'] = backlinkcrypto_crypto_gateway_description($settings);
        $value['instructions'] = backlinkcrypto_build_payment_instructions($settings);
    }

    return $value;
}, 20);

/** Keep gateway title + labels crypto-only; re-sync wallets if Theme Settings already filled. */
add_action('init', static function (): void {
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    // One-time / upgrade: force Crypto title + wipe REPLACE_ placeholders from old seed.
    if (get_option('bc_crypto_payment_labels_v2') !== '1') {
        $settings = backlinkcrypto_get_theme_settings();
        backlinkcrypto_sync_payment_gateway_instructions($settings);
        update_option('bc_crypto_payment_labels_v2', '1');
    }
}, 70);

/**
 * Brand privacy: scrub RankRay from public support + force-resync checkout instructions.
 */
add_action('init', static function (): void {
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    $flag = 'bc_brand_privacy_' . BACKLINKCRYPTO_VERSION;
    if (get_option($flag) === '1') {
        return;
    }

    $saved = get_option(BC_THEME_SETTINGS_OPTION, []);
    if (!is_array($saved)) {
        $saved = [];
    }
    $public = backlinkcrypto_default_support_email();
    $support = trim((string) ($saved['support_email'] ?? ''));
    if ($support === '' || backlinkcrypto_is_internal_brand_email($support)) {
        $saved['support_email'] = $public;
    }
    // Keep notify internal if set; never copy it into support.
    if (isset($saved['payment_note'])) {
        $saved['payment_note'] = backlinkcrypto_scrub_public_text((string) $saved['payment_note']);
    }
    if (isset($saved['other_wallets'])) {
        $saved['other_wallets'] = backlinkcrypto_scrub_public_text((string) $saved['other_wallets']);
    }
    update_option(BC_THEME_SETTINGS_OPTION, $saved);

    $settings = backlinkcrypto_get_theme_settings();
    backlinkcrypto_sync_payment_gateway_instructions($settings);

    // Scrub any leftover RankRay strings already baked into WC gateway option.
    $gateways = (array) get_option('woocommerce_bacs_settings', []);
    if (!empty($gateways['instructions'])) {
        $gateways['instructions'] = backlinkcrypto_scrub_public_text((string) $gateways['instructions']);
        $gateways['instructions'] = backlinkcrypto_build_payment_instructions($settings);
        update_option('woocommerce_bacs_settings', $gateways);
    }

    update_option($flag, '1');
}, 75);

/** Customer-facing mail must never From/Reply with an internal parent brand. */
add_filter('woocommerce_email_from_address', static function ($email) {
    $email = is_string($email) ? $email : '';
    if ($email === '' || backlinkcrypto_is_internal_brand_email($email)) {
        return backlinkcrypto_public_support_email();
    }

    return $email;
}, 50);

add_filter('woocommerce_email_from_name', static function ($name) {
    return 'Backlink Crypto';
}, 50);

add_filter('woocommerce_email_footer_text', static function ($text) {
    return backlinkcrypto_scrub_public_text(is_string($text) ? $text : '');
}, 50);

add_filter('woocommerce_email_content', static function ($content) {
    return backlinkcrypto_scrub_public_text(is_string($content) ? $content : '');
}, 50);

/** Last-line scrub on any outbound mail body/subject shown to humans. */
add_filter('wp_mail', static function (array $args): array {
    if (isset($args['subject']) && is_string($args['subject'])) {
        $args['subject'] = backlinkcrypto_scrub_public_text($args['subject']);
    }
    if (isset($args['message']) && is_string($args['message'])) {
        $args['message'] = backlinkcrypto_scrub_public_text($args['message']);
    }
    // Force From header away from internal brands when present.
    if (isset($args['headers'])) {
        $headers = $args['headers'];
        if (is_string($headers)) {
            $headers = preg_split('/\r\n|\r|\n/', $headers) ?: [];
        }
        if (is_array($headers)) {
            $out = [];
            foreach ($headers as $h) {
                if (!is_string($h)) {
                    continue;
                }
                if (preg_match('/^From:/i', $h) && backlinkcrypto_is_internal_brand_email(
                    (string) preg_replace('/^From:.*<([^>]+)>.*$/i', '$1', $h)
                )) {
                    $out[] = 'From: Backlink Crypto <' . backlinkcrypto_public_support_email() . '>';
                    continue;
                }
                $out[] = backlinkcrypto_scrub_public_text($h);
            }
            $args['headers'] = $out;
        }
    }

    return $args;
}, 20);

add_filter('woocommerce_bacs_accounts', static function ($accounts) {
    // Ensure live checkout always uses Theme Settings wallets (not stale WC rows).
    if (function_exists('backlinkcrypto_build_bacs_accounts')) {
        $built = backlinkcrypto_build_bacs_accounts(backlinkcrypto_get_theme_settings());
        if ($built !== []) {
            return $built;
        }
    }

    return $accounts;
}, 30);

add_filter('woocommerce_gateway_title', static function ($title, $id) {
    if ($id === 'bacs') {
        return __('Crypto', 'backlinkcrypto');
    }
    return $title;
}, 20, 2);

add_filter('woocommerce_gateway_description', static function ($description, $id) {
    if ($id === 'bacs' && function_exists('backlinkcrypto_crypto_gateway_description')) {
        return backlinkcrypto_crypto_gateway_description();
    }
    return $description;
}, 20, 2);

/** Relabel BACS bank fields as crypto wallet fields. */
add_filter('woocommerce_bacs_account_fields', static function ($fields, $order_id) {
    if (!is_array($fields)) {
        return $fields;
    }
    if (isset($fields['account_name'])) {
        $fields['account_name']['label'] = __('Asset', 'backlinkcrypto');
    }
    if (isset($fields['bank_name'])) {
        $fields['bank_name']['label'] = __('Network', 'backlinkcrypto');
    }
    if (isset($fields['account_number'])) {
        $fields['account_number']['label'] = __('Wallet address', 'backlinkcrypto');
    }
    // Hide unused bank fields.
    unset($fields['sort_code'], $fields['iban'], $fields['bic']);
    return $fields;
}, 20, 2);

add_filter('gettext', static function ($translation, $text, $domain) {
    if ($domain !== 'woocommerce') {
        return $translation;
    }
    $map = [
        'Our bank details'     => 'Crypto payment details',
        'Direct bank transfer' => 'Crypto',
        'Direct Bank Transfer' => 'Crypto',
    ];
    return $map[$text] ?? $translation;
}, 20, 3);

add_filter('ngettext', static function ($translation, $single, $plural, $number, $domain) {
    if ($domain === 'woocommerce' && $single === 'Our bank details') {
        return 'Crypto payment details';
    }
    return $translation;
}, 20, 5);

add_action('admin_menu', static function (): void {
    add_menu_page(
        __('Backlink Crypto', 'backlinkcrypto'),
        __('Backlink Crypto', 'backlinkcrypto'),
        'manage_options',
        'backlinkcrypto-settings',
        'backlinkcrypto_render_theme_settings_page',
        'dashicons-store',
        58
    );

    add_submenu_page(
        'backlinkcrypto-settings',
        __('Theme Settings', 'backlinkcrypto'),
        __('Theme Settings', 'backlinkcrypto'),
        'manage_options',
        'backlinkcrypto-settings',
        'backlinkcrypto_render_theme_settings_page'
    );

    // Inventory submenu is registered in inventory-manager.php (edit_products cap).
});

add_action('admin_init', static function (): void {
    register_setting(
        'backlinkcrypto_theme_settings_group',
        BC_THEME_SETTINGS_OPTION,
        [
            'type'              => 'array',
            'sanitize_callback' => 'backlinkcrypto_sanitize_theme_settings',
            'default'           => [],
        ]
    );
});

/**
 * @param mixed $input
 * @return array<string,string>
 */
function backlinkcrypto_sanitize_theme_settings($input): array
{
    $input = is_array($input) ? $input : [];
    $clean = [
        'usdt_trc20'    => sanitize_text_field((string) ($input['usdt_trc20'] ?? '')),
        'usdt_erc20'    => sanitize_text_field((string) ($input['usdt_erc20'] ?? '')),
        'btc'           => sanitize_text_field((string) ($input['btc'] ?? '')),
        'eth'           => sanitize_text_field((string) ($input['eth'] ?? '')),
        'other_wallets' => sanitize_textarea_field((string) ($input['other_wallets'] ?? '')),
        'payment_note'  => sanitize_textarea_field((string) ($input['payment_note'] ?? '')),
        'support_email' => sanitize_email((string) ($input['support_email'] ?? '')),
        'notify_email'  => sanitize_email((string) ($input['notify_email'] ?? '')),
        'telegram_url'  => esc_url_raw(trim((string) ($input['telegram_url'] ?? ''))),
        'ga4_id'        => strtoupper(sanitize_text_field((string) ($input['ga4_id'] ?? ''))),
    ];
    if ($clean['ga4_id'] !== '' && !preg_match('/^G-[A-Z0-9]+$/', $clean['ga4_id'])) {
        $clean['ga4_id'] = '';
    }

    if ($clean['support_email'] === '' || backlinkcrypto_is_internal_brand_email($clean['support_email'])) {
        $clean['support_email'] = backlinkcrypto_default_support_email();
    }
    $clean['payment_note'] = backlinkcrypto_scrub_public_text($clean['payment_note']);
    $clean['other_wallets'] = backlinkcrypto_scrub_public_text($clean['other_wallets']);

    backlinkcrypto_sync_payment_gateway_instructions($clean);

    return $clean;
}

function backlinkcrypto_render_theme_settings_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $s = backlinkcrypto_get_theme_settings();
    $preview = backlinkcrypto_build_payment_instructions($s);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Backlink Crypto — Theme Settings', 'backlinkcrypto'); ?></h1>
        <p><?php esc_html_e('Manage crypto payment wallets and checkout instructions here. Changes sync automatically to WooCommerce → Payments → Crypto (no code edits).', 'backlinkcrypto'); ?></p>

        <?php settings_errors('backlinkcrypto_theme_settings'); ?>

        <form method="post" action="options.php">
            <?php settings_fields('backlinkcrypto_theme_settings_group'); ?>

            <h2 class="title"><?php esc_html_e('Crypto wallets', 'backlinkcrypto'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bc_usdt_trc20"><?php esc_html_e('USDT (TRC20)', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[usdt_trc20]" type="text" id="bc_usdt_trc20" value="<?php echo esc_attr($s['usdt_trc20']); ?>" class="regular-text code" placeholder="T…" />
                        <p class="description"><?php esc_html_e('Tron network USDT address shown at checkout.', 'backlinkcrypto'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bc_usdt_erc20"><?php esc_html_e('USDT (ERC20)', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[usdt_erc20]" type="text" id="bc_usdt_erc20" value="<?php echo esc_attr($s['usdt_erc20']); ?>" class="regular-text code" placeholder="0x…" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bc_btc"><?php esc_html_e('Bitcoin (BTC)', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[btc]" type="text" id="bc_btc" value="<?php echo esc_attr($s['btc']); ?>" class="regular-text code" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bc_eth"><?php esc_html_e('Ethereum (ETH)', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[eth]" type="text" id="bc_eth" value="<?php echo esc_attr($s['eth']); ?>" class="regular-text code" placeholder="0x…" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bc_other"><?php esc_html_e('Other wallets / networks', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <textarea name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[other_wallets]" id="bc_other" rows="4" class="large-text code"><?php echo esc_textarea($s['other_wallets']); ?></textarea>
                        <p class="description"><?php esc_html_e('Freeform lines, e.g. “USDT (BEP20): 0x…” or Solana addresses.', 'backlinkcrypto'); ?></p>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php esc_html_e('Checkout message', 'backlinkcrypto'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bc_note"><?php esc_html_e('Payment note', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <textarea name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[payment_note]" id="bc_note" rows="4" class="large-text"><?php echo esc_textarea($s['payment_note']); ?></textarea>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bc_support"><?php esc_html_e('Public support email', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[support_email]" type="email" id="bc_support" value="<?php echo esc_attr(backlinkcrypto_public_support_email()); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Shown at checkout, Contact, footer, and customer emails. Must be @backlinkcrypto.com — never a parent-brand address.', 'backlinkcrypto'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bc_notify"><?php esc_html_e('Internal notification inbox', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[notify_email]" type="email" id="bc_notify" value="<?php echo esc_attr($s['notify_email']); ?>" class="regular-text" />
                        <p class="description"><?php esc_html_e('Admin-only: where contact-form alerts are delivered (can forward to your private inbox). Never shown to customers.', 'backlinkcrypto'); ?></p>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php esc_html_e('Sales & tracking', 'backlinkcrypto'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="bc_telegram"><?php esc_html_e('Telegram sales URL', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[telegram_url]" type="url" id="bc_telegram" value="<?php echo esc_attr($s['telegram_url']); ?>" class="regular-text" placeholder="https://t.me/yourhandle" />
                        <p class="description"><?php esc_html_e('Shown on the sticky “Chat on Telegram” button. Leave blank to link Talk to sales → Contact.', 'backlinkcrypto'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="bc_ga4"><?php esc_html_e('GA4 Measurement ID', 'backlinkcrypto'); ?></label></th>
                    <td>
                        <input name="<?php echo esc_attr(BC_THEME_SETTINGS_OPTION); ?>[ga4_id]" type="text" id="bc_ga4" value="<?php echo esc_attr($s['ga4_id']); ?>" class="regular-text code" placeholder="G-XXXXXXXX" />
                        <p class="description"><?php esc_html_e('Optional. When set, loads Google Analytics 4 sitewide.', 'backlinkcrypto'); ?></p>
                    </td>
                </tr>
            </table>

            <h2 class="title"><?php esc_html_e('Live checkout preview', 'backlinkcrypto'); ?></h2>
            <p class="description"><?php esc_html_e('This is what customers see after placing an order (and in order emails):', 'backlinkcrypto'); ?></p>
            <pre style="background:#fff;border:1px solid #c3c4c7;padding:12px 14px;max-width:720px;white-space:pre-wrap;font-size:13px"><?php echo esc_html($preview); ?></pre>

            <?php submit_button(__('Save theme settings', 'backlinkcrypto')); ?>
        </form>

        <hr />
        <h2><?php esc_html_e('Also available', 'backlinkcrypto'); ?></h2>
        <ul>
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=backlinkcrypto-inventory')); ?>"><strong><?php esc_html_e('Inventory', 'backlinkcrypto'); ?></strong></a> — <?php esc_html_e('edit price, DR/DA/traffic, featured ★ in one spreadsheet', 'backlinkcrypto'); ?></li>
            <li><a href="<?php echo esc_url(admin_url('admin.php?page=wc-settings&tab=checkout&section=bacs')); ?>"><?php esc_html_e('WooCommerce → Settings → Payments → Crypto', 'backlinkcrypto'); ?></a> — <?php esc_html_e('synced from this page when you save', 'backlinkcrypto'); ?></li>
            <li><a href="<?php echo esc_url(admin_url('customize.php')); ?>"><?php esc_html_e('Appearance → Customize → Site Identity', 'backlinkcrypto'); ?></a> — <?php esc_html_e('logo / site title', 'backlinkcrypto'); ?></li>
            <li><a href="<?php echo esc_url(admin_url('edit.php?post_type=bc_placement')); ?>"><?php esc_html_e('Placements', 'backlinkcrypto'); ?></a> — <?php esc_html_e('article fulfillment queue', 'backlinkcrypto'); ?></li>
        </ul>
    </div>
    <?php
}

add_action('admin_notices', static function (): void {
    if (!current_user_can('manage_options')) {
        return;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || !in_array($screen->id, ['dashboard', 'woocommerce_page_wc-settings', 'toplevel_page_backlinkcrypto-settings'], true)) {
        return;
    }
    $s = backlinkcrypto_get_theme_settings();
    $has = $s['usdt_trc20'] !== '' || $s['usdt_erc20'] !== '' || $s['btc'] !== '' || $s['eth'] !== '' || trim($s['other_wallets']) !== '';
    if ($has) {
        return;
    }
    if ($screen->id === 'toplevel_page_backlinkcrypto-settings') {
        return;
    }
    printf(
        '<div class="notice notice-warning"><p>%s <a href="%s">%s</a></p></div>',
        esc_html__('Backlink Crypto: add your crypto wallet addresses so checkout can show payment instructions.', 'backlinkcrypto'),
        esc_url(admin_url('admin.php?page=backlinkcrypto-settings')),
        esc_html__('Open Theme Settings', 'backlinkcrypto')
    );
});
