# MEMORY.md (Curated, Long-Term)

## Core
- **Owner:** Own-ur-Rehman Sheikh (Rank Ray CEO)
- **Primary Agent:** `antigravity` (Architectural Development, Refactoring, and Automation)
- **Swarm Collaborators:** `openclaw` (SEO, Lead Gen, Writing), `hermes` (Content Publishing)

---

## Non-Negotiables
- **INDEX Protocol:** Read `INDEX.md` first before any file reads or directory writes.
- **Project Structure:** All project files live strictly in root `projects/[site_folder]/`. Duplicates in agent workspaces are strictly prohibited.
- **Antigravity Workspace:** Isolated strictly to `/antigravity/`.
- **Indexing Rules:** Always exclude virtual environments (`.venv`), third-party source clones (`applications/`), and metadata from Obsidian and Graphify scopes.

---

## Workspace Paths
- **Agent Root:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/antigravity/`
- **Shared Projects:** `projects/`
- **Shared Skills:** `skills/`
- **Shared Rules:** `rules/`
- **Shared Memory:** `memory/`

---

## Logged Milestones

### 2026-05-25 — Shared Brain Structural Refactoring & Optimization
- **Goal:** Conduct a full structural audit of `Ai Brain` and rebuild the directory structure to prevent agent errors, clean Obsidian, and purge Graphify pollution.
- **Status:** SUCCESS
- **Actions Taken:**
  1. Updated `.obsidian/app.json` to user-exclude `.venv`, `applications/`, `.git`, `.claude`, `.gemini`, and `graphify-out` from Obsidian scanning.
  2. Consolidated `openclaw/projects/rankray/mastersheet.md` and `openclaw/projects/tonicphysio/mastersheet.md` into their canonical root counterparts `/projects/rankray/mastersheet.md` and `/projects/tonicphysio/mastersheet.md`.
  3. Relocated audits (`tech-audit-2026-05-25.md` and `tech-audit-2026-05-24.md`) from agent subfolders to root `/projects/` folders.
  4. Moved active client project `openclaw/workspace/al-mazrouei-landing` to root `/projects/al-mazrouei-landing/` and created its canonical mastersheet.
  5. Centered scattered project-specific rules, deleting redundant `openclaw/rules/` and `openclaw/projects/`.
  6. Consolidated chronological daily logs (`2026-05-24.md` and `2026-05-25.md`) out of `/openclaw/memory/` into the shared `/memory/` folder.
  7. Created the `/antigravity/` workspace directory with canonical `IDENTITY.md`, `SOUL.md`, and `MEMORY.md`.
- **Result:** Complete structural alignment across the vault, eliminating all duplication, resolving conflicting master sheets, and speeding up Obsidian/Graphify indexing by removing thousands of dependency files.
