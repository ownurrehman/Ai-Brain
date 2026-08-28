<?php
/**
 * One-shot Discover editorial import. Runs on init, then disables itself.
 *
 * Developed by Rank Ray — https://rankray.com
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

add_action('init', static function (): void {
    if (get_option('justccell_editorial_v1') === '1') {
        justccell_editorial_cleanup_plugin();
        return;
    }
    $dir = WP_CONTENT_DIR . '/justccell-blog-import';
    $json = $dir . '/posts.json';
    if (!is_readable($json)) {
        return;
    }
    if (get_transient('justccell_editorial_lock')) {
        return;
    }
    set_transient('justccell_editorial_lock', '1', 10 * MINUTE_IN_SECONDS);

    require_once ABSPATH . 'wp-admin/includes/file.php';
    require_once ABSPATH . 'wp-admin/includes/media.php';
    require_once ABSPATH . 'wp-admin/includes/image.php';

    $posts = json_decode((string) file_get_contents($json), true);
    if (!is_array($posts) || $posts === []) {
        update_option('justccell_editorial_v1', 'error-json');
        return;
    }

    $log = [];
    $deleted = 0;
    $old = get_posts([
        'post_type'      => 'post',
        'post_status'    => ['publish', 'draft', 'pending', 'private', 'future', 'trash'],
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'suppress_filters' => true,
    ]);
    foreach ($old as $id) {
        wp_delete_post((int) $id, true);
        $deleted++;
    }
    $log[] = 'deleted_posts=' . $deleted;

    $author = 1;
    $user = get_user_by('email', 'rankrayofficial@gmail.com');
    if ($user instanceof WP_User) {
        $author = (int) $user->ID;
    }

    $created = 0;
    foreach ($posts as $row) {
        if (!is_array($row)) {
            continue;
        }
        $slug = sanitize_title((string) ($row['slug'] ?? ''));
        $cat_slug = sanitize_title((string) ($row['category'] ?? ''));
        if ($slug === '' || $cat_slug === '') {
            $log[] = 'skip-empty';
            continue;
        }
        $term = get_term_by('slug', $cat_slug, 'category');
        if (!$term instanceof WP_Term) {
            $ins = wp_insert_term(ucfirst($cat_slug), 'category', ['slug' => $cat_slug]);
            if (is_wp_error($ins)) {
                $log[] = 'cat-fail:' . $cat_slug;
                continue;
            }
            $term = get_term((int) $ins['term_id'], 'category');
        }
        $content = (string) ($row['content'] ?? '');
        $id = wp_insert_post([
            'post_title'    => wp_strip_all_tags((string) ($row['title'] ?? '')),
            'post_name'     => $slug,
            'post_status'   => 'publish',
            'post_type'     => 'post',
            'post_content'  => $content,
            'post_excerpt'  => wp_strip_all_tags((string) ($row['excerpt'] ?? '')),
            'post_date'     => (string) ($row['date'] ?? current_time('mysql')),
            'post_author'   => $author,
            'post_category' => $term instanceof WP_Term ? [(int) $term->term_id] : [],
        ], true);
        if (is_wp_error($id) || (int) $id < 1) {
            $log[] = 'insert-fail:' . $slug;
            continue;
        }
        $id = (int) $id;
        update_post_meta($id, 'rank_math_focus_keyword', (string) ($row['focus'] ?? ''));
        update_post_meta($id, 'rank_math_title', (string) ($row['seo_title'] ?? ''));
        update_post_meta($id, 'rank_math_description', (string) ($row['meta'] ?? ''));
        update_post_meta($id, 'rank_math_robots', 'index,follow');

        $image = basename((string) ($row['image'] ?? ''));
        $path = $dir . '/images/' . $image;
        if (is_readable($path)) {
            $tmp = wp_tempnam($image);
            copy($path, $tmp);
            $file_array = [
                'name'     => $image,
                'tmp_name' => $tmp,
            ];
            $att = media_handle_sideload($file_array, $id, (string) ($row['alt'] ?? ''));
            if (!is_wp_error($att) && (int) $att > 0) {
                set_post_thumbnail($id, (int) $att);
                update_post_meta((int) $att, '_wp_attachment_image_alt', (string) ($row['alt'] ?? ''));
            } else {
                $log[] = 'img-fail:' . $slug;
                if (is_string($tmp) && is_file($tmp)) {
                    @unlink($tmp);
                }
            }
        } else {
            $log[] = 'img-missing:' . $image;
        }
        if (function_exists('justccell_assign_default_language')) {
            justccell_assign_default_language($id);
        } elseif (has_action('wpml_set_element_language_details')) {
            $lang = apply_filters('wpml_default_language', null);
            if (is_string($lang) && $lang !== '') {
                do_action('wpml_set_element_language_details', [
                    'element_id'           => $id,
                    'element_type'         => 'post_post',
                    'trid'                 => false,
                    'language_code'        => $lang,
                    'source_language_code' => null,
                ]);
            }
        }
        $created++;
        $log[] = 'ok:' . $id . ':' . $slug;
    }

    update_option('justccell_blog_seeded', '1');
    update_option('justccell_editorial_v1', '1');
    update_option('justccell_editorial_log', implode("\n", $log) . "\ncreated=" . $created);
    delete_transient('justccell_editorial_lock');
    justccell_editorial_cleanup_plugin();
}, 20);

function justccell_editorial_cleanup_plugin(): void
{
    $plugin = WP_CONTENT_DIR . '/mu-plugins/justccell-editorial-import.php';
    if (is_file($plugin)) {
        @unlink($plugin);
    }
}
