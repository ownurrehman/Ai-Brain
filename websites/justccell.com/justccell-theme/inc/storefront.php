<?php
/**
 * Country store URLs only. Languages are WPML — do not add a custom switcher.
 *
 * UK is the default store on the bare domain (justccell.com).
 * Only Spain (/es/) and Switzerland (/ch/) get a country prefix.
 * WPML stays on ?lang= so it never owns /es/ as “Spanish”.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, array{lang:string, currency:string}>
 */
function justccell_stores(): array
{
    return [
        'uk' => ['lang' => 'en', 'currency' => 'GBP', 'prefix' => false],
        'es' => ['lang' => 'es', 'currency' => 'EUR', 'prefix' => true],
        'ch' => ['lang' => 'en', 'currency' => 'CHF', 'prefix' => true],
    ];
}

function justccell_default_store(): string
{
    return 'uk';
}

function justccell_store_uses_url_prefix(string $store): bool
{
    $store = justccell_sanitize_store($store);
    return $store !== '' && !empty(justccell_stores()[$store]['prefix']);
}

/**
 * Old first-path segments that 301 into the UK (bare domain).
 *
 * @return array<string, string>
 */
function justccell_retired_url_stores(): array
{
    return [
        'uk'    => 'uk',
        'other' => 'uk',
        'others'=> 'uk',
        'us'    => 'uk',
        'usa'   => 'uk',
        'de'    => 'uk',
        'fr'    => 'uk',
        'it'    => 'uk',
        'ae'    => 'uk',
        'dubai' => 'uk',
        'uae'   => 'uk',
    ];
}

/**
 * Extra first-path segments reserved so WPML cannot claim them.
 * Values are canonical store slugs.
 *
 * @return array<string, string>
 */
function justccell_store_aliases(): array
{
    return [
        'spain'        => 'es',
        'swiss'        => 'ch',
        'switzerland'  => 'ch',
    ];
}

/**
 * @return list<string>
 */
function justccell_reserved_slugs(): array
{
    $slugs = array_merge(
        array_keys(justccell_stores()),
        array_keys(justccell_store_aliases()),
        array_keys(justccell_retired_url_stores())
    );
    $slugs = array_values(array_unique($slugs));
    usort($slugs, static fn (string $a, string $b): int => strlen($b) <=> strlen($a));
    return $slugs;
}

function justccell_store_pattern(): string
{
    return implode('|', array_map('preg_quote', justccell_reserved_slugs()));
}

function justccell_resolve_store_slug(string $code): string
{
    $code = strtolower($code);
    $aliases = justccell_store_aliases();
    if (isset($aliases[$code])) {
        return $aliases[$code];
    }
    if (array_key_exists($code, justccell_stores())) {
        return $code;
    }
    $retired = justccell_retired_url_stores();
    return $retired[$code] ?? '';
}

function justccell_sanitize_store(string $code): string
{
    return justccell_resolve_store_slug($code);
}

function justccell_is_wpml_active(): bool
{
    return defined('ICL_SITEPRESS_VERSION') || defined('ICL_LANGUAGE_CODE');
}

/**
 * Active languages from WPML. Fallback list is only used if WPML is off.
 *
 * @return array<string, string> code => native label
 */
function justccell_languages(): array
{
    if (justccell_is_wpml_active()) {
        global $sitepress;
        if (is_object($sitepress) && method_exists($sitepress, 'get_active_languages')) {
            $current = $sitepress->get_active_languages();
            if (is_array($current) && $current !== []) {
                $out = [];
                foreach ($current as $code => $row) {
                    $code = strtolower((string) $code);
                    if ($code === '') {
                        continue;
                    }
                    $label = '';
                    if (is_array($row)) {
                        $label = (string) ($row['native_name'] ?? $row['display_name'] ?? $row['english_name'] ?? '');
                    }
                    $out[$code] = $label !== '' ? $label : $code;
                }
                if ($out !== []) {
                    return $out;
                }
            }
        }
    }

    return [
        'en' => 'English',
        'es' => 'Español',
        'fr' => 'Français',
        'de' => 'Deutsch',
        'it' => 'Italiano',
        'ar' => 'العربية',
        'ru' => 'Русский',
    ];
}

