> **Parent Hub:** [[websites/justccell.com/INDEX|🌐 justccell.com Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Cursor brief — justccell.com CCELL 3.0 header hover (2026-09-04)

Read FIRST: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/justccell.com/rules.md` then `docs/STATUS.md`.

Theme only. Deploy Hostinger as `justccell-theme` with `activate: false`. Overwrite that folder only. CSS must still load from `/wp-content/themes/justccell-theme/` (no hash suffix). Do not install a second theme. Do not touch Coming Soon. Do not patch from a partial zip.

Client/Own override (2026-09-04): the header tab must display **CCELL 3.0**, not Justccell 3.0 / justccell3.0. Hover panel must show **3.0 SKUs only**, matching the client screenshot (heading “3.0 CCELL Bio Heating”; tabs All-In-Ones / Cartridges / Pod Systems / 510 Batteries).

## Why REST cannot finish this
`justccell_header_nav_from_tree()` treats both Products and CCELL 3.0 as `products_mega` and always calls `justccell_header_product_tabs()`, which dumps the whole category when ACF `mega_products` is empty. `justccell_header_j3_tabs()` already exists and is unused. ACF REST is disabled, so Chronos cannot write `mega_products`.

## Files to change

### 1. `inc/cms-helpers.php` — `justccell_sanitize_nav_label()`
Today it rewrites `ccell 3.0` / `ccell 3` / `ccell3.0` to `__('Justccell 3.0')`. That is the justccell3.0 hover label.

Change: if the label is CCELL 3.0 (any spacing / NBSP), **return `CCELL 3.0`**. Do not map it to Justccell 3.0. Leave other sanitizing as-is.

Menu item 301 title is currently `CCELL` + NBSP + `3.0` as a REST workaround. After this ships, you may restore a normal space.

### 2. `inc/header-menu.php` — `justccell_header_nav_from_tree()`
When `$kind === 'products_mega'` **and** `justccell_nav_item_is_j3($item)`, use `justccell_header_j3_tabs($kids)` instead of `justccell_header_product_tabs($kids)`. Products mega stays on `justccell_header_product_tabs`.

Also: `justccell_j3_tab_key_from_menu_item()` currently maps any title containing `510` to `cartridge`. Fix that: `510` / `batter` → `battery`. Do not classify 510 Batteries as cartridges.

### 3. `inc/bio-heating.php` — `justccell_j3_product_groups_defaults()`
Add a fourth group (keep existing AIO / cartridge / pod groups):

```
key: battery
heading: 510 Batteries
category: battery
slugs: m4b-pro, m4b-pro-crystalline
names: M4B Pro, M4B Pro Crystalline Edition
```

Live SKUs (already published, do not recreate):
- All-In-Ones: GemBar `gembar` #327270, Flo `flo` #327271, AirOne `airone` #327272, Blade `blade` #327273
- Cartridges: Vita `vita` #327274, Kera `kera` #328869
- Pod Systems: Eazie Pro `eazie-pro` #327276, Eazie Pod `eazie-pod` #327277
- 510 Batteries: M4B Pro `m4b-pro` #328902, M4B Pro Crystalline Edition `m4b-pro-crystalline` #329964

Menu 65 already has CCELL 3.0 (#301) children: All-In-Ones 328908, Cartridges 328909, Pod Systems 328910, 510 Batteries 329963.

## Optional (nice, not required)
If you touch ACF field group for `mega_products` (`field_jc_header_mega_products` on nav items), enable show_in_rest so Chronos can pin IDs later. Not a substitute for the j3_tabs switch.

## Verify
1. Header tab reads **CCELL 3.0** (not Justccell 3.0).
2. Hover AIO = GemBar, Flo, AirOne, Blade only.
3. Hover Cartridges = Vita, Kera only.
4. Hover Pods = Eazie Pro, Eazie Pod only.
5. Hover 510 Batteries = M4B Pro + M4B Pro Crystalline Edition only.
6. Products mega is unchanged (full category).
7. Theme path still `/wp-content/themes/justccell-theme/`.
8. Coming Soon still on.

Update `rules.md` to record this client override for the 3.0 hover label.
