<?php

/**
 * The template for displaying comments
 *
 * This is the template that displays the area of the page that contains both the current comments
 * and the comment form.
 *
 * @package Coinsfera_WordPress_Theme
 */

/*
 * If the current post is protected by a password and
 * the visitor has not yet entered the password we will
 * return early without loading the comments.
 */
 
 /*
if ( post_password_required() ) {
	return;
}
?>

<div id="comments" class="comments-area border-top mb-5 pb-5">
	<div class="card border-0 shadow-none bg-transparent mt-5">
		
		<div class="card-body1 mb-4">
			<?php

				$fields = array(
					'author' => '<div class="row form-group"><div class="col-lg-6 mb-3 mb-lg-0">
									
									<input class="form-control bg-transparent" id="author" name="author" placeholder="'.__( 'Enter your name', 'coinsfera' ).'" type="text" required>
								</div>',
					'email'  => '<div class="col-lg-6">
									
									<input class="form-control bg-transparent" id="email" name="email" placeholder="'.__( 'Enter your email', 'coinsfera' ).'" type="text" required>
								</div></div>',
				);

				$comment_field = '<div class="row form-group"><div class="col-lg-12">
					
					<textarea class="form-control bg-transparent" rows="4" id="comment" name="comment" placeholder="'.__( 'Enter your comment', 'coinsfera' ).'" required></textarea>
				</div></div>';

				$args = array(
					'class_form'			=> 'comment-form mt-4',
					'title_reply_before'   	=> '<h3 id="reply-title" class="comment-reply-title font-21">',
					'title_reply_after'    	=> '</h3>',
					'fields'				=> $fields,
					'comment_field'			=> $comment_field,
					'submit_field'			=> '<div class="form-submit">%1$s %2$s</div>',
					'label_submit'			=> __( 'Submit', 'coinsfera' ),
					'class_submit'			=> 'btn btn-warning mt-3 font-18 font-circular-medium',
					'submit_button'			=> '<button name="%1$s" type="submit" id="%2$s" class="%3$s">%4$s</button>',
					'comment_notes_before' 	=> '<p class="comment-notes-before d-none">'.__( 'Your email address will not be published. Name and Email are required fields.', 'coinsfera' ).'</p>',
					'comment_notes_after'  	=> '',

				);

				comment_form( $args );
			?>
		</div>
		<hr>
		<div class="card-body1 my-4">
			<h3 id="reply-title" class="comment-reply-title font-21 mt-3">
				<?php echo get_comments_number( get_the_ID() ); ?><?php printf( __( ' Comments', 'coinsfera' )  ); ?>
			</h3>
			<div class="card shadow-none news-blog mt-5">
				<div class="card-body">
					<?php

						if ( have_comments() ) : ?>

							<ol class="comment-list">
								<?php wp_list_comments( array(
									'style'      => 'ol',
									'short_ping' => true,
									'max_depth'	 => '4',
									//'callback'	 => 'coinsfera_list_comments',
								) ); ?>
							</ol>

							<?php
							the_comments_pagination( array(
								'prev_text' => '<i class="fa fa-angle-left" aria-hidden="true"></i>',
								'next_text' => '<i class="fa fa-angle-right" aria-hidden="true"></i>',
							) );
						else :

							printf( '<p class="no-comments font-21">'.__( 'No comments found.', 'coinsfera' ).'</p>' );

						endif;
					?>
				</div>
			</div>
		</div>
	</div>
</div><!-- end #comments -->*/
