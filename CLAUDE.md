# Ai Brain — Project Guide (CLAUDE.md)

## 🎯 Project Mission

The central cognitive hub for all agentic workflows. Houses **skills**, **patterns**, **memory**, and **operational guidelines** that teach AI agents how to build and maintain the Rank Ray ecosystem.

## 📁 Repository Structure

- **skills/**: Atomic playbooks for specialized tasks (AI, SEO, Dev, etc.).
- **agents/**: Python-based agent loaders and coordination logic.
- **patterns/**: Reusable engineering and architectural templates.
- **memory/**: Long-term storage for project decisions and context.
- **INDEX.md**: The entry point for any new AI session.

## 🛠 Tech Stack

- **Core:** Markdown (Documentation-driven development).
- **Logic:** Python 3 (Agent orchestration).
- **Skill Format:** `SKILL.md` with YAML frontmatter.

## ⚙️ Engineering Rules

- **Skill First:** New patterns or reusable logic MUST be added as a skill in `skills/`.
- **Minimalism:** Keep documentation sharp and actionable. Avoid fluff.
- **Hierarchy:** First-party skills in `skills/` take precedence over external playbooks.

## 📜 Commands

- **Load Agents:** `python3 -m ai_brain.agents.loader` (if configured)
- **Validate Skills:** `npm test` or specialized python scripts in `tests/`.

## 📖 Agent Workflow

1. **New Session:** Read `INDEX.md`.
2. **Execute Task:** Check if a relevant skill exists in `skills/`.
3. **Contribute:** Update `Memory` or `Skills` after significant project milestones.
