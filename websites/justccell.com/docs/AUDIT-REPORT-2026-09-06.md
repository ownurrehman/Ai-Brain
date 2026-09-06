> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/justccell.com/rules|📜 Site Rules]]

# justccell.com — Full audit & fixes report (2026-09-06)

**Auditor:** Principal WordPress Architect pass (Cursor / Opus).
**Scope:** Live-verified against `justccell.com` via Hostinger MCP + logged-in browser (coming-soon bypass), cross-checked with theme source `justccell-theme/`.
**Theme at report time:** **0.9.296** (live-verified).
**Reference for design intent:** ccell.com (private QA only — never linked publicly, Rule §0.10).

This is a hand-over document for the agent team (Cursor, Grok, Hermes, Antigravity). Everything marked **✅ Fixed** is already live. Everything marked **⏳ Open** needs a follow-up. Everything marked **⚙️ Settings** is a wp-admin/plugin action, not theme code.

---

## 0. Executive summary

The theme is architecturally sound: modular `inc/*.php` controllers, ACF via Local JSON + GUI, WooCommerce-first data, in-place TUS deploys, one theme folder. The audit found **no runaway loops or duplicate-field bugs still active in code** — but it did find **historical residue in the database**, **two stale governance contradictions**, **SEO dedupe/indexation gaps**, and **no formal theme-backup system**. All code-side issues found were fixed and shipped (0.9.293 → 0.9.296). Remaining items are a WooCommerce variation-image repro, launch-time SEO toggles, and paid checkout — none are regressions.

Live-verified truth (not just local files): the whole site currently returns `noindex, nofollow` with no canonical, because **Settings → Reading → "Discourage search engines" is ON** (`blog_public = 0`) for pre-launch. This is correct and expected while coming-soon.

---

## 1. Phase 1 — Rule pruning & AI-Brain cleanup

**Finding:** `rules.md` already mandated ACF via GUI + Local JSON, but two secondary governance files still carried **contradictory legacy instructions** that would make agents make mistakes:

| File | Stale instruction | Truth (rules.md + code) | Status |
|---|---|---|---|
| `.cursorrules` | "ACF field … registered in `inc/acf-*.php`" | Fields = GUI + `acf-json/` only; `inc/acf-*.php` = plumbing | ✅ Fixed |
| `.cursorrules` | Canonical bio URL = `/justccell-3-0/` | Canonical = **`/ccell-3-0/`** (client rename 2026-09-06, 0.9.297); all legacy slugs incl. `/cell-3-0/` 301 there (`justccell_bio_canonical_slug()`) | ✅ Fixed |
| `AGENTS.md` §5 | Canonical bio URL = `/justccell-3-0/` | Canonical = **`/ccell-3-0/`** · title **CCELL 3.0** | ✅ Fixed |
| `AGENTS.md` (prior) | ACF field-array phrasing | MASTER ACF RULE: no PHP `acf_add_local_field_group()` | ✅ Fixed (prior turn) |

**Also:** removed dead `justccell_acf_register_field_group()` from `inc/acf.php` (defined, zero callers — a leftover PHP field-registration stub). ✅ Fixed (0.9.293).

**Master rule now enforced everywhere:** *All ACF Pro fields are managed strictly via the wp-admin GUI and synced via Local JSON (`acf-json/`). Hardcoded PHP field registration is forbidden. `inc/acf-*.php` is plumbing only (JSON paths, location-rule filters, `acf/prepare_field` UI tweaks, one-time repair helpers).*

---

## 2. Phase 2 — ACF Pro & GUI enforcement

- **Field registration:** 20 live field groups load from Local JSON + DB. No `acf_add_local_field_group()` field arrays remain in `inc/acf-*.php`. ✅
- **Location rules:** most groups bind by **page slug** via the custom `justccell_page_slug` rule (template/layout-aware through `justccell_page_layout_matches_slug()`), not raw `page_template`. This is robust and already works; converting to `page_template` was judged a **risky lateral move** and deliberately **not** done. Documented in BUILD-LOG. ✅ (kept by design)
- **Rogue field mutators:** no active `acf/load_field` reordering, `acf_update_field()` loops, or version-bump field appenders. The old repair hooks are **dormant** (their run-once option flags are set). ✅
- **Live DB de-bloat (0.9.293):** the ACF *database* carried import residue invisible at runtime (Local JSON wins on load):
  - Field-group posts: `23` (incl. 3 trashed `group_jc_product_clone__trashed`) → **20** (0 trashed).
  - `acf-field` rows: `825 publish + 35 trash` → **433 publish, 0 trash** (427 orphan/duplicate rows removed).
  - Duplicate field keys: `60` (e.g. `field_jc_prod_banner` ×14) → **0**.
  - Fix: one-time, admin-only, conservative purge `justccell_acf_purge_trashed_and_orphan_fields()` — deletes trashed groups + their trees and only `acf-field` posts whose top ancestor is **not** a live group. Live fields and all product `clone_*` postmeta untouched. Guarded by `justccell_acf_orphan_purge_293`; self-disables after one run. ✅ Shipped + verified live.

