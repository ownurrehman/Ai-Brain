<?php
/**
 * Package products + writing add-on + niche landing pages.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/** Default support address on brand domain — see backlinkcrypto_default_support_email() in setup.php. */

/**
 * @return list<array{slug:string,title:string,niche:string,desc:string,seo_title:string,seo_desc:string}>
 */
function backlinkcrypto_niche_page_defs(): array
{
    return [
        [
            'slug'      => 'crypto-backlinks',
            'title'     => 'Crypto Backlinks',
            'niche'     => 'Crypto',
            'desc'      => 'Guest posts on crypto publishers — exchanges, token sites, and Web3 media.',
            'seo_title' => 'Buy Crypto Backlinks | Guest Posts on Crypto Sites',
            'seo_desc'  => 'Browse crypto-niche guest post placements with DA, DR, and traffic filters. Pay in crypto on Backlink Crypto.',
        ],
        [
            'slug'      => 'defi-backlinks',
            'title'     => 'DeFi Backlinks',
            'niche'     => 'DeFi',
            'desc'      => 'Placements on DeFi-focused publishers for protocols, yields, and on-chain products.',
            'seo_title' => 'Buy DeFi Backlinks | DeFi Guest Posts Marketplace',
            'seo_desc'  => 'Find DeFi guest post opportunities with transparent SEO metrics. Checkout in crypto.',
        ],
        [
            'slug'      => 'nft-backlinks',
            'title'     => 'NFT Backlinks',
            'niche'     => 'NFT',
            'desc'      => 'Guest posts aimed at NFT projects, marketplaces, and collectors.',
            'seo_title' => 'Buy NFT Backlinks | NFT Guest Post Placements',
            'seo_desc'  => 'NFT-focused backlink placements with DA/DR filters. Buy on Backlink Crypto.',
        ],
        [
            'slug'      => 'exchange-backlinks',
            'title'     => 'Trading Platform Backlinks',
            'niche'     => 'Exchange',
            'desc'      => 'Guest posts on publishers covering crypto trading platforms and market audiences — not reciprocal link swaps.',
            'seo_title' => 'Trading Platform Backlinks | Crypto Guest Posts',
            'seo_desc'  => 'Guest posts for trading-platform and market SEO. Filter by DR, DA, and traffic. Buy placements — not link exchanges.',
        ],
        [
            'slug'      => 'finance-backlinks',
            'title'     => 'Finance Backlinks',
            'niche'     => 'Finance',
            'desc'      => 'Finance and fintech publisher placements that overlap with crypto audiences.',
            'seo_title' => 'Finance Backlinks for Crypto Brands | Guest Posts',
            'seo_desc'  => 'Finance-niche guest posts for crypto and fintech SEO campaigns.',
        ],
        [
            'slug'      => 'crypto-news-backlinks',
            'title'     => 'Crypto News Backlinks',
            'niche'     => 'News',
            'desc'      => 'News and media placements for announcements, coverage, and topical authority.',
            'seo_title' => 'Crypto News Backlinks | Media Guest Posts',
            'seo_desc'  => 'Buy guest posts on crypto news publishers. Transparent metrics, crypto checkout.',
        ],
    ];
}

/**
 * @return list<array{sku:string,name:string,price:float,desc:string,bullets:list<string>}>
 */
function backlinkcrypto_package_defs(): array
{
    return [
        [
            'sku'     => 'BC-PKG-TRIAL',
            'name'    => 'Trial Placement — 1 mid-DR site',
            'price'   => 249.0,
            'desc'    => 'One guest post on a mid-DR crypto publisher so you can test fulfillment before a larger pack. Fast-track review when content is ready.',
            'bullets' => [
                '1 guest post placement',
                'DR 40–60 typical match',
                'Ideal first purchase',
                'Typical delivery 3–7 days after approved article',
            ],
        ],
        [
            'sku'     => 'BC-PKG-STARTER',
            'name'    => 'Starter Pack — 5 placements',
            'price'   => 999.0,
            'desc'    => 'Five guest posts on DR 40+ crypto publishers. We pick sites from inventory that match your niche after checkout.',
            'bullets' => [
                '5 guest post placements',
                'DR 40+ publishers',
                'You provide articles (or add writing)',
                'Typical delivery 1–2 weeks',
            ],
        ],
        [
            'sku'     => 'BC-PKG-GROWTH',
            'name'    => 'Growth Pack — 10 placements',
            'price'   => 2499.0,
            'desc'    => 'Ten placements on DR 50+ sites for campaigns that need broader coverage.',
            'bullets' => [
                '10 guest post placements',
                'DR 50+ publishers',
                'Niche + language matching',
                'Dedicated fulfillment notes in My Account',
            ],
        ],
        [
            'sku'     => 'BC-PKG-AUTHORITY',
            'name'    => 'Authority Pack — 5 high-DR',
            'price'   => 4999.0,
            'desc'    => 'Five placements on stronger publishers (DR 70+ where available) for competitive keywords.',
            'bullets' => [
                '5 high-authority placements',
                'DR 70+ focus',
                'Priority review queue',
                'Best for competitive crypto SEO',
            ],
        ],
    ];
}

