# Tonic Physio Content Guidelines

**Site:** tonicphysio.com  
**Location:** Milton, Ontario, Canada  
**Last Updated:** 2026-06-01

---

## Mandatory Rules for All Tonic Physio Content

### 1. AI Answer Engine Optimization (AEO)

- Content must be optimized for AI answers and generative engine citations
- Every section must be extractable as a standalone answer
- No "as mentioned above" or cross-references between sections
- Write for Perplexity, ChatGPT, Gemini extraction — not just Google

### 2. Paragraph & Structure Rules

- **Maximum 3 sentences per paragraph**
- **Maximum 60 words per paragraph**
- Divide content into multiple headings and subheadings for clarity and AI understanding
- Every H2 section MUST contain 1-3 H3 subsections minimum
- Maximum 300 words between headings
- One-idea-per-paragraph: each paragraph makes exactly one point

### 3. Content Density Formula

- 300-400 words per H2 section (including H3s and lists)
- 80-120 words per H3 subsection
- 20-40% of total words in list/table format (not paragraph prose)
- Target: reader can skim headings + bold text + lists and get 80% of value

### 4. Featured Image Rules

- **1 featured image per blog post only**
- Featured image MUST have the blog's focused keyword in its alt text
- Alt text format: "[Focus Keyword] - [Brief Description]"
- Example: `back pain recovery Milton - physiotherapist treating patient at Tonic Physio Milton`
- Source NEW images from Pexels/Unsplash/Pixabay — never reuse existing media
- Verify alt text is set via REST API immediately after upload
- Verify image displays correctly on frontend after publishing

### 5. Post-Publication Verification (CRITICAL)

After finishing ANY blog, ALWAYS:

- [ ] Open the live link and read the published content
- [ ] Check for repetitive words and sentences
- [ ] Check for spelling mistakes and grammar issues
- [ ] Verify all internal links work and point to correct pages
- [ ] Confirm featured image displays with correct alt text
- [ ] Check mobile rendering
- [ ] Verify Yoast title, meta description, and focus keyword render correctly
- [ ] Check that no H1 appears in body content (WordPress title only)
- [ ] Verify categories are set correctly (not default "Topics")

### 6. Local SEO Requirements

- Include "Milton" and nearby cities: Oakville, Burlington, Georgetown, Halton
- Reference local landmarks where natural: Milton Conservation Area, Main St, community centres
- Use "near me" synonyms: "physiotherapy near me", "back pain treatment near Milton"
- Mention OHIP coverage, WSIB claims, extended health insurance where relevant
- Include Ontario-specific health system context

### 7. Skill Loading Requirement

Before writing ANY Tonic Physio content:

| Content Type | Required Skill |
|--------------|---------------|
| ALL content/SEO work | `rankray-seo-content-mastery` at `~/.hermes/profiles/enigma/skills/seo/rankray-seo-content-mastery/SKILL.md` (consolidated master skill, 2026-08-28) |

**Never improvise structure. Follow skill instructions exactly.**

### 8. Internal Linking for Tonic Physio

- Fetch sitemap from `https://tonicphysio.com/sitemap.xml` before writing
- Map URLs to article sections based on contextual relevance
- Never link same URL more than once per article
- Use natural anchor text — not exact-match keyword-stuffed
- Prioritize service pages for condition-specific posts
- Prioritize blog posts for educational/guide content

### 9. Brand Voice

- Caring, professional, health-focused
- Use "you" and "your" (second person) for patient engagement
- Active voice 80%+ of the time
- Include specific health details, anatomy terms, and treatment names
- Show expertise without being overly clinical
- Empathetic tone — acknowledge pain and recovery challenges

### 10. Hard Stops for Tonic Physio

1. No H1 in body content (WordPress title is the only H1)
2. No em-dashes (—) or en-dashes (–) — use hyphens (-) or colons (:)
3. No FAQ sections at bottom (body IS the FAQ for AEO)
4. No FAQ schema markup
5. Status: DRAFT only — never publish without approval
6. 2,000+ words minimum for blog posts
7. No repeated words consecutively
8. No duplicate paragraphs or concept repetition
9. No filler intros: "In today's digital landscape..."
10. No AI shortcodes

---

## Pre-Push Checklist for Tonic Physio

```
1.  [ ] Skill loaded and followed exactly
2.  [ ] Body contains zero <h1> tags
3.  [ ] Word count >= 2,000
4.  [ ] Maximum 3 sentences per paragraph verified
5.  [ ] Maximum 60 words per paragraph verified
6.  [ ] Every H2 has 1-3 H3 subsections
7.  [ ] Maximum 300 words between headings
8.  [ ] No "as mentioned above" references
9.  [ ] Internal links verified from live sitemap
10. [ ] Yoast focus keyword SET
11. [ ] Yoast meta title SET and < 60 chars (verified with len())
12. [ ] Yoast meta description SET and < 160 chars (verified with len())
13. [ ] Categories assigned with correct IDs (never default "Topics")
14. [ ] No em-dashes found
15. [ ] No repeated words found
16. [ ] Featured image sourced from Pexels/Unsplash/Pixabay
17. [ ] Featured image alt text contains focus keyword
18. [ ] Alt text set via REST API
19. [ ] Status = Draft
20. [ ] Post ID + slug logged in projects/tonicphysio.com/post-registry.md
21. [ ] FRONTEND VERIFICATION: Open live URL, confirm all renders correctly
22. [ ] Double-check live link for repetitive words, sentences, mistakes
```

---

## Content Types for Tonic Physio

### Blog Post Types

| Type | Label | Structure |
|------|-------|-----------|
| How-to / Tutorial | "What You'll Learn" | Action-oriented, numbered steps |
| Condition Guide | "Key Takeaway" | Definition + causes + treatment + recovery |
| Comparison | "Quick Comparison" | Table-based, service comparisons |
| Pillar / Ultimate Guide | "Key Takeaway" | Comprehensive, multiple H2s with H3s |
| Case Study | "The Results" | Before/after, treatment outcomes |
| Definition / What Is | "In Short" | Educational, foundational |

### Service Page Types

- Physiotherapy services
- Registered Massage Therapy
- Manual Osteopathy
- Custom Orthotics & Bracing
- Shockwave Therapy
- WSIB & MVA Programs

---

*These rules are mandatory. Violation = content quality failure.*
