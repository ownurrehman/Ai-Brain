<?php
/**
 * Coinsfera Custom WordPress Theme functions and definitions
 *
 * @package Coinsfera_WordPress_Theme
 * @since 1.0
 */

/**
 * Coinsfera only works in WordPress 5.0 or later.
 */
if ( version_compare( $GLOBALS['wp_version'], '5.0', '<' ) ) {

    add_action( 'after_switch_theme', 'coinsfera_switch_theme' );
    return;
}

if (!function_exists('have_rows')) {

    function have_rows() { return false; }

}

if (!function_exists('get_field')) {

    function get_field() { return null; }

}

/**
 * Prevent switching to Coinsfera on old versions of WordPress.
 */
function coinsfera_switch_theme() {

    switch_theme( WP_DEFAULT_THEME );
    unset( $_GET['activated'] );
    add_action( 'admin_notices', 'coinsfera_upgrade_notice' );
}

/**
 * Add a message for unsuccessful theme switch.
 */
function coinsfera_upgrade_notice() {
    
    $message = sprintf( __( 'Coinsfera requires at least WordPress version 5.0 You are running version %s. Please upgrade and try again.', 'coinsfera' ), $GLOBALS['wp_version'] );
    printf( '<div class="error"><p>%s</p></div>', $message );
}

/**
 * Defining current theme version
 */
if( ! defined( 'COINSFERA_VER' ) ) define( 'COINSFERA_VER', wp_get_theme()->get( 'Version' ) );

/**
 * Defining current theme directory path(root)
 */
if( ! defined( 'COINSFERA_PATH' ) ) define( 'COINSFERA_PATH', get_template_directory() );

/**
 * Defining current theme directory url(uri)
 */
if( ! defined( 'COINSFERA_URI' ) ) define( 'COINSFERA_URI', get_template_directory_uri() );

/**
 * Sets up theme defaults and registers support for various WordPress features.
 */
function coinsfera_setup() {

    /**
     * Theme textdomain
     */
    load_theme_textdomain( 'coinsfera' );

    /**
     * Add default posts and comments RSS feed links to head.
     */
    add_theme_support( 'automatic-feed-links' );

    /**
     * <title> tag in the document head
     */
    add_theme_support( 'title-tag' );

    /**
     * Enable support for Post Thumbnails on posts and pages.
     */
    add_theme_support( 'post-thumbnails' );
    add_image_size( 'blog-index', 360, 202, true );

    /**
     * Switch default core markup for search form, comment form, and comments to output valid HTML5
     */
    add_theme_support( 'html5', array(
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ) );

    /**
     * Set the default content width.
     */
    $GLOBALS['content_width'] = 550;

    /**
     * Add theme support for Custom Logo
     */
    add_theme_support( 'custom-logo', array(
        'width'       => 250,
        'height'      => 150,
        'flex-width'  => true,
        'flex-height' => true,
        'header-text' => array( 'site-title', 'site-logo' ), //classes for logo text
    ) );

    /**
     * Add theme support for selective refresh for widgets.
     */
    add_theme_support( 'customize-selective-refresh-widgets' );

    /**
     * This theme uses wp_nav_menu() in two locations
     */
    register_nav_menus( array(
        'primary-menu' => __( 'Header Menu', 'coinsfera' ),
        'dev-menu' => __( 'Dev Menu', 'coinsfera' ),
        'footer-menu' => __( 'Footer Menu', 'coinsfera' ),
        'footer-bottom-menu' => __( 'Footer Bottom Menu', 'coinsfera' ),
        'quick-link-menu' => __( 'Quick Link Menu', 'coinsfera' ),
    ) );
}
add_action( 'after_setup_theme', 'coinsfera_setup' );

/**
 * Register sidbar/widget area.
 */
function coinsfera_register_sidebars() {

    register_sidebar( array(
        'name'          => __( 'Post Sidebar', 'coinsfera' ),
        'id'            => 'post-sidebar',
        'description'   => __( 'Add widgets here to appear in your post sidebar.', 'coinsfera' ),
        'before_widget' => '<div class="sidebar-widget post-sidebar-widget mb-5">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="mb-4 font-30 widget-title">',
        'after_title'   => '</h2>',
    ) );

    register_sidebar( array(
        'name'          => __( 'Page Sidebar', 'coinsfera' ),
        'id'            => 'page-sidebar',
        'description'   => __( 'Add widgets here to appear in your page sidebar.', 'coinsfera' ),
        'before_widget' => '<div class="sidebar-widget page-sidebar-widget mb-5">',
        'after_widget'  => '</div>',
        'before_title'  => '<h2 class="mb-4 font-30 widget-title widget-title">',
        'after_title'   => '</h2>',
    ) );
}
add_action( 'widgets_init', 'coinsfera_register_sidebars' );

