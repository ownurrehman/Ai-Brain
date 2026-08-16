<?php
/**
 * Template part for displaying post content in single.php
 *
 * @package Coinsfera_WordPress_Theme
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-content-area post">

		<div class="post-content mt-4">
			<?php the_content(); ?>
		</div>	
		<div class="row align-items-center">
			<div class="col-lg-12">
				<div class="row align-items-center">
					<div class="col-12 post-taxonomy post-taxonomy-tag">
						<?php
							$post_tags = wp_get_object_terms( get_the_ID(), 'post_tag' );
							if ( ! is_wp_error( $post_tags ) ) {

								foreach ( $post_tags as $term ) {

									$html = '<a href="'.esc_url( get_term_link( $term->term_id ) ).'" class="post-taxonomy-badge tag py-2 pl-2 font-18 text-orange">
										<span class="">#</span>
										<span class="">'.$term->name.'
									</a>';
									printf( esc_html__( '%s', 'portfolio' ), $html );
								}
							}
						?>
					</div>
				</div>
			</div>
		</div>
		<?php

		$post_categories = wp_get_post_categories( get_the_ID(), array( 'fields' => 'ids' ) );

		$related_posts_args = array(
			'post_type'       => 'post',
			'posts_per_page'  => 1,
			'post__not_in'    => array( get_the_ID() ),
			'orderby'         => 'rand',
			'tax_query'       => array(
				array(
					'taxonomy' => 'category',
					'field' => 'ids',
					'terms' => $post_categories,
				),
			),
		);

		$related_posts = get_posts( $related_posts_args );

		if ( $related_posts ) { ?>
			<div class="row">
				<div class="col-lg-12">
					<hr>
					<div class="related-posts my-5 pb-4">
						<h3><?php _e( 'Related Posts', 'coinsfera' ); ?></h3>
						<div class="related-posts-slider mt-3">

							<?php
								foreach ( $related_posts as $r_post ) { ?>

									<div class="related-posts-item row align-items-center no-gutters mt-5">
										<div class="col-md-6">
											<?php if ( has_post_thumbnail( $r_post->ID ) ) { ?>
												<?php $post_thumbnail = get_the_post_thumbnail_url( $r_post->ID, 'full' ); ?>
												<img class="w-100" src="<?php echo esc_url( $post_thumbnail ); ?>" alt="<?php echo get_the_title( $r_post->ID ); ?>">

											<?php } else { ?>

												<img class="rounded" src="<?php echo COINSFERA_URI; ?>/assets/images/alt-img.png" alt="<?php echo get_the_title( $r_post->ID ); ?>">

											<?php } ?>
										</div>
										<div class="col-md-6">
											
								
										<div class="px-4 py-4">
											<span class=""><?php echo get_the_date( 'M d, Y', $r_post->ID); ?></span>
											<a class="card-title" href="<?php the_permalink( $r_post->ID ); ?>">
												<h4 class="blog-title mt-3"><?php echo get_the_title( $r_post->ID ); ?></h4>
											</a>

											<p class="mb-0 mt-3">
					
												<?php

													$post_category = wp_get_object_terms( $r_post->ID, 'category' );
													
													if ( ! is_wp_error( $post_category ) ) {
														foreach ( $post_category as $term ) {
															$coma =  ($term == $post_category[count($post_category)-1] ) ? '' : ',';

															$html = '<a href="' . esc_url( get_term_link( $term->term_id ) ) . '" class="blog-cat"> ' . $term->name .''.$coma.'</a>';
															printf( esc_html__( '%s', 'coinsfera' ), $html );
															
														}
													}

												?>
											</p>
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
								<?php 
								} 
							?>
						</div>
					</div>
				</div>
			</div>
		<?php }	
	?>
	</div>
</div>