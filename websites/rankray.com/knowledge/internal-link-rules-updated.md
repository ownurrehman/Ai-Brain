> **Parent Site:** [[websites/rankray.com/index|🌐 rankray.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# Rank Ray Internal Linking Rules (Updated 2026-05-04)

## MINIMUM REQUIREMENTS

**Per Article:**
- 3000+ word pillar content: **15-20 internal links**
- 2000-3000 word articles: **10-15 internal links**
- Under 2000 words: **10 internal links minimum**

**Link Distribution (Minimums):**
- **5+ Service page links** (link to relevant service landing pages)
- **5+ Blog post links** (link to related articles and guides)
- Optional: 1-2 pillar content links, 1-2 category/tag links

## SERVICE PAGES TO LINK

| Service | URL | When to Link |
|---------|-----|-------------|
| SEO Services | /digital-marketing-services/search-engine-optimization-seo/ | Any SEO-related article |
| GEO Services | /digital-marketing-services/generative-engine-optimization-geo/ | Any AI/GEO article |
| Semantic SEO | /digital-marketing-services/semantic-seo-services/ | Entity/semantic content |
| Technical SEO | /digital-marketing-services/technical-seo/ | Technical topics |
| Local SEO | /digital-marketing-services/local-seo/ | Local content |
| Ecommerce SEO | /digital-marketing-services/ecommerce-seo/ | Ecommerce topics |
| Link Building | /digital-marketing-services/link-building/ | Off-page content |
| Content Marketing | /digital-marketing-services/content-marketing/ | Content strategy |
| PPC | /digital-marketing-services/pay-per-click-ppc/ | Paid media content |
| Web Design | /digital-marketing-services/web-design/ | Web-related content |
| AI Automation | /digital-marketing-services/ai-automation/ | AI/automation topics |
| CRO | /digital-marketing-services/conversion-rate-optimization/ | Conversion topics |

## KEY BLOG POSTS TO LINK (Pillars)

| Post | URL | Topic |
|------|-----|-------|
| GEO Guide | /generative-engine-optimization-geo-guide/ | AI/GEO pillar |
| Semantic SEO | /what-is-semantic-seo-complete-guide/ | Semantic pillar |
| Technical SEO | /what-is-technical-seo/ | Technical pillar |
| Local SEO | /local-seo-complete-guide/ | Local pillar |
| On-Page SEO | /on-page-seo-guide/ | On-page pillar |
| Content Strategy | /content-strategy-guide/ | Content pillar |
| Link Building | /link-building-guide/ | Off-page pillar |
| SEO Checklist | /seo-checklist-for-website-success/ | General SEO |

## INTERNAL LINK RULES (CRITICAL)

**Duplicate Prevention:**
- **ZERO duplicate URLs allowed** within any single article/page
- Each service page URL can appear **maximum 1 time** in body content
- Navigation menu links are template-level and do NOT count toward the minimum
- Violation example: Linking to /search-engine-optimization-seo/ 5 times in different paragraphs = 4 duplicate violations

**Service Page Link Distribution:**
- Link to related services **only where contextually natural** — no quotas, no forced minimums
- Map links to related service verticals when the paragraph topic connects
- Spread links across different paragraphs and sections — never cluster in one area
- Every service paragraph that mentions a related discipline should link to that service page (if it doesn't, no link needed)

**Anchor Text Rules:**
- Use descriptive anchor text that describes the destination (not just "click here")
- Vary anchor text naturally ("SEO services", "search engine optimization", "SEO company") for the same URL across DIFFERENT pages
- Never use identical anchor text + URL combination more than once on a page
- Contextually relevant: the linked text must relate to the paragraph topic

## VERIFICATION
- Before publishing, count internal links in content
- No duplicate URLs within same article — verify with `grep -o 'href="[^"]*"' content | sort | uniq -d` returns empty
- Natural anchor text (not keyword-stuffed)
- Contextually relevant to paragraph content
