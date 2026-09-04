<?php
/**
 * Justccell → CMS Import — seed Pages/Products ACF from PHP clone data.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/catalog-seed.php';

const JUSTCCELL_IMPORT_PRODUCT_BATCH = 8;

/**
 * Quiet WooCommerce “set up your store” so Products opens the product list.
 */
function justccell_dismiss_woocommerce_onboarding(): void
{
    update_option('woocommerce_onboarding_profile', [
        'completed'            => true,
        'skipped'              => true,
        'is_agree_marketing'   => false,
        'store_industry'       => [['slug' => 'other']],
        'product_types'        => ['physical'],
        'business_extensions'  => [],
        'theme'                => '',
        'selling_venues'       => 'other',
        'number_employees'     => '1-10',
        'revenue'              => 'none',
        'setup_client'         => false,
    ]);
    update_option('woocommerce_task_list_hidden', 'yes');
    update_option('woocommerce_task_list_complete', 'yes');
    update_option('woocommerce_task_list_welcome_modal_dismissed', 'yes');
    update_option('woocommerce_admin_transient_notices_queue', []);
    // Homescreeen / Setup checklist off.
    update_option('woocommerce_coming_soon', 'no');
    if (class_exists('\Automattic\WooCommerce\Admin\Features\OnboardingTasks\TaskLists')) {
        // Best-effort; options above cover most Woo 8–11 installs.
    }
}

/**
 * @return array{brand:array{total:int,filled:int},listings:array{total:int,filled:int},home:bool,contact:bool,products:array{total:int,created:int,filled:int},missing_products:list<string>}
 */
function justccell_cms_import_status(): array
{
    $brand_total = 0;
    $brand_filled = 0;
    foreach (justccell_brand_page_slugs() as $slug) {
        $brand_total++;
        $page = get_page_by_path($slug);
        if ($page instanceof WP_Post && function_exists('get_field') && (string) get_field('brand_title', $page->ID) !== '') {
            $brand_filled++;
        }
    }

    $list_total = 0;
    $list_filled = 0;
    foreach (justccell_listing_defaults() as $slug => $_row) {
        $list_total++;
        $page = get_page_by_path($slug);
        if ($page instanceof WP_Post && function_exists('get_field') && (string) get_field('listing_heading', $page->ID) !== '') {
            $list_filled++;
        }
    }

    $front = (int) get_option('page_on_front');
    $home_ok = $front > 0 && function_exists('get_field') && (string) get_field('home_devices_heading', $front) !== '';

    $contact = get_page_by_path('contact');
    $contact_ok = $contact instanceof WP_Post && function_exists('get_field') && (string) get_field('contact_title', $contact->ID) !== '';

    $seed = function_exists('justccell_catalog_import_seed') ? justccell_catalog_import_seed() : [];
    $prod_total = count($seed);
    $prod_created = 0;
    $prod_filled = 0;
    $missing = [];
    foreach ($seed as $item) {
        $slug = (string) $item['slug'];
        $id = justccell_woo_product_id_by_slug($slug);
        if ($id < 1) {
            $missing[] = $slug;
            continue;
        }
        $prod_created++;
        if (function_exists('get_field') && ((string) get_field('clone_tagline', $id) !== '' || justccell_acf_to_attachment_id(get_field('clone_banner', $id)) > 0)) {
            $prod_filled++;
        }
    }

    return [
        'brand'            => ['total' => $brand_total, 'filled' => $brand_filled],
        'listings'         => ['total' => $list_total, 'filled' => $list_filled],
        'home'             => $home_ok,
        'contact'          => $contact_ok,
        'products'         => ['total' => $prod_total, 'created' => $prod_created, 'filled' => $prod_filled],
        'missing_products' => $missing,
    ];
}

