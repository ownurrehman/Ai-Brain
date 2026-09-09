# 🌐 justccell.com — Knowledge & Strategy Hub

> **Parent Hub:** [[websites/index|🌐 Websites Portfolio Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]
> **Sister Store:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] (terpenes — live Justccell → Elite coupons)
> **Audit Rotation:** [[mastersheet|📋 Master Audit Sheet]]
> **Live theme:** **0.9.339** · **Plugin:** justCCELL Features **1.1.44** (2026-09-09) · **Dev paused** — [[websites/justccell.com/docs/dev-environment|production-only until go-live]] · [[websites/justccell.com/docs/theme-plugin-split|theme + plugin split]] · Snapshot: [[websites/justccell.com/docs/STATUS|STATUS]] · Log: [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG]]
> **Never crash admin again (Rule §0.8):** Never return non-array from `acf/load_field_group` — ACFML fatal. Safety plugin `jc-acfml-safety` is live. Gate: [[websites/justccell.com/docs/admin-fatal-smoke-test|admin edit-screen smoke test]] · Cursor: `.cursor/rules/justccell-acfml-fatal-guard.mdc`
> **Framework / cloning:** [[websites/justccell.com/docs/framework-portability|framework-portability.md]] — template-bound, config-driven; read before cloning to eliteterpenez / new stores.
> **Hand-over report:** [[websites/justccell.com/docs/OPUS-4.8-REPORT-AND-FIXES|Opus 4.8 Report & Fixes]] (read first) · **Deep audit:** [[websites/justccell.com/docs/AUDIT-REPORT-2026-09-06|Audit & fixes report (2026-09-06)]] · **Backups/restore:** [[websites/justccell.com/docs/backup-restore|backup-restore.md]]
> **Cross-sell spec:** [[websites/justccell.com/docs/elite-cross-sell|elite-cross-sell.md]] · [[websites/eliteterpenez.com/docs/cross-site-free-delivery|Elite-side spec]]

---

## 🏛️ Root Hub & Master Directives (Root Level Only)
*These core guidance files are maintained strictly at the root of `websites/justccell.com/`:*

- [[websites/justccell.com/mastersheet|Project Mastersheet]] (`mastersheet.md`) — Concise overview, infrastructure facts, and active backlog
- [[websites/justccell.com/rules|AI Coder Rules]] (`rules.md`) — Architectural non-negotiables, backend editability, and client policies
- [[websites/justccell.com/features-code-map|Features Code Map]] (`features-code-map.md`) — Codebase index, hook map, and include order (Rule §0.5)
- [[websites/justccell.com/AGENTS|Multi-Bot Agent Rules & Directives]] (`AGENTS.md`) — Multi-bot development mandates (Cursor, Grok, Hermes, Antigravity)
- [[websites/justccell.com/README|Repo Overview]] (`README.md`) — Quick start, team standards, and repository entry point
- `.cursorrules` — IDE instruction set and guardrails

---

## 📐 Architecture, Specifications & Status (`docs/`)
- [[websites/justccell.com/docs/STATUS|Live Status Snapshot]] (`docs/STATUS.md`) — Live versions, deployment state, and active component status
- [[websites/justccell.com/docs/BUILD-LOG|Build & Deployment Log]] (`docs/BUILD-LOG.md`) — Chronological changelog of releases and hotfixes
- [[websites/justccell.com/docs/ROADMAP|Product Roadmap]] (`docs/ROADMAP.md`) — Planned milestones, phase deliverables, and next priorities
- [[websites/justccell.com/docs/theme-plugin-split|Theme & Features Plugin Split]] (`docs/theme-plugin-split.md`) — Layer boundaries between `justccell-theme` and `plugins/justccell-features`
- [[websites/justccell.com/docs/architecture|Architecture Overview]] (`docs/architecture.md`) — System architecture, template hierarchy, and module design
- [[websites/justccell.com/docs/admin-fatal-smoke-test|Admin Fatal Smoke Test Gate]] (`docs/admin-fatal-smoke-test.md`) — ACFML fatal verification protocol
- [[websites/justccell.com/docs/backup-restore|Theme Backup & Restore Runbook]] (`docs/backup-restore.md`) — Backup procedures and rollback instructions
- [[websites/justccell.com/docs/framework-portability|Framework Portability]] (`docs/framework-portability.md`) — Guide for cloning architecture to eliteterpenez and future stores
- [[websites/justccell.com/docs/product-catalog|Product Catalog]] (`docs/product-catalog.md`) — Official launch-file locked SKU inventory
- [[websites/justccell.com/docs/justccell-weights|WooCommerce Weights & Dimensions]] (`docs/justccell-weights.md`) — Product shipping weight and dimension specifications
- [[websites/justccell.com/docs/woocommerce-build-plan-2026-09-01|WooCommerce Build Plan]] (`docs/woocommerce-build-plan-2026-09-01.md`) — Phased WooCommerce configuration roadmap
- [[websites/justccell.com/docs/laser-engraving-system|Laser Engraving System]] (`docs/laser-engraving-system.md`) — Buy-box custom engraving specification
- [[websites/justccell.com/docs/elite-cross-sell|Elite Terpenes Cross-Sell]] (`docs/elite-cross-sell.md`) — REST coupon bridge and shipping perks
- [[websites/justccell.com/docs/client-requirements|Client Requirements]] (`docs/client-requirements.md`) — Business rules and verified client requests
- [[websites/justccell.com/docs/cms-editor-guide|CMS Editor Guide]] (`docs/cms-editor-guide.md`) — wp-admin field mapping and page creation guide
- [[websites/justccell.com/docs/accounts-vat|Accounts, B2B/B2C, and VAT]] (`docs/accounts-vat.md`) — Tax and checkout customer rules
- [[websites/justccell.com/docs/dev-environment|Dev Environment Guide]] (`docs/dev-environment.md`) — Production-only deployment protocol (dev paused)
- [[websites/justccell.com/docs/geo-language-currency|Geo, Language, and Currency]] (`docs/geo-language-currency.md`) — Multi-region and localization setup
- [[websites/justccell.com/docs/domains-email|Domains and Email]] (`docs/domains-email.md`) — Domain DNS and transactional email config
- [[websites/justccell.com/docs/security|Security Architecture]] (`docs/security.md`) — Security guidelines and hardening
- [[websites/justccell.com/docs/visibility|Visibility Controls]] (`docs/visibility.md`) — Coming soon gate and launch controls
- [[websites/justccell.com/docs/design-clone|Design Clone Checklist]] (`docs/design-clone.md`) — Visual parity and styling checklist
- [[websites/justccell.com/docs/MEDIA-REPLACEMENT|Media Replacement Workflow]] (`docs/MEDIA-REPLACEMENT.md`) — Workflow for swapping staging assets
- [[websites/justccell.com/docs/acf-local-json-migration|ACF Local JSON Migration]] (`docs/acf-local-json-migration.md`) — ACF field group syncing
- [[websites/justccell.com/docs/open-questions|Open Questions]] (`docs/open-questions.md`) — Pending client decisions and technical questions
- [[websites/justccell.com/docs/cursor-ccell-3-0-mega|CCELL 3.0 Mega-Menu Brief]] (`docs/cursor-ccell-3-0-mega.md`) — Implementation notes for CCELL 3.0 navigation
- [[websites/justccell.com/docs/hermes-prompts-product-catalog|Hermes Catalog Cut Prompts]] (`docs/hermes-prompts-product-catalog.md`) — ⚠️ Obsolete (57-SKU locked)
- [[websites/justccell.com/docs/redirect-map-catalog-cut|Catalog Cut Redirect Map]] (`docs/redirect-map-catalog-cut.md`) — ⚠️ Obsolete (rules §7.8)

