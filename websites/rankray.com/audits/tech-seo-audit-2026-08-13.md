# rankray.com — live tech SEO audit

**Date:** 2026-08-13  
**Method:** HTTP fetch of live HTML (homepage, About, Digital Marketing Services, SEO services, robots, sitemaps). Not GSC/PSI (PSI quota previously exhausted).  
**Raw:** `system/reports/_live-audit-2026-08-13.json`

## Snapshot

| Check | Result |
|-------|--------|
| Homepage | 200 · 224 KB · LiteSpeed |
| Title | `Rank Ray \| Full Service Digital Marketing, AI & SEO Agency` (58c) |
| Meta description | 154c, indexable |
| H1 | `Result Driven Digital Marketing Agency` (1) |
| Canonical | `https://rankray.com/` |
| Robots.txt | Yoast allow-all + sitemap |
| Sitemaps | 5 indexes · **148 posts** · **70 pages** |
| Schema (home) | Organization, WebSite |
| Images | 27 · 1 missing alt |

`/seo-services/` now resolves to `/digital-marketing-services/search-engine-optimization-seo/` (200). About has AboutPage + LocalBusiness schema.

## Critical (still open)

1. **Header nav dead links** — “Services” and “Web & Apps” still `href="#"`. Same on inner pages. Hurts crawl paths and UX.
2. **Case-study placeholders** — Digital Marketing Services and SEO service pages still link “Law Firm”, “Ecommerce Store”, “Real Estate Portal” to `#`.

## Medium

1. **Footer social incomplete** — Facebook and Pinterest look real. Twitter still `twitter.com/Rankrayofficial` (not x.com). No YouTube profile URL on the homepage. LinkedIn not in footer (only LinkedIn-ads service pages).
2. **Home schema thin** — Organization + WebSite only; LocalBusiness/FAQ sit on inner pages.
3. **About H1 is generic** (“About Us”) — does not sell an outcome.

## Low

1. One homepage image missing alt.
2. PSI / CWV not re-run this session.

## Priority

1. Point Services → `/digital-marketing-services/` and Web & Apps to a real URL or remove.
2. Replace case-study `#` links with real URLs or remove the blocks.
3. Add LinkedIn + YouTube footer URLs; switch Twitter to x.com if the profile moved.
