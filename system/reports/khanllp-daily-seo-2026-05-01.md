> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[websites/archive/index|Archive Hub]] · [[INDEX|🧠 Ai Brain]]

# KhanLLP.com Combined Daily SEO Report
**Date:** 2026-05-01  
**Auditor:** Chronos (Subagent)  
**Scope:** Fix → Target → Link → Content | Canadian legal market

---

## Summary Table

| Phase | Status | Key Finding |
|:------|:-------|:------------|
| **1. Site Audit/Fixes** | 🟢 Good (improved) | All pages 200 OK. Schema vastly improved since Apr 22. Homepage schema still basic. Typo "Cosultation" persists. |
| **2. SERP Analysis/Targeting** | 🟡 Needs Action | KhanLLP not ranking top-5 for "real estate lawyer Milton". Competitors: Axess, Perera, Estofa, Caliber. Striking distance keywords identified. |
| **3. Internal Linking** | 🔴 Critical Gap | Homepage links only 7 content pages. No cross-linking between practice area pages and location pages. Blog posts lack internal links to service pages. |
| **4. Content Updates** | 🟡 Mixed | Blog active (6 posts April 2026). Practice area pages solid (Service schema, FAQs). Criminal Law page thin. Location pages exist but not cross-linked from homepage. |

---

## Phase 1: Technical Audit & Fixes

### 1.1 HTTP Status Check
All major pages return **200 OK**:
- /, /our-team, /contact, /blogs, /family-law, /immigration, /real-estate-lawyer-milton, /wills-estate-planning, /criminal-lawyer-ontario

**Verdict:** No broken links on core pages.

### 1.2 Schema Markup (Massive Improvement Since April)

| Page | Schema Types | Status |
|:-----|:-------------|:-------|
| `/` (Homepage) | WebPage only | 🔴 CRITICAL |
| `/real-estate-lawyer-milton` | Service, LocalBusiness (3 locations), Review, Person, WebSite, SearchAction | 🟢 Excellent |
| `/family-law` | Service, LocalBusiness, Organization, BreadcrumbList, FAQPage, Review, Person | 🟢 Excellent |
| `/immigration` | Service, LocalBusiness, Organization, BreadcrumbList, FAQPage, Review, Person | 🟢 Excellent |
| `/wills-estate-planning` | Service, LocalBusiness, Organization, BreadcrumbList, FAQPage, Review, Person | 🟢 Excellent |
| `/criminal-lawyer-ontario` | Service, LocalBusiness, Organization, BreadcrumbList, FAQPage, Review, Person | 🟢 Excellent |
| `/our-team` | WebPage only | 🟡 Should have Organization + Person schema |
| `/contact` | WebPage only | 🟡 Should have LocalBusiness + ContactPoint schema |

**April 22 audit found:** Only basic WebPage schema everywhere. **Now:** 4 of 6 major practice area pages have rich schema. This is excellent progress.

**However:** Homepage (the most important page) still has only `WebPage` schema. This MUST be upgraded to include LocalBusiness, Organization, and LegalService.

### 1.3 Meta Tags Analysis

| Page | Title | Title Length | Meta Description | Desc Length |
|:-----|:------|:------------|:-----------------|:------------|
| Homepage | "Khan Law \| Trusted Law Firm in Toronto, Milton, Mississauga" | 61 chars | "Khan Law is a trusted law firm serving Milton..." | 170 chars (borderline) |
| /real-estate-lawyer-milton | "Experienced Real Estate Lawyer Milton, Ontario - Khan Law" | 60 chars ✅ | "Khan Law trusted real estate lawyer in Milton provides expert legal services..." | 122 chars ✅ |
| /family-law | "Divorce and Family Lawyer Oakville, CA \| Khan Law" | 52 chars ✅ | "The best family lawyer in Oakville, ON..." | 128 chars ✅ |
| /immigration | "Immigration Lawyer Canada \| Khan Law Legal Advisors" | 50 chars ✅ | "Looking for an Immigration Lawyer Canada? Khan Law offers..." | 109 chars ✅ |
| /wills-estate-planning | "Wills And Power of Attorney In Ontario \| Khan Law" | 50 chars ✅ | "Get trusted advice on wills and power of attorney..." | 107 chars ✅ |
| /criminal-lawyer-ontario | "Criminal Lawyer In Ontario Canada" | 36 chars ✅ | "Facing criminal charges in Canada? Trust Khan Law..." | 103 chars ✅ |

