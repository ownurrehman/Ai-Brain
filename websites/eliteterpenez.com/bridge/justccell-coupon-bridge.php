<?php
/**
 * Plugin Name: Justccell coupon bridge
 * Description: Applies ?apply_coupon= codes from Justccell magic links and bootstraps a WooCommerce REST key for cross-store free delivery.
 * Author: Rank Ray
 * Author URI: https://rankray.com
 * Version: 1.0.0
 *
 * Must-use plugin on eliteterpenez.com. Do not put secrets in this file.
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const ELITE_JC_BRIDGE_OPTION_CK     = 'elite_jc_bridge_ck';
const ELITE_JC_BRIDGE_OPTION_CS     = 'elite_jc_bridge_cs';
const ELITE_JC_BRIDGE_OPTION_KEY_ID = 'elite_jc_bridge_key_id';
const ELITE_JC_PENDING_COUPON       = 'elite_jc_pending_coupon';

add_action('before_woocommerce_init', static function (): void {
    if (class_exists(\Automattic\WooCommerce\Utilities\FeaturesUtil::class)) {
        \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility(
            'custom_order_tables',
            __FILE__,
            true
        );
    }
});

add_action('init', static function (): void {
    if (!class_exists('WooCommerce')) {
        return;
    }
    if (get_option('woocommerce_enable_coupons') !== 'yes') {
        update_option('woocommerce_enable_coupons', 'yes');
    }
}, 20);

add_filter('woocommerce_coming_soon', static function ($coming_soon) {
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return false;
    }
    return $coming_soon;
});

/**
 * Capture ?apply_coupon=JC-123 into the Woo session, then drop the query arg.
 */
add_action('wp_loaded', static function (): void {
    if (is_admin() || wp_doing_ajax() || wp_doing_cron()) {
        return;
    }
    if (!function_exists('WC') || !isset($_GET['apply_coupon'])) {
        return;
    }

    $raw  = sanitize_text_field(wp_unslash((string) $_GET['apply_coupon']));
    $code = function_exists('wc_format_coupon_code') ? wc_format_coupon_code($raw) : strtolower($raw);
    $code = preg_replace('/[^A-Za-z0-9\-_]/', '', (string) $code);
    if (!is_string($code) || $code === '') {
        return;
    }

    if (!WC()->session) {
        return;
    }
    if (!WC()->session->has_session()) {
        WC()->session->set_customer_session_cookie(true);
    }
    WC()->session->set(ELITE_JC_PENDING_COUPON, strtoupper($code));

    elite_jc_try_apply_coupon(strtoupper($code));

    if (!wp_doing_ajax() && isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] === 'GET') {
        wp_safe_redirect(remove_query_arg('apply_coupon'));
        exit;
    }
}, 25);

add_action('woocommerce_cart_loaded_from_session', static function (): void {
    elite_jc_apply_pending_coupon();
}, 30);

add_action('woocommerce_add_to_cart', static function (): void {
    elite_jc_apply_pending_coupon();
}, 30);

function elite_jc_pending_code(): string
{
    if (!function_exists('WC') || !WC()->session) {
        return '';
    }
    return strtoupper((string) WC()->session->get(ELITE_JC_PENDING_COUPON));
}

function elite_jc_try_apply_coupon(string $code): bool
{
    if ($code === '' || !function_exists('WC') || !WC()->cart) {
        return false;
    }
    if (WC()->cart->has_discount($code)) {
        return true;
    }

    $applied = WC()->cart->apply_coupon($code);
    return $applied === true;
}

function elite_jc_apply_pending_coupon(): void
{
    $code = elite_jc_pending_code();
    if ($code === '') {
        return;
    }
    elite_jc_try_apply_coupon($code);
}

add_filter('woocommerce_coupon_message', static function (string $msg, int $msg_code, $coupon): string {
    unset($msg_code);
    $pending = elite_jc_pending_code();
    if ($pending === '' || !$coupon instanceof WC_Coupon) {
        return $msg;
    }
    if (strtoupper((string) $coupon->get_code()) !== $pending) {
        return $msg;
    }
    return __('Justccell free delivery is ready. It applies at checkout on this order.', 'woocommerce');
}, 10, 3);

/**
 * Create a dedicated REST key once so Justccell can POST /wp-json/wc/v3/coupons.
 * Plaintext is shown only to shop managers on the bridge screen.
 */
add_action('admin_init', static function (): void {
    if (!current_user_can('manage_woocommerce') || !class_exists('WooCommerce')) {
        return;
    }
    elite_jc_ensure_rest_key();
    elite_jc_ensure_coupon_shipping_method();
}, 20);

function elite_jc_ensure_coupon_shipping_method(): void
{
    if (get_option('elite_jc_shipping_seeded') === '1') {
        return;
    }
    if (!class_exists('WC_Shipping_Zone') || elite_jc_has_coupon_free_shipping()) {
        update_option('elite_jc_shipping_seeded', '1', false);
        return;
    }

    $zone = new WC_Shipping_Zone(0);
    $instance_id = (int) $zone->add_shipping_method('free_shipping');
    if ($instance_id > 0) {
        foreach ($zone->get_shipping_methods(true) as $method) {
            if ((int) $method->get_instance_id() !== $instance_id) {
                continue;
            }
            if ($method instanceof WC_Shipping_Free_Shipping) {
                $method->update_option('title', 'Free delivery');
                $method->update_option('requires', 'coupon');
            }
        }
    }
    update_option('elite_jc_shipping_seeded', '1', false);
}