add_action('init', 'backlinkcrypto_ensure_packages_and_niches', 50);

function backlinkcrypto_ensure_packages_and_niches(): void
{
    if (!class_exists('WooCommerce')) {
        return;
    }
    $flag = 'bc_packages_niches_' . BACKLINKCRYPTO_VERSION;
    if (get_option($flag) === '1') {
        return;
    }
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    // Product category for packages.
    $pkg_cat = get_term_by('slug', 'packages', 'product_cat');
    if (!$pkg_cat || is_wp_error($pkg_cat)) {
        $created = wp_insert_term('Packages', 'product_cat', ['slug' => 'packages']);
        $pkg_cat_id = !is_wp_error($created) ? (int) $created['term_id'] : 0;
    } else {
        $pkg_cat_id = (int) $pkg_cat->term_id;
    }

    foreach (backlinkcrypto_package_defs() as $pkg) {
        $id = wc_get_product_id_by_sku($pkg['sku']);
        $product = $id ? wc_get_product($id) : new WC_Product_Simple();
        if (!$product) {
            continue;
        }
        $product->set_name($pkg['name']);
        $product->set_sku($pkg['sku']);
        $product->set_regular_price((string) $pkg['price']);
        $product->set_description($pkg['desc']);
        $product->set_short_description(implode(' · ', $pkg['bullets']));
        $product->set_catalog_visibility('visible');
        $product->set_status('publish');
        $product->set_sold_individually(false);
        $product->set_virtual(true);
        $saved = $product->save();
        if ($saved && $pkg_cat_id > 0) {
            wp_set_object_terms($saved, [$pkg_cat_id], 'product_cat');
        }
        if ($saved) {
            update_post_meta($saved, '_bc_is_package', '1');
            update_post_meta($saved, '_bc_package_bullets', wp_json_encode($pkg['bullets']));
        }
    }

    // Packages page.
    $pkg_page = get_page_by_path('packages');
    if (!$pkg_page) {
        wp_insert_post([
            'post_type'    => 'page',
            'post_name'    => 'packages',
            'post_title'   => 'Backlink Packages',
            'post_content' => '',
            'post_status'  => 'publish',
        ]);
    }
    $pkg_page = get_page_by_path('packages');
    if ($pkg_page) {
        update_post_meta((int) $pkg_page->ID, '_bc_seo_title', 'Crypto Backlink Packages | Starter, Growth & Authority');
        update_post_meta((int) $pkg_page->ID, '_bc_seo_description', 'Buy curated crypto backlink packages — starter, growth, and high-DR authority packs. Pay in crypto.');
    }

    foreach (backlinkcrypto_niche_page_defs() as $def) {
        $page = get_page_by_path($def['slug']);
        if (!$page) {
            $pid = wp_insert_post([
                'post_type'    => 'page',
                'post_name'    => $def['slug'],
                'post_title'   => $def['title'],
                'post_content' => $def['desc'],
                'post_status'  => 'publish',
            ], true);
            if (is_wp_error($pid)) {
                continue;
            }
            $page_id = (int) $pid;
        } else {
            $page_id = (int) $page->ID;
            wp_update_post([
                'ID'           => $page_id,
                'post_title'   => $def['title'],
                'post_content' => $def['desc'],
            ]);
        }
        update_post_meta($page_id, '_bc_niche_filter', $def['niche']);
        update_post_meta($page_id, '_wp_page_template', 'page-niche.php');
        update_post_meta($page_id, '_bc_seo_title', $def['seo_title']);
        update_post_meta($page_id, '_bc_seo_description', $def['seo_desc']);
    }

    // Public support must stay on brand domain; notify may stay internal (never customer-facing).
    $settings = get_option(BC_THEME_SETTINGS_OPTION, []);
    if (!is_array($settings)) {
        $settings = [];
    }
    $current = (string) ($settings['support_email'] ?? '');
    if ($current === '' || backlinkcrypto_is_internal_brand_email($current)) {
        $settings['support_email'] = backlinkcrypto_default_support_email();
    }
    update_option(BC_THEME_SETTINGS_OPTION, $settings);
    if (function_exists('backlinkcrypto_sync_payment_gateway_instructions')) {
        backlinkcrypto_sync_payment_gateway_instructions(backlinkcrypto_get_theme_settings());
    }

    update_option($flag, '1');
}

