<?php
/**
 * AIOSEO + on-page SEO configuration for Backlink Crypto.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BC_SEO_OPTION', 'backlinkcrypto_seo_v1');

add_action('init', 'backlinkcrypto_seo_maybe_bootstrap', 50);
add_action('wp_head', 'backlinkcrypto_seo_extra_head', 1);
add_filter('robots_txt', 'backlinkcrypto_seo_robots_txt', 99, 2);

add_filter('aioseo_title', 'backlinkcrypto_seo_filter_title', 20);
add_filter('aioseo_description', 'backlinkcrypto_seo_filter_description', 20);
add_filter('aioseo_facebook_tags', 'backlinkcrypto_seo_filter_facebook_tags', 20);
add_filter('aioseo_twitter_tags', 'backlinkcrypto_seo_filter_twitter_tags', 20);
add_filter('aioseo_schema_output', 'backlinkcrypto_seo_filter_schema_output', 20);
add_filter('aioseo_disable', 'backlinkcrypto_seo_keep_aioseo_enabled');
add_filter('document_title_parts', 'backlinkcrypto_seo_document_title_parts', 20);

/**
 * One-time / upgrade SEO bootstrap (site identity + AIOSEO globals + cornerstone pages).
 */
function backlinkcrypto_seo_maybe_bootstrap(): void
{
    $current = get_option(BC_SEO_OPTION);
    if (is_array($current) && ($current['version'] ?? '') === '2.0.0') {
        return;
    }

    update_option('blogname', 'Backlink Crypto');
    update_option('blogdescription', 'Buy verified crypto backlinks — filter by DA, DR, traffic & language');
    update_option('blog_public', '1');

    // Pretty permalinks help SEO; do not hard-force if already set.
    if (!get_option('permalink_structure')) {
        update_option('permalink_structure', '/%postname%/');
        flush_rewrite_rules(false);
    }

    backlinkcrypto_seo_configure_aioseo_options();
    backlinkcrypto_seo_ensure_cornerstone_pages();

    update_option(BC_SEO_OPTION, [
        'version'    => '2.0.0',
        'configured' => gmdate('c'),
    ]);
}

