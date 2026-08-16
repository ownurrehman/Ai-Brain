# Full SEO & Web Audit — tonicphysio.com
**Audit Date:** 2026-06-13  
**Auditor:** OpenClaw Subagent (Rank Ray)  
**Domain:** https://tonicphysio.com  
**Scope:** Workspace files, live site crawl, on-page SEO, schema, technical signals, sitemap/robots, GSC/GA4 data (where accessible), content strategy gap analysis, and prioritized action plan.

---

## Executive Summary

Tonic Physio is a mature, content-rich WordPress site with **~92 registered pages** (per `post-registry.md`) and a `site-map.json` declaring **75 live URLs**. The site has strong local SEO fundamentals: most service pages include "Milton" in titles/meta, Yoast SEO is active, XML sitemaps are present, and schema markup (FAQPage + MedicalOrganization/LocalBusiness) is implemented on key service pages.

However, several **high-impact issues** hold back rankings and conversions:

1. **Core Web Vitals / performance are unknown** — no PageSpeed Insights data has been captured.
2. **Indexation and URL hygiene issues** — duplicate blog files in workspace, orphaned drafts, and some pages ranking for the wrong query (e.g., homepage ranking for "physiotherapy milton" instead of the dedicated `/physiotherapy-in-milton/` page).
3. **GSC performance is very low** — top query "lymphatic drainage massage near me" has 25 clicks from 2,855 impressions (0.88% CTR); branded/local core terms sit at positions 10–20.
4. **Content quality drift** — some pillar blogs score poorly on AEO/readability (original back-pain guide scored 42/100) and many 2026 briefs/drafts remain unpublished.
5. **Schema inconsistencies** — `MedicalOrganization` schema uses malformed `geo` coordinates, empty `openingHours`, blank `areaServed`, and a generic description repeated on every page.
6. **Conversion friction** — `/fees/` lacks transparent pricing, `/contact/` is thin, and booking CTAs are present but not consistently above the fold.
7. **GSC/GA4 API access failure** — OAuth token for `oliverjakeseo@gmail.com` has been **revoked or expired**, so fresh 90-day GSC/GA4 data could not be pulled. A stale `search-console-page2-report.json` was used as a proxy.

### Snapshot: Site Health Score
| Category | Score | Rationale |
|----------|-------|-----------|
| **Technical Foundation** | 7/10 | WordPress + Yoast + Cloudflare + sitemaps OK; performance/schema need verification. |
| **On-Page SEO** | 6.5/10 | Most service pages optimized; homepage/blog titles weaker; duplicate workspace content. |
| **Content Depth** | 8/10 | 65 service pages, dozens of blogs, strong topical coverage. |
| **Local SEO** | 6/10 | NAP/schema present but inconsistent; GMB not assessed; local link building unknown. |
| **Indexation & Crawl** | 6/10 | Sitemaps fresh, but drafts/orphans and possible cannibalization. |
| **Conversion Optimization** | 5/10 | Fees page lacks prices, contact page thin, CTAs inconsistent. |
| **Performance (CWV)** | ?/10 | No PSI data captured — must be measured. |
| **Overall** | **~6.5/10** | Strong asset base with clear, prioritized levers for growth. |

---

## 1. Workspace & Asset Inventory

### 1.1 Directory Summary
- **Total files in workspace:** 167
- **Key directories:** `audits/`, `blogs_to_push/`, `briefs/`, `fixes/`, `knowledge/`, `scripts/`, `site-index/`, `assets/`
- **Active project docs:** `content-strategy-plan.md`, `protocol.md`, `post-registry.md`, `content-pipeline.md`, `mastersheet.md`, `semantic-plan.md`, `tonicphysio-semantic-seo-strategy-2026.md`

### 1.2 Site Index (`site-map.json`)
- **Last crawled:** 2026-05-31
- **Total URLs:** 75
- **Homepage:** 1
- **Service pages:** 25 listed (but `post-registry.md` shows **65 service pages** total — many newer child pages under `/physiotherapy-in-milton/` may not be reflected in the older site-map).
- **Blog posts:** 37 listed
- **Therapist pages:** 8 listed (registry shows 13 therapist profiles)
- **Utility pages:** 6

