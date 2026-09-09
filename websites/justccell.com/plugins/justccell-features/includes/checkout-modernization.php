<?php
/**
 * Checkout Phase B — Shopify/Apple split, shipping cards, trust strip.
 *
 * Behavioral WooCommerce overrides live in this plugin module only (filters, actions,
 * fragment guards, cart shipping policy). Never edit WooCommerce core or third-party
 * plugin files — those are replaced on every plugin update.
 *
 * Theme owns: `woocommerce/checkout/*.php` markup shells + visual CSS in `woocommerce.css`.
 *
 * @package Justccell_Features
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_is_active_checkout_form(): bool
{
    if (!function_exists('is_checkout') || !is_checkout()) {
        return false;
    }

    return !(function_exists('justccell_is_order_received_page') && justccell_is_order_received_page());
}

add_action('after_setup_theme', static function (): void {
    if (!class_exists('WooCommerce')) {
        return;
    }

    remove_action('woocommerce_checkout_order_review', 'woocommerce_checkout_payment', 20);
    remove_action('woocommerce_checkout_before_order_review_heading', 'justccell_checkout_summary_open', 1);
    remove_action('woocommerce_checkout_after_order_review', 'justccell_checkout_summary_close', 99);
}, 25);

/**
 * @return array{title: string, price_html: string, eta: string}
 */
function justccell_parse_shipping_rate(WC_Shipping_Rate $rate): array
{
    $raw_label = html_entity_decode(wp_strip_all_tags((string) $rate->get_label()), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $title     = trim(preg_replace('/\s*[:\-–—]\s*£.*$/u', '', $raw_label) ?? $raw_label);
    $title     = trim(preg_replace('/\s*\(\s*£.*\)\s*$/u', '', $title) ?? $title);
    if ($title === '') {
        $title = $raw_label !== '' ? $raw_label : __('Shipping', 'justccell');
    }

    $cost = (float) $rate->get_cost();
    if ($cost <= 0) {
        $price_html = '<span class="jc-shipping-card__price jc-shipping-card__price--free">'
            . esc_html__('Free', 'justccell') . '</span>';
    } else {
        $price_html = '<span class="jc-shipping-card__price">'
            . wp_kses_post(wc_price($cost + (float) $rate->get_shipping_tax()))
            . '</span>';
    }

    return [
        'title'      => $title,
        'price_html' => $price_html,
        'eta'        => justccell_shipping_eta_label($rate),
    ];
}

function justccell_shipping_eta_label(WC_Shipping_Rate $rate): string
{
    $id    = strtolower((string) $rate->get_method_id());
    $label = strtolower(wp_strip_all_tags((string) $rate->get_label()));

    if (
        str_contains($id, 'local_pickup')
        || str_contains($label, 'pickup')
        || str_contains($label, 'collect')
        || str_contains($label, 'bolton')
    ) {
        return __('Same-day when ready', 'justccell');
    }

    if (str_contains($label, 'same day') || str_contains($label, 'same-day')) {
        return __('Same-day UK dispatch', 'justccell');
    }

    if (str_contains($label, 'express') || str_contains($label, 'next day')) {
        return __('Next business day', 'justccell');
    }

    if (str_contains($label, 'fedex') || str_contains($label, 'international')) {
        return __('2–5 business days', 'justccell');
    }

    if ((float) $rate->get_cost() <= 0 || str_contains($id, 'free_shipping')) {
        return __('Standard delivery', 'justccell');
    }

    return __('Estimated 3–7 days', 'justccell');
}

add_filter('woocommerce_cart_shipping_method_full_label', static function ($label, $method): string {
    if (!$method instanceof WC_Shipping_Rate) {
        return (string) $label;
    }

    $parsed = justccell_parse_shipping_rate($method);

    return sprintf(
        '<span class="jc-shipping-card__inner"><span class="jc-shipping-card__main"><span class="jc-shipping-card__title">%s</span><span class="jc-shipping-card__eta">%s</span></span>%s</span>',
        esc_html($parsed['title']),
        esc_html($parsed['eta']),
        $parsed['price_html']
    );
}, 20, 2);

/**
 * @return array<string, WC_Payment_Gateway>
 */
function justccell_checkout_get_available_gateways(): array
{
    if (!function_exists('WC') || !WC()->payment_gateways()) {
        return [];
    }

    $gateways = WC()->payment_gateways()->get_available_payment_gateways();

    return is_array($gateways) ? $gateways : [];
}

function justccell_checkout_render_payment_stack(): void
{
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }
    ?>
    <section id="jc-checkout-payment-stack" class="jc-checkout-payment-stack jc-checkout-section" aria-label="<?php esc_attr_e('Payment and place order', 'justccell'); ?>">
        <h3 class="jc-checkout-section__title"><?php esc_html_e('Payment method', 'justccell'); ?></h3>
        <?php woocommerce_checkout_payment(); ?>
    </section>
    <?php
}

