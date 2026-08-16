<?php
/**
 * Plugin Name: Backlink Crypto Ops
 * Description: Legacy URL redirects + wallet sync for checkout (companion to theme 1.15+).
 * Version: 1.0.1
 * Author: Backlink Crypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('template_redirect', static function (): void {
    if (is_admin() || wp_doing_ajax()) {
        return;
    }
    $map = [
        'about-us'         => '/about/',
        'testimonials'     => '/#bc-proof',
        'pricing'          => '/packages/',
        'become-seller'    => '/contact/?topic=partnership',
        'terms-conditions' => '/terms/',
    ];
    $path = trim((string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH), '/');
    if ($path !== '' && isset($map[$path])) {
        wp_safe_redirect(home_url($map[$path]), 301);
        exit;
    }
}, 0);

register_activation_hook(__FILE__, static function (): void {
    $opt = 'backlinkcrypto_theme_settings';
    $settings = get_option($opt, []);
    if (!is_array($settings)) {
        $settings = [];
    }
    if (trim((string) ($settings['usdt_trc20'] ?? '')) === '') {
        $settings['usdt_trc20'] = 'TUkQ5nfCfeLCP4MAURaNLigu2jkMzoguyi';
        update_option($opt, $settings);
    }
    if (function_exists('backlinkcrypto_sync_payment_gateway_instructions') && function_exists('backlinkcrypto_get_theme_settings')) {
        backlinkcrypto_sync_payment_gateway_instructions(backlinkcrypto_get_theme_settings());
    } else {
        $bacs = (array) get_option('woocommerce_bacs_settings', []);
        $bacs['enabled'] = 'yes';
        $bacs['title'] = 'Crypto';
        $bacs['instructions'] = "Pay with crypto, then reply to your order email with the transaction hash.\n\n"
            . "USDT (TRC20): TUkQ5nfCfeLCP4MAURaNLigu2jkMzoguyi\n\n"
            . "Send the exact order total, then reply to your order email with the transaction hash. Orders stay on hold until we confirm payment.\n\n"
            . "Support: contact@backlinkcrypto.com";
        update_option('woocommerce_bacs_settings', $bacs);
        update_option('woocommerce_bacs_accounts', [[
            'account_name'   => 'USDT (TRC20)',
            'account_number' => 'TUkQ5nfCfeLCP4MAURaNLigu2jkMzoguyi',
            'bank_name'      => 'TRC20',
            'sort_code'      => '',
            'iban'           => '',
            'bic'            => '',
        ]]);
    }
});
