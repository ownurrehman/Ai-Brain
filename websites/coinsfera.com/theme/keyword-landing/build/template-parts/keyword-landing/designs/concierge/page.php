<?php
/**
 * Istanbul Concierge - the whole layout.
 *
 * The section order is the argument this design makes. Hospitality and place
 * come first: the office, the people, the reviews. The quote form only appears
 * once all three have done their work, because a visitor who already trusts
 * the room will read a number differently.
 *
 * The rate board and the feature grid are deliberately absent. A ticking price
 * table belongs to a trading screen, not to a room where someone talks you
 * through the trade before a figure is agreed.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

cfkl_part( 'hero' );
cfkl_part( 'intro' );
cfkl_part( 'trust' );
cfkl_part( 'steps' );
cfkl_part( 'office' );
cfkl_part( 'reviews' );
cfkl_part( 'calc' );
cfkl_part( 'requirements' );
cfkl_part( 'fees' );
cfkl_part( 'coins' );
cfkl_part( 'services' );
cfkl_part( 'faq' );
cfkl_part( 'cta' );