---

## 3. Phase 3 — Template & dependency architecture

- **Hierarchy:** virtual product/listing routes use custom query vars (`justccell_product` / `justccell_listing`) + `template_include`; PDP = `template-parts/product/clone.php`, listing = `catalog-clone.php`. Native Woo endpoints (cart/checkout/account) keep core layout. ✅
- **Enqueues:** assets registered in `inc/assets.php`, conditioned per template/context (e.g. `product-spin` + `product` + `wc-add-to-cart-variation` on PDP only; Woo branding CSS on cart/checkout/account only). No global asset bloat found. ✅
- **Admin isolation:** storefront bundles are not enqueued on `admin_enqueue_scripts`; only narrowly-scoped ACF/mapper CSS in wp-admin (Rule §13). ✅
- **SEO markup:** JSON-LD Organization/WebSite/Breadcrumb/Product handled by Rank Math; theme defers Organization to the active SEO plugin to avoid a duplicate node (see §5). Heading ladder (one H1, H2 tagline, H3 specs) intact. ✅

---

## 4. Phase 4 — Conflict resolution & server optimization

- **Runaway loops (`admin_init` / `save_post`):** none active. ACF repair machinery is dormant; the new purge self-disables; legacy-field hiding uses `acf/prepare_field` only on safe admin GET renders (never during POST/AJAX/validation) so it cannot strip the save registry or trip the "nonce failed verification" bug (Rule §7.7). ✅
- **Query efficiency:** catalog reads go through `justccell_catalog_from_woo()`; no nested `WP_Query` anti-patterns found in the audited paths. ✅
- **Orphaned parts / conflicting CSS:** Woo core CSS retained on endpoints; theme `woocommerce.css` is branding-only. No core-style clobbering found. ✅

---

## 5. SEO audit & fixes

### ✅ Fixed (shipped 0.9.294 / 0.9.295)
- **Duplicate Organization schema:** theme emitted its own Organization JSON-LD *and* Rank Math did. Theme now **defers** to an active SEO plugin (`RANK_MATH_VERSION` / `WPSEO_VERSION` / `AIOSEO_VERSION`); filter `justccell_force_org_schema` to force the theme node. Verified live: **one** Organization node per page.
- **Meta-description length + policy:** `justccell_clamp_meta_description()` on `rank_math/frontend/description` (+ `wpseo_metadesc`) trims to ~155 chars on a word boundary and **scrubs banned "sample(s)" → "quote(s)"** (a live PDP meta still said "Request a Just CCELL sample"). Theme masks it at output; **source text in Rank Math postmeta should still be corrected** (⏳ content task).
- **Empty image alt backfill:** `wp_get_attachment_image_attributes` fills empty `alt` from attachment alt/title → parent post title (never overrides an editor-set alt).

### ✅ Fixed (shipped 0.9.296) — canonical on virtual routes
- **Root cause of "no canonical":** the whole site is `noindex` (see ⚙️ below); Rank Math correctly omits `<link rel="canonical">` on noindexed URLs. **Not a Rank Math bug.**
- **Real code gap:** even once indexing is on, virtual PDP/listing routes have `is_singular=false` and no queried object, so Rank Math cannot self-canonicalize them. Added `justccell_rank_math_view_canonical()` on `rank_math/frontend/canonical` (+ `wpseo_canonical`) in `inc/product-pages.php` → returns `justccell_product_url($slug)` / `justccell_category_url($listing)`. Fills only the empty value; leaves normal pages to Rank Math.

### ⚙️ Settings (launch levers — not theme code)
- **Site-wide `noindex`:** Settings → Reading → **uncheck "Discourage search engines from indexing this site"** (`blog_public = 1`) at launch, and disable coming-soon. Canonicals + indexing then return automatically on all standard pages; the theme filter covers the virtual routes.
- **Rank Math meta:** per-page title / focus keyword / description are being authored by the owner in Rank Math — correct workflow.
- **Historical note:** the `/battery/ /pod-system/ /cartridge/ /all-in-ones/` archives once emitted Discover's meta + noindex; fixed earlier (0.9.56 router now queries the real page objects). Confirm at launch.

