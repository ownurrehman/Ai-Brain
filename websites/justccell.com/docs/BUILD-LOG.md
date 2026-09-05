> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Build log

Append-only. Newest first. No passwords, API keys, or personal customer data.

Format: date, what shipped, what is next.

## 2026-09-05 — ACF migration Phase 2: Local JSON export (0.9.232)

**Done**

- Extended `inc/acf-migration.php` with `justccell_acf_run_migration_phase2()` — exports **15** unique DB field groups to `acf-json/group_{key}.json` on first wp-admin GET per theme version.
- Removed `group_jc_product_clone.json` delete-on-bump hook from `inc/acf.php`.
- Live + vault: **15** JSON files. Postmeta baseline unchanged: **3,287** `clone_*` value rows.
- wp-admin → ACF → Field Groups: all **15** rows show **Saved** under Local JSON.
- Brain: [[websites/justccell.com/docs/acf-local-json-migration|acf-local-json-migration.md]], `features-code-map.md`, `STATUS.md`.

**Shipped (live TUS)**

- `inc/acf-migration.php`, `inc/acf.php` (prior), `functions.php`, `style.css`
- `acf-json/group_*.json` (15 files on server; vault synced)

**Verify**

- Dashboard admin notice: *Wrote 15 field groups… clone_* postmeta rows: 3287 (unchanged: yes).*
- Product edit → Tank: fields still visible; `/all-in-ones/tank/` intact.
- **Phase 3 blocked** until manual sign-off.

---

## 2026-09-05 — ACF migration Phase 0–1: backup + product group dedup (0.9.228)

**Done**

- Added `inc/acf-migration.php` — one-time Phase 0 export + Phase 1 duplicate cleanup on wp-admin GET (`manage_options`).
- **Phase 0:** Exported **19** field groups to `uploads/justccell-acf-backups/acf-field-groups-2026-09-05-120736.json`. Vault copy: `backups/acf-field-groups-2026-09-05-120736.json`. Postmeta baseline: **59** products, **3287** `clone_*` value rows.
- **Phase 1:** Removed duplicate `group_jc_product_clone` row (orphan trashed, not deleted). Survivor: **Product page** (post ID 330066, **24** fields). Ran `justccell_acf_recover_product_clone_field_refs()`.
- Brain: [[websites/justccell.com/docs/acf-local-json-migration|acf-local-json-migration.md]], `backups/INDEX.md`, `rules.md`, `features-code-map.md`.

**Verify**

- ACF → Field Groups: **one** “Product page” row (no “Product page clone”).
- Edit Product → **Tank** — Product page fields visible (Banner, Heading, Tagline, Specs).
- Frontend `/all-in-ones/tank/` (logged-in admin): layout intact — H1, tagline H2, buy box, laser, EVOMAX block.
- **Phase 2 blocked** until manual sign-off.

---

## 2026-09-04 — Features code map + Rule §0.5 (vault)

**Done**

- Added [[websites/justccell.com/features-code-map|features-code-map.md]] — per-feature paths, hooks, functions, meta keys for the live theme.
- `rules.md` **Rule §0.5:** agents must read the map before auditing/modifying code, and must update it on every feature write/refactor/fix. §7 path table now points at the map as SSOT.
- Wired into `AGENTS.md`, `.cursorrules`, `INDEX.md`, `README.md`, `docs/architecture.md`, Cursor `justccell-page-content-editability.mdc`.

**Verify**

- Open `features-code-map.md` from the site hub. Confirm laser, Elite coupons, REST privacy, bio slug, buy box, and catalog rows match `justccell-theme/functions.php` includes.

---

## 2026-09-04 — Elite Terpenes free-delivery REST coupons (0.9.219)

**Done**

- Justccell theme: `inc/elite-cross-sell.php` — Action Scheduler + 4s `wp_remote_request` to Elite `POST /wp-json/wc/v3/coupons` (`JC-{order_id}`, 0% + `free_shipping`, 48h, usage 1, billing-email lock). Order meta `_elite_cross_sell_coupon`.
- Settings: **Justccell → Elite Cross-sell** (API URL, keys, visitor-facing card copy). Optional `JUSTCCELL_ELITE_*` wp-config constants.
- Thank-you card + `woocommerce_email_before_order_table` promo; magic link `https://eliteterpenez.com/?apply_coupon={code}`.
- Elite plugin `justccell-coupon-bridge` installed and active (shared Hostinger `u984013785`). Applies query coupon; REST keys generated; coupon-required Free shipping seeded. Regular plugin at `wp-content/plugins/justccell-coupon-bridge/` (not mu-plugin). Elite vault SSOT: [[websites/eliteterpenez.com/docs/cross-site-free-delivery|cross-site-free-delivery.md]].
- Live REST ping: HTTP 201 create then delete. **Save and test connection** on Justccell returned connected.

**Verify**

- **Justccell → Elite Cross-sell** — Enable on; test connection succeeds.
- Place a processing/completed test order → thank-you card + order meta `_elite_cross_sell_coupon`.
- Elite **WooCommerce → Justccell bridge** — keys present. Magic link applies coupon when the Elite shop is reachable.

---

## 2026-09-04 — Revert ACF JSON import + recover product field data (0.9.225)

**Done**

- **Reverted 0.9.224** — removed `acf_import_field_group()` JSON wipe; restored PHP `acf/load_field` sync for Product page fields.
- Deleted `acf-json/group_jc_product_clone.json` (live copy removed on first wp-admin load).
- Added catalog fields back to Product page group (`clone_card_tagline`, `clone_card_capacity`, `clone_card_image`, `clone_oil_group`, `clone_mega_featured`).
- One-time `justccell_acf_recover_product_clone_field_refs()` re-registers field keys from existing product postmeta (data was never deleted).

**Verify**

- Load **wp-admin** once (any screen) to run recovery.
- Open a filled product — banner, specs, catalog, heating data should repopulate.
- Frontend product page should render again.

