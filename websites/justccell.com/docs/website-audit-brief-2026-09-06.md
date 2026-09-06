> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Website audit brief — justccell.com (for Opus / full-site QA)

**Purpose:** Single entry point for an external model (e.g. Opus 4.8) to audit the live site against vault truth. Read this file, then [[websites/justccell.com/features-code-map|features-code-map.md]], [[websites/justccell.com/rules|rules.md]], and [[websites/justccell.com/docs/STATUS|STATUS]].

**As of:** 2026-09-06 · Live theme **0.9.292** · Hostinger `u392808260` / WP install `30055979`

---

## 1. Access constraints (critical)

| Constraint | Detail |
|---|---|
| **Coming soon** | Anonymous visitors see maintenance. **Log in to wp-admin** (or use an authenticated session) to view PDPs, catalog, cart. |
| **REST** | Anonymous `/wp-json/.../product` returns **401** while coming soon is on (`inc/rest-privacy.php`). |
| **Staging** | https://dev.justccell.com/ — separate clone; do not assume it matches live version without checking `JUSTCCELL_VERSION`. |
| **Sister store** | [eliteterpenez.com](https://eliteterpenez.com/) is Hostinger **`u984013785`** — not this account. Cross-sell only Justccell → Elite. |

---

## 2. What to audit (priority order)

### A. Product PDP — 360°, gallery, variations (2026-09-06 hot area)

**Reference behaviour:** [CCELL Mini Tank](https://www.ccell.com/all-in-ones/mini-tank) `rotate360()` — no loader; all spin frames in HTML; drag toggles opacity stack.

| Check | Expected on Justccell | Test URL (logged in) |
|---|---|---|
| 360° on load | If ACF **360 images** (`clone_spin`) has frames → **drag-to-spin** visible first; `360°` hint badge | `/all-in-ones/tank/` (has 36 frames in CMS) |
| No loader / progress cursor | Spin is interactive immediately; **no** `is-loading` gate | Tank PDP |
| Colour change | Changing **Colour** (or multi-attr) swaps hero to **still** variation image | `/all-in-ones/mini-tank/` |
| Gallery thumbs | Click thumb → still image updates **and** main stage on right; first thumb returns to 360° when spin exists | Mini Tank, Tank |
| Buy box | Tier table, qty, variation dropdowns, **Add to cart** (AJAX drawer) update when colour/tank changes | `/battery/m4b-pro-crystalline/` (11 gallery), Mini Tank (Colour + Tank Size) |
| Scroll / layout | Left: H1, tagline, specs, tier table, thumbs. Right: stage + buy box. No missing buy box (PHP 8 TypeError on variations = broken) | Any variable SKU |

**Code map (do not guess paths):**

| Layer | File |
|---|---|
| Template | `template-parts/product/clone.php` — `data-has-spin`, `clone_spin` → `.p-spin`, Woo gallery thumbs |
| 360° drag | `assets/js/product-spin.js` — CCELL parity, shared all products with spin |
| Variation + still gallery | `assets/js/product.js` — `bindVariationGallery()`, `keepSpinOnStage` |
| Variation JSON (tier SKUs) | `inc/woocommerce.php` — `woocommerce_variation_is_visible`, `woocommerce_variation_is_active` (2 args only) |
| Styles | `assets/css/product.css` — `.p-spin__frames` absolute opacity stack |
| Enqueue | `inc/assets.php` — `justccell-product-spin` before `justccell-product`; `wc-add-to-cart-variation` |

**Data gap (not a code bug):** Media pack has **360 frames for Tank only** (`tank-justccell-vape-360-*.jpg` / `tank-360/`). **Mini Tank has no spin sequence in vault** — if `clone_spin` is empty on live, 360° will not render until frames are uploaded in **Products → Edit → 360 images**.

### B. Catalog hub tabs (0.9.274–0.282)

- `/products/` — category tabs, instant switch, real permalinks (`history.pushState`), not hash-only.
- Files: `catalog-hub.php`, `template-parts/catalog/tabs.php`, `panels.php`, `assets/js/catalog-tabs.js`, `inc/catalog.php`.

### C. Commerce (cart, tiers, laser)

- Tier-priced SKUs: prices in `_justccell_tiered_pricing`, not Woo variation catalog price.
- Cart drawer: `inc/cart-ajax.php`, `assets/js/cart-drawer.js`.
- Laser: `inc/laser-engraving.php`, buy-box integration.
- **No** “Get samples” / sample turnaround anywhere (client mandate).

### D. Content editability (hard rule)

Every visible PDP string must map to Woo or ACF on **Edit Product**. See [[websites/justccell.com/AGENTS|AGENTS.md]] §1–2 and [[websites/justccell.com/docs/cms-editor-guide|cms-editor-guide.md]].

### E. SEO / technical (known issues)

- Four catalog URLs (`/battery/`, `/pod-system/`, `/cartridge/`, `/all-in-ones/`) may emit Discover meta until theme router fix — see [[websites/justccell.com/mastersheet|mastersheet]] Done Log 2026-08-31.
- Coming soon → sitemap may 404 for anonymous.

---

## 3. Regression traps (never re-ship)

Documented in [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG]] 0.9.283–0.9.286:

| Do not re-introduce | Why |
|---|---|
| `WC_Product_Variable::sync()` on product save | wp-admin fatal / save loop |
| Bulk `admin_init` variation repair | Same |
| `woocommerce_variation_is_active` with **4** PHP args | PHP 8 TypeError — buy box dies |
| `woocommerce_hide_invisible_variations` forced false site-wide | Pollutes variation JSON |
| Double `wc_variation_form()` without guard | Breaks Woo matcher |
| `is-loading` / batch preload gate on 360° | User-visible delay; removed 0.9.292 |
| Second theme folder on Hostinger | Theme clone disaster |

---

## 4. Deploy & version verification

1. Live path: `wp-content/themes/justccell-theme/` only.
2. Version: `JUSTCCELL_VERSION` in `functions.php` **and** `style.css` Version header — must match **0.9.292**.
3. Verify asset: `https://justccell.com/wp-content/themes/justccell-theme/assets/js/product-spin.js?ver=0.9.292` exists.
4. Deploy: in-place TUS per file; clear LiteSpeed cache after ship.

Vault source: `websites/justccell.com/justccell-theme/`

---

## 5. Suggested audit report structure

Opus should return:

1. **Executive summary** — pass/fail per area (PDP 360, variations, catalog, cart, content policy, SEO).
2. **PDP matrix** — one row per test URL (Tank, Mini Tank, M4B Crystalline, one simple SKU).
3. **Defects** — severity, URL, expected vs actual, likely file from features-code-map.
4. **Data vs code** — items that need wp-admin/media (e.g. Mini Tank `clone_spin` empty).
5. **Out of scope** — Viva checkout, UPS/FedEx, paid orders (see [[websites/justccell.com/docs/STATUS|STATUS]] Not built).

---

## 6. Related vault links

| Doc | Use |
|---|---|
| [[websites/justccell.com/features-code-map|features-code-map.md]] | All theme paths, hooks, meta keys |
| [[websites/justccell.com/rules|rules.md]] | §7.1 buy box, §7.3 gallery, §7.7 legacy ACF, §7.8 catalog lock |
| [[websites/justccell.com/docs/BUILD-LOG|BUILD-LOG]] | Dated ships 0.9.287–0.9.292 |
| [[websites/justccell.com/docs/cms-editor-guide|cms-editor-guide]] | wp-admin field map |
| [[websites/justccell.com/justccell-product-images-audit|justccell-product-images-audit.md]] | Woo image inventory |
| [[websites/justccell.com/docs/elite-cross-sell|elite-cross-sell.md]] | Cross-store coupons |

---

## 7. Recent ship log (2026-09-06)

| Version | Summary |
|---|---|
| **0.9.287** | Variation visibility in JSON + `product.js` sync; catalog card spec-line fix |
| **0.9.288** | Gallery thumb → stage still image |
| **0.9.289** | Thumb → dropdown sync (preserve tank size) |
| **0.9.290** | 360° stays on first load when spin frames exist |
| **0.9.291** | (Superseded) batch preload + loader — **reverted** |
| **0.9.292** | CCELL `rotate360` parity; `product-spin.js`; no loader; all frames `src` in HTML |
