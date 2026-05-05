# Karpathy Skills Integration Plan — May 4, 2026

## Source
- Repo: https://github.com/forrestchang/andrej-karpathy-skills
- Original Karpathy post: https://x.com/karpathy/status/2015883857489522876
- Medium deep-dive: https://medium.com/data-science-in-your-pocket/andrej-karpathys-claude-code-skills-3db42cc634c8

## Executive Summary

Karpathy identified 4 critical failure modes in LLM coding agents. These map directly to problems we've experienced:
- Agents silently choosing wrong assumptions and running with them
- Overcomplicating simple tasks (1000 lines when 100 would do)
- Touching unrelated code/comments as "improvements"
- Not knowing when to stop and ask for clarification

## The 4 Principles

### 1. Think Before Coding
**Problem it solves:** Wrong assumptions, hidden confusion, missing tradeoffs  
**Our failures:** Multiple instances of agents guessing WP credentials, silently picking wrong channel IDs, assuming page structures  

**Implementation:**
- Before any implementation, agent MUST state assumptions explicitly
- If multiple interpretations exist, present them — don't pick silently
- Push back when a simpler approach exists
- When confused, STOP and ask — name what's unclear

### 2. Simplicity First
**Problem it solves:** Overcomplication, bloated abstractions, dead code  
**Our failures:** Overengineered sub-agent pipelines, parallel workflows that created complexity, bloated cron job prompts  

**Implementation:**
- No features beyond what was asked
- No abstractions for single-use code
- No "flexibility" that wasn't requested
- If 200 lines could be 50, rewrite it
- Test: "Would a senior engineer say this is overcomplicated?"

### 3. Surgical Changes
**Problem it solves:** Orthogonal edits, touching code you shouldn't  
**Our failures:** WordPress batches touching posts not in scope, cron delivery reroutes without verification  

**Implementation:**
- Don't "improve" adjacent code, comments, or formatting
- Don't refactor things that aren't broken
- Match existing style, even if you'd do it differently
- If you notice unrelated dead code, mention it — don't delete it
- Every changed line must trace directly to the user's request

### 4. Goal-Driven Execution
**Problem it solves:** Vague instructions → vague results  
**Our failures:** "Do SEO audit" without defined success criteria, "generate leads" without verification loops  

**Implementation:**
- Transform tasks: "Fix X" → "Write a test that reproduces X, then make it pass"
- Multi-step tasks: state plan with verify checkpoints
- Strong success criteria = autonomous looping. Weak criteria = constant clarification
- The agent should verify its own work before reporting done

## What We're Changing

### AGENTS.md Updates
1. Add Karpathy 4 Principles as mandatory behavioral rules
2. Add "Goal-Driven Execution" to all sub-agent spawn templates
3. Add verification checkpoints to cron job prompts

### Sub-Agent Prompt Template Changes
Before: "Execute Combined Daily SEO for site.com: 1. Audit, 2. Analysis, 3. Links, 4. Content"  
After: Same steps, but add verify checkpoints and success criteria:
```
1. Site Audit → verify: crawled all pages, found all errors, report has 8+ items
2. SERP Analysis → verify: checked top 5 competitors, found 3+ keyword gaps
3. Internal Linking → verify: fixed 3+ broken/opportunity links, no duplicates
4. Content → verify: meta descs <160 chars, KW in H1, word count target met
```

### SOUL.md / Identity Updates
- Add "Karpathy behavioral principles" as core operating rules
- Add "Verify before reporting done" mandate
- Add "Surface ambiguity, don't hide it" mandate

### Memory Updates
- Record the 4 principles as non-negotiable operational rules
- Add verification checklist to daily SEO report templates

## Agent-Specific Mapping

| Principle | Enigma (main) | Nemo (code) | Chronos (research) |
|-----------|--------------|-------------|-------------------|
| Think Before Coding | Surface SEO assumptions | State architectural assumptions | Present research methodology |
| Simplicity First | Simple audits, no fluff | Minimal code, no overengineering | Focused reports, no bloat |
| Surgical Changes | Target specific pages only | Touch only requested files | Scoped analysis only |
| Goal-Driven | Verifiable SEO outcomes | Test-driven development | Evidence-backed findings |

## Immediate Action Items

- [ ] Update AGENTS.md with Karpathy principles
- [ ] Update all 8 cron job prompts with verification checkpoints
- [ ] Update SOUL.md with behavioral principles
- [ ] Update MEMORY.md with new non-negotiables
- [ ] Create verification checklist template for sub-agent tasks
- [ ] Update self-audit protocol to include Karpathy principles
