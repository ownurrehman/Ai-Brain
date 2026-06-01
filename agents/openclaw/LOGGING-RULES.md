# Logging Rules — MANDATORY for All Agents

## The Law

**Every agent MUST write to the Ai Brain vault after EVERY meaningful step.**

- No exceptions. No "I'll log it later." No "This step is too small."
- If you made a decision, changed a file, found information, or completed a sub-task → LOG IT.
- The vault is the ONLY source of truth. Session memory is unreliable.

## What to Log

### Before Starting
1. Read INDEX.md
2. Read MEMORY.md
3. Read relevant mastersheet.md
4. Write: "Starting task X. Reading context..."

### During Task (After Every Step)
- What you did
- What the result was
- What file you changed (if any)
- Any blockers or decisions

### After Completing
1. Update mastersheet.md with results
2. Update MEMORY.md with key learnings
3. Write "Task complete. Results: ..."
4. Report done to user

## How to Log

### Quick Log (via script)
```bash
# Set agent name
export AGENT_NAME="enigma"

# Log a step
~/Ai\ Works\ -\ Local/Ai\ Codes/Ai\ Brain/scripts/log-step.sh \
  "Step Name" \
  "What you did" \
  "Result" \
  "File changed"
```

### Direct Write (via obsidian-cli)
```bash
obsidian-cli create "memory/2026-05-14" --content "## Step: Name
- Action: ...
- Result: ...
- File: ..."
```

### Template for New Sessions
Copy from: `templates/session-log.md`

## Examples

### Good Log
```markdown
### [14:32] SEO Audit — RankRay
- **Action:** Audited 53 pages for meta descriptions
- **Result:** Found 12 missing, 8 too long, 3 duplicates
- **File:** projects/rankray-hq/mastersheet.md (updated)
```

### Bad Log
```markdown
Did some SEO stuff. All good.
```

## Enforcement

- Every agent's SOUL.md contains the Obsidian Persistence Protocol
- AGENTS.md mandates logging in the Communication Hygiene section
- Failure to log = misalignment. Fix immediately.

## Directory

- Daily logs: `memory/YYYY-MM-DD.md`
- Project logs: `projects/{site}/mastersheet.md`
- Agent logs: `{agent}/activity-log.md`
- Templates: `templates/session-log.md`
