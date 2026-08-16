<?php
/**
 * Read-only render check for one hero variant draft.
 *
 * Checks a single page per process on purpose: the theme's header template
 * part declares coinsfera_city_label() at file scope with no function_exists
 * guard, so including the template twice in one process fatals. That never
 * happens on a real request, but it does if this script loops.
 *
 * Run with: CFKL_ID=28486 CFKL_LABEL="A split" wp eval-file variant-check.php
 */

$post_id = (int) getenv( 'CFKL_ID' );
$label   = getenv( 'CFKL_LABEL' ) ?: (string) $post_id;
$expect  = getenv( 'CFKL_EXPECT' );

if ( ! $post_id ) {
	WP_CLI::error( 'Set CFKL_ID.' );
}

/* WP-CLI has no main query. Prime both globals before query_posts, otherwise
   the template's loop finds nothing and renders an empty <main>.
   Use page_id rather than p: with p, WP_Query never sets is_page(), so
   cfkl_is_active() reads false and the schema, preload and asset checks all
   report false negatives even though the real request is fine. */
$args = array( 'page_id' => $post_id, 'post_type' => 'page', 'post_status' => 'draft' );

$GLOBALS['wp_query']     = new WP_Query( $args );
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];

query_posts( $args );

if ( ! have_posts() ) {
	WP_CLI::error( "No post found for ID {$post_id}. Status: " . get_post_status( $post_id ) );
}

ob_start();
include get_theme_file_path( 'page-templates/template-keyword-landing.php' );
$html = ob_get_clean();

printf( "  [rendered %d bytes total]\n", strlen( $html ) );

$s    = strpos( $html, '<main id="cfkl"' );
$e    = strpos( $html, '</main>' );
$main = ( false !== $s && false !== $e ) ? substr( $html, $s, $e - $s ) : '';

$hs   = strpos( $main, '<section class="cfkl-hero' );
$he   = strpos( $main, '</section>' );
$hero = ( false !== $hs && false !== $he ) ? substr( $main, $hs, $he - $hs ) : '';

preg_match_all( '/<img[^>]*>/i', $hero, $imgs );
$nodim = 0;
foreach ( $imgs[0] as $img ) {
	if ( ! preg_match( '/width="\d+"/', $img ) || ! preg_match( '/height="\d+"/', $img ) ) {
		$nodim++;
	}
}

printf( "%s  (id %d)\n", $label, $post_id );
printf( "  layout class        %s\n", ( $expect && strpos( $hero, $expect ) !== false ) ? $expect : 'MISSING (' . $expect . ')' );
printf( "  other layout leaked %s\n", substr_count( $main, 'cfkl-hero--' ) > 1 ? 'YES' : 'no' );
printf( "  h1 in hero          %d\n", substr_count( $hero, '<h1' ) );
printf( "  office block        %s\n", strpos( $hero, 'cfkl-office' ) !== false ? 'yes' : 'MISSING' );
printf( "  maps link           %s\n", strpos( $hero, 'google.com/maps' ) !== false ? 'yes' : 'MISSING' );
printf( "  new tab + noopener  %s\n", strpos( $hero, 'rel="noopener"' ) !== false ? 'yes' : 'no' );
printf( "  stat items          %d\n", substr_count( $hero, 'cfkl-stats__item' ) );
printf( "  hero imgs           %d (missing dims %d)\n", count( $imgs[0] ), $nodim );
printf( "  eager+fetchpriority %s\n", ( strpos( $hero, 'loading="eager"' ) !== false && strpos( $hero, 'fetchpriority="high"' ) !== false ) ? 'yes' : 'NO' );
printf( "  legacy glass card   %s\n", strpos( $main, 'cfkl-card--glass' ) !== false ? 'STILL PRESENT' : 'gone' );
printf( "  sections on page    %d\n", substr_count( $main, 'class="cfkl-section' ) );
printf( "  faq schema          %d\n", substr_count( $html, 'FAQPage' ) );
printf( "  main bytes          %d\n", strlen( $main ) );

wp_reset_query();
