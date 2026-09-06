> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Status — justccell.com

Last updated: 2026-09-06 (theme **0.9.297**)

**Read this first.** Dated history: [BUILD-LOG.md](BUILD-LOG.md). Client wording: [client-requirements.md](client-requirements.md). Sequence: [ROADMAP.md](ROADMAP.md). Unanswered: [open-questions.md](open-questions.md).

## Snapshot

| Item | State |
|---|---|
| Live | https://justccell.com/ — Justccell theme **0.9.297** in `wp-content/themes/justccell-theme/` |
| Bio page | **`/ccell-3-0/`** · title **CCELL 3.0** (client rename 2026-09-06). Legacy `/cell-3-0/`, `/justccell-3-0/`, `/ccell-3.0/`, `/justccell-3.0/` 301 → canonical. SSOT: `justccell_bio_canonical_slug()`/`_title()` (filterable). Bound to bio **template**, not slug. |
| ACF binding | **All 7 page groups bound to `page_template`** (not slug) as of 0.9.297 — portability law for clones. Exception: `group_jc_laser_page` stays slug-bound (shares brand template). One-time retarget migration `justccell_acf_retarget_page_groups_to_templates` (option `justccell_acf_tmpl_locations_297b`). |
| Database | Hostinger `u392808260_Jnr8B` **106 MB** (was 562 MB). InnoDB data ~18 MB. Live content: **57 published WooCommerce products** (21 core + 36 imported expansion SKUs — permanent catalog; see `rules.md` §7.8) |
| Staging / draft | https://dev.justccell.com/ — Hostinger clone (WP `30311599`, folder `public_html/dev`). Cloudflare `dev` A → origin. Public sees **coming soon** until logged in |
| Source | `websites/justccell.com/justccell-theme/` (live overwrite of `wp-content/themes/justccell-theme/` only — in-place TUS) |
| Commerce mode | **Add to cart live; paid checkout pending.** Tier-priced SKUs add to the Woo cart via AJAX + slide-out drawer (`inc/cart-ajax.php`). Contact/inquiry forms remain for general wholesale leads. **Paid card checkout is not live** — next step is **Viva Smart Checkout** (sandbox on `dev.justccell.com` first). WooCommerce Payments stays installed but unused; disable when Viva is configured |
| Public gate | Minimal Coming Soon **on** for logged-out visitors (owner may toggle for own QA; that is not go-live). **Settings → Reading "Discourage search engines" is checked (`blog_public=0`)** → whole site is `noindex, nofollow` and Rank Math suppresses `<link rel=canonical>` everywhere (expected pre-launch). **At launch:** uncheck that box + disable coming-soon → canonicals return automatically. Virtual PDP/listing routes self-canonicalize via theme filter (0.9.296). |
| CMS | ACF Pro. Field groups under **ACF → Field Groups**. **Local JSON + GUI only** (no PHP field arrays; `inc/acf-*.php` = plumbing only). Product page: **Heating / Laser / Listing** tabs; heating heading tag + colour picker. **DB de-bloated 0.9.293:** 20 live groups, `acf-field` rows 825→433, 60 duplicate keys → 0, 3 trashed product groups purged (`justccell_acf_orphan_purge_293`). |
| Developer stamp | Rank Ray / rankray.com |

## What “simple version first” means (client #1)

