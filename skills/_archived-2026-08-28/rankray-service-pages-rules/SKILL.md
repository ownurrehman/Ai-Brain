---
name: rankray-service-pages-rules
description: |
  Canonical rules for RankRay ACF service page content creation and editing.
  Enforces: no founding years, results-driven portfolio format, proper builder detection,
  field-length limits measured from frontend, and post-push live verification.
risk: safe
source: community
date_added: "2026-06-04"
date_updated: "2026-06-04"
references:
  - references/acf-service-page-content-format-2026-05-15.md
  - references/portfolio-format-results-driven-only.md
  - references/elementor-vs-acf-builder-detection-2026-06-03.md
---

> **Parent Hub:** [[skills/_archived-2026-08-28/INDEX|📦 Archived Skills Hub]] · [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# RankRay Service Pages Rules

## Overview

Rules for creating, editing, and auditing RankRay ACF service pages. These are **Elementor-built landing pages** that pull content from ACF fields via dynamic tags. All 54 service pages share an **identical 63-field ACF structure** mapped to specific Elementor widgets. Every rule exists because a mistake was caught by the user.

## Architecture

- **Template:** Elementor (`elementor_header_footer` or blank with `_elementor_data` meta)
- **Content Source:** ACF fields (63 per page) connected via Elementor dynamic tags
- **Widget Types:** iheading, heading, text-editor, icon-box, toggle, image, ibutton
- **Field Count:** 63 fields — identical across ALL 54 service pages
- **Content Ceiling:** Template renders ~14-16 widgets maximum; per-field word targets are measured from frontend, not backend

## When to Use This Skill

- Creating a new ACF service page
- Editing an existing ACF service page (title, ACF fields, Yoast meta)
- Auditing an existing service page for quality issues
- Any work on the 54 service pages in the /digital-marketing-services/ tree

## Complete ACF Field Map (63 Fields)

Every field name maps to **exactly one** frontend widget. Write content knowing where it lands.

### SECTION 1: Hero — H1 + Intro Paragraph

| Field | Widget | Purpose | Word Target |
|---|---|---|---|
| `h1_service_page` | iheading | H1 page title (above fold, largest text) | 5–10 words |
| `h1_paragraph` | text-editor | Subheading paragraph directly under H1 | 30–45 words |
| `slogan_-_span` | span/text | Slogan or tagline element | 5–10 words |

**SEO Rules:**
- `h1_service_page`: MUST include primary keyword + benefit verb (e.g. "Enterprise SEO Audit Services That Scale")
- `h1_paragraph`: 2–3 sentences max. Include brand name "Rank Ray" + keyword mention.
- **ZERO links in `h1_paragraph`** — above-the-fold rule. All links appear after the fold.
- `slogan_-_span`: Punchy tagline with keyword or brand. Renders typically below Services Grid or within Why Us section.

**Good Example:**
```
h1_service_page: Bespoke Custom Website Design That Converts Visitors Into Customers
h1_paragraph: Your website is your most powerful salesperson. Rank Ray designs custom websites that combine stunning aesthetics with conversion psychology, ensuring every visitor interaction moves prospects closer to becoming loyal customers.
```

### SECTION 2: H2 Content — 3 Supporting Paragraphs

| Field | Widget | Purpose | Word Target |
|---|---|---|---|
| `h2_first` | heading | First H2 heading on page (below hero) | 5–12 words |
| `h2_paragraph_1` | text-editor | First body paragraph under H2 | 30–55 words |
| `h2_paragraph_2` | text-editor | Second body paragraph + optional image | 30–55 words |
| `image_for_h2_para_2_` | image | Image inserted beside h2_paragraph_2 | optional |
| `h2_paragraph_3` | text-editor | **THIRD body paragraph — MANDATORY** | 30–55 words |
| `image_h2_paragraph_3` | image | Image inserted beside h2_paragraph_3 | optional |

**CRITICAL — Hermes Mistake #1:** `h2_paragraph_3` must NEVER be empty. The template renders `h2_paragraph_2` and `h2_paragraph_3` as **two separate widgets**. Empty = visible broken section on frontend.

**Content Distribution:**
- `h2_paragraph_1` = Problem/Pain Point. Start with "We help..." or "We specialize..." — define the audience pain that this service solves. Include LSI terms.
- `h2_paragraph_2` = Solution 1 (H3 subheading). Methodology + internal link to related service.
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

### SECTION 3: Portfolio / Success Stories

| Field | Widget | Purpose | Word Target |
|---|---|---|---|
| `h3_portfolio_heading` | heading | H3 heading above portfolio area | 2–4 words |
| `h3_portfolio_paragraph_before_3_boxes` | text-editor | Portfolio description paragraph | 150–250 words |

**CRITICAL — Hermes Mistake #2:** This field is **results-driven summary**, NOT a case study.

**BANNED formats:**
- "Before/After" client stories (e.g. "A healthcare clinic in Dubai was losing...")
- Single-client narratives with clinic names
- "Before: ... After: ..." headers

**MANDATORY format:**
```html
<p><strong>[Service] That [Outcome] — Proven Results Across Industries</strong></p>
<p>Broad capability statement with client type diversity...</p>
<p>Specific numbers + verticals (e.g., "140% lead increases for healthcare brands...")</p>
<p>Methodology/philosophy sentence</p>
<p>CTA</p>
```

Every paragraph must be wrapped in `<p>...</p>` tags. NEVER use `<br/><br/>` for line breaks.

### SECTION 4: CTA Form Section

| Field | Widget | Purpose | Word Target |
|---|---|---|---|
| `form_h3_heading` | heading | H3 heading above the lead form | 5–10 words |
| `form_paragraph` | text-editor | Paragraph between H3 and form fields | 30–45 words |
| `form_heading_h4` | heading | H4 subheading inside/near form | 5–8 words |

**SEO Rules:**
- `form_h3_heading`: Action verb + service name (e.g. "Start Your SEO Audit Today")
- `form_paragraph`: 2–3 sentences, includes CTA, brand mention "Rank Ray"
- `form_heading_h4`: Benefit-driven question or command (e.g. "Request Your Free Proposal")
- **Note:** `form_heading_h4` may render as H4 subheading below form fields or be visually suppressed by the template. Always populate it; if the template doesn't render it visibly, it still contributes to on-page heading structure.

### SECTION 5: Services Grid — 6 Service Offerings

| Field | Widget | Purpose | Word Target |
|---|---|---|---|
| `services_heading_-_h2` | heading | Services section H2 | 5–10 words |
| `before_services_paragraph` | text-editor | Intro paragraph before the grid | 15–25 words |
| `services_1_heading` | icon-box | Box 1 heading | 3–5 words |
| `services_1_paragraph` | icon-box | Box 1 description | 25–40 words |
| `services_2_heading` | icon-box | Box 2 heading | 3–5 words |
| `services_2_paragraph` | icon-box | Box 2 description | 25–40 words |
| `services_3_heading` | icon-box | Box 3 heading | 3–5 words |
| `services_3_paragraph` | icon-box | Box 3 description | 25–40 words |
| `services_4_heading` | icon-box | Box 4 heading | 3–5 words |
| `services_4_paragraph` | icon-box | Box 4 description | 25–40 words |
| `services_5_heading` | icon-box | Box 5 heading | 3–5 words |
| `services_5_paragraph` | icon-box | Box 5 description | 25–40 words |
| `services_6_heading` | icon-box | Box 6 heading | 3–5 words |
| `services_6_paragraph` | icon-box | Box 6 description | 25–40 words |

**CRITICAL — Hermes Mistake #3:** Field name is `services_1_heading`, NOT `services_1`. Writing to `services_1` silently fails.

**SEO Rules:**
- `services_heading_-_h2`: keyword + "Services" or "Solutions" (e.g. "Comprehensive SEO Audit Solutions")
- `before_services_paragraph`: 1–2 sentences setting context
- Each heading: specific, benefit-oriented, max 5 words
- Each paragraph: 2–3 sentences. One may contain an internal link to a related page.

### SECTION 6: Why Choose Us — 6 Boxes

| Field | Widget | Purpose | Word Target |
|---|---|---|---|
| `why_us_h3_heading` | heading | Why Us H3 | 5–10 words |
| `why_us_h3_paragraph` | text-editor | Why Us intro paragraph | 30–45 words |
| `why_us_box_1_heading` | icon-box | Box 1 heading | 2–4 words |
| `why_us_box_1_paragraph` | icon-box | Box 1 description | 20–35 words |
| `why_us_box_2_heading` | icon-box | Box 2 heading | 2–4 words |
| `why_us_box_2_paragraph` | icon-box | Box 2 description | 20–35 words |
| `why_us_box_3_heading` | icon-box | Box 3 heading | 2–4 words |
| `why_us_box_3_paragraph` | icon-box | Box 3 description | 20–35 words |
| `why_us_box_4_heading` | icon-box | Box 4 heading | 2–4 words |
| `why_us_box_4_paragraph` | icon-box | Box 4 description | 20–35 words |
| `why_us_box_5_heading` | icon-box | Box 5 heading | 2–4 words |
| `why_us_box_5_paragraph` | icon-box | Box 5 description | 20–35 words |
| `why_us_box_6_heading` | icon-box | Box 6 heading | 2–4 words |
| `why_us_box_6_paragraph` | icon-box | Box 6 description | 20–35 words |

**SEO Rules:**
- `why_us_h3_heading`: "Why Choose [Service]?" or "Why Rank Ray for [Service]?"
- `why_us_h3_paragraph`: 2–3 sentences, brand mention, connects to broader strategy
- Each box heading: 2–4 words, benefit-oriented
- Each box paragraph: 1–2 sentences, specific (not generic fluff)
- `why_us_h3_paragraph` may contain internal link to app development or related tech service

### SECTION 7: FAQ — 10 Items

| Field | Widget | Purpose | Word Target |
|---|---|---|---|
| `faq_heading` | heading | FAQ section H3 | 5–10 words |
| `question_1` through `question_10` | toggle | FAQ questions | 8–15 words, **40 chars MAX** |
| `answer_1` through `answer_10` | toggle | FAQ answers | 25–40 words |

**SEO Rules:**
- `faq_heading`: "[Service] FAQs" or "[Service] Frequently Asked Questions"
- Each question: natural language, 8–15 words
- Each answer: 2–4 sentences. **Direct answer first**, then context.
- Include LSI terms naturally in answers
- Every answer must be **self-contained** — it should make sense if extracted by an AI engine

**FAQ Question Character Limit (CRITICAL):**
The template physically truncates questions over 40 characters. Count characters including spaces and punctuation. If a question exceeds 40 chars, rewrite it shorter.

Example: "What is included in professional web design?" = 42 chars → **TOO LONG**
Rewrite: "What does professional web design include?" = 40 chars ↔ **EDGE CASE, avoid if possible**
Better: "What's included in web design services?" = 38 chars ↔ **ACCEPTABLE**

**FAQ Schema Note:**
Google deprecated FAQ rich *results display* in SERPs (May 2026), but FAQ structured data remains valuable for **AI crawlers and semantic understanding**. FAQ sections are used for:
- UX and on-page SEO
- AI bot ingestion (LLMs use schema to understand page content)
- Voice search and conversational AI responses

Continue including FAQ sections with proper Q&A. Schema markup is OPTIONAL — if RankRay's theme generates it automatically, that's fine. Don't manually add JSON-LD FAQPage schema unless specifically requested.

### SECTION 8: Form

| Field | Widget | Purpose | Word Target |
|---|---|---|---|
| `form_h3_heading` | heading | Form H3 | 5–10 words |
| `form_paragraph` | text-editor | Form intro | 30–45 words |
| `form_heading_h4` | heading | Form label H4 | 5–8 words |

## Complete Field Count Summary

| Section | Fields | Frontend Widgets | Word Count Target |
|---|---|---|---|
| Hero | 3 | H1 + paragraph + slogan | 40–65 |
| H2 Content | 6 (2 optional images) | H2 + 3 paragraphs + 2 images | 90–165 |
| Portfolio | 2 | H3 + paragraph | 152–254 |
| CTA Form | 3 | H3 + paragraph + H4 | 65–98 |
| Services Grid | 14 | H2 + intro + 6 heading/para pairs | 515–715 |
| Slogan | 1 | Span element | 5–10 |
| Why Us | 14 | H3 + intro + 6 heading/para pairs | 300–450 |
| FAQ | 21 | H3 + 10 Q&A pairs | 625–925 |
| **TOTAL** | **63 fields** | — | **~1800–2500** |

**IMPORTANT:** The frontend physically truncates long content. Exceeding word targets = invisible wasted effort.

## Content Narrative Arc

Every service page must follow this conversion-focused story arc:

1. **Hero:** Grab attention + state value proposition
2. **Problem:** Audience pain (in h2_paragraph_1)
3. **Solution:** Introduce service as answer (h2_paragraph_2 + h2_paragraph_3)
4. **Proof:** Results + portfolio (portfolio section)
5. **Services:** Detailed offerings (services grid)
6. **Trust:** Why choose us (6 boxes)
7. **FAQ:** Objections + on-page SEO (10 Q&A)
8. **CTA:** Conversion action (form section)

## AEO (Answer Engine Optimization) Rules

- FAQ answers are **self-contained**
- **Direct answer in first sentence**
- Under 40 words for AI snippet extraction
- Include "What is [service]?" as one of the FAQ questions when relevant
- Every answer should make sense if extracted by a search engine or AI model without surrounding context

## Word Count Hard Stops (Frontend-Verified)

| Field | Min | Max |
|---|---|---|
| h1_service_page | 5 | 10 words |
| h1_paragraph | 30 | 45 words |
| h2_first | 5 | 12 words |
| h2_paragraph_1 | 30 | 55 words |
| h2_paragraph_2 | 30 | 55 words |
| h2_paragraph_3 | 30 | 55 words |
| h3_portfolio_heading | 2 | 4 words |
| h3_portfolio_paragraph_before_3_boxes | 150 | 250 words |
| services_heading_-_h2 | 5 | 10 words |
| before_services_paragraph | 15 | 25 words |
| services_N_heading | 3 | 5 words |
| services_N_paragraph | 25 | 40 words |
| slogan_-_span | 5 | 10 words |
| why_us_h3_heading | 5 | 10 words |
| why_us_h3_paragraph | 30 | 45 words |
| why_us_box_N_heading | 2 | 4 words |
| why_us_box_N_paragraph | 20 | 35 words |
| faq_heading | 5 | 10 words |
| question_N | 8 | 15 words |
| answer_N | 25 | 40 words |
| form_h3_heading | 5 | 10 words |
| form_paragraph | 30 | 45 words |
| form_heading_h4 | 5 | 8 words |

**FAQ Question Character Limit:** The template physically truncates questions over 40 characters. Count characters including spaces and punctuation. If a question exceeds 40 chars, rewrite it shorter.

## Internal Service Link Mapping

When writing H2 paragraphs, link to related services using contextual anchor text:

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

## Image Alt Text Rules

All images uploaded via ACF image fields should have descriptive alt text:

| Field | Alt Text Rule | Example |
|---|---|---|
| `image_for_h2_para_2_` | Primary keyword + context + "Rank Ray" | `Web Design Wireframing Process by Rank Ray` |
| `image_h2_paragraph_3` | Primary keyword + technical detail + "Rank Ray" | `Responsive Web Design Development by Rank Ray` |

**Alt Text Formula:** `[Primary Keyword] + [What the image shows] + [Rank Ray]`
- Keep under 125 characters
- Never keyword-stuff
- Never leave empty ("" alt text = invisible to search engines)

## Rule 0: Builder Detection — MANDATORY Before Any Edit

**NEVER push ACF content to an Elementor-built page.**

### Detection Steps (execute in order):
1. Check `template` field — if `elementor_header_footer`, STOP
2. Check for `_elementor_data` meta — if present, STOP
3. Check `content.rendered` length vs ACF field count:
   - Body >5,000 chars AND ACF fields = 0 → Elementor
   - Body >5,000 chars AND ACF fields present → HYBRID — dangerous, ACF may override Elementor
4. If Elementor detected: Do NOT touch. Log as "Elementor page — skip" and move on.

### Elementor Page IDs (permanent blocklist — never edit via ACF):
- 11148 (SEO)
- 2593 (Digital Marketing)
- 14498 (LinkedIn Advertising)

## Rule 1: No Founding Years or Time-Based Authority

**NEVER reference:**
- "since 2019"
- "over X years"
- "established in..."
- "for more than a decade"
- Any date that implies company age as credibility

**Why:** User explicitly rejected this. Authority comes from results and expertise, not tenure.

**Correct:**
```
"We build conversion-centered layouts that turn traffic into qualified leads..."
"Our work has delivered 140% lead increases for healthcare brands..."
```

**Wrong:**
```
"Rank Ray has delivered responsive web design... since 2019."
```

## Rule 2: Portfolio Area = Results-Driven Summary, NOT Case Study

**Field:** `h3_portfolio_paragraph_before_3_boxes`

### Format (permanent since 2026-06-04):

```html
<p><strong>[Service] That [Outcome] — Proven Results Across Industries</strong></p>
<p>Broad capability statement with client type diversity...</p>
<p>Specific numbers + verticals (e.g., "140% lead increases for healthcare brands...")</p>
<p>Methodology/philosophy sentence</p>
<p>CTA</p>
```

### What NOT to write:
- **NO "Before/After" client stories** — no "A healthcare clinic in Dubai was losing..."
- **NO single-client narratives** — no clinic names, no problem→solution stories
- **NO "Before:... After:..." headers**

### Comparison:

**WRONG (case study format — BANNED):**
```html
<p><strong>Before: Losing 70 Percent of Mobile Traffic to Broken Layouts</strong></p>
<p>A healthcare clinic in Dubai was losing seven out of ten mobile visitors...</p>
<p><strong>After: Mobile-First Design That Doubled Qualified Leads</strong></p>
<p>We rebuilt the site from a 320-pixel wireframe upward...</p>
```

**RIGHT (results-driven format — MANDATORY):**
```html
<p><strong>Web Design That Converts Visitors Into Revenue</strong></p>
<p>Our web design services go beyond templates and decoration. We build conversion-centered layouts that turn traffic into qualified leads, combining UX research, performance engineering, and responsive design systems that scale.</p>
<p>Whether you need a mobile-first rebuild, a landing page optimized for paid traffic, or a full enterprise platform, we tailor every design decision to your revenue goals. Our work has delivered 140% lead increases for healthcare brands, 90% form completion lifts for real estate portals, and sub-two-second load times for franchise networks.</p>
<p>From wireframe-to-prototype workflows that eliminate redesign waste to CSS grid systems tested on real devices, we optimize what matters most: speed, clarity, and conversion. It is not just about how your site looks. It is about how efficiently it turns visitors into customers.</p>
<p>Explore how our web design experts can turn your traffic into scalable revenue.</p>
```

## Rule 3: Paragraph Tags — <p> ONLY

- Every paragraph must be wrapped in `<p>...</p>` tags
- **NEVER `<br/><br/>`** for line breaks
- Multiple paragraphs = multiple `<p>` blocks

## Rule 4: H2 Must Sell Outcomes

**Pattern:** `High-Impact [Service] Services That Drive [Outcome] to Your Business`

**WRONG:** `What Is Semantic SEO?`
**RIGHT:** `High-Impact Semantic SEO Services That Drive Targeted Organic Traffic to Your Business`

## Rule 5: First Paragraph Below H2 — "We Help..."

Must start with "We help..." or "We specialize..." — outcome-focused, never definitional.

**WRONG:** `Semantic SEO moves beyond single keywords...` (explains)
**RIGHT:** `We help businesses rank for meaning, not just keywords...` (sells)

## Rule 6: h2_paragraph_3 MUST NEVER BE EMPTY

- Template renders h2_paragraph_2 and h2_paragraph_3 as TWO SEPARATE widgets
- h2_paragraph_3 empty = visible broken section on frontend

**Distribution:**
- `h2_paragraph_2` = FIRST H3: `Build [Outcome] That Converts` + 2-3 paragraphs
- `h2_paragraph_3` = SECOND H3: `Data-Driven [Service] That Delivers Measurable Growth` + 2-3 paragraphs

## Rule 7: NO Links in h1_paragraph

- Above-the-fold rule
- h1_paragraph field must contain ZERO links
- All links appear after the fold

## Rule 8: Service Box Headings = `services_N_heading`

- Field name is `services_1_heading`, NOT `services_1`
- Writing to `services_1` silently fails

## Rule 9: Yoast Meta — Check on EVERY Edit

- Title <= 60 chars
- Description <= 160 chars
- Description MUST contain "Rank Ray" brand name
- Focus keyword must be set
- No em-dashes

## Rule 10: After Push — Verify Frontend

Mandatory steps after EVERY ACF field push:

### Step 1: Crawl Live Frontend HTML
Fetch the live page URL (not the API response).

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
- `<title>` ≤ 60 characters
- `<meta name="description">` ≤ 160 characters and contains "Rank Ray"
- Focus keyword set in Yoast JSON-LD
- No em-dashes (—) in meta description

### Step 5: Verify Internal Links
- H2 paragraph 2 must contain 1 contextual link to related service
- H2 paragraph 3 must contain 1 contextual link to related service
- Services grid paragraphs: optional links must be valid (200 status)
- Portfolio stat boxes: NO `href="#"` or empty links

### Step 6: Image Verification
- All images have `alt` attributes (not empty)
- Alt text includes primary keyword or brand name where relevant
- Images load without 404 errors

### Step 7: Word Count Measurement Method
**How to verify word counts from frontend HTML:**
1. Extract text content from the rendered HTML element (strip HTML tags)
2. Count words by splitting on whitespace
3. Compare against the hard stop table above
4. If count exceeds max, trim content and re-push

**Note:** Do NOT count words from the ACF field content in the API response — the template may truncate or append text. Always measure from the live rendered HTML.

## Rule 11: When Editing a Published Page — NEVER Change Status

- Omit `"status"` field from ALL payloads when editing a live page
- If status is accidentally changed to `draft`, restore to `publish` immediately
- This is a permanent hard stop

## Banned Content Summary

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

## FAQ Schema Guidance

**Updated June 2026:** Google removed FAQ rich *snippets* from SERPs, but FAQ structured data is still valuable for:
- AI search crawlers (ChatGPT, Perplexity, Gemini) that ingest structured content
- Semantic page understanding
- Voice search and conversational responses

FAQ sections should remain in service pages. Schema markup is optional — use it if the theme supports it automatically.

## Canonical Reference

- **Semantic SEO (ID 19892)** at `https://rankray.com/digital-marketing-services/semantic-seo/`
- Fetch this page FIRST before writing any new ACF service page
- Mirror its structure field-by-field

## Reference Pages

- `references/acf-service-page-content-format-2026-05-15.md` — Full format rules
- `references/portfolio-format-results-driven-only.md` — Portfolio rule detail
- `references/elementor-vs-acf-builder-detection-2026-06-03.md` — Builder detection
