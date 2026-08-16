<?php
/**
 * The template for displaying search result
 *
 * @package Coinsfera_WordPress_Theme
 */

get_header();

$primary_column_class = is_active_sidebar( 'post-sidebar' ) ? ' col-lg-8' : ' col-lg-12' ;
$secondary_column_class = is_active_sidebar( 'post-sidebar' ) ? ' col-lg-4' : ' d-none' ;
$sidebar_position = get_theme_mod( 'coinsfera_post_sidebar_position', 'right' );
$order_class = $sidebar_position == 'left' ? ' order-lg-2' : '' ;
?>

<div id="primary" class="content-area <?php echo esc_attr( $primary_column_class.$order_class ); ?>">
	<div class="row">
		<?php 
			if ( have_posts() ) :

				while ( have_posts() ) :

					the_post();

					get_template_part( 'template-parts/post/content', 'search' );

				endwhile;

				coinsfera_posts_pagination();

			else :

				get_template_part( 'template-parts/post/content', 'none' );

			endif; 
		?>
	</div>
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