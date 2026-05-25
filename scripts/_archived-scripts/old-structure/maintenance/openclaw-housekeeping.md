Summary: Guidelines and automated workflow for keeping the openclaw workspace organized and free of clutter.

# OpenClaw Housekeeping Workflow

The `openclaw/` directory is an active workspace for autonomous agents. Over time, it naturally accumulates loose scripts, temporary text files, ad-hoc reports, and screenshots. 

To maintain context efficiency and prevent clutter from interfering with agent reasoning, follow this housekeeping workflow.

## 1. Root Directory Integrity

The root directory (`openclaw/`) is strictly reserved for:

- **Core memory and identity files**: `AGENTS.md`, `MEMORY.md`, `SOUL.md`, `IDENTITY.md`, `USER.md`, `TOOLS.md`, `DREAMS.md`, `HEARTBEAT.md`, `self-audit-protocol.md`, `WORKSPACE-README.md`.
- **Core subdirectories**: `agents/`, `system/`, `skills/`, `projects/`, `reports/`, `state/`, `logs/`, `knowledge/`.
- **Environment and hidden files**: `.venv`, `.browser-profiles`, `.clawhub`, etc.

**Never store ad-hoc scripts, reports, or data files in the root directory.**

## 2. File Routing Rules

When generating or saving new files, agents should target these directories:

- **Scripts (`*.py`, `*.js`, `*.sh`)**: 
  - Node/Playwright scripts go to `headless-browser-scripts/`.
  - Python utilities go to `system/scripts/`.
- **Reports (`*report*.md`, `*report*.txt`)**: Move to `reports/`.
- **Temporary/State files (`temp_*`, `*.png`, `*.json`)**: Move to `state/temp/` (for ephemeral files) or `logs/` (for error screenshots/logs).
- **Ad-hoc Markdown (`*.md`)**: If not a core system file, it must be filed under `knowledge/` (for research and collections) or `projects/` (for active project specs).

## 3. Automated Cleanup

A Python script is provided to enforce these rules and sweep the root directory.

Run the following command from the `openclaw/` directory periodically (or ask the agent to run it when the root looks cluttered):

```bash
python3 system/housekeeping.py
```

This script will automatically sort files into their correct subdirectories without touching core configuration files.
