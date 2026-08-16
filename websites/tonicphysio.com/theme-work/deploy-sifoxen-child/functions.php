<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Rankish Child (sifoxen-child) — Tonic Physio
 * v2.4.1 — Elementor-first assets + one-shot Elementor Performance seal.
 */

function sifoxen_child_theme_setup() {
	load_child_theme_textdomain( 'sifoxen-child', get_stylesheet_directory() . '/languages' );
}
add_action( 'after_setup_theme', 'sifoxen_child_theme_setup' );

/**
 * One-shot Elementor performance seal (v2.4.2).
 * Hero uses CSS background slideshow — lazy-load backgrounds delays LCP.
 * Inline SVG icons avoid loading full Elementor icon font CSS.
 * Flagged so it does not fight later manual toggles.
 */
add_action(
	'init',
	static function () {
		if ( get_option( 'tonic_elementor_perf_seal_v242' ) === '1' ) {
			return;
		}
		// Disable = '0' in Elementor Performance UI.
		update_option( 'elementor_lazy_load_background_images', '0' );
		update_option( 'elementor_experiment-e_font_icon_svg', 'active' );
		update_option( 'elementor_experiment-e_optimized_markup', 'active' );
		update_option( 'elementor_google_font', '0' );
		update_option( 'elementor_font_display', 'swap' );
		update_option( 'elementor_css_print_method', 'external' );
		update_option( 'tonic_elementor_perf_seal_v242', '1' );
		// Clear older seal flag so we do not leave stale markers.
		delete_option( 'tonic_elementor_perf_seal_v241' );

		if ( class_exists( '\Elementor\Plugin' ) ) {
			try {
				\Elementor\Plugin::$instance->files_manager->clear_cache();
			} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			}
		}
		if ( class_exists( 'LiteSpeed\Purge' ) ) {
			try {
				\LiteSpeed\Purge::purge_all();
			} catch ( \Throwable $e ) { // phpcs:ignore Generic.CodeAnalysis.EmptyStatement.DetectedCatch
			}
		}
	},
	5
);

/**
 * Whether current singular view is an Elementor-built document.
 */
function tonic_is_elementor_document( $post_id = 0 ) {
	$post_id = $post_id ? (int) $post_id : (int) get_queried_object_id();
	if ( ! $post_id || ! class_exists( '\Elementor\Plugin' ) ) {
		return false;
	}
	$document = \Elementor\Plugin::$instance->documents->get( $post_id );
	return $document && $document->is_built_with_elementor();
}

/**
 * Styles: Elementor full pages do not need parent theme style.css (~544KB)
 * + bootstrap + fontawesome + theme icon pack. Keep child overrides only.
 * Non-Elementor templates still get the parent chain.
 */
