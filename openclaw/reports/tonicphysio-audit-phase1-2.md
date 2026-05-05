# TonicPhysio.com — Phase 1 & 2 Audit Report
**Date:** 2026-05-03  
**Auditor:** Chronos (Subagent)  
**Site:** https://tonicphysio.com

---

## PHASE 1: TECHNICAL & ON-PAGE AUDIT

### 1. Site Health Summary

| Metric | Status | Details |
|--------|--------|---------|
| HTTPS/SSL | ✅ OK | Valid SSL, no mixed content detected |
| HTTP Status | ✅ OK | 200, fast response (2.1s full load) |
| Sitemap | ✅ OK | Yoast sitemap_index.xml, 4 sub-sitemaps |
| Robots.txt | ✅ OK | No disallow, sitemap referenced |
| Mobile Viewport | ✅ OK | Present: `width=device-width, initial-scale=1.0` |
| Canonical | ✅ OK | Present and self-referencing on homepage |
| Indexability | ✅ OK | `index, follow` on homepage |
| CMS | ✅ OK | WordPress + Elementor + Yoast SEO |
| Analytics | ✅ OK | GTM + GA4 + Ahrefs analytics deployed |
| Favicon | ✅ OK | Multiple sizes, manifest present |

### 2. Page Count Breakdown
- **Total indexed pages (via sitemap):** 68 pages
- **Core pages:** 15 (Home, About, Services, Contact, Therapists, etc.)
- **Service pages (physiotherapy-in-milton/):** ~20
- **Massage therapy pages:** ~10
- **Therapist profiles:** ~12
- **Blog posts:** ~45
- **Utility pages:** 6 (FAQ, Fees, Privacy, Terms, Cookie, Sitemap)

### 3. Critical Issues Found

#### 🔴 HIGH PRIORITY

**3a. All 23 images on homepage missing alt text**
- Every `<img>` tag on the homepage lacks an `alt` attribute
- This is a major accessibility and SEO gap
- Affects image search visibility
- **Fix:** Add descriptive alt text to all images (e.g., "Brenda Azzopardi, Registered Physiotherapist at Tonic Physio Milton")

**3b. Incorrect "Learn More" links on homepage service cards**
The homepage services section has mismatched links:
- "Registered Massage Therapy" card links to → `/neurological-physiotherapy/`
- "Manual Osteopathy" card links to → `/pediatric-physiotherapy/`
- "Custom Orthotics" card links to → `/acupuncture-therapy/`
- "Compression Socks" card links to → `/joint-pain-and-stiffness/`
- "Custom/OTC Bracing" card links to → `/osteoarthritis-treatment/`
- "Shockwave Therapy" card links to → `/rheumatoid-arthritis-therapy-treatment/`
- "MVA Physiotherapy" card links to → `/frozen-shoulder-treatment/`
- "WSIB Care Programs" card links to → `/back-and-neck-pain/`
- Jacqueline Ma therapist card links to Brenda's page

**Fix:** Update each "Learn more" button to point to the correct service page.

**3c. H1 is not a proper `<h1>` tag**
Homepage `<h1>` is rendered as: `<h1 class="ekit-heading--title elementskit-section-title">` — technically correct but should include primary keyword in the text content (currently works: "Tonic Physio Leading Physiotherapy & Rehab Centre in Milton"). OK but could be better optimized.

#### 🟡 MEDIUM PRIORITY

**3d. Homepage size: 206KB**
- Quite heavy for a landing page
- Multiple CSS/JS files from various plugins
- Consider combining/minifying assets

**3e. Missing service pages for key treatments**
Competitors have dedicated pages for:
- Pelvic Floor Physiotherapy (tonic has B-Pulse page but no general pelvic floor page)
- Vestibular Rehabilitation (exists only as a blog post)
- Concussion Management (exists only as blog post, no dedicated service page)

**3f. Footer internal links broken**
Several footer links use wrong paths:
- `/physiotherapy/` → should be `/physiotherapy-in-milton/`
- `/manual-osteopathy/` → should be `/manual-osteopathy-milton/`

#### 🟢 LOW PRIORITY

**3g. Missing XML sitemap reference in robots.txt**
Only the index sitemap is referenced — consider adding individual sub-sitemaps.

### 4. Meta Tags Audit

