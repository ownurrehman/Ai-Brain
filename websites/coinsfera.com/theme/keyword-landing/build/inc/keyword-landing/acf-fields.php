<?php
/**
 * ACF field registration for the Keyword Landing template.
 *
 * Field names are inherited from the group previously stored in the database
 * (group_cfkl_keyword_landing) so any content authored against them survives.
 *
 * WPML preferences use the ACFML scale: 0 = don't translate, 1 = copy,
 * 2 = translate, 3 = copy once. Repeater parents are copied while their
 * sub fields translate; ACFML 2.2.0+ is required for that to apply to
 * PHP-registered groups.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const CFKL_WPML_IGNORE      = 0;
const CFKL_WPML_COPY        = 1;
const CFKL_WPML_TRANSLATE   = 2;
const CFKL_WPML_COPY_ONCE   = 3;

/**
 * Build a field definition, applying the defaults every cfkl field shares.
 *
 * @param string $name  Field name, without the cfkl_ prefix.
 * @param string $label Admin label.
 * @param string $type  ACF field type.
 * @param int    $wpml  WPML translation preference.
 * @param array  $extra Additional ACF keys.
 * @return array
 */
function cfkl_field( $name, $label, $type = 'text', $wpml = CFKL_WPML_TRANSLATE, array $extra = array() ) {

	return array_merge(
		array(
			'key'                 => 'field_cfkl_' . $name,
			'label'               => $label,
			'name'                => 'cfkl_' . $name,
			'type'                => $type,
			'wpml_cf_preferences' => $wpml,
		),
		$extra
	);
}

/**
 * Build a repeater whose sub fields are translated individually.
 *
 * @param string $name       Field name, without the cfkl_ prefix.
 * @param string $label      Admin label.
 * @param array  $sub_fields Sub field definitions.
 * @param array  $extra      Additional ACF keys.
 * @return array
 */
function cfkl_repeater( $name, $label, array $sub_fields, array $extra = array() ) {

	return cfkl_field(
		$name,
		$label,
		'repeater',
		CFKL_WPML_COPY,
		array_merge(
			array(
				'layout'       => 'block',
				'button_label' => __( 'Add row', 'coinsfera' ),
				'sub_fields'   => $sub_fields,
			),
			$extra
		)
	);
}

/**
 * Build a repeater sub field.
 *
 * Sub field keys carry their own namespace. Without it a sub field such as the
 * steps repeater's "title" would generate field_cfkl_steps_title, colliding
 * with the top-level cfkl_steps_title field; ACF requires globally unique keys
 * and silently resolves the wrong field when two share one.
 *
 * @param string $parent Parent repeater name, without the cfkl_ prefix.
 * @param string $name   Sub field name.
 * @param string $label  Admin label.
 * @param string $type   ACF field type.
 * @param int    $wpml   WPML translation preference.
 * @param array  $extra  Additional ACF keys.
 * @return array
 */
function cfkl_sub_field( $parent, $name, $label, $type = 'text', $wpml = CFKL_WPML_TRANSLATE, array $extra = array() ) {

	return array_merge(
		array(
			'key'                 => 'field_cfkl_sub_' . $parent . '_' . $name,
			'label'               => $label,
			'name'                => $name,
			'type'                => $type,
			'wpml_cf_preferences' => $wpml,
		),
		$extra
	);
}

/**
 * Build an image sub field. Images are copied; WPML Media Translation owns them.
 *
 * @param string $parent Parent repeater name.
 * @param string $name   Sub field name.
 * @param string $label  Admin label.
 * @return array
 */
function cfkl_sub_image( $parent, $name, $label ) {

	return cfkl_sub_field(
		$parent,
		$name,
		$label,
		'image',
		CFKL_WPML_COPY,
		array(
			'return_format' => 'array',
			'preview_size'  => 'medium',
		)
	);
}

/**
 * Build a section tab.
 *
 * @param string $name  Tab name.
 * @param string $label Tab label.
 * @return array
 */
function cfkl_tab( $name, $label ) {

	return array(
		'key'                 => 'field_cfkl_tab_' . $name,
		'label'               => $label,
		'name'                => '',
		'type'                => 'tab',
		'placement'           => 'top',
		'wpml_cf_preferences' => CFKL_WPML_IGNORE,
	);
}

/**
 * Admin labels for the four designs.
 *
 * @return array<string, string>
 */
function cfkl_design_choices() {

	$choices = array();

	foreach ( cfkl_designs() as $slug => $design ) {
		$choices[ $slug ] = $design['label'];
	}

	return $choices;
}

