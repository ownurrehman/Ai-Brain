# JUSTCCELL — WooCommerce Build Plan (v1, awaiting Sheikh approval before Phase A)

**Site:** https://justccell.com | **WP software id:** 30055979 | **PHP 8.3**
**Owner:** 3Devices LTD | **Authorized CCELL dealer**
**Model:** B2C + B2B hybrid. Prices public. Single + bulk orders. Global shipping with UK/EU focus.
**Sister sites planned:** elite terpenes site + packaging site → cross-store shipping perk + shared fulfillment.

---

## PHASE A — Core WooCommerce configuration (code via REST)

| Item | Setting |
|---|---|
| Store address | UK (exact street to be supplied by Sheikh — flag in report) |
| Currency | GBP (£), position left, thousand sep ",", decimal ".", 2 decimals |
| Selling location | Sell to all countries, restrict shipping by zone |
| Tax | Enabled, prices entered EX VAT, UK 20% standard rate, display inc VAT on shop, show "incl. VAT" |
| Stock management | ON — manage per product, low-stock alert at 5 |
| Cart behaviour | Redirect to cart on add = NO, AJAX add = YES |
| Checkout | Company field = optional (B2B), phone = required, order notes = optional |
| Guest checkout | YES (reduces friction; account optional at checkout) |
| Customer accounts | Allow creation at checkout + my-account; no forced registration to browse |
| Emails | Order confirmation (customer), new order (admin 3Devicesltd@gmail.com), processing, completed, refund; branded footer "3Devices LTD — Authorized CCELL Dealer" |
| Coupons | Enabled (for cross-site free shipping logic later) |
| REST keys | Generate read/write key for automation + cross-site sync |

## PHASE B — Product data completion (47 products)

Already done: images, ACF fields, categories, descriptions structure.
Remaining:
- [ ] Set regular_price + sale_price (Sheikh supplies price list — will chase if missing)
- [ ] SKU per product ({slug}-jc-01 format)
- [ ] Stock quantities + low-stock threshold (per product)
- [ ] Weight/dimensions (needed for shipping rates)
- [ ] Product categories hierarchy: All-In-Ones / Cartridges / Pod Systems / Batteries / Accessories
- [ ] Bulk-edit tool: any product missing price/weight = flagged in report
- [ ] Attributes (capacity, battery mAh, coil type) for filters

## PHASE C — Payment gateways (stubs + client connects keys)

Client adds credentials. We prepare everything else:
1. **Stripe** — install official gateway, create webhook endpoint placeholder, configure currency GBP, payment request buttons (Apple/Google Pay). Client adds sk_live/pk_live.
2. **PayPal** — install official gateway, sandbox mode toggle ready, client connects business account.
3. **USDT (crypto)** — install a USDT/TRC-20 gateway (e.g. "Cryptocurrency Payment Gateway" or CoolPay), client supplies wallet address + API keys. We configure order status flow: pending → paid confirmation via webhook.
4. Order status mapping: all 3 gateways → auto "processing" on payment success; failed → "failed"; refunds handled in Woo admin.

## PHASE D — Taxes + Shipping

**Tax (UK VAT):**
- Standard rate 20% on all products (vape hardware = standard rate)
- Tax class: default "Standard"; EU customers: IOSS-ready if needed later
- Price display: "incl. VAT" suffix on shop/checkout

**Shipping zones:**
| Zone | Countries | Methods |
|---|---|---|
| UK Mainland | GB | Flat rate £4.99 (single) / free over £100 / bulk weight-based |
| EU | EU countries | Flat rate £12.95 / free over £250 |
| International | Rest of world | Flat rate £24.95 (quote at checkout) |

- Bulk/bulk-quantity orders: weight-based shipping calculation (per product weight)
- Free shipping threshold configurable
- Local pickup option (off by default, can enable later)

## PHASE E — Sandbox end-to-end testing (before going live)

