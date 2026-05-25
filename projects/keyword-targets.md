# Daily Position Tracker — Keyword Targets

**Date:** 2026-05-24  
**Purpose:** Canonical keyword lists for daily position tracking across all active client sites + Rank Ray.

---

## rankray.com (SEO Agency)

| # | Keyword | Type | Target Page | Baseline Position (2026-05-24) |
|---|---------|------|-------------|-------------------------------|
| 1 | rankray seo agency | Brand | Homepage | Inferred page 1 |
| 2 | seo agency new york | Service | /seo-agency-new-york/ | Inferred page 1 |
| 3 | seo agency chicago | Service | /seo-agency-chicago/ | Inferred page 1 |
| 4 | geo content strategy | Pillar | /geo-content-strategy/ | Unknown |
| 5 | entity seo | Pillar | /entity-seo/ | Unknown |
| 6 | ai overview optimization | Pillar | /ai-overview-optimization/ | Unknown |
| 7 | digital marketing agency | Service | Homepage | Unknown |

---

## tonicphysio.com (Physiotherapy — Milton, ON)

| # | Keyword | Type | Target Page | Baseline Position (2026-05-24) |
|---|---------|------|-------------|-------------------------------|
| 1 | physiotherapy milton | Hub | /physiotherapy-milton/ (ID 12897, DRAFT) | Inferred page 1 (blog present) |
| 2 | physiotherapy campbellville | Local | /physiotherapy-in-campbellville/ (ID 12894, DRAFT) | Page 2+ (not published) |
| 3 | physiotherapy acton | Local | /physiotherapy-in-acton/ (ID 12895, DRAFT) | Page 2+ (not published) |
| 4 | physiotherapy georgetown | Local | /physiotherapy-in-georgetown/ (ID 12896, DRAFT) | Page 2+ (not published) |
| 5 | custom bracing milton | Service/Blog | Blog post (published) | Unknown |

---

## coinsfera.com (Crypto OTC — Istanbul)

| # | Keyword | Type | Target Page | Baseline Position (2026-05-24) |
|---|---------|------|-------------|-------------------------------|
| 1 | sell bitcoin in Istanbul | Transactional | /sell-bitcoin-in-istanbul/ | #3-5 (V3 audit) |
| 2 | buy bitcoin in Istanbul | Transactional | /buy-bitcoin-in-istanbul/ | #4-6 (V3 audit) |
| 3 | sell usdt in Istanbul | Transactional | /sell-tether-in-istanbul/ | #3 (V3 audit) |
| 4 | buy usdt in Istanbul | Transactional | /buy-tether-in-istanbul/ | #4 (V3 audit) |
| 5 | crypto exchange Istanbul | Service | Homepage | #3-4 (V3 audit) |
| 6 | bitcoin shop Istanbul | Service | Homepage | #2-3 (V3 audit) |

---

## teammotorcycle.com (Motorcycle Gear Ecommerce)

| # | Keyword | Type | Target Page | Baseline Position (2026-05-24) |
|---|---------|------|-------------|-------------------------------|
| 1 | motorcycle gear online | Ecommerce | Homepage | Inferred page 1 |
| 2 | motorcycle parts store | Ecommerce | /collections/motorcycle-parts | Inferred page 1 |
| 3 | best motorcycle helmets | Blog/Category | /blogs/guides/tagged/best-motorcycle-helmets | Inferred page 1 |
| 4 | affordable motorcycle accessories | Blog/Category | /blogs/guides/tagged/affordable-motorcycle-accessories | Inferred page 1 |

**Note:** This site needs a full keyword strategy. The above are inferred from search results, not a formal target list.

---

## Tracking Rules

1. **Daily run:** 07:00 PKT via daily-position-tracker cron
2. **Comparison:** Compare to previous day's positions stored in memory or Google Sheet
3. **Flag thresholds:**
   - Movers up: >+3 positions
   - Movers down: >-3 positions
   - Page 2 stuck: positions 11-20 for 3+ consecutive days
4. **Data source priority:**
   - P0: DataForSEO API (exact positions)
   - P1: SEMrush API (if endpoint fixed)
   - P2: Firecrawl web_search (position inference)
   - P3: OpenSERP local (if port issue resolved)

---

*Created by Enigma during baseline position tracker run — 2026-05-24*
