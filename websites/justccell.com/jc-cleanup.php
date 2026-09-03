<?php
if (isset($_GET['key']) && hash_equals('jc-restore-0944', (string) $_GET['key'])) {
    foreach (['jc-extract-theme.php', 'jc-wp-test.php', 'jc-restore-old-theme.php'] as $f) {
        $p = __DIR__ . '/' . $f;
        if (is_file($p)) {
            unlink($p);
        }
    }
    echo 'cleaned';
}
