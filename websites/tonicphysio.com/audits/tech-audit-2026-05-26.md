# Technical SEO Audit — tonicphysio.com
**Date:** 2026-05-26 (Tuesday)  
**Auditor:** OpenClaw automated tech audit  
**Site:** https://tonicphysio.com

---

## 1. SITE OVERVIEW

| Property | Value |
|----------|-------|
| Platform | WordPress + Elementor + LiteSpeed Cache |
| SEO Plugin | Yoast SEO (sitemap generated) |
| Schema Plugin | Schema & Structured Data for WP (SASWP) |
| Chat Widget | Chaty (WhatsApp) |
| Reviews | Trustindex (Google Reviews — 4.9/5, 112 reviews) |
| CDN | Cloudflare |

---

## 2. PAGES AUDITED

| Page | Status | Title Tag | Notes |
|------|--------|-----------|-------|
| `/` (Homepage) | ✅ 200 | "Tonic Physiotherapy and Rehabilitation Centre in Milton, CA" | H1 present, hero slider, service grid, reviews, blog feed |
| `/services/` | ✅ 200 | "Physiotherapy & Rehabilitation Services \| Tonic Physio" | H1 present, treatment cards with internal links |
| `/about/` | ✅ 200 | "Expert Physiotherapy in Milton, Ontario" | H1 present, team/story content |
| `/contact/` | ✅ 200 | "Tonic Physiotherapy Centre in Milton" | H1 present, contact form, map iframe |

---

## 3. ROBOTS.TXT & SITEMAP

### robots.txt — ✅ ACCESSIBLE
- Location: `https://tonicphysio.com/robots.txt`
- Status: 200 OK
- Key findings:
  - `Sitemap: https://tonicphysio.com/sitemap_index.xml` ✅ declared
  - `User-agent: * Allow: /` ✅ root allowed
  - Cloudflare AI bot blocks present (ClaudeBot, GPTBot, Google-Extended, etc.) — this is intentional and fine
  - Content-Signal headers for search=yes, ai-train=no

### Sitemap — ✅ ACCESSIBLE
- Location: `https://tonicphysio.com/sitemap_index.xml`
- Status: 200 OK
- Structure:
  - `post-sitemap.xml` — blog posts (lastmod 2026-05-24)
  - `page-sitemap.xml` — pages (lastmod 2026-05-25)
  - `category-sitemap.xml` — categories (lastmod 2026-05-24)
  - `author-sitemap.xml` — authors (lastmod 2026-05-12)

**Sitemap Health:** ✅ Good. Lastmods are recent. Image sitemaps included. Yoast handles generation.

---

## 4. SCHEMA / STRUCTURED DATA

### Present on Homepage:
1. **SiteNavigationElement** (SASWP plugin) — 60+ navigation entries ✅
2. **WebSite** schema — with SearchAction potential ✅
3. **MedicalOrganization** schema — with:
   - Name: "Tonic Physio"
   - Logo ImageObject
   - SameAs links (Facebook, Instagram, LinkedIn, TikTok)
   - ContactPoint (telephone +1 905-878-7775)
   - Legal name: "Tonic Physiotherapy Centre"

### Schema Assessment:
| Check | Status | Notes |
|-------|--------|-------|
| MedicalOrganization | ✅ | Present with contact info |
| LocalBusiness / Physician | ⚠️ MISSING | No `LocalBusiness` or `Physician` with `@id` and `geo` |
| WebSite SearchAction | ✅ | Present |
| SiteNavigationElement | ✅ | Comprehensive |
| BreadcrumbList | ⚠️ MISSING | Not detected on homepage |
| FAQPage schema | ⚠️ MISSING | FAQ page exists but no dedicated FAQ schema |
| Review/Rating schema | ⚠️ MISSING | Google reviews widget present but no aggregateRating schema on page |

**Recommended:** Add `LocalBusiness` schema with `geo` coordinates, opening hours, and `aggregateRating` to boost local SEO.

---

## 5. INDEXING STATUS (Search: site:tonicphysio.com)

Google indexed pages verified via search:

| Indexed Page | Snippet Quality |
|--------------|-----------------|
| Homepage | ✅ Good meta description shown |
| /contact/ | ✅ Good |
| /fees/ | ✅ Good |
| /services/ | ✅ Good |
| /blog/ | ✅ Good |
| /therapists/ | ✅ Good |
| /faq/ | ✅ Rich snippets with FAQ questions visible |
| /about/ | ✅ Good |
| /author/brenda/ | ✅ Good |
| /author/sumithra/ | ✅ Good |

