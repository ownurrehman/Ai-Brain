<?php
/**
 * Page template (cart/checkout get marketplace chrome + WC shortcodes).
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

get_header();

$is_cart     = function_exists('is_cart') && is_cart();
$is_checkout = function_exists('is_checkout') && is_checkout();
$is_account  = function_exists('is_account_page') && is_account_page();
$is_commerce = $is_cart || $is_checkout || $is_account;
$wrap_class  = $is_commerce ? 'bc-container bc-commerce' : 'bc-container bc-narrow';
?>
<main class="bc-main<?php echo $is_commerce ? ' bc-main--commerce' : ''; ?>">
    <div class="<?php echo esc_attr($wrap_class); ?>">
        <?php while (have_posts()) : the_post(); ?>
            <article <?php post_class('bc-page' . ($is_commerce ? ' bc-page--commerce' : '')); ?>>
                <?php if ($is_commerce && !$is_account) : ?>
                    <header class="bc-commerce__head">
                        <p class="bc-eyebrow"><?php esc_html_e('Backlink Crypto', 'backlinkcrypto'); ?></p>
                        <h1><?php the_title(); ?></h1>
                    </header>
                <?php elseif (!$is_commerce) : ?>
                    <h1><?php the_title(); ?></h1>
                <?php endif; ?>

                <div class="bc-prose bc-page-body">
                    <?php
                    // Cart / checkout pages often lose their shortcode/block — always render WC UI.
                    if ($is_cart) {
                        echo do_shortcode('[woocommerce_cart]');
                    } elseif ($is_checkout) {
                        echo do_shortcode('[woocommerce_checkout]');
                    } else {
                        the_content();
                    }
                    ?>
                </div>
            </article>
        <?php endwhile; ?>
    </div>
</main>
<?php
get_footer();
