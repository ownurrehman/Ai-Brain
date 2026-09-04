> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# justccell.com — Features Code Map

**Purpose:** Kill discovery latency. Read this file **before** opening theme PHP. Do not glob-hunt `justccell-theme/` for a feature that is already listed here.

**Mandatory sync (Rule §0.5):** Any write, refactor, or fix of a feature is incomplete until this file lists the new paths, functions, hooks, and meta keys.

**Theme constant:** `JUSTCCELL_VERSION` in `justccell-theme/functions.php` (bump with `style.css` on asset ships). Hostinger: `u392808260` / WP `30055979`. Elite sister store is a **different** account (`u984013785`).

**Boot order:** `functions.php` `require_once` list is the load graph. Do not add a second include path for an existing module.

Deep specs (do not duplicate here): [[websites/justccell.com/docs/laser-engraving-system|laser-engraving-system.md]] · [[websites/justccell.com/docs/elite-cross-sell|elite-cross-sell.md]] · [[websites/justccell.com/rules|rules.md]] §7.1–§7.9 · [[websites/justccell.com/docs/cms-editor-guide|cms-editor-guide.md]]

---

## Quick lookup

| Feature | Primary PHP | Also |
|---|---|---|
| Theme bootstrap / version | `justccell-theme/functions.php` | `inc/setup.php`, `inc/assets.php`, `style.css` |
| Storefront geo / URL / currency | `inc/storefront.php` | `docs/geo-language-currency.md` |
| WPML / WCML lock | `inc/wpml-lock.php` | — |
| Coming soon (page template) | `inc/coming-soon-page.php` | `page-templates/justccell-coming-soon.php` |
| REST catalog lockdown | `inc/rest-privacy.php` | `inc/setup.php` (`rest_endpoints`) |
| Header mega menu | `inc/header-menu.php` | `template-parts/header/site-header.php`, `inc/nav-fallback.php`, `inc/chrome.php` |
| Footer menus | `inc/footer-menus.php` | `template-parts/footer/site-footer.php` |
| Chat dock / chrome | `inc/chrome.php` | `template-parts/chrome/chat-dock.php` |
| Page templates + bio slug | `inc/page-layouts.php` | `page-templates/justccell-*.php` |
| Homepage | `inc/acf-catalog-pages.php`, `inc/listing.php` | `template-parts/home/clone.php`, `front-page.php` |
| Category listings | `inc/listing.php` | `template-parts/catalog/clone.php` |
| Just CCELL 3.0 / bio heating | `inc/bio-heating.php` | `template-parts/page/brand-bio-heating.php` |
| Contact | `inc/contact-page.php`, `inc/acf-fields.php` | `template-parts/page/contact.php` |
| Locations | `inc/locations-page.php` | `template-parts/page/brand-locations.php` |
| Laser marketing page | `inc/static-pages.php`, `inc/acf-catalog-pages.php` | `template-parts/page/brand-laser.php` |
| About / Why / generic brand | `inc/acf-remaining-pages.php` | `template-parts/page/brand-*.php` |
| Woo catalog (57 SKUs) | `inc/cms-content.php` (`justccell_catalog_from_woo`) | `inc/catalog.php`, `rules.md` §7.8 |
| Legacy URL 301s | `inc/catalog-redirects.php` | `inc/chrome.php` (`justccell_legacy_redirects`) |
| Product clone PDP | `inc/product-pages.php`, `inc/commerce.php` | `template-parts/product/clone.php`, `buy-box.php` |
| Wholesale tier pricing | `inc/tiered-pricing.php` | `assets/js/product.js`, `assets/css/product.css` |
| Laser engraving engine | `inc/laser-engraving.php` | `inc/admin-laser-zone.php`, Fabric JS |
| Cart AJAX + drawer | `inc/cart-ajax.php` | `assets/js/cart-drawer.js`, `template-parts/cart/drawer.php` |
| Inquiry / quote leads | `inc/inquiry.php` | `template-parts/inquiry/form.php` |
| Zero-samples copy policy | `inc/copy-policy.php` | `rules.md` §0.4 |
| Woo cart / checkout / account CSS+PHP | `inc/woocommerce.php`, `inc/commerce-pages.php` | `assets/css/woocommerce.css`, `woocommerce/` |
| Elite Terpenes coupons | `inc/elite-cross-sell.php` | `woocommerce/checkout/thankyou.php` |
| ACF groups + 1:1 cleanup | `inc/acf.php`, `inc/acf-*.php` | `acf-json/`, `inc/cms-helpers.php` |
| CMS Import / media seed | `inc/cms-import.php` | `inc/catalog-seed.php`, `inc/catalog.php` |
| wp-admin Justccell menu | `inc/admin-menu.php` | Elite settings also in `elite-cross-sell.php` |
| Forms (inquiry options) | `inc/forms-settings.php` | ACF options |
| Breadcrumbs | `inc/breadcrumbs.php` | Rank Math filters |
| Discover / blog | `inc/blog.php` | `template-parts/discover/` |
| Rank Math titles | `inc/woocommerce.php`, `inc/chrome.php`, `inc/setup.php` | — |

