# Ai Brain — Master Index

**Read this first.** Then `docs/ENV.md`, then the site/project `mastersheet.md`.

Ai Brain is the central knowledge base — not an OpenClaw workspace and not RankRay HQ.

---

## Do not confuse

| Name | What it is | Path |
|------|------------|------|
| **rankray.com** | Agency marketing website | `websites/rankray.com/` |
| **RankRay HQ** | Multi-module SaaS (CRM, finance, HRM, SEO, agents) | `projects/rankray-hq/` |
| **seoengineai.com** | Empty website placeholder | `websites/seoengineai.com/` |
| **SEO Engine AI theme** | HQ marketing theme (tracked inside HQ) | `projects/rankray-hq/marketing/seo-engine-ai-theme/` |
| **OpenClaw workspace** | Agent runtime files | `~/.openclaw/workspace/` |
| **openclaw/** in this vault | Nested OpenClaw clone | `openclaw/` (gitignored) |

Client-site themes, plugins, env, PageSpeed dumps, and WP backups belong under `websites/<domain>/`. Never under `projects/rankray-hq/`.

---

## RankRay HQ — how users get agents

Do **not** “install Hermes” by cloning `hermes-agent` into `projects/rankray-hq/.hermes/`. That 650MB folder is a **dev machine clone**, not a SaaS install. Tenants cannot use it.

**Correct model (already in the product):**

| Layer | What it is | Path |
|-------|------------|------|
| Runtime (you host) | Docker OpenClaw + Hermes + RankRay bridge | `projects/rankray-hq/deploy/agent-runtimes/` |
| Catalog (users “install”) | System agent templates cloned into a workspace | `AgentDefinitionService.cloneToWorkspace()` · `agent-roles.config.ts` |
| Runs | Workspace-scoped jobs via Nest | `rankray-hq-backend/src/agents/` |
| Chat | RankRay AI / HQ Lab → **bridge** `:8787` not raw Hermes `:8642` | `docs/operations/agent-runtimes.md` |

User “Install SEO Auditor” = clone the system template + attach keys/tools. RankRay keeps running Hermes in Compose. Optional: keep a Hermes git clone at `~/.hermes/hermes-agent` and set `HERMES_BUILD_CONTEXT` for faster image builds — **outside** the HQ repo.

---

## Vault layout

```
Ai Brain/
  INDEX.md              ← this file
  README.md
  master-env.env        ← secrets (gitignored) — see docs/ENV.md
  mastersheet.md        ← cross-site SEO audit rotation
  docs/ENV.md           ← credential map (names + paths only)
  websites/<domain>/    ← each live site + its mastersheet.md
  projects/rankray-hq/  ← SaaS only
  agents/<name>/        ← Hermes, Chronos, Emilia, Nemo, Antigravity
  credentials/          ← google-oauth/ + websites/
  rules/  skills/  prompts/  scripts/  memory/  system/
```

---

## Credential storage

**Canonical location:** `credentials/` + vault-root `master-env.env`

Full key inventory: [`docs/ENV.md`](docs/ENV.md)

### google-oauth/

- `oliverjakeseo@gmail.com-oauth-credentials.json`
- `oliverjakeseo@gmail.com-oauth-token.json` / `.pickle` (auto-refreshed)
- `ga-mcp-adc.json` — GA4 MCP ADC
- **Email:** oliverjakeseo@gmail.com
- **GCP project:** openclaw-rank-ray-automation
- **Symlinks:** `~/.config/gcp/` and `system/credentials/google-oauth/` → this folder
- **DO NOT MOVE OR DUPLICATE**

### google-sheets/

- `credentials.json` — symlink to `~/.config/google-sheets/credentials.json`
- **Service account:** rank-ray-sheets-bot-80@openclaw-rank-ray-automation.iam.gserviceaccount.com

### websites/

- `coinsfera.env` — CoinSfera WP / FTP / SSH (also merged into `master-env.env`)
- `tonicphysio-wp-cookies.txt` — local WP admin cookie jar

### WordPress keys (in `master-env.env`)

`RANKRAY_WP_*` · `TONICPHYSIO_WP_*` · `COINSFERA_WP_*` (use www REST URL) · `BACKLINKCRYPTO_WP_*`

**Google user account** (Gmail/Drive/Docs/GSC/GA4 OAuth): `oliverjakeseo@gmail.com` only.

---

## Agent memory

```
agents/
  hermes/        MEMORY.md, USER.md, AI-BRAIN-BRIEFING.md
  chronos/       MEMORY.md, USER.md, AI-BRAIN-BRIEFING.md, memory/
  emilia/        workspace files + memory/
  nemo/          memory/
  antigravity/   IDENTITY.md, MEMORY.md, SOUL.md
```

OpenClaw identity files (`AGENTS.md`, `SOUL.md`, …) live at `~/.openclaw/workspace/`, not the vault root.

---

## Key websites

| Path | Role | Mastersheet |
|------|------|-------------|
| `websites/rankray.com/` | Rank Ray marketing site | `websites/rankray.com/mastersheet.md` |
| `websites/tonicphysio.com/` | Tonic Physio | `websites/tonicphysio.com/mastersheet.md` |
| `websites/coinsfera.com/` | CoinSfera OTC | `websites/coinsfera.com/mastersheet.md` |
| `websites/teammotorcycle.com/` | Team Motorcycle | `websites/teammotorcycle.com/mastersheet.md` |
| `websites/backlinkcrypto.com/` | Backlink Crypto | `websites/backlinkcrypto.com/mastersheet.md` |
| `websites/own-ur-rehman.com/` | Placeholder — keep (personal domain) | — |
| `websites/seoengineai.com/` | Placeholder — keep (product domain) | — |
| `websites/outreach/` | Outreach landings | — |
| `websites/archive/` | Archived sites (`khanllp.com`) | `websites/archive/khanllp.com/mastersheet.md` |

Catalog: [`websites/index.md`](websites/index.md)

Cross-site backlink plan: `system/reports/backlink-strategy-2026-08-12.md`

---

## Key projects

| Path | Role |
|------|------|
| `projects/rankray-hq/` | RankRay HQ SaaS (frontend + backend). **Not** rankray.com. |
| `projects/rankray-plugins/` | WordPress plugins for Rank Ray |
| `projects/legendary-bot/` | Bot framework |
| `projects/claude-designs/` | Design assets |
| `projects/lead-generation-system/` | Unfinished test project — keep; only `output/audits/` in vault so far |
| `projects/archives/` | Old projects + `_rankray-v2-discard-*` |
| `openclaw/` | Nested OpenClaw clone (gitignored) — leave |

Catalog: [`projects/index.md`](projects/index.md)

---

## MCP: `analytics-mcp`

- **Run:** `pipx run analytics-mcp`
- **Registered in:** `~/.openclaw/openclaw.json`
- **Credentials:** `credentials/google-oauth/ga-mcp-adc.json`
- **Project ID:** `openclaw-rank-ray-automation`
- **Tools:** `get_account_summaries`, `get_property_details`, `list_google_ads_links`, `run_report`, `run_funnel_report`, `get_custom_dimensions_and_metrics`, `run_realtime_report`
- **Use for:** SEO audits, traffic, conversions, content performance

---

## Skills

- `~/.openclaw/skills/` — OpenClaw skills
- `~/.openclaw/skills/google-workspace-master/` — Google Workspace automation
- `skills/rankray-service-pages-rules/` — RankRay ACF service page rules

---

## Machine runtime

Canonical guide: [`system/MACHINE_RUNTIME.md`](system/MACHINE_RUNTIME.md)

- Shell + OpenClaw Node SSOT: nvm Node 22 (`v22.23.1`)
- Check: `ai-runtime-check` or `scripts/ai-runtime-check.sh`
- Sync: `ai-runtime-sync` or `scripts/ai-runtime-sync.sh`

## Core scripts

- `scripts/ai-runtime-check.sh` / `scripts/ai-runtime-sync.sh`
- `scripts/google-oauth-manager.py`
- `scripts/subagent-manager.py`
- `scripts/agent-ledger.py`
- `scripts/mac-health-check.sh`
- `scripts/docker-cleanup.sh`
- `scripts/graphify-watch.sh`

## Rules

- `rules/content/content-rules.md`
- `rules/content/semantic-seo-writer.md`
- `rules/access/wordpress-rest-api-setup.md`
- `rules/rankray-location-pages.md`
- `rules/rate-limiting.md`
- `rules/file-artifact-mandate.md`

## RankRay **website** (not HQ)

- Scripts: `websites/rankray.com/scripts/`
- ACF reference: `websites/rankray.com/knowledge/ACF-SERVICE-PAGE-REFERENCE.md`
- Post registry: `websites/rankray.com/post-registry.md`
- Mastersheet: `websites/rankray.com/mastersheet.md`
- Email drafts: `websites/rankray.com/email-drafts/` — **rankray.com email marketing** (keep; not a duplicate of `emails/`)
- Audits: `websites/rankray.com/audits/`
- ACF JSON: `websites/rankray.com/acf-content/`
- Themes/plugins from site work: `websites/rankray.com/themes/`, `plugins/`

## OpenClaw workspace

**Location:** `~/.openclaw/workspace/`

- `AGENTS.md`, `SOUL.md`, `USER.md`, `IDENTITY.md`, `TOOLS.md`, `MEMORY.md`, `HEARTBEAT.md`, `DREAMS.md`, `memory/`
- Config: `~/.openclaw/openclaw.json`
