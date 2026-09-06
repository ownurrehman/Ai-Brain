<?php
/**
 * Products list — inline stock quick edit with one save button.
 *
 * @package Justccell_Features
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

function justccell_admin_stock_quick_edit_screen(): bool
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
 * Replace Woo stock column with inline editors.
 *
 * @param array<string, string> $columns
 * @return array<string, string>
 */
function justccell_admin_stock_quick_edit_columns(array $columns): array
{
    if (!justccell_admin_stock_quick_edit_screen()) {
        return $columns;
    }

    $out = [];
    foreach ($columns as $key => $label) {
        if ($key === 'is_in_stock') {
            $out['justccell_stock'] = __('Stock', 'justccell');
            continue;
        }
        $out[$key] = $label;
    }

    return $out;
}

add_filter('manage_edit-product_columns', 'justccell_admin_stock_quick_edit_columns', 100);

/**
 * @param string $column
 * @param int|string $post_id
 */
function justccell_admin_stock_quick_edit_render_column(string $column, $post_id): void
{
    if ($column !== 'justccell_stock' || !justccell_admin_stock_quick_edit_screen()) {
        return;
    }

    $post_id = (int) $post_id;
    if ($post_id < 1 || !function_exists('wc_get_product')) {
        return;
    }

    $product = wc_get_product($post_id);
    if (!$product instanceof WC_Product) {
        return;
    }

    if ($product->is_type('variable')) {
        echo '<div class="jc-stock-cell jc-stock-cell--variable" data-product-id="' . esc_attr((string) $post_id) . '">';
        echo '<span class="jc-stock-pill jc-stock-pill--muted">' . esc_html__('Variations', 'justccell') . '</span>';
        if (class_exists('JC_Quick_Stock')) {
            JC_Quick_Stock::render_trigger_button($post_id);
        }
        echo '</div>';
        return;
    }

    $managed = $product->managing_stock();
    $qty     = $managed ? (int) $product->get_stock_quantity() : 0;
    $status  = $product->get_stock_status();
    $in      = $status === 'instock';

    echo '<div class="jc-stock-cell" data-product-id="' . esc_attr((string) $post_id) . '" data-original-qty="' . esc_attr((string) $qty) . '" data-original-status="' . esc_attr($status) . '">';
    echo '<label class="screen-reader-text" for="jc-stock-qty-' . esc_attr((string) $post_id) . '">' . esc_html__('Stock quantity', 'justccell') . '</label>';
    echo '<input type="number" class="jc-stock-qty" id="jc-stock-qty-' . esc_attr((string) $post_id) . '" min="0" step="1" value="' . esc_attr((string) max(0, $qty)) . '" inputmode="numeric" />';
    echo '<select class="jc-stock-status" aria-label="' . esc_attr__('Stock status', 'justccell') . '">';
    echo '<option value="instock"' . selected($in, true, false) . '>' . esc_html__('In stock', 'justccell') . '</option>';
    echo '<option value="outofstock"' . selected(!$in, true, false) . '>' . esc_html__('Out of stock', 'justccell') . '</option>';
    echo '</select>';
    echo '</div>';
}

add_action('manage_product_posts_custom_column', 'justccell_admin_stock_quick_edit_render_column', 10, 2);

function justccell_admin_stock_quick_edit_toolbar(string $which): void
{
    if ($which !== 'top' || !justccell_admin_stock_quick_edit_screen()) {
        return;
    }

    if (!current_user_can('edit_products')) {
        return;
    }

    echo '<div class="jc-stock-toolbar" id="jc-stock-toolbar">';
    echo '<div class="jc-stock-toolbar__copy">';
    echo '<span class="jc-stock-toolbar__title">' . esc_html__('Quick stock', 'justccell') . '</span>';
    echo '<span class="jc-stock-toolbar__hint">' . esc_html__('Edit quantities in the Stock column, then save everything at once.', 'justccell') . '</span>';
    echo '</div>';
    echo '<div class="jc-stock-toolbar__actions">';
    echo '<span class="jc-stock-toolbar__pending" id="jc-stock-pending" hidden>' . esc_html__('Unsaved changes', 'justccell') . '</span>';
    echo '<button type="button" class="button jc-stock-toolbar__reset" id="jc-stock-reset" disabled>' . esc_html__('Reset', 'justccell') . '</button>';
    echo '<button type="button" class="button button-primary jc-stock-toolbar__save" id="jc-stock-save" disabled>';
    echo esc_html__('Save all stock', 'justccell');
    echo '</button>';
    echo '</div>';
    echo '<div class="jc-stock-toolbar__feedback" id="jc-stock-feedback" role="status" aria-live="polite"></div>';
    echo '</div>';
}

