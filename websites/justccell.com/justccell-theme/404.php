<?php
/**
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

get_header();
?>
<section class="container page-article">
    <h1 class="page-article__title"><?php esc_html_e('Page not found', 'justccell'); ?></h1>
    <p><?php esc_html_e('The page you requested does not exist.', 'justccell'); ?></p>
    <p><a class="btn btn--primary" href="<?php echo esc_url(home_url('/')); ?>"><?php esc_html_e('Back home', 'justccell'); ?></a></p>
</section>
<?php
get_footer();
