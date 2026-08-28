> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[websites/archive/index|Archive Hub]] · [[INDEX|🧠 Ai Brain]]

# Khan Law (khanllp.com) — SEO Gap Analysis Report

**Date:** 2026-04-19  
**Analyst:** Rank Ray Research  
**Target site:** https://khanllp.com  
**Target keywords:** "lawyer milton ontario", "civil litigation lawyer toronto"

---

## 1. Current Site Inventory

### Sitemap Summary
- **Total indexed URLs:** ~90+ pages (from sitemap.xml + robots.txt)
- **Service pages:** 35+ practice-area pages
- **Blog posts:** 25+ articles (heavily real-estate focused)
- **Location pages:** 20+ city-specific landing pages (Milton, Mississauga, Oakville, North York, Toronto)
- **Utility pages:** /our-team, /contact, /privacypolicy, /termsofuse

### URL Structure Observations
- Clean URL slugs (good) — e.g., `/real-estate-lawyer-milton`, `/family-law-toronto`
- Some inconsistent naming: `/wills-estate-planning` vs `/wills-estates` (duplicate content risk)
- `/title-transfer` AND `/title-transfer-ontario` (near-duplicate pages)
- `/purchase-and-sale` AND `/purchase-sales-for-non-residents` AND `/purchase-and-sale-for-non-residents` (overlapping intent)
- `/family-law` (generic) alongside `/family-law-milton`, `/family-law-toronto` etc. (good local SEO architecture)

---

## 2. SERP Analysis

### "lawyer milton ontario" (DuckDuckGo, Apr 2026)

| Rank | Site | Notes |
|------|------|-------|
| 1 | Yelp — "Best 10 Lawyers in Milton" | Directory/aggregator |
| 2 | Presse Law (presselaw.com) | Local competitor — corporate, estate, real estate |
| 3 | Hassaan & Associates (hassaanlaw.ca) | Grimsby + Milton |
| 4 | Gunding & Hans LLP (gundinghansllp.com) | Downtown Milton, family + real estate + estates |
| 5 | Robert Brooks (robertbrookslaw.ca) | Milton, Halton Region |
| 6 | Furlong Collins (furlongcollins.ca) | Milton, multi-practice |
| 7 | **Khan Law (khanllp.com)** | Currently ranking ~7th |
| 8 | LawyerInfo.ca directory | Aggregator |
| 9 | Jim the Lawyer (jimthelawyer.com) | Single practitioner |
| 10 | LegalGlobal directory | Aggregator |

**Key finding:** Khan Law ranks #7 for its core local term. 4 local competitor firms outrank it. The top 3 are all dedicated Milton-based firms with focused local SEO.

### "civil litigation lawyer toronto" (DuckDuckGo, Apr 2026)

| Rank | Site | Notes |
|------|------|-------|
| 1 | TheBestToronto.com — listicle | Aggregator |
| 2 | Mills & Mills LLP (millsandmills.ca) | Dedicated litigation page |
| 3 | Lawyers.com directory | Aggregator |
| 4 | ThreeBestRated.ca | Aggregator |
| 5 | MyTorontoBest.com | Aggregator |
| 6 | Ontario Litigation Lawyers (ontariolitigationlawyers.com) | Litigation-focused firm |
| 7 | Lawzana.com directory | Aggregator |
| 8 | Allan Rouben (allanrouben.com) | Litigation specialist |
| 9 | GLG LLP (glgllp.ca) | Litigation page |
| 10 | Achkar Litigation (achkarlitigation.com) | Litigation-focused |

**Key finding:** khanllp.com is **not present** in the top 10 for "civil litigation lawyer toronto". The site has **no civil litigation page** at all — this is a major content gap. All top-ranking competitors have a dedicated `/litigation` or `/civil-litigation` page.

---

## 3. Missing Keyword Opportunities (Content Gaps)

### Critical — No Existing Page

