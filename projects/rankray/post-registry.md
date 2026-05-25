# RankRay Post Registry

**Snapshot Date:** 2026-05-13
**Total Published:** 113
**Total Drafts:** 7
**Total Trashed:** 10

---

## Quick Stats

| Metric | Value |
|--------|-------|
| Total Published Posts | 113 |
| Total Drafts | 7 |
| Total Trashed | 10 |
| Total Posts (all) | 130 |
| With Featured Image Issues | 1 (21796 - AI Automation draft) |
| Need Internal Links | 2 (21695 partial fix applied, 22184 pending) |

---

## Today's Fixes (2026-05-13)

### Trashed (properly, not deleted)
1. [22088] Duplicate Real Estate post (`/local-seo-real-estate-map-pack-2/`) → redirect to 22007
2. [7267] 2024 old draft "Digital Marketing and Affiliate Marketing" → redirect to digital marketing guide
3. [9772] 2024 old draft "Importance of SEO for Small Businesses" → redirect to why-seo-is-important

### Author Fixes (15 → 21 Own-ur-Rehman)
- [20530] Content Refresh Strategy
- [20539] Programmatic SEO Guide
- [20465] Entity SEO Guide

### Category Fixes
- [21783] Generative Engine Optimization → [455] AI and GEO (was default [1])
- [21796] AI Automation draft → [455, 452] AI and GEO + Digital Marketing (was default [1])
- [22088] Duplicate trashed → [449, 450] Local SEO + SEO Strategy (before trash)
- [7267] Trashed → [453, 452] Content Marketing + Digital Marketing (before trash)
- [9772] Trashed → [445, 450] SEO Fundamentals + SEO Strategy (before trash)

### Yoast Meta Fixes (23 posts updated)
Titles trimmed to <60 chars, descriptions to <160 chars, brand "Rank Ray" added:
- [22012] Dentists (title 61→55c, desc 161→154c + brand)
- [12055] SEO Timeline (title 63→55c + brand)
- [9272] Google Leak (title 65→55c + brand)
- [21796] AI Automation (title 61→55c, desc + brand)
- [21999] Franchise SEO (title 69→55c + brand)
- [22184] Franchise SEO Pillar (title 76→55c, desc 164→160c + brand)
- [22015] Law Firms Map Pack (desc 164→160c + brand)
- [22027] B2B Content Marketing (desc + brand)
- [12185] AI Changed SEO (desc + brand)
- [5399] SEO Content Synergy (desc + brand)
- [21783] GEO Strategy (desc + brand)
- [20468] Information Gain (desc + brand)
- [20472] B2B SEO Guide (desc + brand)
- [20470] International SEO (desc + brand)
- [8717] Keyword Density (desc + brand)
- [20505] Content Cluster (desc + brand)
- [20497] Entity-Based SEO (desc + brand)
- [20414] On-Page SEO (desc + brand)
- [9245] Paid + Organic (desc + brand)
- [22030] Brand Recognition (desc + brand)
- [22024] Ecommerce Links (desc + brand)
- [22021] Product Schema (desc + brand)
- [20392] Link Building (desc + brand)

### Internal Links Added
- [21695] Affiliate Programs: Added 2 service page links (affiliate marketing → DM services, conversion rate → CRO)

### Redirects Created
- `/local-seo-real-estate-map-pack-2/` → `/local-seo-real-estate-map-pack/`
- `/digital-marketing-and-affiliate-marketing/` → `/digital-marketing-ultimate-guide/`
- `/importance-of-seo-for-small-businesses/` → `/why-seo-is-important/`

---

## Drafts Requiring Action

