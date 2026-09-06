<?php
/**
 * Catalog category tab bar.
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$tabs = $args['tabs'] ?? [];
if (!is_array($tabs) || $tabs === []) {
    return;
}
?>
<nav class="c-tabs" aria-label="<?php esc_attr_e('Product categories', 'justccell'); ?>" role="tablist">
    <?php foreach ($tabs as $tab) : ?>
        <?php
        if (!is_array($tab)) {
            continue;
        }
        $panel_key = (string) ($tab['slug'] ?? '');
        if ($panel_key === '') {
            continue;
        }
        $is_active = !empty($tab['is_active']);
        ?>
        <a
            id="<?php echo esc_attr('catalog-tab-' . $panel_key); ?>"
            class="<?php echo $is_active ? 'is-on' : ''; ?>"
            href="<?php echo esc_url((string) ($tab['url'] ?? '#')); ?>"
            data-catalog-tab="<?php echo esc_attr($panel_key); ?>"
            role="tab"
            aria-selected="<?php echo $is_active ? 'true' : 'false'; ?>"
            aria-controls="<?php echo esc_attr('catalog-panel-' . $panel_key); ?>"
        >
            <?php echo esc_html((string) ($tab['label'] ?? '')); ?>
        </a>
    <?php endforeach; ?>
</nav>
