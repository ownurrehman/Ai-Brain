# INDEX

> **MANDATORY — READ THIS FIRST**
> This vault (`Ai Brain`) is your **only** persistent memory. Every thought, decision, log, and update MUST be written here via Obsidian. Never rely on session memory alone. If it's not in the vault, it doesn't exist.

Master navigation for the Ai Brain. Agents: read this first, load only what your task needs.

Base path: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`

## Structure

```
/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/
  INDEX.md            ← you are here (MANDATORY first read for all agents)
  master-env.env      ← ALL credentials (WP, Google, SEMrush, Pexels, etc.)

  websites/           ← GMB cold outreach & staging sites - Stage 1 (Shared)
    outreach/         ← prospecting sites staged for cold outreach
  projects/           ← active internal tooling & dev codebases (Shared)
  clients/            ← active + archived client sites - Stage 2 & 3 (Shared)
    archive/          ← archived/lost clients (mastersheet + assets)
  rules/              ← content quality rules, rate limiting, voice guides (Shared)
  prompts/            ← master prompt + task templates (Shared)
  skills/             ← skill library (Shared — see _CATALOG_MAP.md inside)
  memory/             ← Consolidated daily logs across all agents (Shared)
  templates/          ← reusable note/document templates (Shared)
  system/             ← canonical config, credentials, backups (Shared)
    credentials/      ← OAuth tokens, API keys (never store outside Ai Brain)
    config/           ← agent configs, environment overrides
    backups/          ← automatic backups of critical data
    .memdb/           ← Agent memory trace DB (gitignored, runtime only)
    claude-mcp-skills/← Claude MCP evaluation scripts & references
    WEEKLY_RITUAL.md  ← Weekly skill update checklist

  agents/             ← Unified Swarm directory (all agent workspaces live here)
    openclaw/         ← OpenClaw agent isolated workspace (identity, memory)
    hermes/           ← Hermes agent isolated workspace (content publishing)
    antigravity/      ← Antigravity agent isolated workspace (architect, dev)
  applications/       ← Testing + dev tools from GitHub (camofox, cloakbrowser, etc.)

  ## Dot-Folder Policy (Ai Brain root)
  # .git/            ← Git — MUST be at repo root (tool constraint)
  # .gitignore       ← Git — MUST be at repo root (tool constraint)
  # .gitmodules      ← Git — MUST be at repo root (tool constraint)
  # .obsidian/       ← Obsidian vault config — MUST be at vault root (tool constraint)
  # .claude/         ← Claude Code project config — MUST be at project root (tool constraint)
  # All other data (venvs, memory DBs, MCP scripts) → system/ above
```

## Websites (Stage 1: Staging & Outreach)

### Active Staging Websites
| Site | Mastersheet | Staging Directory |
|------|-------------|-------------------|
| rankray.com | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/rankray.com/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/rankray.com` |

### Cold Outreach Prospect Sites
| Site Prospect | Mastersheet | Staging Directory |
|---------------|-------------|-------------------|
| al-mazrouei-landing | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/outreach/al-mazrouei-landing/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/websites/outreach/al-mazrouei-landing` |

## Active Projects (Tooling & Active Internal Dev)

| Tool/Project | Mastersheet | Path |
|--------------|-------------|------|
| legendary-bot | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/legendary-bot/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/legendary-bot` |
| rank-ray-hq | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rank-ray-hq/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rank-ray-hq` |
| rank-ray-plugins | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rank-ray-plugins/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/rank-ray-plugins` |
| api-tester | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/api-tester/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/api-tester` |
| claude-designs | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/claude-designs/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/claude-designs` |
| crypto-transfer-safety-kit | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/crypto-transfer-safety-kit/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/crypto-transfer-safety-kit` |
| wp-markdown-for-ai | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/wp-markdown-for-ai/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/projects/wp-markdown-for-ai` |

*Note: `.git` is not a project — removed from list.*

## Clients (Stage 2 & 3: Converted & Archived Clients)

### Active Converted Clients (Stage 2)
| Client | Mastersheet | Path |
|--------|-------------|------|
| tonicphysio.com | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/clients/tonicphysio/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/clients/tonicphysio` |
| teammotorcycle.com | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/clients/teammotorcycle.com/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/clients/teammotorcycle.com` |
| coinsfera.com | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/clients/coinsfera.com/mastersheet.md` | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/clients/coinsfera.com` |

