---
name: rankray-acf-service-page-writer
description: |
  Complete ACF service page content writer for rankray.com. Maps every ACF field to its
  exact frontend Elementor widget location, enforces strict word-count limits per field,
  applies SEO landing page writing best practices, and prevents the critical mistakes
  Hermes made (wrong field names, broken h2_paragraph_3, wrong builder type, etc.).
risk: safe
source: workspace
date_added: "2026-06-04"
date_updated: "2026-06-04"
references:
  - acf-field-mapper.json
  - rankray-service-pages-rules
  - seo-aeo-landing-page-writer
---

# RankRay ACF Service Page Writer

## Overview

This skill writes content for rankray.com service pages that are built with **ACF fields mapped to Elementor page design**. Every ACF field renders into a specific frontend widget. Writing to the wrong field, exceeding word counts, or using banned formats creates visible broken sections on the live page.

**WRITING QUALITY STANDARD:**
- Use your intelligence. Be creative, not robotic.
- NEVER repeat the service name in every heading. Be specific and benefit-focused.
- Research the actual service before writing. Understand what it does, who it helps, how it works.
- Write like a human marketer who understands the business, not an SEO template generator.
- Every heading must be unique, specific, and compelling.
- Bad: "Franchise Digital Marketing Strategy" | Good: "Local Market Strategy"
- Bad: "SEO Execution Services" | Good: "Technical Foundation That Google Trusts"

This skill prevents the critical mistakes Hermes made:
- Writing to non-existent field names (`services_1` instead of `services_1_heading`)
- Leaving `h2_paragraph_3` empty → visible broken section
- Pushing ACF content to Elementor-built pages (silent failures)
- Writing case-study portfolio paragraphs instead of results-driven format
- Exceeding word counts that the template physically truncates
- Adding links in `h1_paragraph` (above-the-fold violation)
- Repeating the service name in every heading (robotic, uncreative)

## When to Use This Skill

- Creating a **new** ACF service page (any service under `/digital-marketing-services/`)
- Editing **existing** ACF service page fields
- Auditing a service page for content quality / missing fields
- Any work on the 54 service pages in the `/digital-marketing-services/` tree

## Before You Write Anything — Builder Detection (MANDATORY)

**NEVER push ACF content to an Elementor-built page.**

### Detection Steps (execute in order):

1. Check `template` field — if `elementor_header_footer`, STOP
2. Check for `_elementor_data` meta — if present, STOP
3. Check `content.rendered` length vs ACF field count:
   - Body >5,000 chars AND ACF fields = 0 → Elementor
   - Body >5,000 chars AND ACF fields present → HYBRID — dangerous, ACF may override Elementor
4. If Elementor detected: Do NOT touch. Log as "Elementor page — skip" and move on.

### Permanent Elementor Blocklist (never edit via ACF):
- 11148 (SEO)
- 2593 (Digital Marketing)
- 14498 (LinkedIn Advertising)

## ACF Field-to-Frontend Mapping

Every field name maps to **exactly one** frontend widget. Write content knowing where it lands.

---

### Section 1: Hero — H1 + Intro Paragraph

| Field | Frontend Location | Type | Word Target |
|---|---|---|---|
| `h1_service_page` | Hero H1 heading (above fold, largest text) | text | 5–10 words |
| `h1_paragraph` | Subheading paragraph directly under H1 | text | 30–45 words |

**SEO Rules:**
- `h1_service_page`: MUST include primary keyword + benefit verb (e.g. "Enterprise SEO Audit Services That Scale")
- `h1_paragraph`: 2–3 sentences max. Include brand name "Rank Ray" + keyword mention.
- **ZERO links in `h1_paragraph`** — above-the-fold rule. All links appear after the fold.

**Example (Good):**
```
h1_service_page: Bespoke Custom Website Design That Converts Visitors Into Customers
h1_paragraph: Your website is your most powerful salesperson. Rank Ray designs custom websites that combine stunning aesthetics with conversion psychology, ensuring every visitor interaction moves prospects closer to becoming loyal customers.
```

---

### Section 2: H2 Content — 3 Supporting Paragraphs

| Field | Frontend Location | Type | Word Target |
|---|---|---|---|
| `h2_first` | First H2 heading on page (below hero) | text | 5–12 words |
| `h2_paragraph_1` | First body paragraph under H2 | text | 30–55 words |
| `h2_paragraph_2` | Second body paragraph + optional image | text | 30–55 words |
| `image_for_h2_para_2_` | Image inserted beside h2_paragraph_2 | image | optional |
| `h2_paragraph_3` | **THIRD body paragraph — MANDATORY** | text | 30–55 words |
| `image_h2_paragraph_3` | Image inserted beside h2_paragraph_3 | image | optional |