---

## 6. WooCommerce native-behaviour audit

- **Variation image swap:** the theme **bypasses** the native Woo gallery on PDPs with a custom JS gallery (`product.js` `bindVariationGallery()`) layered over the 360° spin (`product-spin.js`). This is the source of the intermittent "image doesn't change on variation click" the client reported. Live tests **mostly PASS**; failures appear tied to **per-variation image data** (variations without an assigned image), not a clear code bug.
  - **⏳ Open (id d5):** needs one concrete, reproducible SKU + colour where the swap fails, with the variation's image state. Then decide: (a) restore native Woo variation-image reliability for still images while keeping the 360° drag, or (b) patch the data. Deferred pending repro so we don't destabilise working PDPs.
- **Attribute model:** colours/variations use native Woo **Attributes** + variations (never legacy `clone_colours`, Rule §7.7). ✅
- **"Basket" → "cart" wording** and hidden internal laser meta keys are handled via filters, not core edits. ✅

---

## 7. Data-preservation audit (no lost page content)

- All version-gated seeders verified **seed-on-empty** — they never clobber a field the client has filled. ✅
- Copy-policy scrubbers (sample wording) are one-time-flagged and already run; they only replace banned sample language. ✅
- The ACF DB purge (§2) touched only trashed/orphan **registry** posts — **zero** product `clone_*` postmeta or live field values removed. ✅
- 57 published WooCommerce products remain the locked catalog (Rule §7.8) — nothing trashed/hidden. ✅

---

## 8. Vault, backups & declutter (this pass)

- **Backup system (new):** three layers documented in [[websites/justccell.com/docs/backup-restore|backup-restore.md]] — git history (lossless primary), `archive/theme-releases/` rotating **last 10** snapshots via `scripts/backup-theme.sh`, and Hostinger UpdraftPlus (site + DB). Seeded `0.9.296`. Tangible local restore points now: `0.9.202/203/205/296` + zips `0.9.93/122/140/144` (8) + git history.
- **Declutter:** removed obsolete server-side scripts `jc-cleanup.php` / `jc-extract-theme.php` / `jc-wp-test.php` (hardcoded key, restore-era one-offs — in git history if needed); relocated 4 root theme zips (~173 MB) into the gitignored `archive/theme-releases/_zips/` (repo tree slimmed, restore points kept); purged `.DS_Store`.
- **Sync:** `STATUS.md`, `BUILD-LOG.md`, `features-code-map.md`, `rules.md`, `mastersheet.md`, `INDEX.md`, `AGENTS.md`, `.cursorrules`, `README.md` all updated to **0.9.296** and consistent this pass.

---

## 9. Open items (hand-over backlog)

| # | Item | Type | Owner |
|---|---|---|---|
| 1 | WooCommerce variation-image swap — capture a concrete failing SKU+colour + variation image state, then fix data or restore native reliability | ⏳ Code/Data | Cursor + Hermes |
| 2 | Correct "sample" wording at **source** in Rank Math postmeta (theme masks it, but fix the stored text) | ⏳ Content | Hermes / owner |
| 3 | Launch SEO: uncheck "Discourage search engines", disable coming-soon, re-verify canonicals live on PDP/listing | ⚙️ Settings | Owner |
| 4 | Viva Smart Checkout (paid gateway) + VAT/accounts + UPS/FedEx shipping | ⏳ Feature | Owner + Cursor |
| 5 | Ownership transfer (Hostinger/Cloudflare/WP/domains/email to 3Devices) | ⚙️ Ops | Owner |

---

## 10. Verification method (so this isn't "local-only")

- Live theme version read from the server: `hosting_getWebsiteFileContentV1` → `functions.php` = `0.9.296`.
- Robots/canonical read from rendered pages via logged-in browser CDP on `/`, `/all-in-ones/`, `/cell-3-0/`, `/all-in-ones/tank/` → all `noindex, nofollow`, no canonical; `blog_public = 0` confirmed on `options-reading.php`.
- ACF DB counts verified live via diagnostic mu-plugin + phpMyAdmin before/after the purge.
- Files shipped in place via TUS to `wp-content/themes/justccell-theme/`, cache cleared, re-read to confirm.

*Dated ship notes: [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG.md]]. Live snapshot: [[websites/justccell.com/docs/STATUS|STATUS.md]]. Architecture: [[websites/justccell.com/rules|rules.md]]. Code map: [[websites/justccell.com/features-code-map|features-code-map.md]].*
