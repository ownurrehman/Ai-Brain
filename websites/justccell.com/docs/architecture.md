# Architecture

One WordPress + WooCommerce install. One catalog. One customer database. Multiple **storefronts** (country) and **UI languages**.

```
Visitor
  │
  ├─ Domain
  │    justccell.com          (canonical commercial)
  │    3devicescorp.com       (301 / alias → same WP, same cookies)
  │
  ├─ Store (from IP, then cookie / URL)
  │    uk | us | es | de | fr | it | ch | ae | other
  │
  └─ Language (default from store, then selector)
       en | es | fr | de | it | ar | ru
```

## What is one “store”

A store is a **commercial context**, not a separate website.

| Store slug | Default lang | Currency | Typical tax lens |
|---|---|---|---|
| `uk` | en | GBP | UK |
| `us` | en | USD | USA (`/usa` 301 → `/us`) |
| `es` | es | EUR | Spain |
| `de` | de | EUR | Germany |
| `fr` | fr | EUR | France |
| `it` | it | EUR | Italy |
| `ch` | en | CHF | Switzerland |
| `ae` | en | AED | UAE / Dubai (`/dubai` and `/uae` 301 → `/ae`) |
| `other` | en | EUR | Rest of world |

Language can change without changing store. Currency follows store, not language.

## URL contract (do not break this)

```
/{store}/{lang}/{wp-path}
```

Examples:

- `/es/es/` — Spain, Spanish
- `/es/en/` — Spain, English, still EUR
- `/uk/en/product/tank`
- `/other/de/` — rest of world, German UI, EUR

WordPress rewrite must own `{store}` and `{lang}` as prefixes, not as pages named `uk`.

Until this rewrite ships, **internal links in the theme must be relative** (`home_url()`, not hardcoded `https://justccell.com/contact/`) so prefixes can wrap later.

## Stack (locked unless 3Devices objects)

| Layer | Choice | Why |
|---|---|---|
| CMS / shop | WordPress 7.x + WooCommerce | Client already on Hostinger WP; catalog + tax plugins exist |
| Theme | `justccell-theme` (this folder) | Performance, clone control, no page-builder lock-in |
| Fields | ACF Pro | Already licensed on the install |
| SEO | **Rank Math SEO Free** + **WPML SEO** | Free Woo schema + agent REST/MCP. Hreflang stays in the Rank Math sitemap (not `<head>`). See [translation-plugin.md](translation-plugin.md). |
| Languages | **WPML Multilingual CMS + WooCommerce Multilingual** (locked). See [translation-plugin.md](translation-plugin.md). Store prefixes stay custom; WPML must not own `/es/` as “Spanish”. |
| Geo | Cloudflare `CF-IPCountry` first (already proxied). WooCommerce MaxMind as fallback | No extra geo API cost at the edge |
| Currency | WooCommerce Multi-currency via WPML WCML, or dedicated plugin (Aelia / CURCY) with two currencies only: GBP + EUR | Client listed only GBP and EUR |
| VAT / VIES | WooCommerce Tax + EU VAT Number (VIES) + OSS-capable tax plugin | Matches Spanish-entity matrix |
| Cache | LiteSpeed Cache + Memcached (Redis PHP ext is **not** on this Hostinger plan) | Already live |
| CDN / WAF | Cloudflare (zone already on) | SSL, geo header, later WAF |
| Payments | Gateway that never stores PAN on our server (Stripe / similar). Inquiry-first until B2B checkout is ready | PCI scope |
| Backups | UpdraftPlus → 3Devices-owned offsite (Drive/S3), not only Hostinger | Ownership + 6/6 |

## What we will not do

- Six WordPress installs or six WooCommerce shops
- WPML language-in-directory as the country code (`/es/` = language only)
- Currency following the language switcher
- A second live WordPress on 3devicescorp.com
- Elementor / Divi
- Storing card data in WordPress

## Hostinger / Cloudflare facts (2026-08-14)

| | |
|---|---|
| Hostinger user | `u392808260` |
| justccell WP id | `30055979` |
| 3devicescorp WP id | `30055771` (retire as independent site) |
| justccelldevices.com | also on this account — confirm with client if it is in-scope |
| Document root | `/home/u392808260/domains/justccell.com/public_html` |
| PHP | 8.3.30, memory 512M, OPcache, sodium, `expose_php` Off |
| DB | `u392808260_Jnr8B` |
| Origin IP | `187.124.156.180` (Cloudflare A, proxied) |
| Theme deploy | Overwrite `wp-content/themes/justccell-theme/` only. `hosting_deployWordpressTheme` with `activate: false`. Never hashed extra copies. |

## Implementation order (so requirements are not bolted on at the end)

1. **Ownership transfer** of Hostinger, Cloudflare, registrar, email, backups to 3Devices (can run in parallel with design clone).
2. **URL + geo MU-plugin** (store + lang prefixes, IP detect, language cookie, currency). Ship behind a feature flag if needed.
3. **B2B / B2C registration + VIES + tax classes**.
4. **3devicescorp.com alias** + email forwards.
5. Visual clone of remaining templates (product, about, tech, footer).
6. Checkout / payments once tax matrix is testable.
7. Security hardening (Wordfence/WAF, 2FA, cache bypass for account/checkout).

Design clone can continue in step 5 as long as links stay prefix-safe.
