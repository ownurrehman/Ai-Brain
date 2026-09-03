> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Product catalog — justccell.com

**Locked 2026-09-02** from the client launch-file pack. This is the sellable catalogue. The ccell.com clone (47 SKUs) is a design reference only.

**Source folder:** `Rank Ray - Company/RR - Clients/Working - Clients/Mazhar - Just CCELL Devices/Products/Launch files/`  
**Files:** 17 PDFs (20 pages). Image-based sell sheets. Zip of the same pack also in that folder.

**Rule:** if it is not in a launch file, it does not stay on the public catalogue. Do not keep Tank, Mini Tank, Flexcell, Dart, Luster, MixJoy, Palm Pro, M3, etc. because they look good on the clone.

Related: [cms-editor-guide.md](cms-editor-guide.md) · [client-requirements.md](client-requirements.md) · [open-questions.md](open-questions.md) · [hermes-prompts-product-catalog.md](hermes-prompts-product-catalog.md)

---

## Decision in one line

**19 public products.** 11 already exist (keep / rename). 8 must be created. **36 clone SKUs go to Woo trash** (never force-delete). One new category: **Equipment** (the Voltage Tuner).

Homepage rails, Products mega, Justccell 3.0 cards, and related-product modules must follow this list. They currently still advertise clone SKUs (Tank, MixJoy, Flexcell, …).

---

## Work split

| Who | Owns |
|---|---|
| **Hermes** | Woo catalog labor: trash 36, create 8, rename 11, ACF copy from launch sheets, Rank Math, media attach, homepage / mega **content** picks |
| **Cursor (this agent)** | Theme / design: Equipment category in nav, homepage rail queries, mega featured set, 301 map, Tuner layout, extract crop pack from the PDFs, 3.0 page product set |

Do not mix. Hermes does not edit theme PHP. Cursor does not bulk-type 19 product stories into ACF.

---

## Target catalogue (19)

Counts after the cut: All-In-Ones **9** · Pod Systems **2** · Cartridges **4** · 510 Batteries **3** · Equipment **1**.

### All-In-Ones (`/all-in-ones/`)

| # | Name | Slug | Live now | Launch file | Specs to put on the page |
|---|---|---|---|---|---|
| 1 | AirOne | `airone` | Keep `327272` | Airone | 1 ml / 2 ml. BPA-free PA tank. CCELL 3.0. 210 mAh. L 3.0 V / H 3.2 V. Inhale + bottom switch. USB-C. 1 ml 70.5×42.8×9.8 mm; 2 ml 72.5×42.8×11.3 mm. 3D Stomata + Nexus Film. |
| 2 | Blade | `blade` | Keep `327273` | Blade | 1 ml / 2 ml. Thin palm profile, 360° window. Same 3.0 / Stomata / Nexus stack as AirOne. OEM mouthpiece laser area. |
| 3 | Voca Pro Max | `voca-pro-max` | Keep `258` | DS6410-U Voca Pro Max | 0.5 ml / 1 ml same outer size. 280 mAh. Reprogrammable 2.4 / 2.8 / 3.2 V (Tuner). 10 s preheat (double tap). Child lock 5 taps. Clean airway. Clog-free dual vents. USB-C. 76×36.1×13 mm. Model DS6410-U. |
| 4 | Easy Bar Evo Max | `easy-bar` | **Create** | Easy Bar (4 pages) | 0.5 ml & 1.0 ml. 210 mAh. 3.4 V. Evo Max core. Plastic housing. SS center post. Inhale. USB-C. 99.3×23×10.5 mm. Wrap-led AIO (Rookie / lightning / hex / smoke shown). |
| 5 | Eco Star | `eco-star` | Keep `256` | Eco Star DS6310-U | 0.5 ml / 1 ml. 180 mAh. PLA biodegradable housing, removable recyclable battery. Isolated airway. Dual vents. USB-C. 89×22.1×16.6 mm. All-oil. Model DS6310-U. |
| 6 | Flex | `flex` | **Create** | Flex Series (Flex + Flex 2) | Two body widths, same 102 mm height: 0.5/1.0 ml (20×10.5 mm) and 1.5/2.0 ml (25×10.5 mm). 280 mAh. SiL core. Graphite + Mist Blue. Custom oil-window shapes. USB-C. Inhale. Dual vents. |
| 7 | Flex 2 | `flex-2` | **Create** | same sheet | Pocket body. 2.0 ml. 89×25×15.5 mm. 280 mAh. Same core / colours / OEM window story as Flex. **Not** Flexcell / Flexcell Pro / Flexcell X. |
| 8 | Flo | `flo` | Keep `327271` | Flo | 1.0 ml. BPA-free PA. CCELL 3.0. 200 mAh. 90×18×15.5 mm. Draw 0.35±0.1 kPa. Quiet 35±5 dB. USB-C. Inhale. |
| 9 | GemBar | `gembar` | Keep `327270` | GemBar | 1 ml / 2 ml. Faceted postless tank, smart display. CCELL 3.0 Bio-Heating. 280 mAh. 98.21×22.96×14.64 mm. USB-C. Inhale. |

