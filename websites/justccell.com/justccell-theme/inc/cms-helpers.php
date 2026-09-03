<?php
/**
 * Shared CMS helpers: heading tags, ACF image IDs, brand page slugs.
 *
 * Developed by Rank Ray — https://rankray.com
 *
 * @package Justccell
 */
declare(strict_types=1);

if (!defined('ABSPATH')) {
    exit;
}

/**
 * @return array<string, string>
 */
function justccell_heading_tag_choices(): array
{
    return [
        'h1' => 'H1',
        'h2' => 'H2',
        'h3' => 'H3',
        'h4' => 'H4',
        'h5' => 'H5',
        'h6' => 'H6',
    ];
}

function justccell_normalize_heading_tag(string $tag, string $default = 'h2'): string
{
    $tag = strtolower(trim($tag));
    return array_key_exists($tag, justccell_heading_tag_choices()) ? $tag : $default;
}

/**
 * Print a heading. $html true allows a safe subset (br, em, strong) for multi-line titles.
 */
function justccell_echo_heading(string $text, string $tag = 'h2', string $class = '', bool $html = false): void
{
    $text = trim($text);
    if ($text === '') {
        return;
    }
    // ACF often stores multi-line titles with <br> or real newlines.
    if (!$html && (str_contains($text, '<br') || str_contains($text, "\n"))) {
        $html = true;
    }
    $el = justccell_normalize_heading_tag($tag);
    $attr = $class !== '' ? ' class="' . esc_attr($class) . '"' : '';
    if ($html) {
        $text  = preg_replace("/\r\n|\r|\n/", '<br>', $text) ?? $text;
        $inner = wp_kses($text, ['br' => [], 'em' => [], 'strong' => [], 'span' => ['class' => true]]);
    } else {
        $inner = esc_html($text);
    }
    echo '<' . $el . $attr . '>' . $inner . '</' . $el . '>';
}

/**
 * ACF image field → attachment ID.
 *
 * @param mixed $value
 */
function justccell_acf_to_attachment_id($value): int
{
    if (is_numeric($value)) {
        return (int) $value;
    }
    if (is_array($value)) {
        return (int) ($value['ID'] ?? $value['id'] ?? 0);
    }
    return 0;
}

/**
 * Resolve a media key or attachment ID to an attachment ID.
 */
function justccell_resolve_media_id(string|int $key_or_id): int
{
    if (is_int($key_or_id) || ctype_digit((string) $key_or_id)) {
        $id = (int) $key_or_id;
        return $id > 0 && get_post_type($id) === 'attachment' ? $id : 0;
    }
    $key = (string) $key_or_id;
    if ($key === '') {
        return 0;
    }
    justccell_ensure_media_url($key);
    return justccell_media_id($key);
}

/**
 * @return list<string>
 */
function justccell_legal_page_slugs(): array
{
    return ['privacy-policy', 'terms', 'cookies'];
}

function justccell_is_legal_page_slug(string $slug): bool
{
    return in_array($slug, justccell_legal_page_slugs(), true);
}

/**
 * Marketing clone pages that still use the generic brand template.
 *
 * @return list<string>
 */
function justccell_generic_brand_page_slugs(): array
{
    return [
        'solution',
        'choose-hardware',
        'oil-types',
        '510-thread',
        'packaging',
        'laser-engraving',
    ];
}

/**
 * Canonical Location page slug plus the retired plural.
 *
 * @return list<string>
 */
function justccell_location_page_slugs(): array
{
    return ['location', 'locations'];
}

function justccell_is_location_page_slug(string $slug): bool
{
    return in_array($slug, justccell_location_page_slugs(), true);
}

function justccell_find_location_page(): ?WP_Post
{
    if (!function_exists('justccell_find_page_by_slug')) {
        return null;
    }
    foreach (justccell_location_page_slugs() as $slug) {
        $page = justccell_find_page_by_slug($slug);
        if ($page instanceof WP_Post) {
            return $page;
        }
    }
    return null;
}

function justccell_location_page_url(): string
{
    $page = justccell_find_location_page();
    if ($page instanceof WP_Post) {
        $link = get_permalink($page);
        if (is_string($link) && $link !== '') {
            return $link;
        }
    }
    return home_url('/location/');
}

function justccell_brand_page_slugs(): array
{
    return array_merge(
        ['about', 'ccell-3-0', 'justccell-3-0'],
        justccell_location_page_slugs(),
        justccell_why_page_slugs(),
        justccell_generic_brand_page_slugs()
    );
}

function justccell_is_brand_page_slug(string $slug): bool
{
    return in_array($slug, justccell_brand_page_slugs(), true);
}

function justccell_page_hides_block_editor(int $post_id): bool
{
    if ($post_id < 1) {
        return false;
    }
    if (function_exists('justccell_page_layout_kind')) {
        $kind = justccell_page_layout_kind($post_id);
        if ($kind === 'legal' || $kind === 'flexible' || $kind === '') {
            if ($kind === 'legal') {
                return false;
            }
        } else {
            return true;
        }
    }
    if ($post_id === (int) get_option('page_on_front')) {
        return true;
    }
    $slug = (string) get_post_field('post_name', $post_id);
    if ($slug === 'contact' || $slug === 'discover') {
        return true;
    }
    if (justccell_is_legal_page_slug($slug)) {
        return false;
    }
    if (justccell_is_brand_page_slug($slug)) {
        return true;
    }
    if (function_exists('justccell_product_category_labels') && array_key_exists($slug, justccell_product_category_labels())) {
        return true;
    }
    $discover_id = function_exists('justccell_discover_page_id') ? justccell_discover_page_id() : 0;
    return $discover_id > 0 && $post_id === $discover_id;
}

