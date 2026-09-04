> **Parent Site:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Master Ai Brain Hub]]

# eliteterpenez.com — AI coder rules

**Read this before changing anything under `websites/eliteterpenez.com/`.**  
Client: **3Devices / Mr Nas**. Live: https://eliteterpenez.com/  
Reference Clone Target: **Abstrax Tech** (https://abstraxtech.com/)  
Sister Store: **Just CCELL** (https://justccell.com/ — Hardware, Vapes & Pods)  
Theme source of truth: `eliteterpenez-theme/` · Docs: `docs/` · Snapshot: `docs/STATUS.md`

These rules exist so the site stays **client-editable**, **media-correct**, **fast**, **lean**, and **future-proof**. Do not invent shortcuts that violate them.

---

## 0. Non-negotiables (stop and fix if you break one)

1. **NO BLIND EXTRA CUSTOM CODING (Lean Native WordPress & WooCommerce First).**
   - **Strict prohibition:** Do NOT write custom code, custom tables, heavy custom frameworks, or ad-hoc custom post management when WordPress or WooCommerce already provides a battle-tested native solution.
   - Every single feature, layout, and catalog element must leverage native WordPress core APIs, WooCommerce standard data structures, and established hooks/filters first.
   - Custom coding is permitted **only when strictly necessary or when there is literally no native alternative**.
   - Before writing any custom controller or function, verify if WooCommerce or WordPress core natively handles it.

2. **HARD MANDATE — 100% Backend Content Editability (Native WP / WooCommerce First, ACF Pro Mapped Everywhere Else).**
   - **Every single content-related field on every page must be editable in the WordPress backend edit screen (`wp-admin → Pages → Edit Page`, `Products → Edit Product`, `Posts → Edit Post`).**
   - This includes:
     - **All Headings & Subheadings:** H1, H2, H3, section titles, eyebrow tags, badges.
     - **All Paragraphs & Body Text:** Lead copy, product descriptions, bullet points, terpene profile summaries, scientific blurbs, fine print.
     - **All Button Text & CTA Labels:** "Shop Terpenes", "Explore Blends", "View Profile", "Inquire Now", "Add to Cart", etc.
     - **All Button Destination URLs:** Links, query args, anchor targets (`_self`, `_blank`).
     - **All Media:** Section banners, botanical photography, lab testing badges, aroma wheels, icon graphics (strictly via WordPress Media Library attachments).
   - **Strict Content Hierarchy:**
     1. **Native WordPress & WooCommerce FIRST:**
        - Pages/Posts: Page Title, Content (`post_content` / Gutenberg block editor), Excerpt (`post_excerpt`), Featured Image (`_thumbnail_id`), Menu items.
        - WooCommerce Products: Product Name, Short Description (summary / aroma notes), Long Description (deep terpene & botanical science / lab specs), Regular/Sale Price, Product Attributes (Strain Type, Aroma Profile, Dominant Terpenes, Format), Product Categories, Product Tags, Product Gallery.
     2. **ACF Pro Mapping Mandatory When Native is Insufficient:**
        - If a custom section, aroma wheel, strain card grid, flavor profile chart, or multi-button CTA cannot be captured cleanly with native fields, **you MUST create and map ACF Pro fields to that page or post type edit screen**.
   - **ZERO CODE HARDCODING:** NEVER hardcode marketing copy, section headlines, descriptions, or button labels inside PHP template files, template parts, CSS (`content:`), or JavaScript.
   - **Dynamic fallback pattern:** Fallback strings in PHP code are permitted ONLY as initial defaults when the backend field is completely empty:
     ```php
     $headline = get_field('hero_headline') ?: get_the_title();
     ```
     The saved backend database field must ALWAYS take precedence over any code default.

3. **MINIMALISTIC & COMPACT ACF UI (Zero Screen-Wasting Fields).**
   - **Client-Friendly & Space-Efficient:** When ACF Pro fields are registered, they MUST NOT consume massive vertical or horizontal screen space on the backend edit screen.
   - **Rules for ACF UI design:**
     - Always use **ACF Tabs** (`'type' => 'tab'`) to organize multi-section pages cleanly.
     - Use compact layouts for repeaters and flexible content (`'layout' => 'table'` or `'layout' => 'block'` with collapsed rows by default).
     - Textareas must have `'rows' => 2` or `'rows' => 3`, not the default 8-row height, unless long copy is explicitly required.
     - Instructions must be concise and informative. Never pad field groups with empty spacers or redundant UI elements.
     - Return formats: Always return **ID** for images/files (`'return_format' => 'id'`), never raw URLs or heavy arrays.

4. **ZERO LEFTOVER ACF FIELDS & STRICT 1:1 SYNC (No Ghost Fields).**
   - **Never leave leftover, duplicate, or orphaned ACF fields from previous design experiments.**
   - Whenever a page design, section, or layout is updated, **immediately clean up and delete deprecated ACF fields** from theme code (`inc/acf-*.php`), `acf-json/`, and `wp-admin`.
   - **Strict 1:1 synchronization:** Every field visible on the front end must have an active backend field, and every backend field presented to the client must actually be rendered on the front end. Never confuse the client with ghost fields that do nothing.

5. **PIXEL-TO-PIXEL FRONTEND CLONE OF ABSTRAX TECH (`abstraxtech.com`).**
   - Frontend pages must replicate the visual excellence, typography, layout proportions, card rails, botanical aesthetic, and responsiveness of `https://abstraxtech.com/`.
   - High visual standard: crisp modern typography, precise grid alignment, elegant micro-interactions, responsive mobile views.
   - Work methodically page-by-page: Homepage → Shop/Catalog → Strain/Terpene Product Detail Pages → Collections/Categories → Brand/About/Contact.

6. **CRITICAL — ZERO PUBLIC FOOTPRINT OF ABSTRAX TECH (`abstraxtech.com`).**
   - `eliteterpenez.com` must **never** reveal to search engines, browsers, competitors, or users that the design reference was Abstrax Tech.
   - **Strictly forbidden in live/shipped code (HTML, CSS, JS, JSON-LD, sitemaps, feeds, emails, headers, asset names):**
     - Any link to `abstraxtech.com`, `abstrax.com`, or their CDNs.
     - Any media or asset URL pointing at their servers (`src`, `srcset`, `url()`, `@import`, fonts, video).
     - Any meta tags, schema markup, CSS classes, PHP comments, JavaScript strings, or alt text mentioning "Abstrax", "Abstrax Tech", or "clone".
     - Hotlinking, iframes, or external CDN imports from their domains.
   - All branding, copy, assets, and metadata must belong exclusively to **Elite Terpenes** (`eliteterpenez.com`).

7. **CROSS-SITE 48-HOUR FREE DELIVERY SYNERGY (Just CCELL ↔ Elite Terpenes).**
   - **Client Strategic Feature:** Customers ordering hardware (vapes, cartridges, pods) on `justccell.com` receive **free delivery** on `eliteterpenez.com` (terpenes, flavor profiles) if they order within 48 hours, and vice versa.
   - Automated via WooCommerce REST API + `justccell-coupon-bridge` mu-plugin:
     - On order completion, a unique coupon (`JC-{order_id}`) is generated via REST API (`POST /wp-json/wc/v3/coupons`) with a 48-hour expiry.
     - Magic links (`eliteterpenez.com/?apply_coupon=JC-{order_id}`) automatically capture and apply the coupon in the customer session.
     - A dedicated WooCommerce "Free shipping" method requiring a valid coupon is configured.
   - Must never block or slow down checkout on either store; all background jobs run asynchronously via Action Scheduler.

8. **MEDIA LIBRARY ONLY FOR PRODUCTION ASSETS.**
   - Every photo, illustration, terpene chart, and video displayed on the frontend must be an attachment in the local WordPress Media Library (`/wp-content/uploads/...`).
   - Never hotlink external URLs. Never output hardcoded theme asset paths (`/wp-content/themes/.../assets/img/...`) in production markup.
   - Always output images using `wp_get_attachment_image()` or `wp_get_attachment_url()` so responsive `srcset`, lazy loading, and admin media replacements function natively.
   - Prefer modern WebP format; strip EXIF data before uploading.

9. **NO PAGE BUILDERS.**
   - No Elementor, Divi, Beaver Builder, or bloated block suites.
   - The site uses a clean, bespoke theme (`eliteterpenez-theme`) built on native WordPress template hierarchy + WooCommerce hooks + ACF Pro.

10. **ONE LIVE THEME FOLDER & IN-PLACE DEPLOYMENTS.**
    - The live theme directory on Hostinger is always `wp-content/themes/eliteterpenez-theme/`.
    - Local source is `websites/eliteterpenez.com/eliteterpenez-theme/`.
    - Updates are deployed **in place** via Hostinger MCP / TUS to individual files. Never upload hashed theme archives (`eliteterpenez-theme-0.9.x.zip`) or activate secondary theme directories.
    - Clear Hostinger and LiteSpeed caches after every live deployment.

11. **WORDPRESS CODING STANDARDS & SKILL ENFORCEMENT.**
    - Follow official WordPress Coding Standards (WPCS): `declare(strict_types=1);`, strict escaping (`esc_html`, `esc_attr`, `esc_url`, `wp_kses_post`), input sanitization (`sanitize_text_field`, `sanitize_key`), and nonces for all state-changing actions.
    - Leverage WordPress skills and guidelines (e.g. `wordpress-expert`) before touching or coding theme templates.

12. **OBSIDIAN / AI BRAIN IS THE SECOND MEMORY (Zero Naked Hashes & Graph Integrity).**
    - Theme code updates without matching documentation updates are incomplete.
    - In the **same turn** as shipping code, update:
      - `docs/STATUS.md` (live theme version, snapshot table)
      - `docs/BUILD-LOG.md` (dated ship log with release notes)
      - `rules.md` (if architecture, ACF fields, or URLs change)
      - `docs/cms-editor-guide.md` (if wp-admin fields or page layouts change)
    - **Vault Graph Integrity:** Every markdown note created MUST begin with:
      `> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]`
    - **Strict Hashtag Hygiene:** **ZERO naked hashes** in prose! Never write `#XXXX`, `#123`, or hex codes like `#FFFFFF` outside backticks. Obsidian treats naked hashes as tag nodes that float disconnected from the graph. Always wrap in backticks: `\`#XXXX\``, `\`#FFFFFF\``.

---

## 1. CMS & Content Model (Native First, Minimal Compact ACF)

### 1.1 Core Architecture Matrix

| Content Element | Primary Backend Location | Fallback / Custom Section Mechanism | Template Role / Function |
|---|---|---|---|
| **Page Title & Hero Heading** | Native Page/Post Title | ACF Text field (`hero_heading`) | `esc_html()` with title fallback |
| **Section Headings & Eyebrows** | Native Block / Title | ACF Text field (`section_eyebrow`, `section_title`) | `esc_html()` |
| **Lead Copy & Descriptions** | Native Page Content (`post_content`) | ACF Textarea (`rows: 2`, `section_desc`) | `wp_kses_post()` |
| **Button Text / CTA Labels** | Native Button Block / Menu | ACF Text field (`cta_button_text`) | `esc_html()` |
| **Button Target URLs** | Native Link | ACF URL field (`cta_button_url`) | `esc_url()` |
| **Feature Grids / Strains** | Native WooCommerce Products | ACF Repeater (compact `'table'` layout) | Subfields with escaping |
| **Product Titles & Aroma Profiles** | Native Woo Product Title | Native Woo Product Short Description | `the_title()`, `woocommerce_template_single_excerpt()` |
| **Terpene Specifications / Science** | Native Woo Product Description | Native Woo Attributes (Dominant Terpenes, Strain Type) | Native attribute tables + Woo description |
| **Product Images & Galleries** | Native Woo Featured Image + Gallery | Native Woo Product Gallery | `woocommerce_show_product_images()` |
| **Sitewide Settings & Contacts** | Customizer / Menus | Elite Terpenes → Settings (Options page) | Global options with native fallback |

### 1.2 Minimalist ACF Pro Design Guidelines

To prevent the WordPress admin edit screen from becoming bloated or overwhelming:
1. **Tabs Are Mandatory:** Every custom page template with more than 3 custom fields must group fields under logical ACF Tab fields (e.g., `Hero`, `Terpene Profiles`, `Science & Testing`, `Call to Action`).
2. **Compact Rows for Textareas:** Always configure `'rows' => 2` or `'rows' => 3` for textareas. A 2-line description does not need a massive 12-line editor box.
3. **Compact Repeaters:** For repeaters (e.g., aroma notes, feature highlights, scientific stats), set `'layout' => 'table'` or use `'collapsed'` mode with the main title subfield as the row label.
4. **Return Format = ID:** For image and file fields, set `'return_format' => 'id'`. This keeps database payloads ultra-light and enables responsive `wp_get_attachment_image()` output.
5. **No Visual Clutter:** Do not add empty message fields, decorative separators, or complex conditional logic unless essential for client usability.

---

## 2. Media Library Rules

1. **Self-Hosted Attachments Only:** Every picture, banner, terpene profile diagram, and video must reside in the WordPress Media Library on `eliteterpenez.com`.
2. **Never Hotlink:** Zero external image URLs in shipped HTML markup or stylesheets.
3. **Native WordPress Image Functions:** Always use:
   ```php
   echo wp_get_attachment_image($image_id, 'large', false, ['class' => 'et-hero-img', 'loading' => 'eager']);
   ```
4. **Naming Convention:** All production assets must use clean, branded filenames:
   - ✅ `elite-terpenes-botanical-profile-og-kush.webp`
   - ✅ `elite-terpenes-lab-extraction-banner.jpg`
   - ❌ `abstrax_banner_01.jpg` (BANNED — source leak)
   - ❌ `screenshot_2026.png`
5. **Strip Metadata:** Remove all camera EXIF, GPS tags, and author metadata before uploading images to the media library.

---

## 3. Abstrax Tech Design Clone QA Standards

1. **Visual Fidelity:** Replicate the sleek, scientific, premium botanical branding of `abstraxtech.com`:
   - Sophisticated dark/clean color palette (precision accents, clean neutral backgrounds, high-contrast typography).
   - Clear visual hierarchy for aroma profiles, flavor categories (Botanical, Live Resin, Cannabis-Derived, Exotic Blends).
   - Responsive layout: mobile-first flexbox/grid with zero horizontal overflow.
2. **Performance Budget:** Fast Time-to-First-Byte (TTFB), minimal layout shift (CLS < 0.1), and Largest Contentful Paint (LCP < 2.5s).
3. **No Design-Source Leaks:** Inspect DOM, CSS stylesheets, image alt tags, and JSON-LD schema to verify zero occurrences of "Abstrax".

---

## 4. WordPress Technical & Performance Standards

1. **PHP Architecture:**
   - Target version: **PHP 8.2+** (Hostinger production is on PHP 8.3).
   - Enable strict types: `declare(strict_types=1);` at the top of all custom PHP files.
   - Prefix all custom functions, constants, and hooks with `elite_` or `ELITE_`.
2. **Output Escaping & Input Sanitization:**
   - Every output variable must be escaped: `esc_html()`, `esc_attr()`, `esc_url()`, `wp_kses_post()`.
   - Every input variable must be sanitized: `sanitize_text_field()`, `sanitize_key()`, `absint()`.
   - Protect all form posts and AJAX actions with `check_admin_referer()` or `check_ajax_referer()`.
3. **Asset Management:**
   - Register and enqueue all styles and scripts in `inc/assets.php`.
   - Never insert ad-hoc `<link>` or `<script>` tags in template files.
   - Use cache-busting version constants (`ELITE_VERSION`) tied to theme releases.
4. **Caching & CDN:**
   - Optimized for LiteSpeed Cache and Cloudflare.
   - Purge cache after every production deployment.

---

## 5. Cross-Site 48-Hour Free Delivery Protocol

1. **Synergy:** `justccell.com` (Vape hardware/pods) ↔ `eliteterpenez.com` (Terpenes/flavorings).
2. **Mechanism:**
   - Customer completes an order on Store A.
   - Hook `woocommerce_order_status_processing` / `woocommerce_order_status_completed` fires Action Scheduler job.
   - Action Scheduler calls Store B REST API (`POST /wp-json/wc/v3/coupons`) using dedicated read/write REST credentials.
   - Store B creates a unique coupon:
     - Code format: `JC-{order_id}` (for Justccell orders) or `ET-{order_id}` (for Elite Terpenes orders).
     - Discount type: `percent` (0% discount) with `free_shipping => true`.
     - Expiration date: Current time + 48 hours.
     - Usage limit: 1 time per coupon.
     - Customer email restriction: Tied to the purchasing customer's billing email.
   - Store A sends the customer a magic URL: `https://eliteterpenez.com/?apply_coupon=JC-{order_id}` via order confirmation screen and email notification.
   - Store B's `justccell-coupon-bridge` mu-plugin captures `apply_coupon`, persists it in the WooCommerce session, and automatically applies it to the cart upon adding products.

---

## 6. Deployment & Live Ops via Hostinger MCP

1. **Source of Truth:** Local directory `websites/eliteterpenez.com/eliteterpenez-theme/`.
2. **Live Theme Directory:** `public_html/wp-content/themes/eliteterpenez-theme/`.
3. **In-Place Updates Only:**
   - Deploy individual modified files via Hostinger MCP / TUS upload with `override=true`.
   - Never upload theme zip files to `wp-content/themes/`.
   - Never activate duplicate or hashed theme folders.
4. **Post-Deployment Verification:**
   - Verify uploaded file contents using `hosting_getWebsiteFileContentV1`.
   - Purge website cache via `hosting_clearWebsiteCacheV1`.
   - Immediately update `docs/BUILD-LOG.md` and `docs/STATUS.md` in the same turn.

---

## 7. Obsidian Vault Graph Integrity Standard

Every markdown file under `websites/eliteterpenez.com/` MUST:
1. Include Line 1 breadcrumb:
   `> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]`
2. Be indexed in `websites/eliteterpenez.com/INDEX.md` and `websites/index.md`.
3. Contain **ZERO naked hashes** in prose:
   - Wrap order IDs, tickets, hex codes, and technical keys in backticks: `\`#XXXX\``, `\`#123\``, `\`#0A2540\``.
   - Keep markdown headings standard (`# `, `## `, `### `).
