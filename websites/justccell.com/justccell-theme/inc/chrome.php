<?php
/**
 * Header/footer chrome, customizer, redirects, schema.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_group_anchor(string $title): string
{
    return sanitize_title($title);
}

/**
 * @return list<array{title:string,url:string}>
 */
function justccell_why_links(): array
{
    return [
        ['title' => __('All-New Technology', 'justccell'), 'url' => home_url('/technology/')],
        ['title' => __('Safety', 'justccell'), 'url' => home_url('/safety/')],
        ['title' => __('R&D Capability', 'justccell'), 'url' => home_url('/research/')],
        ['title' => __('Manufacturing Capability', 'justccell'), 'url' => home_url('/manufacture/')],
        ['title' => __('Packaging', 'justccell'), 'url' => home_url('/packaging/')],
        ['title' => __('Laser engraving', 'justccell'), 'url' => home_url('/laser-engraving/')],
        ['title' => __('Location', 'justccell'), 'url' => function_exists('justccell_location_page_url') ? justccell_location_page_url() : home_url('/location/')],
    ];
}

/**
 * Hero SKUs surfaced as cards in the Products mega, per category.
 *
 * @return array<string, list<string>>
 */
function justccell_mega_featured(): array
{
    return [
        'all-in-ones' => ['airone', 'blade', 'voca-pro-max', 'easy-bar', 'eco-star', 'flex', 'flex-2', 'flo', 'gembar'],
        'cartridge'   => ['kera', 'vita', 'th2-evo', 'm6t-evo', 'th2-evomax', 'm6t-evomax'],
        'pod-system'  => ['eazie-pro', 'eazie-pod', 'eazie-pro-3-0', 'eazie-pod-3-0'],
        'battery'     => ['m4', 'palm-se', 'stylo'],
    ];
}

/**
 * Product cards for one Products-mega tab.
 *
 * Menu ACF “Mega product cards” are used first, but only SKUs that belong to
 * this tab’s WooCommerce category. Remaining slots fill from that category
 * (featured flag, curated slugs, then menu_order). Images are the Woo featured image.
 *
 * @param list<int> $product_ids
 * @return list<array{name:string,url:string,image:string,image_id:int}>
 */
function justccell_mega_cards_for_category(string $key, array $product_ids = [], int $limit = 5): array
{
    $limit = max(1, min(8, $limit));
    $cards = [];
    $seen  = [];

    foreach ($product_ids as $pid) {
        $pid = (int) $pid;
        if ($pid < 1 || isset($seen[$pid])) {
            continue;
        }
        if ($key !== '' && function_exists('justccell_product_in_storefront_category')
            && !justccell_product_in_storefront_category($pid, $key)
        ) {
            continue;
        }
        $card = justccell_mega_card_from_product_id($pid);
        if ($card === null) {
            continue;
        }
        $seen[$pid] = true;
        $cards[]    = $card;
        if (count($cards) >= $limit) {
            return $cards;
        }
    }

    // All-In-Ones oil-group cards only when this tab has no category SKUs yet.
    if ($cards === [] && $key === 'all-in-ones') {
        $oil = justccell_mega_oil_group_cards($limit);
        if ($oil !== []) {
            return $oil;
        }
    }

    $pool = justccell_catalog_by_category()[$key] ?? [];
    usort($pool, static function (array $a, array $b): int {
        return ((int) ($a['menu_order'] ?? 0)) <=> ((int) ($b['menu_order'] ?? 0));
    });

    $by_slug = [];
    foreach ($pool as $item) {
        if ((string) ($item['category'] ?? '') !== $key) {
            continue;
        }
        $by_slug[(string) $item['slug']] = $item;
    }

    $picked = [];
    $append = static function (array $item) use ($key, &$picked, $seen, $limit): void {
        if (count($picked) >= $limit) {
            return;
        }
        if ((string) ($item['category'] ?? '') !== $key) {
            return;
        }
        $woo = (int) ($item['woo_id'] ?? 0);
        if ($woo > 0 && isset($seen[$woo])) {
            return;
        }
        $slug = (string) ($item['slug'] ?? '');
        if ($slug === '' || isset($picked[$slug])) {
            return;
        }
        $picked[$slug] = $item;
    };

    foreach ($pool as $item) {
        if (!empty($item['mega_featured'])) {
            $append($item);
        }
    }
    foreach (justccell_mega_featured()[$key] ?? [] as $slug) {
        if (isset($by_slug[$slug])) {
            $append($by_slug[$slug]);
        }
    }
    foreach ($pool as $item) {
        $append($item);
    }

    $need = $limit - count($cards);
    foreach (array_slice($picked, 0, $need, true) as $item) {
        $cards[] = justccell_mega_card_from_catalog_item($item);
    }
    return $cards;
}