### Pod systems (`/pod-system/`)

Client merchandising (2/6) wants **one Eazie Pro page** with combination dropdowns (pod+battery / pod only / battery only), plus pods as their own SKU.

| # | Name | Slug | Live now | Launch file | Specs |
|---|---|---|---|---|---|
| 10 | Eazie Pro | `eazie-pro` | Remap `eazie-pro-3-0` (`327276`) | Eazie Pro | Battery 280 mAh, 83.3×22×11.5 mm. L/M/H. Display (preheat, battery, puff). 1 click = battery, 2 = preheat, 5 = lock, long press = mode. Type-C. Inhale. Colours: black, rose gold, mint, lavender. Compatible with Eazie Pod. |
| 11 | Eazie Pod | `eazie-pod` | Remap `eazie-pod-3-0` (`327277`) | Eazie POD | Pods 0.5 / 1.0 ml (46.5×20.4×10.3 mm) and 2.0 ml (52.8×20.4×10.3 mm). EVO heating. Medical-grade SS post. Fill + cap instructions on the sheet. |

**Trash** `eazie-pod-only-3-0` (`327278`). Pod-only is a **combination** on Eazie Pro, not a third product page. 301 that URL to `/pod-system/eazie-pro/`.

### Cartridges (`/cartridge/`)

| # | Name | Slug | Live now | Launch file | Specs |
|---|---|---|---|---|---|
| 12 | Kera | `kera` | **Create** (do **not** reuse Diama) | 1.5 KERA (CGS07) | Full ceramic. 0.5 ml Φ10.5×52 mm / 1.0 ml Φ10.5×62 mm. 1.4 Ω. 510. Snap-fit ceramic mouthpiece, zirconia post, borosilicate glass. 60% less capping force. Model CGS07. |
| 13 | Vita | `vita` | Keep `327274` | Vita | Postless 100% clear tank. 1 ml / 2 ml. 1.5 Ω (±0.15). CCELL 3.0 Bio-Heating. BPA-free thermoplastic. ~Φ10.5×59.9 / Φ14.1×60.3 mm. |
| 14 | TH2-EVO | `th2-evo` | Remap `th2-evomax` (`261`) | TH2-EVO \| M6T-EVO sell sheet | 0.5 / 1.0 / 1.2 ml. Φ10.5 × 52 / 62 / 66.2 mm. 1.4 Ω. 4×Ø2 mm apertures. Screw-on/snap-fit ceramic mouthpiece (0.5+1.0); snap-fit ceramic (1.2). Borosilicate + SS post. **This is EVO, not EVOMAX.** |
| 15 | M6T-EVO | `m6t-evo` | Remap `m6t-evomax` (`262`) | same sheet | 0.5 / 1.0 ml. Φ10.5 × 57.4 / 67.9 mm. 1.4 Ω. Snap-fit/press-in plastic mouthpiece. BPA-free body. SS post. |

### 510 Batteries (`/battery/`)

