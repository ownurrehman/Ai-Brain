# Obsidian + OpenClaw + Hermes Integration Guide

## What This Is

Your Obsidian vault (`Ai Brain`) is now the persistent memory layer for all AI agents. Every agent reads from and writes to this vault. No more scattered files, no more "where did I save that?"

## Vault Structure

```
Ai Brain/
├── INDEX.md                    ← Master navigation (read first)
├── README.md                   ← Overview + quick start
├── master-env.env              ← ALL credentials (never share)
├── MEMORY.md                   ← Curated long-term memory
│
├── .obsidian/                  ← Obsidian config (plugins, themes)
│   ├── community-plugins.json  ← Active plugins
│   ├── plugins/                ← Plugin code
│   └── workspace.json          ← UI state
│
├── projects/                   ← Client sites
│   ├── rankray/
│   ├── teammotorcycle/
│   ├── tonicphysio/
│   ├── coinsfera/
│   └── khanllp/
│
├── rules/                      ← Content quality rules
│   ├── content-rules.md
│   ├── rate-limiting.md
│   └── rankray-location-pages.md
│
├── prompts/                    ← Agent prompts
│   ├── onboarding-flow-prompt.md
│   ├── landing-page-creation-prompt.md
│   └── product-page-creation-prompt.md
│
├── skills/                     ← Skill library
│   └── _CATALOG_MAP.md         ← Skill index
│
├── system/                     ← Canonical config, backups
│   ├── credentials/            ← OAuth tokens, API keys
│   └── config/               ← Agent configs
│
├── memory/                     ← Daily logs (YYYY-MM-DD.md)
│   ├── 2026-05-14.md
│   └── ...
│
├── openclaw/                   ← OpenClaw workspace
│   ├── AGENTS.md             ← Agent definitions
│   ├── SOUL.md               ← Persona
│   ├── IDENTITY.md           ← Who am I
│   ├── USER.md               ← User preferences
│   └── TOOLS.md              ← Tool paths
│
└── hermes/                     ← Hermes workspace
    └── ...
```

## How Agents Use It

### Read
- `obsidian-cli print "INDEX"` → Read navigation
- `obsidian-cli print "rankray/mastersheet"` → Read project state
- `obsidian-cli search-content "meta description"` → Search all notes

### Write
- `obsidian-cli create "memory/2026-05-14" --content "..."` → Daily log
- Direct file edits via `write()`/`edit()` → Update any .md file
- `obsidian-cli move "old-name" "new-name"` → Refactor with link updates

### Sync
- `obsidian-git` plugin auto-commits changes
- Git history 