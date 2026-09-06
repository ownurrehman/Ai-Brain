> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Client requirements (source of truth)

Received 2026-08-14 from 3Devices via the project owner. Section **2/6 was not labelled in the original pack**. The 2026-08-26 merchandising brief is recorded below as **2/6**. If they send a differently numbered 2/6, merge it here — do not overwrite this.

Copy below is the client’s wording, then our implementation reading.

---

## 1/6 — General idea (one global website)

One single global website. Show the right version from the visitor’s country (IP).

For justccell.com:

| Connection | Path | Default language | Currency |
|---|---|---|---|
| UK | `/uk` | English | GBP |
| Spain | `/es` | Spanish | EUR |
| Germany | `/de` | German | EUR |
| France | `/fr` | French | EUR |
| Italy | `/it` | Italian | EUR |
| Other countries | `/other` | English | EUR |

Defaults only. A **language selector (top-right)** must always let the visitor pick English, Spanish, French, German, or Italian.

**2026-08-16 (owner):** selector also includes **Arabic** and **Russian** for additional customers. Store defaults in the table above are unchanged. Dubai `/ae/` still defaults to English; visitors can switch to Arabic.

Example: visitor from Spain lands on Spanish + EUR, then switches language to English **while remaining on the Spanish / EU store** (EUR, Spain/EU commercial rules).

Other domains (e.g. 3devicescorp.com) redirect into the **same** platform with the same country detection.

**Bottom line:** one website, one platform, multiple domains, countries, languages, and currencies.

### How we will build this

Country and language are **two independent axes**. Country (store) drives currency and tax region. Language drives UI copy only.

Do not use WPML’s default `/es/` = “Spanish language” as the only prefix — that collides with “Spain store”. Canonical pattern:

`https://justccell.com/{store}/{lang}/…`

Example: `/es/en/product/tank` = Spain store, English UI, EUR.

Details: [geo-language-currency.md](geo-language-currency.md).

---

## 2/6 — Storefront merchandising (2026-08-26)

Client wording summarised; implementation reading follows.

**Social:** Instagram https://www.instagram.com/justccell — also WhatsApp and Telegram, all backend-editable.

**Landings:** Spain and Switzerland need landing pages “like justccell.com is for the UK”, with a Spain extension and a Swiss extension. **The main page where customers order from remains justccell.com (UK).**

**Product pages:** Tier pricing table (quantity / per-item price) plus **two dropdowns**. Screenshot reference: genuineccell-style buy box (black active row, purple Add to cart). First dropdown = combination (Pod and battery, With {colour} pod, Pod only, Battery only). Second dropdown = pod options (0.5/1.0ml, 1.4/1.2ohm, colourways). Prices supplied for pod+battery and battery-only, **ex VAT**. Layout first; other SKUs copy this pattern. Extra CCELL copy comes later.

**Services:** Dedicated **packaging** page and **laser engraving** page. Laser film on **every product page**. Collection service available.

**Reference:** https://www.genuineccell.co.uk/collections/pod-systems/products/ccell-eazie-pro-battery-vape-pod-system for listing + engraving tone.

### How we will build this

- **UK `/uk/`** = order catalogue (homepage clone + product buy box).
- **Spain `/es/`** (alias `/spain/`) and **Switzerland `/ch/`** (aliases `/swiss/`, `/switzerland/`) = landings that CTA into the UK catalogue. Edit under **Justccell → Storefront**.
- Buy box is **ACF on each product** (Wholesale tab). Empty offers use the client’s default pod/battery table. **Add to cart** adds tier-priced SKUs to the Woo cart via AJAX drawer. **Paid card checkout** waits on **Viva Smart Checkout** + VAT (rule 0.4).
- WhatsApp / Telegram / Instagram: **Justccell → Storefront**. Empty chat URLs hide the floating dock.
- Packaging `/packaging/` and laser `/laser-engraving/` are brand pages (ACF). Site-wide laser MP4 is an Options file field; products can override. Collection copy is site-wide with a per-product hide toggle.
- No Elementor. No temporary importer plugins in the client-facing stack; CMS Import is a **theme Tools** screen, not a plugin.

---

## 3/6 — Customer accounts & VAT

On account creation, identify:

- **B2B** — registered company with a valid VAT number
- **B2C** — private consumer

Two account types: Business and Consumer.

VAT is calculated from **account type + delivery country + VAT status**.

Example for orders **invoiced by the Spanish company**:

**B2B**

1. Spain → Spanish VAT
2. Another EU country + valid EU VAT number → excluding VAT (intra-community)
3. Outside EU → excluding VAT, export rules

**B2C**

1. Spain → Spanish VAT
2. Another EU country → VAT per EU / OSS rules
3. Outside EU → export VAT rules

Details: [accounts-vat.md](accounts-vat.md).

---

## 4/6 — Domains & company emails

- `info@justccell.com` — main inbox
- `info@3devicescorp.com` — forward to `info@justccell.com`

Both domains exist for **trademark protection**. CCELL is a registered trademark. justccell.com may be fine today; 3devicescorp.com is the safety net if they must stop using a CCELL-containing domain.

justccell.com = commercial brand. 3devicescorp.com = same platform, switchable primary domain.

