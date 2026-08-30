> **Parent Hub:** [[memory/procedural/INDEX|🛠️ Procedural Memory Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🔄 SOP: Rapid WordPress Site & Content Recovery

> **Distilled operational runbook for recovering compromised, damaged, or corrupted WordPress client sites.**

---

## ⚡ 1. Immediate Diagnostic Phase
1. **Check HTTP Status:** Test homepage, `/wp-json/wp/v2/posts`, and `/wp-admin`.
2. **Inspect Error Logs:** Access server error log via Hostinger API or SSH.
3. **Isolate Plugin / Theme Conflict:** Disable non-essential plugins via WP-CLI: `wp plugin deactivate --all`.

---

## 💾 2. Content & Media Restoration
1. **JSON Backup Extraction:** Locate the latest verified snapshot in `websites/<domain>/` (e.g. `tonicphysio-backup-FULL-2026-08-16.json`).
2. **REST API Bulk Ingestion:** Run the restoration script using application passwords from `master-env.env`.
3. **Verify URL Slugs & Taxonomies:** Ensure permalinks match the master URL registry.

---

## 🔒 3. Post-Recovery Hardening
- Re-enable LiteSpeed / Redis object cache.
- Run `content-pre-push-validator.py` to ensure 0 broken links and correct image URLs.
- Update `memory/YYYY-MM-DD.md` with incident post-mortem.
