Summary: Pick which AI runs a task before picking which skill. Opus 4.7 is scarce — protect it.

# Model Routing

User runs on Opus 4.7 with a hard 5-hour token bundle. Gemini Flash (inside Antigravity) is cheap and abundant. Route every task to the smallest capable model.

## Model tiers

| Tier | Model | Role | Use for |
| :--- | :--- | :--- | :--- |
| **Scarce** | **Claude Opus 4.7** | Precision Builder | Final code edits, typed business logic, high-density UI, tricky bug fixes where other models failed, architectural decisions that ship |
| **Cheap** | **Gemini Flash (Antigravity)** | Planner · Structurer · Fixer | Planning docs, folder structure audits, codemaps, routine fixes, rename/move refactors, doc updates, migration drafts, placeholder/mock data, scaffolding, bulk formatting |
| **Medium** | Claude Sonnet 4.6 | Mid-weight builder | Codebase exploration, code review, multi-file reads, intermediate feature work when Opus is overkill |
| **Trivial** | Claude Haiku 4.5 | Quick worker | Status checks, simple edits, string replacements, agent fan-out |

## Routing rules

1. **Default to Gemini Flash.** If a task can be done by a cheaper model without quality loss, it must be.
2. **Opus 4.7 only builds.** No exploration, no planning docs, no folder organization, no "figure out the architecture," no file-by-file reading to understand.
3. **Handoff before Opus fires.** Antigravity/Gemini produces the spec, paths, and diffs; Opus executes.
4. **If Opus starts exploring → stop and escalate to Gemini.** Any task that requires reading more than ~3 files to understand context is a planning task, not a build task.
5. **Reviews use Sonnet or Haiku agents**, never Opus, unless a CRITICAL security issue is suspected.

## Task → Model map

| Task | Model |
| :--- | :--- |
| New feature planning · PRD drafting | Gemini Flash |
| Folder reorganization · codemap generation | Gemini Flash |
| Bulk rename · path fixes · import drift | Gemini Flash |
| Doc updates · README · changelog | Gemini Flash |
| Prisma migration drafting | Gemini Flash → Opus final review |
| Scaffolding new modules (skeleton files) | Gemini Flash |
| Mock / placeholder data | Gemini Flash |
| Implementing typed business logic | **Opus 4.7** |
| High-density dashboard UI code | **Opus 4.7** |
| Bug fix where 2+ attempts have failed | **Opus 4.7** |
| Routine single-file bug fix | Gemini Flash |
| Code review · style audit | Sonnet agent |
| Security review | Sonnet agent (escalate to Opus if CRITICAL) |
| Codebase exploration · "how does X work" | Sonnet agent (Explore) |
| Status, ls, git status, simple greps | Haiku / direct tool |

## Opus handoff envelope (Antigravity → Claude)

Every Opus prompt should arrive in this shape. If it doesn't, Opus should ask for it instead of exploring.

```
## Task
<one sentence>

## Files to touch
- <relative/path.tsx>:<lines> — <what changes>
- <relative/path.ts>:<lines> — <what changes>

## Context (pre-loaded, no prose)
- <fact 1>
- <fact 2>

## Success criteria
- <verifiable check 1>
- <verifiable check 2>

## Out of scope
- <anything Opus should NOT touch>
```