function backlinkcrypto_seo_configure_aioseo_options(): void
{
    if (!function_exists('aioseo')) {
        return;
    }

    try {
        $options = aioseo()->options;

        // Global search appearance.
        if (isset($options->searchAppearance->global)) {
            $options->searchAppearance->global->siteTitle = '#site_title | Crypto Backlinks Marketplace';
            $options->searchAppearance->global->metaDescription = 'Buy verified crypto & finance backlinks. Filter sites by DA, DR, traffic, niche, and language — then add to cart instantly.';
            $options->searchAppearance->global->separator = '|';
        }

        if (isset($options->searchAppearance->advanced)) {
            $options->searchAppearance->advanced->useKeywords = false;
            $options->searchAppearance->advanced->runShortcodes = true;
            $options->searchAppearance->advanced->crawlCleanup = true;
        }

        // Homepage.
        if (isset($options->searchAppearance->global->homePage)) {
            // Some versions nest home under global; ignore if missing.
        }

        // Social.
        if (isset($options->social->facebook->general)) {
            $options->social->facebook->general->siteName = 'Backlink Crypto';
            $options->social->facebook->general->defaultImageSourcePosts = 'default';
        }
        if (isset($options->social->facebook->homePage)) {
            $options->social->facebook->homePage->title = 'Backlink Crypto | Buy Verified Crypto Backlinks';
            $options->social->facebook->homePage->description = 'Marketplace of crypto & finance sites with DA, DR, traffic filters and instant checkout.';
        }
        if (isset($options->social->twitter->homePage)) {
            $options->social->twitter->homePage->title = 'Backlink Crypto | Crypto Backlinks Marketplace';
            $options->social->twitter->homePage->description = 'Filter crypto sites by DA, DR & traffic. Verified listings. Fast checkout.';
            $options->social->twitter->general->cardType = 'summary_large_image';
        }

        // Sitemap.
        if (isset($options->sitemap->general)) {
            $options->sitemap->general->enable = true;
            $options->sitemap->general->indexes = true;
        }
        if (isset($options->sitemap->rss)) {
            $options->sitemap->rss->enable = true;
        }

        // Posts (blog).
        if (isset($options->searchAppearance->dynamic->postTypes->post)) {
            $options->searchAppearance->dynamic->postTypes->post->title = '#post_title | Backlink Crypto Blog';
            $options->searchAppearance->dynamic->postTypes->post->metaDescription = '#post_excerpt';
            $options->searchAppearance->dynamic->postTypes->post->show = true;
            $options->searchAppearance->dynamic->postTypes->post->advanced->robotsMeta->default = true;
            $options->searchAppearance->dynamic->postTypes->post->advanced->robotsMeta->index = true;
            $options->searchAppearance->dynamic->postTypes->post->advanced->robotsMeta->follow = true;
        }

        // WooCommerce / Products dynamic templates when available.
        if (isset($options->searchAppearance->dynamic->postTypes->product)) {
            $options->searchAppearance->dynamic->postTypes->product->title = '#post_title | Buy Backlink | #site_title';
            $options->searchAppearance->dynamic->postTypes->product->metaDescription = 'Order a verified backlink placement on #post_title. Check DA, DR, traffic and language — secure checkout on Backlink Crypto.';
            $options->searchAppearance->dynamic->postTypes->product->show = true;
            $options->searchAppearance->dynamic->postTypes->product->advanced->robotsMeta->default = true;
            $options->searchAppearance->dynamic->postTypes->product->advanced->robotsMeta->index = true;
            $options->searchAppearance->dynamic->postTypes->product->advanced->robotsMeta->follow = true;
        }

        if (isset($options->searchAppearance->archives->product)) {
            $options->searchAppearance->archives->product->title = 'Crypto Backlink Marketplace | #site_title';
            $options->searchAppearance->archives->product->metaDescription = 'Browse the full crypto backlink marketplace. Sort and filter by DA, DR, traffic, niche, and language.';
            $options->searchAppearance->archives->product->show = true;
        }

        if (method_exists($options, 'save')) {
            $options->save(true);
        } elseif (method_exists(aioseo()->options, 'save')) {
            aioseo()->options->save(true);
        }
    } catch (Throwable $e) {
        // Keep filters as the always-on fallback.
    }
}

