# Rank Ray Location Pages ACF Audit — 2026-04-19

## Architecture
- Location pages use ACF fields, NOT standard WP content editor
- `select_service` field determines which template/fields render:
  - "SEO" → SEO-style ACF fields (59 fields)
  - "Digital Marketing" → DM-style ACF fields (36 fields)
- Pages are dynamic: frontend renders based on service type + city values

## Summary
- Total location pages: 38
- SEO pages: 21 (all have select_service = ["SEO"])
- Digital Marketing pages: 17 (all have select_service = ["Digital Marketing"])

## Digital Marketing Pages (17) — All 100% Fields Filled
All 17 DM pages have 36/36 ACF fields completed.
- Abu Dhabi, Chicago, Dallas, Dubai, Houston, Islamabad, Karachi, Lahore, London, Los Angeles, Miami, Milton, New York, Sydney, Toronto, Vancouver, Rawalpindi

## SEO Pages (21) — All 100% Fields Filled, 6 Have Quality Issues

### Quality Issues Found:

| Page | ID | Issue |
|------|----|-------|
| SEO Agency in Austin | 19285 | H2 too similar to H1 (80% overlap) |
| SEO Agency in Houston | 19283 | H2 too similar to H1 (80% overlap) |
| SEO Agency in Miami | 19281 | H2 too similar to H1 (80% overlap) |
| SEO Agency in New York | 19253 | H2_P2 too short vs P1 (129w vs 143w) |
| SEO Agency in Los Angeles | 19254 | H2_P2 too short vs P1 (115w vs 156w) |
| SEO Agency in Dubai | 18020 | H2_P2 too short + missing state field |
| Real Estate SEO Dubai | 18999 | 0 internal links in ACF content |

### SEO Pages with No Issues (14):
Muscat, Sharjah, Doha, Ajman, Abu Dhabi, Dallas, Mississauga, Ottawa, Calgary, Seattle, Vancouver, Toronto, Milton, Chicago

## Action Items

### 1. Fix H1/H2 Cannibalization (3 pages)
- Austin, Houston, Miami have H2 almost identical to H1
- H2 needs to target a different angle/keyword than H1

### 2. Expand H2_Paragraph_2 (3 pages)
- New York, Los Angeles, Dubai have P2 shorter than P1
- Per rules: P2 must be materially longer than a short summary
- Expand to 150+ words with substance, no fluff

### 3. Add State Field to Dubai SEO (ID:18020)
- Missing state field

### 4. Add Internal Links to Real Estate SEO Dubai (ID:18999)
- 0 internal links across all ACF fields
- Need 3-5 minimum, verified against sitemap

### 5. Featured Images — DONE
- All 38 pages have unique images with proper alt text
- Bad images (cooking/food) deleted

### 6. Cross-page Cannibalization Check — TODO
- Need to verify H1 and H2 uniqueness across all 38 pages
- No two pages should target the same primary keyword