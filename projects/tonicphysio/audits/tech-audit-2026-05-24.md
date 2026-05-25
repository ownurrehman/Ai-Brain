# Technical SEO Audit: tonicphysio.com

**Date:** 2026-05-24 (Sunday)  
**Auditor:** Enigma (automated cron audit)  
**Scope:** Homepage + 3 key pages + sitemap + robots + index check  
**Rotation:** Weekend (tonicphysio.com)

---

## Summary

| Metric | Result | Status |
|--------|--------|--------|
| Homepage load | OK (200) | Pass |
| Services page | OK (200) | Pass |
| About page | OK (200) | Pass |
| Contact page | OK (200) | Pass |
| Sitemap.xml | OK (200, 4 sitemaps) | Pass |
| Robots.txt | OK (200) | Pass |
| Google index | 5+ pages indexed | Pass |
| Schema (homepage) | 1 block present | Pass |
| Meta descriptions | All present | Pass |
| Canonical tags | All present | Pass |
| H1 tags | 1 per page | Pass |
| Viewport meta | Present | Pass |
| Mobile viewport | Configured | Pass |

---

## Detailed Findings

### 1. Homepage (https://tonicphysio.com/)

- **Title:** "Tonic Physiotherapy and Rehabilitation Centre in Milton, CA" — 65 chars (good)
- **Meta Description:** "Expert physiotherapy and rehab services at Tonic Physio. Move Better and Live Better with personalized care tailored to your needs." — 142 chars (good)
- **Canonical:** Present → `https://tonicphysio.com/`
- **Robots:** `index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1` (good)
- **H1s:** 1 (correct)
- **H2s:** 5
- **OG Title:** Present
- **OG Image:** Present
- **Viewport:** `width=device-width, initial-scale=1.0, viewport-fit=cover`
- **Lang:** `en-US`
- **Schema:** 1 JSON-LD block containing:
  - SiteNavigationElement (82 nav items — comprehensive)
  - WebSite
  - MedicalOrganization (with ContactPoint, logo, sameAs)

### 2. Services Page (https://tonicphysio.com/services/)

- **Title:** "Physiotherapy & Rehabilitation Services | Tonic Physio" — 58 chars
- **Meta Description:** "Discover expert physiotherapy & rehabilitation services at Tonic Physio. Our treatments help restore mobility, relieve pain, and support your recovery journey." — 156 chars
- **Canonical:** Present
- **Schema:** 1 block
- **H1s:** 1
- **H2s:** 0 (zero H2s is a concern — page may lack semantic structure)

### 3. About Page (https://tonicphysio.com/about/)

- **Title:** "About Tonic Physio: Expert Physiotherapy in Milton, Ontario" — 63 chars
- **Meta Description:** "Tonic Physiotherapy and Rehabilitation Centre is a leading physio and rehab health clinic in Milton, ON, Canada. Get moving fast at Tonic." — 142 chars
- **Canonical:** Present
- **Schema:** 1 block
- **H1s:** 1
- **H2s:** 3

### 4. Contact Page (https://tonicphysio.com/contact/)

- **Title:** "Contact | Tonic Physiotherapy Centre in Milton" — 49 chars
- **Meta Description:** "Get in touch with Tonic Physiotherapy Centre in Milton. Book appointments, ask questions, or learn more about our expert physio services today." — 143 chars
- **Canonical:** Present
- **Schema:** 1 block
- **H1s:** 1
- **H2s:** 2

---

## Sitemap Analysis

- **Index URL:** `https://tonicphysio.com/sitemap_index.xml` → **200 OK**
- **Sub-sitemaps:**
  1. `post-sitemap.xml` — lastmod: 2026-05-24
  2. `page-sitemap.xml` — lastmod: 2026-05-22
  3. `category-sitemap.xml` — lastmod: 2026-05-24
  4. `author-sitemap.xml` — lastmod: 2026-05-12
- **Generator:** Yoast SEO
- **Status:** All sub-sitemaps accessible and recently updated

---

## Robots.txt Analysis

- **URL:** `https://tonicphysio.com/robots.txt` → **200 OK**
- **Content:** Contains Content-Signal directives (AI-related signals: search=yes, ai-input=no, ai-train=no)
- **Observation:** No crawl directives like `Disallow:` or `Sitemap:` reference found in the visible portion. The file appears to use a newer content-signal format rather than traditional robots.txt syntax. Verify that search engines can still crawl effectively.

---

## Google Index Check

- **Query:** `site:tonicphysio.com`
- **Results:** 5 indexed pages visible in search
  1. Homepage
  2. Contact
  3. Fees
  4. Services
  5. Blog
- **Status:** Site is indexed. Core pages present.

---

## Issues Found

| # | Issue | Severity | Page | Recommended Fix |
|---|-------|----------|------|-----------------|
| 1 | Zero H2 headings | Medium | /services/ | Add H2 subsections to improve semantic structure and accessibility |
| 2 | robots.txt uses Content-Signal format | Low | Site-wide | Verify search engine compatibility; ensure traditional `Disallow`/`Allow` directives are not needed |
| 3 | Schema on homepage shows "Unknown" type when parsed | Low | Homepage | Verify JSON-LD validity; appears structurally sound in raw output |
| 4 | Some "Learn more" links under services section point to mismatched URLs (e.g., Massage Therapy links to Neurological Physiotherapy) | Medium | Homepage | Review and correct internal link mapping in service cards |

---

## Action Items

- [ ] Add H2 headings to `/services/` page
- [ ] Verify robots.txt is not blocking crawlers unintentionally
- [ ] Audit homepage service card links for URL accuracy
- [ ] Verify schema markup passes Google Rich Results Test
- [ ] Consider adding LocalBusiness schema for Milton location specificity

---

## Audit Complete

**Next audit:** Tuesday (tonicphysio.com) per rotation schedule.
