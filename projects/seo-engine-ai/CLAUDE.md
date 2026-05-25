# SEO Engine Ai — AI Router

Opus 4.7 runs on a tight 5-hour bundle. Every token buys code, not chatter.

## Read order (session start)

1. `Ai Brain/core/model-routing.md` — who runs this task (Opus vs Gemini Flash).
2. `Ai Brain/core/token-budget-protocol.md` — Opus discipline.
3. `Ai Brain/projects/seo-engine-ai/` — current state, plans, decisions (authoritative).
4. `.claude/rules/protocol.md` — two-agent workflow.

Stop loading once the next action is clear.

## Dual-AI split (hard rule)

| AI | Role |
| :--- | :--- |
| **Antigravity / Gemini Flash** | Planner, folder structurer, doc writer, routine fixer, codemap, migration drafts, mock data |
| **Claude Opus 4.7** | Precision builder only — typed SEO logic, bulk generator algorithms, final architectural edits |
| Sonnet agents | Code review, exploration, security scan |

Full task → model map: `Ai Brain/core/model-routing.md`.

## Opus handoff envelope

Antigravity must deliver Opus a spec shaped like:

```
## Task — one sentence
## Files to touch — path:lines — change
## Context (facts, no prose)
## Success criteria
## Out of scope
```

## Stack

- Core: PHP / WordPress
- Data: WP Meta / ACF integrations
- Branch: `dev` (integration), `main` (baseline)

## Authoritative sources

- Mastersheet: `Ai Brain/Mastersheet.md`
- Technical Guide: `Ai Brain/projects/seo-engine-ai/TESTING-GUIDE.md`
- Changelog: `Ai Brain/projects/seo-engine-ai/CHANGELOG.md`

## Commands

- `composer install` — dependency management
- `./build.sh` — packaging ritual
- `phpunit` — test suite

**Operational lock:** Follow the vault documentation. No deviation without explicit user approval.
