<?php
/**
 * Justccell theme bootstrap.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('JUSTCCELL_VERSION', '0.9.219');
define('JUSTCCELL_DIR', get_template_directory());
define('JUSTCCELL_URI', get_template_directory_uri());
define('JUSTCCELL_DEVELOPER', 'Rank Ray');
define('JUSTCCELL_DEVELOPER_URL', 'https://rankray.com');

require_once JUSTCCELL_DIR . '/inc/setup.php';
require_once JUSTCCELL_DIR . '/inc/storefront.php';
require_once JUSTCCELL_DIR . '/inc/tiered-pricing.php';
require_once JUSTCCELL_DIR . '/inc/commerce.php';
require_once JUSTCCELL_DIR . '/inc/commerce-pages.php';
require_once JUSTCCELL_DIR . '/inc/cart-ajax.php';
require_once JUSTCCELL_DIR . '/inc/laser-engraving.php';
require_once JUSTCCELL_DIR . '/inc/admin-laser-zone.php';
require_once JUSTCCELL_DIR . '/inc/forms-settings.php';
require_once JUSTCCELL_DIR . '/inc/wpml-lock.php';
require_once JUSTCCELL_DIR . '/inc/cms-helpers.php';
require_once JUSTCCELL_DIR . '/inc/breadcrumbs.php';
require_once JUSTCCELL_DIR . '/inc/page-layouts.php';
require_once JUSTCCELL_DIR . '/inc/catalog.php';
require_once JUSTCCELL_DIR . '/inc/listing.php';
require_once JUSTCCELL_DIR . '/inc/product-pages.php';
require_once JUSTCCELL_DIR . '/inc/catalog-redirects.php';
require_once JUSTCCELL_DIR . '/inc/static-pages.php';
require_once JUSTCCELL_DIR . '/inc/coming-soon-page.php';
require_once JUSTCCELL_DIR . '/inc/rest-privacy.php';
require_once JUSTCCELL_DIR . '/inc/copy-policy.php';
require_once JUSTCCELL_DIR . '/inc/bio-heating.php';
require_once JUSTCCELL_DIR . '/inc/contact-page.php';
require_once JUSTCCELL_DIR . '/inc/locations-page.php';
require_once JUSTCCELL_DIR . '/inc/cms-content.php';
require_once JUSTCCELL_DIR . '/inc/admin-menu.php';
require_once JUSTCCELL_DIR . '/inc/nav-fallback.php';
require_once JUSTCCELL_DIR . '/inc/assets.php';
require_once JUSTCCELL_DIR . '/inc/acf.php';
if (is_readable(JUSTCCELL_DIR . '/inc/acf-page-groups.php')) {
    require_once JUSTCCELL_DIR . '/inc/acf-page-groups.php';
}
require_once JUSTCCELL_DIR . '/inc/acf-catalog-pages.php';
require_once JUSTCCELL_DIR . '/inc/acf-remaining-pages.php';
require_once JUSTCCELL_DIR . '/inc/acf-fields.php';
require_once JUSTCCELL_DIR . '/inc/cms-import.php';
require_once JUSTCCELL_DIR . '/inc/woocommerce.php';
require_once JUSTCCELL_DIR . '/inc/elite-cross-sell.php';
require_once JUSTCCELL_DIR . '/inc/inquiry.php';
require_once JUSTCCELL_DIR . '/inc/chrome.php';
require_once JUSTCCELL_DIR . '/inc/header-menu.php';
require_once JUSTCCELL_DIR . '/inc/footer-menus.php';
require_once JUSTCCELL_DIR . '/inc/blog.php';