**CRITICAL — Hermes Mistake #1:** `h2_paragraph_3` must NEVER be empty. The template renders `h2_paragraph_2` and `h2_paragraph_3` as **two separate widgets**. Empty = visible broken section on frontend.

**Content Distribution:**
- `h2_paragraph_1` = Problem/Pain Point. Start with "We help..." or "We specialize..." — define the audience pain that this service solves. Include LSI terms.
- `h2_paragraph_2` = Solution 1 (H3 subheading). Methodology + internal link to related service. See Internal Link Mapping table below.
- `h2_paragraph_3` = Solution 2 (H3 subheading). Technical details / differentiator + internal link to related service. Must be present and substantive.

**Narrative Arc for H2 Section:**
```
h2_paragraph_1: Problem → why templates/generic solutions fail
h2_paragraph_2: Solution 1 → methodology, includes internal link
h2_paragraph_3: Solution 2 → technical/performance angle, includes internal link
```

**H2 Heading Pattern:**
`High-Impact [Service] Services That Drive [Outcome] to Your Business`

**WRONG:** `What Is Semantic SEO?`
**RIGHT:** `High-Impact Semantic SEO Services That Drive Targeted Organic Traffic to Your Business`

**Image Fields:**
- `image_for_h2_para_2_` — 940×529 recommended. Can be empty if no relevant image.
- `image_h2_paragraph_3` — 940×529 recommended. Can be empty.

---

### Section 3: Portfolio / Success Stories

| Field | Frontend Location | Type | Word Target |
|---|---|---|---|
| `h3_portfolio_heading` | H3 heading above portfolio area | text | 2–4 words |
| `h3_portfolio_paragraph_before_3_boxes` | Portfolio description paragraph | text | 150–250 words |

**CRITICAL — Hermes Mistake #2:** This field is **results-driven summary**, NOT a case study.

**Portfolio Heading Rules:**
- Must explain WHAT results and WHO from
- BANNED: generic phrases like "Results That Scale" or "Our Portfolio"
- GOOD: "Proven SEO Performance Of Our Clients" or "Web Design Results From Real Projects"
- Pattern: "Proven [Service] [Outcome] Of [Who]"

**BANNED formats:**
- "Before/After" client stories (e.g. "A healthcare clinic in Dubai was losing...")
- Single-client narratives with clinic names
- "Before: ... After: ..." headers

**MANDATORY format — Natural Narrative Style:**
Write like a human, not a template. Use your brand voice. Include specific numbers with context.

**Example (Good — from live SEO page):**
```html
<p><strong>SEO That Drives Visibility, Ranking, and Growth</strong></p>
<p>At Rank Ray, our SEO strategies are built for long-term success. We do not just aim for page-one rankings. We help you dominate your niche with technical precision, authoritative content, and smart linking strategies.</p>
<p>Whether you run a local clinic, a national eCommerce store, or an international service business, our SEO approach adapts to your goals. We have helped law firms achieve an 822% increase in organic traffic, real estate businesses capture hundreds of monthly leads, and eCommerce brands grow 1900% in search-driven revenue.</p>
<p>Each project is powered by deep keyword intelligence, on-page optimization, technical SEO, and white-hat link building. It is not about traffic, it is about qualified leads and measurable business outcomes.</p>
<p>Discover how our proven SEO systems can move your business forward. If you are ready for visibility that converts, let us talk.</p>
```

**Key elements to include:**
1. Strong opening with service + outcome
2. Brand voice paragraph ("At Rank Ray, our...")
3. Specific numbers WITH client types (law firms: 822%, real estate: hundreds of leads, ecommerce: 1900%)
4. Methodology sentence
5. Direct CTA that feels personal ("If you are ready..., let us talk")

**Every paragraph must be wrapped in `<p>...</p>` tags. NEVER use `<br/><br/>` for line breaks.**

---

### Section 4: CTA Form Section

| Field | Frontend Location | Type | Word Target |
|---|---|---|---|
| `form_h3_heading` | H3 heading above the lead form | text | 5–10 words |
| `form_paragraph` | Paragraph between H3 and form fields | text | 30–45 words |
| `form_heading_h4` | H4 subheading inside/near form | text | 5–8 words |

