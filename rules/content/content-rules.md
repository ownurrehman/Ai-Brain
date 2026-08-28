# Content Rules

MANDATORY for ALL agents, ALL projects, ALL content types. No exceptions.

## HARD STOPS (never push if violated)

1. **No H1 in body.** WordPress title is the only H1. Body starts with `<p>` or `<h2>`.
2. **2,000+ words minimum.** Pillar content: 3,000-5,000. No padding to hit count.
3. **Internal links must be contextually rich.** Every internal link serves the reader by connecting to genuinely relevant resources. There is no quota. A post with 5 perfect links outperforms one with 10 robotic links.

Link guidance by post depth:
- **Pillar/ultimate guides** (3,000-5,000 words): 8-15 links naturally distributed across topic breadth
- **How-to/tutorials** (2,000-3,000 words): 5-10 links to supporting guides and tools
- **Definition/beginner posts** (2,000-3,000 words): 4-8 links to related concepts and next steps
- **Updates/refreshes** (2,000+ words): 3-6 links to newer or related content

Never force links where none fit. Never pad a post with irrelevant links to hit a number.
4. **Yoast fields MUST be set before push:**
   - `yoast_focuskw`: exact primary keyword
   - `yoast_title`: <60 chars, includes keyword + brand — **count chars precisely, not estimated**
   - `yoast_metadesc`: <160 chars, includes keyword + LSI + brand — **count chars precisely, not estimated**
5. **Categories MUST be set before push:**
   - Never leave as default (WordPress "Topics" [1] is NOT acceptable)
   - Primary category = most specific relevant category
   - Posts covering multiple verticals get 2 categories
   - Verify category list from live API before assigning
5. **No em-dashes or en-dashes.** Use hyphens (-) or colons (:).
6. **Status: DRAFT only.** Never publish without user approval.
7. **No duplicate images.** Search Media Library before uploading. 1 featured image only.
**CRITICAL IMAGE RULES:**
1. ALWAYS source NEW images from external (Pexels/Unsplash/Pixabay). NEVER reuse existing media library images.
2. NEVER use an image already used on the site before. Search Media Library by filename before uploading.
3. EVERY image MUST have descriptive alt text set via REST API immediately after upload.
4. Featured image = WordPress `featured_media` field ONLY. NEVER insert `<img>` tags in body for featured images.
5. After uploading, verify the image actually displays on the frontend (clear cache if needed).
6. 1 featured image per blog post only. No images under H2 sections.

If ANY of these fail, DO NOT push. Fix first.

---

## MASTERSHEET SYNCHRONIZATION (Non-Negotiable)

After completing **ANY** task on a project — publish, draft, fix, audit, image upload, category change, Yoast update — you **MUST** update the project's `mastersheet.md` and `post-registry.md` **before reporting completion.**

### What to Update

| File | Path | Update Trigger | What to Log |
|------|------|---------------|-------------|
| **Mastersheet** | `websites/{project}/mastersheet.md` (or `projects/` for apps) | Every task | Date, what changed, metric deltas, status changes |
| **Post Registry** | `websites/{project}/post-registry.md` | Every push/publish | Post ID, title, date, slug, word count, categories, Yoast status, author |
| **Content Calendar** | Google Sheet (if applicable) | Every status change | Status column: TO-WRITE → DRAFT → PUBLISHED |

### Minimum Updates Per Action

**After publishing a blog post:**
- [ ] Add entry to `post-registry.md` with ID, title, date, slug, categories, word count, Yoast status
- [ ] Update `mastersheet.md` "Last Updated" date, increment Total Posts count, log in Changes table
- [ ] Update Content Calendar status to "PUBLISHED"

**After pushing a draft:**
- [ ] Add entry to `post-registry.md` (marked DRAFT)
- [ ] Update `mastersheet.md` Drafts count + Changes table
- [ ] Update Content Calendar status to "DRAFT"

**After fixing issues (broken images, Yoast, categories):**
- [ ] Log fix in `mastersheet.md` Changes table
- [ ] Update `post-registry.md` affected entries

**After any audit:**
- [ ] Export findings summary to `audit-{date}.md` or append to `mastersheet.md`
- [ ] Update `mastersheet.md` metrics to reflect current state

### Synchronization Failure = Task Incomplete

**If the mastersheet/post-registry is not updated, the task is NOT done.**

Do not say "done" or move to the next task until at least the mastersheet date and changes table are updated. Post-registry update is mandatory for publish/draft pushes.

## GOOGLE SHEET CREATION RULE (Non-Negotiable)

