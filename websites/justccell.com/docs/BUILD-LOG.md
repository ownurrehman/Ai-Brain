# Build log

Append-only. Newest first. No passwords, API keys, or personal customer data.

Format: date, what shipped, what is next.

---

## 2026-08-16 — Full site clone pass (theme 0.5.5)

**Done**

- Header uses uploaded horizontal `Just-CCELL-logo-line.png` from Media Library; round PNG is the site icon. Stopped overwriting the logo with the old CCELL pack file.
- Front-end images only render from Media Library attachments (sideload from disk first). Plugin/theme folder URLs are no longer used as `<img>` sources.
- Wired all 22 catalog SKUs to `/{category}/{slug}/` clones. Tank keeps unique 360/highlights/details; other products use catalog photos until unique galleries exist in Media.
- Category clones at `/all-in-ones/` `/cartridge/` `/pod-system/` `/battery/` (ccell groups on All-In-Ones). Woo `/product-category/` redirects there.
- About, Why Justccell, Solution, Safety, Research, Manufacture, Contact cloned to ccell page shape. Coming soon **stayed on**.

**Next**

- QA logged in, hard-refresh homepage, category grids, a non-Tank product, About / Solution / Contact.

---

## 2026-08-16 — Tank layout closer to ccell (theme 0.5.4)

**Done**

- Audited live https://www.ccell.com/all-in-ones/tank against justccell Tank.
- Matched the product column (72.9%), taller hero type, no quote button in specs, highlight scroll at 70vh/slide, full-bleed EVOMAX panel, stacked detail mosaic, centered Explore cards (name + line + cyan capacity).
- Coming soon **stayed on**.

**Next**

- QA logged in, hard-refresh: https://justccell.com/uk/all-in-ones/tank/ vs https://www.ccell.com/all-in-ones/tank

---

## 2026-08-16 — Tank page actually renders (theme 0.5.3)

**Done**

- WordPress `extract()`s the pagination query var `page` into product templates. The clone was reading an empty `$page`, so the hero, title, specs, 360, and highlights never printed. Only Explore More (catalog, not `$page`) showed.
- Clone now loads Tank from `justccell_product` and never uses `$page`. First view copies Tank photos + 360 frames from the media pack into the Media Library (uploads), then serves those attachments.
- Slim deploy activated as `justccell-theme-XlizFji9`. Coming soon **stayed on**.

**Next**

- QA logged in, hard-refresh: https://justccell.com/uk/all-in-ones/tank/ vs https://www.ccell.com/all-in-ones/tank

---

## 2026-08-16 — Tank images + sticky scroll (theme 0.4.8)

**Done**

- Images were on the server but hidden: 36 stacked 360 frames at opacity 0, and the highlight block had no height so the scroll-pin never played.
- Tank now shows one 360 image (drag to spin), highlight images as full-bleed backgrounds, and a 550vh sticky scroll section like ccell.
- Route `/all-in-ones/tank/` even if permalinks have not flushed.
- Coming soon **stayed on**.

**Next**

- QA logged in: https://justccell.com/uk/all-in-ones/tank/ — drag the device, then keep scrolling through the five highlights.

---

## 2026-08-16 — Tank page scroll/360 match (theme 0.4.7)

**Done**

- Rebuilt Tank against the live ccell page: left specs + thumbnail strip, right **drag-to-spin 360**, then a **sticky full-viewport feature section** that changes as you scroll (same behaviour as `.high` on ccell).
- Coming soon **stayed on**.

**Next**

- QA logged in vs https://www.ccell.com/all-in-ones/tank
- Then Mini Tank / Eco Star or catalog grid.

---

## 2026-08-16 — Tank product page clone (theme 0.4.6)

**Done**

- Visual clone of ccell.com `/all-in-ones/tank` at `/{store}/all-in-ones/tank/` (prefix-safe). Inquiry-first: no prices, no cart.
- Homepage Tank card + Products mega-menu Tank link go to the product page. Quote CTA still goes to `/contact/?sku=tank`.
- Extra WCML currencies postponed (owner tired). Shop stays GBP default; theme still maps EUR/USD/CHF/AED by store URL when those currencies exist later. Do **not** switch WCML to “Site Language”.
- Coming soon **stayed on**.

**Next**

- QA Tank logged in, then clone Mini Tank / Eco Star or the All-In-Ones grid.
- When ready: WCML Client Location + add EUR, USD, CHF, AED.