if ( ! function_exists( 'sifoxen_child_thm_parent_css' ) ) :
	function sifoxen_child_thm_parent_css() {
		$ver = '2.4.2';

		$use_elementor_slim = tonic_is_elementor_document()
			|| is_front_page()
			|| ( function_exists( 'elementor_theme_do_location' ) && ( is_singular() || is_home() ) );

		// Extra: Elementor Theme Builder canvas / full-width templates.
		if ( is_singular() ) {
			$template = get_page_template_slug( get_queried_object_id() );
			if ( in_array( $template, array( 'elementor_header_footer', 'elementor_canvas' ), true ) ) {
				$use_elementor_slim = true;
			}
		}

		if ( $use_elementor_slim ) {
			// Dequeue parent theme bulk CSS often registered by parent at default priority.
			$dequeue = array(
				'sifoxen-parent-style',
				'sifoxen-style-parent',
				'sifoxen-main',
				'bootstrap',
				'fontawesome',
				'font-awesome',
				'sifoxen-icons',
				'sifoxen-fontawesome',
				'animate',
				'owl-carousel',
				'swiper',
				'magnific-popup',
				'nice-select',
			);
			foreach ( $dequeue as $handle ) {
				wp_dequeue_style( $handle );
				wp_deregister_style( $handle );
			}

			wp_enqueue_style(
				'sifoxen-style',
				get_stylesheet_directory_uri() . '/style.css',
				array(),
				$ver
			);
			return;
		}

		// Fallback for classic theme templates (rare).
		wp_enqueue_style(
			'sifoxen-parent-style',
			get_template_directory_uri() . '/style.css',
			array( 'sifoxen-fonts', 'sifoxen-icons', 'bootstrap', 'fontawesome' ),
			null
		);
		wp_enqueue_style(
			'sifoxen-style',
			get_stylesheet_directory_uri() . '/style.css',
			array( 'sifoxen-parent-style' ),
			$ver
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'sifoxen_child_thm_parent_css', 991 );

/**
 * Late pass: strip leftover parent/theme vendor CSS+JS on Elementor documents.
 */
add_action(
	'wp_enqueue_scripts',
	function () {
		if ( is_admin() || ! tonic_is_elementor_document() ) {
			return;
		}
		foreach (
			array(
				'bootstrap',
				'bootstrap-select',
				'fontawesome',
				'font-awesome',
				'sifoxen-icons',
				'sifoxen-parent-style',
				'animate',
				'owl-carousel',
			) as $handle
		) {
			wp_dequeue_style( $handle );
			wp_dequeue_script( $handle );
		}
		// Parent addon CSS is for classic theme widgets — Elementor pages do not need it.
		wp_dequeue_style( 'sifoxen-addon-style' );
		// Dashicons not needed for guests.
		wp_dequeue_style( 'dashicons' );
	},
	9999
);

add_filter(
	'rank_math/opengraph/facebook',
	function ( $og ) {
		if ( is_array( $og ) && isset( $og['og:updated_time'] ) ) {
			unset( $og['og:updated_time'] );
		}
		return $og;
	},
	20
);

/**
 * FAQ schema — only when accordion markup exists.
 * Avoid re-running the_content() on every page (TTFB cost).
 */
add_action(
	'wp_footer',
	function () {
		if ( ! is_singular( array( 'page', 'post' ) ) ) {
			return;
		}
		global $post;
		if ( empty( $post->ID ) ) {
			return;
		}

		$cache_key = 'tonic_faq_schema_' . $post->ID . '_' . $post->post_modified_gmt;
		$cached    = get_transient( $cache_key );
		if ( false !== $cached ) {
			if ( $cached ) {
				echo $cached; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			}
			return;
		}

		$raw = (string) $post->post_content;
		if ( false === strpos( $raw, 'accordion-title' ) && false === strpos( $raw, 'elementor-tab-title' ) ) {
			set_transient( $cache_key, '', WEEK_IN_SECONDS );
			return;
		}

		// Prefer already-rendered global post content if available; else skip heavy re-render.
		$content = $GLOBALS['tonic_rendered_content'] ?? '';
		if ( ! $content ) {
			$content = apply_filters( 'the_content', $raw );
		}

		preg_match_all( '/<[^>]*class="[^"]*accordion-title[^"]*"[^>]*>(.*?)<\/[^>]+>/is', $content, $title_matches );
		preg_match_all( '/<[^>]*class="[^"]*accordion-content[^"]*"[^>]*>(.*?)<\/[^>]+>/is', $content, $answer_matches );

		$faq_titles  = ! empty( $title_matches[1] ) ? array_map( 'wp_strip_all_tags', $title_matches[1] ) : array();
		$faq_answers = ! empty( $answer_matches[1] ) ? array_map( 'wp_strip_all_tags', $answer_matches[1] ) : array();

		if ( empty( $faq_titles ) || empty( $faq_answers ) ) {
			set_transient( $cache_key, '', WEEK_IN_SECONDS );
			return;
		}

		$faq_schema = array(
			'@context'   => 'https://schema.org',
			'@type'      => 'FAQPage',
			'mainEntity' => array(),
		);

		$count = min( count( $faq_titles ), count( $faq_answers ) );
		for ( $i = 0; $i < $count; $i++ ) {
			$faq_schema['mainEntity'][] = array(
				'@type'          => 'Question',
				'name'           => trim( $faq_titles[ $i ] ),
				'acceptedAnswer' => array(
					'@type' => 'Answer',
					'text'  => trim( $faq_answers[ $i ] ),
				),
			);
		}

		$out = "\n" . '<script type="application/ld+json">' . wp_json_encode( $faq_schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) . "</script>\n";
		set_transient( $cache_key, $out, WEEK_IN_SECONDS );
		echo $out; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
	},
	20
);

add_action(
	'elementor/query/author_posts_only',
	function ( $query ) {
		if ( is_author() ) {
			$query->set( 'author', get_queried_object_id() );
		}
	}
);

/* -----------------------------------------------------------------------------
 * HARDENING & LIGHT PERF (no layout CSS injection)
 * -------------------------------------------------------------------------- */

remove_action( 'wp_head', 'wp_generator' );
add_filter( 'xmlrpc_enabled', '__return_false' );
foreach ( array( 'rsd_link', 'wlwmanifest_link', 'wp_shortlink_wp_head', 'rest_output_link_wp_head' ) as $link ) {
	remove_action( 'wp_head', $link );
}

add_action(
	'init',
	function () {
		remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
		remove_action( 'wp_print_styles', 'print_emoji_styles' );
		remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
		remove_action( 'admin_print_styles', 'print_emoji_styles' );
		remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
		remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
		remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
	}
);

add_action( 'init', 'rankray_register_page_categories' );
function rankray_register_page_categories() {
	$labels = array(
		'name'          => 'Page Categories',
		'singular_name' => 'Page Category',
		'menu_name'     => 'Page Categories',
	);
	$args   = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'page-category' ),
		'show_in_rest'      => true,
	);
	register_taxonomy( 'page_category', array( 'page' ), $args );
}

/**
 * Prefer Elementor SVG icons over FA font files when feature is on.
 * Do not dequeue Elementor icon CSS blindly (breaks header social icons).
 */

add_filter(
	'wpseo_title',
	function ( $title ) {
		if ( is_string( $title ) ) {
			$title = preg_replace( '/\bMilton,\s*CA\b/i', 'Milton, Ontario', $title );
		}
		return $title;
	},
	20
);

add_action(
	'template_redirect',
	function () {
		if ( is_search() ) {
			global $wp_query;
			if ( 1 === (int) $wp_query->post_count && 1 === (int) $wp_query->max_num_pages ) {
				wp_redirect( get_permalink( $wp_query->posts['0']->ID ) );
				exit;
			}
		}
	}
);
