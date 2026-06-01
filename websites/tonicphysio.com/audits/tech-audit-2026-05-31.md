# Technical SEO Audit — tonicphysio.com
**Date:** 2026-05-31 (Sunday)  
**Auditor:** OpenClaw Automated Audit  
**Domain:** https://tonicphysio.com  
**Pages Audited:** Homepage, /services/, /contact/, /fees/, /physiotherapy-in-milton/  

---

## 1. SITEMAP & ROBOTS.TXT

| Item | Status | Notes |
|------|--------|-------|
| robots.txt | ✅ OK | Accessible at /robots.txt. Standard Yoast block. Cloudflare-managed content signals present. Blocks AI crawlers (ClaudeBot, GPTBot, etc.). |
| sitemap.xml | ✅ OK | Sitemap index at /sitemap_index.xml. Contains 4 sitemaps: post, page, category, author. Last modified: 2026-05-25 (page) / 2026-05-24 (post, category). |
| Sitemap freshness | ⚠️ WARNING | Author sitemap last modified 2026-05-12. Page sitemap updated 2026-05-25. |

**robots.txt Issues:**
- `User-agent: *` → `Allow: /` ✅ Good
- Sitemap declared: `https://tonicphysio.com/sitemap_index.xml` ✅ Good
- AI training crawlers blocked (ClaudeBot, GPTBot, Google-Extended, etc.) — this is intentional and acceptable

---

## 2. INDEXATION STATUS

| Check | Result |
|-------|--------|
| site:tonicphysio.com | ✅ Indexed. 5 pages visible in search results. |
| Homepage | ✅ Indexed |
| /services/ | ✅ Indexed |
| /contact/ | ✅ Indexed |
| /fees/ | ✅ Indexed |
| Author page (Frank Zhang) | ✅ Indexed |

**Observation:** Good indexation coverage for core pages. No signs of de-indexation or major penalties.

---

## 3. PAGE AUDIT FINDINGS

### 3.1 Homepage (https://tonicphysio.com)

| Check | Status | Detail |
|-------|--------|--------|
| Title Tag | ✅ OK | "Tonic Physiotherapy and Rehabilitation Centre in Milton, CA" |
| Meta Description | ⚠️ CHECK | Fetched via readability; no explicit meta description visible in snippet. Verify via source. |
| H1 Tag | ✅ OK | "Tonic Physio Leading Physiotherapy & Rehab Centre in Milton" |
| Canonical | ⚠️ CHECK | Needs verification in page source |
| Viewport | ⚠️ CHECK | Needs verification in page source |
| Schema Markup | ⚠️ PARTIAL | No JSON-LD schema detected in fetched content. Likely present but not in readability-extracted text. **Requires manual verification.** |
| Image Alt Text | ⚠️ ISSUE | Multiple images present. Some decorative icons may lack alt. **Recommendation:** Audit all `<img>` tags for missing alt attributes. |
| Internal Links | ⚠️ ISSUE | Menu items "Products" and "Programs" link to `#` (empty anchors). These are broken/placeholder links. |
| Mobile Usability | ⚠️ CHECK | Page renders in desktop mode in browser. Need mobile viewport test. |

### 3.2 Services Page (/services/)

| Check | Status | Detail |
|-------|--------|--------|
| Title Tag | ✅ OK | "Physiotherapy & Rehabilitation Services \| Tonic Physio" |
| H1 Tag | ✅ OK | "Treatments We Provide at Tonic Physio" |
| Content | ✅ OK | Comprehensive list of 16+ specialized services with descriptions |
| Image Alt | ⚠️ ISSUE | Service card images detected; verify alt text coverage |
| Internal Links | ✅ OK | All "Learn more" links point to specific treatment pages |

### 3.3 Contact Page (/contact/)

| Check | Status | Detail |
|-------|--------|--------|
| Title Tag | ⚠️ ISSUE | "Tonic Physiotherapy Centre in Milton" — **Missing location keyword optimization** |
| H1 Tag | ✅ OK | "Start Your Recovery Journey Today" |
| CTA | ✅ OK | Clear booking link to JaneApp |
| Content | ⚠️ THIN | Very minimal content. Consider adding: office hours, parking info, nearby landmarks, embedded map. |

### 3.4 Fees Page (/fees/)

| Check | Status | Detail |
|-------|--------|--------|
| Title Tag | ✅ OK | "Fees \| Tonic Physio" |
| H1 Tag | ✅ OK | "Tonic Physio Consultations & Therapy Fees" |
| Content | ⚠️ ISSUE | **Pricing data is missing/placeholder.** All sections show "Reduced pricing is available... please contact us" without actual numbers. This is poor UX and may hurt conversion. |
| Schema | ⚠️ MISSING | No PriceRange or Service schema detected. Add LocalBusiness or Service schema with pricing. |

### 3.5 Physiotherapy in Milton (/physiotherapy-in-milton/)

