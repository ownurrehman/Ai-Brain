# Just CCELL cutout + Enigma gap report — 2026-09-07

> **RESEARCH / PROPOSAL ONLY** · No live Woo/WordPress edits · Own approves via Chronos queue.
> Woo REST: authenticated GET (Coming Soon aware) · Materials: Chronos inventory on box (`justccell-client-materials-index.json`: **1472 cutouts / 268 banners**).

## Executive counts

| Metric | Count |
|---|---:|
| Published parent products scanned | 59 |
| Materials cutouts (Chronos) | 1472 |
| Materials banners/lifestyle (Chronos) | 268 |
| Chronos asset-gap products (cutout/colour) | 37 (see asset-gap-audit) |
| Enigma-flagged products (copy/SEO) | 40 |
| Batch E approval rows (incl. alt rollup) | 17 |
| Sample/boilerplate long desc (P0 copy) | 4 |
| Brand slip Justccell (P0) | 1 |
| AI chatgpt-image basenames (P1 SEO) | 3 products |
| H1 vs Rank Math EVOMAX/EVO (P1) | 2 |
| Missing image alts (P2 SEO) | 27 products |
| Empty short_description | 0 |
| Missing Rank Math title/desc/focus | 0 |

## Artifacts / approval path

| Artifact | Path |
|---|---|
| Chronos asset audit | `/workspace/justccell-asset-gap-audit.md` |
| **Own approval queue (Batch A + Batch E)** | `/workspace/justccell-approval-queue.md` |
| Materials inventory | `/workspace/justccell-client-materials-inventory.md` |
| Materials index JSON | `/workspace/justccell-client-materials-index.json` |
| Enigma scan JSON | `/workspace/justccell-enigma-scan.json` |
| This report | `/workspace/justccell-cutout-gap-20260907.md` |
| JSON summary | `/workspace/justccell-cutout-gap-20260907.json` |

Cutout/colour proposals live in **Batch A** (Chronos). Enigma content/SEO proposals live in **Batch E** (this agent). Own approves per row; nothing applied until then.

## GemBox #331665 — verify notes (Enigma + assets)

| Area | Status | Action |
|---|---|---|
| Featured + gallery PNGs (3) | **Good** — cutout-leaning, descriptive alts | **Do not overwrite** |
| Short description | OK (237 chars, Just CCELL) | Keep |
| Rank Math title/desc/focus | Present | Keep |
| Long description | Thin (302 chars) vs peer AIOs | Optional Enigma enrich — Batch E |
| Colour / model image wiring / prices | Gaps | Chronos Batch **A1** only |
| Materials | `Product Material/AIO/3.2 Gem Box20250710` — 23 cutouts; Launch `GemBox/` = banners only (≠ product images) | Use cutouts for A1 if Own expands colours/models; never banners on PDP |

## Enigma-lane gap table (Chronos card shape)

