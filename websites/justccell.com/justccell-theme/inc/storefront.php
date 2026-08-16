<?php
/**
 * Store (country) + language axes.
 *
 * Store lives in the first URL segment: /uk /es /de /fr /it /other
 * Language stays ?lang= + cookie so WPML can take it later without owning /es/.
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
        'uk'    => ['lang' => 'en', 'currency' => 'GBP'],
        'us'    => ['lang' => 'en', 'currency' => 'USD'],
        'es'    => ['lang' => 'es', 'currency' => 'EUR'],
        'de'    => ['lang' => 'de', 'currency' => 'EUR'],
        'fr'    => ['lang' => 'fr', 'currency' => 'EUR'],
        'it'    => ['lang' => 'it', 'currency' => 'EUR'],
        'ch'    => ['lang' => 'en', 'currency' => 'CHF'],
        'ae'    => ['lang' => 'en', 'currency' => 'AED'],
        'other' => ['lang' => 'en', 'currency' => 'EUR'],
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
        'usa'   => 'us',
        'dubai' => 'ae',
        'uae'   => 'ae',
    ];
}

/**
 * @return list<string>
 */
function justccell_reserved_slugs(): array
{
    $slugs = array_merge(array_keys(justccell_stores()), array_keys(justccell_store_aliases()));
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
    return array_key_exists($code, justccell_stores()) ? $code : '';
}

function justccell_sanitize_store(string $code): string
{
    return justccell_resolve_store_slug($code);
}

/**
 * @return array<string, string> code => native label
 */
function justccell_languages(): array
{
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
    return array_key_exists($code, justccell_languages()) ? $code : '';
}

function justccell_is_wpml_active(): bool
{
    return defined('ICL_SITEPRESS_VERSION') || defined('ICL_LANGUAGE_CODE');
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
        'GB', 'UK' => 'uk',
        'US' => 'us',
        'ES' => 'es',
        'DE' => 'de',
        'FR' => 'fr',
        'IT' => 'it',
        'CH' => 'ch',
        'AE' => 'ae',
        default => 'other',
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

    return 'other';
}

function justccell_current_store(): string
{
    $from_request = justccell_sanitize_store((string) ($GLOBALS['justccell_request_store'] ?? ''));
    if ($from_request !== '') {
        return $from_request;
    }

    return justccell_detect_store();
}

function justccell_current_currency(): string
{
    $store = justccell_current_store();
    return justccell_stores()[$store]['currency'] ?? 'EUR';
}

function justccell_current_lang(): string
{
    if (justccell_is_wpml_active() && defined('ICL_LANGUAGE_CODE') && is_string(ICL_LANGUAGE_CODE)) {
        $wpml = justccell_sanitize_lang(ICL_LANGUAGE_CODE);
        if ($wpml !== '') {
            return $wpml;
        }
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

function justccell_lang_url(string $lang): string
{
    $lang = justccell_sanitize_lang($lang);
    if ($lang === '') {
        $lang = 'en';
    }

    $url = home_url(justccell_request_path());
    if (isset($_GET) && is_array($_GET)) {
        foreach ($_GET as $key => $value) {
            if ($key === 'lang' || !is_scalar($value)) {
                continue;
            }
            $url = add_query_arg((string) $key, (string) $value, $url);
        }
    }

    if (justccell_is_wpml_active() && has_filter('wpml_permalink')) {
        $filtered = apply_filters('wpml_permalink', $url, $lang, true);
        if (is_string($filtered) && $filtered !== '') {
            return $filtered;
        }
    }

    return add_query_arg('lang', $lang, $url);
}

function justccell_inject_store_prefix(string $url, string $store): string
{
    if ($store === '' || $url === '') {
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

    $lang = justccell_current_lang();
    if ($lang !== '') {
        justccell_set_front_cookie('jc_lang', $lang);
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
    if (preg_match('#^/(' . justccell_store_pattern() . ')(/|$)#i', $original_path, $match) === 1) {
        $raw = strtolower($match[1]);
        $aliases = justccell_store_aliases();
        if (isset($aliases[$raw])) {
            justccell_redirect_to_store($aliases[$raw], justccell_path_without_store($original_path), 301);
        }
    }

    if (justccell_sanitize_store((string) ($GLOBALS['justccell_request_store'] ?? '')) !== '') {
        return;
    }

    $store = justccell_detect_store();
    justccell_set_front_cookie('jc_store', $store);
    if (!isset($_COOKIE['jc_lang'])) {
        justccell_set_front_cookie('jc_lang', justccell_stores()[$store]['lang']);
    }

    justccell_redirect_to_store($store, justccell_request_path(), 302);
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

    header('X-LiteSpeed-Vary: cookie=jc_store,cookie=jc_lang', false);
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
    $cookies[] = 'jc_lang';
    return array_values(array_unique($cookies));
}
