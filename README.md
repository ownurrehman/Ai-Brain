# Ai Brain

Central knowledge base for all AI agents. Not an OpenClaw workspace and not RankRay HQ.

## Quick start

1. Read [`INDEX.md`](INDEX.md) — navigation and “do not confuse” map
2. Read [`docs/ENV.md`](docs/ENV.md) — where credentials live (names only)
3. Read `master-env.env` — actual secrets (gitignored)
4. Read the site or project `mastersheet.md` for the job you are on
5. Optional: [`prompts/MASTER-SYSTEM-BOOTSTRAP.md`](prompts/MASTER-SYSTEM-BOOTSTRAP.md)

## Folders

| Path | What it is |
|------|------------|
| `websites/` | Client and marketing **websites** (rankray.com, tonicphysio.com, …) |
| `projects/rankray-hq/` | RankRay HQ **SaaS** (finance, CRM, HRM, SEO modules). Not the website. |
| `projects/` | Other internal apps (plugins, bots, archives) |
| `agents/` | Per-agent memory (`hermes/`, `chronos/`, …) |
| `credentials/` | Secrets (OAuth, per-site env). Gitignored. |
| `rules/` | Content and access standards |
| `skills/` | Skill library |
| `prompts/` | Master prompt + templates |
| `openclaw/` | Nested OpenClaw clone (runtime). Agent workspace is `~/.openclaw/workspace/` |

## Hard split

- **rankray.com** → `websites/rankray.com/`
- **RankRay HQ** → `projects/rankray-hq/`
