# Ai Brain

Read `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/INDEX.md` first. Load only the files your task needs.

## Quick Reference

- **Credentials:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/master-env.env`
- **System data (OAuth, tokens, backups):** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/` ← canonical, never store outside
- **Content rules:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/content-rules.md`
- **SEO method:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/semantic-seo-writer.md`
- **Master prompt:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/MASTER-SYSTEM-BOOTSTRAP.md`
- **Skills catalog:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/skills/_CATALOG_MAP.md`
- **Projects:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/{name}/mastersheet.md`

## Hard Rules

- This file (Ai Brain) is the headquarters. All persistent data — credentials, tokens, backups, configs, plans, skills — MUST live here. `~/.hermes/` is only for agent runtime symlinks and session state.
- Always read `INDEX.md` first to locate files before creating anything new. Update `INDEX.md` when you add new sections or files.
- **Workspace Isolation:** Every active project related file goes in root `projects/` under the correct project folder. Every GMB cold outreach landing page goes in root `websites/` under the correct staging folder. Do not keep project/website files inside agent workspaces. Redundant `[agent]/projects` or `[agent]/websites` folders are strictly prohibited.
- **Skills progression:** All shared playbooks and tools live canonically in root `skills/`. Load relevant skills based on `INDEX.md` and upgrade them over time with use and user guidance.
- **Index/Graph Exclusions:** Always ignore virtual environments (`.venv`), third-party repositories (`applications/`), and git logs (`.git/`) in Obsidian settings and Graphify runs to prevent performance lag and cognitive graph pollution.
- Do not bulk-load the repository. Pick only the files needed.
- Do not hardcode credentials. Read `master-env.env`.
- Do not publish. Push as DRAFT only.
- Do not add H1 tags in content body. WordPress title is the only H1.
- **Synchronization**: After completing any task, update the relevant project's `mastersheet.md` and log to the shared daily `memory/YYYY-MM-DD.md` to ensure state alignment across all agents.