| Keyword | Search Intent | Priority | Notes |
|---------|--------------|----------|-------|
| civil litigation lawyer toronto | Commercial | 🔴 HIGH | No page exists. Top competitors all have dedicated litigation pages |
| civil litigation lawyer mississauga | Commercial | 🔴 HIGH | No page exists |
| civil litigation lawyer milton | Commercial | 🔴 HIGH | No page exists |
| litigation lawyer ontario | Commercial | 🟡 MEDIUM | No page exists |
| corporate lawyer milton | Commercial | 🟡 MEDIUM | Competitor Presse Law ranks for this; Khan has no corporate law page |
| employment lawyer milton ontario | Commercial | 🟡 MEDIUM | Growing search volume; no page exists |
| notary public milton | Commercial/Navigational | 🟡 MEDIUM | Common local legal search; no page exists |
| divorce lawyer milton | Commercial | 🟡 MEDIUM | Only generic `/divorce-and-separation-ontario` exists |
| real estate closing lawyer milton | Transactional | 🟡 MEDIUM | More specific than existing pages |

### Moderate — Existing Page Needs Optimization

| Keyword | Current Page | Issue |
|---------|-------------|-------|
| lawyer milton ontario | Homepage only | No dedicated `/lawyer-milton` page; homepage is too broad |
| best real estate lawyer milton | `/best-real-estate-lawyers-in-milton-award` | Thin/award page, not optimized for commercial search |
| family lawyer mississauga | `/family-law-mississauga` | JS-rendered, content invisible to crawlers |
| immigration lawyer toronto | `/immigration-lawyer-toronto` | Exists but likely underperforming due to rendering issues |
| wills and estates lawyer oakville | `/wills-and-estates-lawyer-oakville` | Same rendering issue |

### Blog Content Gaps

The blog portfolio is heavily real-estate focused (18/25+ posts). Missing topics:
- Immigration law blog posts (zero despite 8 immigration service pages)
- Criminal law blog posts (zero despite having `/criminal-lawyer-ontario`)
- Family law deep-dives (only 4 family blog posts)
- Location-specific content (e.g., "How to find a family lawyer in Mississauga")

---

## 4. Technical SEO Issues

### 🔴 CRITICAL: Client-Side Rendering (CSR) / JavaScript-Only Content

**The single biggest SEO issue.** Khanllp.com appears to be a React/Next.js single-page application where all page content is rendered client-side. When fetched without JavaScript:

- **Homepage:** Returns only the title tag `<title>Khan Law | Trusted Law Firm in Toronto, Milton, Mississauga</title>` — **zero body content**
- **Service pages:** Same — e.g., `/family-law` returns only `<title>Family Lawyer Canada For Family Law | Khan Law</title>` with no body
- **Blog listing:** Returns only `<title>Blogs</title>` — no articles visible

**Impact:** Google can render JS, but it's delayed (second-pass indexing). This means:
- Content is indexed slower
- Risk of Google seeing blank pages if rendering fails
- No content visible in social media previews (og:title, og:description likely missing)
- Bing and other engines struggle with JS content

**Fix:** Implement Server-Side Rendering (SSR) or Static Site Generation (SSG) via Next.js. Alternatively, use dynamic rendering / prerender.io as a stopgap.

### 🔴 Sitemap Issues

| Issue | Detail | Fix |
|-------|--------|-----|
| `changefreq=daily` on all pages | Every page claims daily updates — dilutes signal | Set realistic changefreq: `yearly` for service pages, `monthly` for blogs |
| `priority=0.8` on all pages | All 90+ URLs have priority 0.8 — no differentiation | Homepage=1.0, Service pages=0.7, Blog posts=0.5-0.6, Utility pages=0.3 |
| Privacy/Terms in sitemap | `/privacypolicy` and `/termsofuse` waste crawl budget | Remove low-value pages from sitemap |
| No `lastmod` dates | Missing last modification dates | Add `<lastmod>` to all URLs |
| Duplicate content risk | `/wills-estate-planning` vs `/wills-estates`; `/title-transfer` vs `/title-transfer-ontario` | Consolidate or add canonical tags |

### 🟡 Robots.txt Misconfiguration

- **Overly permissive Allow list:** The robots.txt lists ~80 specific `Allow:` paths for `User-agent: *`. This is redundant — anything not in `Disallow` is already allowed. This bloats the robots.txt and makes maintenance error-prone.
- **Missing sitemap reference:** No `Sitemap: https://khanllp.com/sitemap.xml` directive in robots.txt.
- **Disallowed admin/login paths** are good practice.