function backlinkcrypto_seo_ensure_cornerstone_pages(): void
{
    $pages = [
        'about' => [
            'title'   => 'About Backlink Crypto',
            'content' => "Backlink Crypto is a marketplace for verified crypto and finance backlinks.\n\nBrowse publisher sites by DA, DR, traffic, niche, and language, then add placements to your cart and checkout in minutes.\n\nWe focus on transparent metrics, vetted listings, and fast fulfillment after payment confirmation.",
            'seo'     => [
                'title' => 'About Backlink Crypto | Crypto Backlinks Marketplace',
                'desc'  => 'Learn how Backlink Crypto helps SEO teams buy verified crypto & finance backlinks with DA/DR/traffic filters and fast checkout.',
            ],
        ],
        'contact' => [
            'title'   => 'Contact Backlink Crypto',
            'content' => "Need a custom package, bulk order, or help with an existing order?\n\nUse the contact form or email contact@backlinkcrypto.com and include your order number when possible.\n\nWe typically respond within 1 business day.",
            'seo'     => [
                'title' => 'Contact Backlink Crypto | Support & Custom Orders',
                'desc'  => 'Contact Backlink Crypto for custom crypto backlink packages, bulk orders, or support with your marketplace order.',
            ],
        ],
        'privacy-policy' => [
            'title'   => 'Privacy Policy',
            'content' => "This Privacy Policy explains how Backlink Crypto collects and uses information when you browse our marketplace, create an order, or contact support.\n\nWe collect order and contact details needed to fulfill backlink placements and process payments. We do not sell personal data.\n\nFor privacy requests, email contact@backlinkcrypto.com.",
            'seo'     => [
                'title' => 'Privacy Policy | Backlink Crypto',
                'desc'  => 'Read the Backlink Crypto privacy policy covering order data, payments, and how to contact us about privacy requests.',
            ],
        ],
        'terms' => [
            'title'   => 'Terms of Service',
            'content' => "By purchasing on Backlink Crypto you agree that listings display estimated SEO metrics (DA/DR/traffic) for guidance only, and fulfillment begins after payment confirmation.\n\nAll sales are final. We do not issue cash refunds. If a placement cannot be published on the purchased site, we reallocate an equal-value slot on another site from inventory. See /policies/ for full fulfillment and reallocation rules.\n\nMetrics can change over time. Contact support for order issues.",
            'seo'     => [
                'title' => 'Terms of Service | Backlink Crypto',
                'desc'  => 'Terms for buying crypto backlinks on Backlink Crypto: no cash refunds, equal-value slot reallocation, metrics guidance, and fulfillment.',
            ],
        ],
    ];

    foreach ($pages as $slug => $page) {
        $existing = get_page_by_path($slug);
        if ($existing) {
            $id = (int) $existing->ID;
            wp_update_post([
                'ID'           => $id,
                'post_title'   => $page['title'],
                'post_content' => $page['content'],
                'post_status'  => 'publish',
            ]);
        } else {
            $id = wp_insert_post([
                'post_type'    => 'page',
                'post_name'    => $slug,
                'post_title'   => $page['title'],
                'post_content' => $page['content'],
                'post_status'  => 'publish',
            ], true);
            if (is_wp_error($id)) {
                continue;
            }
            $id = (int) $id;
        }

        if ($slug === 'privacy-policy') {
            update_option('wp_page_for_privacy_policy', $id);
        }

        update_post_meta($id, '_bc_seo_title', $page['seo']['title']);
        update_post_meta($id, '_bc_seo_description', $page['seo']['desc']);
    }
}

function backlinkcrypto_seo_keep_aioseo_enabled($disabled)
{
    return false;
}

function backlinkcrypto_seo_home_title(): string
{
    return 'Buy Crypto Backlinks | DA DR Traffic Filters | Backlink Crypto';
}

function backlinkcrypto_seo_home_description(): string
{
    $count = 0;
    if (function_exists('wc_get_products')) {
        $count = (int) count(wc_get_products([
            'status' => 'publish',
            'limit'  => -1,
            'return' => 'ids',
        ]));
    }
    if ($count > 0) {
        return sprintf(
            'Browse %d+ verified crypto & finance sites. Filter by DA, DR, traffic, niche, and language — then add backlinks to cart and checkout fast.',
            $count
        );
    }

    return 'Browse verified crypto & finance sites. Filter by DA, DR, traffic, niche, and language — then add backlinks to cart and checkout fast.';
}

function backlinkcrypto_seo_product_title(WC_Product $product): string
{
    $domain = (string) get_post_meta($product->get_id(), '_bc_domain', true);
    $label = $domain !== '' ? $domain : $product->get_name();
    $dr = get_post_meta($product->get_id(), '_bc_dr', true);
    $da = get_post_meta($product->get_id(), '_bc_da', true);

    $bits = [$label, 'Guest Post / Backlink'];
    if ($da !== '' && $da !== null) {
        $bits[] = 'DA ' . (int) $da;
    }
    if ($dr !== '' && $dr !== null) {
        $bits[] = 'DR ' . (int) $dr;
    }
    $bits[] = 'Buy on Backlink Crypto';

    return implode(' | ', $bits);
}

