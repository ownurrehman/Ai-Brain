# INDEX

Master navigation for the Ai Brain. Agents: read this first, load only what your task needs.

Base path: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`

## Structure

```
/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/
  INDEX.md            ← you are here
  master-env.env      ← ALL credentials (WP, Google, SEMrush, Pexels, etc.)

  projects/           ← each client site has a folder with mastersheet.md
  rules/              ← content quality rules, rate limiting, voice guides
  prompts/            ← master prompt + task templates
  skills/             ← skill library (see _CATALOG_MAP.md inside)
  system/             ← canonical config, credentials, backups (hard rule: all persistent data lives here)
    credentials/      ← OAuth tokens, API keys (never store outside Ai Brain)
    config/           ← agent configs, environment overrides
    backups/          ← automatic backups of critical data

  openclaw/           ← OpenClaw agent workspace (identity, memory, tools)
  hermes/             ← Hermes agent workspace (content publishing)
```

## Projects

| Site | Mastersheet |
|------|-------------|
| rankray.com | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rankray/mastersheet.md` |
| tonicphysio.com | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/tonicphysio/mastersheet.md` |
| khanllp.com | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/khanllp/mastersheet.md` |
| legendary-bot | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/legendary-bot/mastersheet.md` |

## Rules

| What | File |
|------|------|
| Content quality + pre-push checklist | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/content-rules.md` |
| SEO writing method (Koray) | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/semantic-seo-writer.md` |
| API rate limiting | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/rate-limiting.md` |
| Voice: TonicPhysio | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/voice/tonicphysio.md` |

## Prompts

| What | File |
|------|------|
| Master system prompt | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/MASTER-SYSTEM-BOOTSTRAP.md` |
| Google Workspace access (pre-configured auth) | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/config/google-workspace-agent-prompt.md` |
| Site audit task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/site-audit-prompt.md` |
| Landing page task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/landing-page-creation-prompt.md` |
| Product page task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/product-page-creation-prompt.md` |
| Onboarding flow task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/onboarding-flow-prompt.md` |

## Credentials

All API keys and passwords: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/master-env.env`

**HARD RULE:** All persistent system data (OAuth tokens, credentials, backups, configs) MUST be stored under `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/`. Never store canonical data outside Ai Brain. The `~/.hermes/` folder contains only symlinks and agent runtime settings. When creating new tokens, configs, or schedules, use the Ai Brain system tree as the canonical location.

| What | Ai Brain Canonical Path | Note |
|------|-------------------------|------|
| Google OAuth token | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/token.json` | oliverjakeseo@gmail.com, auto-refreshes |
| Google client secret | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/credentials/google-oauth/client_secret.json` | Desktop app client |
| Environment overrides | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/config/` | Per-agent env overrides |
| Backups | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/backups/` | Auto-backups of critical data |

## Agent Workspaces

Each agent has its own folder with identity, memory, and tools. The Ai Brain (rules, projects, skills, prompts) is the shared layer all agents read from.

| Agent | Workspace | What it does |
|-------|-----------|--------------|
| OpenClaw | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/` | Main agent. SEO, content, automation, research. |
| Hermes | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/hermes/` | Content publishing. Blog strategies, published logs. |

To add a new agent: create its folder, add IDENTITY.md + MEMORY.md, register it here.

## Skills

Full catalog: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/skills/_CATALOG_MAP.md`