/**
 * @param array<string, mixed> $item
 * @return array{name:string,url:string,image:string,image_id:int}
 */
function justccell_mega_card_from_catalog_item(array $item): array
{
    return [
        'name'     => (string) ($item['name'] ?? ''),
        'url'      => justccell_item_url($item),
        'image'    => (string) ($item['image'] ?? ''),
        'image_id' => (int) ($item['image_id'] ?? 0),
    ];
}

/**
 * @return array{name:string,url:string,image:string,image_id:int}|null
 */
function justccell_mega_card_from_product_id(int $pid): ?array
{
    if ($pid < 1 || !function_exists('wc_get_product')) {
        return null;
    }
    $product = wc_get_product($pid);
    if (!$product instanceof WC_Product) {
        return null;
    }
    $thumb = (int) $product->get_image_id();
    $item  = [
        'name'     => $product->get_name(),
        'slug'     => $product->get_slug(),
        'category' => '',
        'image'    => '',
        'image_id' => $thumb,
        'woo_id'   => $pid,
    ];
    return justccell_mega_card_from_catalog_item($item);
}

/**
 * All-In-Ones mega matches the reference: four oil-group cards that jump to
 * listing anchors. Other tabs stay featured SKUs.
 *
 * @return list<array{name:string,url:string,image:string,image_id:int}>
 */
function justccell_mega_oil_group_cards(int $limit = 4): array
{
    $limit = max(1, min(8, $limit));
    $cards = [];
    if (!function_exists('justccell_catalog_groups')) {
        return $cards;
    }
    foreach (justccell_catalog_groups('all-in-ones') as $group) {
        if (!is_array($group)) {
            continue;
        }
        $title = (string) ($group['title'] ?? '');
        if ($title === '') {
            continue;
        }
        $sample = null;
        $rows   = $group['items'] ?? [];
        if (is_array($rows) && $rows !== [] && is_array($rows[0])) {
            $sample = $rows[0];
        }
        if (!is_array($sample)) {
            foreach ((array) ($group['slugs'] ?? []) as $slug) {
                $sample = function_exists('justccell_catalog_item') ? justccell_catalog_item((string) $slug) : null;
                if (is_array($sample)) {
                    break;
                }
            }
        }
        if (!is_array($sample)) {
            continue;
        }
        $card         = justccell_mega_card_from_catalog_item($sample);
        $card['name'] = $title;
        $card['url']  = justccell_category_url('all-in-ones') . '#' . justccell_group_anchor($title);
        $cards[]      = $card;
        if (count($cards) >= $limit) {
            break;
        }
    }
    return $cards;
}

/**
 * @return array<string, array{label:string,url:string,items:list<array{name:string,url:string,image:string,image_id:int}>}>
 */
function justccell_mega_columns(): array
{
    $labels = justccell_product_category_labels();
    $out    = [];
    foreach ($labels as $key => $label) {
        $out[$key] = [
            'label' => $label,
            'url'   => justccell_category_url($key),
            'items' => justccell_mega_cards_for_category($key),
        ];
    }
    return $out;
}

/**
 * @return list<array{title:string,url:string,links:list<array{title:string,url:string}>}>
 */