---

## 1. Theme bootstrap and assets

| | |
|---|---|
| **Paths** | `justccell-theme/functions.php`, `inc/setup.php`, `inc/assets.php`, `header.php`, `footer.php`, `style.css`, `assets/css/globals.css`, `assets/css/chrome.css`, `assets/js/main.js` |
| **Keys** | `JUSTCCELL_VERSION`, `JUSTCCELL_DIR`, `JUSTCCELL_URI`. Options: `justccell_pages_ver` |
| **Hooks** | `after_setup_theme`, `wp_enqueue_scripts`, `body_class`, `wp_head`, `after_switch_theme` → `justccell_seed_site` |
| **Rules** | One live folder `wp-content/themes/justccell-theme/`. In-place TUS only. Bump version in **both** `functions.php` and `style.css` when assets change. Media Library only on the front end. |

---

## 2. Storefront geo, language, currency

| | |
|---|---|
| **Paths** | `inc/storefront.php` · spec `docs/geo-language-currency.md`, `docs/architecture.md` |
| **Functions** | `justccell_detect_store`, `justccell_current_store`, `justccell_current_currency`, `justccell_filter_home_url`, `justccell_geo_redirect`, `justccell_inject_store_prefix`, `justccell_format_money` |
| **Hooks** | `init` (`justccell_persist_front_cookies`), `wp` (`justccell_geo_redirect`), `home_url`, `redirect_canonical`, `woocommerce_currency`, `language_attributes`, `body_class`, `send_headers`, `litespeed_vary_curr_cookies` |
| **Cookies / globals** | `jc_store`, `jc_lang`. `$GLOBALS['justccell_request_store']` |
| **Rules** | UK = bare `justccell.com`. Prefixes **only** `/es/` `/spain/` and `/ch/` `/swiss/`. Do not send traffic to `/uk/` or `/other/`. WPML owns language UI. Cache must vary on store cookies. |

---

## 3. WPML / WooCommerce Multilingual lock

| | |
|---|---|
| **Paths** | `inc/wpml-lock.php` |
| **Functions** | `justccell_lock_wpml_settings_array`, `justccell_lock_wpml_runtime`, `justccell_lock_wcml_settings`, `justccell_wcml_client_currency`, `justccell_recover_untranslated_page` |
| **Hooks** | `pre_update_option_icl_sitepress_settings`, `option_icl_sitepress_settings`, `wpml_loaded`, `pre_update_option__wcml_settings`, `wcml_client_currency`, `wp` / `template_redirect` |
| **Rules** | Do not code a custom language switcher. Recover untranslated pages instead of 404ing. |

---

## 4. Coming soon and REST catalog lockdown

