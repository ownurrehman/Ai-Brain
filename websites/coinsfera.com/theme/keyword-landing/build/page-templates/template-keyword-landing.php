<?php
/**
 * Template Name: Coinsfera - Keyword Landing (ACF)
 *
 * A full-bleed landing template driven entirely by ACF fields.
 *
 * This template builds its own document shell instead of calling get_header()
 * and get_footer(), because those files wrap page content in a breadcrumb and
 * title band that this design replaces with its own hero. The site header and
 * footer themselves are reused as-is via their template parts, so navigation,
 * language switcher and footer stay identical to every other page.
 *
 * @package Coinsfera_WordPress_Theme
 */

do_action( 'get_header', null );
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

	<div id="page" class="main-wrapper site">

		<div id="header" class="header-wrapper site-header">
			<?php get_template_part( 'template-parts/header/header' ); ?>
		</div>

		<div id="content" class="content-wrapper page-wrapper site-content">
			<?php
			while ( have_posts() ) :
				the_post();
				?>
				<main id="cfkl" class="cfkl cfkl--<?php echo esc_attr( cfkl_design() ); ?>">
					<?php
					/*
					 * Each design owns its own section order and markup. page.php
					 * inside the design folder is the whole layout for that design.
					 */
					cfkl_part( 'page' );
					?>
				</main>
				<?php
			endwhile;
			?>
		</div>

		<div id="footer" class="footer-wrapper site-footer">
			<?php get_template_part( 'template-parts/footer/footer' ); ?>
		</div>

	</div>

<?php
do_action( 'get_footer', null );
wp_footer();
?>
</body>
</html>
