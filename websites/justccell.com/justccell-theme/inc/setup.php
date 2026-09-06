<?php
/**
 * Theme supports, menus, permalinks.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('after_setup_theme', static function (): void {
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('custom-logo', [
        'height'      => 48,
        'width'       => 180,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    add_theme_support('html5', [
        'search-form',
        'gallery',
        'caption',
        'style',
        'script',
    ]);
    add_theme_support('align-wide');
    add_theme_support('responsive-embeds');
    add_theme_support('rank-math-breadcrumbs');

    register_nav_menus([
        'primary'       => __('Primary (header)', 'justccell'),
        'footer_top'    => __('Footer Top', 'justccell'),
        'footer_bottom' => __('Footer Bottom', 'justccell'),
        'footer_last'   => __('Footer Last', 'justccell'),
        // Legacy slugs — migrated to the locations above on first load.
        'footer'        => __('Footer (legacy)', 'justccell'),
        'legal'         => __('Legal (legacy)', 'justccell'),
    ]);

    add_image_size('justccell-card', 720, 720, true);
    add_image_size('justccell-discover', 840, 520, true);
});

add_filter('body_class', static function ($classes): array {
    if (!is_array($classes)) {
        $classes = [];
    }
    $classes[] = 'rankray-built';
    return $classes;
});

add_action('wp_head', static function (): void {
    $name = defined('JUSTCCELL_DEVELOPER') ? JUSTCCELL_DEVELOPER : 'Rank Ray';
    $url  = defined('JUSTCCELL_DEVELOPER_URL') ? JUSTCCELL_DEVELOPER_URL : 'https://rankray.com';
    printf(
        "<!-- Justccell theme %s · developed by %s · %s -->\n",
        defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '',
        esc_html($name),
        esc_url($url)
    );
    printf(
        '<link rel="author" href="%s" title="%s">' . "\n",
        esc_url($url),
        esc_attr($name)
    );
}, 1);

add_action('after_switch_theme', 'justccell_seed_site');
add_action('init', static function (): void {
    if (wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    if (get_option('justccell_seeded') === '1') {
        justccell_ensure_core_pages();
        if (function_exists('justccell_ensure_blog')) {
            justccell_ensure_blog();
        }
        return;
    }
    justccell_seed_site();
}, 30);

/**
 * Cookie policy says no pixels. Rank Math “Install Analytics Code” still prints GA4.
 * Keep WooCommerce Payments; this only strips gtag until a consent banner exists.
 */
add_action('wp_enqueue_scripts', static function (): void {
    wp_dequeue_script('google_gtagjs');
    wp_deregister_script('google_gtagjs');
    wp_dequeue_script('google-tag-manager');
}, PHP_INT_MAX);

add_action('wp_print_scripts', static function (): void {
    wp_dequeue_script('google_gtagjs');
    wp_deregister_script('google_gtagjs');
}, PHP_INT_MAX);

add_filter('rank_math/analytics/gtag', static function ($data) {
    unset($data);
    return false;
});

add_filter('script_loader_tag', static function (string $tag, string $handle): string {
    if ($handle === 'google_gtagjs' || $handle === 'google-tag-manager' || str_contains($tag, 'G-JV1T79ZNB6')) {
        return '';
    }
    return $tag;
}, 100, 2);

add_action('wp_head', static function (): void {
    ob_start();
}, 0);

add_action('wp_head', static function (): void {
    $html = ob_get_clean();
    if (!is_string($html) || $html === '') {
        return;
    }
    $html = preg_replace('#<script\b[^>]*\bid=["\']google_gtagjs[^"\']*["\'][^>]*>.*?</script>#is', '', $html) ?? $html;
    $html = preg_replace('#<script\b[^>]*src=["\'][^"\']*googletagmanager[^"\']*["\'][^>]*>.*?</script>#is', '', $html) ?? $html;
    echo $html;
}, PHP_INT_MAX);

/**
 * @param mixed $post_id
 */
function justccell_assign_default_language($post_id): void
{
    $id = (int) $post_id;
    if ($id < 1 || !has_action('wpml_set_element_language_details')) {
        return;
    }
    $lang = apply_filters('wpml_default_language', null);
    $lang = is_string($lang) && $lang !== '' ? $lang : 'en';
    $type = apply_filters('wpml_element_type', 'page');
    do_action('wpml_set_element_language_details', [
        'element_id'           => $id,
        'element_type'         => is_string($type) && $type !== '' ? $type : 'post_page',
        'trid'                 => false,
        'language_code'        => $lang,
        'source_language_code' => null,
    ]);
}

