<?php
/**
 * Checkout Phase B — Shopify/Apple split, shipping cards, trust strip.
 *
 * @package Justccell
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

    ob_start();
    woocommerce_checkout_payment();
    $payment = ob_get_clean();
    if ($payment !== false && $payment !== '') {
        $fragments['#jc-checkout-payment'] = $payment;
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
    if (!class_exists('WooCommerce') || is_admin() || !justccell_is_active_checkout_form()) {
        return;
    }

    wp_enqueue_script(
        'justccell-checkout-phase-a',
        JUSTCCELL_URI . '/assets/js/checkout-phase-a.js',
        ['jquery'],
        JUSTCCELL_VERSION,
        true
    );
}, 35);