/**
 * Coin options for the admin, taken from the live feed's coin list.
 *
 * @return array<string, string>
 */
function cfkl_coin_choices() {

	$choices = array();

	foreach ( cfkl_rate_coins() as $symbol => $coin ) {
		$choices[ $symbol ] = $symbol . ' - ' . $coin['label'];
	}

	return $choices;
}

/**
 * Assemble the full field list.
 *
 * @return array
 */
function cfkl_get_fields() {

	$image_extra = array(
		'return_format' => 'array',
		'preview_size'  => 'medium',
	);

	return array(

		cfkl_tab( 'design', __( 'Design', 'coinsfera' ) ),
		cfkl_field( 'design', __( 'Design', 'coinsfera' ), 'select', CFKL_WPML_COPY, array(
			'choices'       => cfkl_design_choices(),
			'default_value' => 'desk',
			'return_format' => 'value',
			'allow_null'    => 0,
			'instructions'  => __( 'Each design is a separate visual language with its own layout, type, colour and section order. All four read the same content below.', 'coinsfera' ),
		) ),

		cfkl_tab( 'banner', __( 'Hero', 'coinsfera' ) ),
		cfkl_field( 'banner_tagline', __( 'Hero eyebrow text', 'coinsfera' ), 'text', CFKL_WPML_TRANSLATE, array(
			'instructions' => __( 'Short line above the headline, e.g. "Istanbul OTC desk since 2015".', 'coinsfera' ),
		) ),
		cfkl_field( 'banner_heading', __( 'Hero headline', 'coinsfera' ), 'text' ),
		cfkl_field( 'banner_subtext', __( 'Hero subtext', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 3,
		) ),
		cfkl_field( 'banner_cta_label', __( 'Hero button label', 'coinsfera' ) ),
		cfkl_field( 'banner_cta_url', __( 'Hero button URL', 'coinsfera' ), 'url', CFKL_WPML_COPY_ONCE ),
		cfkl_field( 'hero_image', __( 'Hero image', 'coinsfera' ), 'image', CFKL_WPML_COPY, array_merge(
			$image_extra,
			array( 'instructions' => __( 'Largest image on the page. Preloaded as the LCP candidate.', 'coinsfera' ) )
		) ),
		cfkl_repeater( 'banner_stats', __( 'Hero stat strip', 'coinsfera' ), array(
			cfkl_sub_field( 'banner_stats', 'value', __( 'Value', 'coinsfera' ) ),
			cfkl_sub_field( 'banner_stats', 'label', __( 'Label', 'coinsfera' ) ),
		), array(
			'max'          => 4,
			'instructions' => __( 'Up to four proof points, e.g. "500+ / coins supported".', 'coinsfera' ),
		) ),

		cfkl_tab( 'card', __( 'Office / Google Maps', 'coinsfera' ) ),
		cfkl_field( 'banner_office_label', __( 'Office label', 'coinsfera' ), 'text', CFKL_WPML_TRANSLATE, array(
			'instructions' => __( 'Small line above the address, e.g. "Our Istanbul office".', 'coinsfera' ),
		) ),
		cfkl_field( 'banner_card_title', __( 'Office address', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows'         => 2,
			'instructions' => __( 'Street address shown in the hero.', 'coinsfera' ),
		) ),
		cfkl_field( 'banner_card_btn_label', __( 'Maps button label', 'coinsfera' ) ),
		cfkl_field( 'banner_card_btn_url', __( 'Google Business Profile URL', 'coinsfera' ), 'url', CFKL_WPML_COPY_ONCE, array(
			'instructions' => __( 'Link to the Google listing, not the website contact page.', 'coinsfera' ),
		) ),
		cfkl_field( 'banner_office_rating', __( 'Google rating', 'coinsfera' ), 'text', CFKL_WPML_COPY, array(
			'instructions' => __( 'Optional, e.g. "4.9". Shown beside the Maps button where the layout supports it.', 'coinsfera' ),
		) ),

		cfkl_tab( 'intro', __( 'Intro', 'coinsfera' ) ),
		cfkl_field( 'intro_title', __( 'Intro title', 'coinsfera' ) ),
		cfkl_field( 'intro_text', __( 'Intro text', 'coinsfera' ), 'wysiwyg', CFKL_WPML_TRANSLATE, array(
			'media_upload' => 0,
			'tabs'         => 'visual',
		) ),

		cfkl_tab( 'trust', __( 'Why us', 'coinsfera' ) ),
		cfkl_field( 'trust_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'trust_text', __( 'Section text', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 3,
		) ),
		cfkl_field( 'trust_image', __( 'Section image', 'coinsfera' ), 'image', CFKL_WPML_COPY, $image_extra ),
		cfkl_repeater( 'trust_points', __( 'Trust points', 'coinsfera' ), array(
			cfkl_sub_field( 'trust_points', 'title', __( 'Title', 'coinsfera' ) ),
			cfkl_sub_field( 'trust_points', 'desc', __( 'Description', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array( 'rows' => 2 ) ),
		) ),

		cfkl_tab( 'steps', __( 'Steps', 'coinsfera' ) ),
		cfkl_field( 'steps_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_repeater( 'steps', __( 'Steps', 'coinsfera' ), array(
			cfkl_sub_field( 'steps', 'title', __( 'Title', 'coinsfera' ) ),
			cfkl_sub_field( 'steps', 'desc', __( 'Description', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array( 'rows' => 2 ) ),
			cfkl_sub_image( 'steps', 'image', __( 'Icon', 'coinsfera' ) ),
		) ),

		cfkl_tab( 'requirements', __( 'Requirements', 'coinsfera' ) ),
		cfkl_field( 'req_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'req_text', __( 'Section text', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 3,
		) ),
		cfkl_repeater( 'req_cards', __( 'Requirement cards', 'coinsfera' ), array(
			cfkl_sub_field( 'req_cards', 'title', __( 'Title', 'coinsfera' ) ),
			cfkl_sub_field( 'req_cards', 'desc', __( 'Description', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array( 'rows' => 2 ) ),
			cfkl_sub_image( 'req_cards', 'image', __( 'Icon', 'coinsfera' ) ),
		) ),

		cfkl_tab( 'features', __( 'Features', 'coinsfera' ) ),
		cfkl_field( 'features_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_repeater( 'features', __( 'Feature items', 'coinsfera' ), array(
			cfkl_sub_field( 'features', 'title', __( 'Title', 'coinsfera' ) ),
			cfkl_sub_field( 'features', 'desc', __( 'Description', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array( 'rows' => 2 ) ),
			cfkl_sub_image( 'features', 'image', __( 'Icon', 'coinsfera' ) ),
		) ),

		cfkl_tab( 'services', __( 'Services', 'coinsfera' ) ),
		cfkl_field( 'services_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_repeater( 'services', __( 'Service items', 'coinsfera' ), array(
			cfkl_sub_field( 'services', 'title', __( 'Title', 'coinsfera' ) ),
			cfkl_sub_field( 'services', 'desc', __( 'Description', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array( 'rows' => 2 ) ),
			cfkl_sub_field( 'services', 'url', __( 'Link', 'coinsfera' ), 'url', CFKL_WPML_COPY_ONCE ),
			cfkl_sub_image( 'services', 'icon', __( 'Icon', 'coinsfera' ) ),
		), array(
			'instructions' => __( 'Internal cross-links. Translators can repoint each URL at its /ru/ or /tr/ equivalent.', 'coinsfera' ),
		) ),

		cfkl_tab( 'calc', __( 'Calculator', 'coinsfera' ) ),
		cfkl_field( 'calc_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'calc_text', __( 'Section text', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 2,
		) ),
		cfkl_field( 'calc_spread_buy', __( 'Buy spread %', 'coinsfera' ), 'number', CFKL_WPML_COPY, array(
			'default_value' => 1.5,
			'step'          => 0.1,
			'min'           => 0,
			'max'           => 15,
			'instructions'  => __( 'Added to the market rate when a visitor buys. The calculator quotes market rate plus this.', 'coinsfera' ),
		) ),
		cfkl_field( 'calc_spread_sell', __( 'Sell spread %', 'coinsfera' ), 'number', CFKL_WPML_COPY, array(
			'default_value' => 1.5,
			'step'          => 0.1,
			'min'           => 0,
			'max'           => 15,
			'instructions'  => __( 'Deducted from the market rate when a visitor sells.', 'coinsfera' ),
		) ),
		cfkl_field( 'calc_default_coin', __( 'Default coin', 'coinsfera' ), 'select', CFKL_WPML_COPY, array(
			'choices'       => cfkl_coin_choices(),
			'default_value' => 'BTC',
			'return_format' => 'value',
			'allow_null'    => 0,
		) ),
		cfkl_field( 'calc_default_currency', __( 'Default currency', 'coinsfera' ), 'select', CFKL_WPML_COPY, array(
			'choices'       => array(
				'usd' => 'USD',
				'eur' => 'EUR',
				'try' => 'TRY',
			),
			'default_value' => 'usd',
			'return_format' => 'value',
			'allow_null'    => 0,
			'instructions'  => __( 'Turkish pages usually want TRY here.', 'coinsfera' ),
		) ),
		cfkl_field( 'calc_whatsapp', __( 'WhatsApp number', 'coinsfera' ), 'text', CFKL_WPML_COPY, array(
			'instructions' => __( 'Digits only or with +; the quote button opens WhatsApp with the amount already written.', 'coinsfera' ),
		) ),
		cfkl_field( 'calc_cta_label', __( 'Quote button label', 'coinsfera' ) ),
		cfkl_field( 'calc_note', __( 'Small print', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows'         => 2,
			'instructions' => __( 'Shown under the calculator, e.g. that the rate is indicative until confirmed at the desk.', 'coinsfera' ),
		) ),

		cfkl_tab( 'rates', __( 'Rate board', 'coinsfera' ) ),
		cfkl_field( 'rates_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'rates_text', __( 'Section text', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 2,
		) ),
		cfkl_field( 'rates_coins', __( 'Coins on the board', 'coinsfera' ), 'checkbox', CFKL_WPML_COPY, array(
			'choices'       => cfkl_coin_choices(),
			'default_value' => array( 'BTC', 'USDT', 'ETH', 'XRP' ),
			'return_format' => 'value',
			'layout'        => 'horizontal',
		) ),

		cfkl_tab( 'coins', __( 'Coins we trade', 'coinsfera' ) ),
		cfkl_field( 'coins_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'coins_text', __( 'Section text', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 2,
		) ),
		cfkl_repeater( 'coins_list', __( 'Coins', 'coinsfera' ), array(
			cfkl_sub_field( 'coins_list', 'symbol', __( 'Ticker', 'coinsfera' ), 'text', CFKL_WPML_COPY ),
			cfkl_sub_field( 'coins_list', 'name', __( 'Name', 'coinsfera' ) ),
			cfkl_sub_field( 'coins_list', 'url', __( 'Link', 'coinsfera' ), 'url', CFKL_WPML_COPY_ONCE ),
		) ),

		cfkl_tab( 'compare', __( 'Comparison', 'coinsfera' ) ),
		cfkl_field( 'compare_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'compare_text', __( 'Section text', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 2,
		) ),
		cfkl_field( 'compare_col_us', __( 'Our column heading', 'coinsfera' ) ),
		cfkl_field( 'compare_col_b', __( 'Second column heading', 'coinsfera' ) ),
		cfkl_field( 'compare_col_c', __( 'Third column heading', 'coinsfera' ) ),
		cfkl_repeater( 'compare_rows', __( 'Comparison rows', 'coinsfera' ), array(
			cfkl_sub_field( 'compare_rows', 'label', __( 'What is being compared', 'coinsfera' ) ),
			cfkl_sub_field( 'compare_rows', 'us', __( 'Us', 'coinsfera' ) ),
			cfkl_sub_field( 'compare_rows', 'b', __( 'Second column', 'coinsfera' ) ),
			cfkl_sub_field( 'compare_rows', 'c', __( 'Third column', 'coinsfera' ) ),
		) ),

		cfkl_tab( 'fees', __( 'Fees', 'coinsfera' ) ),
		cfkl_field( 'fees_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'fees_text', __( 'Section text', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 2,
		) ),
		cfkl_repeater( 'fees_rows', __( 'Fee lines', 'coinsfera' ), array(
			cfkl_sub_field( 'fees_rows', 'label', __( 'Item', 'coinsfera' ) ),
			cfkl_sub_field( 'fees_rows', 'value', __( 'Amount', 'coinsfera' ) ),
			cfkl_sub_field( 'fees_rows', 'note', __( 'Note', 'coinsfera' ) ),
		) ),

		cfkl_tab( 'reviews', __( 'Reviews', 'coinsfera' ) ),
		cfkl_field( 'reviews_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'reviews_rating', __( 'Rating', 'coinsfera' ), 'text', CFKL_WPML_COPY, array(
			'instructions' => __( 'e.g. 4.9', 'coinsfera' ),
		) ),
		cfkl_field( 'reviews_count', __( 'Number of reviews', 'coinsfera' ), 'text', CFKL_WPML_COPY ),
		cfkl_field( 'reviews_url', __( 'Link to reviews', 'coinsfera' ), 'url', CFKL_WPML_COPY_ONCE ),
		cfkl_repeater( 'reviews_items', __( 'Quoted reviews', 'coinsfera' ), array(
			cfkl_sub_field( 'reviews_items', 'text', __( 'Review', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array( 'rows' => 3 ) ),
			cfkl_sub_field( 'reviews_items', 'name', __( 'Name', 'coinsfera' ), 'text', CFKL_WPML_COPY ),
			cfkl_sub_field( 'reviews_items', 'meta', __( 'Detail under the name', 'coinsfera' ) ),
		) ),

		cfkl_tab( 'office', __( 'Office', 'coinsfera' ) ),
		cfkl_field( 'office_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_field( 'office_text', __( 'Section text', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array(
			'rows' => 3,
		) ),
		cfkl_repeater( 'office_hours', __( 'Opening hours', 'coinsfera' ), array(
			cfkl_sub_field( 'office_hours', 'days', __( 'Days', 'coinsfera' ) ),
			cfkl_sub_field( 'office_hours', 'hours', __( 'Hours', 'coinsfera' ), 'text', CFKL_WPML_COPY ),
		) ),
		cfkl_repeater( 'office_directions', __( 'Getting here', 'coinsfera' ), array(
			cfkl_sub_field( 'office_directions', 'label', __( 'Mode', 'coinsfera' ) ),
			cfkl_sub_field( 'office_directions', 'desc', __( 'Directions', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array( 'rows' => 2 ) ),
		) ),
		cfkl_field( 'office_map', __( 'Google Maps embed URL', 'coinsfera' ), 'url', CFKL_WPML_COPY, array(
			'instructions' => __( 'The src from the Maps embed code. Loaded lazily, below the fold only.', 'coinsfera' ),
		) ),

		cfkl_tab( 'faq', __( 'FAQ', 'coinsfera' ) ),
		cfkl_field( 'faq_title', __( 'Section title', 'coinsfera' ) ),
		cfkl_repeater( 'faq_items', __( 'FAQ items', 'coinsfera' ), array(
			cfkl_sub_field( 'faq_items', 'title', __( 'Question', 'coinsfera' ) ),
			cfkl_sub_field( 'faq_items', 'desc', __( 'Answer', 'coinsfera' ), 'textarea', CFKL_WPML_TRANSLATE, array( 'rows' => 4 ) ),
		) ),
		cfkl_field( 'faq_schema', __( 'Output FAQPage schema', 'coinsfera' ), 'true_false', CFKL_WPML_COPY, array(
			'ui'            => 1,
			'default_value' => 1,
			'instructions'  => __( 'Emits FAQPage JSON-LD for the questions above. Turn off if another plugin already outputs FAQ schema on this page.', 'coinsfera' ),
		) ),

		cfkl_tab( 'cta', __( 'Closing CTA', 'coinsfera' ) ),
		cfkl_field( 'cta_title', __( 'CTA title', 'coinsfera' ) ),
		cfkl_field( 'cta_label', __( 'CTA button label', 'coinsfera' ) ),
		cfkl_field( 'cta_url', __( 'CTA button URL', 'coinsfera' ), 'url', CFKL_WPML_COPY_ONCE ),
	);
}

