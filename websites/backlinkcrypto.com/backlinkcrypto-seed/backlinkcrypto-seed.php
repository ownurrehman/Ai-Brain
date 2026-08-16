<?php
/**
 * Plugin Name: Backlink Crypto Seed
 * Description: Idempotent USD store setup, crypto categories, starter packages, and crypto/manual payment instructions for backlinkcrypto.com.
 * Version: 1.0.0
 * Author: RankRay
 * Requires Plugins: woocommerce
 * Text Domain: backlinkcrypto-seed
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BC_SEED_VERSION', '1.0.0');
define('BC_SEED_OPTION', 'backlinkcrypto_seed_v2');

register_activation_hook(__FILE__, 'backlinkcrypto_seed_activate');

/**
 * Run seed on activation (and allow manual re-run via admin action).
 */
function backlinkcrypto_seed_activate(): void
{
    if (!class_exists('WooCommerce')) {
        return;
    }

    backlinkcrypto_seed_run(true);
}

/**
 * Hostinger MCP activate may skip register_activation_hook — auto-seed once when active.
 */
add_action('init', static function (): void {
    if (!class_exists('WooCommerce')) {
        return;
    }
    if (get_option(BC_SEED_OPTION)) {
        return;
    }
    // Avoid racing during plugin bootstrap.
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    backlinkcrypto_seed_run(true);
}, 30);

add_action('admin_init', static function (): void {
    if (!current_user_can('manage_woocommerce')) {
        return;
    }

    if (!isset($_GET['bc_seed_run']) || !wp_verify_nonce((string) ($_GET['_wpnonce'] ?? ''), 'bc_seed_run')) {
        return;
    }

    if (!class_exists('WooCommerce')) {
        wp_die(esc_html__('WooCommerce is required.', 'backlinkcrypto-seed'));
    }

    backlinkcrypto_seed_run(true);
    wp_safe_redirect(admin_url('admin.php?page=wc-settings&bc_seed=1'));
    exit;
});

add_action('admin_notices', static function (): void {
    if (!current_user_can('manage_woocommerce')) {
        return;
    }

    if (isset($_GET['bc_seed']) && $_GET['bc_seed'] === '1') {
        echo '<div class="notice notice-success is-dismissible"><p>Backlink Crypto seed completed.</p></div>';
    }

    if (get_option(BC_SEED_OPTION)) {
        return;
    }

    if (!class_exists('WooCommerce')) {
        return;
    }

    $url = wp_nonce_url(admin_url('admin.php?bc_seed_run=1'), 'bc_seed_run');
    echo '<div class="notice notice-warning"><p><strong>Backlink Crypto Seed</strong> has not finished. ';
    echo '<a href="' . esc_url($url) . '">Run store seed now</a>.</p></div>';
});

/**
 * Idempotent seed.
 */
function backlinkcrypto_seed_run(bool $force = false): void
{
    if (!$force && get_option(BC_SEED_OPTION)) {
        return;
    }

    backlinkcrypto_seed_store_settings();
    $term_ids = backlinkcrypto_seed_categories();
    backlinkcrypto_seed_products($term_ids);
    backlinkcrypto_seed_payment_instructions();
    backlinkcrypto_seed_pages();

    update_option(BC_SEED_OPTION, [
        'version' => BC_SEED_VERSION,
        'seeded_at' => gmdate('c'),
    ]);

    if (function_exists('wc_delete_product_transients')) {
        wc_delete_product_transients();
    }

    flush_rewrite_rules();
}

function backlinkcrypto_seed_store_settings(): void
{
    update_option('blogname', 'Backlink Crypto');
    update_option('blogdescription', 'Crypto backlinks marketplace');
    update_option('woocommerce_currency', 'USD');
    update_option('woocommerce_price_thousand_sep', ',');
    update_option('woocommerce_price_decimal_sep', '.');
    update_option('woocommerce_price_num_decimals', '2');
    update_option('woocommerce_default_country', 'US:CA');
    update_option('woocommerce_allow_tracking', 'no');
    update_option('woocommerce_enable_guest_checkout', 'yes');
    update_option('woocommerce_enable_checkout_login_reminder', 'yes');
    update_option('woocommerce_cart_redirect_after_add', 'no');
    update_option('show_on_front', 'posts'); // theme front-page.php handles home
}

/**
 * @return array<string,int> slug => term_id
 */
