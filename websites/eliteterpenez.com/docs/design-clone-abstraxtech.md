> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Abstrax Tech Design Clone & Zero-Footprint Protocol

Visual benchmarks, design system components, and privacy protocols for cloning `https://abstraxtech.com/` onto **Elite Terpenes** (`eliteterpenez.com`).

---

## 1. Design System Benchmarks (Abstrax Tech Inspiration)

Abstrax Tech is the gold standard for botanical extraction and aroma science branding. Elite Terpenes will mirror its sleek aesthetic:

### 1.1 Color Palette & Visual Style
- **Primary Canvas:** Dark, sophisticated charcoal/black backgrounds with crisp high-contrast white text for hero and science sections.
- **Accents:** Vibrant botanical highlights (emerald greens, citrus ambers, berry purples) corresponding to terpene flavor classes.
- **Clean Content Blocks:** Off-white/neutral light card containers for detailed specifications, strain tables, and checkout flows.

### 1.2 Typography & Spacing
- **Font Stack:** Modern geometric sans-serif (e.g. Inter, Outfit, or Montserrat).
- **Proportions:** Large, bold hero headlines (`2.5rem`–`4rem`), tight letter-spaced uppercase eyebrows, and readable body text (`1rem`–`1.125rem`, line-height `1.6`).
- **Layout:** Generous whitespace, precise CSS grid alignment, and rounded card containers (`border-radius: 8px` to `16px`).

### 1.3 Key Component Patterns
1. **Hero Banner:** Full-width botanical visual, high-impact value proposition, dual CTA ("Shop Terpenes" / "Explore Strains").
2. **Category Rails:** Grid of terpene categories (Native Series, Botanical Blends, Live Resin Terpenes, Cloud Drop).
3. **Terpene Profile Breakdown:** Visual aroma radar / sensory notes (e.g. Sweet, Earthy, Diesel, Citrus).
4. **Lab Testing & Purity Badges:** 100% pure terpene isolate, no heavy metals, ISO certified, food grade, solventless certification badges.
5. **Product Cards:** Clean product photography, strain type badge (Indica / Sativa / Hybrid), dominant terpenes list, price, and instant "Add to Cart" or "Inquire" button.

---

## 2. Zero-Footprint Protocol (STRICT MANDATE)

While the visual structure is cloned, `eliteterpenez.com` must **never** leak the identity of the source reference.

### 2.1 Forbidden Items on Shipped Site
- ❌ **No External Links:** Zero links to `abstraxtech.com`, `abstrax.com`, or any subdomain.
- ❌ **No Asset Hotlinks:** Zero images, scripts, fonts, or stylesheets loaded from Abstrax servers or CDNs.
- ❌ **No Name Leaks:** Zero occurrences of "Abstrax" or "Abstrax Tech" in:
  - HTML body, comments, or data attributes.
  - CSS class names, ID selectors, or variable names.
  - JavaScript variables, filenames, or console logs.
  - Image alt text, captions, descriptions, or file names.
  - JSON-LD structured data or Open Graph meta tags.
  - Git commit messages or theme `style.css` author details.

### 2.2 Asset Renaming & Cleansing
- All images scraped or downloaded during design QA must be renamed before upload:
  - ❌ `abstrax-og-kush-bottle.jpg`
  - ✅ `elite-terpenes-og-kush-profile.webp`
- Strip all camera EXIF data and metadata before importing to the WordPress Media Library.
