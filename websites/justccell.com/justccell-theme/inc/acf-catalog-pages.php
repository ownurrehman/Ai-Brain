<?php
/**
 * ACF groups + seed for Home, catalog listings, and generic brand pages.
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
function justccell_home_page_text_defaults(): array
{
    return [
        'devices_heading'     => __('Devices Crafted for Cannabis', 'justccell'),
        'devices_heading_tag' => 'h1',
        'tab_all_in_ones'     => __('All-In-Ones', 'justccell'),
        'tab_cartridge'       => __('Cartridges', 'justccell'),
        'tab_pod_system'      => __('Pod Systems', 'justccell'),
        'tab_battery'         => __('510 Batteries', 'justccell'),
        'custom_heading'      => 'Customize<br>Your Own Products',
        'custom_heading_tag'  => 'h2',
        'custom_kicker'       => __('Classic Customization', 'justccell'),
        'custom_copy'         => __('Set your brand apart with personalized finishes and distinctive secondary features that make your products truly unique.', 'justccell'),
        'premium_heading'     => __('Premium Customization', 'justccell'),
        'premium_heading_tag' => 'h3',
        'premium_copy'        => __('From concept to creation, our expert engineering and design teams are here to transform your vision into a masterpiece from the ground up.', 'justccell'),
        'fill_heading'        => __('Make Filling and Capping Effortless', 'justccell'),
        'fill_heading_tag'    => 'h2',
        'fill_copy'           => __('The filling and capping solution delivers unmatched quality, efficiency, and affordability. Streamline production and turn filling and capping your devices into a hassle-free process.', 'justccell'),
        'fill_link_label'     => __('View Details', 'justccell'),
        'fill_link_url'       => home_url('/solution/'),
        'trusted_heading'     => __('Laser engraving', 'justccell'),
        'trusted_heading_tag' => 'h2',
    ];
}

/**
 * Seed laser-only layout fields (intro buttons, section headings).
 *
 * @param array<string, mixed> $raw
 */
function justccell_laser_page_seed_layout(int $post_id, array $raw): void
{
    if (!function_exists('justccell_acf_seed_text_if_empty')) {
        return;
    }
    $pairs = [
        'brand_intro_primary_label'   => 'intro_primary_label',
        'brand_intro_primary_url'     => 'intro_primary_url',
        'brand_intro_secondary_label' => 'intro_secondary_label',
        'brand_intro_secondary_url'   => 'intro_secondary_url',
        'brand_steps_heading'         => 'steps_heading',
        'brand_steps_lede'            => 'steps_lede',
        'brand_hardware_heading'      => 'hardware_heading',
        'brand_hardware_lede'         => 'hardware_lede',
    ];
    foreach ($pairs as $field => $key) {
        justccell_acf_seed_text_if_empty($field, (string) ($raw[$key] ?? ''), $post_id);
    }
    justccell_acf_seed_text_if_empty('brand_steps_heading_tag', 'h2', $post_id);
    justccell_acf_seed_text_if_empty('brand_hardware_heading_tag', 'h2', $post_id);
}

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_listing_faq_seed_defaults(string $category): array
{
    if ($category !== 'all-in-ones') {
        return [];
    }

    return [
        [
            'q' => __('What is an all-in-one vape?', 'justccell'),
            'a' => __('All-in-one devices offer a simpler vaping experience. Each vaporizer comes with a pre-filled oil tank and a pre-charged internal battery. No need to recharge or refill them; once finished, start another one.', 'justccell'),
        ],
        [
            'q' => __('How to use Justccell all-in-one vapes?', 'justccell'),
            'a' => __('Justccell all-in-one products are activated by inhalation. You can use them directly without additional operations.', 'justccell'),
        ],
        [
            'q' => __('Can I reuse my all-in-one vape?', 'justccell'),
            'a' => __('No. All-in-one devices are filled on automated machines, so the design is not meant for hand filling. Discard the device once the oil is used up.', 'justccell'),
        ],
        [
            'q' => __('How long does an all-in-one vape last?', 'justccell'),
            'a' => __('It depends on draw length and oil type. Oil is vaporized at a steady rate of about 5mg every 3-second draw. A 0.5mL cartridge lasts approximately 100 draws at that rate.', 'justccell'),
        ],
        [
            'q' => __('Does Justccell fill all-in-one vapes?', 'justccell'),
            'a' => __('No. Justccell produces and sells hardware only. We do not produce, distribute, or sell any material filled in cartridges and all-in-one devices.', 'justccell'),
        ],
        [
            'q' => __('Do Justccell all-in-one vapes test for heavy metals?', 'justccell'),
            'a' => __('Yes. All-in-one devices are specified with heavy-metal testing, and the core component material is medical-grade 316L stainless steel (except DS0103 and DS0105).', 'justccell'),
        ],
        [
            'q' => __('Are Justccell all-in-one devices safe?', 'justccell'),
            'a' => __('Products are manufactured with high-quality materials under rigorous safety control and carry FDA, RoHS, FCC, CE, and UL certificates as applicable. Battery-containing products comply with UN38.3, PI967, and SP188 for lithium transport.', 'justccell'),
        ],
    ];
}