/**
 * @return list<string>
 */
function justccell_why_page_slugs(): array
{
    return ['technology', 'safety', 'research', 'manufacture'];
}

function justccell_is_why_page_slug(string $slug): bool
{
    return in_array($slug, justccell_why_page_slugs(), true);
}

/**
 * Four Why Justccell tabs under the hero (ccell .pro_tab). Packaging / laser stay in the header mega only.
 *
 * @return list<array{title:string,slug:string,url:string}>
 */
function justccell_why_page_tabs(): array
{
    return [
        ['title' => __('All-New Technology', 'justccell'), 'slug' => 'technology', 'url' => home_url('/technology/')],
        ['title' => __('Safety', 'justccell'), 'slug' => 'safety', 'url' => home_url('/safety/')],
        ['title' => __('R&D Capability', 'justccell'), 'slug' => 'research', 'url' => home_url('/research/')],
        ['title' => __('Manufacturing Capability', 'justccell'), 'slug' => 'manufacture', 'url' => home_url('/manufacture/')],
    ];
}

/**
 * @return list<string>
 */
function justccell_listing_page_slugs(): array
{
    return array_keys(justccell_product_category_labels());
}

/**
 * ACF field group location: page is one of these slugs.
 *
 * @param list<string> $slugs
 * @return list<list<array{param:string,operator:string,value:string}>>
 */
function justccell_acf_location_pages(array $slugs): array
{
    $rules = [];
    foreach ($slugs as $slug) {
        $slug = sanitize_title((string) $slug);
        if ($slug === '') {
            continue;
        }
        $rules[] = [
            [
                'param'    => 'justccell_page_slug',
                'operator' => '==',
                'value'    => $slug,
            ],
        ];
    }
    return $rules;
}

/**
 * Shared ACF select field for heading tags.
 *
 * @return array<string, mixed>
 */
function justccell_acf_heading_tag_field(string $key, string $name, string $default = 'h2', string $label = 'Heading tag'): array
{
    return [
        'key'           => $key,
        'label'         => $label,
        'name'          => $name,
        'type'          => 'select',
        'choices'       => justccell_heading_tag_choices(),
        'default_value' => $default,
        'return_format' => 'value',
        'allow_null'    => 0,
        'wrapper'       => ['width' => '20'],
    ];
}

function justccell_acf_page_help(string $what): string
{
    return $what
        . ' Headings: the H1–H6 dropdown sits next to each heading. Keep one H1 per page (usually the hero title).'
        . ' SEO title, meta description, canonical, and social share are the Rank Math box on this screen.'
        . ' Image alt text is edited in Media Library.';
}

/**
 * @return array<string, mixed>
 */
function justccell_acf_image_field(string $key, string $name, string $label, string $width = '50'): array
{
    $field = [
        'key'           => $key,
        'label'         => $label,
        'name'          => $name,
        'type'          => 'image',
        'return_format' => 'id',
        'preview_size'  => 'medium',
        'library'       => 'all',
    ];
    if ($width !== '') {
        $field['wrapper'] = ['width' => $width];
    }
    return $field;
}

/**
 * Print product clone image from attachment ID or media key.
 *
 * @param array<string, mixed> $attrs
 */
function justccell_echo_product_media(int $id, string $key, array $attrs = []): void
{
    if ($id > 0) {
        unset($attrs['size']);
        echo wp_get_attachment_image($id, 'full', false, $attrs);
        return;
    }
    if ($key !== '') {
        echo justccell_media_img($key, $attrs);
    }
}

/**
 * Attachment URL for thumbs / spin JSON.
 */
function justccell_product_media_url(int $id, string $key): string
{
    if ($id > 0) {
        $url = wp_get_attachment_url($id);
        return is_string($url) ? $url : '';
    }
    return $key !== '' ? justccell_ensure_media_url($key) : '';
}

/**
 * True when the product is assigned to this storefront product_cat slug
 * (All-In-Ones, Cartridges, …). Child terms are not treated as a match.
 */
function justccell_product_in_storefront_category(int $product_id, string $category): bool
{
    if ($product_id < 1 || $category === '' || !taxonomy_exists('product_cat')) {
        return false;
    }
    if (function_exists('justccell_product_category_labels')
        && !array_key_exists($category, justccell_product_category_labels())
    ) {
        return false;
    }
    return has_term($category, 'product_cat', $product_id);
}

add_filter('use_block_editor_for_post', static function ($use, $post) {
    if (!$post instanceof WP_Post || $post->post_type !== 'page') {
        return $use;
    }
    if (justccell_page_hides_block_editor((int) $post->ID)) {
        return false;
    }
    return $use;
}, 20, 2);

add_action('load-post.php', static function (): void {
    $id = (int) ($_GET['post'] ?? 0);
    if ($id > 0 && justccell_page_hides_block_editor($id)) {
        remove_post_type_support('page', 'editor');
    }
});