/**
 * @deprecated Split render kept for backwards compatibility — use justccell_checkout_render_payment_stack().
 */
function justccell_checkout_render_payment_methods_section(): void
{
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }

    if (!wp_doing_ajax()) {
        do_action('woocommerce_review_order_before_payment');
    }
    ?>
    <section id="jc-checkout-payment-methods" class="jc-checkout-payment-methods jc-checkout-section" aria-labelledby="jc-checkout-payment-heading">
        <h3 id="jc-checkout-payment-heading" class="jc-checkout-section__title">
            <?php esc_html_e('Payment method', 'justccell'); ?>
        </h3>
        <div id="payment" class="jc-checkout-payment-panel">
            <?php if (WC()->cart->needs_payment()) : ?>
                <ul class="wc_payment_methods payment_methods methods">
                    <?php
                    $available_gateways = justccell_checkout_get_available_gateways();
                    if ($available_gateways !== []) {
                        foreach ($available_gateways as $gateway) {
                            wc_get_template('checkout/payment-method.php', ['gateway' => $gateway]);
                        }
                    } else {
                        $message = WC()->customer->get_billing_country()
                            ? esc_html__('Sorry, it seems that there are no available payment methods. Please contact us if you require assistance or wish to make alternate arrangements.', 'woocommerce')
                            : esc_html__('Please fill in your details above to see available payment methods.', 'woocommerce');
                        echo '<li>';
                        wc_print_notice(apply_filters('woocommerce_no_available_payment_methods_message', $message), 'notice');
                        echo '</li>';
                    }
                    ?>
                </ul>
            <?php endif; ?>
        </div>
    </section>
    <?php
}

function justccell_checkout_render_place_order_section(): void
{
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }

    $order_button_text = apply_filters('woocommerce_order_button_text', __('Place order', 'woocommerce'));
    $button_class        = 'button alt';
    if (function_exists('wc_wp_theme_get_element_class_name')) {
        $theme_button = wc_wp_theme_get_element_class_name('button');
        if ($theme_button) {
            $button_class .= ' ' . $theme_button;
        }
    }
    ?>
    <div id="jc-checkout-place-order" class="jc-checkout-place-order">
        <div class="form-row place-order">
            <?php wc_get_template('checkout/terms.php'); ?>
            <?php do_action('woocommerce_review_order_before_submit'); ?>
            <?php
            echo apply_filters(
                'woocommerce_order_button_html',
                '<button type="submit" class="' . esc_attr($button_class) . '" name="woocommerce_checkout_place_order" id="place_order" value="' . esc_attr($order_button_text) . '" data-value="' . esc_attr($order_button_text) . '">' . esc_html($order_button_text) . '</button>'
            );
            ?>
            <?php do_action('woocommerce_review_order_after_submit'); ?>
            <?php wp_nonce_field('woocommerce-process_checkout', 'woocommerce-process-checkout-nonce'); ?>
        </div>
    </div>
    <?php

    if (!wp_doing_ajax()) {
        do_action('woocommerce_review_order_after_payment');
    }
}

