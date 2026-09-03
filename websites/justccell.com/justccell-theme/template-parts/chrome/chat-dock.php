<?php
/**
 * Floating WhatsApp / Telegram. Direct Storefront URLs first; otherwise Contact.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$links = function_exists('justccell_chat_dock_links') ? justccell_chat_dock_links() : [];
if ($links === []) {
    return;
}
?>
<nav class="jc-dock" aria-label="<?php esc_attr_e('Chat', 'justccell'); ?>">
    <?php foreach ($links as $link) : ?>
        <?php $net = (string) $link['network']; ?>
        <a
            class="jc-dock__btn jc-dock__btn--<?php echo esc_attr($net); ?>"
            href="<?php echo esc_url((string) $link['url']); ?>"
            target="_blank"
            rel="noopener noreferrer"
            aria-label="<?php echo esc_attr((string) $link['label']); ?>"
        >
            <?php if ($net === 'whatsapp') : ?>
                <svg class="jc-dock__icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M12.04 2c-5.46 0-9.91 4.44-9.91 9.91 0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21 5.46 0 9.91-4.44 9.91-9.91C21.95 6.44 17.5 2 12.04 2zm5.79 14.13c-.24.68-1.4 1.25-1.93 1.33-.49.07-1.12.1-1.81-.11-.42-.13-.95-.31-1.63-.6-2.87-1.24-4.74-4.13-4.88-4.32-.14-.19-1.15-1.53-1.15-2.92 0-1.39.73-2.07.99-2.36.24-.27.53-.34.7-.34.18 0 .35 0 .5.01.16.01.37-.06.58.44.24.58.81 2 .88 2.14.07.14.12.31.02.5-.09.19-.14.31-.27.48-.14.16-.29.37-.41.49-.14.14-.28.29-.12.56.16.27.71 1.17 1.52 1.89 1.05.93 1.93 1.22 2.2 1.36.27.14.43.12.59-.07.16-.19.68-.79.86-1.06.18-.27.36-.22.6-.13.24.08 1.54.73 1.8.86.27.13.44.2.51.31.07.11.07.64-.17 1.32z"/></svg>
            <?php else : ?>
                <svg class="jc-dock__icon" viewBox="0 0 24 24" aria-hidden="true"><path fill="#fff" d="M21.5 3.5 2.8 10.7c-1.28.5-1.27 1.21-.23 1.53l4.8 1.5 11.14-7.03c.53-.32 1.01-.14.61.18L9.7 15.4l-.35 5.27c.51 0 .73-.23 1.01-.5l2.42-2.35 5.02 3.71c.93.51 1.59.25 1.82-.86L22.9 4.7c.32-1.27-.46-1.84-1.4-1.2z"/></svg>
            <?php endif; ?>
        </a>
    <?php endforeach; ?>
</nav>
