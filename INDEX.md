# Ai Brain — Master Index

**Read this first.** Then `docs/ENV.md`, then the site/project `mastersheet.md`.

Ai Brain is the central knowledge base — not an OpenClaw workspace and not RankRay HQ.

---

## Do not confuse

| Name | What it is | Path |
|------|------------|------|
| **rankray.com** | Agency marketing website strategy | `websites/rankray.com/` |
| **RankRay HQ (Docs)** | SaaS Architecture & Specifications | `projects/rankray-hq/` |
| **RankRay HQ (Code)** | SaaS Source Code (Next.js / Node) | `../Apps/rankray-hq/` |
| **Legendary Bot (Code)**| Trading Bot Codebase | `../Apps/legendary-bot/` |
| **Hermes Gateway** | Global Multi-Agent Gateway | `~/.hermes/` |


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

**Full catalog:** [`websites/index.md`](websites/index.md) — synced with Hostinger API 2026-08-28 (15 hosted WP sites). Highlights:

| Path | Role | Mastersheet |
|------|------|-------------|
| `websites/rankray.com/` | Rank Ray marketing site | `websites/rankray.com/mastersheet.md` |
| `websites/tonicphysio.com/` | Tonic Physio | `websites/tonicphysio.com/mastersheet.md` |
| `websites/coinsfera.com/` | CoinSfera OTC (not on Hostinger) | `websites/coinsfera.com/mastersheet.md` |
| `websites/teammotorcycle.com/` | Team Motorcycle (not on Hostinger) | `websites/teammotorcycle.com/mastersheet.md` |
| `websites/backlinkcrypto.com/` | Backlink Crypto | `websites/backlinkcrypto.com/mastersheet.md` |
| `websites/sellcryptoindubai.com/` | Crypto OTC Dubai (+ sellbitcoinindubai, sellusdtindubai) | `websites/sellcryptoindubai.com/mastersheet.md` |
| `websites/justccell.com/` | Vaporizer hardware | `websites/justccell.com/mastersheet.md` |
| `websites/impactestatemarketing.com/` | Real estate marketing | `websites/impactestatemarketing.com/mastersheet.md` |
| `websites/classicshop.pk/` | E-commerce PK | `websites/classicshop.pk/mastersheet.md` |
| `websites/whiterosepvt.com/` | General order supply | `websites/whiterosepvt.com/mastersheet.md` |
| `websites/gemstonespk.com/` | Gemstones PK | `websites/gemstonespk.com/mastersheet.md` |
| `websites/mariaoasis.com/` | Beauty salon | `websites/mariaoasis.com/mastersheet.md` |
| `websites/own-ur-rehman.com/` | Personal domain (Hostinger, live) | placeholder |
| `websites/seoengineai.com/` | Product domain (Hostinger, live) | placeholder |
| `websites/outreach/` | Outreach landings | — |
| `websites/archive/` | Archived sites (`khanllp.com`) | `websites/archive/khanllp.com/mastersheet.md` |

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
- `skills/rankray-location-updater/` — RankRay location page updater tool
- **Master content skill:** `~/.hermes/profiles/enigma/skills/seo/rankray-seo-content-mastery/` — ALL content/SEO/AEO work for every agent (consolidated 2026-08-28; former 27 per-type skills archived to `skills/_archived-2026-08-28/`)

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

## AgentMail (Agent-Owned Email)

- **Inbox:** `sheikhown@agentmail.to`
- **API Key:** `~/.hermes/.env` as `AGENTMAIL_API_KEY`
- **SDK:** `agentmail` v0.5.9 (Python, in Hermes venv)
- **Skill:** `~/.hermes/skills/email/agentmail/SKILL.md` (full API reference, workflows, pitfalls)
- **Docs:** https://docs.agentmail.to/
- **Console:** https://console.agentmail.to
- **Free Tier:** 3 inboxes, 3,000 emails/month
- **Capabilities:** Send/receive email, threads, drafts, scheduled sending, attachments, webhooks, websockets, labels, allowlists/blocklists, IMAP/SMTP access
- **Use cases:** Outreach emails, service signups + verification, scheduled follow-ups, auto-reply, email-based automation, agent-to-human communication

## OpenClaw workspace

**Location:** `~/.openclaw/workspace/`

