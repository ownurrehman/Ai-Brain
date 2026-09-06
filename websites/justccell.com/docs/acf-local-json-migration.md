> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# ACF Pro Local JSON migration

Approved strategy for moving justccell.com field **definitions** from PHP arrays to **ACF Pro GUI + `acf-json/`**, without losing product/page data or breaking layouts.

**Status:** Phase 0–2 **complete**; Phase **2.5** + Phase **3 (all batches)** **complete** (2026-09-05, theme **0.9.236**). **Zero PHP field-array registrations remain.**

Related: [[websites/justccell.com/rules|rules.md]] · [[websites/justccell.com/features-code-map|features-code-map.md]] · [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG]]

---

## Target architecture

```
ACF Pro GUI (edit labels, tabs, instructions)
        ↓ save
acf-json/group_*.json  (version control, deploy with theme)
        ↓ load (read-only at runtime)
WordPress DB (ACF field-group posts — runtime cache)
        ↓
get_field() → wp_postmeta (values — NEVER change field name/key)
        ↓
Templates (unchanged)
```

**End state:** Zero PHP field-array overrides. `acf/load_field` and `acf/load_field_group` filters removed per group after JSON sync. Legacy **value** fallbacks (`clone_details`, `acf/load_value`) stay until all SKUs are re-saved.

---

## Why the 0.9.224 incident happened

`acf_import_field_group()` on admin load **replaced** the live Product group DB registry. Postmeta was **not** deleted, but ACF stopped reading values when field keys drifted. **Never** auto-import JSON on production again.

References:

