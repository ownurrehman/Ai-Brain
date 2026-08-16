<?php
/**
 * Sales ops — wallet import, legacy redirects, payment chase, GA4, sticky sales CTA.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * Import USDT wallets from existing WooCommerce BACS instructions when Theme Settings are empty.
 * Fixes checkout wiping a wallet that was only stored in gateway instructions.
 */
add_action('init', static function (): void {
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    $flag = 'bc_wallet_import_v115';
    if (get_option($flag) === '1') {
        return;
    }

    $settings = get_option(BC_THEME_SETTINGS_OPTION, []);
    if (!is_array($settings)) {
        $settings = [];
    }

    $has = trim((string) ($settings['usdt_trc20'] ?? '')) !== ''
        || trim((string) ($settings['usdt_erc20'] ?? '')) !== ''
        || trim((string) ($settings['btc'] ?? '')) !== ''
        || trim((string) ($settings['eth'] ?? '')) !== ''
        || trim((string) ($settings['other_wallets'] ?? '')) !== '';

    if (!$has) {
        $bacs = (array) get_option('woocommerce_bacs_settings', []);
        $instructions = (string) ($bacs['instructions'] ?? '');
        $accounts = (array) get_option('woocommerce_bacs_accounts', []);

        if (preg_match('/USDT\s*\(TRC20\)\s*:\s*([A-Za-z0-9]+)/i', $instructions, $m)) {
            $settings['usdt_trc20'] = $m[1];
        }
        if (preg_match('/USDT\s*\(ERC20\)\s*:\s*(0x[A-Fa-f0-9]+)/i', $instructions, $m)) {
            $settings['usdt_erc20'] = $m[1];
        }
        if (preg_match('/\bBTC\s*:\s*([A-Za-z0-9]+)/i', $instructions, $m)) {
            $settings['btc'] = $m[1];
        }
        if (preg_match('/\bETH\s*:\s*(0x[A-Fa-f0-9]+)/i', $instructions, $m)) {
            $settings['eth'] = $m[1];
        }

        foreach ($accounts as $row) {
            if (!is_array($row)) {
                continue;
            }
            $name = strtoupper((string) ($row['account_name'] ?? $row['bank_name'] ?? ''));
            $addr = trim((string) ($row['account_number'] ?? ''));
            if ($addr === '') {
                continue;
            }
            if (str_contains($name, 'TRC20') && empty($settings['usdt_trc20'])) {
                $settings['usdt_trc20'] = $addr;
            } elseif (str_contains($name, 'ERC20') && empty($settings['usdt_erc20'])) {
                $settings['usdt_erc20'] = $addr;
            } elseif (str_contains($name, 'BTC') && empty($settings['btc'])) {
                $settings['btc'] = $addr;
            } elseif (str_contains($name, 'ETH') && empty($settings['eth'])) {
                $settings['eth'] = $addr;
            }
        }

        // Known production wallet recovered from live BACS instructions (2026-08 audit).
        if (empty($settings['usdt_trc20'])) {
            $settings['usdt_trc20'] = 'TUkQ5nfCfeLCP4MAURaNLigu2jkMzoguyi';
        }
    }

    update_option(BC_THEME_SETTINGS_OPTION, $settings);
    if (function_exists('backlinkcrypto_sync_payment_gateway_instructions')) {
        backlinkcrypto_sync_payment_gateway_instructions(backlinkcrypto_get_theme_settings());
    }
    update_option($flag, '1');
}, 40);

/**
 * Redirect empty legacy pages to canonical URLs.
 *
 * @return array<string,string> slug => destination path
 */
function backlinkcrypto_legacy_page_redirects(): array
{
    return [
        'about-us'         => '/about/',
        'testimonials'     => '/#bc-proof',
        'pricing'          => '/packages/',
        'become-seller'    => '/contact/?topic=partnership',
        'terms-conditions' => '/terms/',
    ];
}

add_action('template_redirect', static function (): void {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    $map = backlinkcrypto_legacy_page_redirects();
    $path = trim((string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH), '/');
    // Match draft or missing legacy slugs via request path (is_page fails for drafts → 404).
    if ($path !== '' && isset($map[$path])) {
        wp_safe_redirect(home_url($map[$path]), 301);
        exit;
    }
    foreach ($map as $slug => $dest) {
        if (is_page($slug)) {
            wp_safe_redirect(home_url($dest), 301);
            exit;
        }
    }
}, 1);

/** Soft-unpublish empty legacy pages once (keep redirects via slug check + 301). */
add_action('init', static function (): void {
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    $flag = 'bc_legacy_pages_draft_v115';
    if (get_option($flag) === '1') {
        return;
    }
    foreach (array_keys(backlinkcrypto_legacy_page_redirects()) as $slug) {
        $page = get_page_by_path($slug);
        if ($page && $page->post_status === 'publish') {
            wp_update_post([
                'ID'          => (int) $page->ID,
                'post_status' => 'draft',
            ]);
        }
    }
    update_option($flag, '1');
}, 55);

/**
 * Payment chase emails for on-hold crypto orders (2h + 24h).
 */
add_action('init', static function (): void {
    if (!wp_next_scheduled('bc_payment_chase_cron')) {
        wp_schedule_event(time() + 300, 'hourly', 'bc_payment_chase_cron');
    }
});