| Check | Status | Detail |
|-------|--------|--------|
| Title Tag | ✅ GOOD | "Physiotherapy in Milton \| Pain Relief & Rehab – Tonic Physio" — Well-optimized with primary keyword |
| H1 Tag | ✅ OK | Present ("Looking for a reliable physio in Milton?") |
| Content | ✅ GOOD | Substantial content with CPA citation, 5 pillars of physiotherapy, clear value proposition |
| Internal Linking | ✅ OK | Links to treatment pages and booking |

---

## 4. SCHEMA MARKUP AUDIT

| Schema Type | Status | Location |
|-------------|--------|----------|
| LocalBusiness | ⚠️ UNVERIFIED | Likely on homepage but not detectable via fetch. **Must verify in source.** |
| MedicalBusiness / Physiotherapy | ⚠️ UNVERIFIED | Should be present for a physiotherapy clinic |
| Service | ⚠️ MISSING | Not detected on /services/ or treatment pages |
| FAQPage | ⚠️ MISSING | Consider adding to service pages |
| BreadcrumbList | ⚠️ UNVERIFIED | Check if Yoast generates this |
| WebSite | ⚠️ UNVERIFIED | Check if Yoast generates this |
| Organization | ⚠️ UNVERIFIED | Should be on homepage |

**Critical:** The site uses Yoast SEO (evident from sitemap). Yoast typically auto-generates Organization and WebSite schema. However, I cannot confirm schema presence via fetched content alone. **Manual verification required.**

---

## 5. MOBILE USABILITY

| Check | Status | Detail |
|-------|--------|--------|
| Viewport Meta | ⚠️ UNVERIFIED | Cannot confirm from current data |
| Responsive Design | ⚠️ UNVERIFIED | Page snapshot shows desktop layout |
| Touch Targets | ⚠️ UNVERIFIED | Menu items and CTAs need mobile testing |
| Font Size | ⚠️ UNVERIFIED | Need mobile viewport test |

**Recommendation:** Run Google Mobile-Friendly Test and PageSpeed Insights for concrete mobile scores.

---

## 6. PAGE SPEED (Indirect Assessment)

| Indicator | Observation |
|-----------|-------------|
| Load Time (fetch) | ~1.7–3.3s for various pages |
| Cloudflare | Active (evident from robots.txt) — should provide CDN benefits |
| WordPress | Site runs on WordPress with Yoast SEO |
| Image Optimization | ⚠️ CHECK | Hero images and service card images may need WebP/optimized formats |

**Recommendation:** Run Google PageSpeed Insights for Core Web Vitals (LCP, FID, CLS) scores.

---

## 7. ISSUES SUMMARY

### 🔴 CRITICAL (Fix Immediately)
| Issue | Page | Impact | Fix |
|-------|------|--------|-----|
| Missing actual pricing on /fees/ | /fees/ | HIGH — Users leave without booking | Add transparent pricing table or at least starting prices |
| Broken menu links (Products, Programs → #) | All pages | MEDIUM — Navigation dead ends | Either create pages or remove from menu |

### 🟡 MEDIUM (Fix This Week)
| Issue | Page | Impact | Fix |
|-------|------|--------|-----|
| Contact page content is thin | /contact/ | MEDIUM — Low engagement, poor local SEO | Add hours, map, parking, team photo |
| Verify schema markup presence | All | MEDIUM — Rich snippets eligibility | Check source for JSON-LD, validate with Google's Rich Results Test |
| Image alt text audit | All | MEDIUM — Accessibility & image SEO | Audit all images, add descriptive alt text |
| Meta description verification | Homepage | LOW-MEDIUM | Ensure unique, compelling meta descriptions |

### 🟢 LOW (Nice to Have)
| Issue | Page | Impact | Fix |
|-------|------|--------|-----|
| Add FAQ schema to service pages | /services/ | LOW — Enhanced SERP appearance | Add FAQ sections with schema |
| Add PriceRange to LocalBusiness schema | All | LOW — Google knowledge panel | Include price range indicator |
| Author sitemap stale | Sitemap | LOW | Either update or consider removing if not needed |

---

## 8. RECOMMENDED ACTIONS (Priority Order)

1. **Add pricing to /fees/** — This is the highest-impact fix. Even ranges or "starting from" prices help conversions.
2. **Fix broken menu links** — Remove "Products" and "Programs" from nav or create the pages.
3. **Verify schema markup** — Use Google's Rich Results Test on homepage, /services/, and /contact/.
4. **Enrich /contact/ page** — Add business hours, embedded Google Map, parking instructions.
5. **Run PageSpeed Insights** — Get Core Web Vitals baseline and optimize.
6. **Mobile-friendly test** — Confirm responsive behavior across devices.
7. **Image optimization audit** — Check for WebP usage, lazy loading, and alt text.

---

## 9. PREVIOUS AUDIT COMPARISON

*No previous audit found for tonicphysio.com.*

---

**Audit completed:** 2026-05-31 02:00 AM PKT  
**Next audit:** Monday 2026-06-01 (rankray.com)
