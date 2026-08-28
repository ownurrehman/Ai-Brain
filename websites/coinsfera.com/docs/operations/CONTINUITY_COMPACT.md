> **Parent Site:** [[websites/coinsfera.com/index|🌐 coinsfera.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# coinsfera.com — Operations Continuity (Compact)

**Created:** 2026-08-13 — this file did not previously exist anywhere in the vault or home directory, despite being referenced as a prerequisite. Written from live verification.

---

## 1. Access (verified 2026-08-13)

| Item | Value |
|------|-------|
| SSH alias | `ssh coinsfera` — works, key auth, no password prompt |
| SSH config | `~/.ssh/config`: `ssh.coinsfera.com`, port `18765`, key `~/.ssh/coinsfera_sg` |
| Host | `giowm1287.siteground.biz` (SiteGround) |
| Docroot | `/home/u2095-ezyskmwwfto7/www/coinsfera.com/public_html` |
| PHP / WP-CLI | PHP 8.2.33, WP-CLI available at `/usr/local/bin/wp` |
| Credentials | `credentials/websites/coinsfera.env` + `master-env.env` (`COINSFERA_*`) |

**`.env.coinsfera` does not exist.** Credentials live at the two paths above — see `docs/ENV.md`.

Other docroots on the same account: `coinsfera.ae`, `coinsfera.org`, `backup.`, `suid.`, `staging8.`, `staging18.`

## 2. Platform

| Component | Version / state |
|-----------|-----------------|
| WordPress | 7.0.4 |
| Active theme | `coinsfera` v1.0 (custom, not a child theme) |
| ACF Pro | 6.8.0.1 |
| ACFML (ACF ↔ WPML glue) | 2.2.4 |
| WPML | 4.9.6 — languages **en / ru / tr** |
| Page builder | Elementor + Elementor Pro 4.2.x (homepage is page ID 9) |
| Caching | SG Optimizer (`sg-cachepress`) + Memcached object-cache dropin |
| Other | Yoast 28.2, `schema-and-structured-data-for-wp`, `wps-hide-login`, `coinsfera-plugin` (custom Elementor widgets) |

Homepage and existing service pages store content in **custom Elementor widgets** from `coinsfera-plugin`, translated via `<elementor-widgets>` in the theme's `wpml-config.xml`. This is a different architecture from the ACF template below — do not confuse the two.

## 3. Gotchas that cost real time (read before coding here)

1. **ACF initialises before the theme loads.** Some plugin triggers ACF during `plugins_loaded`, so by the time `functions.php` runs, `did_action('acf/init')` and `did_action('acf/include_fields')` are both already `1` (while `did_action('init')` is still `0`). Any `add_action('acf/init', …)` or `add_action('acf/include_fields', …)` registered from the theme is **dead code on this site**. Register immediately instead:

   ```php
   if ( did_action( 'acf/include_fields' ) ) {
       cfkl_register_field_group();
   } else {
       add_action( 'acf/include_fields', 'cfkl_register_field_group' );
   }
   ```

2. **ACF field keys are global, including repeater sub fields.** A sub field key built as `field_cfkl_{parent}_{name}` collides with a top-level field key when the repeater name prefixes it (`cfkl_steps` + `title` vs `cfkl_steps_title`). ACF then silently resolves the wrong field and rows save but read back empty. Sub fields here use the `field_cfkl_sub_*` namespace.

3. **SG Optimizer renames assets.** `keyword-landing.css` is served as `assets/css/coinsfera-keyword-landing.min.css`, and JS from `wp-content/uploads/siteground-optimizer-assets/`. Grepping rendered HTML for the original filename gives a false negative.

4. **Stale object cache lies to you.** `acf_get_field_group()` returned a post ID that no longer existed in `wp_posts`. Run `wp cache flush` before trusting ACF/DB reads.

5. **`header.php` / `footer.php` wrap content in a breadcrumb + title band** for every template except the hardcoded string `page-fullwidth-elementor.php`. Full-bleed templates must build their own document shell (see below) rather than editing those shared files.

6. **The nav has no background until you scroll.** `.topbar` is `position: fixed` with no `background`; `custom.js` adds `.fixed-header` (which sets `background-color:#fff`) only past 50px of scroll. So on first paint the menu's dark text floats over whatever the hero is — a dark hero makes the nav vanish into it. Two consequences for any new template: give `.topbar` a surface yourself (scope it to your body class so other pages are unaffected), and offset your first section by the ~92px header height, since `position: fixed` reserves no space.

7. **`header.php` hardcodes a preload** for the homepage hero image, which is wasted bytes on any other page.

8. Use the **www** REST URL. Non-www 301 drops POST bodies.

9. **`template-parts/header/header.php` declares `coinsfera_city_label()` at file scope** with no `function_exists` guard. Including the header part twice in one PHP process is a fatal. Harmless on real requests, but any CLI script that renders more than one page per process will die — render one page per process.

10. **Bot protection answers scripted requests with `202` and `content-type: text/html`.** Repeated `curl` from one IP, and all `HEAD` requests, get challenged. Site health checks from a laptop can look like failures when the site is fine; run `curl` from the server, or trust WP-CLI. It also means cross-origin asset loading into a local HTML file does not work.

11. **`WP_DEBUG_LOG` is on in production** and `wp-content/debug.log` has reached **629 MB** (2026-08-13). It is inside the web root. Worth rotating and turning off.

## 4. Keyword Landing template (built 2026-08-13)

Native WordPress page template, ACF-driven, no new plugins, no page builder.

**Third pass (2026-08-13, evening) replaced the single design with four.** The
second pass shipped one design and four hero arrangements of it, which read as
the same page four times. A page now picks one of four independent designs via
the `cfkl_design` field; each owns its section order, markup and stylesheet, and
they share only the data layer, the rate feed and the calculator behaviour.

```
wp-content/themes/coinsfera/
  functions.php                                    ← one require_once appended
  inc/keyword-landing/
    bootstrap.php    ← design router, conditional enqueue, schema, preload
    helpers.php      ← design registry, field readers, icon set, office, rate board
    rates.php        ← cached rate feed + REST route + cron refresh
    acf-fields.php   ← 86 fields incl. design selector, calculator, 6 new sections
  page-templates/template-keyword-landing.php      ← "Coinsfera - Keyword Landing (ACF)"
  template-parts/keyword-landing/
    designs/{desk,concierge,neo,ledger}/page.php   ← section order per design
    designs/{...}/<section>.php                    ← 13-15 partials per design
    shared/map.php                                 ← lazy Maps iframe
  assets/css/keyword-landing.css                   ← base layer only (209 lines)
  assets/css/design-{desk,concierge,neo,ledger}.css
  assets/js/keyword-landing.js                     ← reveal + sticky CTA
  assets/js/keyword-landing-calc.js                ← calculator engine
```

### The four designs

| Slug | Character | Type | Radius | Depth |
|------|-----------|------|--------|-------|
| `desk` | OTC dealing desk, dense, tabular | Circular + mono figures | 2px | **no shadows at all**, hairlines only |
| `concierge` | Editorial, warm, spacious | system serif display | arch shapes | large soft atmospheric blurs |
| `neo` | Bold consumer fintech | Circular 900, huge | 28px / 999px | solid offset, never blurred |
| `ledger` | Swiss annual report | micro-caps + giant numerals | 0 | 1px rules only |

Section order differs per design and is defined in each `page.php`; designs skip
sections that do not suit them (`desk` has no prose intro, `concierge` has no
rate board). `DESIGN-CONTRACT.md` in the vault is the binding spec — read it
before adding a fifth design or editing an existing one.

### Rate feed and calculator

- **Source:** CoinGecko (one call returns 8 coins × USD/EUR/TRY + 24h change), falling back to Coinbase `exchange-rates` (one call, no change data). Binance is **451 from this server** — geo-blocked, do not use.
- **The site's existing `getSingleCoinPrice` admin-ajax action is broken in production**: it proxies CryptoCompare per keystroke on a free key and now answers `{"Response":"Error","Message":"You are over your rate limit"}` (269 calls against a 100/month cap). The calculators on the existing coin pages are not returning prices. This feed deliberately does not use it.
- **Rendering never calls upstream.** CoinGecko intermittently 429s this shared IP, so a page render reads cache only and hands any refresh to cron. `cfkl_get_rates()` = cache read (0ms). `cfkl_refresh_rates()` = the only function that fetches, called by the 5-minute cron event `cfkl_refresh_rates_event` and by the REST route.
- Two transients: `cfkl_rates_v1` (180s, the live copy) and `cfkl_rates_backup_v1` (7 days, so a dead upstream shows a slightly old price rather than nothing). A stale read is flagged `stale: true` and the UI says "last known rate".
- **Public endpoint:** `GET /wp-json/cfkl/v1/rates` — 541 bytes, ~20ms cached.
- `serialize_precision` is 17 on this host, so floats serialise as `0.99900299999999997`. The route resets it to `-1` at encode time via `rest_pre_echo_response`.
- Calculator markup is found by **data attribute, never class**, so each design lays it out completely differently against one engine. Contract in `DESIGN-CONTRACT.md`. Spread comes from `cfkl_calc_spread_buy` / `_sell`; the CTA builds a WhatsApp link with the amount already written into the message.

- **Field group:** `group_cfkl_keyword_landing`, registered in PHP only (no DB row). **86 fields** after the third pass (was 42), across 15 tabs.
- Five duplicate DB copies of this group (created 2026-08-12, IDs 28203/28257/28311/28365/28420) were deleted via `acf_delete_field_group()`. Backup: `websites/coinsfera.com/acf-backups/keyword-landing-db-groups-20260813.json`.
- **WPML:** preferences set per field in code via `wpml_cf_preferences` (0 ignore / 1 copy / 2 translate / 3 copy once). ACFML synced them automatically — no manual ACF → Tools sync was needed. Repeater parents `1`, their text sub fields `2`, images `1`, URLs `3`.
- Template builds its own `<!DOCTYPE>` … shell and reuses `template-parts/header/header.php` and `template-parts/footer/footer.php`, so `header.php`/`footer.php` are untouched and no other page changes.
- Assets enqueue only when `is_page_template()` matches. FAQ accordion is native `<details>`; JS is ~1.5KB vanilla (IntersectionObserver reveals + sticky mobile CTA).
- Backup of the pre-change `functions.php` on the server: `functions.php.bak-20260813T163921Z`.

**Vault source of truth:** `websites/coinsfera.com/theme/keyword-landing/build/` mirrors the theme paths — `rsync` that directory to the theme root to redeploy.

### Design system (revised 2026-08-13, second pass)

The first pass used a dark hero, which collided with the transparent nav (gotcha 6) and read as off-brand. Rebuilt light and warm to match the site:

| Token | Value | Use |
|-------|-------|-----|
| `--cf-ink` | `#14110E` | headings |
| `--cf-ink-2` | `#56504A` | body copy |
| `--cf-orange` | `#F07922` | brand primary (from theme `--primary`) |
| `--cf-orange-lt` | `#F9A541` | gradient partner (theme `--warning`) |
| `--cf-orange-dk` | `#B95610` | orange text on light, for contrast |
| `--cf-cream` | `#FDFAF6` | alternating section band |

- Type is the theme's own **Circular Pro** — `circularbook` body, `Circular` (500) for medium weight, `circularbold` for headings. No webfont added.
- `.cfkl-container` deliberately mirrors Bootstrap's `.container` (1140px / 15px gutters, 1200px above 1600px) so section edges line up with the logo in the shared header. Getting this wrong is what made the first pass look misaligned.
- One 8px spacing scale plus `--cf-section` for band padding; every section title goes through `cfkl_heading()` so heading rhythm is identical everywhere. Previously only `requirements.php` wrapped its heading, which is why spacing looked arbitrary.
- Rhythm: paper / cream alternating bands, one orange gradient band for the closing CTA. No dark sections.
- Depth comes from hairline borders and layered shallow shadows, not heavy blurs. Arrow glyph and check marks are drawn in CSS (masked inline SVG), so no icon font request.
- Step numbers are rendered by the template, so **step titles in ACF must not start with "1."** or the number appears twice.
- Base element resets are wrapped in **`:where()`** (`.cfkl :where(h1, h2, h3, h4)`, `.cfkl :where(a)`). Without it, a reset like `.cfkl h2` scores (0,1,1) and silently beats every single-class component rule such as `.cfkl-cta__title` (0,1,0) — which painted the white CTA heading dark and the orange primary button's label dark orange on orange. `:where()` contributes zero specificity, so component rules win without `!important`. Do not unwrap those selectors.

## 5. Draft pages — one per design

All four carry identical content, so the only variable is the design.

| Design | ID | Slug | Preview |
|--------|----|------|---------|
| A OTC Desk | 28491 | `buy-bitcoin-istanbul-design-a-desk` | `?page_id=28491&preview=true` |
| B Istanbul Concierge | 28492 | `buy-bitcoin-istanbul-design-b-concierge` | `?page_id=28492&preview=true` |
| C Neo Fintech | 28493 | `buy-bitcoin-istanbul-design-c-neo` | `?page_id=28493&preview=true` |
| D Swiss Ledger | 28494 | `buy-bitcoin-istanbul-design-d-ledger` | `?page_id=28494&preview=true` |

All **drafts**, English, never published. Seed script (idempotent, seeds all four): `websites/coinsfera.com/theme/keyword-landing/seed-buy-bitcoin-istanbul.php`

Superseded second-pass drafts 28486, 28488, 28489, 28490 still exist and can be trashed.

**Review quotes are deliberately unseeded.** `cfkl_reviews_rating` (4.9) and
`cfkl_reviews_count` (1,043) are the real figures from the Google Business
Profile, but the quoted reviews must be real text pasted from that profile
rather than written, so `cfkl_reviews_items` is empty and the designs skip the
quote block until it is filled.

**Google listing:** the Maps button uses the Place FTID lifted from the map embed already on the contact page — `https://www.google.com/maps?ftid=0x14cab9eaf6c4d8b3:0xb19442e13f909950`. Editable via the `cfkl_banner_card_btn_url` field.

**`/buy-bitcoin-in-istanbul/` already exists** as page ID 2036, published, Elementor, 1,908 words. The draft is a rebuild candidate for it, not a replacement yet. `buy-*-in-istanbul` and `sell-*-in-istanbul` pages exist for BTC, ETH, USDT/Tether, BNB, LTC, XRP, TRX, BCH and generic crypto, in all three languages.

## 6. Verification state

Tooling, all read-only, in `websites/coinsfera.com/theme/keyword-landing/`:

| Script | Checks |
|--------|--------|
| `design-check.php <id>` | renders one draft and reports structure, calculator wiring, images, assets, schema, heading outline |
| `css-audit.py` | every design stylesheet is scoped, no `!important`, no `@import`, no external `url()` |

Run `design-check.php` **one page per process** (gotcha 9). It must query with an
explicit `post_status`, otherwise a page query returns nothing for a draft and
every count reads zero.

Confirmed 2026-08-13 after the third pass:

- 66 PHP files lint clean; all 5 stylesheets audit clean and fully scoped
- All four designs: exactly one `<h1>`, clean h2/h3 outline, 12–15 sections, no inline styles, every image carries width/height, one eager hero image
- Calculator renders in all four with live server-side values (BTC 63,649 USD / 3,040,860 TRY at time of check)
- One `FAQPage` block with 7 questions per page, no duplicate from the schema plugin
- `/wp-json/cfkl/v1/rates` returns 200 in ~20ms cached
- Homepage and the buy/sell pages return 200 after deployment, no new errors in `debug.log`

## 7. Open items

- **Visual QA in a browser is the next step** — the four designs are verified structurally, not visually.
- Paste real Google reviews into `cfkl_reviews_items` on whichever design is chosen.
- The site's own `getSingleCoinPrice` handler is over its CryptoCompare quota, so calculators on the **existing** coin pages are broken. Separate fix; this feed could replace it.
- Landing copy is ~1,900 words with the new sections, comparable to existing page 2036.
- No Yoast title/meta description or featured image set on the drafts.
- RU/TR translations not created yet. Coded correctly for ACFML 2.2.4 but not exercised end to end.
- Decide whether the chosen design replaces page 2036 or ships as an additional page, then delete the three unused designs' folders and stylesheets if they will never be used.
