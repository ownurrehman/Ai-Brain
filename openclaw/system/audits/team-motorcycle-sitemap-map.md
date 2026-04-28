# Team Motorcycle Site Map Audit — Phase 1 Complete

**Date:** 2026-04-21
**Time:** 13:15 PKT
**Status:** ✅ Site mapping complete

---

## Site Architecture Overview

**Platform:** Shopify (shopify.com CDN detected)
**Primary Domain:** https://www.teammotorcycle.com

---

## URL Inventory by Type

| Type | Sitemap File | Estimated Count | Priority |
|------|--------------|-----------------|----------|
| **Collections** | sitemap_collections_1.xml | 150+ | 🔴 HIGH |
| **Products** | sitemap_products_1.xml | 2000+ (truncated) | 🟡 MEDIUM |
| **Pages** | sitemap_pages_1.xml | 88 | 🟢 LOW |
| **Blog Posts** | sitemap_blogs_1.xml | 50+ (truncated) | 🟡 MEDIUM |
| **Homepage** | Root sitemap | 1 | 🔴 HIGH |

**Total Indexed URLs:** 2300+ (minimum)

---

## Priority Tier Breakdown

### 🔴 Tier 1 — Critical (Audit First)
1. **Homepage** — `/`
2. **Main Collections** — Core category pages:
   - `/collections/motorcycle-helmets`
   - `/collections/motorcycle-jackets`
   - `/collections/motorcycle-vests`
   - `/collections/motorcycle-gloves`
   - `/collections/motorcycle-luggage`
   - `/collections/motorcycle-gear`
   - `/collections/motorcycle-accessories`

### 🟡 Tier 2 — Important (Audit Second)
1. **Sub-Collections** — Specialized categories:
   - Helmet types: full-face, modular, dual-sport, motocross, open-face, half, cruiser, sportbike, touring, snowmobile
   - Jacket types: mesh, textile, leather, denim, 4-season, hi-viz, waterproof, winter, summer
   - Gender-specific: men's, women's collections
   - Brand collections: Shoei, etc.
   - Closeout/sale collections

2. **Blog Content** — Two main sections:
   - `/blogs/news` — Company updates
   - `/blogs/guides` — SEO content hub (50+ articles)
   - Sample guide topics: cold weather jackets, leather vests, motorcycle chaps, helmet guides, gear comparisons

### 🟢 Tier 3 — Low Priority (Sample Only)
1. **Static Pages** — 88 pages, mostly:
   - Size charts (40+ pages for different brands/products)
   - Policy pages: contact, privacy, terms, returns, FAQs
   - Feature pages: dealer signup, Klarna, loyalty program, custom varsity jackets

2. **Individual Products** — 2000+ SKUs
   - **Strategy:** Sample top 20-30 products from key collections
   - **Focus:** Product template consistency, schema markup, image optimization

---

## Key Observations

### ✅ Strengths
- Clean URL structure (Shopify standard)
- Comprehensive collection hierarchy
- Active blog with guide content
- Daily changefreq on most pages (fresh content signals)

### ⚠️ Potential Issues
- **Massive product count** — 2000+ pages dilute crawl budget
- **Duplicate size charts** — 40+ similar pages may cause cannibalization
- **Blog truncation** — Sitemap exceeded fetch limit (need pagination)
- **No XML sitemap index** — All sitemaps are direct, not hierarchical

---

## Next Steps — Phase 2

**Technical Baseline Audit** will focus on:

1. **Homepage** — Full technical audit
2. **Top 7 main collections** — Duplicate title/meta check, content depth
3. **Sample 10 products** — Schema, descriptions, image alt text
4. **Blog hub** — `/blogs/guides` structure and internal linking

**Files to create:**
- `audits/team-motorcycle-technical-baseline.md`
- `audits/team-motorcycle-keyword-gaps.md` (Phase 3)
- `audits/team-motorcycle-priority-page-audits.md` (Phase 4)

---

## Sitemap Fetch Notes

- All sitemaps fetched successfully at 13:11 PKT
- Products + Collections + Blogs sitemaps truncated (Shopify limit)
- Pages sitemap fully captured (88 URLs)
- Homepage included in products sitemap as root URL

**Recommendation:** For full product audit, use Shopify API or paginate through collection pages instead of sitemap scraping.
