<?php
/**
 * Template part for displaying page content in page.php
 *
 * @package Coinsfera_WordPress_Theme
 */

?>

<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<div class="post-content-area page">
		<?php if ( has_post_thumbnail() ) { $post_thumbnail = get_the_post_thumbnail_url( get_the_ID(), 'full' ); ?>
			<img class="post-thumbnail w-100 mb-5" src="<?php echo esc_url( $post_thumbnail ); ?>" alt="<?php the_title(); ?>">
		<?php } ?>
		<div class="post-content">
			<?php the_content(); ?>
		</div>
	</div>
</div>