---

## 2026-08-16 — Arabic + Russian kept (theme 0.4.5)

**Done**

- Owner confirmed AR + RU are intentional for additional customers, not wizard leftovers.
- Header selector now includes العربية and Русский. Theme lock no longer strips extra WPML languages.
- Store defaults unchanged (Dubai still defaults to English; Arabic is a switch).

**Next**

- Tank product page clone.

---

## 2026-08-16 — WPML/WCML wizard audit (theme 0.4.4)

**Done**

- Audited live WPML Languages + WCML Multi-currency after the wizards.
- URL format is **parameter** (`?lang=`, negotiation type 3). Browser redirect **Off**.
- Country prefixes still own `/es/` `/de/` `/uk/` `/us/` `/ae/` `/ch/` etc. Spain + English stays Spain (`/es/?lang=en`).
- WCML multi-currency is **independent** (not by language). Only **USD** is added as an extra WCML currency so far; store URL still sets GBP/EUR/CHF/AED in the theme.
- Coming soon **stayed on**. ACFML is on (fine with ACF Pro).
- Theme 0.4.4: WPML permalink switcher, currency filter wins over WCML, Hostinger autologin no longer blocked by coming soon.

**Fix remaining**

- WPML wizard also enabled **Arabic** and **Russian**. Uncheck them in WPML → Languages. Header already only lists EN/ES/FR/DE/IT.

**Next**

- Tank product page clone (logged in).
- Add GBP/EUR/CHF/AED in WCML when public prices exist — still never “currency by language”.

---

## 2026-08-14 — WPML activated in parameter mode (theme 0.4.2)

**Done**

- WPML Multilingual CMS 4.9.6, String Translation 3.5.3, WooCommerce Multilingual 5.5.7 **active**.
- Theme lock (`inc/wpml-lock.php`): `language_negotiation_type = 3` (`?lang=`), browser redirect Off, languages EN/ES/FR/DE/IT.
- `/es/` and `/de/` still set `jc_store` cookies (country stores). Coming soon **stayed on**.
- Left inactive: WPML Media, WPML Export/Import, ACFML.

**Next**

- If WPML still shows the setup wizard, pick **Language name as a parameter**. Never directories.
- WCML: currencies follow **store**, not language (Spain + English = EUR).
- Product page clone (Tank).

---

## 2026-08-14 — Coming soon back on; US / Dubai / CH / DE URLs booked (0.4.1)

**Done**

- Public gate: Minimal Coming Soon turned **back on**. Logged-in admins still see the real site. Plan in `docs/visibility.md`.
- Booked store prefixes before WPML: `/us` (USA, USD), `/ae` (Dubai/UAE, AED), `/ch` (Switzerland, CHF), `/de` (Germany, already). Aliases `/usa` → `/us`, `/dubai` and `/uae` → `/ae`.
- WPML still **not** activated.

**Next**

- Do not activate WPML until these store URLs are confirmed behind coming soon (log in to QA).
- Then WPML in parameter mode only.

---

## 2026-08-14 — Store prefixes live (theme 0.4.0). WPML still off.

**Done**

- `/{store}/` live: `/uk` `/es` `/de` `/fr` `/it` `/other`. Apex `/` 302s by Cloudflare country.
- Language stays `?lang=` on the same store (`/es/?lang=en` is still Spain / EUR).
- UK store reports GBP; EU stores EUR.
- Coming soon plugin was blocking the front end; deactivated so prefixes are testable.
- WCML 5.5.7 and OTGS Installer are on the server **inactive**. WPML core is not in the plugin list yet.

**WPML**

- Country URLs are ready. Do not run the WPML wizard unattended (must be parameter mode, not directories).

---

## 2026-08-14 — Translation plugin locked: WPML + WCML

**Done**

- Decision: WPML Multilingual CMS + WooCommerce Multilingual for languages/products/currencies.
- Geo/IP country stays a custom MU-plugin + Cloudflare, not a translation plugin.
- Recorded in `docs/translation-plugin.md`. Polylang is the only fallback. Weglot/TranslatePress/Multisite rejected.

**Next**

- Do not install WPML until `/{store}/` prefixes (or at least the MU-plugin) exist, so `/es/` is never registered as “Spanish language”.

---

## 2026-08-14 — Theme 0.3.0: language selector + B2B/B2C quote fields

