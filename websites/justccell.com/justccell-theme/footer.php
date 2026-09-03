<?php
/**
 * @package Justccell
 *
 * Developed by Rank Ray — https://rankray.com
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}
?>
</main>
<?php get_template_part('template-parts/footer/site-footer'); ?>
<?php get_template_part('template-parts/chrome/chat-dock'); ?>
<?php if (class_exists('WooCommerce')) : ?>
    <?php get_template_part('template-parts/cart/drawer'); ?>
<?php endif; ?>
<?php wp_footer(); ?>
</body>
</html>