- `AGENTS.md`, `SOUL.md`, `USER.md`, `IDENTITY.md`, `TOOLS.md`, `MEMORY.md`, `HEARTBEAT.md`, `DREAMS.md`, `memory/`
- Config: `~/.openclaw/openclaw.json`


---

## Agent Fleet Orchestration

**Dashboard:** `agents/FLEET-ORCHESTRATION.md`

| Agent | Model | Role | Profile |
|---|---|---|---|
| Hermes | glm-5.2 (Ollama Cloud) | Main manager, WhatsApp | default |
| Chronos | kimi-k2.7-code (Ollama Cloud) | Dev, infra, coding | chronos |
| Enigma | qwen3.5:397b (Ollama Cloud) | Content writing, SEO | enigma |
| Nemo | nemotron-3-ultra-550b-a55b (NVIDIA API) | Elite code, architecture | nemo |
| Scout | gpt-oss:20b (Ollama Cloud) | Research, intel | scout |
| Emilia | minimax-m3 (Ollama Cloud) | Outreach, backlinks | emilia |

**Spawn:** `hermes -p <profile> chat -q "task"` or via Hermes terminal background
**Rules:** All agents read INDEX.md first, share Ai Brain credentials, report to Hermes

---

## Outreach System (Rank Ray Client Acquisition)

**Location:** `system/outreach/`

Self-learning email prospecting engine. Sends 100 SEO/web dev pitch emails/day to UAE businesses. Only notifies user when a prospect replies.

### Architecture

| File | Purpose |
|------|---------|
| `outreach-engine.py` | Main sender: scrapes prospect website, finds real SEO issues, generates personalized skill-compliant email, sends via AgentMail |
| `extract-emails.py` | Email scraper: 3-layer extraction (Python requests → Firecrawl JS rendering → curl fallback) + email-sleuth SMTP verification |
| `templates/templates.json` | 5 email template variants competing by reply rate |
| `data/prospects.json` | 4,584 UAE businesses scraped from Google Places API (40 industries × 3 cities) |
| `data/sent_log.json` | Tracks every email sent (who, when, which template, SEO issues found) |
| `data/reply_log.json` | All prospect replies |
| `data/learning.json` | Template performance stats (sent, replies, reply rate, status) |
| `logs/daily_logs.json` | Daily batch summaries |

### How It Works

1. **Daily 9 AM cron**: Engine scans prospect websites for real SEO issues (SSL, title, meta, H1, schema, load time, content thinness, OG tags)
2. **Conversion filter**: Only emails businesses with 2+ SEO issues AND 20+ reviews (established + actually needs help)
3. **Skill-compliant emails**: 80-110 words, lowercase subject, "Own" sign-off, low-friction CTA, passes cold_email_check.py validation
4. **Self-learning**: Templates with higher reply rates get used more. Dead templates (50+ sends, 0 replies) get killed
5. **Reply monitor cron (every 3h)**: Checks AgentMail inbox, notifies user only when prospect replies

### Tools Integrated

- **AgentMail API** (`sheikhown@agentmail.to`) - sends 100/day (3,000/month free tier)
- **Google Places API** - prospect data (business name, website, phone, reviews, rating)
- **Firecrawl API** - JavaScript-rendered website scraping for email extraction
- **email-sleuth** (`es` command, v1.1.0) - SMTP email verification
- **marketing-copywriting skill** - cold_email_check.py quality gate (banned phrases, word count, subject rules)
- **Master content skill** - real SEO findings per prospect website (`rankray-seo-content-mastery`, former seo-audit skill consolidated 2026-08-28)

### Cron Jobs

- `rankray-outreach-daily` (job ID: c10bcc223be1) - daily 9 AM, sends 100 emails
- `rankray-outreach-reply-check` (job ID: 048fe7562bfe) - every 3h, checks for replies

### Target Markets

Dubai, Sharjah, Abu Dhabi. 40 industries including dental clinics, law firms, real estate, construction, restaurants, hotels, gyms, car repair, pharmacies, accounting firms, and more.

### Status

- 4,584 prospects scraped from Google Places API
- 2,057 have websites (email extraction targets)
- Email extraction in progress (3-layer scraper + email-sleuth verification)
- 8 emails sent in first batch (Aug 17), 0 replies so far
- System rebuilt v3 with skill compliance, conversion filtering, and real SEO auditing
