<?php
/**
 * Render one Keyword Landing draft and report on the result.
 *
 * Run with: wp eval-file design-check.php <post_id>
 *
 * One post per process on purpose. The theme's header template part declares
 * coinsfera_city_label() at file scope with no function_exists guard, so
 * including the template twice in a single PHP process is a fatal error.
 */

if ( ! defined( 'WP_CLI' ) ) {
	exit( "Run through WP-CLI.\n" );
}

$post_id = isset( $args[0] ) ? (int) $args[0] : 0;

if ( ! $post_id || ! get_post( $post_id ) ) {
	WP_CLI::error( 'Pass a valid post id.' );
}

/* Prime the loop the way a real front-end request would, so is_page() and
   is_page_template() are true and the conditional asset enqueue actually runs.
   post_status has to be explicit: these pages are drafts, and a page query
   defaults to publish only, which would return an empty loop. */
$GLOBALS['wp_query'] = new WP_Query( array(
	'page_id'     => $post_id,
	'post_status' => array( 'draft', 'pending', 'private', 'publish' ),
) );
$GLOBALS['wp_the_query'] = $GLOBALS['wp_query'];
$GLOBALS['post'] = get_post( $post_id );
setup_postdata( $GLOBALS['post'] );

$design = cfkl_design( $post_id );

if ( ! cfkl_is_active() ) {
	WP_CLI::error( 'Template is not active for this post; the check would be meaningless.' );
}

do_action( 'wp_enqueue_scripts' );

ob_start();
include locate_template( 'page-templates/template-keyword-landing.php' );
$html = ob_get_clean();

/* Only judge the landing markup. The shared header and footer are not ours. */
$main = '';

if ( preg_match( '#<main id="cfkl".*?</main>#s', $html, $m ) ) {
	$main = $m[0];
}

/**
 * Count occurrences of a pattern in the landing markup.
 *
 * @param string $pattern Regex without delimiters.
 * @param string $subject Markup.
 * @return int
 */
function cfkl_count( $pattern, $subject ) {

	return preg_match_all( '#' . $pattern . '#s', $subject, $ignored );
}

$checks = array(
	'design'              => $design,
	'main bytes'          => number_format( strlen( $main ) ),
	'design class'        => cfkl_count( 'cfkl--' . preg_quote( $design, '#' ), $main ) > 0 ? 'yes' : 'MISSING',
	'other design class'  => cfkl_count( 'cfkl--(?!' . preg_quote( $design, '#' ) . ')(desk|concierge|neo|ledger)', $main ),
	'h1'                  => cfkl_count( '<h1', $main ),
	'h2'                  => cfkl_count( '<h2', $main ),
	'sections'            => cfkl_count( '<section', $main ),
	'calculator roots'    => cfkl_count( 'data-cfkl-calc', $main ),
	'calc fiat input'     => cfkl_count( 'data-calc-fiat', $main ),
	'calc crypto input'   => cfkl_count( 'data-calc-crypto', $main ),
	'calc mode buttons'   => cfkl_count( 'data-calc-mode=', $main ),
	'calc outputs'        => cfkl_count( 'data-calc-out=', $main ),
	'calc cta'            => cfkl_count( 'data-calc-cta', $main ),
	'currency options'    => cfkl_count( 'data-calc-currency', $main ),
	'details (faq)'       => cfkl_count( '<details', $main ),
	'images'              => cfkl_count( '<img ', $main ),
	'images missing dims' => cfkl_count( '<img (?![^>]*width=)[^>]*>', $main ),
	'lazy images'         => cfkl_count( 'loading="lazy"', $main ),
	'eager images'        => cfkl_count( 'loading="eager"', $main ),
	'maps links'          => cfkl_count( 'google\.com/maps', $main ),
	'target _blank'       => cfkl_count( 'target="_blank"', $main ),
	'noopener'            => cfkl_count( 'rel="noopener', $main ),
	'inline style attrs'  => cfkl_count( ' style="', $main ),
	'!important in markup' => cfkl_count( '!important', $main ),
);