function justccell_acf_seed_text_if_empty(string $field, string $value, int $post_id): void
{
    if ($value === '' || !function_exists('get_field') || !function_exists('update_field')) {
        return;
    }
    if (trim((string) get_field($field, $post_id)) === '') {
        update_field($field, $value, $post_id);
    }
}

function justccell_acf_seed_image_if_empty(string $field, string $media_key, int $post_id): void
{
    if (!function_exists('get_field') || !function_exists('update_field') || $media_key === '') {
        return;
    }
    if ((int) get_field($field, $post_id) > 0) {
        return;
    }
    justccell_ensure_media_url($media_key);
    $id = justccell_media_id($media_key);
    if ($id > 0) {
        update_field($field, $id, $post_id);
    }
}

function justccell_home_seed_page_acf_content(): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }

    if (get_option('justccell_home_acf_seeded_initial') === '1') {
        return;
    }

    $front = function_exists('justccell_home_content_page_id')
        ? justccell_home_content_page_id()
        : (int) get_option('page_on_front');
    if ($front < 1) {
        return;
    }

    $d = justccell_home_page_text_defaults();
    $text_map = [
        'home_devices_heading'      => 'devices_heading',
        'home_devices_heading_tag'  => 'devices_heading_tag',
        'home_tab_all_in_ones'      => 'tab_all_in_ones',
        'home_tab_cartridge'        => 'tab_cartridge',
        'home_tab_pod_system'       => 'tab_pod_system',
        'home_tab_battery'          => 'tab_battery',
        'home_custom_heading'       => 'custom_heading',
        'home_custom_heading_tag'   => 'custom_heading_tag',
        'home_custom_kicker'        => 'custom_kicker',
        'home_custom_copy'          => 'custom_copy',
        'home_premium_heading'      => 'premium_heading',
        'home_premium_heading_tag'  => 'premium_heading_tag',
        'home_premium_copy'         => 'premium_copy',
        'home_fill_heading'         => 'fill_heading',
        'home_fill_heading_tag'     => 'fill_heading_tag',
        'home_fill_copy'            => 'fill_copy',
        'home_fill_link_label'      => 'fill_link_label',
        'home_fill_link_url'        => 'fill_link_url',
        'home_trusted_heading'      => 'trusted_heading',
        'home_trusted_heading_tag'  => 'trusted_heading_tag',
    ];
    foreach ($text_map as $field => $key) {
        justccell_acf_seed_text_if_empty($field, $d[$key], $front);
    }

    $keys = justccell_home_asset_keys();
    justccell_acf_seed_image_if_empty('home_fill_image', (string) ($keys['fill'] ?? ''), $front);
    justccell_acf_seed_image_if_empty('home_premium_image', (string) ($keys['premium'] ?? ''), $front);
    justccell_acf_seed_image_if_empty('home_arrow_image', (string) ($keys['arrow'] ?? ''), $front);

    $custom_ids = [];
    foreach (['cust1', 'cust2', 'cust3', 'cust4'] as $slot) {
        $file = (string) ($keys[$slot] ?? '');
        if ($file === '') {
            continue;
        }
        justccell_ensure_media_url($file);
        $id = justccell_media_id($file);
        if ($id > 0) {
            $custom_ids[] = $id;
        }
    }
    $existing_gallery = get_field('home_custom_images', $front);
    if ((!is_array($existing_gallery) || $existing_gallery === []) && $custom_ids !== []) {
        update_field('home_custom_images', $custom_ids, $front);
    }

    if (function_exists('justccell_seed_home_hero_fields')) {
        justccell_seed_home_hero_fields();
    }

    update_option('justccell_home_acf_seeded_initial', '1', false);
}

