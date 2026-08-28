> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[websites/tonicphysio.com/index|tonicphysio.com Hub]] · [[INDEX|🧠 Ai Brain]]

# TonicPhysio.com - Phase 1 & 2 Audit Report
**Date:** 2026-05-04 | **Agent:** Chronos (DeepSeek Specialist)  
**Site:** tonicphysio.com - Physiotherapy & Rehabilitation Centre, Milton, ON

---

## PHASE 1: Technical & On-Page Audit

### 1. Technical Infrastructure (GOOD)
| Metric | Value | Status |
|--------|-------|--------|
| Platform | WordPress + Elementor 4.0.5 | OK |
| PHP Version | 8.3.30 | Good |
| Server | LiteSpeed (Hostinger) | Fast |
| SSL | Valid HTTPS only | Pass |
| www→non-www Redirect | 301 working | Pass |
| HTTP→HTTPS Redirect | 301 working | Pass |
| Robots.txt | Clean, Yoast-generated | Pass |
| Sitemap | Yoast SEO, 4 sitemaps | Pass |
| Mobile Viewport | Set (width=device-width) | Pass |

### 2. Plugins Detected
- **Yoast SEO** (sitemap, robots.txt)
- **Elementor 4.0.5 + Elementor Pro**
- **Schema & Structured Data for WP** (SASWP)
- **Essential Addons for Elementor**
- **ElementsKit Lite**
- **Contact Form 7**
- **Chaty** (WhatsApp chat widget)
- **Google Site Kit 1.177.0**
- Theme: **Sifoxen** (custom theme + child theme)

### 3. Page Speed (CRITICAL)
| Page | Download Size | TTFB |
|------|--------------|------|
| Homepage | **206 KB** | 1.43s |
| Back & Neck Pain | **191 KB** | 1.45s |
| Deep Tissue Massage | ~190 KB | 1.45s (est.) |

**Issue:** Pages are heavy (190-206 KB raw HTML). This is typical for Elementor but will impact Core Web Vitals. TTFB of 1.4s is borderline. Likely no caching plugin active. `Content-Encoding: gzip` not confirmed. Recommend: LiteSpeed Cache plugin (compatible with LiteSpeed server).

### 4. Meta Data Audit

| Page | Title (chars) | Description (chars) | Status |
|------|--------------|---------------------|--------|
| Homepage | 59 | 131 | OK |
| Physiotherapy (parent) | 66 | 153 | **Title >60 chars** |
| Back & Neck Pain | 43 | 137 | **Title too short** |
| Sports Physiotherapy | 45 | 126 | **Title too short** |
| Deep Tissue Massage | 60 (est.) | 153 | OK |
| B-Pulse Pelvic Floor | 57 | 135 | OK |
| RMT (parent) | 51 | 154 | OK |
| Fees | ~30 (est.) | ~100 (est.) | Title likely too short |

**Issues:**
- **Service page titles lack primary keywords at the front.** E.g., "Back and Neck Pain in Milton - Tonic Physio" should front-load "Back and Neck Pain Treatment" or "Physiotherapy for Back and Neck Pain" for better CTR.
- Physiotherapy parent page title has `&amp;` entity instead of `&` - use `&` or pipe.
- Several titles are under 50 chars, missing full keyword opportunity.

### 5. Heading Structure
Service pages reviewed all follow: **1 H1 + 5 H2 + 2 H3** - consistent and well-structured. Good.

### 6. Image SEO
All images have alt attributes. No missing alt tags found on sampled pages. **Good.**

### 7. Schema / Structured Data (MIXED)

**Positive findings:**
- `LocalBusiness` schema on homepage with NAP, reviews (4.9/5, 121 reviews), phone - **excellent for local SEO.**
- `MedicalOrganization` schema with sameAs (social profiles), logo, contact point
- `SiteNavigationElement` schema covering all nav links
- `WebSite` schema with SearchAction
- Plugin: "Essential Addons for Elementor" + SASWP

**Issues identified:**
- **Schema type mismatch:** Homepage uses `LocalBusiness` but service pages should use `MedicalBusiness` or `Physiotherapy` schema. SASWP likely injects the same schema site-wide.
- **Review markup:** Reviews are inline JSON-LD but no `itemReviewed` property linking them to the business entity.
- **No FAQ schema** on the FAQ page (`/faq/`) - missed opportunity for rich results.
- **Service pages lack individual service schema** - e.g., "Back and Neck Pain" page should have `MedicalProcedure` or `MedicalTherapy` schema.

### 8. URL Structure Issues (RESIDUAL FROM APRIL)
Memory records show 3 "ugly URLs" were identified in April with full URLs embedded in slugs:
1. `/guide/https-tonicphysio-com-blog-b-pulse-pelvic-floor-strengthening/`
2. `/guide/https-tonicphysio-com-physiotherapy-for-injury-recovery/`
3. `/guide/https-tonicphysio-com-blog-b-pulse-postpartum-pelvic-weakness/`

