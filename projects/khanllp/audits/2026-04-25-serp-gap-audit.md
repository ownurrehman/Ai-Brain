# Khan LLP SERP Gap Audit — 2026-04-25

## Site Overview
- **Platform**: Custom-built (not WordPress) on Apache/Ubuntu
- **Domain**: khanllp.com (no www redirect)
- **Sitemap**: /sitemap.xml (custom, not Yoast) — 227 URLs (78 pages + 149 blogs)
- **robots.txt**: Present, well-structured with disallow rules
- **Divi theme**: Used (detected in page source)

---

## Schema Markup — Mixed

| Page Type | Schema Found | Status |
|-----------|-------------|--------|
| Homepage | `WebPage` (JSON-LD) | **Too generic** |
| Service pages (real-estate, family, immigration) | `Service` + `Organization` + `BreadcrumbList` + `FAQPage` (JSON-LD) | **Excellent** |
| Location pages (real-estate-lawyer-milton, etc.) | `LocalBusiness` + `Service` (JSON-LD) | **Good** |
| Blog detail pages | `WebPage` (JSON-LD) only | **Missing Article/BlogPosting** |
| Blog archive | `BlogPosting` (microdata on blog cards) | **Partial** |

## Schema Gaps

### 1. Homepage Schema — MEDIUM
- Only `WebPage` type — should be `LegalService` or `Attorney` with NAP
- Missing: `OpeningHours`, `address`, `telephone`, `areaServed`
- Location pages have richer schema than homepage

### 2. Blog Article Schema — MEDIUM
- Blog archive listing cards have `BlogPosting` microdata (good)
- Individual blog detail pages have only `WebPage` JSON-LD
- Should include `Article` or `BlogPosting` with `headline`, `datePublished`, `author` in JSON-LD
- 149 blog posts not eligible for rich results

### 3. Location Pages — INCONSISTENT
- Some have `LocalBusiness` + `Service`, others missing `BreadcrumbList` and `FAQPage`
- Not all location pages include `FAQPage`

---

## Content Gaps

| Metric | Status |
|--------|--------|
| Pages | 78 (service pages, location pages, practice areas) |
| Blog posts | 149 (strong content library) |
| Blog freshness | **Appears good** — recent topics covering 2025-2026 issues |
| Meta titles | Present, keyword-focused, include city names |
| Meta descriptions | Present |

## Content Opportunities
- **Missing practice area**: Criminal law pages exist but schema is unknown
- **City landing pages**: Strong coverage — Milton, Mississauga, Toronto, Oakville, North York

---

## Priority Actions

| Priority | Issue | Effort | Impact |
|----------|-------|--------|--------|
| **P0** | Add `LegalService`/`Attorney` schema to homepage (with NAP) | 30min | High |
| **P1** | Add `Article`/`BlogPosting` JSON-LD to blog detail pages (149 posts) | 2h | High |
| **P2** | Standardize location page schema (uniformly add BreadcrumbList + FAQPage) | 1h | Medium |
| **P3** | Add www → non-www or www canonical redirect | 15min | Low |

## Immediate Wins
1. Update homepage JSON-LD from `WebPage` to `LegalService` with address + phone
2. Add `Article` schema template to blog detail template
3. Ensure all location pages have `BreadcrumbList` + `FAQPage`
