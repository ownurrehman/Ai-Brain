<?php
/**
 * Per-page ACF groups. Each screen only lists fields that render on that URL.
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_register_acf_about_page(): void
{
    justccell_acf_register_field_group(justccell_acf_about_page_group());
}

function justccell_register_acf_why_pages(): void
{
    justccell_acf_register_field_group(justccell_acf_why_page_group());
}

function justccell_register_acf_bio_heating_page(): void
{
    justccell_acf_register_field_group(justccell_acf_j3_page_group());
}

/**
 * Justccell 3.0 page — fields map 1:1 to template-parts/page/brand-bio-heating.php.
 *
 * @return array<string, mixed>
 */
function justccell_acf_j3_page_group(): array
{
    $d = justccell_j3_page_text_defaults();
    $banner_only = [[['field' => 'field_jc_j3_sec_type', 'operator' => '==', 'value' => 'banner']]];
    $split_only  = [[['field' => 'field_jc_j3_sec_type', 'operator' => '==', 'value' => 'split']]];

    return [
        'key'    => 'group_jc_j3_page',
        'title'  => 'Justccell 3.0 page',
        'fields' => [

            [
                'key'   => 'field_jc_j3_tab_hero',
                'label' => '① Hero (top banner)',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_j3_kicker',
                'label'         => 'Hero line 1',
                'name'          => 'j3_kicker',
                'type'          => 'text',
                'default_value' => $d['kicker'],
                'instructions'  => 'First line of the H1 on the hero (e.g. Justccell 3.0).',
            ],
            [
                'key'           => 'field_jc_j3_title_line',
                'label'         => 'Hero line 2',
                'name'          => 'j3_title_line',
                'type'          => 'text',
                'default_value' => $d['title_line'],
                'instructions'  => 'Second line of the H1 (e.g. Heating Core).',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_j3_title_tag', 'j3_title_tag', 'h1', 'Hero tag'),
            [
                'key'           => 'field_jc_j3_subtitle',
                'label'         => 'Hero subtitle',
                'name'          => 'j3_subtitle',
                'type'          => 'text',
                'default_value' => $d['subtitle'],
                'instructions'  => 'Line under the H1. Use | for a line break.',
            ],
            array_merge(justccell_acf_image_field('field_jc_j3_hero_desktop', 'j3_hero_desktop', 'Hero image (desktop)'), [
                'instructions' => 'Full-width hero background. Empty = theme default image.',
            ]),
            array_merge(justccell_acf_image_field('field_jc_j3_hero_mobile', 'j3_hero_mobile', 'Hero image (mobile)'), [
                'instructions' => 'Mobile hero. Empty = theme default image.',
            ]),
            [
                'key'   => 'field_jc_j3_tab_story',
                'label' => '② Story sections (middle)',
                'type'  => 'tab',
            ],
            [
                'key'          => 'field_jc_j3_sections',
                'label'        => 'Story sections',
                'name'         => 'j3_sections',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add section',
                'instructions' => 'Full-width banners and image+copy splits in page order. Pick type first — only relevant fields show.',
                'collapsed'    => 'field_jc_j3_sec_type',
                'sub_fields'   => [
                    [
                        'key'           => 'field_jc_j3_sec_type',
                        'label'         => 'Section type',
                        'name'          => 'type',
                        'type'          => 'select',
                        'choices'       => [
                            'banner' => 'Full-width banner (big headline + image)',
                            'split'  => 'Image + copy row',
                        ],
                        'default_value' => 'split',
                        'return_format' => 'value',
                        'wrapper'       => ['width' => '40'],
                    ],
                    [
                        'key'               => 'field_jc_j3_sec_reverse',
                        'label'             => 'Flip image to the right',
                        'name'              => 'reverse',
                        'type'              => 'true_false',
                        'ui'                => 1,
                        'wrapper'           => ['width' => '30'],
                        'conditional_logic' => $split_only,
                    ],
                    [
                        'key'               => 'field_jc_j3_sec_title',
                        'label'             => 'Banner headline',
                        'name'              => 'title',
                        'type'              => 'text',
                        'instructions'      => 'Banner only. Use | for a line break.',
                        'wrapper'           => ['width' => '80'],
                        'conditional_logic' => $banner_only,
                    ],
                    justccell_acf_heading_tag_field('field_jc_j3_sec_title_tag', 'title_tag', 'h2'),
                    [
                        'key'               => 'field_jc_j3_sec_heading',
                        'label'             => 'Split heading',
                        'name'              => 'heading',
                        'type'              => 'text',
                        'wrapper'           => ['width' => '80'],
                        'conditional_logic' => $split_only,
                    ],
                    justccell_acf_heading_tag_field('field_jc_j3_sec_heading_tag', 'heading_tag', 'h3'),
                    [
                        'key'               => 'field_jc_j3_sec_copy',
                        'label'             => 'Split body copy',
                        'name'              => 'copy',
                        'type'              => 'textarea',
                        'rows'              => 3,
                        'conditional_logic' => $split_only,
                    ],
                    justccell_acf_image_field('field_jc_j3_sec_desk', 'image_desktop', 'Image (desktop)'),
                    justccell_acf_image_field('field_jc_j3_sec_mob', 'image_mobile', 'Image (mobile)'),
                ],
            ],
            [
                'key'   => 'field_jc_j3_tab_products',
                'label' => '③ Product rail (tabs + cards)',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_j3_products_title',
                'label'         => 'Rail heading',
                'name'          => 'j3_products_title',
                'type'          => 'text',
                'default_value' => $d['products_title'],
                'instructions'  => 'H2 above All-In-Ones / Cartridges / Pod Systems tabs.',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_j3_products_tag', 'j3_products_title_tag', 'h2'),
            [
                'key'          => 'field_jc_j3_product_groups',
                'label'        => 'Product tabs',
                'name'         => 'j3_product_groups',
                'type'         => 'repeater',
                'layout'       => 'block',
                'min'          => 3,
                'max'          => 3,
                'button_label' => 'Add tab',
                'instructions' => 'Exactly three tabs: All-In-Ones, Cartridges, Pod Systems. Drag Woo products to reorder cards.',
                'sub_fields'   => [
                    [
                        'key'     => 'field_jc_j3_group_heading',
                        'label'   => 'Tab label',
                        'name'    => 'heading',
                        'type'    => 'text',
                        'wrapper' => ['width' => '50'],
                    ],
                    [
                        'key'           => 'field_jc_j3_group_key',
                        'label'         => 'Category',
                        'name'          => 'key',
                        'type'          => 'select',
                        'choices'       => [
                            'all-in-ones' => 'All-In-Ones',
                            'cartridge'   => 'Cartridges (510)',
                            'pod-system'  => 'Pod Systems',
                        ],
                        'default_value' => 'all-in-ones',
                        'return_format' => 'value',
                        'wrapper'       => ['width' => '50'],
                    ],
                    [
                        'key'           => 'field_jc_j3_group_products',
                        'label'         => 'Product cards',
                        'name'          => 'products',
                        'type'          => 'relationship',
                        'post_type'     => ['product'],
                        'filters'       => ['search'],
                        'return_format' => 'id',
                        'min'           => 0,
                        'max'           => 8,
                    ],
                ],
            ],
            [
                'key'   => 'field_jc_j3_tab_cta',
                'label' => '④ Footer CTA (bottom)',
                'type'  => 'tab',
            ],
            [
                'key'           => 'field_jc_j3_cta_title',
                'label'         => 'CTA heading',
                'name'          => 'j3_cta_title',
                'type'          => 'text',
                'default_value' => $d['cta_title'],
                'instructions'  => 'Heading above the contact button — not the product rail heading.',
                'wrapper'       => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_j3_cta_tag', 'j3_cta_title_tag', 'h2'),
            [
                'key'           => 'field_jc_j3_cta_copy',
                'label'         => 'CTA copy',
                'name'          => 'j3_cta_copy',
                'type'          => 'textarea',
                'rows'          => 2,
                'default_value' => $d['cta_copy'],
            ],
            [
                'key'           => 'field_jc_j3_cta_label',
                'label'         => 'CTA button label',
                'name'          => 'j3_cta_label',
                'type'          => 'text',
                'default_value' => $d['cta_label'],
            ],
            [
                'key'           => 'field_jc_j3_cta_url',
                'label'         => 'CTA button link',
                'name'          => 'j3_cta_url',
                'type'          => 'page_link',
                'post_type'     => ['page'],
                'allow_null'    => 1,
                'default_value' => $d['cta_url'] ?? '/contact/',
            ],
        ],
        'location'      => [[
            [
                'param'    => 'page_template',
                'operator' => '==',
                'value'    => 'page-templates/justccell-bio.php',
            ],
        ]],
        'menu_order'    => 0,
        'position'      => 'acf_after_title',
        'style'         => 'default',
        'label_placement' => 'top',
        'hide_on_screen' => ['the_content'],
        'active'        => true,
    ];
}

/**
 * Flatten Justccell 3.0 ACF defs for live UI sync.
 *
 * @return array<string, array<string, mixed>>
 */
function justccell_acf_j3_page_field_map(): array
{
    static $map = null;
    if (is_array($map)) {
        return $map;
    }
    $map = [];
    $walk = static function (array $fields) use (&$walk, &$map): void {
        foreach ($fields as $index => $field) {
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
    $walk(justccell_acf_j3_page_group()['fields'] ?? []);
    return $map;
}

function justccell_register_acf_generic_brand_pages(): void
{
    justccell_acf_register_field_group(justccell_acf_generic_brand_page_group());
}

function justccell_register_acf_laser_page(): void
{
    justccell_acf_register_field_group(justccell_acf_laser_page_group());
}

function justccell_register_acf_legal_pages(): void
{
    $location = justccell_acf_location_pages(justccell_legal_page_slugs());

    justccell_acf_register_field_group([
        'key'    => 'group_jc_legal_pages',
        'title'  => 'Legal page',
        'fields' => [

        ],
        'location'   => $location,
        'menu_order' => 0,
        'position'   => 'acf_after_title',
        'style'      => 'default',
        'active'     => true,
    ]);
}

function justccell_register_acf_locations_page(): void
{
    $location = justccell_acf_location_pages(function_exists('justccell_location_page_slugs') ? justccell_location_page_slugs() : ['location', 'locations']);

    justccell_acf_register_field_group([
        'key'      => 'group_jc_locations_page',
        'title'    => 'Location page',
        'fields'   => [

            [
                'key'   => 'field_jc_loc_tab_hero',
                'label' => 'Hero',
                'type'  => 'tab',
            ],
            [
                'key'   => 'field_jc_loc_kicker',
                'label' => 'Kicker',
                'name'  => 'brand_kicker',
                'type'  => 'text',
            ],
            [
                'key'     => 'field_jc_loc_title',
                'label'   => 'Hero title',
                'name'    => 'brand_title',
                'type'    => 'text',
                'wrapper' => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_loc_title_tag', 'brand_title_tag', 'h1', 'Title tag'),
            [
                'key'  => 'field_jc_loc_lede',
                'label'=> 'Lede',
                'name' => 'brand_lede',
                'type' => 'textarea',
                'rows' => 3,
            ],
            justccell_acf_image_field('field_jc_loc_image', 'brand_image', 'Hero image (desktop)'),
            justccell_acf_image_field('field_jc_loc_image_mobile', 'brand_image_mobile', 'Hero image (mobile)'),
            [
                'key'   => 'field_jc_loc_tab_places',
                'label' => 'Locations',
                'type'  => 'tab',
            ],
            [
                'key'          => 'field_jc_loc_items',
                'label'        => 'Location cards',
                'name'         => 'locations_items',
                'type'         => 'repeater',
                'layout'       => 'block',
                'button_label' => 'Add location',
                'instructions' => 'UK office only for now. Spain/EU will use a later domain — do not add extra country cards here yet. Maps auto-embed from the address unless you paste a custom embed URL.',
                'sub_fields'   => [
                    [
                        'key'     => 'field_jc_loc_item_country',
                        'label'   => 'Country / region label',
                        'name'    => 'country',
                        'type'    => 'text',
                        'instructions' => 'Shown above the heading, e.g. United Kingdom',
                        'wrapper' => ['width' => '50'],
                    ],
                    [
                        'key'     => 'field_jc_loc_item_title',
                        'label'   => 'Office heading',
                        'name'    => 'title',
                        'type'    => 'text',
                        'wrapper' => ['width' => '50'],
                    ],
                    justccell_acf_heading_tag_field('field_jc_loc_item_tag', 'title_tag', 'h2'),
                    [
                        'key'  => 'field_jc_loc_item_summary',
                        'label'=> 'Short description',
                        'name' => 'summary',
                        'type' => 'textarea',
                        'rows' => 3,
                        'instructions' => 'One or two sentences for buyers — not the raw address block.',
                    ],
                    justccell_acf_image_field('field_jc_loc_item_image', 'image', 'Photo (optional — used if no map)', '100'),
                    [
                        'key'   => 'field_jc_loc_item_soon',
                        'label' => 'Coming soon',
                        'name'  => 'coming_soon',
                        'type'  => 'true_false',
                        'ui'    => 1,
                    ],
                    [
                        'key'               => 'field_jc_loc_item_soon_label',
                        'label'             => 'Coming soon label',
                        'name'              => 'coming_soon_label',
                        'type'              => 'text',
                        'default_value'     => 'Coming soon',
                        'conditional_logic' => [
                            [
                                [
                                    'field'    => 'field_jc_loc_item_soon',
                                    'operator' => '==',
                                    'value'    => '1',
                                ],
                            ],
                        ],
                    ],
                    [
                        'key'          => 'field_jc_loc_item_address',
                        'label'        => 'Address',
                        'name'         => 'address',
                        'type'         => 'textarea',
                        'rows'         => 4,
                        'instructions' => 'One line per row. Used for the page and to build the map embed.',
                    ],
                    [
                        'key'  => 'field_jc_loc_item_hours',
                        'label'=> 'Hours / visits',
                        'name' => 'hours',
                        'type' => 'text',
                        'instructions' => 'e.g. Monday–Friday 9:00–17:00 or Visits by appointment',
                    ],
                    [
                        'key'           => 'field_jc_loc_item_phone_label',
                        'label'         => 'Phone label',
                        'name'          => 'phone_label',
                        'type'          => 'text',
                        'default_value' => 'Tel:',
                        'wrapper'       => ['width' => '30'],
                    ],
                    [
                        'key'     => 'field_jc_loc_item_phone',
                        'label'   => 'Phone',
                        'name'    => 'phone',
                        'type'    => 'text',
                        'wrapper' => ['width' => '70'],
                    ],
                    [
                        'key'   => 'field_jc_loc_item_email',
                        'label' => 'Email (optional)',
                        'name'  => 'email',
                        'type'  => 'email',
                    ],
                    [
                        'key'         => 'field_jc_loc_item_maps_embed',
                        'label'       => 'Google Maps embed URL (optional)',
                        'name'        => 'maps_embed_url',
                        'type'        => 'url',
                        'instructions'=> 'Leave empty to auto-embed from the address. Or paste the src URL from Google Maps → Share → Embed a map.',
                    ],
                    [
                        'key'   => 'field_jc_loc_item_map',
                        'label' => 'Directions link',
                        'name'  => 'map_url',
                        'type'  => 'url',
                        'instructions' => 'Google Maps directions or listing URL. Auto-filled from address if empty.',
                        'wrapper' => ['width' => '50'],
                    ],
                    [
                        'key'           => 'field_jc_loc_item_map_label',
                        'label'         => 'Directions button label',
                        'name'          => 'map_label',
                        'type'          => 'text',
                        'default_value' => 'Get directions',
                        'wrapper'       => ['width' => '50'],
                    ],
                    [
                        'key'         => 'field_jc_loc_item_gmb',
                        'label'       => 'Google Business Profile link',
                        'name'        => 'gmb_url',
                        'type'        => 'url',
                        'instructions'=> 'Paste the public Google Business / Maps listing URL (GMB). Shows a second button on the card.',
                    ],
                ],
            ],
            [
                'key'   => 'field_jc_loc_tab_cta',
                'label' => 'CTA',
                'type'  => 'tab',
            ],
            [
                'key'     => 'field_jc_loc_cta_title',
                'label'   => 'CTA heading',
                'name'    => 'brand_cta_title',
                'type'    => 'text',
                'wrapper' => ['width' => '80'],
            ],
            justccell_acf_heading_tag_field('field_jc_loc_cta_tag', 'brand_cta_title_tag', 'h2'),
            [
                'key'  => 'field_jc_loc_cta_copy',
                'label'=> 'CTA copy',
                'name' => 'brand_cta_copy',
                'type' => 'textarea',
                'rows' => 2,
            ],
            [
                'key'     => 'field_jc_loc_cta_label',
                'label'   => 'CTA button',
                'name'    => 'brand_cta_label',
                'type'    => 'text',
                'wrapper' => ['width' => '50'],
            ],
            [
                'key'     => 'field_jc_loc_cta_url',
                'label'   => 'CTA link',
                'name'    => 'brand_cta_url',
                'type'    => 'url',
                'wrapper' => ['width' => '50'],
            ],
        ],
        'location'   => $location,
        'menu_order' => 0,
        'position'   => 'acf_after_title',
        'style'      => 'default',
        'active'     => true,
    ]);
}
