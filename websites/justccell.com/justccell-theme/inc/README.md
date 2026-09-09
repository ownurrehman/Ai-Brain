> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Theme `inc/` — moved to plugin

All PHP feature modules formerly in `justccell-theme/inc/` now live in:

**`websites/justccell.com/plugins/justccell-features/includes/`**

| Layer | Path | Owns |
|---|---|---|
| **Plugin** | `plugins/justccell-features/` | wp-admin **Justccell** menu, WooCommerce, ACF hooks, cart, checkout, inquiry, geo, laser, Elite cross-sell |
| **Theme** | `justccell-theme/` | Templates, `assets/`, `acf-json/`, `style.css` |

Spec: [[websites/justccell.com/docs/theme-plugin-split|theme-plugin-split.md]]

Do not add new feature PHP under the theme. Edit the plugin copy and deploy both when templates + logic ship together.
