<?php
/**
 * Template part for displaying post content in index.php
 *
 * @package Coinsfera_WordPress_Theme
 */

?>

<div id="post-<?php the_ID(); ?>" class="col-lg-4 col-md-6 d-flex">

	<div class="news-blog card shadow-none">
		<?php if ( has_post_thumbnail() ) { $post_thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>
			<div class="news-img">
				<a href="<?php the_permalink(); ?>"><img class="img-fluid" src="<?php echo esc_url( $post_thumbnail ); ?>" alt="<?php the_title(); ?>"></a>
			</div>
		<?php } ?>

		<div class="card-body">

			<div class="news-title">
				<span class=""><?php echo get_the_date( 'M d, Y' ); ?></span>
				<h2 class="font-21 mb-0 mt-3">
					<a href="<?php the_permalink(); ?>" class="blog-title">
						<?php the_title(); ?>
					</a>
				</h2>
			</div>
			<div class="news-date-category mt-3">
				<p class="mb-0 mt-1">
					
					
					<?php

						$post_category = wp_get_object_terms( get_the_ID(), 'category' );
						
						if ( ! is_wp_error( $post_category ) ) {
							foreach ( $post_category as $term ) {
								$coma =  ($term == $post_category[count($post_category)-1] ) ? '' : ',';

								$html = '<a href="' . esc_url( get_term_link( $term->term_id ) ) . '" class="blog-cat"> ' . $term->name .''.$coma.'</a>';
								printf( esc_html__( '%s', 'coinsfera' ), $html );
								
							}
						}

					?>
				</p>
			</div>
			<div class="news-content mt-4">
				<div class="credits d-flex flex-row">
					<p class="pr-3 mr-3 mb-0"><?php _e( 'By', 'coinsfera' ); ?>&nbsp;
						<a class="author-name" href="<?php echo get_author_posts_url( get_the_author_meta( 'ID' ) ); ?>"><?php the_author(); ?></a>
					</p>
				</div>
			</div>
		</div>
	</div>
</div>