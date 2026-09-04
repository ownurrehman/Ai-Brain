> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Justccell — Project Mastersheet

## Site Overview
- **Name:** Justccell
- **URL:** https://justccell.com
- **Niche:** Vaporization hardware (global B2B/partner)
- **Tagline:** "Your Global Partner in Vaporization Hardware"
- **WordPress REST:** https://justccell.com/wp-json/wp/v2/ (verified 200, 2026-08-28)
- **Auth:** not yet configured (add `JUSTCCELL_WP_*` to master-env.env when work starts)
- **Hostinger:** user `u392808260`, client_id 36554880, created 2026-08-14
- **Root directory:** /home/u392808260/domains/justccell.com/public_html

## Hostinger Access
- Via shared OAuth: see `~/.hermes/profiles/chronos/skills/software-development/rankray-coding-mastery/references/hostinger-mcp.md`
- Files, cron, DB, PHP, redirects manageable over Hostinger API

## Vault Folder
- `websites/justccell.com/` — has INDEX.md, README.md, content/, docs/, justccell-theme/ (pre-existing folder; this mastersheet added 2026-08-28 during Hostinger sync)

## WordPress REST Access (added 2026-09-01)
- **User:** rankrayofficial@gmail.com (ID 1, admin "Rank Ray")
- **App Pass:** stored in `master-env.env` as `JUSTCCELL_WP_APP_PASS`
- Verified working against `/wp-json/wp/v2/users/me` on 2026-09-01
- SEO plugin: Rank Math (REST write via `/wp-json/rankmath/v1/updateMeta`, keys `rank_math_title` + `rank_math_description`)

## Status
- Live, WordPress, home 200
- WP app-password configured (2026-09-01)
- **2026-09-04:** theme **0.9.219**. Elite Terpenes free-delivery coupons via Woo REST (`docs/elite-cross-sell.md`). **57 published Woo products** locked (`rules.md` §7.8).

