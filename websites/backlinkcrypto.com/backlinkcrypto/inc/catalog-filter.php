<?php
/**
 * One-shot catalog filter — draft products that are not crypto / tech related.
 *
 * Runs once after deploy (admin_init) and can be re-run from Inventory.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/** Option flag when cleanup has completed. */
const BC_CATALOG_FILTER_OPTION = 'bc_catalog_crypto_tech_filter_v1';

/**
 * Domains that are sports, betting, entertainment, weather, lottery, general news, etc.
 *
 * @return list<string>
 */
function backlinkcrypto_catalog_remove_domains(): array
{
    return [
        'tribuneonlineng.com',
        'completesports.com',
        'd-addicts.com',
        'inkl.com',
        'diariodorio.com',
        'photosnow.org',
        'skymetweather.com',
        'justshowbiz.net',
        'feedinco.com',
        'oceanofgames.com',
        'tellyexpress.com',
        'arcarrierpoint.net',
        'sportsport.ba',
        'cgn.inf.br',
        'thesportsbank.net',
        'ria-m.tv',
        'fightmatrix.com',
        'nordest24.it',
        'sportshike.co.uk',
        'wheongaming.com',
        'aquinoticias.com',
        'victorspredict.com',
        'lmhmod.me',
        'betensured.com',
        'hdmovieshub.us',
        'diarioconvos.com',
        'nfldraftbuzz.com',
        'messivsronaldo.app',
        'wrytin.com',
        'lawbhoomi.com',
        'nagalandstatelottery.in',
        'shayaria.com',
        'bekaboy.com',
        'dailybulls.in',
        'venasbet.com',
        'focuspredict.com',
        'liverpool.in.th',
        'passionpredict.com',
        'legitpredict.com',
        'bettinghike.net',
        'dhankesari.org',
        'primatips.com',
        'casinocheckmate.net',
    ];
}

/**
 * Name-only listings (no domain) that are not clearly crypto/tech.
 *
 * @return list<string>
 */
function backlinkcrypto_catalog_remove_names(): array
{
    return [
        'Prices',
        'Times Tabloid',
        'Eleven News',
        'Telegaon',
    ];
}

/**
 * Normalize a domain or title for comparison.
 */
function backlinkcrypto_catalog_normalize_host(string $value): string
{
    $value = strtolower(trim($value));
    $value = preg_replace('#^https?://#', '', $value) ?? $value;
    $value = preg_replace('#^www\.#', '', $value) ?? $value;
    $value = explode('/', $value)[0];
    return rtrim($value, '.');
}

/**
 * @return array{drafted:int,ids:list<int>,labels:list<string>}
 */
function backlinkcrypto_run_catalog_crypto_tech_filter(bool $force = false): array
{
    $result = [
        'drafted' => 0,
        'ids'     => [],
        'labels'  => [],
    ];

    if (!function_exists('wc_get_product')) {
        return $result;
    }

    if (!$force && get_option(BC_CATALOG_FILTER_OPTION) === '1') {
        return $result;
    }

    $remove_domains = array_fill_keys(backlinkcrypto_catalog_remove_domains(), true);
    $remove_names   = [];
    foreach (backlinkcrypto_catalog_remove_names() as $n) {
        $remove_names[strtolower(trim($n))] = true;
    }

    $q = new WP_Query([
        'post_type'              => 'product',
        'post_status'            => ['publish', 'private'],
        'posts_per_page'         => -1,
        'fields'                 => 'ids',
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ]);

    foreach ($q->posts as $product_id) {
        $product_id = (int) $product_id;
        $product    = wc_get_product($product_id);
        if (!$product) {
            continue;
        }

        $domain = backlinkcrypto_catalog_normalize_host((string) get_post_meta($product_id, '_bc_domain', true));
        $name   = trim($product->get_name());
        $name_l = strtolower($name);
        $title_as_domain = backlinkcrypto_catalog_normalize_host($name);

        $should_draft = false;
        $label        = $name;

        if ($domain !== '' && isset($remove_domains[$domain])) {
            $should_draft = true;
            $label = $domain;
        } elseif ($domain === '' && isset($remove_names[$name_l])) {
            $should_draft = true;
        } elseif ($domain === '' && isset($remove_domains[$title_as_domain])) {
            $should_draft = true;
            $label = $title_as_domain;
        } elseif ($domain === '' && isset($remove_domains[$name_l])) {
            $should_draft = true;
        } elseif (isset($remove_domains[$title_as_domain]) && strpos($title_as_domain, '.') !== false) {
            // Title looks like a domain that is on the remove list.
            $should_draft = true;
            $label = $title_as_domain;
        }

        if (!$should_draft) {
            continue;
        }

        $product->set_status('draft');
        $product->save();
        $result['drafted']++;
        $result['ids'][] = $product_id;
        $result['labels'][] = $label;
    }

    update_option(BC_CATALOG_FILTER_OPTION, '1', false);

    return $result;
}

add_action('init', static function (): void {
    if (get_option(BC_CATALOG_FILTER_OPTION) === '1') {
        return;
    }
    if (!function_exists('wc_get_product')) {
        return;
    }
    // One-shot after deploy: draft non crypto/tech products on first request.
    backlinkcrypto_run_catalog_crypto_tech_filter(false);
}, 30);

add_action('admin_init', static function (): void {
    if (!current_user_can('edit_products')) {
        return;
    }
    if (get_option(BC_CATALOG_FILTER_OPTION) === '1') {
        return;
    }
    backlinkcrypto_run_catalog_crypto_tech_filter(false);
});

add_action('wp_ajax_bc_catalog_filter_run', static function (): void {
    if (!current_user_can('edit_products')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    check_ajax_referer('bc_inventory_save', 'nonce');
    // Allow re-run from Inventory.
    delete_option(BC_CATALOG_FILTER_OPTION);
    $result = backlinkcrypto_run_catalog_crypto_tech_filter(true);
    wp_send_json_success($result);
});
