> **Parent Site:** [[websites/tonicphysio.com/index|🌐 tonicphysio.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Technical SEO Audit — tonicphysio.com
**Date:** 2026-05-30 (Saturday)  
**Auditor:** OpenClaw SEO Bot  
**Scope:** Homepage + 3 Key Pages (Services, Contact, Blog)

---

## Executive Summary
| Metric | Status |
|--------|--------|
| robots.txt | ✅ Accessible, sitemap declared |
| sitemap.xml | ✅ Accessible, 4 sub-sitemaps, lastmod current |
| Indexing | ✅ Strong — core pages indexed with rich snippets |
| Schema | ⚠️ MedicalOrganization present but missing LocalBusiness/Physician with geo; Missing FAQPage; Missing BreadcrumbList |
| Mobile | ✅ Good, responsive nav, readable fonts |
| Speed | ⚠️ LiteSpeed active but heavy Elementor DOM; needs CWV check |
| Critical Issue #1 | Homepage counter stats showing "0+" — broken |
| Critical Issue #2 | No LocalBusiness schema for local pack |
| Blog Activity | Active (May 2026 posts) |
| Reviews | 4.9/5 Google (112 reviews) — not in schema |

---

## Page-by-Page Findings

### 1. Homepage (https://tonicphysio.com/)
| Check | Status | Notes |
|-------|--------|-------|
| Title Tag | ✅ | "Tonic Physiotherapy and Rehabilitation Centre in Milton, CA" |
| Meta Description | ✅ | Present, well-written |
| H1 Tag | ✅ | "Tonic Physio Leading Physiotherapy & Rehab Centre in Milton" |
| Heading Hierarchy | ✅ | Logical H1 → H2 → H3 → H4 flow |
| Images Alt Text | ⚠️ | 9 empty alt attributes (from TrustIndex widget) |
| Schema | ⚠️ | MedicalOrganization + SiteNavigationElement + WebSite present. Missing LocalBusiness with geoCoordinates, openingHours, aggregateRating |
| Broken Counter | ❌ | Stats show "0+ Years / 0+ Patients / 0% Satisfaction" — JavaScript animation failure |
| Mobile Usability | ✅ | Responsive nav, readable fonts, touch-friendly buttons |
| Load Speed | ⚠️ | Elementor-heavy DOM, lazy loading active, LiteSpeed cache enabled |

### 2. Services Page (https://tonicphysio.com/services/)
| Check | Status | Notes |
|-------|--------|-------|
| Title Tag | ✅ | "Physiotherapy & Rehabilitation Services | Tonic Physio" |
| Meta Description | ✅ | Present, keyword-rich |
| H1 Tag | ✅ | "Treatments We Provide at Tonic Physio" |
| Content Depth | ✅ | 18+ treatment cards with unique descriptions |
| Internal Linking | ✅ | Each treatment links to dedicated sub-page |
| Schema | ❌ | No schema markup detected |
| Mobile Usability | ✅ | Responsive grid layout |

### 3. Contact Page (https://tonicphysio.com/contact/)
| Check | Status | Notes |
|-------|--------|-------|
| Title Tag | ✅ | "Contact | Tonic Physiotherapy Centre in Milton" |
| Meta Description | ✅ | Present, action-oriented |
| H1 Tag | ✅ | "Contact Us" |
| NAP Consistency | ✅ | Phone, email, address match GMB and footer |
| WhatsApp Link | ✅ | Present with click-to-chat |
| Google Maps Embed | ✅ | Active iframe with location |
| Schema | ❌ | No LocalBusiness or ContactPoint schema |
| Mobile Usability | ✅ | Form fields are touch-friendly |

### 4. Blog Page (https://tonicphysio.com/blog/)
| Check | Status | Notes |
|-------|--------|-------|
| Title Tag | ✅ | "Blog | Tonic Physio" |
| Meta Description | ✅ | Present |
| H1 Tag | ✅ | "Our Blogs" |
| Content Freshness | ✅ | Latest posts from May 24, 2026 |
| Author Bylines | ✅ | Multiple authors (Dan Torres, Brenda Azzopardi, etc.) |
| Schema | ❌ | No Article or BlogPosting schema detected |
| Pagination | ⚠️ | No clear pagination controls visible |
| Mobile Usability | ✅ | Responsive card grid |

---

## Sitemap & robots.txt

### robots.txt
- **Status:** ✅ Accessible (200)
- **Sitemap Declared:** https://tonicphysio.com/sitemap_index.xml
- **Blocks:** AI crawlers (ClaudeBot, GPTBot, etc.) — intentional
- **Note:** Uses Cloudflare-managed robots.txt with content-signals

### sitemap_index.xml
- **Status:** ✅ Accessible (200)
- **Sub-sitemaps:** 4 (posts, pages, categories, authors)
- **Lastmod:** Page sitemap updated 2026-05-25 (recent)
- **Yoast SEO:** Properly generated

---

## Schema Audit

### Present Schema
- ✅ **SiteNavigationElement** — 50+ nav items mapped
- ✅ **WebSite** — with SearchAction potential
- ✅ **MedicalOrganization** — basic org info with sameAs social links
- ✅ **ContactPoint** — phone + contact URL

### Missing Schema (High Priority)
- ❌ **LocalBusiness** / **Physician** — needed for local pack
- ❌ **geoCoordinates** — no lat/long for Milton location
- ❌ **openingHours** — not specified
- ❌ **aggregateRating** — 4.9/5 from 112 Google reviews not in schema
- ❌ **BreadcrumbList** — no breadcrumb schema
- ❌ **FAQPage** — /faq/ page exists but no FAQ schema
- ❌ **Article** / **BlogPosting** — blog posts lack structured data

---

## Indexing Verification
**Query:** `site:tonicphysio.com`

**Results:** 10+ indexed pages confirmed
- ✅ Homepage
- ✅ /about/
- ✅ /services/
- ✅ /contact/
- ✅ /blog/
- ✅ /therapists/
- ✅ /faq/
- ✅ /fees/
- ✅ Author pages (Brenda Azzopardi, Frank Zhang)
- ✅ Individual blog posts

**Index Health:** Strong — core pages indexed with meta descriptions showing in SERP snippets

---

## Issues Summary

| Priority | Issue | Severity | Recommended Fix |
|----------|-------|----------|-----------------|
| P0 | Homepage counter shows "0+" | 🔴 Critical | Fix JavaScript counter animation or replace with static stats |
| P0 | Missing LocalBusiness schema | 🔴 Critical | Add LocalBusiness with @type: Physician, geo, hours, aggregateRating |
| P1 | Missing BreadcrumbList schema | 🟡 High | Add breadcrumb JSON-LD to all pages |
| P1 | Missing FAQPage schema | 🟡 High | Add FAQ schema to /faq/ page |
| P1 | Empty alt attributes (9) | 🟡 High | Fix TrustIndex widget alt tags or add descriptive alts |
| P2 | Missing Article schema on blog | 🟢 Medium | Add BlogPosting schema to blog posts |
| P2 | Missing openingHours in schema | 🟢 Medium | Add business hours to LocalBusiness |
| P2 | Speed / CWV unchecked | 🟢 Medium | Run PageSpeed Insights for LCP/CLS/INP scores |
| P2 | Large PNG images | 🟢 Medium | Convert hero/team images to WebP |

---

## Previous Audit Comparison

| Item | 2026-05-26 | 2026-05-30 | Status |
|------|------------|------------|--------|
| robots.txt | ✅ | ✅ | No change |
| sitemap.xml | ✅ | ✅ | No change |
| Indexing | ✅ | ✅ | No change |
| Schema gaps | ⚠️ | ⚠️ | Still open — no fixes applied |
| Counter bug | ❌ | ❌ | Still broken |
| Blog activity | Active | Active | Fresh May posts |
| Reviews in schema | ❌ | ❌ | Still missing |

**No fixes from previous audit have been implemented.** All P0 and P1 items remain open.

---

## Action Items (Open)
- [ ] P0: Add LocalBusiness/Physician schema with geo, hours, aggregateRating
- [ ] P0: Fix homepage counter animation (showing 0)
- [ ] P1: Add BreadcrumbList schema
- [ ] P1: Add FAQPage schema to /faq/
- [ ] P1: Fix empty alt attributes on TrustIndex widget images
- [ ] P2: Add Article/BlogPosting schema to blog posts
- [ ] P2: Run PageSpeed Insights for LCP/CLS/INP
- [ ] P2: Convert large PNGs to WebP where possible

---

## Next Audit
**Scheduled:** Tuesday 2026-06-02 (tonicphysio.com)