function justccell_seed_site(): void
{
    if (get_option('permalink_structure') !== '/%category%/%postname%/') {
        global $wp_rewrite;
        $wp_rewrite->set_permalink_structure('/%category%/%postname%/');
        $wp_rewrite->flush_rules(true);
    }

    if (get_option('blogname') === 'justccell.com') {
        update_option('blogname', 'Justccell');
    }

    $cats = [
        'all-in-ones' => __('All-In-Ones', 'justccell'),
        'cartridge'   => __('Cartridges', 'justccell'),
        'pod-system'  => __('Pod Systems', 'justccell'),
        'battery'     => __('510 Batteries', 'justccell'),
    ];

    if (taxonomy_exists('product_cat')) {
        foreach ($cats as $slug => $name) {
            if (!term_exists($slug, 'product_cat')) {
                wp_insert_term($name, 'product_cat', ['slug' => $slug]);
            }
        }
    }

    $created_ids = justccell_ensure_core_pages();

    justccell_seed_menus($created_ids);
    if (function_exists('justccell_ensure_blog')) {
        justccell_ensure_blog();
    }
    update_option('justccell_seeded', '1');
}

/**
 * @return array<string, int>
 */
function justccell_ensure_core_pages(): array
{
    $pages = [
        'contact'        => __('Contact us', 'justccell'),
        'about'          => __('About Justccell', 'justccell'),
        'technology'     => __('Why Justccell', 'justccell'),
        'solution'       => __('Solution', 'justccell'),
        'safety'         => __('Safety', 'justccell'),
        'research'       => __('Research', 'justccell'),
        'manufacture'    => __('Manufacture', 'justccell'),
        'privacy-policy' => __('Privacy policy', 'justccell'),
        'cell-3-0'       => __('Just CCELL 3.0', 'justccell'),
        'discover'       => __('Discover', 'justccell'),
        'terms'          => __('Terms of use', 'justccell'),
        'cookies'        => __('Cookie policy', 'justccell'),
        'choose-hardware'=> __('Choose hardware by oil', 'justccell'),
        'oil-types'      => __('Oil types', 'justccell'),
        '510-thread'     => __('What is a 510 thread?', 'justccell'),
        'packaging'      => __('Packaging', 'justccell'),
        'elite-terpenes' => __('Elite Terpenes', 'justccell'),
        'laser-engraving'=> __('Laser engraving', 'justccell'),
        'location'       => __('Location', 'justccell'),
    ];

    $created_ids = [];
    $changed     = false;
    foreach ($pages as $slug => $title) {
        $existing = justccell_find_page_by_slug($slug);
        if (!$existing instanceof WP_Post && $slug === 'cell-3-0') {
            foreach (['justccell-3-0', 'ccell-3-0', 'ccell-3.0', 'justccell-3.0'] as $legacy) {
                $existing = justccell_find_page_by_slug($legacy);
                if ($existing instanceof WP_Post) {
                    break;
                }
            }
        }
        if (!$existing instanceof WP_Post && $slug === 'location' && function_exists('justccell_find_location_page')) {
            $existing = justccell_find_location_page();
        }
        if ($existing instanceof WP_Post) {
            $needs = $existing->post_status !== 'publish' || $existing->post_name !== $slug;
            if ($needs) {
                if ($existing->post_status === 'trash') {
                    wp_untrash_post($existing->ID);
                }
                wp_update_post([
                    'ID'          => $existing->ID,
                    'post_status' => 'publish',
                    'post_name'   => $slug,
                    'post_title'  => $existing->post_title !== '' ? $existing->post_title : $title,
                ]);
                $changed = true;
            }
            $created_ids[$slug] = (int) $existing->ID;
            justccell_assign_default_language($existing->ID);
            continue;
        }
        $id = wp_insert_post([
            'post_title'   => $title,
            'post_name'    => $slug,
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '<!-- justccell page -->',
            'post_author'  => 1,
        ], true);
        if (is_wp_error($id)) {
            continue;
        }
        if (is_int($id) && $id > 0) {
            $created_ids[$slug] = $id;
            justccell_assign_default_language($id);
            $changed = true;
        }
    }

    $have_custom = isset($created_ids['packaging'], $created_ids['laser-engraving'], $created_ids['location']);
    if ($changed || ($have_custom && get_option('justccell_pages_ver') !== JUSTCCELL_VERSION)) {
        flush_rewrite_rules(false);
        if ($have_custom) {
            update_option('justccell_pages_ver', JUSTCCELL_VERSION);
        }
    }

    return $created_ids;
}

