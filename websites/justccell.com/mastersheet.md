> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Justccell — Project Mastersheet

Concise, current project overview. **Dated history lives in [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG.md]]** (append-only) and git history — this sheet stays short and current.

## Site overview
- **Name:** Justccell · **Client:** 3Devices LTD (UK)
- **URL:** https://justccell.com/ · **Dev:** https://dev.justccell.com/ ([[websites/justccell.com/docs/dev-environment|dev-first policy]])
- **Tagline:** "Your Global Partner in Vaporization Hardware"
- **Theme (live):** **0.9.302** · source of truth `justccell-theme/`
- **Sister store:** [[websites/eliteterpenez.com/INDEX|eliteterpenez.com]] — live Justccell → Elite 48h free-delivery coupons ([[websites/justccell.com/docs/elite-cross-sell|spec]])

## Start-here docs
- [[websites/justccell.com/docs/OPUS-4.8-REPORT-AND-FIXES|Opus 4.8 Report & Fixes]] — **read first**; full hand-over of the whole engagement
- [[websites/justccell.com/docs/AUDIT-REPORT-2026-09-06|Full audit & fixes report (2026-09-06)]] — current state + open backlog
- [[websites/justccell.com/rules|rules.md]] — architecture brain (read before coding)
- [[websites/justccell.com/features-code-map|features-code-map.md]] — file/hook/meta map (Rule §0.5, read first)
- [[websites/justccell.com/docs/STATUS|STATUS.md]] — live snapshot · [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG.md]] — dated ships
- [[websites/justccell.com/docs/dev-environment|dev-environment.md]] — **dev-first deploy** (dev.justccell.com)
- [[websites/justccell.com/docs/backup-restore|backup-restore.md]] — theme backup + rollback runbook
- [[websites/justccell.com/docs/admin-fatal-smoke-test|admin-fatal-smoke-test.md]] — mandatory pre/post-deploy gate for ACF/WPML changes (never return non-array from `acf/load_field_group`; `jc-acfml-safety` plugin is the net)
- [[websites/justccell.com/docs/framework-portability|framework-portability.md]] — cloning this theme to eliteterpenez + future stores (template-bound, config-driven)
- [[websites/justccell.com/AGENTS|AGENTS.md]] · `.cursorrules` — multi-bot directives

## Access & infrastructure (unique facts)
- **Hostinger:** user `u392808260`, client_id `36554880`, created 2026-08-14. WP software id `30055979`.
- **Server root:** `/home/u392808260/domains/justccell.com/public_html`
- **Sister (Elite) Hostinger:** user `u984013785`, WP `30437919` — **never** mix hosts / patch Elite files here.
- **CDN/DNS:** Cloudflare proxied.
- **WordPress REST:** https://justccell.com/wp-json/wp/v2/ — admin user `rankrayofficial@gmail.com` (ID 1, "Rank Ray"). App password in `master-env.env` as `JUSTCCELL_WP_APP_PASS` (never commit).
- **SEO plugin:** Rank Math (REST write `/wp-json/rankmath/v1/updateMeta`, keys `rank_math_title` + `rank_math_description`).
- **Hostinger access for agents:** via shared OAuth / Hostinger MCP in Cursor.

## Current status (2026-09-06)
- **Theme 0.9.301 live.** Recent: removed Storefront **Store landings** (Spain/CH separate sites). Prior: native 18+ age gate (0.9.300).
- **Catalog:** **57 published WooCommerce products locked** (Rule §7.8) — 21 core + 36 imported, all permanent. The 2026-09-02 "catalog cut" playbooks are **obsolete**; never trash/301 published SKUs to shrink the catalog.
- **Commerce mode:** Add to cart + AJAX drawer live. **Dev-first:** code ships to dev.justccell.com; prod on promote. **Paid checkout not live** — Viva sandbox on dev first.
- **Indexation:** site is `noindex` (coming-soon, `blog_public=0`) — correct pre-launch; canonicals return at launch (§7.11).
- **Backups:** git history (primary) + `archive/theme-releases/` rotating 10 (`scripts/backup-theme.sh`) + Hostinger UpdraftPlus.

## Open backlog
See [[websites/justccell.com/docs/AUDIT-REPORT-2026-09-06|audit report §9]]: (1) WooCommerce variation-image repro, (2) correct "sample" wording at source in Rank Math, (3) launch SEO toggles, (4) Viva checkout + VAT + shipping, (5) ownership transfer to 3Devices.

## Hard rules (do not regress)
100% backend content editability · ACF via GUI + Local JSON only (no PHP field arrays) · Media Library only · zero ccell.com footprint · zero "samples" wording · one theme folder · canonical bio URL `/ccell-3-0/` (title **CCELL 3.0**) · ACF groups bound to page **template** not slug · coming-soon ON until owner says otherwise · Obsidian synced same turn as code (rules.md §0.6).