| ID | Title | Date | Slug | Issue | Priority |
|----|-------|------|------|-------|----------|
| 22184 | How Franchise SEO Works: Scaling Local Rankings | 2026-05-13 | franchise-seo-pillar | YOAST_TITLE_LONG, YOAST_DESC_LONG, LOW_LINKS | P1 |
| 22030 | How Brand Recognition Boosts Your Google Rankings | 2026-05-12 | brand-seo-rankings-boost | YOAST_DESC_LONG, NO_BRAND | P2 (fix pending) |
| 22024 | How to Build Internal Links for 10,000+ Product Pages | 2026-05-12 | ecommerce-internal-links-10000-products | YOAST_DESC_LONG | P2 (fix pending) |
| 22021 | How to Optimize Product Pages for Rich Results | 2026-05-12 | product-page-schema-rich-results | YOAST_DESC_LONG | P2 (fix pending) |
| 21796 | AI Automation for Marketing: Workflow Guide for Agencies | 2026-05-07 | ai-automation-marketing-workflow | NO_FEATURED_IMAGE, TOO_SHORT(1706w) | P0 |
| 20530 | Content Refresh Strategy: How to Update Old Blog Posts | 2026-04-02 | content-refresh-strategy | EM_DASH | P2 |
| 21695 | Top Affiliate Programs on Impact Radius | 2026-05-12 (was pub) | top-affiliate-programs | TOO_SHORT, LOW_LINKS, NO_SUMMARY | P1 |
| 22720 | SEO Company Dubai: UAE Business Rankings | 2026-05-20 | seo-company-dubai-uae-business-rankings | DRAFT - transactional blog | P0 |

## New Location Pages (2026-05-20)

| ID | Title | Slug | City | Status | Words |
|----|-------|------|------|--------|-------|
| 22719 | SEO Agency in Sydney | seo-agency-sydney | Sydney, Australia | DRAFT | ~1800 |
| 22721 | SEO Agency in London | seo-agency-london | London, UK | DRAFT | ~1800 |

## Service Pages Expanded (2026-05-20)

| ID | Title | Before | After | Status |
|----|-------|--------|-------|--------|
| 13708 | Google Ads Management Services | 889w | 1328w | ✅ Updated |
| 13779 | YouTube Advertising Services | 847w | 1243w | ✅ Updated |
| 13810 | Lead Generation Services | 826w | 1242w | ✅ Updated |



---

## Internal Link Fix Applied (2026-05-20)

### Updated Pages (46 ACF pages)
All ACF service pages received contextual internal links via REST API. Each page now links to 4-8 relevant service pages from its portfolio_paragraph, h2_paragraph, and services_paragraph fields.

| Slug | ID | Before | After | Status |
|------|-----|--------|-------|--------|
| ai-automation | 18073 | 8 | 8 | OK |
| branding | 11530 | 0 | 5 | ✅ Fixed |
| content-marketing | 402 | 0 | 7 | ✅ Fixed |
| content-writing | 14111 | 0 | 5 | ✅ Fixed |
| conversion-rate-optimization | 14084 | 0 | 5 | ✅ Fixed |
| copywriting-services | 14142 | 1 | 3 | ✅ Fixed |
| custom-website-design | 15037 | 0 | 5 | ✅ Fixed |
| digital-marketing-services | 2593 | 0 | 2 | ✅ Fixed |
| digital-marketing-strategy-development | 11366 | 0 | 4 | ✅ Fixed |
| email-marketing-services | 14280 | 0 | 5 | ✅ Fixed |
| enterprise-digital-marketing | 12316 | 0 | 5 | ✅ Fixed |
| enterprise-ppc-marketing | 13830 | 0 | 4 | ✅ Fixed |
| enterprise-seo | 13228 | 4 | 4 | OK |
| enterprise-seo-audit-services | 13348 | 0 | 5 | ✅ Fixed |
| enterprise-social-media-marketing | 13434 | 0 | 5 | ✅ Fixed |
| franchise-digital-marketing | 12281 | 0 | 4 | ✅ Fixed |
| franchise-ppc-marketing | 13876 | 0 | 4 | ✅ Fixed |
| franchise-seo | 13196 | 1 | 5 | ✅ Fixed |
| franchise-seo-audit-services | 13314 | 0 | 5 | ✅ Fixed |
| franchise-social-media-marketing | 13416 | 0 | 3 | ✅ Fixed |
| google-ads-management-services | 13708 | 1 | 4 | ✅ Fixed |
| haro-link-building | 14914 | 4 | 4 | OK |
| link-building | 14723 | 4 | 4 | OK |
| linkedin-advertising | 14498 | 4 | 4 | OK |
| local-seo | 12502 | 3 | 3 | OK |
| media-production-services | 14337 | 3 | 3 | OK |
| outbound-marketing | 14408 | 4 | 4 | OK |
| pay-per-click-ppc | 2676 | 1 | 3 | ✅ Fixed |
| product-photography | 14812 | 4 | 4 | OK |
| search-engine-marketing | 13643 | 0 | 3 | ✅ Fixed |
| search-engine-optimization-seo | 11148 | 4 | 5 | OK |
| semantic-seo | 19892 | 3 | 6 | ✅ Fixed |
| seo-audit-services | 13291 | 1 | 3 | ✅ Fixed |
| social-media-advertising | 14367 | 8 | 8 | OK |
| social-media-brand-management | 13632 | 0 | 3 | ✅ Fixed |
| social-media-management | 13678 | 0 | 3 | ✅ Fixed |
| social-media-marketing | 11424 | 0 | 3 | ✅ Fixed |
| video-production-services | 14690 | 4 | 4 | OK |
| video-testimonial-services | 14661 | 0 | 4 | ✅ Fixed |
| web-design | 2439 | 0 | 3 | ✅ Fixed |
| web-development | 2525 | 0 | 6 | ✅ Fixed |

