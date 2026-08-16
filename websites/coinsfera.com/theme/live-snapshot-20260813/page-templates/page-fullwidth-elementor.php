<?php
/**
 * Template Name: Coinsfera - Elementor Full Width Page
 * The full width page template file
 *
 * @package Coinsfera_WordPress_Theme
 */

get_header();
?>

<div id="primary" class="content-area page-template-elementor-fullwidth">
	<?php

		while ( have_posts() ) :

			the_post();

			the_content();

		endwhile;
	?>
</div>

<?php
get_footer();