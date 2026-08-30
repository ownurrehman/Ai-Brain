> **Parent Hub:** [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]] · [[scripts/INDEX|🛠️ Scripts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

Summary: Explains the three-layer agent context architecture used across this workspace so any AI platform stays synchronized.

# Cross-Platform Agent Sync Architecture

## Problem

Multiple AI coding platforms (Cursor, Codex, Claude Code, Gemini, Windsurf, Copilot, Kimi, etc.) work on this workspace. Each reads different config files. Without a sync strategy, agent context fragments and drifts.

## Solution: Three Layers

### Layer 1 — `AGENTS.md` (Universal)

Every major AI coding platform now reads `AGENTS.md` natively. It is the industry standard.

- **Root `AGENTS.md`**: workspace map, global rules, project table.
- **Per-repo `AGENTS.md`**: project-specific commands, caveats, architecture.
- **`Mastersheet.md`**: cross-project changelog and status.

This is the single source of truth for **project facts**.

### Layer 2 — `Ai-Brain/` (Shared Knowledge)

Reusable skills, patterns, memory, and agent behavior that applies **across all projects**.

- Entry: `Ai-Brain/INDEX.md` — routing table for selective loading.
- `core/rules.md` — baseline operating rules.
- `skills/` — domain-specific skills (SEO, debugging, SaaS, etc.).
- `patterns/` — architecture, backend, UI patterns.
- `memory/` — lessons learned, past wins.

This is the single source of truth for **how agents should think and work**.

### Layer 3 — Platform Shims (Thin Pointers)

Each platform has its own config file. These are **thin redirects** (10-15 lines) that say:

> "Read `AGENTS.md` → then check `Ai-Brain/INDEX.md`."

| Platform | Shim file | Notes |
|:---|:---|:---|
| Cursor | `.cursorrules` + `.cursor/rules/*.mdc` | MDC rules for Cursor-specific precedence |
| Claude Code | `CLAUDE.md` | Read by Claude Code standalone |
| OpenAI Codex | `codex.md` | Read by Codex CLI agents |
| Windsurf | `.windsurfrules` | Read by Windsurf/Codeium |
| Copilot | `.github/copilot-instructions.md` | Read by GitHub Copilot |
| Gemini | Configured externally (`~/.gemini/`) | Antigravity reads AGENTS.md via user rules |
| Kimi / Others | Follows markdown in workspace | AGENTS.md works universally |

## Key Rules

1. **Never duplicate project facts in shims.** All real content lives in `AGENTS.md` and `Ai-Brain/`.
2. **When you update `AGENTS.md`, every platform benefits.** No shim needs to change.
3. **Shims only change if the platform changes how it reads config.**
4. **Closest `AGENTS.md` wins.** When editing `RankRay-HQ/`, that folder's `AGENTS.md` takes priority.
5. **Skills are portable markdown.** Any platform can read `Ai-Brain/skills/seo/SKILL.md`.

## Maintenance

- After material changes to any project: update `Mastersheet.md` and relevant `AGENTS.md`.
- After changing the sync architecture itself: update this file and `Ai-Brain/INDEX.md`.
- Shims should be reviewed quarterly — platforms may add new config conventions.