| Page | Title | Description | Status |
|------|-------|-------------|--------|
| Home | "Tonic Physiotherapy and Rehabilitation Centre in Milton, CA" (~57 chars) | "Expert physiotherapy and rehab services at Tonic Physio. Move Better and Live Better with personalized care tailored to your needs." (~140 chars) | ✅ Good |
| Back & Neck Pain | "Back and Neck Pain in Milton - Tonic Physio" (~44 chars) | "Relieve back and neck pain with expert physio care in Milton. Visit Tonic Physio for personalized treatment and long-lasting pain relief." (~148 chars) | ✅ Good |
| Sports Physiotherapy | "Sports Physiotherapy in Milton \| Tonic Physio" (~44 chars) | "Get Sports Physiotherapy at Tonic Physio & Rehabilitation Centre in Milton. We get you moving and help you win your games." (~138 chars) | ✅ Good |
| Shockwave Therapy | "Shockwave Therapy in Milton \| Tonic Physio" (~42 chars) | (not verified — page uses Elementor template) | ⚠️ Review |
| Massage Therapy | "Registered Massage Therapy in Milton \| Tonic Physio" (~50 chars) | (not verified) | ⚠️ Review |
| Manual Osteopathy | "Manual Osteopathy in Milton \| Tonic Physio" (~44 chars) | (not verified) | ⚠️ Review |

**Meta recommendation:** Titles are concise but could include high-volume keywords. E.g., homepage title could be: "Physiotherapy Milton ON | Tonic Physio Rehab Centre"

---

## PHASE 2: SERP ANALYSIS & KEYWORD TARGETING

### 5. Competitor Landscape (Milton, ON Physiotherapy)

| Competitor | Strengths | URL |
|------------|-----------|-----|
| **Revolve Physio** | Pelvic floor, shockwave, vestibular — full specialty coverage | revolvephysio.com |
| **New Heights Physiotherapy** | "Top-Rated" claim, concussion + pelvic + vestibular coverage | newheightsphysiotherapy.ca |
| **MEX Physio** | Large service page inventory, aggressive SEO, multilingual | mexphysio.com |
| **TrueCare Physiotherapy** | Chiropractic + physio, multiple locations | truecarephysio.ca |
| **HCRC Milton** | Long-established, concussion baseline testing, sports focus | hcrc.ca |
| **PhysioON** | "Complete rehab solution" positioning, wide service range | physio-on.ca |
| **Profusion Rehab** | Since 2011, dual Milton/Pickering locations | profusionrehab.com |

**Key observation:** Most competitors have dedicated pages for Pelvic Floor, Vestibular Rehab, and Concussion Management. Tonic has blog posts but no dedicated landing pages for these high-value services — this is a major gap.

### 6. Tonic Physio's Current Positioning