function justccell_footer_columns(): array
{
    $labels = justccell_product_category_labels();
    $products = [];
    foreach ($labels as $slug => $label) {
        $products[] = ['title' => $label, 'url' => justccell_category_url($slug)];
    }

    return [
        [
            'title' => __('Products', 'justccell'),
            'url'   => justccell_category_url('all-in-ones'),
            'links' => $products,
        ],
        [
            'title' => __('Why Justccell', 'justccell'),
            'url'   => home_url('/technology/'),
            'links' => justccell_why_links(),
        ],
        [
            'title' => __('About Justccell', 'justccell'),
            'url'   => home_url('/about/'),
            'links' => [
                ['title' => __('Corporate culture', 'justccell'), 'url' => home_url('/about/#corporate-culture')],
                ['title' => __('Company introduction', 'justccell'), 'url' => home_url('/about/#company-introduction')],
                ['title' => __('Brand history', 'justccell'), 'url' => home_url('/about/#brand-history')],
                ['title' => __('Customer centricity', 'justccell'), 'url' => home_url('/about/#customer-centricity')],
            ],
        ],
        [
            'title' => __('Solution', 'justccell'),
            'url'   => home_url('/solution/'),
            'links' => [
                ['title' => __('Just CCELL 3.0', 'justccell'), 'url' => function_exists('justccell_bio_page_url') ? justccell_bio_page_url() : home_url('/justccell-3-0/')],
                ['title' => __('Discover', 'justccell'), 'url' => home_url('/discover/')],
                ['title' => __('Elite Terpenes', 'justccell'), 'url' => home_url('/elite-terpenes/')],
                ['title' => __('Packaging', 'justccell'), 'url' => home_url('/packaging/')],
                ['title' => __('Laser engraving', 'justccell'), 'url' => home_url('/laser-engraving/')],
                ['title' => __('Contact', 'justccell'), 'url' => home_url('/contact/')],
            ],
        ],
    ];
}

/**
 * @return list<array{label:string,url:string}>
 */
function justccell_legal_links(): array
{
    return [
        ['label' => __('Privacy', 'justccell'), 'url' => home_url('/privacy-policy/')],
        ['label' => __('Terms', 'justccell'), 'url' => home_url('/terms/')],
        ['label' => __('Cookies', 'justccell'), 'url' => home_url('/cookies/')],
    ];
}

function justccell_legal_name(): string
{
    $name = trim((string) get_theme_mod('justccell_legal_name', ''));
    return $name !== '' ? $name : get_bloginfo('name');
}

function justccell_footer_disclaimer(): string
{
    $fallback = __('Justccell does not produce, distribute or sell any material filled in cartridges and pods. Not for sale to minors.', 'justccell');
    if (function_exists('justccell_option_string')) {
        return justccell_option_string('store_footer_note', $fallback);
    }
    return $fallback;
}

function justccell_developer_name(): string
{
    return defined('JUSTCCELL_DEVELOPER') ? JUSTCCELL_DEVELOPER : 'Rank Ray';
}

function justccell_developer_url(): string
{
    return defined('JUSTCCELL_DEVELOPER_URL') ? JUSTCCELL_DEVELOPER_URL : 'https://rankray.com';
}

function justccell_show_developer_credit(): bool
{
    if (function_exists('justccell_option_bool')) {
        return justccell_option_bool('store_show_developer_credit', true);
    }
    return true;
}

function justccell_inquiry_recipient(): string
{
    $email = sanitize_email((string) get_theme_mod('justccell_inquiry_email', ''));
    if (is_email($email)) {
        return $email;
    }
    $admin = sanitize_email((string) get_option('admin_email'));
    return is_email($admin) ? $admin : '';
}

/**
 * @return list<array{network:string,url:string,label:string}>
 */
function justccell_social_links(): array
{
    $map = [
        'instagram' => __('Instagram', 'justccell'),
        'youtube'   => __('YouTube', 'justccell'),
        'linkedin'  => __('LinkedIn', 'justccell'),
        'facebook'  => __('Facebook', 'justccell'),
        'x'         => __('X', 'justccell'),
    ];
    $defaults = [
        'instagram' => 'https://www.instagram.com/justccell',
    ];
    $out = [];
    foreach ($map as $key => $label) {
        $url = '';
        if (function_exists('justccell_social_option_url')) {
            $url = justccell_social_option_url($key);
        }
        if ($url === '') {
            $url = esc_url_raw((string) get_theme_mod('justccell_social_' . $key, ''));
        }
        if ($url === '' && isset($defaults[$key])) {
            $url = esc_url_raw($defaults[$key]);
        }
        if ($url === '') {
            continue;
        }
        $out[] = [
            'network' => $key,
            'url'     => $url,
            'label'   => $label,
        ];
    }
    return $out;
}

