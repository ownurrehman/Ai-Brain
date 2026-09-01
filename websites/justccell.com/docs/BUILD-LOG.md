> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Build log

Append-only. Newest first. No passwords, API keys, or personal customer data.

Format: date, what shipped, what is next.

## 2026-09-01 — Location URL `/location/` (0.9.74)

**Done**

- Client renamed the page to https://justccell.com/location/. Theme now treats `location` as the canonical slug and still accepts `locations` as an alias.
- Office template, ACF group, header/footer links, and page seeding all follow `/location/`. `/locations/` 301s to `/location/`.
- Overwrote live `justccell-theme/` in place (`activate: false`). Cache cleared.

**Next**

1. Confirm `/location/` shows UK headquarters + map (log in past coming soon).
2. Spain/EU as a **new domain** covering all EU markets — not started; wait for the domain.

---

## 2026-09-01 — Locations UK-only (0.9.72)

**Done**

- Client: keep Locations to the UK for now. Removed Spain (opening soon) and Switzerland (Ecublens) from the Locations page defaults and from stored ACF `locations_items`.
- Page copy now talks about Bolton HQ only. Grid is a single card. **Pages → Locations** still owns the content.
- Overwrote live `justccell-theme/` in place (`activate: false`). Cache cleared.

**Next**

1. Client to confirm `/locations/` shows only the UK office (log in past coming soon).
2. Spain/EU as a **new domain** covering all EU markets — not started; wait for the domain.

---


**Done**

- Removed the previous hack stack that was breaking the grid: `wp_prepare_attachment_for_js` overrides, `wp_get_missing_image_subsizes` / `intermediate_image_sizes_advanced` empty-on-query filters, and the one-time `justccell_small_grid_patch` admin hook.
- Replaced thumbnail repair with WordPress-native `wp_generate_attachment_metadata()` batches. Small originals (smaller than 150×150) are marked complete without forcing a thumbnail file. Full-library regen runs when theme repair version bumps.
- Deployed **0.9.59** to live (`functions.php`, `media-sanitize.php`, `media-repair.php`, `media-import.php`). Old `jc-media-repair-cli.php` mu-plugin is not on the server. Hostinger cache cleared.

**Next**

1. Hard-refresh **Media → Library** grid (Cmd+Shift+R). The blank grid should be fixed now that core query handling is restored.
2. Open **Justccell → Media** and leave the page open until **Library thumbnails** says done (full metadata regen runs automatically).
3. Confirm grid tiles look correct, then we can run **Phase 2** SEO filename renaming.

---

## 2026-09-01 — Media grid repair + thumbnail backfill (0.9.58)

**Done**

- Diagnosed grid “thin strip” issue: wide originals were listed in Media Library but many lacked usable `thumbnail` sizes in `_wp_attachment_metadata`; repair queue was also stuck on undersized icons (e.g. attachment 61, 95×43px) that can never generate a 150×150 file.
- Shipped theme **0.9.58** to live: `wp_prepare_attachment_for_js` now guarantees a valid grid thumbnail payload; removed dead custom grid serializer; small-image handling in `media-repair.php`.
- Live API check: recent attachments return proper `150×150` thumbnail URLs. WPML language filter bypass for attachment queries remains in place.

**Next**

- Hard-refresh **Media → Library** grid (Cmd+Shift+R). If any tile still looks wrong, open **Justccell → Media** and leave the page open until thumbnail + filename steps complete.
- **Phase 2 (SEO rename)** is ready via `justccell_repair_rename_batch` but intentionally not run until you confirm the grid looks correct.

---

**Done**

- List view already had **690** items and the files/thumbnails are on disk. Grid showed two tiles because a custom `query-attachments` serializer (built for missing thumbs) does not match WordPress 7.1, and WPML was hiding the rest in English grid view.
- Removed that override. Core WordPress now builds the grid JSON. Attachment queries in wp-admin ignore WPML language so every photo shows in every language.

**Next**

- Hard-refresh Media → Library **grid** (Cmd-Shift-R). Switch to list if the first load is cached; both should now show the same ~690 items.

---## 2026-08-31 — Media Library thumbnails (0.9.56)

**Done**

- WordPress grid (Media Library + ACF Select Image) needs real `thumbnail` / `medium` files. Sideload was copying originals during admin-ajax without those sizes, and a filter was emptying sizes on every upload/REST request.
- **Justccell → Media** now runs three keep-the-page-open steps: copy seed files → clean filenames → write 150×150 thumbnails (and medium) in batches of 4.
- New uploads through Media Library / REST keep native WordPress sizes. The empty-sizes filter only applies to `query-attachments` so listing the library does not try to regenerate images.

**Next**

- Leave **Justccell → Media** open until it says thumbnails are ready, then hard-refresh Media → Library (grid) and any ACF image field.

------

## 2026-08-31 — Media Library grid (0.9.49)

**Done**

- Stopped enqueueing `admin-media.css` (flex + max-height 100% collapsed grid tiles).
- `query-attachments` and `rest_prepare_attachment` now inject the original file URL as thumbnail/medium/large/full. No thumb regeneration.
- `author` is an int; `post_parent=0` still means all; `suppress_filters` stays on so WPML does not hide items.

**Next**

- Hard-refresh wp-admin Media Library grid (Cmd-Shift-R). List view should still show ~690.

---

## 2026-08-30 — Strip setup import nags (0.9.43)

**Done**

- Removed the dashboard “15 Justccell field groups are now listed here” notice and stopped re-importing ACF groups on every admin load.
- Hid **CMS Import** and **Media** from the Justccell menu. Removed media-import, clone, and menu how-to admin banners.

**Next**

- Hard-refresh wp-admin. Dashboard should be clean of those setup messages.

---

## 2026-08-29 — Product clone gaps (0.9.41–0.9.42)

**Done**

- Highlight scroller no longer reserves 70vh per panel when the photo is missing (that was the white gap under Sandwave). Heating-core block only shows when that product has copy — batteries no longer share one EVOMAX image. Sparse ACF galleries, details, and feature rows fall back to that product’s own seed photos. Media seed folder is a sideload source; filenames are renamed and EXIF stripped on import.

**Next**

- Hard-refresh product URLs. Confirm Sandwave has no heating-core block, gallery thumbs, and highlight photos. Other SKUs pick up the same rules as remaining seed photos land.

---

## 2026-08-29 — Product colours + order module (0.9.40)

**Done**

- Colour dropdown is per product: WooCommerce Colour attribute and/or the **Colours** field on Edit Product. Generic Orange/Black/Blue/Green/Purple is gone. No colours on that product = dropdown hidden.
- Buy box is its own module under the product photo + specs (clone dart is copy | gallery again, same page background). Larger tap targets on tablet/mobile.

**Next**

- On each product: Edit Product → Wholesale buy box → Colours (one per line), or Attributes → Colour. Stylo will hide Colour until colours are saved.

---

## 2026-08-28 — Canonical product URLs (0.9.39)

**Done**

- Public product URL is only `/{category}/{slug}/` (e.g. `/all-in-ones/voca/`). Woo `get_permalink()`, Rank Math, and sitemaps emit that. `/product/{slug}/` stays a 301 into the canonical so it is not a second indexable page. Coming soon is still on, so this is locked before public indexing.

