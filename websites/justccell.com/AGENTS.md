# AGENTS.md — justccell.com Multi-Bot Development Rules

> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]] · [[websites/justccell.com/rules|📜 Site Rules]]
> **Target Site:** https://justccell.com/ · **Directory:** `websites/justccell.com/`  
> **Master Rules File:** [[websites/justccell.com/rules|rules.md]] (MANDATORY READ FOR ALL BOTS: Cursor, Grok, Hermes, Antigravity)  
> **Theme Working Copy:** `justccell-theme/`  
> **Sister Site:** [[websites/eliteterpenez.com/INDEX|eliteterpenez.com]] — [[websites/justccell.com/docs/elite-cross-sell|48h free-delivery coupons]]  

---

## 🚨 TOP DIRECTIVE FOR ALL AI AGENTS (CURSOR, GROK, HERMES, ANTIGRAVITY)

### 0. HARD MANDATE: Codebase map pre-check and sync (Rule §0.5)
Before modifying or auditing any Justccell theme code, read [[websites/justccell.com/features-code-map|features-code-map.md]] and use the listed paths. After you write, refactor, or fix a feature, update that file with the new paths, functions, hooks, and meta keys in the same turn. The task is not done until the map matches the code.

### 0b. External full-site audit (Opus / Antigravity QA)
When performing a **read-only site audit** (no code changes), start with [[websites/justccell.com/docs/website-audit-brief-2026-09-06|website-audit-brief-2026-09-06.md]], then [[websites/justccell.com/docs/STATUS|STATUS]], [[websites/justccell.com/rules|rules.md]], and [[websites/justccell.com/features-code-map|features-code-map.md]]. **Log in to wp-admin** before testing PDPs (coming soon is on for anonymous users). Report using the structure in the audit brief §5. Do not treat missing Mini Tank `clone_spin` data as a code defect unless the brief says otherwise.

### 1. HARD MANDATE: 100% Backend Content Editability
**Every page on this website must be editable in the WordPress backend edit area (`Pages → Edit Page` / `Products → Edit Product` / `Posts → Edit Post`) without touching code.**

- **All content-related fields must be editable:**
  - **Headings & Subheadings:** H1, H2, H3, section titles, eyebrow tags, badges.
  - **Paragraphs & Body Text:** Lead paragraphs, feature blurbs, descriptions, bullet points, tooltips.
  - **Button Text & CTA Labels:** "Get a Quote", "Learn More", "Download Specs", "Inquire Now", "Customize", etc.
  - **Button URLs & Targets:** Destination links, button actions.
  - **Media:** Banners, section photos, icons, backgrounds, videos (Media Library attachments ONLY).

- **Hierarchy of Content Management:**
  1. **Native WordPress & Native WooCommerce First:**
     - Use native Page Title, Gutenberg/standard Content editor, Excerpt, Featured Image.
     - Use native WooCommerce Product fields: Title, Short Description, Long Description, Regular/Sale Price, Attributes, Product Categories, Product Tags, Product Gallery.
  2. **ACF (Advanced Custom Fields) Mandatory for Custom Sections & Layouts:**
     - If native fields cannot accommodate the design (e.g. multi-column hero, custom feature cards, tech showcase, laser engraving options, custom tabs):
     - **You MUST create and properly map ACF fields directly to that page/product's backend edit screen.**
     - Ensure ACF field keys/names are clean, human-readable, and grouped logically into tabs so any non-technical store manager can open the page in wp-admin and update it instantly.
     - **Field GROUPS/FIELDS are created and edited only in the wp-admin ACF GUI, then saved to `justccell-theme/acf-json/` via Local JSON.** `inc/acf-*.php` may hold ONLY plumbing (JSON load/save paths, location-rule filters, `acf/prepare_field` UI tweaks, one-time repair/seed helpers) — never `acf_add_local_field_group()` field-array definitions.