---

## 📊 Audits, Reports & Quality Assurance (`reports/` & `docs/`)
- [[websites/justccell.com/docs/OPUS-4.8-REPORT-AND-FIXES|Opus 4.8 Report & Fixes]] (`docs/OPUS-4.8-REPORT-AND-FIXES.md`) — Full engagement handover report
- [[websites/justccell.com/docs/AUDIT-REPORT-2026-09-06|Full Audit & Fixes Report (2026-09-06)]] (`docs/AUDIT-REPORT-2026-09-06.md`) — Deep dive into components, bug fixes, and open backlog
- [[websites/justccell.com/docs/website-audit-brief-2026-09-06|Website Audit Brief (2026-09-06)]] (`docs/website-audit-brief-2026-09-06.md`) — Read-only QA brief and inspection checklist
- [[websites/justccell.com/reports/justccell-product-images-audit|Product Image Inventory Audit]] (`reports/justccell-product-images-audit.md`) — Audit of all 58 published product image attachments
- [[websites/justccell.com/reports/homepage-custom-gallery-report-2026-09-01|Homepage Custom Gallery Report]] (`reports/homepage-custom-gallery-report-2026-09-01.md`) — Classic Customization gallery media assignment log
- [[websites/justccell.com/reports/pdp-responsive-audit-2026-09-08|PDP frontend responsive audit (2026-09-08)]] (`reports/pdp-responsive-audit-2026-09-08.md`) — Stage slider + tablet/phone gallery check

---

## ✍️ Content & Client Data (`content/` & `csvs/`)
- [[websites/justccell.com/docs/post-registry|Discover Post Registry]] (`docs/post-registry.md`) — Blog posts index and publishing registry
- [[websites/justccell.com/csvs/CLIENT-FILL-GUIDE|Client Fill Guide (Prices & Stock)]] (`csvs/CLIENT-FILL-GUIDE.md`) — Instructions for bulk price/inventory filling
- `content/discover-2026/` — Markdown source files for Discover blog posts
- `csvs/` — CSV product catalogs, inventory lists, and media manifests

---

## 📦 Staged Media Packs
- [[websites/justccell.com/media-upload-ready/README|Media Upload Ready Pack]] (`media-upload-ready/`) — Staged images, upload manifests, and attachment reports
- [[websites/justccell.com/media-replace-ready/README|Media Replace Ready Pack]] (`media-replace-ready/`) — Replacement assets and staging manifest

---

## ⚙️ Codebases, Deployments & Backups
- `justccell-theme/` — Custom WordPress theme source code (templates, assets, `acf-json/`, WooCommerce overrides)
- `plugins/justccell-features/` — `justCCELL Features` plugin source code (all business logic, hooks, REST, AJAX)
- `_deploy/` — Production deployment scripts and drop-in file manifests
- `backups/` — ACF field group emergency backups (`backups/INDEX.md`)
- `scripts/` — Theme backup scripts (`scripts/backup-theme.sh`)
- `archive/` — Historical theme releases archive (`archive/theme-releases/`)
- `sister-sites/eliteterpenez/` — Elite Terpenes integration resources