### Archived Clients (Stage 3)
| Client | Status | Mastersheet |
|--------|--------|-------------|
| khanllp.com | ARCHIVED (CMS lost 2026-05-14) | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/clients/archive/khanllp.com/mastersheet.md` |

## Rules

| What | File |
|------|------|
| Content quality + pre-push checklist | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/content-rules.md` |
| SEO writing method (Koray) | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/semantic-seo-writer.md` |
| API rate limiting | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/rate-limiting.md` |
| Voice: TonicPhysio | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/voice/tonicphysio.md` |
| OpenClaw swarm master rules | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/agents/openclaw/MASTER-RULES.md` |
| OpenClaw Enigma agent contract | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/agents/openclaw/ENIGMA-MASTER-AGENT.md` |
| Image verification & SEO rule | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/content/image-verification-rule.md` |
| WordPress REST API setup & curl | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/rules/access/wordpress-rest-api-setup.md` |

## Prompts

| What | File |
|------|------|
| Master system prompt | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/MASTER-SYSTEM-BOOTSTRAP.md` |
| Google Workspace access (pre-configured auth) | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/system/config/google-workspace-agent-prompt.md` |
| Site audit task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/site-audit-prompt.md` |
| Landing page task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/landing-page-creation-prompt.md` |
| Product page task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/product-page-creation-prompt.md` |
| Onboarding flow task | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/prompts/onboarding-flow-prompt.md` |

## Scripts / P0 Deterministic Tools

All scripts live canonically in `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/scripts/` and are symlinked to `~/.hermes/scripts/` for runtime access.

| Script | Purpose |
|--------|---------|
| `content-pre-push-validator.py` | Pre-push validation: word count, real internal links (excl. TOC anchors), Yoast fields, em-dashes, raw markdown, heading hierarchy, emojis, filler intros. Returns JSON `can_push`. |
| `wp-blog-auditor.py` | Full-site audit of all WP posts: real internal links, thin content, missing Yoast, featured image + alt, em-dashes, raw markdown. **PLUS** Semantic SEO: LSI coverage, entity extraction, schema markup detection, readability scoring, anchor diversity, CTA detection, FAQ/snippet targeting, canonical validation, information density, image size audit, self-plagiarism (duplicate titles/descriptions). |
| `media-dedup-checker.py` | Checks WP media library before upload. Returns CLEAR or CONFLICT. |
| `validate-index-paths.py` | Cron: reads INDEX.md, verifies referenced paths, alerts on drift. |
| `rankray-service-page-manager.py` | **NEW (2026-05-14):** RankRay service page automation — audit, generate, validate, push ACF content for all 53 service pages. Chunked API pushes, rate-limited, builder-aware (Elementor vs ACF detection). |
| `push-service-pages-chunked.py` | **NEW (2026-05-14):** Immediate-use script for pushing pre-generated ACF content to specific pages with Yoast meta. |

**Rule — Scripts vs. AI Intelligence:**
- Use scripts for: **auditing, finding issues, big data pushes, validation**. Deterministic, fast, no creativity required.
- Use AI for: **correcting issues, writing content, adding internal links, adding featured images**. These need versatile context, varied anchors, and strategic thinking. Never robotically inject the same internal link anchor across all posts.

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
| OpenClaw | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/agents/openclaw/` | Main agent. SEO, content, automation, GMB leads. |
| Hermes | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/agents/hermes/` | Content publishing. Blog strategies, published logs. |
| Antigravity | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/agents/antigravity/` | Architectural development, code reviews, UI builder. |

**WORKSPACE ISOLATION RULES (HARD Stop):**
- All client project data (mastersheets, audits, assets, drafts) MUST be placed in root `projects/[site_folder]/` to ensure visibility and collaboration across all agents.
- Agents are strictly prohibited from keeping project repositories or folders inside their workspaces. No `agents/openclaw/projects/` is permitted!
- Reusable skills and playbooks must live canonically in root `skills/`.
- Chronological daily logs must live canonically in root `memory/`.
- `agents/openclaw/system/` is NOT canonical. All canonical system data lives in root `system/`.

## Applications (Testing + Dev Tools)

All GitHub testing/dev apps live in `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/applications/`.
These are NOT agent workspaces — they are standalone tools for Rank Ray operations.

| App | Path | What it does | Status |
|-----|------|-------------|--------|
| camofox-browser | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/applications/camofox-browser` | Stealth browser for scraping | testing |
| cloakbrowser | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/applications/cloakbrowser` | Cloak browser for fingerprint evasion | testing |
| gitleaks | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/applications/gitleaks` | Secret scanning in repos | testing |
| openserp | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/applications/openserp` | Open-source SERP scraper | testing |
| searxng | `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/applications/searxng` | Meta search engine | testing |

**Rule:** Any new GitHub tool for testing goes here, NOT in home directory. Update this table when adding.

## Skills

Full catalog: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/skills/_CATALOG_MAP.md`

## Autonomous SEO Agency — Cron Schedule

**Status:** LIVE (15 active crons, 0 disabled)
**Last Updated:** 2026-05-25
**Canonical File:** `memory/2026/2026-05-24-autonomous-crons-deployed.md`

All agent automation schedules are defined in OpenClaw cron jobs. The master schedule lives in memory/ and is referenced here.

| Time (PKT) | Cron Name | Channel | Purpose |
|------------|-----------|---------|---------|
| Every 3h | mac-health-check-3h | #claw-status | System health + memory compaction |
| 02:00 | tech-audit-rotation | #claw-developer | Daily technical SEO audit (rotating sites) |
| 05:00 | gsc-opportunity-scan | #claw-status | GSC opportunities: page 2, low CTR, drops |
| 06:00 | gmb-usa-daily | #claw-status | Lead finder |
| 07:00 | daily-position-tracker | #claw-status | Keyword position tracking all sites |
| 08:00 | gmb-canada-daily | #claw-status | Lead finder |
| 10:00 | gmb-uae-daily | #claw-status | Lead finder |
| 12:00 | gmb-australia-daily | #claw-status | Lead finder |
| 14:00 | gmb-uk-daily | #claw-status | Lead finder |
| Sun 03:00 | docker-cleanup-weekly | #claw-status | Docker purge |
| Sun 09:00 | weekly-content-briefs | #claw-writer | Content brief generation |
| Sun 20:00 | weekly-client-report | #rankray | Client performance report |

**Management:** `openclaw cron list` to view, `openclaw cron remove <id>` to delete.
**Models:** All crons use `ollama/kimi-k2.6:cloud` (verified working).
