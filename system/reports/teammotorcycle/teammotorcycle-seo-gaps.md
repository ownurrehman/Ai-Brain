> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[websites/teammotorcycle.com/index|teammotorcycle.com Hub]] · [[INDEX|🧠 Ai Brain]]

# Team Motorcycle SEO Gap Analysis

**Audit Date:** April 19, 2026  
**Domain:** teammotorcycle.com  
**Platform:** Shopify (Warehouse Theme v6.3.0)  
**Auditor:** Chaos, Rank Ray

---

## Executive Summary

Team Motorcycle is a Daytona-based motorcycle gear e-commerce site with 2,171 products, 429 collections, and 359 blog posts. The site has solid technical foundations (fast load times, proper sitemaps, schema markup) but suffers from critical on-page SEO gaps that limit organic visibility.

**Key Issues:**
- Missing meta descriptions on collection and product pages
- 55% of product images have empty alt text
- No review rich snippets despite Judge.me integration
- Thin content on key collection pages
- Internal links in blog content use raw URLs instead of descriptive anchor text

---

## 1. Critical Technical Findings

### 1.1 Missing Meta Descriptions (Critical)

**URL:** `https://www.teammotorcycle.com/collections/motorcycle-helmets`  
**Element:** `<meta name="description">`  
**Issue:** No standard meta description tag present. Only `og:description` exists.

**Evidence:**
```html
<!-- Found: og:description only -->
<meta property="og:description" content="Upto 30% off on Motorcycle Helmets...">
<!-- Missing: <meta name="description" content="..."> -->
```

**Impact:** Google may generate snippets from page content, reducing CTR control.

**Affected Pages:**
- `/collections/motorcycle-helmets` - No meta description
- `/collections/motorcycle-boots` - No meta description  
- `/products/*` - Product pages lack meta descriptions (2,171 URLs)

**Recommendation:** Add unique meta descriptions (150-160 characters) targeting primary keywords for all collection and product templates.

---

### 1.2 Empty Image Alt Text (Critical)

**URL:** `https://www.teammotorcycle.com/collections/motorcycle-helmets`  
**Element:** Product grid images  
**Metric:** 30 images with `alt=""` vs 25 with descriptive alt text (55% empty)

**Evidence:**
```html
<img src="..." alt="">  <!-- 30 instances -->
<img src="..." alt="Team Motorcycle">  <!-- Generic -->
```

**Impact:** Lost image search visibility, accessibility issues, missed keyword relevance signals.

**Recommendation:** Implement dynamic alt text using product title + brand + key attribute (e.g., "Bell Qualifier DLX Street Helmet - Matte Black").

---

### 1.3 Missing Review Rich Snippets (Critical)

**URL:** `https://www.teammotorcycle.com/products/high-mileage-premium-mens-black-leather-motorcycle-jacket`  
**Element:** Product schema  
**Issue:** Judge.me app installed but `aggregateRating` schema not rendering.

**Evidence:**
```bash
$ grep -c 'aggregateRating' product-page.html
0
```

**Current Schema:** ProductGroup with variants (price, availability, SKU)  
**Missing:** `aggregateRating`, `reviewCount`

**Impact:** No star ratings in SERPs, reducing CTR vs competitors with review snippets.

**Recommendation:** Configure Judge.me to output JSON-LD review schema. Verify with Google Rich Results Test.

---

## 2. On-Page Content Gaps

### 2.1 Homepage Title Optimization (Warning)

**URL:** `https://www.teammotorcycle.com/`  
**Element:** `<title>`  
**Current:** "Buy Motorcycle Gear Online with Reviews and Guides - Team Motorcycle"  
**Length:** 67 characters

**Issue:** Brand name appears at end. Primary keyword "motorcycle gear" is competitive; brand should lead for branded search recognition.

**Recommendation:** 
```
Team Motorcycle | Motorcycle Helmets, Jackets, Boots & Gear
```
(58 characters, brand-first, includes top category keywords)

---

### 2.2 Thin Collection Content (Critical)

**URL:** `https://www.teammotorcycle.com/collections/motorcycle-boots`  
**Element:** Collection description  
**Issue:** Only "Filter Gear By Category" rendered. No SEO content block.

**Comparison:** `/collections/motorcycle-jackets` has 750+ words of SEO content with internal links to:
- `/collections/motorcycle-pants`
- `/collections/motorcycle-gloves`
- `/collections/denim-motorcycle-jackets`
- `/collections/leather-motorcycle-jackets`

**Evidence:**
```html
<!-- motorcycle-boots: minimal content -->
<h1>Motorcycle Boots</h1>
<!-- No substantial description block -->

<!-- motorcycle-jackets: rich content -->
<h2>Ride Safely with the Right Motorcycle Jacket</h2>
<h2>Find a Jacket That Matches Your Riding Style</h2>
<h3>Key Jacket Features to Watch For</h3>
```

