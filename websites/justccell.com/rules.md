> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# justccell.com — AI coder rules

**Read this before changing anything under `websites/justccell.com/`.**  
Client: **3Devices**. Live: https://justccell.com/  
Theme source of truth: `justccell-theme/` · Docs: `docs/` · Snapshot: `docs/STATUS.md`

These rules exist so the site stays **client-editable**, **media-correct**, **fast**, and **future-proof**. Do not invent shortcuts that violate them.

---

## 0. Non-negotiables (stop and fix if you break one)

1. **Backend-editable.** Clients edit Pages, Products, menus, and media in wp-admin — not theme PHP, not hard-coded strings in CSS, not Elementor.
2. **Media Library only for pictures and video.** Every image and video the visitor sees must be a WordPress Media Library attachment (`wp_get_attachment_image()` / `wp_get_attachment_url()`), picked in ACF or WooCommerce. Never hardcode an `<img src>` to another website, a CDN, or `/wp-content/themes/.../assets/img/...`. Theme folders may hold files only so **Justccell → Media** can copy them into Media; after that, the front end must use the Library URL. Fonts in CSS are the exception (self-hosted `.woff2`).
3. **No page builders.** No Elementor, Divi, Gutenberg “full page kits,” or similar. Custom theme + ACF + Default template.
4. **Inquiry-first.** Catalog is quote/inquiry, not open card checkout, until VAT + payments are explicitly ready. Wholesale **tier tables** on product pages are allowed (ex VAT). “Add to basket” must not create a Woo cart line until that switch is explicit.
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

---

## 1. CMS & content model (client can change everything)

### Required pattern

| What | Where the client edits it | Theme role |
|---|---|---|
| Homepage / About / Contact / brand pages | **Pages** + ACF field groups | Render only |
| Product clone pages | **WooCommerce Products** + ACF product fields (including Wholesale + Laser tabs) | Render only |
| Catalog listings | Products + categories + ACF listing fields | Render only |
| Instagram, WhatsApp, Telegram, collection, laser video, ES/CH landings | **Justccell → Storefront** | Render only |
| Menus | Appearance → Menus | Fallback PHP only if menu empty |
| Images / video | **Media Library** → attached via ACF image / gallery / file / featured image | `wp_get_attachment_image()` / attachment URL |
| Copy / headings | ACF text / WYSIWYG / heading fields | `justccell_echo_heading()` helpers |

### Do

- Prefer ACF on real Pages/Products with **Template = Default**.
- Seed once via **Justccell → CMS Import**, then treat the DB as source of truth.
- PHP arrays in `inc/catalog.php`, `inc/cms-content.php`, `inc/product-data.php` are **fallbacks only** until content is imported. Prefer ACF → Woo → PHP fallback order already in getters.
- After changing field groups, keep `acf-json/` in sync and re-sync on the live site if needed.
- New section on a page → new ACF fields + template part. Never “just hardcode the HTML forever.”

### Do not

- Ship final marketing copy only inside PHP or CSS `content:`.
- Add `page-about.php` / `page-contact.php` overrides that bypass the CMS unless STATUS says otherwise.
- Put client-facing strings only in JS.
- Leave duplicate sources of truth (theme array *and* ACF) without a clear fallback order.

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
- After every version bump (`JUSTCCELL_VERSION` + `style.css` Version), save a frozen copy:

```bash
rsync -a --delete justccell-theme/ archive/theme-releases/X.Y.Z/
```

  Name the folder the version number (`0.9.2`, `0.9.3`, …). Do not create `_deploy-theme-*` or extra slugs.
- Ship updates into **`wp-content/themes/justccell-theme/`** on the live site (same path every time).
- Hostinger MCP: `hosting_deployWordpressTheme` with `slug: justccell-theme` and **`activate: false`**. If files still land in a hashed folder, TUS-upload into `wp-content/themes/justccell-theme/` with `override=true`, then **uninstall** the hashed leftover immediately.
- Exclude bulky `assets/img/ref` and `archive/media-seed` from live deploys. Photos belong in the WordPress Media Library.

### After every live change

1. Confirm the CSS URL is `/wp-content/themes/justccell-theme/` (no hash suffix).
2. Clear Hostinger cache (+ LiteSpeed if available).
3. Update `docs/BUILD-LOG.md` (what shipped) and `docs/STATUS.md` (version + what’s approved).
4. Ask owner for hard-refresh (Cmd+Shift+R) when coming soon / admin view is in play.

### Do not

- Leave live ahead of STATUS/BUILD-LOG.
- Call theme deploy with `activate: true` (creates extra Justccell folders).
- Leave `justccell-theme-*` leftovers in Appearance → Themes. Delete them.
- Force-activate themes in a loop that hammers Hostinger rate limits.
- Turn off coming soon without explicit owner instruction.

