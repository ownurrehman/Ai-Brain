> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# WordPress & WooCommerce Coding Standards — eliteterpenez.com

Strict coding guidelines and architectural standards for all PHP, HTML, CSS, and JavaScript development on `eliteterpenez.com`.

---

## 1. Core Principles

1. **WordPress Coding Standards (WPCS):** All PHP code must adhere to WordPress PHP standards, including tab indentation, Yoda conditions (where standard), and clear semantic variable naming.
2. **Strict Typing:** All custom PHP files must declare strict types:
   ```php
   <?php
   declare(strict_types=1);

   if (!defined('ABSPATH')) {
       exit; // Exit if accessed directly
   }
   ```
3. **Prefixing:**
   - Functions, hooks, and classes must be prefixed with `elite_` or `Elite_`.
   - Constants must be prefixed with `ELITE_`.
   - CSS classes must be prefixed with `et-` or `elite-` (e.g. `.et-hero`, `.et-terpene-card`).

---

## 2. Security: Escaping, Sanitization & Nonces

Every piece of data entering or leaving the system MUST be secured:

### 2.1 Output Escaping (Late Escaping)
Never output raw variables in templates. Always escape at the point of output:
- `esc_html()`: For plain text strings inside tags (e.g. `<h1><?php echo esc_html($title); ?></h1>`).
- `esc_attr()`: For HTML attributes (e.g. `<div class="<?php echo esc_attr($class); ?>">`).
- `esc_url()`: For URLs and links (e.g. `<a href="<?php echo esc_url($link); ?>">`).
- `wp_kses_post()`: For rich HTML / WYSIWYG content allowed in WordPress posts.

### 2.2 Input Sanitization
Clean all user input and query parameters:
- `sanitize_text_field()`: Standard single-line strings.
- `sanitize_textarea_field()`: Multi-line strings.
- `sanitize_key()`: Slugs, IDs, field keys.
- `absint()`: Positive integers (e.g. post IDs, quantities).
- `esc_url_raw()`: URLs saved to the database.

### 2.3 Nonces & Capabilities
- Protect all state-changing actions, AJAX handlers, and form submissions with `wp_verify_nonce()` or `check_admin_referer()`.
- Always check user permissions using `current_user_can('manage_woocommerce')` or appropriate capabilities.

---

## 3. WordPress Database & Performance Best Practices

1. **No Direct SQL When WP Core Exists:**
   - Use `WP_Query`, `wc_get_products()`, `get_posts()`, and `wp_get_attachment_image()` instead of manual `$wpdb` queries.
2. **Prevent N+1 Queries:**
   - Batch load posts or use transients (`set_transient()` / `get_transient()`) for expensive calculations or external REST API syncs.
3. **Action Scheduler for Background Tasks:**
   - Use WooCommerce Action Scheduler (`as_schedule_single_action()`) for cross-site REST API calls and webhooks. Never run long-running external HTTP requests in the user checkout thread.

---

## 4. Skills & Agent References

When developing or refactoring theme code, AI agents should invoke and consult:
- **`wordpress-expert`** (`~/.gemini/config/skills/wordpress-expert/SKILL.md`): Master workflow covering themes, plugins, WooCommerce, security, and performance.
- Official WordPress Codex & Developer Handbook.
- WooCommerce Developer Documentation.
