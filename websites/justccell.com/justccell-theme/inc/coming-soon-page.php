<?php
/**
 * Simple Coming Soon page template helpers.
 *
 * Assign Template: "Justccell Coming Soon" on any page.
 * No ACF options — title / excerpt / content drive the spotlight.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_COMING_SOON_TEMPLATE = 'page-templates/justccell-coming-soon.php';

function justccell_page_uses_coming_soon_template(int $post_id = 0): bool
{
    $post_id = $post_id > 0 ? $post_id : (int) get_queried_object_id();
    if ($post_id < 1) {
        return false;
    }
    return (string) get_page_template_slug($post_id) === JUSTCCELL_COMING_SOON_TEMPLATE;
}

function justccell_page_shows_coming_soon(int $post_id = 0): bool
{
    return justccell_page_uses_coming_soon_template($post_id);
}

/**
 * Built-in copy when the page has no excerpt/content yet.
 *
 * @return array{title?:string,lede?:string,eyebrow_sr?:string}
 */
function justccell_coming_soon_slug_defaults(string $slug): array
{
    $map = [
        'packaging' => [
            'eyebrow_sr' => __('Packaging page coming soon.', 'justccell'),
            'title'      => __('Packaging is on the way', 'justccell'),
            'lede'       => __('We are building a dedicated packaging brief for sleeves, boxes, and inserts. Search the catalogue or send a message — you can still ask about packaging on your hardware enquiry.', 'justccell'),
        ],
        'elite-terpenes' => [
            'eyebrow_sr' => __('Elite Terpenes page coming soon.', 'justccell'),
            'title'      => __('Elite Terpenes is on the way', 'justccell'),
            'lede'       => __('Elite Terpenes will launch as its own storefront. Until then, search the Justccell catalogue or contact us about your next fill.', 'justccell'),
        ],
    ];
    return $map[$slug] ?? [];
}

/**
 * Spotlight args for template-parts/page/spotlight.php.
 *
 * @return array<string, mixed>
 */
function justccell_get_coming_soon_spotlight(int $post_id = 0): array
{
    $post_id = $post_id > 0 ? $post_id : (int) get_queried_object_id();
    $slug    = $post_id > 0 ? (string) get_post_field('post_name', $post_id) : '';
    $defaults = justccell_coming_soon_slug_defaults($slug);

    $page_title = $post_id > 0 ? trim((string) get_the_title($post_id)) : '';
    $lede       = '';
    if ($post_id > 0) {
        $excerpt = trim((string) get_post_field('post_excerpt', $post_id));
        if ($excerpt !== '') {
            $lede = $excerpt;
        } else {
            $raw = (string) get_post_field('post_content', $post_id);
            $raw = trim(wp_strip_all_tags($raw));
            if ($raw !== '' && !str_starts_with($raw, '<!--')) {
                $lede = $raw;
            }
        }
    }

    $title = (string) ($defaults['title'] ?? '');
    if ($title === '' && $page_title !== '' && strcasecmp($page_title, 'Coming Soon') !== 0) {
        /* translators: %s: page title */
        $title = sprintf(__('%s is on the way', 'justccell'), $page_title);
    }
    if ($title === '') {
        $title = $page_title !== '' ? $page_title : __('Coming soon', 'justccell');
    }
    if ($lede === '') {
        $lede = (string) ($defaults['lede'] ?? __('This page is being prepared. Browse the hardware catalogue or contact us in the meantime.', 'justccell'));
    }

    return [
        'eyebrow'         => __('Coming soon', 'justccell'),
        'eyebrow_sr'      => (string) ($defaults['eyebrow_sr'] ?? __('This page is coming soon.', 'justccell')),
        'title'           => $title,
        'lede'            => $lede,
        'primary_label'   => __('Browse hardware', 'justccell'),
        'primary_url'     => '/',
        'secondary_label' => __('Contact us', 'justccell'),
        'secondary_url'   => '/contact/',
        'show_search'     => true,
        'show_showcase'   => true,
        'shop_heading'    => __('Hardware in the catalogue', 'justccell'),
        'shop_lede'       => __('Live SKUs you can open from the catalogue today.', 'justccell'),
    ];
}

/**
 * Ensure Packaging + Elite Terpenes use the Coming Soon template (clone pattern).
 */
