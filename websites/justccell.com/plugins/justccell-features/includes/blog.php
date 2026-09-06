<?php
/**
 * Discover as the WordPress posts hub. Guides / News / Blogs are post categories.
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
 * @return array<string, string>
 */
function justccell_discover_categories(): array
{
    return [
        'guides' => __('Guides', 'justccell'),
        'news'   => __('News', 'justccell'),
        'blogs'  => __('Blogs', 'justccell'),
    ];
}

/**
 * @return list<string>
 */
function justccell_discover_category_slugs(): array
{
    return array_keys(justccell_discover_categories());
}

function justccell_discover_hero_key(): string
{
    return 'discover/justccell-discover-hero.jpg';
}

function justccell_is_discover_view(): bool
{
    if (function_exists('justccell_is_catalog_clone') && justccell_is_catalog_clone()) {
        return false;
    }
    if ((string) get_query_var('justccell_listing') !== '') {
        return false;
    }
    if (is_home() || is_singular('post') || is_page('discover')) {
        return true;
    }
    if (!is_category()) {
        return false;
    }
    $term = get_queried_object();
    return $term instanceof WP_Term
        && $term->taxonomy === 'category'
        && in_array($term->slug, justccell_discover_category_slugs(), true);
}

function justccell_discover_url(): string
{
    $id = justccell_discover_page_id();
    if ($id > 0) {
        $url = get_permalink($id);
        if (is_string($url) && $url !== '') {
            return $url;
        }
    }
    return home_url('/discover/');
}

function justccell_discover_page_id(): int
{
    if (function_exists('justccell_find_page_by_slug')) {
        $page = justccell_find_page_by_slug('discover');
        if ($page instanceof WP_Post) {
            return (int) $page->ID;
        }
    }
    return (int) get_option('page_for_posts');
}

/**
 * ACF hub chrome for Discover (hero, heading, intro). Posts stay in the grid.
 *
 * @return array{
 *   title:string,
 *   title_tag:string,
 *   lede:string,
 *   intro:string,
 *   image_id:int,
 *   image_key:string,
 *   image_mobile_id:int,
 *   image_mobile_key:string
 * }
 */
function justccell_discover_hub_content(): array
{
    $id      = justccell_discover_page_id();
    $key     = justccell_discover_hero_key();
    $title   = '';
    $lede    = '';
    $intro   = '';
    $tag     = 'h1';
    $image   = 0;
    $mobile  = 0;

    if ($id > 0 && function_exists('get_field')) {
        $title  = trim((string) get_field('discover_title', $id));
        $lede   = trim((string) get_field('discover_lede', $id));
        $intro  = (string) get_field('discover_intro', $id);
        $saved  = (string) get_field('discover_title_tag', $id);
        $tag    = $saved !== '' ? $saved : 'h1';
        $image  = function_exists('justccell_acf_to_attachment_id')
            ? justccell_acf_to_attachment_id(get_field('discover_image', $id))
            : 0;
        $mobile = function_exists('justccell_acf_to_attachment_id')
            ? justccell_acf_to_attachment_id(get_field('discover_image_mobile', $id))
            : 0;
    }

    if ($title === '' && $id > 0) {
        $title = trim((string) get_the_title($id));
    }
    if ($title === '') {
        $title = __('Discover', 'justccell');
    }
    if ($mobile < 1) {
        $mobile = $image;
    }

    $tag = function_exists('justccell_normalize_heading_tag')
        ? justccell_normalize_heading_tag($tag, 'h1')
        : 'h1';

    return [
        'title'            => $title,
        'title_tag'        => $tag,
        'lede'             => $lede,
        'intro'            => $intro,
        'image_id'         => $image,
        'image_key'        => $image > 0 ? '' : $key,
        'image_mobile_id'  => $mobile,
        'image_mobile_key' => $mobile > 0 ? '' : $key,
    ];
}

