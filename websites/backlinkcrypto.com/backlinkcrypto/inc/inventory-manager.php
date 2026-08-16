<?php
/**
 * Inventory Manager — spreadsheet-style bulk edit for marketplace products.
 *
 * WP Admin → Backlink Crypto → Inventory
 *
 * @package BacklinkCrypto
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('admin_menu', static function (): void {
    add_submenu_page(
        'backlinkcrypto-settings',
        __('Inventory', 'backlinkcrypto'),
        __('Inventory', 'backlinkcrypto'),
        'edit_products',
        'backlinkcrypto-inventory',
        'backlinkcrypto_render_inventory_page'
    );
}, 20);

add_action('admin_enqueue_scripts', static function (): void {
    $page = isset($_GET['page']) ? sanitize_text_field((string) wp_unslash($_GET['page'])) : '';
    if ($page !== 'backlinkcrypto-inventory') {
        return;
    }

    wp_enqueue_style(
        'backlinkcrypto-inventory-admin',
        BACKLINKCRYPTO_URI . '/assets/css/inventory-admin.css',
        [],
        BACKLINKCRYPTO_VERSION
    );
    wp_enqueue_script(
        'backlinkcrypto-inventory-admin',
        BACKLINKCRYPTO_URI . '/assets/js/inventory-admin.js',
        [],
        BACKLINKCRYPTO_VERSION,
        true
    );
    wp_localize_script('backlinkcrypto-inventory-admin', 'bcInventory', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('bc_inventory_save'),
        'i18n'    => [
            'saving'  => __('Saving…', 'backlinkcrypto'),
            'saved'   => __('Saved', 'backlinkcrypto'),
            'error'   => __('Save failed — try again', 'backlinkcrypto'),
            'dirty'   => __('unsaved change(s)', 'backlinkcrypto'),
            'confirm' => __('Save all edited rows?', 'backlinkcrypto'),
        ],
    ]);
});

/**
 * @return list<array<string,mixed>>
 */
function backlinkcrypto_inventory_rows(): array
{
    if (!function_exists('wc_get_product')) {
        return [];
    }

    $q = new WP_Query([
        'post_type'              => 'product',
        'post_status'            => ['publish', 'draft', 'private'],
        'posts_per_page'         => 1000,
        'orderby'                => 'title',
        'order'                  => 'ASC',
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => false,
    ]);

    $rows = [];
    while ($q->have_posts()) {
        $q->the_post();
        $id = get_the_ID();
        $product = wc_get_product($id);
        if (!$product) {
            continue;
        }
        $m = backlinkcrypto_product_metrics($id);
        $langs = $m['languages'] ?? ['EN'];
        $rows[] = [
            'id'        => $id,
            'name'      => $product->get_name(),
            'domain'    => $m['domain'] !== '' ? $m['domain'] : $product->get_name(),
            'da'        => $m['da'] !== '' && $m['da'] !== null ? (string) (int) $m['da'] : '',
            'dr'        => $m['dr'] !== '' && $m['dr'] !== null ? (string) (int) $m['dr'] : '',
            'traffic'   => $m['traffic'] !== '' && $m['traffic'] !== null ? (string) (int) $m['traffic'] : '',
            'niche'     => (string) ($m['niche'] ?: 'Crypto'),
            'languages' => implode(',', $langs),
            'price'     => (string) $product->get_regular_price(),
            'verified'  => !empty($m['verified']),
            'dofollow'  => !empty($m['dofollow']),
            'featured'  => $product->get_featured(),
            'status'    => get_post_status($id) ?: 'draft',
            'edit_url'  => get_edit_post_link($id, 'raw') ?: '',
        ];
    }
    wp_reset_postdata();

    usort($rows, static function (array $a, array $b): int {
        if ($a['featured'] !== $b['featured']) {
            return $a['featured'] ? -1 : 1;
        }
        $drA = $a['dr'] !== '' ? (int) $a['dr'] : -1;
        $drB = $b['dr'] !== '' ? (int) $b['dr'] : -1;
        if ($drA !== $drB) {
            return $drB <=> $drA;
        }
        return strcasecmp((string) $a['domain'], (string) $b['domain']);
    });

    return $rows;
}

