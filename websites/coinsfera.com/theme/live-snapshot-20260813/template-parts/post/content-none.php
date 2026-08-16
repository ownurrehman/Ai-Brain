<?php
/**
 * Template part for displaying page not found (404) message
 *
 * @package Coinsfera_WordPress_Theme
 */

?>

<div id="nothing-found" class="nothing-found col-12">
	<div class="post-content-area page">
		<?php
			if ( is_home() && current_user_can( 'publish_posts' ) ) :

				printf( '<p>' . wp_kses( __( 'Ready to publish your first post? <a href="%1$s">Get started here</a>.', 'coinsfera' ),
						array( 'a' => array( 'href' => array(),	), ) ) . '</p>', esc_url( admin_url( 'post-new.php' ) ) );

			elseif ( is_search() ) :

				printf( '<p>'.__( 'Sorry, but nothing matched your search terms. Please try again with some different keywords.', 'coinsfera' ).'</p>' );
				get_search_form();

			else :

				printf( '<p>'.__( 'It seems we can&rsquo;t find what you&rsquo;re looking for. Perhaps searching can help.', 'coinsfera' ).'</p>' );
				get_search_form();

			endif;
		?>
	</div>
</div>