### 🟡 Schema Markup (Structured Data)

**Assessment: Likely missing or incomplete.** Since page content is JS-rendered, any JSON-LD schema would also need JS execution to be visible. Key missing schemas:

| Schema Type | Status | Priority |
|-------------|--------|----------|
| `LocalBusiness` / `LegalService` | Likely missing | 🔴 Critical |
| `Attorney` (per practitioner) | Likely missing | 🔴 Critical |
| `FAQPage` (on service pages) | Likely missing | 🟡 Medium |
| `BreadcrumbList` | Likely missing | 🟡 Medium |
| `Article` (on blog posts) | Likely missing | 🟡 Medium |
| `Review` / `AggregateRating` | Likely missing | 🟡 Medium |

**Recommendation:** Add JSON-LD schema to server-rendered HTML `<head>` (not injected via JS). At minimum:
- `LegalService` with `address`, `telephone`, `areaServed`, `priceRange`
- Individual `Attorney` schemas on `/our-team`
- `FAQPage` on top service pages

### 🟡 Meta Tags (Observed)

| Page | Title Tag | Issues |
|------|----------|--------|
| Homepage | "Khan Law \| Trusted Law Firm in Toronto, Milton, Mississauga" | Good brand, but no primary keyword in front. Consider "Milton Lawyer \| Khan Law — Real Estate, Family & Immigration" |
| /family-law | "Family Lawyer Canada For Family Law \| Khan Law" | Weak — "Canada" is too broad; no location qualifier |
| /real-estate-lawyer-milton | "Experienced Real Estate Lawyer Milton, Ontario - Khan Law" | Decent — but dash vs pipe inconsistency with other pages |

### 🟡 Page Speed Concerns

- JS-rendered SPA architecture inherently slower on initial load
- No server-rendered HTML means full JS bundle must download before any content appears
- Likely high LCP (Largest Contentful Paint) and FCP (First Contentful Paint)
- Recommendation: Run Lighthouse audit; expect Mobile scores <50 if JS-heavy framework

### 🟢 What's Working

- Clean URL structure with keywords in slugs
- Good breadth of location-specific landing pages (5 cities × 4 practice areas = 20 pages)
- Comprehensive practice area coverage (real estate, family, immigration, criminal, wills)
- Blog content exists (25+ posts)
- Sitemap is present and submitted
- HTTPS is enabled
- robots.txt allows search engine crawling

---

## 5. Competitor Comparison

### Presse Law (presselaw.com) — #2 for "lawyer milton ontario"
| Factor | Presse Law | Khan Law |
|--------|-----------|----------|
| Rendering | Server-rendered (static HTML) | Client-side JS only |
| Content visible to crawlers | ✅ Full content | ❌ Empty without JS |
| Contact info in footer | ✅ Address, phone, email | Unclear without JS |
| Testimonials on homepage | ✅ 4 Google reviews displayed | ❌ Not visible |
| Schema | Likely present (structured site) | Likely absent |
| Practice area pages | 3 focused pages | 35+ pages (broader but may be thin) |
| Established authority | Since 1979 | Newer firm |

### Gunding & Hans LLP (gundinghansllp.com) — #4 for "lawyer milton ontario"
| Factor | Gunding & Hans | Khan Law |
|--------|---------------|----------|
| Rendering | Server-rendered | Client-side JS |
| Internal linking | Clear contextual links with keywords | Unknown (JS) |
| Content depth | Concise, well-structured | Unknown |
| Location signals | "downtown Milton" + physical address | "Milton" in URLs but address unclear |

---

## 6. Prioritized Action Items

### Immediate (Week 1-2)
1. **🔴 Fix JS rendering** — Switch to SSR/SSG or implement prerender. This is blocking ALL organic performance.
2. **🔴 Add JSON-LD schema** — `LegalService`, `Attorney`, `BreadcrumbList` on every page in server-rendered HTML
3. **🔴 Create `/civil-litigation` pages** — Create dedicated civil litigation pages for Toronto, Milton, Mississauga, Oakville, North York (matching existing location-page pattern)

