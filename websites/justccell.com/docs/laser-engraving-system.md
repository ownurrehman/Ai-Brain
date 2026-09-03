> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[INDEX|🧠 Ai Brain]]

# Inline Laser Engraving System — Architecture Standard

**Status:** Production module in `justccell-theme` (from `0.9.114`)  
**Stack:** Custom PHP theme · WooCommerce · ACF Pro · Fabric.js (self-hosted)  
**Owner rule:** Catalog remains **inquiry-first**. Laser engraving is the **only** explicit path that may create a WooCommerce cart line when the customer opts in on a product that has `enable_engraving` on.

Future agents must extend this module — do not invent a second engraving stack, Elementor widget, or CDN Fabric/Google Fonts load.

---

## 1. Goals

1. Per-product engraving rules from ACF (enable, fees, tiers, canvas plate, safe zone).
2. Inline PDP editor (no modal) that adapts by product / category via those ACF fields.
3. Zero double-entry: canvas snapshot + text + cost flow into cart → checkout → order meta as a permanent upload.
4. Category adaptation without hardcoding SKUs: different All-in-Ones / Pods / Cartridges / Batteries get different `canvas_background_image` + `safe_zone_coordinates` on each product (or category defaults).

---

## 2. File map

| Path | Role |
|---|---|
| `docs/laser-engraving-system.md` | This standard |
| `inc/laser-engraving.php` | ACF group, config helpers, localize, cart/checkout/order hooks |
| `template-parts/product/laser-engraving.php` | Inline checkbox + editor markup |
| `assets/js/vendor/fabric.min.js` | Fabric.js 5.3 (vendored — **never** CDN) |
| `assets/js/laser-engraving.js` | Canvas UI, safe-zone clamp, monochrome filter, ATC intercept |
| `assets/css/laser-engraving.css` | Inline editor chrome |
| `acf-json/group_jc_laser_engraving.json` | ACF JSON sync companion (optional export) |

Bootstrapped from `functions.php` (`require_once …/inc/laser-engraving.php`). Assets enqueued from `inc/assets.php` only when the current product has engraving enabled.

---

## 3. ACF data model (WooCommerce product)

Field group key: `group_jc_laser_engraving`  
Location: `post_type == product`  
Prefix: all field **names** below are exact meta keys.

### 3.1 Product fields

| Name | Type | Purpose |
|---|---|---|
| `enable_engraving` | true_false | Master switch. Off = no UI, no scripts, no cart path. |
| `setup_fee` | number | One-time engraving setup fee (store currency), applied as a cart fee when engraving is on the line. |
| `tiered_pricing_matrix` | repeater | Volume pricing for engraving **per unit**. |
| `tiered_pricing_matrix.min_qty` | number | Inclusive lower bound. |
| `tiered_pricing_matrix.max_qty` | number | Inclusive upper bound. `0` = open-ended. |
| `tiered_pricing_matrix.price_per_unit` | number | Engraving add-on per unit in that band. |
| `canvas_background_image` | image (array/ID/URL) | Device plate shown under the Fabric canvas. Category look comes from this image. |
| `safe_zone_coordinates` | repeater | One or more rectangles objects must stay inside. |
| `safe_zone_coordinates.x` | number | Left edge (px on the design canvas). |
| `safe_zone_coordinates.y` | number | Top edge. |
| `safe_zone_coordinates.width` | number | Width. |
| `safe_zone_coordinates.height` | number | Height. |
| `safe_zone_json` | textarea | Optional escape hatch: JSON array of `{x,y,width,height}` merged with the repeater. |

**Admin mapper:** On the product edit screen, `inc/admin-laser-zone.php` + `assets/js/admin-laser-zone.js` overlay a drag/resize box on the plate preview. Coordinates sync into `safe_zone_coordinates` in the same **640×640 Fabric canvas space** (plate contain-fitted from top-left, matching the storefront editor). Manual number inputs stay visible as read-only indicators.

### 3.2 Category defaults (optional, `product_cat`)

Field group key: `group_jc_laser_engraving_cat`  
Used only when a product has `enable_engraving` but empty canvas / safe-zone / pricing. Lets All-in-Ones vs Batteries share a plate without per-SKU duplication.

