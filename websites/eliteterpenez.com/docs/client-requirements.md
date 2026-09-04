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
- **The Offer:** A customer who places an order on either store (`justccell.com` or `eliteterpenez.com`) unlocks **free shipping** on the companion store for the next **48 hours**.
- **Customer Experience:**
  - After completing checkout on Justccell, the order confirmation page and customer receipt email feature a banner and CTA:  
    *"Unlock Free Delivery on Elite Terpenes for the next 48 hours. Your unique code is `JC-{order_id}`."*
  - Clicking the CTA directs the user to `https://eliteterpenez.com/?apply_coupon=JC-{order_id}`.
  - The coupon is silently loaded into their WooCommerce session.
  - When the customer adds terpenes to their cart on Elite Terpenes, the coupon is applied automatically, granting free delivery.
- **Reverse Flow:** The same feature will operate in reverse for customers purchasing terpenes first and hardware second.

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
