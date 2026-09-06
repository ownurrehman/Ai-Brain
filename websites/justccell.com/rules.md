> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# justccell.com — AI coder rules

**Read this before changing anything under `websites/justccell.com/`.**  
Client: **3Devices**. Live: https://justccell.com/  
Theme source of truth: `justccell-theme/` · **Feature index:** [[websites/justccell.com/features-code-map|features-code-map.md]] (Rule §0.5) · Docs: `docs/` · Snapshot: `docs/STATUS.md`

These rules exist so the site stays **client-editable**, **media-correct**, **fast**, and **future-proof**. Do not invent shortcuts that violate them.

---

## 0. Non-negotiables (stop and fix if you break one)

### Rule §0.5: Codebase Map Pre-Check & Continuous Sync
- **Zero Blind Scans:** All agents must read `features-code-map.md` prior to code inspection or modification. File discovery latency must be zero.
- **Single Source of Truth:** `features-code-map.md` indexes the exact include order, template paths, hook names, database/meta keys, and architectural edge cases.
- **Continuous Documentation:** Any code write, refactor, hook change, or meta adjustment requires an immediate update to `features-code-map.md`. No delivery or pull request is complete without this sync.


1. **HARD MANDATE — 100% Backend Content Editability (Native WP / WooCommerce First, ACF Mapped Everywhere Else).**
   - **Every single content-related field on every page must be editable in the WordPress backend edit screen (`wp-admin → Pages → Edit Page`, `Products → Edit Product`, `Posts → Edit Post`).**
   - This includes **all headings** (H1, H2, H3, section titles, eyebrow tags, badges), **all paragraphs** (lead copy, descriptions, bullet points, feature blurbs, fine print), **all button text & CTA labels** ("Get a Quote", "Learn More", "Download Specs", "Inquire Now", etc.), **all button destination URLs**, and **all media/images/icons/videos** (attached via Media Library).
   - **Hierarchy:**
     1. **Native WordPress & WooCommerce first:** Use native fields wherever possible (Page Title, Post Content / Gutenberg editor, Excerpt, Featured Image, Menu items; WooCommerce Product Title, Short/Long Description, Price, Attributes, Categories, Gallery, etc.).
     2. **ACF mapping mandatory if native is not enough:** If native WP/Woo cannot support the custom section layout, structure, or repeaters, **you MUST create and properly map ACF fields directly to that page or post type edit screen**.
   - **Zero Code Hardcoding:** NEVER hardcode marketing copy, headings, paragraph body text, or button strings inside PHP template files, template parts, CSS (`content:`), or JavaScript.
   - **Dynamic fallback pattern:** Fallback strings in PHP code are permitted ONLY as initial defaults when the backend field is completely empty. The backend field (`get_field()` / `get_post_meta()` / native field) must ALWAYS take precedence over any code default.
   - **Field organization:** Field names, keys, and labels in ACF must be clear, human-readable, and logically grouped into tabs/sections on the backend edit screen so any non-technical user, client, or site manager can update pages without touching code.
   - **MANDATORY ACF CLEANUP & 1:1 SYNC (No Leftovers / No Ghost Fields):**
     - **Never leave leftover, duplicate, or orphaned ACF fields from previous work.**
     - If a page design, section, or layout changes, **immediately clean up and delete deprecated ACF fields** from `acf-json/` and wp-admin (not PHP field arrays).
     - **Strict 1:1 synchronization:** Every field visible on the front end must have an active backend field, and every backend field presented to the client must actually be rendered on the front end. Never confuse the client with ghost fields that do nothing.
2. **Media Library only for pictures and video.** Every image and video the visitor sees must be a WordPress Media Library attachment (`wp_get_attachment_image()` / `wp_get_attachment_url()`), picked in ACF or WooCommerce. Never hardcode an `<img src>` to another website, a CDN, or `/wp-content/themes/.../assets/img/...`. Theme folders may hold files only so **Justccell → Media** can copy them into Media; after that, the front end must use the Library URL. Fonts in CSS are the exception (self-hosted `.woff2`).
3. **No page builders.** No Elementor, Divi, Gutenberg “full page kits,” or similar. Custom theme + ACF + Default template.
4. **Inquiry-first & ZERO 'SAMPLES' SITAWIDE (Strict Client Policy).**
   - **NO 'GET SAMPLES & QUOTES' OR SAMPLE OFFERINGS:** As explicitly mandated by Mr Nas (CCELL Mazhar, 2026-09-03): *"Anywhere you see get samples and quotes on the whole site please remove. Its not something we offer."*
   - **Strictly forbidden sitewide:** Never use "Get samples", "Get samples and quotes", "Request sample & quote", sample trays, free samples, or turnaround promises like "Samples delivered in 3–15 days" in any button, CTA, headline, paragraph, card, or form.
   - All conversion touchpoints must focus on business inquiries, wholesale quotes, or direct contact (e.g. "Inquire Now", "Get in Touch", "Contact Us", "Request a Quote").
   - Catalog allows **Add to cart** on tier-priced / purchasable SKUs (AJAX cart drawer — `inc/cart-ajax.php`). **Paid card checkout** is not live until **Viva Smart Checkout** + VAT are explicitly ready. Wholesale **tier tables** on product pages are allowed (ex VAT). Contact/inquiry forms stay for general wholesale leads and non-purchasable SKUs. CTA label is **Add to cart** (not “Add to basket”).
5. **Coming soon stays ON** until the owner turns it off. Anonymous users see maintenance; logged-in admins see the real site.
6. **One WordPress.** Not one install per country. **justccell.com with no prefix is the UK order site.** Spain (`/es/`, `/spain/`) and Switzerland (`/ch/`, `/swiss/`) are the only country prefixes. Any other country (including Pakistan) stays on the UK site. Do not send visitors to `/other/` or `/uk/`.
7. **3Devices owns everything.** Never leave the developer as the only admin of Hostinger, Cloudflare, WP, domains, backups, or email.
8. **No leftover throwaway plugins.** Do not leave All-in-One WP Migration, dummy-content importers, or similar active when sharing the site with the client. Theme **Justccell → CMS Import** is the supported seeder — it is not a plugin. Media packs named `justccell-media*` are temporary; remove after attachments exist.
9. **Self-contained front end.** Never `href`/`src`/`srcset`/`url()`/`fetch` another vendor’s storefront or CDN. No third-party domain in shipped CSS/JS comments either. **Pictures and video: Media Library only** (rule 2). If an outside host blocks our IP, justccell.com must still render.
10. **CRITICAL — zero public footprint of ccell.com (or any design-source site).** justccell.com must never tell Google, other search engines, browsers, or users that the design was copied from ccell.com (or any third-party storefront).

   **Forbidden on the live / shipped front end (HTML, CSS, JS, JSON-LD, sitemaps, feeds, emails, Open Graph, response headers):**
   - Any link to `ccell.com`, `www.ccell.com`, subdomains, or their CDNs (`href`, canonical, hreflang, `rel=`, sitemap entries, “Visit original”, credit footers, etc.).
   - Any media or asset URL pointing at those domains (`src`, `srcset`, `poster`, `url()`, `@import`, fonts, video, favicon, prefetch/preconnect/dns-prefetch).
   - Meta / schema / comments / filenames / alt text / aria-labels that name ccell.com, “clone of ccell”, “based on ccell”, or similar.
   - Hotlinking, iframe embeds, or third-party embeds that load from those domains.

   **Allowed only off the public site:** designers/devs may open ccell.com privately for visual QA. That URL must never ship in theme code, Media Library attachment URLs, ACF values, or docs that get deployed with the site.

   **If you find a leak:** remove it immediately, re-upload self-hosted Media/theme assets, **rename public filenames** so they cannot be reversed to the source path, strip EXIF, clear caches, and note the fix in `docs/BUILD-LOG.md`.