Details: [domains-email.md](domains-email.md).

---

## 5/6 — Ownership & control

3Devices must have **full ownership and control** of everything: domains, hosting, WordPress/WooCommerce admin, database, customer data, emails, backups, FTP/SFTP.

Developers may build and maintain. Developers must **never** be the only people with admin or technical control.

They cannot repeat the previous incident (Indian developers / internal dispute → lost control of their own site).

3Devices remains the ultimate owner and administrator of all assets and access.

Details: [ownership-control.md](ownership-control.md). This is a **process requirement**, not a theme feature — it is blocking.

---

## 6/6 — Security

High security from day one: site, customer data, payment environment. Protect against hacking, breaches, and attacks. Proper measures + regular backups.

Details: [security.md](security.md).

---

## Later client notes (not in the original 1–6 pack)

Recorded 2026-08-28 from the owner. Implementation reading only.

1. **Simple version first, then develop while live.** Catalogue + **Add to cart** can go public; **Viva** payments/VAT/shipping iterate after.
2. **Payment gateway + UPS + FedEx** accounts to integrate. Gateway choice: **Viva Smart Checkout**. Wait for demo/live credentials on the 3Devices entity.
3. **They supply remaining information once they see draft.** Staging is that draft: https://dev.justccell.com/
4. **Draft / development mode.** Same URL. Logged-out = coming soon.
5. **ASAP / UK database / orders currently manual.** They need a working storefront so they stop processing by hand. Quote form is the stopgap; paid checkout is Q14.
6. **Collection service.** Theme copy exists; Woo local pickup still to build (Q13).
7. **Reference product:** [genuineccell Eazie Pro](https://www.genuineccell.co.uk/collections/pod-systems/products/ccell-eazie-pro-battery-vape-pod-system) — listing layout, buy box, laser/customisation tone. Cart + buy box layout is in 0.9.x; **Viva Smart Checkout** (paid gateway), pickup widget, and courier checkout are not live yet.

---

## Client messages — 2026-09-01 (locations / EU domain)

Recorded from the owner. Do them one at a time.

1. **Locations: keep it to UK for now.** Public Location page (`/location/`) shows Bolton HQ only. Spain and Switzerland office cards are removed. Contact-form country list is unchanged (buyers can still say they are in Spain). Store prefixes `/es/` `/ch/` are untouched until the Spain domain exists. Old URL `/locations/` 301s to `/location/`.
2. **New domain for Spain that will cover all EU markets.** Separate site later. Do not add EU offices back onto justccell.com. Wait for the domain name before DNS, hosting, or a second storefront.

---

## Client messages — 2026-09-01 (domains vs language)

Owner: Spain and Switzerland get **their own domains**. justccell.com only needs **translations** so a UK visitor can switch language without leaving the UK shop.

**How we will build this (do not mix the two jobs)**

| Job | What it is | Tool |
|---|---|---|
| UK visitor wants Spanish / German / French | Same shop, same GBP, same products | **WPML** on justccell.com (`?lang=`). Not a second domain. |
| Spain / EU market | Own hostname when they give the domain | Same WordPress later (one platform). Not WPML “language = domain”. |
| Switzerland market | Own hostname when they give the domain | Same as Spain. Not `/ch/` as the long-term Swiss shop. |

**WPML now:** yes, for justccell.com language switching. Keep URL format **Language name as a parameter**. Browser redirect **Off**. Do **not** set WPML to language directories (`/es/` = Spanish) and do **not** use WPML “language per domain” (that would send a UK Spanish-speaker to the Spain site).

**Spain / Switzerland domains:** wait for the names (Q15). Until then `/es/` and `/ch/` stay Storefront landings that CTA into the UK catalogue. Do not clone the UK homepage onto those prefixes. Do not start a second WordPress install for Spanish.

---

## Constraints we already accepted in the build

- Visual/structural clone of ccell.com for design approval; **reference images are temporary**.
- Cart + inquiry forms coexist until **Viva** paid checkout is live; the URL and store model must still allow country prefixes. **Wholesale quantity tables** show on product pages (ex VAT). Purple **Add to cart** → AJAX drawer for purchasable SKUs. **Viva Smart Checkout** is the planned payment gateway.
- Rank Math SEO Free (not Yoast; AIOSEO Woo module was paid). One SEO plugin only. Hreflang via WPML SEO in the sitemap, not `<head>`.
- Custom lightweight theme (no Elementor). Vanilla JS + BEM/CSS variables.

---

## Client messages — 2026-09-03 (Zero "Get Samples & Quotes" sitewide)

Client instruction from Mr Nas - CCELL Mazhar (2026-09-03):
> *"Anywhere you see get samples and quotes on the whole site please remove. Its not something we offer."*

**Implementation mandate:**
- Remove all instances of "Get Samples", "Get Samples & Quotes", "Request sample & quote", sample trays, and sample delivery promises ("Samples delivered in 3–15 days") sitewide.
- We do not offer hardware samples.
- All CTA buttons and conversion elements must use standard business/wholesale inquiry copy (e.g. "Inquire Now", "Get in Touch", "Contact Us", "Request a Quote").
