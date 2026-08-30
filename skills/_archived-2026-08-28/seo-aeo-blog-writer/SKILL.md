---
name: seo-aeo-blog-writer
description: "Writes long-form blog posts with direct-answer summary block, definition sentence, and comparison table for AEO (Answer Engine Optimization). Activate when the user wants to write a blog post, article, or long-form content piece."
risk: safe
source: community
date_added: "2026-04-01"
date_updated: "2026-05-17"
---

> **Parent Hub:** [[skills/_archived-2026-08-28/INDEX|📦 Archived Skills Hub]] · [[skills/_CATALOG_MAP|⚡ Skills Catalog]] · [[INDEX|🧠 Master Ai Brain Hub]]

# SEO-AEO Blog Writer

## Overview

Writes structured long-form blog posts (800-3000 words) that satisfy both SEO ranking signals and AEO (Answer Engine Optimization) citation requirements. Every post includes a direct-answer summary block, a definition sentence, structured H2/H3 hierarchy, and a comparison table where relevant. Content is structured so AI engines extract answers directly from the BODY, not from a separate FAQ block.

**CRITICAL RULE CHANGE (2026-05-17):** Google no longer prefers FAQ schema. FAQ sections are REMOVED from all content. Write for AEO — AI extracts answers from heading-adjacent body content.

## When to Use This Skill

- Use when writing a cluster article from a content cluster map
- Use when creating a long-form guide to build topical authority
- Use when you need content that can be cited by AI engines like Perplexity or ChatGPT
- Use when you need a blog post that follows a consistent, auditable structure

## How It Works

### Step 1: Write the Direct-Answer Summary Block First

Write a 2-3 sentence direct answer to the article's core question. Place it immediately after the intro paragraph in a blockquote. Label the block based on post type:

| Post Type | Label |
|-----------|-------|
| How-to / Tutorial | "What You'll Learn" |
| Data / Research / Study | "Key Findings" |
| Comparison / VS / Alternatives | "Quick Comparison" |
| Pillar / Ultimate Guide / Complete | "Key Takeaway" |
| Opinion / Thought Leadership / Trends | "The Bottom Line" |
| Listicle / Examples / Resources | "Quick Overview" |
| Case Study / Client Story | "The Results" |
| Definition / What Is / Beginner | "In Short" |

**Never use "TL;DR"** — it is banned from all RankRay content. This block is the first content AI engines attempt to extract.

### Step 2: Build the Heading Skeleton
Set H1, H2s (4-6), and H3s before writing any body content. The first H2 must be a "What Is" section with a clean definition sentence as its opening line.

### Step 3: Write Body Sections
Follow the section order: What Is (with definition sentence) -> Why It Matters -> How It Works (with H3 sub-concepts) -> Practical Steps -> Common Mistakes -> Comparison Table (if relevant) -> Conclusion.

Each H2/H3 section must be self-contained and answerable on its own. No "as mentioned above" references.

### Step 4: AEO Extraction Points
Structure content so AI engines can extract answers from these points:
- **Definition sentence** in "What Is [Topic]?" section (first sentence of first H2)
- **List items** under "How It Works" and "Practical Steps" H3s
- **Table cells** in comparison sections
- **Summary block** (blockquote after intro)
- **Bold key terms** within paragraphs

### Step 5: Run AEO and SEO Checklists
Verify summary block presence, definition sentence, keyword placement, heading structure, and self-contained sections before outputting.

## Examples

### Example: Direct-Answer Summary Block
How to Manage a Remote Engineering Team

**Key Takeaway:** Managing a remote engineering team requires async
communication tools, clear documentation standards, and
timezone-aware sprint planning. Teams that nail these three
areas ship consistently regardless of where members are located.

### Example: Self-Contained H3 Section
<h3>What Is Cervical Spondylosis?</h3>
<p>Cervical spondylosis is age-related wear and tear affecting the spinal disks in your neck. As the disks dehydrate and shrink, signs of osteoarthritis develop, including bone spurs. Most people experience no symptoms, but when they do, neck pain and stiffness are the most common.</p>

### Example: Comparison Table
<table>
<tr><th>Feature</th><th>Hot Stone Massage</th><th>Swedish Massage</th></tr>
<tr><td>Heat Source</td><td>Heated basalt stones</td><td>Therapist's hands only</td></tr>
<tr><td>Pressure Level</td><td>Light to medium</td><td>Light to firm</td></tr>
</table>

## Best Practices

- Do: Write the summary block before writing anything else — it anchors the article
- Do: Make the "What Is" definition sentence extractable on its own — one clean sentence
- Do: Use secondary keywords as H2/H3 headings to capture long-tail traffic
- Do: Use comparison tables for any vs/alternatives content — AI extracts tables easily
- Do: Write self-contained H3 sub-sections (no cross-references)
- Don't: Add FAQ sections at the bottom — the body IS the FAQ
- Don't: Write answers longer than 50 words in list/table items
- Don't: Skip the comparison table if the topic involves comparing options

## Common Pitfalls

- **Problem:** Summary block is too vague to be extracted as a direct answer
  **Solution:** The summary block must answer the article's core question in 2-3 sentences. If it doesn't answer a specific question, rewrite it.

- **Problem:** Definition sentence is buried in a paragraph and not extractable
  **Solution:** Make it the first sentence of the first H2. One clean, standalone sentence.

- **Problem:** Body sections reference other parts of the article ("as mentioned above")
  **Solution:** Every H2/H3 section must stand completely alone. No references to other sections.

## Related Skills

- `@seo-aeo-content-cluster` — provides the topic and keyword for this article
- `@seo-aeo-content-quality-auditor` — audits the completed post for SEO and AEO signals
- `@seo-aeo-internal-linking` — maps links between this post and related pages

## Additional Resources

- [SEO-AEO Engine Repository](https://github.com/mrprewsh/seo-aeo-engine)

## Limitations
- Use this skill only when the task clearly matches the scope described above.
- Do not treat the output as a substitute for environment-specific validation, testing, or expert review.
- Stop and ask for clarification if required inputs, permissions, safety boundaries, or success criteria are missing.
