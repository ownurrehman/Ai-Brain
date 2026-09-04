<?php
/**
 * ACF Pro field groups — 1:1 with front-end sections.
 * Location: real Pages / Products. Template dropdown stays Default.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('acf/init', static function (): void {
    if (!function_exists('acf_add_local_field_group')) {
        return;
    }

    foreach (
        [
            'justccell_register_acf_about_page',
            'justccell_register_acf_why_pages',
            'justccell_register_acf_bio_heating_page',
            'justccell_register_acf_generic_brand_pages',
            'justccell_register_acf_laser_page',
            'justccell_register_acf_locations_page',
            'justccell_register_acf_legal_pages',
            'justccell_register_acf_discover_page',
            'justccell_register_acf_homepage',
            'justccell_register_acf_listing_pages',
            'justccell_register_acf_contact_page',
            'justccell_register_acf_product_clone',
            'justccell_register_acf_header_menu',
            'justccell_register_acf_storefront',
        ] as $register
    ) {
        if (function_exists($register)) {
            $register();
        }
    }
});


function justccell_register_acf_discover_page(): void
{
    $location = justccell_acf_location_pages(['discover']);
    $location[] = [
        [
            'param'    => 'page_type',
            'operator' => '==',
            'value'    => 'posts_page',
        ],
    ];

    justccell_acf_register_field_group([
        'key'                   => 'group_jc_discover_hub',
        'title'                 => 'Discover hub',
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        'active'                => true,
        'location'              => $location,
        'fields'                => [

            [
                'key'     => 'field_jc_discover_title',
                'label'   => 'Hero title',
                'name'    => 'discover_title',
                'type'    => 'text',
                'wrapper' => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_discover_title_tag', 'discover_title_tag', 'h1', 'Title tag'),
            [
                'key'   => 'field_jc_discover_lede',
                'label' => 'Hero subtitle (optional)',
                'name'  => 'discover_lede',
                'type'  => 'textarea',
                'rows'  => 2,
            ],
            [
                'key'           => 'field_jc_discover_image',
                'label'         => 'Hero image',
                'name'          => 'discover_image',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_discover_image_mobile',
                'label'         => 'Hero image (mobile)',
                'name'          => 'discover_image_mobile',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'library'       => 'all',
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'          => 'field_jc_discover_intro',
                'label'        => 'Intro (above the post grid)',
                'name'         => 'discover_intro',
                'type'         => 'wysiwyg',
                'tabs'         => 'all',
                'toolbar'      => 'full',
                'media_upload' => 1,
                'delay'        => 0,
                'instructions' => 'Do not add an H1 here — the hero title is the page H1.',
            ],
            [
                'key'     => 'field_jc_discover_tab_all',
                'label'   => 'Tab: All',
                'name'    => 'discover_tab_all',
                'type'    => 'text',
                'default_value' => 'All',
                'wrapper' => ['width' => '25'],
            ],
            [
                'key'     => 'field_jc_discover_tab_guides',
                'label'   => 'Tab: Guides',
                'name'    => 'discover_tab_guides',
                'type'    => 'text',
                'default_value' => 'Guides',
                'wrapper' => ['width' => '25'],
            ],
            [
                'key'     => 'field_jc_discover_tab_news',
                'label'   => 'Tab: News',
                'name'    => 'discover_tab_news',
                'type'    => 'text',
                'default_value' => 'News',
                'wrapper' => ['width' => '25'],
            ],
            [
                'key'     => 'field_jc_discover_tab_blogs',
                'label'   => 'Tab: Blogs',
                'name'    => 'discover_tab_blogs',
                'type'    => 'text',
                'default_value' => 'Blogs',
                'wrapper' => ['width' => '25'],
            ],
        ],
    ]);
}

function justccell_register_acf_homepage(): void
{
    justccell_acf_register_field_group(justccell_acf_home_page_group());
}

function justccell_register_acf_listing_pages(): void
{
    justccell_acf_register_field_group(justccell_acf_listing_page_group());
}

function justccell_acf_contact_page_group(): array
{
    return [
        'key'    => 'group_jc_contact_page',
        'title'  => 'Contact page content',
        'fields' => [
            ['key' => 'field_jc_contact_hero_tab', 'label' => 'Hero', 'type' => 'tab'],
            [
                'key'           => 'field_jc_contact_hero_title',
                'label'         => 'Hero title',
                'name'          => 'contact_hero_title',
                'type'          => 'text',
                'default_value' => 'Contact us',
                'instructions'  => 'Shown as the large title on the hero image.',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_contact_hero_tag', 'contact_hero_title_tag', 'h1', 'Title tag'),
            [
                'key'           => 'field_jc_contact_hero_desktop',
                'label'         => 'Desktop hero image',
                'name'          => 'contact_hero_desktop',
                'type'          => 'image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_contact_hero_mobile',
                'label'         => 'Mobile hero image',
                'name'          => 'contact_hero_mobile',
                'type'          => 'image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '50'],
            ],
            ['key' => 'field_jc_contact_main_tab', 'label' => 'Contact information', 'type' => 'tab'],
            [
                'key'           => 'field_jc_contact_logo',
                'label'         => 'Section logo',
                'name'          => 'contact_logo',
                'type'          => 'image',
                'return_format' => 'id',
                'preview_size'  => 'medium',
                'instructions'  => 'Justccell wordmark for the grey contact panel. Leave empty to use the site logo.',
            ],
            [
                'key'           => 'field_jc_contact_info_heading',
                'label'         => 'Information heading',
                'name'          => 'contact_info_heading',
                'type'          => 'text',
                'default_value' => 'Contact Information',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_contact_info_tag', 'contact_info_heading_tag', 'h2'),
            [
                'key'           => 'field_jc_contact_sales_label',
                'label'         => 'Purchase email label',
                'name'          => 'contact_sales_label',
                'type'          => 'text',
                'default_value' => 'Purchase Inquiry:',
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'     => 'field_jc_contact_sales_email',
                'label'   => 'Public purchase email',
                'name'    => 'contact_sales_email',
                'type'    => 'email',
                'instructions' => 'Shown if Public emails below is empty.',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_contact_support_label',
                'label'         => 'Support email label',
                'name'          => 'contact_support_label',
                'type'          => 'text',
                'default_value' => 'Justccell Support:',
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'     => 'field_jc_contact_support_email',
                'label'   => 'Public support email',
                'name'    => 'contact_support_email',
                'type'    => 'email',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_contact_phone_label',
                'label'         => 'Phone label',
                'name'          => 'contact_phone_label',
                'type'          => 'text',
                'default_value' => 'Tel:',
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'     => 'field_jc_contact_phone',
                'label'   => 'Public phone number',
                'name'    => 'contact_phone',
                'type'    => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_contact_address_label',
                'label'         => 'Address label',
                'name'          => 'contact_address_label',
                'type'          => 'text',
                'default_value' => 'Address:',
                'wrapper'       => ['width' => '30'],
            ],
            [
                'key'           => 'field_jc_contact_address',
                'label'         => 'Address',
                'name'          => 'contact_address',
                'type'          => 'textarea',
                'rows'          => 4,
                'default_value' => "112 - 116 Hamill House\nChorley New Road,\nBolton,\nBL1 4DH",
                'instructions'  => 'One line per row. Shown under phone on the Contact page.',
                'wrapper'       => ['width' => '70'],
            ],
            [
                'key'          => 'field_jc_contact_emails',
                'label'        => 'Public emails',
                'name'         => 'contact_emails',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add email',
                'instructions' => 'Add or remove public email lines. Leave empty to use the purchase/support fields above.',
                'sub_fields'   => [
                    [
                        'key'     => 'field_jc_contact_email_label',
                        'label'   => 'Label',
                        'name'    => 'label',
                        'type'    => 'text',
                        'wrapper' => ['width' => '40'],
                    ],
                    [
                        'key'     => 'field_jc_contact_email_value',
                        'label'   => 'Email',
                        'name'    => 'email',
                        'type'    => 'email',
                        'wrapper' => ['width' => '60'],
                    ],
                ],
            ],
            [
                'key'           => 'field_jc_contact_follow_heading',
                'label'         => 'Social heading',
                'name'          => 'contact_follow_heading',
                'type'          => 'text',
                'default_value' => 'Follow Us',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_contact_follow_tag', 'contact_follow_heading_tag', 'h2'),
            [
                'key'          => 'field_jc_contact_social',
                'label'        => 'Social links',
                'name'         => 'contact_social',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add social link',
                'instructions' => 'Add, reorder, or remove Follow Us links. Leave empty to use Justccell → Storefront URLs. Upload an icon to replace the default mark.',
                'sub_fields'   => [
                    [
                        'key'     => 'field_jc_contact_social_label',
                        'label'   => 'Label',
                        'name'    => 'label',
                        'type'    => 'text',
                        'wrapper' => ['width' => '25'],
                    ],
                    [
                        'key'     => 'field_jc_contact_social_url',
                        'label'   => 'URL',
                        'name'    => 'url',
                        'type'    => 'url',
                        'required'=> 1,
                        'wrapper' => ['width' => '35'],
                    ],
                    [
                        'key'           => 'field_jc_contact_social_network',
                        'label'         => 'Icon',
                        'name'          => 'network',
                        'type'          => 'select',
                        'allow_null'    => 0,
                        'default_value' => 'instagram',
                        'choices'       => [
                            'instagram' => 'Instagram',
                            'youtube'   => 'YouTube',
                            'linkedin'  => 'LinkedIn',
                            'facebook'  => 'Facebook',
                            'x'         => 'X',
                            'link'      => 'Generic link',
                        ],
                        'wrapper'       => ['width' => '20'],
                    ],
                    [
                        'key'           => 'field_jc_contact_social_icon',
                        'label'         => 'Custom icon',
                        'name'          => 'icon',
                        'type'          => 'image',
                        'return_format' => 'id',
                        'preview_size'  => 'thumbnail',
                        'wrapper'       => ['width' => '20'],
                    ],
                ],
            ],
            ['key' => 'field_jc_contact_form_tab', 'label' => 'Form section', 'type' => 'tab'],
            [
                'key'           => 'field_jc_contact_form_title',
                'label'         => 'Form heading',
                'instructions'  => 'Heading above the inquiry form.',
                'name'          => 'contact_form_title',
                'type'          => 'text',
                'default_value' => 'Contact us',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_contact_form_tag', 'contact_form_title_tag', 'h2'),
            [
                'key'           => 'field_jc_contact_form_copy',
                'label'         => 'Form introduction',
                'name'          => 'contact_form_copy',
                'type'          => 'textarea',
                'rows'          => 3,
                'default_value' => 'Please fill the form below to submit your inquiry, and a Justccell sales representative will contact you promptly.',
            ],
            ['key' => 'field_jc_contact_distributors_tab', 'label' => 'Distributors', 'type' => 'tab'],
            [
                'key'           => 'field_jc_contact_dist_heading',
                'label'         => 'Section heading',
                'name'          => 'contact_distributors_heading',
                'type'          => 'text',
                'default_value' => 'Our Distributors',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_contact_dist_tag', 'contact_distributors_heading_tag', 'h2'),
            [
                'key'          => 'field_jc_contact_distributors',
                'label'        => 'Distributor cards',
                'name'         => 'contact_distributors',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add distributor',
                'instructions' => 'These cards are the public distributor row. Empty list hides the section. Import seeds the three current logos once if this is empty.',
                'sub_fields'   => [
                    [
                        'key'      => 'field_jc_contact_distributor_name',
                        'label'    => 'Name',
                        'name'     => 'name',
                        'type'     => 'text',
                        'required' => 1,
                        'wrapper'  => ['width' => '35'],
                    ],
                    [
                        'key'     => 'field_jc_contact_distributor_url',
                        'label'   => 'Website',
                        'name'    => 'url',
                        'type'    => 'url',
                        'wrapper' => ['width' => '35'],
                    ],
                    [
                        'key'           => 'field_jc_contact_distributor_image',
                        'label'         => 'Logo/image',
                        'name'          => 'image',
                        'type'          => 'image',
                        'return_format' => 'id',
                        'preview_size'  => 'thumbnail',
                        'wrapper'       => ['width' => '30'],
                    ],
                ],
            ],
            ['key' => 'field_jc_contact_faq_tab', 'label' => 'FAQ', 'type' => 'tab'],
            [
                'key'           => 'field_jc_contact_faq_heading',
                'label'         => 'Section heading',
                'name'          => 'contact_faq_heading',
                'type'          => 'text',
                'default_value' => 'FAQ',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_contact_faq_tag', 'contact_faq_heading_tag', 'h2'),
            [
                'key'          => 'field_jc_contact_faq',
                'label'        => 'FAQ',
                'name'         => 'contact_faq',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add FAQ',
                'sub_fields'   => [
                    [
                        'key'  => 'field_jc_contact_faq_q',
                        'label'=> 'Question',
                        'name' => 'q',
                        'type' => 'text',
                    ],
                    [
                        'key'  => 'field_jc_contact_faq_a',
                        'label'=> 'Answer',
                        'name' => 'a',
                        'type' => 'textarea',
                        'rows' => 3,
                    ],
                ],
            ],
        ],
        'location'      => [[
            [
                'param'    => 'page_template',
                'operator' => '==',
                'value'    => 'page-templates/justccell-contact.php',
            ],
        ]],
        'menu_order' => 0,
        'position'   => 'acf_after_title',
        'style'      => 'default',
        'active'     => true,
        'hide_on_screen' => ['the_content'],
    ];
}

function justccell_acf_contact_page_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = function_exists('justccell_acf_build_field_map')
        ? justccell_acf_build_field_map(justccell_acf_contact_page_group()['fields'] ?? [])
        : [];
    return $map;
}

function justccell_register_acf_contact_page(): void
{
    justccell_acf_register_field_group(justccell_acf_contact_page_group());
}


/**
 * Product editor field group — page content only.
 * Price, SKU, weight, colours, gallery fallbacks → WooCommerce product fields.
 * Field names stay clone_* so existing postmeta keeps working.
 *
 * @return array<string, mixed>
 */