Test matrix (all must pass before marking complete):
- [ ] Single product order: add to cart → checkout → place order → order received email
- [ ] Bulk order (10+ units): quantity updates, stock decrements correctly
- [ ] Tax: 20% VAT calculated and shown correctly on checkout + order emails
- [ ] Shipping: UK flat rate applies, free-shipping threshold triggers
- [ ] Payment gateways in TEST MODE: Stripe test card 4242…, PayPal sandbox, USDT test webhook
- [ ] Order confirmation emails: customer + admin receive correct branded emails
- [ ] Stock management: order reduces stock, out-of-stock blocks purchase
- [ ] Guest checkout works (no login required)
- [ ] Account creation at checkout works
- [ ] Order refund test (full refund via Stripe test mode)
- [ ] Mobile responsive checkout
- [ ] LiteSpeed cache: cart/checkout pages excluded from caching (critical - verify)
- [ ] ACF product pages still render after all changes (regression check)

## PHASE F — Cross-site shipping sync (justccell ↔ sister sites)

**Goal:** customer pays shipping once; sister site checkout recognizes the linked order and applies free shipping; warehouse links both orders for bundled fulfillment.

### Design (code-only, no paid plugins)

**Option chosen: Shared token + REST verification API**

1. **Token generation (Site A = justccell.com):**
   After order payment, generate a secure one-time token: hash of (order_id + email + site_secret). Store on the order as meta.
2. **Site A → customer:** after checkout, show banner: "You qualify for FREE shipping on your sister-site order → https://eliteterpenes.com/shop?freetoken={token}"
3. **Site B (sister site):**
   - Reads `freetoken` from URL, stores in session + cart object
   - On checkout, calls Site A REST endpoint: `GET /wp-json/rankray-newsletter/v1/verify-token?token={token}` (or a new dedicated namespace e.g. `cross-store/v1/verify-token`)
   - Site A responds: `{valid: true, email: "buyer@x.com", order_id: 123, expires: "...", used: false}`
   - If valid AND the Site B order email matches the Site A order email → apply 100% free shipping automatically + mark token `used: true` (single-use, abuse prevention)
   - Bi-directional: Site B has the same token system pointing back at Site A
4. **Fulfillment link:**
   - Both orders store `cross_site_token` + `cross_site_order_id` in meta
   - Admin order page shows "Linked order: #XXXX on eliteterpenes.com" link
   - Warehouse gets a combined packing slip via a shared "fulfillment bundle" meta on both orders

### Abuse prevention
- Token = HMAC(order_id + email + secret), single-use, expires in 7 days
- Tied to the buyer's email at Site B checkout (email must match the token's email)
- Used-tokens cannot be reused

### Implementation steps
- [ ] Site A (justccell.com): REST endpoint `cross-store/v1/issue-token` (creates token after paid order) + `cross-store/v1/verify-token` (validates + marks used)
- [ ] Site B: same two endpoints (roles reversed)
- [ ] Both sites: banner/shortcode after checkout ("Free shipping on your next order at [sister site]" with token link)
- [ ] Both sites: checkout hook reads token from session/URL, calls the OTHER site's verify endpoint, applies free shipping (Woo shipping method override) + stores cross-ref meta
- [ ] Order emails mention the cross-store discount applied

### Sandbox test (cross-site)
- [ ] Place paid order on Site A → receive token → visit Site B → token validated → free shipping applied → order placed → both orders show linked meta
- [ ] Attempt token reuse → rejected
- [ ] Wrong email at Site B checkout → token rejected

---

## What Sheikh needs to provide
1. Product prices (regular + sale) for all 47 products — or a price list file
2. Product weights/dimensions (or approve estimated ones for shipping calc)
3. Company address + company number for legal pages footer
4. Confirmation of UK VAT registration (20% VAT on all products?)
5. Any shipping exclusions (countries not served)
6. Payment gateway accounts (Stripe + PayPal business + USDT wallet/gateway choice) — he connects keys himself

## What client does after we finish
1. Connect Stripe live keys (API + webhooks)
2. Connect PayPal Business credentials
3. Connect USDT gateway keys
4. Confirm VAT settings (we set defaults; he validates)
5. Review first live orders

## What we do NOT do
- Payment gateway credential connection (client's keys)
- Legal/tax advice (we implement UK standard; Sheikh confirms with accountant)
- Ship actual products / warehouse logic