- [ACF Local JSON](https://www.advancedcustomfields.com/resources/local-json/)
- [ACF Synchronized JSON](https://www.advancedcustomfields.com/resources/synchronized-json/)
- [ACF Register fields via PHP](https://www.advancedcustomfields.com/resources/register-fields-via-php/) — PHP defs are not GUI-editable; Local JSON is the correct distribution path.

---

## Phased rollout (approved order)

| Phase | Scope | Status |
|-------|--------|--------|
| **0** | Emergency export + postmeta baseline | ✅ Done 2026-09-05 |
| **1** | Deduplicate `group_jc_product_clone` | ✅ Done 2026-09-05 |
| **2** | Export all DB groups to `acf-json/` (read-only snapshot) | ✅ Done 2026-09-05 |
| **2.5** | Export PHP-only laser/location groups to `acf-json/` | ✅ Done 2026-09-05 |
| **3** | Remove PHP overrides one group at a time | ✅ **Complete** (Batch 4 shipped **0.9.236**) |
| **3 order** | Low-risk options/pages → laser groups → **Product group last** | Approved |

### Data safety rules (all phases)

1. Never change field **`name`** or **`key`** for fields with live postmeta.
2. Never run `acf_import_field_group()` automatically on live.
3. Use manual ACF Sync or WP-CLI with verification — not theme `admin_init` import.
4. Keep all legacy read fallbacks until data audit passes.
5. One group per deploy batch; verify admin + frontend before next group.

---

## Phase 0 — baseline (2026-09-05)

**Theme:** `0.9.228` · **Runner:** `inc/acf-migration.php` on first wp-admin GET (`manage_options`).

| Artifact | Location |
|----------|----------|
| Full field-group export (19 groups) | Live: `wp-content/uploads/justccell-acf-backups/acf-field-groups-2026-09-05-120736.json` |
| Vault copy | `backups/acf-field-groups-2026-09-05-120736.json` |
| Baseline option | `justccell_acf_postmeta_baseline` |

### Postmeta baseline (recorded)

| Metric | Count |
|--------|------:|
| Product posts (publish/draft/private) | 59 |
| `clone_*` value rows (excludes `_clone_*` refs) | 3,287 |
| Field groups exported | 19 |
| Product group fields in export | 24 |

---

## Phase 1 — product group dedup (2026-09-05)

**Before:** Two ACF list rows shared key `group_jc_product_clone` (“Product page” + “Product page clone”).

**After:**

- Single survivor: **Product page** (DB post ID `330066`, 24 fields).
- Orphan duplicate **moved to Trash** (not permanently deleted).
- `justccell_acf_recover_product_clone_field_refs()` ran on migration pass.
- ACF Field Groups list shows **one** Product page row only.

### Verification (live)

| Check | Result |
|-------|--------|
| ACF → Field Groups | One “Product page”; no “Product page clone” |
| Edit Product → Tank (ID 255) | Product page metabox: Banner, Heading, Product Tagline, Specs, etc. |
| Frontend `/all-in-ones/tank/` (admin session) | H1 Tank, H2 tagline, buy box, laser, EVOMAX section — no errors |

---

## Phase 2 — Local JSON export (2026-09-05)

**Theme:** `0.9.232` · **Runner:** `justccell_acf_run_migration_phase2()` in `inc/acf-migration.php` (one-time per theme version on wp-admin GET).

### What shipped

- Exported **15** unique active DB field groups to `justccell-theme/acf-json/group_{key}.json` (read-only schema snapshot).
- Removed theme hook that deleted `group_jc_product_clone.json` on version bump (`inc/acf.php`).
- `acf/settings/save_json` and `acf/settings/load_json` unchanged — both point to `JUSTCCELL_DIR/acf-json`.
- **PHP field-array overrides left intact** (Phase 3 only).
- Option `justccell_acf_migration_phase2` = `0.9.232`.

### Exported groups (15)

| Key | Title |
|-----|-------|
| `group_jc_about_page` | About page |
| `group_jc_contact_page` | Contact page content |
| `group_jc_discover_hub` | Discover hub |
| `group_jc_forms_options` | Forms |
| `group_jc_generic_brand` | Page content |
| `group_jc_header_menu_item` | Header menu item |
| `group_jc_header_options` | Header CTA |
| `group_jc_home_full` | Home page |
| `group_jc_j3_page` | CCELL 3.0 page (bio; bound to `page-templates/justccell-bio.php`) |
| `group_jc_legal_pages` | Legal pages |
| `group_jc_listing_page` | Listing page |
| `group_jc_page_sections` | Page sections |
| `group_jc_product_clone` | Product page |
| `group_jc_storefront` | Storefront |
| `group_jc_why_pages` | Why pages |

**Not in DB yet (PHP-local only — export before Phase 3):** ~~`group_jc_laser_page`, `group_jc_laser_engraving`, `group_jc_laser_engraving_cat`, `group_jc_laser_engraving_global`, `group_jc_locations_page`.~~ **Exported Phase 2.5** (2026-09-05, `0.9.233`); **PHP removed Phase 3 Batch 3** (`0.9.235`).

Phase 0 backup listed **19** groups because duplicate `group_jc_product_clone` rows still existed.

### Postmeta verification (unchanged)

| Metric | Phase 0 baseline | After Phase 2 |
|--------|------------------:|--------------:|
| `clone_*` value rows | 3,287 | **3,287** |

Admin notice on first wp-admin load after `0.9.232`: *"Wrote 15 field groups to acf-json/. clone_* postmeta rows: 3287 (unchanged: yes)."*

### ACF Local JSON column

wp-admin → **ACF → Field Groups** → **All (15)** — every row shows **Saved** under Local JSON (same state as `group_jc_page_sections`).

### Product group note (`group_jc_product_clone`)

- JSON snapshot includes **all field keys/names** from the live DB registry (**32** top-level field posts, including legacy orphans from the 0.9.224 recovery: `clone_banner_heading`, `clone_colours`, `clone_j3`, repeater sub-fields stored as flat siblings).
- Canonical PHP SSOT still defines **24** active product fields; `acf/load_field` overrides in `inc/acf.php` remain authoritative until Phase 3.
- Repeater `sub_fields` in JSON may be empty where DB parent refs point at the group post ID instead of the parent field key — **postmeta is unaffected**. Phase 3 will reconcile DB hierarchy when PHP overrides are removed (Product group **last**).

### Zero mutation rule

No field `key`, `name`, `type`, or conditional-logic rule was altered during export. No `acf_import_field_group()` on live.

---

---

## Phase 2.5 — PHP-only JSON export (2026-09-05)

**Theme:** `0.9.233` · **Runner:** `justccell_acf_run_migration_phase25()` in `inc/acf-migration.php` (one-time per theme version on wp-admin GET).

### What shipped

- Exported **5** PHP-only field groups from their `justccell_acf_*_group()` factories via `acf_prepare_field_group_for_export()` → `acf-json/group_{key}.json`.
- Extracted `justccell_acf_locations_page_group()` from inline registration in `inc/acf-page-groups.php` (callable required for export).
- Option `justccell_acf_migration_phase25` = `0.9.233`.
- **PHP arrays for laser/location groups unchanged** — JSON is snapshot only until a later Phase 3 batch removes those PHP defs.

### Exported groups (5)

| Key | Title | PHP source |
|-----|-------|------------|
| `group_jc_laser_page` | Laser page layout | `justccell_acf_laser_page_group()` · `inc/acf-catalog-pages.php` |
| `group_jc_laser_engraving` | Laser engraving (buy box) | `justccell_acf_laser_engraving_product_group()` · `inc/laser-engraving.php` |
| `group_jc_laser_engraving_cat` | Laser engraving defaults | `justccell_acf_laser_engraving_category_group()` · `inc/laser-engraving.php` |
| `group_jc_laser_engraving_global` | Laser Engraving | `justccell_acf_laser_engraving_global_group()` · `inc/laser-engraving.php` |
| `group_jc_locations_page` | Location page | `justccell_acf_locations_page_group()` · `inc/acf-page-groups.php` |

### Verification (live)

| Check | Result |
|-------|--------|
| wp-admin admin notice | *Wrote 5 PHP-only field groups to acf-json/ (ok).* |
| `acf-json/` file count | **20** `group_*.json` files (15 Phase 2 + 5 Phase 2.5) |
| JSON validity | All 5 files parse as valid JSON (vault + live) |
| ACF → Field Groups | Laser/location rows present; **3** groups show Sync available (DB vs JSON drift on pre-existing rows — expected until synced or PHP removed) |

---

## Phase 3 Batch 1 — low-risk PHP removal (2026-09-05)

**Theme:** `0.9.233` · **Scope:** options/pages groups already in DB + JSON from Phase 2.

### Groups migrated (PHP arrays removed)

| Key | Title | What changed |
|-----|-------|--------------|
| `group_jc_header_options` | Header CTA | Removed PHP array from `justccell_register_acf_header_menu()` — kept options sub-page + `group_jc_header_menu_item` PHP |
| `group_jc_storefront` | Storefront | Removed PHP array from `justccell_register_acf_storefront()` — kept options sub-page only |
| `group_jc_forms_options` | Forms | Removed PHP array from `inc/forms-settings.php` — kept options sub-page only |
| `group_jc_legal_pages` | Legal page | Gutted `justccell_register_acf_legal_pages()`; removed from `acf/init` bootstrap list |

**No** `acf/load_field` / `acf/load_field_group` overrides existed for these groups in `inc/acf.php`.

### Verification (live)

| Check | Result |
|-------|--------|
| Justccell → Storefront | All tabs render (Social, Collection, Buy box, Laser video, Footer, Landings) |
| Justccell → Header / Forms | Options screens load |
| Label edit persistence | Storefront **WhatsApp label** changed to `WhatsApp (Batch1 test)` → Update → hard reload → value persisted; restored to `WhatsApp` |
| Frontend | Site loads (no PHP fatals from missing group registration) |

---

## Phase 3 Batch 2 — medium-risk page PHP removal (2026-09-05)

**Theme:** `0.9.234` · **Scope:** 10 page/menu field groups already in DB + JSON from Phase 2.

### Groups migrated (PHP arrays + overrides removed)

| Key | Title | PHP source removed |
|-----|-------|-------------------|
| `group_jc_home_full` | Home page | `justccell_acf_home_page_group()` · `inc/acf-catalog-pages.php` |
| `group_jc_listing_page` | Catalog listing content | `justccell_acf_listing_page_group()` · `inc/acf-catalog-pages.php` |
| `group_jc_about_page` | About page | `justccell_acf_about_page_group()` · `inc/acf-page-groups.php`, `inc/acf-remaining-pages.php` |
| `group_jc_contact_page` | Contact page content | `justccell_register_acf_contact_page()` · `inc/acf-fields.php` |
| `group_jc_discover_hub` | Discover hub | `justccell_register_acf_discover_hub()` · `inc/acf-fields.php` |
| `group_jc_why_pages` | Why Justccell page | `justccell_acf_why_page_group()` · `inc/acf-page-groups.php`, `inc/acf-remaining-pages.php` |
| `group_jc_j3_page` | CCELL 3.0 page | `justccell_acf_j3_page_group()` · `inc/acf-page-groups.php` (seed defaults remain in `inc/bio-heating.php`) |
| `group_jc_generic_brand` | Page content | `justccell_acf_generic_brand_page_group()` · `inc/acf-catalog-pages.php`, `inc/acf-page-groups.php` |
| `group_jc_page_sections` | Page sections | No PHP array ever registered — JSON/DB only; one-time `acf/init` location scoping hook kept |
| `group_jc_header_menu_item` | Header menu item | PHP array removed from `justccell_register_acf_header_menu()` · `inc/acf-fields.php` |

### Hooks removed (`inc/acf.php`)

- Entire `acf/load_field_group` filter that re-injected home/listing/generic/about/why/contact/j3 group arrays.
- `acf/load_field` prefix-map filter for page field UI overrides.
- `justccell_acf_sync_group_field_ui()` calls for all Batch 2 groups (and J3 UI sync block).
- **Kept:** `justccell_acf_sync_group_field_ui` for `group_jc_laser_page` only; product clone `acf/load_field` + `admin_init` maintenance unchanged.

### Bootstrap after Batch 2 (`inc/acf-fields.php` `acf/init`)

Still registers PHP for: `laser_page`, `locations_page`, `product_clone`, `header_menu` (options sub-page only), `storefront` (options sub-page only).

`inc/acf-migration.php` PHP-only export callables reduced to `group_jc_product_clone` only.

Seed helpers (`justccell_*_seed_page_acf_content`, `justccell_home_page_text_defaults`, etc.) **kept** in `inc/acf-catalog-pages.php` and `inc/acf-remaining-pages.php`.

### Verification (live)

| Check | Result |
|-------|--------|
| Live theme version | `JUSTCCELL_VERSION` **0.9.234** in `functions.php` |
| ACF → Field Groups | All 10 Batch 2 groups present and editable (About, Contact, Discover, Catalog listing, Home, Why, J3, Page content, Page sections, Header menu item) |
| Label edit persistence | About page **Hero title** → `Hero title (Batch2 test)` → Update → hard reload → persisted; restored to `Hero title` |
| Frontend `/about/` | Renders full About layout (logged-in admin session) — no PHP fatals |

## Phase 3 Batch 3 — laser & locations PHP removal (2026-09-05)

**Theme:** `0.9.235` · **Scope:** 5 laser/location field groups exported in Phase 2.5.

### Groups migrated (PHP arrays + overrides removed)

| Key | Title | PHP source removed |
|-----|-------|-------------------|
| `group_jc_laser_page` | Laser page layout | `justccell_acf_laser_page_group()`, `justccell_acf_laser_page_field_map()`, `justccell_acf_sync_group_field_ui()` · `inc/acf-catalog-pages.php`, `inc/acf-page-groups.php`, `inc/acf.php` |
| `group_jc_laser_engraving` | Laser engraving (product) | PHP arrays + `justccell_acf_laser_engraving_product_field_map()` + `acf/prepare_field` hide filter · `inc/laser-engraving.php` |
| `group_jc_laser_engraving_cat` | Laser engraving (category) | PHP array · `inc/laser-engraving.php` |
| `group_jc_laser_engraving_global` | Laser Engraving (options) | PHP array · `inc/laser-engraving.php` |
| `group_jc_locations_page` | Location page | `justccell_acf_locations_page_group()`, `justccell_register_acf_locations_page()` · `inc/acf-page-groups.php`, `inc/acf-fields.php` |

### Hooks removed (`inc/acf.php`, `inc/laser-engraving.php`)

- `justccell_acf_sync_group_field_ui()` for `group_jc_laser_page`.
- Laser product `acf/prepare_field` filter that hid stale laser field keys.
- All five group PHP `acf_add_local_field_group()` registrations.

### Intentionally kept (not field-definition PHP)

- `inc/laser-engraving.php` — runtime cart/checkout helpers, options sub-page registration, tier/setup-fee resolvers.
- `inc/admin-laser-zone.php` — `acf/load_field_group` UX filter (hide cat group; limit product group to product edit screen).
- `justccell_laser_page_seed_layout()` in `inc/acf-catalog-pages.php`.
- Legacy location field `acf/prepare_field` hides in `inc/acf.php` (stale keys only).

### Bootstrap after Batch 3 (`inc/acf-fields.php` `acf/init`)

Still registered PHP for: **`product_clone`** (removed Batch 4), `header_menu` (options sub-page only), `storefront` (options sub-page only).

`inc/acf-migration.php` `justccell_acf_php_only_group_sources()` returns `[]` (historical Phase 2.5 only).

### Live sync (wp-admin)

Bulk-synced 3 JSON-only groups to DB: `group_jc_laser_engraving_global`, `group_jc_locations_page`, `group_jc_laser_page`. Product/cat laser groups were already in DB from prior PHP registration.

### Verification (live)

| Check | Result |
|-------|--------|
| Live theme version | `JUSTCCELL_VERSION` **0.9.235** in `functions.php` |
| ACF → Field Groups | 18 active groups; laser + location groups present; **Sync available (0)** after import |
| Justccell → Laser Engraving | Setup fee, WhatsApp toggle, tier matrix render from JSON+DB |
| Product edit (Blade) | `group_jc_laser_engraving` fields visible (Enable laser engraving editor, Engraving Canvas tabs) |
| Label edit persistence | `group_jc_laser_engraving_global` **Setup fee** → `Setup fee (Batch3 test)` → Update → hard reload → persisted; restored to `Setup fee` |
| Frontend buy box | `/cartridge/th2-evomax/` with `enable_engraving` toggled on for test: **Add on Laser Engraving** checkbox + incomplete-editor hint render; restored `enable_engraving` to off after test |

### Batch 4 (complete — 2026-09-05, theme **0.9.236**)

**`group_jc_product_clone` only** — final PHP teardown.

#### Removed

| Item | Location |
|------|----------|
| `justccell_acf_product_clone_group()` + `justccell_register_acf_product_clone()` | `inc/acf-fields.php` |
| `justccell_acf_product_clone_field_map()` | `inc/acf.php` |
| `acf/load_field_group` override for `group_jc_product_clone` | `inc/acf.php` |
| `acf/load_field` override for `field_jc_prod_*` | `inc/acf.php` |
| `acf/prepare_field` hide for unknown `field_jc_prod_*` keys (PHP map) | `inc/acf.php` |
| `admin_init` version-bump → `justccell_acf_maintain_product_clone_field_group()` | `inc/acf.php` |
| `justccell_acf_maintain_product_clone_field_group()` | `inc/cms-helpers.php` |
| PHP callable map in `justccell_acf_export_php_group_definition()` | `inc/acf-migration.php` (now reads `acf-json/*.json`) |

#### Kept (critical)

- `acf/load_value` fallbacks for `clone_detail_1` / `clone_detail_2` / `clone_detail_3` (`justccell_acf_load_product_detail_photo`) in `inc/cms-helpers.php`
- Legacy field hiding: `justccell_acf_legacy_product_clone_field_names()` / `justccell_acf_legacy_product_clone_field_keys()` + `acf/prepare_field` in `inc/acf.php`
- `justccell_acf_recover_product_clone_field_refs()` — refactored to `justccell_acf_product_clone_field_map_from_db()` (DB/JSON registry, not PHP array)
- `justccell_acf_highlight_text_color_field()` in `inc/cms-helpers.php` (feature-slide sub-field)

#### Bootstrap after Batch 4 (`inc/acf-fields.php` `acf/init`)

Registers PHP **only** for options sub-pages: `header_menu`, `storefront`. **No field-group PHP arrays.**

#### Live sync

No bulk sync required — `group_jc_product_clone` was already in DB from prior PHP registration. **Sync available (0)** on Field Groups list.

#### Verification (live)

| Check | Result |
|-------|--------|
| Live theme version | `JUSTCCELL_VERSION` **0.9.236** in `functions.php` + `style.css` |
| ACF → Field Groups → **Product page** | Group loads; **24** active editor fields (legacy keys hidden); **Sync available (0)** |
| Product edit (TH2-EVOMAX, post 261) | `clone_subtitle`, `clone_specs_heading`, buy-box-related ACF + laser tabs populate from postmeta |
| Label edit persistence | Group title **Product page** → `Product page (Batch4 test)` → Save Changes → hard reload → persisted; restored to **Product page** |
| Frontend | `/cartridge/th2-evomax/` — layout, specs, heating block, laser section, tier buy box (`Add to cart`, attribute selects) render without errors |

**Shipped (live TUS):** `functions.php`, `style.css`, `inc/acf-fields.php`, `inc/acf.php`, `inc/cms-helpers.php`

---

## Post-migration cleanup (2026-09-05, theme **0.9.243**)

- **606-field loop fix:** Removed `justccell_acf_recover_product_clone_field_refs()` and related helpers from `inc/cms-helpers.php`.
- **`group_jc_product_clone.json`:** Restored Phase 0 backup (**24** fields). Field order is **not** hardcoded by devs — client sorts via ACF GUI drag-and-drop (`rules.md`).
- **One-time import:** `justccell_acf_import_product_clone_from_local_json_once()` purges corrupted DB group on first admin load.
- **`group_jc_home_full.json`:** Hero slide repeater `sub_fields` (`image` / `url` / `alt`) — Home Slider media uploader.

## Post-migration cleanup (2026-09-05, theme **0.9.237**)

- **Deleted `inc/acf-migration.php`** — Phase 0–2.5 runner and `admin_notices` removed; dashboard clean.
- **`group_jc_product_clone.json`:** Flattened repeater sub-fields (Phase 2 export bug) restored from Phase 0 backup; top-level field order fixed (heading → banner → tagline → specs → media → details → highlights → heating → laser → catalog).
- **`group_jc_home_full.json`:** Hero slide repeater `sub_fields` restored (`image` / `url` / `alt`) — fixes broken Home Slider media uploader.

---

## Rollback

1. Restore ACF export: `backups/acf-field-groups-2026-09-05-120736.json` via **ACF → Tools → Import**.
2. Restore trashed duplicate from wp-admin Trash if needed.
3. Revert theme to pre-0.9.228 if migration code must be removed.

---

## Code map

| File | Role |
|------|------|
| `inc/acf-migration.php` | **Deleted 0.9.237** — was Phase 0–2.5 export/dedup runner |
| `inc/acf.php` | JSON paths; legacy product/location `acf/prepare_field` hides only (no `load_field` overrides) |
| `inc/acf-fields.php` | Options sub-pages only (`header_menu`, `storefront`) — **zero** field-group PHP arrays |
| `inc/forms-settings.php` | Forms options sub-page only (Batch 1) |
| `inc/acf-page-groups.php` | Legal-pages stub only (Batch 3 removed laser/locations factories) |
| `inc/acf-catalog-pages.php` | `justccell_laser_page_seed_layout()` + page seed helpers (laser page group PHP removed Batch 3) |
| `inc/laser-engraving.php` | Runtime laser engine + options sub-page only (group PHP removed Batch 3) |
| `inc/acf-remaining-pages.php` | About/why/contact seed helpers only (group PHP removed Batch 2) |
| `inc/cms-helpers.php` | Legacy `acf/load_value` fallbacks; `justccell_acf_legacy_product_clone_field_names()` — **no** recover/append helpers |
| `acf-json/` | **20** `group_*.json` files (Local JSON SSOT for definitions after Phase 3) |

Options: `justccell_acf_migration_phase0`, `justccell_acf_migration_phase1`, `justccell_acf_migration_phase2`, `justccell_acf_migration_phase25`, `justccell_acf_postmeta_baseline`, `justccell_acf_migration_report`.
