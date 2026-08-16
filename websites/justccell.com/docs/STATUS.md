# Status — justccell.com

Last updated: 2026-08-16

## Snapshot

| Item | State |
|---|---|
| Platform | One WordPress + WooCommerce on Hostinger (`u392808260`), domain justccell.com |
| Design | Homepage, 4 category listings, 22 product clones, About / Why / Solution / Contact matched to ccell.com. Front images come from the **WordPress Media Library only** |
| Theme | `justccell-theme` **0.5.5** behind coming soon. Header logo is uploaded `Just-CCELL-logo-line.png` (horizontal). Round logo is the site icon |
| Geo / language / currency | Stores booked. WPML **parameter URLs** + browser redirect Off. Languages: EN ES FR DE IT **AR RU**. WCML independent; theme maps currency by store. Extra WCML currencies **deferred** — shop default GBP |
| Public visibility | **Coming soon ON.** Public/client see the gate. Admins logged in see the real site. See [visibility.md](visibility.md) |
| B2B / B2C + VAT | Quote form collects B2B vs B2C + VAT number. Woo tax / VIES / OSS **not** built |
| 3devicescorp.com | Separate WordPress install exists today — **wrong**. Must become an alias of this same platform |
| Ownership | Hostinger/Cloudflare/WP currently sit on Rank Ray credentials — **control transfer to 3Devices is a hard requirement** |
| SEO | **Rank Math 1.0.276** + **WPML SEO 2.2.5**. AIOSEO removed. Hreflang: sitemap only, not `<head>`. Coming soon still HTML-gates `/sitemap_index.xml` |
| Security | Baseline edge SSL + LiteSpeed + UpdraftPlus. Wordfence installed but inactive. Full hardening still open |

## Live URLs (QA logged in)

- https://justccell.com/ — homepage clone
- https://justccell.com/all-in-ones/ `/cartridge/` `/pod-system/` `/battery/` — category clones (ccell URL shape)
- https://justccell.com/all-in-ones/tank/ — full Tank clone (360 + highlights). Other SKUs use catalog photos until unique galleries are in Media Library
- https://justccell.com/about/ `/technology/` `/solution/` `/contact/` `/safety/` `/research/` `/manufacture/`
- Store prefixes wrap all of the above (`/uk/…`)

## This week’s goal

Design-approval clone of ccell.com. Keep coming soon on. Do not hardcode `/contact/`-only URLs. Lock 3Devices ownership of Hostinger, Cloudflare, email, and backups.

## Next implementation step

1. QA logged in, hard-refresh (`?ver=0.5.5`). Confirm horizontal Justccell logo, category grids, remaining product pages, About / Why / Solution / Contact.
2. Import extra product hero/gallery photos into Media Library when they exist — pages already refuse to output missing files.

See [ROADMAP.md](ROADMAP.md).