/** Writing add-on unit price (USD). */
function backlinkcrypto_writing_addon_price(): float
{
    return 149.0;
}

add_action('woocommerce_cart_calculate_fees', static function (WC_Cart $cart): void {
    if (is_admin() && !defined('DOING_AJAX')) {
        return;
    }
    if (empty($_POST['bc_writing_addon']) && empty(WC()->session->get('bc_writing_addon'))) {
        // Keep session if set via cart update.
    }
    $enabled = false;
    if (isset($_POST['bc_writing_addon'])) {
        $enabled = (string) wp_unslash($_POST['bc_writing_addon']) === '1';
        if (WC()->session) {
            WC()->session->set('bc_writing_addon', $enabled ? '1' : '0');
        }
    } elseif (WC()->session) {
        $enabled = WC()->session->get('bc_writing_addon') === '1';
    }
    if (!$enabled) {
        return;
    }
    $qty = 0;
    foreach ($cart->get_cart() as $item) {
        $pid = (int) ($item['product_id'] ?? 0);
        if ($pid && get_post_meta($pid, '_bc_is_package', true) === '1') {
            // Packages: 1 writing fee per package unit (bundle).
            $qty += (int) ($item['quantity'] ?? 1);
            continue;
        }
        if ($pid && get_post_meta($pid, '_bc_is_package', true) !== '1') {
            $qty += (int) ($item['quantity'] ?? 1);
        }
    }
    if ($qty <= 0) {
        return;
    }
    $amount = backlinkcrypto_writing_addon_price() * $qty;
    $cart->add_fee(
        sprintf(
            /* translators: %d: article count */
            __('Article writing (%d)', 'backlinkcrypto'),
            $qty
        ),
        $amount,
        false
    );
});

add_action('woocommerce_before_cart_totals', static function (): void {
    if (!WC()->cart || WC()->cart->is_empty()) {
        return;
    }
    $checked = WC()->session && WC()->session->get('bc_writing_addon') === '1';
    $price = backlinkcrypto_writing_addon_price();
    ?>
    <div class="bc-writing-addon">
        <label class="bc-writing-addon__label">
            <input
                type="checkbox"
                name="bc_writing_addon"
                value="1"
                form="woocommerce-cart"
                <?php checked($checked); ?>
                onchange="(function(){var b=document.querySelector('button[name=update_cart]');if(b){b.disabled=false;b.click();}})();"
            />
            <span>
                <?php
                printf(
                    esc_html__('Add professional article writing (+%s per placement)', 'backlinkcrypto'),
                    wp_strip_all_tags(wc_price($price))
                );
                ?>
            </span>
        </label>
        <p class="bc-writing-addon__hint"><?php esc_html_e('Our writers draft the guest post from your brief. You approve before publish.', 'backlinkcrypto'); ?></p>
    </div>
    <?php
});

// Persist checkbox when cart updates (form may omit unchecked boxes).
add_action('woocommerce_update_cart_action_cart_updated', static function (): void {
    if (!WC()->session) {
        return;
    }
    $on = isset($_POST['bc_writing_addon']) && (string) wp_unslash($_POST['bc_writing_addon']) === '1';
    WC()->session->set('bc_writing_addon', $on ? '1' : '0');
}, 5);

add_action('woocommerce_checkout_update_order_meta', static function (int $order_id): void {
    if (WC()->session && WC()->session->get('bc_writing_addon') === '1') {
        update_post_meta($order_id, '_bc_writing_addon', '1');
    }
});


add_action('woocommerce_review_order_before_payment', static function (): void {
    if (!WC()->cart || WC()->cart->is_empty()) {
        return;
    }
    $checked = WC()->session && WC()->session->get('bc_writing_addon') === '1';
    $price = backlinkcrypto_writing_addon_price();
    ?>
    <div class="bc-writing-addon bc-writing-addon--checkout">
        <label class="bc-writing-addon__label">
            <input type="checkbox" name="bc_writing_addon" value="1" <?php checked($checked); ?> />
            <span>
                <?php
                printf(
                    esc_html__('Add professional article writing (+%s per placement)', 'backlinkcrypto'),
                    wp_strip_all_tags(wc_price($price))
                );
                ?>
            </span>
        </label>
        <p class="bc-writing-addon__hint"><?php esc_html_e('Writers draft from your brief; you approve before publish.', 'backlinkcrypto'); ?></p>
    </div>
    <?php
});

add_action('woocommerce_checkout_update_order_review', static function ($post_data): void {
    if (!WC()->session) {
        return;
    }
    parse_str((string) $post_data, $data);
    $on = isset($data['bc_writing_addon']) && (string) $data['bc_writing_addon'] === '1';
    WC()->session->set('bc_writing_addon', $on ? '1' : '0');
});