---

## 2026-09-04 — Product page ACF: JSON sync + GUI as source of truth (0.9.224)

**Done**

- Added `acf-json/group_jc_product_clone.json` (title **Product page**, 17 fields incl. `clone_detail_1`–`3`).
- On theme version bump, `justccell_acf_maintain_product_clone_field_group()` imports that JSON into **ACF → Field Groups** via `acf_import_field_group()` so wp-admin matches the vault.
- Removed PHP `acf/load_field` overrides on `field_jc_prod_*` that were fighting GUI edits.

**Verify**

- **ACF → Field Groups → Product page** — shows Detail photo 1–3 (not old gallery). May show **Sync available** until first admin load after deploy imports JSON.
- Edit labels in GUI — they stick after save (no PHP rewrite).

---

## 2026-09-04 — Product detail photos: compact ACF image fields (0.9.221)

**Done**

- Replaced **Extra detail photos** gallery (`clone_details`) with three compact **Image** fields (`clone_detail_1`–`3`): label left, picker right.
- Frontend reads via `justccell_product_detail_photo_ids()` — new fields first, legacy gallery meta fallback so existing products keep their strip until re-saved.
- CMS import sets all three slots + legacy gallery when seeding from JSON.
- Admin CSS tightened for product edit screen (no tall gallery box).

**Verify**

- **Products → Edit** — three small image rows under heating, not a full-width gallery.
- Product with old gallery data still shows detail strip on the live page.
- Re-save product after swapping images — frontend updates.

---

## 2026-09-04 — Primary menu: strip custom ACF controls, use WP menu tree (0.9.220)

**Done**

- Removed **Item type** ACF field and all `init` hooks that auto-seeded or rewrote menu items.
- Header now follows the normal **Appearance → Menus** tree only: nested children = dropdown; submenu **Product categories** = product-card mega.
- Optional **Featured product cards** field shows only on category submenu rows (not every item).
- Admin tip on Menus screen: drag-indent to nest; add categories from the left panel.

**Verify**

- Edit **CCELL 3.0** — no Item type clutter; only optional product field on category children.
- **Menu Parent** dropdown is core WordPress (lists items in this menu); use drag-indent on the right list to nest.

---

## 2026-09-04 — Primary menu: editor-controlled labels + menu-driven mega (0.9.219)

**Done**

- Removed automatic nav title rewrites (`CCELL 3.0` → `Just CCELL 3.0`) and bio-page menu title forcing — **Appearance → Menus** labels render exactly as saved.
- Header item type is now menu-driven: **Products mega** only when **Item type** is set (or Auto + WooCommerce category children). Bio / CCELL 3.0 no longer auto-forced to duplicate product mega.
- Text dropdowns support nested 2–3 level submenu trees; product tabs only from category menu items.
- One-time cleanup sets the bio top-level item to **Text dropdown** so it stops mirroring Products mega until you change **Item type** yourself.

**Verify**

- Rename bio menu item to **CCELL 3.0** → hard-refresh header shows **CCELL 3.0**.
- **Products** still shows category tabs + cards when children are product categories.
- **CCELL 3.0** shows a normal link dropdown (edit children in Menus; set **Item type → Products mega** only if you want cards).

---

## 2026-09-04 — Footer menus + logo control (0.9.218)

**Done**

- **Appearance → Menus** now exposes **Footer Top** (column headings + nested links), **Footer Bottom** (social row), and **Footer Last** (Privacy / Terms / Cookies). Legacy Footer / Legal locations migrate automatically.
- Custom column walker outputs the existing `.foot_ul` markup from nested menu items (parent = heading, children = links).
- Footer logo: **Justccell → Storefront → Footer branding** image field, with fallback to Site Identity / header logo chain (fixes missing footer logo when brand filename scan fails).
- Default **Footer Top**, **Footer Bottom**, and **Footer Last** menus seeded on first load if unassigned.

**Verify**

- **Appearance → Menus** — three footer locations visible; edit Footer Top columns.
- Homepage footer — logo visible; link columns match menu; legal strip editable via Footer Last.

---

## 2026-09-04 — Order received: fix duplicate product thumbs (0.9.217)

**Done**

- Root cause: `woocommerce_order_item_name` wrapper (view-order) also ran on checkout `order-received` while `thankyou.php` already outputs `.jc-order-item__thumb` → two product images per row.
- `justccell_wc_should_wrap_order_item_row()` now skips order-received; thankyou uses plain `$item->get_name()` (no filter).
- Engraving artwork meta renders as a small 48px thumb in the meta list (not a second product image).

**Verify**

- `/checkout/order-received/329794/?key=…` — one product thumb per line; engraving preview only under “Engraving artwork”.

---

## 2026-09-04 — View order attribute lines inline (0.9.216)

**Done**

- Woo wraps variation values in `<p>` (block) — forced `.wc-item-meta-label` + `li p` to `display: inline` so rows read `Colour: Black` / `Tank Size: 0.5ml` on one line each.
- Engraving rows with images still stack label then thumb.
- Verified live on `/my-account/view-order/329479/` (logged in).

---

## 2026-09-04 — View order row layout + product thumbs (0.9.215)

**Done**

- Fixed broken flex on `td.product-name` (was laying out title, qty, and meta side-by-side).
- `inc/woocommerce.php`: inject product thumbnail + `.jc-wc-order-item` wrapper on view-order / checkout tables.
- `woocommerce.css`: thumb left / details right; clean `wc-item-meta` label:value lines; 48px engraving preview in meta.
- `laser-engraving.php`: engraving thumb uses classes (no inline styles); admin-only duplicate laser block after meta.
- Fixed broken `commerce.css` orphan rules after `.jc-order-address__body`.

**Verify**

- `/my-account/view-order/{id}/` — every row has product image; attributes + engraving stacked under title; total column aligned.

---

## 2026-09-04 — Order table polish: thumbnails, headers, typography (0.9.214)

**Done**