function justccell_acf_product_clone_group(): array
{
    return [
        'key'    => 'group_jc_product_clone',
        'title'  => 'Product page',
        'fields' => [
            [
                'key'           => 'field_jc_prod_banner',
                'label'         => 'Banner image',
                'name'          => 'clone_banner',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
                'instructions'  => 'Hero background only (no overlay heading). Empty uses the WooCommerce product image.',
            ],
            [
                'key'          => 'field_jc_prod_product_heading',
                'label'        => 'Product heading',
                'name'         => 'clone_product_heading',
                'type'         => 'text',
                'wrapper'      => ['width' => '50'],
                'instructions' => 'Sole page H1. Leave empty to use the WooCommerce product name.',
            ],
            [
                'key'          => 'field_jc_prod_subtitle',
                'label'        => 'Product Tagline',
                'name'         => 'clone_subtitle',
                'type'         => 'text',
                'wrapper'      => ['width' => '50'],
                'instructions' => 'Accent line under the product heading, rendered as H2. Leave empty to hide it. Full product copy belongs in the WooCommerce Product description box (supports H2, H3, and lists).',
            ],
            [
                'key'          => 'field_jc_prod_specs_heading',
                'label'        => 'Specs section title',
                'name'         => 'clone_specs_heading',
                'type'         => 'text',
                'default_value'=> 'Specifications',
                'instructions' => 'Rendered as H3 above the specs list. Leave empty to hide the title (the list still shows).',
            ],
            [
                'key'          => 'field_jc_prod_specs',
                'label'        => 'Specs',
                'name'         => 'clone_specs',
                'type'         => 'repeater',
                'layout'       => 'table',
                'button_label' => 'Add line',
                'instructions' => 'Specification lines under the H3 title. Frontend outputs a semantic unordered list for SEO scraping (capacity, voltage, size, …).',
                'sub_fields'   => [
                    [
                        'key'   => 'field_jc_prod_spec_line',
                        'label' => 'Line',
                        'name'  => 'line',
                        'type'  => 'text',
                    ],
                ],
            ],
            [
                'key'           => 'field_jc_prod_spin',
                'label'         => '360 images (optional)',
                'name'          => 'clone_spin',
                'type'          => 'gallery',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
                'height'        => 168,
                'min'           => 0,
                'insert'        => 'append',
                'instructions'  => 'Drag into rotation order. Leave empty to skip 360. Use the WooCommerce Product Gallery for standard photos.',
            ],
            [
                'key'          => 'field_jc_prod_features',
                'label'        => 'Highlight slides (vertical scroll)',
                'name'         => 'clone_features',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add slide',
                'collapsed'    => 'field_jc_prod_feat_title',
                'instructions' => 'Full-screen slides that change as the visitor scrolls. A photo is enough — heading and text are optional when the image already includes them.',
                'sub_fields'   => [
                    [
                        'key'   => 'field_jc_prod_feat_title',
                        'label' => 'Heading',
                        'name'  => 'title',
                        'type'  => 'text',
                    ],
                    [
                        'key'   => 'field_jc_prod_feat_copy',
                        'label' => 'Text',
                        'name'  => 'copy',
                        'type'  => 'textarea',
                        'rows'  => 4,
                    ],
                    justccell_acf_highlight_text_color_field('field_jc_prod_feat_text_color', 'text_color'),
                    [
                        'key'           => 'field_jc_prod_feat_image',
                        'label'         => 'Photo',
                        'name'          => 'image',
                        'type'          => 'image',
                        'return_format' => 'array',
                        'preview_size'  => 'medium',
                    ],
                ],
            ],
            [
                'key'           => 'field_jc_prod_details',
                'label'         => 'Extra detail photos (optional)',
                'name'          => 'clone_details',
                'type'          => 'gallery',
                'return_format' => 'array',
                'preview_size'  => 'thumbnail',
                'max'           => 3,
                'instructions'  => 'Wide photo strip under heating (max 3). First image is the large tile.',
            ],
            [
                'key'          => 'field_jc_prod_evomax_title',
                'label'        => 'Heating section heading (optional)',
                'name'         => 'clone_evomax_title',
                'type'         => 'text',
                'instructions' => 'e.g. EVOMAX Heating Core. Leave empty to hide this block.',
                'wrapper'      => ['width' => '50'],
            ],
            [
                'key'           => 'field_jc_prod_evomax_bg',
                'label'         => 'Heating background',
                'name'          => 'clone_evomax_bg',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'wrapper'       => ['width' => '50'],
            ],
            [
                'key'          => 'field_jc_prod_evomax_copy',
                'label'        => 'Heating text',
                'name'         => 'clone_evomax_copy',
                'type'         => 'textarea',
                'rows'         => 5,
                'instructions' => 'Shown over the heating background. Needs heading + background + text.',
            ],
            [
                'key'           => 'field_jc_prod_show_laser',
                'label'         => 'Show OEM laser engraving',
                'name'          => 'clone_show_laser',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 1,
                'instructions'  => 'Turn off to hide the laser section on this product. On = use storefront defaults unless you override heading/text below.',
            ],
            [
                'key'               => 'field_jc_prod_laser_heading',
                'label'             => 'Laser heading (optional)',
                'name'              => 'clone_laser_heading',
                'type'              => 'text',
                'placeholder'       => 'OEM laser engraving',
                'instructions'      => 'Leave empty to use the default storefront heading.',
                'conditional_logic' => [[[
                    'field'    => 'field_jc_prod_show_laser',
                    'operator' => '==',
                    'value'    => '1',
                ]]],
            ],
            [
                'key'               => 'field_jc_prod_laser_copy',
                'label'             => 'Laser text (optional)',
                'name'              => 'clone_laser_copy',
                'type'              => 'textarea',
                'rows'              => 3,
                'instructions'      => 'Leave empty to use the default storefront paragraph.',
                'conditional_logic' => [[[
                    'field'    => 'field_jc_prod_show_laser',
                    'operator' => '==',
                    'value'    => '1',
                ]]],
            ],
        ],
        'location' => [[
            [
                'param'    => 'post_type',
                'operator' => '==',
                'value'    => 'product',
            ],
        ]],
        'menu_order'            => 0,
        'position'              => 'acf_after_title',
        'style'                 => 'default',
        'label_placement'       => 'top',
        'instruction_placement' => 'label',
        // Keep WooCommerce Product description (the_content) editable — H2/H3/lists for SEO.
        'hide_on_screen'        => [],
        'active'                => true,
    ];
}