function backlinkcrypto_seed_categories(): array
{
    $cats = [
        'bitcoin' => 'Bitcoin',
        'defi' => 'DeFi',
        'nft' => 'NFT',
        'exchanges' => 'Exchanges',
        'crypto-news' => 'Crypto News',
        'web3' => 'Web3',
    ];

    $out = [];
    foreach ($cats as $slug => $name) {
        $existing = get_term_by('slug', $slug, 'product_cat');
        if ($existing && !is_wp_error($existing)) {
            $out[$slug] = (int) $existing->term_id;
            continue;
        }

        $result = wp_insert_term($name, 'product_cat', [
            'slug' => $slug,
            'description' => sprintf('%s niche crypto backlink packages.', $name),
        ]);

        if (!is_wp_error($result)) {
            $out[$slug] = (int) $result['term_id'];
        }
    }

    return $out;
}

/**
 * @param array<string,int> $term_ids
 */
function backlinkcrypto_seed_products(array $term_ids): void
{
    $products = [
        [
            'sku' => 'BC-BTC-GUEST-01',
            'name' => 'Bitcoin Guest Post — Mid Authority',
            'cat' => 'bitcoin',
            'price' => '149',
            'short' => 'DR 40–55 · dofollow · niche: Bitcoin / BTC news',
            'desc' => 'One guest post placement on a vetted Bitcoin publisher. Includes one dofollow contextual link. Delivery after payment confirmation.',
        ],
        [
            'sku' => 'BC-BTC-HOME-01',
            'name' => 'Bitcoin Homepage Link',
            'cat' => 'bitcoin',
            'price' => '299',
            'short' => 'DR 50+ · homepage · permanent placement',
            'desc' => 'Homepage contextual or resource link on a Bitcoin site. Best for brand authority pushes.',
        ],
        [
            'sku' => 'BC-DEFI-GUEST-01',
            'name' => 'DeFi Guest Post Package',
            'cat' => 'defi',
            'price' => '179',
            'short' => 'DR 35–50 · DeFi / protocols · dofollow',
            'desc' => 'Article placement on a DeFi-focused site covering protocols, yield, or on-chain finance.',
        ],
        [
            'sku' => 'BC-DEFI-NICHE-01',
            'name' => 'DeFi Niche Edit Link',
            'cat' => 'defi',
            'price' => '119',
            'short' => 'Existing article insert · contextual · dofollow',
            'desc' => 'Link insertion into an existing DeFi article. Faster turnaround than a full guest post.',
        ],
        [
            'sku' => 'BC-NFT-GUEST-01',
            'name' => 'NFT / Collectibles Guest Post',
            'cat' => 'nft',
            'price' => '129',
            'short' => 'DR 30–45 · NFT / Web3 culture',
            'desc' => 'Guest post on an NFT or digital collectibles publisher with one contextual backlink.',
        ],
        [
            'sku' => 'BC-EX-REVIEW-01',
            'name' => 'Crypto Exchange Review Link',
            'cat' => 'exchanges',
            'price' => '249',
            'short' => 'Exchange / trading niche · review or comparison page',
            'desc' => 'Placement on an exchange or trading review property. Strong for CEX/DEX landing pages.',
        ],
        [
            'sku' => 'BC-EX-RESOURCE-01',
            'name' => 'Exchange Resource Page Link',
            'cat' => 'exchanges',
            'price' => '199',
            'short' => 'Resource / tools page · dofollow',
            'desc' => 'Link from a curated trading tools or exchange resource page.',
        ],
        [
            'sku' => 'BC-NEWS-GUEST-01',
            'name' => 'Crypto News Guest Post',
            'cat' => 'crypto-news',
            'price' => '219',
            'short' => 'DR 45–60 · news publisher · editorial style',
            'desc' => 'Editorial-style guest contribution on a crypto news site. Subject to publisher guidelines.',
        ],
        [
            'sku' => 'BC-WEB3-GUEST-01',
            'name' => 'Web3 Startup Guest Post',
            'cat' => 'web3',
            'price' => '159',
            'short' => 'Web3 / builders · dofollow · niche relevant',
            'desc' => 'Guest post aimed at Web3 builders, wallets, infra, or dApps audiences.',
        ],
        [
            'sku' => 'BC-WEB3-BUNDLE-01',
            'name' => 'Web3 Starter Bundle (3 links)',
            'cat' => 'web3',
            'price' => '399',
            'short' => '3 mixed Web3 placements · package discount',
            'desc' => 'Bundle of three Web3-niche placements. Ideal for new protocol launches.',
        ],
        [
            'sku' => 'BC-BTC-BUNDLE-01',
            'name' => 'Bitcoin Authority Bundle (2 links)',
            'cat' => 'bitcoin',
            'price' => '449',
            'short' => '2 Bitcoin placements · higher authority mix',
            'desc' => 'Two Bitcoin-niche placements selected for stronger combined authority.',
        ],
        [
            'sku' => 'BC-MIX-STARTER-01',
            'name' => 'Crypto Mix Starter (DeFi + News)',
            'cat' => 'crypto-news',
            'price' => '329',
            'short' => '1 DeFi + 1 Crypto News · starter pack',
            'desc' => 'Balanced starter pack: one DeFi and one crypto news placement.',
        ],
    ];

    foreach ($products as $item) {
        $existing_id = wc_get_product_id_by_sku($item['sku']);
        if ($existing_id) {
            continue;
        }

        $product = new WC_Product_Simple();
        $product->set_name($item['name']);
        $product->set_sku($item['sku']);
        $product->set_regular_price($item['price']);
        $product->set_short_description($item['short']);
        $product->set_description($item['desc']);
        $product->set_status('publish');
        $product->set_catalog_visibility('visible');
        $product->set_sold_individually(false);
        $product->set_manage_stock(false);
        $product->set_stock_status('instock');

        if (!empty($term_ids[$item['cat']])) {
            $product->set_category_ids([$term_ids[$item['cat']]]);
        }

        $product->save();
    }

    // Draft legacy placeholder products (non BC-* SKUs) so catalog is crypto-only.
    $legacy = get_posts([
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => 50,
        'fields'         => 'ids',
    ]);

    foreach ($legacy as $pid) {
        $sku = (string) get_post_meta((int) $pid, '_sku', true);
        if ($sku !== '' && str_starts_with($sku, 'BC-')) {
            continue;
        }
        // Keep newly seeded; draft anything else still published without BC- sku.
        $product = wc_get_product((int) $pid);
        if (!$product) {
            continue;
        }
        if (str_starts_with((string) $product->get_sku(), 'BC-')) {
            continue;
        }
        $product->set_status('draft');
        $product->save();
    }
}

