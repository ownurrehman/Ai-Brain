> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/justccell.com/rules|📜 Site Rules]]

# justccell.com — Opus 4.8 Report & Fixes (hand-over)

**Model / author:** Claude **Opus 4.8** in Cursor (Principal WordPress Architect pass).
**Dates:** 2026-09-06 (multi-session).
**Theme at hand-over:** **0.9.297** (live-verified) in `wp-content/themes/justccell-theme/` only.
**Hostinger:** account `u392808260` · site `justccell.com` · WP `30055979`.
**Method:** live-verified against the running site via **Hostinger MCP + logged-in browser CDP** (coming-soon bypass) and authenticated **WP REST**, cross-checked with theme source. **Not local-only.**
**How to read this:** ✅ = shipped + live · ⏳ = open follow-up · ⚙️ = wp-admin/plugin/ops action (not theme code).

> This is a single hand-over doc for the agent team (Cursor, Grok, Hermes, Antigravity) and the owner. It consolidates the whole engagement so anyone can pick up and continue. Dated ship notes live in [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG.md]]; the earlier deep audit is [[websites/justccell.com/docs/AUDIT-REPORT-2026-09-06|AUDIT-REPORT-2026-09-06.md]]; live snapshot is [[websites/justccell.com/docs/STATUS|STATUS.md]].

---

## 0. Executive summary

The theme is **architecturally sound**: modular `inc/*.php` controllers, ACF via Local JSON + GUI (no PHP field arrays), WooCommerce-first data, in-place TUS deploys, one theme folder. Across this engagement Opus 4.8:

1. **Audited the live site** (Phases 1–4 + SEO + WooCommerce + data-preservation) — found **no active runaway loops or duplicate-field bugs in code**, but **historical DB residue**, **stale governance contradictions**, **SEO dedupe/indexation gaps**, and **no formal backup system**. All code-side issues fixed and shipped (**0.9.293 → 0.9.297**).
2. **De-bloated the live database** — ACF registry cut from **825 → 433** field rows, **60 → 0** duplicate keys, **3 trashed groups purged**; zero live fields or product data lost.
3. **Fixed SEO** — removed duplicate Organization schema, clamped meta descriptions to ≤155 chars, scrubbed banned "sample" wording at output, backfilled empty image alts, and made virtual PDP/listing routes self-canonicalize.
4. **Fixed the bio page URL revert bug** — the client renamed the page to **`/ccell-3-0/` / CCELL 3.0** in wp-admin, but the theme was **silently reverting it** on every load. Neutralized all revert paths and made slug/title a filterable single source of truth.
5. **Made the theme clone-safe** — retargeted **7 page ACF groups** from page-**slug** binding to page-**template** binding (portability law), so client renames never lose fields and the theme can be duplicated for eliteterpenez + future stores.
6. **Hardened governance** — added **Rule §0.6** (every AI *and detected manual* change synced to local docs same turn), rewrote the stale rules that caused the revert, and wrote a **framework portability guide**.
7. **Built the backup system** — git history + rotating 10 local theme snapshots + Hostinger UpdraftPlus; tagged release **`justccell-0.9.297`**.

**Live truth (expected):** the whole site currently returns `noindex, nofollow` with no `<link rel=canonical>` because **Settings → Reading → "Discourage search engines" is ON** (`blog_public = 0`) for pre-launch. This is correct while coming-soon; canonicals/indexing return at launch.

---

## 1. Phase 1 — Rule pruning & AI-Brain cleanup ✅

Two secondary governance files carried **contradictory legacy instructions** that would make agents repeat old mistakes:

| File | Stale instruction | Corrected truth | Status |
|---|---|---|---|
| `.cursorrules` | "ACF field … registered in `inc/acf-*.php`" | Fields = GUI + `acf-json/` only; `inc/acf-*.php` = plumbing | ✅ |
| `.cursorrules` / `AGENTS.md §5` | Canonical bio URL = `/justccell-3-0/` (later `/cell-3-0/`) | Canonical = **`/ccell-3-0/`** · title **CCELL 3.0** (client rename); all legacy slugs 301 in | ✅ |
| `rules.md` §7.5 / nav-label rule | "Menu titles must not show CCELL 3.0 … use `/justccell-3-0/`, never `/ccell-3-0/`" | **The exact landmine** that caused the revert — rewritten to the new canonical | ✅ |