**Finding:** The workspace site-map is **out of sync** with `post-registry.md`. The registry is the more current source (2026-05-12) and should be used as the canonical URL list going forward.

### 1.3 Post Registry (`post-registry.md`)
- **Total pages:** 92
- **Service pages (category 325):** 65
- **Missing "Milton" in SEO fields:** 22 pages, mostly utility/therapist pages (acceptable), plus **Motor Vehicle Accident Physiotherapy** and **Arthritis Pain Relief** (needs fixing).
- **Duplicate file names in root:** Several `.md` blog drafts exist twice (e.g., `tonicphysio-blog-01`, `-02`, `-03`, `-04`, `-05`, `-07`). This creates risk of overwriting or publishing stale versions.

### 1.4 Content Strategy Status (`content-strategy-plan.md`)
- **Phase 1:** 8 draft posts to be upgraded to 2,000+ words (semantic SEO) — in progress.
- **Phase 2:** 4 pillar pages planned.
- **Phase 3:** 12 supporting cluster articles — not yet defined.
- **Blogs ready to push:** 42 `.md` files in `blogs_to_push/` (many still unpublished).

### 1.5 Topic Gap Analysis (`site-index/topic-gaps.md`)
Many gaps from 2026-05-31 have **already been closed** in `post-registry.md`:
- Vestibular Rehabilitation ✅
- Pelvic Health Physiotherapy ✅
- Pre-Surgical Rehabilitation ✅
- Geriatric Physiotherapy ✅
- Workplace Ergonomics Assessment ✅
- Running Injury Assessment ✅
- Knee Pain Treatment ✅
- Plantar Fasciitis (still missing from live registry, though blog 29 is in `blogs_to_push`)
- Whiplash (blog 31 in `blogs_to_push`, but no dedicated service page)
- Dry Needling ✅ (service page exists)
- Cupping Therapy ✅

**Remaining gaps:**
- Dedicated **plantar fasciitis service/condition page** (only blog planned).
- Dedicated **whiplash treatment page** (only MVA blogs exist).
- Dedicated **fibromyalgia management** page.
- **Insurance/billing cluster:** direct billing, extended health, OHIP coverage.
- **Local comparison content:** "best physiotherapy in Milton" pillar.

---

## 2. Live Site Technical Audit

### 2.1 robots.txt
- **Status:** 200 OK
- **Observation:** Contains Cloudflare content-signal directives (`search`, `ai-input`, `ai-train`) and blocks AI crawlers (ClaudeBot, GPTBot, Google-Extended, etc.). This is intentional but limits AI-driven snippet/answer-engine visibility.
- **Sitemap reference:** `https://tonicphysio.com/sitemap_index.xml` ✅
- **No critical disallow rules** found for core content.

### 2.2 XML Sitemaps
- **Sitemap index:** `/sitemap_index.xml` ✅
  - `post-sitemap.xml` — lastmod 2026-06-08
  - `page-sitemap.xml` — lastmod 2026-06-06
  - `category-sitemap.xml` — lastmod 2026-06-08
  - `author-sitemap.xml` — lastmod 2026-06-03
- **Sitemap freshness:** Good; updated within the last week.
- **No video/image/news sitemaps** — image sitemap tags are embedded inside post/page sitemaps (Yoast default).

### 2.3 Crawlability & Indexation
- `site:tonicphysio.com` web search returns homepage, contact, fees, services, blog, therapists, FAQ, about, and individual massage/blog pages ✅.
- **No signs of penalty or mass de-indexation**.
- **Risk:** Many child service pages (e.g., `/physiotherapy-in-milton/dry-needling/`) not confirmed in `site:` search results; may rank only for long-tail terms with low search volume.