| | |
|---|---|
| **Paths** | `inc/coming-soon-page.php`, `page-templates/justccell-coming-soon.php`, `template-parts/page/brand-coming-soon.php`, `inc/rest-privacy.php`, `inc/setup.php` |
| **Functions** | `justccell_page_shows_coming_soon`, `justccell_rest_prelaunch_gated`, `justccell_rest_route_is_blocked` |
| **Hooks** | `rest_endpoints`, `rest_pre_dispatch` (401 `justccell_rest_prelaunch`). Coming-soon ACF via `acf/load_field_group`, `hidden_meta_boxes` |
| **Options** | `signals_csmm_options`, `csmm_status`, `woocommerce_coming_soon` |
| **Blocked REST prefixes** | `/wp/v2/product`, `/wp/v2/products`, `/wc/v3/products`, `/wc/store/v1/products` |
| **Rules** | Anonymous visitors stay on coming soon until the owner turns it off. Logged-in admins see the site. REST lockdown must not break WP-CLI, cron, or logged-in editors. Filter `justccell_rest_prelaunch_gated` exists for overrides. |

---

## 5. Header mega menu

| | |
|---|---|
| **Paths** | `inc/header-menu.php`, `inc/nav-fallback.php`, `inc/chrome.php`, `template-parts/header/site-header.php`, `inc/acf-fields.php` (`justccell_register_acf_header_menu`) |
| **Functions** | `justccell_primary_menu_tree`, `justccell_header_nav_from_tree`, `justccell_nav_kids_are_product_tabs`, `justccell_header_product_tabs`, `justccell_mega_cards_for_category` |
| **Hooks** | `acf/fields/relationship/query/name=mega_products`, `acf/prepare_field/key=field_jc_header_mega_products`, `admin_head-nav-menus.php` |
| **ACF** | Menu item field `mega_products` (`field_jc_header_mega_products`). Nested children in **Appearance → Menus** = dropdown. Category children = product-card mega. |
| **Rules** | Menu labels render as saved (no auto-rewrite of CCELL 3.0). Optional featured product cards only on category submenu rows. Do not restore retired “Item type” ACF. |

---

## 6. Footer, chat dock, chrome

| | |
|---|---|
| **Paths** | `inc/footer-menus.php`, `inc/chrome.php`, `template-parts/footer/site-footer.php`, `template-parts/chrome/chat-dock.php` |
| **Functions** | `justccell_render_footer_column_menu`, `justccell_chat_dock_links`, `justccell_whatsapp_url`, `justccell_telegram_url`, `justccell_legal_links` |
| **Options** | Justccell → Storefront (`store_*` ACF options): chat URLs, laser video, collection, landings |
| **Rules** | Footer locations seeded on `init`. Chat URLs from Storefront options, not hardcoded. |

---

## 7. Page layouts, templates, bio slug canonicalizer

| | |
|---|---|
| **Paths** | `inc/page-layouts.php`, `page.php`, `page-templates/justccell-home.php`, `justccell-bio.php`, `justccell-about.php`, `justccell-why.php`, `justccell-contact.php`, `justccell-listing.php`, `justccell-location.php`, `justccell-discover.php`, `justccell-brand.php`, `justccell-legal.php`, `justccell-coming-soon.php` |
| **Functions** | `justccell_page_layout_kind`, `justccell_canonicalize_bio_page_slug`, `justccell_bio_canonical_slug` (`justccell-3-0`), `justccell_duplicate_page_admin` |
| **Hooks** | `init` priority 22 (`justccell_canonicalize_bio_page_slug`), `admin_init` (`justccell_ensure_page_layouts`), `page_row_actions`, `admin_action_justccell_duplicate_page` |
| **Options** | `justccell_bio_slug_justccell_3_0`, `justccell_page_layouts_ver` |
| **Rules** | Public bio URL is **`/justccell-3-0/`**. Never 301 it to `/ccell-3-0/`. Duplicate Page is a wp-admin row action. |

---

## 8. Homepage, listings, brand pages (ACF-mapped)

