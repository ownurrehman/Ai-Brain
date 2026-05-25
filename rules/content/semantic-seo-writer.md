# Semantic SEO Writing : Koray Method

## Trigger Phrases

Users can invoke this skill with any of these:

- "semantic seo blog for [topic]"
- "write semantic article about [topic]"
- "koray method article on [topic]"
- "semantic content for [website] about [topic]"
- "pillar content [topic]"
- "entity-based article [topic]"

## Overview

This is the **Koray Tuğberk Gübür semantic SEO methodology** adapted as a prompt-based execution system. It replaces the Python-scripted Semantic Brief Engine with a pure reasoning workflow that any agent (Main, Nemo, or Chronos) can execute on-demand.

**What it produces:** 2,500-5,000+ word pillar articles with full semantic coverage, entity integration, internal linking, and publication-ready structure.

**Time per article:** ~15-20 minutes (vs 6+ hours manual).

---

## Phase 1: Semantic Research (4-6 minutes)

### Step 1: SERP Landscape Analysis
**Tool:** OpenSERP (local) or web_search

Execute research queries:

1. Primary keyword
2. Primary + "guide"
3. Primary + "services"
4. Primary + "vs"
5. Primary + "examples"
6. "what is" + primary
7. "how to" + primary
8. Primary + "best practices"
9. Primary + "2026"
10. Primary + "tools"
11. Primary + "case study"
12. Primary + "benefits"

**For each query, extract:**

- Top 5 ranking URLs
- Page titles (H1 patterns)
- Meta descriptions (LSI terms used)
- Featured snippet type (paragraph, list, table)
- People Also Ask questions
- Related searches at bottom

### Step 2: Entity Extraction
**Goal:** Build entity map (target: 300-500 entities)

From SERP results, extract:

- **Named entities:** People (Koray Tuğberk Gübür), organizations (Google), products (Yoast SEO), locations
- **Concepts:** Topic clusters, semantic search, natural language processing
- **Attributes:** Properties of concepts (schema markup types, ranking factors)
- **Relationships:** How entities connect (Google uses BERT for semantic understanding)

**Tools:**

- Read top 3 ranking pages (web_fetch)
- Extract headings, subheadings, bold terms
- Build frequency table: which entities appear most often across competitors?

### Step 3: Intent Classification (5 Streams)

Classify each query into intent buckets:

1. **Informational** : "what is", "how to", "guide" (educate, define)
2. **Commercial** : "best", "top", "vs" (compare options)
3. **Transactional** : "services", "hire", "buy" (ready to purchase)
4. **Navigational** : brand + keyword (find specific company)
5. **Local** : "near me", city name (location-based)

**Output:** Intent distribution chart (e.g., 60% informational, 30% commercial, 10% transactional)

### Step 4: Semantic Frame Analysis (9 Frames)

Map content across 9 semantic frames:

| Frame | What It Covers | Example for "Semantic SEO" |
|-------|---------------|---------------------------|
| **Definition** | What it is, core meaning | "Semantic SEO optimizes content for meaning, not just keywords" |
| **Process** | How it works step-by-step | "Crawl -> Extract entities -> Build knowledge graph -> Rank" |
| **Components** | Parts/elements that make it up | Entities, schema markup, topic clusters, LSI keywords |
| **Benefits** | Why it matters | Higher rankings, better CTR, future-proof against algorithm updates |
| **Comparison** | vs alternatives | Semantic SEO vs traditional keyword-based SEO |
| **Tools** | Software/resources needed | Yoast SEO, Schema Pro, Surfer SEO, Clearscope |
| **Case Studies** | Real examples with results | Koray's case studies, documented ranking improvements |
| **Implementation** | Practical how-to guide | Step-by-step implementation for any website |
| **Future/Trends** | Where it's going | AI search, SGE, voice search, entity-first indexing |

**Target:** Cover at least 7 of 9 frames (78% coverage = competitive).

---

## Phase 2: Content Brief Generation (3-4 minutes)

### Step 5: Structure Design

Create section outline based on frame coverage:

```
SEO Title: [Primary Keyword]: [Value Proposition] | [Brand]
(NO H1 IN BODY CONTENT - WordPress generates H1 from the post title)

Intro (150-200 words):
- Hook: Problem or surprising stat
- Intent clarification: "This guide covers X, Y, and Z"
- Promise: What reader will learn
- Scope: Who this is for

H2 Sections (8-12 sections, 250-400 words each):
1. What Is [Topic]? (Definition frame)
2. How [Topic] Works (Process frame)
3. Key Components of [Topic] (Components frame)
4. Benefits of [Topic] (Benefits frame)
5. [Topic] vs [Alternative] (Comparison frame)
6. Best Tools for [Topic] (Tools frame)
7. [Topic] Case Studies (Case Studies frame)
8. How to Implement [Topic] (Implementation frame)
9. Future of [Topic] (Future/Trends frame)
10. Expert Tips for [Topic] (Unique insights)
11. Common Mistakes to Avoid (Pain points)

Conclusion (100-150 words):
- Summary of key takeaways
- Next steps/action items
- Soft CTA (not aggressive)
```

