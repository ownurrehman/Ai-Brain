<?php
/**
 * Plugin Name: Justccell Media Pack 2
 * Description: Extra Justccell photos. Unzips into ref/ on first load for Media Library sideload.
 * Version: 1.0.1
 * Text Domain: justccell
 */
if (!defined('ABSPATH')) {
    exit;
}

add_action('plugins_loaded', static function (): void {
    $dir = __DIR__ . '/ref';
    $zip = __DIR__ . '/ref.zip';
    $ready = is_dir($dir) && count(glob($dir . '/*') ?: []) > 5;
    if ($ready || !is_readable($zip) || !class_exists('ZipArchive')) {
        return;
    }
    if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
        return;
    }
    $archive = new ZipArchive();
    if ($archive->open($zip) !== true) {
        return;
    }
    $archive->extractTo($dir);
    $archive->close();
}, 1);