function justccell_checkout_render_shipping_section(): void
{
    if (!function_exists('WC') || !WC()->cart) {
        return;
    }

    if (!WC()->cart->needs_shipping() || !WC()->cart->show_shipping()) {
        return;
    }

    $packages = WC()->shipping()->get_packages();
    if ($packages === []) {
        return;
    }

    $chosen_methods = WC()->session
        ? (array) WC()->session->get('chosen_shipping_methods', [])
        : [];
    ?>
    <section id="jc-checkout-shipping" class="jc-checkout-shipping" aria-labelledby="jc-checkout-shipping-heading">
        <h3 id="jc-checkout-shipping-heading" class="jc-checkout-section__title">
            <?php esc_html_e('Shipping method', 'justccell'); ?>
        </h3>
        <div class="jc-checkout-shipping__methods">
            <?php
            foreach ($packages as $i => $package) {
                $index         = (int) $i;
                $chosen_method = isset($chosen_methods[$index]) ? (string) $chosen_methods[$index] : '';

                if (count($packages) > 1) {
                    $package_name = apply_filters(
                        'woocommerce_shipping_package_name',
                        sprintf(
                            /* translators: %d: shipping package number */
                            _nx('Shipping', 'Shipping %d', ($index + 1), 'shipping packages', 'woocommerce'),
                            ($index + 1)
                        ),
                        $index,
                        $package
                    );
                    echo '<p class="jc-checkout-shipping__package-name">' . esc_html((string) $package_name) . '</p>';
                }

                wc_get_template(
                    'cart/cart-shipping.php',
                    [
                        'package'                 => $package,
                        'available_methods'       => $package['rates'],
                        'show_package_details'    => false,
                        'package_details'         => '',
                        'index'                   => $index,
                        'chosen_method'           => $chosen_method,
                        'formatted_destination'   => WC()->countries->get_formatted_address($package['destination'], ', '),
                        'has_calculated_shipping' => WC()->customer->has_calculated_shipping(),
                    ]
                );
            }
            ?>
        </div>
        <div class="jc-checkout-shipping-skeleton" hidden aria-hidden="true">
            <div class="jc-shipping-skeleton-card"></div>
            <div class="jc-shipping-skeleton-card"></div>
        </div>
    </section>
    <?php
}

function justccell_checkout_selected_shipping_total_html(): string
{
    if (!function_exists('WC') || !WC()->cart) {
        return '&mdash;';
    }

    if (!WC()->cart->needs_shipping() || !WC()->cart->show_shipping()) {
        return '&mdash;';
    }

    if (!WC()->customer->has_calculated_shipping()) {
        return esc_html__('Calculated after address', 'justccell');
    }

    $total = WC()->cart->get_cart_shipping_total();
    if ($total === '') {
        return esc_html__('Free', 'justccell');
    }

    return wp_kses_post($total);
}

/**
 * Classic cart page and cart fragment AJAX — never checkout.
 */
function justccell_is_cart_not_checkout(): bool
{
    if (function_exists('is_checkout') && is_checkout()) {
        return false;
    }

    if (function_exists('is_cart') && is_cart()) {
        return true;
    }

    if (function_exists('wp_doing_ajax') && wp_doing_ajax()) {
        $wc_ajax = isset($_REQUEST['wc-ajax'])
            ? sanitize_key(wp_unslash((string) $_REQUEST['wc-ajax']))
            : '';

        return $wc_ajax === 'get_refreshed_fragments';
    }

    return false;
}

add_filter('woocommerce_cart_ready_to_calc_shipping', static function ($ready): bool {
    if (justccell_is_cart_not_checkout()) {
        return false;
    }

    return (bool) $ready;
}, 99);

add_filter('woocommerce_cart_needs_shipping', static function ($needs) {
    if (justccell_is_cart_not_checkout()) {
        return false;
    }

    return $needs;
}, 99);

add_filter('woocommerce_shipping_calculator_enable_on_cart', static function (): bool {
    return false;
});

add_filter('woocommerce_update_order_review_fragments', static function (array $fragments): array {
    if (!justccell_is_active_checkout_form()) {
        return $fragments;
    }

    ob_start();
    justccell_checkout_render_shipping_section();
    $html = ob_get_clean();
    if ($html !== false && $html !== '') {
        $fragments['#jc-checkout-shipping'] = $html;
    }

    return $fragments;
}, 20);

