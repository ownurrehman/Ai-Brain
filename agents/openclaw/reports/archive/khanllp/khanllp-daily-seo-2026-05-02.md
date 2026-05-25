# KhanLLP.com Daily SEO Operation Report
**Date:** 2026-05-02 | **Agent:** Chronos (DeepSeek v4) | **Sitemap URLs:** ~80

---

## Phase 1: Technical Audit & Site Health

### Overall Health Score: 62/100 (Moderate Concern)

| Category | Score | Status |
|:---|:---|:---|
| Crawlability | 75/100 | OK |
| Indexability | 65/100 | ⚠️ Issues |
| Security/HTTPS | 55/100 | ⚠️ Issues |
| Schema/Structured Data | 40/100 | 🔴 Critical Issues |
| Page Speed | 55/100 | ⚠️ Issues |
| Mobile | 60/100 | ⚠️ Issues |
| On-Page SEO | 70/100 | OK |
| Internal Linking | 75/100 | OK |

### 1. Crawlability (75/100)

**Sitemap:** Found at `/sitemap.xml` with ~80 URLs. Includes all service pages, location pages, blog posts. No lastmod dates provided - only changefreq and priority. Sitemap is not referenced in robots.txt (no Sitemap: directive).

**robots.txt:** Overly verbose. Lists every single URL as `Allow:` which is unnecessary and bloated. The file blocks `/admin/`, `/login/`, and `/get-in-touch/`. Standard `User-agent: *` with no crawl delay.

**www/non-www:** ⚠️ www.khanllp.com does NOT redirect to khanllp.com. It returns 200 on www. This creates duplicate content. Both versions serve identical content.

**HTTP to HTTPS:** ✅ `http://khanllp.com` correctly 301 redirects to `https://khanllp.com`.

### 2. Indexability (65/100)

**Duplicate Content Issues Found:**
1. 🔴 **www vs non-www:** Both `www.khanllp.com` and `khanllp.com` return 200 with identical content. No canonical cross-domain setup.
2. 🔴 **Two near-identical non-resident pages:** Both `/purchase-sales-for-non-residents` and `/purchase-and-sale-for-non-residents` exist and return 200.
3. ⚠️ **Two title transfer pages:** `/title-transfer-ontario` and `/title-transfer` both return 200. Likely duplicate/overlapping.
4. ⚠️ **Blogs page meta:** Uses identical meta description as homepage. No unique meta.
5. ⚠️ **No hreflang tags:** Site serves only English-Canada market but has no hreflang.

**Canonicals:** ✅ Each page has a self-referencing canonical.

### 3. Security & HTTPS (55/100)

| Header | Present | Status |
|:---|:---|:---|
| HTTPS | ✅ | Active, valid cert |
| HSTS (Strict-Transport-Security) | ❌ | Missing |
| Content-Security-Policy | ❌ | Missing |
| X-Frame-Options | ❌ | Missing |
| X-Content-Type-Options | ❌ | Missing |
| Referrer-Policy | ❌ | Missing |
| Permissions-Policy | ❌ | Missing |

**Severity:** No security headers at all on Apache/2.4.54. This is a trust signal gap for a law firm site. HSTS is critical for SEO and user trust.

### 4. Schema / Structured Data (40/100) 🔴

**Homepage:** Only has `WebPage` schema with basic description - no `LocalBusiness`, `Organization`, or `LegalService` schema. No NAP data, no reviews, no service types.

**Service Pages (real-estate-lawyer-ontario, family-law, immigration):** ❌ NO schema markup whatsoever. Zero JSON-LD detected.

**Blog Posts:** Only `WebPage` schema - no `Article` or `BlogPosting` schema. Missing author, datePublished, publisher data.

**Critical Missing Schema:**
- `LocalBusiness` / `LegalService` (on all pages)
- `Organization` (homepage)
- `Review` + `AggregateRating` (testimonial-heavy pages)
- `FAQPage` (family-law page has FAQ section)
- `Article` / `BlogPosting` (all blog posts)
- `BreadcrumbList` (navigation)
- `Service` (individual service pages)

### 5. Page Speed & Performance (55/100)

- **Homepage HTML Size:** ~127 KB (heavy for a static page)
- **Time to First Byte:** ~1.5s (moderately slow, Apache server)
- **Server:** Apache/2.4.54 Ubuntu (no CDN detected, no caching headers beyond PHP sessions)
- **Cache-Control:** Mixed signals - sets `no-store, no-cache, must-revalidate` AND `max-age=1000000, private`. Conflicting.
- **No compression indication** in response headers.

### 6. Mobile-Friendliness (60/100)

