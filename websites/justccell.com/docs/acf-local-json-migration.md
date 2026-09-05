> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# ACF Pro Local JSON migration

Approved strategy for moving justccell.com field **definitions** from PHP arrays to **ACF Pro GUI + `acf-json/`**, without losing product/page data or breaking layouts.

**Status:** Phase 0–2 **complete** (2026-09-05, theme **0.9.232**). **Phase 3 blocked** until manual sign-off.

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
| **3** | Remove PHP overrides one group at a time | ⏸ Blocked on Phase 2 sign-off |
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
| `group_jc_j3_page` | Just CCELL 3.0 page |
| `group_jc_legal_pages` | Legal pages |
| `group_jc_listing_page` | Listing page |
| `group_jc_page_sections` | Page sections |
| `group_jc_product_clone` | Product page |
| `group_jc_storefront` | Storefront |
| `group_jc_why_pages` | Why pages |

**Not in DB yet (PHP-local only — export before Phase 3):** `group_jc_laser_page`, `group_jc_laser_engraving`, `group_jc_laser_engraving_cat`, `group_jc_laser_engraving_global`, `group_jc_locations_page`.

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

## Phase 3 preview (not started)

Remove per group (in order):

1. `justccell_acf_*_group()` PHP array
2. Matching `acf/load_field` / `acf/load_field_group` entries in `inc/acf.php`
3. `justccell_acf_sync_group_field_ui()` / `justccell_acf_maintain_product_clone_field_group()` hooks

**Laser engraving** groups migrate to JSON **immediately before** Product group.

---

## Rollback

1. Restore ACF export: `backups/acf-field-groups-2026-09-05-120736.json` via **ACF → Tools → Import**.
2. Restore trashed duplicate from wp-admin Trash if needed.
3. Revert theme to pre-0.9.228 if migration code must be removed.

---

## Code map

| File | Role |
|------|------|
| `inc/acf-migration.php` | Phase 0–2 export/dedup (one-time per theme version) |
| `inc/acf.php` | JSON paths, PHP overrides (to be removed in Phase 3) |
| `inc/cms-helpers.php` | `justccell_acf_recover_product_clone_field_refs()`, legacy fallbacks |
| `acf-json/` | **15** `group_*.json` files (Local JSON SSOT for definitions after Phase 3) |

Options: `justccell_acf_migration_phase0`, `justccell_acf_migration_phase1`, `justccell_acf_migration_phase2`, `justccell_acf_postmeta_baseline`, `justccell_acf_migration_report`.
