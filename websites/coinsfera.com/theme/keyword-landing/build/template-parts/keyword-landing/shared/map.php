<?php
/**
 * Google Maps embed.
 *
 * Purely functional, so every design shares it and styles the wrapper itself.
 * The iframe is lazy so it never competes with the hero for bandwidth.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$cfkl_map   = (string) cfkl_get( 'office_map' );
$cfkl_class = isset( $args['class'] ) ? $args['class'] : 'cfkl-map';

if ( '' === $cfkl_map ) {
	return;
}
?>
<div class="<?php echo esc_attr( $cfkl_class ); ?>">
	<iframe
		src="<?php echo esc_url( $cfkl_map ); ?>"
		title="<?php esc_attr_e( 'Coinsfera office location on Google Maps', 'coinsfera' ); ?>"
		loading="lazy"
		referrerpolicy="no-referrer-when-downgrade"
		allowfullscreen></iframe>
</div>