---

## 7. Codebase map (for agents)

| Path | Purpose |
|---|---|
| `justccell-theme/` | Live theme source (only working copy) |
| `justccell-theme/inc/admin-menu.php` | wp-admin **Justccell** sidebar (Overview, Storefront, Header, leads, import, media) |
| `archive/theme-releases/` | Frozen local copies of each shipped version |
| `archive/media-seed/photos/` | Merged photo seed (old `justccell-media` packs) |
| `justccell-theme/inc/cms-*.php` | ACF getters, import, helpers |
| `justccell-theme/inc/catalog.php` | Catalog + home rails/blurbs |
| `justccell-theme/template-parts/` | Front markup |
| `justccell-theme/assets/css/` | Split CSS; bump version when changing |
| `docs/STATUS.md` | Current truth |
| `docs/BUILD-LOG.md` | Dated ship log |
| `justccell-theme/inc/commerce.php` | Buy box, chat, landings, laser/collection getters |
| `docs/client-requirements.md` | Client brief (1–6; **2/6 = 2026-08-26 merchandising**) |
| `rules.md` | This file |

Hostinger: user `u392808260`, WP software id `30055979`.

---

## 8. What we have done so far (context for AI)

Use this so you don’t redo or undo finished work. Refresh from STATUS if versions moved on.

- **Platform:** One WP + Woo on Hostinger; Cloudflare proxied; coming soon ON.
- **Theme:** Custom `justccell-theme` (no Elementor). CMS model: ACF on Pages/Products; CMS Import tool.
- **Homepage (in progress / QA):** visual clone — banner slider, product rails (~4-up + short blurbs, not full Woo dump), Customize / Premium Customization row, Trusted by collage; quote form under Trusted removed; WPML/language chrome stripped from header/footer where locked; Premium title forced charcoal `#333` per owner; Customize copy on `mon-b` to match `font-b`.
- **About:** rebuilt toward the approved about layout (see STATUS for version).
- **Contact:** next after homepage/About approval.
- **Deploy:** overwrite `wp-content/themes/justccell-theme/` only (`activate: false`). Hashed `justccell-theme-*` folders were a Hostinger activate bug — do not recreate them.
- **SEO / i18n stack chosen:** Rank Math Free + WPML + WCML + WPML SEO (hreflang in sitemap). Store prefixes custom; language not colliding with `/es/` store.
- **Still open:** full store/lang URL rewrite, B2B/B2C + VAT matrix, payments, ownership transfer checklist, turning coming soon off, pixel-perfect remaining pages. **2/6 merchandising layout is in theme 0.9.0** (not live until deploy).

Do not regress homepage CMS wiring, 4-up rails, or media/ACF patterns when touching later pages.

---

## 9. Working style for AI coders

1. Read **STATUS** → **this rules file** → relevant `docs/*` → then code.
2. Prefer the smallest diff. **If WPML, Rank Math, Woo, or ACF already has a setting, do not write PHP for it — list the wp-admin clicks for the owner.**
3. Fix the **live** CSS path the browser actually loads, not only local files.
4. Visual bugs: compare the approved HTML/CSS (classes like `font-b`, `g_tw`, `top_txt`) before guessing.
5. One page at a time; pause for owner approval when STATUS says so.
6. No drive-by refactors, no new markdown docs unless asked (except STATUS/BUILD-LOG updates when shipping).
7. Never commit secrets. Never disable security/cache “to make it easier.”
8. If something must be temporary (theme-bundled ref images, PHP fallback copy), label it in code comments and BUILD-LOG with a removal condition.

---

## 10. Quick checklist before you say “done”

- [ ] Client can change the content from wp-admin (ACF/Woo/Menus/Media)?
- [ ] Every photo/video on the front is a Media Library attachment (no theme-folder `src`, no other website)?
- [ ] **No ccell.com (or design-source) links, media URLs, preconnects, schema, or credits anywhere public?**
- [ ] No Elementor / no hardcoded permanent copy in theme for that section?
- [ ] CSS/JS enqueued; version bumped; live file verified; cache cleared?
- [ ] Fonts/colors/weights match the approved layout (or explicit owner override)?
- [ ] Store/lang URLs and inquiry-first behavior preserved?
- [ ] No outbound third-party storefront/CDN URLs in HTML, CSS, or JS?
- [ ] STATUS + BUILD-LOG updated?
- [ ] Coming soon still ON unless owner said otherwise?

---

*If a rule here conflicts with a newer `docs/STATUS.md` note, STATUS wins for “what shipped,” and this file should be updated in the same pass.*
