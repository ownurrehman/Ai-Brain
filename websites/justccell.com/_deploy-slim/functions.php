<?php
/**
 * Justccell theme bootstrap.
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

define('JUSTCCELL_VERSION', '0.5.6');
define('JUSTCCELL_DIR', get_template_directory());
define('JUSTCCELL_URI', get_template_directory_uri());
// Hostinger activates a hashed theme folder. Photos stay in justccell-theme.
$justccell_stable_dir = get_theme_root() . '/justccell-theme';
$justccell_stable_uri = get_theme_root_uri() . '/justccell-theme';
define(
    'JUSTCCELL_MEDIA_URI',
    is_dir($justccell_stable_dir . '/assets/img/ref/tank-360')
        ? $justccell_stable_uri
        : JUSTCCELL_URI
);
unset($justccell_stable_dir, $justccell_stable_uri);

require_once JUSTCCELL_DIR . '/inc/setup.php';
require_once JUSTCCELL_DIR . '/inc/storefront.php';
require_once JUSTCCELL_DIR . '/inc/wpml-lock.php';
require_once JUSTCCELL_DIR . '/inc/catalog.php';
require_once JUSTCCELL_DIR . '/inc/product-pages.php';
require_once JUSTCCELL_DIR . '/inc/static-pages.php';
require_once JUSTCCELL_DIR . '/inc/media-import.php';
require_once JUSTCCELL_DIR . '/inc/nav-fallback.php';
require_once JUSTCCELL_DIR . '/inc/assets.php';
require_once JUSTCCELL_DIR . '/inc/acf.php';
require_once JUSTCCELL_DIR . '/inc/woocommerce.php';
require_once JUSTCCELL_DIR . '/inc/inquiry.php';
