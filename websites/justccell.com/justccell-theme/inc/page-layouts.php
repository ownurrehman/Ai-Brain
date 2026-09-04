<?php
/**
 * Page layout templates so cloned pages keep ACF fields without matching the original slug.
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
 * @return array<string, string> kind => theme-relative template path
 */
function justccell_page_layout_templates(): array
{
    return [
        'home'     => 'page-templates/justccell-home.php',
        'contact'  => 'page-templates/justccell-contact.php',
        'about'    => 'page-templates/justccell-about.php',
        'why'      => 'page-templates/justccell-why.php',
        'bio'      => 'page-templates/justccell-bio.php',
        'brand'    => 'page-templates/justccell-brand.php',
        'coming-soon' => 'page-templates/justccell-coming-soon.php',
        'listing'  => 'page-templates/justccell-listing.php',
        'discover' => 'page-templates/justccell-discover.php',
        'location' => 'page-templates/justccell-location.php',
        'legal'    => 'page-templates/justccell-legal.php',
    ];
}

/**
 * @return array<string, string> template path => kind
 */
function justccell_page_layout_template_kinds(): array
{
    $map = array_flip(justccell_page_layout_templates());
    $map['page-templates/template-catalog.php'] = 'listing';
    $map['page-templates/template-flexible.php'] = 'flexible';
    return $map;
}

function justccell_page_layout_from_template(string $template): string
{
    $kinds = justccell_page_layout_template_kinds();
    return $kinds[$template] ?? '';
}

function justccell_page_layout_from_slug(string $slug, int $post_id = 0): string
{
    if ($post_id > 0 && $post_id === (int) get_option('page_on_front')) {
        return 'home';
    }
    if ($slug === 'home') {
        return 'home';
    }
    if ($slug === 'contact') {
        return 'contact';
    }
    if ($slug === 'about') {
        return 'about';
    }
    // Bio / Justccell 3.0 — any historical or renamed slug; layout template is the real source of truth.
    if (in_array($slug, ['justccell-3-0', 'ccell-3-0', 'justccell-3.0', 'ccell-3.0'], true)) {
        return 'bio';
    }
    if ($slug === 'discover') {
        return 'discover';
    }
    if (function_exists('justccell_is_why_page_slug') && justccell_is_why_page_slug($slug)) {
        return 'why';
    }
    if (function_exists('justccell_is_location_page_slug') && justccell_is_location_page_slug($slug)) {
        return 'location';
    }
    if (function_exists('justccell_is_legal_page_slug') && justccell_is_legal_page_slug($slug)) {
        return 'legal';
    }
    if (function_exists('justccell_generic_brand_page_slugs') && in_array($slug, justccell_generic_brand_page_slugs(), true)) {
        return 'brand';
    }
    if (function_exists('justccell_product_category_labels') && array_key_exists($slug, justccell_product_category_labels())) {
        return 'listing';
    }
    return '';
}

function justccell_page_layout_kind(int $post_id): string
{
    if ($post_id < 1) {
        return '';
    }
    $template = (string) get_page_template_slug($post_id);
    if ($template !== '') {
        $kind = justccell_page_layout_from_template($template);
        if ($kind !== '' && $kind !== 'flexible') {
            return $kind;
        }
    }
    $slug = (string) get_post_field('post_name', $post_id);
    return justccell_page_layout_from_slug($slug, $post_id);
}

function justccell_page_layout_matches_slug(int $post_id, string $expected): bool
{
    $expected = sanitize_title($expected);
    if ($expected === '' || $post_id < 1) {
        return false;
    }
    $kind = justccell_page_layout_kind($post_id);
    if ($kind === 'home' && $expected === 'home') {
        return true;
    }
    if ($kind === 'contact' && $expected === 'contact') {
        return true;
    }
    if ($kind === 'about' && $expected === 'about') {
        return true;
    }
    if ($kind === 'bio' && in_array($expected, ['justccell-3-0', 'ccell-3-0', 'justccell-3.0', 'ccell-3.0'], true)) {
        return true;
    }
    if ($kind === 'bio' && justccell_page_layout_from_slug($expected) === 'bio') {
        return true;
    }
    if ($kind === 'discover' && $expected === 'discover') {
        return true;
    }
    if ($kind === 'why' && function_exists('justccell_is_why_page_slug') && justccell_is_why_page_slug($expected)) {
        return true;
    }
    if ($kind === 'location' && function_exists('justccell_is_location_page_slug') && justccell_is_location_page_slug($expected)) {
        return true;
    }
    if ($kind === 'legal' && function_exists('justccell_is_legal_page_slug') && justccell_is_legal_page_slug($expected)) {
        return true;
    }
    if ($kind === 'brand' && function_exists('justccell_generic_brand_page_slugs') && in_array($expected, justccell_generic_brand_page_slugs(), true)) {
        return true;
    }
    if ($kind === 'listing' && function_exists('justccell_product_category_labels') && array_key_exists($expected, justccell_product_category_labels())) {
        return true;
    }
    $slug = (string) get_post_field('post_name', $post_id);
    return $slug !== '' && $slug === $expected;
}