11. **One live theme folder.** The only Justccell theme on the server is `wp-content/themes/justccell-theme/` (wp-admin name: **Justccell**). Local source is `websites/justccell.com/justccell-theme/`. Every update **overwrites that same folder**. Never install a second Justccell copy (`justccell-theme-XXXX` hashed folders). Never `hosting_deployWordpressTheme` with `activate: true` — Hostinger then creates a new folder instead of updating the live one. Twenty Twenty-Five may stay as WordPress’s fallback only.

12. **Plugins first.** WPML = languages. Rank Math + WPML SEO = sitemaps/hreflang. WooCommerce = shop. Do **not** code a language switcher, hreflang printer, or extra SEO plugin. If a setting must change, tell the owner the wp-admin clicks. Theme code is only for what those plugins cannot do (country URL `/es/` `/ch/` vs UK on the bare domain).

13. **wp-admin UI is native core only.** Never write custom CSS to style, modify, or override native WordPress or WooCommerce wp-admin UI elements (checkboxes, meta boxes, taxonomy panels, inputs, tables). The admin backend must remain untouched and run strictly on native core styles to ensure long-term stability and compatibility. **Forbidden:** `add_editor_style()` with storefront `globals.css` (universal `*` / `ul` resets bleed into admin and break category checkboxes). **Forbidden:** enqueueing `assets/css/globals.css`, `chrome.css`, `product.css`, or any storefront bundle on `admin_enqueue_scripts`. **Allowed:** narrowly scoped ACF-only layout CSS under `.acf-field` / `.acf-postbox[data-key=…]` / theme-owned mapper widgets (e.g. laser safe-zone) — never `#product_catdiv`, `#woocommerce-product-data`, or other core/Woo metabox IDs. **Never** patch admin bugs with more override CSS; remove the offending enqueue instead.

14. **Obsidian / Ai Brain is the second memory (same turn as code).** Shipping theme files without vault docs is incomplete. After every justccell.com implementation:
    - Append `docs/BUILD-LOG.md` (newest first: version, what shipped, how to verify).
    - Refresh `docs/STATUS.md` (live theme version, snapshot table, Built / Broken).
    - Update `rules.md` when architecture, ACF, URLs, or SEO hierarchy changed.
    - Update `docs/cms-editor-guide.md` when wp-admin fields or page templates changed.
    - Update `AGENTS.md` / `.cursorrules` when a hard mandate moved.
    - Update `features-code-map.md` whenever a feature’s files, hooks, functions, or meta keys change (Rule §0.5).
    - Hub files (`index.md`, `README.md`, `mastersheet.md`) get a one-line current version when the snapshot changes.
    - **Vault Graph Integrity:** Every markdown file created must have line 1 `> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]` and be linked in `INDEX.md`. **Zero naked hashes** (never write `#XXXX`, `#123`, `#TODO`, or hex colors outside backticks) — Obsidian parses them as tag nodes, creating detached floating balls in graph view! (See [[rules/obsidian-vault-graph-integrity|Obsidian Vault Graph Integrity Standard]]).
    Do **not** treat a `rules.md` snippet as enough. Code + live deploy + Obsidian must stay in lockstep.

---

## 1. CMS & content model (client can change everything)

### Core Content Policy: 100% Backend-Editable & Zero Ghost Fields

Any bot or human building or updating pages on this website **MUST** ensure that every text, heading, button, link, and media asset is editable in the WordPress admin backend edit screen. **No non-technical user should ever need to open PHP code or contact a developer to change a headline, body text, button label, or link.**

Equally critical: **Never leave behind leftover, obsolete, or disconnected ACF fields.** Whenever a page is redesigned or updated, old fields must be purged immediately so the backend edit area remains clean and in exact sync with the frontend.

### Required pattern & mapping matrix

| Content Element | Primary Backend Location | Fallback / Custom Section Mechanism | Template Role |
|---|---|---|---|
| **Page Headings & Subheadings** (H1, H2, H3, eyebrows) | Native Page/Post Title or native blocks | **ACF Text / Textarea field** mapped to Page edit screen | Output via `esc_html()` with empty fallback only |
| **Paragraphs, Descriptions & Body Copy** | Native WP Content (Editor/Gutenberg) | **ACF Textarea / WYSIWYG field** mapped to Page edit screen | Output via `wp_kses_post()` |
| **Button Text / CTA Labels** ("Inquire", "Quote", "Explore") | Native button block / Woo settings | **ACF Text field** (e.g., `cta_button_text`) | Output via `esc_html()` |
| **Button Destination URLs & Targets** | Native link field | **ACF URL / Link field** (e.g., `cta_button_url`) | Output via `esc_url()` |
| **Images, Banners, Backgrounds, Icons** | Native Featured Image / Woo Gallery | **ACF Image / Gallery / File field** (return format: Image ID) | `wp_get_attachment_image()` / `wp_get_attachment_url()` |
| **Feature Grids & Repeated Items** | Native Woo attributes / custom posts | **ACF Repeater / Flexible Content** with title, description, icon, button fields | Loop over subfields with escaping |
| **Product clone pages** | **WooCommerce Products** + native fields | **ACF product tabs** (Wholesale tier tables, Laser engraving, specs) | Render only |
| **Catalog listings** | WooCommerce Products + categories | ACF listing fields | Render only |
| **Sitewide links, socials, global settings** | Appearance → Menus | **Justccell → Storefront** (options page) | Fallback PHP only if option empty |

### Do

- **Map every field:** When creating or modifying a page layout or template part, create or verify corresponding ACF fields in **`acf-json/`** (sync to wp-admin). Use `inc/acf-*.php` only for options-page registration and non-field glue — **not** for defining field schemas.
- **Clean up on every change:** If a section or layout is removed or replaced, immediately delete the old ACF fields in `acf-json/` and prune wp-admin. Delete any orphaned field keys from the database.
- **Maintain strict 1:1 frontend-to-backend sync:** If the frontend displays it, the backend must expose it. If the backend exposes a field, the frontend must render it. No disconnected ghost fields.
- **Use native WordPress & WooCommerce first:** If a native field fits the purpose cleanly (like Post Title, Content, WooCommerce Product attributes, prices, short description), use it.
- **Use ACF whenever native doesn't fit:** If the layout has custom cards, multi-column banners, split feature rows, or specific button text fields, map them as distinct ACF fields.
- **Provide graceful PHP fallbacks:** Always use the pattern `$text = get_field('field_name') ?: 'Sensible Default';` so pages render nicely before data is entered, but the saved backend value ALWAYS takes precedence.
- **Keep `acf-json/` synced:** After adding/updating/deleting field definitions, keep `justccell-theme/acf-json/` in sync. **Migration complete:** **20** field groups load from Local JSON + DB; migration runner removed. Live DB de-bloated 0.9.293 (orphan/duplicate `acf-field` rows purged). See [[websites/justccell.com/docs/acf-local-json-migration|ACF Local JSON migration]].
- **ACF GUI owns field order:** Field sort order is controlled **only** by drag-and-drop in **ACF → Field Groups** (or by editing `menu_order` in JSON when syncing). **Forbidden:** PHP `acf/load_field` reordering, `acf_update_field()` loops, `justccell_acf_recover_*` field appenders, or version-bump hooks that rewrite group structure. AI/devs define fields in JSON; the client sorts them in the GUI.
- **Organize fields with Tabs:** On complex pages, use ACF Tab fields to separate sections (e.g. "Hero Banner", "Feature Grid", "Tech Specs", "CTA Section") so the edit screen is clean and intuitive for non-technical users.

