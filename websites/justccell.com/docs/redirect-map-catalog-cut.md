> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[INDEX|🧠 Ai Brain]]

# 301 Redirect Map — Catalog Cut (Hermes → Cursor handoff)

**Job 1 of 5 completed 2026-09-02.** 36 Woo products moved to TRASH (recoverable, never force-deleted).
Published catalog verified: exactly 11 products (airone, blade, voca-pro-max, eco-star, flo, gembar, eazie-pro-3-0, eazie-pod-3-0, vita, th2-evomax, m6t-evomax).

**Cursor owns the actual 301 implementation** (no redirect plugin installed by Hermes).

## Category URLs (targets)

- `/all-in-ones/` — All-In-Ones
- `/pod-system/` — Pod Systems (products: `/pod-system/eazie-pro/`, `/pod-system/eazie-pod/`)
- `/cartridge/` — Cartridges
- `/battery/` — 510 Batteries
- Equipment category: pending (Cursor creates; Tuner page will live there)

## 36 redirects: trashed slug → suggested 301 target

| Trashed slug | Suggested 301 target |
|---|---|
| bellos | /all-in-ones/ |
| bellos-pod | /all-in-ones/ |
| blanc | /all-in-ones/ |
| ceramic-evomax | /all-in-ones/ |
| dart | /all-in-ones/ |
| dart-pod | /all-in-ones/ |
| dart-x | /all-in-ones/ |
| diama | /cartridge/ (do NOT 301 to kera — different device) |
| ds0103 | /all-in-ones/ |
| eazie-pod-only-3-0 | /pod-system/eazie-pro/ (combination, per doc) |
| fino | /all-in-ones/ |
| flexcell | /all-in-ones/ (do NOT 301 to flex — different product) |
| flexcell-pro | /all-in-ones/ (do NOT 301 to flex) |
| flexcell-x | /all-in-ones/ (do NOT 301 to flex) |
| go-stik | /all-in-ones/ |
| listo | /all-in-ones/ |
| luster-pro | /all-in-ones/ |
| luster-pro-pod | /all-in-ones/ |
| m3-plus | /battery/ (do NOT 301 to m4 — different device) |
| m3b-plus | /battery/ (do NOT 301 to m4) |
| mini-tank | /all-in-ones/ |
| mixjoy | /all-in-ones/ |
| palm-pro | /battery/ (do NOT 301 to palm-se — different device) |
| rosin-bar | /all-in-ones/ |
| sandwave | /all-in-ones/ |
| skye-ii | /all-in-ones/ |
| slym | /all-in-ones/ |
| stylo | /all-in-ones/ |
| tank | /all-in-ones/ (clone hero SKU, not in pack) |
| th2-se | /cartridge/th2-evomax/ (pack is TH2-EVO line; until remap goes live, closest live page) |
| m6t-se | /cartridge/m6t-evomax/ (pack is M6T-EVO line) |
| vision-box | /all-in-ones/ |
| vision-box-elite | /all-in-ones/ |
| voca | /all-in-ones/voca-pro-max/ (pack is Voca Pro Max only) |
| voca-max | /all-in-ones/voca-pro-max/ (pack is Voca Pro Max only) |
| voca-pro | /all-in-ones/voca-pro-max/ (pack is Voca Pro Max only) |

**Notes for Cursor:**
- Doc rule: do NOT 301 flexcell→flex, palm-pro→palm-se, m3-plus→m4, diama→kera (different devices). Category-level targets used instead.
- th2-se / m6t-se: doc says pack is TH2-EVO / M6T-EVO. The live pages are still slugged th2-evomax / m6t-evomax (renames happen in job 2). After Cursor's remap 301s (th2-evomax→th2-evo), these should point at the final URLs: /cartridge/th2-evo/ and /cartridge/m6t-evo/.
- All trashed products remain in WP trash (36 verified via wc/v3?status=trash). Nothing force-deleted.


---

## Job 2 addendum (2026-09-02): Renames for Cursor's 301 map

| Old permalink | New permalink (verified in DB) | Front-end status |
|---|---|---|
| /pod-system/eazie-pro-3-0/ | /pod-system/eazie-pro/ | old -> 200 same page; new -> 200 OK. Cursor: add 301 old->new. |
| /pod-system/eazie-pod-3-0/ | /pod-system/eazie-pod/ | old -> 200 same page. Cursor: add 301 old->new. |
| /cartridge/th2-evomax/ | /cartridge/th2-evo/ | BOTH currently resolve to the product; new URL 301s to old (theme router stale map - see mastersheet job 2 note). Cursor: fix router, then 301 old->new. |
| /cartridge/m6t-evomax/ | /cartridge/m6t-evo/ | Same as above. |
