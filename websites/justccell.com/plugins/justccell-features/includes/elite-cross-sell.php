<?php
/**
 * Cross-domain Elite Terpenes free-shipping coupon (WooCommerce REST).
 *
 * After a Justccell order reaches processing/completed, a unique coupon is
 * created on eliteterpenez.com via POST /wp-json/wc/v3/coupons. Checkout never
 * waits on the remote site: Action Scheduler runs first; a 4s timeout is the
 * hard cap on any inline fallback (thank-you / email).
 *
 * Credentials: Justccell → Elite Cross-sell, or wp-config constants.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_ELITE_META_COUPON   = '_elite_cross_sell_coupon';
const JUSTCCELL_ELITE_META_EXPIRES  = '_elite_cross_sell_expires';
const JUSTCCELL_ELITE_META_ERROR    = '_elite_cross_sell_last_error';
const JUSTCCELL_ELITE_META_LOCK     = '_elite_cross_sell_lock';
const JUSTCCELL_ELITE_ACTION        = 'justccell_elite_create_coupon';
const JUSTCCELL_ELITE_TIMEOUT       = 4;
const JUSTCCELL_ELITE_TTL_HOURS     = 48;
const JUSTCCELL_ELITE_OPTION        = 'justccell_elite_cross_sell';

/**
 * @return array{
 *     enabled:string,
 *     api_url:string,
 *     consumer_key:string,
 *     consumer_secret:string,
 *     store_url:string,
 *     heading:string,
 *     body:string,
 *     kicker:string,
 *     code_label:string,
 *     cta_label:string,
 *     copy_label:string
 * }
 */
function justccell_elite_defaults(): array
{
    return [
        'enabled'         => 'yes',
        'api_url'         => 'https://eliteterpenez.com',
        'consumer_key'    => '',
        'consumer_secret' => '',
        'store_url'       => 'https://eliteterpenez.com',
        'heading'         => __('Unlock Free Delivery on Elite Terpenes for the next 48 hours.', 'justccell'),
        'body'            => __('Your Justccell order unlocks free delivery on Elite Terpenes. Use the code below, or tap the button to apply it automatically.', 'justccell'),
        'kicker'          => __('Elite Terpenes', 'justccell'),
        'code_label'      => __('Your unique code', 'justccell'),
        'cta_label'       => __('Shop Elite Terpenes', 'justccell'),
        'copy_label'      => __('Copy code', 'justccell'),
    ];
}

/**
 * @return array<string, string>
 */
function justccell_elite_settings(): array
{
    $defaults = justccell_elite_defaults();
    $saved    = get_option(JUSTCCELL_ELITE_OPTION, []);
    if (!is_array($saved)) {
        $saved = [];
    }

    $out = [];
    foreach ($defaults as $key => $fallback) {
        $value = isset($saved[$key]) && is_string($saved[$key]) ? $saved[$key] : $fallback;
        $out[$key] = $value;
    }

    if (defined('JUSTCCELL_ELITE_API_URL') && is_string(JUSTCCELL_ELITE_API_URL) && JUSTCCELL_ELITE_API_URL !== '') {
        $out['api_url'] = JUSTCCELL_ELITE_API_URL;
    }
    if (defined('JUSTCCELL_ELITE_STORE_URL') && is_string(JUSTCCELL_ELITE_STORE_URL) && JUSTCCELL_ELITE_STORE_URL !== '') {
        $out['store_url'] = JUSTCCELL_ELITE_STORE_URL;
    }
    if (defined('JUSTCCELL_ELITE_CONSUMER_KEY') && is_string(JUSTCCELL_ELITE_CONSUMER_KEY) && JUSTCCELL_ELITE_CONSUMER_KEY !== '') {
        $out['consumer_key'] = JUSTCCELL_ELITE_CONSUMER_KEY;
    }
    if (defined('JUSTCCELL_ELITE_CONSUMER_SECRET') && is_string(JUSTCCELL_ELITE_CONSUMER_SECRET) && JUSTCCELL_ELITE_CONSUMER_SECRET !== '') {
        $out['consumer_secret'] = JUSTCCELL_ELITE_CONSUMER_SECRET;
    }

    $out['api_url']   = untrailingslashit(esc_url_raw($out['api_url']));
    $out['store_url'] = untrailingslashit(esc_url_raw($out['store_url'] !== '' ? $out['store_url'] : $out['api_url']));

    return $out;
}