### Do not

- **NEVER leave leftover ACF fields from previous work:** Do not abandon dead fields in ACF field groups when swapping or refactoring page sections.
- **NEVER hardcode text:** Do not write permanent marketing copy, headings, paragraph text, or button text directly into PHP templates or template parts.
- **NEVER hardcode button labels:** Do not write `<a href="...">Get a Quote</a>` — make the label and the URL editable from the backend.
- **NEVER put client-facing strings in JS or CSS:** No `content: "..."` in CSS for visible words; no hardcoded UI copy strings in frontend JavaScript.
- **NEVER leave orphaned sections:** Do not build a front-end section that has no corresponding fields on the page edit screen.
- **NEVER leave ghost fields in wp-admin:** Do not show fields to the client that have no effect on the front end.
- **NEVER override user input:** Do not let a hardcoded PHP default override a field the user left blank or changed in wp-admin.

---

## 2. Media Library rules

Front-end pictures and video are **only** allowed from the Media Library on this WordPress site (`/wp-content/uploads/...`). Not from another domain. Not from a hardcoded theme path.

### Do

- Upload every production image/video into **Media Library** (or run **Justccell → Media** / CMS Import so theme files become attachments).
- Bind them through ACF (return format: **Image ID** / File ID preferred) or Woo featured / gallery.
- Output with `wp_get_attachment_image()` / `wp_get_attachment_url()` so `srcset` and swaps in wp-admin work.
- Keep reference scrapes under `assets/img/ref/` **local-only / temporary**. They are a seed folder, not a public CDN. After import, the page must not point at that folder.
- Prefer WebP/optimized uploads; avoid megabyte PNGs in the critical path.
- Name files as Justccell assets (`justccell-home-banner-1.jpg`). Never ship `public_uploads_images_…` hashes, `ccell` in the basename, or leftover camera EXIF. Strip metadata on import (Justccell → Media).

### Do not

- Hotlink **ccell.com** (or any design-source / competitor CDN) — see non-negotiable **§0.10**.
- Hotlink any other third-party site or CDN for production photos/video.
- Hardcode `/wp-content/themes/.../assets/img/...` or `JUSTCCELL_URI . '/assets/img/...'` in templates.
- Leave broken `<img src="">` or theme paths that 404 after a theme folder rename.
- Ship filenames, alt text, or captions that mention ccell or “clone”.

---

## 3. Design QA vs the visual reference

Work **page by page**: Homepage → About → Contact → then others. Wait for owner approval before jumping ahead.

### Match carefully

- Layout proportions (rem-based widths, e.g. Premium row ~47.9% / 52.1%).
- Typography: face families (`mon-b`, `mon-r`, …) at **font-weight: 400** with different files — not browser faux-bold.
- Colors: section titles often `#333`; accents `#0504a8` / `#0504aa` only where the reference uses them. **Do not assume** — inspect the live pixel look. Owner may insist on grey even if a stylesheet still lists blue.
- Product rails: ~4-up cards, short blurbs (not full Woo attribute dumps).
- Sliders: scoped JS; do not break other carousels.

### Do not

- “Close enough” navy vs charcoal, regular vs bold, or 3-up vs 4-up.
- Restyle the whole site when fixing one section.
- Remove coming soon or add language UI in footer/header that fights WPML lock without an explicit request.

---

## 4. WordPress technical & performance (future-proof)

### Theme & PHP

- Live theme folder is always `wp-content/themes/justccell-theme/`. Overwrite files there. Do not patch hashed `justccell-theme-*` copies.
- Bump `JUSTCCELL_VERSION` + `style.css` Version on every front-end CSS/JS change (cache bust).
- `declare(strict_types=1);`, escape output (`esc_html`, `esc_url`, `esc_attr`, `wp_kses_post`), sanitize input.
- Enqueue assets in `inc/assets.php` only — no ad-hoc `<link>`/`<script>` in templates unless unavoidable.
- Prefer `home_url()` / `wc_get_page_permalink()` — **never** hardcode `https://justccell.com/...` without store/lang awareness.
- Keep CSS modular (`globals`, `chrome`, `home`, `catalog`, `product`, `pages`). Prefer CSS variables already in `globals.css`.
- Vanilla JS in `assets/js/` — no jQuery plugins unless Woo requires it.
- Avoid N+1 queries; cache expensive catalog helpers if you add loops.
- PHP target: **8.2+** (Hostinger is on 8.3).

### Caching & speed

- Assume **LiteSpeed Cache + Cloudflare**. After deploy: clear Hostinger site cache and LiteSpeed when possible; tell the owner to hard-refresh.
- Don’t disable caching plugins without a written reason.
- Don’t enqueue unused CSS/JS on every page — condition on template/context.
- Lazy-load below-fold images; don’t lazy-load LCP hero if it hurts LCP.
- Fonts: self-hosted woff2 in theme; `@font-face` weight must match how CSS uses the family (ccell pattern: `mon-b` + weight 400).
- No layout shift from missing image dimensions when you control markup.
- Memcached is available; Redis extension is **not** on this Hostinger plan — don’t require Redis.

### Plugins (locked unless STATUS changes)

| Use | Do not add casually |
|---|---|
| ACF Pro | Extra field plugins |
| WooCommerce | Second shop plugin |
| WPML + WCML + WPML SEO | Polylang / TranslatePress / Weglot |
| Rank Math Free | Yoast / AIOSEO (one SEO plugin only) |
| LiteSpeed Cache | Competing page caches |
| UpdraftPlus → client-owned offsite | Backups only on Hostinger |

Hreflang: **Rank Math sitemap via WPML SEO**, not duplicate tags in `<head>`.

### Security & ownership

- No secrets in the repo (`.env`, passwords, TUS tokens, DB dumps with PII).
- Separate `dev-*` WP admins; client remains Administrator.
- Don’t store card PAN in WP. Inquiry forms stay email/CRM until PCI-ready gateway.
- Follow `docs/security.md` and `docs/ownership-control.md`.

---

## 5. Geo, language, currency (URL contract)

Country (**store**) and **language** are independent.

```
justccell.com/…          → UK (default, no prefix)
justccell.com/es/…       → Spain landing
justccell.com/ch/…       → Switzerland landing
```

- Store drives currency/tax context (`uk`, `es`, `ch` only for now).
- **Language is WPML** (`en`, `es`, `de`, `fr`, … whatever you enable there). Do not build a custom language switcher.
- **WPML must not own `/es/` as “Spanish only.”** Parameter / locked mode as in `docs/translation-plugin.md`.
- Currency follows **store**, not language switcher.
- Unknown countries default to **UK**, never `/other/`.
- Do not ship six WordPress installs or a second live WP on 3devicescorp.com (alias/301 into the same platform).

Details: `docs/geo-language-currency.md`, `docs/architecture.md`.

---

## 6. Deploy & live ops

### Source & packages

- Edit **`justccell-theme/`** only. That is the theme.
- **Backups — keep ≥10 restorable versions (mandatory).** After every version bump (`JUSTCCELL_VERSION` + `style.css` Version), run the snapshot + rotation script:

```bash
bash scripts/backup-theme.sh        # freezes current version → archive/theme-releases/<version>/, keeps newest 10
```

  Then capture a durable git restore point (git history is the lossless primary backup):

```bash
git add "websites/justccell.com" && git commit -m "justccell theme X.Y.Z — <what shipped>"
git tag justccell-theme-X.Y.Z && git push origin main --tags
```

  `archive/theme-releases/` is **gitignored** (heavy snapshots stay local). Do not create `_deploy-theme-*` or extra theme slugs. Full runbook + restore steps: [[websites/justccell.com/docs/backup-restore|docs/backup-restore.md]].
- Ship updates into **`wp-content/themes/justccell-theme/`** on the live site (same path every time).
- Hostinger MCP: `hosting_deployWordpressTheme` with `slug: justccell-theme` and **`activate: false`**. If files still land in a hashed folder, TUS-upload into `wp-content/themes/justccell-theme/` with `override=true`, then **uninstall** the hashed leftover immediately.
- Exclude bulky `assets/img/ref` and `archive/media-seed` from live deploys. Photos belong in the WordPress Media Library.

### After every live change

1. Confirm the CSS URL is `/wp-content/themes/justccell-theme/` (no hash suffix).
2. Clear Hostinger cache (+ LiteSpeed if available).
3. **Obsidian sync (mandatory, same turn):** `docs/BUILD-LOG.md` + `docs/STATUS.md`. If fields/URLs/SEO/copy policy changed, also `rules.md`, `docs/cms-editor-guide.md`, and `AGENTS.md`.
4. Ask owner for hard-refresh (Cmd+Shift+R) when coming soon / admin view is in play.

### Do not

- Leave live ahead of STATUS / BUILD-LOG / rules (Obsidian is the second memory).
- Call theme deploy with `activate: true` (creates extra Justccell folders).
- Leave `justccell-theme-*` leftovers in Appearance → Themes. Delete them.
- Force-activate themes in a loop that hammers Hostinger rate limits.
- Turn off coming soon without explicit owner instruction.

---

## 7. Codebase map (for agents)

**Single source of truth:** [[websites/justccell.com/features-code-map|features-code-map.md]] (Rule §0.5). Do not hunt theme files until you have read it. Do not keep a second path table here — it will drift.

Hostinger: user `u392808260`, WP software id `30055979`. Elite Terpenes (shared client): user `u984013785`, WP software id `30437919`.

Feature-specific architecture that must not regress lives in §7.1–§7.9 below (buy box, REST privacy, bio slug, Woo UI, catalog lock, Elite coupons). Those sections **complement** the map; if you change files listed there, update **both** the subsection and `features-code-map.md`.

---

## 7.9 Elite Terpenes cross-sell (REST coupons)

