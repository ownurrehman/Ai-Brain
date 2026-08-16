# RankRay.com Full Site Audit Report
**Date:** 2026-06-22 22:28
**Auditor:** Hermes Agent (automated, full frontend + backend scan)

---

## EXECUTIVE SUMMARY

### Site Overview
- **Blog Posts:** 143 published, 0 drafts, 0 trashed
- **Service Pages:** 53 pages (children of Digital Marketing Services ID 2593)
- **Tool Pages:** 5 pages (children of Free SEO Tools ID 18241)
- **Location Pages:** 37 valid + 1 error (custom post type 'location-page')
- **Root Pages:** 12 (homepage, about, contact, blog, locations, etc.)
- **Total WP Pages:** 70

### Health Scorecard
| Metric | Service Pages | Location Pages | Blog Posts |
|--------|--------------|----------------|------------|
| Total | 53 | 37 | 143 |
| Yoast Focus KW | 53/53 (100%) | 37/37 (100%) | 143/143 (100%) |
| Meta Desc < 160c | 49/53 (92%) | 37/37 (100%) | 142/143 (99%) |
| Brand in Desc | 52/53 (98%) | 37/37 (100%) | 107/143 (75%) |
| Featured Image | 51/53 (96%) | 22/37 (59%) | 143/143 (100%) |
| H1 > 20 chars | 51/53 (96%) | 37/37 (100%) | N/A |
| 2000+ words | 3/53 (6%) | 37/37 (100%) | 143/143 (100%) |
| 10+ int. links | 53/53 (100%) | 37/37 (100%) | 143/143 (100%) |
| Em-dash free | 52/53 (98%) | 20/37 (54%) | 121/143 (85%) |

---

## 1. SERVICE PAGES (53 pages)

### 1.1 Word Count (Frontend Visible)
- Range: 1407-2816 words
- Average: 1630 words
- Under 1500w: 8 pages
- 1500-2000w: 42 pages
- 2000+: 3 pages

**Finding:** ACF template ceiling is ~1,500-1,800 visible words. The 5 Elementor-built
pages (Local SEO, Technical SEO, SEO Company, GEO, E-commerce) reach 1,800-2,800 words.

### 1.2 H1 Quality Issues
- ID:17114 | H1='E-commerce SEO' (14c) | E-commerce SEO
- ID:12502 | H1='Local SEO' (9c) | Local SEO

### 1.3 Yoast Issues

**Meta descriptions too long (>160 chars):**
- ID:14782 | 177c | Digital PR Services
- ID:13810 | 172c | Lead Generation Services
- ID:13779 | 168c | YouTube Advertising Services
- ID:13708 | 169c | Google Ads Management Services

**Missing brand 'Rank Ray' in description:**
- ID:13179 | Technical SEO

### 1.4 ACF Fields
- Pages with all 63 ACF fields populated: 48
- Pages with ZERO ACF fields (Elementor-only): 5

**Elementor-only pages (cannot edit via REST API):**
- ID:17114 | E-commerce SEO | /ecommerce-seo
- ID:13247 | Generative Engine Optimization | /generative-engine-optimization-geo
- ID:13179 | Technical SEO | /technical-seo
- ID:12502 | Local SEO | /local-seo
- ID:11148 | Search Engine Optimization – SEO Company | /search-engine-optimization-seo

### 1.5 Em-Dashes
- Pages with em-dashes: 1
  - ID:11148 | 1 occurrences | Search Engine Optimization – SEO Company

### 1.6 Missing Featured Images
- ID:15037 | Custom Website Design
- ID:13247 | Generative Engine Optimization

### 1.7 Weakest Service Pages (priority for improvement)
| ID | Words | H1 | Links | ACF | Slug |
|-----|-------|-----|-------|------|------|
| 6876 | 1407 | App Development That Deli | 136 | 63/63 | /app-development |
| 11530 | 1420 | Branding That Builds Trus | 136 | 63/63 | /branding |
| 2525 | 1425 | Web Development That Driv | 136 | 63/63 | /web-development |
| 13314 | 1444 | Franchise SEO Audits That | 136 | 63/63 | /franchise-seo-audit-servi |
| 13750 | 1465 | Programmatic Advertising  | 133 | 63/63 | /programmatic-advertising |
| 14690 | 1481 | Video Production Services | 132 | 63/63 | /video-production-services |
| 13434 | 1484 | Enterprise Social Media M | 136 | 63/63 | /enterprise-social-media-m |
| 13416 | 1499 | Franchise Social Media Ma | 136 | 63/63 | /franchise-social-media-ma |
| 14782 | 1507 | Digital PR Services That  | 132 | 63/63 | /digital-pr-services |
| 11424 | 1514 | Social Media Marketing Th | 136 | 63/63 | /social-media-marketing |

---

## 2. LOCATION PAGES (37 valid + 1 error)