function justccell_render_cms_import_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    $force = !empty($_POST['justccell_force']);
    $report = null;
    $action = '';

    if (isset($_POST['justccell_cms_action']) && check_admin_referer('justccell_cms_import') && function_exists('update_field')) {
        $action = sanitize_key((string) $_POST['justccell_cms_action']);
        justccell_dismiss_woocommerce_onboarding();
        if ($action === 'pages') {
            $report = justccell_run_cms_import_pages($force);
        } elseif ($action === 'products') {
            $offset = max(0, (int) ($_POST['justccell_product_offset'] ?? 0));
            $report = justccell_run_cms_import_products($force, $offset, JUSTCCELL_IMPORT_PRODUCT_BATCH);
        } elseif ($action === 'all_pages_then_hint') {
            $report = justccell_run_cms_import_pages($force);
            $report['hint'] = 'pages_done';
        } elseif ($action === 'woo_skip') {
            justccell_dismiss_woocommerce_onboarding();
            $report = ['woo_skipped' => true];
        }
    }

    $status = justccell_cms_import_status();

    echo '<div class="wrap"><h1>' . esc_html__('Justccell CMS Import', 'justccell') . '</h1>';
    echo '<p>' . esc_html__('Seeds real Pages and WooCommerce products from the clone data into ACF. Run Pages first, then Products in batches until complete.', 'justccell') . '</p>';

    if (!function_exists('update_field')) {
        echo '<div class="notice notice-error"><p>' . esc_html__('ACF Pro must be active.', 'justccell') . '</p></div></div>';
        return;
    }
    if (!class_exists('WooCommerce')) {
        echo '<div class="notice notice-error"><p>' . esc_html__('WooCommerce must be active to import products.', 'justccell') . '</p></div></div>';
        return;
    }

    // Status table
    echo '<h2>' . esc_html__('Progress', 'justccell') . '</h2>';
    echo '<table class="widefat striped" style="max-width:720px"><tbody>';
    echo '<tr><td>' . esc_html__('Brand pages', 'justccell') . '</td><td><strong>' . (int) $status['brand']['filled'] . ' / ' . (int) $status['brand']['total'] . '</strong></td></tr>';
    echo '<tr><td>' . esc_html__('Catalog listing pages', 'justccell') . '</td><td><strong>' . (int) $status['listings']['filled'] . ' / ' . (int) $status['listings']['total'] . '</strong></td></tr>';
    echo '<tr><td>' . esc_html__('Homepage', 'justccell') . '</td><td><strong>' . ($status['home'] ? esc_html__('ready', 'justccell') : esc_html__('pending', 'justccell')) . '</strong></td></tr>';
    echo '<tr><td>' . esc_html__('Contact', 'justccell') . '</td><td><strong>' . ($status['contact'] ? esc_html__('ready', 'justccell') : esc_html__('pending', 'justccell')) . '</strong></td></tr>';
    echo '<tr><td>' . esc_html__('Products created', 'justccell') . '</td><td><strong>' . (int) $status['products']['created'] . ' / ' . (int) $status['products']['total'] . '</strong></td></tr>';
    echo '<tr><td>' . esc_html__('Products with clone fields', 'justccell') . '</td><td><strong>' . (int) $status['products']['filled'] . ' / ' . (int) $status['products']['total'] . '</strong></td></tr>';
    echo '</tbody></table>';

    if ($status['missing_products'] !== []) {
        echo '<p><strong>' . esc_html__('Still missing product posts:', 'justccell') . '</strong> ';
        echo esc_html(implode(', ', array_slice($status['missing_products'], 0, 20)));
        if (count($status['missing_products']) > 20) {
            echo '…';
        }
        echo '</p>';
    }

    if (is_array($report)) {
        if (!empty($report['woo_skipped'])) {
            echo '<div class="notice notice-success"><p>' . esc_html__('WooCommerce setup checklist dismissed. Open Products → All products.', 'justccell') . '</p></div>';
        } else {
            echo '<div class="notice notice-success"><p>';
            echo esc_html(sprintf(
                __('Batch done. Brand pages: %1$d. Listings: %2$d. Products this batch: %3$d. Homepage: %4$s. Contact: %5$s.', 'justccell'),
                (int) ($report['pages'] ?? 0),
                (int) ($report['listings'] ?? 0),
                (int) ($report['products'] ?? 0),
                !empty($report['home']) ? __('yes', 'justccell') : __('—', 'justccell'),
                !empty($report['contact']) ? __('yes', 'justccell') : __('—', 'justccell')
            ));
            if (isset($report['next_offset'])) {
                echo ' ' . esc_html(sprintf(
                    __('Next product offset: %1$d of %2$d.', 'justccell'),
                    (int) $report['next_offset'],
                    (int) ($report['product_total'] ?? 0)
                ));
            }
            if (!empty($report['products_complete'])) {
                echo ' ' . esc_html__('All products imported.', 'justccell');
            }
            echo '</p></div>';
        }
        if (!empty($report['errors']) && is_array($report['errors'])) {
            echo '<div class="notice notice-warning"><ul>';
            foreach ($report['errors'] as $err) {
                echo '<li>' . esc_html((string) $err) . '</li>';
            }
            echo '</ul></div>';
        }
    }

    $pages_done = $status['brand']['filled'] >= $status['brand']['total']
        && $status['listings']['filled'] >= $status['listings']['total']
        && $status['home']
        && $status['contact'];
    $products_done = $status['products']['filled'] >= $status['products']['total'] && $status['products']['total'] > 0;
    $next_offset = max(0, (int) $status['products']['created']);
    // Prefer continuing from filled count if some posts exist without fields.
    if ($status['products']['created'] > $status['products']['filled']) {
        $next_offset = (int) $status['products']['filled'];
    }
    if (!$products_done && $status['missing_products'] === [] && $status['products']['created'] === $status['products']['total']) {
        // All posts exist; refill from start of unfilled — importer skips filled unless force.
        $next_offset = 0;
    }

    echo '<h2>' . esc_html__('Actions', 'justccell') . '</h2>';

    echo '<form method="post" style="margin-bottom:1.5rem">';
    wp_nonce_field('justccell_cms_import');
    echo '<label style="display:block;margin:0 0 12px"><input type="checkbox" name="justccell_force" value="1" /> ';
    echo esc_html__('Force overwrite (re-fill ACF even if fields already have values)', 'justccell');
    echo '</label>';

    echo '<p>';
    echo '<button type="submit" class="button button-primary" name="justccell_cms_action" value="pages">' . esc_html__('1. Import all Pages (brand + listings + home + contact)', 'justccell') . '</button> ';
    if ($pages_done) {
        echo '<span style="color:green">✓</span>';
    }
    echo '</p>';

    echo '<p>';
    echo '<input type="hidden" name="justccell_product_offset" value="' . esc_attr((string) $next_offset) . '" />';
    echo '<button type="submit" class="button button-primary" name="justccell_cms_action" value="products">';
    echo esc_html(sprintf(
        __('2. Import next %1$d products (from #%2$d)', 'justccell'),
        JUSTCCELL_IMPORT_PRODUCT_BATCH,
        $next_offset + 1
    ));
    echo '</button> ';
    if ($products_done) {
        echo '<span style="color:green">✓ ' . esc_html__('Complete', 'justccell') . '</span>';
    } else {
        echo '<span class="description">' . esc_html__('Click repeatedly until products show Complete.', 'justccell') . '</span>';
    }
    echo '</p>';

    echo '<p>';
    echo '<button type="submit" class="button" name="justccell_cms_action" value="woo_skip">' . esc_html__('Dismiss WooCommerce setup wizard', 'justccell') . '</button>';
    echo '</p>';
    echo '</form>';

    echo '<h2>' . esc_html__('Where to edit after import', 'justccell') . '</h2>';
    echo '<ul>';
    echo '<li>' . esc_html__('Pages → Home / About / All-In-Ones… — ACF field groups on each page', 'justccell') . '</li>';
    echo '<li>' . esc_html__('Products → All products → open a SKU — Product page fields', 'justccell') . '</li>';
    echo '<li>' . esc_html__('ACF → Field Groups — manage field definitions (after Sync if listed)', 'justccell') . '</li>';
    echo '</ul>';
    echo '</div>';
}

