> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/justccell.com/docs/elite-cross-sell|Justccell contract]]

# Cross-site 48-hour free delivery — Elite Terpenes side

Sister store: [[websites/justccell.com/INDEX|justccell.com]] (hardware). This site: [eliteterpenez.com](https://eliteterpenez.com/) (terpenes).

**Shipped 2026-09-04:** Justccell → Elite only. An order that reaches **processing** or **completed** on Justccell creates a one-use free-delivery coupon **on this store** via WooCommerce REST. The reverse (Elite order → Justccell coupon `ET-{order_id}`) is **not built yet**.

Justccell-side contract (hooks, settings, thank-you card): [[websites/justccell.com/docs/elite-cross-sell|elite-cross-sell.md]].

---

## 1. Live facts (do not guess)

| Item | Value |
|---|---|
| Hostinger username | `u984013785` (shared client account — **not** Justccell `u392808260`) |
| WordPress software id | `30437919` |
| Public URL | https://eliteterpenez.com/ |
| WooCommerce | 11.1.0, active |
| Bridge plugin | **`justccell-coupon-bridge`** — regular plugin, **active** |
| Live path | `wp-content/plugins/justccell-coupon-bridge/justccell-coupon-bridge.php` |
| Vault PHP | `websites/eliteterpenez.com/bridge/justccell-coupon-bridge.php` (mirror: `websites/justccell.com/sister-sites/eliteterpenez/`) |
| Admin screen | **WooCommerce → Justccell bridge** |
| REST endpoint Justccell calls | `POST https://eliteterpenez.com/wp-json/wc/v3/coupons` |
| Public storefront | Coming soon for anonymous visitors. Authenticated REST coupon create **works**. Magic-link shop UX is incomplete until the shop is public. |

Hostinger `hosting_generateUploadURLV1` returned 404 for this shared account. Plugin was installed via wp-admin zip upload, not TUS into `mu-plugins`. Prefer plugin zip / plugin editor for updates unless TUS is restored.

---

## 2. What Justccell sends

Auth: HTTPS Basic (WooCommerce REST consumer key + secret generated on this site). Timeout on Justccell: **4 seconds**. Action Scheduler first so Justccell checkout never waits.

Payload:

```json
{
  "code": "JC-{order_id}",
  "discount_type": "percent",
  "amount": "0",
  "individual_use": true,
  "free_shipping": true,
  "date_expires": "{ISO8601 GMT + 48h}",
  "usage_limit": 1,
  "email_restrictions": ["{Justccell billing email}"]
}
```

Verified 2026-09-04: POST HTTP 201, then force-delete of a ping coupon. Justccell **Save and test connection** succeeded.

Never commit the consumer key/secret. Rotate in **WooCommerce → Settings → Advanced → REST API** and paste the new pair into **Justccell → Elite Cross-sell**.

---

## 3. Plugin behaviour (`justccell-coupon-bridge`)

1. Declares WooCommerce HPOS compatibility (`custom_order_tables`).
2. Enables Woo coupons if they were off.
3. On `wp_loaded`: reads `?apply_coupon=`, sanitizes, stores `elite_jc_pending_coupon` on the Woo session, applies if the cart exists, `wp_safe_redirect` without the query arg.
4. Re-applies on `woocommerce_cart_loaded_from_session` and `woocommerce_add_to_cart`.
5. First shop-manager admin load: creates a Read/Write REST key (options `elite_jc_bridge_ck` / `elite_jc_bridge_cs` / `elite_jc_bridge_key_id`) and shows it on **WooCommerce → Justccell bridge**.
6. Seeds Zone 0 (locations not covered) **Free shipping** with `requires = coupon` once (`elite_jc_shipping_seeded`).
7. `woocommerce_coming_soon` returns false during `REST_REQUEST` so coupon POSTs are not blocked by coming soon.

Magic link format: `https://eliteterpenez.com/?apply_coupon=JC-{order_id}`

---

## 4. Shipping requirement

The coupon is 0% + `free_shipping`. WooCommerce only zeroes delivery if a **Free shipping** method exists with “A valid free shipping coupon” (or `either` / `both`). The plugin seeds this on first admin visit. Do not delete that method without replacing it.

---

## 5. Owner clicks (Elite)

1. **WooCommerce → Justccell bridge** — confirm key pair exists (shown to `manage_woocommerce`).
2. **WooCommerce → Settings → Shipping** — keep coupon-required Free shipping.
3. If keys leak: create a new REST key, update Justccell → Elite Cross-sell, delete the old key.
4. When the shop is public, test `/?apply_coupon=JC-…` then add a product → free delivery at checkout, email must match the Justccell order.

---

## 6. Not shipped (do not invent)

- Elite order → Justccell coupon (`ET-{order_id}`).
- Shared warehouse / linked packing slips.
- Token-verify REST (`cross-store/v1/verify-token`) from the old Justccell build-plan sketch — **replaced** by native coupon REST.

---

## 7. Files agents must not lose

| Role | Path |
|---|---|
| Elite live plugin | `wp-content/plugins/justccell-coupon-bridge/` |
| Elite vault PHP | `websites/eliteterpenez.com/bridge/justccell-coupon-bridge.php` |
| Justccell theme worker | `websites/justccell.com/justccell-theme/inc/elite-cross-sell.php` |
| Justccell settings | wp-admin **Justccell → Elite Cross-sell** (`justccell_elite_cross_sell` option) |
| Justccell docs | [[websites/justccell.com/docs/elite-cross-sell|elite-cross-sell.md]] · [[websites/justccell.com/rules|rules.md]] §7.9 |
| Elite rules | [[websites/eliteterpenez.com/rules|rules.md]] §0.7 and §5 |
