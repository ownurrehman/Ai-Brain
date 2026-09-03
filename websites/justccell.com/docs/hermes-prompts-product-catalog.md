> **Parent Site:** [[websites/justccell.com/index|🌐 justccell.com Hub]]

# Hermes prompts — justccell.com product catalog cut

Paste **one prompt per Hermes session**. Finish and verify before the next. Source of truth: [product-catalog.md](product-catalog.md).

**Hard rules for every prompt**

- Site: https://justccell.com/ (Coming Soon on for logged-out — work logged in).
- Woo REST + Hostinger WP autologin as usual. Theme PHP is **out of scope**.
- **Never permanently delete.** Woo trash only. Do not empty trash. Do not `force=true`.
- Do not invent GBP prices. Tuner sheet $50 EXW is not a UK price — leave buy-box empty.
- Do not upload a full 12-up launch slide as a product image. Isolated crops only.
- After each job: append `websites/justccell.com/docs/BUILD-LOG.md` and a line in Hermes MEMORY. No passwords.

Launch files (read-only source):

`/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Mazhar - Just CCELL Devices/Products/Launch files/`

---

## Prompt 1 — trash the 36 clone SKUs

```
Justccell.com catalog cut, job 1 of 5. Labor only. Do not edit the theme.

Read websites/justccell.com/docs/product-catalog.md section "Trash (36)".

Move these 36 Woo products to TRASH (status=trash). Never force-delete, never empty trash.

IDs: 268 bellos, 271 bellos-pod, 252 blanc, 260 ceramic-evomax, 267 dart, 270 dart-pod, 266 dart-x, 327275 diama, 245 ds0103, 327278 eazie-pod-only-3-0, 273 fino, 244 flexcell, 250 flexcell-pro, 254 flexcell-x, 275 go-stik, 247 listo, 265 luster-pro, 269 luster-pro-pod, 278 m3-plus, 277 m3b-plus, 242 mini-tank, 327269 mixjoy, 276 palm-pro, 248 rosin-bar, 274 sandwave, 246 skye-ii, 253 slym, 272 stylo, 255 tank, 263 th2-se, 264 m6t-se, 257 vision-box, 249 vision-box-elite, 243 voca, 259 voca-max, 251 voca-pro.

Do NOT trash: airone, blade, voca-pro-max, eco-star, flo, gembar, eazie-pro-3-0, eazie-pod-3-0, vita, th2-evomax, m6t-evomax.

Verify: GET /wp-json/wc/v3/products?status=publish&per_page=100 returns only those 11 (plus any you already created — should be 11 now). Count published = 11.

Write a two-column list of trashed slug → suggested 301 target category URL. Do not install redirect plugins. Cursor owns 301s.

Stop. Do not create products in this job.
```

---

## Prompt 2 — remap 5 slugs + titles

```
Justccell.com catalog cut, job 2 of 5. Labor only. Do not edit the theme.

Read websites/justccell.com/docs/product-catalog.md section "Remap slugs".

On the EXISTING products, change slug + public title. Do not create duplicates.

1. ID 327276  slug eazie-pro-3-0  → eazie-pro     title "Eazie Pro"
2. ID 327277  slug eazie-pod-3-0  → eazie-pod     title "Eazie Pod"
3. ID 261     slug th2-evomax     → th2-evo       title "TH2-EVO"
4. ID 262     slug m6t-evomax     → m6t-evo       title "M6T-EVO"

Keep categories. Keep IDs. Confirm old URLs 404 or still resolve via Woo; note both old and new permalinks for Cursor's 301 map.

Verify GET each new permalink logged-in returns 200 with the clone product template.

Stop. Do not fill ACF copy yet.
```

---

## Prompt 3 — create the 8 missing products

```
Justccell.com catalog cut, job 3 of 5. Labor only. Do not edit the theme.

Read websites/justccell.com/docs/product-catalog.md "Create (8)" and the spec rows.

Create 8 published Woo simple products. Duplicate a cousin, then retitle. Classic editor. Fill Woo Name, slug, category, catalog visibility published. Leave prices empty. Short description optional. Long story stays in ACF later.

| Name | Slug | Category | Duplicate cousin |
| Easy Bar Evo Max | easy-bar | All-In-Ones | AirOne |
| Flex | flex | All-In-Ones | Eco Star |
| Flex 2 | flex-2 | All-In-Ones | Eco Star |
| Kera | kera | Cartridges | Vita |
| M4 | m4 | 510 Batteries | (any battery cousin still in trash is OK to duplicate from trash then change; else add new) |
| M4 Tiny | m4-tiny | 510 Batteries | same |
| Palm SE | palm-se | 510 Batteries | same |
| AIO Voltage Tuner | aio-voltage-tuner | Uncategorised (Equipment category is not in Woo yet) | any sparse SKU |

Do NOT use slugs flexcell, palm-pro, m3-plus, diama, tank.

Verify 8 new IDs + permalinks. Published catalogue should now be 11 remapped + 8 new = 19.

Stop. Do not write feature essays yet. A placeholder title on the Tuner is fine.
```

---

## Prompt 4 — ACF Product page copy from launch files

