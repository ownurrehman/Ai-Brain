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

        if ($pagenow === 'edit.php' && $typenow === 'product') {
            return true;
        }

        if (!function_exists('get_current_screen')) {
            return false;
        }

        $screen = get_current_screen();

        return $screen instanceof WP_Screen && $screen->id === 'edit-product';
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
            'ajaxUrl' => admin_url('admin-ajax.php'),
            'nonce'   => wp_create_nonce(self::NONCE_ACTION),
            'i18n'    => [
                'title'           => __('Quick Stock', 'justccell'),
                'attribute'       => __('Variation', 'justccell'),
                'quantity'        => __('Stock qty', 'justccell'),
                'manageOff'       => __('Stock was not managed — will enable when you save a quantity.', 'justccell'),
                'loading'         => __('Loading variations…', 'justccell'),
                'saving'          => __('Saving stock…', 'justccell'),
                'saved'           => __('Variation stock updated.', 'justccell'),
                'error'           => __('Could not update stock. Try again.', 'justccell'),
                'empty'           => __('No variations found for this product.', 'justccell'),
                'save'            => __('Save stock', 'justccell'),
                'cancel'          => __('Cancel', 'justccell'),
                'close'           => __('Close', 'justccell'),
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
                <h2 id="jc-quick-stock-modal-title" class="jc-quick-stock-modal__title"><?php esc_html_e('Quick Stock', 'justccell'); ?></h2>
                <p class="jc-quick-stock-modal__product" id="jc-quick-stock-product-name"></p>
                <div class="jc-quick-stock-modal__body" id="jc-quick-stock-modal-body">
                    <p class="jc-quick-stock-modal__loading"><?php esc_html_e('Loading variations…', 'justccell'); ?></p>
                </div>
                <div class="jc-quick-stock-modal__footer">
                    <button type="button" class="button" data-jc-quick-stock-close><?php esc_html_e('Cancel', 'justccell'); ?></button>
                    <button type="button" class="button button-primary" id="jc-quick-stock-save" disabled>
                        <?php esc_html_e('Save stock', 'justccell'); ?>
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
     *     stock_status:string
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
            ];
        }

        return $rows;
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

        if (!current_user_can('edit_post', $product_id)) {
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
        ]);
    }

    public static function ajax_save_variation_stock(): void
    {
        check_ajax_referer(self::NONCE_ACTION, 'nonce');

        if (!current_user_can('edit_products')) {
            wp_send_json_error(['message' => __('Permission denied.', 'justccell')], 403);
        }

        $product_id = isset($_POST['product_id']) ? (int) wp_unslash($_POST['product_id']) : 0;
        $raw_stock  = isset($_POST['stock']) ? wp_unslash($_POST['stock']) : [];

        if ($product_id < 1 || !is_array($raw_stock) || $raw_stock === []) {
            wp_send_json_error(['message' => __('No stock data submitted.', 'justccell')], 400);
        }

        if (!current_user_can('edit_post', $product_id)) {
            wp_send_json_error(['message' => __('Permission denied.', 'justccell')], 403);
        }

        $parent = wc_get_product($product_id);
        if (!$parent instanceof WC_Product_Variable) {
            wp_send_json_error(['message' => __('Product is not variable.', 'justccell')], 400);
        }

        $updated = 0;

        foreach ($raw_stock as $variation_id => $qty_raw) {
            $variation_id = (int) $variation_id;
            if ($variation_id < 1) {
                continue;
            }

            $variation = wc_get_product($variation_id);
            if (!$variation instanceof WC_Product_Variation || (int) $variation->get_parent_id() !== $product_id) {
                continue;
            }

            if (!current_user_can('edit_post', $variation_id)) {
                continue;
            }

            $qty = max(0, (int) $qty_raw);

            $variation->set_manage_stock(true);

            if (function_exists('wc_update_product_stock')) {
                wc_update_product_stock($variation, $qty, 'set');
            } else {
                $variation->set_stock_quantity($qty);
                $variation->set_stock_status($qty > 0 ? 'instock' : 'outofstock');
            }

            if (function_exists('wc_update_product_stock_status')) {
                wc_update_product_stock_status($variation);
            }

            $variation->save();
            ++$updated;
        }

        if ($updated < 1) {
            wp_send_json_error(['message' => __('No variations were updated.', 'justccell')], 400);
        }

        if (class_exists('WC_Product_Variable')) {
            WC_Product_Variable::sync($product_id);
        }

        wp_send_json_success([
            'updated' => $updated,
            'message' => sprintf(
                /* translators: %d: number of variations updated */
                _n('%d variation updated.', '%d variations updated.', $updated, 'justccell'),
                $updated
            ),
        ]);
    }
}

JC_Quick_Stock::init();
