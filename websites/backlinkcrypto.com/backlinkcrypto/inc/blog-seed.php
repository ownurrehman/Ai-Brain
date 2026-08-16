<?php
/**
 * Blog reading settings + SEO cornerstone articles (idempotent upgrade).
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BC_BLOG_SEED_OPTION', 'backlinkcrypto_blog_v1');

add_action('init', 'backlinkcrypto_blog_maybe_bootstrap', 55);

function backlinkcrypto_blog_maybe_bootstrap(): void
{
    $state = get_option(BC_BLOG_SEED_OPTION);
    if (is_array($state) && ($state['version'] ?? '') === '1.0.0') {
        return;
    }
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }

    backlinkcrypto_blog_free_blog_slug();
    $home_id = backlinkcrypto_blog_ensure_page('home', 'Home', '');
    $blog_id = backlinkcrypto_blog_ensure_page(
        'blog',
        'Blog',
        'Guides on crypto backlinks, guest posting, DA/DR metrics, and marketplace buying strategy.'
    );

    if ($home_id > 0 && $blog_id > 0) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $home_id);
        update_option('page_for_posts', $blog_id);
        update_post_meta($blog_id, '_bc_seo_title', 'Crypto Backlinks Blog | Guest Posts, DA/DR & Link Building');
        update_post_meta($blog_id, '_bc_seo_description', 'Read Backlink Crypto guides on buying crypto guest posts, evaluating DA/DR, dofollow placements, and building safer link campaigns.');
    }

    update_option('posts_per_page', 9);
    backlinkcrypto_blog_seed_posts();
    flush_rewrite_rules(false);

    update_option(BC_BLOG_SEED_OPTION, [
        'version'    => '1.0.0',
        'configured' => gmdate('c'),
        'home_id'    => $home_id,
        'blog_id'    => $blog_id,
    ]);
}

/**
 * Free the /blog/ path if a product/page/post occupies it.
 */
function backlinkcrypto_blog_free_blog_slug(): void
{
    $hits = get_posts([
        'name'           => 'blog',
        'post_type'      => ['product', 'post', 'page', 'attachment'],
        'post_status'    => 'any',
        'posts_per_page' => 20,
        'suppress_filters' => true,
    ]);

    foreach ($hits as $post) {
        if (!$post instanceof WP_Post) {
            continue;
        }
        // Keep a real Blog page so we can assign it as page_for_posts.
        if ($post->post_type === 'page' && $post->post_name === 'blog') {
            continue;
        }
        wp_update_post([
            'ID'        => (int) $post->ID,
            'post_name' => sanitize_title($post->post_title . '-listing'),
        ]);
    }
}

function backlinkcrypto_blog_ensure_page(string $slug, string $title, string $content): int
{
    $existing = get_page_by_path($slug);
    if ($existing) {
        $id = (int) $existing->ID;
        wp_update_post([
            'ID'           => $id,
            'post_title'   => $title,
            'post_content' => $content,
            'post_status'  => 'publish',
            'post_name'    => $slug,
        ]);
        return $id;
    }

    $id = wp_insert_post([
        'post_type'    => 'page',
        'post_name'    => $slug,
        'post_title'   => $title,
        'post_content' => $content,
        'post_status'  => 'publish',
    ], true);

    return is_wp_error($id) ? 0 : (int) $id;
}

function backlinkcrypto_blog_ensure_category(string $slug, string $name): int
{
    $term = get_term_by('slug', $slug, 'category');
    if ($term && !is_wp_error($term)) {
        return (int) $term->term_id;
    }
    $created = wp_insert_term($name, 'category', [
        'slug'        => $slug,
        'description' => 'Crypto SEO, guest posts, and backlink strategy.',
    ]);
    return is_wp_error($created) ? 0 : (int) $created['term_id'];
}

/**
 * @return list<array{slug:string,title:string,excerpt:string,seo_title:string,seo_desc:string,tags:list<string>,content:string}>
 */