function justccell_home_content_page_id(): int
{
    $id = (int) get_queried_object_id();
    if ($id > 0 && justccell_page_layout_kind($id) === 'home') {
        return $id;
    }
    return (int) get_option('page_on_front');
}

/**
 * True when this page (or the current query) uses the Justccell 3.0 / bio layout.
 */
function justccell_is_bio_page(?int $post_id = null): bool
{
    $post_id = $post_id ?? (int) get_queried_object_id();
    if ($post_id < 1) {
        return is_page() && justccell_page_layout_kind((int) get_queried_object_id()) === 'bio';
    }
    return justccell_page_layout_kind($post_id) === 'bio';
}

/**
 * Canonical Justccell 3.0 page — by layout template, not by slug.
 */
function justccell_bio_page(): ?WP_Post
{
    static $cached = false;
    static $page = null;
    if ($cached) {
        return $page instanceof WP_Post ? $page : null;
    }
    $cached = true;

    $ids = get_posts([
        'post_type'      => 'page',
        'post_status'    => 'publish',
        'posts_per_page' => 20,
        'fields'         => 'ids',
        'no_found_rows'  => true,
        'meta_key'       => '_wp_page_template',
        'meta_value'     => 'page-templates/justccell-bio.php',
        'orderby'        => 'ID',
        'order'          => 'ASC',
    ]);
    foreach ($ids as $id) {
        $post = get_post((int) $id);
        if (!$post instanceof WP_Post) {
            continue;
        }
        // Prefer the canonical public slug when duplicates still use the bio template.
        if ($post->post_name === justccell_bio_canonical_slug()) {
            $page = $post;
            return $page;
        }
        if ($page === null) {
            $page = $post;
        }
    }
    if ($page instanceof WP_Post) {
        return $page;
    }

    // Fallback: canonical first, then historical aliases.
    foreach ([justccell_bio_canonical_slug(), 'ccell-3-0', 'justccell-3.0', 'ccell-3.0'] as $slug) {
        $found = function_exists('justccell_find_page_by_slug')
            ? justccell_find_page_by_slug($slug)
            : get_page_by_path($slug);
        if ($found instanceof WP_Post) {
            $page = $found;
            return $page;
        }
    }
    return null;
}

function justccell_bio_canonical_slug(): string
{
    return 'justccell-3-0';
}

function justccell_bio_canonical_title(): string
{
    return __('Just CCELL 3.0', 'justccell');
}

/**
 * Rename /ccell-3-0/ (and dotted aliases) to /justccell-3-0/ — never reverse.
 */
function justccell_canonicalize_bio_page_slug(): void
{
    if (get_option('justccell_bio_slug_justccell_3_0') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug')) {
        return;
    }

    $canonical = justccell_bio_canonical_slug();
    $title     = justccell_bio_canonical_title();
    $live      = justccell_find_page_by_slug($canonical);
    $legacy    = null;
    foreach (['ccell-3-0', 'ccell-3.0', 'justccell-3.0'] as $old_slug) {
        $found = justccell_find_page_by_slug($old_slug);
        if ($found instanceof WP_Post) {
            $legacy = $found;
            break;
        }
    }

    if ($live instanceof WP_Post) {
        $update = ['ID' => (int) $live->ID];
        if ($live->post_status !== 'publish') {
            $update['post_status'] = 'publish';
        }
        if ($live->post_name !== $canonical) {
            $update['post_name'] = $canonical;
        }
        $current_title = trim((string) $live->post_title);
        if (
            $current_title === ''
            || preg_match('/^ccell\s*3\.0$/i', $current_title) === 1
            || preg_match('/^justccell\s*3\.0$/i', $current_title) === 1
            || preg_match('/^cc\s*ell\s*3\.0$/i', $current_title) === 1
        ) {
            $update['post_title'] = $title;
        }
        if (count($update) > 1) {
            wp_update_post($update);
        }
        if (
            $legacy instanceof WP_Post
            && (int) $legacy->ID !== (int) $live->ID
            && $legacy->post_status !== 'trash'
        ) {
            wp_update_post([
                'ID'          => (int) $legacy->ID,
                'post_status' => 'draft',
                'post_name'   => 'ccell-3-0-legacy-' . (int) $legacy->ID,
            ]);
        }
    } elseif ($legacy instanceof WP_Post) {
        wp_update_post([
            'ID'          => (int) $legacy->ID,
            'post_status' => 'publish',
            'post_name'   => $canonical,
            'post_title'  => (
                preg_match('/ccell\s*3\.0/i', (string) $legacy->post_title) === 1
                || trim((string) $legacy->post_title) === ''
            ) ? $title : (string) $legacy->post_title,
        ]);
    }

    update_option('justccell_bio_slug_justccell_3_0', '1', false);
    delete_option('justccell_rewrite_ver');
}