### 2.1 Overview
- SEO Agency pages: 21
- DM Agency pages: 16
- All pages have strong H1s, Yoast set, and ACF populated
- One page error: ID:17889 (404 on REST API)

### 2.2 Em-Dashes (CRITICAL)
- Pages with em-dashes: 17

- ID:17990 | 7 occurrences | Digital Marketing Agency Lahore
- ID:17992 | 6 occurrences | Digital Marketing Agency Los Angeles
- ID:17996 | 5 occurrences | Digital Marketing Agency Miami
- ID:17991 | 5 occurrences | Digital Marketing Agency Sydney
- ID:17995 | 4 occurrences | Digital Marketing Agency Dallas
- ID:17994 | 4 occurrences | Digital Marketing Agency Houston
- ID:15826 | 4 occurrences | Digital Marketing Agency Islamabad
- ID:17998 | 3 occurrences | Digital Marketing Agency Vancouver
- ID:17993 | 3 occurrences | Digital Marketing Agency Chicago
- ID:17834 | 3 occurrences | Digital Marketing Agency Milton
- ID:18999 | 2 occurrences | Real Estate SEO Agency Dubai
- ID:18016 | 1 occurrences | Digital Marketing Agency in Rawalpindi
- ID:17828 | 1 occurrences | Digital Marketing Agency Dubai
- ID:17835 | 1 occurrences | Digital Marketing Agency Toronto
- ID:17833 | 1 occurrences | Digital Marketing Agency Abu Dhabi
- ID:17832 | 1 occurrences | Digital Marketing Agency London
- ID:17831 | 1 occurrences | Digital Marketing Agency New York

### 2.3 Missing Featured Images
- Pages without featured image: 15
  - ID:19246 | SEO Agency in Milton
  - ID:18016 | Digital Marketing Agency in Rawalpindi
  - ID:17998 | Digital Marketing Agency Vancouver
  - ID:17996 | Digital Marketing Agency Miami
  - ID:17995 | Digital Marketing Agency Dallas
  - ID:17994 | Digital Marketing Agency Houston
  - ID:17992 | Digital Marketing Agency Los Angeles
  - ID:17991 | Digital Marketing Agency Sydney
  - ID:17990 | Digital Marketing Agency Lahore
  - ID:17828 | Digital Marketing Agency Dubai
  - ID:17835 | Digital Marketing Agency Toronto
  - ID:17834 | Digital Marketing Agency Milton
  - ID:17833 | Digital Marketing Agency Abu Dhabi
  - ID:17832 | Digital Marketing Agency London
  - ID:17831 | Digital Marketing Agency New York

### 2.4 Word Count
- Range: 1660-2614 words
- Average: 1942 words
- All pages are 1,500+ words.

---

## 3. BLOG POSTS (143 posts)

### 3.1 Word Count (Frontend)
- Range: 2187-6311 words
- Average: 3295 words
- Under 2000w: 0 posts
- 2000-3000w: 72 posts
- 3000+: 71 posts

**All 143 blog posts are 2000+ words. Zero thin content.**

### 3.2 Internal Links
- Zero internal links: 0 posts
- 1-4 links: 0 posts
- 5-9 links: 0 posts
- 10+ links: 143 posts (100%)

**All blog posts have 10+ internal links.**

### 3.3 Em-Dashes (NEEDS FIXING)
- Posts with em-dashes: 22

**Posts with em-dashes:**
- ID:5408 | 180 occurrences | BEST PRESS RELEASE SERVICES TO BUILT E-E-A-T
- ID:22184 | 78 occurrences | How Franchise SEO Works: Scaling Local Rankings Ac
- ID:22024 | 39 occurrences | How to Build Internal Links for 10,000+ Product Pa
- ID:22027 | 30 occurrences | How B2B Content Marketing Generates Leads Through 
- ID:22588 | 27 occurrences | Outbound Marketing vs SEO: When to Use Each Channe
- ID:21999 | 27 occurrences | Franchise SEO: How to Rank Every Location Without 
- ID:22012 | 24 occurrences | How Dentists Get 30+ New Patients Monthly from Goo
- ID:22585 | 21 occurrences | Lead Generation SEO: Content That Captures High-In
- ID:22549 | 15 occurrences | Email Marketing Metrics That Actually Matter for S
- ID:22015 | 15 occurrences | How Law Firms Rank #1 in the Map Pack for &#8220;L
- ID:19812 | 14 occurrences | What Is Semantic SEO? Complete Guide
- ID:22579 | 12 occurrences | Video Schema Markup: Rich Results for Video Conten
- ID:22030 | 12 occurrences | How Brand Recognition Boosts Your Google Rankings 
- ID:20414 | 12 occurrences | On-Page SEO Optimization: Complete Guide to Higher
- ID:21996 | 9 occurrences | Enterprise SEO Reporting Dashboards That C-Suite A