function justccell_seed_discover_hub_fields(): void
{
    if (!function_exists('update_field') || !function_exists('get_field')) {
        return;
    }
    $id = justccell_discover_page_id();
    if ($id < 1) {
        return;
    }

    if (trim((string) get_field('discover_title', $id)) === '') {
        $title = trim((string) get_the_title($id));
        update_field('discover_title', $title !== '' ? $title : __('Discover', 'justccell'), $id);
    }
    if ((string) get_field('discover_title_tag', $id) === '') {
        update_field('discover_title_tag', 'h1', $id);
    }
    if (function_exists('justccell_acf_set_if_empty')) {
        justccell_acf_set_if_empty('discover_tab_all', __('All', 'justccell'), $id, false);
        justccell_acf_set_if_empty('discover_tab_guides', __('Guides', 'justccell'), $id, false);
        justccell_acf_set_if_empty('discover_tab_news', __('News', 'justccell'), $id, false);
        justccell_acf_set_if_empty('discover_tab_blogs', __('Blogs', 'justccell'), $id, false);
    }

    $has_image = function_exists('justccell_acf_to_attachment_id')
        && justccell_acf_to_attachment_id(get_field('discover_image', $id)) > 0;
    if ($has_image) {
        return;
    }

    $key = justccell_discover_hero_key();
    if (function_exists('justccell_ensure_media_files')) {
        justccell_ensure_media_files([$key]);
    }
    $thumb = function_exists('justccell_media_id') ? justccell_media_id($key) : 0;
    if ($thumb < 1 && function_exists('justccell_sideload_media_file')) {
        $thumb = (int) justccell_sideload_media_file($key, false);
    }
    if ($thumb > 0) {
        update_field('discover_image', $thumb, $id);
        if (justccell_acf_to_attachment_id(get_field('discover_image_mobile', $id)) < 1) {
            update_field('discover_image_mobile', $thumb, $id);
        }
    }
}

/**
 * @return list<array{slug:string,title:string,url:string,on:bool}>
 */
function justccell_discover_tabs(): array
{
    $current = '';
    if (is_category()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term) {
            $current = $term->slug;
        }
    }
    $all_on = $current === '' && (is_home() || is_page('discover'));
    $labels = justccell_discover_tab_labels();
    $tabs   = [
        [
            'slug'  => '',
            'title' => $labels['all'],
            'url'   => justccell_discover_url(),
            'on'    => $all_on,
        ],
    ];
    foreach (justccell_discover_categories() as $slug => $title) {
        $tabs[] = [
            'slug'  => $slug,
            'title' => $labels[$slug] ?? $title,
            'url'   => home_url('/' . $slug . '/'),
            'on'    => $current === $slug,
        ];
    }
    return $tabs;
}

/**
 * @return array{all:string,guides:string,news:string,blogs:string}
 */
function justccell_discover_tab_labels(): array
{
    $defaults = [
        'all'    => __('All', 'justccell'),
        'guides' => __('Guides', 'justccell'),
        'news'   => __('News', 'justccell'),
        'blogs'  => __('Blogs', 'justccell'),
    ];
    $id = justccell_discover_page_id();
    if ($id < 1 || !function_exists('get_field')) {
        return $defaults;
    }
    foreach ($defaults as $key => $label) {
        $saved = trim((string) get_field('discover_tab_' . $key, $id));
        if ($saved !== '') {
            $defaults[$key] = $saved;
        }
    }
    return $defaults;
}

function justccell_discover_crumb_label(): string
{
    if (is_category()) {
        $term = get_queried_object();
        if ($term instanceof WP_Term && $term->name !== '') {
            return $term->name;
        }
    }
    return __('Discover', 'justccell');
}