### Elementor Pages (4 — CANNOT FIX via REST API)
| Slug | ID | Status | Notes |
|------|-----|--------|-------|
| free-seo-tools | 18241 | 0 links | Elementor JSON data overrides content.raw |
| seo-audit | 20293 | 0 links | Elementor JSON data overrides content.raw |
| ai-visibility-audit | 19999 | 0 links | Elementor JSON data overrides content.raw |
| generative-engine-optimization-geo | 13247 | 0 links | Elementor JSON data overrides content.raw |

**Warning:** These 4 pages use Elementor's proprietary JSON storage for content. Updating via REST API's `content` field has no effect because Elementor renders from meta, not from standard WordPress content. Manual Elementor editing required to add internal links. Changing Elementor meta via REST risks breaking entire page layout.




## Service Page Content Rewrite (2026-05-20)

**46 ACF service pages completely rewritten.** All now have:
- h1_paragraph that sells outcome (not just names service)
- h2_paragraph_1 that names buyer pain, not process
- h3 paragraphs that sell authority and measurable growth
- h3_portfolio_paragraph with specific "Stop Wasting Budget" positioning
- Why Us boxes with concrete claims (certifications, targeting logic)
- Answers that handle objections honestly (timeline, budget, results)
- Form paragraph requesting proposal with 48-hour commitment
- 3+ contextual internal links per page
- Zero mentions of "leading," "industry-leading," "world-class"

**4 Elementor pages remain unchanged** (content locked in Elementor JSON):
- free-seo-tools (18241)
- seo-audit (20293) 
- ai-visibility-audit (19999)
- generative-engine-optimization-geo (13247)

These require manual Elementor editing to add links + convert copy.

## Remaining SEO Location Gaps

Cities with DM page but NO SEO page:
- Islamabad → missing seo-agency-islamabad
- Karachi → missing seo-agency-karachi
- Lahore → missing seo-agency-lahore
- Rawalpindi → missing seo-agency-rawalpindi

---

## Trashed Posts

