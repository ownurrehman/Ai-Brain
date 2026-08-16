<?php
/**
 * Template Name: Coinsfera - News Page Template
 * The full width page template file
 *
 * @package Coinsfera_WordPress_Theme
 */

get_header();
?>
<?php
	$args = array(
		'post_type' => 'coinsfera_news',
		'post_status' => 'publish',
		'posts_per_page' => -1,
		'order'     	=> 'ASC',
	);
	query_posts( $args );
	?>

<div id="primary" class="content-area <?php echo esc_attr( $primary_column_class.$order_class ); ?>">
	<div class="row">
		<?php 

			$flag = true; //1st blog check

			while ( have_posts() ) {

				the_post();

				if ( $flag ) {

					get_template_part( 'template-parts/news/content-index-full' );

					$flag = false;
					
				} else {
					
					get_template_part( 'template-parts/news/content-index' );
				}					
			}
			wp_reset_query();
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
</div> --> <!-- end #secondary .widget-area

<?php
get_footer();