```
Justccell.com catalog cut, job 4 of 5. Labor only. Do not edit the theme.

Read websites/justccell.com/docs/product-catalog.md spec tables.

For ALL 19 keep products, fill ACF "Product page" from the matching launch PDF in:

/Users/sheikhown/Library/CloudStorage/OneDrive-Personal/Rank Ray - Company/RR - Clients/Working - Clients/Mazhar - Just CCELL Devices/Products/Launch files/

OCR/read the PDFs. Put:

- Hero tagline + subtitle from the sheet headline
- Specs exactly as printed (tank, battery mAh, mm + in, voltages, materials, activation, USB-C)
- Feature rows from the sheet (do not invent a fifth feature)
- Fill / cap steps where the sheet has them (Kera, Vita, Voca Pro Max, Eco Star, Flex, Eazie, GemBar)
- Catalog listing tagline
- Colours: only colours shown on that sheet (Eazie Pro black/rose gold/mint/lavender; M4 and M4 Tiny black/white/silver; Flex Graphite/Mist Blue). Hide colour dropdown if the sheet has none.
- Combinations: tank sizes as combinations (e.g. AirOne 1ml / 2ml). Eazie Pro also needs Pod+battery / Pod only / Battery only with empty prices.
- Laser: ON for hardware, OFF for AIO Voltage Tuner
- Heating / EVOMAX panel: only if the sheet is a 3.0 / Evo / Evo Max device. Batteries and Tuner = skip that block.

Brand voice: Justccell, authorized CCELL hardware, B2B. No "buy on ccell.com". No emojis. No em-dashes. No filled-oil claims. Disclaimer: hardware only, not for minors.

Do not attach images in this job.

Verify 3 URLs logged-in: /all-in-ones/flex/ /cartridge/kera/ /pod-system/eazie-pro/ show specs + features, no clone leftover names (no Flexcell, no 3.0 in H1, no Tank).
```

---

## Prompt 5 — Rank Math + homepage / mega content picks

```
Justccell.com catalog cut, job 5 of 5. Labor only. Do not edit the theme.

1) Rank Math on all 19 products: unique title <60, description <160, focus keyword = product name + "CCELL" or "vape cartridge/battery/AIO" as fits. Featured image = OG when images exist; skip OG if no image yet.

2) Homepage (Pages → Home, Justccell Home template): set device-rail product picks to ONLY the 19. Suggested rails:
   - All-In-Ones: Voca Pro Max, Eco Star, AirOne, GemBar, Flex, Easy Bar Evo Max, Blade, Flo
   - Pods: Eazie Pro, Eazie Pod
   - Cartridges: Kera, Vita, TH2-EVO, M6T-EVO
   - Batteries: Palm SE, M4, M4 Tiny
   Remove Tank, Mini Tank, Flexcell, MixJoy, Dart, any trashed slug. If an ACF relationship still points at a trashed ID, clear it.

3) Appearance → Menus → Products mega: featured cards = Voca Pro Max, Eazie Pro, Kera, Palm SE, GemBar, Flex, AirOne, Eco Star. No MixJoy. No Tank.

4) Justccell 3.0 page: remove MixJoy. Keep GemBar, Flo, AirOne, Blade. Add Vita if that page has a cartridge slot.

5) Confirm quote form / SKU dropdown (if ACF options) only lists the 19.

Verify logged-in: homepage rails have no Tank/Flexcell/MixJoy; Products mega same; 19 product URLs 200.

Append BUILD-LOG. List any ACF field you could not find so Cursor can fix the theme.
```

---

## Prompt 6 — Featured images + data QA (after Cursor theme 0.9.93 is live)

```
Justccell.com catalog cut — job 6 of 6. Labor only. No theme PHP, no redirect plugins.

Read:
- websites/justccell.com/docs/product-catalog.md
- websites/justccell.com/docs/BUILD-LOG.md (jobs 1–5 + Cursor 0.9.93 notes)

DO NOT fix URL redirects or theme router — Cursor owns theme 0.9.93 deploy.

=== 0) Trash stray publish ===
Diama (product ID 327275, slug diama) is still published — NOT in the 19-SKU lock. Move to trash.

=== 1) Featured + card images ===
8 SKUs have NO Woo featured image:
easy-bar, flex, flex-2, kera, m4, m4-tiny, palm-se, aio-voltage-tuner

Crop from launch PDFs in:
Rank Ray - Company/RR - Clients/Working - Clients/Mazhar - Just CCELL Devices/Products/Launch files/

Upload pattern: {slug}-justccell-{kind}-featured.png
Set Woo product image + ACF clone_card_image + alt text.

Eazie Pro + Eazie Pod: replace any eazie-*-3-0-* media filenames with eazie-pro / eazie-pod naming.

=== 2) Data QA ===
Verify M4 mAh (catalog lock says 250, Rank Math may say 290) against launch PDF; fix ACF + RM if wrong.
Confirm all 19 keep SKUs have clone_card_tagline + clone_oil_group.

=== 3) Verify ===
REST: exactly 19 publish, 19/19 with featured image, 0 stray publishes outside the lock list.
Frontend: all 19 reachable at canonical URLs once Cursor theme 0.9.93 is live on the server.
Log any slug still missing from category grids.
Append BUILD-LOG + mastersheet. Stop.
```

---

## After Hermes finishes

Cursor still owns: deploy theme 0.9.93 to live (hPanel zip or fixed TUS), purge cache, post-deploy URL QA. Optional job 7: re-push Rank Math for tuner at `/equipment/aio-voltage-tuner/` after Equipment routing is live.
