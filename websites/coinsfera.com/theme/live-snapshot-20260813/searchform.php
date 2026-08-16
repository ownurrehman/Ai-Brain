<?php
/**
 * The template for displaying search form
 *
 * @package Coinsfera_WordPress_Theme
 */

?>

<?php $unique_id = esc_attr( uniqid( 'search-form-' ) ); ?>
<div class="search-form-wraper">
	<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">		
		<input type="search" id="<?php echo esc_attr( $unique_id ); ?>" class="form-control" placeholder="<?php _e( 'Search', 'coinsfera' ); ?>" value="<?php echo get_search_query(); ?>" name="s" />			
	</form>
</div>
