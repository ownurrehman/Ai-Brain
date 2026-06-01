# Technical SEO Audit Master Sheet

**Last Updated:** 2026-06-01  
**Current Rotation:** Monday → rankray.com

---

## Audit History

| Date | Site | Status | Critical Issues | Medium Issues | Low Issues |
|------|------|--------|----------------|---------------|------------|
| 2026-05-31 | tonicphysio.com | ✅ Audited | 2 | 4 | 3 |
| 2026-06-01 | rankray.com | ✅ Audited | 2 | 4 | 4 |

---

## rankray.com — 2026-06-01

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK (Yoast SEO index with 5 sub-sitemaps)
- **Robots.txt:** ✅ OK (all allowed, sitemap referenced)
- **Indexation:** ✅ Good (10+ pages indexed)
- **Schema:** ⚠️ Unverified (Yoast likely generating; needs manual check)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Not measured (needs PSI)

### Critical Issues (2)
1. **Broken "Services" nav link** — Points to `#` across all pages. Hurts UX and crawlability.
2. **Social media URL mismatches** — LinkedIn, YouTube, and Pinterest footer links all redirect to the same Pinterest URL. Twitter link uses old `twitter.com` domain.

### Medium Issues (4)
1. **About Us page thin content** — Only ~5 short paragraphs; lacks team photos, history, or structured data.
2. **Duplicate Vancouver link** — Canada footer lists Vancouver twice with identical URLs.
3. **Missing dedicated Services nav target** — `/digital-marketing-services/` exists but is not linked in top nav.
4. **Case study placeholders** — On SEO services page, "Law Firm", "Real Estate Portal", and "Ecommerce Store" blocks link to `#`.

### Low Issues (4)
1. Author sitemap stale (lastmod 2026-05-12)
2. Old Twitter domain in footer (`twitter.com` vs `x.com`)
3. Some image alt text generic (client logos)
4. Title tags appeared empty in browser snapshots (verify in source)

### Recommended Fixes (Priority)
1. Fix "Services" nav link to `/digital-marketing-services/`
2. Correct LinkedIn and YouTube footer URLs to actual profiles
3. Enrich `/about-us/` with photos, timeline, and AboutPage schema
4. Fix or remove case study placeholder links
5. Remove duplicate Vancouver footer link
6. Verify schema with Google Rich Results Test
7. Run PageSpeed Insights + Mobile-Friendly Test

---

## tonicphysio.com — 2026-05-31

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK
- **Robots.txt:** ✅ OK
- **Indexation:** ✅ Good (5+ pages indexed)
- **Schema:** ⚠️ Unverified (Yoast likely generating, needs manual check)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Needs PSI test

### Critical Issues (2)
1. **Missing pricing on /fees/** — All fee sections show placeholder "contact us" text. No actual prices.
2. **Broken nav links** — "Products" and "Programs" menu items link to `#` (empty anchors).

### Medium Issues (4)
1. **Thin contact page** — Minimal content, missing hours/map/parking.
2. **Schema unverified** — Cannot confirm JSON-LD presence via automated fetch.
3. **Image alt text** — Needs audit across all pages.
4. **Meta descriptions** — Need verification on homepage.

### Low Issues (3)
1. FAQ schema missing on service pages
2. PriceRange not in LocalBusiness schema
3. Author sitemap stale (lastmod 2026-05-12)

### Recommended Fixes (Priority)
1. Add transparent pricing to /fees/ page
2. Fix or remove broken nav links (Products, Programs)
3. Verify schema with Google's Rich Results Test
4. Enrich /contact/ with hours, map, parking info
5. Run PageSpeed Insights + Mobile-Friendly Test
6. Audit all image alt text

---

## Sites in Rotation

| Day | Site | Last Audit | Next Audit |
|-----|------|------------|------------|
| Monday | rankray.com | 2026-06-01 | 2026-06-08 |
| Tuesday | tonicphysio.com | 2026-05-31 | 2026-06-02 |
| Wednesday | coinsfera.com | — | 2026-06-03 |
| Thursday | teammotorcycle.com | — | 2026-06-04 |
| Friday | rankray.com | 2026-06-01 | 2026-06-05 |
| Weekend | tonicphysio.com | 2026-05-31 | 2026-06-07 |

---

*This sheet is updated automatically after each audit.*
