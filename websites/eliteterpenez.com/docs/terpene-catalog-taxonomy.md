> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Terpene Catalog & Taxonomy Architecture — eliteterpenez.com

Technical specification of the WooCommerce product catalog, taxonomic hierarchies, strain attributes, and sensory profile mapping for **Elite Terpenes**.

---

## 1. Catalog Architecture Principles

1. **Native WooCommerce First:**
   - Products are standard WooCommerce products (`post_type = product`).
   - Bottle sizes (e.g. 5ml, 30ml, 100ml, 500ml, 1L) are handled as native WooCommerce **Variable Products** with variations on attribute `pa_bottle_size`.
2. **Lean Database Footprint:**
   - No custom tables.
   - Global attributes (`pa_*`) are registered in WooCommerce so standard WooCommerce layered nav and faceted search work out of the box.
3. **100% Backend Editability:**
   - Product title = Strain/Blend Name.
   - Product short description = Aroma & flavor summary.
   - Product description = Full botanical profile, effects, testing information, and recommended mixing ratios.

---

## 2. Product Categories (`product_cat`)

| Category Slug | Category Name | Description |
|---|---|---|
| `botanical-terpenes` | **Botanical Blends** | Formulated from natural botanical isolates mimicking legendary cannabis strains. |
| `live-resin-terpenes` | **Live Resin Terpenes** | Fresh-frozen botanical capture with rich monoterpene and sesquiterpene preservation. |
| `cannabis-derived` | **Cannabis-Derived Terpenes (CDT)** | 100% pure steam-distilled or CO2-extracted authentic cannabis profiles. |
| `terpene-isolates` | **Pure Terpene Isolates** | Single-molecule terpene standards (Myrcene, Limonene, Linalool, Beta-Caryophyllene) for custom formulation. |
| `flavor-infusions` | **Specialty Flavor Enhancers** | Curated natural flavor and aroma enhancers for cartridges, gummies, and vape liquids. |

---

## 3. Native Global Attributes (`pa_*`)

All attributes must be registered under **Products → Attributes** in WordPress admin:

### 3.1 Strain Classification (`pa_strain_type`)
- **Indica:** Relaxing, sedating, heavy myrcene/linalool profile.
- **Sativa:** Energizing, uplifting, high limonene/pinene profile.
- **Hybrid:** Balanced profile.

### 3.2 Dominant Aroma Notes (`pa_aroma_profile`)
Multi-select terms used for filtering and badge generation:
- `citrus`: Orange, lemon, grapefruit, tangy zest.
- `earthy`: Rich soil, wood, musk, herbal undertones.
- `floral`: Lavender, rose, sweet blossoms.
- `diesel`: Pungent fuel, sharp chemical, authentic gas notes.
- `pine`: Crisp cedar, pine needles, evergreen forest.
- `sweet`: Candy, vanilla, honeyed notes.
- `spicy`: Black pepper, clove, warm spice.
- `fruity`: Berry, tropical fruit, mango, grape.

### 3.3 Dominant Terpenes (`pa_dominant_terpenes`)
- `myrcene`: Beta-Myrcene (herbal, earthy, clove).
- `limonene`: D-Limonene (citrus, rind, lemon).
- `caryophyllene`: Beta-Caryophyllene (peppery, spicy, woody).
- `terpinolene`: Terpinolene (fruity, herbal, piney).
- `pinene`: Alpha-Pinene & Beta-Pinene (pine needles, rosemary).
- `linalool`: Linalool (floral, lavender, sweet spice).
- `humulene`: Alpha-Humulene (hops, earthy, woody).
- `ocimene`: Beta-Ocimene (sweet, herbaceous, woody).

---

## 4. Sensory Radar & Scientific Specifications (Compact ACF)

For the visual aroma radar (matching Abstrax Tech's flavor wheel), ACF Pro fields are mapped to WooCommerce products under group `group_et_product_specs`:

### 4.1 Sensory Radar Scores (1 to 10 scale)
Organized into a compact horizontal sub-group or tab:
- `sensory_sweet` (Number, 1–10)
- `sensory_earthy` (Number, 1–10)
- `sensory_citrus` (Number, 1–10)
- `sensory_pine` (Number, 1–10)
- `sensory_diesel` (Number, 1–10)

### 4.2 Lab Credentials & Specifications
- `certificate_of_analysis` (File field, return format: ID, PDF download)
- `purity_percentage` (Text field, e.g. "99.2% Pure")
- `recommended_mixing_ratio` (Text field, e.g. "3% – 7% by volume")
- `heavy_metals_pass` (True/False toggle)
- `solvents_pass` (True/False toggle)

---

## 5. Wholesale Tiers & Pricing Model

1. **Retail / Sample Bottles:** Sold via standard WooCommerce variation pricing (5ml, 30ml).
2. **Bulk / Wholesale Tiers:**
   - Handled via native WooCommerce tiered pricing meta or wholesale inquiry modal.
   - Wholesale inquiries trigger B2B lead capture, connecting clients directly with sales account managers.
