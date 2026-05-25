# CONTENT QUALITY RULES — CRITICAL FOR ALL AI-GENERATED CONTENT

**Effective:** 2026-04-21  
**Status:** MANDATORY — ALL AGENTS MUST FOLLOW  
**Applies to:** ALL content generation (blogs, pages, articles) across ALL 5 sites

---

## 🚨 CRITICAL RULES (NEVER VIOLATE)

### 1. ❌ NO `[rankray_ai_summary]` SHORTCODE
**Issue:** AI-generated shortcode appears in content  
**Rule:** NEVER include any AI summary shortcodes in content  
**Fix:** Remove all shortcodes before publishing

---

### 2. ❌ NO DUPLICATE H1 AND TITLE
**Issue:** `<title>` and `<h1>` are identical ("Semantic Seo: Complete Guide & Professional Services")  
**Rule:** H1 MUST be different from title tag
- **Title tag:** Optimized for SERP (includes brand, under 60 chars)
- **H1:** User-facing headline (engaging, different wording)

**Example:**
```
Title: Semantic SEO Services: Complete Guide | Rank Ray
H1: What Is Semantic SEO and Why It Matters for Rankings
```

---

### 3. ❌ NO LONG DASHES (EM DASH / EN DASH)
**Issue:** AI uses "—" (em dash) and "–" (en dash) excessively  
**Examples from content:**
- "clusters — all play specific roles"
- "SERP analysis — including semantic"

**Rule:** NEVER use em dashes (—) or en dashes (–) in content
**Fix:** Use regular hyphens (-), colons (:), or rephrase sentences

**Why:** These dashes are obvious AI footprints that Google and readers recognize

---

### 4. ❌ NO REPEATED WORDS TOGETHER
**Issue:** "Understanding Understanding Semantic Search"  
**Rule:** NEVER repeat the same word consecutively  
**Fix:** Proofread all content for duplicate words before publishing

**Common AI mistakes:**
- "Understanding Understanding..."
- "The The..."
- "Semantic Semantic..."
- "SEO SEO..."

---

### 5. ❌ NO CONTENT DUPLICATION / FILLER
**Issue:** Same paragraphs repeated throughout content:
```
First, you need to establish a clear understanding of the core concepts...
Second, you apply these concepts systematically...
Third, you measure and refine based on actual performance data...
```

**Rule:** NEVER repeat the same concept/paragraph multiple times
**Fix:** Each paragraph must add NEW information, not rephrase previous content

**AI Tendency:** LLMs try to hit word counts by repeating concepts in different words  
**Solution:** Focus on quality over quantity — 2,500 words of unique content > 5,000 words with repetition

---

## 📝 CONTENT STRUCTURE RULES

### Headings Hierarchy
```
H1: One only (different from title tag)
H2: Main sections (6-10 for pillar content)
H3: Subsections under H2 (2-4 per H2)
H4: Only when necessary (rare)
```

### Paragraph Rules
- **Length:** 2-4 sentences max
- **Structure:** Topic sentence → Supporting detail → Example/transition
- **No filler:** Every paragraph must add new information
- **No repetition:** Don't say the same thing twice

### Word Count Guidelines
- **Pillar content:** 3,000-5,000 words (comprehensive, not padded)
- **Cluster content:** 1,500-2,500 words (focused depth)
- **Quality > Quantity:** Better to be shorter and unique than longer and repetitive

---

## ✅ PRE-PUBLISHING CHECKLIST

Before ANY content goes live, verify:

- [ ] No `[rankray_ai_summary]` or any shortcodes
- [ ] H1 is different from title tag
- [ ] No em dashes (—) or en dashes (–) anywhere
- [ ] No repeated words together ("Understanding Understanding")
- [ ] No duplicate paragraphs or concepts
- [ ] Every paragraph adds new information
- [ ] Content flows logically without repetition
- [ ] Word count is appropriate (not padded)
- [ ] All images have alt text
- [ ] Yoast fields are set (focus keyphrase, meta description)
- [ ] Internal links are verified from sitemap
- [ ] No external links to competitors

---

## 🔧 AI CONTENT GENERATION PROMPT FIXES

When generating content, agents MUST use these instructions:

```
CONTENT GENERATION RULES:
1. Never use em dashes (—) or en dashes (–). Use regular hyphens (-) or colons (:) instead.
2. Never repeat the same word consecutively (e.g., "Understanding Understanding").
3. Never include shortcodes like [rankray_ai_summary].
4. Make H1 different from title tag.
5. Each paragraph must introduce NEW information — no repetition or rephrasing.
6. Avoid filler phrases like "First, you need to...", "Second, you apply...", "Third, you measure..."
7. Focus on quality over word count — 2,500 unique words > 5,000 padded words.
8. Proofread for AI footprints: long dashes, repeated concepts, template structures.
```

---

## 📖 EXAMPLES

### ❌ WRONG (AI-generated with issues):
```html
<title>Semantic SEO: Complete Guide & Services</title>
<h1>Semantic SEO: Complete Guide & Services</h1>

<p>Understanding Understanding Semantic SEO is important — very important.</p>

<p>First, you need to establish a clear understanding of the core concepts. This means going beyond surface-level definitions.</p>

<p>Second, you apply these concepts systematically across your content. This isn't about keyword stuffing — it's about creating useful content.</p>

<p>Third, you measure and refine based on actual performance data. What works for one topic — may need adjustment.</p>

[rankray_ai_summary]
```

### ✅ CORRECT (Human-quality):
```html
<title>Semantic SEO Services: Complete Guide | Rank Ray</title>
<h1>What Is Semantic SEO and Why It Drives Rankings</h1>

<p>Semantic SEO transforms how search engines interpret your content. Instead of matching keywords, modern algorithms analyze meaning, context, and entity relationships.</p>

<p>Google's Knowledge Graph connects over 500 billion facts about 5 billion entities. When your content mirrors these connections, rankings improve naturally.</p>

<p>Our clients see 40-120% traffic increases within 6 months by implementing entity-based optimization strategies.</p>
```

---

## 🎯 ENFORCEMENT

**ALL agents (main, enigma, chronos, researcher, subagents) MUST:**
1. Read these rules before content generation
2. Apply rules to EVERY piece of content
3. Verify checklist before publishing
4. Reject content that violates these rules

**Memory Location:** These rules are stored in:
- `/workspace/CONTENT-QUALITY-RULES.md`
- Obsidian vault: `projects/openclaw-ops/CONTENT-QUALITY-RULES.md`
- Daily memory files reference this document

---

## 📚 RELATED FILES

- `MASTER-RULES.md` — Universal SEO rules
- `WORDPRESS-REST-API-FIX.md` — Publishing workflow
- `memory/2026-04-21.md` — Documentation of these issues

---

**Last Updated:** 2026-04-21  
**Version:** 1.0  
**Status:** ACTIVE — MANDATORY COMPLIANCE