function backlinkcrypto_seo_product_description(WC_Product $product): string
{
    $domain = (string) get_post_meta($product->get_id(), '_bc_domain', true);
    $label = $domain !== '' ? $domain : $product->get_name();
    $dr = get_post_meta($product->get_id(), '_bc_dr', true);
    $da = get_post_meta($product->get_id(), '_bc_da', true);
    $traffic = get_post_meta($product->get_id(), '_bc_traffic', true);
    $niche = (string) (get_post_meta($product->get_id(), '_bc_niche', true) ?: 'Crypto');
    $langs = (string) get_post_meta($product->get_id(), '_bc_languages', true);
    $price = $product->get_price();

    $parts = [
        sprintf('Buy a verified backlink placement on %s.', $label),
    ];
    if ($da !== '' || $dr !== '') {
        $parts[] = sprintf(
            'Metrics: DA %s, DR %s.',
            $da !== '' && $da !== null ? (string) (int) $da : '—',
            $dr !== '' && $dr !== null ? (string) (int) $dr : '—'
        );
    }
    if ($traffic !== '' && $traffic !== null) {
        $parts[] = 'Traffic: ' . number_format((int) $traffic) . '.';
    }
    $parts[] = 'Niche: ' . $niche . '.';
    if ($langs !== '') {
        $parts[] = 'Languages: ' . strtoupper($langs) . '.';
    }
    if ($price !== '' && $price !== null) {
        $parts[] = 'Price from $' . number_format((float) $price, 0) . '. Instant cart checkout on Backlink Crypto.';
    }

    return implode(' ', $parts);
}

function backlinkcrypto_seo_filter_title($title)
{
    if (is_front_page()) {
        return backlinkcrypto_seo_home_title();
    }

    if (is_home()) {
        $custom = get_post_meta((int) get_option('page_for_posts'), '_bc_seo_title', true);
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }
        return 'Crypto Backlinks Blog | Guest Posts, DA/DR & Link Building | Backlink Crypto';
    }

    if (is_singular('post')) {
        $custom = get_post_meta(get_the_ID(), '_bc_seo_title', true);
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }
    }

    if (function_exists('is_product') && is_product()) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            return backlinkcrypto_seo_product_title($product);
        }
    }

    if (function_exists('is_shop') && is_shop()) {
        return 'Crypto Backlink Marketplace | Filter by DA, DR & Traffic | Backlink Crypto';
    }

    if (is_page()) {
        $custom = get_post_meta(get_the_ID(), '_bc_seo_title', true);
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }
    }

    return $title;
}

function backlinkcrypto_seo_filter_description($description)
{
    if (is_front_page()) {
        return backlinkcrypto_seo_home_description();
    }

    if (is_home()) {
        $custom = get_post_meta((int) get_option('page_for_posts'), '_bc_seo_description', true);
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }
        return 'Read Backlink Crypto guides on buying crypto guest posts, evaluating DA/DR, dofollow placements, and building safer link campaigns.';
    }

    if (is_singular('post')) {
        $custom = get_post_meta(get_the_ID(), '_bc_seo_description', true);
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }
        $excerpt = get_the_excerpt();
        if (is_string($excerpt) && $excerpt !== '') {
            return wp_strip_all_tags($excerpt);
        }
    }

    if (function_exists('is_product') && is_product()) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            return backlinkcrypto_seo_product_description($product);
        }
    }

    if (function_exists('is_shop') && is_shop()) {
        return 'Explore the full Backlink Crypto marketplace. Sort and filter publisher sites by DA, DR, traffic, niche, language, and verification status.';
    }

    if (is_page()) {
        $custom = get_post_meta(get_the_ID(), '_bc_seo_description', true);
        if (is_string($custom) && $custom !== '') {
            return $custom;
        }
    }

    return $description;
}

/**
 * @param array<string,string> $tags
 * @return array<string,string>
 */