Ship a **visible catalogue + working cart** they can use while we add **Viva payments**, shipping, VAT, and accounts **on the live site**. That is the current plan. We are **not** at “take paid card orders.” They are still processing orders **manually** (client #5).

## Client messages (2026 pack + later)

| # | They said | Our reading | Status |
|---|---|---|---|
| 1 | Simple version first, then develop live | Catalogue + cart now; paid checkout later | **In progress** — cart/drawer live; Viva checkout not on |
| 2 | Payment gateway + UPS + FedEx accounts | **Viva Smart Checkout** + live shipping rates | **In progress** — Viva sandbox on dev first; need demo credentials + UPS/FedEx accounts |
| 3 | They give remaining info once they see draft | Legal, VAT, prices, photos, shipping, payments | **Unblocked** — send them staging + login |
| 4 | Draft / development mode today | Hostinger staging | **Done** — https://dev.justccell.com/ |
| 5 | Updates? UK database, site ASAP, orders manual | They want to stop spreadsheet orders | **Gap** — cart works; no paid gateway checkout yet |
| 6 | Collection service | Pickup as well as courier | **Partial** — Storefront copy + per-product hide. Not a Woo shipping method / pickup slot |
| 7 | [genuineccell Eazie Pro](https://www.genuineccell.co.uk/collections/pod-systems/products/ccell-eazie-pro-battery-vape-pod-system) as listing + laser example | Buy box, tiers, engraving, product story | **Partial** — buy box, cart drawer, laser yes; stock, pickup widget, **Viva paid checkout** no |
| 8 | Locations: keep it to UK for now | Public Location page (`/location/`) = Bolton HQ only | **Done** — theme 0.9.74 |
| 9 | New domain for Spain covering all EU markets | Separate Spain/EU site later; not `/es/` on justccell.com | **Not started** — waiting on the domain |

## Built (theme / WP)

- Custom theme clone of the manufacturer storefront: home, catalog, product, About, Contact, Products mega, footer. **CCELL 3.0** canonical URL is **`/ccell-3-0/`** (legacy `/cell-3-0/`, `/justccell-3-0/`, `/ccell-3.0/`, `/justccell-3.0/` 301 here). Checklist: [design-clone.md](design-clone.md).
- UK = bare `justccell.com`. **Location page (`/location/`) = UK (Bolton) only.** `/locations/` 301s to `/location/`. Spain `/es/` and Switzerland `/ch/` landings still exist in Storefront until the Spain/EU domain is set up. Old prefixes 301 to UK.
- Language = **WPML** (`?lang=`). No custom language switcher.
- Product buy box: Quantity / Per Item Price table, Colour + Select a Combination dropdowns, quantity stepper, purple **Add to cart** (AJAX → slide-out drawer when SKU has tier pricing / is purchasable). Total is the hero; active tier row follows qty. Laser engraving editor lives in the buy box when enabled.
- WooCommerce cart / checkout / my-account: Apple-style light `woocommerce.css` (white cards, hairline borders, purple CTAs only). Header clearance on `.jc-shop`. Classic shortcodes via `commerce-shell.php`.
- Product PDP heading ladder (theme 0.9.197+): **Product heading** = sole `<h1>`; **Product Tagline** = `<h2>` (PDP only); **Specs** = `<h3>` + semantic `<ul>`. Catalog / Explore cards (0.9.284) reuse Specs: marketing sentence = grey line, Tank volume = cyan. Listing tagline / Listing capacity ACF removed. Banner heading / Banner text ACF fields removed. Woo Product description is the long-copy editor (H2/H3/lists).
- Packaging + Elite Terpenes: **Justccell Coming Soon** template (title + excerpt). Laser + collection copy on products.
- WhatsApp / Telegram floating dock always visible. Direct links from **Justccell → Storefront**; empty URLs open Contact.
- Quote leads CPT under Justccell. Inquiry honeypot + IP throttle.
- Media sanitizer (rename leaky `public_uploads_*` / `Just-CCELL-*` filenames, purge leftover thumbs, strip EXIF). Homepage slide 1 is the same full-bleed artwork as ccell.com.
- Rank Ray credits; theme screenshot in Appearance → Themes.
- Discover is the WordPress posts index (`/discover/`). Guides, News, and Blogs are post categories at `/guides/`, `/news/`, `/blogs/`. **15 published editorial posts** (5 per category, 2000+ words). Featured images are 510 / ceramic hardware photos (`justccell-v2-*.jpg`), not lab stock. Demo/seed stubs are gone. Inventory: [post-registry.md](post-registry.md). Overlay hero, tab bar, 3-col cards (image, two-line title, YYYY-MM-DD), pagination, and a related-posts article layout. Edit the hub chrome on **Pages → Discover** via **Discover hub** ACF. Edit articles under **Posts**.
- Packaging + Elite Terpenes use **Justccell Coming Soon**. Laser-engraving page is published. Bio heating page is **`/ccell-3-0/`**.
- Dead `/{category}/{slug}/` URLs return HTTP 404 (not Discover). Anonymous `/wp-json/.../product` is 401 while coming soon is on.
- Public pages are edited on the matching wp-admin screen only. Field groups are listed under **ACF → Field Groups** (each bound to its page **template**, not slug). **About / Why Justccell / CCELL 3.0 / generic brand** each have their own group. Coming Soon pages hide leftover brand ACF. **Legal** uses the WordPress editor + `the_content()`. Clone templates hide Gutenberg. Products stay on **Edit Product** (native description + Product page ACF). Keep existing field names when editing groups. After empty fields, run **Justccell → CMS Import**.
- **Elite Terpenes cross-sell (0.9.219):** after processing/completed, Justccell POSTs a 48-hour free-delivery coupon to [eliteterpenez.com](https://eliteterpenez.com/) `/wp-json/wc/v3/coupons`. Credentials + card copy: **Justccell → Elite Cross-sell**. REST ping verified 2026-09-04. Elite plugin `justccell-coupon-bridge` applies `?apply_coupon=`. Spec: [elite-cross-sell.md](elite-cross-sell.md).
- **Features code map:** [[websites/justccell.com/features-code-map|features-code-map.md]] — Rule §0.5. Read before hunting theme files; update it whenever a feature’s paths/hooks/meta change.
- Plugins in use: WooCommerce, ACF Pro, WPML + WCML, Rank Math, LiteSpeed, UpdraftPlus, coming-soon. No Elementor.
- **Obsidian vault** (`websites/justccell.com/`): STATUS + BUILD-LOG + `rules.md` + `features-code-map.md` must stay in lockstep with live theme (rules §0.5 and §0.13).
- **External audit entry point:** [[websites/justccell.com/docs/website-audit-brief-2026-09-06|website-audit-brief-2026-09-06.md]] — test matrix, regression traps, PDP 360° contract. Full audit + backlog: [[websites/justccell.com/docs/AUDIT-REPORT-2026-09-06|AUDIT-REPORT-2026-09-06.md]].

## PDP QA focus (2026-09-06, theme 0.9.296)

| Area | Live behaviour | Verify on |
|---|---|---|
| 360° spin | `clone_spin` → drag, no loader; first load when frames exist | `/all-in-ones/tank/` |
| Variation still | Colour / attr change hides spin, shows variation image | `/all-in-ones/mini-tank/` |
| Gallery thumbs | Thumb click updates stage; syncs dropdown where possible | Mini Tank, M4B Crystalline |
| Tier buy box | JSON includes tier-priced variations; Add to cart AJAX | Any tier-priced SKU |
| Data gap | Mini Tank may lack `clone_spin` frames — needs Media Library upload, not theme fix | wp-admin → Edit Product |

## Broken / still messy

| Item | Severity | Notes |
|---|---|---|
| Coming soon on for anonymous | Expected | Staging + live both show “coming soon” until wp-admin login |
| Image filenames still look like ccell paths | Done | **Justccell → Media** reports clean. Public homepage URLs are `justccell-*`. Leftover `public_uploads_*` / `Just-CCELL-*` URLs 404. |
| WPML “development site” banner | P1 | Production key when the site moves to the client Hostinger account |
| Homepage visual approval | Open | Slide 1 matches ccell.com again; still needs client sign-off |
| 33 shared CSS class names with ccell | Deferred | Q9 — expensive rename, not public text |
| Hero banners may still say CCELL in artwork | Open | Slide 1 restored to match ccell.com (Medical Grade Inhaler). That artwork still has Powered by CCELL / Curaleaf / Jupiter baked in |
| 3devicescorp.com still a second WP | Split-brain | 301 on the client Hostinger account once they create it |
| Wordfence installed, inactive | Leave | Off until development is done |
| Rank Math `sitemap_index.xml` | Leave | May 404 while coming soon is on |

## Not built (blocks go-live for paid orders)

| Item | Requirement | Needs from client |
|---|---|---|
| Card / gateway checkout | #2, 5/6, 6/6 | **Viva Smart Checkout** (demo credentials → sandbox on dev → live keys on **3Devices** entity) |
| UPS + FedEx live rates | #2 | Account numbers, API keys, origin address |
| Collection as Woo pickup | #6 | Pickup address, hours, “usually ready in X” |
| B2B/B2C accounts + VAT matrix | 3/6 | Legal name, ES VAT ID, accountant (UK B2C VAT) |
| Customer accounts / my-account | 3/6 | After VAT rules |
| Email `info@justccell.com` + forward | 4/6 | Mailbox host choice |
| 3Devices owns Hostinger, CF, WP, backups | 5/6 | Their admin user this week |
| Wordfence on + backup restore test | 6/6 | After ownership |
| Translations filled | 1/6 | WPML strings; uncheck unused langs |
| WCML EUR/CHF rates | 1/6 | Confirm independent of language |
| Real 3Devices product photos | A5 / §0.10 | Their assets |

## Owner actions (this week)

1. Client reviews **live** (coming soon on; discourage search). Do not use staging until they have seen this.
2. WPML production key when they buy hosting / move the site.
3. Storefront: WhatsApp / Telegram URLs.
4. Invite a **3Devices** WP administrator; then hide developer Gmail.
5. Confirm packaging + laser pages are published.
6. 3devicescorp.com 301 after their Hostinger account exists.
7. Ask client for: **Viva demo + live credentials**, UPS, FedEx, collection address, VAT/legal, whether they will take **paid card orders** on this first version or keep cart-only until VAT is ready.

## Go-live (public catalogue)

Deactivate coming soon on **live** only when they say. Staging can stay gated. Test **Viva Smart Checkout** on dev in demo mode first. Do **not** enable live Viva on justccell.com until sandbox pass + tax + shipping are explicit.
