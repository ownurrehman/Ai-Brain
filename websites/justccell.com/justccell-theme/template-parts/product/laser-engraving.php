<?php
/**
 * Inline laser engraving editor — markup contract §4.3.
 *
 * @package Justccell
 *
 * @var array{product_id?:int,config?:array<string,mixed>} $args
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$product_id = (int) ($args['product_id'] ?? 0);
$config     = is_array($args['config'] ?? null) ? $args['config'] : null;
if ($product_id < 1 || $config === null) {
    return;
}

$i18n         = is_array($config['i18n'] ?? null) ? $config['i18n'] : [];
$fonts        = is_array($config['fonts'] ?? null) ? $config['fonts'] : [];
$tiers        = is_array($config['tiers'] ?? null) ? $config['tiers'] : [];
$width        = (int) ($config['canvas']['width'] ?? 640);
$height       = (int) ($config['canvas']['height'] ?? 640);
$editor_ready = (bool) ($config['editorReady'] ?? true);
unset($form_action);

$format_tier_range = static function (int $min, int $max): string {
    if ($max < 1) {
        return sprintf(
            /* translators: %d: minimum quantity */
            __('%d+', 'justccell'),
            $min
        );
    }
    return sprintf(
        /* translators: 1: min qty, 2: max qty */
        __('%1$d – %2$d', 'justccell'),
        $min,
        $max
    );
};

$format_tier_price = static function (float $ppu): string {
    if (function_exists('justccell_format_money')) {
        return justccell_format_money($ppu);
    }
    return '£ ' . number_format($ppu, 2, '.', '');
};
?>
<div
    class="jc-laser"
    data-laser-engraving
    data-product-id="<?php echo esc_attr((string) $product_id); ?>"
