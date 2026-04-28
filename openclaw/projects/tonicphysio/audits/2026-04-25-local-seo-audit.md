# Tonic Physio Local SEO Audit — 2026-04-25

## Site Overview
- **Total URLs**: ~55 (12 pages + ~11 service/therapist sub-pages + ~45 guides/blog posts)
- **Yoast SEO**: Active (v27.4) across all pages
- **Last homepage update**: Apr 11, 2026

---

## Schema Markup — GOOD (mostly)

| Page Type | Schema Types Found | Status |
|-----------|-------------------|--------|
| Homepage | LocalBusiness, WebSite, MedicalOrganization | Good |
| Service pages | Service, medicalclinic, FAQPage, WebSite, BreadcrumbList, MedicalOrganization | Excellent |
| /faq/ | FAQPage, Service, medicalclinic, WebSite, BreadcrumbList, MedicalOrganization | Excellent |
| /contact/ | ContactPage, Service, medicalclinic, WebSite, BreadcrumbList, MedicalOrganization | Excellent |
| Blog/guides | WebSite, BreadcrumbList, MedicalOrganization | **MISSING Article/BlogPosting** |
| Therapist pages | Not checked | Needs verification |

**Blog Article schema gap**: All 45+ blog posts are missing Article/BlogPosting schema. Yoast should output it by default — likely needs schema settings enabled for posts.

---

## NAP Consistency — GOOD
- Address/city "Milton" + area code "905-" found consistently across homepage
- Google Maps embed on contact page
- Phone numbers visible on page

## Meta Data — GOOD
- All checked pages have unique meta titles and descriptions
- Titles include city "Milton" + service keywords
- Descriptions under control

---

## Content & Freshness
| Metric | Status |
|--------|--------|
| Blog posts count | ~45 guides/news |
| Last post update | ~Oct 2025 (6 months ago) |
| Service page updates | Apr 10-24, 2026 (fresh) |
| Content depth | Good — detailed guides |

**Gap**: Blog content is 6+ months old. No new posts in 2026.

---

## Top Priority Actions

1. **P0** — Enable Article/BlogPosting schema for blog posts via Yoast settings
2. **P1** — Add a new blog post this month (target: "physiotherapy for [seasonal] pain" or similar)
3. **P2** — Update existing blog post dates to signal freshness
4. **P3** — Verify therapist pages have local schema