/**
 * @return array{pages:int,listings:int,products:int,home:bool,contact:bool,errors:list<string>}
 */
function justccell_run_cms_import_pages(bool $force = false): array
{
    $report = [
        'pages'    => 0,
        'products' => 0,
        'listings' => 0,
        'home'     => false,
        'contact'  => false,
        'errors'   => [],
    ];

    justccell_ensure_core_pages();
    justccell_ensure_listing_pages();
    justccell_ensure_front_page();

    foreach (justccell_static_pages() as $slug => $data) {
        $page = get_page_by_path($slug);
        if (!$page instanceof WP_Post) {
            $report['errors'][] = 'Missing page: ' . $slug;
            continue;
        }
        if (function_exists('justccell_page_layout_from_slug') && function_exists('justccell_assign_page_layout')) {
            $kind = justccell_page_layout_from_slug($slug, (int) $page->ID);
            if ($kind !== '') {
                justccell_assign_page_layout((int) $page->ID, $kind);
            }
        }
        justccell_import_brand_page((int) $page->ID, $data, $force);
        $report['pages']++;
    }

    $contact = get_page_by_path('contact');
    if ($contact instanceof WP_Post) {
        if (function_exists('justccell_assign_page_layout')) {
            justccell_assign_page_layout((int) $contact->ID, 'contact');
        }
        justccell_import_contact_page((int) $contact->ID, $force);
        $report['contact'] = true;
    }

    foreach (justccell_listing_defaults() as $slug => $row) {
        $page = get_page_by_path($slug);
        if (!$page instanceof WP_Post) {
            continue;
        }
        if (function_exists('justccell_assign_page_layout')) {
            justccell_assign_page_layout((int) $page->ID, 'listing');
        }
        justccell_import_listing_page((int) $page->ID, $slug, $row, $force);
        $report['listings']++;
    }

    $front = (int) get_option('page_on_front');
    if ($front > 0) {
        if (function_exists('justccell_assign_page_layout')) {
            justccell_assign_page_layout($front, 'home');
        }
        justccell_import_homepage($front, $force);
        $report['home'] = true;
    }

    justccell_import_storefront_options($force);

    update_option('justccell_cms_pages_imported', JUSTCCELL_VERSION);
    return $report;
}

/**
 * @return array{pages:int,listings:int,products:int,home:bool,contact:bool,errors:list<string>,next_offset:int,product_total:int,products_complete:bool}
 */
