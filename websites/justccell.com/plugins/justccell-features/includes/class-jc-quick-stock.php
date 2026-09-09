<?php
/**
 * Variable product quick stock modal — Products list screen.
 *
 * @package Justccell_Features
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

final class JC_Quick_Stock
{
    private const NONCE_ACTION = 'jc_quick_stock';

    public static function init(): void
    {
        if (!function_exists('wc_get_product')) {
            return;
        }

        add_action('admin_footer-edit.php', [self::class, 'render_modal'], 5);
        add_action('admin_enqueue_scripts', [self::class, 'enqueue_assets']);
        add_action('wp_ajax_jc_get_variation_stock', [self::class, 'ajax_get_variation_stock']);
        add_action('wp_ajax_jc_save_variation_stock', [self::class, 'ajax_save_variation_stock']);
    }

    public static function is_products_list_screen(): bool
    {
        if (!is_admin()) {
            return false;
        }

        global $pagenow, $typenow;

        $post_type = is_string($typenow) ? $typenow : '';
        if ($post_type === '' && isset($_GET['post_type'])) {
            $post_type = sanitize_key((string) wp_unslash($_GET['post_type']));
        }

        if ($pagenow === 'edit.php' && $post_type === 'product') {
            return true;
        }

        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();

        return $screen instanceof WP_Screen && $screen->id === 'edit-product';
    }

    private static function user_can_edit_product(int $post_id): bool
    {
        if ($post_id < 1) {
            return false;
        }

        return current_user_can('edit_product', $post_id) || current_user_can('edit_post', $post_id);
    }

    /**
     * Button markup for the Stock column (variable products only).
     */
    public static function render_trigger_button(int $product_id): void
    {
        if ($product_id < 1 || !current_user_can('edit_products')) {
            return;
        }

        printf(
            '<button type="button" class="button button-small jc-quick-stock-btn" data-product-id="%1$d">%2$s</button>',
            $product_id,
            esc_html__('Quick Stock', 'justccell')
        );
    }

    public static function enqueue_assets(string $hook_suffix): void
    {
        if ($hook_suffix !== 'edit.php' || !self::is_products_list_screen()) {
            return;
        }

        if (!current_user_can('edit_products')) {
            return;
        }

        $ver = defined('JUSTCCELL_FEATURES_VERSION') ? JUSTCCELL_FEATURES_VERSION : '1.0.0';

        wp_enqueue_style(
            'justccell-admin-quick-stock',
            JUSTCCELL_FEATURES_URL . 'assets/css/admin-quick-stock.css',
            [],
            $ver
        );

        wp_enqueue_script(
            'justccell-admin-quick-stock',
            JUSTCCELL_FEATURES_URL . 'assets/js/admin-quick-stock.js',
            ['jquery'],
            $ver,
            true
        );

        wp_localize_script('justccell-admin-quick-stock', 'jcQuickStock', [
            'ajaxUrl'  => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce(self::NONCE_ACTION),
            'currency' => function_exists('get_woocommerce_currency_symbol')
                ? html_entity_decode(get_woocommerce_currency_symbol(), ENT_QUOTES | ENT_HTML5, 'UTF-8')
                : '£',
            'i18n'     => [
                'title'        => __('Quick stock & prices', 'justccell'),
                'attribute'    => __('Variation', 'justccell'),
                'regular'      => __('Regular price', 'justccell'),
                'sale'         => __('Sale price', 'justccell'),
                'quantity'     => __('Stock qty', 'justccell'),
                'regularFor'   => __('Regular price for %s', 'justccell'),
                'saleFor'      => __('Sale price for %s', 'justccell'),
                'qtyFor'       => __('Stock quantity for %s', 'justccell'),
                'manageOff'    => __('Stock was not managed — will enable when you save a quantity.', 'justccell'),
                'saleInvalid'  => __('Sale price must be lower than regular price.', 'justccell'),
                'loading'      => __('Loading variations…', 'justccell'),
                'saving'       => __('Saving…', 'justccell'),
                'saved'        => __('Variation stock and prices updated.', 'justccell'),
                'error'        => __('Could not update variations. Try again.', 'justccell'),
                'empty'        => __('No variations found for this product.', 'justccell'),
                'save'         => __('Save changes', 'justccell'),
                'cancel'       => __('Cancel', 'justccell'),
                'close'        => __('Close', 'justccell'),
            ],
        ]);
    }

    public static function render_modal(): void
    {
        if (!self::is_products_list_screen() || !current_user_can('edit_products')) {
            return;
        }
        ?>
        <div id="jc-quick-stock-modal" class="jc-quick-stock-modal" hidden aria-hidden="true">
            <div class="jc-quick-stock-modal__overlay" data-jc-quick-stock-close></div>
            <div class="jc-quick-stock-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="jc-quick-stock-modal-title">
                <button type="button" class="jc-quick-stock-modal__close button-link" data-jc-quick-stock-close aria-label="<?php esc_attr_e('Close', 'justccell'); ?>">
                    <span class="screen-reader-text"><?php esc_html_e('Close', 'justccell'); ?></span>
                </button>
                <h2 id="jc-quick-stock-modal-title" class="jc-quick-stock-modal__title"><?php esc_html_e('Quick stock & prices', 'justccell'); ?></h2>
                <p class="jc-quick-stock-modal__product" id="jc-quick-stock-product-name"></p>
                <div class="jc-quick-stock-modal__body" id="jc-quick-stock-modal-body">
                    <p class="jc-quick-stock-modal__loading"><?php esc_html_e('Loading variations…', 'justccell'); ?></p>
                </div>
                <p class="jc-quick-stock-modal__feedback" id="jc-quick-stock-feedback" role="status" aria-live="polite"></p>
                <div class="jc-quick-stock-modal__footer">
                    <button type="button" class="button" data-jc-quick-stock-close><?php esc_html_e('Cancel', 'justccell'); ?></button>
                    <button type="button" class="button button-primary" id="jc-quick-stock-save" disabled>
                        <?php esc_html_e('Save changes', 'justccell'); ?>
                    </button>
                    <span class="spinner" id="jc-quick-stock-spinner"></span>
                </div>
            </div>
        </div>
        <?php
    }

    /**
     * @return list<array{
     *     variation_id:int,
     *     label:string,
     *     stock_quantity:int|null,
     *     manage_stock:bool,
     *     stock_status:string,
     *     regular_price:string,
     *     sale_price:string
     * }>
     */
    private static function variation_rows(WC_Product_Variable $product): array
    {
        $rows = [];

        foreach ($product->get_children() as $variation_id) {
            $variation = wc_get_product((int) $variation_id);
            if (!$variation instanceof WC_Product_Variation) {
                continue;
            }

            $label = wc_get_formatted_variation($variation, true, false, false);
            if ($label === '') {
                $label = sprintf(
                    /* translators: %d: WooCommerce variation ID */
                    __('Variation %d', 'justccell'),
                    (int) $variation_id
                );
            }

            $rows[] = [
                'variation_id'   => (int) $variation_id,
                'label'          => wp_strip_all_tags(html_entity_decode($label, ENT_QUOTES, 'UTF-8')),
                'stock_quantity' => $variation->managing_stock() ? (int) $variation->get_stock_quantity() : null,
                'manage_stock'   => $variation->managing_stock(),
                'stock_status'   => (string) $variation->get_stock_status(),
                'regular_price'  => (string) $variation->get_regular_price('edit'),
                'sale_price'     => (string) $variation->get_sale_price('edit'),
            ];
        }

        return $rows;
    }

    /**
     * Compact stock line for the Products list cell (no page reload needed).
     */
    private static function list_summary(WC_Product_Variable $product): string
    {
        $rows  = self::variation_rows($product);
        $count = count($rows);
        $qty   = 0;
        foreach ($rows as $row) {
            $qty += (int) ($row['stock_quantity'] ?? 0);
        }

        return sprintf(
            /* translators: 1: variation count, 2: total stock quantity */
            _n('%1$d variation · %2$s in stock', '%1$d variations · %2$s in stock', $count, 'justccell'),
            $count,
            number_format_i18n($qty)
        );
    }

    /**
     * Woo catalog price string, or empty to clear the field.
     */
    private static function parse_price_field(mixed $raw): string
    {
        if (!is_scalar($raw)) {
            return '';
        }

        $raw = trim((string) $raw);
        if ($raw === '') {
            return '';
        }

        if (function_exists('wc_format_decimal')) {
            $formatted = wc_format_decimal($raw, false, true);
            if (!is_string($formatted) && !is_numeric($formatted)) {
                return '';
            }
            $formatted = (string) $formatted;
            if ($formatted === '' || (float) $formatted < 0) {
                return '';
            }

            return $formatted;
        }

        if (!is_numeric($raw) || (float) $raw < 0) {
            return '';
        }

        return number_format((float) $raw, 2, '.', '');
    }

    public static function ajax_get_variation_stock(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        if (!current_user_can('edit_products')) {
            wp_send_json_error(['message' => __('Permission denied.', 'justccell')], 403);
        }

        $product_id = isset($_POST['product_id']) ? (int) wp_unslash($_POST['product_id']) : 0;
        if ($product_id < 1) {
            wp_send_json_error(['message' => __('Invalid product.', 'justccell')], 400);
        }

        if (!self::user_can_edit_product($product_id)) {
            wp_send_json_error(['message' => __('Permission denied.', 'justccell')], 403);
        }

        $product = wc_get_product($product_id);
        if (!$product instanceof WC_Product_Variable) {
            wp_send_json_error(['message' => __('Product is not variable.', 'justccell')], 400);
        }

        wp_send_json_success([
            'product_id'   => $product_id,
            'product_name' => $product->get_name(),
            'variations'   => self::variation_rows($product),
            'summary'      => self::list_summary($product),
        ]);
    }

    public static function ajax_save_variation_stock(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        if (!current_user_can('edit_products')) {
            wp_send_json_error(['message' => __('Permission denied.', 'justccell')], 403);
        }

        $product_id = isset($_POST['product_id']) ? (int) wp_unslash($_POST['product_id']) : 0;
        $raw_rows   = isset($_POST['rows']) ? wp_unslash($_POST['rows']) : [];
        $raw_stock  = isset($_POST['stock']) ? wp_unslash($_POST['stock']) : [];

        if ($product_id < 1) {
            wp_send_json_error(['message' => __('No variation data submitted.', 'justccell')], 400);
        }

        if (!is_array($raw_rows)) {
            $raw_rows = [];
        }
        if (!is_array($raw_stock)) {
            $raw_stock = [];
        }

        if ($raw_rows === [] && $raw_stock !== []) {
            foreach ($raw_stock as $variation_id => $qty_raw) {
                $raw_rows[(int) $variation_id] = [
                    'qty'      => $qty_raw,
                    'regular'  => '',
                    'sale'     => '',
                    'prices'   => false,
                ];
            }
        }

        if ($raw_rows === []) {
            wp_send_json_error(['message' => __('No variation data submitted.', 'justccell')], 400);
        }

        if (!self::user_can_edit_product($product_id)) {
            wp_send_json_error(['message' => __('Permission denied.', 'justccell')], 403);
        }

        $parent = wc_get_product($product_id);
        if (!$parent instanceof WC_Product_Variable) {
            wp_send_json_error(['message' => __('Product is not variable.', 'justccell')], 400);
        }

        $parsed = [];
        $errors = [];

        foreach ($raw_rows as $variation_id => $row) {
            $variation_id = (int) $variation_id;
            if ($variation_id < 1) {
                continue;
            }

            $variation = wc_get_product($variation_id);
            if (!$variation instanceof WC_Product_Variation || (int) $variation->get_parent_id() !== $product_id) {
                continue;
            }

            if (!self::user_can_edit_product($variation_id)) {
                continue;
            }

            $row          = is_array($row) ? $row : [];
            $qty          = max(0, (int) ($row['qty'] ?? 0));
            $write_prices = true;
            if (array_key_exists('prices', $row) && in_array($row['prices'], [false, '0', 0, 'false'], true)) {
                $write_prices = false;
            }
            $regular = $write_prices ? self::parse_price_field($row['regular'] ?? '') : (string) $variation->get_regular_price('edit');
            $sale    = $write_prices ? self::parse_price_field($row['sale'] ?? '') : (string) $variation->get_sale_price('edit');

            if ($sale !== '' && ($regular === '' || (float) $sale >= (float) $regular)) {
                $label    = wc_get_formatted_variation($variation, true, false, false);
                $errors[] = sprintf(
                    /* translators: %s: variation label */
                    __('Sale price must be lower than regular price for %s.', 'justccell'),
                    $label !== '' ? $label : (string) $variation_id
                );
                continue;
            }

            $parsed[] = [
                'variation' => $variation,
                'qty'       => $qty,
                'regular'   => $regular,
                'sale'      => $sale,
                'prices'    => $write_prices,
            ];
        }

        if ($errors !== []) {
            wp_send_json_error(['message' => implode(' ', $errors)], 400);
        }

        if ($parsed === []) {
            wp_send_json_error(['message' => __('No variations were updated.', 'justccell')], 400);
        }

        try {
            $updated = self::persist_variation_rows($parsed);

            if (class_exists('WC_Product_Variable')) {
                WC_Product_Variable::sync($product_id);
            }

            if (function_exists('wc_delete_product_transients')) {
                wc_delete_product_transients($product_id);
            }
        } catch (Throwable $e) {
            if (function_exists('wc_get_logger')) {
                wc_get_logger()->error(
                    'Quick Stock save failed: ' . $e->getMessage(),
                    ['source' => 'justccell-quick-stock']
                );
            }

            $message = __('Could not update variations. Try again.', 'justccell');
            if (current_user_can('manage_woocommerce')) {
                $message .= ' ' . $e->getMessage();
            }

            wp_send_json_error(['message' => $message], 500);
        }

        $fresh = wc_get_product($product_id);
        if (!$fresh instanceof WC_Product_Variable) {
            $fresh = $parent;
        }

        wp_send_json_success([
            'updated'    => $updated,
            'product_id' => $product_id,
            'variations' => self::variation_rows($fresh),
            'summary'    => self::list_summary($fresh),
            'message'    => sprintf(
                /* translators: %d: number of variations updated */
                _n('%d variation updated.', '%d variations updated.', $updated, 'justccell'),
                $updated
            ),
        ]);
    }

    /**
     * Write stock + prices on variation objects in one save each.
     *
     * Do not call wc_update_product_stock_status() here — Woo requires ($product_id, $status).
     * Passing a WC_Product as the only argument fatals on PHP 8 and 500s the AJAX save.
     *
     * @param list<array{variation:WC_Product_Variation,qty:int,regular:string,sale:string,prices:bool}> $parsed
     */
    private static function persist_variation_rows(array $parsed): int
    {
        $updated = 0;

        foreach ($parsed as $item) {
            $variation = $item['variation'];
            $qty       = (int) $item['qty'];

            $variation->set_manage_stock(true);
            $variation->set_stock_quantity($qty);
            $variation->set_stock_status($qty > 0 ? 'instock' : 'outofstock');

            if (!empty($item['prices'])) {
                $regular = (string) $item['regular'];
                $sale    = (string) $item['sale'];
                $variation->set_regular_price($regular);
                $variation->set_sale_price($sale);
                $variation->set_price($sale !== '' ? $sale : $regular);
            }

            $variation->save();
            ++$updated;
        }

        return $updated;
    }
}

JC_Quick_Stock::init();
