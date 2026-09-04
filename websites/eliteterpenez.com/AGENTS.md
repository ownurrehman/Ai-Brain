# AGENTS.md — eliteterpenez.com Multi-Bot Development Rules

> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/eliteterpenez.com/rules|📜 Site Rules]]
> **Target Site:** https://eliteterpenez.com/ · **Directory:** `websites/eliteterpenez.com/`  
> **Master Rules File:** [[websites/eliteterpenez.com/rules|rules.md]] (MANDATORY READ FOR ALL BOTS: Cursor, Grok, Hermes, Antigravity)  
> **Theme Working Copy:** `eliteterpenez-theme/`  
> **Sister Site:** https://justccell.com/ (`websites/justccell.com/`)

---

## 🚨 TOP DIRECTIVES FOR ALL AI AGENTS (CURSOR, GROK, HERMES, ANTIGRAVITY)

### 1. HARD MANDATE: No Blind Extra Custom Coding
- **Lean WordPress & WooCommerce Native Core First:**
  - Do NOT invent custom tables, complex custom logic, or redundant custom post types when WordPress core or WooCommerce already supports the functionality.
  - Custom code is strictly reserved for instances where native WP/Woo cannot accomplish the requirement.
  - Utilize standard WooCommerce product structures, attributes, taxonomies, and hooks before writing bespoke PHP controllers.

### 2. HARD MANDATE: 100% Backend Content Editability
- **Every page and section must be editable in the WordPress backend edit area (`Pages → Edit Page` / `Products → Edit Product` / `Posts → Edit Post`) without touching code.**
- **Hierarchy:**
  1. **Native WP & WooCommerce First:**
     - Page Title, Post Content / Gutenberg editor, Excerpt, Featured Image, Menus.
     - WooCommerce Product Title, Short Description, Long Description, Regular/Sale Price, Product Attributes (Aroma Profile, Strain Type, Dominant Terpenes), Categories, Tags, Gallery.
  2. **ACF Pro Mandatory for Custom Sections & Layouts:**
     - If native fields cannot accommodate the design (e.g. aroma wheel diagrams, custom terpene cards, split scientific showcases, multi-button CTAs):
     - **You MUST create and map ACF Pro fields directly to that page or product backend edit screen.**
     - Synchronize definitions in `eliteterpenez-theme/acf-json/` and register in `inc/acf-*.php`.
- **Strictly Forbidden:**
  - ❌ **NO hardcoding of text in PHP:** Never output fixed marketing copy, titles, or paragraphs directly in PHP templates.
  - ❌ **NO hardcoding of button text:** Never hardcode `<a class="btn">Shop Terpenes</a>`. Button text and URLs must be wired to backend fields.
  - ❌ **NO client-facing copy in CSS or JS:** No CSS `content: "..."`, no hardcoded strings in frontend scripts.
  - ❌ **PHP fallbacks are defaults only:** `$text = get_field('...') ?: 'Default';` is permitted solely as a fallback when the database field is empty. Backend fields must ALWAYS override defaults.

### 3. HARD MANDATE: Minimalist & Compact ACF Pro UI (Zero Screen-Wasting)
- **Compact UI Settings:** ACF fields MUST NOT consume massive vertical screen space on the backend edit screen.
- **Mandatory settings for ACF fields:**
  - Always use **ACF Tabs** (`'type' => 'tab'`) to group sections logically.
  - For textareas, set `'rows' => 2` or `'rows' => 3` unless long copy is explicitly needed.
  - Use `'layout' => 'table'` or compact collapsed blocks for repeaters.
  - Set image return formats to **ID** (`'return_format' => 'id'`).
  - Keep labels and instructions brief and clear; eliminate empty spacers.

### 4. HARD MANDATE: Zero Leftover ACF Fields & Strict 1:1 Sync
- **No Leftover / Ghost Fields:** Never leave abandoned, duplicate, or unrendered ACF fields from previous design iterations.
- **Clean Up Immediately:** When a section or template is modified or replaced, immediately purge obsolete ACF fields from `inc/acf-*.php`, `acf-json/`, and `wp-admin`.
- **Strict 1:1 Synchronization:**
  - Every field displayed on the front end must have a corresponding backend edit field.
  - Every field in the backend edit screen must actively control frontend rendering (no ghost fields).

