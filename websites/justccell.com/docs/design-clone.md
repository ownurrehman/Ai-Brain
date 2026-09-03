> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Design clone — chrome and pages

Visual source for QA is the live manufacturer storefront (open privately; never ship its URLs, filenames, or credits on justccell.com). This file is the working checklist so header / footer / home / about / contact are compared one surface at a time.

## Sequence (do not mix)

| Step | Surface | Status |
|---|---|---|
| 1 | **Header — Justccell 3.0 item** | **Done 0.9.76** — plain link to `/justccell-3-0/`, no mega / no dropdown |
| 2 | Header remainder (logo, Products mega, Why dropdown, Solution, About, Discover, Contact, samples CTA) | Next |
| 3 | Footer | Not started |
| 4 | Home | Not started |
| 5 | About | Not started |
| 6 | Contact | Not started |
| 7 | Product card images (catalog + Products mega) | Not started — Media Library **admin grid** is no longer blocked by ACF/image-size work (0.9.77); frontend card images still a later step |

## Step 1 — Justccell 3.0 heading

**Source behaviour:** the “3.0” nav item is a single underlined link to the bio-heating page. Hover does not open tabs or product cards.

**What was wrong:** we treated Justccell 3.0 like Products — `header_item_kind = products_mega`, child items All-In-Ones / Cartridges / Pod Systems, MixJoy / GemBar / Flo / AirOne / Blade cards in the panel.

**What 0.9.76 does**

- Theme always renders the 3.0 item as `type: link`.
- Fallback nav and first-time menu seed do the same.
- One-time `justccell_flatten_j3_header_link()` deletes leftover child items in Appearance → Menus and stores `header_item_kind = link`.
- MixJoy and other 3.0 SKUs stay on the `/justccell-3-0/` page itself, not in the header.

Edit path: **Appearance → Menus** (Primary). Do not add children under Justccell 3.0.

## Header remainder (step 2 notes)

| Item | Source | Justccell |
|---|---|---|
| PRODUCTS | Mega: category tabs + product cards | Keep mega (this is the only product dropdown) |
| 3.0 | Plain link | Step 1 |
| WHY … | Text dropdown | Why Justccell dropdown |
| SOLUTION / ABOUT / DISCOVER / CONTACT | Plain links | Same |
| Right CTA | “Get Samples & Quotes” | Hidden by default (`justccell_hide_header_cta`) — confirm in step 2 |

## Docs vs live

- Client wording: [client-requirements.md](client-requirements.md)
- Snapshot: [STATUS.md](STATUS.md)
- History: [BUILD-LOG.md](BUILD-LOG.md)
- Theme: `Apps/justccell-theme/` → live `wp-content/themes/justccell-theme/`