**Impact:** `/collections/motorcycle-boots` cannot rank for informational queries; misses internal link equity distribution.

**Recommendation:** Replicate motorcycle-jackets content model across all top-level collections (helmets, boots, vests, gloves, pants).

---

### 2.3 Blog Internal Link Structure (Warning)

**URL:** `https://www.teammotorcycle.com/blogs/guides/best-cold-weather-motorcycle-jackets-2024`  
**Element:** Product links in content  
**Issue:** Raw URL links instead of descriptive anchor text.

**Evidence:**
```html
<!-- Current -->
<a href="https://www.teammotorcycle.com/arctiva-pivot-6-mens-snow-jacket">
  https://www.teammotorcycle.com/arctiva-pivot-6-mens-snow-jacket
</a>

<!-- Should be -->
<a href="/arctiva-pivot-6-mens-snow-jacket">
  Arctiva Pivot 6 Men's Snow Jacket
</a>
```

**Occurrences:** 3 instances of raw URL links in this post alone.

**Impact:** Poor UX, missed anchor text relevance signals, looks spammy to crawlers.

**Recommendation:** Audit all 359 blog posts; replace raw URL links with proper anchor text.

---

## 3. Technical SEO Audit

### 3.1 Site Architecture

| Metric | Count |
|--------|-------|
| Products | 2,171 |
| Collections | 429 |
| Blog Posts | 359 |
| Sitemap Files | 4 (products, collections, pages, blogs) |

**Status:** ✅ Proper sitemap index structure. All sitemaps accessible.

---

### 3.2 Pagination Implementation

**URL:** `https://www.teammotorcycle.com/collections/motorcycle-helmets`  
**Status:** ✅ Correct implementation

**Evidence:**
```html
<link rel="canonical" href="https://www.teammotorcycle.com/collections/motorcycle-helmets">
<link rel="next" href="/collections/motorcycle-helmets?page=2">

<!-- Page 2 -->
<link rel="canonical" href="https://www.teammotorcycle.com/collections/motorcycle-helmets?page=2">
<link rel="prev" href="/collections/motorcycle-helmets?page=1">
<link rel="next" href="/collections/motorcycle-helmets?page=3">
```

**Depth:** 24 pages for motorcycle-helmets collection.

---

### 3.3 Schema Markup Inventory

| Schema Type | Homepage | Collections | Products | Blog Posts |
|-------------|----------|-------------|----------|------------|
| Organization | ✅ | ❌ | ❌ | ❌ |
| BreadcrumbList | ✅ | ✅ | ✅ | ✅ |
| Product | ❌ | ❌ | ✅ | ❌ |
| Article | ❌ | ❌ | ❌ | ✅ |
| aggregateRating | ❌ | ❌ | ❌ | ❌ |
| FAQPage | ❌ | ❌ | ❌ | ❌ |

**Critical Gap:** Blog posts contain FAQ sections but no FAQPage schema.

**Example:** `/blogs/guides/best-cold-weather-motorcycle-jackets-2024` has 5 FAQ Q&As that could generate FAQ rich results.

---

### 3.4 Page Performance

| URL | TTFB | Total Load | Size |
|-----|------|------------|------|
| Homepage | 92ms | 415ms | 601KB |
| Collection | - | - | 1.2MB |
| Product | - | - | 719KB |

**Status:** ✅ Excellent server response times.  
**Warning:** Collection page sizes (1.2MB) may impact mobile Core Web Vitals.

---

### 3.5 Robots.txt Analysis

**Status:** ✅ Standard Shopify configuration.

**Key Directives:**
- Blocks: `/admin`, `/cart`, `/checkout`, `/account`, `/search`
- Allows: All collection, product, blog, and page URLs
- Crawl-delay: 10s for AhrefsBot, MJ12bot

**No Issues:** Sitemap declared, no accidental blocking of indexable content.

---

## 4. Keyword Opportunities

### 4.1 Collection Page Target Keywords

Based on current title/H1 structure:

| Collection | Current H1 | Suggested Primary Keyword |
|------------|-----------|---------------------------|
| /collections/motorcycle-helmets | "Motorcycle Helmets" | "motorcycle helmets", "biker helmets" |
| /collections/motorcycle-jackets | "Motorcycle Jacket" | "motorcycle jackets", "biker jackets" |
| /collections/motorcycle-boots | "Motorcycle Boots" | "motorcycle boots", "riding boots" |
| /collections/motorcycle-vests | "Motorcycle Vests" | "motorcycle vests", "biker vests" |

**Opportunity:** Add secondary keyword variations to meta descriptions and H2 headings.

---

### 4.2 Blog Content Gaps

**Existing High-Value Topics:**
- `/blogs/guides/best-cold-weather-motorcycle-jackets-2024` ✅
- `/blogs/guides/motorcycle-riding-injuries-prevention-gear` ✅

