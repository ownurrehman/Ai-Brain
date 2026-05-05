# CONTENT QUALITY RULES

## MASTER RULE SETTINGS
These rules apply across ALL projects (Rank Ray, Tonic Physio, Khan LLP, etc.) and ALL agents. They override project-specific instructions unless a special exception is documented.

1. **H1 Protocol:** No H1 tags in the content body. The WordPress title is the only H1.
2. **AI Footprint:** No em-dashes, no repeated words, no "filler" intros.
3. **Verification:** Every article must pass the `self-audit-protocol.md`.

---

**Effective:** 2026-05-02 
**Status:** MANDATORY : ALL AGENTS MUST FOLLOW 
**Applies to:** ALL content generation (blogs, pages, articles) for Rank Ray, Tonic Physio, and Khan LLP.

---

## CRITICAL RULES (NEVER VIOLATE)

### 1. GLOBAL MASTER RULE: NO H1 IN BODY
**Issue:** Placing an `<h1>` tag inside the post content causes double H1s on WordPress (Title + Content). 
**Rule:** NEVER add an `<h1>` tag to the body of any post, page, or article.
- **WordPress Title Field:** This is the H1.
- **Content Headings:** Start with H2 for all main sections.
- **Enforcement:** Any agent finding an H1 in a draft MUST remove it immediately.

**Example:**
- **Title:** `Semantic SEO Services: Complete Guide | Rank Ray`
- **H1:** `How to Master Semantic Search for Explosive Rankings`

---

### 2. NO LONG DASHES (EM DASH / EN DASH)
**Rule:** NEVER use em-dashes (the long horizontal line) or en-dashes in content.
**Fix:** Use regular hyphens (-), colons (:), or rephrase the sentence.

**Why:** These dashes are obvious AI footprints that signal "generated content" to both users and search engines.

---

### 3. NO REPEATED WORDS
**Issue:** "Understanding Understanding Semantic Search." 
**Rule:** NEVER repeat the same word consecutively. 
**Fix:** Always perform a final proofread for double-word hallucinations.

---

### 4. NO CONCEPT REPETITION / FILLER
**Issue:** Rephrasing the same point 3 times in different paragraphs to hit word counts.
**Rule:** Every paragraph must add NEW value or data. If you have nothing new to say, stop writing.
**Solution:** Quality > Quantity. A tight 1,500-word piece is superior to a repetitive 3,000-word piece.

---

## CONTENT STRUCTURE RULES

### Headings Hierarchy
- **H1:** Handled by WordPress title field. **NEVER** place an `<h1>` tag in the body.
- **H2:** Main sections (approx. 6-10 per pillar post).
- **H3:** Sub-sections (2-4 per H2 where needed).
- **H4:** Use sparingly for deep lists or technical breakdowns.

### Paragraph & Tone
- **Length:** 2-4 sentences max per paragraph.
- **Tone:** Professional yet conversational. Avoid "In conclusion," "Furthermore," or "Moreover."
- **Formatting:** Use bullet points and bold text for readability.

---

## PRE-PUBLISHING CHECKLIST

Before ANY content is pushed to WordPress, verify:
- [ ] H1 is distinct from Title Tag.
- [ ] NO em-dashes or en-dashes.
- [ ] NO repeated words ("The the", "SEO SEO").
- [ ] NO `<h1>` tags in body content.
- [ ] Internal links: 5-20 verified URLs from sitemap.
- [ ] Images: All have descriptive Alt text (no generic placeholders).
- [ ] Yoast/RankMath: Focus keyphrase is set and SEO analysis is green.

---

## AGENT COMPLIANCE

**ALL agents (Main, Nemo, Chronos) MUST:**
1. Reference these rules before starting any content task.
2. Execute a mandatory self-audit using `self-audit-protocol.md`.
3. Reject any draft that contains long dashes or repetitive phrasing.

**Related Documentation:**
- `agents/MASTER-INDEX.md` : Team overview.
- `rules/content/self-audit-protocol.md` : Verification framework.
- `rules/content/semantic-seo-writer.md` : SEO framework.

---

**Last Updated:** 2026-05-02 
**Version:** 1.1 
**Status:** ACTIVE
