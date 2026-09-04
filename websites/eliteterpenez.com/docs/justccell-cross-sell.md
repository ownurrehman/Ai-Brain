> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/justccell.com/docs/elite-cross-sell|Justccell-side spec]]

# Just CCELL Cross-Sell — 48-Hour Free Delivery Coupons

Live target: [eliteterpenez.com](https://eliteterpenez.com/). Remote store: [justccell.com](https://justccell.com/).  
Hostinger account: user `u984013785`, WP software ID `30437919`.  
Plugin slug: `justccell-coupon-bridge` (live at `wp-content/plugins/justccell-coupon-bridge/`).

---

## 1. Executive Summary

Elite Terpenes and Just CCELL provide an automated cross-promotional perk:
- Customers ordering vape hardware/cartridges on `justccell.com` unlock **free delivery** on `eliteterpenez.com` for 48 hours.
- Customers ordering terpenes on `eliteterpenez.com` unlock **free delivery** on `justccell.com` for 48 hours.
- Coupons are created asynchronously via the WooCommerce REST API (`POST /wp-json/wc/v3/coupons`) and never block checkout.

---

## 2. Inbound Coupon Handling on Elite Terpenes

### 2.1 The Bridge Plugin (`justccell-coupon-bridge`)
The bridge is a WordPress plugin deployed to `wp-content/plugins/justccell-coupon-bridge/` on Hostinger account `u984013785`.

**Core Mechanics:**
1. **REST Key Bootstrapping:**
   - On `admin_init`, generates a dedicated Read/Write WooCommerce REST API key pair (`elite_jc_bridge_ck` / `elite_jc_bridge_cs`).
   - Displays keys under `wp-admin → WooCommerce → Justccell bridge` for easy copying to Justccell's settings.
2. **Shipping Method Seeding:**
   - Automatically inspects WooCommerce Shipping Zone 0 (Rest of the World).
   - If no Free Shipping method requiring a coupon exists, it seeds a Free Shipping method titled "Free delivery" with `requires => 'coupon'`.
3. **Magic Link Ingestion:**
   - Listens on `wp_loaded` for incoming URLs with `?apply_coupon=JC-XXXX`.
   - Sanitizes and formats the coupon code via `wc_format_coupon_code()`.
   - Persists the code in the active WooCommerce customer session: `WC()->session->set('elite_jc_pending_coupon', $code)`.
   - Silently redirects via `wp_safe_redirect(remove_query_arg('apply_coupon'))` to keep visitor URLs clean.
4. **Auto-Apply on Cart / Add-to-Cart:**
   - Listens to `woocommerce_cart_loaded_from_session` and `woocommerce_add_to_cart`.
   - Calls `WC()->cart->apply_coupon($code)` automatically.
5. **Coming Soon Gating Bypass:**
   - Adds a filter to `woocommerce_coming_soon` returning `false` when `defined('REST_REQUEST') && REST_REQUEST` is true.
   - This ensures Justccell can create coupons via REST API even while the public frontend of Elite Terpenes is behind a coming-soon or maintenance gate.
6. **High-Performance Order Storage (HPOS):**
   - Declares compatibility with `custom_order_tables`.

---

## 3. Outbound Coupon Generation (Elite Terpenes → Justccell)

When a customer completes an order on `eliteterpenez.com`:
1. **Trigger:** `woocommerce_order_status_processing` or `woocommerce_order_status_completed`.
2. **Action Scheduler:** Queues `elite_jc_create_coupon` with the order ID.
3. **Payload Sent to `https://justccell.com/wp-json/wc/v3/coupons`:**
   ```json
   {
     "code": "ET-{order_id}",
     "discount_type": "percent",
     "amount": "0",
     "individual_use": true,
     "free_shipping": true,
     "date_expires": "{ISO8601 GMT + 48h}",
     "usage_limit": 1,
     "email_restrictions": ["{customer_billing_email}"]
   }
   ```
4. **Order Confirmation & Email:**
   - Displays the Just CCELL promotional card with code `ET-{order_id}` and magic link: `https://justccell.com/?apply_coupon=ET-{order_id}`.

---

## 4. Credentials & Options

| Location | Key / Constant | Details |
|---|---|---|
| **Elite Database** | `elite_jc_bridge_ck` | Auto-generated REST consumer key for Justccell |
| **Elite Database** | `elite_jc_bridge_cs` | Auto-generated REST consumer secret for Justccell |
| **Elite Database** | `elite_jc_shipping_seeded` | Flag indicating Free Shipping method has been configured |
| **Elite Theme** | `inc/justccell-cross-sell.php` | Outbound coupon dispatcher to Justccell |
| **Secrets Policy** | Never commit keys | Store in `wp-admin` or define in `wp-config.php` |

---

## 5. Merchant & Testing Checklist

1. **Verify Bridge Screen:** Navigate to `WooCommerce → Justccell bridge` in `wp-admin`. Confirm credentials appear.
2. **Verify Shipping:** Navigate to `WooCommerce → Settings → Shipping`. Confirm a Free Shipping method with "A valid free shipping coupon" exists.
3. **Verify REST Route:** Send a test POST from Justccell. Confirm HTTP 201 response.
4. **Test Magic Link:** Visit `https://eliteterpenez.com/?apply_coupon=JC-TEST`. Verify that `JC-TEST` persists in session and applies when an item is added to the cart.