function elite_jc_ensure_rest_key(): void
{
    if (!function_exists('wc_rand_hash') || !function_exists('wc_api_hash')) {
        return;
    }
    if ((int) get_option(ELITE_JC_BRIDGE_OPTION_KEY_ID) > 0 && (string) get_option(ELITE_JC_BRIDGE_OPTION_CK) !== '') {
        return;
    }

    global $wpdb;
    $table = $wpdb->prefix . 'woocommerce_api_keys';
    $exists = $wpdb->get_var("SHOW TABLES LIKE '{$table}'");
    if ($exists !== $table) {
        return;
    }

    $user_id = get_current_user_id();
    if ($user_id < 1) {
        $user_id = 1;
    }

    $consumer_key    = 'ck_' . wc_rand_hash();
    $consumer_secret = 'cs_' . wc_rand_hash();

    $inserted = $wpdb->insert(
        $table,
        [
            'user_id'         => $user_id,
            'description'     => 'Justccell cross-sell',
            'permissions'     => 'read_write',
            'consumer_key'    => wc_api_hash($consumer_key),
            'consumer_secret' => $consumer_secret,
            'truncated_key'   => substr($consumer_key, -7),
        ],
        ['%d', '%s', '%s', '%s', '%s', '%s']
    );

    if (!$inserted) {
        return;
    }

    update_option(ELITE_JC_BRIDGE_OPTION_CK, $consumer_key, false);
    update_option(ELITE_JC_BRIDGE_OPTION_CS, $consumer_secret, false);
    update_option(ELITE_JC_BRIDGE_OPTION_KEY_ID, (int) $wpdb->insert_id, false);
}

add_action('admin_menu', static function (): void {
    add_submenu_page(
        'woocommerce',
        __('Justccell bridge', 'woocommerce'),
        __('Justccell bridge', 'woocommerce'),
        'manage_woocommerce',
        'elite-jc-bridge',
        'elite_jc_render_bridge_page'
    );
});

function elite_jc_render_bridge_page(): void
{
    if (!current_user_can('manage_woocommerce')) {
        return;
    }
    elite_jc_ensure_rest_key();

    $ck = (string) get_option(ELITE_JC_BRIDGE_OPTION_CK);
    $cs = (string) get_option(ELITE_JC_BRIDGE_OPTION_CS);
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Justccell coupon bridge', 'woocommerce'); ?></h1>
        <p><?php esc_html_e('Paste these values into Justccell → Elite Cross-sell. They are shown here once so Rank Ray can connect the stores. Rotate them in WooCommerce → Settings → Advanced → REST API if they leak.', 'woocommerce'); ?></p>
        <table class="form-table" role="presentation">
            <tr>
                <th scope="row"><?php esc_html_e('Consumer Key', 'woocommerce'); ?></th>
                <td><code><?php echo $ck !== '' ? esc_html($ck) : esc_html__('Not generated yet — open this page after WooCommerce is active.', 'woocommerce'); ?></code></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('Consumer Secret', 'woocommerce'); ?></th>
                <td><code><?php echo $cs !== '' ? esc_html($cs) : '—'; ?></code></td>
            </tr>
            <tr>
                <th scope="row"><?php esc_html_e('API URL', 'woocommerce'); ?></th>
                <td><code><?php echo esc_html(untrailingslashit(home_url())); ?></code></td>
            </tr>
        </table>
        <p><?php esc_html_e('Magic links from Justccell look like /?apply_coupon=JC-123. The coupon is stored on the cart and applied at checkout. Enable a Free shipping method that allows coupons so the 0% + free_shipping coupon can zero delivery.', 'woocommerce'); ?></p>
    </div>
    <?php
}

add_action('admin_notices', static function (): void {
    if (!current_user_can('manage_woocommerce') || !class_exists('WooCommerce')) {
        return;
    }
    if (!function_exists('wc_get_shipping_zone')) {
        return;
    }
    if (!elite_jc_has_coupon_free_shipping()) {
        echo '<div class="notice notice-warning"><p>';
        echo esc_html__('Justccell free-delivery coupons need a WooCommerce Free shipping method with “A valid free shipping coupon”. Add it under WooCommerce → Settings → Shipping.', 'woocommerce');
        echo '</p></div>';
    }
});

function elite_jc_has_coupon_free_shipping(): bool
{
    if (!class_exists('WC_Shipping_Zones')) {
        return false;
    }
    $zones = WC_Shipping_Zones::get_zones();
    $zones[] = ['id' => 0];
    foreach ($zones as $zone_data) {
        $zone_id = isset($zone_data['id']) ? (int) $zone_data['id'] : 0;
        $zone    = new WC_Shipping_Zone($zone_id);
        foreach ($zone->get_shipping_methods(true) as $method) {
            if (!$method instanceof WC_Shipping_Free_Shipping) {
                continue;
            }
            $req = (string) $method->get_option('requires');
            if (in_array($req, ['coupon', 'either', 'both'], true)) {
                return true;
            }
        }
    }
    return false;
}