add_action('manage_posts_extra_tablenav', 'justccell_admin_stock_quick_edit_toolbar', 15);

function justccell_admin_stock_quick_edit_assets(string $hook_suffix): void
{
    if ($hook_suffix !== 'edit.php' || !justccell_admin_stock_quick_edit_screen()) {
        return;
    }

    if (!current_user_can('edit_products')) {
        return;
    }

    $ver = defined('JUSTCCELL_FEATURES_VERSION') ? JUSTCCELL_FEATURES_VERSION : '1.0.0';

    wp_enqueue_style(
        'justccell-admin-stock-quick-edit',
        JUSTCCELL_FEATURES_URL . 'assets/css/admin-stock-quick-edit.css',
        [],
        $ver
    );

    wp_enqueue_script(
        'justccell-admin-stock-quick-edit',
        JUSTCCELL_FEATURES_URL . 'assets/js/admin-stock-quick-edit.js',
        [],
        $ver,
        true
    );

    wp_localize_script('justccell-admin-stock-quick-edit', 'justccellStockQuickEdit', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('justccell_bulk_stock'),
        'i18n'    => [
            'saving'  => __('Saving stock…', 'justccell'),
            'saved'   => __('Stock updated.', 'justccell'),
            'error'   => __('Could not save stock. Try again.', 'justccell'),
            'none'    => __('No changes to save.', 'justccell'),
            'updated' => __('Updated %d product(s).', 'justccell'),
        ],
    ]);
}

add_action('admin_enqueue_scripts', 'justccell_admin_stock_quick_edit_assets');

/**
 * @return array{product_id:int,quantity:int,status:string}|null
 */
function justccell_admin_stock_quick_edit_parse_row(mixed $row): ?array
{
    if (!is_array($row)) {
        return null;
    }

    $product_id = isset($row['product_id']) ? (int) $row['product_id'] : 0;
    if ($product_id < 1) {
        return null;
    }

    $quantity = isset($row['quantity']) ? max(0, (int) $row['quantity']) : 0;
    $status   = isset($row['status']) ? sanitize_key((string) $row['status']) : 'instock';
    if (!in_array($status, ['instock', 'outofstock'], true)) {
        $status = $quantity > 0 ? 'instock' : 'outofstock';
    }

    return [
        'product_id' => $product_id,
        'quantity'   => $quantity,
        'status'     => $status,
    ];
}

function justccell_admin_stock_quick_edit_save_ajax(): void
{
    check_ajax_referer('justccell_bulk_stock', 'nonce');

    if (!current_user_can('edit_products')) {
        wp_send_json_error(['message' => __('Permission denied.', 'justccell')], 403);
    }

    $raw_rows = isset($_POST['rows']) ? wp_unslash($_POST['rows']) : '';
    if (!is_string($raw_rows) || $raw_rows === '') {
        wp_send_json_error(['message' => __('No stock rows submitted.', 'justccell')], 400);
    }

    $rows = json_decode($raw_rows, true);
    if (!is_array($rows) || $rows === []) {
        wp_send_json_error(['message' => __('No stock rows submitted.', 'justccell')], 400);
    }

    $updated = 0;
    $skipped = 0;
    $errors  = [];

    foreach ($rows as $row) {
        $parsed = justccell_admin_stock_quick_edit_parse_row($row);
        if ($parsed === null) {
            ++$skipped;
            continue;
        }

        $product = wc_get_product($parsed['product_id']);
        if (!$product instanceof WC_Product) {
            ++$skipped;
            continue;
        }

        if ($product->is_type('variable')) {
            ++$skipped;
            continue;
        }

        if (!current_user_can('edit_post', $parsed['product_id'])) {
            $errors[] = $parsed['product_id'];
            continue;
        }

        $product->set_manage_stock(true);
        $product->set_stock_quantity($parsed['quantity']);
        $product->set_stock_status($parsed['status']);
        $product->save();
        ++$updated;
    }

    if ($errors !== []) {
        wp_send_json_error([
            'message' => __('Some products could not be updated.', 'justccell'),
            'updated' => $updated,
            'skipped' => $skipped,
        ], 500);
    }

    wp_send_json_success([
        'updated' => $updated,
        'skipped' => $skipped,
        'message' => sprintf(
            /* translators: %d: number of products updated */
            _n('%d product updated.', '%d products updated.', $updated, 'justccell'),
            $updated
        ),
    ]);
}

add_action('wp_ajax_justccell_save_bulk_stock', 'justccell_admin_stock_quick_edit_save_ajax');
