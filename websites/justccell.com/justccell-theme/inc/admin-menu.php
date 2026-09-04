<?php
/**
 * One wp-admin sidebar: Justccell → settings, setup tools, quote leads.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */

declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

const JUSTCCELL_ADMIN_SLUG = 'justccell';

add_filter('admin_footer_text', static function ($text): string {
    $url = defined('JUSTCCELL_DEVELOPER_URL') ? JUSTCCELL_DEVELOPER_URL : 'https://rankray.com';
    return sprintf(
        /* translators: %s: developer site URL */
        __('Justccell theme by <a href="%s" target="_blank" rel="noopener noreferrer">Rank Ray</a>.', 'justccell'),
        esc_url($url)
    );
});

function justccell_admin_page_url(string $slug): string
{
    if ($slug === 'leads') {
        return admin_url('edit.php?post_type=jc_lead');
    }
    return admin_url('admin.php?page=' . $slug);
}

add_action('admin_menu', static function (): void {
    add_menu_page(
        __('Justccell', 'justccell'),
        __('Justccell', 'justccell'),
        'edit_theme_options',
        JUSTCCELL_ADMIN_SLUG,
        'justccell_render_admin_hub',
        'dashicons-store',
        3
    );
    add_submenu_page(
        JUSTCCELL_ADMIN_SLUG,
        __('Overview', 'justccell'),
        __('Overview', 'justccell'),
        'edit_theme_options',
        JUSTCCELL_ADMIN_SLUG,
        'justccell_render_admin_hub'
    );
}, 5);

add_action('admin_menu', static function (): void {
    add_submenu_page(
        'options.php',
        __('CMS Import', 'justccell'),
        __('CMS Import', 'justccell'),
        'manage_options',
        'justccell-cms-import',
        'justccell_render_cms_import_page'
    );
}, 20);

add_action('admin_menu', static function (): void {
    global $submenu;
    if (empty($submenu[JUSTCCELL_ADMIN_SLUG]) || !is_array($submenu[JUSTCCELL_ADMIN_SLUG])) {
        return;
    }
    $want = [
        JUSTCCELL_ADMIN_SLUG,
        'justccell-storefront',
        'justccell-header',
        'justccell-forms',
        'justccell-laser-settings',
        'justccell-elite-cross-sell',
        'edit.php?post_type=jc_lead',
    ];
    $by = [];
    foreach ($submenu[JUSTCCELL_ADMIN_SLUG] as $item) {
        if (!is_array($item) || empty($item[2])) {
            continue;
        }
        $by[(string) $item[2]] = $item;
    }
    $ordered = [];
    foreach ($want as $slug) {
        if (isset($by[$slug])) {
            $ordered[] = $by[$slug];
            unset($by[$slug]);
        }
    }
    $submenu[JUSTCCELL_ADMIN_SLUG] = array_merge($ordered, array_values($by));
}, 999);

add_action('admin_init', static function (): void {
    if (!is_admin()) {
        return;
    }
    $page = isset($_GET['page']) ? sanitize_key((string) wp_unslash($_GET['page'])) : '';
    $from_tools = ['justccell-cms-import'];
    $from_themes = ['justccell-storefront', 'justccell-header', 'justccell-forms', 'justccell-laser-settings', 'justccell-elite-cross-sell'];
    global $pagenow;
    if ($pagenow === 'tools.php' && in_array($page, $from_tools, true)) {
        wp_safe_redirect(justccell_admin_page_url($page));
        exit;
    }
    if ($pagenow === 'themes.php' && in_array($page, $from_themes, true)) {
        wp_safe_redirect(justccell_admin_page_url($page));
        exit;
    }
});