function justccell_ensure_blog(): void
{
    justccell_ensure_discover_categories();
    justccell_ensure_discover_posts_page();
    justccell_ensure_discover_permalinks();
    justccell_seed_discover_posts();
    justccell_seed_discover_hub_fields();

    $ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '';
    if ($ver !== '' && get_option('justccell_blog_ver') !== $ver) {
        flush_rewrite_rules(false);
        update_option('justccell_blog_ver', $ver);
    }
}

function justccell_ensure_discover_categories(): void
{
    foreach (justccell_discover_categories() as $slug => $name) {
        $existing = get_term_by('slug', $slug, 'category');
        if ($existing instanceof WP_Term) {
            continue;
        }
        $result = wp_insert_term($name, 'category', ['slug' => $slug]);
        if (is_wp_error($result) || !isset($result['term_id'])) {
            continue;
        }
        justccell_assign_term_language((int) $result['term_id'], 'category');
    }
}

function justccell_ensure_discover_posts_page(): void
{
    if (!function_exists('justccell_find_page_by_slug')) {
        return;
    }
    $page = justccell_find_page_by_slug('discover');
    if (!$page instanceof WP_Post) {
        return;
    }
    if ($page->post_status !== 'publish') {
        wp_update_post([
            'ID'          => $page->ID,
            'post_status' => 'publish',
        ]);
    }
    if ((int) get_option('page_for_posts') !== (int) $page->ID) {
        update_option('page_for_posts', (int) $page->ID);
    }
    if (get_option('show_on_front') !== 'page') {
        $front = (int) get_option('page_on_front');
        if ($front > 0 && $front !== (int) $page->ID) {
            update_option('show_on_front', 'page');
        }
    }
}

function justccell_ensure_discover_permalinks(): void
{
    $wanted = '/%category%/%postname%/';
    if (get_option('permalink_structure') === $wanted) {
        return;
    }
    global $wp_rewrite;
    if (isset($wp_rewrite) && $wp_rewrite instanceof WP_Rewrite) {
        $wp_rewrite->set_permalink_structure($wanted);
    } else {
        update_option('permalink_structure', $wanted);
    }
}

/**
 * @param mixed $term_id
 */
function justccell_assign_term_language($term_id, string $taxonomy): void
{
    $id = (int) $term_id;
    if ($id < 1 || !has_action('wpml_set_element_language_details')) {
        return;
    }
    $lang = apply_filters('wpml_default_language', null);
    if (!is_string($lang) || $lang === '') {
        return;
    }
    do_action('wpml_set_element_language_details', [
        'element_id'           => $id,
        'element_type'         => 'tax_' . $taxonomy,
        'trid'                 => false,
        'language_code'        => $lang,
        'source_language_code' => null,
    ]);
}

/**
 * @return list<array{slug:string,title:string,cat:string,date:string,image:string,content:string}>
 */
