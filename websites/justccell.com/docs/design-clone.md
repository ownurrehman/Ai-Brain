> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Design clone — chrome and pages

Visual source for QA is the live manufacturer storefront (open privately; never ship its URLs, filenames, or credits on justccell.com). This file is the working checklist so header / footer / home / about / contact are compared one surface at a time.

## Sequence (do not mix)

| Step | Surface | Status |
|---|---|---|
| 1 | **Header — Just CCELL 3.0 item** | **Done** — link to **`/justccell-3-0/`** (0.9.201 canonical). Never `/ccell-3-0/`. Label **Just CCELL 3.0**, not “CCELL 3.0”. |
| 2 | Header remainder (logo, Products mega, Why dropdown, Solution, About, Discover, Contact) | Next — **no samples CTA** (client ban) |
| 3 | Footer | Not started |
| 4 | Home | Not started |
| 5 | About | Not started |
| 6 | Contact | Not started |
| 7 | Product card images (catalog + Products mega) | Not started — Media Library **admin grid** is no longer blocked by ACF/image-size work (0.9.77); frontend card images still a later step |

## Step 1 — Just CCELL 3.0 heading

**Source behaviour:** the “3.0” nav item is a single underlined link to the bio-heating page. Hover does not open tabs or product cards.

**Canonical (0.9.201):** public URL is **`/justccell-3-0/`**. Title **Just CCELL 3.0**. Legacy `/ccell-3-0/` 301s here. Never reverse that redirect.

**What was wrong (historic):** we treated 3.0 like Products (mega + category children). 0.9.76 flattened it to a link. Seeders later still used `ccell-3-0` until 0.9.201.

Edit path: **Appearance → Menus** (Primary). Point the item at the `justccell-3-0` page. Do not recreate a `ccell-3-0` page.

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
