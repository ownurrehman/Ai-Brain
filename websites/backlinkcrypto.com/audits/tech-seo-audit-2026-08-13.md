> **Parent Site:** [[websites/backlinkcrypto.com/index|🌐 backlinkcrypto.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# backlinkcrypto.com — live tech SEO audit

**Date:** 2026-08-13  
**Method:** Live HTML fetch (home, robots, sitemap.xml).  
**Raw:** `system/reports/_live-audit-2026-08-13.json`

## Snapshot

| Check | Result |
|-------|--------|
| Home | 200 · 76 KB · Hostinger CDN cache HIT |
| Title | 62c — crypto backlinks + DA/DR filters |
| Meta description | 138c |
| H1 | Verified crypto backlinks for exchanges, DeFi & Web3. |
| Schema | BreadcrumbList, FAQPage, Organization, WebPage, WebSite |
| Images | 1 · 0 missing alt |
| Robots | Allow / · Disallow cart, checkout, my-account, add-to-cart |
| Sitemap | `sitemap.xml` (6 loc entries; AIOSEO-style, not Yoast index) |

## Issues

1. Title 62c (trim to ≤60).
2. Homepage has almost no `<img>` in the HTML snapshot (1) — confirm catalog cards are CSS/background or JS-injected (indexation of product images).
3. No LocalBusiness (not needed). No Product schema on home (OK if catalog is custom, but product URLs should carry Offer schema — not sampled this run).

## Priority

1. Confirm marketplace listing URLs are in the 6 sitemap children and have Product/Offer JSON-LD.
2. Shorten title.
