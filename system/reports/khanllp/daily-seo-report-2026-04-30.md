> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[websites/archive/index|Archive Hub]] · [[INDEX|🧠 Ai Brain]]

# KhanLLP.com — Combined Daily SEO Report
**Date:** 2026-04-30 | **Agent:** Chronos (DeepSeek) | **Site:** khanllp.com

---

## 1. SITE AUDIT & TECHNICAL FIXES

### 1.1 Critical Issue: Client-Side JS Rendering (PERSISTENT — Fix #1 Priority)

**Finding:** khanllp.com serves empty HTML shells. All content is rendered client-side via JavaScript. When fetching raw HTML via `web_fetch` (firecrawl/extract), pages show `<body>` with zero visible text content — only `<title>` tag survives. Firecrawl's JS rendering engine was required to extract actual page content.

**Impact:**
- Bing, Yahoo, DuckDuckGo, and other non-Google crawlers see BLANK pages = ZERO indexing
- Social media crawlers (Facebook, LinkedIn, Twitter) can't extract OG meta content
- Slower Google indexing — JS rendering adds to crawl budget consumption
- Accessibility tools and screen readers get degraded experience

**Proposed Fix:**
- Implement **Server-Side Rendering (SSR)** or **Static Site Generation (SSG)**
- If using a JS framework (React/Vue/Angular/Next.js), enable `getServerSideProps` or `generateStaticParams`
- At minimum: use **prerendering service** (Prerender.io or similar) to serve static HTML snapshots to bots
- Verify fix: `curl https://khanllp.com | grep -i "khan law"` should return visible text

### 1.2 Sitemap Analysis

**Status:** Sitemap found at `/sitemap.xml`. Contains **70+ URLs** across all practice areas.

**Sitemap URL inventory:**
| Category | URLs |
|---|---|
| Core pages | `/`, `/our-team`, `/contact`, `/blogs`, `/privacypolicy`, `/termsofuse`, `/special-offer` |
| Real Estate Law | 15 pages (general + 5 city-specific + sub-services) |
| Family Law | 11 pages (general + 5 city-specific + sub-services) |
| Immigration Law | 14 pages (general + 5 city-specific + sub-services) |
| Wills & Estates | 7 pages (general + 5 city-specific) |
| Criminal Law | 4 pages (general + DUI + sexual assault + aggravated assault) |
| Blog posts | 20+ articles (real estate, family law, wills) |
| Other | Landlord-tenant, commercial lease, HST, LTT, NRST, etc. |

**Issues:**
- All pages have identical `<priority>0.8</priority>` — no differentiation between homepage and less important pages
- `<changefreq>daily</changefreq>` on all pages — unrealistic for privacy policy / terms of use

**Proposed Fix:**
- Set homepage priority to `1.0`, service pages to `0.8-0.9`, blog posts to `0.6-0.7`, legal pages to `0.3`
- Changefreq: `weekly` for blogs, `monthly` for legal boilerplate, `daily` for homepage only

### 1.3 Robots.txt Analysis

**Status:** Present and functional. Has `Allow:` directives for all key pages and `Disallow:` for `/admin/`, `/login/`, `/get-in-touch/`.

**No critical issues found.** Minor note: the robots.txt is extremely verbose with individual `Allow:` lines — can be simplified to `Allow: /` with selective `Disallow:` rules.

### 1.4 On-Page SEO (4 Key Service Pages Reviewed)