**Next**

- Confirm a product “View” goes to `/all-in-ones/…` not `/product/…`. Settings → Permalinks → Save if rewrite rules look stale.

---

## 2026-08-28 — Native Woo product URLs (0.9.38)

**Done**

- `/product/voca/` (and other Woo permalinks) 301 to the catalog URL `/{category}/{slug}/` and render the same product layout. Catalog URLs now query the WooCommerce product so **Edit Product** and the **Product page clone** ACF group on that product are the editors. PHP arrays remain fallback only when ACF is empty.

**Next**

- Open `/product/voca/` logged in — it should land on `/all-in-ones/voca/` looking complete. Edit via Products → Voca.

---

## 2026-08-28 — Edit Product on catalog URLs (0.9.37)

**Done**

- `/all-in-ones/flexcell-pro/` is a theme rewrite, not Woo’s native product URL, so the admin bar had no Edit Product. Clone URLs now add that link when a matching Woo product exists.

**Next**

- Hard-refresh a product page logged in and use **Edit Product** in the admin bar.

---

## 2026-08-28 — Stop animating tabs and Discover (0.9.36)

**Done**

- Scroll-up no longer runs on homepage category pills, Discover/blog cards, listing product cards, or tab bars. Motion stays on larger split sections (About, Why, product copy/media).

**Next**

- Hard-refresh `/` and `/discover/` — tabs and post cards should stay put while scrolling.

---

## 2026-08-28 — Homepage slider dots (0.9.35)

**Done**

- Hero pagination is no longer four solid center dots. Inactive marks are hollow rings; the current slide is a 50px white pill. The row sits on the right of the 72.9% column, same as the rest of the homepage grid.

**Next**

- Hard-refresh `/` and click the dots. Listing heroes reuse the same control.

---

## 2026-08-28 — Heading tags + SEO on evolving pages (0.9.34)

**Done**

- Every evolving page heading now has an H1–H6 dropdown in ACF (About, Why, 3.0, generic brand, home, contact, Discover, listings FAQ, Storefront laser + ES/CH landings). New fields merge into existing Field Groups without overwriting edits.
- Rank Math remains the SEO title / meta / canonical / social box. Theme help text on each group says so. Homepage devices heading defaults to the single H1.

**Next**

- Logged-in: open ACF → Field Groups or any evolving Page and confirm a heading-tag dropdown sits next to each heading. Check Rank Math on the same screen.

---

## 2026-08-28 — Explore More rail centered (0.9.33)

**Done**

- Product-page Explore More slider uses the same centered 72.9% column as the heading. Desktop shows three cards with equal side space; arrows sit on the column edges. Arrow buttons now step by one explore card.

**Next**

- Hard-refresh a product page (e.g. `/all-in-ones/flexcell-pro/`) and confirm the three-card row lines up under “Explore More”.

---

## 2026-08-28 — ACF groups in Field Groups UI (0.9.32)

**Done**

- Page field groups were PHP-only, so Custom Fields → Field Groups only listed leftover **Page sections**. The theme now publishes those groups into the ACF database on first wp-admin load, then stops PHP registration so the UI is the source of truth. Location uses a **Page slug** rule instead of page IDs. Unused **Page sections** builder is limited to the Flexible sections template.

**Next**

- Logged-in: open ACF → Field Groups and confirm About / Why / 3.0 / Page content / Contact / Discover / listings / product clone / Header / Forms / Storefront are listed. Hard-refresh one page of each type.

---

## 2026-08-28 — Compact laser-engraving band (0.9.31)

**Done**

- Product-page laser block is no longer a full-bleed black slab. It is a short off-white band, dark type, and a cropped ~220px video so it sits with the rest of the product page.

**Next**

- Hard-refresh a product with laser (e.g. `/all-in-ones/tank/`) and confirm height + light background.

---

## 2026-08-28 — ACF groups in Field Groups UI (0.9.30)

**Done**

- Page field groups were PHP-only, so Custom Fields → Field Groups only listed leftover **Page sections**. The theme now publishes those groups into the ACF database on first wp-admin load, then stops PHP registration so the UI is the source of truth. Location uses a **Page slug** rule instead of page IDs. Unused **Page sections** builder is limited to the Flexible sections template.

**Next**

- Logged-in: open ACF → Field Groups and confirm About / Why / 3.0 / Page content / Contact / Discover / listings / product clone / Header / Forms / Storefront are listed. Hard-refresh one page of each type.

---

## 2026-08-28 — Discover featured images: hardware, not lab stock

**Done**

- Replaced all 15 Discover featured images. The first set was generic Unsplash (lab benches, welding, USB cables). New images are 510 carts, batteries, ceramic cores, fill syringes, sample trays, and related hardware. Unique files (`justccell-v2-*.jpg`), new alts, old featured attachments removed.
- Articles and permalinks unchanged.

**Next**

- Logged-in hard-refresh `/discover/` so LiteSpeed is not showing the old cards.

---

## 2026-08-28 — Scroll reveals on About and catalog pages (0.9.29)

**Done**

- Section motion was firing on load and only moving 64px, so About (and other pages) looked static. Reveals now wait until a block is 160px into the viewport, slide 160px up, and use left/right on split layouts (company intro, homepage fill/premium, product buy box, Why rows, 3.0 splits). Culture heading + cards, listing product cards, and customer cards stagger. Heroes, header, footer, and the sticky product highlight stay still.

**Next**

- Hard-refresh `/about/`, the homepage, a category listing, and a product page (logged in) and scroll each section.

---

## 2026-08-28 — Contact country dropdown (0.9.28)

**Done**

- Contact form country list is United Kingdom, Spain, Switzerland, then Others. Those three always sort first even if extra countries are added under **Justccell → Forms**. The old United States / Canada default is replaced on first load.

**Next**

- Hard-refresh `/contact/` logged in and open the Country dropdown.

---

## 2026-08-28 — Discover editorial (15 posts, demo posts removed)

**Done**

- Deleted every previous WordPress post (the nine theme-seeded Discover stubs plus any leftover demo). Discover is no longer a placeholder grid.
- Published 15 original Justccell articles (5 Guides, 5 News, 5 Blogs), each 2000+ words, Rank Math title/meta/focus set, unique 1600x900 featured image with alt, WPML default language assigned.
- Theme seeder in `inc/blog.php` now also returns if `justccell_editorial_v1` is set, so a CMS import cannot recreate the stubs.
- Inventory: [post-registry.md](post-registry.md). Source pack: `content/discover-2026/`.

**Next**

- Logged-in hard-refresh `/discover/`, `/guides/`, `/news/`, `/blogs/`. Coming soon stays on for anonymous visitors.

---

## 2026-08-28 — Restore missing acf-page-groups.php (0.9.27)

**Done**

- Live fatal (`There has been a critical error`) was not the Discover CSS. `functions.php` required `inc/acf-page-groups.php` from the per-page ACF split, and that file was never on the server.
- Uploaded the missing file plus `cms-helpers.php`. `require` is now wrapped in `is_readable()`. ACF init only calls register functions that exist.

**Next**

- Hard-refresh `/discover/` logged in. Finish the per-page ACF split only with matching TUS overwrites (never require a file that is not in the same upload).