/* Rates must be rendered server-side, not left as zeroes for JS to fill. */
$payload = cfkl_calc_payload( $post_id );
$checks['feed source'] = $payload['source'];
$checks['feed stale']  = $payload['stale'] ? 'YES' : 'no';
$checks['feed coins']  = count( $payload['coins'] );
$checks['btc usd']     = isset( $payload['coins']['BTC']['usd'] ) ? round( $payload['coins']['BTC']['usd'] ) : 'MISSING';
$checks['btc try']     = isset( $payload['coins']['BTC']['try'] ) ? round( $payload['coins']['BTC']['try'] ) : 'MISSING';
$checks['whatsapp']    = $payload['whatsapp'] ? $payload['whatsapp'] : 'MISSING';

/* Assets. SG Optimizer rewrites URLs on the live front end, so check the
   registration queue rather than the rendered filename. */
$styles  = wp_styles();
$scripts = wp_scripts();

$checks['css base queued']   = in_array( 'coinsfera-keyword-landing', $styles->queue, true ) ? 'yes' : 'MISSING';
$checks['css design queued'] = in_array( 'coinsfera-keyword-design', $styles->queue, true ) ? 'yes' : 'MISSING';
$checks['js calc queued']    = in_array( 'coinsfera-keyword-calc', $scripts->queue, true ) ? 'yes' : 'MISSING';

/* Check the file on disk rather than the registered src: SG Optimizer rewrites
   the src to a minified copy in its own cache directory, so deriving a path
   from the URL reports a missing file even when everything is fine. */
$css_file = get_template_directory() . '/assets/css/design-' . $design . '.css';

$checks['design css exists'] = file_exists( $css_file )
	? 'yes (' . number_format( filesize( $css_file ) ) . ' bytes)'
	: 'MISSING ' . $css_file;

$checks['faq schema entries'] = cfkl_count( '"@type":"Question"', cfkl_faq_jsonld( $post_id ) );

WP_CLI::log( '' );
WP_CLI::log( str_pad( strtoupper( $design ), 60, '=', STR_PAD_BOTH ) );

foreach ( $checks as $label => $value ) {
	WP_CLI::log( sprintf( '  %-22s %s', $label, $value ) );
}

/* Heading outline, to confirm the document structure reads sensibly. */
if ( preg_match_all( '#<(h[1-3])[^>]*>(.*?)</\1>#s', $main, $headings, PREG_SET_ORDER ) ) {

	WP_CLI::log( '' );
	WP_CLI::log( '  outline' );

	foreach ( $headings as $heading ) {
		WP_CLI::log( sprintf(
			'    %s %s%s',
			$heading[1],
			str_repeat( '  ', (int) substr( $heading[1], 1 ) - 1 ),
			mb_substr( trim( wp_strip_all_tags( $heading[2] ) ), 0, 62 )
		) );
	}
}

$problems = array();

if ( 1 !== $checks['h1'] ) {
	$problems[] = 'expected exactly one h1, found ' . $checks['h1'];
}

if ( $checks['other design class'] > 0 ) {
	$problems[] = 'markup from another design leaked in';
}

if ( $checks['images missing dims'] > 0 ) {
	$problems[] = $checks['images missing dims'] . ' image(s) without width/height';
}

if ( 0 === $checks['calculator roots'] ) {
	$problems[] = 'no calculator rendered';
}

if ( $checks['target _blank'] > $checks['noopener'] ) {
	$problems[] = 'a _blank link is missing rel="noopener"';
}

if ( 'MISSING' === $checks['design class'] ) {
	$problems[] = 'design class missing from main';
}

WP_CLI::log( '' );

if ( $problems ) {
	foreach ( $problems as $problem ) {
		WP_CLI::warning( $problem );
	}
} else {
	WP_CLI::success( 'clean' );
}