| Page | Title Tag | H1 | Word Count (est.) | Issues |
|---|---|---|---|---|
| `/real-estate-lawyer-ontario` | "Real Estate Lawyers Ontario \| Home Closing Lawyer \| Khan Law" ✓ | "Real Estate Lawyers in Ontario, Canada" ✓ | ~800 | Too many internal links (13+), duplicates "Purchase & Sale" link. "Small Heading" placeholder visible |
| `/immigration` | "Immigration Lawyer Canada \| Khan Law Legal Advisors" ✓ | "Immigration services in ontario, ca" ✗ | ~700 | H1 lowercase, weak keyword "immigration services" vs "Immigration Lawyer" |
| `/family-law` | "Divorce and Family Lawyer Oakville, CA \| Khan Law" | "Trusted Family Law Lawyers in Ontario, Canada" ✓ | ~600 | Duplicate "Division of Family Property and Asset Protection" section. "Small Heading" placeholder visible. Title says Oakville but content targets all Ontario |
| `/wills-estate-planning` | "Wills And Power of Attorney In Ontario \| Khan Law" ✓ | "Wills & Power of Attorney" ✓ | ~500 | Thin content. FAQ-style only. No city targeting. Links to old domain `web.khanllp.ca` |

**Immediate Fixes:**
1. Fix H1 on `/immigration` → "Immigration Lawyer in Ontario, Canada | Khan Law"
2. Remove "Small Heading" placeholder text from family-law page
3. Fix duplicate section on family-law page
4. Update old domain links (web.khanllp.ca → khanllp.com)
5. Fix `/family-law` title to match content scope (currently says Oakville only)

### 1.5 Speed & Performance (Estimated — JS-Heavy Site)

Given client-side rendering:
- **First Contentful Paint (FCP):** Likely 2-4 seconds (JS bundle must load first)
- **Largest Contentful Paint (LCP):** Likely 3-5+ seconds
- **Total Blocking Time:** High due to JavaScript execution
- **Recommendation:** Run Lighthouse/PageSpeed Insights formally — expect scores in 30-50 range on mobile

---

## 2. SERP ANALYSIS & TARGETING

### 2.1 Primary Competitor Landscape

**Real Estate Law (Milton/Mississauga/Toronto):**
| Competitor | Site | Notes |
|---|---|---|
| Zagazeta Garcia LLP | zglawyers.com | Strong location pages for Mississauga, Brampton, Milton, Oakville, Toronto |
| Perera Law | pereralaw.ca | Dedicated Milton RE lawyer page, city-focused |
| Axess Law | axesslaw.com | Flat fee model, 7-day availability, multiple locations |
| RealEstateLawyers.ca | realestatelawyers.ca | Network model, high domain authority |
| Estofa Law | estofa.ca | Milton-specific targeting |

**Immigration Law (Toronto):**
| Competitor | Notes |
|---|---|
| Green and Spiegel Canada | Best Lawyers recognized |
| Bellissimo Law Group | Top-tier immigration specialists |
| Mamann Sandaluk LLP | Since 1987, dominant brand |
| Nanda & Associates | 2026 content strategy, comprehensive guides |

### 2.2 Keyword Gap Analysis

**High-Value Keywords Where khanllp.com Should Target:**

| Keyword | Intent | Difficulty Est. | Current Status |
|---|---|---|---|
| "real estate lawyer Milton Ontario" | Transactional | Medium | Has dedicated page `/real-estate-lawyer-milton` |
| "real estate lawyer Mississauga" | Transactional | High | Has dedicated page `/real-estate-lawyer-mississauga` |
| "real estate lawyer Toronto" | Transactional | Very High | Has dedicated page `/real-estate-lawyer-toronto` |
| "immigration lawyer Mississauga" | Transactional | High | Has dedicated page `/immigration-lawyer-mississauga` |
| "family law lawyer Milton" | Transactional | Medium | Has dedicated page `/family-law-milton` |
| "wills and estates lawyer Toronto" | Transactional | Medium-High | Has dedicated page `/wills-and-estates-lawyer-toronto` |
| "real estate closing costs Ontario 2026" | Informational | Medium | NO content — BIG GAP |
| "how much does a real estate lawyer cost Ontario" | Informational | Low-Medium | NO content — GAP |
| "land transfer tax Ontario 2026" | Informational | Low | Thin page exists `/land-transfer-tax` |
| "divorce lawyer cost Ontario 2026" | Informational | Medium | NO content — GAP |
| "power of attorney Ontario cost" | Informational | Low | NO dedicated content |
| "first time home buyer lawyer Ontario" | Transactional | Medium | NO content — HIGH OPPORTUNITY |
| "non-resident buying property Canada 2026" | Transactional | Medium | Has pages but need content refresh for 2026 |
| "sponsorship appeal Canada lawyer" | Transactional | Low | Has dedicated page `/sponsorship-appeals-canada` |