**Master ACF rule now enforced everywhere:** *All ACF Pro fields are managed strictly via the wp-admin GUI and synced via Local JSON (`acf-json/`). Hardcoded PHP field registration is forbidden. `inc/acf-*.php` is plumbing only.* Dead `justccell_acf_register_field_group()` (zero callers) removed (0.9.293).

---

## 2. Phase 2 — ACF Pro & GUI enforcement ✅

- **20 live field groups** load from Local JSON + DB. No `acf_add_local_field_group()` field arrays remain.
- **No rogue field mutators** — no active `acf/load_field` reordering, `acf_update_field()` loops, or version-bump appenders. Old repair hooks are dormant (run-once flags set).
- **Location rules → page TEMPLATE** (see §7, the big portability fix).
- **Live DB de-bloat (0.9.293)** — the ACF *database* carried import residue invisible at runtime (Local JSON wins on load):
  - Field-group posts `23` (incl. 3 trashed) → **20** (0 trashed).
  - `acf-field` rows `825 publish + 35 trash` → **433 publish, 0 trash** (427 orphan/dup rows removed).
  - Duplicate field keys `60` (e.g. `field_jc_prod_banner` ×14) → **0**.
  - Fix: one-time admin-only conservative purge `justccell_acf_purge_trashed_and_orphan_fields()` (deletes trashed groups + trees, and only `acf-field` posts whose top ancestor is **not** a live group). Live fields + all product `clone_*` postmeta untouched. Guarded by `justccell_acf_orphan_purge_293`; self-disables.

---

## 3. Phase 3 — Template & dependency architecture ✅

- **Hierarchy:** virtual product/listing routes use query vars (`justccell_product` / `justccell_listing`) + `template_include`; PDP = `template-parts/product/clone.php`, listing = `catalog-clone.php`. Native Woo endpoints keep core layout.
- **Enqueues:** registered in `inc/assets.php`, conditioned per template/context (e.g. `product-spin` + `product` + `wc-add-to-cart-variation` on PDP only; Woo branding CSS on cart/checkout/account only). No global asset bloat.
- **Admin isolation:** storefront bundles not enqueued in wp-admin; only narrow ACF/mapper CSS.
- **SEO markup:** JSON-LD via Rank Math; theme defers Organization to the SEO plugin (no duplicate node). Heading ladder (one H1, H2 tagline, H3 specs) intact.

---

## 4. Phase 4 — Conflict resolution & server optimization ✅

- **Runaway loops (`admin_init` / `save_post`):** none active. ACF repair machinery dormant; legacy-field hiding uses `acf/prepare_field` only on safe admin GET renders (never during POST/AJAX/validation) so it can't strip the save registry or trip the "nonce failed" bug.
- **Query efficiency:** catalog reads go through `justccell_catalog_from_woo()`; no nested `WP_Query` anti-patterns in audited paths.
- **Orphaned parts / conflicting CSS:** Woo core CSS retained on endpoints; theme `woocommerce.css` is branding-only. No core-style clobbering.

---

## 5. SEO audit & fixes ✅ / ⚙️

**Shipped (0.9.294–0.9.296):**
- **Duplicate Organization schema removed** — theme now defers to an active SEO plugin (`RANK_MATH_VERSION` / `WPSEO_VERSION` / `AIOSEO_VERSION`); filter `justccell_force_org_schema` to force the theme node. Verified: **one** Organization node per page.
- **Meta-description clamp + policy** — `justccell_clamp_meta_description()` on `rank_math/frontend/description` (+ `wpseo_metadesc`) trims to ~155 chars on a word boundary and scrubs banned "sample(s)" → "quote(s)".
- **Empty image alt backfill** — `wp_get_attachment_image_attributes` fills empty `alt` from attachment alt/title → parent title (never overrides an editor-set alt).
- **Canonical on virtual routes** — virtual PDP/listing routes have `is_singular=false`, so Rank Math couldn't self-canonicalize them. Added `justccell_rank_math_view_canonical()` on `rank_math/frontend/canonical` (+ `wpseo_canonical`) → returns `justccell_product_url()` / `justccell_category_url()`. Fills only empty values.

