# Obsidian + OpenClaw + Hermes Integration Guide

## What This Is

Your Obsidian vault (`Ai Brain`) is now the persistent memory layer for all AI agents. Every agent reads from and writes to this vault. No more scattered files, no more "where did I save that?"

## Vault Structure

```
Ai Brain/
├── INDEX.md                    ← Master navigation (read first)
├── README.md
├── docs/ENV.md                 ← Credential map (names + paths)
├── master-env.env              ← Secrets (never share / never commit)
├── mastersheet.md              ← Cross-site SEO audit rotation
│
├── .obsidian/                  ← Obsidian config
│
├── agents/                     ← Per-agent memory
│   ├── hermes/
│   ├── chronos/
│   ├── emilia/
│   ├── nemo/
│   └── antigravity/
│
├── websites/                   ← Client + marketing SITES
│   ├── rankray.com/            ← Agency website (not HQ)
│   ├── tonicphysio.com/
│   ├── coinsfera.com/
│   ├── teammotorcycle.com/
│   ├── backlinkcrypto.com/
│   ├── outreach/
│   └── archive/                ← khanllp.com
│
├── projects/                   ← Internal apps
│   ├── rankray-hq/             ← RankRay HQ SaaS (not rankray.com)
│   ├── rankray-plugins/
│   ├── legendary-bot/
│   └── archives/
│
├── credentials/                ← google-oauth/ + websites/  (gitignored)
├── rules/
├── skills/
├── prompts/
├── scripts/
└── memory/                     ← Daily logs
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