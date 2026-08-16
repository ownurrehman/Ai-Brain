<?php
/**
 * Template Name: Coinsfera - Fullwidth Page
 * The full width page template file
 *
 * @package Coinsfera_WordPress_Theme
 */

get_header();
?>

<div id="primary" class="content-area col-lg-10 offset-lg-1">
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

<?php
get_footer();