**⚙️ Launch levers (not theme code):**
- Uncheck **Settings → Reading → "Discourage search engines"** (`blog_public = 1`) + disable coming-soon → indexing + canonicals return automatically.
- Rank Math per-page title/focus keyword/description authored by owner — correct workflow.

---

## 6. WooCommerce native-behaviour audit ✅ / ⏳

- **Variation image swap** — the theme bypasses the native Woo gallery on PDPs with a custom JS gallery (`product.js` `bindVariationGallery()`) layered over the 360° spin (`product-spin.js`). This is the source of the intermittent "image doesn't change on variation click." Live tests mostly PASS; failures appear tied to **per-variation image data** (variations with no assigned image), not a clear code bug.
  - **⏳ Open (d5):** need one concrete failing SKU + colour + the variation's image state. Then decide: (a) restore native Woo still-image reliability while keeping the 360° drag, or (b) fix the data. Deferred pending repro so working PDPs aren't destabilised.
- **Attribute model** — colours/variations use native Woo **Attributes** + variations (never legacy `clone_colours`).
- **"Basket" → "cart" wording** + hidden internal laser meta keys handled via filters, not core edits.

---

## 7. Bio page rename + ACF template-binding (0.9.297 — the big fixes) ✅

### 7a. Bio URL revert bug — root cause & fix
The client (Mr Nas) renamed the bio page to slug **`ccell-3-0`** / title **CCELL 3.0** directly in wp-admin. The theme + AI brain still forced the old `cell-3-0`, so the code **silently reverted the client's manual change** on every `init`/`admin_init`. Three force-revert paths + one stale rule were found and neutralized:

| Path | File | Fix |
|---|---|---|
| Single source of truth | `inc/page-layouts.php` | `justccell_bio_canonical_slug()` → `ccell-3-0`, `justccell_bio_canonical_title()` → `CCELL 3.0` (both `apply_filters`-able) |
| Rename migration | `inc/page-layouts.php` | `justccell_canonicalize_bio_page_slug()` gate now **keyed to the canonical slug** (`justccell_bio_slug_ccell_3_0`) — self re-runs if canonical changes again; enforces title only on empty / "3.0"-variant titles |
| Redirect map | `inc/catalog-redirects.php` | Legacy bio redirects built **dynamically** from the canonical; `/cell-3-0/`, `/justccell-3-0/`, `/ccell-3.0/`, `/justccell-3.0/` → `/ccell-3-0/`; canonical never redirected away |
| Page seeder | `inc/setup.php` | Seeds `ccell-3-0` / `CCELL 3.0`; adopts a legacy alias page (no duplicate) |
| Hardcoded fallbacks | `inc/acf.php`, `inc/chrome.php`, `inc/listing.php` (×2), `inc/header-menu.php`, `inc/nav-fallback.php`, `inc/static-pages.php`, `template-parts/page/spotlight.php` | Updated to `/ccell-3-0/` + `CCELL 3.0` |

**Live-verified:** `/ccell-3-0/` = 200; `/cell-3-0/` 301 → `/ccell-3-0/`; page ID 201 REST = slug `ccell-3-0`, title `CCELL 3.0`; no page left at `cell-3-0`.

### 7b. ACF groups: page-slug → page-TEMPLATE binding (portability law)
Slug-bound ACF groups break the moment a client renames a page (exactly what happened) and can't be reused when cloning. Converted **7 page groups** in `acf-json/` from the custom `justccell_page_slug` rule to native `page_template`:

| Group | Template |
|---|---|
| `group_jc_about_page` | `page-templates/justccell-about.php` |
| `group_jc_why_pages` | `page-templates/justccell-why.php` |
| `group_jc_legal_pages` | `page-templates/justccell-legal.php` *(0-field native-content stub — legal uses the WP editor)* |
| `group_jc_locations_page` | `page-templates/justccell-location.php` |
| `group_jc_generic_brand` | `page-templates/justccell-brand.php` |
| `group_jc_j3_page` | `page-templates/justccell-bio.php` |
| `group_jc_discover_hub` | `page-templates/justccell-discover.php` (+ Posts page) |