These should have been fixed with 301 redirects. **Verify they were resolved.**

### 9. Missing Key Elements
- **No breadcrumb markup** detected (Yoast breadcrumbs likely not configured)
- **No hreflang** (not critical for single-location clinic)
- **No caching plugin** (LiteSpeed Cache strongly recommended for Hostinger/LiteSpeed)
- **No XML sitemap submitted to Google Search Console** (can't verify but recommend checking)

---

## PHASE 2: SERP Analysis & Keyword Targeting

### 1. Competitive Landscape: "physiotherapy milton ontario"

**Top 10 organic competitors:**
| # | Competitor | Domain |
|---|-----------|--------|
| 1 | Revolve Physiotherapy | revolvephysio.com |
| 2 | New Heights Physiotherapy | newheightsphysiotherapy.ca |
| 3 | TrueCare Physiotherapy | truecarephysio.ca |
| 4 | HCRC Milton | hcrc.ca |
| 5 | MEX Physio | mexphysio.com |
| 6 | Pro Fusion Rehab | profusionrehab.com |
| 7 | Altima Physiotherapy | altimaphysiomilton.ca |
| 8 | CBI Health | cbihealth.ca |
| 9 | Escarpment Sports Med | escarpmentsportsmed.ca |
| 10 | PhysioON | physio-on.ca |

**TonicPhysio.com does NOT appear in top 10 for "physiotherapy milton ontario"**. This is the most critical finding.

### 2. Niche Keyword Opportunities

#### TIER 1: High-Intent Local (Immediate Targets)
| Keyword | Competition | Notes |
|---------|------------|-------|
| physiotherapy milton | HIGH | Tonic NOT in top 10 |
| physio clinic milton | HIGH | 10+ established competitors |
| physiotherapy near me milton | HIGH | Map pack dependent |
| sports physiotherapy milton | MED | Tonic has dedicated page |
| massage therapy milton | HIGH | 8+ RMT providers |
| back pain treatment milton | MED | Tonic ranks #5 for back pain SERP |

#### TIER 2: Niche/Procedure Keywords (Opportunity)
| Keyword | Volume (Est.) | Current Visibility | Priority |
|---------|--------------|-------------------|----------|
| B-Pulse pelvic floor milton | LOW | RANKING (page exists) | MAINTAIN |
| deep tissue massage milton | MED | Partially ranking | PUSH |
| shockwave therapy milton | MED | Has dedicated page | PUSH |
| osteopathy milton ontario | MED | 8 competitors | PUSH |
| vestibular rehab milton | LOW | Has blog post | OPTIMIZE |
| TMJ treatment milton physio | LOW-MED | Has dedicated page | PUSH |
| frozen shoulder treatment milton | MED | Has dedicated page | PUSH |
| post-surgery physiotherapy milton | LOW-MED | Has blog post | OPTIMIZE |
| neurological physiotherapy milton | LOW-MED | Has dedicated page | PUSH |
| concussion management milton | LOW | Has blog post | OPTIMIZE |
| plantar fasciitis treatment milton | LOW-MED | Has blog post | OPTIMIZE |
| motor vehicle accident physio milton | MED | Has dedicated page | PUSH |
| WSIB physiotherapy milton | MED | Has dedicated page | PUSH |
| custom orthotics milton | MED | Has dedicated page | PUSH |

#### TIER 3: Long-Tail Informational (Content Gap)
| Keyword | Has Content? |
|---------|-------------|
| how to choose physiotherapy clinic milton | YES (blog post) |
| physiotherapy vs medication for chronic pain | YES (blog) |
| active vs passive physiotherapy | YES (blog) |
| manual osteopathy vs physiotherapy | YES (blog) |
| physiotherapy for forward head posture | YES (blog) |
| physiotherapy for arthritis milton | YES (blog) |
| what injuries does physiotherapy treat | YES (blog) |
| pelvic floor physiotherapy milton | PARTIAL (B-Pulse only) |

### 3. Top Competitor Analysis

**New Heights Physiotherapy** (newheightsphysiotherapy.ca) - #2 position
- Strong condition-specific pages: `/conditions-we-treat/back-pain/`
- Clean URL structure
- Good content depth on condition pages
- "Milton's Top-Rated Physio Clinic" branding

**TrueCare Physiotherapy** (truecarephysio.ca) - #3 position
- Mississauga + Milton dual-location advantage
- Dedicated service pages with city modifiers
- Clear service-to-condition mapping

**HCRC Milton** (hcrc.ca) - #4 position
- Multi-location brand authority
- Pelvic floor specialty (direct competitor to B-Pulse)
- FCAMPT-certified physiotherapists (trust signal)

### 4. Content/Page Gap Analysis

**Pages TonicPhysio HAS that most competitors DON'T:**
- B-Pulse Pelvic Floor Strengthening (unique differentiator)
- Comprehensive therapist bio pages with signatures
- Awards/badges highlighted well (Diamond Winner, Readers Choice)

**Pages competitors HAVE that TonicPhysio is MISSING:**
- **Location/service area pages** (serving Milton + surrounding areas)
- **Insurance-specific landing pages** (Sun Life, Manulife, etc.)
- **Condition-specific FAQ pages** with FAQ schema
- **Patient success stories/testimonials** as dedicated content
- **Virtual/online physiotherapy** offerings (post-COVID differentiator)

### 5. Priority Target Keywords (Top 15)

| # | Target Keyword | Page to Optimize | Est. Volume | Difficulty |
|---|---------------|-----------------|-------------|------------|
| 1 | physiotherapy milton | /physiotherapy-in-milton/ | High | High |
| 2 | physio clinic milton | Homepage + /physiotherapy-in-milton/ | High | High |
| 3 | sports physiotherapy milton | /physiotherapy-in-milton/sports-physiotherapy/ | Medium | Medium |
| 4 | massage therapy milton | /registered-massage-therapy/ | High | High |
| 5 | back pain treatment milton | /physiotherapy-in-milton/back-and-neck-pain/ | Medium | Medium |
| 6 | deep tissue massage milton | /registered-massage-therapy/deep-tissue-massage-therapy/ | Medium | Medium |
| 7 | osteopathy milton | /manual-osteopathy-milton/ | Medium | Medium |
| 8 | shockwave therapy milton | /shockwave-therapy/ | Medium | Medium |
| 9 | frozen shoulder treatment milton | /physiotherapy-in-milton/frozen-shoulder-treatment/ | Medium | Medium |
| 10 | TMJ treatment milton | /tmj-treatment/ | Low-Med | Low |
| 11 | MVA physiotherapy milton | /motor-vehicle-accident-physiotherapy/ | Medium | Medium |
| 12 | WSIB physio milton | /wsib-care-programs/ | Medium | Medium |
| 13 | custom orthotics milton | /custom-orthotics/ | Medium | Medium |
| 14 | pelvic floor physio milton | /b-pulse-pelvic-floor-strengthening/ | Low-Med | Low |
| 15 | neurological physiotherapy milton | /physiotherapy-in-milton/neurological-physiotherapy/ | Low-Med | Low |

---

## PHASE 1 & 2: Recommended Action Plan

### IMMEDIATE FIXES (This Week)
1. **Install LiteSpeed Cache** plugin - leverage Hostinger's LiteSpeed server for page speed (est. 2-3x faster)
2. **Fix homepage title** - Include primary keyword: "Physiotherapy Milton | Tonic Physiotherapy & Rehab Centre"
3. **Add city modifier to all service page titles** - E.g., "Sports Physiotherapy in Milton | Tonic Physio"
4. **Fix Physiotherapy page title** - Remove `&amp;` HTML entity
5. **Add breadcrumb schema** via Yoast (toggle on in settings)
6. **Add FAQ schema** to `/faq/` page
7. **Verify the 3 ugly /guide/ URLs** from April were fixed with 301s

### SHORT-TERM (Next 2 Weeks)
8. **Optimize 5 priority service pages** for target keywords (meta, headings, internal links)
9. **Create "Areas We Serve" page** listing Milton neighborhoods + nearby towns
10. **Add insurance landing pages** (at minimum: "Direct Billing Physiotherapy Milton")
11. **Expand B-Pulse page** to also target "pelvic floor physiotherapy milton" (currently only B-Pulse keyword is in title - add pelvic floor physiotherapy as secondary keyword throughout)
12. **Add service-specific schema** to each service page (MedicalTherapy, MedicalProcedure)
13. **Request/manage Google Business Profile reviews** more aggressively (121 reviews is good, but competitors like New Heights may have more)

### MEDIUM-TERM (Next Month)
14. **Patient testimonial page** with individual success stories + review schema
15. **Condition-focused blog cluster** interlinked to service pages (silent internal linking power)
16. **Local citation audit** - ensure NAP consistency across all directories (Yelp, HealthGrades, RateMDs, etc.)
17. **Google Business Profile optimization** - add services, Q&A, weekly posts

### AEO Notes (Semantic Framework)
- Current schema is fragmented (3 different plugins/types). Recommended: consolidate to Rank Math SEO (already used on other Rank Ray sites) OR configure SASWP to output cleaner, unified schema.
- For AI answer engines: add `MedicalClinic` schema with `medicalSpecialty: Physiotherapy`, `availableService` array, and `areaServed: Milton, ON`
- Add `FAQPage` schema to all service pages with 3-5 relevant Q&As
- Entity optimization: add `sameAs` links to Google Business Profile, HealthGrades, and RateMDs profiles in `MedicalOrganization` schema
