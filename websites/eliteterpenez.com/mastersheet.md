> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[mastersheet|📋 Master Audit Sheet]]

# Elite Terpenes — Project Mastersheet

## 📌 Project Overview & Metadata

| Field | Value | Notes |
| :--- | :--- | :--- |
| **Site Name** | **Elite Terpenes** | Premium Botanical & Strain-Specific Terpenes |
| **Domain** | https://eliteterpenez.com/ | Primary Live Domain |
| **Client** | 3Devices / Mr Nas | Same client as Just CCELL |
| **Sister Store** | https://justccell.com/ | Hardware, Vapes & Pods (`websites/justccell.com/`) |
| **Design Reference** | https://abstraxtech.com/ | Abstrax Tech (Zero Public Footprint Mandate) |
| **Hosting Platform** | Hostinger Business / Cloud | Hostinger MCP Managed (`u392808260`) |
| **Tech Stack** | WordPress + WooCommerce + ACF Pro | Custom `eliteterpenez-theme` |
| **Live Theme Directory** | `wp-content/themes/eliteterpenez-theme/` | Single live folder, in-place TUS deploy |
| **Key Synergy** | 48-Hour Cross-Store Free Delivery | REST API coupon creation + session bridge |

---

## 🎯 Implementation Sprint Roadmap

| Phase | Milestone | Focus Areas | Status |
| :--- | :--- | :--- | :--- |
| **Phase 1** | **Repository & Rules Setup** | Directory structure, `rules.md`, `AGENTS.md`, `.cursorrules`, Cursor MDC rules, Obsidian graph hubs | ✅ Complete |
| **Phase 2** | **Cross-Site Free Delivery Bridge** | REST API bootstrap, `justccell-coupon-bridge` mu-plugin, Action Scheduler coupon generator | 🟡 In Progress |
| **Phase 3** | **Design Clone & Theme Scaffolding** | Replicate `abstraxtech.com` visual aesthetic, header, footer, typography, color tokens, zero source footprint | ⚪ Scheduled |
| **Phase 4** | **Homepage Build & 100% Backend Sync** | Hero banner, aroma categories, featured terpene rails, minimal compact ACF mapping | ⚪ Scheduled |
| **Phase 5** | **WooCommerce Catalog & Strain PDPs** | Terpene profiles, native attributes (Aroma, Dominant Terpenes, Strain Type), lab specs, gallery | ⚪ Scheduled |
| **Phase 6** | **Cart, Checkout & Final QA** | 48h free delivery coupon testing, performance audit (LCP/CLS), cache warming, client review | ⚪ Scheduled |

---

## 🏛️ Core Principles & Non-Negotiables

1. **Zero Blind Custom Code:** WordPress core and WooCommerce native features are first-class citizens. Avoid custom database tables or bespoke logic when core hooks exist.
2. **100% Backend Content Editability:** Every page headline, text paragraph, CTA button label, URL, and media item must be backend-editable via native fields or mapped ACF Pro fields.
3. **Compact ACF UI:** Keep backend edit screens clean and minimal. Use ACF tabs, 2-line textareas (`'rows' => 2`), table repeaters, and attachment ID returns.
4. **Zero Leftover / Ghost Fields:** Strict 1:1 synchronization between frontend and backend. Purge dead fields immediately upon redesign.
5. **Zero Footprint of Abstrax Tech:** Zero occurrences of "Abstrax" in live HTML, CSS, JS, JSON-LD, or media filenames.
6. **Obsidian Graph Integrity:** Every `.md` file starts with Line 1 breadcrumb; zero naked hashes in prose.