Whenever you create a new Google Sheet for any project, client, audit, or tracking purpose, the **FIRST action** after creation must be sharing it with `rankrayofficial@gmail.com` as **Editor** (writer role). No exceptions.

Steps:
1. Create the sheet
2. Immediately call Drive API: `POST https://www.googleapis.com/drive/v3/files/{sheetId}/permissions` with `{"role": "writer", "type": "user", "emailAddress": "rankrayofficial@gmail.com"}`
3. Verify the permission was added successfully
4. Report the shared sheet URL to the user

**If a sheet is not shared with rankrayofficial@gmail.com, the task is incomplete.**

---

## DUPLICATE PREVENTION (Non-Negotiable)

Before pushing **ANY** post (draft or publish), you **MUST** verify the target slug does NOT already exist on the live site. WordPress silently appends `-2`, `-3`, etc. to duplicate slugs, creating duplicate content without warning.

### Pre-Push Slug Collision Check

**Every push MUST include this step:**

```python
import requests, base64

# 1. Build the slug from your title
slug = "your-target-slug"

# 2. Query live site for existing posts with same or similar slug
r = requests.get(
    f"{WP_BASE}/posts?slug={slug}&status=publish,draft",
    headers=auth_headers
)
existing = r.json()

if existing:
    # WordPress returns empty list if no match
    for post in existing:
        print(f"CONFLICT: ID {post['id']} already has slug '{post['slug']}'")
    print("STOP. Do NOT push. Resolve conflict first.")
    exit(1)

# 3. NOW push
```

**Why this matters:**
- If slug `local-seo-real-estate-map-pack` is already taken, WordPress auto-creates `local-seo-real-estate-map-pack-2`
- The -2 post looks like a duplicate, confuses readers, dilutes authority, and may trigger canonical issues
- WordPress does NOT warn you — it silently appends the suffix

**Resolution rules:**
1. If slug is taken by a **published** post with the same topic: STOP. Don't create a duplicate. Merge into existing post or pick a new unique angle.
2. If slug is taken by a **draft** with the same topic: Use the existing draft. Don't create a new one.
3. If slug is taken by an **unrelated** post with a different topic: Choose a more specific slug.

**If you push a -2 suffix, the task FAILED.** Fix before reporting completion.

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
- Distribution: service pages vs blog posts happens naturally — no forced 50/50 split
- Word count does NOT determine link count; topic depth does
- Every link must genuinely help the reader at that exact point in the article
- "Rank Ray" anchor text links to homepage ONLY (for RankRay content)

### Service Page Internal Links

Service pages should link to related services **only where contextually natural**. No quotas. No forced minimums. If a paragraph naturally mentions another service, link it. If not, leave it.

**Rules when linking:**
- **Zero duplicate URLs** within any single page — each service URL appears once maximum
- Navigation menu links are template-level and do NOT count
- Vary anchor text naturally across different pages
- Descriptive anchor text that describes the destination service
- Authority flows naturally through contextual connections

## Deduplication Gate

Before drafting: verify the topic is not already covered by an existing or planned article.
- If keywords share >40% core entities with another topic: MERGE into one pillar
- Never publish two articles where the "What is [X]" section is identical

## HARD STOP 0: Load Writing Skill First (MANDATORY)

**Before writing ANY content, the agent MUST load the master content skill: `rankray-seo-content-mastery`.** This is a non-negotiable step. The skill contains the full structural framework, formatting rules, and quality standards for modern SEO content (it consolidates the former blog-writer, landing-page-writer, keyword-research, content-cluster, internal-linking, meta-generator, schema-generator, snippet-hunter, technical-audit, refresher, and WordPress-publisher skills).

| Content Type | Load |
|--------------|------|
| ALL content/SEO work (blogs, service pages, keywords, clusters, links, meta, schema, snippets, audits, technical, publishing) | `~/.hermes/profiles/enigma/skills/seo/rankray-seo-content-mastery/SKILL.md` |

The skill's reference files (writing-standards, semantic-research, ai-tells-removal, schema-and-meta, audits-technical-refresh, service-pages-and-publishing) load per task as directed inside the skill.

**Violation:** Writing content without loading the mastery skill = immediate content quality failure. Do not proceed past this step without confirming skill content is loaded. (Former per-type skill paths under `Ai Brain/skills/` were consolidated on 2026-08-28; see `skills/_CATALOG_MAP.md`.)

## Formatting & Readability Rules (MANDATORY)

Articles MUST be scannable. Dense paragraphs = reader bounce = ranking drop.

### Paragraph Structure
- **Maximum 3 sentences per paragraph.** Break long thoughts into separate paragraphs.
- **Maximum 60 words per paragraph.** If a paragraph exceeds 60 words, split it.
- One-idea-per-paragraph rule: each paragraph makes exactly one point.

