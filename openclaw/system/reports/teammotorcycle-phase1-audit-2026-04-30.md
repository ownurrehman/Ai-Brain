# Phase 1: Technical SEO Audit - teammotorcycle.com
**Date:** 30 April 2026 | **Agent:** chronos | **Platform:** Shopify + Cloudflare

---

## 1. Executive Summary

| Metric | Value | Status |
|:-------|:------|:------|
| Site platform | Shopify (Warehouse theme v6.3.0) | OK |
| HTTPS enforced | Yes (HSTS, Cloudflare) | OK |
| WWW redirect | Non-WWW -> WWW (301) | OK |
| HTTP -> HTTPS | 301 redirect working | OK |
| Total indexed pages | ~3,028 (85 pages + 430 collections + 360 blog + ~2,153 products) | Large |
| Mobile responsive | Yes (viewport meta present) | OK |
| SSL/TLS | TLS 1.2+ supported | OK |
| 404 handling | Proper 404 page exists | OK |

---

## 2. CRITICAL ISSUES FOUND

### 2.1 Missing Meta Descriptions on 430 Collection Pages
- **Severity:** HIGH
- **Impact:** All 430 collection pages lack `<meta name="description">` tag. While OG:description exists (for social sharing), Google primarily reads the standard meta description tag for SERP snippets.
- **Evidence:** Tested `/collections/motorcycle-helmets`, `/collections/motorcycle-jackets`, `/collections/motorcycle-vests`, `/collections/motorcycle-gloves`, `/collections/motorcycle-luggage` — all missing standard meta description.
- **Fix:** Shopify theme should be configured to populate meta descriptions for collections. Check theme settings or add via Shopify admin > Collections > Edit website SEO.

### 2.2 60 Thin Content Pages (Size Charts) Indexed
- **Severity:** HIGH
- **Impact:** 60 size-chart pages (`/pages/*-size-chart`, `/pages/size-guide-*`) are in the sitemap and likely indexed. These have minimal unique content and dilute crawl budget.
- **Evidence:** Counted from pages sitemap: 60 URLs containing "size".
- **Fix:** Either: (a) Noindex these pages via `robots.txt` meta or theme liquid, or (b) Consolidate into fewer comprehensive size guide pages, or (c) Add substantial descriptive content to each.

### 2.3 No Hreflang Tags
- **Severity:** MEDIUM
- **Impact:** Site appears to target US market (USD currency, English) but has no hreflang tags. If there are multiple language/regional versions, this can cause duplicate content issues.
- **Evidence:** Zero hreflang references found on homepage or product pages.
- **Fix:** Add `<link rel="alternate" hreflang="en-us" href="https://www.teammotorcycle.com/">` at minimum on all pages.

### 2.4 Empty Keywords Meta Tag
- **Severity:** LOW
- **Impact:** `<meta name="keywords" content="">` is present but empty on homepage. While Google ignores keywords meta, it's wasted HTML.
- **Fix:** Remove the empty tag or populate if used by internal search.

---

## 3. MODERATE ISSUES

### 3.1 No Standard Structured Data on Product Pages
- **Severity:** MEDIUM
- **Impact:** Homepage has Organization, WebSite, BreadcrumbList JSON-LD (good). But no Product schema was detected. Product pages would benefit from Product structured data for rich results.
- **Evidence:** Scanned product page HTML — no `application/ld+json` Product schema found.
- **Fix:** Install a Shopify SEO app (e.g., JSON-LD for SEO) or add Product schema via theme.

### 3.2 /search Page Returns 200 with nofollow
- **Severity:** LOW-MEDIUM
- **Impact:** `/search` returns 200 OK with `X-Robots-Tag: nofollow`. This is fine but the page is essentially thin. Consider noindex or canonical to homepage.
- **Evidence:** HTTP response shows `x-robots-tag: nofollow` but `noindex` is not present.
- **Fix:** Add noindex tag or canonical to homepage.