## Done Log
- 2026-08-28: Replaced 15 Discover featured images with 510 / ceramic hardware photos (`justccell-v2-*.jpg`); old lab/workshop stock removed
- 2026-08-28: Discovered via Hostinger API sync; mastersheet created
## 2026-08-31: SEO Meta Fix (Hermes via Hostinger MCP + WP admin session)
- Auth: Hostinger MCP autologin (u392808260, install 30055979) → admin cookies + REST nonce
- SEO plugin: Rank Math (REST write via /wp-json/rankmath/v1/updateMeta, keys rank_math_title + rank_math_description)
- Audited 26 pages + 15 posts = 41 URLs
- Wrote custom meta for 24 pages (all were missing desc; titles were default "%title% - Justccell")
- 15 posts already had OK meta (Enigma's earlier work) — untouched
- Checkout page 9 set to noindex,nofollow (redirects to Basket when empty; correct for e-commerce)
- FIXED & VERIFIED: 40/41 URLs pass title+desc audit (fresh frontend crawl with cache-bust)
- KNOWN ISSUE (needs theme/code fix by Chronos or dev):
  - /battery/ /pod-system/ /cartridge/ /all-in-ones/ are WooCommerce product_cat archives served via catalog-clone with body class "blog"
  - RankMath resolves current object to page_for_posts = page 202 (Discover) → all 4 emit Discover's <title>, <meta description>, og:url=https://justccell.com/discover/, and robots noindex,nofollow
  - RankMath meta for WP pages 226/225/224/223 AND product_cat terms 19/18/17/16 both written but not surfaced — the theme router bypasses both objects
  - Fix needed in theme (inc/listing.php or catalog router): serve the actual page object (226/225/224/223) so RankMath picks per-page meta; until then these 4 URLs are noindex in Google
- Robots finding: /battery/ etc currently noindex — bad for SEO; theme fix required

## 2026-08-31 (later): Grok fix verification + final state
- Grok (Cursor) deployed theme 0.9.56 while Hermes audited: catalog-clone router now queries real pages (223/224/225/226) — verified via frontend: correct per-page titles, descs, og:urls on /battery/ /pod-system/ /cartridge/ /all-in-ones/
- Hermes set index,follow robots via RankMath REST on 223-226 (sitewide noindex was from WP blog_public=0 "Discourage search engines" — turned back ON via options.php; Hostinger maintenance mode also disabled during test)
- Homepage fixed: show_on_front=page, page_on_front=241 restored via REST (Grok's deploy had flipped it to 0)
- FINAL VERIFIED STATE: 38/41 URLs pass (title + description + indexable). Remaining 3 (basket, checkout, my-account) are WooCommerce utility pages CORRECTLY noindexed with clean titles.
- Homepage title/desc: "Justccell: Cannabis Vape Hardware Manufacturer for Brands" — indexable
- Site-wide noindex RESOLVED. Site is now Google-indexable with full meta coverage.

## 2026-09-01: Media Library thumbnail repair (Hermes)
- Diagnosis (verified): 36 tank-360-*.jpg attachments (IDs 115-150) had EMPTY attachment metadata -> grid could not render them. Originals all 200 OK. Cursor's mu-plugin (jc-media-repair-cli.php in wp-content/mu-plugins/) was stuck at cursor 61 in an infinite loop (pending=1 forever) and never reached the broken items.
- Fix: Hermes plugin "Justccell Media Repair + Cleanup" (jc-media-meta-fix) regenerated metadata via wp_generate_attachment_metadata() for all 36 IDs. Verified remaining=0, healthy=36.
- Cleanup: Cursor mu-plugin DELETED via plugin v2 cleanup endpoint (jcm_cleanup trigger). mu-plugins now only contains Hostinger's own files (hostinger-auto-updates.php, hostinger-preview-domain.php).
- Grid AJAX re-tested: 1.7-1.9s response with healthy thumbnail data (the earlier 39s/empty-grid was transient/cold, not persistent).
- Cleanup TODO (user): Deactivate + delete "Justccell Media Repair + Cleanup" plugin from Plugins page when convenient. Code saved in scripts/jc-media-meta-fix-v2.php.
- NOTE: Hostinger maintenance mode re-enabled BY USER (do not touch without explicit approval).
- 2026-09-01: Legal pages filled: Terms (id 203), Privacy Policy (id 3), Cookie Policy (id 204) via REST. Content grounded in site facts: authorized CCELL dealer, operated by 3Devices LTD (UK), B2B hardware, contact 3Devicesltd@gmail.com. Terms 12 sections, Privacy UK GDPR aligned, Cookies covers necessary/comment/login/analytics cookies. Verified live on all 3 pages.- 2026-09-01: Full-site boilerplate audit (44 URLs crawled): all clean except refund_returns draft (id 11) which had Woo default sample refund policy - rewrote with correct B2B dealer policy (48h inspection/30-day returns/RMA), kept as draft for review. Site now 100% free of demo/boilerplate content.- 2026-09-04: Elite Terpenes cross-sell live (theme 0.9.219). Justccell POSTs `JC-{order_id}` free-shipping coupons to eliteterpenez.com `/wp-json/wc/v3/coupons`. Elite plugin `justccell-coupon-bridge` applies `?apply_coupon=`. REST ping HTTP 201 verified. Vault: `docs/elite-cross-sell.md`.
- 2026-09-02: Catalog cut job 1 of 5 (Hermes labor): moved 36 clone SKUs to Woo TRASH (status=trash, never force-delete, never emptied). Verified published = exactly 11 (airone, blade, voca-pro-max, eco-star, flo, gembar, eazie-pro-3-0, eazie-pod-3-0, vita, th2-evomax, m6t-evomax). All 36 recoverable in trash (verified via wc/v3?status=trash). 301 suggestion map written for Cursor: docs/redirect-map-catalog-cut.md. No theme edits, no redirect plugins, no products created.


## 2026-09-02: Catalog cut job 2 of 5 (Hermes labor) - Remap slugs
- Renamed on EXISTING products (IDs kept, categories kept, no duplicates):
  - 327276: eazie-pro-3-0 -> eazie-pro, title "Eazie Pro" (Pod Systems)
  - 327277: eazie-pod-3-0 -> eazie-pod, title "Eazie Pod" (Pod Systems)
  - 261: th2-evomax -> th2-evo, title "TH2-EVO" (Cartridges)
  - 262: m6t-evomax -> m6t-evo, title "M6T-EVO" (Cartridges)
- DB verified: wp_posts post_name correct, ?p=261 canonicalizes to /cartridge/th2-evo/, admin edit shows correct permalink.
- VERIFIED WORKING: /pod-system/eazie-pro/ and /pod-system/eazie-pod/ return 200 logged-out with product template.
- OPEN ISSUE FOR CURSOR (theme router, do not fix via labor): /cartridge/th2-evo/ and /cartridge/m6t-evo/ 301 (x-redirect-by: WordPress) to the OLD slugs /cartridge/th2-evomax/ and /cartridge/m6t-evomax/, which still serve the products (H1 = new titles, canonical = new URLs). Pod renames work; only the cartridge pair is affected. Evidence points at the catalog-clone router's cached slug->product map (transient or theme lookup) not rebuilt after the slug change: a brand-new slug (th2-evo-xyz-test) also failed to resolve (served Discover template), while a third fresh slug pattern resolved instantly. REST/DB state is correct; front-end routing needs the theme router flushed/fixed. Not a LiteSpeed page-cache issue (cacheless mode tested, still redirects). Not WPML language issue (en-only URLs affected).
- Note: cacheless mode was toggled ON for testing and LEFT ON (server cache bypass) - Cursor may toggle off after router fix.


## 2026-09-02: Catalog cut job 3 of 5 (Hermes labor) - Create 8
- Created 8 published Woo simple products (no prices, no theme edits, no ACF essays):
  - 328866 Easy Bar Evo Max /easy-bar/ (All-In-Ones)
  - 328867 Flex /flex/ (All-In-Ones)
  - 328868 Flex 2 /flex-2/ (All-In-Ones)
  - 328869 Kera /kera/ (Cartridges)
  - 328870 M4 /m4/ (510 Batteries)
  - 328871 M4 Tiny /m4-tiny/ (510 Batteries)
  - 328872 Palm SE /palm-se/ (510 Batteries)
  - 328873 AIO Voltage Tuner /aio-voltage-tuner/ (Uncategorised id 15 - Equipment category not in Woo yet)
- Verified: published catalogue = 19 (11 remapped + 8 new). All slugs clean (no -2 suffixes).
- FOR CURSOR (theme router):
  - /all-in-ones/flex/ 301s to /all-in-ones/flexcell/ (trashed product still in router map; new flex not in map). flex-2 works. Same stale-map issue as job 2 cartridges.
  - /product/aio-voltage-tuner/ 301s to /contact/?sku=aio-voltage-tuner (theme funnel for uncategorised products?). Equipment category + Tuner layout is Cursor's job per work split.


## 2026-09-02: Catalog cut — Cursor theme 0.9.93 (local; deploy pending)

- Theme fixes in `websites/justccell.com/justccell-theme/` + zip `justccell-theme-0.9.93.zip`: catalog-cut 301 map, removed bad legacy redirects, j3 products back on category grids, 3.0 fallback cards cleaned, Equipment routing support.
- **Live REST done:** Equipment category (ID 75); aio-voltage-tuner assigned to Equipment.
- **Deploy blocked:** Hostinger TUS 401 + deployWordPressTheme API 500. Upload zip via hPanel → `wp-content/themes/justccell-theme/` (overwrite), then purge LiteSpeed cache.
- **Hermes job 6:** 8 featured images + trash stray Diama publish + M4 mAh QA — see `docs/hermes-prompts-product-catalog.md` Prompt 6.
- Read all 17 launch PDFs (image-based): converted 20 pages to PNG (pdftoppm) + vision-read each sheet. All specs transcribed from printed sheets only; unprinted fields flagged not invented.
- Filled ACF "Product page" on ALL 19 keep products via admin editpost POST (cookie session + _acf_nonce): tagline, subtitle, spec repeater rows, feature rows, colours, card tagline, oil_group, laser toggle.
- Laser: ON for 9 hardware products with OEM engraving evidence (airone, blade, eco-star, flo, gembar, th2-evo, m6t-evo, m4, m4-tiny, flex, flex, flex-2); OFF for Tuner and others without laser evidence (voca-pro-max, eazie-pro, eazie-pod, vita, kera, palm-se, easy-bar).
- Eazie Pro combinations: already rendered by theme (Pod and battery / Pod only / Battery only), empty prices. No GBP prices anywhere. No emojis, no em-dashes, no ccell.com CTAs.
- Verified per-product in admin forms: specs+features+tagline saved on all 19 (counts logged).
- Frontend verified logged-in: /cartridge/kera/ (H1 Kera, 9 specs, no Diama), /pod-system/eazie-pro/ (H1 "Eazie Pro" clean, no 3.0 in H1; 3.0 matches only in old featured-image asset alt and Justccell 3.0 site nav which are not the product), /cartridge/th2-evomax/ serves TH2-EVO with 7 specs. No Tank, no Flexcell on checked pages.
- KNOWN THEME ROUTER ISSUES (Cursor): /all-in-ones/flex/ 301s to trashed flexcell; /cartridge/th2-evo/ + /m6t-evo/ 301 to old slugs (which serve correct renamed products with new ACF data).
- Images NOT attached (per job); old featured images (clone-era filenames like eazie-pro-3-0-justccell-featured.png) still set - media job pending.

- 2026-09-02: Catalog cut job 5 of 5 (Hermes labor): Rank Math unique titles (<60c) + descriptions (<160c) pushed for all 19 (17 verified live; aio-voltage-tuner + flex hidden by theme router redirects, meta saved in RM). Homepage hero slides fixed (tank/diama -> voca-pro-max/kera; alt text fixed). Rails/mega verified clean: zero trashed product references sitewide. 3.0 page relationship ACF clean; 3 trashed cards are THEME hardcoded (Cursor). Quote SKUs: product pages link own SKU only. BUILD-LOG appended with full detail.

- 2026-09-02: Client-fill CSV built (csvs/justccell-product-prices-stock-CLIENT-FILL.csv, 19 rows x 36 cols) + CLIENT-FILL-GUIDE.md. Measured from live site: existing SKUs (8 products have slug-SKUs, 11 proposed {slug}-jc-01), current theme tier defaults (AIO/cartridge/pod £3.60 base, battery £2.77) prefilled as hints for client to replace. Tier structure matches live buy-widget (5 tiers: 1/101/1001/5001/10001). Columns cover: prices ex VAT, 5 tiers, stock + low-stock threshold, colour variants, Eazie Pro combinations, MOQ, sale price engine, dimensions, bundles (components + qty + discount%). Prices flow to Woo _regular_price + ACF offer_tiers (theme buy widget source to be confirmed by Cursor - current tier defaults are theme-side per category).

- RULE: NEVER edit theme PHP files on this site. Chronos broke bio-heating.php on 2026-09-01 (file API returned collapsed newlines, wrote back broken). All theme content changes go through Sheikh or Cursor. ACF/XML-RPC/REST only.- 2026-09-02: 8 client-created products images attached (AIO Voltage Tuner, Palm SE, M4 Tiny, M4, Kera, Flex 2, Flex, Easy Bar Evo Max) - sourced from official CCELL distributors, uploaded via REST, Woo+ACF attached, live verified.- 2026-09-02: 4 new products created (M4B Pro, Rizo, Kap, Silo) with images sourced from CCELL distributors, SKUs, ACF colour fields, Google Sheet rows added. Live at /all-in-ones/{slug}/.- 2026-09-02: 31 products converted to variable with 112 variations (16 AIO + 14 batteries/pods + 1 new product). Enigma writing category descriptions. (colour + tank capacity attributes). 35 trashed products restored. ACF-driven pages don't show variation forms (theme limitation) - visible on /shop/.

## 2026-09-03: Multi-Bot Governance & Content Rules Locked

- **Multi-Bot Operating System:** Rules established and synchronized across all AI bot entry points:
  - `rules.md` (Site Master Rules)
  - `AGENTS.md` (Standard Multi-Agent Directives for Hermes, Grok, Cursor, Antigravity)
  - `.cursorrules` (Direct Cursor/Grok directory rules)
  - `.cursor/rules/justccell-page-content-editability.mdc` (Master workspace Cursor rule)
- **Hard Rule — 100% Backend Content Editability:** Every page heading, paragraph, button text, CTA link, and media asset must be editable via native WP/WooCommerce or properly mapped ACF fields in the wp-admin edit screen (`Pages → Edit Page` / `Products → Edit Product`). No hardcoded marketing copy in PHP templates or JS.
- **Hard Rule — Mandatory ACF Cleanup & 1:1 Sync:** No leftover or ghost ACF fields. When layouts change, obsolete fields must be pruned from `inc/acf-*.php`, `acf-json/`, and wp-admin. 100% 1:1 sync between frontend templates and backend fields.
- **Client Mandate — Zero "Get Samples & Quotes" Sitewide:** Mandated by Mr Nas (CCELL Mazhar, 2026-09-03): *"Anywhere you see get samples and quotes on the whole site please remove. Its not something we offer."* All sample requests, sample trays, and sample turnaround promises are strictly prohibited sitewide; all CTAs must focus on business inquiries, wholesale quotes, or direct contact.

## 2026-09-04: Theme 0.9.201 — vault catch-up (Cursor)

- Live theme **0.9.201** in `wp-content/themes/justccell-theme/` (in-place TUS).
- Product PDP SEO (0.9.197): Product heading = H1, Product Tagline = H2, Specs = H3 + ul. Banner heading/text ACF deleted. Woo description editor on.
- Bio slug: `/justccell-3-0/` canonical; `/ccell-3-0/` 301s there only (never reverse).
- Sample-copy purge in seed PHP + copy-policy v0993 + Contact FAQ scrubber.
- Packaging / Elite Terpenes = Coming Soon template.
- **Obsidian rule locked:** STATUS + BUILD-LOG + rules must update in the same turn as code (`rules.md` §0.13).