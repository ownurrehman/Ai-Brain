# MEMORY.md (Curated, Long-Term)

## Core
- **Owner:** Own-ur-Rehman Sheikh (Rank Ray CEO)
- **Users:** Own (+923335261658), Tahir (+923355973143), Fawad (+923349570172)
- **Agents:** `main` (Ranki), `enigma` (SEO/Content), `nemo` (Elite Code), `chronos` (Deep Research)
- **Routing:** General/SEO/Research/Outreach → `main` | Extreme Engineering → `nemo` | Deep Audits → `chronos`

## Non-negotiables
- **Karpathy Principles:** 1. Think Before Acting 2. Simplicity First 3. Surgical Changes 4. Goal-Driven Verification
- **AI Brain Logging (MANDATORY):** After completing ANY task, the agent MUST write the outcome to the memory file (`memory/YYYY-MM-DD.md` or `projects/{site}/mastersheet.md`) BEFORE declaring the task complete. No exceptions. No "I will log it later." Log first, confirm second.
- **INDEX Protocol (MANDATORY):** Every agent MUST read `INDEX.md` → `mastersheet.md` → required `skills/` before starting ANY task. Agents that skip this produce broken output.
- Links: Verify via sitemap, no duplicates per page.
- Meta: <160 chars (KWD + LSI + Brand).
- Content: No emojis, no double-dashes, no em-dashes. H1 must differ from title tag.
- Never act on assumptions about config values without verification.
- **Post ID Tracking:** After creating ANY post/page, log ID + title + slug in `projects/{project}/post-registry.md`.
- **Credentials:** All API keys in `master-env.env`. Never hardcode passwords in scripts.
- Restricted Channels: Never reply in Discord channel `1476561093454200923` (#claw-documents).
- No LaTeX-style codes (e.g., `$->$`) in messages; use → or =.

## WordPress REST API
- Auth: Basic (base64 of USER:REST_API_KEY from master-env.env)
- Never use APP_PASSWORD for REST API (blocked by Cloudflare). APP_PASSWORD is for browser login only.
- Yoast fields writable via REST API (functions.php snippet already deployed on rankray.com).
- Always convert Markdown to HTML before any WP API call.
- Search Media Library before uploading. No duplicate images.
- Push as DRAFT. Never publish without user approval.

## Content Quality Rules
- No [rankray_ai_summary] or any AI shortcodes
- H1 must be DIFFERENT from title tag
- No em-dashes or en-dashes (AI footprint)
- No repeated words consecutively
- No repeated paragraphs/filler content
- Pre-publishing checklist is MANDATORY (see `rules/content/pre-publish-checklist.md`)

## Image Sourcing
- Use Pexels API (key in master-env.env) for stock images
- Do NOT use Firecrawl or Brave Search for images (unreliable)
- All images: <100kb, WebP preferred, matching filename/alt text
- Always check WP Media Library for existing images before uploading

## Site Access
- `rankray.com`: WP REST API (creds in master-env.env)
- `tonicphysio.com`: WP REST API, ACF fields for service pages (creds in master-env.env)
- ~~`khanllp.com`: CMS Access (creds in master-env.env)~~ — ARCHIVED 2026-05-25 (moved to `/clients/archive/khanllp.com/`)
- `coinsfera.com`: WP REST API (creds in master-env.env) — SEO Audit V3 Complete 2026-05-14 (Score: 70/100 Fair)
- TonicPhysio service pages use ACF fields, NOT standard content. Template: `services-pages.php`, Category: `page_category: [325]`

## Agent Habits (Updated 2026-05-19)
- **Channel Context:** When context is unclear, ALWAYS read last 10 messages from the current channel before asking "what do you need" or "what were you waiting on." Prevents missing tasks and appearing unresponsive.

## Autonomous SEO Agency (Updated 2026-05-24)
- **Master Schedule:** `system/autonomous-seo-agency-cron-schedule.md`
- **Phase 1 Deploy Script:** `scripts/deploy-phase1-crons.sh`
- **Status:** Planned, pending deployment approval
- **Workstreams:** 6 (Lead Gen, Research, Content, Technical, Analytics, System Health)
- **Cron Jobs Planned:** 20+ new jobs across all workstreams
- **Automation Strategy:** 30/70 Hybrid (30% fully auto, 50% AI-assisted, 20% human-led)
- **Critical Rule:** NEVER auto-publish content — always push as DRAFT to WordPress
- **Target:** 200+ leads/month, 60+ content drafts/month, 100+ keywords tracked daily per site

## Cron Jobs (Active — Updated 2026-05-24)

| Time (PKT) | Name | Channel | Purpose |
|------------|------|---------|---------|
| Every 3h | mac-health-check-3h | #claw-status | System health + memory compaction |
| 02:00 | tech-audit-rotation | #claw-developer | Daily technical SEO audit (rotating sites) |
| 05:00 | gsc-opportunity-scan | #claw-status | GSC opportunities: page 2, low CTR, drops |
| 06:00 | gmb-usa-daily | #claw-status | Lead finder |
| 07:00 | daily-position-tracker | #claw-status | Keyword position tracking all sites |
| 08:00 | gmb-canada-daily | #claw-status | Lead finder |
| 10:00 | gmb-uae-daily | #claw-status | Lead finder |
| 11:00 Mon | weekly-hot-lead-proposals | #claw-emailer | SEO audit proposals for interested leads |
| 12:00 | gmb-australia-daily | #claw-status | Lead finder |
| 14:00 | gmb-uk-daily | #claw-status | Lead finder |
| 15:00 | daily-lead-email-drafter | #claw-emailer | Draft personalized cold emails for A/B leads |
| 16:00 Sun/Wed | follow-up-email-drafter | #claw-emailer | Follow-up drafts for non-responders |
| Sun 03:00 | docker-cleanup-weekly | #claw-status | Docker purge |
| Sun 09:00 | weekly-content-briefs | #claw-writer | Content brief generation |
| Sun 20:00 | weekly-client-report | #rankray | Client performance report |

**Total: 15 active crons. 0 disabled. 0 broken models.**

**Cleaned up and removed:**
- memory-cleanup (14 errors, wrong model deepseek-v4-flash)
- token-research (disabled, unused)
- ollama-monitor (disabled, unused)
- intelligent-lead-generator (disabled, delivery broken)
- gmb-pakistan-daily (removed per user: low-ball clients)
- ~~seo-khanllp-5pm~~ — REMOVED 2026-05-25, CMS access lost, no longer a client
- khanllp.com references purged from system (see memory/2026-05-25.md)
- token-research (disabled, unused)
- ollama-monitor (disabled, unused)
- intelligent-lead-generator (disabled, delivery broken)
- gmb-pakistan-daily (removed per user: low-ball clients)

**Management:** `openclaw cron list` to view, `openclaw cron remove <id>` to delete.

## Workspace Paths
- **Root:** `/Users/sheikhown/Ai Works - Local/Ai Codes/Ai Brain/openclaw`
- **Scripts:** `scripts/`
- **Reports:** `reports/`
- **Memory:** `memory/`
- **.env template:** `.env.wordpress` (fill in real credentials)

## Agent Performance
- **Model:** `ollama/kimi-k2.6:cloud`
- **Session size limit:** Truncate trajectory to last 30 entries when >2MB
- **Auto-compact:** Recommended after 50 turns
- **Context strategy:** Use `sessions_spawn` for heavy research tasks

## Architecture
- Sequential work only (Parallel DEPRECATED)
- Daily: Fix → Target → Link → Content
- AEO Framework: All content must align with `unified-aeo-semantic-framework.md`
- Self-Correction: Mandatory Audit Phase using `self-audit-protocol.md`
- Browser: CamoFox for stealth scraping when needed

## Discord Channels (Updated 2026-05-06)

| Channel | ID | Purpose |
|---------|-----|---------|
| claw-status | 1476131657663909970 | Main status channel |
| claw-chat | 1476025453599789191 | General chat |
| claw-writer | 1482488418532589712 | Content writing |
| claw-developer | 1272860753535307817 | Development tasks |
| claw-emailer | 1496584632026796112 | Email operations |
| rankray | 1156128279430959165 | Rank Ray SEO reports |
| claw-tests | 1156165272223363092 | Testing |
| coinsfera | 1156145694730620928 | Coinsfera SEO |
| ~~khanllp~~ | ~~1272860276437422101~~ | ~~KhanLLP SEO~~ | ❌ REMOVED |
| tonicphysio | 1156322019072299068 | TonicPhysio SEO |
| teammotorcycle | 1475806039600271472 | TeamMotorcycle SEO |
| claw-documents | 1476561093454200923 | 📄 Restricted — never reply |

**SEO Cron Schedules (PKT) — Updated 2026-05-07:**
| Time | Site | Target Channel | Cron Name |
|------|------|----------------|-----------|
| 06:00 | Token Optimization | #claw-chat | token-optimization-6am |
| 11:00 | coinsfera | #coinsfera | seo-coinsfera-11am |
| 14:00 | tonicphysio | #tonicphysio | seo-tonicphysio-2pm |
| 17:00 | ~~khanllp~~ | ~~#khanllp~~ | ~~seo-khanllp-5pm~~ | ❌ REMOVED - CMS access lost 2026-05-14 |
| 20:00 | teammotorcycle | #teammotorcycle | seo-teammotorcycle-8pm |
| 22:00 | rankray | #rankray | seo-rankray-10pm |

**Session Management:**
- Main session: `0054a659-57a5-47b4-a4a7-877d06fc775c`
- Trajectory truncated to last 30 entries (was 95)
- Sessions dir: 4.1MB (down from 33MB)
- Auto-cleanup: `.deleted`, `.bak`, `.checkpoint` files removed after 1 day

**WordPress Autoblogger:**
- Skill: `~/.openclaw/skills/wordpress-aeo-autoblogger/`
- Config template: `workspace/.env.wordpress`
- Setup script: `scripts/setup-wordpress-autoblogger.py`
- Status: Pending WP credentials (WP_USERNAME, WP_APP_PASSWORD, GEMINI_API_KEY)

**Restricted:** Never reply in #claw-documents (1476561093454200923).

## Key Lessons Learned
- WordPress `www.rankray.com` redirects to `rankray.com` — always use non-www
- TonicPhysio server time runs ~3.6 hours ahead of UTC
- Google Sheets service account may hit Drive storage quota — fall back to CSV
- Firecrawl fails consistently for images — use Pexels direct API
- 430 TeamMotorcycle collection pages still missing meta descriptions
- `/collections/helmets` on TeamMotorcycle returns 404 — needs redirect

> Full memory archive: `_archived-scripts/MEMORY-FULL-BACKUP-2026-05-05.md`

## Promoted From Short-Term Memory (2026-05-05)

<!-- openclaw-memory-promotion:memory:memory/2026-05-01.md:21:21 -->
- **Condition-Specific (9):** [score=0.833 recalls=0 avg=0.620 source=memory/2026-05-01.md:21-21]
<!-- openclaw-memory-promotion:memory:memory/2026-05-01.md:27:27 -->
- **Specialty Services (6):** [score=0.833 recalls=0 avg=0.620 source=memory/2026-05-01.md:27-27]
<!-- openclaw-memory-promotion:memory:memory/2026-05-01.md:32:32 -->
- **Treatment Modalities (5):** [score=0.833 recalls=0 avg=0.620 source=memory/2026-05-01.md:32-32]

## Promoted From Short-Term Memory (2026-05-06)

<!-- openclaw-memory-promotion:memory:memory/2026-05-01.md:36:36 -->
- **Workplace/Sports (5):** [score=0.856 recalls=0 avg=0.620 source=memory/2026-05-01.md:36-36]