function justccell_sanitize_lang(string $code): string
{
    $code = strtolower($code);
    if ($code === '') {
        return '';
    }
    if (justccell_is_wpml_active() && defined('ICL_LANGUAGE_CODE') && strtolower((string) ICL_LANGUAGE_CODE) === $code) {
        return $code;
    }
    return array_key_exists($code, justccell_languages()) ? $code : '';
}

function justccell_request_path(?string $uri = null): string
{
    $uri  = $uri ?? (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $path = (string) (wp_parse_url($uri, PHP_URL_PATH) ?: '/');
    return $path === '' ? '/' : $path;
}

function justccell_is_skipped_path(string $path): bool
{
    $path = strtolower($path);
    $needles = [
        '/wp-admin',
        '/wp-login.php',
        '/wp-cron.php',
        '/xmlrpc.php',
        '/wp-json',
        '/wc-api',
        '/.well-known',
        '/wp-content',
        '/wp-includes',
    ];
    foreach ($needles as $needle) {
        if (str_starts_with($path, $needle)) {
            return true;
        }
    }

    if (str_contains($path, 'sitemap') || str_contains($path, 'wp-sitemap')) {
        return true;
    }

    $file = basename($path);
    return in_array($file, ['robots.txt', 'favicon.ico', 'ads.txt'], true);
}

function justccell_is_storefront_context(): bool
{
    if (defined('WP_CLI') && WP_CLI) {
        return false;
    }
    if (function_exists('wp_installing') && wp_installing()) {
        return false;
    }
    if (function_exists('wp_doing_cron') && wp_doing_cron()) {
        return false;
    }
    if (is_admin() && !wp_doing_ajax()) {
        return false;
    }

    $original = (string) ($GLOBALS['justccell_original_uri'] ?? ($_SERVER['REQUEST_URI'] ?? '/'));
    return !justccell_is_skipped_path(justccell_request_path($original));
}

function justccell_store_from_path(string $path): string
{
    if (preg_match('#^/(' . justccell_store_pattern() . ')(/|$)#i', $path, $match) !== 1) {
        return '';
    }
    return justccell_sanitize_store($match[1]);
}

function justccell_path_without_store(string $path): string
{
    $stripped = preg_replace('#^/(' . justccell_store_pattern() . ')(?=/|$)#i', '', $path);
    $stripped = is_string($stripped) ? $stripped : $path;
    if ($stripped === '' || $stripped[0] !== '/') {
        $stripped = '/' . ltrim($stripped, '/');
    }
    return $stripped === '' ? '/' : $stripped;
}

function justccell_country_from_request(): string
{
    $cf = strtoupper((string) ($_SERVER['HTTP_CF_IPCOUNTRY'] ?? ''));
    if (strlen($cf) === 2 && $cf !== 'XX' && $cf !== 'T1') {
        return $cf;
    }

    if (class_exists('WC_Geolocation')) {
        $geo = WC_Geolocation::geolocate_ip();
        $cc  = strtoupper((string) ($geo['country'] ?? ''));
        if (strlen($cc) === 2) {
            return $cc;
        }
    }

    return '';
}

function justccell_store_from_country(string $country): string
{
    $country = strtoupper($country);
    return match ($country) {
        'ES' => 'es',
        'CH' => 'ch',
        default => justccell_default_store(),
    };
}

function justccell_detect_store(): string
{
    if (isset($_COOKIE['jc_store'])) {
        $from_cookie = justccell_sanitize_store(sanitize_text_field(wp_unslash((string) $_COOKIE['jc_store'])));
        if ($from_cookie !== '') {
            return $from_cookie;
        }
    }

    $country = justccell_country_from_request();
    if ($country !== '') {
        return justccell_store_from_country($country);
    }

    return justccell_default_store();
}

function justccell_current_store(): string
{
    $from_request = justccell_sanitize_store((string) ($GLOBALS['justccell_request_store'] ?? ''));
    if ($from_request !== '' && justccell_store_uses_url_prefix($from_request)) {
        return $from_request;
    }

    return justccell_default_store();
}

function justccell_current_currency(): string
{
    $store = justccell_current_store();
    return justccell_stores()[$store]['currency'] ?? 'GBP';
}

/**
 * Turn Woo HTML money (&pound;3.36, <span>…</span>) into a plain UTF-8 string.
 */
function justccell_decode_money_text(string $text): string
{
    return html_entity_decode(wp_strip_all_tags($text), ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

/**
 * Store currency symbol as a real character (£ / € / CHF), never an HTML entity.
 */
function justccell_currency_symbol(): string
{
    $code = justccell_current_currency();
    $symbol = function_exists('get_woocommerce_currency_symbol')
        ? (string) get_woocommerce_currency_symbol($code)
        : '£';
    $decoded = justccell_decode_money_text($symbol);
    return $decoded !== '' ? $decoded : $code;
}

/**
 * Plain-text price for JSON, JS, and esc_html() contexts. Follows Woo currency.
 */
function justccell_format_money(float $amount): string
{
    if (function_exists('wc_price')) {
        return justccell_decode_money_text(wc_price($amount));
    }
    return justccell_currency_symbol() . "\u{00A0}" . number_format($amount, 2, '.', '');
}

/**
 * Keep a thin space between the currency glyph and the amount (£ 3.48, not £3.48).
 */
add_filter('woocommerce_price_format', static function (string $format, string $currency_pos): string {
    if ($currency_pos === 'left') {
        return '%1$s&nbsp;%2$s';
    }
    if ($currency_pos === 'right') {
        return '%2$s&nbsp;%1$s';
    }
    return $format;
}, 10, 2);

/**
 * HTML price (Woo markup) for innerHTML / wp_kses_post output.
 */
function justccell_format_money_html(float $amount): string
{
    if (function_exists('wc_price')) {
        return wp_kses_post(wc_price($amount));
    }
    return esc_html(justccell_format_money($amount));
}

function justccell_current_lang(): string
{
    if (justccell_is_wpml_active() && defined('ICL_LANGUAGE_CODE') && is_string(ICL_LANGUAGE_CODE) && ICL_LANGUAGE_CODE !== '') {
        return strtolower(ICL_LANGUAGE_CODE);
    }

    if (isset($_GET['lang'])) {
        $from_query = justccell_sanitize_lang(sanitize_text_field(wp_unslash((string) $_GET['lang'])));
        if ($from_query !== '') {
            return $from_query;
        }
    }

    if (isset($_COOKIE['jc_lang'])) {
        $from_cookie = justccell_sanitize_lang(sanitize_text_field(wp_unslash((string) $_COOKIE['jc_lang'])));
        if ($from_cookie !== '') {
            return $from_cookie;
        }
    }

    $store = justccell_current_store();
    return justccell_stores()[$store]['lang'] ?? 'en';
}

function justccell_set_front_cookie(string $name, string $value): void
{
    $path = defined('COOKIEPATH') && COOKIEPATH !== '' ? COOKIEPATH : '/';
    $host = defined('COOKIE_DOMAIN') ? COOKIE_DOMAIN : '';

    setcookie($name, $value, [
        'expires'  => time() + YEAR_IN_SECONDS,
        'path'     => $path,
        'domain'   => is_string($host) ? $host : '',
        'secure'   => is_ssl(),
        'httponly' => false,
        'samesite' => 'Lax',
    ]);
    $_COOKIE[$name] = $value;
}

function justccell_inject_store_prefix(string $url, string $store): string
{
    $store = justccell_sanitize_store($store);
    if ($store === '' || $url === '' || !justccell_store_uses_url_prefix($store)) {
        return $url;
    }

    $home = untrailingslashit((string) get_option('home'));
    if ($home === '' || !str_starts_with($url, $home)) {
        return $url;
    }

    $rest = substr($url, strlen($home));
    if ($rest === '') {
        $rest = '/';
    }

    $path = (string) (wp_parse_url($rest, PHP_URL_PATH) ?: '/');
    if (preg_match('#^/(' . justccell_store_pattern() . ')(/|$)#i', $path) === 1) {
        return $url;
    }

    return $home . '/' . $store . $rest;
}

/**
 * Strip /{store} so WordPress still resolves /contact/, /product/… as usual.
 */
function justccell_strip_store_prefix_from_request(): void
{
    $original = (string) ($_SERVER['REQUEST_URI'] ?? '/');
    $GLOBALS['justccell_original_uri'] = $original;

    $path = justccell_request_path($original);
    if (justccell_is_skipped_path($path)) {
        return;
    }

    $store = justccell_store_from_path($path);
    if ($store === '') {
        return;
    }

    $stripped = justccell_path_without_store($path);
    $query    = (string) (wp_parse_url($original, PHP_URL_QUERY) ?? '');
    $_SERVER['REQUEST_URI'] = $stripped . ($query !== '' ? '?' . $query : '');
    $GLOBALS['justccell_request_store'] = $store;
}

justccell_strip_store_prefix_from_request();

add_action('init', 'justccell_allow_hostinger_autologin', 0);

function justccell_is_hostinger_autologin_request(): bool
{
    $uri = (string) ($_SERVER['REQUEST_URI'] ?? '');
    return preg_match('#/create_autologin_[a-z0-9]+\.php#i', $uri) === 1;
}

function justccell_allow_hostinger_autologin(): void
{
    if (!justccell_is_hostinger_autologin_request()) {
        return;
    }

    remove_action('init', 'csmm_plugin_init');
}

if (justccell_is_hostinger_autologin_request()) {
    remove_action('init', 'csmm_plugin_init');
}

add_action('init', 'justccell_persist_front_cookies', 20);

function justccell_persist_front_cookies(): void
{
    if (!justccell_is_storefront_context()) {
        return;
    }

    $store = justccell_sanitize_store((string) ($GLOBALS['justccell_request_store'] ?? ''));
    if ($store !== '') {
        justccell_set_front_cookie('jc_store', $store);
    }

    if (!justccell_is_wpml_active()) {
        $lang = justccell_current_lang();
        if ($lang !== '') {
            justccell_set_front_cookie('jc_lang', $lang);
        }
    }
}

add_action('wp', 'justccell_geo_redirect', 0);

function justccell_geo_redirect(): void
{
    if (!justccell_is_storefront_context()) {
        return;
    }

    $method = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
    if ($method !== 'GET' && $method !== 'HEAD') {
        return;
    }

    if (function_exists('is_preview') && is_preview()) {
        return;
    }

    $original = (string) ($GLOBALS['justccell_original_uri'] ?? ($_SERVER['REQUEST_URI'] ?? '/'));
    $original_path = justccell_request_path($original);
    $raw = '';
    if (preg_match('#^/(' . justccell_store_pattern() . ')(/|$)#i', $original_path, $match) === 1) {
        $raw = strtolower($match[1]);
    }

    $aliases = justccell_store_aliases();
    if ($raw !== '' && isset($aliases[$raw])) {
        justccell_redirect_to_store($aliases[$raw], justccell_path_without_store($original_path), 301);
    }

    $retired = justccell_retired_url_stores();
    if ($raw !== '' && isset($retired[$raw])) {
        justccell_redirect_to_store(justccell_default_store(), justccell_path_without_store($original_path), 301);
    }

    $from_path = justccell_sanitize_store((string) ($GLOBALS['justccell_request_store'] ?? ''));
    if ($from_path !== '' && justccell_store_uses_url_prefix($from_path)) {
        return;
    }

    // Bare justccell.com is the UK catalogue. Do not send people to /uk/ or /other/.
    $has_store_cookie = isset($_COOKIE['jc_store'])
        && justccell_sanitize_store(sanitize_text_field(wp_unslash((string) $_COOKIE['jc_store']))) !== '';

    if (!$has_store_cookie) {
        $detected = justccell_store_from_country(justccell_country_from_request());
        if (justccell_store_uses_url_prefix($detected)) {
            justccell_set_front_cookie('jc_store', $detected);
            justccell_redirect_to_store($detected, justccell_request_path(), 302);
        }
    }

    justccell_set_front_cookie('jc_store', justccell_default_store());
}

function justccell_redirect_to_store(string $store, string $path, int $status): void
{
    $GLOBALS['justccell_skip_home_url'] = true;
    $url = justccell_inject_store_prefix(home_url($path), $store);
    $GLOBALS['justccell_skip_home_url'] = false;

    $qs = (string) ($_SERVER['QUERY_STRING'] ?? '');
    if ($qs !== '' && !str_contains($url, '?')) {
        $url .= '?' . $qs;
    }

    nocache_headers();
    header('X-LiteSpeed-Cache-Control: no-cache');
    wp_safe_redirect($url, $status);
    exit;
}

add_filter('home_url', 'justccell_filter_home_url', 1, 4);

/**
 * @param mixed $scheme
 * @param mixed $blog_id
 */
function justccell_filter_home_url(string $url, string $path, $scheme, $blog_id): string
{
    unset($path, $scheme, $blog_id);

    if (!empty($GLOBALS['justccell_skip_home_url'])) {
        return $url;
    }
    if (!justccell_is_storefront_context()) {
        return $url;
    }

    return justccell_inject_store_prefix($url, justccell_current_store());
}

add_filter('redirect_canonical', 'justccell_keep_store_canonical', 10, 2);

/**
 * @param string|false $redirect_url
 */
function justccell_keep_store_canonical($redirect_url, string $requested_url)
{
    unset($requested_url);
    if (justccell_sanitize_store((string) ($GLOBALS['justccell_request_store'] ?? '')) !== '') {
        return false;
    }
    return $redirect_url;
}

add_filter('woocommerce_currency', 'justccell_filter_currency', 999);

function justccell_filter_currency(string $currency): string
{
    unset($currency);
    return justccell_current_currency();
}

add_filter('language_attributes', 'justccell_language_attributes');

function justccell_language_attributes(string $output): string
{
    if (justccell_is_wpml_active()) {
        return $output;
    }

    $lang = justccell_current_lang();
    if (preg_match('/lang="[^"]*"/', $output) === 1) {
        return (string) preg_replace('/lang="[^"]*"/', 'lang="' . esc_attr($lang) . '"', $output, 1);
    }

    return $output . ' lang="' . esc_attr($lang) . '"';
}

add_filter('body_class', 'justccell_storefront_body_class');

/**
 * @param array<int, string> $classes
 * @return array<int, string>
 */
function justccell_storefront_body_class(array $classes): array
{
    $classes[] = 'jc-store-' . justccell_current_store();
    $classes[] = 'jc-lang-' . justccell_current_lang();
    $classes[] = 'jc-currency-' . strtolower(justccell_current_currency());
    return $classes;
}

add_action('send_headers', 'justccell_vary_cache_headers');

function justccell_vary_cache_headers(): void
{
    if (!justccell_is_storefront_context()) {
        return;
    }

    header('X-LiteSpeed-Vary: cookie=jc_store,cookie=wp-wpml_current_language,cookie=jc_lang', false);
    header('Vary: Cookie', false);
}

add_filter('litespeed_vary_curr_cookies', 'justccell_litespeed_vary_cookies');

/**
 * @param array<int, string> $cookies
 * @return array<int, string>
 */
function justccell_litespeed_vary_cookies(array $cookies): array
{
    $cookies[] = 'jc_store';
    $cookies[] = 'wp-wpml_current_language';
    $cookies[] = 'jc_lang';
    return array_values(array_unique($cookies));
}