### 2.4 URL Structure
- Service child pages use `/physiotherapy-in-milton/{service-slug}/` ✅ (good topical clustering).
- Massage pages use `/registered-massage-therapy/{massage-slug}/` ✅.
- Some slugs contain `-2`, `-3` suffixes (e.g., `back-and-neck-pain`, `chronic-pain-management-2`, `wrist-and-hand-pain-treatment-3`) indicating **duplicate-title/slug history**; not user-friendly and may dilute signals.

---

## 3. On-Page SEO Findings

### 3.1 Title Tags, Meta Descriptions, H1s

Live fetch results for key pages:

| URL | Title | Meta Description | H1 | Word Count (approx) | Status |
|-----|-------|------------------|----|---------------------|--------|
| `/` | Tonic Physiotherapy and Rehabilitation Centre in Milton, CA | Generic; mentions "Move Better and Live Better" | Tonic Physio Leading Physiotherapy & Rehab Centre in Milton | 8,303 | ⚠️ Title OK; meta could be more compelling; H1 has encoded ampersand |
| `/physiotherapy-in-milton/` | Physiotherapy in Milton \| Pain Relief & Rehab – Tonic Physio | Good, keyword + location | Physiotherapy in Milton | 10,578 | ✅ Strong |
| `/motor-vehicle-accident-physiotherapy/` | Motor Vehicle Accident Physiotherapy in Milton \| Tonic Physio | Good | Motor Vehicle Accident Physiotherapy in Milton for Complete Recovery | 9,779 | ✅ Good |
| `/wsib-care-programs/` | WSIB Care Programs in Milton \| Tonic Physio | OK | WSIB Care Programs for Workplace Injury Recovery | 10,229 | ✅ Good |
| `/tmj-treatment/` | TMJ Treatment in Milton, ON \| Tonic Physio | Good | TMJ Treatment for Lasting Jaw Pain Relief | 10,092 | ✅ Good |
| `/custom-orthotics/` | Custom Orthotics in Milton \| Tonic Physio | Good | Custom Orthotics in Milton for Precision Foot Alignment and Pain Relief | 9,643 | ✅ Good |
| `/shockwave-therapy/` | Shockwave Therapy in Milton \| Tonic Physio | Good | Shockwave Therapy for Chronic Pain Relief | 10,046 | ✅ Good |
| `/registered-massage-therapy/` | Registered Massage Therapy in Milton \| Tonic Physio | Good | Registered Massage Therapy in Milton | 9,414 | ✅ Good |
| `/manual-osteopathy-milton/` | Manual Osteopathy in Milton \| Tonic Physio | Typo: "hand to" in desc | Manual Osteopathy in Milton | 9,519 | ⚠️ Fix typo |
| `/back-pain-recovery-milton-professional-final/` | Back Pain Recovery Guide \| Tonic Physio | Good | Stop Managing Back Pain: The Definitive Guide to Total Recovery in Milton | 9,535 | ✅ Good (rewritten v2) |
| `/mva-recovery-timeline-what-to-expect-week-by-week/` | MVA Recovery Timeline: Physiotherapy Guide \| Tonic Physio | Good | MVA Recovery Timeline: What to Expect Week by Week | 9,196 | ✅ Good |
| `/wsib-claims-process-workplace-injury-milton/` | WSIB Claims Process for Workplace Injury \| Tonic | Truncated brand | WSIB Claims Process: How to Navigate Workplace Injury Benefits | 9,021 | ⚠️ Title missing brand completion; H1 lacks Milton |

### 3.2 Issues Identified
1. **Homepage meta description is weak** — no clear value prop, no CTA, no differentiator.
2. **Homepage H1 contains HTML entity `&amp;`** instead of clean `&` (minor rendering/semantic issue).
3. **Manual Osteopathy meta description typo** — "gentle hand to" should be "gentle hands-on" or "gentle, hands-on".
4. **WSIB blog title cuts off brand** — "\| Tonic" should be "\| Tonic Physio".
5. **Compression Socks** (`/compression-socks/`) is the only service page with **no Milton keyword** in title/meta/slug per `meta-audit-report.md`.
6. **MVA and Arthritis Pain Relief** service pages also missing Milton in Yoast fields.
7. **Duplicate blog file names** in workspace root risk publishing wrong versions.

