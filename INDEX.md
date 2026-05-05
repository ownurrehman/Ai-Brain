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
| Site audit task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/site-audit-prompt.md` |
| Landing page task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/landing-page-creation-prompt.md` |
| Product page task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/product-page-creation-prompt.md` |
| Onboarding flow task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/onboarding-flow-prompt.md` |

## Credentials

All API keys and passwords: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/master-env.env`

## Agent Workspaces

Each agent has its own folder with identity, memory, and tools. The Ai Brain (rules, projects, skills, prompts) is the shared layer all agents read from.

| Agent | Workspace | What it does |
|-------|-----------|--------------|
| OpenClaw | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw/` | Main agent. SEO, content, automation, research. |
| Hermes | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/hermes/` | Content publishing. Blog strategies, published logs. |

To add a new agent: create its folder, add IDENTITY.md + MEMORY.md, register it here.

## Skills

Full catalog: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/skills/_CATALOG_MAP.md`