add_action('init', 'justccell_canonicalize_bio_page_slug', 22);

function justccell_bio_page_id(): int
{
    $page = justccell_bio_page();
    return $page instanceof WP_Post ? (int) $page->ID : 0;
}

function justccell_bio_page_url(string $fragment = ''): string
{
    $id = justccell_bio_page_id();
    if ($id > 0) {
        $url = get_permalink($id);
        if (is_string($url) && $url !== '') {
            return $fragment !== '' ? trailingslashit($url) . '#' . ltrim($fragment, '#') : $url;
        }
    }
    $path = '/' . justccell_bio_canonical_slug() . '/';
    return $fragment !== '' ? home_url($path . '#' . ltrim($fragment, '#')) : home_url($path);
}

function justccell_assign_page_layout(int $post_id, string $kind): void
{
    $templates = justccell_page_layout_templates();
    if ($post_id < 1 || !isset($templates[$kind])) {
        return;
    }
    update_post_meta($post_id, '_wp_page_template', $templates[$kind]);
}

function justccell_ensure_page_layouts(): void
{
    if (get_option('justccell_page_layouts_ver') === JUSTCCELL_VERSION) {
        return;
    }
    $pages = get_posts([
        'post_type'      => 'page',
        'post_status'    => ['publish', 'draft', 'private'],
        'posts_per_page' => 200,
        'fields'         => 'ids',
        'no_found_rows'  => true,
    ]);
    foreach ($pages as $id) {
        $id = (int) $id;
        $kind = justccell_page_layout_from_slug((string) get_post_field('post_name', $id), $id);
        if ($kind === '') {
            continue;
        }
        $current = justccell_page_layout_from_template((string) get_page_template_slug($id));
        if ($current === 'coming-soon') {
            continue;
        }
        if ($current === $kind) {
            continue;
        }
        justccell_assign_page_layout($id, $kind);
    }
    update_option('justccell_page_layouts_ver', JUSTCCELL_VERSION, false);
}

add_action('admin_init', 'justccell_ensure_page_layouts');

/**
 * @param array<string, string> $actions
 * @return array<string, string>
 */
function justccell_page_row_actions(array $actions, WP_Post $post): array
{
    if ($post->post_type !== 'page' || !current_user_can('edit_pages')) {
        return $actions;
    }
    $url = wp_nonce_url(
        admin_url('admin.php?action=justccell_duplicate_page&post=' . $post->ID),
        'justccell_duplicate_page_' . $post->ID
    );
    $actions['justccell_duplicate'] = '<a href="' . esc_url($url) . '">' . esc_html__('Duplicate', 'justccell') . '</a>';
    return $actions;
}

add_filter('page_row_actions', 'justccell_page_row_actions', 10, 2);