| | |
|---|---|
| **Paths** | `inc/acf-catalog-pages.php`, `inc/acf-remaining-pages.php`, `inc/acf-page-groups.php`, `inc/listing.php`, `inc/bio-heating.php`, `inc/contact-page.php`, `inc/locations-page.php`, `inc/static-pages.php`, `template-parts/home/clone.php`, `template-parts/catalog/clone.php`, `template-parts/page/*`, `template-parts/flexible/*` |
| **Functions** | `justccell_home_rails`, `justccell_listing_hero`, `justccell_j3_acf_string`, `justccell_get_locations_page_data`, `justccell_render_flexible_sections` |
| **ACF groups** | `group_jc_home_full`, `group_jc_listing_page`, `group_jc_generic_brand`, `group_jc_j3_page`, `group_jc_about_page`, `group_jc_why_pages`, `group_jc_contact_page`, `group_jc_laser_page`, `group_jc_locations_page`, `group_jc_legal_pages` |
| **Rules** | Every heading/CTA/image is ACF or native. Seed-on-empty runs on `init` — backend values always win. Locations are UK-only copy (2026-09-01 upgrade). Coming-soon brand slugs use spotlight ACF, not live catalog. |

---

## 9. WooCommerce catalog (locked 57 published products)

| | |
|---|---|
| **Paths** | `inc/cms-content.php` (`justccell_catalog_from_woo`), `inc/catalog.php`, `inc/catalog-seed.php` (import **only**), `inc/product-data.php`, `docs/product-catalog.md` |
| **Functions** | `justccell_catalog()`, `justccell_catalog_item`, `justccell_home_rails`, `justccell_item_url` |
| **Rules** | Public catalog is **Woo published products only**. Do not restore a hardcoded SKU array as the storefront. Seed file is for CMS Import, not front-end. See `rules.md` §7.8. Categories: `all-in-ones`, `cartridge`, `pod-system`, `battery`, `equipment`. |

---

## 10. Catalog and legacy redirects

| | |
|---|---|
| **Paths** | `inc/catalog-redirects.php`, `inc/chrome.php` (`justccell_legacy_redirects`) |
| **Functions** | `justccell_catalog_redirects`, `justccell_catalog_cut_redirects` |
| **Hooks** | `template_redirect` |
| **Rules** | Slug renames + legacy paths only. Do not reintroduce the old catalog-cut trash map. |

---

## 11. Product clone PDP, buy box, wholesale tiers

| | |
|---|---|
| **Paths** | `inc/product-pages.php`, `inc/commerce.php`, `inc/tiered-pricing.php`, `product-clone.php`, `woocommerce/single-product.php`, `template-parts/product/clone.php`, `template-parts/product/buy-box.php`, `assets/js/product.js`, `assets/js/product-high-scroll.js`, `assets/css/product.css`, `assets/js/admin-tiered-pricing.js` |
| **Functions** | `justccell_product_buy_box`, `justccell_product_buy_attributes`, `justccell_get_product_tiered_pricing`, `justccell_tier_unit_price_for_qty` |
| **Hooks** | `woocommerce_product_data_tabs` / `_panels`, `woocommerce_process_product_meta`, `woocommerce_before_calculate_totals` (hardware tiers) |
| **Meta** | `_justccell_tiered_pricing` (`JUSTCCELL_TIER_META`). ACF product group `group_jc_product_clone`: `clone_product_heading` (H1), `clone_subtitle` (H2), `clone_specs` / `clone_specs_heading` (H3), `clone_features` (incl. `text_color`), `clone_banner`. Woo gallery + featured image for media. |
| **JS** | `paintTiers()` on `[data-buy-qty]` → class `.active-tier`. Variation gallery swap in `product.js`. Config JSON in `[data-buy-config]`. |
| **Rules** | Inquiry-first: Add to basket does **not** create a Woo line unless laser opt-in (see §12). Buy-box visual hierarchy is `rules.md` §7.1 — do not restore “Your price” or `.is-on` on tiers. Overlay text colour: `rules.md` §7.2 (`.p-high__txt--white`). Do not restore Banner heading ACF. |

