<?php
/**
 * Customizer additional settings and sections for theme options
 */

/**
 * Register customizer sections, controls and settings
 */
function coinsfera_customize_register( $wp_customize ) {

	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';

	/*--------------------------------------------------------------------------------------------------------*/
	// Theme Options
	/*--------------------------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'coinsfera_customizer_theme_options' , array(
	'title'		=> __( 'Theme Options', 'coinsfera' ),
	'priority'	=> 120,
	) );

		$wp_customize->add_setting( 'coinsfera_post_sidebar_position' , array(
		'default'    => 'right',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_post_sidebar_position', array(
		'label'      => __( 'Post Sidebar Position', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_theme_options',
		'settings'   => 'coinsfera_post_sidebar_position',
		'priority'   => 1,
		'type'       => 'select',
		'choices'    => array(
			//'none'   		=> __( 'No Sidebar', 'coinsfera' ),
			'right'  		=> __( 'Right', 'coinsfera' ),
			'left'  		=> __( 'Left', 'coinsfera' )
		) ) ) );

		$wp_customize->add_setting( 'coinsfera_page_sidebar_position' , array(
		'default'    => 'right',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_page_sidebar_position', array(
		'label'      => __( 'Page Sidebar Position', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_theme_options',
		'settings'   => 'coinsfera_page_sidebar_position',
		'priority'   => 1,
		'type'       => 'select',
		'choices'    => array(
			//'none'   		=> __( 'No Sidebar', 'coinsfera' ),
			'right'  		=> __( 'Right', 'coinsfera' ),
			'left'  		=> __( 'Left', 'coinsfera' )
		) ) ) );

	/*--------------------------------------------------------------------------------------------------------*/
	// Header
	/*--------------------------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'coinsfera_customizer_header_options' , array(
	'title'		=> __( 'Header', 'coinsfera' ),
	'priority'	=> 120,
	) );

		$wp_customize->add_setting( 'coinsfera_header_logo' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'coinsfera_header_logo', array(
		'label'    	=> __( 'Header Logo', 'coinsfera' ),
		'section'  	=> 'coinsfera_customizer_header_options',
		'settings' 	=> 'coinsfera_header_logo',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_contact_lbl' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_contact_lbl', array(
		'label'      => __( 'Contact Button Label', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_header_options',
		'settings'   => 'coinsfera_contact_lbl',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_contact_link' , array(
		'default'    => '#',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_contact_link', array(
		'label'      => __( 'Contact No', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_header_options',
		'settings'   => 'coinsfera_contact_link',
		'type'       => 'text',
		'priority'   => 1,
		) ) );


	/*--------------------------------------------------------------------------------------------------------*/
	// Footer
	/*--------------------------------------------------------------------------------------------------------*/
	$wp_customize->add_section( 'coinsfera_customizer_footer_options' , array(
	'title'		=> __( 'Footer', 'coinsfera' ),
	'priority'	=> 120,
	) );

		$wp_customize->add_setting( 'coinsfera_footer_logo' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'sanitize_text_field',
		) );

		$wp_customize->add_control( new WP_Customize_Image_Control( $wp_customize, 'coinsfera_footer_logo', array(
		'label'    	=> __( 'Footer Logo', 'coinsfera' ),
		'section'  	=> 'coinsfera_customizer_footer_options',
		'settings' 	=> 'coinsfera_footer_logo',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_desc' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_desc', array(
		'label'      => __( 'Description', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_desc',
		'type'       => 'textarea',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_btn_lbl' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_btn_lbl', array(
		'label'      => __( 'Footer Button Label', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_btn_lbl',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_btn_link' , array(
		'default'    => '#',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_btn_link', array(
		'label'      => __( 'Footer Button Link', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_btn_link',
		'type'       => 'text',
		'priority'   => 1,
		) ) );
        
        /********* Social Icons***********/
	$wp_customize->add_setting( 'coinsfera_footer_fb' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_fb', array(
		'label'      => __( 'facebook', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_fb',
		'type'       => 'text',
		'priority'   => 1,
		) ) );
	
	$wp_customize->add_setting( 'coinsfera_footer_tw' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_tw', array(
		'label'      => __( 'twitter', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_tw',
		'type'       => 'text',
		'priority'   => 1,
		) ) );
	
	$wp_customize->add_setting( 'coinsfera_footer_instagram' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_instagram', array(
		'label'      => __( 'Instagram', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_instagram',
		'type'       => 'text',
		'priority'   => 1,
		) ) );
	
	$wp_customize->add_setting( 'coinsfera_footer_lnkin' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_lnkin', array(
		'label'      => __( 'Linkedin', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_lnkin',
		'type'       => 'text',
		'priority'   => 1,
		) ) );
	
	$wp_customize->add_setting( 'coinsfera_footer_yt' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_yt', array(
		'label'      => __( 'Youtube', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_yt',
		'type'       => 'text',
		'priority'   => 1,
		) ) );
	
	$wp_customize->add_setting( 'coinsfera_footer_reddit' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_reddit', array(
		'label'      => __( 'Reddit', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_reddit',
		'type'       => 'text',
		'priority'   => 1,
		) ) );
	
	$wp_customize->add_setting( 'coinsfera_footer_medium' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_medium', array(
		'label'      => __( 'Medium', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_medium',
		'type'       => 'text',
		'priority'   => 1,
		) ) );
        

		$wp_customize->add_setting( 'coinsfera_footer_menu_title' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_menu_title', array(
		'label'      => __( 'Footer Menu Title', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_menu_title',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_quick_links_menu' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_quick_links_menu', array(
		'label'      => __( 'Quick Link Menu Title', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_quick_links_menu',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_add_title' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_add_title', array(
		'label'      => __( 'Address Title', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_add_title',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_address' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_address', array(
		'label'      => __( 'Address', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_address',
		'type'       => 'textarea',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_contact_title' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_contact_title', array(
		'label'      => __( 'Contact Title', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_contact_title',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_contact_no' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_contact_no', array(
		'label'      => __( 'Mobile No', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_contact_no',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_email_title' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_email_title', array(
		'label'      => __( 'Email Title', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_email_title',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_footer_email' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => 'esc_attr',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_footer_email', array(
		'label'      => __( 'Email ID', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_footer_email',
		'type'       => 'text',
		'priority'   => 1,
		) ) );

		$wp_customize->add_setting( 'coinsfera_copyright_text' , array(
		'default'    => '',
		'transport'  => 'refresh',
		'sanitize_callback' => '',
		) );

		$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'coinsfera_copyright_text', array(
		'label'      => __( 'Copyright Text', 'coinsfera' ),
		'section'    => 'coinsfera_customizer_footer_options',
		'settings'   => 'coinsfera_copyright_text',
		'type'       => 'text',
		'priority'   => 1,
		) ) );


}
add_action( 'customize_register', 'coinsfera_customize_register' );


/*Dynamic color css include*/
function coinsfera_customizer_dynamic_color_css(){
	//require_once GUIDE_PATH . '/inc/customizer/customizer-dynamic-color-css.php';
}
add_action( 'wp_head', 'coinsfera_customizer_dynamic_color_css');


/*Live preview js*/
function coinsfera_customizer_live_preview(){
	wp_enqueue_script( 'coinsfera-customizer', COINSFERA_URI . '/assets/js/customizer.js', '', '', true );
}
add_action( 'customize_preview_init', 'coinsfera_customizer_live_preview' );