function justccell_duplicate_page_admin(): void
{
    $id = isset($_GET['post']) ? (int) $_GET['post'] : 0;
    if ($id < 1 || !current_user_can('edit_pages')) {
        wp_die(esc_html__('You cannot duplicate this page.', 'justccell'));
    }
    check_admin_referer('justccell_duplicate_page_' . $id);
    $source = get_post($id);
    if (!$source instanceof WP_Post || $source->post_type !== 'page') {
        wp_die(esc_html__('Page not found.', 'justccell'));
    }

    $new_id = wp_insert_post([
        'post_type'    => 'page',
        'post_status'  => 'draft',
        'post_title'   => $source->post_title . ' (Copy)',
        'post_content' => $source->post_content,
        'post_excerpt' => $source->post_excerpt,
        'post_parent'  => (int) $source->post_parent,
        'post_author'  => get_current_user_id(),
        'menu_order'   => (int) $source->menu_order,
        'comment_status' => $source->comment_status,
        'ping_status'    => $source->ping_status,
    ], true);
    if (is_wp_error($new_id) || $new_id < 1) {
        wp_die(esc_html__('Could not duplicate this page.', 'justccell'));
    }

    $skip = ['_edit_lock' => true, '_edit_last' => true];
    $meta = get_post_meta($id);
    if (is_array($meta)) {
        foreach ($meta as $key => $values) {
            if (isset($skip[$key]) || !is_array($values)) {
                continue;
            }
            foreach ($values as $value) {
                add_post_meta((int) $new_id, (string) $key, maybe_unserialize($value));
            }
        }
    }

    $taxonomies = get_object_taxonomies('page');
    foreach ($taxonomies as $taxonomy) {
        $terms = wp_get_object_terms($id, $taxonomy, ['fields' => 'ids']);
        if (is_array($terms) && $terms !== []) {
            wp_set_object_terms((int) $new_id, array_map('intval', $terms), $taxonomy, false);
        }
    }

    wp_safe_redirect(get_edit_post_link((int) $new_id, 'raw') ?: admin_url('edit.php?post_type=page'));
    exit;
}

add_action('admin_action_justccell_duplicate_page', 'justccell_duplicate_page_admin');

function justccell_render_page_layout(string $kind): void
{
    switch ($kind) {
        case 'home':
            get_header();
            $landing = function_exists('justccell_current_store_landing') ? justccell_current_store_landing() : null;
            if (is_array($landing) && !empty($landing['enabled']) && is_front_page()) {
                get_template_part('template-parts/home/store-landing', null, ['landing' => $landing]);
            } else {
                get_template_part('template-parts/home/clone');
            }
            get_footer();
            return;
        case 'contact':
            get_header();
            get_template_part('template-parts/page/contact');
            get_footer();
            return;
        case 'about':
            get_header();
            get_template_part('template-parts/page/brand', 'about');
            get_footer();
            return;
        case 'why':
            get_header();
            get_template_part('template-parts/page/brand', 'why');
            get_footer();
            return;
        case 'bio':
            get_header();
            get_template_part('template-parts/page/brand', 'bio-heating');
            get_footer();
            return;
        case 'location':
            get_header();
            get_template_part('template-parts/page/brand', 'locations');
            get_footer();
            return;
        case 'brand':
            get_header();
            get_template_part('template-parts/page/brand');
            get_footer();
            return;
        case 'coming-soon':
            get_header();
            get_template_part('template-parts/page/brand', 'coming-soon');
            get_footer();
            return;
        case 'discover':
            get_header();
            get_template_part('template-parts/discover/archive');
            get_footer();
            return;
        case 'listing':
            $slug = (string) get_post_field('post_name', get_the_ID());
            if (function_exists('justccell_product_category_labels') && array_key_exists($slug, justccell_product_category_labels())) {
                set_query_var('justccell_listing', $slug);
                include JUSTCCELL_DIR . '/catalog-clone.php';
                return;
            }
            get_header();
            echo '<main class="container"><p>';
            esc_html_e('This catalog template is for All-In-Ones, Cartridges, Pod Systems, and 510 Batteries. Keep those four URLs; do not clone them for extra landings.', 'justccell');
            echo '</p></main>';
            get_footer();
            return;
        case 'legal':
            get_header();
            while (have_posts()) {
                the_post();
                ?>
                <article <?php post_class('page-article container page-article--legal'); ?>>
                    <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--page'); ?>
                    <header class="page-article__header">
                        <h1 class="page-article__title"><?php the_title(); ?></h1>
                    </header>
                    <div class="page-article__content entry-content">
                        <?php the_content(); ?>
                    </div>
                </article>
                <?php
            }
            get_footer();
            return;
        case 'flexible':
            break;
        default:
            $never = $kind;
            unset($never);
            break;
    }

    get_header();
    while (have_posts()) {
        the_post();
        ?>
        <article <?php post_class('page-article container'); ?>>
            <?php justccell_the_breadcrumbs('jc-crumbs jc-crumbs--page'); ?>
            <header class="page-article__header">
                <h1 class="page-article__title"><?php the_title(); ?></h1>
            </header>
            <div class="page-article__content entry-content">
                <?php the_content(); ?>
            </div>
            <?php
            if (function_exists('justccell_render_flexible_sections')) {
                justccell_render_flexible_sections();
            }
            ?>
        </article>
        <?php
    }
    get_footer();
}
