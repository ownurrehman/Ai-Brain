<?php
/**
 * Legacy loader shim — modules are loaded from justccell-features.php.
 *
 * @package Justccell_Features
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

if (function_exists('justccell_features_load_modules')) {
    justccell_features_load_modules();
}
