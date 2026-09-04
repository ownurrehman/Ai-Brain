> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Client Requirements & Project Scope — eliteterpenez.com

**Client:** 3Devices / Mr Nas  
**Live Site:** https://eliteterpenez.com/  
**Sister Site:** https://justccell.com/  
**Reference Site:** https://abstraxtech.com/  

---

## 1. Executive Summary & Client Intent

The client operates two synergistic e-commerce platforms:
1. **Just CCELL (`justccell.com`):** B2B distribution of official CCELL hardware, vaporizers, cartridges, batteries, and all-in-one pods.
2. **Elite Terpenes (`eliteterpenez.com`):** Formulation and distribution of premium botanical terpenes, cannabis-derived terpene profiles, aroma blends, and flavoring solutions.

The target audience for Elite Terpenes consists of vape brands, extractors, licensed producers, formulation chemists, and manufacturers who purchase hardware from Just CCELL and need terpenes to fill cartridges and pods.

---

## 2. Core Business Requirements

### 2.1 Cross-Store 48-Hour Free Delivery Synergy
- **The Offer (client intent):** A customer who orders on either store should unlock **free shipping** on the companion store for **48 hours**.
- **Shipped 2026-09-04:** **Justccell → Elite only.** Hardware order on Justccell creates coupon `JC-{order_id}` on this store via Woo REST. Thank-you + email on Justccell. Magic link `https://eliteterpenez.com/?apply_coupon=JC-{order_id}`. Plugin `justccell-coupon-bridge` applies the coupon in session.
- **Not shipped:** reverse (Elite order → Justccell coupon `ET-{order_id}`). Do not document it as live.
- Specs: [[websites/eliteterpenez.com/docs/cross-site-free-delivery|cross-site-free-delivery.md]] · [[websites/justccell.com/docs/elite-cross-sell|Justccell elite-cross-sell.md]].

### 2.2 Visual Design & Clone Specification
- **Reference Site:** `https://abstraxtech.com/` (Abstrax Tech).
- **Aesthetic Direction:**
  - Premium botanical and aroma science look and feel.
  - Dark/sleek presentation, modern sans-serif typography, scientific aroma wheels, sensory profile breakdowns, and crisp product photography.
  - Replicate the front-end design page by page with pixel-to-pixel accuracy.
- **Strict Zero Public Footprint:**
  - Under no circumstances may `abstraxtech.com` be referenced, credited, linked, or hinted at in public code, schema, CSS, JS, or image names.
  - Everything must be branded 100% as **Elite Terpenes** (`eliteterpenez.com`).

### 2.3 Technical Development Rules
- **No Blind Extra Custom Coding:**
  - Rely on native WordPress core and WooCommerce features.
  - Use native fields: Page Title, Content editor, Excerpt, Featured Image, WooCommerce Product Name, Short Description, Long Description, Regular/Sale Price, Product Attributes (Strain Type, Aroma Profile, Dominant Terpenes), Categories, and Product Gallery.
  - ACF Pro is strictly reserved for custom layouts, banners, repeaters, or diagrams that native fields cannot support.
- **Minimalist, Compact ACF UI:**
  - ACF fields must not consume massive vertical screen space in `wp-admin`.
  - Use tabs, 2-line textareas, and table repeaters.
- **100% Backend Content Editability:**
  - Zero hardcoded marketing copy or button labels in PHP or JavaScript.
  - Every element on every page must be editable from the WordPress admin edit screen.
