# Technical SEO Audit Master Sheet

**Last Updated:** 2026-06-01  
**Current Rotation:** Monday → rankray.com

---

## Audit History

| Date | Site | Status | Critical Issues | Medium Issues | Low Issues |
|------|------|--------|----------------|---------------|------------|
| 2026-06-01 | rankray.com | ✅ Audited | 2 | 4 | 4 |
| 2026-05-31 | tonicphysio.com | ✅ Audited | 2 | 4 | 3 |
| 2026-05-14 | coinsfera.com | ✅ Audited | 1 | 2 | 1 |
| 2026-04-25 | teammotorcycle.com | ✅ Audited | 1 | 1 | 3 |

---

## rankray.com — 2026-06-01

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK (Yoast SEO index with 5 sub-sitemaps)
- **Robots.txt:** ✅ OK (all allowed, sitemap referenced)
- **Indexation:** ✅ Good (10+ pages indexed)
- **Schema:** ⚠️ Unverified (Yoast likely generating; needs manual check)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Not measured (needs PSI)

### Critical Issues (2)
1. **Broken "Services" nav link** — Points to `#` across all pages. Hurts UX and crawlability.
2. **Social media URL mismatches** — LinkedIn, YouTube, and Pinterest footer links all redirect to the same Pinterest URL. Twitter link uses old `twitter.com` domain.

### Medium Issues (4)
1. **About Us page thin content** — Only ~5 short paragraphs; lacks team photos, history, or structured data.
2. **Duplicate Vancouver link** — Canada footer lists Vancouver twice with identical URLs.
3. **Missing dedicated Services nav target** — `/digital-marketing-services/` exists but is not linked in top nav.
4. **Case study placeholders** — On SEO services page, "Law Firm", "Real Estate Portal", and "Ecommerce Store" blocks link to `#`.

### Low Issues (4)
1. Author sitemap stale (lastmod 2026-05-12)
2. Old Twitter domain in footer (`twitter.com` vs `x.com`)
3. Some image alt text generic (client logos)
4. Title tags appeared empty in browser snapshots (verify in source)

### Recommended Fixes (Priority)
1. Fix "Services" nav link to `/digital-marketing-services/`
2. Correct LinkedIn and YouTube footer URLs to actual profiles
3. Enrich `/about-us/` with photos, timeline, and AboutPage schema
4. Fix or remove case study placeholder links
5. Remove duplicate Vancouver footer link
6. Verify schema with Google Rich Results Test
7. Run PageSpeed Insights + Mobile-Friendly Test

---

## tonicphysio.com — 2026-05-31

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK
- **Robots.txt:** ✅ OK
- **Indexation:** ✅ Good (5+ pages indexed)
- **Schema:** ⚠️ Unverified (Yoast likely generating, needs manual check)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Needs PSI test

### Critical Issues (2)
1. **Missing pricing on /fees/** — All fee sections show placeholder "contact us" text. No actual prices.
2. **Broken nav links** — "Products" and "Programs" menu items link to `#` (empty anchors).

### Medium Issues (4)
1. **Thin contact page** — Minimal content, missing hours/map/parking.
2. **Schema unverified** — Cannot confirm JSON-LD presence via automated fetch.
3. **Image alt text** — Needs audit across all pages.
4. **Meta descriptions** — Need verification on homepage.

### Low Issues (3)
1. FAQ schema missing on service pages
2. PriceRange not in LocalBusiness schema
3. Author sitemap stale (lastmod 2026-05-12)

### Recommended Fixes (Priority)
1. Add transparent pricing to /fees/ page
2. Fix or remove broken nav links (Products, Programs)
3. Verify schema with Google's Rich Results Test
4. Enrich /contact/ with hours, map, parking info
5. Run PageSpeed Insights + Mobile-Friendly Test
6. Audit all image alt text

---

## coinsfera.com — 2026-05-14

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK
- **Robots.txt:** ✅ OK
- **Indexation:** ✅ Good (5+ pages indexed)
- **Schema:** ✅ Good (FAQ, Service, FinancialService, and Breadcrumb schemas are all implemented correctly site-wide)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Needs PSI test

### Critical Issues (1)
1. **`/services/usdt/` Redirect Broken** — The `/services/usdt/` URL 301-redirects to a Russian comparison blog post (`/ru/блоги/usdt-против-usdc-что-выбрать/`) instead of loading the English USDT service landing page.

### Medium Issues (2)
1. **Sitemap Coverage Gap** — The `/services/usdt/` URL is missing from the XML sitemap index.
2. **Internal Link Structure** — Homepage internal links mostly point to `/wp-content/` assets. Cross-linking between service pages is thin and needs improvement.

### Low Issues (1)
1. **Blog Freshness** — Needs review of recent blog update schedules to ensure content remains fresh and authoritative.

### Recommended Fixes (Priority)
1. Correct the USDT service redirect rule in WordPress Redirections or Yoast SEO.
2. Add the proper `/services/usdt/` service landing page back to the XML sitemap.
3. Improve internal contextual linking from the homepage and between individual service pages.
4. Run Google PageSpeed Insights for Core Web Vitals.

---

## teammotorcycle.com — 2026-04-25

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK (native auto-generated Shopify sitemap)
- **Robots.txt:** ✅ OK (standard Shopify default)
- **Indexation:** ✅ Good (large catalog indexed, 2,149 products / 430 collections)
- **Schema:** ⚠️ Partial (ProductGroup present on product pages, but missing key pricing and offers schema)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Needs PSI test

### Critical Issues (1)
1. **Missing Price & Availability Schema** — Product pages use `ProductGroup` but are missing the standard `Product` type and `offers` (price, currency, availability) schemas. Rich snippets won't show prices or in-stock status in Google Search.

### Medium Issues (1)
1. **Missing CollectionPage Schema** — Shopify Collection pages only have `BreadcrumbList` and are missing the `CollectionPage` and `ItemList` schemas for products listed within the collection.

### Low Issues (3)
1. **Author/Date refinement in Blog** — Article schema is present but needs author and datePublished improvements.
2. **Missing FAQPage Schema** — No FAQ schema on `/pages/frequently-asked-questions`.
3. **Generic Alt Text** — Product images could benefit from a structured audit of alt attributes.

### Recommended Fixes (Priority)
1. Edit the Shopify theme liquid files to insert the `Product` + `Offer` structured data on product pages.
2. Add `CollectionPage` and `ItemList` schema templates to your collection liquid theme files.
3. Add FAQPage schema to your main Frequently Asked Questions page.
4. Audit product image alt text for descriptive, keyword-aligned descriptions.

---

## Sites in Rotation

| Day | Site | Last Audit | Next Audit |
|-----|------|------------|------------|
| Monday | rankray.com | 2026-06-01 | 2026-06-08 |
| Tuesday | tonicphysio.com | 2026-05-31 | 2026-06-02 |
| Wednesday | coinsfera.com | — | 2026-06-03 |
| Thursday | teammotorcycle.com | — | 2026-06-04 |
| Friday | rankray.com | 2026-06-01 | 2026-06-05 |
| Weekend | tonicphysio.com | 2026-05-31 | 2026-06-07 |

---

*This sheet is updated automatically after each audit.*
