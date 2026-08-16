<?php
/**
 * Backlink Crypto theme functions.
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('BACKLINKCRYPTO_VERSION', '1.15.1');
define('BACKLINKCRYPTO_DIR', get_template_directory());
define('BACKLINKCRYPTO_URI', get_template_directory_uri());

require_once BACKLINKCRYPTO_DIR . '/inc/setup.php';
require_once BACKLINKCRYPTO_DIR . '/inc/marketplace.php';
require_once BACKLINKCRYPTO_DIR . '/inc/gallery.php';
require_once BACKLINKCRYPTO_DIR . '/inc/contact.php';
require_once BACKLINKCRYPTO_DIR . '/inc/theme-settings.php';
require_once BACKLINKCRYPTO_DIR . '/inc/inventory-manager.php';
require_once BACKLINKCRYPTO_DIR . '/inc/catalog-filter.php';
require_once BACKLINKCRYPTO_DIR . '/inc/catalog-sync.php';
require_once BACKLINKCRYPTO_DIR . '/inc/offers.php';
require_once BACKLINKCRYPTO_DIR . '/inc/woocommerce.php';
require_once BACKLINKCRYPTO_DIR . '/inc/ajax-cart.php';
require_once BACKLINKCRYPTO_DIR . '/inc/seo-aioseo.php';
require_once BACKLINKCRYPTO_DIR . '/inc/blog-seed.php';
require_once BACKLINKCRYPTO_DIR . '/inc/logo-media.php';
require_once BACKLINKCRYPTO_DIR . '/inc/seed.php';
require_once BACKLINKCRYPTO_DIR . '/inc/placements.php';
require_once BACKLINKCRYPTO_DIR . '/inc/sales-ops.php';