function backlinkcrypto_seed_payment_instructions(): void
{
    // Enable BACS (bank transfer) and customize as crypto/manual payment.
    $gateways = (array) get_option('woocommerce_bacs_settings', []);
    $gateways['enabled'] = 'yes';
    $gateways['title'] = 'Crypto / Manual payment';
    $gateways['description'] = 'Pay with USDT (TRC20/ERC20) or bank transfer. Order stays on hold until we confirm payment.';
    $gateways['instructions'] = "Send payment using the details below, then reply to your order email with the tx hash or transfer reference.\n\nUSDT (TRC20): REPLACE_WITH_YOUR_USDT_TRC20_WALLET\nUSDT (ERC20): REPLACE_WITH_YOUR_USDT_ERC20_WALLET\n\nInclude your order number in the memo/note when possible.";
    update_option('woocommerce_bacs_settings', $gateways);

    // Keep COD disabled; enable cheque disabled.
    $cod = (array) get_option('woocommerce_cod_settings', []);
    $cod['enabled'] = 'no';
    update_option('woocommerce_cod_settings', $cod);

    // Optional account name shown on BACS accounts list.
    $accounts = get_option('woocommerce_bacs_accounts', []);
    if (!is_array($accounts) || empty($accounts)) {
        update_option('woocommerce_bacs_accounts', [
            [
                'account_name'   => 'Backlink Crypto — USDT',
                'account_number' => 'REPLACE_USDT_WALLET',
                'bank_name'      => 'Crypto / Manual',
                'sort_code'      => '',
                'iban'           => '',
                'bic'            => '',
            ],
        ]);
    }
}

function backlinkcrypto_seed_pages(): void
{
    // Ensure Woo pages exist.
    if (function_exists('wc_create_page')) {
        // no-op — Woo already installed pages on this site
    }

    $shop_id = (int) get_option('woocommerce_shop_page_id');
    if ($shop_id) {
        wp_update_post([
            'ID' => $shop_id,
            'post_title' => 'Catalog',
        ]);
    }

    // Prefer posts index so front-page.php renders marketplace home.
    update_option('show_on_front', 'posts');
}
