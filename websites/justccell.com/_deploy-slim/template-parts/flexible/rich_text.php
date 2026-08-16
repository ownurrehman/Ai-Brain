<?php
/**
 * Rich text / Gutenberg-adjacent ACF block.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$content = function_exists('get_sub_field') ? (string) get_sub_field('content') : '';
if ($content === '') {
    return;
}
?>
<section class="rich-section">
    <div class="container entry-content">
        <?php echo wp_kses_post($content); ?>
    </div>
</section>