---

## 2026-08-28 — Per-page ACF editors (0.9.27)

**Done**

- Split the old unified Brand ACF group so each public page only shows fields that render on that URL: Justccell 3.0, Why Justccell, About, generic brand (solution / packaging / laser / choose-hardware / oil-types / 510-thread), legal, homepage tabs, contact crumb, Discover tabs, listing FAQ heading.
- Justccell 3.0 and Why rows (layout, intro image, stats, meet heading, compare heading, tab labels) read ACF first; PHP arrays are fallback only. 3.0 images no longer print a theme-folder URL.
- About company photo + section headings are ACF. Legal pages left the marketing clone and use `page.php` + the block editor so policy copy pastes in wp-admin.
- Gutenberg is hidden on clone pages. CMS Import seeds the new fields if empty. Products stay on Edit Product (wholesale + laser unchanged).

**Next**

- Logged-in: Justccell → CMS Import (pages, if empty). Then hard-refresh `/justccell-3-0/`, `/technology/`, `/about/`, `/privacy-policy/`, a generic brand URL, home, contact, Discover, a listing, and one product. Confirm each wp-admin screen matches the front.

---

## 2026-08-28 — Discover grid CSS (0.9.26)

**Done**

- Discover listing CSS was calculating widths/gaps with `--r`, but that unit only existed on the header/footer and Why pages. Tabs rendered as `AllGuidesNewsBlogs` and cards stacked as full-width images. `--r` now lives on `.d-clone`, the tab bar is `.d-tab` with ccell spacing, cards are a 3-column crop, hero is 44.8vw with a dark overlay so the H1 reads, container is 72.9%.
- All tab only lists Guides / News / Blogs posts, so Hello World / Uncategorized is out of the grid.

**Next**

- Hard-refresh `/discover/` logged in. Confirm All / Guides / News / Blogs and the 3-column cards.

---

## 2026-08-28 — Discover hub ACF (0.9.25)

**Done**

- Pages → Discover now has a **Discover hub** ACF group (after the title): hero title + tag, optional subtitle, desktop/mobile hero images, WYSIWYG intro above the grid. The post grid is still Posts (Guides / News / Blogs). Gutenberg is turned off on this posts page so the ACF fields are the editor.
- Empty fields seed once from the current page title and the Discover hero image in the Media Library. Replace them in ACF; the listing, Guides, News, and Blogs hubs all read the same chrome.

**Next**

- Open **Pages → Discover** (logged in). Change the hero image and heading, update, hard-refresh `/discover/`.

---

## 2026-08-28 — Discover as WordPress posts (0.9.24)

**Done**

- `/discover/` is now the posts hub (`page_for_posts`), not a static brand card page. Guides, News, and Blogs are native post categories at `/guides/`, `/news/`, `/blogs/` (no `/category/` prefix). The old 301s from those slugs into Discover are gone.
- Listing matches ccell: overlay hero, All / Guides / News / Blogs tab bar (navy underline), 3-column cards (featured image, two-line title, `YYYY-MM-DD`), pagination. Cards use the site-wide slide-up reveal. Single posts use the article + sticky related list.
- Seeded nine Justccell-branded sample posts so the grid is not empty. New posts: set featured image, assign Guides / News / Blogs, publish. Permalinks are `/%category%/%postname%/` so a News post lives at `/news/your-slug/`.

**Next**

- Hard-refresh `/discover/` while logged in. Click Guides / News / Blogs and open one article. Add or recategorize posts in **Posts → All Posts**.

---

## 2026-08-28 — Product tier table + chat dock (0.9.23)

**Done**

- Every product page shows a wholesale box in the genuineccell layout: Quantity / Per Item Price table (active row black), Colour dropdown, Select a Combination dropdown, quantity stepper, purple **Add to basket**. Clicking a tier row sets quantity. The button still opens a quote until paid checkout is switched on. Empty ACF offers use category defaults (pod/cartridge/battery) with colour options and a filled price table.
- WhatsApp and Telegram floating buttons always render. Storefront URLs win; otherwise the buttons open Contact (`?via=whatsapp` / `?via=telegram`). Paste `https://wa.me/…` and `https://t.me/…` in **Justccell → Storefront** when the client has handles.

**Next**

- Hard-refresh a product (e.g. Flexcell) while logged in. Confirm the table, both dropdowns, and the green/blue dock. Owner can replace default prices per product under Wholesale in the product editor.

---

## 2026-08-28 — Why Justccell heroes + site-wide scroll reveal (0.9.22)

**Done**

- The four Why Justccell pages (`/technology/`, `/safety/`, `/research/`, `/manufacture/`) now follow the reference layout: full-bleed overlay hero with white H1 and crumbs, four-tab bar under the hero (navy underline on the current page), then quote / split intro / alternating rows. Hero images are seeded as `assets/img/why/justccell-why-*` and sideloaded into the Media Library.
- Scroll-up-on-scroll (ccell `slideInUp2`) is now a first-party IntersectionObserver on `main.js` + `globals.css`. New sections on home, catalog, product, About, Contact, Why, and 3.0 slide up as they enter the viewport. Header, footer, and hero banners stay still. `prefers-reduced-motion` skips the motion.

**Next**

- Hard-refresh `/technology/` (logged-in) and walk the four Why tabs. Scroll the homepage and a product page to confirm sections lift.

---

## 2026-08-28 — Contact page cleanup (0.9.21)

**Done**

- Contact no longer falls back to a hashed source-site logo. The grey panel uses the Justccell Media Library wordmark (or a text wordmark), and leaky ACF logo attachments are ignored.
- FAQ sits in a centered 1100px column with inner padding instead of stretching full width.
- Contact form fields are two columns (source + message stay full width).
- ACF on the Contact page: Public emails repeater and Social links repeater (label, URL, icon, optional custom image). Empty socials still read Justccell → Storefront.

**Next**

- Edit Contact in wp-admin to add public emails and any extra socials. Hard-refresh `/contact/` after deploy.

---

## 2026-08-28 — Homepage device rail 3.7 cards (0.9.20)

**Done**

- Homepage product slider matches the reference: 3.7 cards in view (three large + a peek), 38px gap, left-aligned to the 1720px column, overflow clipped on the right. Dual 13.55% padding plus four-up math was packing five or six small cards across the full viewport.

**Next**

- Hard-refresh the homepage (logged-in) and confirm three large cards plus a sliver of the fourth.

---

## 2026-08-28 — Mega, buy box, gallery, packaging, GTM (0.9.18)

**Done**

- PRODUCTS mega matches ccell: full-bleed white panel under the header (`left:0; width:100%` of `nav`), inner cards at 72.9%. All-In-Ones is four oil-group cards (the previous 72.9%/translateX panel was a floating island; All-In-Ones was empty because oil groups read `slugs` instead of catalog `items`).
- Product buy box always has combination + pod dropdowns when ACF offers are empty. Selects stay native (no Select2). WooPayments is not deactivated.
- Gallery thumbs swap the centre photo by clearing WordPress `srcset`.
- `/packaging/` and `/laser-engraving/` are published, assigned to the default WPML language, permalinks flushed.
- Rank Math GA4 `G-JV1T79ZNB6` is stripped until a consent banner exists.