function backlinkcrypto_seo_filter_facebook_tags($tags)
{
    if (!is_array($tags)) {
        $tags = [];
    }

    if (is_front_page()) {
        $tags['og:title'] = backlinkcrypto_seo_home_title();
        $tags['og:description'] = backlinkcrypto_seo_home_description();
        $tags['og:type'] = 'website';
        $tags['og:url'] = home_url('/');
        $tags['og:site_name'] = 'Backlink Crypto';
    }

    if (is_home()) {
        $tags['og:title'] = (string) backlinkcrypto_seo_filter_title('');
        $tags['og:description'] = (string) backlinkcrypto_seo_filter_description('');
        $tags['og:type'] = 'website';
        $tags['og:url'] = backlinkcrypto_blog_url();
    }

    if (is_singular('post')) {
        $tags['og:title'] = (string) backlinkcrypto_seo_filter_title(get_the_title());
        $tags['og:description'] = (string) backlinkcrypto_seo_filter_description(get_the_excerpt());
        $tags['og:type'] = 'article';
        $tags['og:url'] = get_permalink();
        $tags['article:published_time'] = get_the_date(DATE_W3C);
        $tags['article:modified_time'] = get_the_modified_date(DATE_W3C);
    }

    if (function_exists('is_product') && is_product()) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $tags['og:title'] = backlinkcrypto_seo_product_title($product);
            $tags['og:description'] = backlinkcrypto_seo_product_description($product);
            $tags['og:type'] = 'product';
            $tags['og:url'] = get_permalink($product->get_id());
            $tags['product:price:amount'] = (string) $product->get_price();
            $tags['product:price:currency'] = get_woocommerce_currency();
        }
    }

    return $tags;
}

/**
 * @param array<string,string> $tags
 * @return array<string,string>
 */
function backlinkcrypto_seo_filter_twitter_tags($tags)
{
    if (!is_array($tags)) {
        $tags = [];
    }

    $tags['twitter:card'] = 'summary_large_image';

    if (is_front_page()) {
        $tags['twitter:title'] = backlinkcrypto_seo_home_title();
        $tags['twitter:description'] = backlinkcrypto_seo_home_description();
    }

    if (is_home() || is_singular('post')) {
        $tags['twitter:title'] = (string) backlinkcrypto_seo_filter_title('');
        $tags['twitter:description'] = (string) backlinkcrypto_seo_filter_description('');
    }

    if (function_exists('is_product') && is_product()) {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $tags['twitter:title'] = backlinkcrypto_seo_product_title($product);
            $tags['twitter:description'] = backlinkcrypto_seo_product_description($product);
        }
    }

    return $tags;
}

/**
 * @param array<int,array<string,mixed>> $graphs
 * @return array<int,array<string,mixed>>
 */
