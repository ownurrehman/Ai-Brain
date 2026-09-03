> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Editor guide — clone pages and add products

For the owner and the client. Live site: https://justccell.com/  
Theme **0.9.87**. No Elementor. All public copy and images are WordPress + ACF.

**Hard rule:** never put images in the theme folder or paste image URLs into PHP. Upload to **Media Library**, then attach in ACF / Featured image / Product gallery. Rank Math uses those same attachments for Open Graph.

Upload pack (named from live products): `websites/justccell.com/media-upload-ready/upload/`. One folder — select all. File `mini-tank-justccell-vape-featured.png`, Media title `Mini Tank - Justccell - Vape`. CSV: `upload-manifest.csv`. Ignore `media-replace-ready/unused/`.

---

## What lives where (tested on live)

| You want | WordPress object | Template to pick | Edit screen |
|---|---|---|---|
| UK homepage (or a campaign landing that *looks* like home) | **Page** | **Justccell Home** | Pages → that page. ACF: hero slides, device rails copy, customize, fill, laser, quote. Product cards still come from Woo products. |
| Contact | **Page** | **Justccell Contact** | Pages → Contact |
| About | **Page** | **Justccell About** | Pages → About |
| Technology / Safety / R&D / Manufacturing | **Page** | **Justccell Why** | Pages → that Why page |
| Justccell 3.0 | **Page** | **Justccell 3.0** | Pages → Justccell 3.0 |
| Solution, packaging, laser, 510 thread, oil types | **Page** | **Justccell Brand** | Pages → that page |
| Location (Bolton HQ) | **Page** | **Justccell Location** | Pages → Location |
| Privacy / Terms / Cookies | **Page** | **Justccell Legal** | WordPress editor + Rank Math |
| Discover hub | **Page** | **Justccell Discover** | Pages → Discover. Articles are **Posts**, not extra Discover pages. |
| All-In-Ones / Cartridges / Pod Systems / 510 Batteries | **Page** | **Justccell Catalog** | Keep these **four URLs**. Do not clone them for extra landings (duplicate catalog = bad SEO). |
| A sellable SKU | **Product** | (WooCommerce product) | Products → that product. ACF **Product page** + Woo fields below. |
| Inline laser engraving on a SKU | **Product** (+ optional **product category** defaults) | — | Product → **Laser engraving (buy box)**: enable, setup fee, tiers, canvas plate, safe zones. Category term can supply defaults when product fields are empty. Spec: [[laser-engraving-system\|laser-engraving-system.md]]. |
| Spain / Switzerland country landing (until their own domains exist) | Not a duplicated homepage | — | **Justccell → Storefront**. CTA into the UK catalogue. Language on justccell.com is WPML, not a second domain. |
| Header / footer links | Menu | — | **Appearance → Menus** (Primary / Footer / Legal). Justccell 3.0 stays a plain link. |
| WhatsApp, Telegram, Instagram, site-wide laser film | Options | — | **Justccell → Storefront** |

---

## Clone an existing landing (homepage, about, contact, …)

WordPress does not copy a page by itself. The theme adds **Duplicate** on **Pages**.

1. **Pages → All Pages**.
2. Hover the source page → **Duplicate**. A draft opens with the same template, ACF fields, and Rank Math meta.
3. Change **title** and **permalink** (slug). Example: `Home` → `Spain launch`, slug `spain-launch` → `https://justccell.com/spain-launch/`.
4. Page attributes **Template** must stay the matching Justccell template (Home, Contact, About, …). That is what loads the ACF fields — **not** the slug.
5. Replace copy. For every image: **Media → Add New**, then pick it in the ACF image / gallery field. Do not leave the copied manufacturer photo if the client does not own it.
6. **Rank Math** on the same screen: unique Title, Meta description, Focus keyword. One H1 (already in ACF heading tags on clone pages).
7. If WPML is on: finish English first, then **+** to translate. Do not invent a second English page per language.
8. Publish. Add the URL to **Appearance → Menus** only if it should appear in the header/footer.
9. Preview logged-in (Coming Soon is still on for anonymous visitors).

**Add blank instead of duplicate:** Pages → Add New → pick the template in Page attributes → fill empty ACF. Same layout, no copied content.

---

## Add or clone a product (ccell-style page)

The public product page **is** this layout. There is no second template. **Products → Add new** shows **Product page** (tabs under the title). Fill those fields; the live page is that design.

Duplicate a cousin (Tank, Mini Tank, Luster Pro) if you want copy and photos already in place. Woo Duplicate copies ACF.

1. **Products → Add new** (blank) or hover a cousin → **Duplicate**.
2. **WooCommerce (required for catalogue + SEO)**
   - Name, slug (URL is `/category/slug/`, e.g. `/all-in-ones/tank/`).
   - SKU.
   - Catalog visibility: Published.
   - **Product image** (Media Library) + **Product gallery**.
   - **Categories:** All-In-Ones / Cartridges / Pod Systems / 510 Batteries (one primary).
   - Menu order (mega menu + grids follow this).
   - Short description optional; long story lives in ACF, not Gutenberg.
3. **ACF — Product page (tabs under the title)**
   - **Hero:** Tagline, subtitle, **Banner**, **Photos**, **360** (drag into rotation order), Specs.
   - **Features:** one slide per row (heading, text, photo). Skip if this SKU has none.
   - **Heating:** heading, text, background. Skip if this SKU has no EVOMAX panel.
   - **Details:** extra photos (first is the wide tile).
   - **Catalog:** listing tagline, capacity, card image, oil type, Products-menu + Justccell 3.0 flags.
   - **Quote:** colours (one per line *or* Woo Colour attribute), combinations with quantity / per-item **ex VAT** price breaks. Button still opens a **quote**, not paid checkout.
   - **Laser:** on unless the client says no; video from Storefront unless this SKU has its own MP4.
4. **Rank Math** on the product: Title, description, focus keyword, product schema. Featured image = OG image.
5. **WPML:** translate after English SKU is final.
6. Publish. Confirm the public URL, catalog card, and mega card (if featured).

Paid checkout, live UPS/FedEx, and VAT accounts are **not** on yet. Do not tell the client this SKU can take card payments.

---

## SEO (this site will be pushed hard)

- **One URL, one intent.** Do not clone All-In-Ones / Cartridges / Pod Systems / 510 Batteries into extra slugs.
- Campaign homepages are fine if Title + H1 + body are unique and Rank Math is filled.
- Canonical: Rank Math. Do not noindex money pages.
- Images: descriptive filenames and alt text in Media Library (Rank Math / attachment alt).
- Discover posts: category Guides / News / Blogs; featured image from Media;  the hub is `/discover/`.
- hreflang / languages: WPML SEO when translations exist. UK catalogue stays the order URL (client 1/6 + 2/6).
- Sitemap: Rank Math `sitemap_index.xml` (may 404 while Coming Soon is on).

---

## Client brief (paste this)

- Edit pages in **Pages**. Duplicate, then pick a **Justccell …** template so the fields appear.
- Edit products in **Products**. Duplicate a similar SKU, then fill Woo + Product page + Rank Math.
- Every photo goes through **Media Library** first.
- Header links: **Appearance → Menus**.
- Chat URLs and the default laser film: **Justccell → Storefront**.
- Spain / Switzerland “homepage-like” landings: **Justccell → Storefront**, not a second UK home, unless you explicitly want a campaign URL.
- Quotes only until gateway + VAT + shipping are signed off.