### Step 6: Internal Linking Plan

**Rule:** Minimum 10 internal links per article. Stretch to 15-20 for pillar content (3000+ words).

**Link Distribution:**

- 50% Service pages (minimum 5 service page links)
- 50% Blog posts (minimum 5 blog post links)

**Word Count Targets:**

- 3000+ word pillar content: 15-20 internal links
- 2000-3000 word articles: 10-15 internal links
- Under 2000 words: 10 internal links minimum

**Link Types:**

1. Service pages (link to relevant treatment/service pages) - MINIMUM 5
2. Blog posts (link to related articles and guides) - MINIMUM 5
3. Pillar content (link to broader topic guides) - 1-2 per article
4. Category/tag pages (optional) - 1-2 per article

**Process:**

1. Fetch site sitemap (sitemap_index.xml -> post-sitemap.xml + page-sitemap.xml)
2. Extract all URLs and categorize (services, blogs, pillars)
3. Map URLs to article sections based on contextual relevance
4. Never link same URL more than once per article
5. Use natural anchor text (not exact match keyword-stuffed)
6. Ensure links are contextually relevant to the paragraph

**Example anchor mapping:**

- Section "What Is Semantic SEO" -> Link to `/what-is-seo/` with anchor "search engine optimization fundamentals"
- Section "Schema Markup" -> Link to `/technical-seo/` with anchor "structured data implementation"
- Section "MVA Recovery" -> Link to `/motor-vehicle-accident-physiotherapy/` with anchor "MVA physiotherapy services"

**Internal Link Verification:**

- Must fetch and verify URLs from sitemap before writing
- Links must be to existing pages (not 404s)
- Anchor text must be natural and varied
- Every link should provide value to the reader
- MINIMUM 10 total internal links per article (5 service + 5 blog minimum)

### Step 7: Meta Fields

- **Title:** < 60 chars, includes primary keyword + brand
- **Description:** < 160 chars, includes exact keyword + LSI terms + brand name
- **Focus Keyphrase:** Primary keyword (exact match)

**Example:**

- Title: `Semantic SEO Services: Complete Guide | Rank Ray` (41 chars)
- Description: `Master semantic SEO with Rank Ray complete guide. Learn entity optimization, topic clusters, and LSI strategies that boost rankings.` (149 chars)
- Focus: `semantic seo services`

---

## Phase 3: Content Generation (8-12 minutes)

### Step 8: Writing Rules (CRITICAL : Zero AI Footprint)

**NEVER do these (Content Quality Rules):**

1. Em dashes (—) or en dashes (–) — obvious AI signal
2. Repeated words consecutively ("Understanding Understanding")
3. Duplicate paragraphs or concepts (filler to hit word count)
4. AI shortcodes like `[rankray_ai_summary]`
5. H1 tag anywhere in the content body (WordPress title is the only H1)
6. Body starting with an <h1> tag (must start with <p> or <h2>)
7. Generic intros like "In today's digital landscape..."
8. Fluff phrases: "It is important to note that...", "As we know..."

**ALWAYS do these:**

1. Vary sentence length (short punchy + longer explanatory)
2. Use transition words naturally ("Here's why:", "The result?", "But there's a catch")
3. Include specific numbers, stats, years when possible
4. Write in active voice 80%+ of the time
5. Add skepticism/disagreement where appropriate ("Most guides miss this...")
6. Include "expert tips" section with contrarian or advanced advice
7. Use "you" and "your" (second person) for engagement

### Step 9: Entity Integration

**Goal:** Naturally weave 300-500 extracted entities throughout content.

**Technique : Contextual Bridges:**

- Don't just list entities : connect them in sentences
- Example: "When Google processes your content, it doesn't just scan for keywords. It uses Natural Language Processing to identify entities like your brand name, key people, and core concepts, then maps them against its Knowledge Graph."

**Distribution:**

- Intro: 5-10 core entities
- Each H2: 15-25 entities naturally integrated

- Total: 300-500 unique entities

### Step 10: Image Brief Generation

**Rule:** 1 featured image ONLY per blog post. No images under H2 sections.

**Image brief format per image:**
```
Image #: [number]
Section: [H2 heading this supports]
Concept: [what the image should show]
Filename: [keyword-based, descriptive, no "featured image"]
Alt text: [natural description with keyword variation]
Source: Pexels/Unsplash/Pixabay
Orientation: Landscape (for Rank Ray) / As needed
```

**CRITICAL:** Never reuse images from site's media library. Always source fresh external images.

---

## Phase 4: Publishing Workflow (5-8 minutes)

### Step 11: Image Sourcing & Upload

**Tool:** Direct Pexels URLs (NOT Firecrawl or Brave Search : both fail for images)

**Process:**