**SEO Rules:**
- `form_h3_heading`: Action verb + service name (e.g. "Start Your SEO Audit Today")
- `form_paragraph`: 2–3 sentences, includes CTA, brand mention "Rank Ray"
- `form_heading_h4`: Benefit-driven question or command (e.g. "Request Your Free Proposal")
- **Note:** `form_heading_h4` may render as H4 subheading below form fields or be visually suppressed by the template. Always populate it; if the template doesn't render it visibly, it still contributes to on-page heading structure.

---

### Section 5: Services Grid — 6 Service Offerings

| Field | Frontend Location | Type | Word Target |
|---|---|---|---|
| `services_heading_-_h2` | H2 heading above 6 service boxes | text | 5–10 words |
| `before_services_paragraph` | Intro paragraph before the grid | text | 15–25 words |
| `services_1_heading` | Box 1 heading | text | 3–5 words |
| `services_1_paragraph` | Box 1 description | text | 25–40 words |
| `services_2_heading` | Box 2 heading | text | 3–5 words |
| `services_2_paragraph` | Box 2 description | text | 25–40 words |
| `services_3_heading` | Box 3 heading | text | 3–5 words |
| `services_3_paragraph` | Box 3 description | text | 25–40 words |
| `services_4_heading` | Box 4 heading | text | 3–5 words |
| `services_4_paragraph` | Box 4 description | text | 25–40 words |
| `services_5_heading` | Box 5 heading | text | 3–5 words |
| `services_5_paragraph` | Box 5 description | text | 25–40 words |
| `services_6_heading` | Box 6 heading | text | 3–5 words |
| `services_6_paragraph` | Box 6 description | text | 25–40 words |

**CRITICAL — Hermes Mistake #3:** Field name is `services_1_heading`, NOT `services_1`. Writing to `services_1` silently fails.

**SEO Rules:**
- `services_heading_-_h2`: keyword + "Services" or "Solutions" (e.g. "Comprehensive SEO Audit Solutions")
- `before_services_paragraph`: 1–2 sentences setting context
- Each heading: specific, benefit-oriented, max 5 words
- Each paragraph: 2–3 sentences. One may contain an internal link to a related page.
- Optional internal link targets:
  - `services_1_paragraph` → related blog post or service
  - `services_2_paragraph` → CRO or conversion service
  - `services_3_paragraph` → email marketing or outreach
  - `services_4_paragraph` → web development
  - `services_5_paragraph` → digital marketing overview
  - `services_6_paragraph` → branding

---

### Section 6: Slogan

| Field | Frontend Location | Type | Word Target |
|---|---|---|---|
| `slogan_-_span` | Short tagline, often bold/italic span element | text | 5–10 words |

**Rules:**
- Include keyword or brand name
- Memorable, punchy phrase
- Example: "Design That Performs. Websites That Convert."
- **Frontend Location:** Typically renders below the Services Grid section, above or within the Why Us section. If the template places it elsewhere, follow the template's visual hierarchy.

---

### Section 7: Why Choose Us — 6 Boxes

| Field | Frontend Location | Type | Word Target |
|---|---|---|---|
| `why_us_h3_heading` | H3 heading above 6 boxes | text | 5–10 words |
| `why_us_h3_paragraph` | Intro paragraph before boxes | text | 30–45 words |
| `why_us_box_1_heading` | Box 1 heading | text | 2–4 words |
| `why_us_box_1_paragraph` | Box 1 description | text | 20–35 words |
| `why_us_box_2_heading` | Box 2 heading | text | 2–4 words |
| `why_us_box_2_paragraph` | Box 2 description | text | 20–35 words |
| `why_us_box_3_heading` | Box 3 heading | text | 2–4 words |
| `why_us_box_3_paragraph` | Box 3 description | text | 20–35 words |
| `why_us_box_4_heading` | Box 4 heading | text | 2–4 words |
| `why_us_box_4_paragraph` | Box 4 description | text | 20–35 words |
| `why_us_box_5_heading` | Box 5 heading | text | 2–4 words |
| `why_us_box_5_paragraph` | Box 5 description | text | 20–35 words |
| `why_us_box_6_heading` | Box 6 heading | text | 2–4 words |
| `why_us_box_6_paragraph` | Box 6 description | text | 20–35 words |