function justccell_discover_seed_specs(): array
{
    return [
        [
            'slug'    => 'justccell-hardware-for-extract-brands',
            'title'   => 'Justccell hardware for extract brands',
            'cat'     => 'news',
            'date'    => '2025-06-10 09:00:00',
            'image'   => 'discover/justccell-discover-hero.jpg',
            'content' => '<p>Justccell builds empty ceramic hardware for licensed fillers. This note is for brand and operations teams choosing a first hardware line — not a consumer vape guide.</p><p>When you contact us, tell us the oil type, target fill volume, and whether you need child-resistant or laser-ready parts. We scope wholesale pricing from that brief; this site is not a consumer basket.</p>',
        ],
        [
            'slug'    => 'what-are-terpenes-and-why-they-matter',
            'title'   => 'What are terpenes and why they matter for hardware',
            'cat'     => 'blogs',
            'date'    => '2025-03-19 09:00:00',
            'image'   => 'why/justccell-why-technology-hero.jpg',
            'content' => '<p>Terpenes are the aromatic compounds that give an extract its citrus, pine, or floral character. They are also heat-sensitive. A core that runs too hot flattens flavour before the oil is finished.</p><p>Justccell 3.0 heating is built to stay in a lower, tighter band so fillers can keep strain character in the vapour, not in the room. Pair the core with the oil you actually fill — distillate, live resin, or live rosin — rather than one SKU for every viscosity.</p>',
        ],
        [
            'slug'    => 'hardware-that-keeps-strain-character',
            'title'   => 'Hardware that keeps strain character',
            'cat'     => 'blogs',
            'date'    => '2025-02-28 09:00:00',
            'image'   => 'j3/justccell-j3-hero-desktop.jpg',
            'content' => '<p>Strain character is lost in two places: the core temperature, and the airway. Cheap wicks scorch. Oversized chambers leave oil sitting in heat between draws.</p><p>Justccell ceramic cores and matched mouthpieces are specified together so a filling line can hold flavour from first draw to last. Ask for the Vision Box and 3.0 trays if terpene retention is the brief.</p>',
        ],
        [
            'slug'    => 'justccell-3-0-heating-core',
            'title'   => 'Justccell 3.0 heating core',
            'cat'     => 'blogs',
            'date'    => '2025-01-21 09:00:00',
            'image'   => 'j3/justccell-j3-flavor-desktop.jpg',
            'content' => '<p>Justccell 3.0 is the ultra-low-temperature ceramic heating generation. It is aimed at live extracts and botanical terpene blends that burn on older cores.</p><p>Start with the 3.0 product pages, then contact us with the oil you fill. Do not assume a distillate cart will behave the same as a rosin pod.</p>',
        ],
        [
            'slug'    => 'choose-hardware-by-oil-type',
            'title'   => 'How to choose hardware by oil type',
            'cat'     => 'guides',
            'date'    => '2024-12-18 09:00:00',
            'image'   => 'why/justccell-why-research-hero.jpg',
            'content' => '<p>Start with the extract, not the device photo. Distillate, live resin, live rosin, and mixed-oil SKUs need different chambers, seals, and heat.</p><p>Use the Choose hardware by oil page for the tray map, then open a quote with oil type and fill volume. That is faster than sampling every colourway.</p>',
        ],
        [
            'slug'    => 'how-to-charge-a-510-battery',
            'title'   => 'How to charge a 510 battery',
            'cat'     => 'guides',
            'date'    => '2024-12-18 10:00:00',
            'image'   => 'why/justccell-why-safety-hero.jpg',
            'content' => '<p>Most Justccell 510 batteries charge over USB-C. Seat the cable fully, keep the port dry, and stop at a full indicator rather than leaving the pack on a charger overnight.</p><p>If a pack will not take charge, check the cartridge is not shorting the 510 pin, then request a replacement through your account contact — do not open the shell.</p>',
        ],
        [
            'slug'    => 'live-resin-hardware-notes',
            'title'   => 'Live resin hardware notes',
            'cat'     => 'guides',
            'date'    => '2024-12-18 11:00:00',
            'image'   => 'why/justccell-why-manufacture-hero.jpg',
            'content' => '<p>Live resin is thinner and more terpene-forward than distillate. It fights generic carts on both leak and flavour. Flexcell Pro, Voca Pro, Blanc, and Slym are the usual first picks for this viscosity band.</p><p>Warm the oil to the filling spec, do not overfill the chamber, and cap on the same shift. Confirm pilot quantities with our team before a production PO.</p>',
        ],
        [
            'slug'    => 'what-is-a-510-thread',
            'title'   => 'What is a 510 thread?',
            'cat'     => 'guides',
            'date'    => '2024-12-09 09:00:00',
            'image'   => 'contact/justccell-contact-hero-desktop.jpg',
            'content' => '<p>510 is the common screw connection between a cartridge and a battery. Justccell 510 batteries are built for that standard — they are not a proprietary lock-in.</p><p>Match voltage to the oil. Live extracts want a lower band than thick distillate. The 510 thread page lists which batteries sit in this family.</p>',
        ],
        [
            'slug'    => 'justccell-at-industry-events',
            'title'   => 'Justccell at industry events',
            'cat'     => 'news',
            'date'    => '2024-10-07 09:00:00',
            'image'   => 'j3/justccell-j3-reliable-desktop.jpg',
            'content' => '<p>Trade-show meetings are for fillers and brand teams, not consumer sampling. If you are meeting Justccell at an event, bring oil type, target volumes, and any child-resistant or laser brief.</p><p>Follow up through the contact form after the show so the hardware line matches the conversation.</p>',
        ],
    ];
}