- `woocommerce.css`: 60px max product thumbnails in `.woocommerce-table--order-details`; flex product-name cell; soft `#e5e7eb` section header borders; sans-serif + `line-height: 1.6` on tables/addresses.
- `commerce.css`: aligned `.jc-order-item__thumb` to 60px; order-received panel titles use light divider; address/body typography inherits theme sans.
- `rules.md` §7.6: documented order table image constraints and typography rules.

**Verify**

- `/my-account/view-order/{id}/` — thumbnails ≤60px; product title/meta beside image; light section borders; Montserrat throughout.
- Post-checkout order received — `.jc-order-table` thumbs 60px; panel headers soft grey underline.

---

## 2026-09-04 — My Account: Woo core tables + editable addresses (0.9.209)

**Done**

- Account pages load WooCommerce `general` / `layout` / `smallscreen`. Cart/checkout still dequeue those sheets.
- Dashboard is the stock Woo template (custom tiles removed).
- Orders list is a normal `shop_table`: equal cell padding, `vertical-align: middle`. Status pill is on `<mark>` only (not the `<td>`). Same 0.9375rem type as other cells.
- View-order uses Woo’s Product / Total table (totals right-aligned). Intro marks stay bold, not yellow.
- Addresses: `<address>` is not italic. **Edit** is a primary button to Woo’s billing/shipping forms (`/my-account/edit-address/billing/` and `/shipping/`).
- `rules.md` §7.6 updated: do not invent a custom customer-area layout.

**Verify**

- Logged-in: `/my-account/orders/` — status bubble vertically centered with date/total; columns evenly padded.
- `/my-account/view-order/{id}/` — prices under Total.
- `/my-account/edit-address/` — Edit opens the native form; save works.
- Sheet `woocommerce.css?ver=0.9.209`. Woo `woocommerce.css` (plugin) also loads on account.

---

## 2026-09-04 — Account details form + password eye fix (0.9.213)

**Done**

- Double eye icons: removed theme `::after` mask on account (Woo core CSS already draws the toggle); excluded `.show-password-input` from `commerce.css` purple button rules.
- Edit account: max-width form, two-column first/last name, clean fieldset card, input focus states, purple Save CTA.

**Verify**

- `/my-account/edit-account/`: single eye icon per password field; form aligned and minimal.

---

## 2026-09-04 — My Account addresses alignment + edit buttons (0.9.212)

**Done**

- Fixed broken `commerce.css` selector (notice/button rules were merged incorrectly in 0.9.211).
- `my-account.php`: wrapped nav + content in `.jc-account__shell.woocommerce` so Woo float layout clears correctly below the hero.
- `woocommerce.css`: address cards (light border/padding), flex title row, purple **Edit** buttons, normal `address` typography.

**Verify**

- `/my-account/edit-address/`: billing/shipping sit in the content column beside nav; Edit links are purple buttons aligned with headings.

---

## 2026-09-04 — Woo native layout rollback + laser meta leak fix (0.9.210)

**Done**

- Re-enabled WooCommerce core CSS on cart, checkout, and my-account (`inc/woocommerce.php`).
- Stripped custom grid/flex/table layout from `woocommerce.css` and `commerce.css`; theme overlay is branding only (purple CTAs, header clearance, order-details table alignment).
- `my-account.php`: removed custom nav/content grid wrapper — stock Woo float/columns restored.
- Laser meta leak: `justccell_order_item_meta_lines()` no longer uses `get_formatted_meta_data('')`; added `justccell_laser_is_internal_meta_key()` + filters on `woocommerce_order_item_get_formatted_meta_data` and cart item data.
- `rules.md` §7.6 rewritten for native-structural CSS policy.

**Verify**

- `/cart/`, `/checkout/`, `/my-account/orders/`, `/my-account/view-order/{id}/`: native Woo layout; no squished order-details table.
- Cart + order received: only human-readable engraving labels (no `_justccell_laser_*` keys).

---

## 2026-09-04 — ACF product save nonce fix (0.9.208)

**Done**

- Root cause (0.9.204): `acf/prepare_field` returned `false` during `acf/validate_save_post` and product POST saves, stripping fields from ACF’s save registry → **“provided nonce failed verification.”** Legacy `acf_delete_field()` purge also ran on `acf/init` during admin requests overlapping saves.
- Fix: `justccell_acf_should_hide_field_in_ui()` gates all UI-only hides (no hide on POST or any AJAX). `justccell_acf_maintain_product_clone_field_group()` runs on safe GET `admin_init` only. Documented in `rules.md` §7.7.

**Verify**

- Products → Edit Product → Update: saves without nonce error. Variations/attributes/prices unchanged.
- Legacy Colours / `field_jc_prod_*` ghosts still hidden on screen load.

---

## 2026-09-04 — Account password toggle grey pills (0.9.207)

**Done**

- WooCommerce `button.show-password-input` was unstyled (core WC CSS dequeued) and rendered as a 16×6 grey UA pill under every password field on `/my-account/edit-account/` (and login/register).
- `woocommerce.css`: wrap `.password-input` as relative; sit the toggle inside the field with a mask eye icon; exclude it from the purple CTA rule.
- `rules.md` §7.6 documents the toggle requirement.

**Verify**

- Logged-in `/my-account/edit-account/`: no grey pills below Current / New / Confirm password. Eye control sits inside the right edge of each field. Sheet `woocommerce.css?ver=0.9.207`.

---

## 2026-09-04 — 57-product catalog lock + redirect cleanup (0.9.206)

**Done**

- `rules.md` **§7.8**: permanent catalog = **57 published Woo products** (21 core + 36 imported expansion SKUs). Obsolete “catalog cut / trash 36 clones” instructions superseded.
- `inc/catalog-redirects.php`: removed 2026 catalog-cut “trashed SKU → category” map. Keeps slug renames, legacy path aliases, and live-product skip guard only.
- `template-parts/product/clone.php` + `product.css`: `.p-dart__box--no-stage` when a product has no gallery/360° (sparse imported SKUs).
- Legacy ACF purge (`clone_colours`, ghost `field_jc_prod_*`) from 0.9.204 — Woo attributes only for colours/variations.