**SEO Rules:**
- `why_us_h3_heading`: "Why Choose [Service]?" or "Why Rank Ray for [Service]?"
- `why_us_h3_paragraph`: 2–3 sentences, brand mention, connects to broader strategy
- Each box heading: 2–4 words, benefit-oriented
- Each box paragraph: 1–2 sentences, specific (not generic fluff)
- `why_us_h3_paragraph` may contain internal link to app development or related tech service

---

### Section 8: FAQ — 10 Items for Rich Snippets

| Field | Frontend Location | Type | Word Target |
|---|---|---|---|
| `faq_heading` | H3 heading above FAQ list | text | 5–10 words |
| `question_1` | FAQ item 1 question | text | 8–15 words |
| `answer_1` | FAQ item 1 answer | text | 25–40 words |
| `question_2` | FAQ item 2 question | text | 8–15 words |
| `answer_2` | FAQ item 2 answer | text | 25–40 words |
| ... | ... | ... | ... |
| `question_10` | FAQ item 10 question | text | 8–15 words |
| `answer_10` | FAQ item 10 answer | text | 25–40 words |

**SEO Rules:**
- `faq_heading`: "[Service] FAQs" or "[Service] Frequently Asked Questions"
- Each question: natural language, 8–15 words
- Each answer: 2–4 sentences. **Direct answer first**, then context.
- Include LSI terms naturally in answers
- Every answer must be **self-contained** — it should make sense if extracted by an AI engine
- Answers must be under 40 words for AI snippet extraction

---

## Complete Field Count Summary

| Section | Fields | Frontend Widgets | Word Count Target |
|---|---|---|---|
| Hero | 2 | H1 + paragraph | 35–55 |
| H2 Content | 6 (2 optional images) | H2 + 3 paragraphs + 2 images | 90–165 |
| Portfolio | 2 | H3 + paragraph | 152–254 |
| CTA Form | 3 | H3 + paragraph + H4 | 65–98 |
| Services Grid | 14 | H2 + intro + 6 heading/para pairs | 515–715 |
| Slogan | 1 | Span element | 5–10 |
| Why Us | 14 | H3 + intro + 6 heading/para pairs | 300–450 |
| FAQ | 21 | H3 + 10 Q&A pairs | 625–925 |
| **TOTAL** | **63 fields** | — | **~1800–2500** |

**IMPORTANT:** The frontend physically truncates long content. Exceeding word targets = invisible wasted effort.

## Word Count Hard Stops (Frontend-Verified)

Measured from live reference page (Semantic SEO, ID 19892):

| Field | Min | Max |
|---|---|---|
| h1_paragraph | 30 | 45 words |
| h2_paragraph_1 | 30 | 55 words |
| h2_paragraph_2 | 30 | 55 words |
| h2_paragraph_3 | 30 | 55 words |
| services_N_paragraph | 25 | 40 words |
| why_us_box_N_paragraph | 20 | 35 words |
| FAQ answers | 25 | 40 words |
| Portfolio heading | 2 | 4 words |
| Why Us headings | 2 | 4 words |
| FAQ questions | short | **40 chars max** |

**FAQ Question Length:** The template physically truncates questions over 40 characters. Count characters including spaces and punctuation. If a question exceeds 40 chars, rewrite it shorter. Example: "What is included in professional web design?" = 42 chars → TOO LONG. Rewrite: "What does professional web design include?" = 40 chars.

## Image Alt Text SEO Rules

All images uploaded via ACF image fields should have descriptive alt text:

| Field | Alt Text Rule | Example |
|---|---|---|
| `image_for_h2_para_2_` | Primary keyword + context + "Rank Ray" | `Web Design Wireframing Process by Rank Ray` |
| `image_h2_paragraph_3` | Primary keyword + technical detail + "Rank Ray" | `Responsive Web Design Development by Rank Ray` |

**Alt Text Formula:** `[Primary Keyword] + [What the image shows] + [Rank Ray]`
- Keep under 125 characters
- Never keyword-stuff
- Never leave empty ("" alt text = invisible to search engines)

## Internal Service Link Mapping

When writing H2 paragraphs, link to related services using contextual anchor text. Here are the canonical mappings for major service pages:

| Current Page | h2_paragraph_2 Link Target | h2_paragraph_2 Anchor Text | h2_paragraph_3 Link Target | h2_paragraph_3 Anchor Text |
|---|---|---|---|---|
| Web Design | `/search-engine-optimization-seo/` | comprehensive SEO services | `/custom-website-design/` | custom website design services |
| Custom Website Design | `/conversion-rate-optimization/` | Conversion Rate Optimization | `/search-engine-optimization-seo/` | SEO services |
| SEO | `/content-marketing/` | content marketing strategy | `/technical-seo/` | technical SEO services |
| Local SEO | `/google-business-profile/` | Google Business Profile optimization | `/citation-building/` | citation building services |
| E-Commerce SEO | `/product-page-optimization/` | product page optimization | `/technical-seo/` | technical SEO audit |
| Enterprise SEO | `/semantic-seo/` | semantic SEO strategy | `/ai-automation/` | AI automation for SEO |
| Content Marketing | `/seo-content-writing/` | SEO content writing | `/social-media-marketing/` | social media distribution |
| PPC / Google Ads | `/conversion-rate-optimization/` | CRO for landing pages | `/landing-page-design/` | landing page design |
| Social Media Marketing | `/content-marketing/` | content marketing strategy | `/ppc/` | paid social advertising |
| AI Automation | `/enterprise-seo/` | enterprise SEO | `/technical-seo/` | technical SEO infrastructure |

**Link Rules:**
- Only 1 link per H2 paragraph maximum
- Anchor text must be natural, descriptive, and keyword-rich
- Never use "click here", "learn more", or generic anchors
- If no relevant related service exists for a page, omit the link rather than force one

## Banned Content (Hermes Mistakes to Avoid)

1. **No founding years / time-based authority:**
   - BANNED: "since 2019", "over X years", "established in..."
   - Authority comes from results, not tenure.

2. **No case-study portfolio format:**
   - BANNED: "Before: Losing 70% of mobile traffic..."
   - BANNED: "A healthcare clinic in Dubai was losing..."
   - Use results-driven summary format only.

3. **No links in h1_paragraph:**
   - Above-the-fold violation.

4. **No `<br/><br/>` line breaks:**
   - Use proper `<p>...</p>` blocks only.

5. **No status changes on edit:**
   - When editing a published page, **omit `"status"`** from payload.
   - Accidentally changing to `draft` = invisible page.

6. **No dead/broken links in portfolio stat boxes:**
   - Portfolio client logos and stat boxes must link to real case study URLs or have no link at all.
   - Never use `href="#"` or `href="javascript:void(0)"`.
   - If no dedicated case study page exists, remove the `<a>` tag entirely.

7. **NO EM DASHES OR EN DASHES — EVER:**
   - BANNED characters: `—` (em dash), `–` (en dash), `―` (figure dash)
   - BANNED patterns: `--` (double hyphen), `---` (triple hyphen)
   - **ALWAYS use**: commas, colons, semicolons, or periods instead
   - **WRONG**: "We build audience systems that find buyers already showing intent — then serve creative that moves them to act."
   - **RIGHT**: "We build audience systems that find buyers already showing intent, then serve creative that moves them to act."
   - **RIGHT**: "Behavioral data: past purchases, content engagement, and competitor interactions."
   - **RIGHT**: "It is not about reach; it is about qualified leads."
   - **CRITICAL**: Even in examples, sample text, or placeholder content — NEVER use dashes. This rule has zero exceptions.
   - **Self-check**: Before every push, grep for `[—–―]` and `[--]` in all text fields. If any found, rewrite those sentences immediately.

## SEO Landing Page Writing Integration

This skill incorporates best practices from `@seo-aeo-landing-page-writer`:

1. **Narrative Arc:**
   - Hero: grab attention + state value
   - Problem: audience pain (in H2 paragraph 1)
   - Solution: introduce service as answer (H2 paragraph 2)
   - Proof: results + portfolio (portfolio section)
   - Services: detailed offerings (services grid)
   - Trust: why choose us (6 boxes)
   - FAQ: objections + rich snippets (10 Q&A)
   - CTA: conversion action (form section)

2. **Keyword Placement:**
   - Primary keyword in `h1_service_page`
   - Secondary/LSI keywords in `h2_paragraph_1`
   - Natural variation in `services_heading_-_h2` and service descriptions
   - FAQ questions use natural language containing keyword variations

3. **AEO (Answer Engine Optimization):**
   - FAQ answers are self-contained
   - Direct answer in first sentence
   - Under 40 words for extraction
   - Include "What is [service]?" as one of the FAQ questions

## After Push — Live Verification Checklist

After EVERY ACF field update, perform these steps in order:

### Step 1: Crawl Live Frontend HTML
Fetch the live page URL (not the API response) using `web_fetch` or browser snapshot.

