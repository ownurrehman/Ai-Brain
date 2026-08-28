---
name: seo-aeo-schema-generator
description: "Generates valid JSON-LD structured data for 10 schema types with rich result eligibility validation and implementation-ready script blocks. Activate when the user wants to generate schema markup, JSON-LD, or structured data for any page."
risk: safe
source: community
date_added: "2026-04-01"
---

# SEO-AEO Schema Generator

## Overview

Generates implementation-ready JSON-LD schema markup for 9 schema types including Article, Product, HowTo, and BreadcrumbList. Validates all required fields against Google rich result eligibility rules, flags missing fields with exact fix instructions, and outputs one clean `<script>` block per schema type ready to paste into the page `<head>`.

Part of the [SEO-AEO Engine](https://github.com/mrprewsh/seo-aeo-engine).

## When to Use This Skill

- Use when adding structured data to a new landing page or blog post
- Use when a page needs product star ratings or HowTo rich results in search
- Use when validating existing schema for Google rich result eligibility
- Use after the content-quality-auditor flags missing schema

## Supported Schema Types

| Type | Rich Result Unlocked |
|------|---------------------|
| Article | Article rich result, Top Stories |
| Product | Price, availability, rating in SERP |
| HowTo | Step-by-step rich result |
| Review | Star rating in SERP |
| AggregateRating | Star rating with review count |
| BreadcrumbList | Breadcrumb path in SERP URL |
| Organization | Brand knowledge panel signals |
| WebPage | Enhanced page understanding |
| WebSite | Sitelinks Searchbox |

## How It Works

### Step 1: Recommend Schema Types
If schema types are not specified, recommend the appropriate types based on the page type. Landing pages get Product + BreadcrumbList + HowTo. Blog posts get Article + BreadcrumbList.

### Step 2: Use Built-In Schema Templates
Using your knowledge of schema.org and Google's rich result requirements, construct the JSON-LD template for each requested schema type. Use the required and recommended fields listed in the Google Rich Results documentation for that type.

### Step 3: Populate Fields
Map all page data to template placeholders. Check every required field against the rich result eligibility rules.

### Step 4: Validate
Flag any missing required field as a Critical issue. Flag missing recommended fields as warnings. Do not output schema with missing required fields.

### Step 5: Output Script Blocks
Write one `<script type="application/ld+json">` block per schema type. Include implementation instructions and testing tool links.

## Examples

### Example: HowTo Schema Output
```html
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@type": "HowTo",
  "name": "How to Optimize Your Site for AI Search Engines",
  "description": "A step-by-step guide to GEO and semantic SEO implementation.",
  "step": [
    {
      "@type": "HowToStep",
      "name": "Audit Current Content",
      "text": "Review existing pages for entity coverage, schema markup, and topical gaps."
    },
    {
      "@type": "HowToStep",
      "name": "Build a Topical Map",
      "text": "Map pillar topics and supporting subtopics aligned with search intent."
    }
  ]
}
</script>
```

## Best Practices

- ✅ **Do:** Use one `<script>` block per schema type — never combine multiple types
- ✅ **Do:** Test every output in Google's Rich Results Test before deploying
- ❌ **Don't:** Use relative URLs anywhere in schema — all URLs must start with `https://`
- ❌ **Don't:** Leave placeholder text in any field before deploying
- ❌ **Don't:** Use HTML tags inside JSON-LD string values

## Common Pitfalls

- **Problem:** Schema passes validation but rich result doesn't appear in search
  **Solution:** Rich results can take weeks to appear after deployment. Request re-indexing in Google Search Console immediately after adding schema.

- **Problem:** Product schema missing star rating display
  **Solution:** Add AggregateRating object with ratingValue, reviewCount, bestRating, and worstRating — all four fields required.

## Related Skills

- `@seo-aeo-landing-page-writer` — provides product data and HowTo steps for schema population
- `@seo-aeo-content-quality-auditor` — flags schema gaps during the audit

## Additional Resources

- [SEO-AEO Engine Repository](https://github.com/mrprewsh/seo-aeo-engine)
- [Full Schema Generator SKILL.md](https://github.com/mrprewsh/seo-aeo-engine/blob/main/.agent/skills/schema-generator/SKILL.md)

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.
