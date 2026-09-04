> **Parent Hub:** [[websites/eliteterpenez.com/INDEX|🌐 eliteterpenez.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Theme & WooCommerce Architecture — eliteterpenez.com

Technical specification and architectural overview for the custom WordPress theme and WooCommerce integration on `eliteterpenez.com`.

---

## 1. Theme Overview

- **Theme Name:** Elite Terpenes (`eliteterpenez-theme`)
- **Location:** `wp-content/themes/eliteterpenez-theme/`
- **Philosophy:** Clean, lean, bespoke WordPress + WooCommerce theme. No page builders, no heavy external JS frameworks, minimal dependencies.

---

## 2. Directory Structure

```
eliteterpenez-theme/
├── style.css                 # Theme header, metadata, versioning
├── functions.php             # Core bootstrap & include loader
├── index.php                 # Fallback blog archive
├── header.php                # Semantic <header>, navigation, notices
├── footer.php                # Semantic <footer>, copyright, links
├── front-page.php            # Homepage template (Abstrax Tech clone)
├── page.php                  # Standard page template
├── single.php                # Standard post template
├── 404.php                   # Error 404 template
├── acf-json/                 # Version-controlled ACF JSON definitions
├── inc/                      # Theme PHP modules & controllers
│   ├── setup.php             # Theme supports, nav menus, image sizes
│   ├── assets.php            # Enqueue scripts & styles with cache busting
│   ├── woocommerce.php       # WooCommerce hooks, overrides & templates
│   ├── acf-fields.php        # Compact ACF field group registrations
│   ├── template-tags.php     # Helper functions, formatters, escaping
│   └── cross-sell.php        # 48h free delivery coupon automation
├── template-parts/           # Reusable UI component templates
│   ├── header/               # Desktop & mobile nav, mini-cart
│   ├── footer/               # Footer columns, badges, newsletter
│   ├── home/                 # Hero, category grid, terpene rails, CTA
│   └── product/              # Aroma radar, dominant terpenes, specs
└── assets/
    ├── css/                  # Modular Vanilla CSS (globals, home, product, woo)
    └── js/                   # Vanilla JavaScript (navigation, cart, profile widgets)
```

---

## 3. WooCommerce Integration Pattern

1. **Native Templates First:**
   - Override WooCommerce templates cleanly inside `eliteterpenez-theme/woocommerce/` only when native action hooks (`woocommerce_before_single_product_summary`, etc.) are insufficient.
2. **Terpene Product Attributes:**
   - Leverage WooCommerce's built-in global attributes (`pa_strain_type`, `pa_aroma_profile`, `pa_dominant_terpenes`) to power faceted filtering and product specs tables.
3. **Cart & Checkout Customization:**
   - Keep checkout streamlined with native WooCommerce blocks or classic checkout forms styled with modular CSS.
   - Support the cross-store free shipping coupon without modifying core checkout logic.

---

## 4. Performance & Caching Architecture

1. **Vanilla JavaScript:** Zero jQuery dependencies where possible; use modern ES6+ vanilla scripts for carousels, mobile menus, and coupon listeners.
2. **Modular CSS:** Split stylesheets by page context (`globals.css`, `home.css`, `product.css`, `woocommerce.css`) to minimize unused CSS.
3. **Cache Busting:** Automatically append the theme version constant (`ELITE_VERSION`) to all enqueued assets.
4. **LiteSpeed Cache Compatibility:** Pre-configured for LiteSpeed page caching and Object Cache.
