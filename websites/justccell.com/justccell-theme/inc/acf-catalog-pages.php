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
 * Homepage ACF — tabs match template-parts/home/clone.php top to bottom.
 *
 * @return array<string, mixed>
 */
function justccell_acf_home_page_group(): array
{
    $d = justccell_home_page_text_defaults();

    return [
        'key'                   => 'group_jc_home_full',
        'title'                 => 'Homepage content',
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => ['the_content'],
        'active'                => true,
        'location'              => [[[
            'param'    => 'page_type',
            'operator' => '==',
            'value'    => 'front_page',
        ]]],
        'menu_order' => 0,
        'fields'     => [

            ['key' => 'field_jc_home_hero_tab', 'label' => 'Hero slider', 'type' => 'tab'],
            [
                'key'          => 'field_jc_home_hero_slides',
                'label'        => 'Hero slides',
                'name'         => 'home_hero_slides',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add slide',
                'instructions' => 'Full-width banner carousel. Link each slide to a product or landing page.',
                'sub_fields'   => [
                    [
                        'key'           => 'field_jc_home_slide_image',
                        'label'         => 'Image',
                        'name'          => 'image',
                        'type'          => 'image',
                        'return_format' => 'array',
                        'preview_size'  => 'medium',
                        'library'       => 'all',
                    ],
                    [
                        'key'   => 'field_jc_home_slide_url',
                        'label' => 'Link URL',
                        'name'  => 'url',
                        'type'  => 'url',
                    ],
                    [
                        'key'   => 'field_jc_home_slide_alt',
                        'label' => 'Alt text',
                        'name'  => 'alt',
                        'type'  => 'text',
                    ],
                ],
            ],
            ['key' => 'field_jc_home_devices_tab', 'label' => 'Device rails', 'type' => 'tab'],
            [
                'key'           => 'field_jc_home_devices_heading',
                'label'         => 'Section heading',
                'name'          => 'home_devices_heading',
                'type'          => 'text',
                'default_value' => $d['devices_heading'],
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_home_devices_tag', 'home_devices_heading_tag', $d['devices_heading_tag']),
            [
                'key'           => 'field_jc_home_tab_aio',
                'label'         => 'Tab: All-In-Ones',
                'name'          => 'home_tab_all_in_ones',
                'type'          => 'text',
                'default_value' => $d['tab_all_in_ones'],
                'wrapper'       => ['width' => '25'],
            ],
            [
                'key'           => 'field_jc_home_tab_cart',
                'label'         => 'Tab: Cartridges',
                'name'          => 'home_tab_cartridge',
                'type'          => 'text',
                'default_value' => $d['tab_cartridge'],
                'wrapper'       => ['width' => '25'],
            ],
            [
                'key'           => 'field_jc_home_tab_pod',
                'label'         => 'Tab: Pod Systems',
                'name'          => 'home_tab_pod_system',
                'type'          => 'text',
                'default_value' => $d['tab_pod_system'],
                'wrapper'       => ['width' => '25'],
            ],
            [
                'key'           => 'field_jc_home_tab_batt',
                'label'         => 'Tab: 510 Batteries',
                'name'          => 'home_tab_battery',
                'type'          => 'text',
                'default_value' => $d['tab_battery'],
                'wrapper'       => ['width' => '25'],
                'instructions'  => 'Product cards in each tab come from the catalog — edit products in WooCommerce.',
            ],
            ['key' => 'field_jc_home_custom_tab', 'label' => 'Customize section', 'type' => 'tab'],
            [
                'key'           => 'field_jc_home_custom_heading',
                'label'         => 'Heading (HTML: br allowed)',
                'name'          => 'home_custom_heading',
                'type'          => 'textarea',
                'rows'          => 2,
                'default_value' => $d['custom_heading'],
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_home_custom_htag', 'home_custom_heading_tag', $d['custom_heading_tag']),
            [
                'key'           => 'field_jc_home_custom_kicker',
                'label'         => 'Classic kicker',
                'name'          => 'home_custom_kicker',
                'type'          => 'text',
                'default_value' => $d['custom_kicker'],
            ],
            [
                'key'           => 'field_jc_home_custom_copy',
                'label'         => 'Classic copy',
                'name'          => 'home_custom_copy',
                'type'          => 'textarea',
                'rows'          => 3,
                'default_value' => $d['custom_copy'],
            ],
            [
                'key'           => 'field_jc_home_custom_images',
                'label'         => 'Classic images (4)',
                'name'          => 'home_custom_images',
                'type'          => 'gallery',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
            ],
            [
                'key'           => 'field_jc_home_premium_image',
                'label'         => 'Premium image',
                'name'          => 'home_premium_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            [
                'key'           => 'field_jc_home_premium_heading',
                'label'         => 'Premium heading',
                'name'          => 'home_premium_heading',
                'type'          => 'text',
                'default_value' => $d['premium_heading'],
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_home_premium_tag', 'home_premium_heading_tag', $d['premium_heading_tag']),
            [
                'key'           => 'field_jc_home_premium_copy',
                'label'         => 'Premium copy',
                'name'          => 'home_premium_copy',
                'type'          => 'textarea',
                'rows'          => 3,
                'default_value' => $d['premium_copy'],
            ],
            ['key' => 'field_jc_home_fill_tab', 'label' => 'Filling section', 'type' => 'tab'],
            [
                'key'           => 'field_jc_home_fill_heading',
                'label'         => 'Heading',
                'name'          => 'home_fill_heading',
                'type'          => 'text',
                'default_value' => $d['fill_heading'],
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_home_fill_tag', 'home_fill_heading_tag', $d['fill_heading_tag']),
            [
                'key'           => 'field_jc_home_fill_copy',
                'label'         => 'Copy',
                'name'          => 'home_fill_copy',
                'type'          => 'textarea',
                'rows'          => 3,
                'default_value' => $d['fill_copy'],
            ],
            [
                'key'           => 'field_jc_home_fill_image',
                'label'         => 'Image',
                'name'          => 'home_fill_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            [
                'key'           => 'field_jc_home_fill_link_label',
                'label'         => 'Link label',
                'name'          => 'home_fill_link_label',
                'type'          => 'text',
                'default_value' => $d['fill_link_label'],
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_home_fill_link_url',
                'label'         => 'Link URL',
                'name'          => 'home_fill_link_url',
                'type'          => 'url',
                'default_value' => $d['fill_link_url'],
                'wrapper'       => ['width' => '50'],
            ],
            ['key' => 'field_jc_home_laser_tab', 'label' => 'Laser engraving', 'type' => 'tab'],
            [
                'key'           => 'field_jc_home_trusted_heading',
                'label'         => 'Heading',
                'name'          => 'home_trusted_heading',
                'type'          => 'text',
                'default_value' => $d['trusted_heading'],
                'wrapper'       => ['width' => '80'],
                'instructions'  => 'Body copy and video come from the storefront laser offer — only the heading is editable here.',
            ],
            justccell_acf_heading_tag_field('field_jc_home_trusted_tag', 'home_trusted_heading_tag', $d['trusted_heading_tag']),
            [
                'key'           => 'field_jc_home_trusted_image',
                'label'         => 'Logo collage (legacy — not rendered)',
                'name'          => 'home_trusted_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
            ],
            ['key' => 'field_jc_home_assets_tab', 'label' => 'Shared assets', 'type' => 'tab'],
            [
                'key'           => 'field_jc_home_arrow',
                'label'         => '“More” arrow icon',
                'name'          => 'home_arrow_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
                'instructions'  => 'Small arrow beside “More” links in device rails and the filling CTA.',
            ],
        ],
    ];
}

/**
 * Catalog listing ACF — template-parts/catalog/clone.php.
 *
 * @return array<string, mixed>
 */
function justccell_acf_listing_page_group(): array
{
    $sample = justccell_listing_defaults()['all-in-ones'] ?? [
        'heading' => __('All-In-Ones', 'justccell'),
        'lede'    => '',
    ];

    return [
        'key'                   => 'group_jc_listing_page',
        'title'                 => 'Catalog listing content',
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => ['the_content'],
        'active'                => true,
        'location'              => justccell_acf_location_pages(justccell_listing_page_slugs()),
        'menu_order'            => 0,
        'fields'                => [

            ['key' => 'field_jc_list_hero_tab', 'label' => 'Hero', 'type' => 'tab'],
            [
                'key'           => 'field_jc_listing_heading',
                'label'         => 'Heading (over hero)',
                'name'          => 'listing_heading',
                'type'          => 'text',
                'default_value' => $sample['heading'],
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_listing_htag', 'listing_heading_tag', 'h1'),
            [
                'key'           => 'field_jc_listing_lede',
                'label'         => 'Lede',
                'name'          => 'listing_lede',
                'type'          => 'textarea',
                'rows'          => 3,
                'default_value' => $sample['lede'],
            ],
            [
                'key'          => 'field_jc_listing_hero_slides',
                'label'        => 'Hero slides',
                'name'         => 'listing_hero_slides',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add slide',
                'instructions' => 'Desktop + mobile image pair. One slide is enough for most categories.',
                'sub_fields'   => [
                    [
                        'key'           => 'field_jc_listing_slide_desktop',
                        'label'         => 'Desktop image',
                        'name'          => 'desktop',
                        'type'          => 'image',
                        'return_format' => 'array',
                        'preview_size'  => 'medium',
                        'wrapper'       => ['width' => '50'],
                    ],
                    [
                        'key'           => 'field_jc_listing_slide_mobile',
                        'label'         => 'Mobile image',
                        'name'          => 'mobile',
                        'type'          => 'image',
                        'return_format' => 'array',
                        'preview_size'  => 'medium',
                        'wrapper'       => ['width' => '50'],
                    ],
                    [
                        'key'   => 'field_jc_listing_slide_url',
                        'label' => 'Optional link',
                        'name'  => 'url',
                        'type'  => 'url',
                    ],
                ],
            ],
            ['key' => 'field_jc_list_faq_tab', 'label' => 'FAQ', 'type' => 'tab'],
            [
                'key'           => 'field_jc_listing_faq_heading',
                'label'         => 'FAQ heading',
                'name'          => 'listing_faq_heading',
                'type'          => 'text',
                'default_value' => 'FAQ',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_listing_faq_tag', 'listing_faq_heading_tag', 'h2'),
            [
                'key'          => 'field_jc_listing_faq',
                'label'        => 'FAQ items',
                'name'         => 'listing_faq',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add FAQ',
                'instructions' => 'Leave empty to hide the FAQ block on categories that do not need one.',
                'sub_fields'   => [
                    [
                        'key'  => 'field_jc_listing_faq_q',
                        'label'=> 'Question',
                        'name' => 'q',
                        'type' => 'text',
                    ],
                    [
                        'key'  => 'field_jc_listing_faq_a',
                        'label'=> 'Answer',
                        'name' => 'a',
                        'type' => 'textarea',
                        'rows' => 3,
                    ],
                ],
            ],
        ],
    ];
}

/**
 * Generic brand pages — template-parts/page/brand.php (+ laser variant).
 *
 * @return array<string, mixed>
 */
function justccell_acf_generic_brand_page_group(): array
{
    return [
        'key'                   => 'group_jc_generic_brand',
        'title'                 => 'Page content',
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'hide_on_screen'        => ['the_content'],
        'active'                => true,
        'location'              => justccell_acf_location_pages(justccell_generic_brand_page_slugs()),
        'menu_order'            => 0,
        'fields'                => [

            ['key' => 'field_jc_gbrand_hero_tab', 'label' => 'Hero', 'type' => 'tab'],
            [
                'key'  => 'field_jc_gbrand_kicker',
                'label'=> 'Kicker',
                'name' => 'brand_kicker',
                'type' => 'text',
            ],
            [
                'key'     => 'field_jc_gbrand_title',
                'label'   => 'Title',
                'name'    => 'brand_title',
                'type'    => 'text',
                'wrapper' => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_gbrand_title_tag', 'brand_title_tag', 'h1', 'Title tag'),
            [
                'key'  => 'field_jc_gbrand_lede',
                'label'=> 'Lede',
                'name' => 'brand_lede',
                'type' => 'textarea',
                'rows' => 3,
            ],
            justccell_acf_image_field('field_jc_gbrand_image', 'brand_image', 'Hero image'),
            justccell_acf_image_field('field_jc_gbrand_image_mobile', 'brand_image_mobile', 'Hero image (mobile — legacy, not rendered on this template)'),
            ['key' => 'field_jc_gbrand_sections_tab', 'label' => 'Text sections', 'type' => 'tab'],
            [
                'key'          => 'field_jc_gbrand_sections',
                'label'        => 'Text sections',
                'name'         => 'brand_sections',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add section',
                'instructions' => 'Full-width text bands below the hero image.',
                'sub_fields'   => [
                    [
                        'key'     => 'field_jc_gbrand_sec_id',
                        'label'   => 'Anchor ID (optional)',
                        'name'    => 'id',
                        'type'    => 'text',
                        'wrapper' => ['width' => '30'],
                    ],
                    [
                        'key'     => 'field_jc_gbrand_sec_title',
                        'label'   => 'Heading',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '50'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_gbrand_sec_tag', 'title_tag', 'h2'),
                    [
                        'key'  => 'field_jc_gbrand_sec_copy',
                        'label'=> 'Copy',
                        'name' => 'copy',
                        'type' => 'textarea',
                        'rows' => 3,
                    ],
                ],
            ],
            ['key' => 'field_jc_gbrand_blocks_tab', 'label' => 'Cards', 'type' => 'tab'],
            [
                'key'          => 'field_jc_gbrand_blocks',
                'label'        => 'Cards',
                'name'         => 'brand_blocks',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add card',
                'instructions' => 'Three-column card grid.',
                'sub_fields'   => [
                    justccell_acf_image_field('field_jc_gbrand_block_image', 'image', 'Image (optional)', '100'),
                    [
                        'key'     => 'field_jc_gbrand_block_title',
                        'label'   => 'Heading',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '80'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_gbrand_block_tag', 'title_tag', 'h2'),
                    [
                        'key'   => 'field_jc_gbrand_block_kicker',
                        'label' => 'Kicker (optional)',
                        'name'  => 'kicker',
                        'type'  => 'text',
                    ],
                    [
                        'key'  => 'field_jc_gbrand_block_copy',
                        'label'=> 'Copy',
                        'name' => 'copy',
                        'type' => 'textarea',
                        'rows' => 3,
                    ],
                ],
            ],
            ['key' => 'field_jc_gbrand_cards_tab', 'label' => 'Hub links', 'type' => 'tab'],
            [
                'key'          => 'field_jc_gbrand_cards',
                'label'        => 'Hub link cards',
                'name'         => 'brand_cards',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add link card',
                'instructions' => 'Linked cards with image, title, and path (e.g. /technology/).',
                'sub_fields'   => [
                    justccell_acf_image_field('field_jc_gbrand_card_image', 'image', 'Image (optional)', '100'),
                    [
                        'key'     => 'field_jc_gbrand_card_title',
                        'label'   => 'Heading',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '80'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_gbrand_card_tag', 'title_tag', 'h2'),
                    [
                        'key'  => 'field_jc_gbrand_card_copy',
                        'label'=> 'Copy',
                        'name' => 'copy',
                        'type' => 'textarea',
                        'rows' => 2,
                    ],
                    [
                        'key'         => 'field_jc_gbrand_card_url',
                        'label'       => 'Link path',
                        'name'        => 'url',
                        'type'        => 'text',
                        'placeholder' => '/choose-hardware/',
                    ],
                    [
                        'key'           => 'field_jc_gbrand_card_more',
                        'label'         => 'Link label',
                        'name'          => 'more_label',
                        'type'          => 'text',
                        'default_value' => 'View details',
                    ],
                ],
            ],
            ['key' => 'field_jc_gbrand_video_tab', 'label' => 'Video', 'type' => 'tab'],
            [
                'key'     => 'field_jc_gbrand_video_heading',
                'label'   => 'Video heading',
                'name'    => 'brand_video_heading',
                'type'    => 'text',
                'wrapper' => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_gbrand_video_tag', 'brand_video_heading_tag', 'h2'),
            [
                'key'  => 'field_jc_gbrand_video_copy',
                'label'=> 'Video copy',
                'name' => 'brand_video_copy',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'key'           => 'field_jc_gbrand_video',
                'label'         => 'Video file',
                'name'          => 'brand_video',
                'type'          => 'file',
                'return_format' => 'id',
                'library'       => 'all',
                'mime_types'    => 'mp4,webm,mov',
            ],
            ['key' => 'field_jc_gbrand_cta_tab', 'label' => 'Footer CTA', 'type' => 'tab'],
            [
                'key'           => 'field_jc_gbrand_cta_title',
                'label'         => 'CTA heading',
                'name'          => 'brand_cta_title',
                'type'          => 'text',
                'instructions'  => 'Optional footer band. Leave empty to hide.',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_gbrand_cta_tag', 'brand_cta_title_tag', 'h2'),
            [
                'key'           => 'field_jc_gbrand_cta_copy',
                'label'         => 'CTA copy',
                'name'          => 'brand_cta_copy',
                'type'          => 'textarea',
                'rows'          => 2,
            ],
            [
                'key'           => 'field_jc_gbrand_cta_label',
                'label'         => 'CTA button',
                'name'          => 'brand_cta_label',
                'type'          => 'text',
            ],
            [
                'key'         => 'field_jc_gbrand_cta_url',
                'label'       => 'CTA button link',
                'name'        => 'brand_cta_url',
                'type'        => 'text',
                'placeholder' => '/contact/',
                'instructions'=> 'Path or full URL. Defaults to the contact page when empty. On Laser engraving, leave the whole Footer CTA tab empty to hide the bottom band.',
            ],
        ],
    ];
}

/**
 * Laser engraving page — intro buttons and section headings (brand-laser.php).
 *
 * @return array<string, mixed>
 */
function justccell_acf_laser_page_group(): array
{
    return [
        'key'                   => 'group_jc_laser_page',
        'title'                 => 'Laser page layout',
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'menu_order'            => 1,
        'active'                => true,
        'location'              => justccell_acf_location_pages(['laser-engraving']),
        'fields'                => [
            ['key' => 'field_jc_laser_intro_tab', 'label' => 'Intro buttons', 'type' => 'tab'],
            [
                'key'           => 'field_jc_laser_intro_primary_label',
                'label'         => 'Primary button label',
                'name'          => 'brand_intro_primary_label',
                'type'          => 'text',
                'default_value' => __('Contact us', 'justccell'),
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'         => 'field_jc_laser_intro_primary_url',
                'label'       => 'Primary button link',
                'name'        => 'brand_intro_primary_url',
                'type'        => 'text',
                'placeholder' => '/contact/',
                'wrapper'     => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_laser_intro_secondary_label',
                'label'         => 'Secondary button label',
                'name'          => 'brand_intro_secondary_label',
                'type'          => 'text',
                'default_value' => __('Packaging', 'justccell'),
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'         => 'field_jc_laser_intro_secondary_url',
                'label'       => 'Secondary button link',
                'name'        => 'brand_intro_secondary_url',
                'type'        => 'text',
                'placeholder' => '/packaging/',
                'wrapper'     => ['width' => '50'],
                'instructions'=> 'Leave the label empty to hide the secondary button.',
            ],
            ['key' => 'field_jc_laser_steps_tab', 'label' => 'Steps section', 'type' => 'tab'],
            [
                'key'     => 'field_jc_laser_steps_note',
                'label'   => '',
                'type'    => 'message',
                'message' => '<p>Step rows (numbered list) are edited under <strong>Page content → Text sections</strong>.</p>',
            ],
            [
                'key'           => 'field_jc_laser_steps_heading',
                'label'         => 'Steps heading',
                'name'          => 'brand_steps_heading',
                'type'          => 'text',
                'default_value' => __('How to brief us', 'justccell'),
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_laser_steps_tag', 'brand_steps_heading_tag', 'h2'),
            [
                'key'           => 'field_jc_laser_steps_lede',
                'label'         => 'Steps intro copy',
                'name'          => 'brand_steps_lede',
                'type'          => 'textarea',
                'rows'          => 2,
                'default_value' => __('Artwork, colourway, and quantity sit on the same enquiry as the hardware. We proof a small batch before a production run.', 'justccell'),
            ],
            ['key' => 'field_jc_laser_hw_tab', 'label' => 'Hardware section', 'type' => 'tab'],
            [
                'key'     => 'field_jc_laser_hw_note',
                'label'   => '',
                'type'    => 'message',
                'message' => '<p>Hardware link cards are edited under <strong>Page content → Hub link cards</strong>.</p>',
            ],
            [
                'key'           => 'field_jc_laser_hw_heading',
                'label'         => 'Hardware heading',
                'name'          => 'brand_hardware_heading',
                'type'          => 'text',
                'default_value' => __('Hardware we mark', 'justccell'),
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_laser_hw_tag', 'brand_hardware_heading_tag', 'h2'),
            [
                'key'           => 'field_jc_laser_hw_lede',
                'label'         => 'Hardware intro copy',
                'name'          => 'brand_hardware_lede',
                'type'          => 'textarea',
                'rows'          => 2,
                'default_value' => __('Logos and micro text go on batteries, pods, and selected all-in-ones. Open a product to add engraving to your order.', 'justccell'),
            ],
        ],
    ];
}

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_laser_page_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = justccell_acf_build_field_map(justccell_acf_laser_page_group()['fields'] ?? []);
    return $map;
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
 * @param list<array<string, mixed>> $fields
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_build_field_map(array $fields): array
{
    $map = [];
    $walk = static function (array $items) use (&$walk, &$map): void {
        foreach ($items as $index => $field) {
            if (!is_array($field) || empty($field['key'])) {
                continue;
            }
            $field['_ui_order'] = (int) $index;
            $map[(string) $field['key']] = $field;
            $sub = $field['sub_fields'] ?? null;
            if (is_array($sub) && $sub !== []) {
                $walk($sub);
            }
        }
    };
    $walk($fields);
    return $map;
}

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_home_page_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = justccell_acf_build_field_map(justccell_acf_home_page_group()['fields'] ?? []);
    return $map;
}

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_listing_page_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = justccell_acf_build_field_map(justccell_acf_listing_page_group()['fields'] ?? []);
    return $map;
}

/**
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_generic_brand_page_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = justccell_acf_build_field_map(justccell_acf_generic_brand_page_group()['fields'] ?? []);
    return $map;
}

/**
 * @return list<array{q:string,a:string}>
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

    $seed_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
    if (get_option('justccell_home_acf_seeded') === $seed_ver) {
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

    update_option('justccell_home_acf_seeded', $seed_ver, false);
}

function justccell_listing_seed_pages_acf_content(): void
{
    if (!function_exists('get_field') || !function_exists('update_field')) {
        return;
    }

    $seed_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
    if (get_option('justccell_listing_acf_seeded') === $seed_ver) {
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

    update_option('justccell_listing_acf_seeded', $seed_ver, false);
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

    $seed_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1';
    if (get_option('justccell_gbrand_acf_seeded') === $seed_ver) {
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

    update_option('justccell_gbrand_acf_seeded', $seed_ver, false);
}

/**
 * Sync stored ACF field UI from PHP definitions (admin only).
 */
function justccell_acf_sync_group_field_ui(string $option_key, string $group_key, callable $group_fn, callable $map_fn): void
{
    $ui_ver = defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '';
    if (
        $ui_ver === ''
        || get_option($option_key) === $ui_ver
        || !function_exists('acf_get_field')
        || !function_exists('acf_update_field')
        || justccell_acf_field_group_post_id($group_key) < 1
    ) {
        return;
    }

    if (function_exists('acf_get_field_group') && function_exists('acf_update_field_group')) {
        $stored = acf_get_field_group($group_key);
        $src    = $group_fn();
        if (is_array($stored) && !empty($stored['ID']) && is_array($src)) {
            $stored['title']           = (string) ($src['title'] ?? $stored['title']);
            $stored['position']        = (string) ($src['position'] ?? 'acf_after_title');
            $stored['label_placement'] = (string) ($src['label_placement'] ?? 'top');
            $stored['hide_on_screen']  = $src['hide_on_screen'] ?? ['the_content'];
            if (!empty($src['location'])) {
                $stored['location'] = $src['location'];
            }
            acf_update_field_group($stored);
        }
    }

    foreach ($map_fn() as $key => $src) {
        $existing = acf_get_field($key);
        if (!is_array($existing) || empty($existing['ID'])) {
            continue;
        }
        foreach (['label', 'instructions', 'button_label', 'placeholder', 'wrapper', 'collapsed', 'placement', 'rows', 'message', 'default_value', 'conditional_logic', 'min', 'max'] as $prop) {
            if (array_key_exists($prop, $src)) {
                $existing[$prop] = $src[$prop];
            }
        }
        if (array_key_exists('_ui_order', $src)) {
            $existing['menu_order'] = (int) $src['_ui_order'];
        }
        unset($existing['sub_fields']);
        acf_update_field($existing);
    }

    update_option($option_key, $ui_ver, false);
}

add_action('init', 'justccell_home_seed_page_acf_content', 22);
add_action('init', 'justccell_listing_seed_pages_acf_content', 22);
add_action('init', 'justccell_generic_brand_seed_pages_acf_content', 22);