add_action('bc_payment_chase_cron', 'backlinkcrypto_run_payment_chase');

function backlinkcrypto_run_payment_chase(): void
{
    if (!function_exists('wc_get_orders')) {
        return;
    }

    $orders = wc_get_orders([
        'status'       => ['on-hold', 'pending'],
        'limit'        => 40,
        'orderby'      => 'date',
        'order'        => 'DESC',
        'date_created' => '>' . (time() - WEEK_IN_SECONDS),
    ]);

    $support = function_exists('backlinkcrypto_public_support_email')
        ? backlinkcrypto_public_support_email()
        : 'contact@backlinkcrypto.com';
    $settings = function_exists('backlinkcrypto_get_theme_settings')
        ? backlinkcrypto_get_theme_settings()
        : [];
    $instructions = function_exists('backlinkcrypto_build_payment_instructions')
        ? backlinkcrypto_build_payment_instructions($settings)
        : '';

    foreach ($orders as $order) {
        if (!$order instanceof WC_Order) {
            continue;
        }
        $email = $order->get_billing_email();
        if ($email === '' || !is_email($email)) {
            continue;
        }
        $created = $order->get_date_created();
        if (!$created) {
            continue;
        }
        $age = time() - $created->getTimestamp();
        $id = $order->get_id();

        if ($age >= 2 * HOUR_IN_SECONDS && $order->get_meta('_bc_chase_2h') !== '1') {
            $subject = sprintf(
                /* translators: %s: order number */
                __('Reminder: complete crypto payment for order #%s', 'backlinkcrypto'),
                $order->get_order_number()
            );
            $body = "Hi {$order->get_billing_first_name()},\n\n"
                . "Your Backlink Crypto order #{$order->get_order_number()} is waiting on crypto payment.\n\n"
                . "Total due: {$order->get_formatted_order_total()}\n\n"
                . ($instructions !== '' ? "Payment instructions:\n{$instructions}\n\n" : '')
                . "After sending, reply to this email with the transaction hash so we can unlock article upload.\n\n"
                . "Order: " . $order->get_checkout_order_received_url() . "\n"
                . "Support: {$support}\n\n"
                . "— Backlink Crypto\n";
            wp_mail($email, $subject, $body, [
                'Content-Type: text/plain; charset=UTF-8',
                'From: Backlink Crypto <' . $support . '>',
            ]);
            $order->update_meta_data('_bc_chase_2h', '1');
            $order->save();
            continue;
        }

        if ($age >= DAY_IN_SECONDS && $order->get_meta('_bc_chase_24h') !== '1') {
            $subject = sprintf(
                /* translators: %s: order number */
                __('Still need help paying order #%s?', 'backlinkcrypto'),
                $order->get_order_number()
            );
            $body = "Hi {$order->get_billing_first_name()},\n\n"
                . "Just checking in — order #{$order->get_order_number()} is still unpaid.\n"
                . "If you already sent USDT, reply with the tx hash and we’ll confirm ASAP.\n"
                . "If plans changed, reply and we’ll cancel the hold.\n\n"
                . ($instructions !== '' ? "Wallets:\n{$instructions}\n\n" : '')
                . "Talk to sales: " . home_url('/contact/?topic=billing') . "\n"
                . "Support: {$support}\n\n"
                . "— Backlink Crypto\n";
            wp_mail($email, $subject, $body, [
                'Content-Type: text/plain; charset=UTF-8',
                'From: Backlink Crypto <' . $support . '>',
            ]);
            $order->update_meta_data('_bc_chase_24h', '1');
            $order->save();
        }
        unset($id);
    }
}

/** GA4 snippet when Measurement ID is set in Theme Settings. */
add_action('wp_head', static function (): void {
    if (!function_exists('backlinkcrypto_get_theme_settings')) {
        return;
    }
    $s = backlinkcrypto_get_theme_settings();
    $ga = trim((string) ($s['ga4_id'] ?? ''));
    if ($ga === '' || !preg_match('/^G-[A-Z0-9]+$/', $ga)) {
        return;
    }
    echo "<!-- Backlink Crypto GA4 -->\n";
    echo '<script async src="https://www.googletagmanager.com/gtag/js?id=' . esc_attr($ga) . '"></script>' . "\n";
    echo "<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config'," . wp_json_encode($ga) . ");</script>\n";
}, 20);

/** Sticky sales CTA (Telegram if set, else contact retainer form). */
add_action('wp_footer', static function (): void {
    if (is_admin()) {
        return;
    }
    $s = function_exists('backlinkcrypto_get_theme_settings') ? backlinkcrypto_get_theme_settings() : [];
    $tg = trim((string) ($s['telegram_url'] ?? ''));
    if ($tg !== '' && !preg_match('#^https?://#i', $tg)) {
        $tg = 'https://t.me/' . ltrim($tg, '@/');
    }
    $href = $tg !== '' ? $tg : home_url('/contact/?topic=bulk');
    $label = $tg !== ''
        ? __('Chat on Telegram', 'backlinkcrypto')
        : __('Talk to sales', 'backlinkcrypto');
    ?>
    <a class="bc-sales-fab" href="<?php echo esc_url($href); ?>" <?php echo $tg !== '' ? 'target="_blank" rel="noopener noreferrer"' : ''; ?>>
        <?php echo esc_html($label); ?>
    </a>
    <?php
}, 40);