function justccell_run_cms_import_products(bool $force = false, int $offset = 0, int $limit = JUSTCCELL_IMPORT_PRODUCT_BATCH): array
{
    if (function_exists('set_time_limit')) {
        set_time_limit(300);
    }

    $report = [
        'pages'             => 0,
        'products'          => 0,
        'listings'          => 0,
        'home'              => false,
        'contact'           => false,
        'errors'            => [],
        'next_offset'       => $offset,
        'product_total'     => 0,
        'products_complete' => false,
    ];

    // Always seed from PHP catalog, never from Woo loop.
    $seed = function_exists('justccell_catalog_import_seed') ? justccell_catalog_import_seed() : [];
    $report['product_total'] = count($seed);
    $slice = array_slice($seed, $offset, $limit);

    // Ensure categories exist.
    if (taxonomy_exists('product_cat')) {
        foreach (justccell_product_category_labels() as $slug => $name) {
            if (!term_exists($slug, 'product_cat')) {
                wp_insert_term($name, 'product_cat', ['slug' => $slug]);
            }
        }
    }

    foreach ($slice as $item) {
        $result = justccell_import_woo_product($item, $force);
        if ($result > 0) {
            $report['products']++;
        } elseif ($result < 0) {
            $report['errors'][] = 'Product failed: ' . $item['slug'];
        }
    }

    $next = $offset + count($slice);
    $report['next_offset'] = $next;
    $report['products_complete'] = $next >= count($seed);

    if ($report['products_complete']) {
        update_option('justccell_cms_imported', JUSTCCELL_VERSION);
        flush_rewrite_rules(false);
    }

    return $report;
}

/**
 * Full import (legacy one-shot). Prefer batched UI.
 *
 * @return array{pages:int,products:int,listings:int,home:bool,contact:bool,errors:list<string>}
 */
function justccell_run_cms_import(bool $force = false): array
{
    $report = justccell_run_cms_import_pages($force);
    $seed = function_exists('justccell_catalog_import_seed') ? justccell_catalog_import_seed() : [];
    $prod = justccell_run_cms_import_products($force, 0, count($seed));
    $report['products'] = (int) ($prod['products'] ?? 0);
    $report['errors'] = array_merge($report['errors'], $prod['errors'] ?? []);
    return $report;
}

function justccell_ensure_front_page(): void
{
    $front = (int) get_option('page_on_front');
    if ($front > 0 && get_post_status($front) === 'publish') {
        update_option('show_on_front', 'page');
        return;
    }
    $existing = get_page_by_path('home');
    if (!$existing instanceof WP_Post) {
        $id = wp_insert_post([
            'post_title'   => 'Home',
            'post_name'    => 'home',
            'post_status'  => 'publish',
            'post_type'    => 'page',
            'post_content' => '',
        ]);
        if (is_int($id) && $id > 0) {
            $front = $id;
        }
    } else {
        $front = (int) $existing->ID;
    }
    if ($front > 0) {
        update_option('show_on_front', 'page');
        update_option('page_on_front', $front);
    }
}

/**
 * @param mixed $value
 */
function justccell_acf_set_if_empty(string $field, $value, int|string $post_id, bool $force = false): void
{
    if (!function_exists('update_field') || !function_exists('get_field')) {
        return;
    }
    if (!$force) {
        $current = get_field($field, $post_id);
        if (is_string($current) && trim($current) !== '') {
            return;
        }
        if (is_array($current) && $current !== []) {
            return;
        }
        if (is_numeric($current) && (int) $current > 0) {
            return;
        }
    }
    update_field($field, $value, $post_id);
}

/**
 * @param array<string, mixed> $data
 */