function justccell_elite_is_enabled(): bool
{
    $settings = justccell_elite_settings();
    return $settings['enabled'] === 'yes'
        && $settings['api_url'] !== ''
        && $settings['consumer_key'] !== ''
        && $settings['consumer_secret'] !== '';
}

function justccell_elite_coupon_code(int $order_id): string
{
    return 'JC-' . $order_id;
}

function justccell_elite_magic_url(string $code): string
{
    $settings = justccell_elite_settings();
    $base     = $settings['store_url'] !== '' ? $settings['store_url'] : $settings['api_url'];
    if ($base === '') {
        $base = 'https://eliteterpenez.com';
    }

    return add_query_arg('apply_coupon', rawurlencode($code), trailingslashit($base));
}

add_action('admin_menu', static function (): void {
    add_submenu_page(
        JUSTCCELL_ADMIN_SLUG,
        __('Elite Cross-sell', 'justccell'),
        __('Elite Cross-sell', 'justccell'),
        'manage_woocommerce',
        'justccell-elite-cross-sell',
        'justccell_elite_render_settings_page'
    );
}, 16);

add_action('admin_init', 'justccell_elite_handle_settings_save');

function justccell_elite_handle_settings_save(): void
{
    if (!is_admin() || !isset($_POST['justccell_elite_save'])) {
        return;
    }
    if (!current_user_can('manage_woocommerce')) {
        return;
    }
    check_admin_referer('justccell_elite_cross_sell_save', 'justccell_elite_nonce');

    $defaults = justccell_elite_defaults();
    $current  = justccell_elite_settings();
    $posted   = isset($_POST['justccell_elite']) && is_array($_POST['justccell_elite'])
        ? wp_unslash($_POST['justccell_elite'])
        : [];

    $clean = [];
    $clean['enabled'] = isset($posted['enabled']) && $posted['enabled'] === 'yes' ? 'yes' : 'no';
    $clean['api_url'] = isset($posted['api_url'])
        ? untrailingslashit(esc_url_raw((string) $posted['api_url']))
        : $defaults['api_url'];
    $clean['store_url'] = isset($posted['store_url'])
        ? untrailingslashit(esc_url_raw((string) $posted['store_url']))
        : $clean['api_url'];

    $new_key = isset($posted['consumer_key']) ? sanitize_text_field((string) $posted['consumer_key']) : '';
    $clean['consumer_key'] = $new_key !== '' ? $new_key : $current['consumer_key'];

    $new_secret = isset($posted['consumer_secret']) ? sanitize_text_field((string) $posted['consumer_secret']) : '';
    $clean['consumer_secret'] = $new_secret !== '' ? $new_secret : $current['consumer_secret'];

    foreach (['heading', 'kicker', 'code_label', 'cta_label', 'copy_label'] as $field) {
        $clean[$field] = isset($posted[$field])
            ? sanitize_text_field((string) $posted[$field])
            : $defaults[$field];
    }
    $clean['body'] = isset($posted['body'])
        ? sanitize_textarea_field((string) $posted['body'])
        : $defaults['body'];

    update_option(JUSTCCELL_ELITE_OPTION, $clean, false);

    $redirect = add_query_arg(
        [
            'page'    => 'justccell-elite-cross-sell',
            'updated' => '1',
        ],
        admin_url('admin.php')
    );

    if (isset($_POST['justccell_elite_test'])) {
        $test = justccell_elite_test_connection();
        $redirect = add_query_arg(
            [
                'elite_test' => $test['ok'] ? 'ok' : 'fail',
                'elite_msg'  => rawurlencode($test['message']),
            ],
            $redirect
        );
    }

    wp_safe_redirect($redirect);
    exit;
}

/**
 * @return array{ok:bool, message:string}
 */