function backlinkcrypto_seo_filter_schema_output($graphs)
{
    if (!is_array($graphs)) {
        $graphs = [];
    }

    if (is_front_page()) {
        // Drop polluted default breadcrumbs (e.g. Hello world / Uncategorized).
        $graphs = array_values(array_filter($graphs, static function ($g): bool {
            if (!is_array($g)) {
                return true;
            }
            $type = $g['@type'] ?? '';
            return $type !== 'BreadcrumbList';
        }));

        $graphs[] = [
            '@type'       => 'WebSite',
            '@id'         => home_url('/#website'),
            'url'         => home_url('/'),
            'name'        => 'Backlink Crypto',
            'description' => backlinkcrypto_seo_home_description(),
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => home_url('/?s={search_term_string}'),
                'query-input' => 'required name=search_term_string',
            ],
        ];
        $graphs[] = [
            '@type' => 'Organization',
            '@id'   => home_url('/#organization'),
            'name'  => 'Backlink Crypto',
            'url'   => home_url('/'),
            'email' => 'contact@backlinkcrypto.com',
        ];
        $graphs[] = [
            '@type'           => 'BreadcrumbList',
            '@id'             => home_url('/#breadcrumb'),
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => home_url('/'),
                ],
            ],
        ];
    }

    if (is_home()) {
        $graphs[] = [
            '@type'           => 'CollectionPage',
            '@id'             => backlinkcrypto_blog_url() . '#webpage',
            'url'             => backlinkcrypto_blog_url(),
            'name'            => (string) backlinkcrypto_seo_filter_title(''),
            'description'     => (string) backlinkcrypto_seo_filter_description(''),
            'isPartOf'        => ['@id' => home_url('/#website')],
            'breadcrumb'      => ['@id' => backlinkcrypto_blog_url() . '#breadcrumb'],
        ];
        $graphs[] = [
            '@type'           => 'BreadcrumbList',
            '@id'             => backlinkcrypto_blog_url() . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => home_url('/'),
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => 'Blog',
                    'item'     => backlinkcrypto_blog_url(),
                ],
            ],
        ];
    }

    if (is_singular('post')) {
        $permalink = get_permalink();
        $graphs[] = [
            '@type'            => 'Article',
            '@id'              => $permalink . '#article',
            'headline'         => get_the_title(),
            'datePublished'    => get_the_date(DATE_W3C),
            'dateModified'     => get_the_modified_date(DATE_W3C),
            'mainEntityOfPage' => $permalink,
            'author'           => [
                '@type' => 'Organization',
                'name'  => 'Backlink Crypto',
            ],
            'publisher'        => [
                '@type' => 'Organization',
                'name'  => 'Backlink Crypto',
                'url'   => home_url('/'),
            ],
            'description'      => (string) backlinkcrypto_seo_filter_description(get_the_excerpt()),
        ];
        $graphs[] = [
            '@type'           => 'BreadcrumbList',
            '@id'             => $permalink . '#breadcrumb',
            'itemListElement' => [
                [
                    '@type'    => 'ListItem',
                    'position' => 1,
                    'name'     => 'Home',
                    'item'     => home_url('/'),
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 2,
                    'name'     => 'Blog',
                    'item'     => backlinkcrypto_blog_url(),
                ],
                [
                    '@type'    => 'ListItem',
                    'position' => 3,
                    'name'     => get_the_title(),
                    'item'     => $permalink,
                ],
            ],
        ];
    }

    return $graphs;
}

/**
 * Extra head tags when AIOSEO is missing a piece (robots + marketplace hints).
 */
function backlinkcrypto_seo_extra_head(): void
{
    echo '<link rel="sitemap" type="application/xml" title="Sitemap" href="' . esc_url(home_url('/sitemap.xml')) . '" />' . "\n";

    if (is_front_page() || is_home() || is_singular(['post', 'page', 'product'])) {
        $robots = 'index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1';
        echo '<meta name="robots" content="' . esc_attr($robots) . '" />' . "\n";
    }
}

/**
 * @param string $output
 * @param bool   $public
 */
function backlinkcrypto_seo_robots_txt($output, $public): string
{
    if (!(bool) $public) {
        return (string) $output;
    }

    $lines = [
        'User-agent: *',
        'Allow: /',
        'Disallow: /wp-admin/',
        'Allow: /wp-admin/admin-ajax.php',
        'Disallow: /cart/',
        'Disallow: /checkout/',
        'Disallow: /my-account/',
        'Disallow: /?add-to-cart=',
        '',
        'Sitemap: ' . home_url('/sitemap.xml'),
        'Sitemap: ' . home_url('/sitemap.rss'),
    ];

    return implode("\n", $lines) . "\n";
}

/**
 * @param array<string,string> $parts
 * @return array<string,string>
 */
function backlinkcrypto_seo_document_title_parts(array $parts): array
{
    $title = backlinkcrypto_seo_filter_title($parts['title'] ?? '');
    if (is_string($title) && $title !== '') {
        // Full title strings already include brand — use as sole title part.
        if (is_front_page() || is_home() || is_singular('post') || (function_exists('is_product') && is_product()) || is_page()) {
            return ['title' => $title];
        }
    }
    return $parts;
}