function justccell_register_acf_product_clone(): void
{
    justccell_acf_register_field_group(justccell_acf_product_clone_group());
}

function justccell_register_acf_header_menu(): void
{
    if (function_exists('acf_add_options_sub_page')) {
        acf_add_options_sub_page([
            'page_title'  => __('Header', 'justccell'),
            'menu_title'  => __('Header', 'justccell'),
            'parent_slug' => 'justccell',
            'menu_slug'   => 'justccell-header',
            'capability'  => 'edit_theme_options',
        ]);
    }

    justccell_acf_register_field_group([
        'key'    => 'group_jc_header_options',
        'title'  => 'Header CTA',
        'fields' => [

            [
                'key'           => 'field_jc_header_cta_label',
                'label'         => 'Header button label',
                'name'          => 'header_cta_label',
                'type'          => 'text',
                'default_value' => '',
                'instructions'  => 'Optional. Leave empty to hide the header button (recommended).',
            ],
            [
                'key'           => 'field_jc_header_cta_url',
                'label'         => 'Header button link',
                'name'          => 'header_cta_url',
                'type'          => 'page_link',
                'post_type'     => ['page'],
                'allow_null'    => 1,
            ],
            [
                'key'           => 'field_jc_header_mega_limit',
                'label'         => 'Product cards per tab',
                'name'          => 'header_mega_limit',
                'type'          => 'number',
                'default_value' => 5,
                'min'           => 1,
                'max'           => 8,
            ],
        ],
        'location' => [[
            [
                'param'    => 'options_page',
                'operator' => '==',
                'value'    => 'justccell-header',
            ],
        ]],
        'active' => true,
    ]);

    justccell_acf_register_field_group([
        'key'    => 'group_jc_header_menu_item',
        'title'  => 'Header item',
        'fields' => [
            [
                'key'           => 'field_jc_header_item_kind',
                'label'         => 'Item type',
                'name'          => 'header_item_kind',
                'type'          => 'select',
                'choices'       => [
                    'auto'          => 'Auto (product mega when children are WooCommerce categories; else text dropdown)',
                    'products_mega' => 'Products mega (category tabs + product cards)',
                    'dropdown'      => 'Text dropdown (1–3 levels of links)',
                    'link'          => 'Plain link (ignore submenu items)',
                ],
                'default_value' => 'auto',
                'return_format' => 'value',
            ],
            [
                'key'           => 'field_jc_header_mega_products',
                'label'         => 'Mega product cards',
                'name'          => 'mega_products',
                'type'          => 'relationship',
                'instructions'  => 'Set on each category tab under a Products mega parent (not on the parent). Pick products from that tab’s category, or leave empty to auto-fill featured SKUs. Drag to reorder. Max 8.',
                'post_type'     => ['product'],
                'filters'       => ['search'],
                'return_format' => 'id',
                'min'           => 0,
                'max'           => 8,
            ],
        ],
        'location' => [[
            [
                'param'    => 'nav_menu_item',
                'operator' => '==',
                'value'    => 'location/primary',
            ],
        ]],
        'active' => true,
    ]);
}