- One-time live migration `justccell_acf_retarget_page_groups_to_templates()` (`inc/acf-product-clone-maintenance.php`, `admin_init` pri 25, gate `justccell_acf_tmpl_locations_297b`) re-imports each group from Local JSON so the DB location rule persists (a plain `acf_update_field_group()` no-ops when Local JSON owns the group).
- **Documented exception:** `group_jc_laser_page` stays slug-bound (`laser-engraving`) — it shares `justccell-brand.php` with 4 sibling brand pages but its fields belong only to the laser page.
- **Live-verified:** About group (post 549) shows `page_template == …/justccell-about.php` in wp-admin; ACF fields still render on all 7 pages (about 63, why 55, legal 0-by-design, brand 72, bio 108, discover 10, location 26); "Sync available" notices 10 → 3 (remaining 3 = pre-existing drift on untouched groups). **No field/data loss.**

---

## 8. Governance hardening — Rule §0.6 ✅

Added to `rules.md`: **AI Brain is the live mirror — sync every change, same turn.**
- Every change (code, ACF, URL/slug, title, menu, setting, plugin, content structure) → update the relevant docs the **same turn**; "code shipped" without "docs synced" = task failed.
- **Detected manual/client changes count too** — if live diverges from docs (like the un-logged URL rename), fix the code so it won't revert the client, then sync every doc.
- **No stale landmines** — when reality reverses a rule, rewrite/delete it; contradictory rules are the root cause of revert bugs.
- **Verify, don't assume** — confirm live state before writing "done."

---

## 9. Framework portability (clone-safety for eliteterpenez + future stores) ✅

New doc [[websites/justccell.com/docs/framework-portability|framework-portability.md]] — the reusable-ecommerce-framework law:
- **3 portability laws:** (1) bind ACF to template not slug, (2) site identity = filter/options value never a literal, (3) each clone keeps its own in-sync brain folder.
- What's already portable (template-bound ACF, filterable bio slug/title, options-page glue, Woo-native commerce, Local JSON ACF).
- The per-clone **config surface** (bio slug/title, brand name, phone/email, colours, redirects, cross-sell endpoints, footprint rule).
- **Known debt:** the `justccell_` function/text-domain prefix — keep it for the first 1–2 clones (invisible to visitors; override display strings via filters/options) or do a deliberate scripted rebrand once the framework stabilises.
- Step-by-step **clone procedure** + anti-patterns that caused real bugs.

---

## 10. Backups, vault de-clutter & release ✅

- **Backup system (3 layers):** git history (lossless primary) + `archive/theme-releases/` rotating **last 10** via `scripts/backup-theme.sh` (gitignored) + Hostinger UpdraftPlus (site + DB). Runbook: [[websites/justccell.com/docs/backup-restore|backup-restore.md]].
- **Current local restore points:** `0.9.202 / 0.9.203 / 0.9.205 / 0.9.296 / 0.9.297` + archived zips + full git history.
- **Declutter:** removed restore-era one-off server scripts (`jc-cleanup.php` etc., in git history), relocated ~173 MB of root theme zips into the gitignored archive, purged `.DS_Store`, linked orphan docs with parent-hub breadcrumbs.
- **Release:** commit `dc099e44`, annotated tag **`justccell-0.9.297`** (local; not yet pushed).

---

## 11. Current live state snapshot

| Item | State |
|---|---|
| Theme | **0.9.297**, one folder `wp-content/themes/justccell-theme/` |
| Bio page | `/ccell-3-0/` · title **CCELL 3.0**; all legacy slugs 301 in; template-bound |
| ACF | 20 groups, Local JSON + GUI; 7 page groups template-bound; DB de-bloated |
| Catalog | **57 published WooCommerce products locked** (Rule §7.8) — nothing trashed/hidden |
| Commerce | Add-to-cart + AJAX drawer live for tier-priced SKUs; **paid card checkout not live** (Viva Smart Checkout + VAT pending) |
| Indexation | `noindex` sitewide (coming-soon, `blog_public=0`) — correct pre-launch |
| SEO stack | Rank Math + WPML + WCML + WPML SEO |
| Cross-sell | Justccell → Elite Terpenes free-delivery coupons live (`inc/elite-cross-sell.php`) |