| Product (id/sku/name) | Gap type (cutout/colour/copy/SEO) | What’s missing | Proposed asset/source path | Proposed change |
|---|---|---|---|---|
| P0 328902/M4BPRO-JC/M4B Pro | copy | sample/boilerplate long desc (88 chars): 'M4B Pro by CCELL. Premium 510 thread battery with reliable performance and sleek design.' | Enigma rewrite (no asset path) | See Batch E — sample_long |
| P0 328903/RIZO-JC/Rizo | copy | sample/boilerplate long desc (85 chars): 'Rizo by CCELL. Premium 510 thread battery with reliable performance and sleek design.' | Enigma rewrite (no asset path) | See Batch E — sample_long |
| P2 328903/RIZO-JC/Rizo | SEO | generic featured alt on rizo-justccell-featured.webp: 'rizo Just CCELL Featured' | Media Library alt / Rank Math / rename | See Batch E — weak_alt |
| P0 328904/KAP-JC/Kap | copy | sample/boilerplate long desc (84 chars): 'Kap by CCELL. Premium 510 thread battery with reliable performance and sleek design.' | Enigma rewrite (no asset path) | See Batch E — sample_long |
| P2 328904/KAP-JC/Kap | SEO | generic featured alt on kap-justccell-featured.png: 'kap Just CCELL Featured' | Media Library alt / Rank Math / rename | See Batch E — weak_alt |
| P0 328905/SILO-JC/Silo | copy | sample/boilerplate long desc (85 chars): 'Silo by CCELL. Premium 510 thread battery with reliable performance and sleek design.' | Enigma rewrite (no asset path) | See Batch E — sample_long |
| P2 328905/SILO-JC/Silo | SEO | generic featured alt on silo-justccell-featured.jpg: 'silo Just CCELL Featured' | Media Library alt / Rank Math / rename | See Batch E — weak_alt |
| P0 329152/easy-bar/Easy bar | copy | alt on easy-bar-se-justccell-featured.png: contains Justccell | Enigma rewrite (no asset path) | See Batch E — brand_slip_alt |
| P1 257/vision-box/Vision Box | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-08_10_33-pm.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 257/vision-box/Vision Box | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-08_14_15-pm.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 260/ceramic-evomax/Ceramic-EVOMAX | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-09_25_58-pm-1.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 260/ceramic-evomax/Ceramic-EVOMAX | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-09_15_54-pm-1.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 260/ceramic-evomax/Ceramic-EVOMAX | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-09_15_55-pm-2.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 260/ceramic-evomax/Ceramic-EVOMAX | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-09_25_58-pm-2.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 260/ceramic-evomax/Ceramic-EVOMAX | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-09_34_07-pm.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 261/th2-evomax/TH2-EVOMAX | SEO | product name 'TH2-EVOMAX' vs Rank Math title lead 'TH2-EVO Glass Cartridge' (EVOMAX vs EVO wording) | Media Library alt / Rank Math / rename | See Batch E — h1_rm_mismatch |
| P1 262/m6t-evomax/M6T-EVOMAX | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-09_54_41-pm-1.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 262/m6t-evomax/M6T-EVOMAX | SEO | media basename looks AI-export: chatgpt-image-sep-6-2026-09_54_41-pm-2.png | Media Library alt / Rank Math / rename | See Batch E — ai_filename |
| P1 262/m6t-evomax/M6T-EVOMAX | SEO | product name 'M6T-EVOMAX' vs Rank Math title lead 'M6T-EVO Plastic Cartridge' (EVOMAX vs EVO wording) | Media Library alt / Rank Math / rename | See Batch E — h1_rm_mismatch |
| P2 328866/EASY-BAR-JC/Easy Bar Evo Max | SEO | generic featured alt on easy-bar-justccell-featured.png: 'easy bar Just CCELL Featured' | Media Library alt / Rank Math / rename | See Batch E — weak_alt |
| P2 328868/FLEX-2-JC/Flex 2 | SEO | generic featured alt on flex-2-justccell-featured.jpg: 'flex 2 Just CCELL Featured' | Media Library alt / Rank Math / rename | See Batch E — weak_alt |
| P2 331665/—/GemBox | copy | long desc 302 chars vs peer AIOs ~1700–1900; assets OK — content enrich only | Content only — keep live PNGs gembox-featured.png + gallery | See Batch E — thin_long_vs_peers |
| P2 (rollup) many products / — / missing alts | SEO | Empty featured/gallery alts on **27** products | Media Library alts only | Batch-fill descriptive alts; no file replace |

## Priority legend

- **P0** — sample leftovers / brand slip Justccell / empty critical copy
- **P1** — AI media basenames, H1↔Rank Math mismatches, weak long desc
- **P2** — missing/weak alts, optional GemBox long-desc enrich
- **Cutout/colour P0–P1** — owned by Chronos Batch A (not duplicated here as apply work)

## Materials reminder

Banner/lifestyle/KV/Launch Slider JPGs are **not** product images. Transparent PNG/WebP renders under Product Material (and legacy web PNGs) are cutouts. See Chronos inventory for paths.