1. Search Pexels/Unsplash for each image concept
2. Download with descriptive filename
3. Upload to WordPress media library via REST API
4. Set alt text for every image
5. Note media IDs for embedding

**WordPress REST API Auth:**

- Use: `<WP_USER>:<WP_REST_API_KEY>` from .env
- NEVER use app password for REST API (blocked by Cloudflare)

### Step 12: Post Creation

**Via WordPress REST API:**

1. Create draft post with title, slug, content
2. Set featured image (media ID)
3. Embed body images using WordPress block markup
4. Set Yoast fields:

- `yoast_focuskw`: primary keyword
- `yoast_metadesc`: meta description
- `yoast_title`: SEO title

**Verify:**

- Yoast SEO analysis shows green/good
- No '-draft' in permalink slug
- All images display correctly
- Internal links work

### Step 13: Final QA Checklist

Before marking complete:

- [ ] Word count: 2,500+ (ideal 3,000-5,000)
- [ ] 9 semantic frames: 7+ covered
- [ ] Entities: 300+ integrated naturally
- [ ] Internal links: 8-12 minimum, verified from sitemap
- [ ] **No H1 in body** : WordPress generates H1 from title
- [ ] **Images:** 1 featured image only, unique, with alt text
- [ ] Meta title: <60 chars
- [ ] Meta description: <160 chars, includes keyword + LSI + brand
- [ ] Yoast focus keyphrase set
- [ ] No em dashes (search for :)
- [ ] No repeated words
- [ ] No duplicate paragraphs
- [ ] Soft CTA in conclusion
- [ ] Summary block present with approved label
- [ ] Status: Draft (not published without review)

---

## Output Format

When user triggers this skill, deliver in this order:

1. **Research Summary** (2-3 bullets):

- "Analyzed 20 SERP queries, extracted 484 entities"
- "Intent: 60% informational, 30% commercial, 10% transactional"
- "Frame coverage: 8/9 (missing: Case Studies)"

2. **Content Brief** (outline with H2s + word targets):

- Section list with frame mapping
- Internal link plan
- Image count + concepts

3. **Generated Content** (full article):

- Complete markdown
- Ready to paste into WordPress

4. **Publishing Confirmation** (if WordPress credentials available):

- Post ID
- Media IDs
- Yoast fields set
- Edit URL

---

## Site-Specific Configurations

### Rank Ray (rankray.com)

- Brand voice: Professional, authoritative, data-driven
- Image orientation: Landscape
- Target word count: 3,000-5,000
- Internal link strategy: Service pages, case studies, methodology pages

### Tonic Physio (tonicphysio.com)

- Brand voice: Caring, professional, health-focused
- Image orientation: Landscape
- Target word count: 2,000-3,000
- Internal link strategy: Service pages, location pages, condition pages

### Team Motorcycle (teammotorcycle.com)

- Brand voice: Enthusiast, technical, community-focused
- Image orientation: Landscape
- Target word count: 2,500-4,000
- Internal link strategy: Product pages, category pages, review pages

### Khan LLP (khanllp.com)

- Brand voice: Professional, trustworthy, legal expertise
- Image orientation: Landscape
- Target word count: 2,000-3,500
- Internal link strategy: Practice area pages, lawyer profiles, location pages

### Coinsfera (coinsfera.com)

- Brand voice: Professional, crypto-savvy, international
- Image orientation: Landscape
- Target word count: 2,000-3,500
- Internal link strategy: Service pages, location pages, crypto guides

---

## Tools Required

| Tool | Purpose | When to Use |
|------|---------|-------------|
| web_search | SERP analysis, entity discovery | Phase 1, Step 1 |
| web_fetch | Read competitor pages, extract content | Phase 1, Step 2 |
| image | Source images from Pexels/Unsplash | Phase 3, Step 11 |
| exec (curl) | WordPress REST API | Phase 4, Steps 11-12 |
| read | Check sitemaps, verify URLs | Phase 2, Step 6 |

---

## Example Invocation

**User says:**
> "semantic seo blog for tonic physio about physiotherapy for back pain"

**Agent executes:**

1. Research "physiotherapy for back pain milton" + 11 related queries
2. Extract 300+ entities (spinal decompression, herniated disc, etc.)
3. Classify intent (70% informational, 20% commercial, 10% transactional)
4. Map 8/9 semantic frames
5. Generate brief with 10 sections, 8 internal links
6. Write 3,000-word article with entity integration
7. Source 11 fresh images
8. Upload to tonicphysio.com WordPress as draft
9. Set Yoast fields
10. Return: "Draft created: ID 12345. Edit: https://tonicphysio.com/wp-admin/post.php?post=12345"

---

## Memory Notes

- This skill was extracted from the full Semantic Brief Engine built on 2026-04-21
- Replaces Python scripts with pure prompt-based execution
- All agents (Main, Nemo, Chronos) can execute this
- WordPress REST API key must be in .env for publishing
- For sites without WordPress credentials, deliver markdown + image files for manual upload