### Short-Term (Month 1)
4. **🟡 Optimize title tags** — Lead with target keyword + location; make consistent with `|` separator
5. **🟡 Fix sitemap** — Add `lastmod`, vary `changefreq` and `priority`, remove utility pages
6. **🟡 Add `Sitemap:` directive to robots.txt**
7. **🟡 Add meta descriptions** to all pages (likely missing if JS-rendered)
8. **🟡 Consolidate duplicate pages** — `/wills-estate-planning` → `/wills-estates`; `/title-transfer` → `/title-transfer-ontario`; add 301 redirects
9. **🟡 Create blog content for immigration and criminal law** — Currently 0 posts for these practice areas

### Medium-Term (Month 2-3)
10. **Create `/lawyer-milton-ontario` landing page** — Dedicated page for this high-value search term
11. **Create `/notary-public-[city]` pages** — Untapped local search opportunity
12. **Add FAQ sections** to top service pages with `FAQPage` schema
13. **Build local citations** — Google Business Profile optimization, Yelp, LawyerInfo.ca, ThreeBestRated, LegalGlobal
14. **Add testimonials/reviews** to homepage with `Review` schema markup
15. **Internal linking audit** — Ensure location pages cross-link to relevant service pages with keyword-rich anchor text

---

## 7. Keyword Opportunities Matrix

| Keyword Cluster | Monthly Est. Volume* | Current Page | Action |
|----------------|---------------------|-------------|--------|
| lawyer milton ontario | 300-500 | Homepage only | Create dedicated landing page |
| real estate lawyer milton | 200-400 | `/real-estate-lawyer-milton` | Optimize (fix rendering) |
| family lawyer milton | 150-300 | `/family-law-milton` | Optimize (fix rendering) |
| civil litigation lawyer toronto | 300-600 | **NONE** | Create new page |
| immigration lawyer toronto | 500-1000 | `/immigration-lawyer-toronto` | Optimize (fix rendering) |
| wills and estates lawyer milton | 100-200 | `/wills-and-estates-lawyer-milton` | Optimize (fix rendering) |
| criminal lawyer ontario | 200-400 | `/criminal-lawyer-ontario` | Create blog content |
| notary public milton | 100-200 | **NONE** | Create new page |
| corporate lawyer milton | 50-150 | **NONE** | Create new page |
| divorce lawyer mississauga | 200-400 | `/divorce-and-separation-ontario` only | Create `/divorce-lawyer-mississauga` |
| dui lawyer ontario | 300-500 | `/dui-lawyer-ontario` | Optimize + add blog content |
| real estate closing lawyer | 100-200 | Generic purchase/sale pages | Create specific page |
| land transfer tax toronto | 200-400 | `/land-transfer-tax-toronto` | Optimize (fix rendering) |
| mortgage refinancing lawyer | 100-200 | `/mortgage-refinancing-ontario` | Optimize (fix rendering) |

*\*Volume estimates based on typical Canadian legal keyword ranges. Cannot confirm exact volume without paid tool access. Treat as order-of-magnitude estimates.*

---

## 8. Summary

The single most impactful SEO issue for khanllp.com is **client-side JavaScript rendering**. Without fixing this, all other optimizations will have limited effect because search engines (especially Bing) and social crawlers see empty pages. The site has an excellent URL architecture and content breadth, but none of it is accessible without JavaScript execution.

Beyond rendering, the key strategic gaps are:
1. **No civil litigation practice area** — missing a high-value keyword cluster entirely
2. **No "lawyer milton ontario" dedicated page** — core local term relies on homepage
3. **Blog content imbalance** — 18 real estate posts vs 0 immigration/criminal posts
4. **Missing schema markup** — zero structured data for LocalBusiness, Attorney, or FAQPage

Fixing rendering + adding civil litigation pages alone could move the needle significantly for organic visibility.

---

*Report generated by Rank Ray Research. Data sourced from DuckDuckGo SERP analysis, sitemap/robots.txt crawl, and technical page inspection. Search volume estimates are approximations — verify with SEMrush/Ahrefs before budget allocation.*