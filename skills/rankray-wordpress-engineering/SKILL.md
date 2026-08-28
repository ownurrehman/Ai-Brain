---
name: rankray-wordpress-engineering
description: "WordPress engineering standards: ACF JSON schemas, WP REST API endpoints, custom plugin/theme development, transient caching, and PHP 8.2+ practices."
---

# 🔌 RankRay WordPress Engineering & Plugin Standards

> **Development rules for custom WordPress plugins, themes, ACF synchronization, and REST APIs.**

---

## 🛠️ 1. ACF Local JSON Synchronization
- Always save ACF field group schemas to `theme-folder/acf-json/`.
- Ensure write permissions are active so field updates auto-generate version-controlled JSON files:
```php
add_filter('acf/settings/save_json', function($path) {
    return get_stylesheet_directory() . '/acf-json';
});
```

---

## 🚀 2. Custom WP REST API Endpoints
- Namespace all agency routes under `rankray/v1/`.
- Require Application Passwords for write operations:
```php
add_action('rest_api_init', function() {
    register_rest_route('rankray/v1', '/posts/audit-update', [
        'methods' => 'POST',
        'callback' => 'rankray_handle_audit_update',
        'permission_callback' => function() {
            return current_user_can('edit_posts');
        }
    ]);
});
```

---

## ⚡ 3. Performance & Caching Rules
- Use WordPress Transients for external API fetches:
```php
$data = get_transient('rankray_external_metrics');
if (false === $data) {
    $data = rankray_fetch_remote_data();
    set_transient('rankray_external_metrics', $data, 12 * HOUR_IN_SECONDS);
}
```
- Dequeue unused block library styles on pages that do not use Gutenberg blocks.
