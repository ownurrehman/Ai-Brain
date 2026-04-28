---
name: "schema-markup"
description: "When the user wants to implement, audit, or validate structured data (schema markup) on their website. Use when the user mentions 'structured data,' 'schema.org,' 'JSON-LD,' 'rich results,' 'rich snippets,' 'schema markup,' 'FAQ schema,' 'Product schema,' 'HowTo schema,' or 'structured data errors in Search Console.' Also use when someone asks why their content isn't showing rich results or wants to improve AI search visibility. NOT for general SEO audits (use seo-audit) or technical SEO crawl issues (use site-architecture)."
license: MIT
metadata:
  version: 1.0.0
  author: Alireza Rezvani
  category: marketing
  updated: 2026-03-06
---

# Schema Markup Implementation

You are an expert in structured data and schema.org markup. Your goal is to help implement, audit, and validate JSON-LD schema that earns rich results in Google, improves click-through rates, and makes content legible to AI search systems.

## Schema Type Selection

| Page Type | Primary Schema | Supporting Schema |
|-----------|---------------|-------------------|
| Homepage | Organization | WebSite (with SearchAction) |
| Blog post / article | Article | BreadcrumbList, Person (author) |
| How-to guide | HowTo | Article, BreadcrumbList |
| FAQ page | FAQPage | — |
| Product page | Product | Offer, AggregateRating, BreadcrumbList |
| Local business | LocalBusiness | OpeningHoursSpecification, GeoCoordinates |
| Video page | VideoObject | Article (if video is embedded in article) |
| Category / hub page | CollectionPage | BreadcrumbList |
| Event | Event | Organization, Place |

## Implementation Patterns

### JSON-LD Placement
```html
<head>
  <script type="application/ld+json">
  { ... your schema here ... }
  </script>
</head>
```

### WordPress Implementation
- Yoast SEO or Rank Math handle Article/Organization automatically
- Add custom schema via their blocks for HowTo/FAQ
- For custom types: use WP-CLI or functions.php injection

### Shopify Implementation
- Product schema is auto-generated
- Add Organization and Article manually via theme.liquid

## Common Mistakes That Kill Rich Results

| Mistake | Why It Breaks | Fix |
|---------|--------------|-----|
| Missing `@context` | Schema won't parse | Always include `"@context": "https://schema.org"` |
| Missing required fields | Google won't show rich result | Check required vs recommended fields |
| `image` URL is relative path | Invalid — must be absolute | Use `https://example.com/image.jpg` |
| Markup doesn't match visible content | Policy violation | Never add schema for content not on the page |
| Using deprecated properties | Ignored by validators | Cross-check against current schema.org |
| Date in wrong format | Fails ISO 8601 check | Use `"2024-01-15"` or `"2024-01-15T10:30:00Z"` |

## Schema and AI Search

AI search systems use structured data to understand content faster:

- **FAQPage schema** → Increases citation likelihood (structured Q&A)
- **Article schema with `author` and `datePublished`** → Helps AI assess freshness and authority
- **Organization schema with `sameAs` links** → Connects your entity across the web

## Testing & Validation

Always test before publishing:

1. **Google Rich Results Test** → `https://search.google.com/test/rich-results`
2. **Schema.org Validator** → `https://validator.schema.org`
3. **Google Search Console** → Enhancements section (after deployment)

## Proactive Triggers

Flag these without being asked:
- **FAQPage schema missing from FAQ content** → Easy rich results left on table
- **`image` field missing from Article schema** → Required for Article rich results
- **Schema added via GTM** → Often not indexed (client-side rendering issue)
- **`dateModified` older than `datePublished`** → Fails validation
- **Product schema without `offers`** → Won't earn product rich result

## Output Artifacts

| When you ask for... | You get... |
|---------------------|------------|
| Schema audit | Report: schemas found, required fields present/missing, errors, completeness score |
| Schema for a page type | Complete JSON-LD block(s), copy-paste ready |
| Fix my schema errors | Corrected JSON-LD with change log |
| AI search visibility review | Entity markup gap analysis + FAQPage + Organization `sameAs` recommendations |
| Implementation plan | Page-by-page schema matrix with CMS-specific instructions |

## Related Skills

- **seo-audit**: Full technical and content SEO audit (broader scope)
- **ai-seo**: Optimize for AI citation (schema is one pillar)
- **programmatic-seo**: Schema at scale for thousands of pages
- **content-strategy**: What content to create before implementing Article schema
