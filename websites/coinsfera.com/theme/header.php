<?php
/**
 * The template for displaying the header
 *
 * @package Coinsfera_WordPress_Theme
 */
if ( defined('WP_DEBUG') && WP_DEBUG ) {
    error_log("Header loaded for: " . ($_SERVER['REQUEST_URI'] ?? 'unknown'));
}
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" as="style" onload="this.rel='stylesheet'">
    <link rel="preload" as="image" href="https://www.coinsfera.com/wp-content/uploads/2022/10/Cryptocurrency-Exchange-Shop-in-Istanbul.png" fetchpriority="high">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
    <!-- main-wrapper -->
    <div id="page" class="main-wrapper site">

		<!-- header-wrapper -->
		<div id="header" class="header-wrapper site-header">
			<?php get_template_part( 'template-parts/header/header' ); ?>
		</div>
		<!-- end header-wrapper -->

		<!-- content-wrapper -->
		<div id="content" class="content-wrapper page-wrapper site-content">
			<?php
				if ( ! is_page_template( 'page-templates/page-fullwidth-elementor.php' ) ) {
					
					coinsfera_open_content_wrapper();
				}
                
			?>