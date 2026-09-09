> **Parent Hub:** [[prompts/INDEX|🎯 Prompts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# 🛍️ Master Prompt: High-Conversion Product Page (PDP) Architecture

**Role:** Principal E-Commerce Architect, DTC Product Marketer, and Conversion Specialist.  
**Objective:** Create a high-converting, technically robust Product Detail Page (PDP) for `[Product Name]` with complete schema, wholesale/tier pricing, and full CMS backend editability.

---

## 📋 Input Parameters (Fill Before Prompting)

* **Target Store / Site:** `[e.g. websites/justccell.com/ or client domain]`
* **Product Name & Slug:** `[e.g. CCELL TH2-EVO / th2-evo]`
* **Category:** `[e.g. Cartridges / All-In-Ones / Batteries / Pod Systems]`
* **Key Specifications:** `[Reservoir capacity, coil resistance, battery mAh, dimensions, material]`
* **Pricing & Volume Tiers:** `[Regular/Sale price, wholesale quantity break tiers (e.g. 100, 500, 1000 units)]`
* **Customization / Engraving Options:** `[Available colors, finishes, custom laser engraving zones]`
* **Primary Conversion Action:** `[e.g. Add to Cart / Inquire for Wholesale / Request Bulk Order]`

---

## ⚡ 1. Boot Sequence (Execute Before Code/Content Work)

1. **Locate Target Store:** Find target site under `websites/{domain}/` or `projects/` via `INDEX.md`. Read its `mastersheet.md` and `rules.md`.
2. **Review Store Credentials:** Read `master-env.env` for WordPress/WooCommerce REST API application passwords.
3. **Verify Catalog Lock & Guidelines:** Check `docs/product-catalog.md` and `rules.md` (e.g. Rule §7.8 catalog lock, Rule §4 zero "samples" ban).
4. **Load Production Skills:**
   * **CRO & Buy-Box Optimization:** `skills/rankray-cro-copywriting/SKILL.md`
   * **Product Schema JSON-LD:** `skills/rankray-schema-jsonld/SKILL.md`
   * **WooCommerce Engineering:** `skills/rankray-wordpress-engineering/SKILL.md`
   * **Semantic Content Writing:** `skills/rankray-seo-content-writing/SKILL.md` & `rules/content/content-rules.md`

---

## 📦 2. Product Detail Page (PDP) Architecture

Structure the product page with the following required sections:

### 1. Buy-Box & Conversion Area (Above the Fold)
* **Gallery Stage:** High-resolution featured product image, multi-angle thumbnails, and optional 360° spin interaction (`clone_spin`).
* **Breadcrumbs:** Clean category trail (`Home > Category > Product Name`).
* **Title & Subhead:**
  * `<h1>` Product Name (e.g. *CCELL TH2-EVO Oil Cartridge*).
  * `<h2>` Benefit-focused tagline (e.g. *Medical-Grade Ceramic Heating for Pure Flavor*).
* **Pricing & Wholesale Tier Table:**
  * Clear unit price with standard ecommerce sale strikethrough (muted regular price with red line + current price).
  * Scannable quantity discount tier table (Quantity breaks with per-unit prices and savings).
* **Variant Selectors:** Clean buttons/swatches for capacity (0.5ml, 1.0ml, 2.0ml), mouthpiece material, and aperture size.
* **Customization / Laser Engraving Addon (if available):** Checkbox/toggle for custom branding with real-time price preview.
* **Primary Conversion Button:** Prominent, high-contrast CTA button (e.g. *[Add to Cart]* or *[Request Wholesale Quote]*).
* **Trust & Shipping Badges:** 100% authentic hardware guarantee, rapid European/UK dispatch, CE/RoHS certified.

### 2. AEO Quick-Answer Summary
* 40–50 word definitive specification summary placed in the description for AI search engine extraction (Google AI Overviews, Perplexity).

### 3. Feature Showcase & Technology Deep-Dive
* 3 to 4 feature cards using the **Feature $\rightarrow$ Function $\rightarrow$ Customer Payoff** framework.
* Highlight engineering advantages (e.g. anti-clogging dual-airway, ceramic heating core, zero-leak design).

### 4. Technical Specifications Table
* Comprehensive spec matrix:
  * Dimensions (H × W × D in mm and cm)
  * Weight (grams)
  * Oil Capacity & Aperture Holes
  * Resistance & Coil Material
  * Threading / Connection type (e.g. standard 510 thread)

### 5. Compliance, Certificates & FAQ Accordion
* 4 to 6 high-intent product questions answered clearly (filling instructions, viscosity compatibility, battery pairing).
* Full compliance listings (ISO 9001, cGMP, heavy-metal free testing).

### 6. Related Products & Cross-Sell Rail
* 3 to 4 complementary SKUs (e.g. pairing cartridges with compatible 510 batteries or Elite Terpenes).

---

## 🚨 3. Technical & Store Non-Negotiables

* **100% Backend Editability:** Every visible headline, description, spec line, and button MUST be editable in `wp-admin → Products → Edit Product`.
  * Use native WooCommerce fields first (Title, Short Description, Regular/Sale Price, Attributes, Gallery, Categories).
  * Use mapped ACF fields for custom showcase sections (`acf-json/`).
  * ZERO hardcoded marketing copy in PHP templates.
* **Zero Ghost Fields:** Strict 1:1 sync between ACF fields and frontend output.
* **Zero "Samples" Policy:** Absolutely NO mentions of "samples", "get samples", or sample turnaround promises anywhere on the page. Use wholesale inquiry language.
* **Structured Data:** Emit valid Schema.org `Product` JSON-LD schema with nested `Offer`, `brand`, `sku`, `price`, `priceCurrency`, and `availability`.
* **Zero Clone Footprint:** Never reference external design-source URLs (e.g. `ccell.com`) in images, links, or JSON-LD schema.
* **Save as Draft:** Push new products or PDP layouts as **DRAFT** (`status: draft`) for verification before public release.