---

## 12. Laser engraving engine

| | |
|---|---|
| **Paths** | `inc/laser-engraving.php`, `inc/admin-laser-zone.php`, `template-parts/product/laser-engraving.php`, `template-parts/product/laser-offer.php`, `assets/js/laser-engraving.js`, `assets/js/vendor/fabric.min.js` (vendored — **never CDN**), `assets/css/laser-engraving.css`, `assets/js/admin-laser-zone.js`, `assets/css/admin-laser-zone.css`, `assets/css/admin-laser-acf.css` |
| **Functions** | `justccell_laser_config`, `justccell_laser_render_ui`, `justccell_laser_ingest_cart_item_data`, `justccell_laser_persist_artwork`, `justccell_laser_is_internal_meta_key` |
| **Cart key** | `$cart_item['justccell_laser']` (enabled, artwork, preview, text, whatsapp, unit, setup_fee, layout, safe_zones) |
| **Order item meta (hidden)** | `_justccell_laser`, `_justccell_laser_artwork_url`, `_justccell_laser_preview_url`, `_justccell_laser_text`, `_justccell_laser_whatsapp`, `_justccell_laser_unit`, `_justccell_laser_setup_fee`, `_justccell_laser_layout`, `_justccell_laser_safe_zones` |
| **Customer-facing meta** | `Engraving artwork`, `Engraving text`, `WhatsApp (proof)`, `Engraving setup fee`, `Engraving (per unit)` |
| **Hooks** | `woocommerce_add_cart_item_data`, `woocommerce_add_to_cart_validation`, `woocommerce_before_calculate_totals`, `woocommerce_cart_calculate_fees`, `woocommerce_checkout_create_order_line_item`, `woocommerce_hidden_order_itemmeta`, `woocommerce_order_item_get_formatted_meta_data`, `woocommerce_get_item_data`, `woocommerce_is_purchasable` (999), `wp_loaded` (custom ATC handler), `wpo_wcpdf_after_item_meta` |
| **Uploads** | `JUSTCCELL_LASER_UPLOAD_DIR` = `laser-engravings`, max `JUSTCCELL_LASER_MAX_BYTES` |
| **ACF** | Product `group_jc_laser_engraving` (`enable_engraving`, canvas, zones). Options `group_jc_laser_engraving_global`. Category defaults `group_jc_laser_engraving_cat`. |
| **Rules** | Only explicit engraving path may create a cart line while inquiry-first is on. Underscore meta must stay hidden on cart, emails, account, PDF. HPOS: use `$item->add_meta_data()`. Do not invent a second engraving stack. Full contract: `docs/laser-engraving-system.md`. |

---

## 13. Cart AJAX and mini-cart drawer

| | |
|---|---|
| **Paths** | `inc/cart-ajax.php`, `assets/js/cart-drawer.js`, `assets/css/cart-drawer.css`, `template-parts/cart/drawer.php` |
| **Functions** | `justccell_process_add_to_cart`, `justccell_cart_ajax_add_to_cart`, `justccell_cart_drawer_payload`, `justccell_cart_prepare_variable_add_to_cart_request` |
| **Ajax** | `wp_ajax(_nopriv)_justccell_add_to_cart`, `wp_ajax(_nopriv)_justccell_cart_drawer` |
| **Rules** | Variable products: resolve attributes before Woo ATC. Laser payload rides the same AJAX add. Nonce required. |

---

## 14. Inquiry / quote leads (not checkout)

