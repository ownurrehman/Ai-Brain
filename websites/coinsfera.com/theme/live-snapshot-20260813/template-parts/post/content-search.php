<?php
/**
 * Template part for displaying results in search page
 *
 * @package Coinsfera_WordPress_Theme
 */

?>

<div id="post-<?php the_ID(); ?>" class="<?php echo is_active_sidebar( 'post-sidebar' ) ? 'col-lg-12' : 'col-lg-12' ; ?> col-sm-12 d-flex">

	<div class="news-blog">
		
		<div class="news-title">
			<h2 class="font-30 mb-0">
				<a href="<?php the_permalink(); ?>" class="text-dark">
					<?php the_title(); ?>
				</a>
			</h2>
		</div>
		<div class="news-date-category mt-3">
			<p class="mb-0 mt-1">
				<span class="text-dark"><?php echo get_the_date( 'd F Y' ); ?></span>
				
				<?php

					/*$post_category = wp_get_object_terms( get_the_ID(), 'category' );
					if ( ! is_wp_error( $post_category ) ) {
						
						foreach ( $post_category as $term ) {
							$coma =  ($term == $post_category[count($post_category)-1] ) ? '' : ',';
							
							$html = '<a href="' . esc_url( get_term_link( $term->term_id ) ) . '" class="text-danger"> ' . $term->name . ''.$coma.'</a>';
							printf( esc_html__( '%s', 'coinsfera' ), $html );
						}
					}*/
				?>
				<!-- <span class="text-danger">corona myths, covid19</span> -->
			</p>
		</div>
		<div class="news-content">
			<p><?php echo get_the_excerpt(); ?></p>

			<a href="<?php the_permalink(); ?>"><?php esc_html_e( 'Read More', 'coinsfera' ); ?> <img src="<?php echo COINSFERA_URI; ?>/assets/images/rm.png"></a>
		</div>
	</div>
</div>