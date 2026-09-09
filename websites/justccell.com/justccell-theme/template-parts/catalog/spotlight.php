<?php
/**
 * Catalog lifestyle spotlight — wide image + two-up row (ccell.com sub_img).
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$page_id = (int) ($args['page_id'] ?? 0);
if ($page_id < 1) {
    $page_id = (int) get_queried_object_id();
}
if ($page_id < 1 || !function_exists('justccell_listing_spotlight')) {
    return;
}

$spot = justccell_listing_spotlight($page_id);
if (!is_array($spot)) {
    return;
}

$wide_id   = (int) ($spot['wide_id'] ?? 0);
$left_id   = (int) ($spot['left_id'] ?? 0);
$right_id  = (int) ($spot['right_id'] ?? 0);
$wide_key  = (string) ($spot['wide_key'] ?? '');
$left_key  = (string) ($spot['left_key'] ?? '');
$right_key = (string) ($spot['right_key'] ?? '');

if ($wide_id < 1 && $wide_key === '') {
    return;
}
if ($left_id < 1 && $left_key === '' && $right_id < 1 && $right_key === '') {
    return;
}

$render = static function (int $id, string $key, string $class): void {
    if ($id > 0) {
        echo wp_get_attachment_image($id, 'full', false, [
            'class'   => $class,
            'loading' => 'lazy',
            'alt'     => '',
        ]);
        return;
    }
    if ($key !== '') {
        echo justccell_media_img($key, [
            'class'   => $class,
            'loading' => 'lazy',
            'alt'     => '',
        ]);
    }
};
?>
<section class="c-spotlight" aria-hidden="true">
    <div class="container c-spotlight__box">
        <div class="c-spotlight__wide">
            <?php $render($wide_id, $wide_key, 'c-spotlight__img'); ?>
        </div>
        <?php if ($left_id > 0 || $left_key !== '' || $right_id > 0 || $right_key !== '') : ?>
            <div class="c-spotlight__pair">
                <?php if ($left_id > 0 || $left_key !== '') : ?>
                    <div class="c-spotlight__half">
                        <?php $render($left_id, $left_key, 'c-spotlight__img'); ?>
                    </div>
                <?php endif; ?>
                <?php if ($right_id > 0 || $right_key !== '') : ?>
                    <div class="c-spotlight__half">
                        <?php $render($right_id, $right_key, 'c-spotlight__img'); ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
