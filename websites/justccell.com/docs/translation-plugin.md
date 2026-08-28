> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Translation plugin decision

Locked: 2026-08-14

## Recommendation

**WPML Multilingual CMS + WooCommerce Multilingual (WCML).**

Do **not** buy a “geo translation” plugin. IP → country store is **our** MU-plugin + Cloudflare `CF-IPCountry`. The translation plugin only switches **language**. Mixing those two jobs is how `/es` becomes “Spanish” instead of “Spain store”.

## Why WPML (not Polylang, TranslatePress, or Weglot)

This site is WooCommerce, **WPML for every language**, products/attributes later, multi-currency by **store**, Rank Math, and 3Devices must **own** the translations.

| Plugin | Verdict for this project |
|---|---|
| **WPML + WCML** | **Use this.** Deepest Woo catalog + checkout translation. WCML can do two currencies. Strings, products, taxonomies, translation jobs. Data stays in WordPress. |
| Polylang Pro + Polylang for WooCommerce | Lighter, slightly easier custom rewrites. Woo add-on is extra; multi-currency is a third plugin. Weaker translation-management workflow. **Fallback** only if WPML rewrite fights the store prefix too hard. |
| TranslatePress | Nice visual editor for a brochure clone. Weaker on Woo AJAX/checkout and large catalogs. Still wants to own a language directory. Skip. |
| Weglot / GTranslate | Fast, but translations live in a vendor cloud and price by word count. Conflicts with **full ownership (5/6)**. Skip. |
| MultilingualPress | One site per language (multisite). Conflicts with **one platform**. Skip. |

## How it fits country URLs

WPML’s default “language in directories” would make `/es/` mean **Spanish**. The client’s `/es/` means **Spain store** (EUR), where the visitor can still pick English.

So we configure WPML as **language only**, never as country:

| Axis | Owner | Example |
|---|---|---|
| Store (`uk` on bare domain, `es`, `ch`) | Custom storefront + Cloudflare | justccell.com = UK; `/es/` = Spain; `/ch/` = Switzerland |
| Language (whatever you enable in WPML) | WPML | `/es/?lang=en` still Spain / EUR |

**Reserved first-path segments (do not give these to WPML as language folders):**  
`es` `ch` `spain` `swiss` `switzerland` plus retired prefixes that 301 to UK (`uk` `other` `others` `us` `de` `fr` `it` `ae` …)

`de` is German-the-language (`?lang=de`). It is **not** a country folder anymore. WPML **must** use `?lang=` and never directories.

**Phase 2 (SEO):** `/{store}/{lang}/` once rewrites are proven. WPML still does not own `/es/` as language.

**Never enable**

- WPML “redirect visitors by browser language” (wrong signal; client wants IP country, then manual language)
- WCML “currency follows language” (Spain + English must stay **EUR**)

Currency follows **store**. Language follows the selector.

## License to buy

- **WPML Multilingual CMS** (not the Blog plan — CMS is the WooCommerce tier)
- **WooCommerce Multilingual** (included / free companion)
- Optional: WPML automatic translation credits (DeepL) as **drafts**, then human edit for product/legal copy

Account must be **3Devices-owned**, not a developer OnTheGoSystems login.

## Who does what (updated 2026-08-14, after activate)

Country prefixes are live. WPML CMS + String Translation + WCML are **active**. Theme `0.4.2` forces parameter URLs.

**Locked in code** (`inc/wpml-lock.php`) — only so `/es/` cannot become “Spanish”:

- Language URL format stays **parameter** (`?lang=`). Do not switch WPML to directories.
- Browser language redirect stays **Off**.

Languages themselves are **not** coded. You add/remove them in WPML.

## Owner: do this in wp-admin (not theme PHP)

### WPML → Languages

1. Default language: **English**.
2. Uncheck languages you do not want yet (**Italian, Arabic, Russian**, …). Keep **English**, **Spanish**, and Swiss ones you need (**German**, **French**).
3. Confirm URL format is **Language name as a parameter**. If you set directories, `/es/` will mean Spanish and the Spain store will break.
4. **Browser language redirect: Off.**
5. Language switcher: use **WPML → Languages → Language switcher options** if you want a menu. Do not ask for a custom theme dropdown.
6. To add a language later, enable it here and translate with WPML/WCML as usual.

### WooCommerce → WooCommerce Multilingual → Multi-currency

- Mode: **independent / by location**, never “currency follows language”.
- Shop default currency: **GBP** (WooCommerce → Settings → General → United Kingdom).

### Rank Math + WPML SEO

- Leave **hreflang in the sitemap** (WPML’s default). Do **not** also print hreflang in `<head>`.
- One SEO plugin only (Rank Math). Do not add Yoast.

### After CMS Import (pages)

1. Same screen: **2. Import next products** until Complete.
2. **Media → Library** — if photos are there, ignore Tools → Justccell Media (no Media Pack plugin).
3. **Appearance → Storefront** appears only after the 0.9.x theme overwrite (WhatsApp / Telegram). Until then that menu will not exist.

If `/es/` starts behaving as a language folder, stop and check WPML URL format is still parameter.

**Left off on purpose**

- WPML Media Translation, WPML Export and Import
- ACFML (turn on later if ACF fields need translation jobs)

## Geo: no extra plugin

Cloudflare already sends `CF-IPCountry`. WooCommerce MaxMind is fallback only. Plugins that “detect language from IP” will bounce a UK visitor to English **and** a Spanish visitor to Spanish in a way that collides with store prefixes. We will not install those.

## Cache

LiteSpeed must **vary** HTML by store + language (cookie or URL). Otherwise UK/GBP pages get served to Spain. Configure when prefixes go live.

## Rank Math + hreflang (locked 2026-08-16)

**Rank Math SEO Free** replaced AIOSEO. **WPML SEO** (addon, currently 2.2.5) is required and already active.

When WPML shows *“Hreflang tags moved to your sitemap”*: **leave that default.** Do **not** re-enable “Display alternative languages in the HEAD section.”

Google accepts hreflang in the XML sitemap. Rank Math + WPML SEO inject `xhtml:link` alternates into Rank Math’s sitemap. Putting the same tags in `<head>` as well is the duplicate WPML is preventing. To force head tags you would also have to set `WPML_SEO_ENABLE_SITEMAP_HREFLANG` to `false` in `wp-config.php` — do not do that.

Language-switcher `hreflang="es"` on `<a>` tags in the header is unrelated (browser hint, not SEO `<link rel="alternate">`).

WPML hreflang is **language only** (`?lang=`). It does not encode country stores (`/uk/` vs `/es/`). That is correct for now. Theme-owned regional hreflang comes with Phase 2 `/{store}/{lang}/`.

Coming soon still serves HTML for `/sitemap_index.xml`. Verify sitemap hreflang (view-source of a child sitemap, not the index) after the gate is off, or exclude sitemap URLs from Minimal Coming Soon.