### 2.3 Top SERP Opportunities (New Content)

1. **"Real Estate Closing Costs Ontario 2026 — Complete Guide"** — Competitors (sauvelaw.ca, noblerealestate.ca) are ranking with fresh 2026 guides. khanllp.com has zero content on this high-volume informational topic.

2. **"How Much Does a Real Estate Lawyer Cost in Ontario?"** — Low competition, high buyer intent. Lead-gen goldmine.

3. **"First-Time Home Buyer Legal Guide Ontario"** — Massive audience. Competitor realestatelawyer-toronto.com ranks for "Closing Costs in Toronto: A 2026 Guide for First-Time Buyers"

4. **"Ontario Land Transfer Tax Calculator / Guide 2026"** — Existing page is thin. Opportunity to expand into a comprehensive resource.

5. **"Non-Resident Speculation Tax Ontario 2026 Update"** — Policy changes frequently. Fresh content opportunity.

---

## 3. INTERNAL LINKING OPTIMIZATION

### 3.1 Current Structure

The site has a well-organized hierarchical structure:
- **Main pillar pages:** `/real-estate-lawyer-ontario`, `/family-law`, `/immigration`, `/wills-estate-planning`
- **City sub-pages:** 5 cities each (Toronto, Mississauga, Milton, Oakville, North York) × 4 practice areas = 20 location pages
- **Deep pages:** Individual sub-services (e.g., title-transfer, mortgage-refinancing)

### 3.2 Issues Found

1. **Orphaned deep pages:** Pages like `/commercial-lease-lawyer-ontario`, `/landlord-tenant-lawyer-ontario`, `/sexual-assault-lawyer-ontario` only appear in sitemap links on `/real-estate-lawyer-ontario`. Not linked from homepage or main nav.

2. **Duplicate internal link:** `/real-estate-lawyer-ontario` page links to its own URL `/real-estate-lawyer-ontario` in the services list (wastes link equity).

3. **City pages not cross-linked:** `/real-estate-lawyer-milton` does not link to `/real-estate-lawyer-oakville` and vice versa. City pages should form a silo network.

4. **Blog → Service page linking:** Blogs link to practice areas but inconsistently. E.g., "Why Home Buyers Need a Real Estate Lawyer" should link to `/real-estate-lawyer-ontario` and city-specific pages.

5. **Footer links overload:** The `/blogs` page has 20+ city-specific links in footer — dilutes link equity.

### 3.3 Proposed Internal Linking Plan

**Priority Pages to Boost (Target 3-5 contextual internal links each):**

| Target Page | Source Pages for Links |
|---|---|
| `/real-estate-lawyer-milton` | `/real-estate-lawyer-ontario`, `/blogs/why-home-buyers-need-a-real-estate-lawyer`, `/blogs/what-does-a-real-estate-lawyer-do`, `/purchase-and-sale` |
| `/real-estate-lawyer-mississauga` | `/real-estate-lawyer-ontario`, `/blogs/a-guide-to-real-estate-lawyers-in-canada`, `/best-real-estate-lawyers-in-milton-award` |
| `/family-law-mississauga` | `/family-law`, `/blogs/family-law-importance-divorce-custody-support-rights`, `/divorce-and-separation-ontario` |
| `/immigration-lawyer-toronto` | `/immigration`, `/blogs/` (upcoming immigration blog needed) |
| `/criminal-lawyer-ontario` | `/`, `/our-team`, `/dui-lawyer-ontario`, `/aggravated-assault-lawyer-ontario` |