function justccell_listing_seed_pages_acf_content(): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }

    if (get_option('justccell_listing_acf_seeded_initial') === '1') {
        return;
    }

    foreach (justccell_listing_defaults() as $slug => $row) {
        $page_id = justccell_listing_page_id($slug);
        if ($page_id < 1) {
            continue;
        }

        justccell_acf_seed_text_if_empty('listing_heading', $row['heading'], $page_id);
        justccell_acf_seed_text_if_empty('listing_lede', $row['lede'], $page_id);
        justccell_acf_seed_text_if_empty('listing_heading_tag', 'h1', $page_id);
        justccell_acf_seed_text_if_empty('listing_faq_heading', 'FAQ', $page_id);
        justccell_acf_seed_text_if_empty('listing_faq_heading_tag', 'h2', $page_id);

        $slides = get_field('listing_hero_slides', $page_id);
        if (!is_array($slides) || $slides === []) {
            justccell_ensure_media_files([$row['desktop'], $row['mobile']]);
            $desk = justccell_media_id($row['desktop']);
            $mob  = justccell_media_id($row['mobile']);
            if ($desk > 0) {
                update_field('listing_hero_slides', [[
                    'desktop' => $desk,
                    'mobile'  => $mob > 0 ? $mob : $desk,
                    'url'     => '',
                ]], $page_id);
            }
        }

        $faq_defaults = justccell_listing_faq_seed_defaults($slug);
        if ($faq_defaults !== []) {
            $faq_rows = [];
            foreach ($faq_defaults as $faq) {
                $faq_rows[] = ['q' => $faq['q'], 'a' => $faq['a']];
            }
            $existing_faq = get_field('listing_faq', $page_id);
            if (!is_array($existing_faq) || $existing_faq === []) {
                update_field('listing_faq', $faq_rows, $page_id);
            }
        }
    }

    update_option('justccell_listing_acf_seeded_initial', '1', false);
}

/**
 * @param array<string, mixed> $raw
 * @return list<array<string, mixed>>
 */
function justccell_generic_brand_sections_acf_rows(array $raw): array
{
    $rows = [];
    foreach (justccell_normalize_brand_sections($raw['sections'] ?? []) as $section) {
        $rows[] = [
            'id'        => (string) ($section['id'] ?? ''),
            'title'     => (string) ($section['title'] ?? ''),
            'title_tag' => (string) ($section['title_tag'] ?? 'h2'),
            'copy'      => (string) ($section['copy'] ?? ''),
        ];
    }
    return $rows;
}

/**
 * @param array<string, mixed> $raw
 * @return list<array<string, mixed>>
 */
function justccell_generic_brand_blocks_acf_rows(array $raw): array
{
    $rows = [];
    foreach (justccell_normalize_brand_blocks($raw['blocks'] ?? []) as $block) {
        $image_id = 0;
        $key = (string) ($block['image_key'] ?? '');
        if ($key !== '') {
            justccell_ensure_media_url($key);
            $image_id = justccell_media_id($key);
        }
        $rows[] = [
            'title'     => (string) ($block['title'] ?? ''),
            'title_tag' => (string) ($block['title_tag'] ?? 'h2'),
            'kicker'    => (string) ($block['kicker'] ?? ''),
            'copy'      => (string) ($block['copy'] ?? ''),
            'image'     => $image_id > 0 ? $image_id : '',
        ];
    }
    return $rows;
}

/**
 * @param array<string, mixed> $raw
 * @return list<array<string, mixed>>
 */