**Next**

- Owner connects Woo gateways / VAT in wp-admin. Owner creates the 3Devices admin user when they are happy with this pass.

---

## 2026-08-28 — Contact match, complete ACF, Forms manager (0.9.19)

**Done**

- Reworked Contact’s main area into the approved single grey rounded panel: brand/contact/social column on the left and the stacked white-field quote form on the right.
- Added Contact page ACF tabs for hero images/title, logo, all contact labels/details, social heading, form copy, distributor cards, and FAQ content.
- Added **Justccell → Forms** to manage inquiry/newsletter recipients, email subject prefixes, success/error messages, form placeholders, dropdown choices, and submit labels.
- Inquiry and newsletter handlers now use the centralized routing settings while continuing to save every submission under Quote leads.

**Next**

- In wp-admin, open Pages → Contact to edit page content and Justccell → Forms to set delivery addresses and form wording.

---

## 2026-08-28 — Public contact privacy fix (0.9.17)

**Done**

- Public Contact details and Organization schema now use only explicitly configured business addresses; the private WordPress administrator address remains available for lead delivery but is never printed publicly.

---

## 2026-08-28 — Mega match, product form, gallery, packaging (0.9.16)

**Done**

- PRODUCTS mega is full-width white again (ccell). Inner row is 72.9%. All-In-Ones tab is the four oil-group cards, not five SKUs.
- Empty `clone_offers` now uses the default combination + pod dropdowns (2/6). Hidden empty selects no longer show as a blank box.
- Gallery thumbs swap the centre image (clears `srcset` so the old photo cannot stick).
- Theme creates/publishes `/packaging/` and `/laser-engraving/` if they were missing or in trash, then flushes permalinks.
- Rank Math GA4/gtag is dequeued until a consent banner exists. WooCommerce Payments stays on.

**Next**

- Hard-refresh a product page, hover PRODUCTS, open `/packaging/` and `/laser-engraving/`.

---

## 2026-08-28 — Complete 3.0 and Contact pages (0.9.15)

**Done**

- Replaced the short 3.0 page with the complete responsive hero, technology bands, alternating feature rows, hardware rail, and inquiry CTA.
- Rebuilt Contact with its image hero, contact information, social links, two-column inquiry form, distributor cards, and FAQ accordion.
- Fixed Contact form validation so first/last name fields work and the contact-specific B2B form no longer fails for lacking a VAT field it does not display.
- All new page imagery is imported into the WordPress Media Library before rendering; theme-path image fallbacks and design-source references were removed.

**Next**

- Hard-refresh `/justccell-3-0/` and `/contact/` while logged in, then verify the configured sales/support email and telephone values.

---

## 2026-08-28 — Mega match, product form, gallery, packaging (0.9.16)

**Done**

- PRODUCTS mega is full-width white again (ccell). Inner row is 72.9%. All-In-Ones tab is the four oil-group cards, not five SKUs.
- Empty `clone_offers` now uses the default combination + pod dropdowns (2/6). Hidden empty selects no longer show as a blank box.
- Gallery thumbs swap the centre image (clears `srcset` so the old photo cannot stick).
- Theme creates/publishes `/packaging/` and `/laser-engraving/` if they were missing or in trash, then flushes permalinks.
- Rank Math GA4/gtag is dequeued until a consent banner exists. WooCommerce Payments stays on.

**Next**

- Hard-refresh a product page, hover PRODUCTS, open `/packaging/` and `/laser-engraving/`.

---

## 2026-08-28 — Product layout, heading colour, missing heroes (0.9.14)

**Done**

- Product listing, homepage device rail, and PRODUCTS mega dropdown now use the same ~72.9% centered column as ccell.com (side gutters, not edge-to-edge).
- Section and product titles are #333 like ccell. Blue stays on the CTA, active nav, “View all”, and ml specs.
- Woo/ACF products with an empty clone banner now fall back to the seeded hero art. Mini Tank and the other 36 product heroes sideloaded into Media on first view.

**Next**

- Hard-refresh `/all-in-ones/`, hover PRODUCTS, open Mini Tank. Staging still untouched.

---

## 2026-08-28 — Hero gap gone on every page (0.9.13)

**Done**

- Removed `.site-main` padding-top site-wide. 0.9.12 only zeroed the homepage; product/catalog heroes (e.g. `/all-in-ones/voca/`) still had the white strip. That exception is gone.

**Next**

- Hard-refresh any product or catalog URL.

---

## 2026-08-28 — Remove homepage gap under the menu (0.9.12)

**Done**

- `.site-main { padding-top: var(--navh) }` was the empty strip between the white bar and the hero. Homepage padding is now 0 so the slider sits against the menu. Other pages still pad so content is not under the fixed bar.

**Next**

- Hard-refresh the homepage.

---

## 2026-08-28 — Header height + hero gap (0.9.11)

**Done**