| | |
|---|---|
| **Paths** | `inc/inquiry.php`, `inc/forms-settings.php`, `template-parts/inquiry/form.php`, `template-parts/inquiry/form-contact.php`, `template-parts/flexible/cta_inquiry.php` |
| **CPT** | `jc_lead` (not public). Admin under **Justccell → Quote leads** |
| **Hooks** | `admin_post(_nopriv)_justccell_inquiry`, `admin_post(_nopriv)_justccell_subscribe` |
| **Functions** | `justccell_register_leads`, `justccell_handle_inquiry`, `justccell_store_lead`, `justccell_form_setting` |
| **Rules** | Native form, no CF7. Recipient from Forms / Storefront options. VAT field stored but not mailed as body spam. Inquiry-first until payments authorized. |

---

## 15. Zero-samples copy policy

| | |
|---|---|
| **Paths** | `inc/copy-policy.php` |
| **Functions** | `justccell_banned_cta_phrases`, `justccell_text_has_banned_cta`, `justccell_upgrade_client_copy_policy_v0991` / `_v0992` / `_v0993` |
| **Hooks** | `init` priorities 77–79 (one-shot option scrubs) |
| **Rules** | Never output “Get samples” / sample turnaround. Client Mr Nas. Use Inquire / Contact / Quote. |

---

## 16. WooCommerce native overrides (cart, checkout, my account)

| | |
|---|---|
| **Paths** | `inc/woocommerce.php`, `inc/commerce-pages.php`, `assets/css/woocommerce.css`, `assets/js/cart-wording.js`, `woocommerce/cart/cart-empty.php`, `woocommerce/checkout/thankyou.php`, `woocommerce/myaccount/my-account.php`, `woocommerce/myaccount/dashboard.php`, `woocommerce/archive-product.php`, `woocommerce/content-product.php`, `commerce-shell.php` |
| **Functions** | `justccell_cart_label`, `justccell_replace_basket_with_cart`, `justccell_is_order_received_page`, `justccell_order_received_meta_rows` |
| **Hooks** | `woocommerce_enqueue_styles` (dequeue default Woo CSS), `gettext*` (basket → cart), `template_include` (commerce shell), `woocommerce_add_to_cart_redirect`, `woocommerce_product_tabs`, `post_type_link` (category/slug permalinks), product_cat admin columns |
| **Rules** | Classic product editor (block product editor off). Overlap/spacing in `rules.md` §7.6. Thank-you also renders Elite card. Do not restyle with Elementor. |

---

## 17. Cross-domain coupon generation (Elite Terpenes)

| | |
|---|---|
| **Paths** | `inc/elite-cross-sell.php`, `woocommerce/checkout/thankyou.php`, `assets/css/commerce.css` (`.jc-elite-card`), `inc/admin-menu.php` (parent menu). Elite plugin is **not** this theme. |
| **Constants** | `JUSTCCELL_ELITE_META_COUPON` `_elite_cross_sell_coupon`, `_elite_cross_sell_expires`, `_elite_cross_sell_last_error`, `_elite_cross_sell_lock`. Action `justccell_elite_create_coupon`. Option `justccell_elite_cross_sell`. Timeout **4** seconds. TTL **48** hours. |
| **Functions** | `justccell_elite_queue_for_order`, `justccell_elite_create_coupon_for_order`, `justccell_elite_remote_request`, `justccell_elite_thankyou_card`, `justccell_elite_render_promo_card` |
| **Hooks** | `woocommerce_order_status_processing`, `woocommerce_order_status_completed`, `woocommerce_payment_complete`, Action Scheduler `justccell_elite_create_coupon`, `woocommerce_thankyou` (priority 4), `woocommerce_email_before_order_table`, `woocommerce_admin_order_data_after_billing_address` |
| **REST** | `POST {api_url}/wp-json/wc/v3/coupons` — code `JC-{order_id}`, percent `0`, `free_shipping` true, usage 1, email lock |
| **wp-config overrides** | `JUSTCCELL_ELITE_API_URL`, `JUSTCCELL_ELITE_STORE_URL`, `JUSTCCELL_ELITE_CONSUMER_KEY`, `JUSTCCELL_ELITE_CONSUMER_SECRET` — **never commit secrets** |
| **Rules** | Never fail Justccell checkout. Action Scheduler first; inline fallback 4s. Reverse `ET-{order_id}` is **not built**. Elite Hostinger `u984013785`. Specs: `docs/elite-cross-sell.md` · Elite `docs/cross-site-free-delivery.md`. |

