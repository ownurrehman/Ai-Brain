# AGENTS.md

## Cross-repo work (read this first)

If the task touches **RankRay-HQ**, **SEO Engine AI**, **WordPress plugins**, or another folder under **AI Codes**, follow the workspace **Universal agent workflow** in **`../AGENTS.md`** (Mastersheet → AGENTS → skills → Antigravity → target `docs/` → execute → update Mastersheet + report).

---

## Session bootstrap (agents) — work inside **Ai-Brain** only

**Start of a new conversation or a new multi-step task confined to this repo:**

1. Open **`INDEX.md`** and read **only** the rows that match the job (routing beats loading everything).
2. If the task is **code / bugfix** and nothing in the table fits yet, open **`core/engineering-standards.md`**. If it is **research / strategy**, open **`core/strategic-research.md`**.
3. If first-party **`skills/*`** does not cover the task, use **Antigravity**: run **`python3 scripts/find_antigravity_skill.py <keywords>`** (from repo root), or search **`antigravity-awesome-skills/skills_index.json`** / **`antigravity-awesome-skills/CATALOG.md`**, then open the matching **`SKILL.md`**. Read **`skills/README.md`**, **`SKILLS.md`**, and **`ANTIGRAVITY.md`** (do not bulk-copy upstream into `skills/`). **Custom `skills/` always wins** when both apply.

Details: **`ANTIGRAVITY.md`**.

---

## What this repo is

Shared **markdown-first** agent knowledge and a thin **Python runtime** that loads agent folders into a single model payload.

**Antigravity catalog:** [Antigravity Awesome Skills](https://github.com/sickn33/antigravity-awesome-skills) lives as a **git submodule** at **`antigravity-awesome-skills/`** (next to **`skills/`**). See **`ANTIGRAVITY.md`** and **`SKILLS.md`**.

| Area | Role |
|------|------|
| `ai-brain/` | Human-edited rules, skills, patterns, memory (not tied to one runtime) |
| `ai_brain/` | Package: `python3 -m ai_brain` loads agents from `ai_brain/agents/<name>/` |

## Agent folder convention

Top-level `.md` files in an agent directory load in a fixed order (`identity.md`, `instructions.md`, `tasks.md`, … then alphabetical). See `README.md` for the full list.

## Commands (from repo root)

```bash
python3 -m ai_brain --list-agents
python3 -m ai_brain --agent researcher
python3 -m ai_brain --agent-path ai_brain/agents/coder
python3 -m ai_brain --agent strategist --show-files
```

## Where to read first

- `README.md` — architecture and how to add agents
- `INDEX.md` — routing to the smallest useful subtree

## Rules for changes

- Prefer new behavior as **markdown** before adding code or structured config.
- Keep `ai-brain/` as a **single source of truth**; avoid duplicating the same guidance across many files.

## Workspace coordination

- Parent folder **`../AGENTS.md`** and **`../Mastersheet.md`** describe how this repo fits with **RankRay-HQ**, **SEO Engine AI**, **Rank Ray Plugins**, and **WP Markdown for AI**. After large skill or agent changes that affect cross-repo workflows, add a short note there under **Recent Updates**.
