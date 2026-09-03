<?php
/**
 * Discover grid card — featured image, two-line title, YYYY-MM-DD.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);
if (!defined('ABSPATH')) {
    exit;
}

$title = get_the_title();
?>
<a class="d-card" href="<?php the_permalink(); ?>">
    <div class="d-card__img">
        <?php
        if (has_post_thumbnail()) {
            the_post_thumbnail('justccell-discover', [
                'alt'      => $title,
                'loading'  => 'lazy',
                'decoding' => 'async',
            ]);
        } else {
            $fallback = justccell_discover_hero_key();
            $id       = justccell_media_id($fallback);
            if ($id > 0) {
                echo wp_get_attachment_image($id, 'justccell-discover', false, [
                    'alt'     => $title,
                    'loading' => 'lazy',
                ]);
            }
        }
        ?>
    </div>
    <div class="d-card__txt">
        <h3><?php echo esc_html($title); ?></h3>
        <span><?php echo esc_html(get_the_date('Y-m-d')); ?></span>
    </div>
</a>
