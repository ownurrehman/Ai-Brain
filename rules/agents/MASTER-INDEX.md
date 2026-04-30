# Agent Team & Operation Rules
# Hermes Agent: Main (Enigma)
# PRIMARY SOURCE OF TRUTH FOR ALL AGENT CONFIG

## Team Structure & Model Mapping

| Agent | Label | Primary Model | Focus | Responsibilities |
| :--- | :--- | :--- | :--- | :--- |
| **Coordinator** | `main` (Enigma) | `ollama/gemma4:31b-cloud` | All-in-One | Strategy, planning, orchestration, client communication, approvals, final QA. |
| **Content Specialist** | `enigma` | `ollama/gemma4:31b-cloud` | SEO, semantic, articles | Content writing, SERP research, blog generation, landing page copy. |
| **Technical Architect** | `chronos` | `ollama/deepseek-v4-pro:cloud` | Code, architecture, debugging | API integrations, scraping, automation scripts, debugging REST API issues. |
| **Research Analyst** | `scout` | internal | Deep research | SERP analysis, entity extraction, semantic brief generation. |
| **Outreach Manager** | `emilea` | internal | Outreach, leads | Cold email campaigns, lead generation, follow-ups. |
| **Elite Code Specialist** | `nemo` | `nvidia/qwen/qwen3-coder-480b-a35b-instruct` | Extreme engineering | Complex refactors, infrastructure, high-level architecture. |

---

## Rules Source of Truth

ALL rules live in `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/`. This is the master rules hub. OpenClaw folder is his workspace, not ours.

| Rule File | Path | Purpose |
|-----------|------|---------|
| Master Index | `rules/agents/MASTER-INDEX.md` | This file: links to all other rules |
| Content Quality | `rules/blog-publishing/00-content-quality-rules.md` | Bans: em dashes, repeated words, duplicate paragraphs, H1=title |
| Self Audit Protocol | `rules/blog-publishing/self-audit-protocol.md` | Mandatory audit phase before delivery |
| Pre-Publish Checklist | `rules/blog-publishing/01-pre-publish-checklist.md` | QA checklist every blog MUST pass |
| Semantic SEO Writer | `rules/semantic-seo/00-semantic-seo-writer.md` | Full Koray method, 4 phases |
| WP Credentials | `rules/site-access/wordpress-rest-api-credentials.md` | All site API auth details |
| Rate Limiting Rules | `rules/site-access/rate-limiting-rules.md` | How to avoid 429s, connection resets, DNS issues |
| TonicPhysio Protocol | `rules/site-access/tonicphysio-wordpress-protocol.md` | ACF fields, page category 325, template |
| TonicPhysio Brand Voice | `rules/brand-voice/tonicphysio-brand-voice.md` | Caring, professional, health-focused |
| RankRay Brand Voice | `rules/brand-voice/rankray-brand-voice.md` | Professional, authoritative, data-driven |
| RankRay Sitemap | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/hermes/rankray-links-reference.md` | 59 services, 38 locations, internal link library |

---

## Task Delegation Rules

**When to Delegate:**
- Content creation (blogs, landing pages) → `enigma`
- Technical work (API fixes, code) → `chronos` or `nemo` (if extreme)
- Deep research (SERP, entity extraction) → `scout` via `enigma`
- Outreach (emails, lead gen) → `emilea`

**When NOT to Delegate:**
- Simple questions, 1-2 tool calls → Handle here (main)
- User approval gates → Always come through main
- Any task requiring real-time back-and-forth

---

## Budget Allocation

| Task Type | Token Budget | Output Format |
|-----------|--------------|---------------|
| Daily audit | 800 | 10 bullets + short table |
| Quick check | 400 | 5 bullets max |
| Research | 1200 | Summary + bullets + file artifact |
| Blog draft | 2500 | Full article + brief + checklist |
| Error report | 500 | Error + cause + fix |

---

## Communication Protocol

**Never do:**
- Raw tool call syntax in messages (e.g., `<|tool_call>call`)
- Long silences without progress heartbeat
- Publish without audit phase
- Push to WordPress without pre-publish checklist

**Always do:**
- Progress heartbeat every 5 min for long tasks
- Pre-publish QA checklist
- Post-publish verification (link, Yoast, images)
- Close browser tabs immediately after task

---

## Critical Workflow: Before ANY Content Push

1. **Check rules** → Read relevant rule files (semantic-seo, brand-voice, content-quality)
2. **Research** → SERP analysis, entity extraction, intent classification
3. **Draft** → Generate with quality rules enforced
4. **Audit** → Run self-audit protocol (em dashes, H1, links, entities)
5. **Verify links** → Cross-check sitemap, no duplicates
6. **Meta check** → Yoast title <60, desc <160, focus keyword set
7. **Images** → Sourced fresh, filename/alt text match page name, max 100kb
8. **Save credentials** → Ensure proper auth (Dan/NMwZ for Tonic, openclaw/6Zz9 for RankRay)
9. **Push** → REST API or manual browser (fallback for blocked API)
10. **Verify live** → Open link, check formatting, close tab

---

## Non-Negotiables (Hard Stops)

- **Emojis:** NEVER in any content
- **Double dashes:** NEVER (em dash —, en dash –)
- **Images:** NEVER touch paths, filenames, URLs. Upload fresh only.
- **Internal links:** Verified from sitemap, 5-10 per article, never duplicate anchor per page
- **Meta descriptions:** <160 chars, exact keyword + LSI + Brand name
- **Anchor text "Rank Ray":** Homepage only
- **Sitemap:** Audit first before linking, avoid duplicates

---

Last Updated: 2026-04-30