/**
 * Register/Enqueue scripts and styles.
 */
function coinsfera_enqueue_scripts_and_styles() {

    //fonts
    //wp_enqueue_style( 'coinsfera-fonts', coinsfera_fonts_url(), array(), COINSFERA_VER );
    //wp_enqueue_style( 'fontawesome-all', COINSFERA_URI . '/assets/icons/font-awesome/css/fontawesome-all.min.css', array(), COINSFERA_VER, 'all' );

    //styles
    wp_enqueue_style( 'coinsfera-owl-carousel-style', COINSFERA_URI . '/assets/css/owl.carousel.min.css', '', COINSFERA_VER, 'all' );
    //wp_enqueue_style( 'coinsfera-custom-style', COINSFERA_URI . '/assets/css/style.min.css', '', COINSFERA_VER, 'all' );
    wp_enqueue_style( 'coinsfera-custom-style', COINSFERA_URI . '/assets/css/style.css', '', COINSFERA_VER, 'all' );
    wp_enqueue_style( 'coinsfera-style', get_stylesheet_uri(), '', COINSFERA_VER, 'all' );
    
    if( defined( 'ICL_LANGUAGE_CODE' ) ) {
        if ( ICL_LANGUAGE_CODE == 'ar' ) {
            wp_enqueue_style( 'rtl-wpml', COINSFERA_URI . '/assets/css/wpml-rtl.css', '', COINSFERA_URI, 'all' );
        }
    }
    //scripts
    wp_enqueue_script( 'jquery' );
    wp_enqueue_script( 'jquery-min', COINSFERA_URI . '/assets/libs/jquery/dist/jquery.min.js', array( 'jquery' ), COINSFERA_VER, true );
    wp_enqueue_script( 'popper', COINSFERA_URI . '/assets/libs/popper.js/dist/umd/popper.min.js', array( 'jquery' ), COINSFERA_VER, true );
    wp_enqueue_script( 'bootstrap', COINSFERA_URI . '/assets/libs/bootstrap/dist/js/bootstrap.min.js', array( 'jquery' ), COINSFERA_VER, true );
    wp_enqueue_script( 'owl-carousel', COINSFERA_URI . '/assets/js/owl.carousel.min.js', array( 'jquery' ), COINSFERA_VER, true );
    wp_enqueue_script( 'coinsfera-custom-script', COINSFERA_URI . '/assets/js/custom.js', array( 'jquery' ), COINSFERA_VER, true );

    wp_localize_script( 'coinsfera-custom-script', 'coinsfera_site_data', array(
        'is_user_login' => is_user_logged_in(),
        'home_url'      => home_url( '/' ),
        // 'ajaxurl'        => admin_url( 'admin-ajax.php' ),
        // 'mapsvg'     => esc_url( COINSFERA_URI . '/assets/images/europe.svg' ),  
    ));

    if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
        wp_enqueue_script( 'comment-reply' );
    }
}
add_action( 'wp_enqueue_scripts', 'coinsfera_enqueue_scripts_and_styles' );

/**
 * Meta Box Options For Posts
 *
 * This file is third-party Meta-Box plugin file used for additional post meta options. 
 */
//require_once COINSFERA_PATH . '/inc/meta-box-options.php';

/**
 * Theme Customizer
 *
 * This file is theme file which contains customizer controls used for theme options and homepage sections.
 */
require_once COINSFERA_PATH . '/inc/customizer.php';

/**
 * AJAX Functionality
 *
 * This file is theme file which contains ajax functionalities.
 */
require_once COINSFERA_PATH . '/inc/ajax-functionality.php';

/**
 * Custom Template Functionalities
 *
 * This file is theme file which contains theme custom functionalities.
 */
require_once COINSFERA_PATH . '/inc/template-functions.php';

/**
 * Custom Template Tags
 *
 * This file is theme file which contains theme custom tags functions.
 */
require_once COINSFERA_PATH . '/inc/template-tags.php';

/**
 * TGM plugin activation
 *
 * This is third-party TGM plugin activation plugin.
 */
require_once COINSFERA_PATH . '/inc/tgm/required-plugins.php';

/*
 * Theme options page
 */
if( function_exists('acf_add_options_page') ) {

    acf_add_options_page(array(
        'page_title'    => 'Theme Settings',
        'menu_title'    => 'Theme Settings',
        'menu_slug'     => 'theme-general-settings',
        'capability'    => 'edit_posts',
        'redirect'      => false
    ));
}

/*
 * Hide admin bar
 */
//add_filter('show_admin_bar', '__return_false');