---

## 4. Schema Markup Audit

### 4.1 Homepage
- `WebSite` schema ✅
- `MedicalOrganization` ✅ (but fields need cleanup)
- `SiteNavigationElement` graph ✅

### 4.2 Service Pages (e.g., `/physiotherapy-in-milton/`, `/motor-vehicle-accident-physiotherapy/`)
- `FAQPage` schema ✅
- `medicalclinic` / `LocalBusiness` ✅
- `Service` ✅
- `WebSite`, `BreadcrumbList`, `MedicalOrganization` ✅

### 4.3 Blog Posts
- Only `WebSite`, `BreadcrumbList`, `MedicalOrganization` and `SiteNavigationElement` detected.
- **No Article/BlogPosting schema** — missing author, publish date, featured image structured data.

### 4.4 Critical Schema Defects
The `MedicalOrganization` / `medicalclinic` JSON-LD repeats across pages with these issues:

| Field | Current Value | Issue |
|-------|---------------|-------|
| `openingHours` | `[""]` | Empty array — invalid |
| `priceRange` | `""` | Empty |
| `servesCuisine` | `""` | Empty (not applicable) |
| `additionalType` | `""` | Empty |
| `hasMenu` | `""` | Empty (not applicable) |
| `areaServed` | `[Milton, Toronto, ""]` | Blank third entry; Toronto is questionable unless a real service area |
| `geo.latitude` | `"43.5198335107543,"` | Trailing comma — not a valid number |
| `geo.longitude` | `"-79.87211905767205"` | OK, but pair is malformed |
| `aggregateRating` | present | `ratingValue` and `reviewCount` truncated in crawl; verify authenticity per Google guidelines |
| `description` | "Expert physiotherapy and rehab services..." | Generic homepage description reused on every page |
| `image` | `Tonicphysio.png` | Verify logo image is correct and accessible |

**Impact:** Malformed schema can cause Google to ignore or downgrade rich-result eligibility and knowledge-panel accuracy.

---

## 5. Internal Linking & Content Architecture

### 5.1 Strengths
- Clear service hub: `/physiotherapy-in-milton/` with 40+ child condition/treatment pages.
- Clear massage hub: `/registered-massage-therapy/` with child modality pages.
- Utility pages (FAQ, fees, privacy, terms, sitemap) present.
- Recent blog rewrites include internal link maps (e.g., back-pain guide → 12 contextual links).

### 5.2 Weaknesses
- **Therapist profiles** lack internal links to services/conditions they treat (opportunity).
- **Utility pages** (FAQ, Fees, Terms) do not link back to relevant service clusters.
- **Breadcrumb** present but must be verified for all child pages.
- **No "Related Services" module** consistently shown across service pages.

---

## 6. Content Quality & AEO

### 6.1 Back Pain Recovery Blog Rewrite
- **Original score:** 42/100 (fail).
- **Rewritten v2 score:** 86/100 (pass).
- **Key improvements:** H1 removed from body, paragraphs shortened, comparison table added, AEO summary block, local signals, internal links.
- **Status:** The live page now appears to be the rewritten version (H1 is user-friendly, content ~9,500 words).

### 6.2 Content Pipeline
- 42 blogs in `blogs_to_push/` are ready but many not yet published.
- Risk of **publishing duplicate or outdated versions** because root also contains older `.md` files with identical base names.

### 6.3 AEO / Generative Engine Readiness
- FAQ schema is strong for service pages.
- Blogs lack clear "definition sentence" and "key takeaway" blocks unless rewritten.
- No HowTo, QAPage, or speakable schema detected.

---

## 7. Google Search Console & GA4 Data

### 7.1 Data Access Status
- **Live GSC API pull failed** due to OAuth token revocation/expiry for `oliverjakeseo@gmail.com`.
- Re-authentication script requires interactive browser flow; not completed in this subagent session.
- **GA4 data:** Not accessible for the same reason.

