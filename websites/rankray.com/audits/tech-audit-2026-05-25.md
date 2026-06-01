# Technical SEO Audit Report: rankray.com

**Date:** 2026-05-25 (Monday)  
**Auditor:** Enigma (Automated)  
**Domain:** https://rankray.com

---

## Executive Summary

Overall Status: **Good** with **2 medium** and **3 low** issues. No critical blocking errors found.

| Category | Status | Notes |
|----------|--------|-------|
| Site Accessibility | Pass | Pages load, no 404s on checked URLs |
| Sitemap | Pass | Valid XML sitemap index present |
| Robots.txt | Pass | Clean, allows all, points to sitemap |
| Indexation | Pass | Site pages indexed and appearing in search |
| Page Speed | Needs Attention | Potential issues (see details) |
| Mobile Usability | Needs Attention | Needs verification |
| Schema / Structured Data | Needs Attention | Needs verification |

---

## Pages Audited

1. **Homepage** - https://rankray.com/
2. **Services Page** - https://rankray.com/digital-marketing-services/
3. **Blog** - https://rankray.com/blog/
4. **Contact** - https://rankray.com/contact/

---

## Findings

### 1. Sitemap & Robots.txt

**Sitemap:** https://rankray.com/sitemap_index.xml
- Status: **200 OK**
- Type: XML Sitemap Index (Yoast SEO generated)
- Contains 5 sitemaps:
  - post-sitemap.xml (lastmod: 2026-05-24)
  - page-sitemap.xml (lastmod: 2026-05-24)
  - location-page-sitemap.xml (lastmod: 2026-05-23)
  - category-sitemap.xml (lastmod: 2026-05-24)
  - author-sitemap.xml (lastmod: 2026-05-12)
- Last updated: May 24, 2026 (fresh)

**Robots.txt:** https://rankray.com/robots.txt
- Status: **200 OK**
- Allows all user-agents
- Sitemap reference: `https://rankray.com/sitemap_index.xml`
- Clean configuration

**Severity:** None - Pass

---

### 2. Indexation Status

Verified via `site:rankray.com` search:

| Indexed Page | Status |
|-------------|--------|
| Homepage | Indexed |
| Locations page | Indexed |
| Contact page | Indexed |
| Sitemap page | Indexed |
| FAQs page | Indexed |

**Severity:** None - Pass

---

### 3. Page Speed (Browser Observation)

**Observation:**
- Homepage loads with visible content but appears to have multiple large images and carousels
- Content renders progressively
- No visible render-blocking detected in browser, but **actual metrics cannot be confirmed without PageSpeed Insights or Lighthouse**

**Recommendations:**
- Run Google PageSpeed Insights on homepage and services page
- Check for image optimization opportunities (WebP format, lazy loading)
- Consider preloading critical resources

**Severity:** Medium - Needs proper measurement

---

### 4. Mobile Usability

**Observation:**
- Browser audit ran in desktop mode
- Site uses responsive navigation pattern
- Forms and CTAs visible on all checked pages
- **Actual mobile usability cannot be verified without mobile viewport testing or Search Console data**

**Recommendations:**
- Verify in Google Search Console for mobile usability errors
- Test key pages with browser mobile emulation
- Ensure tap targets are appropriately sized

**Severity:** Medium - Needs verification

---

### 5. Schema / Structured Data

**Observation:**
- Homepage contains what appears to be an embedded Google Maps iframe for location
- No visible JSON-LD schema markup detected in page source via browser snapshot
- Sitemap is Yoast-generated, which typically includes basic schema
- **Full schema audit requires checking page source for specific markup types**

**Recommendations:**
- Verify Organization schema is present sitewide
- Check for LocalBusiness schema on contact/location pages
- Ensure FAQ schema on FAQs page
- Validate using Google's Rich Results Test

**Severity:** Low - Needs verification

---

### 6. Internal Linking Observations

**Noticed Issues:**
- Footer duplicate: "Vancouver" appears twice under Canada section
- Social media links for LinkedIn and YouTube both point to Pinterest URL (`https://www.pinterest.com/rankray/_created/`) - likely incorrect
- Twitter link points to correct URL
- Multiple location pages linked in footer (good for local SEO)

**Severity:** Low - Social link inaccuracies

---

### 7. Content Observations

**Blog:**
- Active publishing: articles dated May 24, 2026 (very recent)
- Pagination present (7 pages)
- Article topics cover SEO, CRO, content marketing
- Images present with alt text

**Services Page:**
- Contains spelling error: "Digital Marekting" in dropdown option
- Multiple case study references with claimed metrics

**Severity:** Low - Minor content issues

---

## Action Items

| Priority | Task | Owner |
|----------|------|-------|
| Medium | Run PageSpeed Insights and address Core Web Vitals | chronos |
| Medium | Verify mobile usability in Search Console / emulator | chronos |
| Low | Fix LinkedIn and YouTube footer links (currently pointing to Pinterest) | chronos |
| Low | Fix "Digital Marekting" typo in services dropdown | chronos |
| Low | Verify schema markup with Rich Results Test | Enigma |
| Low | Remove duplicate "Vancouver" link in footer Canada section | chronos |

---

## Next Audit

**Scheduled:** Tuesday, 2026-05-26 - tonicphysio.com

---

*Report generated by Enigma automated technical SEO audit system.*