function justccell_render_admin_hub(): void
{
    if (!current_user_can('edit_theme_options')) {
        return;
    }

    $keep = [
        [
            'title' => __('Storefront', 'justccell'),
            'url'   => justccell_admin_page_url('justccell-storefront'),
            'blurb' => __('Instagram, WhatsApp, Telegram, collection copy, laser video, Spain/Switzerland landings, footer legal line.', 'justccell'),
        ],
        [
            'title' => __('Header', 'justccell'),
            'url'   => justccell_admin_page_url('justccell-header'),
            'blurb' => __('Optional header button label and link. Navigation stays in Appearance → Menus (Primary).', 'justccell'),
        ],
        [
            'title' => __('Forms', 'justccell'),
            'url'   => justccell_admin_page_url('justccell-forms'),
            'blurb' => __('Recipients, email subjects, messages, labels, placeholders, and dropdown choices for every website form.', 'justccell'),
        ],
        [
            'title' => __('Laser Engraving', 'justccell'),
            'url'   => justccell_admin_page_url('justccell-laser-settings'),
            'blurb' => __('Global setup fee and tiered per-unit engraving prices for all engraved products.', 'justccell'),
        ],
        [
            'title' => __('Elite Cross-sell', 'justccell'),
            'url'   => justccell_admin_page_url('justccell-elite-cross-sell'),
            'blurb' => __('Elite Terpenes free-delivery coupon API URL, REST keys, and thank-you card copy.', 'justccell'),
        ],
        [
            'title' => __('Quote leads', 'justccell'),
            'url'   => justccell_admin_page_url('leads'),
            'blurb' => __('Inbound wholesale inquiry forms and footer newsletter signups.', 'justccell'),
        ],
    ];
    $wp = [
        ['title' => __('ACF field groups', 'justccell'), 'url' => admin_url('edit.php?post_type=acf-field-group')],
        ['title' => __('Products', 'justccell'), 'url' => admin_url('edit.php?post_type=product')],
        ['title' => __('Menus', 'justccell'), 'url' => admin_url('nav-menus.php')],
        ['title' => __('Media Library', 'justccell'), 'url' => admin_url('upload.php')],
        ['title' => __('WPML languages', 'justccell'), 'url' => admin_url('admin.php?page=sitepress-multilingual-cms/menu/languages.php')],
        ['title' => __('Rank Math SEO', 'justccell'), 'url' => admin_url('admin.php?page=rank-math')],
    ];
    ?>
    <div class="wrap">
        <h1><?php esc_html_e('Justccell', 'justccell'); ?></h1>
        <p>
            <?php
            printf(
                wp_kses(
                    /* translators: 1: developer site URL */
                    __('Theme developed by <a href="%1$s" target="_blank" rel="noopener noreferrer">Rank Ray</a>.', 'justccell'),
                    ['a' => ['href' => [], 'target' => [], 'rel' => []]]
                ),
                esc_url(defined('JUSTCCELL_DEVELOPER_URL') ? JUSTCCELL_DEVELOPER_URL : 'https://rankray.com')
            );
            ?>
        </p>
        <p><?php esc_html_e('Site settings live here. WordPress, WooCommerce, WPML, and Rank Math stay in their own menus — this list only points to them.', 'justccell'); ?></p>

        <h2><?php esc_html_e('Site settings', 'justccell'); ?></h2>
        <ul>
            <?php foreach ($keep as $row) : ?>
                <li>
                    <a href="<?php echo esc_url((string) $row['url']); ?>"><strong><?php echo esc_html((string) $row['title']); ?></strong></a>
                    — <?php echo esc_html((string) $row['blurb']); ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <h2><?php esc_html_e('Elsewhere in wp-admin', 'justccell'); ?></h2>
        <p>
            <?php
            $links = [];
            foreach ($wp as $row) {
                $links[] = '<a href="' . esc_url((string) $row['url']) . '">' . esc_html((string) $row['title']) . '</a>';
            }
            echo wp_kses(implode(' · ', $links), ['a' => ['href' => []]]);
            ?>
        </p>
    </div>
    <?php
}
