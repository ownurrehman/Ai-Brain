<?php
/**
 * The template for displaying post
 *
 * @package Coinsfera_WordPress_Theme
 */

get_header();
?>

<div id="primary" class="content-area col-md-8">
	<?php

		while ( have_posts() ) :

			the_post();

			get_template_part( 'template-parts/post/content', 'locations' );

			if ( comments_open() || get_comments_number() ) :
				comments_template();
			endif;

		endwhile;
	?>
</div><!-- end #primary .content-area -->
<!-- <div id="secondary" class="widget-area blog-sidebar mt-5 mt-lg-0 <?php echo esc_attr( $secondary_column_class ); ?>">
	<div class="is-sticky">
    	<?php
    
    		if ( is_active_sidebar( 'post-sidebar' ) ) {
    
    			dynamic_sidebar( 'post-sidebar' );
    		}
    	?>
    </div>
</div> --><!-- end #secondary .widget-area -->

<?php
get_footer();