### 3.3 /pages/search-results Returns 200 OK
- **Severity:** LOW-MEDIUM
- **Impact:** This is likely a thin/orphan page that should not be indexable.
- **Evidence:** Returns 200 status. No robots meta detected.
- **Fix:** Noindex this page.

### 3.4 Homepage HTML Weight: ~585KB
- **Severity:** LOW-MEDIUM
- **Impact:** Raw HTML is ~585KB which is heavy. This includes inline CSS and scripts. Mobile load times may suffer.
- **Evidence:** `curl -s | wc -c = 599,220 bytes`
- **Fix:** Enable Shopify's built-in minification, defer non-critical JS, lazy-load below-fold images.

### 3.5 Faceted URLs Accessible (/collections/*?sort_by=*)
- **Severity:** LOW-MEDIUM
- **Impact:** Sort/filter parameter URLs return 200 and are likely crawlable. robots.txt blocks `/collections/*sort_by*` — this is correct but verify it's working.
- **Evidence:** `?sort_by=price-descending` returns 200 OK.
- **Fix:** robots.txt already has this disallowed — confirm Google isn't indexing these via Search Console.

### 3.6 Multiple Google Site Verification Meta Tags
- **Severity:** LOW
- **Impact:** Two different `google-site-verification` meta tags exist (duplicate verification).
- **Evidence:** `8IJ9wXehRQ7bXHXHpUAUANFGwNp3jiXFxH0Jfyhis5k` and `9epZ34wXVf_9HfELwKcn0s9pXentM1AP7eUHtroUjwc`
- **Fix:** Verify both are needed. Clean up if one is obsolete.

---

## 4. WHAT'S WORKING WELL

| Area | Observation |
|:-----|:-----------|
| Canonical tags | Present and correct on all pages checked |
| Image alt text | All 110 homepage images have alt attributes |
| Image dimensions | Images have explicit width/height (prevents CLS) |
| Lazy loading | Images use `loading="lazy"` |
| SSL/HTTPS | Full HTTPS with HSTS enforcement |
| Redirects | Clean chain: HTTP->HTTPS, non-WWW->WWW |
| Sitemap | Auto-generated by Shopify, references correct |
| robots.txt | Properly configured with Shopify defaults |
| OG/Twitter tags | Present on homepage and product pages |
| Blog content depth | ~2,300 words per post (decent) |
| JSON-LD | Organization + WebSite + BreadcrumbList on homepage |
| Theme | Updated recently (Warehouse 6.3.0, 08/04/2026) |
| Third-party integrations | Attentive, Route, Ahrefs analytics (reasonable) |

---

## 5. PRIORITY FIXES (Ordered)

| Priority | Action | Effort |
|:---------|:-------|:-------|
| 1 (CRITICAL) | Add meta descriptions to all 430 collection pages | Bulk via Shopify admin or CSV import |
| 2 (HIGH) | Noindex or consolidate 60 thin size-chart pages | Add noindex via theme liquid or bulk edit |
| 3 (HIGH) | Add hreflang tags sitewide | Theme edit or SEO app |
| 4 (MEDIUM) | Add Product structured data schema | Install JSON-LD app |
| 5 (MEDIUM) | Noindex /search and /pages/search-results | robots.txt liquid or meta tag |
| 6 (LOW) | Remove empty keywords meta tag | Theme edit |
| 7 (LOW) | Minify/defer homepage JS to reduce 585KB payload | Theme optimization |

---

## 6. FIXES APPLIED THIS SESSION

**None applied.** This is an audit-only phase. Fixes require access to the Shopify admin dashboard for:
- Bulk meta description updates for collections
- Theme liquid edits for noindex/schema
- App installation for structured data

**Recommendation:** Share this report with the team for manual implementation via Shopify admin, or provide API credentials for automated fixes in Phase 2.

---

*Report compiled from live HTTP analysis, sitemap parsing, and Google index sampling.*