function justccell_import_brand_page(int $post_id, array $data, bool $force = false): void
{
    justccell_acf_set_if_empty('brand_kicker', (string) ($data['kicker'] ?? ''), $post_id, $force);
    justccell_acf_set_if_empty('brand_title', (string) ($data['title'] ?? ''), $post_id, $force);
    justccell_acf_set_if_empty('brand_title_tag', 'h1', $post_id, $force);
    justccell_acf_set_if_empty('brand_lede', (string) ($data['lede'] ?? ''), $post_id, $force);

    $img_key = (string) ($data['image'] ?? '');
    if ($img_key !== '') {
        $id = justccell_resolve_media_id($img_key);
        if ($id > 0) {
            justccell_acf_set_if_empty('brand_image', $id, $post_id, $force);
        }
    }
    $img_m = (string) ($data['image_mobile'] ?? '');
    if ($img_m !== '') {
        $mid = justccell_resolve_media_id($img_m);
        if ($mid > 0) {
            justccell_acf_set_if_empty('brand_image_mobile', $mid, $post_id, $force);
        }
    }
    justccell_acf_set_if_empty('brand_tagline', (string) ($data['tagline'] ?? ''), $post_id, $force);

    $culture = [];
    foreach ((array) ($data['culture'] ?? []) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $cid = justccell_resolve_media_id((string) ($row['image'] ?? ''));
        $culture[] = [
            'title'     => (string) ($row['title'] ?? ''),
            'title_tag' => 'h3',
            'copy'      => (string) ($row['copy'] ?? ''),
            'image'     => $cid > 0 ? $cid : '',
        ];
    }
    justccell_acf_set_if_empty('brand_culture', $culture, $post_id, $force);

    $customer = [];
    foreach ((array) ($data['customer'] ?? []) as $row) {
        if (!is_array($row)) {
            continue;
        }
        $cid = justccell_resolve_media_id((string) ($row['image'] ?? ''));
        $customer[] = [
            'title'     => (string) ($row['title'] ?? ''),
            'title_tag' => 'h3',
            'copy'      => (string) ($row['copy'] ?? ''),
            'image'     => $cid > 0 ? $cid : '',
        ];
    }
    justccell_acf_set_if_empty('brand_customer', $customer, $post_id, $force);

    $sections = [];
    foreach ((array) ($data['sections'] ?? []) as $section) {
        if (!is_array($section)) {
            continue;
        }
        $sections[] = [
            'id'        => (string) ($section['id'] ?? ''),
            'title'     => (string) ($section['title'] ?? ''),
            'title_tag' => 'h2',
            'copy'      => (string) ($section['copy'] ?? ''),
        ];
    }
    justccell_acf_set_if_empty('brand_sections', $sections, $post_id, $force);

    $blocks = [];
    foreach ((array) ($data['blocks'] ?? []) as $block) {
        if (!is_array($block)) {
            continue;
        }
        $blocks[] = [
            'title'     => (string) ($block['title'] ?? ''),
            'title_tag' => 'h2',
            'copy'      => (string) ($block['copy'] ?? ''),
        ];
    }
    justccell_acf_set_if_empty('brand_blocks', $blocks, $post_id, $force);

    $compare = is_array($data['compare'] ?? null) ? $data['compare'] : null;
    if (is_array($compare)) {
        justccell_acf_set_if_empty('brand_compare_left_title', (string) ($compare['left']['title'] ?? ''), $post_id, $force);
        justccell_acf_set_if_empty('brand_compare_right_title', (string) ($compare['right']['title'] ?? ''), $post_id, $force);
        $left = [];
        foreach ((array) ($compare['left']['items'] ?? []) as $item) {
            $left[] = ['item' => (string) $item];
        }
        $right = [];
        foreach ((array) ($compare['right']['items'] ?? []) as $item) {
            $right[] = ['item' => (string) $item];
        }
        justccell_acf_set_if_empty('brand_compare_left_items', $left, $post_id, $force);
        justccell_acf_set_if_empty('brand_compare_right_items', $right, $post_id, $force);
    }

    $cards = [];
    foreach ((array) ($data['cards'] ?? []) as $card) {
        if (!is_array($card)) {
            continue;
        }
        $cards[] = [
            'title'     => (string) ($card['title'] ?? ''),
            'title_tag' => 'h2',
            'copy'      => (string) ($card['copy'] ?? ''),
            'url'       => (string) ($card['url'] ?? '/'),
        ];
    }
    justccell_acf_set_if_empty('brand_cards', $cards, $post_id, $force);

    $timeline = [];
    $years = (array) ($data['timeline_years'] ?? []);
    if ($years !== []) {
        foreach ($years as $row) {
            if (!is_array($row)) {
                continue;
            }
            $year = (string) ($row['year'] ?? '');
            foreach ((array) ($row['items'] ?? []) as $item) {
                $timeline[] = [
                    'year' => $year,
                    'item' => (string) $item,
                ];
            }
        }
    } else {
        foreach ((array) ($data['timeline'] ?? []) as $item) {
            $timeline[] = ['item' => (string) $item];
        }
    }
    justccell_acf_set_if_empty('brand_timeline', $timeline, $post_id, $force);

    justccell_acf_set_if_empty('brand_cta_title_tag', 'h2', $post_id, $force);

    $video_key = (string) ($data['video'] ?? '');
    if ($video_key !== '') {
        $vid = justccell_resolve_media_id($video_key);
        if ($vid > 0) {
            justccell_acf_set_if_empty('brand_video', $vid, $post_id, $force);
        }
    }
    justccell_acf_set_if_empty('brand_video_heading', (string) ($data['video_heading'] ?? ''), $post_id, $force);
    justccell_acf_set_if_empty('brand_video_copy', (string) ($data['video_copy'] ?? ''), $post_id, $force);

    if (!empty($data['places']) && is_array($data['places'])) {
        justccell_acf_set_if_empty('locations_items', $data['places'], $post_id, $force);
    } elseif (function_exists('justccell_default_location_rows') && function_exists('justccell_is_location_page_slug') && justccell_is_location_page_slug((string) get_post_field('post_name', $post_id))) {
        justccell_acf_set_if_empty('locations_items', justccell_default_location_rows(), $post_id, $force);
    }
}

