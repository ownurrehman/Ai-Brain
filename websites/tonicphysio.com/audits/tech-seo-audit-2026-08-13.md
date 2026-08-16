# tonicphysio.com — live tech SEO audit

**Date:** 2026-08-13  
**Method:** Live HTML fetch (home, /fees/, /contact/, robots, sitemaps).  
**Raw:** `system/reports/_live-audit-2026-08-13.json`

## Snapshot

| Check | Result |
|-------|--------|
| Homepage | 200 · 223 KB · Cloudflare |
| Title | `Tonic Physiotherapy and Rehabilitation Centre in Milton, CA` (59c) |
| Meta description | 131c |
| H1 | `Tonic Physio Leading Physiotherapy & Rehab Centre in Milton` |
| Schema (home) | MedicalOrganization, WebSite |
| Images | 15 · **0** missing alt |
| Sitemaps | 4 indexes · **74 posts** · **89 pages** |
| /fees/ | 200 — **real prices are live** ($120 initial, follow-ups, RMT, orthotics, etc.) |
| /contact/ | 200 — ContactPage + MedicalOrganization schema |

## Critical

1. **Title says “Milton, CA”** — clinic is Milton, **Ontario**. “CA” reads as California. Wrong geo in the title tag.

## High (still open)

1. **Products and Programs nav** still `href="#"` on home, fees, and contact.

## Medium

1. Home meta description is generic (no Milton / physio keyword in the first clause).
2. Fees page uses `#` anchors for price cards (OK as in-page, but noisy).
3. robots.txt is Cloudflare content-signal boilerplate, not a simple Yoast allow-all (confirm sitemap is still advertised — sitemap_index.xml itself is 200).

## Fixed since 2026-05-31

- **/fees/ pricing** — no longer “contact us” placeholders; dollar amounts are on the page.
- Contact page has ContactPage schema.

## Priority

1. Change title to Milton, Ontario (or “Milton, ON”).
2. Point Products / Programs to real URLs or remove those items.