- **Strictly Forbidden:**
  - ❌ **MASTER ACF RULE — NO PHP field registration:** All ACF Pro fields are managed strictly via the wp-admin GUI and synced via Local JSON (`acf-json/`). Registering or defining fields with hardcoded PHP arrays (`acf_add_local_field_group([...])`) is strictly forbidden. `inc/acf-*.php` is plumbing only.
  - ❌ **NO hardcoding of text in PHP:** Never output fixed marketing copy, titles, or paragraphs directly in PHP templates or template parts.
  - ❌ **NO hardcoding of button text:** Never hardcode `<a class="btn">Get a Quote</a>`. Button text and URLs must be pulled from backend fields.
  - ❌ **NO client-facing copy in CSS or JS:** No CSS `content: "..."`, no hardcoded text in front-end scripts.
  - ❌ **NO unmapped custom sections:** Never create a visual section in a template that lacks corresponding backend edit fields in wp-admin.
  - ❌ **PHP fallbacks are defaults only:** In template code, `$text = get_field('...') ?: 'Default';` is allowed solely as a fallback when the database field is empty. Backend fields must ALWAYS override defaults.

### 2. HARD MANDATE: Zero Leftover ACF Fields & Strict 1:1 Frontend/Backend Sync
- **No Leftover / Ghost Fields:** Never leave abandoned, duplicate, or unrendered ACF fields from previous design iterations or tests.
- **Clean Up on Page Changes:** Whenever a page design, section, or template is modified or replaced, **immediately clean up and delete obsolete ACF fields** in the wp-admin ACF GUI (which updates `acf-json/`). Never leave dead schemas in `acf-json/`.
- **Strict 1:1 Synchronization:**
  - Every field displayed on the front end must have a corresponding backend edit field.
  - Every field in the backend edit screen must actually be used on the front end. Do not present ghost fields that do nothing.
  - Keep `acf-json/` clean, version-controlled, and pruned of dead schemas.

### 3. HARD MANDATE: ZERO "Get Samples & Quotes" Sitewide (Samples NOT Offered)
- **Strict Client Mandate:** As explicitly instructed by client Mr Nas (CCELL Mazhar, 2026-09-03):  
  > *"Anywhere you see get samples and quotes on the whole site please remove. Its not something we offer."*
- **Strictly Banned Sitewide:** Never output "Get samples", "Get samples and quotes", "Request sample & quote", sample trays, free samples, or turnaround promises like "Samples delivered in 3–15 days" in any button, CTA, heading, paragraph, card, or form.
- **Permitted Alternatives:** Conversion and inquiry buttons must focus on business inquiries, wholesale quotes, or direct contact (e.g., "Inquire Now", "Get in Touch", "Contact Us", "Request a Quote").

### 4. HARD MANDATE: Obsidian / Ai Brain is the second memory
Theme deploys without vault updates are incomplete. Same turn as code:
- `features-code-map.md` — if any feature files, hooks, functions, or meta keys changed (Rule §0.5)
- `docs/STATUS.md` — live version + snapshot
- `docs/BUILD-LOG.md` — dated ship note
- `rules.md` — if architecture, ACF, URLs, or SEO hierarchy changed
- `docs/cms-editor-guide.md` — if wp-admin fields/templates changed
- `AGENTS.md` / `.cursorrules` — if a hard mandate moved
- **Obsidian Graph Integrity:** Every `.md` file MUST start with `> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]` and be linked in `INDEX.md`. **Zero naked hashes in prose** (never write `#XXXX`, `#123`, `#TODO` outside backticks) — Obsidian parses them as tag nodes which float detached from the main graph! (See [[rules/obsidian-vault-graph-integrity|Obsidian Vault Graph Integrity Standard]]).

### 5. Canonical CCELL 3.0 URL (client rename 2026-09-06)
Canonical slug is **`ccell-3-0`** → live URL **`/ccell-3-0/`** (resolved by the Justccell 3.0 page **template** `page-templates/justccell-bio.php`, not slug alone). Title **CCELL 3.0**. Legacy `/cell-3-0/`, `/justccell-3-0/`, `/ccell-3.0/`, `/justccell-3.0/` **301 into `/ccell-3-0/`** — never the reverse. Single source of truth = `justccell_bio_canonical_slug()` (`ccell-3-0`) + `justccell_bio_canonical_title()` (`CCELL 3.0`) in `inc/page-layouts.php`, both filterable for clones. ACF group `group_jc_j3_page` binds to the template, not the slug. See rules.md §7.5.

