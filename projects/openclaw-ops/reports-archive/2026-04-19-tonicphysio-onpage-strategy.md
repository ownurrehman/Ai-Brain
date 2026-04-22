## TonicPhysio On-Page SEO Audit — 2026-04-19

**Auditor:** Chaos, Senior Technical SEO Auditor — Rank Ray  
**Target:** tonicphysio.com  
**Competitor:** mexphysio.com (Milton, ON physiotherapy market)  
**Method:** Live crawl + source HTML analysis. All findings cite specific URLs, elements, and copy.

---

### Critical Issues (P0)

**1. [https://tonicphysio.com/] Title tag uses "CA" instead of "ON" / "Ontario" — killing local search intent**
- **Found:** `<title>Tonic Physiotherapy and Rehabilitation Centre in Milton, CA</title>`
- **Issue:** "CA" is the ISO country code (Canada), not the province. Searchers type "physiotherapy Milton Ontario" or "Milton ON" — never "Milton, CA". This title is leaking primary local keyword relevance. MexPhysio's title is "Best Physiotherapy in Milton, Ontario - MEX Physio" which directly matches search intent.
- **Fix:** Change to `Tonic Physiotherapy & Rehabilitation Centre in Milton, Ontario | Tonic Physio` — include "Ontario" explicitly, append brand.

**2. [https://tonicphysio.com/] Meta description lacks "Milton, Ontario" and primary service keywords**
- **Found:** `<meta name="description" content="Expert physiotherapy and rehab services at Tonic Physio. Move Better and Live Better with personalized care tailored to your needs." />`
- **Issue:** Missing "Milton", "Ontario", "pain relief", "injury" — the terms users actually search. MexPhysio's description: "At MEX Physio, our team of therapists provides personalized physiotherapy in Milton, ON to help you heal your pain. Schedule a free consult today!" — includes city, province abbreviation, pain keyword, and CTA.
- **Fix:** Rewrite to: `"Expert physiotherapy in Milton, Ontario — pain relief, injury rehab, and personalized care at Tonic Physio. Move better, live better. Book your appointment today."`

**3. [https://tonicphysio.com/] H4 typo "Goolge Maps" — eroding trust and E-E-A-T signals**
- **Found:** `<h4>Find Us On Goolge Maps</h4>`
- **Issue:** "Goolge" is a misspelling of "Google". Visible on the homepage. Undermines credibility for both users and quality raters.
- **Fix:** Change to "Find Us On Google Maps"

**4. [https://tonicphysio.com/] LocalBusiness schema URL points to Google Maps, not the website**
- **Found:** `"url":"https://maps.google.com/?cid=8607105444028839468"` in the LocalBusiness JSON-LD block.
- **Issue:** The `url` property in LocalBusiness schema should point to the business website (`https://tonicphysio.com/`), not a Google Maps CID URL. This confuses search engines about the canonical entity and may cause knowledge panel issues.
- **Fix:** Change `"url"` to `"https://tonicphysio.com/"`. Add the Maps CID as a `sameAs` entry instead.

**5. [https://tonicphysio.com/] No geo meta tags — missing local signal**
- **Found:** No `geo.region`, `geo.placename`, `geo.position`, or `ICBM` meta tags on homepage.
- **Issue:** MexPhysio has all four: `<meta name="geo.region" content="CA-ON">`, `<meta name="geo.placename" content="Milton">`, `<meta name="geo.position" content="61.066692;-107.991707">`, `<meta name="ICBM" content="61.066692, -107.991707">`. These reinforce local relevance.
- **Fix:** Add all four geo meta tags to `<head>`. Use correct Milton, ON coordinates (43.5173, -79.8894 — not the incorrect 61.0, -107.9 that MexPhysio uses, which appears to be a Yukon coordinate).

---

### Warnings (P1)

**6. [https://tonicphysio.com/services/] H1 is just "Services" — no keyword context**
- **Found:** `<h1>Services</h1>`
- **Issue:** Generic H1 misses opportunity to target "Physiotherapy & Rehabilitation Services in Milton, Ontario". MexPhysio uses "What We Treat" for their services page H1 which is slightly better for user intent, but still generic.
- **Fix:** Change H1 to "Physiotherapy & Rehabilitation Services in Milton, Ontario"

**7. [https://tonicphysio.com/physiotherapy-in-milton/back-and-neck-pain/] Title uses dash instead of pipe, no "Ontario"**
- **Found:** `<title>Back and Neck Pain in Milton - Tonic Physio</title>`
- **Issue:** Inconsistent title separator (dash vs. pipe on other pages). Missing "Ontario" and no condition keyword like "Treatment" or "Relief". MexPhysio's equivalent page title: "Sciatica & Back Pain Relief Milton" — shorter, keyword-rich.
- **Fix:** Standardize to: `Back and Neck Pain Treatment in Milton, Ontario | Tonic Physio`

**8. [https://tonicphysio.com/] Multiple H2s compete for same topic — keyword dilution**
- **Found:**
  - H2: "Start Your Recovery with a Clinic That Truly Cares"
  - H2: "Our Physiotherapy and Rehabilitation Services"
  - H2: "Our Expert Therapists"
  - H2: "Why Choose Tonic Physio?"
  - H2: "An award-winning Physiotherapy Centre in Milton, CA"
- **Issue:** "Why Choose Tonic Physio?" and "An award-winning Physiotherapy Centre in Milton, CA" are redundant. The second one also repeats the "CA" error from the title tag. 5 H2s on a homepage is fine structurally, but the content overlap dilutes the "physiotherapy Milton Ontario" signal.
- **Fix:** Consolidate the last two H2s. Change "An award-winning Physiotherapy Centre in Milton, CA" → "An Award-Winning Physiotherapy Centre in Milton, Ontario". Remove redundancy with the "Why Choose" section.

**9. [https://tonicphysio.com/] No OG:image on homepage — wasted social sharing opportunity**
- **Found:** `<meta property="og:image" content="https://tonicphysio.com/wp-content/uploads/2025/01/Brenda-Azzopardi-about-us.jpg" />`
- **Issue:** The OG image is a staff portrait (800×1600), not a branded clinic image or hero shot. When shared on Facebook/LinkedIn, this will show a person's face instead of the clinic. Should be a landscape branded image (1200×630 recommended).
- **Fix:** Create and set a proper branded OG image showing the clinic exterior/interior or logo, 1200×630px.

**10. [https://mexphysio.com/sitemap_index.xml] Rogue staging subdomain in MexPhysio sitemap — but TonicPhysio should audit its own for similar issues**
- **Found in MexPhysio sitemap:** `https://herostencil2k19.ythzzv9z-liquidwebsites.com/page-sitemap.xml` — a staging domain leaking into the production sitemap.
- **Lesson for TonicPhysio:** Audit your own sitemap_index.xml and sub-sitemaps for any staging/dev URLs. Currently TonicPhysio's sitemap looks clean (4 sub-sitemaps: post, page, category, author), but verify no test URLs exist in page-sitemap.xml.

**11. [https://tonicphysio.com/sitemap_index.xml] Missing image sitemaps**
- **Found:** Only post-sitemap, page-sitemap, category-sitemap, and author-sitemap exist.
- **Issue:** No dedicated image sitemap. MexPhysio's post-sitemap.xml includes `<image:image>` and `<image:loc>` for each blog post, giving Google explicit image discovery signals.
- **Fix:** Enable Yoast's image sitemap feature or add image entries to existing page/post sitemaps.

**12. [https://tonicphysio.com/] LiteSpeed Cache delay may cause CWV issues on interaction**
- **Found:** LiteSpeed Cache 7.8.1 with deferred JS loading (`litespeed/javascript` type) and lazy-loaded images.
- **Issue:** While LiteSpeed caching is good for TTFB, the aggressive JS deferral pattern (wait for user interaction) may cause high INP (Interaction to Next Paint) if critical interactivity scripts load late. MexPhysio uses NitroPack which has similar aggressive optimization but may handle it differently.
- **Fix:** Test Core Web Vitals via PageSpeed Insights. If INP > 200ms, exclude critical booking/interaction scripts from LiteSpeed deferral.

---

### Info (P2)

**13. [https://tonicphysio.com/robots.txt] Schemamap line is non-standard**
- **Found:** `Schemamap: https://tonicphysio.com/wp-json/yoast/v1/schema-aggregator/get-xml`
- **Note:** `Schemamap` is not a recognized robots.txt directive. Google ignores it. Not harmful, but not useful either. Leave as-is — Yoast generates it.

**14. [https://tonicphysio.com/robots.txt] Allow-all robots.txt is fine but minimal**
- **Found:** `User-agent: * Disallow:` — fully open.
- **Note:** This is acceptable for a small business site. No sensitive paths are blocked. Consider adding a `Crawl-delay` if bot traffic becomes an issue, but not currently needed.

**15. [https://tonicphysio.com/] Site uses Speculation Rules for prefetch**
- **Found:** `<script type="speculationrules">` with conservative prefetch rules.
- **Note:** Good for navigation speed. Keep this — it's a modern performance feature Google supports.

**16. [https://tonicphysio.com/] Comprehensive SiteNavigationElement schema**
- **Found:** Full SiteNavigationElement JSON-LD with all 38+ nav links mapped.
- **Note:** Good implementation. MexPhysio also has this but with fewer entries. TonicPhysio's is more thorough.

**17. [https://tonicphysio.com/] MedicalOrganization schema type is good**
- **Found:** `"@type":"MedicalOrganization"` with `sameAs` links to TikTok, Facebook, Instagram, LinkedIn.
- **Note:** This is more specific than MexPhysio's generic `Organization` type. Good for health-related E-E-A-T signals.

**18. [https://tonicphysio.com/] SearchAction schema present**
- **Found:** `"potentialAction":{"@type":"SearchAction","target":"https://tonicphysio.com?s={search_term_string}"}`
- **Note:** Good. MexPhysio also has this. Both sites support sitelinks search box potential.

**19. [https://tonicphysio.com/] Blog section ("Health Hub") with recent posts**
- **Found:** 3 recent blog posts displayed on homepage, tagged with relevant keywords (e.g., "physiotherapy-in-milton", "registered-massage-therapy").
- **Note:** Active blog (posts from March-April 2026) is a positive content freshness signal. MexPhysio's latest blog post was from March 2026 as well but content was last significantly updated in 2019-2020.

---

### Competitor Comparison: MexPhysio.ca → MexPhysio.com

**What MexPhysio Does Better:**

1. **Title tag local keywords:** "Best Physiotherapy in Milton, Ontario" — exact match for "physiotherapy Milton Ontario". TonicPhysio says "Milton, CA" which is ambiguous and doesn't match local search patterns.

2. **Meta description with CTA:** MexPhysio includes "Schedule a free consult today!" — a direct call-to-action in the SERP snippet. TonicPhysio's description has no CTA.

3. **Geo meta tags:** MexPhysio has `geo.region`, `geo.placename`, `geo.position`, `ICBM`. TonicPhysio has none.

4. **Google site verification:** MexPhysio has `google-site-verification` meta tags. TonicPhysio doesn't expose these in source (may still be verified via DNS, but having them in-page is belt-and-suspenders).

5. **Dedicated sitemap for custom post types:** MexPhysio has `faq-sitemap.xml`, `testimonial-sitemap.xml`, `location-sitemap.xml`, `our_staff-sitemap.xml`, `body_parts-sitemap.xml`. This signals rich content structure. TonicPhysio only has standard post/page/category/author sitemaps.

6. **Multi-location structure:** MexPhysio has separate location pages (Oakville, Milton) in their sitemap. This gives them a location-sitemap.xml and signals multi-clinic authority.

7. **Service page content depth:** MexPhysio's `/physiotherapy-services/back-pain-relief/` page has long-form, condition-specific content with statistics ("Four out of five Canadians...") and multi-discipline mentions (physiotherapy + chiropractic + massage therapy). This is strong topical authority content.

8. **Google Reviews integration:** MexPhysio prominently displays "4.9 Based on 243 reviews powered by Google" on every page (in sidebar). TonicPhysio shows reviews too (121 reviews, 4.9 rating in schema) but MexPhysio has nearly double the review count (243 vs 121), which is a significant local ranking factor.

**What TonicPhysio Does Better:**

1. **Schema specificity:** TonicPhysio uses `MedicalOrganization` + `LocalBusiness` with full address, phone, reviews, and aggregate rating. MexPhysio uses generic `Organization`. TonicPhysio's schema is richer for health-related E-E-A-T.

2. **Navigation depth:** TonicPhysio has 38+ service/treatment pages organized hierarchically (e.g., `/physiotherapy-in-milton/orthopedic-physiotherapy/`). MexPhysio has a flatter structure. This is both a strength (more indexable content) and a risk (thin pages).

3. **Blog freshness:** TonicPhysio's blog has recent 2026 posts with proper tagging. MexPhysio's blog content appears largely from 2019-2020, with fewer recent updates.

4. **Social presence in schema:** TonicPhysio's `sameAs` includes TikTok, Facebook, Instagram, LinkedIn. MexPhysio doesn't expose social links in schema.

5. **Speculation Rules:** TonicPhysio uses modern `<script type="speculationrules">` for prefetch. MexPhysio doesn't.

6. **URL structure:** TonicPhysio uses logical URL hierarchies (`/physiotherapy-in-milton/back-and-neck-pain/`, `/registered-massage-therapy/deep-tissue-massage-therapy/`). MexPhysio's are flatter (`/physiotherapy-services/back-pain-relief/`). Both work, but TonicPhysio's hierarchy better signals content relationships.

**Gaps to Close:**

1. **Review count:** 121 vs. 243. TonicPhysio needs ~120 more Google reviews to match MexPhysio. This is likely the single biggest local ranking gap.
2. **Title/Meta local keywords:** TonicPhysio must add "Ontario" / "ON" across all page titles and descriptions.
3. **Geo meta tags:** Quick win — add 4 meta tags to every page.
4. **Service page content depth:** TonicPhysio's service pages are shorter than MexPhysio's condition-specific long-form pages. Need to add stats, conditions treated, treatment methodology descriptions, and FAQ sections.
5. **Multi-location signals:** If TonicPhysio plans to expand, create location-specific landing pages early.
6. **Image sitemaps:** Enable in Yoast.
7. **CTA in meta descriptions:** Add "Book your appointment" or "Schedule a free consult" to all meta descriptions.

---

### Top 10 Priority Actions (by Impact)

1. **Fix homepage title tag** — Change "Milton, CA" → "Milton, Ontario" across ALL pages. The homepage title `Tonic Physiotherapy and Rehabilitation Centre in Milton, CA` → `Tonic Physiotherapy & Rehabilitation Centre in Milton, Ontario | Tonic Physio`. Audit every page title for "CA" → "Ontario". **Expected impact:** +15-25% improvement in local keyword relevance. This is the highest-leverage single fix.

2. **Rewrite homepage meta description with local keywords + CTA** — Include "Milton, Ontario", "pain relief", "book your appointment". **Expected impact:** +10-20% CTR improvement in SERPs for branded and near-branded queries.

3. **Fix LocalBusiness schema `url` property** — Point to `https://tonicphysio.com/` instead of Google Maps CID URL. Move Maps URL to `sameAs`. **Expected impact:** Proper knowledge panel attribution, eliminates entity confusion.

4. **Add geo meta tags to site-wide `<head>`** — `geo.region: CA-ON`, `geo.placename: Milton`, `geo.position: 43.5173;-79.8894`, `ICBM: 43.5173, -79.8894`. **Expected impact:** Strengthens local relevance signal; quick technical win.

5. **Fix "Goolge Maps" typo on homepage** — Change H4 text. **Expected impact:** Trust/quality signal; removes a visible error that undermines E-E-A-T.

6. **Accelerate Google Review collection** — Current: 121 reviews (4.9★). Target: 250+ to match/beat MexPhysio's 243. Implement post-appointment SMS/email review requests. **Expected impact:** Direct local ranking factor improvement. Review count is a top 3 local pack ranking signal.

7. **Deepen service page content** — Add 800+ words per service page with: conditions treated, treatment methodology, statistics, patient success stories, FAQ section. Priority pages: `/physiotherapy-in-milton/`, `/physiotherapy-in-milton/back-and-neck-pain/`, `/physiotherapy-in-milton/orthopedic-physiotherapy/`. **Expected impact:** Improved topical authority, longer dwell time, better long-tail keyword coverage.

8. **Add CTA to all meta descriptions** — Every service page description should end with "Book your appointment today" or "Schedule a free consultation". **Expected impact:** +5-15% CTR lift across service pages in SERPs.

9. **Enable image sitemap in Yoast** — Add `<image:image>` entries to post/page sitemaps. **Expected impact:** Faster image indexing, improved image search visibility for clinic/treatment photos.

10. **Standardize title tag format site-wide** — Use `Primary Keyword in Milton, Ontario | Tonic Physio` consistently. Currently: some pages use pipe (`|`), some use dash (`-`), some omit "Ontario". **Expected impact:** Consistent branding in SERPs, improved local keyword consistency.

---

*Audit complete. All findings based on live crawl data collected 2026-04-19. Next steps: implement P0 fixes immediately (1-2 days), P1 within 2 weeks, P2 as part of ongoing optimization.*