add_filter('woocommerce_cart_item_name', static function (string $name, $cart_item, $cart_item_key): string {
    unset($cart_item_key);

    if (!justccell_is_active_checkout_form() || !is_array($cart_item) || !isset($cart_item['data'])) {
        return $name;
    }

    $product = $cart_item['data'];
    if (!$product instanceof WC_Product) {
        return $name;
    }

    $qty = max(1, (int) ($cart_item['quantity'] ?? 1));

    $thumb = $product->get_image(
        [100, 100],
        [
            'class' => 'jc-checkout-line__thumb',
            'alt'   => esc_attr($product->get_name()),
        ]
    );
    if ($thumb === '' && function_exists('wc_placeholder_img')) {
        $thumb = wc_placeholder_img(
            [100, 100],
            ['class' => 'jc-checkout-line__thumb']
        );
    }

    if (!empty($cart_item['justccell_laser']['enabled']) && is_array($cart_item['justccell_laser'])) {
        $laser_src = (string) ($cart_item['justccell_laser']['preview'] ?? '');
        if ($laser_src !== '' && str_starts_with($laser_src, 'http')) {
            $thumb = sprintf(
                '<img src="%s" alt="" class="jc-checkout-line__thumb jc-checkout-line__thumb--laser" width="50" height="50" loading="lazy">',
                esc_url($laser_src)
            );
        }
    }

    return '<div class="jc-checkout-line">'
        . '<div class="jc-checkout-line__media">'
        . $thumb
        . '<span class="jc-checkout-line__qty-badge" aria-label="'
        . esc_attr(sprintf(
            /* translators: %d: item quantity */
            _n('Quantity %d', 'Quantity %d', $qty, 'justccell'),
            $qty
        ))
        . '">' . esc_html((string) $qty) . '</span>'
        . '</div>'
        . '<div class="jc-checkout-line__body"><span class="jc-checkout-line__title">' . $name . '</span></div>'
        . '</div>';
}, 25, 3);

add_filter('woocommerce_checkout_cart_item_quantity', static function (string $html, $cart_item, $cart_item_key): string {
    unset($cart_item, $cart_item_key);

    if (!justccell_is_active_checkout_form()) {
        return $html;
    }

    return '';
}, 20, 3);

add_filter('woocommerce_available_payment_gateways', static function ($gateways) {
    if (!is_array($gateways) || $gateways === []) {
        return $gateways;
    }

    if (!function_exists('is_checkout') || !is_checkout() || justccell_is_order_received_page()) {
        return $gateways;
    }

    $primary_ids = [
        'paygatedottocryptogateway',
        'depay',
        'depay_wc',
        'cryptocurrency',
        'cryptocurrency_payment',
    ];

    $primary_id = null;
    foreach ($primary_ids as $id) {
        if (isset($gateways[$id])) {
            $primary_id = $id;
            break;
        }
    }

    if ($primary_id === null) {
        foreach ($gateways as $id => $gateway) {
            if (!$gateway instanceof WC_Payment_Gateway) {
                continue;
            }
            $title = strtolower(wp_strip_all_tags($gateway->get_title()));
            if (str_contains($title, 'cryptocurrency') || str_contains($title, 'depay')) {
                $primary_id = (string) $id;
                break;
            }
        }
    }

    if ($primary_id === null) {
        return $gateways;
    }

    foreach ($gateways as $id => $gateway) {
        if ($id === $primary_id || !$gateway instanceof WC_Payment_Gateway) {
            continue;
        }

        $title = strtolower(wp_strip_all_tags($gateway->get_title()));
        if ($id === 'crypto' || $title === 'crypto') {
            unset($gateways[$id]);
        }
    }

    return $gateways;
}, 50);

function justccell_checkout_trust_strip(): void
{
    if (!justccell_is_active_checkout_form()) {
        return;
    }

    $items = [
        __('Discreet B2B Packaging', 'justccell'),
        __('Same-Day UK Dispatch', 'justccell'),
        __('Secure SSL & Encrypted Payment', 'justccell'),
    ];
    ?>
    <div class="jc-checkout-trust" role="note" aria-label="<?php esc_attr_e('Order assurances', 'justccell'); ?>">
        <ul class="jc-checkout-trust__list">
            <?php foreach ($items as $text) : ?>
                <li class="jc-checkout-trust__item"><?php echo esc_html($text); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php
}
add_action('woocommerce_review_order_after_submit', 'justccell_checkout_trust_strip', 15);

add_action('wp_enqueue_scripts', static function (): void {
    if (!class_exists('WooCommerce') || is_admin()) {
        return;
    }

    $on_cart     = function_exists('is_cart') && is_cart();
    $on_checkout = justccell_is_active_checkout_form();

    if (!$on_cart && !$on_checkout) {
        return;
    }

    wp_enqueue_style(
        'justccell-checkout-modernization',
        JUSTCCELL_FEATURES_URL . 'assets/css/checkout-modernization.css',
        [],
        JUSTCCELL_FEATURES_VERSION
    );

    if (!$on_checkout) {
        return;
    }

    wp_enqueue_script(
        'justccell-checkout-phase-a',
        JUSTCCELL_FEATURES_URL . 'assets/js/checkout-phase-a.js',
        ['jquery', 'wc-checkout'],
        JUSTCCELL_FEATURES_VERSION,
        true
    );
}, 35);