### Heading Hierarchy
- Every H2 section MUST contain 1-3 H3 subsections minimum.
- **Maximum 300 words between headings.** If an H2 section exceeds 300 words without an H3, insert an H3 or split the H2.
- H3s should be specific and scannable (e.g., "Server Latency to UAE" not "More Technical Details").

### Summary Block Label by Post Type

Every blog post MUST have a 2-3 sentence summary block placed immediately after the intro paragraph, before the first H2. The label changes based on post type to match reader expectations.

| Post Type | Label | When to Use |
|-----------|-------|-------------|
| **How-to / Tutorial / Step-by-step** | **"What You'll Learn"** | Action-oriented, process-driven content |
| **Data / Research / Study / Report** | **"Key Findings"** | Posts with statistics, analysis, original research |
| **Comparison / VS / Alternatives** | **"Quick Comparison"** | When comparing tools, methods, or services |
| **Pillar / Ultimate Guide / Complete** | **"Key Takeaway"** | Long-form comprehensive guides |
| **Opinion / Thought Leadership / Trends** | **"The Bottom Line"** | Perspective pieces, predictions, commentary |
| **Listicle / Examples / Resources** | **"Quick Overview"** | Roundup posts, lists, curated resources |
| **Case Study / Client Story / Results** | **"The Results"** | Before/after, performance data, client wins |
| **Definition / What Is / Beginner Guide** | **"In Short"** | Educational, explanatory, foundational content |

**Rules for the summary block:**
- Exactly 2-3 sentences
- Answers the article's core question directly
- Extractable without surrounding context
- No self-referential phrases ("in this guide", "as we discussed")
- Place inside a `<blockquote>` for visual distinction
- Never use "TL;DR" — banned from all RankRay content

### Lists & Tables

- Any sequence of 3+ related items MUST be a `<ul>` or `<ol>` list, not a paragraph.
- Comparisons MUST use tables (`<table>`): vs sections, tool comparisons, pricing tiers, feature sets.
- Process steps MUST be numbered lists (`<ol>`).
- Key benefits MUST be bullet lists (`<ul>`).

### Visual Breaks
- Insert a horizontal rule (`<hr>`) between major H2 sections where topic shifts significantly.
- Use **bold text** for key terms, stats, and actionable takeaways within paragraphs.
- Include a "Key Takeaway" or "Quick Summary" box (blockquote or colored div) at the end of every major H2 section.

### Content Density Formula (2026 Standard)
- **300-400 words per H2 section** (including H3s and lists).
- **80-120 words per H3 subsection**.
- **20-40% of total words in list/table format** (not paragraph prose).
- Target: reader can skim headings + bold text + lists and still get 80% of the value.

### Before & After Example
WRONG (dense paragraph, no breaks):
```
Technical SEO in Dubai faces the same core requirements as any market, with additional complexity around hosting location, Arabic text rendering, and Core Web Vitals performance across diverse device and connection environments. Server location and hosting infrastructure matter for Dubai SEO. Google's crawling and indexing performance improves when sites load from servers with low latency to UAE users.
```

RIGHT (scannable, structured):
```
<h3>Server Location for UAE Audiences</h3>
<p>Google crawls faster from nearby servers. For Dubai SEO:</p>
<ul>
  <li><strong>Target:</strong> Under 200ms response time from Dubai test locations</li>
  <li><strong>CDN:</strong> Use Cloudflare or AWS CloudFront with UAE edge nodes</li>
  <li><strong>Hosting:</strong> UAE-based providers (e.g., AWS Middle East) outperform EU servers</li>
</ul>

<h3>Arabic Text Rendering</h3>
<p>Arabic introduces technical issues English sites never face:</p>
<table>
  <tr><th>Issue</th><th>Impact</th><th>Fix</th></tr>
  <tr><td>RTL direction</td><td>Layout breaks on mobile</td><td>Add <code>dir="rtl"</code> + CSS</td></tr>
  <tr><td>Font compatibility</td><td>Glyphs render as boxes</td><td>Use web-safe Arabic fonts</td></tr>
</table>
```

## Pre-Push Sequence

Run this checklist IN ORDER before every push:

