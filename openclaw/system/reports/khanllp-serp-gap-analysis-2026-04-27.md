# Khan LLP SERP Gap Analysis — 2026-04-27

## Site Inventory
| Category | Count |
|----------|-------|
| Service/core pages | 51 |
| Location-optimized pages | 22 |
| Blog posts | 149 |
| Info/utility pages | 4 |
| **Total sitemap URLs** | **227** |

**Practice Areas:** Real Estate Law, Family Law, Immigration Law, Criminal Law, Wills & Estates

**Location Coverage (5 cities):** Toronto, Mississauga, Milton, Oakville, North York

## Critical Gaps

### 1. Zero Images in Sitemap (CRITICAL)
- **All 227 URLs** have no `<image:image>` tag in the sitemap — even the homepage.
- No featured images uploaded to media library or registered in Yoast sitemap.
- **Impact:** Zero image search visibility, missing visual ranking signal.
- **Fix:** Upload themed images to every service/location page, ensure Yoast includes them in sitemap.

### 2. REST API Disabled / 404
- `/wp-json/wp/v2/` endpoints return 404.
- **Impact:** Cannot audit Yoast meta titles/descriptions programmatically.
- **Fix:** Enable WP REST API access or use alternative audit method.

### 3. Location Page Coverage
- 22 location pages across 5 cities and 4 practice areas + some extras.
- Good structure, but all missing images and likely missing schema.

### 4. Blog Health
- **149 blog posts** — strong volume.
- Topics cover: Real estate transactions, title transfers, family law, immigration, criminal defense.
- Post titles are keyword-rich and question-based (good for featured snippets).
- **Unknown:** Last update dates (REST API unavailable). Need manual sampling.

## Structural Review
- **Sitemap:** Flat XML (not Yoast sitemap index format). Every page has priority=0.8, changefreq=daily. This is too homogeneous — homepage and key practice pages should have higher priority.
- **Site uses:** Custom WordPress (non-Yoast sitemap engine or RankMath/other).
- **URL structure:** Clean `/practice-area` and `/blogs/post-title` format. Good.

## Recommended Actions
| Priority | Action | Effort |
|----------|--------|--------|
| P1 | Upload featured images to all 227 pages (service, location, blog) | 4-6h |
| P2 | Enable REST API for programmatic audits | 30m |
| P3 | Audit and fix Yoast meta titles/descriptions (manual sample check) | 2h |
| P4 | Add LegalService schema to location pages | 2h |
| P5 | Check blog freshness — identify posts >1 year old for refresh | 2h |
| P6 | Add FAQ schema to high-value service pages | 1h |

## Summary
**Site health: Moderate.** 227 URLs with good topical depth across 4 practice areas and 5 cities. Strong blog at 149 posts. **Critical issue: zero images in sitemap** — this is killing visual search potential. REST API is blocked preventing deeper analysis.
