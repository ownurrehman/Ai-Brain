<?php
/**
 * rankish functions and definitions
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 * @package rankish
 */

if ( ! defined( 'RANKISH_VERSION' ) ) {
	define( 'RANKISH_VERSION', '2.4.3' ); // Copy leftover sifoxen files to rankish names
}

if ( ! function_exists( 'rankish_setup' ) ) :
	function rankish_setup() {
		load_theme_textdomain( 'rankish', get_template_directory() . '/languages' );
		add_theme_support( 'automatic-feed-links' );
		add_theme_support( 'title-tag' );
		set_post_thumbnail_size( 770, 428, true );
		add_theme_support( 'post-thumbnails' );

		register_nav_menus( array(
			'menu-1' => esc_html__( 'Primary', 'rankish' ),
		) );

		add_theme_support( 'html5', array(
			'search-form',
			'comment-form',
			'comment-list',
			'gallery',
			'caption',
			'style',
			'script',
		) );

		add_theme_support( 'customize-selective-refresh-widgets' );
		add_theme_support( 'custom-logo', array(
			'height'      => 250,
			'width'       => 250,
			'flex-width'  => true,
			'flex-height' => true,
		) );
	}
endif;
add_action( 'after_setup_theme', 'rankish_setup' );

function rankish_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'rankish_content_width', 640 );
}
add_action( 'after_setup_theme', 'rankish_content_width', 0 );

function rankish_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Sidebar', 'rankish' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Add widgets here.', 'rankish' ),
		'before_widget' => '<section id="%1$s" class="sidebar__single widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<div class="title"><h2>',
		'after_title'   => '</h2></div>',
	) );

	if ( class_exists( 'WooCommerce' ) ) {
		register_sidebar( array(
			'name'          => esc_html__( 'Shop Sidebar', 'rankish' ),
			'id'            => 'shop',
			'description'   => esc_html__( 'Add widgets here.', 'rankish' ),
			'before_widget' => '<section id="%1$s" class="shop-category product__sidebar-single widget sidebar__single %2$s"><div class="widget-inner">',
			'after_widget'  => '</div></section>',
			'before_title'  => '<h3 class="product__sidebar-title">',
			'after_title'   => '</h3>',
		) );
	}
}
add_action( 'widgets_init', 'rankish_widgets_init' );

function rankish_fonts_url() {
	$font_url = '';
	if ( 'off' !== _x( 'on', 'Google font: on or off', 'rankish' ) ) {
		$font_url = add_query_arg(
			'family',
			urlencode( 'Plus Jakarta Sans:400,500,600,700,800|DM Sans:400,500,600,700|EB Garamond:400,500,600,700&subset=latin,latin-ext' ),
			'https://fonts.googleapis.com/css'
		);
	}
	return esc_url_raw( $font_url );
}

function rankish_scripts() {
	$version = RANKISH_VERSION;

	wp_enqueue_style( 'rankish-fonts', rankish_fonts_url(), array(), null );
	wp_enqueue_style( 'flaticons', get_template_directory_uri() . '/assets/vendors/flaticons/css/flaticon.css', array(), '1.1' );
	wp_enqueue_style( 'rankish-icons', get_template_directory_uri() . '/assets/vendors/rankish-icons/style.css', array(), '1.1' );
	wp_enqueue_style( 'bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap/css/bootstrap.min.css', array(), '5.0.0' );
	wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/assets/vendors/fontawesome/css/all.min.css', array(), '5.15.1' );
	wp_enqueue_style( 'rankish-style', get_stylesheet_uri(), array(), $version );
	wp_style_add_data( 'rankish-style', 'rtl', 'replace' );

	wp_enqueue_script( 'bootstrap', get_template_directory_uri() . '/assets/vendors/bootstrap/js/bootstrap.min.js', array( 'jquery' ), '5.0.0', true );
	wp_enqueue_script( 'isotope', get_template_directory_uri() . '/assets/vendors/isotope/isotope.js', array( 'jquery' ), '2.1.1', true );
	wp_enqueue_script( 'imagesloaded', get_template_directory_uri() . '/assets/vendors/imagesloaded/imagesloaded.pkgd.min.js', array( 'jquery' ), '4.1.4', true );
	wp_enqueue_script( 'rankish-theme', get_template_directory_uri() . '/assets/js/rankish-theme.js', array( 'jquery' ), $version, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// Handle dark mode toggle
	$dark_mode_status = get_theme_mod( 'rankish_dark_mode', false );
	if ( is_page() ) {
		$page_dark_mode = get_post_meta( get_the_ID(), 'rankish_enable_dark_mode', true );
		$dark_mode_status = $page_dark_mode ?: $dark_mode_status;
	}
	if ( isset( $_GET['dark_mode'] ) ) {
		$dark_mode_status = sanitize_text_field( $_GET['dark_mode'] );
	}
	if ( $dark_mode_status === 'on' ) {
		wp_enqueue_style( 'rankish-dark-mode', get_template_directory_uri() . '/assets/css/modes/rankish-dark.css', array(), $version );
	}

	// Handle RTL toggle
	$rtl_mode_status = get_theme_mod( 'rankish_rtl_mode', false );
	if ( is_page() ) {
		$page_rtl_mode = get_post_meta( get_the_ID(), 'rankish_enable_rtl_mode', true );
		$rtl_mode_status = $page_rtl_mode ?: $rtl_mode_status;
	}
	if ( isset( $_GET['rtl_mode'] ) ) {
		$rtl_mode_status = sanitize_text_field( $_GET['rtl_mode'] );
	}
	if ( $rtl_mode_status === 'yes' || is_rtl() ) {
		wp_enqueue_style( 'rankish-custom-rtl', get_template_directory_uri() . '/assets/css/rankish-rtl.css', array(), $version );
	}
}
add_action( 'wp_enqueue_scripts', 'rankish_scripts', 991 );

// Modular includes
require get_template_directory() . '/inc/template-tags.php';
require get_template_directory() . '/inc/template-functions.php';

if ( class_exists( 'Layerdrops\Rankish\Customizer' ) ) {
	require get_template_directory() . '/inc/theme-customizer-styles.php';
}

require get_template_directory() . '/inc/plugins.php';

if ( class_exists( 'OCDI_Plugin' ) ) {
	require get_template_directory() . '/inc/demo-import.php';
}

if ( class_exists( 'WooCommerce' ) ) {
	require get_template_directory() . '/inc/woocommerce.php';
}