```
1.  [ ] Body contains zero <h1> tags
2.  [ ] Word count >= 2,000
3.  [ ] Internal links are contextually relevant — no forced minimum, quality over quantity
4.  [ ] Yoast focus keyword SET
5.  [ ] Yoast meta title SET and < 60 chars — **verify with `len()` before push, never estimate**
6.  [ ] Yoast meta description SET and < 160 chars — **verify with `len()` before push, never estimate**
7.  [ ] Categories assigned with correct IDs (never default "Topics" [1]) — fetch live category list first
8.  [ ] No em-dashes found (search the content)
9.  [ ] No repeated words found
10. [ ] Markdown converted to HTML
11. [ ] Featured image uploaded with **descriptive alt text set via REST API**
12. [ ] Status = Draft
13. [ ] Post ID + slug logged in /Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/{project}/post-registry.md
14. [ ] **FRONTEND VERIFICATION:** Open live URL (or preview URL for drafts), confirm title tag, meta description, categories, and featured image all render correctly. **NEVER mark a post DONE without this step.**
```

## Brand Voice

- **RankRay:** Professional, authoritative, data-driven
- **TonicPhysio:** Caring, professional, health-focused
- **TeamMotorcycle:** Enthusiast, technical, community-focused
- **Coinsfera:** Crypto-savvy, international, professional

## Tonic Physio Additional Content Rules (MANDATORY)

These rules apply to ALL Tonic Physio blog posts and override general rules where specified:

1. **AI Answer Engine Optimization (AEO) Priority**
   - Content must be optimized for AI answers and generative engine citations
   - Every section must be extractable as a standalone answer
   - No "as mentioned above" or cross-references between sections

2. **Paragraph & Structure Rules**
   - Maximum 3 sentences per paragraph
   - Maximum 60 words per paragraph
   - Divide content into multiple headings and subheadings for AI understanding
   - Every H2 section MUST contain 1-3 H3 subsections minimum
   - Maximum 300 words between headings

3. **Featured Image Alt Text**
   - Featured image MUST have the blog's focused keyword in its alt text
   - Alt text format: "[Focus Keyword] - [Brief Description]"
   - Example: "back pain recovery Milton - physiotherapist treating patient at Tonic Physio"
   - Verify alt text is set via REST API immediately after upload

4. **Post-Publication Verification**
   - After finishing any blog, ALWAYS double-check the live link
   - Check for: repetitive words, repetitive sentences, spelling mistakes, grammar issues
   - Verify all internal links work and point to correct pages
   - Confirm featured image displays with correct alt text
   - Check mobile rendering

5. **Skill Usage Requirement**
   - Before writing ANY Tonic Physio content, load the master skill first:
     - ALL content work: `rankray-seo-content-mastery` at `~/.hermes/profiles/enigma/skills/seo/rankray-seo-content-mastery/SKILL.md`
   - Follow skill instructions exactly — do not improvise structure

6. **Local SEO Signals**
   - Include Milton and nearby cities (Oakville, Burlington, Georgetown, Halton)
   - Reference local landmarks where natural (Milton Conservation Area, community centres)
   - Use "near me" synonyms in headings and body
   - Mention OHIP, WSIB, and extended health coverage where relevant

## ACF Service Page Format Rules (RankRay — 2026-05-15)

### Paragraph Format — `<p>` Tags ONLY
- **NEVER use `<br/><br/>`** for line breaks between paragraphs.
- Every paragraph must be wrapped in `<p>...</p>` tags.
- Multiple paragraphs = multiple `<p>` blocks.
- **CORRECT:** `<p>First para...</p>\n<p>Second para...</p>`
- **WRONG:** `First para...<br/><br/>Second para...`

### H2 Headings — Sell, Don't Explain
- Pattern: `High-Impact [Service Name] Services That Drive [Outcome] to Your Business`
- **WRONG:** `What Is Semantic SEO?` (informational)
- **RIGHT:** `High-Impact Semantic SEO Services That Drive Targeted Organic Traffic to Your Business` (sales)

### First Paragraph Below H2 — "We Help..."
- Must start with "We help..." or "We specialize..."
- Outcome-focused, never definitional.
- **WRONG:** `Semantic SEO moves beyond single keywords...` (explaining)
- **RIGHT:** `We help businesses rank for meaning... By optimizing for entities... we drive qualified organic traffic.`

### H3 Subheadings Inside h2_paragraph Fields (CRITICAL — Frontend Breaks if Wrong)
- h2_paragraph fields CAN contain `<h3>` subheadings.
- **Field distribution (MANDATORY):**
  - `h2_paragraph_2` = FIRST `<h3>`Build [Outcome] That Converts`</h3>` + 2-3 `<p>` blocks
  - `h2_paragraph_3` = SECOND `<h3>`Data-Driven [Service] That Delivers Measurable Growth`</h3>` + 2-3 `<p>` blocks
