<?php
/**
 * Front-end performance: lazy images and deferred third-party tags.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Add loading=lazy to content images except the homepage hero.
 *
 * @param string $html HTML.
 * @return string
 */
function coinsfera_lazy_images_html( $html ) {

	if ( ! is_string( $html ) || '' === $html || false === stripos( $html, '<img' ) ) {
		return $html;
	}

	return preg_replace_callback(
		'/<img\b[^>]*>/i',
		static function ( $m ) {
			$tag = $m[0];
			if ( preg_match( '/\bloading\s*=/i', $tag ) ) {
				return $tag;
			}
			if ( preg_match( '/fetchpriority\s*=\s*[\'"]high[\'"]/i', $tag ) ) {
				return $tag;
			}
			if ( preg_match( '/Cryptocurrency-Exchange-Shop-in-Istanbul|home-banner|banner-img|skip-lazy|no-lazy|data-src=|custom-logo|class=["\'][^"\']*\blogo\b/i', $tag ) ) {
				return $tag;
			}
			if ( preg_match( '/mc\.yandex\.ru\/watch/i', $tag ) ) {
				return $tag;
			}
			if ( ! preg_match( '/\bdecoding\s*=/i', $tag ) ) {
				$tag = preg_replace( '/<img\b/i', '<img decoding="async"', $tag, 1 );
			}
			return preg_replace( '/<img\b/i', '<img loading="lazy"', $tag, 1 );
		},
		$html
	);
}
add_filter( 'the_content', 'coinsfera_lazy_images_html', 99 );
add_filter( 'elementor/frontend/the_content', 'coinsfera_lazy_images_html', 99 );
add_filter( 'elementor/widget/render_content', 'coinsfera_lazy_images_html', 99 );
add_filter( 'widget_text', 'coinsfera_lazy_images_html', 99 );

/**
 * Default lazy loading on attachment images.
 *
 * @param array $attr Image attributes.
 * @return array
 */
function coinsfera_lazy_attachment_attr( $attr ) {

	if ( empty( $attr['loading'] ) ) {
		$attr['loading'] = 'lazy';
	}
	if ( empty( $attr['decoding'] ) ) {
		$attr['decoding'] = 'async';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'coinsfera_lazy_attachment_attr', 20 );

/**
 * Load Yandex / Ahrefs / Trustpilot after idle time so they stay off the critical path.
 * GTM remains in the header.
 *
 * @return void
 */
function coinsfera_deferred_third_parties() {

	if ( is_admin() ) {
		return;
	}
	?>
<script>
(function () {
  var loaded = false;
  function load() {
    if (loaded) return;
    loaded = true;
    function add(src, attrs) {
      var s = document.createElement('script');
      s.src = src;
      s.async = true;
      if (attrs) Object.keys(attrs).forEach(function (k) { s.setAttribute(k, attrs[k]); });
      document.head.appendChild(s);
    }
    window.ym = window.ym || function () { (window.ym.a = window.ym.a || []).push(arguments); };
    window.ym.l = 1 * new Date();
    try { ym(90753747, 'init', { webvisor: true, clickmap: true, trackLinks: true, accurateTrackBounce: true, ecommerce: 'dataLayer' }); } catch (e) {}
    add('https://mc.yandex.ru/metrika/tag.js');
    add('https://analytics.ahrefs.com/analytics.js', { 'data-key': '0oCdoUr+VP9PqKZ3B8gzeg' });
    add('https://widget.trustpilot.com/bootstrap/v5/tp.widget.bootstrap.min.js');
  }
  ['scroll', 'click', 'touchstart', 'keydown'].forEach(function (ev) {
    window.addEventListener(ev, load, { once: true, passive: true });
  });
  setTimeout(load, 4000);
})();
</script>
	<?php
}
add_action( 'wp_footer', 'coinsfera_deferred_third_parties', 30 );
add_filter(
	'sgo_javascript_combine_excluded_inline_content',
	static function ( $exclude ) {
		$exclude   = is_array( $exclude ) ? $exclude : array();
		$exclude[] = 'mc.yandex.ru';
		$exclude[] = 'analytics.ahrefs.com';
		$exclude[] = 'widget.trustpilot.com';
		return $exclude;
	}
);