| ID | Title | Date | Slug | Redirect Target |
|----|-------|------|------|-----------------|
| 22088 | How Real Estate Agents Dominate the Google Map Pack (duplicate) | 2026-05-11 | local-seo-real-estate-map-pack-2__trashed | /local-seo-real-estate-map-pack/ |
| 7267 | Digital Marketing and Affiliate Marketing Comparis… | 2024-10-10 | digital-marketing-and-affiliate-marketing__trashed | /digital-marketing-ultimate-guide/ |
| 9772 | What Is The Importance Of SEO For Small Busin… | 2024-07-12 | importance-of-seo-for-small-businesses__trashed | /why-seo-is-important/ |
| 9423 | Hiring An In-House SEO Expert vs an SEO Agency? | 2024-07-15 | in-house-seo-expert-vs-an-seo-agency__trashed | None |
| 9255 | Ways To Make The Best Of The Five Latest Google Ad | 2024-06-24 | best-of-the-five-latest-google-ads-features__trashed | None |
| 5371 | How To Invest In Digital Marketing | 2024-01-26 | how-to-invest-in-digital-marketing__trashed | None |
| 6626 | Digital Marketing Mistakes To Avoid Next year | 2023-12-06 | digital-marketing-mistakes-to-avoid-next-year__trashed | None |
| 6692 | How To Create A Digital Marketing Strategy? | 2022-05-13 | how-to-create-a-digital-marketing-strategy__trashed | None |
| 5412 | Top 300 Free Business Directories List | 2021-10-11 | free-business-directories-list-to-get-backlinks__trashed | None |
| 5415 | TOP 200 FREE ARTICLE SUBMISSION SITES | 2021-09-19 | 200-free-article-submission-sites-to-help-you-get-traffic__trashed | None |

**Note:** Post 21979 was permanently deleted (not trashed) — this was a violation. No further deletions without explicit approval.

---

## Recent Published Posts (Last 30)