function justccell_elite_test_connection(): array
{
    if (!justccell_elite_is_enabled()) {
        return [
            'ok'      => false,
            'message' => __('Add the remote API URL, Consumer Key, and Consumer Secret first.', 'justccell'),
        ];
    }

    $response = justccell_elite_remote_request('GET', '/wp-json/wc/v3/coupons', ['per_page' => 1]);
    if (is_wp_error($response)) {
        return [
            'ok'      => false,
            'message' => $response->get_error_message(),
        ];
    }

    $code = (int) wp_remote_retrieve_response_code($response);
    if ($code >= 200 && $code < 300) {
        return [
            'ok'      => true,
            'message' => __('Connected. Elite Terpenes accepted the WooCommerce REST credentials.', 'justccell'),
        ];
    }

    return [
        'ok'      => false,
        'message' => sprintf(
            /* translators: %d: HTTP status */
            __('Remote API returned HTTP %d. Check the keys and that WooCommerce REST is reachable.', 'justccell'),
            $code
        ),
    ];
}

function justccell_elite_render_settings_page(): void
{
    if (!current_user_can('manage_woocommerce')) {
        return;
    }

    $settings = justccell_elite_settings();
    $updated  = isset($_GET['updated']);
    $test     = isset($_GET['elite_test']) ? sanitize_key((string) wp_unslash($_GET['elite_test'])) : '';
    $test_msg = isset($_GET['elite_msg']) ? sanitize_text_field(wp_unslash((string) $_GET['elite_msg'])) : '';
    $key_set  = $settings['consumer_key'] !== '';
    $secret_set = $settings['consumer_secret'] !== '';
    $const_key = defined('JUSTCCELL_ELITE_CONSUMER_KEY') && JUSTCCELL_ELITE_CONSUMER_KEY !== '';
    $const_secret = defined('JUSTCCELL_ELITE_CONSUMER_SECRET') && JUSTCCELL_ELITE_CONSUMER_SECRET !== '';
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Elite Terpenes cross-sell', 'justccell'); ?></h1>
        <p><?php esc_html_e('When a Justccell order is paid (processing or completed), the theme creates a one-use free-shipping coupon on Elite Terpenes and shows it on the thank-you page and customer email. The remote call times out after 4 seconds and never fails checkout.', 'justccell'); ?></p>

        <?php if ($updated) : ?>
            <div class="notice notice-success is-dismissible"><p><?php esc_html_e('Settings saved.', 'justccell'); ?></p></div>
        <?php endif; ?>
        <?php if ($test === 'ok') : ?>
            <div class="notice notice-success is-dismissible"><p><?php echo esc_html($test_msg); ?></p></div>
        <?php elseif ($test === 'fail') : ?>
            <div class="notice notice-error is-dismissible"><p><?php echo esc_html($test_msg); ?></p></div>
        <?php endif; ?>

        <form method="post" action="">
            <?php wp_nonce_field('justccell_elite_cross_sell_save', 'justccell_elite_nonce'); ?>
            <input type="hidden" name="justccell_elite_save" value="1">

            <h2><?php esc_html_e('Remote API', 'justccell'); ?></h2>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><?php esc_html_e('Enable', 'justccell'); ?></th>
                    <td>
                        <label>
                            <input type="checkbox" name="justccell_elite[enabled]" value="yes" <?php checked($settings['enabled'], 'yes'); ?>>
                            <?php esc_html_e('Create Elite Terpenes coupons after Justccell orders', 'justccell'); ?>
                        </label>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-api-url"><?php esc_html_e('API site URL', 'justccell'); ?></label></th>
                    <td>
                        <input name="justccell_elite[api_url]" id="jc-elite-api-url" type="url" class="regular-text" value="<?php echo esc_attr($settings['api_url']); ?>" placeholder="https://eliteterpenez.com">
                        <p class="description"><?php esc_html_e('Posted to /wp-json/wc/v3/coupons on this host.', 'justccell'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-store-url"><?php esc_html_e('Customer storefront URL', 'justccell'); ?></label></th>
                    <td>
                        <input name="justccell_elite[store_url]" id="jc-elite-store-url" type="url" class="regular-text" value="<?php echo esc_attr($settings['store_url']); ?>" placeholder="https://eliteterpenez.com">
                        <p class="description"><?php esc_html_e('Used for the magic link: storefront/?apply_coupon=JC-123', 'justccell'); ?></p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-ck"><?php esc_html_e('Consumer Key', 'justccell'); ?></label></th>
                    <td>
                        <input name="justccell_elite[consumer_key]" id="jc-elite-ck" type="text" class="regular-text" value="" autocomplete="off" placeholder="<?php echo $key_set ? esc_attr__('Stored — paste a new key to replace', 'justccell') : ''; ?>">
                        <p class="description">
                            <?php
                            echo $key_set
                                ? esc_html__('A key is stored. Leave blank to keep it.', 'justccell')
                                : esc_html__('WooCommerce → Settings → Advanced → REST API on Elite Terpenes (Read/Write).', 'justccell');
                            if ($const_key) {
                                echo ' ' . esc_html__('Overridden by JUSTCCELL_ELITE_CONSUMER_KEY in wp-config.php.', 'justccell');
                            }
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-cs"><?php esc_html_e('Consumer Secret', 'justccell'); ?></label></th>
                    <td>
                        <input name="justccell_elite[consumer_secret]" id="jc-elite-cs" type="password" class="regular-text" value="" autocomplete="new-password" placeholder="<?php echo $secret_set ? esc_attr__('Stored — paste a new secret to replace', 'justccell') : ''; ?>">
                        <p class="description">
                            <?php
                            echo $secret_set
                                ? esc_html__('A secret is stored. Leave blank to keep it.', 'justccell')
                                : esc_html__('Never commit this value. Optional override: JUSTCCELL_ELITE_CONSUMER_SECRET in wp-config.php.', 'justccell');
                            if ($const_secret) {
                                echo ' ' . esc_html__('Overridden by wp-config.php.', 'justccell');
                            }
                            ?>
                        </p>
                    </td>
                </tr>
            </table>

            <h2><?php esc_html_e('Thank-you card and email copy', 'justccell'); ?></h2>
            <p><?php esc_html_e('All visitor-facing words on the promotional card are edited here. Empty fields fall back to the defaults.', 'justccell'); ?></p>
            <table class="form-table" role="presentation">
                <tr>
                    <th scope="row"><label for="jc-elite-kicker"><?php esc_html_e('Kicker', 'justccell'); ?></label></th>
                    <td><input name="justccell_elite[kicker]" id="jc-elite-kicker" type="text" class="regular-text" value="<?php echo esc_attr($settings['kicker']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-heading"><?php esc_html_e('Heading', 'justccell'); ?></label></th>
                    <td><input name="justccell_elite[heading]" id="jc-elite-heading" type="text" class="large-text" value="<?php echo esc_attr($settings['heading']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-body"><?php esc_html_e('Body', 'justccell'); ?></label></th>
                    <td><textarea name="justccell_elite[body]" id="jc-elite-body" class="large-text" rows="3"><?php echo esc_textarea($settings['body']); ?></textarea></td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-code-label"><?php esc_html_e('Code label', 'justccell'); ?></label></th>
                    <td><input name="justccell_elite[code_label]" id="jc-elite-code-label" type="text" class="regular-text" value="<?php echo esc_attr($settings['code_label']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-cta"><?php esc_html_e('Button text', 'justccell'); ?></label></th>
                    <td><input name="justccell_elite[cta_label]" id="jc-elite-cta" type="text" class="regular-text" value="<?php echo esc_attr($settings['cta_label']); ?>"></td>
                </tr>
                <tr>
                    <th scope="row"><label for="jc-elite-copy"><?php esc_html_e('Copy-button text', 'justccell'); ?></label></th>
                    <td><input name="justccell_elite[copy_label]" id="jc-elite-copy" type="text" class="regular-text" value="<?php echo esc_attr($settings['copy_label']); ?>"></td>
                </tr>
            </table>

            <?php submit_button(__('Save settings', 'justccell')); ?>
            <?php submit_button(__('Save and test connection', 'justccell'), 'secondary', 'justccell_elite_test', false); ?>
        </form>
    </div>
    <?php
}

add_action('woocommerce_order_status_processing', 'justccell_elite_queue_for_order', 20, 2);
add_action('woocommerce_order_status_completed', 'justccell_elite_queue_for_order', 20, 2);
add_action('woocommerce_payment_complete', 'justccell_elite_queue_for_order_id', 20);
add_action(JUSTCCELL_ELITE_ACTION, 'justccell_elite_run_create_coupon', 10, 1);

function justccell_elite_queue_for_order_id($order_id): void
{
    justccell_elite_queue_for_order($order_id, null);
}

/**
 * @param mixed          $order_id
 * @param WC_Order|null  $order
 */
function justccell_elite_queue_for_order($order_id, $order = null): void
{
    $id = absint($order_id);
    if ($id < 1) {
        return;
    }

    if (!$order instanceof WC_Order) {
        $order = wc_get_order($id);
    }
    if (!$order instanceof WC_Order) {
        return;
    }
    if ($order->has_status(['failed', 'cancelled', 'refunded', 'checkout-draft'])) {
        return;
    }
    if ((string) $order->get_meta(JUSTCCELL_ELITE_META_COUPON, true) !== '') {
        return;
    }
    if (!justccell_elite_is_enabled()) {
        return;
    }

    if (function_exists('as_enqueue_async_action')) {
        $args = ['order_id' => $id];
        $busy = function_exists('as_has_scheduled_action')
            && as_has_scheduled_action(JUSTCCELL_ELITE_ACTION, $args, 'justccell-elite');
        if (!$busy) {
            as_enqueue_async_action(JUSTCCELL_ELITE_ACTION, $args, 'justccell-elite');
        }
        return;
    }

    justccell_elite_run_create_coupon($id);
}

/**
 * Action Scheduler worker. Never throws out of the request.
 *
 * @param mixed $order_id
 */
function justccell_elite_run_create_coupon($order_id): void
{
    try {
        justccell_elite_create_coupon_for_order(absint($order_id));
    } catch (Throwable $e) {
        $order = wc_get_order(absint($order_id));
        if ($order instanceof WC_Order) {
            $order->update_meta_data(JUSTCCELL_ELITE_META_ERROR, 'exception');
            $order->save();
        }
    }
}

function justccell_elite_create_coupon_for_order(int $order_id): string
{
    $order = wc_get_order($order_id);
    if (!$order instanceof WC_Order) {
        return '';
    }

    $existing = (string) $order->get_meta(JUSTCCELL_ELITE_META_COUPON, true);
    if ($existing !== '') {
        return $existing;
    }

    if (!justccell_elite_is_enabled()) {
        return '';
    }

    $lock = (int) $order->get_meta(JUSTCCELL_ELITE_META_LOCK, true);
    if ($lock > 0 && (time() - $lock) < 30) {
        return '';
    }

    $email = sanitize_email($order->get_billing_email());
    if (!is_email($email)) {
        $order->update_meta_data(JUSTCCELL_ELITE_META_ERROR, 'missing_billing_email');
        $order->save();
        return '';
    }

    $order->update_meta_data(JUSTCCELL_ELITE_META_LOCK, (string) time());
    $order->save();

    $code    = justccell_elite_coupon_code($order_id);
    $expires = time() + (JUSTCCELL_ELITE_TTL_HOURS * HOUR_IN_SECONDS);
    $payload = [
        'code'               => $code,
        'discount_type'      => 'percent',
        'amount'             => '0',
        'individual_use'     => true,
        'exclude_sale_items' => false,
        'free_shipping'      => true,
        'date_expires'       => gmdate('Y-m-d\TH:i:s', $expires),
        'usage_limit'        => 1,
        'email_restrictions' => [$email],
        'description'        => sprintf('Justccell order %d', $order_id),
    ];

    $response = justccell_elite_remote_request('POST', '/wp-json/wc/v3/coupons', $payload);
    $code_out = justccell_elite_parse_coupon_response($response, $code);

    if ($code_out === '') {
        $code_out = justccell_elite_fetch_existing_coupon($code);
    }

    $order = wc_get_order($order_id);
    if (!$order instanceof WC_Order) {
        return $code_out;
    }

    $order->delete_meta_data(JUSTCCELL_ELITE_META_LOCK);

    if ($code_out !== '') {
        $order->update_meta_data(JUSTCCELL_ELITE_META_COUPON, $code_out);
        $order->update_meta_data(JUSTCCELL_ELITE_META_EXPIRES, (string) $expires);
        $order->delete_meta_data(JUSTCCELL_ELITE_META_ERROR);
        $order->add_order_note(
            sprintf(
                /* translators: %s: coupon code */
                __('Elite Terpenes free-delivery coupon created: %s', 'justccell'),
                $code_out
            )
        );
    } else {
        $order->update_meta_data(JUSTCCELL_ELITE_META_ERROR, 'remote_failed');
    }

    $order->save();

    return $code_out;
}

/**
 * @param mixed $response
 */
function justccell_elite_parse_coupon_response($response, string $fallback_code): string
{
    if (is_wp_error($response)) {
        return '';
    }

    $status = (int) wp_remote_retrieve_response_code($response);
    $body   = json_decode((string) wp_remote_retrieve_body($response), true);
    if (!is_array($body)) {
        $body = [];
    }

    if ($status >= 200 && $status < 300) {
        $remote_code = isset($body['code']) ? sanitize_text_field((string) $body['code']) : '';
        return $remote_code !== '' ? strtoupper($remote_code) : $fallback_code;
    }

    $woo_code = isset($body['code']) ? (string) $body['code'] : '';
    $message  = isset($body['message']) ? (string) $body['message'] : '';
    if ($status === 400 && ($woo_code === 'woocommerce_rest_coupon_code_already_exists' || stripos($message, 'already exists') !== false)) {
        return $fallback_code;
    }

    return '';
}

function justccell_elite_fetch_existing_coupon(string $code): string
{
    $response = justccell_elite_remote_request('GET', '/wp-json/wc/v3/coupons', ['code' => $code]);
    if (is_wp_error($response)) {
        return '';
    }
    $status = (int) wp_remote_retrieve_response_code($response);
    if ($status < 200 || $status >= 300) {
        return '';
    }
    $body = json_decode((string) wp_remote_retrieve_body($response), true);
    if (!is_array($body) || $body === []) {
        return '';
    }
    $first = $body[0] ?? $body;
    if (!is_array($first) || empty($first['code'])) {
        return '';
    }
    return strtoupper(sanitize_text_field((string) $first['code']));
}

/**
 * @param array<string, mixed> $body
 * @return array<string, mixed>|WP_Error
 */
function justccell_elite_remote_request(string $method, string $path, array $body = [])
{
    $settings = justccell_elite_settings();
    $base     = $settings['api_url'];
    $url      = $base . $path;

    $args = [
        'method'    => $method,
        'timeout'   => JUSTCCELL_ELITE_TIMEOUT,
        'sslverify' => true,
        'headers'   => [
            'Authorization' => 'Basic ' . base64_encode($settings['consumer_key'] . ':' . $settings['consumer_secret']),
            'Accept'        => 'application/json',
        ],
        'user-agent' => 'Justccell-Elite-Bridge/' . (defined('JUSTCCELL_VERSION') ? JUSTCCELL_VERSION : '1.0'),
    ];

    if ($method === 'GET') {
        $url = add_query_arg($body, $url);
    } else {
        $args['headers']['Content-Type'] = 'application/json';
        $args['body'] = wp_json_encode($body);
    }

    $response = wp_remote_request($url, $args);
    if (!is_wp_error($response)) {
        return $response;
    }

    // Some hosts strip Authorization; WooCommerce also accepts query credentials over HTTPS.
    $fallback = add_query_arg(
        [
            'consumer_key'    => $settings['consumer_key'],
            'consumer_secret' => $settings['consumer_secret'],
        ],
        $url
    );
    unset($args['headers']['Authorization']);

    return wp_remote_request($fallback, $args);
}

function justccell_elite_order_coupon(WC_Order $order, bool $try_now = false): string
{
    $code = (string) $order->get_meta(JUSTCCELL_ELITE_META_COUPON, true);
    if ($code !== '') {
        return $code;
    }
    if (!$try_now) {
        return '';
    }
    try {
        return justccell_elite_create_coupon_for_order($order->get_id());
    } catch (Throwable $e) {
        return '';
    }
}

add_action('woocommerce_thankyou', 'justccell_elite_thankyou_card', 4);

function justccell_elite_thankyou_card($order_id): void
{
    $order = $order_id instanceof WC_Order ? $order_id : wc_get_order(absint($order_id));
    if (!$order instanceof WC_Order) {
        return;
    }
    $code = justccell_elite_order_coupon($order, true);
    if ($code === '') {
        return;
    }
    justccell_elite_render_promo_card($order, 'thankyou');
}

add_action('woocommerce_email_before_order_table', static function ($order, $sent_to_admin, $plain_text, $email = null): void {
    if ($sent_to_admin || !$order instanceof WC_Order) {
        return;
    }
    if ($plain_text) {
        $code = justccell_elite_order_coupon($order, true);
        if ($code === '') {
            return;
        }
        justccell_elite_render_promo_plain($order);
        return;
    }

    $id = '';
    if (is_object($email) && isset($email->id)) {
        $id = (string) $email->id;
    }
    $allowed = [
        'customer_processing_order',
        'customer_completed_order',
        'customer_on_hold_order',
        'customer_invoice',
        'new_order',
    ];
    if ($id !== '' && !in_array($id, $allowed, true) && strpos($id, 'customer_') !== 0) {
        return;
    }

    $code = justccell_elite_order_coupon($order, true);
    if ($code === '') {
        return;
    }
    justccell_elite_render_promo_card($order, 'email');
}, 10, 4);

/**
 * @param 'thankyou'|'email' $context
 */
function justccell_elite_render_promo_card(WC_Order $order, string $context = 'thankyou'): void
{
    static $thankyou_done = false;
    if ($context === 'thankyou') {
        if ($thankyou_done) {
            return;
        }
        $thankyou_done = true;
    }

    $code = (string) $order->get_meta(JUSTCCELL_ELITE_META_COUPON, true);
    if ($code === '') {
        return;
    }

    $settings = justccell_elite_settings();
    $heading  = $settings['heading'] !== '' ? $settings['heading'] : justccell_elite_defaults()['heading'];
    $body     = $settings['body'] !== '' ? $settings['body'] : justccell_elite_defaults()['body'];
    $kicker   = $settings['kicker'] !== '' ? $settings['kicker'] : justccell_elite_defaults()['kicker'];
    $code_lbl = $settings['code_label'] !== '' ? $settings['code_label'] : justccell_elite_defaults()['code_label'];
    $cta      = $settings['cta_label'] !== '' ? $settings['cta_label'] : justccell_elite_defaults()['cta_label'];
    $copy     = $settings['copy_label'] !== '' ? $settings['copy_label'] : justccell_elite_defaults()['copy_label'];
    $url      = justccell_elite_magic_url($code);

    if ($context === 'email') {
        justccell_elite_render_promo_email($heading, $body, $kicker, $code_lbl, $cta, $code, $url);
        return;
    }

    $copy_id = 'jc-elite-copy-' . $order->get_id();
    ?>
    <aside class="jc-elite-card" aria-labelledby="jc-elite-card-heading">
        <?php if ($kicker !== '') : ?>
            <p class="jc-elite-card__kicker"><?php echo esc_html($kicker); ?></p>
        <?php endif; ?>
        <h2 id="jc-elite-card-heading" class="jc-elite-card__heading"><?php echo esc_html($heading); ?></h2>
        <?php if ($body !== '') : ?>
            <p class="jc-elite-card__body"><?php echo esc_html($body); ?></p>
        <?php endif; ?>
        <p class="jc-elite-card__code-label"><?php echo esc_html($code_lbl); ?></p>
        <div class="jc-elite-card__code-row">
            <code class="jc-elite-card__code" id="<?php echo esc_attr($copy_id); ?>"><?php echo esc_html($code); ?></code>
            <button type="button" class="jc-elite-card__copy" data-copy-target="<?php echo esc_attr($copy_id); ?>">
                <?php echo esc_html($copy); ?>
            </button>
        </div>
        <a class="btn btn--primary jc-elite-card__cta" href="<?php echo esc_url($url); ?>">
            <?php echo esc_html($cta); ?>
        </a>
    </aside>
    <?php
    justccell_elite_print_copy_script();
}

function justccell_elite_print_copy_script(): void
{
    static $printed = false;
    if ($printed) {
        return;
    }
    $printed = true;
    $copied  = __('Copied', 'justccell');
    ?>
    <script>
    (function () {
      document.addEventListener('click', function (event) {
        var btn = event.target.closest('.jc-elite-card__copy');
        if (!btn) return;
        var id = btn.getAttribute('data-copy-target');
        var el = id ? document.getElementById(id) : null;
        if (!el) return;
        var text = (el.textContent || '').trim();
        if (!text) return;
        var done = function () {
          btn.textContent = <?php echo wp_json_encode($copied); ?>;
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(text).then(done).catch(function () {
            window.getSelection().selectAllChildren(el);
          });
          return;
        }
        window.getSelection().selectAllChildren(el);
      });
    })();
    </script>
    <?php
}

function justccell_elite_render_promo_plain(WC_Order $order): void
{
    $code     = (string) $order->get_meta(JUSTCCELL_ELITE_META_COUPON, true);
    $settings = justccell_elite_settings();
    $heading  = $settings['heading'] !== '' ? $settings['heading'] : justccell_elite_defaults()['heading'];
    echo "\n" . $heading . "\n";
    echo $settings['code_label'] . ': ' . $code . "\n";
    echo justccell_elite_magic_url($code) . "\n\n";
}

function justccell_elite_render_promo_email(
    string $heading,
    string $body,
    string $kicker,
    string $code_label,
    string $cta,
    string $code,
    string $url
): void {
    ?>
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" width="100%" style="margin:24px 0;border:1px solid #e5e7eb;border-radius:16px;background:#ffffff;">
        <tr>
            <td style="padding:28px 24px;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Helvetica,Arial,sans-serif;color:#111827;">
                <?php if ($kicker !== '') : ?>
                    <p style="margin:0 0 8px;font-size:12px;letter-spacing:0.08em;text-transform:uppercase;color:#6b7280;"><?php echo esc_html($kicker); ?></p>
                <?php endif; ?>
                <p style="margin:0 0 12px;font-size:20px;line-height:1.3;font-weight:600;color:#111827;"><?php echo esc_html($heading); ?></p>
                <?php if ($body !== '') : ?>
                    <p style="margin:0 0 20px;font-size:15px;line-height:1.5;color:#4b5563;"><?php echo esc_html($body); ?></p>
                <?php endif; ?>
                <p style="margin:0 0 6px;font-size:12px;color:#6b7280;"><?php echo esc_html($code_label); ?></p>
                <p style="margin:0 0 20px;font-size:22px;letter-spacing:0.08em;font-weight:700;color:#111827;"><?php echo esc_html($code); ?></p>
                <a href="<?php echo esc_url($url); ?>" style="display:inline-block;background:#0504aa;color:#ffffff;text-decoration:none;font-size:14px;font-weight:600;padding:12px 22px;border-radius:999px;">
                    <?php echo esc_html($cta); ?>
                </a>
            </td>
        </tr>
    </table>
    <?php
}

add_action('woocommerce_admin_order_data_after_billing_address', static function ($order): void {
    if (!$order instanceof WC_Order) {
        return;
    }
    $code = (string) $order->get_meta(JUSTCCELL_ELITE_META_COUPON, true);
    $err  = (string) $order->get_meta(JUSTCCELL_ELITE_META_ERROR, true);
    if ($code === '' && $err === '') {
        return;
    }
    echo '<div class="jc-elite-admin-meta" style="margin-top:12px;">';
    echo '<h3>' . esc_html__('Elite Terpenes coupon', 'justccell') . '</h3>';
    if ($code !== '') {
        echo '<p><strong>' . esc_html($code) . '</strong></p>';
        echo '<p><a href="' . esc_url(justccell_elite_magic_url($code)) . '" target="_blank" rel="noopener noreferrer">' . esc_html__('Magic link', 'justccell') . '</a></p>';
    } elseif ($err !== '') {
        echo '<p>' . esc_html__('Coupon not created (remote timeout or credentials). Checkout was not affected.', 'justccell') . '</p>';
    }
    echo '</div>';
});