- ✅ Viewport meta present
- ⚠️ `maximum-scale=1, user-scalable=no` — accessibility issue. Blocks users from zooming.
- ✅ Responsive layout indicators present
- No mobile-specific issues observed in content extraction

### 7. On-Page SEO (70/100)

**Meta Description Audit:**

| Page | Title Length | Desc Length | Issues |
|:---|:---|:---|:---|
| Homepage | 59c ✅ | 169c ⚠️ | Desc over 160 char limit |
| /real-estate-lawyer-ontario | 57c ✅ | 152c ✅ | OK |
| /family-law | 49c ✅ | 154c ✅ | OK |
| /immigration | 51c ✅ | 148c ✅ | OK |
| /wills-estates | 41c ✅ | 150c ✅ | OK |
| /real-estate-lawyer-milton | 57c ✅ | 152c ✅ | "milton" lowercase - should be "Milton" |
| /immigration-lawyer-toronto | 46c ✅ | 148c ✅ | OK |
| /blogs | 6c ❌ | 169c ⚠️ | Title is just "Blogs", desc duplicates homepage |

**H1 Issues:**
- `/family-law`: Contains orphan "## Small Heading" H2 (debug artifact)
- `/real-estate-lawyer-ontario`: Has an empty span in an H2: `<span style="color:#27ae60;"></span>`

---

## Phase 2: SERP Analysis & Keyword Targeting

### Competitor Landscape

**Real Estate Lawyer Space (Toronto/GTA):** Highly competitive. Competitors include:
- sumalaw.com, dwelllaw.ca, dukelawfirm.ca, pereralaw.ca, nanda.ca
- All have dedicated local landing pages with city-specific content

**Immigration Lawyer Space:** Even more competitive with national players:
- immigrationway.com, poonahimmigrationlaw.com, seiflawfirm.com
- TopLawyersCanada.ca directory listings dominate some queries

**Family Law:** Regional competition from smaller firms. Khan Law has reviews advantage.

### High-Opportunity Keywords to Target

#### Tier 1: High Volume + Achievable
| Keyword | Est. Volume | Current Visibility | Opportunity |
|:---|:---|:---|:---|
| real estate lawyer milton | Medium | Ranking (page has content) | Optimize H1, add schema, target FAQ |
| family lawyer milton | Medium | Has dedicated page | Strengthen content depth |
| wills and estates lawyer mississauga | Low-Med | Has location page | Add FAQ schema, increase word count |
| immigration lawyer mississauga | Medium | Has location page | Thin content on location pages |
| divorce lawyer oakville | Medium | family-law page targets this | Title says "Oakville, CA" - optimize for Oakville ON |

#### Tier 2: Long-Tail Gems
| Keyword | Opportunity |
|:---|:---|
| real estate closing lawyer milton ontario | No dedicated page |
| how to transfer property title ontario | Blog opportunity |
| family law separation agreement ontario cost | Family law page covers this partially |
| express entry immigration lawyer toronto | Express entry page exists, thin |
| power of attorney lawyer mississauga | Dedicated pages exist, optimize |

#### Tier 3: Blog Content Opportunities
| Topic | Notes |
|:---|:---|
| Ontario land transfer tax calculator guide 2026 | High search intent, existing page to link to |
| First-time home buyer legal checklist Ontario | No existing content |
| What to expect at a real estate closing in Ontario | Blog opportunity |
| Sponsorship appeal process Canada | Appeals pages exist, need supporting blog |
| Common law vs married property rights Ontario | Family law FAQ touches this, expand to blog |

### SERP Insights
1. **Google Business Profile presence:** Khan Law has reviews across multiple platforms (Birdeye 54 reviews, Yelp, Trustpilot) — NAP consistency needs verification across all.
2. **Local pack competition:** Most real estate lawyer searches trigger local 3-pack. NAP + reviews + schema are the ranking factors there.
3. **Zero featured snippets** detected for khanllp.com content. FAQ schema on FAQ-rich pages could capture these.

---

## Phase 3: Internal Linking Analysis

### Current Link Architecture

The site uses a hub-and-spoke model:
- **Main Hubs:** 5 service categories (Real Estate, Family, Wills, Immigration, Criminal)
- **Spokes:** 5 city location pages per service category (Toronto, Mississauga, Milton, Oakville, North York)
- **Footer:** Full site-wide link block with all location pages

