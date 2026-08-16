<?php
/**
 * Theme setup, assets, helpers.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function backlinkcrypto_default_support_email(): string
{
    return 'contact@backlinkcrypto.com';
}

/**
 * True if an address belongs to an internal/parent brand that must never
 * appear on customer-facing checkout, emails, or public pages.
 */
function backlinkcrypto_is_internal_brand_email(string $email): bool
{
    $email = strtolower(trim($email));
    if ($email === '' || !is_email($email)) {
        return false;
    }
    $host = substr(strrchr($email, '@') ?: '', 1);
    $blocked = ['rankray.com', 'rankray.io', 'rankray.net'];
    return in_array($host, $blocked, true);
}

/**
 * Public support address customers may see (never an internal brand domain).
 */
function backlinkcrypto_public_support_email(): string
{
    $settings = function_exists('backlinkcrypto_get_theme_settings')
        ? backlinkcrypto_get_theme_settings()
        : [];
    $email = is_array($settings) ? trim((string) ($settings['support_email'] ?? '')) : '';
    if ($email !== '' && is_email($email) && !backlinkcrypto_is_internal_brand_email($email)) {
        return $email;
    }

    return backlinkcrypto_default_support_email();
}

/**
 * Scrub leaked parent-brand domains from any customer-facing string.
 */
function backlinkcrypto_scrub_public_text(string $text): string
{
    $text = preg_replace('/\b[\w.+-]+@rankray\.(com|io|net)\b/i', backlinkcrypto_default_support_email(), $text) ?? $text;
    $text = preg_replace('/\b(?:https?:\/\/)?(?:www\.)?rankray\.(com|io|net)\b/i', 'backlinkcrypto.com', $text) ?? $text;
    $text = preg_replace('/\bRank\s*Ray\b/i', 'Backlink Crypto', $text) ?? $text;

    return $text;
}

add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script']);
    add_theme_support('custom-logo', [
        'height'      => 80,
        'width'       => 80,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('woocommerce');

    register_nav_menus([
        'primary' => __('Primary Menu', 'backlinkcrypto'),
        'footer'  => __('Footer Menu', 'backlinkcrypto'),
    ]);
});

add_action('wp_enqueue_scripts', static function (): void {
    wp_enqueue_style(
        'backlinkcrypto-main',
        BACKLINKCRYPTO_URI . '/assets/css/main.css',
        [],
        BACKLINKCRYPTO_VERSION
    );

    wp_enqueue_script(
        'backlinkcrypto-main',
        BACKLINKCRYPTO_URI . '/assets/js/main.js',
        [],
        BACKLINKCRYPTO_VERSION,
        true
    );

    wp_enqueue_script(
        'backlinkcrypto-cart',
        BACKLINKCRYPTO_URI . '/assets/js/cart.js',
        [],
        BACKLINKCRYPTO_VERSION,
        true
    );

    wp_localize_script('backlinkcrypto-cart', 'bcCart', [
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'cartUrl'  => function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/cart/'),
        'checkout' => function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/checkout/'),
        'i18n'     => [
            'added'    => __('Added to cart', 'backlinkcrypto'),
            'adding'   => __('Adding…', 'backlinkcrypto'),
            'add'      => __('ADD', 'backlinkcrypto'),
            'viewCart' => __('View cart', 'backlinkcrypto'),
            'empty'    => __('Your cart is empty', 'backlinkcrypto'),
            'error'    => __('Could not update cart', 'backlinkcrypto'),
        ],
    ]);

    $load_marketplace_js = is_page('marketplace')
        || is_page_template('page-niche.php')
        || is_page(['crypto-backlinks', 'defi-backlinks', 'nft-backlinks', 'exchange-backlinks', 'finance-backlinks', 'crypto-news-backlinks'])
        || is_shop()
        || is_post_type_archive('product');
    if ($load_marketplace_js) {
        wp_enqueue_script(
            'backlinkcrypto-marketplace',
            BACKLINKCRYPTO_URI . '/assets/js/marketplace.js',
            ['backlinkcrypto-cart'],
            BACKLINKCRYPTO_VERSION,
            true
        );
    }
});

function backlinkcrypto_cart_count(): int
{
    if (!function_exists('WC') || !WC()->cart) {
        return 0;
    }

    return (int) WC()->cart->get_cart_contents_count();
}

/**
 * @return array<string,mixed>
 */
function backlinkcrypto_product_metrics(int $product_id): array
{
    $langs_raw = (string) get_post_meta($product_id, '_bc_languages', true);
    if ($langs_raw === '') {
        // Legacy single language field.
        $legacy = (string) get_post_meta($product_id, '_bc_language', true);
        $langs_raw = $legacy !== '' ? $legacy : 'EN';
    }

    return [
        'domain'    => (string) get_post_meta($product_id, '_bc_domain', true),
        'da'        => get_post_meta($product_id, '_bc_da', true),
        'dr'        => get_post_meta($product_id, '_bc_dr', true),
        'pa'        => get_post_meta($product_id, '_bc_pa', true),
        'traffic'   => get_post_meta($product_id, '_bc_traffic', true),
        'languages' => backlinkcrypto_parse_languages($langs_raw),
        'niche'     => (string) (get_post_meta($product_id, '_bc_niche', true) ?: 'Crypto'),
        'verified'  => get_post_meta($product_id, '_bc_verified', true) === '1',
        'dofollow'  => get_post_meta($product_id, '_bc_dofollow', true) !== '0',
        'extras'    => (string) get_post_meta($product_id, '_bc_extras', true),
        'as_of'     => (string) (get_post_meta($product_id, '_bc_metrics_as_of', true) ?: get_the_modified_date('Y-m-d', $product_id)),
    ];
}