function backlinkcrypto_render_inventory_page(): void
{
    if (!current_user_can('edit_products')) {
        wp_die(esc_html__('You do not have permission to manage products.', 'backlinkcrypto'));
    }

    $rows = backlinkcrypto_inventory_rows();
    $featured_count = count(array_filter($rows, static fn(array $r): bool => !empty($r['featured'])));
    ?>
    <div class="wrap bc-inv">
        <div class="bc-inv__head">
            <div>
                <h1><?php esc_html_e('Inventory', 'backlinkcrypto'); ?></h1>
                <p class="bc-inv__lead">
                    <?php
                    printf(
                        /* translators: 1: total sites, 2: featured count */
                        esc_html__('Edit price & metrics in one table — no need to open each product. %1$d sites · %2$d featured.', 'backlinkcrypto'),
                        count($rows),
                        $featured_count
                    );
                    ?>
                </p>
            </div>
            <div class="bc-inv__actions">
                <input type="search" id="bc-inv-search" class="bc-inv__search" placeholder="<?php esc_attr_e('Filter domain…', 'backlinkcrypto'); ?>" />
                <label class="bc-inv__toggle">
                    <input type="checkbox" id="bc-inv-featured-only" />
                    <?php esc_html_e('Featured only', 'backlinkcrypto'); ?>
                </label>
                <button type="button" class="button button-primary button-hero" id="bc-inv-save">
                    <?php esc_html_e('Save all changes', 'backlinkcrypto'); ?>
                </button>
                <span class="bc-inv__status" id="bc-inv-status" aria-live="polite"></span>
            </div>
        </div>

        <p class="bc-inv__hint">
            <?php esc_html_e('Star = Featured (shows in the homepage Featured box and sorts to the top). Change cells, then Save all — or blur a row to auto-save.', 'backlinkcrypto'); ?>
            <?php if (get_option(BC_CATALOG_FILTER_OPTION) === '1') : ?>
                <br /><?php esc_html_e('Catalog filter applied: non crypto/tech sites were moved to Draft (recoverable in Products → Drafts).', 'backlinkcrypto'); ?>
            <?php endif; ?>
        </p>

        <div class="bc-inv__table-wrap">
            <table class="bc-inv__table" id="bc-inv-table">
                <thead>
                    <tr>
                        <th class="bc-inv__col-star" title="<?php esc_attr_e('Featured', 'backlinkcrypto'); ?>">★</th>
                        <th><?php esc_html_e('Domain', 'backlinkcrypto'); ?></th>
                        <th><?php esc_html_e('DA', 'backlinkcrypto'); ?></th>
                        <th><?php esc_html_e('DR', 'backlinkcrypto'); ?></th>
                        <th><?php esc_html_e('Traffic', 'backlinkcrypto'); ?></th>
                        <th><?php esc_html_e('Niche', 'backlinkcrypto'); ?></th>
                        <th><?php esc_html_e('Lang', 'backlinkcrypto'); ?></th>
                        <th><?php esc_html_e('Price', 'backlinkcrypto'); ?></th>
                        <th title="<?php esc_attr_e('Verified', 'backlinkcrypto'); ?>">✓</th>
                        <th title="<?php esc_attr_e('Dofollow', 'backlinkcrypto'); ?>">DF</th>
                        <th><?php esc_html_e('Status', 'backlinkcrypto'); ?></th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row) : ?>
                        <tr
                            class="bc-inv__row<?php echo !empty($row['featured']) ? ' is-featured' : ''; ?>"
                            data-id="<?php echo esc_attr((string) $row['id']); ?>"
                            data-domain="<?php echo esc_attr(strtolower((string) $row['domain'])); ?>"
                            data-featured="<?php echo !empty($row['featured']) ? '1' : '0'; ?>"
                        >
                            <td class="bc-inv__col-star">
                                <label class="bc-inv__star">
                                    <input type="checkbox" data-field="featured" <?php checked(!empty($row['featured'])); ?> />
                                    <span aria-hidden="true">★</span>
                                </label>
                            </td>
                            <td>
                                <input type="text" data-field="domain" value="<?php echo esc_attr((string) $row['domain']); ?>" />
                            </td>
                            <td>
                                <input type="number" min="0" max="100" data-field="da" value="<?php echo esc_attr((string) $row['da']); ?>" />
                            </td>
                            <td>
                                <input type="number" min="0" max="100" data-field="dr" value="<?php echo esc_attr((string) $row['dr']); ?>" />
                            </td>
                            <td>
                                <input type="number" min="0" data-field="traffic" value="<?php echo esc_attr((string) $row['traffic']); ?>" />
                            </td>
                            <td>
                                <input type="text" data-field="niche" value="<?php echo esc_attr((string) $row['niche']); ?>" />
                            </td>
                            <td>
                                <input type="text" data-field="languages" value="<?php echo esc_attr((string) $row['languages']); ?>" title="<?php esc_attr_e('Comma-separated, e.g. EN,ES', 'backlinkcrypto'); ?>" />
                            </td>
                            <td>
                                <input type="number" min="0" step="0.01" data-field="price" value="<?php echo esc_attr((string) $row['price']); ?>" />
                            </td>
                            <td class="bc-inv__check">
                                <input type="checkbox" data-field="verified" <?php checked(!empty($row['verified'])); ?> />
                            </td>
                            <td class="bc-inv__check">
                                <input type="checkbox" data-field="dofollow" <?php checked(!empty($row['dofollow'])); ?> />
                            </td>
                            <td>
                                <select data-field="status">
                                    <option value="publish" <?php selected($row['status'], 'publish'); ?>><?php esc_html_e('Live', 'backlinkcrypto'); ?></option>
                                    <option value="draft" <?php selected($row['status'], 'draft'); ?>><?php esc_html_e('Draft', 'backlinkcrypto'); ?></option>
                                    <option value="private" <?php selected($row['status'], 'private'); ?>><?php esc_html_e('Private', 'backlinkcrypto'); ?></option>
                                </select>
                            </td>
                            <td class="bc-inv__row-actions">
                                <?php if ($row['edit_url'] !== '') : ?>
                                    <a href="<?php echo esc_url((string) $row['edit_url']); ?>" target="_blank" rel="noopener"><?php esc_html_e('Full', 'backlinkcrypto'); ?></a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if ($rows === []) : ?>
            <p><?php esc_html_e('No products found. Seed inventory or add WooCommerce products first.', 'backlinkcrypto'); ?></p>
        <?php endif; ?>
    </div>
    <?php
}