### Issues Found:
1. ⚠️ **Footer link bloat:** Every page footer contains ~25+ links to all location pages. This dilutes link equity.
2. 🔴 **"Show more" dead links:** Footer has `Show more` links pointing to `#` (empty anchors) — wasted crawl budget.
3. ⚠️ **Service Areas section mislinks:**
   - "Real Estate" in footer links to `/commercial-properties` (should be `/real-estate-lawyer-ontario`)
   - "Immigration" links to `/expression-of-interest-ontario` (too specific, should be `/immigration`)
   - "Wills & Estates" links to `/wills-estates` (OK)
   - "Criminal Law" links to `/sexual-assault-lawyer-ontario` (too specific)
4. ⚠️ **Homepage service links broken:** Multiple homepage links point to `#` (Buying a Condo, Closing Adjustments, HST in Ontario, Commercial Properties, Expression of Interest, Impaired Driving, Bail Hearing, Assault, Domestic Assault, Sexual Assault Lawyer Ontario)
5. ✅ **Blog-to-service linking:** Not verified in this scan but likely minimal. Blog posts should link contextually to service pages.

### Recommended Internal Linking Map

| Source Page | → Target Page | Anchor Text | Purpose |
|:---|:---|:---|:---|
| /real-estate-lawyer-ontario | /real-estate-lawyer-milton | "Milton real estate closing services" | Cross-link location pages |
| /real-estate-lawyer-milton | /real-estate-lawyer-oakville | "Oakville property transactions" | Hub authority |
| /family-law | /divorce-and-separation-ontario | "divorce and separation process" | Deep-link to child page |
| /immigration | /express-entry-to-canada | "Express Entry program" | Support deeper pages |
| /blogs (each post) | Relevant service page | Contextual anchor | Pass equity to money pages |
| /wills-estates | /power-of-attorney-property | "property power of attorney" | Deep linking |
| /wills-estates | /power-of-attorney-health | "healthcare power of attorney" | Deep linking |

---

## Phase 4: Content Updates & Gaps

### Critical Fixes Needed

1. **🔴 Fix www.khanllp.com canonical issue:** Add 301 redirect www → non-www, or set canonical.
2. **🔴 Add security headers:** Minimum HSTS, X-Frame-Options, X-Content-Type-Options.
3. **🔴 Add proper schema:** LocalBusiness on homepage, Article on blogs, FAQPage on family-law, Service on service pages.
4. **🔴 Fix homepage dead links:** ~10 links pointing to `#` — these are broken CTAs.
5. **🔴 Remove duplicate non-resident pages:** Consolidate `/purchase-sales-for-non-residents` and `/purchase-and-sale-for-non-residents` into one canonical URL.

### Content Quality Issues

- `/family-law`: "Small Heading" debug artifact visible in page. Remove orphan H2.
- `/real-estate-lawyer-ontario`: Empty styled span in H2 needs cleanup.
- `/blogs`: Meta title is just "Blogs" — should be "Khan Law Blog | Legal Insights for Ontario" or similar.
- Location pages appear templated with thin content. Should be differentiated with unique local content per city.

### Quick Wins (<1 hour each)

1. Fix robots.txt — clean up verbose Allow directives, add Sitemap: reference
2. Fix /blogs title from "Blogs" to a branded title
3. Remove "Small Heading" debug text from family-law page
4. Fix meta description on homepage to under 160 chars
5. Capitalize "milton" to "Milton" in real-estate-lawyer-milton meta desc
6. Remove `maximum-scale=1, user-scalable=no` from viewport meta

---

## Summary & Priority Action Items

### Immediate (This Week)
| # | Action | Impact | Effort |
|:---|:---|:---|:---|
| 1 | Add 301 redirect www → non-www | Prevents duplicate content | Low |
| 2 | Add LocalBusiness + Organization schema to homepage | Huge trust signal | Medium |
| 3 | Fix homepage broken links (10+ # links) | User experience + crawl | Medium |
| 4 | Add security headers (HSTS, X-Frame, X-Content-Type) | Trust + SEO signal | Low |
| 5 | Consolidate duplicate non-resident pages | Clean indexation | Low |

### Short-Term (This Month)
| # | Action | Impact | Effort |
|:---|:---|:---|:---|
| 6 | Add FAQPage schema to family-law page | Snippet opportunity | Low |
| 7 | Add Article schema to all blog posts | Rich result potential | Low |
| 8 | Optimize location pages with unique city content | Local SEO boost | High |
| 9 | Reduce footer link bloat | Link equity | Medium |
| 10 | Create 3 new blog posts targeting tier-2 keywords | Content gap fill | Medium |

### Ongoing
- Monitor indexation after www fix
- Track keyword rankings for Tier 1 targets
- Review Google Business Profile NAP consistency
- Build backlinks/citations for location pages
