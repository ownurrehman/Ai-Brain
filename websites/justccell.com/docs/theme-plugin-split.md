> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Theme + plugin split — justccell.com

Justccell ships as **two installables** that must run together on production.

## justCCELL Features (plugin)

| | |
|---|---|
| **Vault** | `websites/justccell.com/plugins/justccell-features/` |
| **Live** | `wp-content/plugins/justccell-features/` |
| **Main file** | `justccell-features.php` |
| **Version** | `JUSTCCELL_FEATURES_VERSION` (currently **1.0.0**) |
| **Author** | Rank Ray |

**Owns:** All PHP formerly under `justccell-theme/inc/`, including:

- wp-admin **Justccell** sidebar (Overview, Storefront, Header, Forms, Laser Engraving, Elite Cross-sell, Quote leads)
- WooCommerce catalog, tiers, cart AJAX, checkout modernization, laser engraving
- ACF hooks (JSON still saved under the theme `acf-json/`)
- Inquiry / quote leads CPT
- Geo / storefront cookies, WPML lock, REST prelaunch gate, coming soon logic
- Asset enqueue (`includes/assets.php` — URLs point at the active theme)

## Justccell theme

| | |
|---|---|
| **Vault** | `websites/justccell.com/justccell-theme/` |
| **Live** | `wp-content/themes/justccell-theme/` |
| **Version** | `JUSTCCELL_VERSION` in `functions.php` + `style.css` (asset cache bust) |

**Owns:** Templates, `assets/` (CSS/JS), `acf-json/`, `woocommerce/` overrides, `template-parts/`.

**Does not load feature PHP** — only constants + admin notice if the plugin is inactive.

## Separate plugin (deprecated)

| Plugin | Purpose |
|---|---|
| `jc-acfml-safety` | **Deprecated 1.1.0** — ACFML guard now in `justccell-features/includes/acfml-safety.php`. Deactivate after deploy. |

## Deploy

1. **Plugin:** TUS each changed file under `wp-content/plugins/justccell-features/{path}` **or** zip + wp-admin upload.
2. **Theme:** In-place TUS under `wp-content/themes/justccell-theme/{path}` (see `.cursor/rules/justccell-auto-deploy.mdc`).
3. After plugin deploy: confirm **Plugins → justCCELL Features** is **Active**.
4. Smoke: open **Justccell → Overview**, a product edit screen, and the storefront home.

## Edit rule

| Change type | Edit in |
|---|---|
| New hook, CPT, admin page, Woo logic | `plugins/justccell-features/includes/` |
| New template, CSS, JS, ACF JSON | `justccell-theme/` |
| Both changed | Deploy **both** in the same batch |

Feature index: [[websites/justccell.com/features-code-map|features-code-map.md]]
