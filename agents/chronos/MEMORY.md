# Chronos Memory

## Human
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