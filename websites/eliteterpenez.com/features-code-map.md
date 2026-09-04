> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# eliteterpenez.com — Features Code Map

**Purpose:** Kill discovery latency. Read this file **before** opening theme PHP. Do not glob-hunt `eliteterpenez-theme/` or run blind directory sweeps for a feature that is already cataloged here.

**Mandatory sync (Universal Zero-Latency Directive):** Any write, refactor, or fix of a feature is incomplete until this file lists the new paths, functions, hooks, template parts, and meta keys.

**Theme constant:** `ELITE_VERSION` in `eliteterpenez-theme/functions.php` (bump with `style.css` on asset changes).  
**Hostinger:** User `u984013785` / WP Software `30437919`. Live path: `wp-content/themes/eliteterpenez-theme/`.  
**Sister store:** Just CCELL (Hostinger user `u392808260` / WP `30055979`, `https://justccell.com/`).

**Boot order:** `functions.php` `require_once` list is the load graph. Do not add a second include path for an existing module.

Deep specs: [[websites/eliteterpenez.com/docs/cross-site-free-delivery|cross-site-free-delivery.md]] · [[websites/eliteterpenez.com/docs/justccell-cross-sell|justccell-cross-sell.md]] · [[websites/eliteterpenez.com/docs/cms-editor-guide|cms-editor-guide.md]] · [[websites/eliteterpenez.com/docs/design-clone-abstraxtech|design-clone-abstraxtech.md]] · [[websites/eliteterpenez.com/rules|rules.md]]

---

## Quick lookup

| Feature | Primary PHP / File | Also |
|---|---|---|
| Theme bootstrap / version | `eliteterpenez-theme/functions.php` | `inc/setup.php`, `inc/assets.php`, `style.css` |
| Zero-Latency Pre-flight / Rules | `rules.md` | `AGENTS.md`, `.cursorrules` |
| Coming soon gate & REST unblock | `bridge/justccell-coupon-bridge.php` | `inc/setup.php` (`woocommerce_coming_soon`) |
| Header navigation & mega menu | `inc/header-menu.php` | `template-parts/header/site-header.php`, `header.php` |
| Footer menus & legal links | `inc/footer-menus.php` | `template-parts/footer/site-footer.php`, `footer.php` |
| Abstrax Tech design clone home | `front-page.php` | `template-parts/home/clone.php`, `assets/css/home.css` |
| Aroma category listings | `inc/listing.php` | `template-parts/catalog/category-grid.php`, `taxonomy-product_cat.php` |
| Terpene Product PDP (Clone) | `inc/product-pages.php` | `woocommerce/single-product.php`, `template-parts/product/clone.php` |
| Terpene sensory radar / aroma wheel | `inc/aroma-wheel.php` | `template-parts/product/aroma-radar.php`, `assets/js/aroma-wheel.js` |
| Native WooCommerce product data | `inc/woocommerce.php` | Native attributes: `pa_strain_type`, `pa_aroma_profile`, `pa_dominant_terpenes` |
| Minimal compact ACF field groups | `inc/acf-fields.php` | `acf-json/`, `inc/cms-helpers.php` (tabs, `'rows' => 2`, table repeaters) |
| 100% Backend editability fallback | `inc/cms-content.php` | `get_field('field') ?: 'Fallback'` pattern |
| Cross-site 48h coupon bridge (inbound) | `bridge/justccell-coupon-bridge.php` | `wp-content/plugins/justccell-coupon-bridge/` (Hostinger `u984013785`) |
| Cross-site 48h coupon creator (outbound) | `inc/justccell-cross-sell.php` | Action Scheduler `elite_jc_create_coupon` (reverse flow) |
| Cart & checkout overrides | `inc/commerce-pages.php` | `woocommerce/cart/`, `woocommerce/checkout/`, `assets/css/woocommerce.css` |
| Inquiry / wholesale lead capture | `inc/inquiry.php` | `template-parts/inquiry/form.php` (B2B wholesale form) |
| Media Library only enforcement | `inc/media-hygiene.php` | `wp_get_attachment_image()` / zero external hotlink filters |
| Zero Abstrax footprint scrubber | `inc/privacy-scrubber.php` | Strips source mentions in output buffers and schema |
| wp-admin Elite Terpenes hub | `inc/admin-menu.php` | Admin options under **Elite Terpenes** menu |

---

## 1. Theme bootstrap and assets

