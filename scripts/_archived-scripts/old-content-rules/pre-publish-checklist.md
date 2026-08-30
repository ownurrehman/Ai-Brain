> **Parent Hub:** [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]] · [[scripts/INDEX|🛠️ Scripts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

# Pre-Publishing QA Checklist

# Mandatory for every blog post and page before sending to WordPress

## A. Content Quality Gates

- [ ] **No em dashes (:) or en dashes (–).** Use hyphens (-) or colons (:).
- [ ] **No repeated words consecutively** (e.g., "Understanding Understanding").
- [ ] **No duplicate paragraphs or concepts.** Every paragraph adds NEW info.
- [ ] **H1 is NEVER in body content.** WordPress generates H1 from post title. Body starts with <p> or <h2>.
- [ ] **No filler phrases:** "In today's digital landscape...", "It is important to note...", "As we know..."
- [ ] **No AI shortcodes** like `[rankray_ai_summary]`.
- [ ] **Active voice dominates** (80%+).
- [ ] **Sentence length varied** (short punchy + longer explanatory).
- [ ] **Second person** ("you" / "your") for engagement.

## B. Semantic SEO Verification

- [ ] **Primary keyword in Title which is H1.**
- [ ] **Primary keyword in first 100 words.**
- [ ] **Primary keyword in at least one H2.**
- [ ] **LSI/semantic keywords** woven naturally (8-12 per 1000 words).
- [ ] **300+ entities** integrated naturally (from semantic research).
- [ ] **7 of 9 semantic frames covered** (Definition, Process, Components, Benefits, Comparison, Tools, Case Studies, Implementation, Future/Trends).
- [ ] **Word count:** 2,500+ (ideal 3,000-5,000).
- [ ] **Content structure:** Intro -> 8-12 H2s -> Conclusion -> FAQs.

## C. On-Page SEO

- [ ] **Meta title:** < 60 characters, includes primary keyword + brand.
- [ ] **Meta description:** < 160 characters, includes keyword + LSI + brand name.
- [ ] **Focus keyword** set in Yoast.
- [ ] **Keyphrase density:** 1-2% (natural, not stuffed).
- [ ] **Social sharing meta tags** (Open Graph, Twitter Cards) : if supported.
- [ ] **Schema markup** relevant to content type (Article, FAQPage).

## D. Links

- [ ] **Internal links:** 8-12 minimum for 2,500+ word articles. 5-8 for shorter content.
- [ ] **Link distribution:** Service pages (40%) + Blog posts (40%) + Pillar content (20%).
- [ ] **No duplicate internal links** (same page linked twice).
- [ ] **External links:** Only high-authority educational/official sources.
- [ ] **No external links to competitor sites.**
- [ ] **Anchor text:** Natural, varied, not keyword-stuffed.
- [ ] **Link relevance:** Every link must be contextually relevant to the section.
- [ ] **Sitemap verification:** Fetch sitemap before writing to identify link opportunities.
- [ ] **"Rank Ray" anchor text -> homepage ONLY** (if RankRay content).

## E. Media (Images)

- [ ] **1 featured image ONLY per blog post.** No images under H2 sections.
- [ ] **Image filename:** Descriptive, keyword-based, no "image1.jpg".
- [ ] **Alt text:** Natural description with keyword variation.
- [ ] **Image source:** Pexels, Unsplash, or Pixabay (copyright-free).
- [ ] **Max file size:** 100KB.
- [ ] **No hotlinking.** Upload to WordPress Media Library.
- [ ] **NEVER reuse** existing media library images for new content.

## F. HTML & Markup

- [ ] **No broken HTML tags.** Valid `<h2>`, `<p>`, `<ul>`, `<li>`, `<a>`, `<strong>`, `<em>`.
- [ ] **No raw Markdown left** in published content.
- [ ] **Links open in new tab:** `target="_blank" rel="noopener noreferrer"`.
- [ ] **All `<img>` tags have `alt` and `loading="lazy"`.**

## G. Brand Voice

- [ ] Tone matches site:
- **RankRay:** Professional, authoritative, data-driven.
- **TonicPhysio:** Caring, professional, health-focused, gentle.
- **TeamMotorcycle:** Enthusiast, technical, community-focused.
- **KhanLLP:** Professional, trustworthy, legal expertise.
- **Coinsfera:** Crypto-savvy, international, professional.
- [ ] **Soft CTA** in conclusion (not aggressive sales pitch).
- [ ] **No emojis.**

## H. Publishing Verification

- [ ] **Status:** Draft (never publish without user approval).
- [ ] **Slug:** Clean, keyword-focused, no "-draft" suffix.
- [ ] **Author:** Correct user assigned.
- [ ] **Category:** Correct category selected.
- [ ] **Tags:** 3-5 relevant tags.
- [ ] **Yoast analysis:** Green/Good (if possible).
- [ ] **Open live link** and verify formatting.
- [ ] **Verify no H1 in body** : inspect page source.
- [ ] **Close browser tab.**

## I. AEO & LLM Readiness

- [ ] **AEO Extraction Sentence:** Explicitly stating the main answer/fact in one clear sentence at the start or end of the relevant section.
- [ ] **Entity Clarity:** No ambiguous "it" or "they" when referring to key topics.
- [ ] **Semantic Frames:** All 9 frames considered (if relevant).
- [ ] **No Hallucinations:** Every claim or statistic verified against project data or reliable sources.

## J. Post-Push Log

After pushing, record in log:

```
Date / Blog # / Post ID / Slug / WP Link / WP Edit Link / Status (draft/published)
```

---

## Final Hard Stop

**NEVER push if ANY of these are violated:**

1. **H1 TAG IN BODY CONTENT** (WordPress title is the only H1).
2. **BODY STARTS WITH <h1>** (Must start with <p> or <h2>).
3. **EM DASHES DETECTED** (The long horizontal line — ).
4. Fewer than 8 internal links for 2,500+ word articles.
5. Internal links not verified from sitemap.
6. Missing Yoast meta description.
7. Content under 2,000 words.
8. Duplicate paragraphs detected.

---

Last Updated: 2026-05-02