| # | Name | Slug | Live now | Launch file | Specs |
|---|---|---|---|---|---|
| 16 | M4 | `m4` | **Create** | M4 | 250 mAh. Φ10.5 × 76.5 mm. 510. USB-C. Inhale. LED. Stainless housing. Black / white / silver. **Not** M3 Plus / M3B Plus. |
| 17 | M4 Tiny | `m4-tiny` | **Create** | M4 Tiny | 400 mAh. Φ14 × 59 mm. 510 (fits CCELL 3.0 + other 510). USB-C. Inhale. SS. Black / white / silver. Finger-length. |
| 18 | Palm SE | `palm-se` | **Create** | Palm SE | 500 mAh. 55×42×8.9 mm. 510. 2.8 / 3.2 / 3.6 V. 10 s stable heat. Inhale. USB-C. 3 LED dots. Side window for the cart. **Not** Palm Pro. |

### Equipment (new category)

| # | Name | Slug | Live now | Launch file | Specs |
|---|---|---|---|---|---|
| 19 | AIO Voltage Tuner | `aio-voltage-tuner` | **Create** | RN CCELL TUNER v_0306 | Programs Voca Pro Max (sheet also names Atom — Atom is **not** in this pack). 9 voltages 2.0–3.6 V. 10 USB-C ports. 294×284×103 mm. ABS. Set = tuner + power cable + 10 USB-C leads. EXW **$50** on the sheet (do not publish a price until the client confirms GBP ex VAT). |

Cursor adds the Woo category + Products-mega tab. Until that ships, publish the Tuner as a product in **Uncategorised** and hide it from the four clone grids if needed.

---

## Create (8)

`easy-bar` · `flex` · `flex-2` · `kera` · `m4` · `m4-tiny` · `palm-se` · `aio-voltage-tuner`

Duplicate a cousin first (AirOne for AIOs, Vita for Kera, Palm Pro **layout only** then retitle to Palm SE, M3 Plus layout for M4). Then replace every field. Do not leave Flexcell photos on Flex.

## Remap slugs (5)

| From | To | 301 |
|---|---|---|
| `/pod-system/eazie-pro-3-0/` | `/pod-system/eazie-pro/` | yes |
| `/pod-system/eazie-pod-3-0/` | `/pod-system/eazie-pod/` | yes |
| `/pod-system/eazie-pod-only-3-0/` | `/pod-system/eazie-pro/` | yes (combination, not a page) |
| `/cartridge/th2-evomax/` | `/cartridge/th2-evo/` | yes |
| `/cartridge/m6t-evomax/` | `/cartridge/m6t-evo/` | yes |

Woo: change the slug on the existing product, then add the 301. Do not create a second product.

## Trash (36) — Woo trash only

Never `force=true`. Never empty trash. Coming Soon is still on; still 301 each public URL to its **category** (not to a similarly named different SKU).

| ID | Slug | Why |
|---|---|---|
| 268 | `bellos` | No launch file |
| 271 | `bellos-pod` | No launch file |
| 252 | `blanc` | No launch file |
| 260 | `ceramic-evomax` | No launch file |
| 267 | `dart` | No launch file |
| 270 | `dart-pod` | No launch file |
| 266 | `dart-x` | No launch file |
| 327275 | `diama` | Replaced by Kera |
| 245 | `ds0103` | No launch file |
| 327278 | `eazie-pod-only-3-0` | Combination on Eazie Pro |
| 273 | `fino` | No launch file |
| 244 | `flexcell` | Different product from Flex |
| 250 | `flexcell-pro` | Different product |
| 254 | `flexcell-x` | Different product |
| 275 | `go-stik` | No launch file |
| 247 | `listo` | No launch file |
| 265 | `luster-pro` | No launch file |
| 269 | `luster-pro-pod` | No launch file |
| 278 | `m3-plus` | Replaced by M4 family |
| 277 | `m3b-plus` | Replaced by M4 family |
| 242 | `mini-tank` | No launch file |
| 327269 | `mixjoy` | No launch file (was a 3.0 clone card) |
| 276 | `palm-pro` | Replaced by Palm SE |
| 248 | `rosin-bar` | No launch file |
| 274 | `sandwave` | No launch file |
| 246 | `skye-ii` | No launch file |
| 253 | `slym` | No launch file |
| 272 | `stylo` | No launch file |
| 255 | `tank` | Clone hero SKU — not in pack |
| 263 | `th2-se` | Pack is TH2-EVO |
| 264 | `m6t-se` | Pack is M6T-EVO |
| 257 | `vision-box` | No launch file |
| 249 | `vision-box-elite` | No launch file |
| 243 | `voca` | Pack is Voca Pro Max only |
| 259 | `voca-max` | Pack is Voca Pro Max only |
| 251 | `voca-pro` | Pack is Voca Pro Max only |

