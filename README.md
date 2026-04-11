Summary: This repository is a shared, markdown-only knowledge base for agentic coding across multiple independent projects.

# AI Brain

This is the central "brain" for all agents working in this workspace. It provides a consistent set of rules, skills, and patterns to ensure high-quality, predictable engineering across **RankRay-HQ**, **SEO Engine AI**, and other linked repositories.

## Core Architecture

The brain is organized into four main areas:

1.  **[`core/`](core/)**: The fundamental operating system. Contains global rules, task routing logic, and cross-platform sync definitions.
2.  **[`skills/`](skills/)**: Domain-specific expertise. Each subdirectory is a "skill" containing a `SKILL.md` with instructions, workflows, and verification steps.
3.  **[`patterns/`](patterns/)**: Proven solutions for recurring problems. Covers backend architecture, UI/UX consistency, and SEO-specific logic.
4.  **[`memory/`](memory/)**: Durable context. Stores lessons learned from past wins and failures to avoid repeating mistakes.

5.  **[`antigravity-awesome-skills/`](antigravity-awesome-skills/)** (optional git submodule): **[Antigravity Awesome Skills](https://github.com/sickn33/antigravity-awesome-skills)** — 1,300+ extra `SKILL.md` playbooks, **sibling of `skills/`**. **First-party [`skills/`](skills/) wins** when both apply. Map: **[`SKILLS.md`](SKILLS.md)**; clone / pull / Cursor: **[`ANTIGRAVITY.md`](ANTIGRAVITY.md)**.

## Key Skills

The following skills are now the primary way to guide agent behavior:

- **[`coding-discipline`](skills/coding-discipline/SKILL.md)**: (New) Unified engineering standards, start-up checklists, tool usage, and verification rules.
- **[`research-methods`](skills/research-methods/SKILL.md)**: (New) Disciplined fact-gathering, source evaluation, and decision-useful summarizing.
- **[`strategic-planning`](skills/strategic-planning/SKILL.md)**: (New) Turning ambiguous goals into sequenced, actionable plans.
- **[`rankray-seo-ui`](skills/rankray-seo-ui/SKILL.md)**: Specific patterns for the RankRay product ecosystem.
- **[`seo`](skills/seo/SKILL.md)**: Intelligence patterns for search intent, clustering, and SERP systems.

## How to use the brain

1.  **Find the right skill**: Start with **[`INDEX.md`](INDEX.md)** to see the full list of available knowledge.
2.  **Load the markdown**: Every AI platform (Cursor, Claude, Codex, etc.) reads these files directly. Simply load the relevant `.md` file into your context.
3.  **Follow the instructions**: Each skill is designed to be operational. Use the checklists and rules to guide your work.

## Maintenance and Updates

- **Markdown Only**: This is a pure-text repository. Do not add executables, Python scripts, or structured config unless absolutely necessary.
- **Durable Knowledge**: Only add guidance that is worth keeping for the long term. One-off project notes belong in the specific project repository.
- **Update Process**: See **[`maintenance/brain-update-process.md`](maintenance/brain-update-process.md)** for how to expand the brain safely.

## Workspace Synchronization

This brain is the source of truth for the workspace. Platform-specific files (e.g., `.cursorrules`, `.windsurfrules`, `codex.md`) all point back to this repository. See **[`core/cross-platform.md`](core/cross-platform.md)** for details on how this sync is maintained.