---

## 12. Open backlog (hand-over)

| # | Item | Type | Owner |
|---|---|---|---|
| 1 | WooCommerce variation-image swap — capture a concrete failing SKU + colour + variation image state, then fix data or restore native reliability | ⏳ Code/Data | Cursor + Hermes |
| 2 | Correct "sample" wording at **source** in Rank Math postmeta (theme masks output; fix stored text) | ⏳ Content | Hermes / owner |
| 3 | Launch SEO: uncheck "Discourage search engines" + disable coming-soon + re-verify canonicals live on PDP/listing | ⚙️ Settings | Owner |
| 4 | Viva Smart Checkout (paid gateway) + VAT/accounts + UPS/FedEx shipping | ⏳ Feature | Owner + Cursor |
| 5 | Ownership transfer (Hostinger/Cloudflare/WP/domains/email → 3Devices) | ⚙️ Ops | Owner |
| 6 | Resolve remaining 3 ACF "Sync available" notices (pre-existing drift on untouched groups) | ⏳ Housekeeping | Cursor |
| 7 | First clone (eliteterpenez) using `framework-portability.md`; decide `justccell_` prefix strategy | ⏳ Feature | Owner + Cursor |

---

## 13. How to continue (next agent, read this first)

1. **Read order:** this report → [[websites/justccell.com/rules|rules.md]] (esp. §0.5, §0.6, ACF §, §7.5) → [[websites/justccell.com/features-code-map|features-code-map.md]] → [[websites/justccell.com/docs/STATUS|STATUS.md]].
2. **Every change syncs docs the same turn** (Rule §0.6). No exceptions.
3. **ACF:** GUI + Local JSON only; bind groups to the page **template**, never the slug.
4. **Bio page name/slug:** change only via the `justccell_bio_canonical_slug` / `justccell_bio_canonical_title` filters — never hardcode a slug.
5. **Deploy:** in-place TUS to `wp-content/themes/justccell-theme/` only; bump `JUSTCCELL_VERSION` in `functions.php` **and** `style.css`; clear cache; verify live; run `scripts/backup-theme.sh`; commit + tag.
6. **Never** create a second theme folder, add sample/"get samples" wording, leak `ccell.com`, or trash published SKUs to shrink the catalog.

---

## 14. Changed files index (0.9.297 delta)

**Theme code:** `functions.php`, `style.css`, `inc/page-layouts.php`, `inc/catalog-redirects.php`, `inc/setup.php`, `inc/acf.php`, `inc/chrome.php`, `inc/listing.php`, `inc/header-menu.php`, `inc/nav-fallback.php`, `inc/static-pages.php`, `inc/acf-product-clone-maintenance.php`, `template-parts/page/spotlight.php`.
**ACF JSON:** `acf-json/group_jc_about_page.json`, `…why_pages.json`, `…legal_pages.json`, `…locations_page.json`, `…generic_brand.json`, `…j3_page.json`, `…discover_hub.json`.
**Docs:** `rules.md`, `mastersheet.md`, `INDEX.md`, `features-code-map.md`, `AGENTS.md`, `docs/STATUS.md`, `docs/BUILD-LOG.md`, `docs/ROADMAP.md`, `docs/cms-editor-guide.md`, `docs/design-clone.md`, `docs/acf-local-json-migration.md`, `docs/AUDIT-REPORT-2026-09-06.md`, **new** `docs/framework-portability.md`, **new** `docs/OPUS-4.8-REPORT-AND-FIXES.md` (this file).

*Earlier deep audit: [[websites/justccell.com/docs/AUDIT-REPORT-2026-09-06|AUDIT-REPORT-2026-09-06.md]]. Dated ships: [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG.md]].*