function justccell_import_storefront_options(bool $force = false): void
{
    justccell_acf_set_if_empty('store_instagram', 'https://www.instagram.com/justccell', 'option', $force);
    justccell_acf_set_if_empty('store_laser_heading', __('Laser engraving', 'justccell'), 'option', $force);
    justccell_acf_set_if_empty('store_laser_on_products', 1, 'option', $force);
    justccell_acf_set_if_empty('store_collection_enabled', 1, 'option', $force);
    justccell_acf_set_if_empty(
        'store_collection_copy',
        __('Collection from our UK warehouse is available. Mention collection on your enquiry and we will confirm a slot.', 'justccell'),
        'option',
        $force
    );
    justccell_acf_set_if_empty(
        'store_laser_copy',
        __('From beam to brand — laser engraving is a process we run for your logo, micro text, and finish. Watch the film, then add engraving to your quote.', 'justccell'),
        'option',
        $force
    );

    $vid = justccell_resolve_media_id('laser-engraving.mp4');
    if ($vid > 0) {
        justccell_acf_set_if_empty('store_laser_video', $vid, 'option', $force);
    }

    $rows = [];
    foreach (justccell_default_store_landings() as $store => $row) {
        $rows[] = [
            'store'     => $store,
            'enabled'   => !empty($row['enabled']),
            'kicker'    => $row['kicker'],
            'title'     => $row['title'],
            'lede'      => $row['lede'],
            'cta_label' => $row['cta_label'],
            'cta_url'   => $row['cta_url'],
        ];
    }
    justccell_acf_set_if_empty('store_landings', $rows, 'option', $force);
}

