> **Parent Site:** [[websites/teammotorcycle.com/index|🌐 teammotorcycle.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Team Motorcycle Ecommerce SEO Audit — 2026-04-25

## Site Overview
- **Platform**: Shopify
- **Domain**: teammotorcycle.com → www.teammotorcycle.com (301 redirect, good)
- **Total Products**: 2,149
- **Collections**: 430
- **Pages**: 85 (mostly size charts, info pages)
- **Blog Posts**: ~360 (guides + news)
- **Yoast/SEO plugin**: None — native Shopify SEO

---

## Schema Markup

| Page Type | Schema Found | Status |
|-----------|-------------|--------|
| Homepage | BreadcrumbList, WebSite, Organization | **Good** |
| Product pages | ProductGroup, BreadcrumbList | **ProductGroup only — missing Product + Offer** |
| Collection pages | BreadcrumbList | **Missing CollectionPage/ItemList** |
| Blog posts | Article, BreadcrumbList | **Good** |

### Product Schema Gap (HIGH PRIORITY)
Product pages use `ProductGroup` schema but are missing:
- `Product` type with `offers` (price, priceCurrency, availability)
- `AggregateRating` (if reviews exist)
- Without Offer/Product markup, products won't show price/availability in rich results

### Collection Schema Gap (MEDIUM PRIORITY)
- Collections have only `BreadcrumbList`
- Missing `CollectionPage` schema
- Missing `ItemList` for products within collections

### Blog Article Schema — GOOD
- Article schema present with BreadcrumbList
- Content is recent (2025-2026 posts for guides)

---

## Technical SEO

| Check | Status |
|-------|--------|
| robots.txt | Good — Shopify default, properly disallows admin/cart/checkout |
| Sitemap | Good — auto-generated Shopify sitemap with products, collections, pages, blogs |
| WWW canonical | Good — non-www → www 301 redirect |
| Page speed | Not tested (Shopify CDN) |
| Product images | Shopify handles automatically (need to check alt text) |

---

## Content & Structure

| Metric | Value |
|--------|-------|
| Products | 2,149 — healthy catalog size |
| Collections | 430 — well-organized (helmets, jackets, vests, gloves, pants, boots, luggage, accessories, brands, sales) |
| Blog posts | 360+ — strong content hub |
| Blog freshness | **Good** — posts throughout 2025 and early 2026 |
| Meta titles/descs | Present on homepage, products, collections, blog |

---

## Gaps & Priority Actions

| Priority | Issue | Effort | Impact |
|----------|-------|--------|--------|
| **P0** | Product pages use ProductGroup — need **Product + Offer** schema for price/availability rich results | Theme edit | High |
| **P1** | Collections missing **CollectionPage + ItemList** schema | Theme edit | Medium |
| **P2** | Blog Article schema present but could include author/datePublished refinement | Low effort | Low |
| **P3** | Product images should be checked for descriptive alt text | Manual check | Medium |
| **P4** | Consider FAQPage schema for /pages/frequently-asked-questions | Low effort | Medium |

## Immediate Action
1. Add `Product` + `Offer` structured data to product pages (Shopify theme liquid edit)
2. Add `CollectionPage` schema to collection template