### Step 2: Extract and Verify Content
1. **H1 tag text** — Must match `h1_service_page` field content
2. **H1 paragraph** — Count words, must be 30-45 words. No links allowed.
3. **H2 heading** — Must include primary keyword + benefit verb
4. **h2_paragraph_3** — Must render visible content (NOT empty/broken section)
5. **H2 paragraph word counts** — Each must be 30-55 words
6. **Services grid** — All 6 boxes render with headings + descriptions
7. **Portfolio section** — Results-driven format, no case studies, no dead links
8. **Why Us boxes** — All 6 render correctly
9. **FAQ section** — All 10 questions + answers visible
10. **Form section** — H3 + paragraph + H4 all present

### Step 3: Check for Layout Breaks
- No empty widget spaces (white gaps)
- No truncated paragraphs mid-sentence
- No overlapping elements
- Mobile responsive check (use browser mobile viewport)

### Step 4: Verify Yoast Meta via View-Source
Open page source and check:
- `<title>` ≤ 60 characters
- `<meta name="description">` ≤ 160 characters and contains "Rank Ray"
- Focus keyword set in Yoast JSON-LD
- **NO em-dashes (—) in meta description or ANY page content**

### Step 5: Verify Internal Links
- H2 paragraph 2 must contain 1 contextual link to related service
- H2 paragraph 3 must contain 1 contextual link to related service  
- Services grid paragraphs: optional links must be valid (200 status)
- Portfolio stat boxes: NO `href="#"` or empty links

### Step 6: Image Verification
- All images have `alt` attributes (not empty)
- Alt text includes primary keyword or brand name where relevant
- Images load without 404 errors

### Step 7: DASH CHECK — CRITICAL
**Before every push, check ALL text fields for banned characters:**
1. Search for em dash `—`, en dash `–`, figure dash `―`
2. Search for double hyphen `--` or triple hyphen `---`
3. If ANY found: rewrite those sentences immediately using commas, colons, or periods
4. **This check is mandatory. Zero exceptions.**

### Step 8: Word Count Measurement Method
**How to verify word counts from frontend HTML:**
1. Extract text content from the rendered HTML element (strip HTML tags)
2. Count words by splitting on whitespace
3. Compare against the hard stop table above
4. If count exceeds max, trim content and re-push

**Note:** Do NOT count words from the ACF field content in the API response — the template may truncate or append text. Always measure from the live rendered HTML.

## Reference Pages

- **Canonical ACF page:** Semantic SEO (ID 19892) at `https://rankray.com/digital-marketing-services/semantic-seo/`
- **ACF field mapper:** `websites/rankray.com/acf-field-mapper.json`
- **Audit log:** `websites/rankray.com/acf-audit-log-2026-05-14.json`

## Related Skills

- `@rankray-service-pages-rules` — builder detection, banned formats, hard stops
- `@seo-aeo-landing-page-writer` — general landing page narrative arc and AEO
- `@seo-aeo-meta-description-generator` — Yoast meta optimization
- `@seo-aeo-content-quality-auditor` — audit completed page

---

## Lessons Learned (2026-06-04)

### What NOT to do (mistakes made on live pages):

1. **Never change the services grid structure**
   - Facebook Ads, Instagram Ads, LinkedIn Ads, Twitter Ads, TikTok Ads, Cross-Platform Attribution is the CORRECT structure for social media advertising
   - Do NOT rename them to generic names like "Meta Ads Strategy" or "Creative Testing Lab"
   - Only fix the DESCRIPTION text within each box, never the headings/layout

2. **Never add standalone sentences just for links**
   - WRONG: "Our paid social integrates with [link] for unified..." as its own paragraph
   - RIGHT: weave the link naturally: "Our paid social campaigns work alongside our [link] to create..."

3. **Never use em dashes — EVER**
   - Even in examples, sample text, or thinking out loud
   - Banned: `—`, `–`, `―`, `--`, `---`
   - Use commas, colons, semicolons, or periods instead

4. **Portfolio headings must explain the results**
   - WRONG: "Results That Scale" (tells nothing)
   - RIGHT: "Proven Ad Performance Of Some of Our Clients" (what + who)

5. **Write like a human, not a template**
   - Your natural narrative style with brand voice, specific numbers, and emotional hooks converts better than rigid SEO formulas
   - Include: "At Rank Ray, our...", "We have helped...", "It is not about X, it is about Y"
   - End with personal CTA: "If you are ready..., let us talk"