function justccell_import_contact_page(int $post_id, bool $force = false): void
{
    justccell_acf_set_if_empty('contact_kicker', __('Contact', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('contact_title', __('Contact us', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('contact_title_tag', 'h1', $post_id, $force);
    justccell_acf_set_if_empty(
        'contact_lede',
        __('Tell us about your extracts, hardware line, and market. A Justccell representative will follow up within one business day.', 'justccell'),
        $post_id,
        $force
    );
    justccell_acf_set_if_empty('contact_hero_title', __('Contact us', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('contact_info_heading', __('Contact Information', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('contact_sales_label', __('Purchase Inquiry:', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('contact_support_label', __('Justccell Support:', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('contact_follow_heading', __('Follow Us', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('contact_form_title', __('Contact us', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty(
        'contact_form_copy',
        __('Please fill the form below to submit your inquiry, and a Justccell sales representative will contact you promptly.', 'justccell'),
        $post_id,
        $force
    );
    justccell_acf_set_if_empty('contact_faq_heading', __('FAQ', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('contact_distributors_heading', __('Our Distributors', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty(
        'contact_address',
        "112 - 116 Hamill House\nChorley New Road,\nBolton,\nBL1 4DH",
        $post_id,
        $force
    );
    justccell_acf_set_if_empty(
        'contact_distributors_copy',
        __('Looking to carry Justccell hardware? Include your region and channel in the message and we will route you to the right team.', 'justccell'),
        $post_id,
        $force
    );
    $faqs = [];
    foreach (justccell_contact_faqs() as $faq) {
        $faqs[] = ['q' => $faq['q'], 'a' => $faq['a']];
    }
    justccell_acf_set_if_empty('contact_faq', $faqs, $post_id, $force);
}

/**
 * @param array{heading:string,lede:string,desktop:string,mobile:string} $row
 */
function justccell_import_listing_page(int $post_id, string $slug, array $row, bool $force = false): void
{
    justccell_acf_set_if_empty('listing_heading', $row['heading'], $post_id, $force);
    justccell_acf_set_if_empty('listing_heading_tag', 'h1', $post_id, $force);
    justccell_acf_set_if_empty('listing_lede', $row['lede'], $post_id, $force);

    $desk = justccell_resolve_media_id($row['desktop']);
    $mob  = justccell_resolve_media_id($row['mobile']);
    if ($desk > 0) {
        justccell_acf_set_if_empty('listing_hero_slides', [
            [
                'desktop' => $desk,
                'mobile'  => $mob > 0 ? $mob : $desk,
                'url'     => '',
            ],
        ], $post_id, $force);
    }

    $faq_rows = [];
    foreach (justccell_listing_faq($slug) as $faq) {
        $faq_rows[] = ['q' => $faq['q'], 'a' => $faq['a']];
    }
    justccell_acf_set_if_empty('listing_faq', $faq_rows, $post_id, $force);
}

function justccell_import_homepage(int $post_id, bool $force = false): void
{
    $keys = justccell_home_asset_keys();
    justccell_seed_home_hero_fields();

    justccell_acf_set_if_empty('home_devices_heading', __('Devices Crafted for Cannabis', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('home_devices_heading_tag', 'h1', $post_id, $force);
    justccell_acf_set_if_empty('home_custom_heading', 'Customize<br>Your Own Products', $post_id, $force);
    justccell_acf_set_if_empty('home_custom_heading_tag', 'h2', $post_id, $force);
    justccell_acf_set_if_empty('home_custom_kicker', __('Classic Customization', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty(
        'home_custom_copy',
        __('Set your brand apart with personalized finishes and distinctive secondary features that make your products truly unique.', 'justccell'),
        $post_id,
        $force
    );

    $custom_ids = [];
    foreach (['cust1', 'cust2', 'cust3', 'cust4'] as $slot) {
        $id = justccell_resolve_media_id((string) ($keys[$slot] ?? ''));
        if ($id > 0) {
            $custom_ids[] = $id;
        }
    }
    justccell_acf_set_if_empty('home_custom_images', $custom_ids, $post_id, $force);

    $premium = justccell_resolve_media_id((string) ($keys['premium'] ?? ''));
    if ($premium > 0) {
        justccell_acf_set_if_empty('home_premium_image', $premium, $post_id, $force);
    }
    justccell_acf_set_if_empty('home_premium_heading', __('Premium Customization', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('home_premium_heading_tag', 'h3', $post_id, $force);
    justccell_acf_set_if_empty(
        'home_premium_copy',
        __('From concept to creation, our expert engineering and design teams are here to transform your vision into a masterpiece from the ground up.', 'justccell'),
        $post_id,
        $force
    );

    justccell_acf_set_if_empty('home_fill_heading', __('Make Filling and Capping Effortless', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('home_fill_heading_tag', 'h2', $post_id, $force);
    justccell_acf_set_if_empty(
        'home_fill_copy',
        __('The filling and capping solution delivers unmatched quality, efficiency, and affordability. Streamline production and turn filling and capping your devices into a hassle-free process.', 'justccell'),
        $post_id,
        $force
    );
    $fill = justccell_resolve_media_id((string) ($keys['fill'] ?? ''));
    if ($fill > 0) {
        justccell_acf_set_if_empty('home_fill_image', $fill, $post_id, $force);
    }
    justccell_acf_set_if_empty('home_fill_link_label', __('View Details', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('home_fill_link_url', home_url('/solution/'), $post_id, $force);

    justccell_acf_set_if_empty('home_trusted_heading', __('Trusted by', 'justccell'), $post_id, $force);
    justccell_acf_set_if_empty('home_trusted_heading_tag', 'h2', $post_id, $force);
    $trusted = justccell_resolve_media_id((string) ($keys['trusted'] ?? ''));
    if ($trusted > 0) {
        justccell_acf_set_if_empty('home_trusted_image', $trusted, $post_id, $force);
    }

    $arrow = justccell_resolve_media_id((string) ($keys['arrow'] ?? ''));
    if ($arrow > 0) {
        justccell_acf_set_if_empty('home_arrow_image', $arrow, $post_id, $force);
    }
}

/**
 * @param array{name:string,slug:string,category:string,image:string,specs:list<string>} $item
 * @return int product id, 0 skip, -1 fail
 */
function justccell_import_woo_product(array $item, bool $force = false): int
{
    $slug = (string) $item['slug'];
    $id   = justccell_woo_product_id_by_slug($slug);

    if ($id < 1) {
        $post_id = wp_insert_post([
            'post_title'  => (string) $item['name'],
            'post_name'   => $slug,
            'post_status' => 'publish',
            'post_type'   => 'product',
            'menu_order'  => 0,
        ]);
        if (!is_int($post_id) || $post_id < 1) {
            return -1;
        }
        $id = $post_id;
        wp_set_object_terms($id, 'simple', 'product_type');
        update_post_meta($id, '_sku', $slug);
        update_post_meta($id, '_virtual', 'yes');
        update_post_meta($id, '_sold_individually', 'yes');
        update_post_meta($id, '_manage_stock', 'no');
        update_post_meta($id, '_price', '');
        update_post_meta($id, '_regular_price', '');
        update_post_meta($id, '_catalog_visibility', 'visible');
    }

    $term = get_term_by('slug', (string) $item['category'], 'product_cat');
    if ($term instanceof WP_Term) {
        wp_set_object_terms($id, [(int) $term->term_id], 'product_cat');
    }

    $thumb = justccell_resolve_media_id((string) $item['image']);
    if ($thumb > 0 && ((int) get_post_thumbnail_id($id) < 1 || $force)) {
        set_post_thumbnail($id, $thumb);
    }

    $page = function_exists('justccell_product_pages') ? (justccell_product_pages()[$slug] ?? null) : null;
    if (!is_array($page)) {
        $specs = [];
        foreach ((array) ($item['specs'] ?? []) as $line) {
            $specs[] = ['line' => (string) $line];
        }
        justccell_acf_set_if_empty('clone_specs', $specs, $id, $force);
        return $id;
    }

    justccell_acf_set_if_empty('clone_subtitle', (string) ($page['subtitle'] ?? ''), $id, $force);
    justccell_acf_set_if_empty('clone_product_heading', (string) ($page['product_heading'] ?? $page['name'] ?? ''), $id, $force);
    justccell_acf_set_if_empty('clone_specs_heading', (string) ($page['specs_heading'] ?? __('Specifications', 'justccell')), $id, $force);

    $banner = justccell_resolve_media_id((string) ($page['banner'] ?? ''));
    if ($banner > 0) {
        justccell_acf_set_if_empty('clone_banner', $banner, $id, $force);
    }

    $gallery = [];
    foreach ((array) ($page['gallery'] ?? []) as $key) {
        $gid = justccell_resolve_media_id((string) $key);
        if ($gid > 0) {
            $gallery[] = $gid;
        }
    }
    if ($gallery !== [] && (get_post_meta($id, '_product_image_gallery', true) === '' || $force)) {
        update_post_meta($id, '_product_image_gallery', implode(',', array_map('strval', $gallery)));
    }

    $spin = [];
    foreach ((array) ($page['spin'] ?? []) as $key) {
        $sid = justccell_resolve_media_id((string) $key);
        if ($sid > 0) {
            $spin[] = $sid;
        }
    }
    justccell_acf_set_if_empty('clone_spin', $spin, $id, $force);

    $specs = [];
    foreach ((array) ($page['specs'] ?? []) as $line) {
        $specs[] = ['line' => (string) $line];
    }
    justccell_acf_set_if_empty('clone_specs', $specs, $id, $force);

    $features = [];
    foreach ((array) ($page['features'] ?? []) as $feature) {
        if (!is_array($feature)) {
            continue;
        }
        $fid = justccell_resolve_media_id((string) ($feature['image'] ?? ''));
        $features[] = [
            'title'      => (string) ($feature['title'] ?? ''),
            'title_tag'  => 'h2',
            'copy'       => (string) ($feature['copy'] ?? ''),
            'note'       => (string) ($feature['note'] ?? ''),
            'text_color' => justccell_normalize_highlight_text_color((string) ($feature['text_color'] ?? 'black')),
            'image'      => $fid > 0 ? $fid : '',
        ];
    }
    justccell_acf_set_if_empty('clone_features', $features, $id, $force);

    justccell_acf_set_if_empty('clone_evomax_title', (string) ($page['evomax_title'] ?? ''), $id, $force);
    justccell_acf_set_if_empty('clone_evomax_title_tag', 'h2', $id, $force);
    justccell_acf_set_if_empty('clone_evomax_copy', (string) ($page['evomax_copy'] ?? ''), $id, $force);
    $evo = justccell_resolve_media_id((string) ($page['evomax_bg'] ?? ''));
    if ($evo > 0) {
        justccell_acf_set_if_empty('clone_evomax_bg', $evo, $id, $force);
    }

    $details = [];
    foreach ((array) ($page['details'] ?? []) as $key) {
        $did = justccell_resolve_media_id((string) $key);
        if ($did > 0) {
            $details[] = $did;
        }
    }
    $detail_slots = ['clone_detail_1', 'clone_detail_2', 'clone_detail_3'];
    foreach ($detail_slots as $i => $field_name) {
        if (!empty($details[$i])) {
            justccell_acf_set_if_empty($field_name, (int) $details[$i], $id, $force);
        }
    }
    justccell_acf_set_if_empty('clone_details', $details, $id, $force);

    $meta = justccell_catalog_card_meta($item);
    justccell_acf_set_if_empty('clone_card_tagline', $meta['tagline'], $id, $force);
    justccell_acf_set_if_empty('clone_card_capacity', $meta['capacity'], $id, $force);
    $card_img = justccell_resolve_media_id($meta['image']);
    if ($card_img > 0) {
        justccell_acf_set_if_empty('clone_card_image', $card_img, $id, $force);
    }

    $oil_map = [
        'mini-tank' => 'distillates', 'voca' => 'distillates', 'flexcell' => 'distillates',
        'ds0103' => 'distillates', 'skye-ii' => 'distillates', 'listo' => 'distillates',
        'rosin-bar' => 'live-rosins', 'vision-box-elite' => 'live-rosins',
        'flexcell-pro' => 'live-resins', 'voca-pro' => 'live-resins', 'blanc' => 'live-resins', 'slym' => 'live-resins',
        'flexcell-x' => 'all-oil', 'tank' => 'all-oil', 'eco-star' => 'all-oil',
        'vision-box' => 'all-oil', 'voca-pro-max' => 'all-oil', 'voca-max' => 'all-oil',
    ];
    if (isset($oil_map[$slug])) {
        justccell_acf_set_if_empty('clone_oil_group', $oil_map[$slug], $id, $force);
    }

    $mega = justccell_mega_featured();
    $featured_slugs = [];
    foreach ($mega as $list) {
        $featured_slugs = array_merge($featured_slugs, $list);
    }
    if (in_array($slug, $featured_slugs, true)) {
        justccell_acf_set_if_empty('clone_mega_featured', 1, $id, $force);
    }

    return $id;
}