**Verify**

- Logged-in: `/all-in-ones/tank/` (and other imported SKUs) return **200**, not 301 to category hub.
- Products → Edit Product: no Colours ghost field. Variable products: colour attribute drives buy box + hero swap.
- Product with empty specs/highlights: no empty section shells.

---

## 2026-09-04 — WooCommerce Apple-style light UI (0.9.205)

**Done**

- Cart / checkout / my-account are **white / `#f9f9fb`**, not navy. Sidebar, “Your order”, cart totals, notices, and account hero are light cards with `#e5e7eb` borders. Purple is CTAs only.
- Fixed header overlap: `.jc-shop` padding-top is `calc(var(--jc-header-h) + 2rem)` so crumbs and H1s clear the fixed 100px nav.
- View-order `<mark>` (order number / date / status): transparent background, bold `#111`.
- Cart table: dropped `table-layout: fixed` + 6rem qty column; qty column 11.5rem; horizontal scroll on mid widths; stack under 780px.
- Checkout H1 via `justccell_checkout_page_header()`. Documented in `rules.md` §7.6.

**Verify**

- Logged-in desktop: `/cart/`, `/checkout/`, `/my-account/`, `/my-account/orders/`, `/my-account/view-order/329479/`, `/my-account/edit-account/`, `/my-account/downloads/`.
- Headings sit below the header. No yellow highlights on view-order. Qty stepper does not overlap Subtotal.
- Sheet: `woocommerce.css?ver=0.9.205`.

---

## 2026-09-04 — Legacy clone ACF purge + sparse PDP fallbacks (0.9.204)

**Done**

- Retired `clone_colours`, `clone_gallery`, `clone_offers`, and other pre-Woo Product page ghosts. Registry + UI hide in `inc/cms-helpers.php`; DB field delete on version bump in `inc/acf.php`.
- Colour/combination pickers and variation gallery swaps stay **WooCommerce-only** (`inc/commerce.php`, `assets/js/product.js`). Documented in `rules.md` §7.7.
- `template-parts/product/clone.php` skips the hero stage when there is no gallery or 360° set (imported clones with sparse media).
- CMS Import seeds gallery into `_product_image_gallery` only (no `clone_gallery` write).

**Verify**

- Edit Product → Product page tab: no **Colours**, **Gallery**, or **Offers** ghost fields.
- Variable product: colour attribute drives buy-box dropdown + hero image swap.
- Product with empty highlight slides / specs: no empty `.p-high` / `.p-specs` blocks.

---

## 2026-09-04 — Account details two-column grid (0.9.203)

**Done**

- Woo Blocks CSS was forcing every `.form-row` to `grid-column: 1 / -1`, so First name / Last name stacked. Theme now sets `.form-row-first` / `.form-row-last` to `span 1` and hides Woo’s float-clear `.clear` divs.
- Cache-bust: `JUSTCCELL_VERSION` **0.9.203**.

**Verify**

- `/my-account/edit-account/`: first and last name share one row (`getBoundingClientRect().top` equal). Sheet is `woocommerce.css?ver=0.9.203`.

---

## 2026-09-04 — WooCommerce cart / checkout / account UI (0.9.202)

**Done**

- Purged the `ccell-3-0` alias from `inc/static-pages.php` so CMS Import cannot re-seed the old bio slug. Canonical key remains `justccell-3-0`. Removed `ccell-3-0` from the ACF managed-slug dropdown.
- New `assets/css/woocommerce.css` overhauls native endpoints: my-account sidebar card + order status pills + two-column address/account forms; cart table + buy-box qty stepper + sticky totals + purple `.checkout-button`; two-column checkout with sticky dark “Your order” card; dark notice banners with accent rails.
- Enqueued from `inc/assets.php` on cart / checkout / account (after `commerce.css`). Cart qty plus/minus via Woo quantity-field hooks + `cart-wording.js`. Documented in `rules.md` §7.6.

**Verify**

- Logged-in: `/cart/`, `/checkout/`, `/my-account/`, `/my-account/orders/` load `woocommerce.css?ver=0.9.202`.
- Account nav active tab has a primary left border. Orders show pill statuses. Checkout is two columns from 960px with sticky order review.
- CMS Import page loop has no `ccell-3-0` array key.

---

## 2026-09-04 — Canonical `/justccell-3-0/` + sample-copy purge (0.9.201)

**Done**

- Killed the seeder/redirect loop: theme no longer 301s `/justccell-3-0/` → `/ccell-3-0/`. Map is **legacy → canonical only** (`/ccell-3-0/`, `/ccell-3.0/`, `/justccell-3.0/` → `/justccell-3-0/`).
- Seeders, menus, chrome, nav fallbacks, listing CTAs default to slug `justccell-3-0` / title **Just CCELL 3.0**. `justccell_canonicalize_bio_page_slug()` renames leftover `ccell-3-0` pages on init.
- Purged sample/tray/form/3–15-day language from `inc/static-pages.php` seed copy. Contact FAQ scrubber drops any Q/A containing “sample”. Copy-policy **v0993** scrubs Contact/brand CTA ACF to wholesale inquiry defaults.
- Documented in `rules.md` §7.5 + §0.13 (Obsidian memory).

**Verify**

- Logged-in: `/justccell-3-0/` is 200 (not a bounce back to `/ccell-3-0/`). `/ccell-3-0/` 301s to `/justccell-3-0/`.
- Contact / About / Why seed fallbacks have no “Get Samples” / sample-tray CTAs.

---

## 2026-09-04 — Product PDP semantic HTML / SEO (0.9.197)

**Done**

