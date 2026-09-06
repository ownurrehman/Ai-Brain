<?php
/**
 * Plugin Name: JC Admin Fatal Log (temp)
 * Description: One-shot fatal capture for wp-admin edit screens — delete after read.
 */
declare(strict_types=1);

register_shutdown_function(static function (): void {
    $error = error_get_last();
    if ($error === null) {
        return;
    }
    $fatal = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR];
    if (!in_array($error['type'], $fatal, true)) {
        return;
    }
    $path = WP_CONTENT_DIR . '/jc-admin-fatal.log';
    $line = wp_json_encode([
        'time'    => gmdate('c'),
        'request' => isset($_SERVER['REQUEST_URI']) ? (string) $_SERVER['REQUEST_URI'] : '',
        'error'   => $error,
    ]);
    if (is_string($line)) {
        file_put_contents($path, $line . PHP_EOL, FILE_APPEND | LOCK_EX);
    }
});
