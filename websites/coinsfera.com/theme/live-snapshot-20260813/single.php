<?php
/**
 * The template for displaying single post
 *
 * @package Coinsfera_WordPress_Theme
 */

get_header();
?>

<!-- START OF PRIMARY CONTENT AREA -->
<div id="primary" class="content-area col-md-8">
	<?php
	// Loop through posts (should be only one for single.php)
	while ( have_posts() ) :
		the_post();
		// Load the post content template part
		get_template_part( 'template-parts/post/content', 'post' );
		// Display comments if open or there are comments
		if ( comments_open() || get_comments_number() ) :
			comments_template();
		endif;
	endwhile;
	?>
</div><!-- end #primary .content-area -->
<!-- END OF PRIMARY CONTENT AREA -->

<?php
// START OF SIDEBAR (SECONDARY COLUMN)
// Define the sidebar column class to avoid undefined variable warning
$secondary_column_class = 'col-md-4';
?>
<div id="secondary" class="widget-area blog-sidebar mt-5 mt-lg-0 <?php echo esc_attr( $secondary_column_class ); ?>">
	<div class="is-sticky">
    	<?php
    	// Display the sidebar widgets if the sidebar is active
    	if ( is_active_sidebar( 'post-sidebar' ) ) {
    		dynamic_sidebar( 'post-sidebar' );
    	}
    	?>
    </div>
</div><!-- end #secondary .widget-area -->
<!-- END OF SIDEBAR (SECONDARY COLUMN) -->

<?php
get_footer();
?>