---

## 18. ACF hygiene, product clone cleanup, CMS helpers

| | |
|---|---|
| **Paths** | `inc/acf.php`, `inc/acf-fields.php`, `inc/acf-page-groups.php`, `inc/acf-catalog-pages.php`, `inc/acf-remaining-pages.php`, `inc/cms-helpers.php`, `acf-json/` |
| **Functions** | `justccell_acf_register_field_group`, `justccell_acf_maintain_product_clone_field_group`, `justccell_acf_legacy_product_clone_field_names`, `justccell_highlight_text_color_choices`, `justccell_product_detail_photo_ids` |
| **Hooks** | `acf/settings/save_json` + `load_json` (theme `acf-json/`), `acf/location/rule_* /justccell_page_slug`, `acf/load_field_group`, `acf/prepare_field` (hide legacy `field_jc_prod_*`), `use_block_editor_for_post` (off on mapped pages) |
| **Rules** | Strict 1:1 frontend/backend. Purge ghost fields immediately. Legacy clone fields hidden — `rules.md` §7.7. JSON save path is the theme folder. |

---

## 19. CMS Import, media sideload, admin hub

| | |
|---|---|
| **Paths** | `inc/cms-import.php`, `inc/catalog-seed.php`, `inc/catalog.php` (`justccell_sideload_media_file`), `inc/admin-menu.php` |
| **Functions** | `justccell_run_cms_import`, `justccell_render_cms_import_page`, `justccell_render_admin_hub` |
| **Options** | `justccell_cms_imported`, `justccell_cms_pages_imported` |
| **Admin** | **Justccell** top-level: Overview, Storefront, Header, Forms, Media, Quote leads. CMS Import hidden under `options.php`. Elite Cross-sell registered from `elite-cross-sell.php`. |
| **Rules** | Import is a theme tool, not a leftover plugin. Front end must use Media Library IDs after import. Strip EXIF; no `ccell` in filenames. |

---

## 20. Discover (blog), breadcrumbs, SEO chrome

| | |
|---|---|
| **Paths** | `inc/blog.php`, `inc/breadcrumbs.php`, `home.php`, `single.php`, `category.php`, `search.php`, `template-parts/discover/*`, `assets/css/discover.css` |
| **Functions** | `justccell_is_discover_view`, `justccell_discover_listing_query`, `justccell_the_breadcrumbs`, `justccell_rank_math_breadcrumb_html` |
| **Hooks** | Rank Math `rank_math/frontend/breadcrumb/*`, `pre_get_posts`, `term_link`, `document_title_parts` (also `inc/woocommerce.php`, `inc/chrome.php`) |
| **Rules** | Plugins first: Rank Math + WPML SEO for sitemaps/hreflang. Theme only fixes breadcrumb labels and Woo page titles (Cart vs Basket). Discover registry: `docs/post-registry.md`. |

---

## 21. Files agents should not treat as live features

| Path | Why |
|---|---|
| `archive/theme-releases/` | Frozen zips, not the working copy |
| `archive/media-seed/` | Seed photos only |
| `sister-sites/eliteterpenez/` | Elite plugin **source copy**. Live Elite is Hostinger `u984013785`, plugin `justccell-coupon-bridge` |
| `inc/catalog-seed.php` | Import seed, not storefront catalog |
| Theme `inc/cross-sell.php` on **Elite** | Placeholder; reverse coupon not built |

---

## How to update this map

1. Identify the feature row (or add a new `##` section).
2. Set **Paths**, **Functions/Hooks**, **Meta/Options**, **Rules**.
3. Refresh the **Quick lookup** table.
4. If architecture changed, also patch `rules.md` and the matching `docs/*.md`.
5. No task is done until this file matches the code you shipped.
