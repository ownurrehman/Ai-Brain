> **Parent Hub:** [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]] · [[scripts/INDEX|🛠️ Scripts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

Summary: Rules Opus 4.7 follows so one 5-hour bundle ships maximum output.

# Token Budget Protocol (Opus 4.7)

Opus 4.7 has a hard 5-hour token cap. Every token must buy code, not chatter.

## The five laws

1. **No exploration.** If context is missing, stop and request a Gemini handoff (see [model-routing.md](model-routing.md)). Do not `ls`, `grep`, or speculatively `Read` to "understand."
2. **No restating plans.** If a plan arrived in the prompt, execute it. Do not paraphrase it back.
3. **No narration.** No "Let me start by…", no "I'll check X then Y", no summary paragraphs at the end. State intent in one sentence, act, confirm in one sentence.
4. **Parallelize tool calls.** Independent reads/edits in a single turn, never sequential.
5. **Edit, don't rewrite.** Prefer `Edit` over `Write`. Prefer a targeted patch over a full-file replace.

## Context loading discipline

- Load **only** files being edited this turn.
- `CLAUDE.md` + memory + one vault file is the ceiling for orientation context.
- If a file is >400 lines and only a region is relevant, use `Read` with `offset`/`limit`.
- Never `cat` a binary dump or large log into context — pipe through `head`/`wc` via Bash first.

## Session start checklist (Opus only)

1. Read the project folder in `Ai Brain/projects/<name>/` for current state.
2. Read `todo.md` / `rebirth-tracker.md` only if mentioned.
3. Do NOT read legacy plugins, `.bak` files, or archives unless the task names them.
4. Ask: "Is this a build task with a clear spec?" If no → escalate to Antigravity/Gemini for planning.

## End-of-turn discipline

- No trailing summary.
- No "here's what I changed" bullet list unless the user explicitly asked.
- One-line confirmation + file path(s). That's it.

## Red flags (stop and escalate)

Stop and request a Gemini/Antigravity pass if any of these appear:

- Task requires reading >3 files just to understand scope.
- Spec says "figure out how X works and then fix it."
- User asks for a plan, research, or folder audit.
- Bug reproduction requires multi-step investigation.
- Refactor spans >10 files with uncertain boundaries.

## What Opus IS for

- Final typed implementation of a specified feature.
- High-density React/Tailwind UI with token-driven styling.
- NestJS service / controller / Prisma query writing with full types.
- The last mile of a bug — the one where Gemini already isolated the offending lines.
- Architectural decisions that will ship to `main`.

## What Opus is NOT for

- Reading the codebase to get oriented.
- Writing `.md` planning docs.
- Renaming files / moving folders.
- Generating placeholder data.
- Formatting, linting, or style cleanup.
- Reviewing code (use Sonnet agents).