### 5. HARD MANDATE: Pixel-to-Pixel Frontend Clone of Abstrax Tech (`abstraxtech.com`)
- The visual styling, typography, spacing, card layouts, scientific botanical aesthetic, and responsiveness must replicate `https://abstraxtech.com/`.
- Ensure flawless mobile responsiveness with no horizontal overflow.

### 6. HARD MANDATE: Zero Public Footprint of Abstrax Tech
- **Never reveal the source reference on the public site.**
- Banned in public markup, CSS, JS, JSON-LD, comments, and media filenames:
  - Any links or assets pointing to `abstraxtech.com` or its CDNs.
  - Any mention of "Abstrax" in HTML, CSS class names, alt tags, schema, or filenames.
- All public branding belongs solely to **Elite Terpenes** (`eliteterpenez.com`).

### 7. HARD MANDATE: Cross-Site 48-Hour Free Delivery (Justccell → Elite, shipped)
- Sister site: hardware on `justccell.com` (`u392808260`) + terpenes on this site (`u984013785` / WP `30437919`). Do not upload Elite files to the Justccell Hostinger user.
- **Live plugin (not a mu-plugin):** `justccell-coupon-bridge` at `wp-content/plugins/justccell-coupon-bridge/`. Vault: `bridge/justccell-coupon-bridge.php`.
- Justccell POSTs `POST /wp-json/wc/v3/coupons` (`JC-{order_id}`, 0% + `free_shipping`, 48h, usage 1, billing-email lock). Magic link: `/?apply_coupon=JC-{order_id}`.
- Admin: **WooCommerce → Justccell bridge**. Keep coupon-required Free shipping. Never commit REST secrets.
- Justccell checkout must never wait: Action Scheduler + 4s timeout on that store. Full spec: [[websites/eliteterpenez.com/docs/cross-site-free-delivery|cross-site-free-delivery.md]] · [[websites/justccell.com/docs/elite-cross-sell|Justccell contract]].
- Reverse (`ET-{order_id}` on Justccell) is **not built**. Do not stub `inc/cross-sell.php` as if it were live.

### 8. HARD MANDATE: Obsidian / Ai Brain is the Second Memory
Theme deploys without vault documentation updates are incomplete. In the **same turn** as code:
- `docs/STATUS.md` — live theme version and snapshot table.
- `docs/BUILD-LOG.md` — dated ship log with change details.
- `rules.md` — if architecture, ACF fields, or URLs change.
- `docs/cms-editor-guide.md` — if wp-admin fields or page templates change.
- **Obsidian Graph Integrity:** Every `.md` file MUST start with `> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]` and be indexed in `INDEX.md`.
- **Strict Hashtag Hygiene:** **ZERO naked hashes** in prose. Always wrap IDs, hex colors, and numbers in backticks: `\`#XXXX\``, `\`#123\``, `\`#0A2540\``.

---

## Bot Division of Labor (Reference)

| Bot | Primary Role | Boundary |
|---|---|---|
| **Cursor / Grok** | Theme templates, PHP controllers, CSS, JS, ACF field definitions (`acf-json/` + `inc/acf-*.php`), Hostinger MCP in-place deploys | Must ensure all template elements are wired to backend fields (native or compact ACF). |
| **Hermes** | WordPress REST/XML-RPC labor, WooCommerce catalog, attaching Media Library IDs, filling ACF values | Fills and updates the backend edit fields; does not hardcode theme templates. |
| **Antigravity** | Architecture, planning, multi-bot orchestration, quality assurance, full-stack verification | Enforces rules, audits template mapping, verifies backend editability and lean coding. |

**Before declaring any page or task done, verify that a non-technical admin can edit every single heading, paragraph, and button via the WordPress backend edit screen!**