- **NEVER put both H3s in h2_paragraph_2 and leave h2_paragraph_3 empty** — the ACF template renders both fields as separate frontend widgets. Empty h2_paragraph_3 = broken layout with visible empty section.
- **ALWAYS verify** the reference page (Semantic SEO, ID 19892) has content in BOTH fields before writing any new page.

### NO Links in h1_paragraph (Above the Fold)
- `h1_paragraph` field must contain ZERO links.
- This is the first view when user visits.
- Internal links go AFTER the fold only (h2_paragraph_2+, services, portfolio).

### Portfolio/Case Study Area Format
- Field: `h3_portfolio_paragraph_before_3_boxes`
- Structure:
```html
<p><strong>Dominate Your [Category] with Proven [Service] Services</strong></p>
<p>Problem statement... losing to competitors...</p>
<p>Solution paragraph with <strong>bold key terms</strong>...</p>
<p>Every strategy paragraph with <a href="/path/">internal links</a>...</p>
<p>Multi-location or scale paragraph...</p>
<p><strong>Ready to...</strong> <a href="/contact/">CTA link</a>...</p>
```
- Word count: 150-250 words

### Canonical Reference Page
- **Semantic SEO (ID 19892)** is the canonical ACF service page reference.
- When writing ANY ACF service page, fetch Semantic SEO first and mirror its field-by-field structure.

### ACF Service Page Field-Length Rules (HARD STOPS)
**Violating these = immediate user rejection.**

| Field | Max Length | Violation |
|-------|-----------|-----------|
| `why_us_box_X_heading` | 2-4 words (15 chars max) | Never paragraphs. Never template text from other pages. |
| `services_X_heading` | 2-4 words | Service name only. No descriptions. |
| `question_X` | One sentence question (40 chars max) | Never append answers. Never template text. |
| `faq_heading` | 2-3 words + "FAQs" | e.g. "LinkedIn Advertising FAQs" |

**Rule: ALWAYS read current field content before writing. If a heading contains >15 chars, it's corrupted — reset to 2-4 words.**

## AEO Body-Extraction Standards (2026-05-17)

**Google no longer prefers FAQ schema. Write for Answer Engine Optimization (AEO).**

### What Changed
- FAQ sections at the bottom of posts — REMOVED
- Direct-answer summary blocks — REQUIRED
- Self-contained H2/H3 sections — REQUIRED
- Definition sentences in "What Is" sections — REQUIRED
- Comparison tables where relevant — REQUIRED
- Content structured so AI extracts answers from the BODY, not a separate FAQ block

### AEO Requirements Per Post
1. **Direct-Answer Summary Block** — 2-3 sentences in blockquote after intro. Label based on post type ("Key Takeaway", "In Short", "What You'll Learn", etc.)
2. **Definition Sentence** — First sentence of first H2 ("What Is [Topic]?") must be a self-contained, extractable definition.
3. **Self-Contained Sections** — Every H2/H3 section should answer a specific question standalone. No "as mentioned above" references.
4. **Comparison Tables** — Use `<table>` for any vs/comparison content. Tables are highly extractable by AI.
5. **Scannable Structure** — Bullet lists, numbered steps, bold key terms. AI extracts list items easily.
6. **No FAQ Section** — Do not add a dedicated FAQ block at the bottom. The body itself IS the FAQ.
7. **No FAQ Schema** — Do not add FAQ schema markup.

### AEO Content Structure (Mandatory for Blog Posts)
```
Intro paragraph (100-150 words)

<blockquote>Direct-answer summary: 2-3 sentences answering the core question</blockquote>

H2: What Is [Topic]? (Definition sentence as first line)
H2: Why It Matters
H2: How It Works (with H3 sub-sections)
H2: Practical Steps / Key Benefits
H2: Common Mistakes to Avoid
H2: Comparison / vs (if relevant — use table)
H2: Conclusion + Soft CTA
```

### Why This Works for AEO
- AI engines (ChatGPT, Perplexity, Gemini) extract answers from heading-adjacent content
- Definition sentences get pulled directly for "What is X?" queries
- Tables get extracted for comparison queries
- Lists get extracted for "How to" and "What are" queries
- Summary blocks get pulled for overview questions

### Pre-Push AEO Checklist
- [ ] Direct-answer summary block present (blockquote, 2-3 sentences)
- [ ] Definition sentence in first H2 (self-contained, no context needed)
- [ ] Every H2/H3 answers a specific, standalone question
- [ ] No "as mentioned above" or "as we discussed" references
- [ ] Comparison tables used where relevant
- [ ] NO FAQ section at bottom
- [ ] NO FAQ schema markup

---
Last Updated: 2026-05-17