- Deleted ACF **Banner heading** and **Banner text**. Hero banner is image + breadcrumbs only.
- Renamed **Blue text below heading** → **Product Tagline** (`clone_subtitle`) → frontend `<h2 class="p-dart__sub">`.
- **Product heading** is the sole page `<h1>`. Specs: **Specs section title** `<h3>` + semantic `<ul class="p-specs">`.
- Woo **Product description** stays on the product edit screen (ACF no longer hides `the_content`); TinyMCE allows H2/H3/lists.
- `rules.md` §7.4.

**Verify**

- Products → Edit: Banner heading/text gone; Product Tagline + Specs section title present; Product description editor visible.
- Front: one H1 in `.p-dart__copy`, tagline is H2, specs list is UL under H3.

---

## 2026-09-04 — REST privacy, Tank gallery, catalog cleanup (0.9.198)

**Done**

- `inc/rest-privacy.php` — block anonymous `/wp/v2/product(s)` + Woo product REST while coming soon is on.
- `product.js` — `bindVariationGallery()` swaps main still image on colour/variation change (Tank buy box).
- Mobile: highlight copy overflow + admin-bar/hamburger z-index fixes (`product.css`, `chrome.css`).
- Removed 37-product PHP catalog fallback from frontend; seed moved to `inc/catalog-seed.php` (CMS Import only).
- Header nav: `justccell_sanitize_nav_label()` rewrites **CCELL 3.0** → **Justccell 3.0** (+ one-time menu DB upgrade).
- Documented in `rules.md` §7.3.

**Verify**

- Logged out: `GET /wp-json/wp/v2/product` → 401 (not 200 with product list).
- Tank product: change colour → hero image updates; mobile highlight section no horizontal scroll.

---

## 2026-09-03 — Highlight slide text colour ACF (0.9.195)

**Done**

- Product **Highlight slides** repeater: new **`text_color`** select (black default, white for dark photos) — one value for heading + paragraph.
- Template applies `.p-high__txt--white`; documented in `rules.md` §7.2.

**Verify**

- Products → Edit → Highlight slides → set slide to White on dark photo → heading + body readable on front end.

---

## 2026-09-03 — B2B buy box pricing UI (0.9.194)

**Done**

- Tier table: light bordered grid, `.active-tier` row highlight (5% primary tint) synced to qty input in `product.js`.
- Buy box hierarchy inverted: **Total** hero (`2rem`), unit line subdued (`/ unit (range tier)`), `ex VAT` muted beside total; removed “Your price” kicker.
- Documented standard in `rules.md` §7.1 for all agents.

**Verify**

- Product page: change qty → matching tier row highlights; total updates above Add to cart.
- Laser + tiers: hardware/engraving breakdown lines still show when engraving active.

---

## 2026-09-03 — Dead catalog product URLs → real 404 (0.9.190)

**Done**

- `inc/product-pages.php`: when `/{category}/{slug}/` rewrite matches but no Woo product (or category mismatch), force `set_404()` + `status_header(404)` — same trap fix already used for listing URLs. Unknown slugs no longer fall through to Discover (`page_for_posts`) at HTTP 200.
- `inc/blog.php`: Discover seed post copy scrubbed of “sample tray” / “request samples” editorial wording (client no-samples policy).
- Copy-policy sitewide CTA purge shipped earlier same day (0.9.190).

**Verify**

- `/cartridge/this-slug-does-not-exist-xyz/` → HTTP 404 + theme `404.php` (not Discover).
- `/cartridge/th2-evomax/`, `/all-in-ones/voca/` → 200 product clone.
- `/cartridge/`, `/all-in-ones/` → listing clone (not Discover).
- Rank Math 301s for `th2-evo` / `m6t-evo` unchanged.

---


**Done**

- Architecture standard: `docs/laser-engraving-system.md` (linked from site INDEX).
- ACF product + category defaults (`enable_engraving`, setup fee, tiers, canvas plate, safe zones).
- Inline Fabric.js editor in the buy box (self-hosted vendor — no CDN); monochrome logo filter; live tier pricing; ATC intercept with Base64 → cart → order file under `uploads/laser-engravings/`.
- Catalog stays inquiry-first; engraved ATC is the explicit cart exception.

**Next**

- Deploy theme 0.9.114; enable engraving on a pilot SKU (e.g. Eco Star / Eazie Pro) with plate + safe zone + tiers; verify cart thumb and order meta.

---

## 2026-09-02 — 404 contrast + product rail (0.9.92)

**Done**

- Heading and giant **404** are explicit white on a navy panel (global `h1` color was washing the title out).
- Categories and the quote rail pull live Woo SKUs (not only homepage rail slugs), so cartridges / pods / batteries get real photos.
- Search sits beside the panel. Not a ccell clone — Justccell navy panel + home-style product cards.

**Next**

- Hard-refresh `/this-is-not-a-page/` desktop and phone. Prev/next on the rail.

---

## 2026-09-02 — Launch-file catalogue lock (plan)

**What happened**

- Client sent 17 launch PDFs (20 pages) in the Mazhar / Just CCELL Devices Launch files folder.
- Live Woo still has the 47-SKU ccell.com clone.

**Decision**

- Public catalogue = **19 SKUs**. Spec + keep/drop: [product-catalog.md](product-catalog.md).
- Hermes labor prompts: [hermes-prompts-product-catalog.md](hermes-prompts-product-catalog.md).
- Cursor owns Equipment category, 301s, PDF crop pack, Tuner layout, theme fallbacks.

**Next**

- Run Hermes prompts 1–5 in order.
- Then Cursor implements 301s + nav + crops.

---

## 2026-09-02 — Contact 404 + 404 page (0.9.88)

**What happened**

- Page 12 (`/contact/`) is published with the Justccell Contact template. English `/contact/` was already 200.
- WPML 404s the same slug when the language has no translation: `/contact/?lang=es` and `?lang=de` rendered the theme 404. Logged-in Rank Ray with a non-English WPML language hits that. Advanced DB Cleaner was open in the same session; the page itself was not deleted (still ID 12 from 14 Aug).