/**
 * Local path aliases that should not 404.
 *
 * @return array<string, string>
 */
function justccell_legacy_redirects(): array
{
    // Catalog-cut 301s live in inc/catalog-redirects.php (priority 7).
    return [];
}

add_action('template_redirect', static function (): void {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    $path = justccell_request_path();
    if (function_exists('justccell_path_without_store')) {
        $path = justccell_path_without_store($path);
    }
    $path = '/' . trim(strtolower($path), '/');
    if ($path === '/') {
        return;
    }
    $map = justccell_legacy_redirects();
    $dest = $map[$path] ?? '';
    if ($dest === '') {
        return;
    }
    wp_safe_redirect(home_url($dest), 301);
    exit;
}, 8);

add_action('customize_register', static function (WP_Customize_Manager $wp_customize): void {
    $wp_customize->add_section('justccell_chrome', [
        'title'       => __('Justccell chrome', 'justccell'),
        'description' => __('Social URLs, inquiry inbox, and legal name. Prefer Justccell → Storefront for Instagram, WhatsApp, and Telegram. Empty fields stay hidden.', 'justccell'),
        'priority'    => 40,
    ]);

    $wp_customize->add_setting('justccell_legal_name', ['default' => '', 'sanitize_callback' => 'sanitize_text_field']);
    $wp_customize->add_control('justccell_legal_name', [
        'label'   => __('Legal company name', 'justccell'),
        'section' => 'justccell_chrome',
        'type'    => 'text',
    ]);

    $wp_customize->add_setting('justccell_inquiry_email', ['default' => '', 'sanitize_callback' => 'sanitize_email']);
    $wp_customize->add_control('justccell_inquiry_email', [
        'label'       => __('Inquiry inbox', 'justccell'),
        'description' => __('Defaults to the WordPress admin email until info@justccell.com is ready.', 'justccell'),
        'section'     => 'justccell_chrome',
        'type'        => 'email',
    ]);

    foreach (
        [
            'instagram' => __('Instagram URL', 'justccell'),
            'youtube'   => __('YouTube URL', 'justccell'),
            'linkedin'  => __('LinkedIn URL', 'justccell'),
            'facebook'  => __('Facebook URL', 'justccell'),
            'x'         => __('X / Twitter URL', 'justccell'),
        ] as $key => $label
    ) {
        $id = 'justccell_social_' . $key;
        $wp_customize->add_setting($id, ['default' => '', 'sanitize_callback' => 'esc_url_raw']);
        $wp_customize->add_control($id, [
            'label'   => $label,
            'section' => 'justccell_chrome',
            'type'    => 'url',
        ]);
    }
});

add_action('wp_head', static function (): void {
    $logo = function_exists('justccell_brand_logo_url') ? justccell_brand_logo_url() : '';
    $data = [
        '@context' => 'https://schema.org',
        '@type'    => 'Organization',
        'name'     => justccell_legal_name(),
        'url'      => home_url('/'),
        'brand'    => [
            '@type' => 'Brand',
            'name'  => get_bloginfo('name'),
        ],
    ];
    if ($logo !== '') {
        $data['logo'] = $logo;
    }
    $email = sanitize_email((string) get_theme_mod('justccell_inquiry_email', ''));
    if ($email !== '') {
        $data['email'] = $email;
    }
    $same = array_column(justccell_social_links(), 'url');
    if ($same !== []) {
        $data['sameAs'] = $same;
    }
    echo '<script type="application/ld+json">' . wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) . '</script>' . "\n";
}, 20);

add_filter('document_title_parts', static function (array $parts): array {
    if (is_front_page()) {
        $parts['title'] = __('Precision hardware for cannabis extracts', 'justccell');
        $parts['site']  = get_bloginfo('name');
    }
    return $parts;
}, 20);