| ID | Title | Date | Slug | Live URL |
|----|-------|------|------|----------|
| 22030 | How Brand Recognition Boosts Your Google Rankings (Bran | 2026-05-12 | brand-seo-rankings-boost | [Live](https://rankray.com/brand-seo-rankings-boost/) |
| 22027 | How B2B Content Marketing Generates Leads Through Thoug | 2026-05-12 | b2b-content-marketing-thought-leadership | [Live](https://rankray.com/b2b-content-marketing-thought-leadership/) |
| 22024 | How to Build Internal Links for 10,000+ Product Pages ( | 2026-05-12 | ecommerce-internal-links-10000-products | [Live](https://rankray.com/ecommerce-internal-links-10000-products/) |
| 22021 | How to Optimize Product Pages for Rich Results (Schema | 2026-05-12 | product-page-schema-rich-results | [Live](https://rankray.com/product-page-schema-rich-results/) |
| 22018 | How to Rank Shopify Category Pages on Google (Without P | 2026-05-12 | shopify-category-page-seo | [Live](https://rankray.com/shopify-category-page-seo/) |
| 22015 | How Law Firms Rank #1 in the Map Pack for #8220;Lawyer | 2026-05-12 | local-seo-law-firms-map-pack | [Live](https://rankray.com/local-seo-law-firms-map-pack/) |
| 22012 | How Dentists Get 30+ New Patients Monthly from Google M | 2026-05-12 | local-seo-dental-practices-patient-acquisition | [Live](https://rankray.com/local-seo-dental-practices-patient-acquisition/) |
| 21695 | Top Affiliate Programs on Impact Radius: A Complete Gui | 2026-05-12 | top-affiliate-programs-on-impact-radius | [Live](https://rankray.com/top-affiliate-programs-on-impact-radius/) |
| 12185 | How AI Changed SEO: AEO, GEO and AI Overviews Explained | 2026-05-12 | ai-seo-aeo-geo-guide | [Live](https://rankray.com/ai-seo-aeo-geo-guide/) |
| 12055 | Why SEO Takes 6 to 12 Months: Real Timelines and ROI Ex | 2026-05-12 | why-seo-takes-time | [Live](https://rankray.com/why-seo-takes-time/) |
| 9272 | Google Search Ranking Features Leaked: What Actually Ha | 2026-05-12 | google-ranking-leak-explained | [Live](https://rankray.com/google-ranking-leak-explained/) |
| 5399 | SEO and Content Synergy: The Playbook for Integrated Ra | 2026-05-12 | seo-content-synergy-playbook | [Live](https://rankray.com/seo-content-synergy-playbook/) |
| 22007 | How Real Estate Agents Dominate the Google Map Pack (Wi | 2026-05-12 | local-seo-real-estate-map-pack | [Live](https://rankray.com/local-seo-real-estate-map-pack/) |
| 21981 | How to Rank in Dubai: A Complete SEO Strategy for UAE M | 2026-05-08 | dubai-seo-complete-guide | [Live](https://rankray.com/dubai-seo-complete-guide/) |
| 21783 | Generative Engine Optimization: Complete Strategy Guide | 2026-05-07 | generative-engine-optimization-strategy | [Live](https://rankray.com/generative-engine-optimization-strategy/) |
| 21993 | How to Audit Enterprise Sites with 10,000+ Pages Withou | 2026-05-06 | enterprise-technical-seo-audit-checklist | [Live](https://rankray.com/enterprise-technical-seo-audit-checklist/) |
| 21990 | Enterprise SEO: How Large-Scale Optimization Builds Aut | 2026-05-05 | enterprise-seo-complete-strategy | [Live](https://rankray.com/enterprise-seo-complete-strategy/) |
| 20492 | What Is Answer Engine Optimization: AEO Explained for M | 2026-05-04 | what-is-answer-engine-optimization-aeo-explained | [Live](https://rankray.com/what-is-answer-engine-optimization-aeo-explained/) |
| 21996 | Enterprise SEO Reporting Dashboards That C-Suite Actual | 2026-05-04 | enterprise-seo-reporting-dashboards-c-suite | [Live](https://rankray.com/enterprise-seo-reporting-dashboards-c-suite/) |
| 20529 | 301 Redirect Mapping Guide: How to Plan URL Changes Wit | 2026-05-04 | 301-redirect-mapping-guide-url-changes-seo | [Live](https://rankray.com/301-redirect-mapping-guide-url-changes-seo/) |
| 20516 | ccTLD vs Subdirectory vs Subdomain: Choosing the Right | 2026-05-03 | cctld-vs-subdirectory-subdomain-international-seo | [Live](https://rankray.com/cctld-vs-subdirectory-subdomain-international-seo/) |
| 20495 | AI-Generated Search Results: How They Choose Sources an | 2026-05-03 | ai-generated-search-results-source-selection-seo | [Live](https://rankray.com/ai-generated-search-results-source-selection-seo/) |
| 20512 | Internal Linking Strategy for SEO: Complete Guide to Li | 2026-05-02 | internal-linking-strategy-seo-link-equity-distribution | [Live](https://rankray.com/internal-linking-strategy-seo-link-equity-distribution/) |
| 20468 | Information Gain Score Ultimate Guide: Create Original | 2026-05-02 | information-gain-score-ultimate-guide | [Live](https://rankray.com/information-gain-score-ultimate-guide/) |
| 20494 | Google AI Overview SEO Strategy: How to Rank in AI-Powe | 2026-05-02 | google-ai-overview-seo-strategy | [Live](https://rankray.com/google-ai-overview-seo-strategy/) |
| 20482 | Healthcare SEO Guide: Medical Practice SEO and YMYL Com | 2026-05-02 | healthcare-seo-medical-practice | [Live](https://rankray.com/healthcare-seo-medical-practice/) |
| 20358 | Core Web Vitals Guide: Fix LCP, INP, CLS for Higher Ran | 2026-05-01 | core-web-vitals-guide | [Live](https://rankray.com/core-web-vitals-guide/) |
| 20472 | B2B SEO Guide: Strategy for Long Sales Cycles and Enter | 2026-05-01 | b2b-seo-guide-enterprise | [Live](https://rankray.com/b2b-seo-guide-enterprise/) |
| 20470 | International SEO Guide: Hreflang, ccTLD and Multilingu | 2026-05-01 | international-seo-hreflang-guide | [Live](https://rankray.com/international-seo-hreflang-guide/) |
| 20490 | Perplexity AI SEO: How to Get Your Content Cited in AI | 2026-05-01 | perplexity-ai-seo-content-citation-strategy | [Live](https://rankray.com/perplexity-ai-seo-content-citation-strategy/) |

---

## Issues Remaining

1. **21796** (AI Automation): No featured image, 1706 words (needs 2000+), no summary block
2. **22184** (Franchise Pillar): 0 internal links, Yoast title/desc slightly long
3. **21695** (Affiliate): 1550 words (too short), 2 links added but still low, no summary
4. **Summary blocks**: 42 posts missing `<blockquote>` summary blocks (pre-FAQ removal batch)
5. **Em-dashes detected**: 2 posts with `&#8212;` or `—` in content
6. **21979**: Permanently deleted (not trashed) — violation of hard rule

---

## Category List (Live)

| ID | Name | Count |
|----|------|-------|
| 455 | AI and GEO | 15 |
| 453 | Content Marketing | 15 |
| 452 | Digital Marketing | 18 |
| 502 | eCommerce SEO | 2 |
| 449 | Local SEO | 14 |
| 448 | Off-Page SEO | 7 |
| 446 | On-Page SEO | 23 |
| 454 | Paid Media | 3 |
| 451 | SEO Agency Guides | 3 |
| 445 | SEO Fundamentals | 17 |
| 450 | SEO Strategy | 52 |
| 456 | SEO Tools and Resources | 14 |
| 447 | Technical SEO | 24 |
| 1 | Topics (default) | 0 |

## Service Pages (2026-05-14 Update)

**Automation System:** `rankray-service-page-manager.py` and `push-service-pages-chunked.py` deployed.

### ACF Service Pages Updated

| ID | Title | Builder | Words Pushed | Yoast Fixed | Status |
|----|-------|---------|--------------|-------------|--------|
| 13348 | Enterprise SEO Audit Services | ACF | 1200+ | Title + Desc + FocusKW | ✅ Updated |
| 11366 | Digital Marketing Strategy | ACF | 1200+ | Title + Desc + FocusKW | ✅ Updated |
| 15037 | Custom Website Design | ACF | 1200+ | Title + Desc + FocusKW | ✅ Updated |

**Content files kept at:** `projects/rankray/acf-content/`
- `enterprise-seo-audit-acf.json`
- `digital-marketing-strategy-acf.json`
- `custom-website-design-acf.json`

### Service Page Audit Results (2026-05-14)

- **Total ACF service pages:** 53 (under parent 2593)
- **Thin pages (<800w):** 7 (E-commerce SEO, GEO, Technical SEO, Local SEO, SEO Company, Custom Website Design, Digital Marketing Strategy)
- **Pages with em-dashes:** 0 (all clean)
- **Long titles (>60c):** 15
- **Long descs (>160c):** 8
- **Missing brand:** 2
- **Missing focus KW:** 0
- **Builder breakdown:** All ACF (no Elementor in service pages except 11148 and 2593 which are skipped)

---

*Last Updated: 2026-05-14*


# MAY 2026 Content Batch (Rows 38-44)

## Scheduled Posts
- Row 38 (May 23, 09:00): How to Rank on Google in NYC (ID 22591)
- Row 39 (May 23, 15:00): NYC Restaurant SEO: Yelp vs Google (ID 22594)
- Row 40 (May 23, 21:00): NYC Startup SEO: Pre-Seed to Series A (ID 22601)
- Row 41 (May 24, 03:00): SEO Copywriting vs Sales Copywriting (ID 22597)
- Row 42 (May 24, 09:00): LA Entertainment SEO (ID 22600)
- Row 43 (May 24, 15:00): LA Real Estate SEO (ID 22608)
- Row 44 (May 24, 21:00): LA Restaurant SEO (ID 22609)

## Batch Stats
- Total posts: 44 complete (Rows 17-44)
- Published before batch: 113
- Drafts scheduled: 7 (May 22-24)
- Blog count after batch: 120 published + 14 drafts


---

## NEW SEO Agency Location Pages (2026-05-23)

| City | Country | Page ID | Slug | Status |
|------|---------|---------|------|--------|
| London | UK | 22761 | seo-agency-london-uk | DRAFT |
| Sydney | Australia | 22768 | seo-agency-sydney | DRAFT |
| Islamabad | Pakistan | 22770 | seo-agency-islamabad | DRAFT |
| Karachi | Pakistan | 22772 | seo-agency-karachi | DRAFT |
| Lahore | Pakistan | 22774 | seo-agency-lahore | DRAFT |
| Rawalpindi | Pakistan | 22776 | seo-agency-rawalpindi | DRAFT |

**SEO agency page coverage now complete for all major markets.**
**Total location pages now:** 44 (38 existing + 6 new)
