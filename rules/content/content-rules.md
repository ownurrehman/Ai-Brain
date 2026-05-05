# Content Rules

MANDATORY for ALL agents, ALL projects, ALL content types. No exceptions.

## HARD STOPS (never push if violated)

1. **No H1 in body.** WordPress title is the only H1. Body starts with `<p>` or `<h2>`.
2. **2,000+ words minimum.** Pillar content: 3,000-5,000. No padding to hit count.
3. **Internal links: 10+ minimum.** 5+ to service pages, 5+ to blog posts. Fetch sitemap first.
4. **Yoast fields MUST be set before push:**
   - `yoast_focuskw`: exact primary keyword
   - `yoast_title`: <60 chars, includes keyword + brand
   - `yoast_metadesc`: <160 chars, includes keyword + LSI + brand
5. **No em-dashes or en-dashes.** Use hyphens (-) or colons (:).
6. **Status: DRAFT only.** Never publish without user approval.
7. **No duplicate images.** Search Media Library before uploading. 1 featured image only.

If ANY of these fail, DO NOT push. Fix first.

---

## Writing Quality

- No repeated words consecutively ("Understanding Understanding")
- No duplicate paragraphs or concept repetition (every paragraph adds NEW info)
- No filler intros: "In today's digital landscape...", "It is important to note..."
- No AI shortcodes like `[rankray_ai_summary]`
- Active voice 80%+. Second person ("you"/"your"). Varied sentence length.
- H1 must differ from Title Tag (Title is for SEO, H1 is for reader)
- Heading hierarchy: H2 for main sections (6-10), H3 for sub-sections, never skip levels

## Internal Links

- Fetch sitemap BEFORE writing to find real URLs
- Never link same URL twice in one article
- Natural anchor text, not keyword-stuffed
- Every link must be contextually relevant to the paragraph
- Distribution: service pages (50%) + blog posts (50%)
- Word count scaling: 2000w = 10 links, 3000w = 15 links, 5000w = 20 links
- "Rank Ray" anchor text links to homepage ONLY (for RankRay content)

## Deduplication Gate

Before drafting: verify the topic is not already covered by an existing or planned article.
- If keywords share >40% core entities with another topic: MERGE into one pillar
- Never publish two articles where the "What is [X]" section is identical

## Pre-Push Sequence

Run this checklist IN ORDER before every push:

```
1. [ ] Body contains zero <h1> tags
2. [ ] Word count >= 2,000
3. [ ] Internal links >= 10 (5 service + 5 blog minimum)
4. [ ] Yoast focus keyword SET
5. [ ] Yoast meta title SET and < 60 chars
6. [ ] Yoast meta description SET and < 160 chars
7. [ ] No em-dashes found (search the content)
8. [ ] No repeated words found
9. [ ] Markdown converted to HTML
10.[ ] Featured image uploaded with alt text
11.[ ] Status = Draft
12.[ ] Post ID + slug logged in /Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/{project}/post-registry.md
```

## Brand Voice

- **RankRay:** Professional, authoritative, data-driven
- **TonicPhysio:** Caring, professional, health-focused
- **TeamMotorcycle:** Enthusiast, technical, community-focused
- **KhanLLP:** Professional, trustworthy, legal expertise
- **Coinsfera:** Crypto-savvy, international, professional

---
Last Updated: 2026-05-05