| | |
|---|---|
| **Paths** | `eliteterpenez-theme/functions.php`, `inc/setup.php`, `inc/assets.php`, `header.php`, `footer.php`, `style.css`, `assets/css/globals.css`, `assets/css/chrome.css`, `assets/js/main.js` |
| **Constants** | `ELITE_VERSION`, `ELITE_DIR`, `ELITE_URI`. Options: `elite_pages_ver` |
| **Hooks** | `after_setup_theme`, `wp_enqueue_scripts`, `body_class`, `wp_head`, `after_switch_theme` |
| **Rules** | One live folder `wp-content/themes/eliteterpenez-theme/`. In-place TUS deploy only. Bump version in **both** `functions.php` and `style.css` on any asset ship. Media Library only on the frontend. |

---

## 2. Abstrax Tech visual clone (front-page & branding)

| | |
|---|---|
| **Paths** | `front-page.php`, `template-parts/home/clone.php`, `template-parts/home/hero.php`, `template-parts/home/categories.php`, `template-parts/home/science.php`, `template-parts/home/cta.php`, `assets/css/home.css`, `assets/js/home.js` |
| **Functions** | `elite_home_hero_data()`, `elite_home_category_rails()`, `elite_home_science_credentials()`, `elite_home_cta_block()` |
| **ACF group** | `group_et_home_page` (Organized into Tabs: `Hero`, `Categories`, `Science & Purity`, `Call to Action`) |
| **Rules** | Pixel-to-pixel frontend accuracy matching `abstraxtech.com`. Zero public leak of "Abstrax" in HTML, CSS, JS, or Schema. All headlines, descriptions, button labels, and URLs wired to backend fields. |

---

## 3. Terpene catalog & WooCommerce data model

| | |
|---|---|
| **Paths** | `inc/woocommerce.php`, `inc/catalog.php`, `inc/product-pages.php`, `woocommerce/archive-product.php`, `woocommerce/single-product.php`, `template-parts/product/clone.php` |
| **Native Woo Attributes** | `pa_strain_type` (Indica, Sativa, Hybrid), `pa_aroma_profile` (Citrus, Earthy, Floral, Gas/Diesel, Pine, Sweet), `pa_dominant_terpenes` (Myrcene, Limonene, Caryophyllene, Terpinolene, Pinene, Linalool, Humulene) |
| **Native Fields First** | Title = Product Name, Excerpt = Aroma Summary, Content = Full botanical profile / lab specs, Gallery = Product bottles and aroma graphics |
| **ACF group** | `group_et_product_specs` (Tabs: `Sensory Profile`, `Lab Credentials`, `Usage Guidelines`). Compact repeaters with `'layout' => 'table'`. |
| **Rules** | Native WooCommerce first. Do not store strain names or aroma notes in bespoke custom tables. Product prices and inventory handled via standard WooCommerce CRUD. |

---

## 4. Terpene sensory radar / aroma wheel

| | |
|---|---|
| **Paths** | `inc/aroma-wheel.php`, `template-parts/product/aroma-radar.php`, `assets/css/aroma-radar.css`, `assets/js/aroma-radar.js` |
| **Functions** | `elite_get_product_sensory_scores($product_id)`, `elite_render_aroma_radar($scores)` |
| **ACF Fields** | Repeaters or 5 numeric rating fields (1–10): `sensory_sweet`, `sensory_earthy`, `sensory_citrus`, `sensory_pine`, `sensory_gas` |
| **JS Engine** | Lightweight SVG radar chart; vanilla JS (no heavy external chart libraries like Chart.js if vanilla SVG fits). |
| **Rules** | 100% backend-editable scores. If empty, gracefully hide radar section without layout shift. |

---

## 5. Cross-domain coupon bridge (inbound from Justccell)

| | |
|---|---|
| **Paths** | `bridge/justccell-coupon-bridge.php` · live path `wp-content/plugins/justccell-coupon-bridge/justccell-coupon-bridge.php` |
| **Constants** | `ELITE_JC_BRIDGE_OPTION_CK`, `ELITE_JC_BRIDGE_OPTION_CS`, `ELITE_JC_BRIDGE_OPTION_KEY_ID`, `ELITE_JC_PENDING_COUPON` |
| **Functions** | `elite_jc_ensure_rest_key()`, `elite_jc_ensure_coupon_shipping_method()`, `elite_jc_try_apply_coupon()`, `elite_jc_render_bridge_page()` |
| **Hooks** | `before_woocommerce_init` (HPOS compatibility), `wp_loaded` (capture `?apply_coupon=JC-123`), `woocommerce_cart_loaded_from_session`, `woocommerce_add_to_cart`, `woocommerce_coupon_message`, `admin_menu` (`elite-jc-bridge`) |
| **REST** | Bootstraps Read/Write API key pair. Accepts `POST /wp-json/wc/v3/coupons` from Justccell. |
| **Shipping Zone** | Ensures Zone 0 has Free Delivery requiring a coupon (`requires => 'coupon'`). |
| **Rules** | Live plugin on Hostinger `u984013785`. Bypasses coming soon for authenticated REST requests. |