**Do not 301** `flexcell` → `flex`, `palm-pro` → `palm-se`, `m3-plus` → `m4`, `diama` → `kera`. Those are different devices. 301 to `/all-in-ones/`, `/battery/`, `/cartridge/` as appropriate.

---

## Name collisions (read before creating)

| Clone name | Client name | Action |
|---|---|---|
| Flexcell / Flexcell Pro / Flexcell X | Flex / Flex 2 | New slugs `flex` and `flex-2`. Trash Flexcell family. |
| TH2-EVOMAX / M6T-EVOMAX | TH2-EVO / M6T-EVO | Rename existing. New generation on the sell sheet. |
| Palm Pro | Palm SE | New `palm-se`. Trash Palm Pro. |
| M3 Plus / M3B Plus | M4 / M4 Tiny | New slugs. Trash M3 family. |
| Diama | Kera | New `kera`. Trash Diama. |
| Eazie * 3.0 | Eazie Pro / Eazie Pod | Drop the “3.0” in the public title. |

---

## Copy and images

Launch files are **multi-panel sell sheets**, not web-ready PNGs. Use them as the spec + feature source.

- **Copy:** Hero tagline, feature slides, specs table, fill/cap steps, OEM notes. Strip manufacturer “CCELL.com” CTAs. Brand is Justccell / Just CCELL by 3Devices. Hardware only — no filled oil, not for minors (disclaimer already on the sheets).
- **Images:** Crop isolated devices from the sheets into Media Library (`{slug}-justccell-{kind}-featured.png` pattern). Do not upload a whole 12-up slide as the product banner. Cursor extracts a crop pack; Hermes attaches.
- **Q9 (photos):** launch decks unstick the “waiting on assets” block for **these 19**. Isolated packshots can still replace crops later.
- **Prices:** only the Tuner sheet prints a number ($50 EXW). Leave buy-box prices empty (theme default table) until the client sends GBP ex VAT. Do not invent tiers.
- **Laser:** on for branded hardware; **off** for the Tuner (it is a programming tool).
- **Colours:** from the sheet only. Eazie Pro = black / rose gold / mint / lavender. M4 + M4 Tiny = black / white / silver. Flex = Graphite / Mist Blue. Easy Bar = wrap variants as combinations, not a fake generic rainbow.

---

## Surfaces that must follow the 19 (theme + CMS)

1. Woo catalogue + four category pages.
2. Homepage device rails (still clone-era Tank / Mini Tank / Flexcell).
3. Header **Products** mega cards.
4. Justccell 3.0 page (MixJoy / GemBar / Flo / AirOne / Blade). MixJoy out. Keep GemBar, Flo, AirOne, Blade. Add Vita as the 3.0 cartridge if that page lists carts.
5. Related products on each SKU.
6. Quote form SKU list / `?sku=` values.
7. Discover posts may still name Tank in editorial copy — leave posts unless a sentence claims we **sell** Tank.

---

## Do not add without a new launch file

- **Atom** (named on the Tuner sheet as compatible). Not in the pack.
- MixJoy, Tank, Mini Tank, any Flexcell, Dart/Luster/Bellos, Vision Box, Voca / Voca Max / Voca Pro.
- A fifth Eazie URL.

If the client sends more PDFs, append a row here first, then create the product.