**Done**

- WPML lock: pages/posts/products **display as translated** (English fallback instead of 404).
- If a published page still 404s (WPML or rewrite), rebind it from the slug with `suppress_filters` on `wp` (so Rank Math keeps the real title).
- 404 page: navy hero, search, quote CTA, four category cards, Discover / About / Why / 3.0 / Location. No empty white band.

**Next**

- Hard-refresh `/contact/` logged in (try the WPML language switcher to Spanish). Open a fake URL such as `/this-is-not-a-page/` for the new 404.

---

## 2026-09-02 — Product editor ACF labels (0.9.87)

**Done**

- Product field group title is **Product page** (was “Product page clone”).
- Tabs match the live page: Hero, Features, Heating, Details, Catalog, Quote, Laser.
- Field labels shortened; “How this box works” / “How to edit” messages stay hidden. Field names (`clone_*`) unchanged.
- Collection note sits next to Show quote box. Feature rows collapse to the heading.

**Next**

- Open any product in wp-admin and confirm the tabs read cleanly. First admin load writes the same labels into the ACF field group in the database.

---

## 2026-09-02 — Laser engraving page (0.9.85–0.9.86)

**Done**

- `/laser-engraving/` no longer uses the generic brand dump (kicker, stacked H2s, unframed video, navy CTA, CMS-admin copy).
- Dedicated layout in the About / Location language: overlay hero with the laser film, breadcrumb, split intro + player, numbered service cards, how-to-brief steps, hardware links, inquiry CTA.
- Public copy no longer mentions Appearance → Storefront or PHP. Pages → Laser engraving still owns the fields.
- 0.9.86: darker hero overlay so the title reads on the yellow film; mobile quote buttons no longer sit under the chat dock.

**Next**

- Hard-refresh https://justccell.com/laser-engraving/ (logged in). Check desktop and a phone width.

---

## 2026-09-02 — No on-page editor hints (0.9.84)

---

## 2026-09-02 — No on-page editor hints (0.9.84)

Removed the logged-in banner instruction on product pages and the ACF “How to edit” message.

---

## 2026-09-02 — Restore product hero CSS (0.9.83)

**Done**

- 0.9.82 dropped the base `.p-banner` rule (height, `position: relative`, overflow). Banner image and white H1 became `position: absolute` against the page, overlapping specs, gallery, and buy box (Voca Pro and every clone SKU).
- Restored `.p-banner { position: relative; height: 44.8vw; min-height: 18rem; overflow: hidden; }`. Empty-banner hint styles kept.

**Next**

1. Hard-refresh a filled SKU (Voca Pro, Mini Tank, Tank) and an empty SKU (Eazie Pod 3.0).

---

## 2026-09-01 — Product page is always the clone (0.9.82)

**Done**

- Public product URL always uses the clone template. No Woo generic fallback. No PHP seed images/copy on the front. Data is Woo + ACF (Media Library attachments).
- **Add new product** shows **Product page clone** after the title (classic editor; Woo block product editor forced off). Fill tabs → page looks like Tank.
- CMS Import is optional leftover, not required to get the design.

**Next**

1. Client: Media Library → named files → ACF / Product image. Duplicate a filled SKU to copy fields.

---

## 2026-09-01 — New products use clone layout without ACF seed (0.9.81)

**Done**

- Product clone required ACF banner/tagline. **Add new** SKUs (Eazie Pod 3.0, Flo, GemBar, AirOne, Blade, …) fell through to a blank Woo page: title + quote link, no clone design.
- Theme now always renders the clone layout for any Woo product. Empty ACF still shows banner chrome, specs (if any), wholesale box defaults, laser, explore. Photos come from ACF, else Woo **Product image / gallery** (Media Library attachments). No theme-folder images.
- Client still must upload named files to Media, then attach in Product page clone. Duplicate a filled SKU to copy ACF.

**Next**

1. Client uploads product photos to Media (`justccell-{slug}-banner.jpg` etc.), then sets Featured image + ACF banner/gallery/card.
2. Do not scrape manufacturer images into the theme.

---

## 2026-09-01 — Page templates + Duplicate for cloned landings (0.9.80)

**Done**

- ACF was tied to page **slug**, so a renamed duplicate did not get fields. Templates **Justccell Home / Contact / About / Why / 3.0 / Brand / Catalog / Discover / Location / Legal** now drive layout + ACF. Existing pages get templates on admin load.
- **Pages → Duplicate** copies template, ACF, and Rank Math meta to a draft.
- Homepage ACF reads the current Home-template page (so a campaign home is not stuck to the UK front page).
- Editor guide: [cms-editor-guide.md](cms-editor-guide.md). Hard rule: images only via Media Library.

**Next**

1. Log in → Pages: confirm templates on Home, Contact, About. Duplicate Home to a draft and check ACF fields appear.
2. Do not clone the four catalog URLs.

---

## 2026-09-01 — Strip workaround MU plugins and media-library hacks (0.9.79)

**Done**

- Deleted live `jc-db-audit-clean.php` and `jc-media-grid-fast.php` from mu-plugins. Hostinger’s own mu-plugins stay.
- Uninstalled **JC ACF Guard**. Repeater counts are already fixed in the database; the theme still refuses to re-import ACF groups that already exist.
- Removed hardcoded `media_library_infinite_scrolling` (use Users → Profile). Removed Media Library AJAX skips for extra image sizes / Woo resize.
- Theme 0.9.79. WPML stays off.

**Next**

1. Media Library grid uses core + the user’s profile infinite-scroll setting.
2. Design-clone step 2 (rest of header / footer / home).

---

## 2026-09-01 — Database cleanup (duplicate ACF posts)

**Done**