function justccell_seed_discover_posts(): void
{
    if (get_option('justccell_editorial_v1') === '1') {
        return;
    }
    if (get_option('justccell_blog_seeded') === '1') {
        return;
    }

    $keys = array_values(array_unique(array_filter(array_map(
        static fn(array $row): string => (string) ($row['image'] ?? ''),
        justccell_discover_seed_specs()
    ))));
    $keys[] = justccell_discover_hero_key();
    if (function_exists('justccell_ensure_media_files')) {
        justccell_ensure_media_files($keys);
    }

    $created = 0;
    foreach (justccell_discover_seed_specs() as $row) {
        $slug = (string) ($row['slug'] ?? '');
        if ($slug === '' || justccell_find_post_by_slug($slug) instanceof WP_Post) {
            continue;
        }
        $cat = get_term_by('slug', (string) ($row['cat'] ?? ''), 'category');
        $id  = wp_insert_post([
            'post_title'   => (string) ($row['title'] ?? ''),
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'post',
            'post_content' => (string) ($row['content'] ?? ''),
            'post_date'    => (string) ($row['date'] ?? ''),
            'post_excerpt' => wp_trim_words(wp_strip_all_tags((string) ($row['content'] ?? '')), 28),
        ], true);
        if (is_wp_error($id) || (int) $id < 1) {
            continue;
        }
        if ($cat instanceof WP_Term) {
            wp_set_post_categories((int) $id, [(int) $cat->term_id], false);
        }
        $image_key = (string) ($row['image'] ?? '');
        if ($image_key !== '' && function_exists('justccell_sideload_media_file')) {
            $thumb = justccell_media_id($image_key);
            if ($thumb < 1) {
                $thumb = justccell_sideload_media_file($image_key, false);
            }
            if ($thumb > 0) {
                set_post_thumbnail((int) $id, $thumb);
            }
        }
        if (function_exists('justccell_assign_default_language')) {
            justccell_assign_default_language((int) $id);
        }
        $created++;
    }

    if ($created > 0 || justccell_find_post_by_slug('justccell-hardware-for-extract-brands') instanceof WP_Post) {
        update_option('justccell_blog_seeded', '1');
    }
}

function justccell_find_post_by_slug(string $slug): ?WP_Post
{
    $found = get_posts([
        'name'             => $slug,
        'post_type'        => 'post',
        'post_status'      => ['publish', 'draft', 'pending', 'private', 'future'],
        'posts_per_page'   => 1,
        'suppress_filters' => true,
    ]);
    return isset($found[0]) && $found[0] instanceof WP_Post ? $found[0] : null;
}

/**
 * @return list<int>
 */
function justccell_discover_category_ids(): array
{
    $ids = [];
    foreach (justccell_discover_category_slugs() as $slug) {
        $term = get_term_by('slug', $slug, 'category');
        if ($term instanceof WP_Term) {
            $ids[] = (int) $term->term_id;
        }
    }
    return $ids;
}

function justccell_discover_listing_query(): WP_Query
{
    if (is_page('discover') && !is_home()) {
        $paged = max(1, (int) get_query_var('paged'), (int) get_query_var('page'));
        $args  = [
            'post_type'           => 'post',
            'post_status'         => 'publish',
            'posts_per_page'      => 9,
            'paged'               => $paged,
            'ignore_sticky_posts' => true,
        ];
        $cats = justccell_discover_category_ids();
        if ($cats !== []) {
            $args['category__in'] = $cats;
        }
        return new WP_Query($args);
    }
    global $wp_query;
    return $wp_query instanceof WP_Query ? $wp_query : new WP_Query();
}