**Indexing Health:** ✅ Strong. Core pages indexed with descriptive snippets.

---

## 6. MOBILE USABILITY

| Check | Status | Notes |
|-------|--------|-------|
| Viewport meta | ✅ | `width=device-width, initial-scale=1.0, viewport-fit=cover` |
| Responsive nav | ✅ | Elementor mobile menu with burger toggle |
| Touch targets | ✅ | Large buttons, proper spacing observed |
| Font sizes | ✅ | Readable, no tiny text |
| Images | ⚠️ | Lazy-loaded placeholders visible (SVG data-URI), may cause layout shift |

**Mobile Health:** ✅ Good overall. Minor concern: lazy-loaded image placeholders may cause CLS.

---

## 7. PAGE SPEED OBSERVATIONS

| Factor | Observation |
|--------|-------------|
| Caching | ✅ LiteSpeed Cache active |
| Lazy loading | ✅ Images lazy-loaded with data-src attributes |
| JS defer | ✅ Scripts use `defer` strategy |
| Speculation rules | ✅ `<script type="speculationrules">` for prefetching |
| Image optimization | ⚠️ WebP not consistently used; some images are PNG/JPG at large sizes |
| Third-party scripts | Chaty widget, Trustindex reviews, Google Maps iframe, Elementor assets |
| DOM size | ⚠️ Large — heavy Elementor markup with many nested containers |

**Note:** Actual Core Web Vitals (LCP, CLS, INP) require PageSpeed Insights API or field data. Browser-based timing measurement was not available in this audit.

---

## 8. CRITICAL ISSUES

| # | Issue | Severity | Recommended Fix |
|---|-------|----------|-----------------|
| 1 | **No `LocalBusiness` / `Physician` schema with `geo` coordinates** | 🔴 High | Add `LocalBusiness` or `Physician` schema including `geo`, `openingHours`, `priceRange`, and `aggregateRating`. This is critical for local pack rankings. |
| 2 | **Missing breadcrumb schema** | 🟡 Medium | Add `BreadcrumbList` schema to all service and blog pages for richer SERPs. |
| 3 | **No FAQ schema on FAQ page** | 🟡 Medium | The `/faq/` page has FAQ content but no `FAQPage` structured data. Add it for rich results. |
| 4 | **Image lazy-load placeholders causing potential CLS** | 🟡 Medium | Ensure placeholder SVGs match aspect ratios of final images to reduce Cumulative Layout Shift. |
| 5 | **Homepage "counter" stats showing "0"** | 🟡 Medium | The "Years", "Patients Treated", "Client Satisfaction" counters display "0+" — this looks broken and hurts credibility. Fix the counter animation or hardcode values. |
| 6 | **Missing `aggregateRating` schema despite 112 Google reviews** | 🟡 Medium | Add `aggregateRating` with ratingValue 4.9 and reviewCount 112 to MedicalOrganization or LocalBusiness schema. |

---

## 9. POSITIVE FINDINGS

- ✅ HTTPS enforced
- ✅ Canonical tags present
- ✅ OG tags and Twitter cards configured
- ✅ robots.txt and sitemap accessible and well-structured
- ✅ Strong internal linking via mega menu
- ✅ Google indexing healthy with rich snippets on FAQ
- ✅ Recent blog activity (May 2026 posts)
- ✅ Trustindex reviews widget loading correctly
- ✅ Chaty WhatsApp widget active for conversions
- ✅ Footer contains legal pages (Privacy, Terms, Cookie, Sitemap)

---

## 10. ACTION ITEMS SUMMARY

| Priority | Action | Owner |
|----------|--------|-------|
| P0 | Add `LocalBusiness`/`Physician` schema with geo, hours, rating | Dev / SEO |
| P1 | Add `BreadcrumbList` schema to service & blog pages | Dev |
| P1 | Add `FAQPage` schema to `/faq/` | Dev |
| P1 | Fix homepage counter stats (showing 0) | Dev |
| P2 | Add `aggregateRating` schema | Dev / SEO |
| P2 | Audit image formats — convert large PNGs to WebP | Dev |
| P2 | Run PageSpeed Insights and address LCP/CLS issues | Dev |

---

*Audit completed by OpenClaw on 2026-05-26.*
