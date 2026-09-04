> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Status & Overview — eliteterpenez.com

**Last Updated:** 2026-09-04  
**Target Domain:** https://eliteterpenez.com/  
**Live Theme:** `eliteterpenez-theme` (design clone still scaffolding)  
**Hostinger:** `u984013785` · WordPress software id `30437919` (shared client account — not Justccell `u392808260`)  
**Current Phase:** Phase 1 theme clone continues. **Justccell → Elite coupon bridge is live.**

---

## Live Snapshot

| Component | Status | Details |
| :--- | :--- | :--- |
| **Site Environment** | Live WP + Woo, public coming soon | Hostinger shared / WordPress 6.x / PHP 8.3 / WooCommerce 11.1.0 |
| **Core Plugins** | Active | WooCommerce, ACF Pro, LiteSpeed Cache, **`justccell-coupon-bridge`** |
| **Theme Source** | Scaffolding | `websites/eliteterpenez.com/eliteterpenez-theme/` |
| **Cross-Site Bridge** | **Live (Justccell → Elite)** | Regular plugin `wp-content/plugins/justccell-coupon-bridge/` — not a mu-plugin. REST POST 201 verified 2026-09-04. Spec: [[websites/eliteterpenez.com/docs/cross-site-free-delivery|cross-site-free-delivery.md]] |
| **Public storefront** | Coming soon | Anonymous visitors see coming soon. Authenticated Woo REST coupon create works. Magic-link shop UX waits on public shop. |
| **Rules & Governance** | Updated 2026-09-04 | Live Hostinger IDs + plugin path in `rules.md`, `AGENTS.md`, `.cursorrules` |
| **Clone Target Status** | Reference Mapped | `https://abstraxtech.com/` visual architecture and zero-leak checklist ready |

---

## Active Tasks & Backlog

- [x] Create project directory and rules architecture.
- [x] Configure Cursor workspace rules (`.cursor/rules/eliteterpenez-*.mdc`).
- [x] Link project into master Obsidian vault index (`websites/index.md`, `INDEX.md`).
- [x] Establish `features-code-map.md` (Universal Zero-Latency Architecture Index).
- [x] Author full documentation suite: `ROADMAP`, `justccell-cross-sell`, `terpene-catalog-taxonomy`, `MEDIA-REPLACEMENT`, `accounts-vat`, `domains-email`, `security`, `ownership-control`, `visibility`.
- [x] Connect Hostinger MCP for this site (`u984013785` / WP `30437919`). File TUS `generateUploadURL` was 404 on shared access — plugin shipped via wp-admin zip.
- [x] Deploy `justccell-coupon-bridge` (regular plugin, active). Generate REST keys. Seed coupon-required Free shipping. Unblock REST during coming soon.
- [x] Justccell theme 0.9.219 creates `JC-{order_id}` coupons. Connection test succeeded.
- [ ] Reverse coupon (Elite order → Justccell `ET-{order_id}`) — **not started**.
- [ ] Initialize bespoke `eliteterpenez-theme` based on Abstrax Tech visual structure.
- [ ] Set up minimal compact ACF field groups for Homepage and Product clone templates.
- [ ] Public shop so `/?apply_coupon=` works for customers without logging in.
