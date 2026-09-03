<?php
/**
 * My Account dashboard.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 4.4.0
 *
 * Developed by Rank Ray — https://rankray.com
 */

declare(strict_types=1);

defined('ABSPATH') || exit;

$allowed_html = [
    'a' => [
        'href' => [],
    ],
];

$items = function_exists('wc_get_account_menu_items') ? wc_get_account_menu_items() : [];
$skip  = ['dashboard', 'customer-logout'];
$tiles = [];
foreach ($items as $endpoint => $label) {
    if (in_array($endpoint, $skip, true)) {
        continue;
    }
    $url = function_exists('wc_get_account_endpoint_url')
        ? wc_get_account_endpoint_url($endpoint)
        : '';
    if ($url === '') {
        continue;
    }
    $tiles[] = [
        'endpoint' => $endpoint,
        'label'    => $label,
        'url'      => $url,
    ];
}
?>
<section class="jc-account-dash">
    <p class="jc-account-dash__welcome">
        <?php
        printf(
            /* translators: 1: user display name, 2: logout url */
            wp_kses(__('Hello %1$s (not %1$s? <a href="%2$s">Log out</a>)', 'woocommerce'), $allowed_html),
            '<strong>' . esc_html($current_user->display_name) . '</strong>',
            esc_url(wc_logout_url())
        );
        ?>
    </p>
    <p class="jc-account-dash__copy">
        <?php
        $orders_url   = wc_get_endpoint_url('orders', '', wc_get_page_permalink('myaccount'));
        $edit_url     = wc_get_endpoint_url('edit-account', '', wc_get_page_permalink('myaccount'));
        $address_url  = wc_get_endpoint_url('edit-address', '', wc_get_page_permalink('myaccount'));
        $allowed_copy = [
            'a'      => ['href' => []],
            'strong' => [],
        ];
        echo wp_kses(
            sprintf(
                /* translators: 1: Orders URL 2: Addresses URL 3: Account details URL */
                __('From your account dashboard you can view your <a href="%1$s">recent orders</a>, manage your <a href="%2$s">billing and shipping addresses</a>, and <a href="%3$s">edit your password and account details</a>.', 'woocommerce'),
                esc_url($orders_url),
                esc_url($address_url),
                esc_url($edit_url)
            ),
            $allowed_copy
        );
        ?>
    </p>

    <?php if ($tiles !== []) : ?>
        <ul class="jc-account-tiles" role="list">
            <?php foreach ($tiles as $tile) : ?>
                <li>
                    <a class="jc-account-tile" href="<?php echo esc_url((string) $tile['url']); ?>">
                        <span class="jc-account-tile__label"><?php echo esc_html((string) $tile['label']); ?></span>
                        <span class="jc-account-tile__go" aria-hidden="true">→</span>
                    </a>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</section>

<?php
/**
 * My Account dashboard.
 *
 * @since 2.6.0
 */
do_action('woocommerce_account_dashboard');

/**
 * Deprecated woocommerce_before_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_before_my_account');

/**
 * Deprecated woocommerce_after_my_account action.
 *
 * @deprecated 2.6.0
 */
do_action('woocommerce_after_my_account');