### 7.2 Stale GSC Proxy (`search-console-page2-report.json`)
The file contains query/page-level data (clicks, impressions, CTR, position). Key observations:

| Query | Clicks | Impressions | CTR | Avg Position | Page |
|-------|--------|-------------|-----|--------------|------|
| lymphatic drainage massage near me | 25 | 2,855 | 0.88% | 11.5 | /registered-massage-therapy/lymphatic-drainage-massage-milton/ |
| massage milton | 13 | 1,437 | 0.90% | 10.3 | /registered-massage-therapy/relaxation-massage-in-milton/ |
| head massage near me | 10 | 1,182 | 0.85% | 10.7 | /registered-massage-therapy/indie-head-massage/ |
| physiotherapy milton | 5 | 895 | 0.56% | 10.7 | **Homepage** |
| tmj massage near me | 10 | 454 | 2.20% | 11.6 | /tmj-treatment/ |
| postpartum massage near me | 3 | 189 | 1.59% | 12.6 | /registered-massage-therapy/post-natal-massage-milton/ |

### 7.3 GSC Insights
1. **Massage terms dominate traffic** — the top 5 queries by impressions are all massage-related. Physiotherapy core terms have far lower visibility.
2. **Positions are page 2 (10–20)** — small CTR gains from title/meta improvements could materially increase clicks.
3. **Homepage ranking for "physiotherapy milton"** instead of `/physiotherapy-in-milton/` suggests either cannibalization or the service page has weaker internal authority.
4. **Low overall clicks** — entire dataset shows only a few hundred clicks over the reporting window, indicating the site is still in a low-traffic phase despite 90+ pages.

### 7.4 GA4
- No fresh data available.
- **Recommendation:** Re-authenticate GSC/GA4 and pull 90-day reports for `activeUsers`, `sessions`, `conversions`, `deviceCategory`, `channelGrouping`, and `pageReferrer`.

---

## 8. Competitive Context

From `protocol.md`:

| Metric | TonicPhysio | MexPhysio |
|--------|-------------|-----------|
| Service pages | 65 | 44 |
| Locations | Milton | Milton + Oakville |
| Unique offerings | Nutrition Coaching, Visual Therapy, B-Pulse | — |

**Assessment:** TonicPhysio has **out-published MexPhysio on service pages** but lags in multi-location coverage. The priority now is **quality consolidation** rather than more page volume.

---

## 9. Prioritized Action Plan

### 🔴 P0 — Critical (Do This Week)

| # | Action | Page/Area | Expected Impact |
|---|--------|-----------|-----------------|
| 1 | **Re-authenticate GSC/GA4** and pull fresh 90-day data; add to monthly reporting cadence. | Google APIs | Unlock data-driven decisions. |
| 2 | **Fix malformed LocalBusiness/MedicalOrganization schema:** remove empty fields, fix `geo.latitude` trailing comma, fill `openingHours`, remove blank `areaServed`, add real `priceRange`, and remove irrelevant `servesCuisine`/`hasMenu`. | All pages (footer/header injection) | Improve rich-result eligibility and knowledge panel accuracy. |
| 3 | **Add transparent pricing** to `/fees/` — even ranges or "starting from" prices. | `/fees/` | Major conversion uplift; users currently leave without information. |
| 4 | **Fix Manual Osteopathy meta description typo** and complete WSIB blog title brand. | `/manual-osteopathy-milton/`, `/wsib-claims-process-workplace-injury-milton/` | Professionalism + CTR. |
| 5 | **Consolidate duplicate blog files** in workspace root and `blogs_to_push/`; decide canonical version before any further publishing. | Workspace | Prevent duplicate/stale content going live. |

### 🟠 P1 — High Priority (Next 2–4 Weeks)