- White menu bar is 100px (60px under 1440px), same as ccell.com. Logged-in admin bar no longer covers the menu or leaves a dark strip above the slider.
- Homepage hero is `100vh − header` with a white (not #111) backing, flush under the bar like ccell.

**Next**

- Hard-refresh the homepage.

---

## 2026-08-28 — Homepage banner 1 restored to ccell.com first slide

**Done**

- Reverted the custom left-column copy (“Hardware for cannabis extracts”). Slide 1 is again the same 1920×970 artwork as ccell.com (`20250926/6d26d199…jpg`, Medical Grade Inhaler). Full-bleed image only — no HTML overlay.

**Next**

- Hard-refresh the homepage. If they still want the CCELL/Curaleaf badges painted out, do that in-image without adding new copy.

---

## 2026-08-28 — Theme 0.9.10 (live filename cleanup + homepage hero)

**Done**

- Worked on **live** justccell.com only (coming soon + search discouraged). Staging not touched.
- Media sanitizer now also catches leftover `public_uploads_*` thumbs, `Just-CCELL-*` names, and orphan files the first pass missed. **Justccell → Media** reports clean. Homepage HTML has no `public_uploads` / `Just-CCELL` URLs.
- Left WPML production key, 3devicescorp.com 301, Wordfence, and sitemap for later (client Hostinger / after development).

**Next**

- Client visual sign-off on the homepage. Storefront WhatsApp/Telegram. Do not enable Woo checkout until VAT/payments are explicit.

---

## 2026-08-28 — Staging + status refresh

**Done**

- Hostinger staging: `dev.justccell.com` (WP 30311599, `public_html/dev`). Cloudflare had no `dev` record (zone uses CF nameservers, Hostinger DNS empty). Added A `dev` → origin `187.124.156.180`, proxied, Flexible SSL **only** for that hostname (production stays Strict).
- STATUS.md rewritten as the living overview (done / messy / not built / client messages 1–7).

**Next**

- Client sees draft at https://dev.justccell.com/ (login to skip coming soon). Collect payment + UPS/FedEx + collection address. Do not enable Woo checkout yet.

---

## 2026-08-27 — Theme 0.9.6 (fatal hardening + theme screenshot)

**Done**

- Stopped a PHP TypeError on `admin_footer_text` when another plugin passes null.
- Added `screenshot.png` of the homepage for Appearance → Themes.

**Next**

- Overwrite live `justccell-theme/`. Purge LiteSpeed. Owner: hard-refresh Appearance → Themes.

---

## 2026-08-27 — Theme 0.9.5 (Rank Ray credits)

**Done**

- `style.css` Author / Author URI set to Rank Ray / https://rankray.com. Version 0.9.5.
- Rank Ray signatures in CSS (`:root --jc-developer`), JS, and PHP headers; HTML comment + `rel=author`; footer “Website by Rank Ray”; wp-admin footer and Justccell Overview.
- Storefront true/false to hide the public footer credit if needed. File comments remain.

**Next**

- Overwrite live `justccell-theme/`. Purge LiteSpeed / Cloudflare. Owner: Media sanitizer, Storefront chat URLs, WPML production key.

---

## 2026-08-27 — Theme 0.9.4 (Justccell wp-admin menu)

**Done**

- All Justccell screens sit under a left **Justccell** menu: Overview, Storefront, Header, Quote leads, CMS Import, Media.
- Overview splits keep (Storefront / Header / leads) from setup tools we will remove later.
- Bookmarks to Tools → Justccell Media / CMS Import and Appearance → Storefront / Header redirect to `admin.php?page=…`.

**Next**

- Owner: Justccell → Media until filenames are clean. Storefront chat URLs. WPML production key.

---

## 2026-08-26 — Theme 0.9.3 (clone + brief P0s)

**Done**

- Merged the page-clone audit with the 26 Aug comparison: filenames/EXIF are a live §0.10 breach (not P2). Coming soon ignored per owner.
- Media sanitizer in Tools → Justccell Media: rename `public_uploads_*` / 32-char hashes to `justccell-…` slugs and re-encode images without EXIF. New imports use the same names.
- Buy box moved into the product dart row (copy | gallery | buy). Server-rendered first-offer tiers, visible unit price, qty stepper, sticky quote bar. Add to basket stays a quote `<a>`. Pod default prices no longer applied to tanks/cartridges/batteries.
- `justccell_ensure_core_pages()` now untrashes/publishes packaging and laser-engraving.
- Anonymous `/wp-json/wp/v2/users` and author archives closed. Leaky `/about-ccell` and competitor SKU redirects removed.
- Contact: `s-hero` + form + distributors band + FAQ accordion. Homepage devices heading is `h1`. Catalog group/FAQ heading clamps raised to 60px/48px. Footer disclaimer is Storefront-editable; default has no Prop 65 URL. Inquiry honeypot + IP throttle; VAT stays in meta only.

**Next**

- Owner: Tools → Justccell Media until filenames are clean. WPML production key. Storefront chat URLs. 3Devices WP admin.

---

## 2026-08-26 — Local folder cleanup

**Done**

- Root is now `justccell-theme/` + `docs/` + `archive/`. Removed `_deploy-*`, numbered `justccell-media-*` packs, old zip, empty hold folder, and the brand-patch plugin.
- Merged photo packs into `archive/media-seed/photos/`. Frozen live theme at `archive/theme-releases/0.9.2/`.

---

## 2026-08-26 — Live overwrite Justccell 0.9.2 (same folder)

**Done**

- Patched `wp-content/themes/justccell-theme/` in place (Hostinger zip-activate was 429). No extra theme copy. Twenty Twenty-Five still the WP fallback.
- Hard WPML footer-switcher hide is gone on live. Footer language list is the WPML checkbox.

**Next**

- Owner: WPML → Languages → Language switcher options → uncheck footer if you do not want it. Uncheck unused languages. Hard-refresh; `/other/` should 301 to justccell.com.

---

## 2026-08-26 — UK default URL; WPML owns languages (theme 0.9.2)

**Done (local, not live until overwrite of `justccell-theme/`)**

- Country store only: bare **justccell.com** = UK. `/es/` and `/ch/` only. Pakistan and every other country stay on UK. `/other/` and `/uk/` 301 to the bare domain.
- **Languages stay WPML.** Theme no longer adds/removes WPML languages, no longer hides WPML’s switcher, no longer sets a parallel `jc_lang` cookie when WPML is active.
- Remove extra languages in **WPML → Languages** (uncheck Italian / Arabic / Russian). Do not code a custom switcher.

**Next**

- Overwrite live `justccell-theme/` with `activate: false` when ready. Owner unchecks unused WPML languages. Coming soon stays on.

---

## 2026-08-26 — One live theme folder (`justccell-theme`)

**Done**

- Locked practice: the site stays on **Justccell** in `wp-content/themes/justccell-theme/` (live Version 0.8.8). All future code updates overwrite that same folder.
- Hostinger `activate: true` was creating extra `justccell-theme-XXXX` copies. Rule 0.11 forbids that. Media import no longer scans hashed theme folders.
- Inactive leftovers still on disk until Hostinger uninstall stops timing out: `justccell-theme-WTGpp7yE` (0.4.7), `justccell-theme-cxZfJzuX` (0.8.0), `justccell-theme-68uMFfDD` (0.8.6). Safe to delete in Appearance → Themes. Keep Twenty Twenty-Five as WP fallback.

**Next**

- Delete the three inactive hashed themes in wp-admin. Next code ship overwrites `justccell-theme/` with `activate: false`.

---

## 2026-08-26 — Self-contained front end (theme 0.9.1)

**Done**

- Confirmed the live theme never `href`/`src`/`url()`/`fetch` a third-party storefront. Images resolve only via WordPress attachments.
- Removed third-party domain strings from shipped CSS comments and PHP file headers so page source and stylesheets do not mention another vendor’s site.
- Rule 0.9: if an outside host blocks our server, justccell.com must still render.

**Next**

- Deploy 0.9.1 so browsers pick up the cleaned CSS (cache-bust).

---

## 2026-08-26 — Wholesale box, ES/CH landings, laser + packaging (theme 0.9.0)

**Done**

- Rule book: 2/6 merchandising brief written into `client-requirements.md` and `rules.md`. UK = order site; inquiry-first kept (Add to basket → quote). No throwaway plugins in the client stack.
- Product pages: quantity/price table + two ACF-editable dropdowns (default combination/pod lists and ex-VAT tiers from the client). Collection note. Laser video block.
- Appearance → Storefront: Instagram, WhatsApp, Telegram, collection copy, laser file, Spain/Switzerland landing repeater.
- Pages: `/packaging/`, `/laser-engraving/`. Store aliases `/spain/` → es, `/swiss/` `/switzerland/` → ch.
- Client laser MP4 copied to `assets/video/laser-engraving.mp4` (sideload via CMS Import into Media).
- Coming soon **stayed on**. Hostinger plugin list **timed out** — live importer cleanup still pending.

**Next**

- Deploy 0.9.0 + CMS Import pages. Owner adds WhatsApp/Telegram URLs. QA `/uk/` product vs `/es/` landing.

---

## 2026-08-26 — About page clone vs ccell.com/about-ccell (theme 0.8.9)

**Done**

- About no longer uses the generic `s-clone` stack of three paragraphs.
- Matches ccell structure: full-bleed banner + crumbs, Mission/Vision/Values expand cards, company intro split (photo + cyan tagline), year timeline with giant year + prev/next, two Customer Centricity cards.
- Additive ACF only (`brand_culture`, `brand_customer`, `brand_tagline`, `brand_image_mobile`, timeline `year`). Old fields unchanged.
- PHP fallback drives the layout until culture cards are imported. About images live in `assets/img/about/` so slim deploys (no `ref/`) still sideload.
- Coming soon **stayed on**.

**Next**

- QA logged in: https://justccell.com/uk/about/ vs https://www.ccell.com/about-ccell
- Homepage still needs explicit approval. Contact is next after About approval.

---

## 2026-08-26 — Header from WP Menus, product mega images from CMS (theme 0.8.5)

**Done**

- Products mega cards now use Woo/ACF images (`clone_card_image` / product image), not empty hardcoded media keys after import.
- Live header is the **Primary** menu (Appearance → Menus): reorder, rename, nest Products tabs and Why dropdowns. Per-tab product picks are an ACF relationship on the menu item; otherwise products ticked “Feature in Products mega menu”.
- Samples button: Appearance → Header. Seeded menu “Justccell header”. Coming soon **stayed on**.

**Next**

- Hard-refresh, hover Products, confirm cards + images. Then Appearance → Menus to reorder.

---

## 2026-08-26 — Header hides on scroll down, shows on scroll up (theme 0.8.4)

**Done**

- Matched ccell’s `nav1_none` behaviour: scrolling down slides the white bar off the top in 0.6s; scrolling up brings it back. Mobile drawer stays pinned open. Coming soon **stayed on**.

**Next**

- Hard-refresh https://justccell.com/uk/ and scroll the homepage.

---

## 2026-08-25 — Batched CMS import UI (theme 0.8.1)

**Done**

- Tools → Justccell CMS Import now shows progress (pages / products filled).
- Separate actions: Import Pages, Import next 8 products, Dismiss WooCommerce setup wizard.
- Force overwrite checkbox. Product import always seeds from PHP catalog (not Woo loop).
- Coming soon unchanged.

**Next**

- Owner: run Pages once, then click Products batch until Complete. Then QA Pages → About and Products → Tank.

---

## 2026-08-25 — CMS content model (theme 0.8.0)

**Done**

- Real WordPress editing: ACF Pro field groups on Pages (brand, home, listings, contact) and Products (full product clone). Heading text + H1–H6 tag on every heading. Slider/gallery image fields map 1:1 to the front.
- Removed specialty `page-about.php` etc. Default `page.php` + ACF. Template dropdown stays Default.
- Tools → **Justccell CMS Import** seeds Pages/Products from the old PHP arrays (safe re-run; does not overwrite filled fields).
- Front templates read ACF/Woo first; PHP arrays remain fallback until import runs.
- Coming soon unchanged.

**Next**

- Deploy theme 0.8.0, then run **Tools → Justccell CMS Import** once while logged in.
- QA: Pages → About / Home, Products → Tank — change a heading/image, confirm front updates.
- After QA, PHP content files can be retired in a later pass.

---

## 2026-08-25 — Solid white header on first load (theme 0.7.1)

**Done**

- Header now matches ccell.com on first paint: opaque white bar (`#fff`) with dark links, sitting above the hero instead of transparent over it.
- Removed `jc-nav-over-hero` from homepage, catalog, and product pages. Hero starts below the 100px/60px bar. Coming soon **stayed on**.

**Next**

- Hard-refresh logged in at https://justccell.com/uk/ and confirm the bar is white before any scroll.

---

## 2026-08-25 — Catalog heroes + Media Library slider editors (theme 0.7.0)

**Done**

- All-In-Ones (and the other three catalog listings) now use ccell’s full-bleed hero: desktop + mobile banner, overlay heading/lede, crumbs, category tabs, group titles with cyan rule + copy, 4-up cards (tagline + cyan capacity), All-In-Ones FAQ.
- Hero JPEGs live in the theme `assets/img/ref/` pack. First logged-in view copies them into **Media → Library**, then the listing reads those attachments.
- WordPress page editors: Pages → All-In-Ones / Cartridges / Pod Systems / 510 Batteries (template **Catalog listing**) hold the **Catalog hero slider** repeater. Homepage holds **Homepage hero slider**. Tools → Justccell Media lists the same edit links.
- Slim deploy activated as `justccell-theme-apK7nT29`. Coming soon **stayed on**.

**Next**

- QA logged in: https://justccell.com/uk/all-in-ones/ vs https://www.ccell.com/all-in-ones
- Confirm Media Library has the five catalog hero files, then open Pages → All-In-Ones to swap slides.

---

## 2026-08-18 — Header/footer/fonts match ccell chrome (theme 0.6.1)

**Done**

- Registered ccell font family names (`mon-r` / `mon-m` / `Montserrat_s` …) against self-hosted Montserrat woff2.
- Header rebuilt to `show_nav`: 100px/60px fixed bar, 18px uppercase links, tabbed Products mega with large cards, Why dropdown, Get Samples CTA, language switcher, 1260px hamburger drawer.
- Footer rebuilt to `foot` / `foot_t` / `foot_form` rem-scaled layout; homepage uses ccell’s dark `#1127b0` footer. Coming soon **stayed on**.

**Next**

- QA logged in at 1920 and 375. Do not restyle product pages until chrome matches.

---

## 2026-08-16 — Header, footer, remaining pages (theme 0.6.0)

**Done**

- Header matches ccell chrome: Products mega (All-In-Ones oil groups + 4 SKUs elsewhere), Justccell 3.0, Why dropdown, Solution, About, Discover, Contact, samples CTA, language switcher. No CCELL store link.
- Footer matches ccell columns: newsletter + privacy consent, Products / Why / About / Solution, legal warning, Privacy / Terms / Cookies. Social icons stay hidden until URLs are added in Appearance → Customize → Justccell chrome.
- New pages: `/justccell-3-0/`, `/discover/` plus original guides (`/choose-hardware/`, `/oil-types/`, `/510-thread/`), `/terms/`, `/cookies/`. Static pages (About, Technology, Safety, Research, Manufacture, Solution, Privacy, Contact FAQ) filled to ccell section shape, Justccell-branded.
- Legacy ccell URL aliases 301 into live Justccell paths. Inquiries and newsletter signups store as private **Leads** in wp-admin and email the inquiry inbox.
- Coming soon **stayed on**.

**Next**

- Owner/3Devices: ownership, mailbox, photos, social URLs, translations, currencies, VAT. Do not turn coming soon off until QA.

---

## 2026-08-16 — Full live catalog clone (theme 0.5.6)

**Done**

- Scraped all 37 live ccell.com category products and downloaded unique hero/gallery/feature/detail photos into Media packs 2–5.
- Category listings now match ccell groups (Distillates / Live Rosins / Live Resins / All-Oil-Capable) with the full SKU list.
- Privacy policy page added. Coming soon **stayed on**.

**Next**

- QA logged in: first load of a new product may take several seconds while photos copy into Media Library. Hard-refresh after.

---

## 2026-08-16 — Full site clone pass (theme 0.5.5)

**Done**

- Header uses uploaded horizontal `Just-CCELL-logo-line.png` from Media Library; round PNG is the site icon. Stopped overwriting the logo with the old CCELL pack file.
- Front-end images only render from Media Library attachments (sideload from disk first). Plugin/theme folder URLs are no longer used as `<img>` sources.
- Wired all 22 catalog SKUs to `/{category}/{slug}/` clones. Tank keeps unique 360/highlights/details; other products use catalog photos until unique galleries exist in Media.
- Category clones at `/all-in-ones/` `/cartridge/` `/pod-system/` `/battery/` (ccell groups on All-In-Ones). Woo `/product-category/` redirects there.
- About, Why Justccell, Solution, Safety, Research, Manufacture, Contact cloned to ccell page shape. Coming soon **stayed on**.

**Next**

- QA logged in, hard-refresh homepage, category grids, a non-Tank product, About / Solution / Contact.

---

## 2026-08-16 — Tank layout closer to ccell (theme 0.5.4)

**Done**

- Audited live https://www.ccell.com/all-in-ones/tank against justccell Tank.
- Matched the product column (72.9%), taller hero type, no quote button in specs, highlight scroll at 70vh/slide, full-bleed EVOMAX panel, stacked detail mosaic, centered Explore cards (name + line + cyan capacity).
- Coming soon **stayed on**.

**Next**

- QA logged in, hard-refresh: https://justccell.com/uk/all-in-ones/tank/ vs https://www.ccell.com/all-in-ones/tank

---

## 2026-08-16 — Tank page actually renders (theme 0.5.3)

**Done**

- WordPress `extract()`s the pagination query var `page` into product templates. The clone was reading an empty `$page`, so the hero, title, specs, 360, and highlights never printed. Only Explore More (catalog, not `$page`) showed.
- Clone now loads Tank from `justccell_product` and never uses `$page`. First view copies Tank photos + 360 frames from the media pack into the Media Library (uploads), then serves those attachments.
- Slim deploy activated as `justccell-theme-XlizFji9`. Coming soon **stayed on**.

**Next**

- QA logged in, hard-refresh: https://justccell.com/uk/all-in-ones/tank/ vs https://www.ccell.com/all-in-ones/tank

---

## 2026-08-16 — Tank images + sticky scroll (theme 0.4.8)

**Done**

- Images were on the server but hidden: 36 stacked 360 frames at opacity 0, and the highlight block had no height so the scroll-pin never played.
- Tank now shows one 360 image (drag to spin), highlight images as full-bleed backgrounds, and a 550vh sticky scroll section like ccell.
- Route `/all-in-ones/tank/` even if permalinks have not flushed.
- Coming soon **stayed on**.

**Next**

- QA logged in: https://justccell.com/uk/all-in-ones/tank/ — drag the device, then keep scrolling through the five highlights.

---

## 2026-08-16 — Tank page scroll/360 match (theme 0.4.7)

**Done**

- Rebuilt Tank against the live ccell page: left specs + thumbnail strip, right **drag-to-spin 360**, then a **sticky full-viewport feature section** that changes as you scroll (same behaviour as `.high` on ccell).
- Coming soon **stayed on**.

**Next**

- QA logged in vs https://www.ccell.com/all-in-ones/tank
- Then Mini Tank / Eco Star or catalog grid.

---

## 2026-08-16 — Tank product page clone (theme 0.4.6)

**Done**

- Visual clone of ccell.com `/all-in-ones/tank` at `/{store}/all-in-ones/tank/` (prefix-safe). Inquiry-first: no prices, no cart.
- Homepage Tank card + Products mega-menu Tank link go to the product page. Quote CTA still goes to `/contact/?sku=tank`.
- Extra WCML currencies postponed (owner tired). Shop stays GBP default; theme still maps EUR/USD/CHF/AED by store URL when those currencies exist later. Do **not** switch WCML to “Site Language”.
- Coming soon **stayed on**.

**Next**

- QA Tank logged in, then clone Mini Tank / Eco Star or the All-In-Ones grid.
- When ready: WCML Client Location + add EUR, USD, CHF, AED.

---

## 2026-08-16 — Arabic + Russian kept (theme 0.4.5)

**Done**

- Owner confirmed AR + RU are intentional for additional customers, not wizard leftovers.
- Header selector now includes العربية and Русский. Theme lock no longer strips extra WPML languages.
- Store defaults unchanged (Dubai still defaults to English; Arabic is a switch).

**Next**

- Tank product page clone.

---

## 2026-08-16 — WPML/WCML wizard audit (theme 0.4.4)

**Done**

- Audited live WPML Languages + WCML Multi-currency after the wizards.
- URL format is **parameter** (`?lang=`, negotiation type 3). Browser redirect **Off**.
- Country prefixes still own `/es/` `/de/` `/uk/` `/us/` `/ae/` `/ch/` etc. Spain + English stays Spain (`/es/?lang=en`).
- WCML multi-currency is **independent** (not by language). Only **USD** is added as an extra WCML currency so far; store URL still sets GBP/EUR/CHF/AED in the theme.
- Coming soon **stayed on**. ACFML is on (fine with ACF Pro).
- Theme 0.4.4: WPML permalink switcher, currency filter wins over WCML, Hostinger autologin no longer blocked by coming soon.

**Fix remaining**

- WPML wizard also enabled **Arabic** and **Russian**. Uncheck them in WPML → Languages. Header already only lists EN/ES/FR/DE/IT.

**Next**

- Tank product page clone (logged in).
- Add GBP/EUR/CHF/AED in WCML when public prices exist — still never “currency by language”.

---

## 2026-08-14 — WPML activated in parameter mode (theme 0.4.2)

**Done**

- WPML Multilingual CMS 4.9.6, String Translation 3.5.3, WooCommerce Multilingual 5.5.7 **active**.
- Theme lock (`inc/wpml-lock.php`): `language_negotiation_type = 3` (`?lang=`), browser redirect Off, languages EN/ES/FR/DE/IT.
- `/es/` and `/de/` still set `jc_store` cookies (country stores). Coming soon **stayed on**.
- Left inactive: WPML Media, WPML Export/Import, ACFML.

**Next**

- If WPML still shows the setup wizard, pick **Language name as a parameter**. Never directories.
- WCML: currencies follow **store**, not language (Spain + English = EUR).
- Product page clone (Tank).

---

## 2026-08-14 — Coming soon back on; US / Dubai / CH / DE URLs booked (0.4.1)

**Done**

- Public gate: Minimal Coming Soon turned **back on**. Logged-in admins still see the real site. Plan in `docs/visibility.md`.
- Booked store prefixes before WPML: `/us` (USA, USD), `/ae` (Dubai/UAE, AED), `/ch` (Switzerland, CHF), `/de` (Germany, already). Aliases `/usa` → `/us`, `/dubai` and `/uae` → `/ae`.
- WPML still **not** activated.

**Next**

- Do not activate WPML until these store URLs are confirmed behind coming soon (log in to QA).
- Then WPML in parameter mode only.

---

## 2026-08-14 — Store prefixes live (theme 0.4.0). WPML still off.

**Done**

- `/{store}/` live: `/uk` `/es` `/de` `/fr` `/it` `/other`. Apex `/` 302s by Cloudflare country.
- Language stays `?lang=` on the same store (`/es/?lang=en` is still Spain / EUR).
- UK store reports GBP; EU stores EUR.
- Coming soon plugin was blocking the front end; deactivated so prefixes are testable.
- WCML 5.5.7 and OTGS Installer are on the server **inactive**. WPML core is not in the plugin list yet.

**WPML**

- Country URLs are ready. Do not run the WPML wizard unattended (must be parameter mode, not directories).

---

## 2026-08-14 — Translation plugin locked: WPML + WCML

**Done**

- Decision: WPML Multilingual CMS + WooCommerce Multilingual for languages/products/currencies.
- Geo/IP country stays a custom MU-plugin + Cloudflare, not a translation plugin.
- Recorded in `docs/translation-plugin.md`. Polylang is the only fallback. Weglot/TranslatePress/Multisite rejected.

**Next**

- Do not install WPML until `/{store}/` prefixes (or at least the MU-plugin) exist, so `/es/` is never registered as “Spanish language”.

---

## 2026-08-14 — Theme 0.3.0: language selector + B2B/B2C quote fields

**Done**

- Header language selector (EN / ES / FR / DE / IT) top-right. Persists `jc_lang` cookie via `?lang=`. Does **not** translate copy yet (no WPML / .po files). Store/currency still one default.
- Quote form: Consumer vs Business, VAT number required for B2B, delivery country label. Inquiry email includes account type + VAT.
- `inc/storefront.php` is the stub for later `/{store}/{lang}/` prefixes.
- Deployed to justccell.com (`hosting_deployWordpressTheme` activate true) and LiteSpeed purged.

**Next**

- Product page clone (Tank).
- Ownership transfer (Hostinger / Cloudflare / WP admin / email) to 3Devices.

---

## 2026-08-14 — Project docs + requirements locked into the plan

**Done**

- Created this documentation hub under `websites/justccell.com/docs/`.
- Recorded client sections 1/6, 3/6, 4/6, 5/6, 6/6. Noted **2/6 missing**.
- Locked architecture: one WP; `/{store}/{lang}/`; language ≠ country; 3devicescorp.com is an alias not a second shop; Spanish-entity VAT matrix; 3Devices ownership as P0.

**Next**

- Continue product-page clone (Tank) with prefix-safe URLs.
- Start ownership transfer checklist with 3Devices (Hostinger, Cloudflare, WP admin, email).
- Do not install WPML until store prefixes exist.

---

## 2026-08-14 — Homepage clone live on justccell.com

**Done**

- Custom theme `justccell-theme` 0.2.0 active (not Twenty Twenty-Five).
- Homepage clone of ccell.com: banners, category tabs, product rail, customize / fill / trusted / quote form.
- Inquiry-first Woo: prices and add-to-cart hidden in the theme.
- Seeded pages: contact, about, technology, safety, research, manufacture.
- Product categories: all-in-ones, cartridge, pod-system, battery.
- Self-hosted Montserrat; BEM + CSS variables; ACF JSON for flexible sections.
- 22 homepage products seeded as catalog data (reference images from ccell for **design approval only**).
- Cloudflare: Full Strict SSL, Always HTTPS, TLS 1.2+, HTTP/2+3, Brotli; Rocket Loader off.
- PHP 8.3.30, 512M, `expose_php` Off, session cookies secure/httponly, sodium.
- Plugins in play: WooCommerce 11.0.1, ACF Pro 6.8.7, AIOSEO 5.0.0.1 (kept), LiteSpeed Cache 7.9, UpdraftPlus, Hostinger Tools. Classic Editor deactivated. Wordfence installed inactive. CF7 inactive.
- Permalinks `/%postname%/`.

**Not done that day**

- Inner product templates, mega-menu completeness, footer pixel match.
- WebP, CF HTML cache rules, cart/checkout bypass (no real cart yet).
- Geo paths, languages, currencies, B2B/B2C VAT.
- 3devicescorp.com alias / email forward.
- Ownership inventory in 3Devices’ hands.

---

## 2026-08-14 — Hostinger WordPress + Cloudflare zone

**Done**

- justccell.com WordPress on Hostinger account `u392808260`, software id `30055979`.
- Document root `/home/u392808260/domains/justccell.com/public_html`.
- DB `u392808260_Jnr8B`.
- Cloudflare zone on, NS eugene/joyce.cloudflare.com, A apex → `187.124.156.180` proxied, www CNAME to apex.
- Theme deploys via Hostinger `hosting_deployWordpressTheme` (MCP cannot write `public_html` as a filesystem).

**Note**

- 3devicescorp.com WordPress id `30055771` created the same day — to be merged/redirected, not grown as a second site.

---

## 2026-08-16 — Rank Math replaces AIOSEO; WPML hreflang in sitemap

**Done**

- Rank Math SEO 1.0.276 active. AIOSEO removed from the install.
- WPML SEO 2.2.5 active (required for Rank Math sitemap hreflang).
- Locked decision: keep WPML’s default — hreflang **only in the Rank Math sitemap**, not in `<head>`. Do not add `WPML_SEO_ENABLE_SITEMAP_HREFLANG` false.
- Docs updated: architecture, translation-plugin, client-requirements, geo-language, roadmap C5, status.

**Not done**

- Public sitemap check: coming soon still returns HTML for `/sitemap_index.xml`.
- Product/Offer schema still off until prices are visible.
- Rank Math MCP Application Password not connected yet.

## 2026-08-25 — Theme 0.8.2 homepage fix

- Cap catalog/home card specs to 3 lines (prefer PHP seed over full Woo `clone_specs`).
- Banner aspect `1920/930`; slider scoped per `[data-banners]`.
- Heading helper auto-allows `<br>` / newlines.
- Deployed + activated via Hostinger; cache cleared.

## 2026-08-26 — Theme 0.8.3 heading/font match

- Homepage `.h-title` color `#333` (was brand blue).
- Use `mon-b` / `mon-eb` family names like ccell; named `@font-face` weights set to 400 so faces actually load.
- Deployed + activated `justccell-theme-j4zJ6oaW`.

## 2026-08-26 — Theme 0.8.4 homepage rail match

- Visible cards ~3.7 (ccell Swiper) with 38px gap.
- Curated home rails + marketing blurbs from ccell.
- Removed description clamp so full blurbs show.

## 2026-08-26 — Theme 0.8.5 homepage cleanup

- Removed homepage Get Samples form (ccell has it `display:none`).
- Trusted by: full-width collage image (1764×731), fixed aspect attrs.
- Removed header/mobile language switcher + footer store/lang context; hide WPML LS on front.
- Source of truth: `websites/justccell.com/justccell-theme/` (+ `_deploy-theme-0.8.5`).

## 2026-08-26 — Theme 0.8.6 product rail + Premium Customization

- Product rail: exactly **4 cards** visible (`calc((100% - 3*38px)/4)`), curated rails + ccell marketing blurbs, no description clamp.
- Premium Customization: match ccell `.g_tw` widths (~48/52), mon-b heading `#0504a8`, body 36px-scale, padding-left.
- Patched live `wp-content/themes/justccell-theme/` via TUS (activation API was rate-limited).
- Source: `websites/justccell.com/justccell-theme/` + `_deploy-theme-0.8.6/`.