function justccell_generic_brand_cards_acf_rows(array $raw): array
{
    $rows = [];
    foreach (justccell_normalize_brand_cards($raw['cards'] ?? []) as $card) {
        $image_id = (int) ($card['image_id'] ?? 0);
        $key = (string) ($card['image_key'] ?? '');
        if ($image_id < 1 && $key !== '') {
            justccell_ensure_media_url($key);
            $image_id = justccell_media_id($key);
        }
        $rows[] = [
            'title'      => (string) ($card['title'] ?? ''),
            'title_tag'  => (string) ($card['title_tag'] ?? 'h2'),
            'copy'       => (string) ($card['copy'] ?? ''),
            'url'        => (string) ($card['url'] ?? '/'),
            'more_label' => (string) ($card['more_label'] ?? ''),
            'image'      => $image_id > 0 ? $image_id : '',
        ];
    }
    return $rows;
}

function justccell_generic_brand_seed_page(int $post_id, string $slug, array $raw): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }

    justccell_acf_seed_text_if_empty('brand_kicker', (string) ($raw['kicker'] ?? ''), $post_id);
    justccell_acf_seed_text_if_empty('brand_title', (string) ($raw['title'] ?? ''), $post_id);
    justccell_acf_seed_text_if_empty('brand_lede', (string) ($raw['lede'] ?? ''), $post_id);
    justccell_acf_seed_text_if_empty('brand_title_tag', 'h1', $post_id);
    justccell_acf_seed_text_if_empty('brand_video_heading', (string) ($raw['video_heading'] ?? ''), $post_id);
    justccell_acf_seed_text_if_empty('brand_video_copy', (string) ($raw['video_copy'] ?? ''), $post_id);
    justccell_acf_seed_text_if_empty('brand_video_heading_tag', 'h2', $post_id);
    if ($slug !== 'laser-engraving') {
        justccell_acf_seed_text_if_empty('brand_cta_title_tag', 'h2', $post_id);
    } elseif (function_exists('justccell_laser_page_seed_layout')) {
        justccell_laser_page_seed_layout($post_id, $raw);
    }

    $image_key = (string) ($raw['image'] ?? '');
    if ($image_key !== '') {
        justccell_acf_seed_image_if_empty('brand_image', $image_key, $post_id);
    }

    $sections = get_field('brand_sections', $post_id);
    if (!is_array($sections) || $sections === []) {
        $section_rows = justccell_generic_brand_sections_acf_rows($raw);
        if ($section_rows !== []) {
            update_field('brand_sections', $section_rows, $post_id);
        }
    }

    $blocks = get_field('brand_blocks', $post_id);
    if (!is_array($blocks) || $blocks === []) {
        $block_rows = justccell_generic_brand_blocks_acf_rows($raw);
        if ($block_rows !== []) {
            update_field('brand_blocks', $block_rows, $post_id);
        }
    }

    $cards = get_field('brand_cards', $post_id);
    if (!is_array($cards) || $cards === []) {
        $card_rows = justccell_generic_brand_cards_acf_rows($raw);
        if ($card_rows !== []) {
            update_field('brand_cards', $card_rows, $post_id);
        }
    }

    if ((int) get_field('brand_video', $post_id) < 1) {
        $video_key = (string) ($raw['video'] ?? '');
        if ($video_key !== '') {
            justccell_ensure_media_url($video_key);
            $video_id = justccell_media_id($video_key);
            if ($video_id > 0) {
                update_field('brand_video', $video_id, $post_id);
            }
        }
    }
}

function justccell_generic_brand_seed_pages_acf_content(): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }

    if (get_option('justccell_gbrand_acf_seeded_initial') === '1') {
        return;
    }

    $static = justccell_static_pages();
    foreach (justccell_generic_brand_page_slugs() as $slug) {
        $page = get_page_by_path($slug);
        if (!$page instanceof WP_Post) {
            continue;
        }
        $raw = $static[$slug] ?? [];
        if ($raw === []) {
            continue;
        }
        justccell_generic_brand_seed_page((int) $page->ID, $slug, $raw);
    }

    update_option('justccell_gbrand_acf_seeded_initial', '1', false);
}

add_action('init', 'justccell_home_seed_page_acf_content', 22);
add_action('init', 'justccell_listing_seed_pages_acf_content', 22);
add_action('init', 'justccell_generic_brand_seed_pages_acf_content', 22);