### 3.4 Missing Brand in Meta Description
- Posts missing 'Rank Ray' in meta desc: 36

### 3.5 Meta Description Too Long
- Posts with desc > 160 chars: 1

### 3.6 Category Distribution
| Category | Posts |
|----------|-------|
| SEO Strategy | 80 |
| Digital Marketing | 35 |
| Technical SEO | 28 |
| On-Page SEO | 23 |
| Content Marketing | 18 |
| AI and GEO | 17 |
| SEO Fundamentals | 17 |
| Local SEO | 16 |
| SEO Tools and Resources | 14 |
| Off-Page SEO | 7 |
| Paid Media | 4 |
| eCommerce SEO | 3 |
| SEO Agency Guides | 3 |
| CRO | 1 |

### 3.7 Date Distribution
| Year | Posts |
|------|-------|
| 2026 | 128 |
| 2025 | 5 |
| 2024 | 7 |
| 2023 | 2 |
| 2021 | 1 |

---

## 4. CRITICAL ISSUES PRIORITY LIST

### CRITICAL (Fix Immediately)
1. **17 location pages have em-dashes** in frontend content (DM Agency pages)
2. **22 blog posts have em-dashes** in frontend content
3. **1 service page has em-dash** (ID:11148 SEO Company)
4. **4 service pages have Yoast meta desc > 160 chars**
5. **1 service page missing brand in Yoast desc** (ID:13179 Technical SEO)
6. **1 blog post has meta desc > 160 chars** (ID:22570)
7. **36 blog posts missing brand 'Rank Ray' in meta description**

### HIGH PRIORITY
8. **15 location pages missing featured images**
9. **2 service pages missing featured images** (ID:15037, 13247)
10. **5 service pages are Elementor-only** (no ACF, cannot edit via REST API)
11. **2 service pages have weak H1 tags** (ID:17114, 12502)
12. **1 location page returning 404 on REST API** (ID:17889)

### MEDIUM PRIORITY
13. **Service page conversion quality** — previous audit scored all 50 pages below 60/100
14. **ACF template ceiling** — service pages stuck at 1,400-1,800 words
15. **Double dashes in all pages** — likely in CSS/HTML code, needs visible content check
16. **HARO Link Building page** (ID:14914) — discontinued, do not link to

### GOOD NEWS
- All 143 blog posts are 2000+ words (zero thin content)
- All 143 blog posts have 10+ internal links
- All blog posts have Yoast focus KW, meta desc, categories, and featured images
- All 53 service pages have Yoast focus KW set
- All location pages have strong H1s, Yoast set, ACF populated
- 128/143 blog posts published in 2026 (strong production cadence)
- Zero em-dashes in blog post titles or Yoast descriptions
- No posts using 'Topics' default category

---

## 5. RECOMMENDED ACTION PLAN

### Phase 1: Fix Critical Issues (estimated 2-3 hours)
1. Remove em-dashes from 17 location pages (DM Agency type)
2. Remove em-dashes from 22 blog posts
3. Remove em-dash from 1 service page (ID:11148)
4. Trim 4 service page meta descriptions to < 160 chars
5. Add brand to 1 service page meta desc (ID:13179)
6. Trim 1 blog post meta desc (ID:22570)
7. Add brand to 36 blog post meta descriptions

### Phase 2: Fix High Priority Issues (estimated 1-2 hours)
8. Add featured images to 15 location pages
9. Add featured images to 2 service pages
10. Fix weak H1 tags on 2 service pages
11. Investigate 404 location page (ID:17889)

### Phase 3: Service Page Conversion Improvement (ongoing)
12. Rewrite service page content for conversion (all 48 ACF pages scored < 60/100)
13. Address 5 Elementor-only pages via builder access
14. Plan content expansion strategy within ACF template constraints

### Phase 4: Blog Post Enhancement (ongoing)
15. Add brand to 36 blog post meta descriptions
16. Verify double dashes are in code not visible content
17. Continue content production pipeline

---

## 6. DATA FILES

All raw audit data saved to:
- `audits/service-pages-backend.json` — Backend WP REST data for all 53 service pages
- `audits/service-pages-frontend.json` — Frontend HTML for all 53 service pages
- `audits/service-pages-audit-results.json` — Parsed audit results
- `audits/location-pages-backend.json` — Backend data for 38 location pages
- `audits/location-pages-frontend.json` — Frontend HTML for 37 location pages
- `audits/location-pages-audit-results.json` — Parsed audit results
- `audits/blog-posts-full-audit.json` — Full audit of 143 blog posts
- `audits/raw-pages.json` — Raw WP page listing
- `audits/raw-posts.json` — Raw WP post listing
- `audits/raw-posts-full.json` — Raw WP post listing with meta
- `audits/FULL-SITE-AUDIT-2026-06-22.md` — This report
