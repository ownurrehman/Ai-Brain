> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Cross-Site 48-Hour Free Delivery System

Architecture, data flow, and implementation specification for the 48-hour cross-store free shipping bridge between **Just CCELL** (`justccell.com`) and **Elite Terpenes** (`eliteterpenez.com`).

---

## 1. System Overview

To maximize average order value (AOV) and customer lifetime value across both brands:
- An order placed on `justccell.com` creates a unique free-shipping coupon on `eliteterpenez.com`.
- An order placed on `eliteterpenez.com` creates a corresponding free-shipping coupon on `justccell.com`.
- Coupons are valid for exactly **48 hours** from the time of purchase.
- Checkout on the primary store **never blocks or waits** on the remote site; API communication runs asynchronously via Action Scheduler with a strict 4-second timeout on any synchronous fallback.

---

## 2. Technical Architecture & Data Flow

```
[Customer on Justccell]
         │
         ▼
[Places Order Order-1234] ───► [Order Status: Processing / Completed]
                                   │
                                   ▼
                      [Action Scheduler Background Job]
                                   │
                                   ▼
             [POST to https://eliteterpenez.com/wp-json/wc/v3/coupons]
             - Code: JC-1234
             - Discount: percent (0%)
             - free_shipping: true
             - date_expires: now + 48 hours
             - individual_use: true
             - usage_limit: 1
             - email_restrictions: [customer_email]
                                   │
                                   ▼
[Customer sees confirmation / receives email]
"Get Free Delivery on Elite Terpenes for 48 Hours"
Link: https://eliteterpenez.com/?apply_coupon=JC-1234
                                   │
                                   ▼
                     [Customer clicks Magic Link]
                                   │
                                   ▼
             [justccell-coupon-bridge.php (mu-plugin on ET)]
             1. Captures `?apply_coupon=JC-1234`
             2. Persists code in WooCommerce Session cookie
             3. Strips query arg via wp_safe_redirect
             4. Automatically applies coupon on cart load or add-to-cart
                                   │
                                   ▼
[Checkout on Elite Terpenes: Free Delivery applied!]
```

---

## 3. Server Configuration & mu-plugin

### 3.1 `justccell-coupon-bridge.php`
- Located at: `websites/eliteterpenez.com/bridge/justccell-coupon-bridge.php` (deployed to `wp-content/mu-plugins/justccell-coupon-bridge.php`).
- Key Responsibilities:
  1. **WooCommerce Compatibility:** Declares HPOS (`custom_order_tables`) compatibility.
  2. **Session Storage:** Catches `?apply_coupon=...` on `wp_loaded`, sanitizes input, stores it in `WC()->session->set('elite_jc_pending_coupon', ...)`, and redirects to remove the query arg.
  3. **Auto-Apply:** Hooks into `woocommerce_cart_loaded_from_session` and `woocommerce_add_to_cart` to execute `WC()->cart->apply_coupon($code)`.
  4. **Shipping Method Setup:** Automatically verifies that WooCommerce has a Free Shipping method requiring a coupon:
     - Adds a Free Shipping method with `requires => 'coupon'` to Zone 0 (Rest of the World) if not present.
  5. **Automated REST Key Generation:**
     - On first admin load, generates a dedicated read/write WooCommerce REST API key (`elite_jc_bridge_ck` / `elite_jc_bridge_cs`) for the cross-sell integration.
     - Displays credentials once under `WooCommerce → Justccell bridge` in `wp-admin`.

### 3.2 Security & Rate Limiting
- Coupons are restricted to 1 usage and tied to the buyer's email address.
- REST API keys are stored in the database and should be rotated if exposed.
- REST requests bypass coming soon gates (`woocommerce_coming_soon` filter) specifically for authorized REST API calls.
