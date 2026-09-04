> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# CMS Editor Guide — Native Fields & Minimalist ACF Pro Setup

Guidelines for managing content on **eliteterpenez.com** with **100% backend editability**, native-first priority, and clean, space-efficient ACF Pro fields.

---

## 1. The 100% Backend Editability Rule

Every client-facing text string, heading, paragraph, button label, link URL, and media asset must be editable directly in `wp-admin` (`Pages → Edit Page`, `Products → Edit Product`, `Appearance → Menus`).

**Under no circumstances should a client or non-technical editor need to modify PHP, CSS, or JavaScript files to change copy.**

---

## 2. Native WordPress & WooCommerce Priority

Always utilize native core fields before introducing custom ACF fields:

### 2.1 Standard Pages (`post_type = page`)
- **Main Heading (H1):** Native **Page Title** (`post_title`).
- **Body Copy / Lead Text:** Native **Page Content** (`post_content` via Block Editor or Classic TinyMCE).
- **Page Excerpt / Meta Blurb:** Native **Excerpt** (`post_excerpt`).
- **Hero / Header Image:** Native **Featured Image** (`_thumbnail_id`).
- **Navigation:** Native **Appearance → Menus**.

### 2.2 WooCommerce Terpene Products (`post_type = product`)
- **Product Title:** Native WooCommerce Title (`post_title`).
- **Aroma Summary / Quick Notes:** Native **Short Description** (`post_excerpt`).
- **Deep Botanical & Lab Science:** Native **Product Description** (`post_content`).
- **Pricing:** Native WooCommerce Regular & Sale Price (`_regular_price`, `_sale_price`).
- **Terpene Attributes:** Native WooCommerce Attributes:
  - `pa_strain_type`: Indica, Sativa, Hybrid.
  - `pa_aroma_profile`: Citrus, Earthy, Floral, Fruity, Pine, Sweet.
  - `pa_dominant_terpenes`: Myrcene, Caryophyllene, Limonene, Terpinolene, Pinene.
- **Product Imagery:** Native Featured Image + WooCommerce Product Gallery.

---

## 3. Minimalist & Compact ACF Pro Design Rules

When native fields are insufficient (e.g. multi-column custom sections, aroma wheel diagrams, custom card grids), create ACF Pro fields adhering to these strict UX rules:

### 3.1 Avoid Screen Bloating
Default ACF textareas and repeaters can easily consume several vertical viewports, forcing editors to scroll endlessly. To keep the edit screen compact and fast:
1. **Tabs on Every Multi-Section Page:**
   - Group fields into logical sections (`Hero`, `Aroma Profile`, `Lab Science`, `CTA`).
   - Use `'placement' => 'top'` for quick horizontal tab switching.
2. **Compact Textareas (`rows: 2` or `rows: 3`):**
   - For subheadings, card blurbs, and short descriptions, never use the default 8-row textarea.
   - Example configuration:
     ```php
     [
         'key'          => 'field_et_hero_lead',
         'label'        => 'Hero Lead Copy',
         'name'         => 'hero_lead_copy',
         'type'         => 'textarea',
         'rows'         => 2,
         'new_lines'    => 'br',
     ]
     ```
3. **Compact Repeaters (`layout: table`):**
   - Always set repeater layouts to `'table'` or enable `'collapsed'` with the title subfield as the row label.
   - Editors can see all items in a concise row rather than huge stacked blocks.
4. **Return Format: Attachment ID:**
   - Set image and file fields to `'return_format' => 'id'`.
   - Never return raw URLs or giant object arrays.
5. **No Decorative or Redundant Fields:**
   - Do not add message fields, empty dividers, or redundant instruction boxes that waste vertical space.

---

## 4. Strict 1:1 Synchronization & Cleanup

1. **Frontend-to-Backend Sync:**
   - If an element appears on the live website, an active field must exist in `wp-admin`.
   - If an editor modifies a field in `wp-admin`, the front end must immediately reflect the change.
2. **Zero Leftover Fields:**
   - If a section or template part is retired or redesigned, **immediately delete the obsolete ACF field keys** from theme code (`inc/acf-*.php`), `acf-json/`, and `wp-admin`.
   - Never leave ghost fields on the edit screen that have no effect on the frontend.