**Done**

- Header language selector (EN / ES / FR / DE / IT) top-right. Persists `jc_lang` cookie via `?lang=`. Does **not** translate copy yet (no WPML / .po files). Store/currency still one default.
- Quote form: Consumer vs Business, VAT number required for B2B, delivery country label. Inquiry email includes account type + VAT.
- `inc/storefront.php` is the stub for later `/{store}/{lang}/` prefixes.
- Deployed to justccell.com (`hosting_deployWordpressTheme` activate true) and LiteSpeed purged.

**Next**

- Product page clone (Tank).
- Ownership transfer (Hostinger / Cloudflare / WP admin / email) to 3Devices.

---

## 2026-08-14 — Project docs + requirements locked into the plan

**Done**

- Created this documentation hub under `websites/justccell.com/docs/`.
- Recorded client sections 1/6, 3/6, 4/6, 5/6, 6/6. Noted **2/6 missing**.
- Locked architecture: one WP; `/{store}/{lang}/`; language ≠ country; 3devicescorp.com is an alias not a second shop; Spanish-entity VAT matrix; 3Devices ownership as P0.

**Next**

- Continue product-page clone (Tank) with prefix-safe URLs.
- Start ownership transfer checklist with 3Devices (Hostinger, Cloudflare, WP admin, email).
- Do not install WPML until store prefixes exist.

---

## 2026-08-14 — Homepage clone live on justccell.com

**Done**

- Custom theme `justccell-theme` 0.2.0 active (not Twenty Twenty-Five).
- Homepage clone of ccell.com: banners, category tabs, product rail, customize / fill / trusted / quote form.
- Inquiry-first Woo: prices and add-to-cart hidden in the theme.
- Seeded pages: contact, about, technology, safety, research, manufacture.
- Product categories: all-in-ones, cartridge, pod-system, battery.
- Self-hosted Montserrat; BEM + CSS variables; ACF JSON for flexible sections.
- 22 homepage products seeded as catalog data (reference images from ccell for **design approval only**).
- Cloudflare: Full Strict SSL, Always HTTPS, TLS 1.2+, HTTP/2+3, Brotli; Rocket Loader off.
- PHP 8.3.30, 512M, `expose_php` Off, session cookies secure/httponly, sodium.
- Plugins in play: WooCommerce 11.0.1, ACF Pro 6.8.7, AIOSEO 5.0.0.1 (kept), LiteSpeed Cache 7.9, UpdraftPlus, Hostinger Tools. Classic Editor deactivated. Wordfence installed inactive. CF7 inactive.
- Permalinks `/%postname%/`.

**Not done that day**

- Inner product templates, mega-menu completeness, footer pixel match.
- WebP, CF HTML cache rules, cart/checkout bypass (no real cart yet).
- Geo paths, languages, currencies, B2B/B2C VAT.
- 3devicescorp.com alias / email forward.
- Ownership inventory in 3Devices’ hands.

---

## 2026-08-14 — Hostinger WordPress + Cloudflare zone

**Done**

- justccell.com WordPress on Hostinger account `u392808260`, software id `30055979`.
- Document root `/home/u392808260/domains/justccell.com/public_html`.
- DB `u392808260_Jnr8B`.
- Cloudflare zone on, NS eugene/joyce.cloudflare.com, A apex → `187.124.156.180` proxied, www CNAME to apex.
- Theme deploys via Hostinger `hosting_deployWordpressTheme` (MCP cannot write `public_html` as a filesystem).

**Note**

- 3devicescorp.com WordPress id `30055771` created the same day — to be merged/redirected, not grown as a second site.

---

## 2026-08-16 — Rank Math replaces AIOSEO; WPML hreflang in sitemap

**Done**

- Rank Math SEO 1.0.276 active. AIOSEO removed from the install.
- WPML SEO 2.2.5 active (required for Rank Math sitemap hreflang).
- Locked decision: keep WPML’s default — hreflang **only in the Rank Math sitemap**, not in `<head>`. Do not add `WPML_SEO_ENABLE_SITEMAP_HREFLANG` false.
- Docs updated: architecture, translation-plugin, client-requirements, geo-language, roadmap C5, status.

**Not done**

- Public sitemap check: coming soon still returns HTML for `/sitemap_index.xml`.
- Product/Offer schema still off until prices are visible.
- Rank Math MCP Application Password not connected yet.