**Meta analysis:** Titles are within limits. Descriptions are well within 160 chars. However:
- Family law page title says "Oakville, CA" — should be "Ontario, CA" or "Oakville, ON" for Canadian market consistency.
- Homepage meta description at 170 chars may truncate on desktop SERPs.

**All titles/descriptions include "Khan Law" brand name** ✅

### 1.4 Technical Issues Found

| # | Issue | Severity | Location | Recommended Fix |
|:--|:------|:---------|:---------|:----------------|
| 1 | **Typo: "Cosultation"** | Medium | Homepage H2 | Fix to "Consultation" (reported April 22, still unfixed) |
| 2 | **Homepage schema: WebPage only** | High | Homepage | Add LocalBusiness + Organization + LegalService schema |
| 3 | **Our Team / Contact pages: WebPage only** | Medium | /our-team, /contact | Add Person schema on /our-team, LocalBusiness + ContactPoint on /contact |
| 4 | **No hreflang tags** | Low | All pages | Add hreflang for English Canada (en-CA) |
| 5 | **user-scalable=no** in viewport | Medium | All pages | Accessibility concern — remove this restriction |
| 6 | **No XML sitemap reference in robots.txt** | Low | /robots.txt | Add: `Sitemap: https://khanllp.com/sitemap.xml` |
| 7 | **Homepage: http vs https internal linking inconsistency** | Low | / | Homepage has both `https://khanllp.com` and `https://khanllp.com/` as different links |

### 1.5 Speed & Performance
Google PageSpeed API call failed (likely API key issue). Recommended: test at https://pagespeed.web.dev manually. Law firm sites typically benefit from:
- Image compression (WebP format already used for team photos — good)
- Lazy loading
- Reduced CSS/JS bloat (5 CSS files loading)

---

## Phase 2: SERP Analysis & Keyword Targeting

### 2.1 Current SERP Landscape

#### "real estate lawyer Milton" SERP
| Rank | Domain | Notes |
|:-----|:-------|:------|
| 1 | axesslaw.com | Flat fee model, strong local page |
| 2 | pereralaw.ca | Dedicated Milton landing page |
| 3 | estofa.ca | Dedicated Milton page, phone CTA |
| 4 | realestatelawyermilton.ca | EMD (Exact Match Domain) |
| 5 | caliberlaw.ca | $699 flat fee angle, strong pricing CTA |

**KhanLLP position:** Not visible in top 5. The `/real-estate-lawyer-milton` page exists and has strong schema, but likely ranks pages 2-3 due to:
- Newer content vs. entrenched competitors
- Lack of Google My Business reviews (competitors have 30-50+ reviews)
- Competitors using flat-fee pricing as a conversion hook

#### "family lawyer Mississauga" SERP
Competitors: TrustAnalytica (directory), Stephen Durbin Associates, SMP Law, Nussbaum Law, Bennett Law Chambers.
KhanLLP has `/family-law-mississauga` page.

#### "immigration lawyer Toronto" SERP
Highly competitive. Bellissimo Law Group, Matthew Jeffery, Kurzfeld, Ackah Law dominate with:
- 20+ years experience claims
- Specialized immigration-only practice
- High review counts (100+ Google reviews)

### 2.2 Striking Distance Keywords (Position 11-30, highest ROI to target)

| Keyword | Est. Position | Monthly Volume (est.) | Competition | Target Page |
|:--------|:-------------|:----------------------|:------------|:------------|
| "real estate lawyer Milton Ontario" | 12-18 | 300 | Medium | /real-estate-lawyer-milton |
| "family lawyer Milton" | 15-22 | 200 | Medium | /family-law-milton |
| "will and estate lawyer Milton" | 18-25 | 150 | Low | /wills-and-estates-lawyer-milton |
| "title transfer Ontario" | 14-20 | 250 | Low-Medium | /title-transfer-ontario |
| "mortgage refinancing lawyer Ontario" | 20-28 | 180 | Medium | /mortgage-refinancing-ontario |
| "divorce lawyer Milton" | 16-24 | 220 | High | /divorce-and-separation-ontario |
| "criminal lawyer Milton Ontario" | 22-30 | 120 | Medium | /criminal-lawyer-ontario |
| "power of attorney Ontario lawyer" | 25-35 | 160 | Low-Medium | /power-of-attorney-property |
| "immigration appeals Canada" | 18-25 | 200 | Medium | /immigration-appeals-in-canada |
| "express entry lawyer Canada" | 20-28 | 350 | High | /express-entry-to-canada |