>
    <input type="hidden" name="justccell_laser_enabled" value="0" data-laser-field="enabled">
    <input type="hidden" name="justccell_laser_layout" value="" data-laser-field="layout">
    <label class="jc-laser__toggle">
        <input type="checkbox" value="1" data-laser-toggle>
        <span><?php echo esc_html((string) ($i18n['toggle'] ?? __('Add on Laser Engraving (Allow 2 days extra for delivery)', 'justccell'))); ?></span>
    </label>

    <div class="jc-laser__expand" data-laser-panel aria-hidden="true">
        <div class="jc-laser__expand-inner">
            <?php if (!$editor_ready) : ?>
                <p class="jc-laser__hint jc-laser__hint--incomplete">
                    <?php echo esc_html((string) ($i18n['incomplete'] ?? __('Laser engraving is enabled for this product but the editor is still being configured. You can add to cart without engraving for now.', 'justccell'))); ?>
                </p>
            <?php else : ?>
            <div class="jc-laser__shell">
                <p class="jc-laser__hint">
                    <?php echo esc_html((string) ($i18n['safeHint'] ?? __('Drag within the dashed safe zone only.', 'justccell'))); ?>
                </p>
                <p class="jc-laser__hint jc-laser__hint--compliance">
                    <?php echo esc_html((string) ($i18n['uploadHint'] ?? __('For best results, upload hi-res black & white artwork with clean edges. Avoid fine details, gradients, shadows or blur. Accepted formats: .jpg, .jpeg, .png, .ai, .psd, .svg, .eps, .pdf. If your file doesn\'t follow these guidelines, please contact us before ordering.', 'justccell'))); ?>
                </p>

                <div class="jc-laser__layout">
                    <div class="jc-laser__stage">
                        <canvas
                            data-laser-canvas
                            width="<?php echo esc_attr((string) $width); ?>"
                            height="<?php echo esc_attr((string) $height); ?>"
                            aria-label="<?php esc_attr_e('Laser engraving canvas', 'justccell'); ?>"
                        ></canvas>
                    </div>

                    <div class="jc-laser__tools">
                        <label class="jc-laser__field">
                            <span><?php echo esc_html((string) ($i18n['font'] ?? __('Font', 'justccell'))); ?></span>
                            <select data-laser-font>
                                <?php foreach ($fonts as $font) :
                                    if (!is_array($font)) {
                                        continue;
                                    }
                                    ?>
                                    <option value="<?php echo esc_attr((string) ($font['family'] ?? '')); ?>">
                                        <?php echo esc_html((string) ($font['label'] ?? '')); ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </label>

                        <label class="jc-laser__field">
                            <span><?php echo esc_html((string) ($i18n['size'] ?? __('Size', 'justccell'))); ?></span>
                            <input type="range" min="12" max="96" value="28" data-laser-size>
                        </label>

                        <label class="jc-laser__field">
                            <span><?php echo esc_html((string) ($i18n['spacing'] ?? __('Spacing', 'justccell'))); ?></span>
                            <input type="range" min="-50" max="200" value="0" data-laser-spacing>
                        </label>

                        <div class="jc-laser__actions">
                            <button type="button" class="jc-laser__btn" data-laser-add-text>
                                <?php echo esc_html((string) ($i18n['addText'] ?? __('Add text', 'justccell'))); ?>
                            </button>
                            <label class="jc-laser__btn jc-laser__btn--file">
                                <span><?php echo esc_html((string) ($i18n['upload'] ?? __('Upload logo', 'justccell'))); ?></span>
                                <input type="file" accept="image/png,image/jpeg,image/jpg,image/svg+xml,application/pdf,.ai,.eps,.psd" data-laser-upload hidden>
                            </label>
                            <button type="button" class="jc-laser__btn jc-laser__btn--danger" data-laser-remove>
                                <?php echo esc_html((string) ($i18n['remove'] ?? __('Remove selected', 'justccell'))); ?>
                            </button>
                            <button type="button" class="jc-laser__btn jc-laser__btn--save" data-laser-save>
                                <?php echo esc_html((string) ($i18n['save'] ?? __('Save engraving', 'justccell'))); ?>
                            </button>
                        </div>
                        <p class="jc-laser__save-hint">
                            <?php echo esc_html((string) ($i18n['saveHint'] ?? __('Save keeps your text, logo, and layout until you add to cart or close this tab.', 'justccell'))); ?>
                        </p>
                        <p class="jc-laser__save-status" data-laser-save-status hidden></p>

                        <label class="jc-laser__field jc-laser__field--whatsapp">
                            <span><?php echo esc_html((string) ($i18n['whatsappLabel'] ?? __('WhatsApp Phone Number (for artwork proof approval)', 'justccell'))); ?></span>
                            <input
                                type="tel"
                                name="justccell_laser_whatsapp"
                                data-laser-whatsapp
                                inputmode="tel"
                                autocomplete="tel"
                                placeholder="<?php echo esc_attr((string) ($i18n['whatsappPlaceholder'] ?? __('e.g. +44 7495 338694', 'justccell'))); ?>"
                                <?php echo !empty($config['whatsappRequired']) ? 'required' : ''; ?>
                            >
                        </label>
                    </div>
                </div>

                <div class="jc-laser__summary" data-laser-summary aria-live="polite">
                    <?php if ($tiers !== []) : ?>
                        <p class="jc-laser__summary-title">
                            <?php echo esc_html((string) ($i18n['tiersTitle'] ?? __('Engraving volume pricing', 'justccell'))); ?>
                        </p>
                        <table class="jc-laser__tiers" data-laser-tiers>
                            <thead>
                                <tr>
                                    <th scope="col"><?php echo esc_html((string) ($i18n['tiersQty'] ?? __('Quantity', 'justccell'))); ?></th>
                                    <th scope="col"><?php echo esc_html((string) ($i18n['tiersPpu'] ?? __('Per unit', 'justccell'))); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tiers as $i => $tier) :
                                    if (!is_array($tier)) {
                                        continue;
                                    }
                                    $min = max(1, (int) ($tier['minQty'] ?? 1));
                                    $max = max(0, (int) ($tier['maxQty'] ?? 0));
                                    $ppu = (float) ($tier['pricePerUnit'] ?? 0);
                                    ?>
                                    <tr
                                        class="<?php echo $i === 0 ? 'is-on' : ''; ?>"
                                        data-laser-tier
                                        data-min="<?php echo esc_attr((string) $min); ?>"
                                        data-max="<?php echo esc_attr((string) $max); ?>"
                                    >
                                        <th scope="row"><?php echo esc_html($format_tier_range($min, $max)); ?></th>
                                        <td><?php echo esc_html($format_tier_price($ppu)); ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                    <p class="jc-laser__summary-title<?php echo $tiers !== [] ? ' jc-laser__summary-title--estimate' : ''; ?>">
                        <?php echo esc_html((string) ($i18n['summary'] ?? __('Engraving estimate', 'justccell'))); ?>
                    </p>
                    <dl class="jc-laser__totals">
                        <div>
                            <dt><?php echo esc_html((string) ($i18n['setup'] ?? __('Setup fee', 'justccell'))); ?></dt>
                            <dd data-laser-setup>—</dd>
                        </div>
                        <div>
                            <dt><?php echo esc_html((string) ($i18n['unit'] ?? __('Per unit', 'justccell'))); ?></dt>
                            <dd data-laser-unit>—</dd>
                        </div>
                        <div class="jc-laser__totals-strong">
                            <dt><?php echo esc_html((string) ($i18n['total'] ?? __('Engraving total', 'justccell'))); ?></dt>
                            <dd data-laser-total>—</dd>
                        </div>
                    </dl>
                </div>

                <p class="jc-laser__error" data-laser-error hidden></p>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
