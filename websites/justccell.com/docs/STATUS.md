> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Status — justccell.com

Last updated: 2026-08-29 (theme 0.9.40 — product colours per SKU; buy box under hero)

**Read this first.** Dated history: [BUILD-LOG.md](BUILD-LOG.md). Client wording: [client-requirements.md](client-requirements.md). Sequence: [ROADMAP.md](ROADMAP.md). Unanswered: [open-questions.md](open-questions.md).

## Snapshot

| Item | State |
|---|---|
| Live | https://justccell.com/ — Justccell theme **0.9.40** in `wp-content/themes/justccell-theme/` |
| Staging / draft | https://dev.justccell.com/ — Hostinger clone (WP `30311599`, folder `public_html/dev`). Cloudflare `dev` A → origin. Public sees **coming soon** until logged in |
| Source | `Apps/justccell-theme/` (live overwrite of `wp-content/themes/justccell-theme/`) |
| Commerce mode | **Inquiry-first quote CTA.** WooCommerce Payments stays **on** so the owner can connect gateways, tax, and VAT. Add to basket still opens a quote until paid checkout is explicitly switched on |
| Public gate | Minimal Coming Soon **on** for logged-out visitors (owner may toggle for own QA; that is not go-live) |
| CMS | ACF Pro. Field groups under **ACF → Field Groups**. **Justccell** menu: Overview, Storefront, Header, Forms, Quote leads, CMS Import, Media |
| Developer stamp | Rank Ray / rankray.com |

## What “simple version first” means (client #1)

Ship a **visible catalogue + quote** they can use while we add payments, shipping, VAT, and accounts **on the live site**. That is the current plan. We are **not** at “take paid orders.” They are still processing orders **manually** (client #5).

## Client messages (2026 pack + later)

| # | They said | Our reading | Status |
|---|---|---|---|
| 1 | Simple version first, then develop live | Catalogue + quote now; checkout later | **In progress** — draft exists; paid checkout not on |
| 2 | Payment gateway + UPS + FedEx accounts | Woo payments + live shipping rates | **Not started** — need their gateway + UPS/FedEx credentials |
| 3 | They give remaining info once they see draft | Legal, VAT, prices, photos, shipping, payments | **Unblocked** — send them staging + login |
| 4 | Draft / development mode today | Hostinger staging | **Done** — https://dev.justccell.com/ |
| 5 | Updates? UK database, site ASAP, orders manual | They want to stop spreadsheet orders | **Gap** — quote leads only; no Woo checkout |
| 6 | Collection service | Pickup as well as courier | **Partial** — Storefront copy + per-product hide. Not a Woo shipping method / pickup slot |
| 7 | [genuineccell Eazie Pro](https://www.genuineccell.co.uk/collections/pod-systems/products/ccell-eazie-pro-battery-vape-pod-system) as listing + laser example | Buy box, tiers, engraving, product story | **Partial** — layout/theme yes; real cart, stock, pickup widget, paid checkout no |

## Built (theme / WP)

- Custom theme clone of ccell.com structure: home, catalog, product, About, Contact, header mega, footer.
- UK = bare `justccell.com`. Spain `/es/`, Switzerland `/ch/` landings (edit **Justccell → Storefront**). Old prefixes 301 to UK.
- Language = **WPML** (`?lang=`). No custom language switcher.
- Product buy box: Quantity / Per Item Price table, Colour + Select a Combination dropdowns, quantity stepper, purple Add to basket (quote until payments on).
- Packaging + laser-engraving pages; laser video on products; collection copy.
- WhatsApp / Telegram floating dock always visible. Direct links from **Justccell → Storefront**; empty URLs open Contact.
- Quote leads CPT under Justccell. Inquiry honeypot + IP throttle.
- Media sanitizer (rename leaky `public_uploads_*` / `Just-CCELL-*` filenames, purge leftover thumbs, strip EXIF). Homepage slide 1 is the same full-bleed artwork as ccell.com.
- Rank Ray credits; theme screenshot in Appearance → Themes.
- Discover is the WordPress posts index (`/discover/`). Guides, News, and Blogs are post categories at `/guides/`, `/news/`, `/blogs/`. **15 published editorial posts** (5 per category, 2000+ words). Featured images are 510 / ceramic hardware photos (`justccell-v2-*.jpg`), not lab stock. Demo/seed stubs are gone. Inventory: [post-registry.md](post-registry.md). Overlay hero, tab bar, 3-col cards (image, two-line title, YYYY-MM-DD), pagination, and a related-posts article layout. Edit the hub chrome on **Pages → Discover** via **Discover hub** ACF. Edit articles under **Posts**.
- Public pages are edited on the matching wp-admin screen only. Field groups are listed under **ACF → Field Groups** so they can be managed without PHP. **About / Why Justccell / Justccell 3.0 / generic brand** each have their own group. **Legal** uses the WordPress editor + `the_content()`. Clone templates hide Gutenberg. Products stay on **Edit Product**. Keep existing field names when editing groups. After empty fields, run **Justccell → CMS Import**.
- Plugins in use: WooCommerce, ACF Pro, WPML + WCML, Rank Math, LiteSpeed, UpdraftPlus, coming-soon. No Elementor.

## Broken / still messy

| Item | Severity | Notes |
|---|---|---|
| Coming soon on for anonymous | Expected | Staging + live both show “coming soon” until wp-admin login |
| Image filenames still look like ccell paths | Done | **Justccell → Media** reports clean. Public homepage URLs are `justccell-*`. Leftover `public_uploads_*` / `Just-CCELL-*` URLs 404. |
| WPML “development site” banner | P1 | Production key when the site moves to the client Hostinger account |
| Homepage visual approval | Open | Slide 1 matches ccell.com again; still needs client sign-off |
| `/packaging/` `/laser-engraving/` | Done | Published, WPML-assigned, permalinks flushed (0.9.18) |
| 33 shared CSS class names with ccell | Deferred | Q9 — expensive rename, not public text |
| Hero banners may still say CCELL in artwork | Open | Slide 1 restored to match ccell.com (Medical Grade Inhaler). That artwork still has Powered by CCELL / Curaleaf / Jupiter baked in |
| 3devicescorp.com still a second WP | Split-brain | 301 on the client Hostinger account once they create it |
| Wordfence installed, inactive | Leave | Off until development is done |
| Rank Math `sitemap_index.xml` | Leave | May 404 while coming soon is on |

## Not built (blocks go-live for paid orders)

| Item | Requirement | Needs from client |
|---|---|---|
| Card / gateway checkout | #2, 5/6, 6/6 | Gateway (Stripe/PayPal/etc.) on **3Devices** entity |
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
7. Ask client for: payment gateway, UPS, FedEx, collection address, VAT/legal, whether they will take **paid orders** on this first version or stay quote-only until VAT is ready.

## Go-live (public catalogue)

Deactivate coming soon on **live** only when they say. Staging can stay gated. Do **not** turn on Woo checkout until gateway + tax + shipping are explicit.
