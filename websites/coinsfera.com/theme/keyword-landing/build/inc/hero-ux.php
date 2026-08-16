<?php
/**
 * Homepage hero UX: mobile banner align, tablet header, Google trust strip.
 *
 * This used to live in an Elementor HTML widget. WPML's translation editor
 * strips <style> and <script>, which printed the raw CSS/JS on /tr/.
 *
 * @package Coinsfera_WordPress_Theme
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Print hero CSS on the public site.
 *
 * @return void
 */
function coinsfera_hero_ux_css() {

	if ( is_admin() ) {
		return;
	}
	?>
<style id="coinsfera-mobile-overflow-fix">
/* coinsfera mobile banner align + overflow + hero UX 2026-08-11 */
@media (max-width: 767px) {
  .home-banner .static-banner-image,
  .home-banner .banner-item,
  .home-banner .item {
    width: 100% !important;
    max-width: 100% !important;
    margin-left: auto !important;
    margin-right: auto !important;
    display: block !important;
    box-sizing: border-box !important;
  }
  .home-banner img.banner-img,
  .home-banner .banner-img {
    display: block !important;
    width: 100% !important;
    max-width: 100% !important;
    height: auto !important;
    margin-left: auto !important;
    margin-right: auto !important;
  }
  .home-banner .add-meta {
    width: 100% !important;
    max-width: 100% !important;
    margin-left: 0 !important;
    margin-right: 0 !important;
    box-sizing: border-box !important;
  }
}

.home-banner .banner-heading,
.home-banner h1.banner-heading {
  font-size: clamp(28px, 5.5vw, 42px) !important;
  line-height: 1.2 !important;
  font-weight: 600 !important;
  color: #202020 !important;
}
.home-banner .banne-tag-line,
.home-banner p.banne-tag-line {
  font-size: 14px !important;
  line-height: 1.35 !important;
  font-weight: 500 !important;
  letter-spacing: 0.02em !important;
  color: #6b6b6b !important;
}

.home-banner a.banner-btn,
.home-banner .banner-btn {
  background: #F9A541 !important;
  border: 2px solid #F9A541 !important;
  color: #ffffff !important;
  box-shadow: none !important;
}
.home-banner a.banner-btn:hover,
.home-banner a.banner-btn:focus,
.home-banner .banner-btn:hover,
.home-banner .banner-btn:focus {
  background: #e8942f !important;
  border-color: #e8942f !important;
  color: #ffffff !important;
}
.home-banner a.banner-btn i {
  color: #ffffff !important;
  margin-right: 8px;
}

.cf-trust-strip {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  justify-content: center;
  gap: 8px 12px;
  margin: 18px 0 0;
  padding: 0;
}
@media (min-width: 992px) {
  .cf-trust-strip { justify-content: flex-start; }
}
.cf-trust-strip a {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  text-decoration: none !important;
  color: #202020 !important;
  background: #ffffff;
  border: 1px solid #e6e2dc;
  border-radius: 999px;
  padding: 8px 14px;
  font-size: 14px;
  line-height: 1.2;
  font-weight: 500;
}
.cf-trust-strip a:hover { border-color: #F9A541; }
.cf-trust-strip .cf-stars { color: #F9A541; letter-spacing: 1px; font-size: 15px; }
.cf-trust-strip .cf-score { font-weight: 700; }
.cf-trust-strip .cf-count { color: #6b6b6b; font-weight: 500; }

@media (max-width: 991.98px) {
  header.topbar {
    height: auto !important;
    min-height: 92px;
  }
  header.topbar .container {
    max-width: 100%;
  }
  header.topbar .navbar.navbar-expand-md {
    flex-wrap: wrap !important;
    align-items: center !important;
  }
  header.topbar .navbar-header {
    width: 100% !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    min-height: 92px;
  }
  header.topbar .navbar.navbar-expand-md .navbar-toggler {
    display: block !important;
    margin-left: auto;
    order: 2;
  }
  header.topbar .navbar.navbar-expand-md .navbar-brand {
    order: 1;
  }
  header.topbar .navbar.navbar-expand-md .navbar-collapse {
    display: none !important;
    flex-basis: 100% !important;
    flex-grow: 1 !important;
    width: 100% !important;
    max-width: 100% !important;
    order: 3;
  }
  header.topbar .navbar.navbar-expand-md .navbar-collapse.show {
    display: flex !important;
    flex-direction: column !important;
    align-items: stretch !important;
    background: #ffffff;
    padding: 8px 0 18px;
    border-top: 1px solid #eee;
    max-height: calc(100vh - 92px);
    overflow-x: hidden;
    overflow-y: auto;
  }
  header.topbar .navbar.navbar-expand-md .navbar-collapse.show > .d-block.d-lg-none,
  header.topbar .navbar.navbar-expand-md .navbar-collapse.show > .language-switcher,
  header.topbar .navbar.navbar-expand-md .navbar-collapse.show > .btn-contact {
    width: 100%;
    margin-left: 0 !important;
    margin-right: 0 !important;
  }
  header.topbar .navbar.navbar-expand-md .navbar-collapse.show .mobile-menu {
    width: 100%;
    flex-direction: column !important;
    align-items: stretch !important;
  }
  header.topbar .navbar.navbar-expand-md .navbar-collapse.show .btn-contact {
    display: inline-flex !important;
    width: auto !important;
    align-self: center !important;
    margin-top: 10px !important;
    margin-left: auto !important;
    margin-right: auto !important;
  }
  header.topbar .navbar.navbar-expand-md .navbar-collapse.show .language-switcher {
    display: block !important;
    margin-top: 12px !important;
  }
  header.topbar .navbar.navbar-expand-md .navbar-collapse:not(.show) .language-switcher,
  header.topbar .navbar.navbar-expand-md .navbar-collapse:not(.show) .btn-contact,
  header.topbar .navbar.navbar-expand-md .navbar-collapse:not(.show) .wpml-ls {
    position: static !important;
  }
}

@media (min-width: 992px) and (max-width: 1199.98px) {
  header.topbar .custom-main-menu.desktop-menu > .nav-item > .nav-link {
    padding-left: 10px !important;
    padding-right: 10px !important;
    font-size: 15px !important;
  }
  header.topbar .btn-contact {
    padding-left: 14px !important;
    padding-right: 14px !important;
  }
}
</style>
<script id="coinsfera-hero-ux">
(function () {
  function placeTrustStrip() {
    if (document.getElementById('cf-trust-strip')) return;
    var sub = document.querySelector('.home-banner .banner-subtext');
    var btn = document.querySelector('.home-banner a.banner-btn, .home-banner .banner-btn');
    if (!sub) return;
    var el = document.createElement('div');
    el.id = 'cf-trust-strip';
    el.className = 'cf-trust-strip';
    el.innerHTML = '<a href="https://share.google/jZcVhdUkybPpbiz7O" target="_blank" rel="noopener noreferrer" aria-label="Coinsfera Google rating 4.9 out of 5 from 1043 reviews">'
      + '<span class="cf-stars" aria-hidden="true">★★★★★</span>'
      + '<span class="cf-score">4.9</span>'
      + '<span class="cf-count">1,043 Google reviews</span>'
      + '</a>';
    if (btn && btn.parentElement === sub.parentElement) {
      sub.parentElement.insertBefore(el, btn);
    } else {
      sub.insertAdjacentElement('afterend', el);
    }
  }
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', placeTrustStrip);
  } else {
    placeTrustStrip();
  }
})();
</script>
	<?php
}
add_action( 'wp_head', 'coinsfera_hero_ux_css', 40 );

/**
 * Keep the trust-strip script out of SiteGround JS combine, which was
 * dropping the inline tag and never adding it to the combined file.
 *
 * @param array $exclude Handles or snippets to skip.
 * @return array
 */
function coinsfera_hero_ux_sg_exclude( $exclude ) {

	$exclude   = is_array( $exclude ) ? $exclude : array();
	$exclude[] = 'coinsfera-hero-ux';
	$exclude[] = 'placeTrustStrip';
	return $exclude;
}
add_filter( 'sgo_javascript_combine_exclude', 'coinsfera_hero_ux_sg_exclude' );
add_filter( 'sgo_js_minify_exclude', 'coinsfera_hero_ux_sg_exclude' );
add_filter( 'sgo_javascript_combine_excluded_inline_content', 'coinsfera_hero_ux_sg_exclude' );

/**
 * Inject the Google reviews strip under homepage hero copy.
 *
 * @return void
 */
function coinsfera_hero_ux_js() {

	if ( is_admin() ) {
		return;
	}

	$src = COINSFERA_URI . '/assets/js/hero-ux.js';
	$path = COINSFERA_PATH . '/assets/js/hero-ux.js';
	$ver  = file_exists( $path ) ? (string) filemtime( $path ) : '1';

	wp_enqueue_script( 'coinsfera-hero-ux', $src, array(), $ver, true );
}
add_action( 'wp_enqueue_scripts', 'coinsfera_hero_ux_js', 20 );