/**
 * Register the field group.
 */
function cfkl_register_field_group() {

	if ( ! function_exists( 'acf_add_local_field_group' ) ) {
		return;
	}

	acf_add_local_field_group( array(
		'key'                   => 'group_cfkl_keyword_landing',
		'title'                 => __( 'Keyword Landing Content', 'coinsfera' ),
		'fields'                => cfkl_get_fields(),
		'location'              => array(
			array(
				array(
					'param'    => 'page_template',
					'operator' => '==',
					'value'    => 'page-templates/template-keyword-landing.php',
				),
			),
		),
		'menu_order'            => 0,
		'position'              => 'acf_after_title',
		'style'                 => 'default',
		'label_placement'       => 'top',
		'instruction_placement' => 'label',
		'hide_on_screen'        => array( 'the_content' ),
		'active'                => true,
		'show_in_rest'          => true,
		'description'           => __( 'Sections for pages using the Keyword Landing template.', 'coinsfera' ),
	) );
}
/*
 * On this site another plugin triggers ACF's initialisation during
 * plugins_loaded, so acf/init and acf/include_fields have both already fired by
 * the time the theme loads. Hooking them alone would never run. Register
 * straight away when ACF is up, and fall back to the hook if it is not.
 */
if ( did_action( 'acf/include_fields' ) ) {
	cfkl_register_field_group();
} else {
	add_action( 'acf/include_fields', 'cfkl_register_field_group' );
}
