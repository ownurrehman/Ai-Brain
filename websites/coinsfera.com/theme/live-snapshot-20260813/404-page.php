<?php
/**
 *
 *Template Name:404 page
 * @package Coinsfera_WordPress_Theme
 */

get_header();
?>

<div id="page-not-found" class="page-not-found col-12">
	<div class="post-content-area page w-75 mx-auto">
		<div class="mt-2 text-center">
			<img class="404-img" src="https://www.coinsfera.com/wp-content/uploads/2021/11/404.png" alt="404-img">
		</div>
		<p class="text-center mb-5">
			<?php esc_html_e( 'It looks like nothing was found at this location. Perhaps searching can help.', 'coinsfera' ); ?>
		</p>
		<?php get_search_form(); ?>
	</div>
</div>

<?php
get_footer();