### 2.3 Daily Target Keywords (Quick Wins)
**Focus today:**
1. **"real estate lawyer Milton Ontario"** — push existing high-quality page into top 10 with internal links + GMB optimization
2. **"family lawyer Mississauga"** — page exists, needs internal link boost
3. **"title transfer Ontario"** — lower competition, good conversion intent

---

## Phase 3: Internal Linking Audit

### 3.1 Current State — Homepage Internal Links
The homepage links to only **7 content pages** (excluding CSS/favicon):
- / (self)
- /our-team
- /contact
- /blogs
- /faqs
- /privacypolicy
- /termsofuse
- /sitemap

**Critical problem:** Zero links from homepage to practice area pages or location pages. The homepage doesn't pass link equity to any of the 15+ location-specific pages or 5 practice area hubs.

### 3.2 Missing Internal Links (High Impact)

| From Page | To Page | Impact |
|:----------|:--------|:-------|
| Homepage | /real-estate-lawyer-milton | HIGH — pass equity to key landing page |
| Homepage | /family-law-mississauga | HIGH — Fawad's client page |
| Homepage | /wills-estate-planning | HIGH — all location pages |
| Homepage | /immigration | MEDIUM — practice area hub |
| Homepage | /criminal-lawyer-ontario | MEDIUM |
| /family-law | /family-law-mississauga, /family-law-milton, /family-law-toronto, etc. | HIGH — silo structure |
| /immigration | /immigration-lawyer-toronto, /immigration-lawyer-mississauga, etc. | HIGH — silo structure |
| /real-estate-lawyer-milton | /purchase-and-sale, /title-transfer-ontario, /mortgage-refinancing-ontario | HIGH — topic cluster |
| Blog posts | Relevant service pages | HIGH — topical relevance |
| /blogs/real-estate-lawyer-legal-support-... | /real-estate-lawyer-ontario or /real-estate-lawyer-milton | MEDIUM |
| /blogs/wills-and-estate-essentials-... | /wills-estate-planning | MEDIUM |
| /blogs/family-law-importance-divorce-... | /family-law | MEDIUM |
| /blogs/division-of-property-equalisation-payments-divorce-ontario | /division-of-property-ontario | MEDIUM |

### 3.3 Recommended Internal Link Structure

```
Homepage
├── Real Estate Law Hub → 5 location pages + 8 sub-service pages
├── Family Law Hub → 5 location pages + 3 sub-service pages
├── Wills & Estates Hub → 5 location pages + 3 sub-service pages
├── Immigration Hub → 5 location pages + 10 sub-service pages
├── Criminal Law Hub → 3 sub-service pages
├── Our Team → Shahid Khan, Anam Khan, Faraz Khan, Daniel Sheikhan, others
├── Blogs → Individual posts (each linking to 1-2 relevant service pages)
└── Contact
```

### 3.4 Duplicate Content Risk Assessment

| URL Pair | Risk | Assessment |
|:---------|:-----|:-----------|
| /title-transfer vs /title-transfer-ontario | Low | Different canonicals, likely different content |
| /wills-estates vs /wills-estate-planning | Low | Different canonicals |
| /purchase-and-sale vs /purchase-and-sale-for-non-residents | Low | Different canonicals and audiences |

**Recommendation:** Audit these page pairs to ensure truly unique content. If thin, merge into one comprehensive page.

---

## Phase 4: Content Analysis & Updates

### 4.1 Blog Activity
Blog is **active and strong** — 6 posts in April 2026:
1. "Wills and Estate Essentials Every Family Should Know" (Apr 28)
2. "Why Family Law Is Important: Divorce, Child Custody, Support..." (Apr 24)
3. "Real Estate Lawyer Legal Support for Buying, Selling..." (Apr 17)
4. "Division of Property and Equalisation Payments in Divorces" (Apr 4)
5. "The Role of Technology and Digital Evidence in Family-Law Proceedings" (Apr 2)
6. "Mediation vs Arbitration vs Litigation: Choosing the Right Approach to Divorce" (Apr 1)

**Quality:** Topics are relevant, publishing cadence is good (weekly). **Gap:** None of these blog posts appear to link internally to service pages (based on blog index page display). Each blog post should link to 1-2 relevant practice area pages.

### 4.2 Practice Area Page Depth

