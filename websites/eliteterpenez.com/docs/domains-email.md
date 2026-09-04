> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Domains, DNS & Email Architecture — eliteterpenez.com

Infrastructure mapping for domain routing, DNS records, and transactional mail delivery for **Elite Terpenes**.

---

## 1. Domain Configuration

| Domain | Role | Hosting / Registrar | Status |
|---|---|---|---|
| **`eliteterpenez.com`** | Primary storefront, canonical URLs, SSL | Hostinger (User: `u984013785`, WP: `30437919`) | Active / Coming Soon |
| **`www.eliteterpenez.com`** | Canonical redirect to apex (`https://eliteterpenez.com`) | Cloudflare Edge 301 Redirect | Configured |

---

## 2. DNS & Cloudflare Configuration

1. **Proxy Mode:** Proxied (Orange Cloud) through Cloudflare for DDoS mitigation, edge caching, and SSL termination.
2. **Origin Server:** Points to Hostinger Cloud infrastructure for user `u984013785`.
3. **Cache Rules:**
   - Edge Cache TTL: Standard static assets (CSS, JS, WebP, WOFF2) cached at edge for 30 days.
   - Bypass Cache: Bypass edge cache for `/cart/`, `/checkout/`, `/my-account/`, and `/wp-admin/`.
   - REST API: Bypass caching on `/wp-json/*` to ensure real-time coupon validation between stores.

---

## 3. Transactional & Business Email

### 3.1 Mailboxes
- **`info@eliteterpenez.com`**: Primary business inquiries, customer support, and sales team contact.
- **`orders@eliteterpenez.com`**: WooCommerce transactional notifications (Order Processing, Completed, Coupon Delivery).

### 3.2 Deliverability & Authentication (SPF, DKIM, DMARC)
To ensure order confirmations and 48-hour free shipping coupon links arrive reliably in customer inboxes without landing in spam:
- **SPF:** `v=spf1 include:_spf.mail.hostinger.com ~all`
- **DKIM:** Hostinger / SMTP provider 2048-bit DKIM key enabled on root DNS.
- **DMARC:** `v=DMARC1; p=quarantine; rua=mailto:dmarc@eliteterpenez.com; pct=100`

### 3.3 SMTP Gateway
Do not rely on native PHP `mail()`. Use an authenticated SMTP plugin (e.g. FluentSMTP or Post SMTP) connected via TLS port 587 or 465 to Hostinger Business Mail or Google Workspace.