---

## 6. Cross-domain coupon generator (outbound to Justccell)

| | |
|---|---|
| **Paths** | `inc/justccell-cross-sell.php`, `woocommerce/checkout/thankyou.php`, `template-parts/checkout/justccell-card.php`, `inc/admin-menu.php` |
| **Constants** | `ELITE_JC_META_COUPON` (`_justccell_cross_sell_coupon`), `_justccell_cross_sell_expires`, `_justccell_cross_sell_last_error`. Action `elite_jc_create_coupon`. Option `elite_justccell_cross_sell`. Timeout 4s. TTL 48 hours. |
| **Functions** | `elite_jc_queue_for_order()`, `elite_jc_create_remote_coupon()`, `elite_jc_render_thankyou_card()` |
| **Hooks** | `woocommerce_order_status_processing`, `woocommerce_order_status_completed`, Action Scheduler `elite_jc_create_coupon`, `woocommerce_thankyou`, `woocommerce_email_before_order_table` |
| **REST Target** | `POST https://justccell.com/wp-json/wc/v3/coupons` — code `ET-{order_id}`, 0% discount, `free_shipping => true`, 48h expiry, buyer email lock. |
| **Rules** | Never fail Elite Terpenes checkout if Justccell is unreachable. Action Scheduler first; 4-second timeout on synchronous inline fallbacks. |

---

## 7. Minimal compact ACF UI & 1:1 synchronization

| | |
|---|---|
| **Paths** | `inc/acf.php`, `inc/acf-fields.php`, `inc/cms-helpers.php`, `acf-json/` |
| **Functions** | `elite_acf_register_field_groups()`, `elite_acf_maintain_clean_registry()`, `elite_acf_compact_ui_filters()` |
| **Hooks** | `acf/settings/save_json`, `acf/settings/load_json` (theme `acf-json/`), `acf/prepare_field` |
| **Rules** | All textareas `'rows' => 2` or `3`. All repeaters `'layout' => 'table'` or collapsed. Return format ID for images (`'return_format' => 'id'`). Deprecated fields deleted immediately from code and database upon redesign. Zero ghost fields. |

---

## 8. Zero-footprint privacy & media hygiene

| | |
|---|---|
| **Paths** | `inc/privacy-scrubber.php`, `inc/media-hygiene.php` |
| **Functions** | `elite_scrub_source_fingerprints()`, `elite_sanitize_attachment_filename()`, `elite_validate_media_library_attachment()` |
| **Rules** | Zero references to `abstraxtech.com` in shipped code, comments, CSS classes, schema, or asset names. Media Library attachments only on the frontend (`wp_get_attachment_image()`). Strip EXIF metadata on upload. |

---

## 9. Header, footer, navigation & mobile chrome

| | |
|---|---|
| **Paths** | `inc/header-menu.php`, `inc/footer-menus.php`, `template-parts/header/site-header.php`, `template-parts/footer/site-footer.php`, `assets/css/chrome.css`, `assets/js/chrome.js` |
| **Functions** | `elite_render_header_navigation()`, `elite_render_footer_columns()`, `elite_render_mini_cart_trigger()` |
| **Hooks** | `after_setup_theme` (register nav menus: `primary`, `footer_1`, `footer_2`, `footer_legal`) |
| **Rules** | Native WordPress Appearance → Menus first. Clean responsive drawer menu for mobile with zero layout shift. |

---

## 10. Inquiry-first vs WooCommerce cart checkout

| | |
|---|---|
| **Paths** | `inc/commerce-pages.php`, `inc/inquiry.php`, `template-parts/inquiry/wholesale-modal.php` |
| **Functions** | `elite_is_inquiry_only_mode()`, `elite_handle_wholesale_inquiry()` |
| **Hooks** | `admin_post(_nopriv)_elite_inquiry` |
| **Rules** | If wholesale quote mode is toggled in Elite Terpenes settings, replace "Add to Cart" with "Request Wholesale Quote" modal. Seamlessly toggle to open checkout once payment gateway is verified. |

---

## How to update this map

1. When adding or modifying a feature, locate its section above (or add a new `##` section).
2. Document the exact **Paths**, **Functions**, **Hooks**, **Meta Keys**, and **Rules**.
3. Update the **Quick lookup** table.
4. If architecture changed, update `rules.md` and relevant `docs/*.md`.
5. **A task is NOT done until this file matches the code you shipped.**
