<?php
/**
 * Custom template functions for this theme
 */

/**
 * Add custom classes to the array of body classes.
 */
function coinsfera_body_classes( $classes ) {

	if ( is_singular() ) {
		// Adds `singular` to singular pages.
		$classes[] = 'singular';
	} else {
		// Adds `hfeed` to non singular pages.
		$classes[] = 'hfeed';
	}

	// Adds a class of different sidebars when there is sidebar active or not.
	if ( is_active_sidebar( 'post-sidebar' ) ){

		$classes[] = 'sidebar post-sidebar';

	} elseif ( is_active_sidebar( 'page-sidebar' ) ) {

		$classes[] = 'sidebar page-sidebar';

	} else {

		$classes[] = 'no-sidebar';
	}

	return $classes;
}
add_filter( 'body_class', 'coinsfera_body_classes' );

/**
 * Adds custom class to the array of posts classes.
 */
function coinsfera_post_classes( $classes, $class, $post_id ) {

	$classes[] = 'entry';

	return $classes;
}
add_filter( 'post_class', 'coinsfera_post_classes', 10, 3 );

/**
 * Adds custom class to each <li> of some menu.
 */
function coinsfera_menu_li_item_classes( $classes, $item, $args, $depth ) {

	if ( $args->theme_location == 'primary-menu' ) {

		$classes[] = 'nav-item';

		if( in_array( 'menu-item-has-children', $classes ) ) {
			$classes[] = 'dropdown';
		}
	}

	if ( $args->theme_location == 'footer-menu' || $args->theme_location == 'quick-link-menu' ) {

		$classes[] = 'footer-list-item';
	}
	
	if ( $args->theme_location == 'footer-bottom-menu') {

		$classes[] = 'copyright-list-item';
	}

	return $classes;
}
add_filter( 'nav_menu_css_class', 'coinsfera_menu_li_item_classes', 10, 4 );

/**
 * Adds custom class to each <a> of some menu.
 */
function coinsfera_menu_anchor_tag_classes( $atts, $item, $args, $depth ) {

	if ( $args->theme_location == 'primary-menu' ) {

		$atts['class'] = 'nav-link';
	}

	if ( $args->theme_location == 'footer-menu' || $args->theme_location == 'quick-link-menu' ) {

		$atts['class'] = 'footer-list-link';
	}
	
	return $atts;
}
add_filter( 'nav_menu_link_attributes', 'coinsfera_menu_anchor_tag_classes', 10, 4 );

/**
 * Adds custom class to each submenu <ul> of some menu.
 */
function coinsfera_submenu_ul_css_class( $classes, $args, $depth ) {

	$classes[] = 'list-unstyled';
	$classes[] = 'dropdown-list';

	return $classes;
}
add_filter( 'nav_menu_submenu_css_class', 'coinsfera_submenu_ul_css_class', 10, 3 );

/**
 * Add a pingback url auto-discovery header for single posts, pages, or attachments.
 */
function coinsfera_pingback_header() {

	if ( is_singular() && pings_open() ) {

		printf( '<link rel="pingback" href="%s">', esc_url( get_bloginfo( 'pingback_url' ) ) );
	}
}
add_action( 'wp_head', 'coinsfera_pingback_header' );

/**
 * Filter the except length to 15 words.
 */
function coinsfera_post_excerpt_length( $length ) {

	return 25;
}
add_filter( 'excerpt_length', 'coinsfera_post_excerpt_length', 999 );

/**
 * Filter the "read more" excerpt string link to the post.
 */
/*function coinsfera_excerpt_more( $more ) {

	if ( ! is_single() ) {
			
		$more = sprintf( '<a class="read-more-text" href="%1$s">%2$s</a>',
			get_permalink( get_the_ID() ),
			__( '&nbsp;[read more...]', 'coinsfera' )
		);
	}

	return $more;
}
add_filter( 'excerpt_more', 'coinsfera_excerpt_more' );*/

/**
 * Arranging comment fields (comment field to bottom).
 */
function coinsfera_move_comment_field_to_bottom( $fields ) {

	$comment_field = $fields[ 'comment' ];
	$cookies_field = $fields[ 'cookies' ];

	unset( $fields[ 'comment' ] );
	unset( $fields[ 'cookies' ] );

	$fields[ 'comment' ] = $comment_field;
	$fields[ 'cookies' ] = $cookies_field;
	
	return $fields;
}
add_filter( 'comment_form_fields', 'coinsfera_move_comment_field_to_bottom' );

/**
 * All pages list array. (Returns array of pages list, value array( 'post_id' => 'post_title' ) )
 */
function coinsfera_get_pages() {

	$pages = get_pages();
	$pages_list = array();
	array_push( $pages_list, '' );
	foreach ( $pages as $page ) {
		$pages_list[ $page->ID ] = $page->post_title;
	}
	
	return $pages_list;
}