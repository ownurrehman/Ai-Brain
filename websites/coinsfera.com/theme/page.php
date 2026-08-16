<?php
/**
 * The template for displaying page
 *
 * @package Coinsfera_WordPress_Theme
 */

get_header();

$primary_column_class = is_active_sidebar( 'page-sidebar' ) ? ' col-lg-8' : ' col-lg-12' ;
$secondary_column_class = is_active_sidebar( 'page-sidebar' ) ? ' col-lg-4' : ' d-none' ;
$sidebar_position = get_theme_mod( 'coinsfera_page_sidebar_position', 'right' );
$order_class = $sidebar_position == 'left' ? ' order-lg-2' : '' ;
?>

<div id="primary" class="content-area <?php echo esc_attr( $primary_column_class.$order_class ); ?>">
	<?php

		while ( have_posts() ) :

			the_post();

			get_template_part( 'template-parts/page/content', 'page' );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
	?>
</div><!-- end #primary .content-area -->
<div id="secondary" class="widget-area blog-sidebar mt-5 mt-lg-0 <?php echo esc_attr( $secondary_column_class ); ?>">
    <div class="is-sticky">
    	<?php
    
    		if ( is_active_sidebar( 'page-sidebar' ) ) {

				dynamic_sidebar( 'page-sidebar' );
			}
    	?>
    </div>
</div><!-- end #secondary .widget-area -->

<?php
get_footer();