function justccell_discover_pagination(WP_Query $query): void
{
    $total = (int) $query->max_num_pages;
    if ($total < 2) {
        return;
    }
    $current = max(1, (int) $query->get('paged'), (int) get_query_var('paged'), (int) get_query_var('page'));
    $links   = paginate_links([
        'total'     => $total,
        'current'   => $current,
        'mid_size'  => 3,
        'end_size'  => 1,
        'prev_text' => '‹',
        'next_text' => '›',
        'type'      => 'array',
    ]);
    if (!is_array($links) || $links === []) {
        return;
    }
    echo '<nav class="d-paging" aria-label="' . esc_attr__('Posts', 'justccell') . '">';
    foreach ($links as $link) {
        echo $link; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
    }
    echo '</nav>';
}

add_action('init', static function (): void {
    $slugs = implode('|', array_map('preg_quote', justccell_discover_category_slugs()));
    add_rewrite_rule(
        '^(' . $slugs . ')/page/([0-9]+)/?$',
        'index.php?category_name=$matches[1]&paged=$matches[2]',
        'top'
    );
    add_rewrite_rule(
        '^(' . $slugs . ')/?$',
        'index.php?category_name=$matches[1]',
        'top'
    );
}, 5);

add_filter('term_link', static function (string $url, $term, string $taxonomy): string {
    if ($taxonomy !== 'category' || !$term instanceof WP_Term) {
        return $url;
    }
    if (!in_array($term->slug, justccell_discover_category_slugs(), true)) {
        return $url;
    }
    return home_url('/' . $term->slug . '/');
}, 10, 3);

add_action('pre_get_posts', static function (WP_Query $query): void {
    if (is_admin() || !$query->is_main_query()) {
        return;
    }
    if ((string) $query->get('justccell_listing') !== '') {
        return;
    }
    $cat = (string) $query->get('category_name');
    $is_discover_cat = $cat !== '' && in_array($cat, justccell_discover_category_slugs(), true);
    if ($query->is_home() || $is_discover_cat) {
        $query->set('post_type', 'post');
        $query->set('posts_per_page', 9);
        $query->set('ignore_sticky_posts', true);
        if ($query->is_home()) {
            $cats = justccell_discover_category_ids();
            if ($cats !== []) {
                $query->set('category__in', $cats);
            }
        }
    }
});

add_action('template_redirect', static function (): void {
    if (!is_category()) {
        return;
    }
    $term = get_queried_object();
    if (!$term instanceof WP_Term || !in_array($term->slug, justccell_discover_category_slugs(), true)) {
        return;
    }
    $path = (string) parse_url((string) ($_SERVER['REQUEST_URI'] ?? ''), PHP_URL_PATH);
    if (!str_contains($path, '/category/')) {
        return;
    }
    $target = home_url('/' . $term->slug . '/');
    $paged  = (int) get_query_var('paged');
    if ($paged > 1) {
        $target = home_url('/' . $term->slug . '/page/' . $paged . '/');
    }
    wp_safe_redirect($target, 301);
    exit;
});

add_filter('body_class', static function ($classes): array {
    if (!is_array($classes)) {
        $classes = [];
    }
    if (justccell_is_discover_view()) {
        $classes[] = 'd-clone';
    }
    return $classes;
});

add_filter('use_block_editor_for_post', static function ($use, $post) {
    if (!$post instanceof WP_Post) {
        return $use;
    }
    if (justccell_discover_page_id() > 0 && (int) $post->ID === justccell_discover_page_id()) {
        return false;
    }
    return $use;
}, 10, 2);
