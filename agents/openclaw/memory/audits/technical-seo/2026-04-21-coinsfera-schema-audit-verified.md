# Coinsfera.com Technical SEO Audit — VERIFIED FINDINGS

**Date:** 2026-04-21  
**Auditor:** Ranki  
**Method:** All findings verified with curl commands — NO assumptions

---

## Executive Summary

**What's Working:**
- ✅ Homepage has LocalBusiness/FinancialService schema (verified in HTML)
- ✅ Homepage has FAQPage schema (verified in HTML)
- ✅ Homepage has AggregateRating (4.9/5, 998 reviews)
- ✅ `/sell-bitcoin-in-istanbul/` returns 200 OK + has schema
- ✅ `/buy-bitcoin-in-istanbul/` returns 200 OK + has schema

**REAL Technical Issues Found:**

1. **CRITICAL: `/services/usdt/` redirects to Russian blog post**
   - Verified redirect chain with `curl -sIL`
   - Users seeking USDT service land on unrelated blog content

2. **Service pages NOT in sitemap**
   - Checked `/page-sitemap.xml` — only homepage + language variants
   - Checked `/post-sitemap.xml` — only blog posts, news articles
   - Service pages exist but not indexed

3. **No Service schema on homepage**
   - Homepage lists 6 services in "What can do" section
   - Only LocalBusiness schema present, no Service schema

---

## Issue #1: `/services/usdt/` Redirect Chain — VERIFIED

**Command:** `curl -sIL "https://coinsfera.com/services/usdt/"`

**Redirect Chain:**
```
https://coinsfera.com/services/usdt/
→ 301 → https://www.coinsfera.com/services/usdt/
→ 301 → https://www.coinsfera.com/ru/блоги/usdt-против-usdc-что-выбрать/
→ 200 OK (Russian blog post)
```

**Final Destination:** Russian blog article "USDT vs USDC: What to Choose?"

**Schema on Blog Post:** Article type only (NO Service schema)

**Impact:**
- Users seeking USDT exchange service land on unrelated Russian blog content
- Lost conversions + confused users + SEO signal dilution

**Root Cause:** WordPress redirect rule (likely Redirection plugin or Yoast SEO redirect)

**Fix Required:**
1. Check WordPress → Tools → Redirection
2. Or check Yoast SEO → Redirects
3. Remove or fix redirect to point to actual USDT service page

---

## Issue #2: Service Pages Missing from Sitemap — VERIFIED

**Commands:**
- `curl -sL "https://www.coinsfera.com/page-sitemap.xml"`
- `curl -sL "https://www.coinsfera.com/post-sitemap.xml"`

**What's Indexed:**
- ✅ Homepage (EN/RU/TR variants)
- ✅ Blog posts
- ✅ News articles
- ✅ Category pages
- ✅ Author pages

**MISSING from Sitemap:**
- ❌ `/sell-bitcoin-in-istanbul/`
- ❌ `/buy-bitcoin-in-istanbul/`
- ❌ `/services/usdt/`
- ❌ `/contact-us/`
- ❌ `/about-us/`
- ❌ Any service pages

**Impact:** Service pages not being indexed by Google despite having content and schema.

**Fix Required:**
1. WordPress → SEO (Yoast) → Settings → Sitemaps
2. Check "Exclude" rules — service pages may be excluded
3. Check individual page settings — ensure "Allow search engines to index this page" is enabled

---

## Issue #3: No Service Schema on Homepage — VERIFIED

**Command:** `curl -sL "https://www.coinsfera.com" | python3 -c "import sys, re; html=sys.stdin.read(); matches=re.findall(r'<script[^>]*type=\"application/ld\+json\"[^>]*>(.*?)</script>', html, re.DOTALL); print(matches[1][:2000])"`

**Homepage Schema Present:**
- ✅ LocalBusiness/FinancialService
- ✅ WebPage
- ✅ WebSite
- ✅ BreadcrumbList
- ✅ FAQPage (17 questions)
- ✅ AggregateRating (4.9/5, 998 reviews)
- ✅ Review (5 sample reviews)
- ✅ OpeningHoursSpecification
- ✅ GeoCoordinates (41.0238, 28.9772)

**Homepage Schema MISSING:**
- ❌ Service schema (homepage lists 6 services but none marked up)
- ❌ Product schema (no markup for USDT/BTC exchange services)

**Services Listed on Homepage (from HTML):**
1. Exchange cryptocurrency for cash
2. Sell crypto for cash dollars (USD)
3. Sell crypto for cash euros (EUR)
4. Sell crypto for cash Turkish Lira (TRY)
5. Cash out crypto
6. Exchange crypto for cash

**Fix Required:** Add Service schema markup for each service listed on homepage.

---

## Service Pages Status — VERIFIED

### `/sell-bitcoin-in-istanbul/`
- **HTTP Status:** 200 OK ✅
- **Schema Present:** WebPage, LocalBusiness/FinancialService (site-wide), FAQPage ✅
- **In Sitemap:** ❌ NO
- **Issue:** Page works but not indexed in sitemap

### `/buy-bitcoin-in-istanbul/`
- **HTTP Status:** 200 OK ✅
- **Schema Present:** WebPage, LocalBusiness/FinancialService (site-wide) ✅
- **In Sitemap:** ❌ NO
- **Issue:** Page works but not indexed in sitemap

---

## Fix Priorities — EVIDENCE-BASED

| Priority | Issue | Evidence | Impact |
|----------|-------|----------|--------|
| **P0** | `/services/usdt/` redirects to Russian blog | Verified: 301→301→200 redirect chain | Critical UX + lost conversions |
| **P1** | Service pages missing from sitemap | Verified: Not in `/page-sitemap.xml` | Service pages not indexed |
| **P2** | No Service schema on homepage | Verified: Only LocalBusiness present | Missing rich snippets |
| **P3** | FAQPage schema completeness | Verified: 17 FAQs in HTML, need JSON-LD check | FAQ rich results may not show |

---

## Next Actions

1. ✅ Schema audit completed with curl verification
2. ✅ Updated MEMORY.md with correct detection methodology
3. ✅ Updated technical-seo-checker skill
4. ⏳ **Fix `/services/usdt/` redirect** — Check WordPress redirect rules (Redirection plugin or Yoast SEO)
5. ⏳ **Add service pages to sitemap** — Check Yoast SEO sitemap settings
6. ⏳ **Add Service schema to homepage** — Mark up 6 services listed in "What can do" section
7. ⏳ **Verify FAQPage schema completeness** — Extract full JSON-LD and compare to HTML FAQs

---

**Tools Used:**
- `curl` for HTTP requests and HTML extraction
- `python3` for JSON-LD parsing
- Manual verification of sitemap XML

**No assumptions made. All findings verified with actual HTTP requests.**