function justccell_find_page_by_slug(string $slug): ?WP_Post
{
    $found = get_posts([
        'name'             => $slug,
        'post_type'        => 'page',
        'post_status'      => ['publish', 'draft', 'pending', 'private', 'future', 'trash'],
        'posts_per_page'   => 1,
        'suppress_filters' => true,
    ]);
    if (isset($found[0]) && $found[0] instanceof WP_Post) {
        return $found[0];
    }

    $trashed = get_posts([
        'name'             => $slug . '__trashed',
        'post_type'        => 'page',
        'post_status'      => 'trash',
        'posts_per_page'   => 1,
        'suppress_filters' => true,
    ]);
    if (isset($trashed[0]) && $trashed[0] instanceof WP_Post) {
        return $trashed[0];
    }

    global $wpdb;
    $id = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT ID FROM {$wpdb->posts} WHERE post_type = 'page' AND (post_name = %s OR post_name = %s) ORDER BY FIELD(post_status, 'publish', 'private', 'draft', 'pending', 'future', 'trash') LIMIT 1",
        $slug,
        $slug . '__trashed'
    ));
    if ($id > 0) {
        $post = get_post($id);
        return $post instanceof WP_Post ? $post : null;
    }
    return null;
}

/**
 * @param array<string, int> $page_ids
 */
function justccell_seed_menus(array $page_ids): void
{
    $primary_id = wp_create_nav_menu('Primary');
    if (is_wp_error($primary_id)) {
        $primary_id = 0;
        $menus = wp_get_nav_menus();
        foreach ($menus as $menu) {
            if ($menu->name === 'Primary') {
                $primary_id = (int) $menu->term_id;
                break;
            }
        }
    }

    if ($primary_id > 0 && !wp_get_nav_menu_items($primary_id)) {
        $order = 1;
        foreach (['all-in-ones', 'cartridge', 'pod-system', 'battery'] as $slug) {
            $term = get_term_by('slug', $slug, 'product_cat');
            if (!$term instanceof WP_Term) {
                continue;
            }
            wp_update_nav_menu_item($primary_id, 0, [
                'menu-item-title'  => $term->name,
                'menu-item-url'    => get_term_link($term),
                'menu-item-status' => 'publish',
                'menu-item-type'   => 'custom',
                'menu-item-position' => $order++,
            ]);
        }
        foreach (['technology', 'about', 'contact'] as $slug) {
            if (empty($page_ids[$slug])) {
                continue;
            }
            wp_update_nav_menu_item($primary_id, 0, [
                'menu-item-title'     => get_the_title($page_ids[$slug]),
                'menu-item-object'    => 'page',
                'menu-item-object-id' => $page_ids[$slug],
                'menu-item-type'      => 'post_type',
                'menu-item-status'    => 'publish',
                'menu-item-position'  => $order++,
            ]);
        }
        $locations = get_theme_mod('nav_menu_locations', []);
        if (!is_array($locations)) {
            $locations = [];
        }
        $locations['primary'] = $primary_id;
        $locations['footer']  = $primary_id;
        set_theme_mod('nav_menu_locations', $locations);
    }
}

add_filter('excerpt_length', static function (): int {
    return 24;
});

/**
 * Inquiry landing URL, optionally prefilled with a product SKU.
 */
function justccell_inquiry_url(string $sku = '', array $extra = []): string
{
    $url = home_url('/contact/');
    $args = [];
    if ($sku !== '') {
        $args['sku'] = $sku;
    }
    foreach ($extra as $key => $value) {
        $key = sanitize_key((string) $key);
        if ($key === '' || $value === '') {
            continue;
        }
        $args[$key] = is_scalar($value) ? (string) $value : '';
    }
    if ($args !== []) {
        $url = add_query_arg($args, $url);
    }
    return $url;
}

add_filter('rest_endpoints', static function (array $endpoints): array {
    unset($endpoints['/wp/v2/users'], $endpoints['/wp/v2/users/(?P<id>[\d]+)']);
    return $endpoints;
});

add_filter('wp_sitemaps_add_provider', static function ($provider, string $name) {
    if ($name === 'users') {
        return false;
    }
    return $provider;
}, 10, 2);

add_action('template_redirect', static function (): void {
    if (is_author()) {
        wp_safe_redirect(home_url('/'), 301);
        exit;
    }
}, 1);

/**
 * Product description editor: enable H2 / H3 / lists for SEO body copy.
 */
add_filter('tiny_mce_before_init', static function ($init, $editor_id = '') {
    if (!is_array($init) || !is_admin()) {
        return $init;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || (string) ($screen->post_type ?? '') !== 'product') {
        return $init;
    }
    $init['block_formats'] = 'Paragraph=p;Heading 2=h2;Heading 3=h3';
    return $init;
}, 20, 2);

add_filter('mce_buttons', static function ($buttons, $editor_id = '') {
    if (!is_array($buttons) || !is_admin()) {
        return $buttons;
    }
    $screen = function_exists('get_current_screen') ? get_current_screen() : null;
    if (!$screen || (string) ($screen->post_type ?? '') !== 'product') {
        return $buttons;
    }
    foreach (['formatselect', 'bullist', 'numlist'] as $need) {
        if (!in_array($need, $buttons, true)) {
            array_unshift($buttons, $need);
        }
    }
    return $buttons;
}, 20, 2);