/**
 * @return list<string>
 */
function backlinkcrypto_parse_languages(string $raw): array
{
    $map = [
        'english' => 'EN',
        'german' => 'DE',
        'spanish' => 'ES',
        'french' => 'FR',
        'italian' => 'IT',
        'portuguese' => 'PT',
        'turkish' => 'TR',
        'russian' => 'RU',
        'bulgarian' => 'BG',
        'dutch' => 'NL',
        'polish' => 'PL',
        'ukrainian' => 'UK',
        'thai' => 'TH',
        'bosnian' => 'BS',
        'croatian' => 'HR',
        'arabic' => 'AR',
        'japanese' => 'JA',
        'korean' => 'KO',
        'chinese' => 'ZH',
    ];

    $out = [];
    foreach (preg_split('/[\s,|\/]+/', $raw) ?: [] as $part) {
        $p = strtolower(trim($part));
        if ($p === '') {
            continue;
        }
        if (isset($map[$p])) {
            $code = $map[$p];
        } elseif (preg_match('/^[a-z]{2}$/', $p)) {
            $code = strtoupper($p);
        } else {
            continue;
        }
        if (!in_array($code, $out, true)) {
            $out[] = $code;
        }
    }

    return $out !== [] ? $out : ['EN'];
}

/**
 * Flag + code badge map for common marketplace languages.
 *
 * @return array{flag:string,label:string}
 */
function backlinkcrypto_language_meta(string $code): array
{
    $code = strtoupper($code);
    $map = [
        'EN' => ['flag' => '🇬🇧', 'label' => 'EN'],
        'US' => ['flag' => '🇺🇸', 'label' => 'EN'],
        'DE' => ['flag' => '🇩🇪', 'label' => 'DE'],
        'ES' => ['flag' => '🇪🇸', 'label' => 'ES'],
        'FR' => ['flag' => '🇫🇷', 'label' => 'FR'],
        'IT' => ['flag' => '🇮🇹', 'label' => 'IT'],
        'PT' => ['flag' => '🇧🇷', 'label' => 'PT'],
        'TR' => ['flag' => '🇹🇷', 'label' => 'TR'],
        'RU' => ['flag' => '🇷🇺', 'label' => 'RU'],
        'BG' => ['flag' => '🇧🇬', 'label' => 'BG'],
        'NL' => ['flag' => '🇳🇱', 'label' => 'NL'],
        'PL' => ['flag' => '🇵🇱', 'label' => 'PL'],
        'UK' => ['flag' => '🇺🇦', 'label' => 'UK'],
        'TH' => ['flag' => '🇹🇭', 'label' => 'TH'],
        'BS' => ['flag' => '🇧🇦', 'label' => 'BS'],
        'HR' => ['flag' => '🇭🇷', 'label' => 'HR'],
        'AR' => ['flag' => '🇸🇦', 'label' => 'AR'],
        'JA' => ['flag' => '🇯🇵', 'label' => 'JA'],
        'KO' => ['flag' => '🇰🇷', 'label' => 'KO'],
        'ZH' => ['flag' => '🇨🇳', 'label' => 'ZH'],
        'SV' => ['flag' => '🇸🇪', 'label' => 'SV'],
        'VI' => ['flag' => '🇻🇳', 'label' => 'VI'],
        'ID' => ['flag' => '🇮🇩', 'label' => 'ID'],
    ];

    return $map[$code] ?? ['flag' => '🌐', 'label' => $code];
}

/**
 * @param list<string> $codes
 */
function backlinkcrypto_render_language_badges(array $codes): void
{
    echo '<div class="bc-langs">';
    foreach ($codes as $code) {
        $meta = backlinkcrypto_language_meta($code);
        printf(
            '<span class="bc-lang" title="%s" data-lang="%s"><span class="bc-lang__flag" aria-hidden="true">%s</span><span class="bc-lang__code">%s</span></span>',
            esc_attr(strtoupper($code)),
            esc_attr(strtoupper($code)),
            esc_html($meta['flag']),
            esc_html($meta['label'])
        );
    }
    echo '</div>';
}

function backlinkcrypto_format_traffic($traffic): string
{
    if ($traffic === '' || $traffic === null) {
        return '—';
    }
    $n = (int) $traffic;
    if ($n >= 1000000) {
        return rtrim(rtrim(number_format($n / 1000000, 1), '0'), '.') . 'M';
    }
    if ($n >= 1000) {
        return rtrim(rtrim(number_format($n / 1000, 1), '0'), '.') . 'K';
    }

    return number_format($n);
}

function backlinkcrypto_dr_class($dr): string
{
    $n = (int) $dr;
    if ($n >= 60) {
        return 'is-high';
    }
    if ($n >= 40) {
        return 'is-mid';
    }

    return 'is-low';
}