// Safe ACF Repeater loop
if ( class_exists('ACF') && have_rows('acf_repeaterfield') ):
    while ( have_rows('acf_repeaterfield') ) : the_row();
        $imageID = get_sub_field('acf_subfield'); 
        
        if ( $imageID ) {
            $image = wp_get_attachment_image_src( $imageID, 'full' );
            $alt_text = get_post_meta($imageID , '_wp_attachment_image_alt', true);
            ?>  
            <img src="<?php echo esc_url($image[0]); ?>" alt="<?php echo esc_attr($alt_text); ?>" class="port-img front" />
            <?php
        }
    endwhile;
endif;

//Custom MENU Code Goes Below

add_action('admin_head', 'coinsfera_custom_admin_css');

function coinsfera_custom_admin_css() {
  echo '<style>
    .acf-menu-item-fields:first-of-type, .acf-menu-settings.-default{
    display: none;
    }
    
  </style>';
}

function coinsfera_custom_new_menu() {
  register_nav_menu('my-custom-menu',__( 'My Custom Menu Location' ));
}
add_action( 'init', 'coinsfera_custom_new_menu' );

add_filter('wp_nav_menu_objects', 'coinsfera_wp_nav_menu_objects', 10, 2);

function coinsfera_wp_nav_menu_objects( $items, $args ) {
    
    // loop
    foreach( $items as &$item ) {
        
        // Only run get_field if ACF is active
        if ( class_exists('ACF') ) {
            $icon = get_field('custom_main_menu_image', $item);
            
            // append icon
            if( $icon ) {
                $item->title .= ' <img src="'. esc_url($icon) .'">';
            }
        }
    }
    
    // return
    return $items;
}

// === WPML Footer Strings Registration
function coinsfera_register_footer_strings() {
    if ( function_exists( 'icl_register_string' ) ) {

        // Footer content
        icl_register_string( 'Coinsfera Footer', 'Footer Description', get_theme_mod( 'coinsfera_footer_desc' ) );
        icl_register_string( 'Coinsfera Footer', 'Footer Button Label', get_theme_mod( 'coinsfera_footer_btn_lbl' ) );
        icl_register_string( 'Coinsfera Footer', 'Footer Button Link', get_theme_mod( 'coinsfera_footer_btn_link' ) );
        icl_register_string( 'Coinsfera Footer', 'Footer Copyright Text', get_theme_mod( 'coinsfera_copyright_text' ) );

        // Footer menu headings
        icl_register_string( 'Coinsfera Footer', 'Footer Menu Title', get_theme_mod( 'coinsfera_footer_menu_title' ) );
        icl_register_string( 'Coinsfera Footer', 'Quick Links Menu Title', get_theme_mod( 'coinsfera_footer_quick_links_menu' ) );
    }
}
add_action( 'init', 'coinsfera_register_footer_strings' );

// === End of WPML Footer Strings Registration

/* Remove Default Theme Archive Titles to prevent conflict with Elementor */
add_action( 'wp_head', function() {
    if ( is_archive() || is_category() ) {
        echo '<style>.page-header, .archive-header, h1.page-title, .taxonomy-title { display: none !important; }</style>';
    }
});

// Universal SEO Focus Keyword to Meta Keywords
add_action('wp_head', function () {
    if (!is_singular()) {
        return;
    }

    global $post;

    if (!$post) {
        return;
    }

    $focus_keyword = '';

  

    // Yoast SEO
    if (empty($focus_keyword)) {
        $focus_keyword = get_post_meta($post->ID, '_yoast_wpseo_focuskw', true);
    }

    // Output meta keywords if found
    if (!empty($focus_keyword)) {
        $focus_keyword = preg_replace('/\s*,\s*/', ', ', $focus_keyword);

        echo '<meta name="keywords" content="' . esc_attr(trim($focus_keyword)) . '">' . "\n";
    }
});

// Force HTTPS for all enqueued assets (CSS, JS, SVG)
add_filter('script_loader_src', 'coinsfera_force_https_assets', 10, 2);
add_filter('style_loader_src', 'coinsfera_force_https_assets', 10, 2);
function coinsfera_force_https_assets($src, $handle) {
    if (strpos($src, 'http://') === 0) {
        $src = str_replace('http://', 'https://', $src);
    }
    return $src;
}

/**
 * English pages stay in Elementor. Translations open in WPML and cannot
 * be saved in Elementor, which is what forked the RU/TR layouts.
 */
require_once COINSFERA_PATH . '/inc/wpml-elementor-guard.php';
require_once COINSFERA_PATH . '/inc/hero-ux.php';

/**
 * Keyword Landing template
 *
 * ACF-driven landing page template. Everything it registers is scoped to pages
 * using page-templates/template-keyword-landing.php.
 */
require_once COINSFERA_PATH . '/inc/keyword-landing/bootstrap.php';
