<?php
/**
 * My Account page.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 3.5.0
 *
 * Developed by Rank Ray — https://rankray.com
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

$endpoint = function_exists('WC') && WC()->query instanceof WC_Query
    ? (string) WC()->query->get_current_endpoint()
    : '';
$heading  = __('My account', 'justccell');
if ($endpoint !== '' && function_exists('WC') && isset(WC()->query->query_vars[$endpoint])) {
    $items = function_exists('wc_get_account_menu_items') ? wc_get_account_menu_items() : [];
    if (isset($items[$endpoint]) && is_string($items[$endpoint]) && $items[$endpoint] !== '') {
        $heading = $items[$endpoint];
    }
}
?>
<div class="jc-account">
    <header class="jc-account__hero">
        <p class="jc-account__kicker"><?php esc_html_e('Customer area', 'justccell'); ?></p>
        <h1 class="jc-account__title"><?php echo esc_html($heading); ?></h1>
    </header>
    <div class="jc-account__layout">
        <?php
        /**
         * My Account navigation.
         *
         * @since 2.6.0
         */
        do_action('woocommerce_account_navigation');
        ?>

        <div class="woocommerce-MyAccount-content jc-account__content">
            <?php
            /**
             * My Account content.
             *
             * @since 2.6.0
             */
            do_action('woocommerce_account_content');
            ?>
        </div>
    </div>
</div>