add_action('wp_ajax_bc_inventory_save', static function (): void {
    if (!current_user_can('edit_products')) {
        wp_send_json_error(['message' => 'forbidden'], 403);
    }
    check_ajax_referer('bc_inventory_save', 'nonce');

    if (!function_exists('wc_get_product')) {
        wp_send_json_error(['message' => 'woocommerce_missing'], 500);
    }

    $raw = isset($_POST['rows']) ? wp_unslash($_POST['rows']) : '';
    if (is_string($raw)) {
        $decoded = json_decode($raw, true);
    } elseif (is_array($raw)) {
        $decoded = $raw;
    } else {
        $decoded = null;
    }

    if (!is_array($decoded) || $decoded === []) {
        wp_send_json_error(['message' => 'empty'], 400);
    }

    $saved = 0;
    $errors = [];

    foreach ($decoded as $item) {
        if (!is_array($item)) {
            continue;
        }
        $id = isset($item['id']) ? (int) $item['id'] : 0;
        if ($id <= 0) {
            continue;
        }

        $product = wc_get_product($id);
        if (!$product) {
            $errors[] = $id;
            continue;
        }

        $domain = sanitize_text_field((string) ($item['domain'] ?? ''));
        $da     = sanitize_text_field((string) ($item['da'] ?? ''));
        $dr     = sanitize_text_field((string) ($item['dr'] ?? ''));
        $traffic = sanitize_text_field((string) ($item['traffic'] ?? ''));
        $niche  = sanitize_text_field((string) ($item['niche'] ?? 'Crypto'));
        $langs  = sanitize_text_field((string) ($item['languages'] ?? 'EN'));
        $price  = wc_format_decimal((string) ($item['price'] ?? '0'));
        $verified = !empty($item['verified']);
        $dofollow = !empty($item['dofollow']);
        $featured = !empty($item['featured']);
        $status = sanitize_key((string) ($item['status'] ?? 'publish'));
        if (!in_array($status, ['publish', 'draft', 'private'], true)) {
            $status = 'publish';
        }

        update_post_meta($id, '_bc_domain', $domain);
        update_post_meta($id, '_bc_da', $da === '' ? '' : (string) max(0, min(100, (int) $da)));
        update_post_meta($id, '_bc_dr', $dr === '' ? '' : (string) max(0, min(100, (int) $dr)));
        update_post_meta($id, '_bc_traffic', $traffic === '' ? '' : (string) max(0, (int) $traffic));
        update_post_meta($id, '_bc_niche', $niche !== '' ? $niche : 'Crypto');
        update_post_meta($id, '_bc_languages', $langs !== '' ? $langs : 'EN');
        $lang_list = array_values(array_filter(array_map('trim', explode(',', $langs))));
        update_post_meta($id, '_bc_language', $lang_list[0] ?? 'EN');
        update_post_meta($id, '_bc_verified', $verified ? '1' : '0');
        update_post_meta($id, '_bc_dofollow', $dofollow ? '1' : '0');

        $product->set_regular_price($price);
        $product->set_price($price);
        $product->set_featured($featured);
        $product->set_status($status);

        if ($domain !== '' && $product->get_name() !== $domain) {
            $product->set_name($domain);
        }

        $product->save();
        $saved++;
    }

    wp_send_json_success([
        'saved'  => $saved,
        'errors' => $errors,
    ]);
});