**Goal:** a Justccell buyer gets a 48-hour, one-use, email-locked **free delivery** coupon on [eliteterpenez.com](https://eliteterpenez.com/).

**Justccell (this theme):** `inc/elite-cross-sell.php`.

- **Hooks:** `woocommerce_order_status_processing`, `woocommerce_order_status_completed`, `woocommerce_payment_complete` → Action Scheduler `justccell_elite_create_coupon`. Thank-you (`woocommerce_thankyou` + `thankyou.php`) and `woocommerce_email_before_order_table` may retry inline with a **4s** timeout. Never fail checkout.
- **Remote call:** `POST {api_url}/wp-json/wc/v3/coupons` (HTTPS Basic, Woo consumer key/secret). Payload: `code` = `JC-{order_id}`, `discount_type` = `percent`, `amount` = `0`, `free_shipping` = true, `date_expires` = +48h GMT, `usage_limit` = 1, `email_restrictions` = billing email.
- **Order meta:** `_elite_cross_sell_coupon` (HPOS `WC_Order` CRUD).
- **Settings:** **Justccell → Elite Cross-sell**. Optional `wp-config.php`: `JUSTCCELL_ELITE_API_URL`, `JUSTCCELL_ELITE_STORE_URL`, `JUSTCCELL_ELITE_CONSUMER_KEY`, `JUSTCCELL_ELITE_CONSUMER_SECRET`. Never commit secrets.
- **UI copy** (heading, body, CTA, code label) is edited on that settings screen — not hardcoded in the template. Card: white, `#e5e7eb` hairline border, primary button. Magic link: `https://eliteterpenez.com/?apply_coupon={code}`.

**Elite:** plugin `justccell-coupon-bridge` applies `?apply_coupon=`, generates REST keys (WooCommerce → Justccell bridge), seeds coupon-required Free shipping. Hostinger `u984013785`. Regular plugin, not mu-plugin.

Full contract: [[websites/justccell.com/docs/elite-cross-sell|Elite Terpenes cross-sell]] · [[websites/eliteterpenez.com/docs/cross-site-free-delivery|Elite-side spec]]. Reverse `ET-{order_id}` is not built.

---

## 7.1 B2B wholesale buy box — pricing UI standard

Product clone pages use `template-parts/product/buy-box.php` (slot API: `open` | `tiers` | `purchase` | `close`) embedded in `template-parts/product/clone.php` `.p-dart__shop-grid` + `assets/js/product.js` + `assets/css/product.css`. **Do not regress this hierarchy** when touching tiers, laser totals, or cart AJAX.

### Hero commerce layout (0.9.258+)

- **Left column (`.p-dart__shop-left`):** H1, tagline, short intro, specifications, **tier table**.
- **Right column (`.p-dart__shop-right`):** main image / 360° stage, **gallery thumbs under image** (`.p-thumbs--stage`), then purchase card (variations → qty/stock → laser → price → CTA).
- Legacy standalone `.p-order` section is **removed** — buy box wraps the hero grid via `[data-buy-box]`.
- Mobile: image column stacks above copy (`order: -1` on `.p-dart__shop-right`).

### Visual hierarchy (purchase card, right column)

1. **Total is the hero** — largest element above Add to cart (`2rem`, `font-weight: 700`). Shows hardware + engraving grand total when tiers resolve.
2. **Unit line is subordinate** — directly under the total: `{currency} / unit ({qty-range} tier)` in muted `#6b7280`, `1rem`. Hidden when no unit price resolves.
3. **ex VAT** — muted inline span beside the total amount (`#86868b`, `0.8125rem`); must not compete with the number.
4. **No “Your price” kicker** — removed; do not reintroduce all-caps price headings in the buy box.
5. **Hardware / engraving breakdown** — optional lines under the unit line when laser is active (`data-buy-hardware-row`, `data-buy-laser-row`).
6. **Stock** — pill badge beside quantity (`.p-buy__stock`), not a floating orphan line.
7. **Laser toggle** — compact card in `.p-buy__actions` (bordered `#fafafa` panel), not bare checkbox spacing.

### Tier table (left column, under specs)

- Minimal list/grid: light grey borders (`#e5e7eb`), **no heavy black active row**.
- **Dynamic active tier:** `product.js` `paintTiers()` listens to `[data-buy-qty]` `input` (and stepper / row click). For the tier row whose `data-qty-min` / max bracket matches quantity, add class **`.active-tier`** (remove from siblings).
- **Active tier CSS:** soft branded tint — `color-mix(in srgb, var(--jc-color-primary) 5%, transparent)` (fallback `rgba(5, 4, 170, 0.05)`), bold text. Legacy `.is-on` is **not** used on tier rows.
- Tier data comes from Woo/ACF via `justccell_product_buy_box()` JSON in `[data-buy-config]`; do not hardcode prices in CSS/JS.

### Typography & spacing

- Currency symbols: tight letter-spacing on `.p-buy__quote-total-amount` and unit line; `font-variant-numeric: tabular-nums`.
- `1.5rem` margin below quantity block, pricing block, and before the purple `.p-buy__cta` (primary = `var(--jc-color-primary)`).

### Labels

- Table column headings: from ACF/Woo via `$box['qty_label']` / `$box['price_label']`.
- CTA label: `$box['cta_label']` (default Add to cart).
- `ex VAT`, `unit`, `tier` strings: PHP `__()` on `data-buy-*` attributes only — not visible copy in CSS `content:` or JS literals.

---

## 7.2 Product highlight slides — overlay text colour

Vertical scroll highlight section: `template-parts/product/clone.php` (`.p-high`), ACF repeater **`clone_features`** on WooCommerce products.

- **One colour per slide** controls both heading and paragraph (shared overlay text).
- ACF sub-field: **`text_color`** (`field_jc_prod_feat_text_color`) — select **Black (default)** or **White (dark photos)**. Registered via `justccell_acf_highlight_text_color_field()` in `inc/cms-helpers.php`.
- Frontend: `justccell_normalize_highlight_text_color()` → modifier class **`p-high__txt--white`** on `.p-high__txt`. Default black uses existing `#111` / `#333` styles (no extra class).
- CMS import / pack fallbacks must pass through `text_color` when present; do not hardcode colours in templates.

---

## 7.3 REST API privacy & product gallery scripts

### REST API (pre-launch / coming soon)

While **Minimal Coming Soon** (or Woo `woocommerce_coming_soon`) is active, anonymous users must **not** read product catalog data via REST.

- Logic: `inc/rest-privacy.php` — `justccell_rest_prelaunch_gated()` returns false for logged-in users with `read` capability.
- Blocked routes (401): `/wp/v2/product`, `/wp/v2/products`, `/wc/v3/products`, `/wc/store/v1/products` (+ child paths).
- Uses `rest_endpoints` (unregister) + `rest_pre_dispatch` (hard stop). Logged-in admins/shop managers still get REST for QA.

### Variable product gallery (buy box → hero image)

Product clone pages decouple the gallery (`.p-dart__stage`) from the buy box (`.p-buy` / `form.variations_form`).

- **`assets/js/product-spin.js`:** CCELL `rotate360` parity — all `clone_spin` frames in HTML with `src`, opacity stack via `.is-on`, 20px drag step, no loader gate. Shared by every product PDP with 360° frames.
- **`assets/js/product.js`:** `bindVariationGallery()` on each `form.variations_form` — listens to `show_variation` / `found_variation`, attribute `change`, and matches `data-product_variations` JSON. **On first load, if ACF `clone_spin` frames exist, the 360° stage stays visible** until the shopper changes colour or picks a still gallery thumb; then `paintStill()` swaps the hero. Clicking the first gallery thumb (`data-view="spin"`) returns to 360°.
- Do not rely on WooCommerce default gallery swap (no `.woocommerce-product-gallery` on clone layout).
- **`wc-add-to-cart-variation`** must stay enqueued on product clone pages (`inc/assets.php`).

### Catalog source of truth

- **Active inventory:** **Exactly 57 published WooCommerce products** — the permanent live catalog. See **§7.8**.
- **Public catalog:** WooCommerce only via `justccell_catalog_from_woo()` in `inc/catalog.php`. **No PHP fallback products on the frontend.**
- **CMS Import seed:** `inc/catalog-seed.php` → `justccell_catalog_import_seed()` (import tool only — not a second storefront catalog).

### Nav labels (zero CCELL footprint)

- Menu titles must not show **“CCELL 3.0”**. Canonical public name is **Just CCELL 3.0**. `justccell_sanitize_nav_label()` in `inc/cms-helpers.php` rewrites bare `CCELL 3.0` labels. Seeders and fallbacks must use `/justccell-3-0/`, never `/ccell-3-0/`.

---

## 7.4 Product page semantic HTML / SEO hierarchy

Single product template (`template-parts/product/clone.php`) uses one heading ladder for SERP / GEO scraping. **Do not regress this.**

### Heading map (frontend)

| Role | Backend field | HTML | Notes |
|---|---|---|---|
| Sole page title | **Product heading** (`clone_product_heading`) | `<h1>` | Falls back to WooCommerce product name. Only one H1 on the PDP. |
| Accent under title | **Product Tagline** (`clone_subtitle`) | `<h2 class="p-dart__sub">` | Formerly “Blue text below heading”. Empty = hide. |
| Hero intro | WooCommerce **Product short description** (`post_excerpt`) | `.p-dart__intro` under tagline | RevZilla-style lead copy; also feeds shop card excerpt (trimmed). |
| Specs block title | **Specs section title** (`clone_specs_heading`) | `<h3 class="p-specs__title">` | Default label “Specifications” when specs exist. |
| Spec lines | **Specs** repeater (`clone_specs` → `line`) | `<ul class="p-specs"><li>…</li></ul>` | Semantic list only — never plain `<p>` name/value pairs. **Also drives catalog / Explore cards** (see §7.10). |
| Long copy | WooCommerce **Product description** (`post_content`) | `.p-story` after detail photos | Editor allows **H2 / H3 / lists**. Never fall back to short description. |

**Product Tagline (`clone_subtitle`) is PDP H2 only.** Do not print it on catalog, homepage rails, Explore More, or mega cards.

### Removed (do not restore)

- **Banner heading** (`clone_banner_heading` / `field_jc_prod_banner_heading`) — deleted from Product page ACF.
- **Banner text** (`clone_tagline` / `field_jc_prod_tagline`) — deleted from Product page ACF.
- **Listing tagline** (`clone_card_tagline` / `field_jc_prod_card_tagline`) — catalog grey line now comes from Specs.
- **Listing capacity** (`clone_card_capacity` / `field_jc_prod_card_capacity`) — catalog cyan line now comes from the Specs Tank volume row.
- Hero banner is **image (+ breadcrumbs) only** — no overlay H1/H2 on `.p-banner`.

### Migration / leftovers

- Empty Product heading may still read legacy `clone_banner_heading` meta once.
- Empty Product Tagline may still read legacy `clone_tagline` meta once.
- `acf/prepare_field` hides retired `field_jc_prod_*` keys in the Product edit UI only (see **§7.7**). **No** PHP recovery, append, or version-bump field sync on `group_jc_product_clone`.

### Files

- ACF: `acf-json/group_jc_product_clone.json` + DB (Local JSON SSOT since Batch 4 **0.9.236**)
- Data: `inc/cms-content.php` → `justccell_product_page_from_woo()`
- Markup: `template-parts/product/clone.php`
- Styles: `assets/css/product.css` (`.p-dart__copy h1`, `h2.p-dart__sub`, `.p-specs__title`)
- Editor: `inc/setup.php` TinyMCE `block_formats` + list buttons on `product` screens

---

## 7.10 Catalog listing cards — Specs only (no duplicate listing fields)

Category grids (`template-parts/catalog/category-grid.php`), Explore More, and 404 spotlight SKUs show **name + optional grey line + optional cyan capacity**. Fill-once: the client edits **Specs** on the product. Do not restore Listing tagline / Listing capacity ACF fields.

| Card line | Source | Hide when |
|---|---|---|
| Name (`h3`) | WooCommerce product name | — |
| Grey (`p`) | First Specs row that is **not** a labeled technical line (Tank volume / Volume, Battery, Dimensions, Resistance, …). Rows that contain `Dimensions:` or `Battery:` anywhere are skipped. | No marketing spec row |
| Cyan (`span`) | Specs row whose label is Tank volume or Volume (label stripped, e.g. `0.5ml/1ml`) | No Tank volume / Volume row |
| Image | WooCommerce **Product image** | — |

### Do

- Put a short marketing sentence as the first Specs row (same pattern as the manufacturer catalog we cloned).
- Keep a `Tank volume: …` spec row for the cyan figure.
- Leave Product Tagline as the PDP blue H2.

### Do not

- Dump Woo Colour / Combination attributes onto catalog cards.
- Fall back to Dimensions / Battery as the grey line when no marketing spec exists.
- Write CMS Import into `clone_card_tagline` / `clone_card_capacity`.
- Hardcode per-slug taglines in `inc/listing.php`.

**Keep:** **Featured in Products mega** (`clone_mega_featured`).

**Functions:** `justccell_product_spec_lines()`, `justccell_catalog_card_copy_from_specs()`, `justccell_catalog_card_meta()`, `justccell_catalog_explore_meta()` in `inc/catalog.php` + `inc/listing.php`.

---

## 7.11 SEO indexation, canonicals & virtual routes (0.9.294–0.9.296)

**Plugins own SEO output.** Rank Math (+ WPML SEO) owns meta title, meta description, canonical, OG/Twitter, robots, and the schema graph. The theme only *feeds* or *dedupes* — it never prints a second canonical/robots/Organization node.

### Canonical / noindex facts (do not "fix" as a bug)

- **Site-wide `noindex` is expected pre-launch.** Settings → Reading → **"Discourage search engines"** is ON (`blog_public = 0`) while coming-soon. That makes every URL `noindex, nofollow`, and Rank Math **correctly omits `<link rel="canonical">`** on noindexed URLs. This is not broken.
- **Launch lever:** uncheck "Discourage search engines" (`blog_public = 1`) **and** disable coming-soon. Canonicals + indexing then return automatically on standard pages. Re-verify live after launch.
- **Virtual routes need a fed canonical.** PDPs/listings are custom-routed (`justccell_product` / `justccell_listing` query vars, `is_singular=false`, `set_404()`→`status_header(200)`), so the SEO plugin has no queried object to self-canonicalize. `justccell_rank_math_view_canonical()` in `inc/product-pages.php` supplies it on `rank_math/frontend/canonical` (+ `wpseo_canonical`): `justccell_category_url($listing)` for listings, `justccell_product_url($slug)` for products. It fills only the empty value; normal pages stay with Rank Math. `redirect_canonical` (URL 301s) is a **separate** concern from the `<link>` tag — do not conflate.

### Schema, meta length, alt (theme-side dedupe/guards)

- **Organization JSON-LD:** theme defers to an active SEO plugin (`RANK_MATH_VERSION` / `WPSEO_VERSION` / `AIOSEO_VERSION`); only emits its own node as a no-plugin fallback. Filter `justccell_force_org_schema` to force it. Never allow two Organization nodes.
- **Meta description:** `justccell_clamp_meta_description()` (`rank_math/frontend/description` + `wpseo_metadesc`) trims to ~155 chars on a word boundary and scrubs banned "sample(s)" → "quote(s)". Theme masks output; correct the **source** text in Rank Math postmeta too.
- **Image alt:** `wp_get_attachment_image_attributes` backfills empty `alt` from attachment alt/title → parent title; never overrides an editor-set alt.

**Files:** `inc/chrome.php` (schema/meta/alt), `inc/product-pages.php` (canonical). Do not add a theme canonical/robots printer for standard pages — that is Rank Math's job.

---

## 7.5 Bio page slug + zero-sample seed/template hygiene

### Canonical Just CCELL 3.0 URL

- **Canonical slug / title:** `cell-3-0` / **Just CCELL 3.0**
- **Live URL:** `/cell-3-0/` — resolved by **Justccell 3.0** page template (`page-templates/justccell-bio.php`), not slug alone.
- **Legacy → canonical only:** `/justccell-3-0/`, `/ccell-3-0/`, `/ccell-3.0/`, `/justccell-3.0/` → `/cell-3-0/` via `inc/catalog-redirects.php`.
- **Seeders:** `justccell_ensure_core_pages()` seeds `cell-3-0`. Renames legacy pages via `justccell_canonicalize_bio_page_slug()` in `inc/page-layouts.php` (`justccell_bio_slug_cell_3_0`).
- **URL helpers:** `justccell_bio_page_url()` / menu seeds / nav fallbacks / chrome default to `/cell-3-0/`.
- **CCELL 3.0 header hover:** submenu product cards are **J3-flagged SKUs only** (`justccell_header_j3_tabs()` in `inc/header-menu.php`). Products mega stays full category.
- **Do not hardcode** `home_url('/ccell-3-0/')` in new theme code.

### Sample-string purge (Rule §0.4)

- Theme seed defaults in `inc/static-pages.php` must not mention Get Samples, sample trays, sample forms, or 3–15 day turnaround promises.
- Contact FAQ scrubber: `justccell_contact_faqs_without_samples()` drops any FAQ whose Q/A contains “sample” or 3–15 day shipping claims.
- Copy-policy upgrades (`inc/copy-policy.php`, incl. `v0993`) scrub Contact / brand CTA ACF fields that still contain banned sample language and replace with wholesale inquiry defaults (“Inquire Now”, “Request a wholesale quote”).
- Admin/inquiry file comments and Justccell → Leads blurbs use **wholesale inquiry** wording — not “sample/quote”.

---

## 7.6 WooCommerce core template UI (Cart, Checkout, My Account)

Classic WooCommerce shortcode templates (not Blocks) render inside `commerce-shell.php` with body classes `woocommerce-cart`, `woocommerce-checkout`, `woocommerce-account`.

**Native layout first:** WooCommerce `woocommerce-general`, `woocommerce-layout`, and `woocommerce-smallscreen` **must load** on cart, checkout, and my-account (`inc/woocommerce.php`). The theme does **not** replace Woo’s grid, float, table, or column structure.

**Theme overlay (`assets/css/woocommerce.css`):** fixed-header clearance, edge padding, primary CTA colour, view-order table alignment, password-eye toggle, cart qty stepper chrome, **checkout two-column grid** (see below). **No** custom flex/grid on `.woocommerce-MyAccount-navigation` or `.woocommerce-cart-form` table structure.

**Checkout layout (0.9.257+):** Desktop uses CSS Grid on `form.checkout` — **left ~60%** stacks `#customer_details` (billing → ship to different address → order notes); **right ~40%** wraps `#order_review_heading` + `#order_review` + `#payment` in `.jc-checkout-summary` (`inc/commerce-pages.php` hooks) with `position: sticky; top: calc(var(--jc-header-h) + 1rem);`, `#f9fafb` panel tint, and clean gateway cards. Mobile (`max-width: 768px`) collapses to single column: forms → summary → payment. Do not revert to Woo’s side-by-side billing/shipping float on desktop.

**`assets/css/commerce.css`:** order-received hero/table, empty cart, shop archive, search — **not** cart/checkout/account layout overrides.

### Overlap / spacing (critical)

The site header is `position: fixed` at `--jc-header-h` (100px). Endpoint wrappers clear it:

`body.woocommerce-cart|checkout|account .jc-shop { padding-top: calc(var(--jc-header-h) + 2rem); }`

### Source of truth

| Layer | File | When it loads |
|---|---|---|
| **Woo core layout** | WooCommerce bundled CSS | Cart / checkout / account |
| Tokens / type | `assets/css/globals.css` | Every page |
| Order-received, empty cart, search, archive | `assets/css/commerce.css` | Cart / checkout / account / shop / search (`inc/commerce-pages.php`) |
| **Branding overlay** | `assets/css/woocommerce.css` | Cart / checkout / account only (`inc/assets.php`) |
| Cart qty stepper | `assets/js/cart-wording.js` | Cart / checkout / account |

Bump `JUSTCCELL_VERSION` when any of those CSS/JS files change.

### My Account (`/my-account/*`)

- **Templates:** `woocommerce/myaccount/my-account.php` — branded hero only; nav + content use stock Woo actions. No custom grid wrapper around nav/content.
- **Tables:** Woo `shop_table` / `woocommerce-table--order-details` — Product left, Total right, `width: 100%`, sufficient cell padding (`woocommerce.css`).
- **Order table thumbnails (critical):** Every line item shows the **product** image (variation image when applicable) via `justccell_wc_should_wrap_order_item_row()` in `inc/woocommerce.php` — wrapper `.jc-wc-order-item` with `.jc-wc-order-item__media` (64×64) + `.jc-wc-order-item__content` (title, qty, attributes, engraving meta stacked).
- **Section headers:** `.woocommerce-order-details__title`, `.woocommerce-column__title`, and order-received panel titles use a light divider — `border-bottom: 1px solid #e5e7eb; padding-bottom: 0.5rem;` (not heavy Woo default or thick brand underline).
- **Typography:** All text inside `.woocommerce` wrappers (tables, `th`/`td`, addresses) inherits `var(--jc-font-sans)`; address paragraphs use `line-height: 1.6` and `font-style: normal`.

### Cart / checkout

- Native Woo cart table from core CSS; theme adds qty **− / +** stepper on all lines (including laser bulk orders). Laser rows are **not** locked to qty 1 — engraving PPU recalculates on cart update (`inc/laser-engraving.php`).
- Checkout: two-column sticky summary (rules §7.6). Coupon toggle spans full width above the grid.
- Human-readable laser lines only (see laser meta rules). Never `_justccell_laser_*` keys on the storefront.

### Laser order item meta (customer-facing)

- Storage keys `_justccell_laser_*` are **internal** — hidden via `woocommerce_hidden_order_itemmeta`, `woocommerce_order_item_get_formatted_meta_data`, cart `woocommerce_get_item_data`, and `justccell_order_item_meta_lines()` (never `get_formatted_meta_data('')`).
- Customers see translated labels only: “Engraving artwork”, “Engraving text”, “Engraving setup fee”, etc.

### Do not

- Dequeue WooCommerce core CSS on cart/checkout/account.
- Add theme flex/grid that overrides Woo account nav, cart table, or checkout columns.
- Do not set `display: flex` on `.woocommerce-info` / `.woocommerce-message` when Woo core CSS is loaded — it breaks the `::before` icon and overlaps text (e.g. Downloads empty state).
- Seed or alias `/ccell-3-0/` from `inc/static-pages.php` (canonical slug is `cell-3-0` only).

---

## 7.7 Legacy clone ACF fields — deprecated (WooCommerce is source of truth)

Published products may still have old `clone_*` postmeta from the import era. **The theme must never read these for storefront behaviour** (colours, gallery, tiers). Postmeta can remain in the database; UI fields are purged.

### Retired fields (hidden + purged from Product page ACF UI)

| Legacy field | Replaced by |
|---|---|
| `clone_colours` / `field_jc_prod_colours` | WooCommerce **Attributes** → Colour + variations |
| `clone_gallery` / `field_jc_prod_gallery` | WooCommerce **Product image** + **Product gallery** |
| `clone_offers` / `field_jc_prod_offers` | Tier meta (`_justccell_tiered_pricing`); legacy rows migrated once via `inc/tiered-pricing.php` |
| `clone_buy_tiers`, `clone_buy_enabled`, `clone_buy_note` | Native Woo buy box + tier meta |
| `clone_banner_heading`, `clone_tagline`, `clone_card_image`, `clone_oil_group` | See §7.4 / §7.10 |
| `clone_card_tagline`, `clone_card_capacity` | Specs repeater (`clone_specs`) — grey marketing line + Tank volume |

Registry: `justccell_acf_legacy_product_clone_field_names()` + `justccell_acf_legacy_product_clone_field_keys()` in `inc/cms-helpers.php`. Hidden via `acf/prepare_field` in `inc/acf.php` (UI only). **No** version-bump DB purge hook after Batch 4 — edit field definitions in ACF GUI only.

### ACF save safety (nonce / WooCommerce product edit)

Legacy field hiding uses `acf/prepare_field` **only** when `justccell_acf_should_hide_field_in_ui()` is true (admin GET screen render). It must **not** return `false` during:

- `POST` saves (`editpost`, product Update)
- Any admin AJAX (`wp_doing_ajax()`), including `acf/validate_save_post`, `acf/save_post`, and WooCommerce variation saves

Returning `false` during validation strips fields from the save registry and triggers **“ACF was unable to perform validation because the provided nonce failed verification.”**

Destructive `acf_delete_field()` runs once per version on a safe GET `admin_init` request (`justccell_acf_is_safe_maintenance_request()`), never during the product save lifecycle.

### Frontend contract

- **Buy box / colour dropdown:** `justccell_product_buy_attributes()` in `inc/commerce.php` — Woo only.
- **Variation hero swap:** `assets/js/product.js` → `bindVariationGallery()` — `form.variations_form` / `data-product_variations` only.
- **Product template:** `template-parts/product/clone.php` collapses empty highlight, specs, detail, gallery, and heating blocks.

### CMS Import

- Gallery seed writes **`_product_image_gallery`** only — does **not** re-populate `clone_gallery`.

### Do not restore

- ACF textarea/repeater colour lists on Edit Product.
- Any PHP/JS that reads `get_field('clone_colours')` for the buy box or gallery.

---

## 7.8 WooCommerce catalog — 57 published products (locked)

**The active, permanent storefront catalog is exactly 57 published WooCommerce products.** This is not a staging set, cut list, or disposable import batch.

### Composition

| Layer | Count | Role |
|---|---|---|
| **Core launch SKUs** | 21 | Client launch / merchandising set (original keep list) |
| **Imported expansion SKUs** | 36 | Previously labeled “clones” in vault notes — **permanent, first-class inventory** |
| **Total published** | **57** | All appear in catalog rails, mega menu, search, and `/{category}/{slug}/` PDPs |

### Hard rules for agents

- **Never trash, hide, or 301 away** a published product in the 57-SKU set to “shrink” the catalog. Do not re-run the 2026-09-02 “catalog cut” playbooks (`docs/hermes-prompts-product-catalog.md`, `docs/redirect-map-catalog-cut.md`) — those instructions are **obsolete**.
- **Never treat the 36 imported SKUs as temporary.** They use the same PDP template, buy box, and Woo attribute model as the core 21.
- **Sparse ACF is expected** on some imported SKUs (empty highlight slides, empty specs). `template-parts/product/clone.php` must collapse those sections — never assume every product has full ACF rows.
- **Colours & variations:** WooCommerce **Attributes** + variations only — never `clone_colours` (§7.7).
- **Count changes** (add/remove/publish/draft) require explicit client approval and same-turn updates to this section + `docs/STATUS.md`.

### Theme files

- Catalog query: `inc/catalog.php` → `justccell_catalog_from_woo()`
- PDP: `template-parts/product/clone.php`, `catalog-clone.php`
- Permalink 301s: `inc/catalog-redirects.php` — **slug renames and legacy path aliases only** (no “trashed SKU → category” map)

---

## 8. What we have done so far (context for AI)

Use this so you don’t redo or undo finished work. **Live snapshot is `docs/STATUS.md` (theme 0.9.296 as of 2026-09-06).** Dated ships: `docs/BUILD-LOG.md`. Full audit + backlog: [[websites/justccell.com/docs/AUDIT-REPORT-2026-09-06|AUDIT-REPORT-2026-09-06.md]]. Backups/restore: [[websites/justccell.com/docs/backup-restore|backup-restore.md]].

- **Platform:** One WP + Woo on Hostinger (`u392808260` / WP `30055979`); Cloudflare proxied; coming soon ON for anonymous visitors.
- **Theme:** Custom `justccell-theme` only (no Elementor). Source: `websites/justccell.com/justccell-theme/`. In-place TUS overwrite of `wp-content/themes/justccell-theme/`.
- **CMS:** ACF on Pages/Products; **Justccell → CMS Import** seeder; **57 published Woo products** (§7.8). Public catalog is Woo-only (`inc/catalog.php`). PHP product list in `inc/catalog-seed.php` is import-only.
- **Homepage:** visual clone — banner slider, product rails, Customize / Premium row, Trusted collage. Quote form under Trusted removed. WPML chrome stripped from header/footer where locked.
- **About / Contact / Why / Location:** clone templates + page-specific ACF groups. Contact uses wholesale inquiry copy (no samples).
- **Packaging + Elite Terpenes:** **Justccell Coming Soon** template (`page-templates/justccell-coming-soon.php`) — title + excerpt only; no leftover brand ACF on those screens.
- **Just CCELL 3.0 bio:** canonical URL **`/cell-3-0/`**. Legacy `/justccell-3-0/` and `/ccell-3-0/` 301 here.
- **Product PDP (SEO):** one `<h1>` = Product heading; `<h2>` = Product Tagline; `<h3>` + `<ul>` = Specs. Banner heading/text ACF fields deleted. Woo **Product description** stays on the edit screen (H2/H3/lists). Catalog cards reuse Specs (marketing line + Tank volume) — Listing tagline / Listing capacity ACF fields deleted (theme 0.9.284).
- **Buy box:** B2B tier table + total-as-hero (§7.1). Laser engraving inline in buy box when enabled (§ laser doc).
- **Woo endpoints:** Cart / checkout / my-account use **native WooCommerce core CSS** for layout; theme `woocommerce.css` is branding-only (purple CTAs, header clearance, order table alignment). Classic shortcodes, not Blocks. **Add to cart** + AJAX drawer live for tier-priced SKUs; **Viva Smart Checkout** (paid gateway) not live yet.
- **Elite Terpenes cross-sell (0.9.219):** processing/completed orders POST a free-delivery coupon to Elite `/wp-json/wc/v3/coupons`. Settings + card copy: **Justccell → Elite Cross-sell**. See §7.9.
- **SEO / i18n stack:** Rank Math Free + WPML + WCML + WPML SEO (hreflang in sitemap). Anonymous product REST blocked while coming soon is on (`inc/rest-privacy.php`).
- **Still open:** **Viva Smart Checkout** (paid gateway), UPS/FedEx, VAT/accounts, ownership transfer, coming soon off, Spain/EU domain, pixel-perfect remaining chrome.

Do not regress homepage CMS wiring, 4-up rails, PDP heading ladder, `/cell-3-0/` canonical, CCELL 3.0 J3-only header mega, or the no-samples policy.

---

## 9. Working style for AI coders

1. Read **[[websites/justccell.com/features-code-map|features-code-map.md]]** (Rule §0.5) → **STATUS** → **this rules file** → relevant `docs/*` → then code. After code + deploy, write Obsidian (STATUS, BUILD-LOG, `features-code-map.md` if files/hooks moved, rules if architecture moved) in the **same turn**.
2. **Strict Backend-Editability Mandate:** Every AI bot (Cursor, Grok, Hermes, Antigravity) must verify that every page section has all its headings, paragraphs, and buttons mapped to native WP/Woo fields or ACF fields on the edit screen. Do not deliver static/hardcoded templates. If you add or modify a layout, add/sync the ACF fields in **`acf-json/`** only (then sync in wp-admin).
3. **Mandatory ACF Cleanup on Changes:** If you modify, replace, or redesign a page or section, **clean up all leftover ACF fields**. Never leave deprecated or dead fields in `acf-json/` or the database. Frontend and backend fields must always be in clean 1:1 sync.
4. Prefer the smallest diff. **If WPML, Rank Math, Woo, or ACF already has a setting, do not write PHP for it — list the wp-admin clicks for the owner.**
5. Fix the **live** CSS path the browser actually loads, not only local files.
6. Visual bugs: compare the approved HTML/CSS (classes like `font-b`, `g_tw`, `top_txt`) before guessing.
7. One page at a time; pause for owner approval when STATUS says so.
8. No drive-by refactors, no new markdown docs unless asked (except STATUS/BUILD-LOG updates when shipping).
9. Never commit secrets. Never disable security/cache “to make it easier.”
10. If something must be temporary (theme-bundled ref images, PHP fallback copy), label it in code comments and BUILD-LOG with a removal condition.

---

## 10. Quick checklist before you say “done”

- [ ] **Codebase map synced (Rule §0.5):** Did you read `features-code-map.md` first, and did you update it with any new/changed paths, functions, hooks, or meta keys?
- [ ] **100% Backend Content Editability:** Can a non-technical admin change EVERY heading, paragraph, button text, CTA link, and image on this page from the WordPress backend edit screen (native WP/Woo or ACF)?
- [ ] **ACF fields mapped & synced:** Are all custom section fields saved in `acf-json/` and synced to wp-admin (no PHP field-array registration)?
- [ ] **Zero leftover or ghost ACF fields:** Are all unused/deprecated fields from previous work cleaned up? Are frontend templates and backend edit fields in exact 1:1 sync?
- [ ] **Zero sample offerings sitewide:** Are all mentions of "Get samples", "Get samples and quotes", "Request sample & quote", or sample turnaround promises completely removed/purged (samples are NOT offered per client policy)?
- [ ] Every photo/video on the front is a Media Library attachment (no theme-folder `src`, no other website)?
- [ ] **No ccell.com (or design-source) links, media URLs, preconnects, schema, or credits anywhere public?**
- [ ] No Elementor / no hardcoded permanent copy or button text in theme for that section?
- [ ] CSS/JS enqueued; version bumped; live file verified; cache cleared?
- [ ] Fonts/colors/weights match the approved layout (or explicit owner override)?
- [ ] Store/lang URLs and inquiry-first behavior preserved?
- [ ] No outbound third-party storefront/CDN URLs in HTML, CSS, or JS?
- [ ] Obsidian updated: STATUS + BUILD-LOG + `features-code-map.md` if code locations changed (and rules / cms-editor-guide / AGENTS if architecture or fields changed)?
- [ ] **Backup captured:** ran `scripts/backup-theme.sh` (≥10 snapshots retained) and committed + tagged `justccell-theme-X.Y.Z` (Rule §6)?
- [ ] Coming soon still ON unless owner said otherwise?

---

*If a rule here conflicts with a newer `docs/STATUS.md` note, STATUS wins for “what shipped,” and this file should be updated in the same pass.*