**Missing Topics (Competitor Gap):**
- Motorcycle helmet safety ratings (DOT vs Snell vs ECE)
- How to measure motorcycle jacket size
- Best motorcycle gear for beginners
- Motorcycle luggage buying guide

**Recommendation:** Create 4-6 pillar guides targeting informational keywords with product CTAs.

---

## 5. Internal Linking Analysis

### 5.1 Collection-to-Collection Links

**Strong:** `/collections/motorcycle-jackets` links to:
- `/collections/motorcycle-pants`
- `/collections/motorcycle-gloves`
- `/collections/motorcycle-helmets`
- `/collections/motorcycle-boots`
- `/collections/motorcycle-luggage`

**Weak:** `/collections/motorcycle-helmets` has no cross-collection links in description.

**Recommendation:** Add "Complete Your Gear" section to helmet, boots, and vests collections.

---

### 5.2 Blog-to-Product Links

**Current Pattern:** Blog posts link to products but use inconsistent URL formats.

**Issue Found:**
```
https://www.teammotorcycle.com/arctiva-pivot-6-mens-snow-jacket  (valid)
https://www.teammotorcycle.com/cortech-mens-aero-tec-v2-motorcycle-jacket/  (trailing slash)
https://www.teammotorcycle.com/alpinestars-andes-air-drystar-motorcycle-jacket/  (trailing slash)
```

**Status:** All URLs return 200, but inconsistent formatting is not ideal.

**Recommendation:** Standardize on no trailing slash for product URLs site-wide.

---

## 6. Competitor Benchmarking

*Note: Direct competitor crawling was blocked (Revzilla, ChapMoto returned 403). Analysis based on industry standards.*

### Expected Competitor Features:
- Review rich snippets (star ratings in SERPs)
- FAQ schema on product and guide pages
- Comprehensive collection descriptions (1,000+ words)
- Image alt text on 95%+ of product images
- Brand-first title tags

**Team Motorcycle Gap:** Missing 4 of 5 expected features.

---

## 7. Priority Action Items

### Critical (Fix Within 2 Weeks)

| # | Task | URL/Scope | Expected Impact |
|---|------|-----------|-----------------|
| 1 | Add meta descriptions to collection templates | `/collections/*` (429 URLs) | High - CTR improvement |
| 2 | Fix empty alt text on product images | All collection grids | High - Image search visibility |
| 3 | Enable Judge.me review schema | Product template | High - Star ratings in SERPs |
| 4 | Add SEO content to thin collections | `/collections/motorcycle-boots`, `/collections/motorcycle-helmets` | High - Rankings for collection pages |

### Warning (Fix Within 30 Days)

| # | Task | URL/Scope | Expected Impact |
|---|------|-----------|-----------------|
| 5 | Rewrite homepage title tag | Homepage | Medium - Branded search CTR |
| 6 | Fix raw URL links in blog posts | 359 blog posts | Medium - UX + anchor text relevance |
| 7 | Add FAQPage schema to blog posts | Posts with FAQ sections | Medium - FAQ rich results |
| 8 | Standardize product URL format (no trailing slash) | Site-wide redirect rules | Low - Crawl efficiency |

### Info (Quarterly Optimization)

| # | Task | URL/Scope | Expected Impact |
|---|------|-----------|-----------------|
| 9 | Create 4-6 pillar blog guides | `/blogs/guides/*` | Medium - Top-of-funnel traffic |
| 10 | Add cross-collection links | All collection descriptions | Low - Internal link equity |
| 11 | Optimize collection page size | Reduce from 1.2MB | Low - Core Web Vitals |

---

## 8. Monitoring Recommendations

1. **Google Search Console:** Track impressions/CTR for collection pages after meta description implementation.
2. **Rich Results Test:** Verify review schema rendering post-Judge.me configuration.
3. **Site Crawl (Weekly):** Monitor for new empty alt text on uploaded products.
4. **Rank Tracking:** Target keywords: "motorcycle helmets", "motorcycle jackets", "motorcycle boots" (category-level).

---

## Appendix: Technical Reference

### Sitemap URLs
```
https://www.teammotorcycle.com/sitemap_products_1.xml (2,171 products)
https://www.teammotorcycle.com/sitemap_collections_1.xml (429 collections)
https://www.teammotorcycle.com/sitemap_pages_1.xml
https://www.teammotorcycle.com/sitemap_blogs_1.xml (359 posts)
```

### Key Template Files (Shopify)
- Collection template: `/templates/collection.liquid` or `/sections/collection-template.liquid`
- Product template: `/templates/product.liquid` or `/sections/product-template.liquid`
- Blog article template: `/templates/article.liquid`

### Schema References
- [Product Schema](https://schema.org/Product)
- [AggregateRating](https://schema.org/AggregateRating)
- [FAQPage](https://schema.org/FAQPage)
- [Article](https://schema.org/Article)

---

**Report Generated:** April 19, 2026  
**Next Audit Recommended:** July 2026 (post-implementation review)
