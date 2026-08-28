# Geo, language, and currency

Client: one site. **justccell.com with no prefix is the UK order site.** IP only sends Spain and Switzerland to a country prefix. Everyone else (Pakistan, US, UAE, …) stays on the UK site.

## Two cookies, two axes

| Axis | URL | Cookie | What it changes |
|---|---|---|---|
| Store | *(none)* = UK · `es` · `ch` | `jc_store` | Currency, landing vs catalogue |
| Language | whatever is active in **WPML** | WPML cookie | Translations. Not a custom theme switcher. |

Checkout **delivery country** is still a WooCommerce address field. Store is the *default* commercial context from IP; the customer can ship elsewhere. VAT uses **delivery country + account type**, not only the URL store.

## First-visit flow

1. If the URL is already `/es/` or `/ch/` (or `/spain/`, `/swiss/`, `/switzerland/`), keep that store.
2. If the URL is an old prefix (`/uk/`, `/other/`, `/others/`, `/us/`, `/de/`, `/fr/`, `/it/`, `/ae/`, …), **301** to the same path with **no** country prefix (UK).
3. If the URL has **no** country prefix:
   - No store cookie yet + Cloudflare country `ES` → 302 to `/es/`.
   - No store cookie yet + Cloudflare country `CH` → 302 to `/ch/`.
   - Otherwise stay on **justccell.com** (UK). Pakistan, UK, US, etc. all stay here.
4. Opening bare `justccell.com` is always the UK catalogue (cookie becomes `uk`). Spain/Switzerland landings are `/es/` and `/ch/` only.
5. **Language is WPML.** Do not add a theme language dropdown. WPML URL format stays **Language name as a parameter** (`?lang=`) so `/es/` remains Spain, not Spanish.

Skip redirect for `/wp-admin`, `wp-cron`, REST, Woo AJAX, `xmlrpc.php`, static files.

## Languages — WPML only (how to keep UK / Spain / Swiss)

Do this in wp-admin. The theme must not add or remove WPML languages.

1. **WPML → Languages**
2. Default language: **English**
3. Uncheck languages you do not want yet (Italian, Arabic, Russian, …). Keep **English**, **Spanish**, and Swiss ones you need (**German**, **French**).
4. URL format: **Language name as a parameter** (locked). Do **not** switch to directories.
5. **Browser language redirect: Off** (locked). Country is IP + `/es/` `/ch/`, not the browser language.
6. Language switcher: enable WPML’s own switcher later if you want it in the header. Do not build a custom one.

To add a language later, turn it back on in the same WPML screen and translate with WPML/WCML as usual.

## Currency

| Store | URL | Currency |
|---|---|---|
| uk | justccell.com (no prefix) | GBP |
| es | `/es/` | EUR |
| ch | `/ch/` | CHF |

Prices: WooCommerce **shop default is GBP** (United Kingdom).

Inquiry-first phase can still show “Request a quote” without public prices. When prices go live, they must already be store-aware.

## WCML currencies (set now, 2026-08-16)

GBP is a **currency**. The matching country is **United Kingdom**. Do not set default country to Spain.

**1. WooCommerce shop default**

WooCommerce → **Settings** → **General** → Save:

- Country / location: **United Kingdom**
- Currency: **Pound sterling (GBP)** — not Euro, not USD

**2. WCML extras (independent, not by language)**

WooCommerce → **WooCommerce Multilingual** → **Multi-currency**

- Keep **independent / by location**. Never “currency by language”.
- GBP is already the shop currency — do not add it again.
- Click **Add currency** for:

| Add | Store |
|---|---|
| **EUR** | `/es/` |
| **CHF** | `/ch/` |

USD / AED wait until those stores exist again.

**3. Rates**

Automatic daily rates are fine as a placeholder.

**4. Leave off**

A header currency switcher. justccell.com = GBP, `/es/` = EUR, `/ch/` = CHF. Language does not change money.

## SEO

- `hreflang` for **active** languages lives in the Rank Math XML sitemap (WPML SEO). Do not also print it in `<head>`.
- Canonical always on justccell.com. Rank Math owns canonical.
- After this change, `/uk/` and `/other/` **301** to the unprefixed URL so Google consolidates on justccell.com.
- Leave Product/Offer schema off until prices are visible on the page (inquiry-first).

## Geo accuracy

Cloudflare country is good enough for *defaulting*. Never use it as legal proof of tax residence. VAT uses the checkout address + VIES.

VPN users will land on the wrong store; `/es/` and `/ch/` URLs still work if they type them.

## How to add or remove countries / languages later

**Countries (stores)** — theme: `justccell_stores()` in `inc/storefront.php`. Set `'prefix' => true` for a landing like Spain, `false` for UK-style (no extra URL). Map ISO codes in `justccell_store_from_country()`.

**Languages** — **WPML only.** WPML → Languages. Uncheck what you do not need. Default **English**. URL format **parameter**. Browser redirect **Off**. Do not add a theme language menu.

Do not turn on WPML directory URLs. `/es/` must remain Spain, not “Spanish”.

## Theme work required

- Do not hardcode `https://justccell.com/uk/` in hrefs. UK links are `home_url()` with no prefix.
- Banner/product “More” buttons: `home_url('/contact/?sku=')` until product permalinks exist; then `get_permalink()`.

## Plugins vs custom

Theme `inc/storefront.php` owns:

- Geo redirect (Spain/Switzerland only)
- Cookie set
- Woo `woocommerce_currency` filter
- `body_class` for store/lang
- 301 of retired prefixes (`/uk/`, `/other/`, …)

WPML is string/product translation only, with language as `?lang=`, **not** the first path segment.
