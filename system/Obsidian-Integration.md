# Obsidian + OpenClaw + Hermes Integration Guide

## What This Is

Your Obsidian vault (`Ai Brain`) is now the persistent memory layer for all AI agents. Every agent reads from and writes to this vault. No more scattered files, no more "where did I save that?"

## Vault Structure

```
Ai Brain/
├── INDEX.md                    ← Master navigation (read first)
├── README.md                   ← Overview + quick start
├── master-env.env              ← ALL credentials (never share)
│
├── .obsidian/                  ← Obsidian config (plugins, themes)
│
├── agents/                     ← Unified Swarm Workspaces
│   ├── openclaw/               ← OpenClaw workspace
│   │   ├── AGENTS.md           ← Swarm agent definitions
│   │   ├── SOUL.md             ← Swarm Persona
│   │   ├── IDENTITY.md         ← Swarm Identity
│   │   ├── USER.md             ← Swarm User profile
│   │   └── TOOLS.md            ← Swarm tools config
│   ├── hermes/                 ← Hermes workspace
│   └── antigravity/            ← Antigravity workspace
│
├── websites/                   ← Websites & Staging Layer (Stage 1 & 2)
│   ├── rankray.com/            ← Production agency site
│   ├── tonicphysio.com/        ← Tonic Physio clinic
│   ├── coinsfera.com/          ← CoinSfera OTC Exchange
│   ├── teammotorcycle.com/     ← Team Motorcycle
│   ├── backlinkscrypto.com/    ← Web3 startup backlink index
│   ├── outreach/               ← Prospecting targets (al-mazrouei-landing)
│   └── archive/                ← Archived websites (khanllp.com)
│
├── projects/                   ← Tooling & Active Internal Dev
│   ├── rank-ray-hq/            ← NestJS Monorepo Agency OS
│   └── legendary-bot/          ← Telegram auto-responder bot
│
├── rules/                      ← Content quality & platform rules
│   ├── content/
│   │   └── content-rules.md
│   ├── access/
│   │   └── wordpress-rest-api-setup.md
│   └── rate-limiting.md
│
├── templates/                  ← Unified templates folder
│   ├── memory-template.md      ← Memory log template
│   └── session-log.md          ← Session log template
│
├── scripts/                    ← Global Script Folder (System Utilities Only)
│   ├── google-oauth-manager.py ← PKCE Google OAuth Manager CLI
│   └── subagent-manager.py     ← Transient subagent delegates
│
├── prompts/                    ← Master prompts catalogs
│
├── skills/                     ← Upgraded deep playbooks
│   └── _CATALOG_MAP.md         ← Playbooks catalog map
│
└── memory/                     ← Daily logs (YYYY-MM-DD.md)
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