- Audit: `wp_posts` was ~385 MB / ~326k junk rows. Theme `justccell_acf_ensure_missing_fields` had imported the same 15 ACF groups over and over (`group_jc_about_page` etc. thousands of times). Post meta `clone_features` on 5 products was stored as 328236 (ACF would loop that count). Advanced DB Cleaner’s “unknown” `_clone_features_*` keys are live product ACF, not orphans.
- Kept one row per `(post_type, post_name)` (oldest ID). Deleted 326,023 duplicate `acf-field` / `acf-field-group` posts. Set those 5 feature counts to the real 4–8 rows. OPTIMIZE `wp_posts` + `wp_postmeta`.
- After: 15 field groups, 342 fields, 10,424 postmeta, 0 orphan meta, 0 revisions. Hostinger disk **562 MB → 106 MB**. Left WPML tables (plugin off). Theme 0.9.78 no longer re-imports groups when they already exist.

**Next**

1. Hard-refresh Media Library — grid should be much faster with a small `wp_posts`.
2. Do not click Advanced DB Cleaner “delete unknown” on `_clone_features_*`.
3. Design-clone step 2 (rest of header / footer / home).

---

## 2026-09-01 — Media Library grid: stop theme work on query-attachments (0.9.77)

**Done**

- The grid AJAX (`query-attachments`) is `is_admin()` with `manage_options`. The theme was syncing every ACF field group (`justccell_acf_ensure_missing_fields`) and looking up extra image sizes (`justccell-card` 720, `justccell-discover` 840×520, Woo 720/960) on each page of tiles.
- That request now skips ACF PHP/JSON registration, extra image sizes, rewrite flush, and the 168KB product-data catalog file. Core still builds the grid JSON (thumbnail + medium only). Woo on-the-fly resize stays off for this AJAX.

**Next**

1. Hard-refresh **Media → Library** grid. First page should paint without a long toolbar spinner.
2. Continue design-clone step 2 (rest of header) after this is confirmed.

---

## 2026-09-01 — Header: Justccell 3.0 is a link (0.9.76)

**Done**

- Source header: 3.0 is a page link, not a dropdown. Removed the All-In-Ones / Cartridges / Pod Systems mega under Justccell 3.0.
- Theme always treats that item as `type: link`. One-time menu flatten deletes leftover children in Appearance → Menus.
- New working checklist: [design-clone.md](design-clone.md). Media Library admin grid left alone.

**Next**

1. Confirm hover on JUSTCCELL 3.0 does not open a panel; click goes to `/justccell-3-0/`.
2. Step 2: rest of the header (Products mega, Why dropdown, CTA) vs source.

---

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

## 2026-09-02 — Catalog cut job 5 of 5 (Hermes labor) — Rank Math + rails + mega + 3.0 + quote SKUs

**1) Rank Math (19/19 pushed):**
- Unique RM title <60 + description <160 set on all 19 products via `/wp-json/rankmath/v1/updateMeta` (format: `{objectID, objectType:"post", meta:{rank_math_title, rank_math_description}}`).
- Frontend verified 17/19 live immediately. 2 exceptions (meta saved in RM, hidden by theme router): aio-voltage-tuner (theme 301s /product/aio-voltage-tuner/ to /contact/?sku=) and flex (theme 301s /all-in-ones/flex/ to trashed flexcell).

**2) Homepage rails:**
- Hero slides ACF (field_jc_home_hero_slides on page 241) fixed: row-0 tank/ (TRASHED) -> /all-in-ones/voca-pro-max/; row-3 diama (TRASHED) -> /cartridge/kera/; row-2 contact?sku=eazie-pro -> /pod-system/eazie-pro/; alt row-3 Diama -> Kera.
- Rails: dynamic category queries - trashed products dropped automatically. Verified live: ZERO references to tank, mixjoy, flexcell, diama, m3-plus, palm-pro, blanc, rosin-bar, slym, stylo, fino, sandwave, skye-ii, listo, ds0103, go-stik, mini-tank, vision-box, bellos, dart, luster on the homepage (incl. nav + mega).
- Homepage rails currently show 9 product links (template queries limited per tab) - full 19 rail curation is theme layout work (Cursor).

**3) Products mega:** clean - shows only keep products (th2-evo, m6t-evo, kera, m4, m4-tiny, palm-se, voca-pro-max, eazie-pro, eco-star). No MixJoy, no Tank.

**4) Justccell 3.0 page (201):** ACF relationship (field_jc_j3_product_groups) verified clean - only 7 keep IDs (327270/71/72/73, 327274, 327276/77). NO trashed ID in any ACF field on this page.
- FOR CURSOR: 3 trashed cards (Mixjoy__trashed, Diama__trashed, Eazie Pod Only 3 0__trashed) render from the THEME j3-products template's hardcoded SKU fallback - no ACF source contains them (searched all 28 ACF fields + raw content). Theme-side fix required.

**5) Quote SKU list:** contact form consumes ?sku= URL param; no static SKU list in the form. All 4 spot-checked keep product pages link only their own SKU (no trashed). Trashed quote links exist only in theme j3 template (see above).

**ACF fields I could NOT find:** none - all referenced fields existed. The trashed cards on the 3.0 page have NO ACF source (theme hardcoded).

## 2026-09-02 — Catalog cut theme fix (Cursor) — justccell-theme **0.9.93**

**Scope:** Theme router, 301 map, category grids, Justccell 3.0 fallback cards. No Woo product copy, no media uploads.

