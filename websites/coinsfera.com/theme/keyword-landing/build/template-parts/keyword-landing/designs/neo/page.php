<?php
/**
 * Neo Fintech - page layout.
 *
 * The section order for this design. Intro and trust are deliberately absent:
 * this design carries its argument through the bento features, the comparison
 * cards and the review numbers rather than through paragraphs of prose.
 *
 * The hero renders the calculator itself, because the card is positioned to
 * straddle the bottom edge of the orange field and only makes sense there.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

cfkl_part( 'hero' );
cfkl_part( 'rates' );
cfkl_part( 'features' );
cfkl_part( 'steps' );
cfkl_part( 'compare' );
cfkl_part( 'coins' );
cfkl_part( 'reviews' );
cfkl_part( 'requirements' );
cfkl_part( 'fees' );
cfkl_part( 'office' );
cfkl_part( 'services' );
cfkl_part( 'faq' );
cfkl_part( 'cta' );