| Name | Type |
|---|---|
| `laser_canvas_background_image` | image |
| `laser_safe_zone_coordinates` | repeater (same subfields) |
| `laser_setup_fee` | number |
| `laser_tiered_pricing_matrix` | repeater (same subfields) |

### 3.3 Resolved config shape (PHP → JS)

Returned by `justccell_laser_config( int $product_id ): ?array` and passed via `wp_localize_script( 'justccell-laser', 'JustccellLaser', … )`:

```json
{
  "enabled": true,
  "productId": 123,
  "sku": "eco-star",
  "category": "all-in-ones",
  "currency": { "code": "GBP", "symbol": "£", "precision": 2 },
  "setupFee": 25,
  "tiers": [
    { "minQty": 1, "maxQty": 99, "pricePerUnit": 0.45 },
    { "minQty": 100, "maxQty": 0, "pricePerUnit": 0.30 }
  ],
  "canvas": {
    "backgroundUrl": "https://…/uploads/…/plate.png",
    "width": 640,
    "height": 640
  },
  "safeZones": [
    { "x": 180, "y": 220, "width": 280, "height": 160 }
  ],
  "fonts": [
    { "id": "montserrat", "label": "Montserrat", "family": "Montserrat, sans-serif" },
    { "id": "editorial", "label": "Editorial Serif", "family": "Georgia, 'Times New Roman', serif" },
    { "id": "stencil", "label": "Stencil Mono", "family": "'Courier New', Courier, monospace" },
    { "id": "mark", "label": "Bold Mark", "family": "Impact, 'Arial Narrow', sans-serif" }
  ],
  "i18n": {},
  "ajax": { "url": "/wp-admin/admin-ajax.php", "nonce": "…" },
  "cartUrl": "/cart/"
}
```

Fonts are **self-contained** (theme Montserrat + system stacks). Do not load fonts.googleapis.com.

---

## 4. Frontend injection points

### 4.1 Custom PDP (canonical)

`template-parts/product/buy-box.php` includes `template-parts/product/laser-engraving.php` **above** the quote CTA when `justccell_laser_config( $woo_id )` is non-null.

This is the primary surface. Native `woocommerce_single_product_summary` ATC is removed site-wide (inquiry-first).

### 4.2 Woo hook (compat)

`woocommerce_before_add_to_cart_button` also renders the same partial so any future/native ATC form still gets the editor. Guard with a static flag so the UI never doubles.

### 4.3 Markup contract

Root: `[data-laser-engraving]`  
Checkbox: `[data-laser-toggle]`  
Panel: `[data-laser-panel]` (hidden until checked; CSS grid/max-height expand)  
Canvas host: `<canvas data-laser-canvas>`  
Live summary: `[data-laser-summary]`  
Hidden inputs (inside a real ATC form `[data-laser-form]`):

| Name | Content |
|---|---|
| `justccell_laser_enabled` | `1` when opted in |
| `justccell_laser_artwork` | Full-resolution PNG Base64 (data URL or raw) |
| `justccell_laser_preview` | Smaller JPEG/PNG Base64 for cart thumb |
| `justccell_laser_text` | Concatenated text objects |
| `justccell_laser_cost` | Client-calculated engraving total (server re-validates) |
| `justccell_laser_setup_fee` | Echo of setup fee at submit time |
| `justccell_laser_unit` | Engraving PPU for the qty band |
| `quantity` | Synced from buy-box `[data-buy-qty]` |
| `add-to-cart` | Product ID |

When the checkbox is **off**, the buy-box keeps the inquiry `<a data-buy-submit>`. When **on**, that CTA is visually demoted and the laser form’s **Add engraved item** button becomes primary.

---

## 5. JavaScript behaviour (`laser-engraving.js`)

1. Init Fabric canvas; set `backgroundImage` from `JustccellLaser.canvas.backgroundUrl`.
2. Draw safe-zone overlays (dashed, non-selectable).
3. On `moving` / `scaling` / `rotating` / `modified`, clamp object bounding box into the **union** of safe zones (primary zone if only one).
4. Typography: font select (4 families), size, letter-spacing; “Add text” inserts `fabric.IText`.
5. Image upload → offscreen canvas **1-bit stencil** (threshold monochrome) → `fabric.Image` inside safe zone.
6. Qty changes (buy-box or laser form) recalculate live summary:  
   `setupFee + qty * pricePerUnit(tier)`.