**Code (local + synced to `websites/justccell.com/justccell-theme/`):**
- **NEW** `inc/catalog-redirects.php` — full catalog-cut 301 map (36 trashed slugs, rename pairs, clone aliases). Runs at `template_redirect` priority 7. Sets option `justccell_catalog_cut_2026` + clears rewrite transient on first hit.
- **`inc/chrome.php`** — emptied `justccell_legacy_redirects()` (removed bad `flex→flexcell`, `th2-evo→th2-evomax`, `m6t-evo→m6t-evomax` aliases).
- **`inc/cms-content.php`** — `justccell_catalog_from_woo()` no longer skips `clone_j3=1` products (fixes missing AirOne/Blade/Flo/GemBar/Eazie on category grids).
- **`inc/catalog.php`** — Woo-driven flat catalog groups; `equipment` included in category queries.
- **`inc/product-pages.php`** — `equipment` label added.
- **`inc/bio-heating.php`** — 3.0 fallback groups updated (GemBar/Flo/AirOne/Blade, Vita/Kera, Eazie Pro/Pod); skips trashed/unpublished slugs; `justccell_apply_j3_categories_054()` bails when catalog-cut option is set.
- **`functions.php` + `style.css`** — version **0.9.93**; requires `catalog-redirects.php`.
- **Deploy zip:** `websites/justccell.com/justccell-theme-0.9.93.zip` (~47 MB, excludes `archive/`).

**Live via Woo REST (done):**
- Created **Equipment** product category (ID **75**, slug `equipment`).
- Moved **aio-voltage-tuner** (328873) from Uncategorised → Equipment.

**Deploy (live 2026-09-02 evening):**
- Failed zip deploy renamed `justccell-theme` → `justccell-theme-old-6a9831730313a`; WP still pointed at missing slug → white screen / "theme directory does not exist".
- **Recovery:** Activated backup `justccell-theme-old-6a9831730313a`, TUS-uploaded 8 catalog-cut files (0.9.93) into that folder. Site live again; theme header shows **0.9.93**.
- `/all-in-ones/flex/`, `/cartridge/th2-evo/`, `/equipment/aio-voltage-tuner/` verified 200. Cache cleared via Hostinger API.
- **Cleanup TODO:** In hPanel, duplicate `justccell-theme-old-6a9831730313a` → `justccell-theme` and re-activate proper slug when convenient (cosmetic; site works on backup slug).

## 2026-09-02 — Justccell 3.0 ACF / theme editor incident (Hermes + Cursor fix)

**What broke the site:** Hermes used wp-admin **Theme Editor** on slug `justccell-theme` while a deploy had renamed the real theme to `justccell-theme-old-*`. That left `wp-content/themes/justccell-theme/` as a **stub with only `style.css`**. WordPress then threw *The theme directory "justccell-theme" does not exist* (or could not load the theme).

**Hermes data mistake:** Saved **"3.0 CCELL Bio Heating"** into **`j3_cta_title`** (CTA block) via XML-RPC instead of **`j3_products_title`** (product rail). Product rail still showed PHP default *Brand New Justccell 3.0 Hardware*; CTA wrongly showed the bio heading.

**Theme bug (real):** Templates must never call `get_field('j3_*')` alongside PHP defaults. Getter now uses `justccell_j3_acf_string()` — **ACF value OR default, never both**. One-time `justccell_j3_repair_misplaced_acf_fields()` moves misplaced CTA title → product rail field.

**Cursor 0.9.94 deployed live:**
- Default product rail heading → **3.0 CCELL Bio Heating**
- ACF field `j3_products_title` documents correct label + default in admin
- Active theme folder: `justccell-theme-old-6a9831730313a` (has all assets)
- Proper `justccell-theme/` folder repopulated with PHP/CSS/JS/fonts (images still on backup folder until copy)

**Verified live `/justccell-3-0/`:** single `<h2 class="j3-products__title">3.0 CCELL Bio Heating</h2>`; CTA restored to *Get samples and quotes*.

## 2026-09-02 — Justccell 3.0 ACF ↔ frontend sync (Cursor 0.9.95)

**Problem:** WP admin showed blank/random ACF fields while the public page rendered content from PHP fallbacks (hero, 8 story sections, product tabs). Editors could not see what they were editing.

**Fix (theme 0.9.95):**
- Rebuilt `group_jc_j3_page` with 4 tabs matching the page top-to-bottom: Hero → Story sections → Product rail → Footer CTA.
- Section repeater uses conditional logic (banner fields vs split fields only).
- Removed legacy hidden `j3_product_slugs` repeater from UI.
- `justccell_j3_seed_page_acf_content()` writes live defaults into page 201 on first load after deploy (hero text, 8 sections, 3 product tabs with current Woo IDs).
- ACF admin labels/instructions sync from PHP on version bump (same pattern as Product page group).

**Next:** Apply same 1:1 ACF↔frontend pass to Home, About, Contact, Discover, Why, Location, listing pages, generic brand pages.

**After deploy — verify:**
- `/all-in-ones/flex/` → 200 Flex (not flexcell redirect).
- `/cartridge/th2-evo/`, `/cartridge/m6t-evo/` → 200 at new URLs.
- `/equipment/aio-voltage-tuner/` → 200 product page (not contact funnel).
- Trashed URLs (`/all-in-ones/tank/`, `/flexcell/`, etc.) → 301 to category hub.
- Category grids: 9 / 2 / 4 / 3 + equipment; Justccell 3.0: no MixJoy/Diama/Eazie Pod Only cards.
- Purge LiteSpeed cache; toggle cacheless mode OFF if still on from Hermes testing.

**Data flag for Hermes job 6:** **Diama** (327275) is still **published** in Cartridges — not in the 19-SKU lock; should be trashed.

## 2026-09-02 — ACF ↔ frontend sync batch (Cursor 0.9.96 → 0.9.97)

**0.9.96 (deployed + live):** Home, catalog listings (All-In-Ones / Cartridge / Pod / Battery), generic brand pages (Solution, Choose hardware, Oil types, 510, Packaging, Laser). New `inc/acf-catalog-pages.php` — tabbed groups, seeds, admin UI sync. Contact theme slug restored to **`justccell-theme`** (was backup folder).

**0.9.97 (deployed + live):** About, Why (technology/safety/research/manufacture), Contact distributors wired from ACF, Legal `post_content` seed from static defaults, hide unused Location/Why orphan fields. New `inc/acf-remaining-pages.php`.

**Live QA (2026-09-02):** All checked URLs **200**; theme assets `ver=0.9.97`; no fatals on About/Contact/Technology.
