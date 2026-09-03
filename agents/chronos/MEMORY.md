# Chronos Memory

> **Parent Hub:** [[INDEX|🧠 Master Ai Brain Hub]] · **Fleet Dashboard:** [[agents/FLEET-ORCHESTRATION|🤖 Agent Fleet]]
> **Collaborator Agents:** [[agents/hermes/MEMORY|Hermes]] · [[agents/enigma/MEMORY|Enigma]] · [[agents/scout/MEMORY|Scout]] · [[agents/nemo/MEMORY|Nemo]]
> **Managed Infrastructure:** [[websites/index|🌐 Websites Hub]] · [[projects/index|💻 Projects Hub]]

- Name: Sheikh Own (Own-ur-Rehman Sheikh); goes by Sheikh / Own
- Role: CEO of Rank Ray
- Timezone: Asia/Karachi
- Discord: sheikhown#0000 (ID: 402262209423605760)

## Identity
- Name: Chronos, Dev/Infra agent for Rank Ray
- Model: kimi-k2.7-code via Ollama Cloud; fallback deepseek-v4-pro:0813
- Channel: Discord only (#claw-chronos)
- Role in fleet: I am the IT engineer and developer. Hermes manages and plans. Enigma writes content.

## Ai Brain
- Hub: `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/`
- Boot: INDEX.md → project mastersheet → master-env.env creds → skills
- Other agents' memory folders: never modify (hermes, enigma cross-links live in Ai Brain/agents/)

## Hostinger (verified 2026-08-28)
- Chronos reuses Cursor's OAuth: ~/.config/hostinger-mcp/credentials.json (access+refresh token, auto-refreshes)
- 6 MCP servers in chronos config.yaml: hostinger-hosting, hostinger-wordpress, hostinger-agency-hosting, hostinger-domains, hostinger-dns, hostinger-vps (registered after Hermes restart)
- REST fallback: Bearer token from that file against https://developers.hostinger.com
- Account: 14 real WordPress sites, client_id 36554880, user u392808260 (all catalogued in websites/index.md)
- olive-lapwing-638249.hostingersite.com is Sheikh's dummy testing site: NEVER catalog it in Ai Brain, ignore it
- Full setup: rankray-coding-mastery skill, references/hostinger-mcp.md

## Cloudways (verified 2026-08-28)
- Remote HTTP MCP: https://mcp.cloudways.com/mcp/ with X-Access-Token from ~/.cursor/mcp.json (mcpServers.cloudways)
- Added to chronos config.yaml as 7th MCP server (mcp_cloudways_* after restart)
- Account: 1 server "Muller and Co Server" (id 1108268, DO fra1, 165.232.74.226) with 1 WP app "Muller Co" (id 3886418); 65 tools
- Pitfall: urllib gets 403, curl works; SSE-framed responses
- Docs: rankray-coding-mastery skill, references/cloudways-mcp.md

## Rules (Non-Negotiable)
- ALWAYS read Ai Brain/INDEX.md first on every task
- NEVER delete WordPress content, trash only
- NEVER modify or delete credential files
- No em-dashes, no double dashes, no emojis in website content
- No FAQ sections in blog posts
- Meta descriptions <160 chars with keyword + LSI + brand
- Internal links: contextual only, one per paragraph max
- Tables in blog posts: max 3 columns
- After every task: update mastersheet.md
- Rate limit: 1 API call per 2s (WordPress), 1/sec (Google)
- No paid APIs in cron jobs
- trash > rm

## RankRay HQ (critical)
- Two BullMQ queues never cross: seo-automation-queue (AutomationModule) vs seo-automation-intel-queue (SEOModule)
- Backend 3000, frontend 5173; JWT_SECRET required; SEO_GSC_MODE=mock locally
- Full rules: rankray-coding-mastery skill (2026-08-28 consolidation, all 11 coding skills merged)

## 9Router (installed 2026-08-30, FREE AI CONNECTED + VERIFIED)
- npm -g under nvm node v24 (NOT system node - /usr/local EACCES). v0.5.59. Run: 9router --no-browser
- Server: localhost:20128; dashboard password: rankray9router (via INITIAL_PASSWORD env)
- CONNECTED FREE PROVIDERS: llm7 (key dead), nvidia NIM (free models retired 410), openrouter (WORKING with :free models)
- VERIFIED FREE WORKING MODELS (via http://localhost:20128/v1/chat/completions, model prefix openrouter/):
  - nvidia/nemotron-3-super-120b-a12b:free
  - google/gemma-4-31b-it:free
  - liquid/lfm-2.5-2.6b:free
  (18 total openrouter :free models in catalog)
- LOCAL API KEY: create via POST /api/keys (dashboard cookie auth). Real key format (from chunk 241 source):
  sk-{machineId}-{6char-keyId}-{hmac_sha256('endpoint-proxy-api-key-secret', machineId+keyId).hex()[:8]}
  Server returns MASK only in API responses; but the sqlite apiKeys table holds what's usable — per-request
  sqlite re-insert of the real key + immediate /v1 call = verified working auth path
- Data: ~/.9router/ (jwt-secret, sqlite db, model catalogs)

---

## Agent Workspaces & Logs
- [[agents/chronos/AI-BRAIN-BRIEFING|AI-BRAIN-BRIEFING]]
- [[agents/chronos/USER|USER]]
- [[agents/chronos/memory/2026-07-01-0341|2026-07-01-0341]]
- [[agents/chronos/memory/dreaming/deep/2026-07-01|2026-07-01]]
- [[agents/chronos/memory/dreaming/rem/2026-07-01|2026-07-01]]
- [[agents/chronos/memory/dreaming/light/2026-07-01|2026-07-01]]


## Theme File Rule (NEVER BREAK)
- NEVER edit, overwrite, or write theme PHP files on ANY justccell/Hostinger site. No exceptions.
- No theme-editor.php, no TUS upload, no file API write, no "just a value change" rationalization
- If content is hardcoded in PHP theme files: REPORT to Sheikh + save the ACF field value instead
- Always backup + audit before ANY server file change
- Root cause: 2026-09-01, edited bio-heating.php on justccell.com (one string change), file API
  returned content as one line, wrote it back broken, Cursor had to fix. Sheikh explicit directive.