7. On form `submit`:  
   - `canvas.toDataURL({ format:'png', multiplier: 2 })` → artwork  
   - lower multiplier / JPEG → preview  
   - fill hidden fields → allow native submit (no `preventDefault` after fill).

---

## 6. WooCommerce hooks (PHP)

| Hook | Purpose |
|---|---|
| `woocommerce_before_add_to_cart_button` | Render editor (compat). |
| `woocommerce_add_cart_item_data` | Ingest POST fields into `$cart_item_data['justccell_laser']`. Reject ATC if enabled flag set but artwork missing. |
| `woocommerce_add_cart_item` / `woocommerce_get_cart_item_from_session` | Rehydrate laser payload on session. |
| `woocommerce_before_calculate_totals` | `set_price( base_unit + engraving_unit )` for engraved lines. Server recalculates PPU from ACF tiers by qty — **never trust client `justccell_laser_cost` alone**. |
| `woocommerce_cart_calculate_fees` | Add one setup fee per engraved cart line (`Laser engraving setup`). |
| `woocommerce_get_item_data` | Show miniature `<img src="data:…">` preview + engraving text in cart/checkout. |
| `woocommerce_checkout_create_order_line_item` | Decode Base64 → `wp-content/uploads/laser-engravings/{order}-{item}-{hash}.png`, store URL + params on the order item; drop huge Base64 from permanent meta. |
| `woocommerce_add_to_cart_redirect` | If the add carries laser data → `wc_get_cart_url()`; else keep inquiry redirect. |

HPOS: only `$item->add_meta_data()` / CRUD APIs — never `update_post_meta` on orders.

### 6.1 Cart payload shape

```php
$cart_item_data['justccell_laser'] = [
  'enabled'   => true,
  'artwork'   => 'data:image/png;base64,…', // session only
  'preview'   => 'data:image/jpeg;base64,…',
  'text'      => 'ACME',
  'unit'      => 0.45,   // server-authoritative after calc
  'setup_fee' => 25.0,
  'qty_band'  => [1, 99],
];
```

### 6.2 Order item meta (permanent)

| Meta key | Value |
|---|---|
| `_justccell_laser` | `yes` |
| `_justccell_laser_artwork_url` | HTTPS URL under `/uploads/laser-engravings/` |
| `_justccell_laser_text` | Engraving text |
| `_justccell_laser_unit` | PPU charged |
| `_justccell_laser_setup_fee` | Setup fee charged |
| `_justccell_laser_safe_zones` | JSON snapshot of zones used |

---

## 7. Pricing algorithm (server)

```
ppu = first matrix row where min_qty <= qty && (max_qty == 0 || qty <= max_qty)
if no row: ppu = 0 (and optionally block ATC)
line_product_unit = product->get_price('edit') + ppu
setup_fee = ACF setup_fee (cart fee, once per engraved line key)
```

Client summary must use the same rules; mismatches are corrected at `before_calculate_totals`.

---

## 8. Security & ops

- Validate image Base64 size (cap ~2.5 MB decoded) and MIME (`png`/`jpeg` only).
- `wp_mkdir_p` + `index.php` silence in `laser-engravings/`.
- Nonce on ATC via Woo’s native form fields; optional `wp_verify_nonce` on custom field.
- Do not enqueue Fabric on non-product pages.
- Do not load Fabric from a CDN (site rule: self-contained front end).
- Inquiry CTA remains default when engraving is unchecked — do not force the whole catalog into cart mode.

---

## 9. Editor checklist (client)

1. Edit Product → **Laser engraving** tab → enable.  
2. Upload plate image that matches the device category.  
3. Set safe zone(s) in canvas pixels (match plate resolution).  
4. Enter setup fee + tier rows.  
5. View PDP logged-in → toggle checkbox → confirm clamp + monochrome upload + live price.  
6. Add to cart → confirm thumb in cart → place test order → confirm file under Media/`laser-engravings/` and order item meta.

---

## 10. What not to do

- Hardcode product slugs or category lists inside the JS editor.  
- Store permanent Base64 on the order (file URL only).  
- Re-enable global ATC chrome for non-engraved catalog browsing.  
- Add Elementor / popup modal for the editor.  
- Pull Fabric or Google Fonts from a third-party origin on the live site.