function backlinkcrypto_blog_post_defs(): array
{
    return [
        [
            'slug'      => 'how-to-buy-crypto-backlinks',
            'title'     => 'How to Buy Crypto Backlinks Without Getting Burned',
            'excerpt'   => 'A practical checklist for evaluating crypto guest posts: metrics, niches, dofollow status, and fulfillment before you spend.',
            'seo_title' => 'How to Buy Crypto Backlinks Safely | Backlink Crypto',
            'seo_desc'  => 'Learn how to buy crypto backlinks the smart way — check DA/DR, traffic, niche fit, dofollow labels, and placement tickets before checkout.',
            'tags'      => ['crypto backlinks', 'guest posts', 'link building'],
            'content'   => <<<'HTML'
<p>Buying crypto backlinks can accelerate authority for exchanges, wallets, DeFi apps, and Web3 brands — or waste budget on irrelevant, nofollow, or never-published placements. Use this checklist before you add anything to cart.</p>
<h2>1. Start with niche relevance</h2>
<p>A DR 80 general news site is often weaker for crypto SEO than a DR 45 publisher that actually covers DeFi, NFT, or blockchain news. Search engines reward topical fit. Prefer listings labeled Crypto, DeFi, NFT, or Finance when those match your product.</p>
<h2>2. Read DA, DR, and traffic together</h2>
<p>Domain Authority (Moz) and Domain Rating (Ahrefs) are directional — not guarantees. Pair them with organic traffic estimates. A high DR with near-zero traffic can still be a weak placement. On Backlink Crypto, sort the marketplace table by DR or traffic and set minimum filters before browsing.</p>
<h2>3. Confirm dofollow vs nofollow</h2>
<p>Most buyers want dofollow links for equity. Our rows flag link type clearly. If a listing is nofollow or has special rules, decide consciously — nofollow can still drive referral traffic and brand mentions, but it is a different goal.</p>
<h2>4. Buy tickets, not vague promises</h2>
<p>Each unit you purchase becomes a placement ticket. Five quantities = five articles. After payment confirmation, upload content (or a brief), anchors, and target URLs in My Account → Placements. You get status updates and the live URL when published.</p>
<h2>5. Plan content quality</h2>
<p>Publishers reject thin or spammy drafts. Write for readers first: clear claims, original angles, and natural anchors. Keep one primary URL per placement unless the publisher allows more.</p>
<p><strong>Next step:</strong> open the <a href="/marketplace/">marketplace</a>, filter by niche and language, then ADD the publishers that fit your campaign.</p>
HTML,
        ],
        [
            'slug'      => 'da-vs-dr-crypto-seo',
            'title'     => 'DA vs DR for Crypto SEO: What Actually Matters',
            'excerpt'   => 'Moz DA and Ahrefs DR are not interchangeable. Here’s how to use both when shopping crypto guest posts.',
            'seo_title' => 'DA vs DR Explained for Crypto Link Building | Backlink Crypto',
            'seo_desc'  => 'Understand Domain Authority vs Domain Rating for crypto SEO, and how to combine them with traffic and niche filters when buying backlinks.',
            'tags'      => ['DA', 'DR', 'SEO metrics'],
            'content'   => <<<'HTML'
<p>Marketers often treat DA and DR as the same number. They are not. Both estimate relative link equity, but they come from different crawls and scoring models.</p>
<h2>Domain Authority (DA)</h2>
<p>Moz DA is a 0–100 score predicting how likely a domain is to rank versus others in Moz’s index. It moves slowly and can lag after big link wins or losses.</p>
<h2>Domain Rating (DR)</h2>
<p>Ahrefs DR also uses a 0–100 scale based on referring domains and link graph strength in Ahrefs’ index. Many crypto SEOs lean on DR for prospecting because Ahrefs’ backlink data is widely used in the industry.</p>
<h2>What to optimize for</h2>
<ul>
<li><strong>Relevance first</strong> — crypto/finance topicality beats raw score.</li>
<li><strong>Traffic second</strong> — live organic sessions suggest the domain still earns visibility.</li>
<li><strong>Link attributes third</strong> — dofollow vs nofollow and placement context.</li>
<li><strong>Score as a filter</strong> — use min DA/DR to cut noise, not as the only buying rule.</li>
</ul>
<p>On Backlink Crypto you can filter by both DA and DR in the marketplace table, then sort to compare candidates side by side.</p>
HTML,
        ],
        [
            'slug'      => 'dofollow-guest-posts-crypto',
            'title'     => 'Dofollow Guest Posts in Crypto: What You’re Really Buying',
            'excerpt'   => 'Dofollow is not magic — but it is the default goal for most authority campaigns. Here’s how placements work.',
            'seo_title' => 'Dofollow Crypto Guest Posts Explained | Backlink Crypto',
            'seo_desc'  => 'Learn what dofollow guest posts mean for crypto SEO, how placements are fulfilled, and how to spot link rules before you buy.',
            'tags'      => ['dofollow', 'guest posting'],
            'content'   => <<<'HTML'
<p>A dofollow link passes PageRank-style equity (in search engines’ link graph) when the <code>rel</code> attribute does not include <code>nofollow</code>, <code>sponsored</code>, or <code>ugc</code> in a way that blocks equity. Sponsored disclosures may still appear in content — editorial policies vary by publisher.</p>
<h2>What you purchase on Backlink Crypto</h2>
<p>You buy a sponsored guest post placement on a listed publisher. You choose the site, check out, submit your article (or brief), and we coordinate publication. When it goes live, your placement ticket shows the published URL.</p>
<h2>Before you click ADD</h2>
<ol>
<li>Confirm the row’s dofollow / link-status label.</li>
<li>Match language (English plus other languages where listed).</li>
<li>Check niche tags for crypto or adjacent finance coverage.</li>
<li>Price against expected referral value and campaign budget.</li>
</ol>
<p>If a publisher later requests revisions, the ticket moves to “Needs revision” — update and resubmit without reordering.</p>
HTML,
        ],
        [
            'slug'      => 'crypto-link-building-strategy-2026',
            'title'     => 'Crypto Link Building Strategy for 2026',
            'excerpt'   => 'Blend marketplace placements, digital PR, and entity signals so your brand shows up in classic search and AI answers.',
            'seo_title' => 'Crypto Link Building Strategy 2026 | Backlink Crypto Blog',
            'seo_desc'  => 'A 2026 crypto link building framework: topical relevance, entity mentions, dofollow placements, and measurable marketplace buys.',
            'tags'      => ['link building', 'crypto SEO', 'strategy'],
            'content'   => <<<'HTML'
<p>Crypto SEO in 2026 is competitive: compliance reviews, exchange wars, and AI Overviews all raise the bar for trustworthy mentions. Link building still matters — but random blog spam does not.</p>
<h2>Pillars that work</h2>
<ul>
<li><strong>Entity clarity</strong> — consistent brand, product, and founder names across the web.</li>
<li><strong>Topical clusters</strong> — pages that cover products, docs, comparisons, and education.</li>
<li><strong>Relevant placements</strong> — crypto, DeFi, NFT, and finance publishers with real traffic.</li>
<li><strong>Measurement</strong> — track referring domains, branded search, and assisted conversions — not vanity DR alone.</li>
</ul>
<h2>Where a marketplace fits</h2>
<p>Marketplaces compress prospecting time. Instead of cold outreach for every site, you filter inventory by metrics and language, purchase placement tickets, and fulfill through a clear ops loop. Use them for scalable mid-tier wins while you reserve PR for flagship stories.</p>
<p>Browse curated inventory on the <a href="/marketplace/">Backlink Crypto marketplace</a> and build a monthly placement cadence.</p>
HTML,
        ],
        [
            'slug'      => 'guest-post-brief-template-crypto',
            'title'     => 'Guest Post Brief Template for Crypto Placements',
            'excerpt'   => 'Copy this brief so publishers get title, angle, anchors, disclosures, and assets without endless email threads.',
            'seo_title' => 'Crypto Guest Post Brief Template | Faster Placements',
            'seo_desc'  => 'Use this crypto guest post brief template — title, outline, anchors, disclosures, and assets — to speed up Backlink Crypto placement tickets.',
            'tags'      => ['content brief', 'guest posts'],
            'content'   => <<<'HTML'
<p>Slow placements are usually briefing problems. Use this template when you upload to My Account → Placements.</p>
<h2>Brief template</h2>
<ol>
<li><strong>Preferred title</strong> — clear, non-clickbait, under ~70 characters.</li>
<li><strong>Target URL</strong> — one primary landing page.</li>
<li><strong>Anchor text</strong> — brand or partial-match; avoid exact-match spam.</li>
<li><strong>Angle</strong> — 2–3 sentences on why crypto readers care.</li>
<li><strong>Outline</strong> — H2s only; 800–1,500 words typical unless publisher specifies.</li>
<li><strong>Disclosures</strong> — sponsored / affiliate notes if required.</li>
<li><strong>Assets</strong> — logo, screenshots, author bio, social links.</li>
<li><strong>Restrictions</strong> — competitors to avoid, claims that need legal review.</li>
</ol>
<h2>Upload tips</h2>
<p>You can attach Word/PDF, paste a Google Doc link, or paste the article body. One ticket = one article. Buying quantity 3 of the same site creates three tickets — brief each separately if anchors or URLs differ.</p>
HTML,
        ],
    ];
}

