> **Parent Report Hub:** [[system/reports/INDEX|📊 System Reports Archive]] · [[INDEX|🧠 Ai Brain]]

# Token Optimization Strategies — Daily Report
**Date:** February 18, 2026  
**Agent:** SEO Operations Agent (Main Session)  
**Task ID:** d864c0db-c757-4dc1-b024-e77841c154f8

---

## Overview
Token costs are a primary operating expense for AI-powered workflows. This report documents 3 actionable, evidence-based strategies to reduce token consumption without sacrificing output quality.

---

## Tip 1: Compress Context with Structured Summaries

**Problem:** Full conversation history rapidly consumes context window. Re-sending entire threads on every request is wasteful.

**Strategy:** Implement a rolling summary system. Instead of passing N messages, pass:
- A condensed 2-3 sentence summary of prior context
- Only the last 1-2 messages in full

**Impact:** Reduces token count by 40-60% for long-running sessions.

**Implementation:**
```
[SYSTEM: Prior context summarized as: "User requested site audit; we discussed 
homepage META issues."] + [last user message]
```

---

## Tip 2: Right-Size Model Selection

**Problem:** Defaulting to the largest model (e.g., GPT-4) for all tasks is expensive overkill.

**Strategy:** Route tasks to appropriate model tiers:
- **Small/Cheap Models:** Classification, entity extraction, formatting, simple Q&A
- **Medium Models:** Content summarization, standard analysis, structured output
- **Large Models:** Complex reasoning, creative writing, nuanced analysis requiring deep context

**Impact:** Can reduce costs by 5-10x on tasks suited to smaller models.

**Heuristic:** If the task feels like "pattern matching," use a smaller model. If it requires "novel reasoning," use a larger one.

---

## Tip 3: Use Prompt Caching / Persistent Context Where Available

**Problem:** Static system prompts and repeated instructions add fixed overhead to every request.

**Strategy:** Leverage API-level optimizations:
- **Anthropic:** Use the `cache_control` feature to mark reusable blocks
- **OpenAI:** Use `message` arrays with cached prefix content
- **Self-managed:** Warm up context once, reference by ID rather than re-sending

**Impact:** Cached tokens are ~50% cheaper. For daily workflows with fixed system prompts, this adds up fast.

**Quick Win:** Extract your standard system prompt into a cached block. Reference workflow-specific instructions separately.

---

## Summary

| Strategy | Effort | Savings |
|----------|--------|---------|
| Structured summaries | Low | 40-60% |
| Right-sizing models | Low (once configured) | 5-10x on eligible tasks |
| Prompt caching | Medium (depends on provider) | ~50% on cached blocks |

---

## Sources & References
- OpenAI API documentation: prompt caching best practices
- Anthropic documentation: `cache_control` parameter
- Internal observation: AGENTS.md protocol already prefers summaries over dumps

---

**Next Review:** February 25, 2026 (weekly rotation)
