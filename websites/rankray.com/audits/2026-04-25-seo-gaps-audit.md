> **Parent Site:** [[websites/rankray.com/index|🌐 rankray.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Rank Ray SEO Gaps Audit — 2026-04-25

## Site Structure

**Total indexed URLs: ~82**
- Pages: 19 (homepage + 6 utility + ~12 service sub-pages + 1 real estate SEO)
- Location pages: 26 (SEO agency + Digital marketing agency variants)
- Blog posts: ~33 (last updated Oct-Dec 2024)

All sitemap URLs return 200.

---

## GAP 1: Schema Markup (HIGH)

| Page Type | Current Schema | Missing |
|-----------|---------------|---------|
| Homepage | WebSite + Organization + SiteNavigationElement (Yoast) | Service schema |
| Service pages (12+) | NONE — zero JSON-LD on every sub-page | Service, LocalBusiness, BreadcrumbList |
| Blog posts (~33) | NONE — zero JSON-LD | Article/BlogPosting |
| Location pages (26) | NONE — zero JSON-LD | LocalBusiness |
| FAQ page | NONE | FAQPage |

**Impact:** Internal pages completely invisible to Google rich results. Competitors likely have richer schema coverage. Location pages not signaling LocalBusiness to Google.

**Fix:** Enable Yoast schema for all post types. Add Service schema to service pages. Add LocalBusiness to location pages. Add FAQPage to /faqs/.

---

## GAP 2: Content Freshness (HIGH)

- Blog: Dead since Oct 2024 — 5.5 months of no new content.
- Service pages: Last batch-updated Apr 2, 2026 — content is generic.
- Location pages: Bulk-added Apr 18, 2026 — no unique city-specific content.

**Impact:** Google freshness signal degrading. No topical authority expansion.

**Fix:** Minimum 2 blog posts/month. Add unique city content to location pages.

---

## GAP 3: Internal Linking (MEDIUM)

- No evidence of blog posts linking to location pages.
- No location page linking to service pages.
- Cross-linking between pages is likely homepage-only.

**Impact:** Link equity trapped on homepage. Location pages orphaned from content hub.

**Fix:** Add contextual links from blog to location pages. Link location pages to relevant service pages.

---

## GAP 4: Service Page URL (LOW)

- `/digital-marketing-services/seo-services/` returns 404
- Actual path: `/digital-marketing-services/search-engine-optimization-seo/`
- Could be a broken internal link somewhere.

---

## Priority Actions

1. **P0** — Enable Yoast Article/Breadcrumb schema on all post types
2. **P0** — Add Service schema to service pages
3. **P0** — Create 1 new blog post this week
4. **P1** — Add LocalBusiness schema to top 5 location pages
5. **P1** — Add FAQPage schema to /faqs/
6. **P2** — Cross-link blog posts to location pages
