> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/eliteterpenez.com/docs/cross-site-free-delivery|Elite-side spec]]

# Elite Terpenes cross-sell — free delivery coupons

Live: theme **0.9.219** on [justccell.com](https://justccell.com/). Remote store: [eliteterpenez.com](https://eliteterpenez.com/).

After a Justccell WooCommerce order reaches **processing** or **completed**, Justccell creates a one-use free-delivery coupon on Elite Terpenes over the WooCommerce REST API. The customer sees the code on the order-received page and in the processing email, plus a magic link that applies it on Elite.

## Why this design

Checkout on Justccell must not wait on Elite. The worker is WooCommerce **Action Scheduler** (`justccell_elite_create_coupon`). Any inline fallback (thank-you page or customer email) uses `wp_remote_request` with a **4 second** timeout. Failures are swallowed, stored on the order as `_elite_cross_sell_last_error`, and never change the Justccell order status.

## Constants / credentials

**Do not put keys in git.** Prefer the wp-admin screen. Optional `wp-config.php` overrides win over saved options:

| Constant | Purpose |
|---|---|
| `JUSTCCELL_ELITE_API_URL` | Remote origin, e.g. `https://eliteterpenez.com` |
| `JUSTCCELL_ELITE_STORE_URL` | Customer magic-link origin (usually the same) |
| `JUSTCCELL_ELITE_CONSUMER_KEY` | WooCommerce REST consumer key (`ck_…`) |
| `JUSTCCELL_ELITE_CONSUMER_SECRET` | WooCommerce REST consumer secret (`cs_…`) |

**Justccell wp-admin:** **Justccell → Elite Cross-sell** (`manage_woocommerce`). Fields: enable, API URL, storefront URL, consumer key, consumer secret, plus all visitor-facing card copy (kicker, heading, body, code label, button text, copy-button text). **Save and test connection** GETs `/wp-json/wc/v3/coupons?per_page=1`.

**Elite wp-admin:** **WooCommerce → Justccell bridge**. Shows the generated Read/Write key pair. Plugin slug `justccell-coupon-bridge` (Hostinger user `u984013785`, WP software `30437919`). Live path: `wp-content/plugins/justccell-coupon-bridge/` (regular plugin, **not** mu-plugin). Vault copies: `websites/eliteterpenez.com/bridge/justccell-coupon-bridge.php` and `websites/justccell.com/sister-sites/eliteterpenez/`. Elite TUS `generateUploadURL` returned 404 on that shared account — plugin shipped via wp-admin zip.

Option name on Justccell: `justccell_elite_cross_sell`.

## WooCommerce checkout hook (Justccell)

Theme file: `justccell-theme/inc/elite-cross-sell.php` (loaded from `functions.php`).

| Hook | Role |
|---|---|
| `woocommerce_order_status_processing` | Queue coupon job |
| `woocommerce_order_status_completed` | Queue coupon job |
| `woocommerce_payment_complete` | Queue coupon job |
| Action Scheduler `justccell_elite_create_coupon` | POST remote coupon |
| `woocommerce_thankyou` + `thankyou.php` | 4s fallback create + promo card |
| `woocommerce_email_before_order_table` | Same card in customer emails |
| `woocommerce_admin_order_data_after_billing_address` | Merchant sees code / failure |

Skipped statuses: failed, cancelled, refunded, checkout-draft. Missing billing email → meta error `missing_billing_email`, no remote call.

Order meta (HPOS CRUD via `WC_Order::update_meta_data`):

| Meta | Meaning |
|---|---|
| `_elite_cross_sell_coupon` | Code, e.g. `JC-329794` |
| `_elite_cross_sell_expires` | Unix expiry (48 hours from create) |
| `_elite_cross_sell_last_error` | `remote_failed` / `exception` / `missing_billing_email` |
| `_elite_cross_sell_lock` | Unix lock to prevent double POST (30s) |

## REST payload (Elite `POST /wp-json/wc/v3/coupons`)

Auth: HTTPS Basic (`Consumer Key` : `Consumer Secret`). If the host strips `Authorization`, the theme retries with query credentials.

```json
{
  "code": "JC-{order_id}",
  "discount_type": "percent",
  "amount": "0",
  "individual_use": true,
  "free_shipping": true,
  "date_expires": "{ISO8601 GMT + 48h}",
  "usage_limit": 1,
  "email_restrictions": ["{billing email}"]
}
```

Verified live 2026-09-04: POST returned HTTP 201 (`free_shipping` true, `usage_limit` 1, email restriction set). Duplicate codes are treated as success (already exists).

## Frontend (Justccell)

- Card: white background, 1px `` `#e5e7eb` `` border, primary CTA (`var(--jc-color-primary)`).
- Copy is **Justccell → Elite Cross-sell**, not hardcoded in the template.
- Magic link: `https://eliteterpenez.com/?apply_coupon={coupon_code}`.

## Elite plugin behaviour

`justccell-coupon-bridge`:

1. Reads `apply_coupon`, stores it on the Woo session, redirects without the query arg.
2. Applies the coupon when the cart loads / an item is added.
3. Enables Woo coupons if they were off.
4. Seeds **Locations not covered** Free shipping with “requires a valid free shipping coupon” once.
5. Lets WooCommerce REST through while coming soon is on (`woocommerce_coming_soon` false for `REST_REQUEST`).
6. HPOS compatible (`custom_order_tables`).

Elite’s public homepage can still show Hostinger/WordPress coming soon. The REST coupon API is reachable authenticated. The magic link will only feel complete once Elite’s shop is public.

## Owner clicks

1. **Justccell → Elite Cross-sell** — confirm Enable is on; use **Save and test connection** after rotating keys.
2. **Elite → WooCommerce → Settings → Shipping** — keep a Free shipping method that allows coupons (seeded on first admin visit).
3. Place a test Justccell order (logged-in; coming soon still hides the storefront from guests) → thank-you card + order meta `_elite_cross_sell_coupon`.
4. Open the magic link on Elite while logged into that site if coming soon is still on.

## Files

| Site | Path |
|---|---|
| Justccell theme | `inc/elite-cross-sell.php`, `woocommerce/checkout/thankyou.php`, `assets/css/commerce.css`, `inc/admin-menu.php` |
| Elite plugin | `wp-content/plugins/justccell-coupon-bridge/justccell-coupon-bridge.php` |
| Elite vault PHP | `websites/eliteterpenez.com/bridge/justccell-coupon-bridge.php` |
| Justccell vault copy | `websites/justccell.com/sister-sites/eliteterpenez/` |
| Elite SSOT | [[websites/eliteterpenez.com/docs/cross-site-free-delivery|cross-site-free-delivery.md]] |
