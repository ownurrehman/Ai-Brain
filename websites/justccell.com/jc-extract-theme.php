<?php
declare(strict_types=1);
if (!isset($_GET['key']) || !hash_equals('jc-restore-0944', (string) $_GET['key'])) {
    http_response_code(403);
    exit('forbidden');
}
header('Content-Type: text/plain; charset=utf-8');
$root = __DIR__;
$zip  = $root . '/wp-content/themes/justccell-theme-restore.zip';
$dest = $root . '/wp-content/themes';
$target = $dest . '/justccell-theme';
function jc_rrmdir(string $dir): void {
    if (!is_dir($dir)) return;
    foreach (scandir($dir) ?: [] as $item) {
        if ($item === '.' || $item === '..') continue;
        $path = $dir . '/' . $item;
        is_dir($path) ? jc_rrmdir($path) : unlink($path);
    }
    rmdir($dir);
}
if (!is_file($zip)) exit('no zip');
jc_rrmdir($target);
$za = new ZipArchive();
if ($za->open($zip) !== true) exit('open fail');
if (!$za->extractTo($dest)) exit('extract fail');
$za->close();
echo is_file($target . '/inc/breadcrumbs.php') ? 'full-ok' : 'partial';