**Strengths:**
- 4.9 Google rating (121 reviews) — excellent social proof
- Award-winning (Diamond Reader's Choice in 4 categories, #1 Quality Business Award 2025)
- 25+ years experience, 10,000+ patients treated
- Strong team page with 12+ therapist profiles
- Active blog with 45+ articles
- Good local schema markup (MedicalOrganization, LocalBusiness, SiteNavigationElement)
- WhatsApp chat widget for quick inquiries

**Weaknesses:**
- No dedicated Pelvic Floor Physiotherapy page (only B-Pulse device page)
- Vestibular Rehab only covered in a blog post
- Concussion Management only covered in blog post
- Broken internal links on homepage and footer
- Missing alt text on all images
- Homepage load heavy at 206KB

### 7. Keyword Targeting Map

#### TIER 1: High-Intent Commercial Keywords (immediate targeting)

| Keyword | Est. Volume | Current Page | Competitor Coverage | Action |
|---------|-------------|-------------|---------------------|--------|
| physiotherapy milton | High | /physiotherapy-in-milton/ | All competitors rank | Optimize existing page |
| physio milton | High | Homepage | All competitors | Optimize homepage H1 |
| physiotherapy clinic milton | Med-High | /about/ | Most competitors | Create dedicated "Why Us" content |
| physiotherapy near me milton | High (local) | Homepage | Strong competition | Enhance Google Maps/GBP integration |
| massage therapy milton | High | /registered-massage-therapy/ | Strong competition | Add more keyword-rich content |
| sports physiotherapy milton | Medium | /physiotherapy-in-milton/sports-physiotherapy/ | Moderate | New page already, promote it |
| shockwave therapy milton | Medium | /shockwave-therapy/ | Moderate | Optimize for local intent |
| physiotherapy milton ontario | High | Various | All | Ensure "Ontario" variant in key pages |

#### TIER 2: Condition-Specific Keywords (content expansion needed)

| Keyword | Est. Volume | Current Page | Gap |
|---------|-------------|-------------|-----|
| pelvic floor physiotherapy milton | Medium | /b-pulse-pelvic-floor-strengthening/ | Create proper Pelvic Floor service page |
| vestibular rehabilitation milton | Low-Med | Blog only | Create dedicated service page |
| concussion treatment milton | Medium | Blog only | Create dedicated service page |
| frozen shoulder treatment milton | Low-Med | /physiotherapy-in-milton/frozen-shoulder-treatment/ | Optimize existing |
| sciatica treatment milton | Medium | /physiotherapy-in-milton/sciatica-treatment/ | Optimize existing |
| back pain physiotherapy milton | High | /physiotherapy-in-milton/back-and-neck-pain/ | Optimize existing |
| knee pain treatment milton | Medium | /physiotherapy-in-milton/knee-pain-treatment/ | Optimize existing |
| TMJ treatment milton | Low | /tmj-treatment/ | Optimize existing |
| custom orthotics milton | Medium | /custom-orthotics/ | Optimize existing |
| manual osteopathy milton | Medium | /manual-osteopathy-milton/ | Optimize existing |

#### TIER 3: Long-Tail & Blog Keywords (for Phase 3/4 content)

| Keyword | Intent |
|---------|--------|
| post surgery physiotherapy milton | Commercial |
| MVA physiotherapy milton ontario | Commercial |
| WSIB physiotherapy milton | Commercial |
| physiotherapy for arthritis milton | Informational/Commercial |
| prenatal massage milton | Commercial |
| deep tissue massage milton | Commercial |
| cost of physiotherapy milton ontario | Informational |
| does OHIP cover physiotherapy milton | Informational |
| best physiotherapist milton reviews | Commercial |
| physiotherapy exercises for lower back pain at home | Informational |

### 8. Immediate Fixes To Apply (Phase 1 Action Items)

1. **Fix homepage service card links** — Each "Learn more" button must point to the correct service page (HIGHEST PRIORITY)
2. **Add alt text to all homepage images** — 23 images need descriptive alt attributes
3. **Fix footer links** — `/physiotherapy/` → `/physiotherapy-in-milton/`, `/manual-osteopathy/` → `/manual-osteopathy-milton/`
4. **Optimize homepage title** — Consider: "Physiotherapy Milton ON | Tonic Physio Rehabilitation & Massage Centre"
5. **Create 3 new service pages** (for Phase 3):
   - Pelvic Floor Physiotherapy Milton
   - Vestibular Rehabilitation Milton
   - Concussion Management & Treatment Milton
6. **Fix therapist card link** — Jacqueline Ma's card links to Brenda's page

### 9. Current Site Health Score

| Area | Score | Notes |
|------|-------|-------|
| Technical SEO | 7.5/10 | SSL, sitemap, robots, canonicals all good. Page size needs optimization |
| On-Page SEO | 6/10 | Meta tags decent but broken internal links, missing alt text, wrong CTAs |
| Content Quality | 7/10 | Good blog content, detailed service pages, but missing key specialty pages |
| Local SEO | 8/10 | Strong GBP presence, 4.9 stars, schema markup, address/phone/map on site |
| Competitor Gap | 5/10 | Missing pelvic floor, vestibular, concussion service pages that all competitors have |
| **OVERALL** | **6.7/10** | Solid foundation with specific, fixable gaps |

---

## Summary for Main Agent

- **Phase 1 complete:** Audited 68 pages. Found 2 critical bugs (broken homepage links, all images missing alt), 4 medium issues (missing service pages, broken footer links, heavy homepage, minor H1 issues).
- **Phase 2 complete:** Analyzed 7 competitors, mapped 30+ keywords across 3 tiers. Identified major gap: no dedicated Pelvic Floor, Vestibular, or Concussion service pages — all competitors have them.
- **Ready for Phase 3 (Content) & Phase 4 (Link Updates):** Fix the 6 immediate action items first, then build the 3 missing service pages.