function justccell_register_acf_storefront(): void
{
    if (function_exists('acf_add_options_sub_page')) {
        acf_add_options_sub_page([
            'page_title'  => __('Storefront', 'justccell'),
            'menu_title'  => __('Storefront', 'justccell'),
            'parent_slug' => 'justccell',
            'menu_slug'   => 'justccell-storefront',
            'capability'  => 'edit_theme_options',
        ]);
    }

    justccell_acf_register_field_group([
        'key'    => 'group_jc_storefront',
        'title'  => 'Storefront',
        'fields' => [

            [
                'key'   => 'field_jc_store_social_tab',
                'label' => 'Social & chat',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_store_instagram',
                'label'         => 'Instagram URL',
                'name'          => 'store_instagram',
                'type'          => 'url',
                'default_value' => 'https://www.instagram.com/justccell',
            ],
            [
                'key'   => 'field_jc_store_whatsapp',
                'label' => 'WhatsApp URL',
                'name'  => 'store_whatsapp',
                'type'  => 'url',
                'instructions' => 'Full link, e.g. https://wa.me/44… — if empty, the green button still shows and opens Contact until you set a number.',
            ],
            [
                'key'   => 'field_jc_store_whatsapp_label',
                'label' => 'WhatsApp label',
                'name'  => 'store_whatsapp_label',
                'type'  => 'text',
                'default_value' => 'WhatsApp',
            ],
            [
                'key'   => 'field_jc_store_telegram',
                'label' => 'Telegram URL',
                'name'  => 'store_telegram',
                'type'  => 'url',
                'instructions' => 'Full link, e.g. https://t.me/username — if empty, the button still shows and opens Contact until you set a handle.',
            ],
            [
                'key'   => 'field_jc_store_telegram_label',
                'label' => 'Telegram label',
                'name'  => 'store_telegram_label',
                'type'  => 'text',
                'default_value' => 'Telegram',
            ],
            [
                'key'   => 'field_jc_store_youtube',
                'label' => 'YouTube URL',
                'name'  => 'store_youtube',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_jc_store_linkedin',
                'label' => 'LinkedIn URL',
                'name'  => 'store_linkedin',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_jc_store_facebook',
                'label' => 'Facebook URL',
                'name'  => 'store_facebook',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_jc_store_x',
                'label' => 'X / Twitter URL',
                'name'  => 'store_x',
                'type'  => 'url',
            ],
            [
                'key'   => 'field_jc_store_collect_tab',
                'label' => 'Collection',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_store_collect_on',
                'label'         => 'Show collection note on products',
                'name'          => 'store_collection_enabled',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 1,
            ],
            [
                'key'  => 'field_jc_store_collect_copy',
                'label'=> 'Collection copy',
                'name' => 'store_collection_copy',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'key'   => 'field_jc_store_buy_tab',
                'label' => 'Product buy box',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_store_buy_empty_tiers',
                'label'         => 'Empty tier message',
                'name'          => 'store_buy_empty_tiers',
                'type'          => 'text',
                'default_value' => 'Select options to see pricing for this combination.',
                'instructions'  => 'Shown on product pages when no wholesale tier matches the selected options.',
            ],
            [
                'key'   => 'field_jc_store_laser_tab',
                'label' => 'Laser video',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_store_laser_on',
                'label'         => 'Show laser block on product pages',
                'name'          => 'store_laser_on_products',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 1,
            ],
            [
                'key'           => 'field_jc_store_laser_heading',
                'label'         => 'Heading',
                'name'          => 'store_laser_heading',
                'type'          => 'text',
                'default_value' => 'Laser engraving',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_store_laser_htag', 'store_laser_heading_tag', 'h2'),
            [
                'key'  => 'field_jc_store_laser_copy',
                'label'=> 'Copy',
                'name' => 'store_laser_copy',
                'type' => 'textarea',
                'rows' => 3,
            ],
            [
                'key'   => 'field_jc_store_laser_cta',
                'label' => 'Button label',
                'name'  => 'store_laser_cta_label',
                'type'  => 'text',
                'default_value' => 'See laser engraving',
            ],
            [
                'key'   => 'field_jc_store_laser_url',
                'label' => 'Button link',
                'name'  => 'store_laser_cta_url',
                'type'  => 'url',
            ],
            [
                'key'           => 'field_jc_store_laser_video',
                'label'         => 'Site-wide laser video',
                'name'          => 'store_laser_video',
                'type'          => 'file',
                'return_format' => 'array',
                'mime_types'    => 'mp4,webm,mov',
                'instructions'  => 'Used on every product unless that product has its own file. Upload here or in Media → Library.',
            ],
            [
                'key'   => 'field_jc_store_footer_brand_tab',
                'label' => 'Footer branding',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_store_footer_logo',
                'label'         => 'Footer logo',
                'name'          => 'store_footer_logo',
                'type'          => 'image',
                'return_format' => 'array',
                'preview_size'  => 'medium',
                'instructions'  => 'Optional white/light logo for the blue footer band. Empty = same logo as the header (Appearance → Customize → Site Identity).',
            ],
            [
                'key'   => 'field_jc_store_footer_tab',
                'label' => 'Footer note',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_store_footer_note',
                'label'         => 'Footer legal line',
                'name'          => 'store_footer_note',
                'type'          => 'textarea',
                'rows'          => 3,
                'instructions'  => 'Shown under the copyright line. Leave empty for the default UK/EU wording (no California Prop 65).',
            ],
            [
                'key'           => 'field_jc_store_dev_credit',
                'label'         => 'Show “Website by Rank Ray” in the footer',
                'name'          => 'store_show_developer_credit',
                'type'          => 'true_false',
                'ui'            => 1,
                'default_value' => 1,
                'instructions'  => 'Theme credit linking to rankray.com. Source comments stay even if this is off.',
            ],
            [
                'key'   => 'field_jc_store_land_tab',
                'label' => 'Store landings',
                'type'  => 'tab',
            ],
            [
                'key'          => 'field_jc_store_landings',
                'label'        => 'Landings (Spain / Switzerland)',
                'name'         => 'store_landings',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add store landing',
                'instructions' => 'When enabled, that store’s homepage is a landing, not the UK product homepage. UK should stay off so justccell.com remains the order site.',
                'sub_fields'   => [
                    [
                        'key'           => 'field_jc_store_land_store',
                        'label'         => 'Store',
                        'name'          => 'store',
                        'type'          => 'select',
                        'choices'       => [
                            'es' => 'Spain (/es/ or /spain/)',
                            'ch' => 'Switzerland (/ch/ or /swiss/)',
                        ],
                        'return_format' => 'value',
                    ],
                    [
                        'key'           => 'field_jc_store_land_on',
                        'label'         => 'Use landing instead of catalogue homepage',
                        'name'          => 'enabled',
                        'type'          => 'true_false',
                        'ui'            => 1,
                        'default_value' => 1,
                    ],
                    [
                        'key'   => 'field_jc_store_land_kicker',
                        'label' => 'Kicker',
                        'name'  => 'kicker',
                        'type'  => 'text',
                    ],
                    [
                        'key'     => 'field_jc_store_land_title',
                        'label'   => 'Title',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '80'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_store_land_title_tag', 'title_tag', 'h1', 'Title tag'),
                    [
                        'key'     => 'field_jc_store_land_note',
                        'label'   => 'UK orders heading',
                        'name'    => 'note_heading',
                        'type'    => 'text',
                        'default_value' => 'Orders run through the UK site',
                        'wrapper' => ['width' => '80'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_store_land_note_tag', 'note_heading_tag', 'h2'),
                    [
                        'key'           => 'field_jc_store_land_note_copy',
                        'label'         => 'UK orders copy',
                        'name'          => 'note_copy',
                        'type'          => 'textarea',
                        'rows'          => 3,
                        'default_value' => 'justccell.com is the catalogue where customers request wholesale. This page is the Spain or Switzerland landing.',
                    ],
                    [
                        'key'  => 'field_jc_store_land_lede',
                        'label'=> 'Lede',
                        'name' => 'lede',
                        'type' => 'textarea',
                        'rows' => 3,
                    ],
                    [
                        'key'           => 'field_jc_store_land_image',
                        'label'         => 'Image',
                        'name'          => 'image',
                        'type'          => 'image',
                        'return_format' => 'array',
                    ],
                    [
                        'key'   => 'field_jc_store_land_cta',
                        'label' => 'Button label',
                        'name'  => 'cta_label',
                        'type'  => 'text',
                    ],
                    [
                        'key'   => 'field_jc_store_land_url',
                        'label' => 'Button URL',
                        'name'  => 'cta_url',
                        'type'  => 'url',
                        'instructions' => 'Usually the UK homepage (justccell.com with no /uk/).',
                    ],
                ],
            ],
        ],
        'location' => [[
            [
                'param'    => 'options_page',
                'operator' => '==',
                'value'    => 'justccell-storefront',
            ],
        ]],
        'active' => true,
    ]);
}
