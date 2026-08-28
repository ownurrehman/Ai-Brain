> **Parent Site:** [[websites/teammotorcycle.com/index|🌐 teammotorcycle.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# teammotorcycle.com — live tech SEO audit

**Date:** 2026-08-13  
**Method:** Live HTML fetch (www home, robots, sitemap index, one product, one collection).  
**Raw:** `system/reports/_live-audit-2026-08-13.json`

## Snapshot

| Check | Result |
|-------|--------|
| Home | 200 · **630 KB** · Cloudflare DYNAMIC · canonical www |
| Apex | `teammotorcycle.com` → `www.teammotorcycle.com` |
| Sitemaps | Shopify index (products ×3, collections, pages, blogs, agentic discovery) |
| Product sample | Product **+ Offer + ProductGroup + Brand** present |
| Collection sample `/collections/motorcycle-helmets` | BreadcrumbList + ListItem **only** |
| Home images | 111 · **59 missing alt** |

## Critical

1. **Title tag polluted** — live title concatenates payment-icon alt text:  
   `Buy Motorcycle Gear Online with Reviews and Guides - Team Motorcycle Amazon American Express Apple Pay … Visa` (**167 characters**). Search snippets will look broken.

## High

1. **59 homepage images missing alt** (payment sprites likely the same root cause as the title pollution).
2. **Collection pages still lack CollectionPage / ItemList** schema (unchanged from April 2026).

## Fixed since 2026-04-25

- Product JSON-LD now includes **Product** and **Offer** (not ProductGroup-only).

## Medium

1. Home meta description has leading whitespace; no `robots` meta on home (Shopify default).
2. Page weight 630 KB HTML before assets — watch TTFB/LCP (PSI not run).

## Priority

1. Stop payment-method image alts from leaking into `<title>` (theme / accessibility widget).
2. Add CollectionPage + ItemList on collection templates.
3. Alt-text pass on homepage icons (empty alt on decorative payment marks).