function justccell_ensure_coming_soon_pages(): void
{
    if (get_option('justccell_coming_soon_template_09191') === '1') {
        return;
    }
    if (!function_exists('justccell_find_page_by_slug') || !function_exists('justccell_assign_page_layout')) {
        return;
    }

    $pages = [
        'packaging' => [
            'title'   => __('Packaging', 'justccell'),
            'excerpt' => (string) (justccell_coming_soon_slug_defaults('packaging')['lede'] ?? ''),
        ],
        'elite-terpenes' => [
            'title'   => __('Elite Terpenes', 'justccell'),
            'excerpt' => (string) (justccell_coming_soon_slug_defaults('elite-terpenes')['lede'] ?? ''),
        ],
    ];

    foreach ($pages as $slug => $meta) {
        $page = justccell_find_page_by_slug($slug);
        if (!$page instanceof WP_Post) {
            $id = wp_insert_post([
                'post_title'   => $meta['title'],
                'post_name'    => $slug,
                'post_status'  => 'publish',
                'post_type'    => 'page',
                'post_excerpt' => $meta['excerpt'],
                'post_content' => '',
                'post_author'  => 1,
            ], true);
            if (is_wp_error($id) || !is_int($id) || $id < 1) {
                continue;
            }
            $page = get_post($id);
        }
        if (!$page instanceof WP_Post) {
            continue;
        }

        $post_id = (int) $page->ID;
        if ($page->post_status !== 'publish' || $page->post_name !== $slug) {
            wp_update_post([
                'ID'          => $post_id,
                'post_status' => 'publish',
                'post_name'   => $slug,
                'post_title'  => $page->post_title !== '' ? $page->post_title : $meta['title'],
            ]);
        }
        if (trim((string) $page->post_excerpt) === '' && $meta['excerpt'] !== '') {
            wp_update_post([
                'ID'           => $post_id,
                'post_excerpt' => $meta['excerpt'],
            ]);
        }

        justccell_assign_page_layout($post_id, 'coming-soon');
    }

    update_option('justccell_coming_soon_template_09191', '1', false);
}

add_action('init', 'justccell_ensure_coming_soon_pages', 76);

/**
 * Coming Soon pages share one simple backend: title + excerpt (lede). No brand ACF.
 */
add_action('init', static function (): void {
    add_post_type_support('page', 'excerpt');
}, 20);

/**
 * Hide leftover brand “Page content” ACF when the Coming Soon template is assigned.
 *
 * @param array<string, mixed>|false $group
 * @return array<string, mixed>|false
 */
add_filter('acf/load_field_group', static function ($group) {
    if (!is_array($group)) {
        return $group;
    }
    $key = (string) ($group['key'] ?? '');
    if ($key !== 'group_jc_generic_brand' && $key !== 'group_jc_laser_page') {
        return $group;
    }
    $post_id = 0;
    if (function_exists('acf_get_form_data')) {
        $post_id = (int) acf_get_form_data('post_id');
    }
    if ($post_id < 1 && is_admin()) {
        $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
        if ($post_id < 1 && isset($_POST['post_ID'])) { // phpcs:ignore WordPress.Security.NonceVerification.Missing
            $post_id = (int) $_POST['post_ID']; // phpcs:ignore WordPress.Security.NonceVerification.Missing
        }
    }
    if ($post_id > 0 && justccell_page_uses_coming_soon_template($post_id)) {
        return false;
    }
    return $group;
}, 20);

/**
 * Keep Excerpt visible on Coming Soon pages (lede for the blue hero).
 *
 * @param list<string> $hidden
 * @param WP_Screen    $screen
 * @return list<string>
 */
add_filter('hidden_meta_boxes', static function (array $hidden, $screen): array {
    if (!is_object($screen) || (string) ($screen->id ?? '') !== 'page') {
        return $hidden;
    }
    $post_id = isset($_GET['post']) ? (int) $_GET['post'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
    if ($post_id < 1 || !justccell_page_uses_coming_soon_template($post_id)) {
        return $hidden;
    }
    return array_values(array_filter(
        $hidden,
        static fn ($id): bool => (string) $id !== 'postexcerpt'
    ));
}, 10, 2);

add_action('add_meta_boxes_page', static function (WP_Post $post): void {
    if (!justccell_page_uses_coming_soon_template((int) $post->ID)) {
        return;
    }
    add_meta_box(
        'justccell_coming_soon_help',
        __('Coming Soon page', 'justccell'),
        static function (): void {
            echo '<p style="margin:0.4em 0 0;">';
            echo esc_html__(
                'Same simple screen for Packaging, Elite Terpenes, or any future hub. Edit the page title and the Excerpt box (lede under “Coming soon”). Template must stay “Justccell Coming Soon”.',
                'justccell'
            );
            echo '</p>';
        },
        'page',
        'side',
        'high'
    );
});
