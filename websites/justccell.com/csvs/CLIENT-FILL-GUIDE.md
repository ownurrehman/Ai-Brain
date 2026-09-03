> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Client Fill Guide — justccell.com Product CSV (Prices + Stock)

**File:** `csvs/justccell-product-prices-stock-CLIENT-FILL.csv`
**Built:** 2026-09-02 from the live site (19 products, IDs measured from WooCommerce).
**Currency:** GBP, prices **EX VAT** (site adds VAT at checkout later).

---

## Rules that MUST be followed (or the import fails)

1. **DO NOT change** the following columns: `ID`, `Product Name`, `Slug`, `Product Type`, `Category`. These anchor the update to the exact live product. If a name needs fixing, tell us, do not edit it in the CSV.
2. **SKU column:** 8 products already have a SKU on the site (e.g. `airone`, `vita`). The other 11 have a proposed SKU filled in (`{slug}-jc-01` format). You may edit any SKU BEFORE we run the import; after the import, SKUs become permanent.
3. **One row per product.** Colour variants go in the `Colour Variants` column (comma separated) - do NOT create extra rows for colours.
4. **Prices:** fill numbers only, no `£` sign, no commas as thousand separators. Decimals with a dot (e.g. `3.60`).
5. **Tiers (bulk discounts):** 5 quantity tiers per product, matching the live site structure:
   - Tier 1 = 1 to 100 units: `Unit Price (1-100)`
   - Tier 2 = from 101: `Tier 2 Qty From` (default 101) + price
   - Tier 3 = from 1001, Tier 4 = from 5001, Tier 5 = from 10001.
   - Leave a tier price blank if you do not want that tier.
6. **Stock:** fill `Stock Quantity` (whole number). Keep `Stock Status` = `instock` unless the item is not available, then `outofstock`. `Low Stock Threshold` = 5 unless told otherwise.
7. **Bundles:** if you sell bundles (e.g. cartridge + battery pack), fill `Bundle Components (SKU list)` with the component SKUs comma-separated and `Bundle Component Qty` with matching counts (e.g. `2,1` = 2 cartridges + 1 battery). Optional `Bundle Discount %`.
8. **Do not add rows** for new products in this file. New products = separate request.

## What the measured data already filled in

- `ID`, `Slug`, `SKU` (existing ones), `Category`, `Product Type` for all 19.
- `Colour Variants` from the launch files (client may edit).
- `Combination Options` for Eazie Pro (Pod and battery; Pod only; Battery only).
- `Unit Price` column shows the **current theme display price** (AIO/cartridges/pods 3.60, batteries 2.77) as a reference hint only. **The client must replace these with real prices.**

## What happens after you return the file

1. We validate it (limits, duplicate SKUs, number formats).
2. We import to WooCommerce: prices, tiers (into the site's quote price-break table), stock, SKUs.
3. We verify the frontend: buy-widget tiers, quote SKUs, stock status.
4. Bundles get created as separate grouped products if filled.

## Column reference (quick)

| Column | Required | Meaning |
|---|---|---|
| ID | yes | Woo product ID (fixed) |
| Product Name | yes | fixed |
| Slug | yes | fixed |
| SKU | yes | editable before import |
| Product Type | yes | reference |
| Category | yes | reference |
| Published | yes | 1 or 0 |
| Visible On Site | yes | visible / hidden |
| Currency | yes | GBP |
| Unit Price (1-100, GBP ex VAT) | yes | base unit price |
| Tier 2-5 Qty From | optional | quantity threshold |
| Tier 2-5 Price | optional | per-item price at that tier |
| Stock Quantity | yes | integer |
| Stock Status | yes | instock / outofstock / onbackorder |
| Low Stock Threshold | optional | default 5 |
| Manage Stock | yes | 1 or 0 |
| Colour Variants | optional | comma list |
| Combination Options | optional | semicolon list |
| MOQ | optional | default 1 |
| Sale Price / dates | optional | Woo sale price engine |
| Weight / dimensions | optional | for shipping calc later |
| Bundle columns | optional | only for bundles |
| Notes | no | internal only |
