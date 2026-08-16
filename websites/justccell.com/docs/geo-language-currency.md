# Geo, language, and currency

Client: one site; IP picks country store; language selector is independent; other domains use the same detection.

## Two cookies, two URL segments

| Axis | URL | Cookie | What it changes |
|---|---|---|---|
| Store | `uk` `us` `es` `de` `fr` `it` `ch` `ae` `other` | `jc_store` | Currency, default language, catalog availability if we ever split SKUs, tax *region hint* |
| Language | `en` `es` `fr` `de` `it` `ar` `ru` | `jc_lang` | Theme strings, product translations, Rank Math locale |

Checkout **delivery country** is still a WooCommerce address field. Store is the *default* commercial context from IP; the customer can ship elsewhere. VAT uses **delivery country + account type**, not only the URL store.

## First-visit flow

1. Read `jc_store` cookie. If valid, keep it (do not bounce returning users).
2. Else map Cloudflare `CF-IPCountry`:
   - `GB` → `uk`
   - `US` → `us`
   - `ES` → `es`
   - `DE` → `de`
   - `FR` → `fr`
   - `IT` → `it`
   - `CH` → `ch`
   - `AE` → `ae`
   - else → `other`
3. Default language from store table (UK/other → en, ES → es, …).
4. 302 to `/{store}/{lang}{original-path}` (preserve query string). Skip redirect for `/wp-admin`, `wp-cron`, REST, Woo AJAX, `xmlrpc.php`, static files.
5. Language selector only rewrites the `{lang}` segment. Store stays.

Manual store switch (optional later): a “Delivering to: Spain” control. Not in the first client brief; do not hide the language selector for it.

## Language selector (header, top-right)

Always visible on storefront:

English · Español · Français · Deutsch · Italiano · العربية · Русский

Arabic is RTL (WPML sets `dir="rtl"`). Dubai `/ae/` still defaults to English; visitors can switch to Arabic.

Switching language on `/es/es/product/tank` goes to `/es/en/product/tank`. Currency remains EUR.

## Currency

| Store | Currency |
|---|---|
| uk | GBP |
| us | USD |
| ch | CHF |
| ae | AED |
| es, de, fr, it, other | EUR |

Prices: WooCommerce **shop default is GBP** (United Kingdom). EUR / USD / CHF / AED are WCML extras by store. Spanish VAT entity is tax/invoices later — not this screen.

Inquiry-first phase can still show “Request a quote” without public prices (ccell pattern). When prices go live, they must already be store-aware.

## WCML currencies (set now, 2026-08-16)

GBP is a **currency**. The matching country is **United Kingdom**. Do not set default country to Spain.

Live shop today is still **USD** + **United States (California)** — change that first.

**1. WooCommerce shop default**

WooCommerce → **Settings** → **General** → Save:

- Country / location: **United Kingdom**
- Currency: **Pound sterling (GBP)** — not Euro, not USD

Germany still uses **EUR** (added in step 2). Spain’s legal entity is configured later under tax, not here.

**2. WCML extras (independent, not by language)**

WooCommerce → **WooCommerce Multilingual** → **Multi-currency**

- Keep **independent / by location**. Never “currency by language”.
- GBP is already the shop currency — do not add it again.
- Click **Add currency** for:

| Add | Store | WCML location |
|---|---|---|
| **EUR** | `/es/` `/de/` `/fr/` `/it/` `/other/` | Spain, Germany, France, Italy (Germany = Euro, not a separate currency) |
| **USD** | `/us/` | United States |
| **CHF** | `/ch/` | Switzerland |
| **AED** | `/ae/` | United Arab Emirates |

**3. Rates**

Automatic daily rates are fine as a placeholder.

**4. Leave off**

A header currency switcher. `/uk/` = GBP, `/de/` = EUR, `/ch/` = CHF. Language does not change money.

## SEO

- `hreflang` for the **seven languages** lives in the Rank Math XML sitemap (WPML SEO). Do not also print it in `<head>`.
- Canonical always on justccell.com (not 3devicescorp.com). Rank Math owns canonical; store prefixes stay in the theme.
- Do not index duplicate country URLs that only differ by IP bounce; crawlers should see explicit `/uk/en/` etc.
- Leave Product/Offer schema off until prices are visible on the page (inquiry-first).

## Geo accuracy

Cloudflare country is good enough for *defaulting*. Never use it as legal proof of tax residence. VAT uses the checkout address + VIES.

VPN users will land on the wrong store; the language selector and (later) store switcher fix that.

## Theme work required

- Header: language dropdown top-right (EN ES FR DE IT AR RU).
- Do not hardcode `justccell.com` in hrefs.
- Banner/product “More” buttons: `home_url('/contact/?sku=')` until product permalinks exist; then `get_permalink()`.
- Footer/legal: show store + language so QA can see `/de/en/` vs `/de/de/`.

## Plugins vs custom

Custom **must-use plugin** `justccell-storefront` for:

- Rewrite tags `jc_store`, `jc_lang`
- Geo redirect
- Cookie set
- Woo `woocommerce_currency` filter
- `body_class` for store/lang

WPML (or Polylang) for string/product translation only, with language in the second URL segment (custom language URL mode), **not** the first.