### 6. Product PDP heading ladder
Sole `<h1>` = Product heading. `<h2>` = Product Tagline (PDP only). Specs = `<h3>` + `<ul>`. Catalog / Explore cards reuse Specs (marketing line + Tank volume / Volume) — do not restore Listing tagline / Listing capacity ACF. Do not restore Banner heading / Banner text ACF fields.

### 7. HARD MANDATE: Elite Terpenes 48-hour free-delivery coupons (shipped 2026-09-04)
- Sister store: [[websites/eliteterpenez.com/INDEX|eliteterpenez.com]] (Hostinger `u984013785` / WP `30437919`). This site: `u392808260` / WP `30055979`. Never mix hosts.
- After processing/completed (and `woocommerce_payment_complete`), theme `inc/elite-cross-sell.php` queues Action Scheduler `justccell_elite_create_coupon` then POSTs Elite `POST /wp-json/wc/v3/coupons` (`JC-{order_id}`, 0% + `free_shipping`, 48h, usage 1, billing-email lock). Inline thank-you/email fallback: **4s** timeout. **Never fail Justccell checkout.**
- Settings + thank-you card copy: **Justccell → Elite Cross-sell**. Optional `JUSTCCELL_ELITE_*` wp-config constants. Never commit secrets.
- Elite receiver: plugin `justccell-coupon-bridge` (regular plugin, not mu-plugin). Magic link `https://eliteterpenez.com/?apply_coupon=JC-{order_id}`.
- Reverse (Elite order → Justccell `ET-{order_id}`) is **not built**.
- Full contract: [[websites/justccell.com/docs/elite-cross-sell|elite-cross-sell.md]] · [[websites/eliteterpenez.com/docs/cross-site-free-delivery|Elite-side spec]].

---

## 8. OTHER NON-NEGOTIABLE SITE RULES (Summary from [rules.md](rules.md))

1. **Media Library Only:** All images and videos must be WordPress Media Library attachments (`wp_get_attachment_image()` / `wp_get_attachment_url()`). Never hotlink, never hardcode `/wp-content/themes/.../assets/img/...` in front-facing templates.
2. **Zero Public Footprint of ccell.com:** Never link, mention, hotlink, or attribute ccell.com in public HTML, CSS, JS, Schema, or media filenames.
3. **One Live Theme Folder:** The live theme is always `wp-content/themes/justccell-theme/`. Local source is `websites/justccell.com/justccell-theme/`. Overwrite in place. Never create hashed clones (`justccell-theme-XXXX`).
4. **Deploy Method:** Deploy changed files via TUS in place (`public_html/wp-content/themes/justccell-theme/{rel-path}`). Bump `JUSTCCELL_VERSION` in `functions.php` and `style.css`. Clear cache.
5. **No Page Builders:** No Elementor, Divi, or block kit builders. Custom theme + ACF + native WordPress/Woo only.
6. **Add to cart live; Viva checkout pending:** Tier-priced SKUs use AJAX **Add to cart** + drawer. **Paid card checkout** waits on **Viva Smart Checkout** + VAT. Contact/inquiry forms remain for general wholesale leads.
7. **Coming Soon Stays ON:** Anonymous visitors see maintenance; logged-in admins see the site. Do not disable without explicit instruction.

---

## Bot Division of Labor (Reference)

| Bot | Primary Role | Boundary |
|---|---|---|
| **Cursor / Grok** | Theme templates, PHP controllers, CSS, JS, ACF field schemas (`acf-json/` via wp-admin GUI; `inc/acf-*.php` = plumbing only), in-place deploys | Must ensure all template elements are wired to backend fields (native or ACF). Never define fields with PHP arrays. |
| **Hermes** | WordPress REST/XML-RPC labor, WooCommerce catalog, attaching Media Library IDs, filling ACF values | Fills and updates the backend edit fields; does not hardcode theme templates. |
| **Antigravity** | Architecture, planning, multi-bot orchestration, quality assurance, full-stack verification | Enforces rules, audits template mapping, verifies backend editability. |

**Before declaring any page or task done, confirm that a non-technical admin can edit every single heading, paragraph, and button on that page via the WordPress backend edit screen!**
