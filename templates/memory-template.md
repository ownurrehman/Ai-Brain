# Memory Entry Template
# Both Hermes and OpenClaw MUST use this format for all memory logs
# Copy this template, fill in, save to memory/YYYY-MM-DD.md

# Memory Log — YYYY-MM-DD

## Task: [Brief description]

**Agent:** [hermes | openclaw | main | enigma | chronos]  
**Project:** [rankray | tonicphysio | teammotorcycle | coinsfera | openclaw | etc.]  
**Duration:** [minutes or hours]  
**Tokens Used:** [if tracked]

### What Was Done

- [ ] Step 1: [Action]
- [ ] Step 2: [Action]
- [ ] Step 3: [Action]

### Key Decisions

1. [Decision and rationale]
2. [Decision and rationale]

### Files Modified

| File | Change |
|------|--------|
| `path/to/file` | [brief description] |

### Issues Found / Blockers

- [ ] [Issue and status]

### Verification

- [ ] [Check performed and result]

### Next Steps

1. [Next action for this project]
2. [Next action for this project]

---

## Quick State Update (If project state changed)

| Project | Status Change |
|---------|---------------|
| [project] | [new status] |

## Integration / Tool Updates

| Tool | Change |
|------|--------|
| [tool] | [added/removed/updated] |

## Rules Updated

| Rule | Change |
|------|--------|
| [rule file] | [what changed] |

---

# END TEMPLATE

## Cost Rules for Memory Entries

- **Max 500 words** for simple tasks
- **Max 1000 words** for complex multi-step tasks
- **Bullet points only** — no paragraphs unless explaining rationale
- **Always include:** agent name, project, files modified, next steps
- **Never include:** raw tool output, error dumps, full file contents
- **Link to reports** instead of pasting: `see reports/2026-05-15-audit.md`

## Pre-Task Check (Both Agents)

Before starting any task:

1. Read `memory/2026-05-DD.md` (today's file if exists)
2. Read `memory/2026-05-DD.md` (yesterday's file)
3. Read `projects/{project}/mastersheet.md`
4. If memory says task already done → verify, don't redo
5. If memory says blocker → check if resolved before continuing

## Post-Task Check (Both Agents)

After completing any task:

1. Write to `memory/2026-05-DD.md` using this template
2. Update `projects/{project}/mastersheet.md`
3. If code changed → run `graphify update .`
4. Only THEN report done to user

## Memory File Naming

- `memory/2026-05-15.md` — daily log (main events)
- `memory/2026-05-15-rankray-service-pages.md` — specific task log (if task is large)
- `memory/2026-05-15-cron-jobs.md` — cron-specific log
- Always link from daily log to specific task log
