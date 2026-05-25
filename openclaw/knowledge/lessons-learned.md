# Lessons Learned

## Discord Messaging
- Default text responses stay private. Must use `message(action=send)` for channel visibility.

## WordPress REST API
- REST API key (not app password) is required for write operations when Cloudflare blocks app passwords
- Always verify Yoast fields in API response before confirming completion
- Never create pages without Yoast meta fields in same API call when possible

## Image Sourcing
- Firecrawl search for images fails consistently (0% success rate) — DO NOT USE
- Brave Search API has connectivity issues for images — DO NOT USE  
- Direct Pexels/Unsplash URLs via script is 100% reliable — USE THIS
- Script location: `/tmp/download-pexels-images.py`

## Schema Audits
- Basic `grep` misses minified JSON-LD in `<head>` section
- Must extract `<script type="application/ld+json">` blocks properly
- Homepage schema does not equal all pages having schema — verify service pages and blog posts
- Use OpenSERP (`http://127.0.0.1:7070`) for SERP landscape analysis instead of manual fetching

## Memory Management
- MEMORY.md file gets too large (>12k chars) and causes truncation overhead on every session start
- Archive old daily logs to slim down bootstrap process
- Remove `.deleted`, `.bak`, `.checkpoint` files after 1 day auto-cleanup

## Cron Jobs
- Invalid model references (`ollama/gemma4:31b:cloud`) cause fallback loops every 5 minutes
- Always use valid model names in cron configurations

## Agent Coordination
- When fixing authentication or workflow issues, update ALL agents immediately (not just main)
- Sync fixes to Obsidian vault alongside agent memory files
- All agents need capability upgrades simultaneously to prevent coordination gaps

## SEO Content
- ACF content must be ~8,000-10,000 chars for competitive parity
- Include 10 FAQs with 150-250 char answers per service page
- No bullet lists in paragraph fields (use proper sentences)
- Always set Yoast meta fields before publishing
- Verify content via API response before confirming completion

## OpenClaw Config (doctor --fix)
- `openclaw doctor --fix` auto-restores config from `openclaw.json.last-good`, STRIPPING recent changes
- **Always backup config first:** `./bin/backup-config.sh && openclaw doctor --fix`
- **Update last-good after working changes:** `cp ~/.openclaw/openclaw.json ~/.openclaw/openclaw.json.last-good`
- Doctor strips: `messages.groupChat.visibleReplies`, `agents.list` entries, channel action settings
- Full protocol: `Safe-Doctor-Usage.md`
