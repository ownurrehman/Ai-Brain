> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Visibility Control & Coming Soon Architecture — eliteterpenez.com

Policy and implementation details for gating the public storefront of **Elite Terpenes** (`eliteterpenez.com`) during active development.

---

## 1. The Coming Soon Gate

**The storefront must remain gated behind a Coming Soon / Maintenance screen until the client explicitly authorizes public launch.**

| User Group | Experience | Access Method |
|---|---|---|
| **Public / Anonymous Visitors** | Coming Soon / Under Construction screen | Any standard HTTP request |
| **Search Engines (Google, Bing)** | `noindex, nofollow` headers / Maintenance 503 | Automated bot crawlers |
| **Logged-in Admins & Shop Managers** | Real live storefront and checkout | Logged in via `wp-login.php` |
| **Authenticated WooCommerce REST Calls** | Real REST endpoints (HTTP 200/201) | Authenticated via REST Key pair |

---

## 2. REST API Bypass for Cross-Site Synergy

A critical challenge of coming-soon mode is that external systems (such as Justccell's coupon dispatcher) need to communicate with WooCommerce REST endpoints (`/wp-json/wc/v3/coupons`).

### The Solution (Built into `justccell-coupon-bridge.php`):
```php
add_filter('woocommerce_coming_soon', static function ($coming_soon) {
    if (defined('REST_REQUEST') && REST_REQUEST) {
        return false;
    }
    return $coming_soon;
});
```

This filter ensures:
1. When a customer orders on `justccell.com`, Justccell's backend can successfully `POST` to `https://eliteterpenez.com/wp-json/wc/v3/coupons` and receive an HTTP 201 Created response.
2. Anonymous browser visitors hitting `https://eliteterpenez.com/` still see the maintenance screen.

---

## 3. Previews & Client Review Protocol

1. **Client Demonstrations:**
   - Provide the client with a dedicated Shop Manager login or secret bypass cookie.
   - Do not disable coming soon mode to show a feature.
2. **Launch Cutover Runbook:**
   - Once all tracks in `docs/ROADMAP.md` are signed off by the client:
     1. Disable `woocommerce_coming_soon` in `wp-admin → WooCommerce → Settings → Site visibility`.
     2. Verify XML sitemaps in Rank Math / Yoast.
     3. Submit sitemap to Google Search Console.
     4. Purge LiteSpeed and Cloudflare edge caches.