function backlinkcrypto_blog_seed_posts(): void
{
    $cat_id = backlinkcrypto_blog_ensure_category('crypto-seo', 'Crypto SEO');

    // Soft-remove default Hello world.
    $hello = get_page_by_path('hello-world', OBJECT, 'post');
    if ($hello instanceof WP_Post) {
        wp_trash_post((int) $hello->ID);
    }

    foreach (backlinkcrypto_blog_post_defs() as $def) {
        $existing = get_page_by_path($def['slug'], OBJECT, 'post');
        $payload = [
            'post_type'    => 'post',
            'post_name'    => $def['slug'],
            'post_title'   => $def['title'],
            'post_excerpt' => $def['excerpt'],
            'post_content' => $def['content'],
            'post_status'  => 'publish',
            'post_author'  => 1,
        ];

        if ($existing instanceof WP_Post) {
            $id = (int) $existing->ID;
            $payload['ID'] = $id;
            wp_update_post($payload);
        } else {
            $id = wp_insert_post($payload, true);
            if (is_wp_error($id)) {
                continue;
            }
            $id = (int) $id;
        }

        if ($cat_id > 0) {
            wp_set_post_categories($id, [$cat_id]);
        }
        if ($def['tags'] !== []) {
            wp_set_post_tags($id, $def['tags'], false);
        }

        update_post_meta($id, '_bc_seo_title', $def['seo_title']);
        update_post_meta($id, '_bc_seo_description', $def['seo_desc']);
    }
}

function backlinkcrypto_blog_url(): string
{
    $id = (int) get_option('page_for_posts');
    if ($id > 0) {
        $link = get_permalink($id);
        if (is_string($link) && $link !== '') {
            return $link;
        }
    }
    return home_url('/blog/');
}