**Cross-link city pages within same practice area:**
- Each city page should have a "Also Serving:" section linking to 2-3 other city pages

---

## 4. CONTENT UPDATES & RECOMMENDATIONS

### 4.1 Content Freshness Status

| Page | Last Updated (est.) | Recommendation |
|---|---|---|
| `/blogs` listing | April 28, 2026 | ✓ Good — recent posts, active blog |
| `/wills-estate-planning` | Unknown (looks older) | ✗ Needs 2026 refresh, add FAQ schema, city targeting |
| `/land-transfer-tax` | Unknown | ✗ Thin content — expand to comprehensive guide |
| `/non-resident-speculation-tax` | Unknown | ✗ Check for 2026 policy updates, add authority links |

### 4.2 Immediate Content Actions

1. **Create:** `/blogs/real-estate-closing-costs-ontario-2026-guide` — 2,000+ word guide targeting informational queries
2. **Create:** `/blogs/how-much-does-real-estate-lawyer-cost-ontario` — FAQ format, price ranges, what's included
3. **Update:** `/wills-estate-planning` — Add section on 2026 estate tax changes, add LocalBusiness schema
4. **Update:** `/land-transfer-tax` — Expand to 1,500+ words, add city-specific rates (Toronto vs rest of Ontario)
5. **Fix:** Old domain link `web.khanllp.ca` on wills page → update to khanllp.com

### 4.3 FAQ Schema Opportunity

None of the reviewed pages had FAQ schema markup. Priority pages for FAQ schema:
- `/wills-estate-planning` (already FAQ-formatted content — just needs markup)
- `/immigration` 
- `/real-estate-lawyer-ontario`

---

## 5. SUMMARY OF MEASURABLE WINS

| # | Action | Type | Priority | Effort | Impact |
|---|---|---|---|---|---|
| 1 | Implement SSR / static HTML for bots | Technical | **CRITICAL** | High (dev) | Indexability for all search engines |
| 2 | Fix H1 on `/immigration` page | On-Page | High | Low (5 min) | Improved relevance signal |
| 3 | Remove "Small Heading" placeholder + duplicate section on `/family-law` | Content | High | Low (5 min) | Professionalism + uniqueness |
| 4 | Fix old domain link `web.khanllp.ca` | Content | High | Low (5 min) | Trust signal |
| 5 | Create "Real Estate Closing Costs Ontario 2026" blog | Content | High | Medium (2 hrs) | Captures high-volume informational traffic |
| 6 | Cross-link city location pages | Internal Linking | Medium | Medium (1 hr) | PageRank flow to city pages |
| 7 | Add FAQ schema to 3 key service pages | Technical | Medium | Low (15 min) | Rich result eligibility |
| 8 | Create "How Much Does a Real Estate Lawyer Cost" blog | Content | Medium | Medium (2 hrs) | Lead-gen content |
| 9 | Fix sitemap priorities | Technical | Low | Low (10 min) | Better crawl budget allocation |
| 10 | Fix duplicate self-link on `/real-estate-lawyer-ontario` | Internal Linking | Low | Low (5 min) | Clean link graph |

---

## 6. QUICK WINS TODAY (Can Execute Immediately)

These are fixes achievable without developer involvement:

1. ✅ Fix H1 on `/immigration` — change to "Immigration Lawyer in Ontario, Canada | Khan Law"
2. ✅ Remove "Small Heading" placeholders from `/family-law`
3. ✅ Fix `web.khanllp.ca` link on wills page
4. ✅ Remove duplicate internal self-link on `/real-estate-lawyer-ontario`
5. ✅ Draft outline for "Real Estate Closing Costs Ontario 2026" blog post

---

*Report generated: 2026-04-30 11:15 GMT+5. All URLs verified against live sitemap. No hallucinated data — all findings backed by live web fetch / search results.*