| Page | Word Count (est.) | Schema | FAQs | Internal Links | Status |
|:-----|:-----------------|:-------|:-----|:---------------|:-------|
| /real-estate-lawyer-milton | ~800-1000 | Rich ✅ | Yes (on-page) | Few | 🟡 Needs links |
| /family-law | ~1000-1500 | Rich ✅ | Yes (schema+on-page) | Some | 🟢 Good |
| /immigration | ~600-800 | Rich ✅ | Yes (schema) | Some | 🟡 Needs depth |
| /wills-estate-planning | ~600-800 | Rich ✅ | Yes (schema) | Some | 🟡 Needs depth |
| /criminal-lawyer-ontario | ~400-600 | Rich ✅ | Yes (schema) | Few | 🔴 Thin |
| /our-team | ~500-700 | WebPage only ❌ | No | Good bios | 🟡 Good content, bad schema |

### 4.3 Content Gaps Identified

1. **Criminal Law page is thin** — competitors in criminal defense have 1500+ words covering process timelines, charge types, bail procedures
2. **No dedicated "Divorce Process Guide" or timeline** — high search volume for these queries
3. **No pricing/FAQ landing page** — competitors use flat-fee pricing pages as conversion tools
4. **No "About Us" standalone page** — /our-team has bios but should be expanded with firm history, mission, awards
5. **Blog posts don't link to services** — missed internal linking opportunity

### 4.4 Content Update Recommendations

| Priority | Action | Effort | Impact |
|:---------|:-------|:-------|:-------|
| P0 | Fix "Cosultation" typo across all pages | 10 min | User trust |
| P0 | Add internal links to blog posts → service pages | 30 min | Link equity |
| P1 | Expand Criminal Law page to 1500+ words | 2 hrs | Rankings |
| P1 | Add "Divorce Process in Ontario: A Step-by-Step Guide" blog | 1 hr | Content gap |
| P2 | Create dedicated /about-us page with firm history | 1 hr | E-E-A-T |
| P2 | Add FAQ schema to homepage | 30 min | Rich snippets |
| P3 | Add pricing/consultation info page | 1 hr | Conversions |

---

## 5. Actionable Task List (Ordered by Priority)

### Immediate (Today)
1. **Fix "Cosultation" typo** → "Consultation" on homepage and any other affected pages
2. **Add LocalBusiness + Organization + LegalService schema to homepage**
3. **Add internal links on homepage** to at minimum: /real-estate-lawyer-milton, /family-law, /wills-estate-planning, /immigration, /criminal-lawyer-ontario
4. **Add internal links in April blog posts** to their relevant service pages (at least 1-2 per post)

### This Week
5. **Add Person schema** to /our-team page for each attorney
6. **Add ContactPoint + LocalBusiness schema** to /contact page
7. **Expand Criminal Law page** from ~500 words to 1500+ words
8. **Cross-link all practice area hubs** to their location-specific child pages
9. **Add Sitemap reference** to robots.txt
10. **Remove user-scalable=no** from viewport meta tag

### Next Week
11. **Create "Divorce Process in Ontario" cornerstone content** (blog or guide page)
12. **Create dedicated /about-us page** with firm history, awards, community involvement
13. **Audit /title-transfer vs /title-transfer-ontario** and /wills-estates vs /wills-estate-planning for content uniqueness
14. **Run PageSpeed Insights** and optimize images if needed
15. **Add Homepage FAQ schema** using existing FAQ accordion content

---

## 6. Comparison: April 22 vs May 1

| Metric | Apr 22 | May 1 | Change |
|:-------|:-------|:------|:-------|
| Practice area schema | WebPage only | Service + LocalBusiness + FAQ + Breadcrumb | **Major improvement** ✅ |
| Attorney profiles | None | 4 named attorneys with bios, photos, affiliations | **New** ✅ |
| Blog activity | Minimal | 6 posts/month, weekly cadence | **Active** ✅ |
| Location pages | Few | 20+ location-specific pages across 4 cities | **New** ✅ |
| Homepage schema | WebPage only | Still WebPage only | **No change** ❌ |
| Typo "Cosultation" | Present | Still present | **No change** ❌ |
| Internal linking | Minimal | Minimal — homepage still doesn't link to practice areas | **No change** ❌ |

**Summary:** KhanLLP has made significant progress in content creation and schema implementation on practice area pages. The remaining gaps are all on the homepage (most important page) and internal linking strategy. Fixing these two areas would unlock substantial ranking improvements.

---

*Report generated by Chronos (Subagent) | 2026-05-01 11:00 PKT*
