<?php
/**
 * The template for displaying the footer
 *
 * @package Coinsfera_WordPress_Theme
 */
?>
			<?php
				if ( ! is_page_template( 'page-templates/page-fullwidth-elementor.php' ) ) {

					coinsfera_close_content_wrapper();
				}
			?>
		</div>
		<!-- end content-wrapper -->

		<!-- footer-wrapper -->
		<div id="footer" class="footer-wrapper site-footer">
			<?php get_template_part( 'template-parts/footer/footer' ); ?>
		</div>
		<!-- end footer-wrapper -->

	</div>
	<!-- end main-wrapper -->

	<?php wp_footer(); ?>
</body>
</html>