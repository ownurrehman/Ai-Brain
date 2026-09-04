> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Security & Compliance Architecture — eliteterpenez.com

Security guardrails, data privacy standards, and API protection for **Elite Terpenes** (`eliteterpenez.com`).

---

## 1. Core Principles & PCI-DSS Compliance

1. **Zero Cardholder Data in WordPress:**
   - All payment processing uses PCI-DSS Level 1 compliant hosted fields or redirects (e.g. Stripe Elements or Revolut Pay).
   - WordPress database never stores, processes, or transmits raw credit card numbers, CVVs, or cardholder PANs.
2. **Strict Access Control:**
   - Two-Factor Authentication (2FA) mandatory for all Administrator and Shop Manager accounts.
   - Separate development admin accounts (`dev-*`) from client executive accounts.
3. **Hardened File System:**
   - Disable in-dashboard file editing in `wp-config.php`:
     ```php
     define('DISALLOW_FILE_EDIT', true);
     ```
   - Restrict execution of PHP files inside `/wp-content/uploads/`.

---

## 2. WooCommerce REST API & Bridge Security

The cross-store 48-hour free delivery integration uses the WooCommerce REST API (`POST /wp-json/wc/v3/coupons`):

1. **Key Management:**
   - Dedicated REST API keys (`elite_jc_bridge_ck` / `elite_jc_bridge_cs`) generated automatically on `admin_init`.
   - Keys are stored securely in `wp_options` or defined via `wp-config.php` constants.
   - **Strict prohibition:** NEVER commit consumer keys or secrets to git repositories.
2. **Key Rotation Protocol:**
   - If credentials leak or are rotated:
     1. Open `wp-admin → WooCommerce → Settings → Advanced → REST API`.
     2. Revoke the existing "Justccell cross-sell" key.
     3. Generate a new key and update the consumer key/secret in `justccell.com → Justccell → Elite Cross-sell`.
     4. Test connection via the "Save and test connection" button.
3. **Coupon Scoping & Abuse Prevention:**
   - Free shipping coupons are single-use (`usage_limit => 1`).
   - Coupons are strictly locked to the buyer's email address (`email_restrictions`).
   - Automated expiration set to exactly 48 hours from order creation.

---

## 3. Server & Network Hardening (Hostinger & Cloudflare)

1. **PHP Hardening:**
   - Running on PHP 8.3 with `expose_php = Off`, `session.cookie_secure = 1`, and `session.cookie_httponly = 1`.
   - Strict typing (`declare(strict_types=1);`) in all custom PHP files.
2. **Cloudflare Security:**
   - SSL/TLS: Full (Strict) mode with minimum TLS 1.2.
   - WAF: Cloudflare Bot Fight Mode enabled to block automated scrapers.
   - Dynamic Caching: Never cache `/cart/`, `/checkout/`, `/my-account/`, or `/wp-admin/`.
3. **Data Sanitization & Escaping:**
   - All theme output escaped via `esc_html()`, `esc_attr()`, `esc_url()`, or `wp_kses_post()`.
   - All input sanitized via `sanitize_text_field()`, `sanitize_key()`, `absint()`.
   - Nonces required on all form posts and AJAX actions (`check_admin_referer()`, `check_ajax_referer()`).

---

## 4. Backups & Disaster Recovery

1. **Automated Hostinger Backups:** Daily snapshots maintained with 30-day retention.
2. **Offsite Backups:** UpdraftPlus scheduled weekly to client-owned Google Drive or AWS S3 bucket.
3. **Incident Response:** In the event of compromise:
   - Enable Cloudflare "Under Attack" mode.
   - Restore database and file system from latest verified snapshot.
   - Rotate all Hostinger panel, Cloudflare API, and WordPress administrator passwords.
   - Rotate cross-store REST API credentials.