| # | Action | Page/Area | Expected Impact |
|--------|-----------|-----------------|
| 6 | **Run PageSpeed Insights / Core Web Vitals** on homepage, top 5 service pages, and top 5 blogs; fix LCP/CLS/INP issues. | Performance | Ranking and UX. |
| 7 | **Enrich `/contact/` page:** add full hours, embedded map, parking/landmark info, team photo, and direct booking CTA above the fold. | `/contact/` | Local SEO + conversions. |
| 8 | **Improve homepage meta description** with value prop + CTA + local keyword (e.g., "Book expert physiotherapy in Milton..."). | `/` | CTR. |
| 9 | **Add Article/BlogPosting schema** to all blogs with author, datePublished, dateModified, featured image, and publisher. | Blog posts | Rich snippets + E-E-A-T. |
| 10 | **Build internal linking campaign:** link therapist profiles → relevant services; add "Related Services" sidebar/module to service pages; interlink condition pages. | Site-wide | Distribute authority, reduce orphan pages. |
| 11 | **Add Milton keyword** to Compression Socks, MVA service page, and Arthritis Pain Relief Yoast fields. | 3 service pages | Local relevance. |
| 12 | **Publish the 42 queued blogs** with Yoast meta, featured images, and internal links — but only after deduplication. | `blogs_to_push/` | Topical authority + long-tail traffic. |

### 🟡 P2 — Medium Priority (Next 1–3 Months)

| # | Action | Page/Area | Expected Impact |
|--------|-----------|-----------------|
| 13 | **Create remaining topical gap pages:** plantar fasciitis, whiplash treatment, fibromyalgia management, direct billing/OHIP guide, "best physiotherapy in Milton" comparison. | New pages | Capture unclaimed search demand. |
| 14 | **Resolve slug suffixes** (`-2`, `-3`) by setting canonicals or redirecting older duplicates to the primary URL. | Condition pages | URL hygiene + signal consolidation. |
| 15 | **Add testimonials/case studies** to service pages and homepage (with Review schema). | Conversion pages | Trust + CTR. |
| 16 | **Optimize homepage for "physiotherapy milton":** either strengthen `/physiotherapy-in-milton/` internal links or canonical intent toward the service page. | Homepage + service hub | Clarify ranking target. |
| 17 | **Add HowTo / QAPage schema** to key blogs where step-by-step instructions exist. | Blogs | AEO / featured snippets. |
| 18 | **Audit image alt text** across service pages and blogs; ensure descriptive, keyword-aware alt text. | Site-wide | Accessibility + image SEO. |

### 🟢 P3 — Ongoing / Maintenance

| # | Action | Frequency |
|--------|-----------|-----------|
| 19 | Update `site-map.json` to match live registry (92 pages) and keep it current. | Monthly |
| 20 | Monitor GSC for index coverage, Core Web Vitals, and mobile usability issues. | Weekly |
| 21 | Refresh meta descriptions on low-CTR pages (CTR <1% with position <15). | Monthly |
| 22 | Continue semantic content refreshes and publish new cluster articles per `content-strategy-plan.md`. | Quarterly |

---

## 10. Quick Wins Checklist

- [ ] Re-auth GSC/GA4 and export 90-day reports.
- [ ] Fix schema empty/malformed fields.
- [ ] Add pricing to `/fees/`.
- [ ] Fix Manual Osteopathy meta typo.
- [ ] Complete WSIB blog brand in title.
- [ ] Add Milton to Compression Socks, MVA, Arthritis Pain Relief SEO fields.
- [ ] Run PSI on homepage + top 5 service pages.
- [ ] Enrich `/contact/` with map + hours.
- [ ] Deduplicate blog files in workspace.
- [ ] Add Article schema to top 10 blogs.

---

## 11. Data Limitations & Notes

- GSC/GA4 live API data could not be retrieved because the OAuth token for `oliverjakeseo@gmail.com` is expired/revoked. Stale workspace GSC report used as proxy.
- No PageSpeed Insights / Core Web Vitals data was collected in this audit; this should be the first follow-up task.
- robots.txt contains Cloudflare AI-crawler blocks; this is intentional but may limit AI-search visibility.

---

*Report generated by OpenClaw subagent for tonicphysio.com on 2026-06-13.*
