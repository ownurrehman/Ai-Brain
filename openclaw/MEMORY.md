# MEMORY.md (Curated, Long-Term)

## Core
- **Owner:** Own-ur-Rehman Sheikh (Rank Ray CEO)
- **Users:** Own (+923335261658), Tahir (+923355973143), Fawad (+923349570172)
- **Agents:** `main` (Ranki), `enigma` (SEO/Content), `nemo` (Elite Code), `chronos` (Deep Research)
- **Routing:** General/SEO/Research/Outreach → `main` | Extreme Engineering → `nemo` | Deep Audits → `chronos`

## Non-negotiables
- **Karpathy Principles:** 1. Think Before Acting 2. Simplicity First 3. Surgical Changes 4. Goal-Driven Verification
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
- `khanllp.com`: CMS Access (creds in master-env.env)
- TonicPhysio service pages use ACF fields, NOT standard content. Template: `services-pages.php`, Category: `page_category: [325]`

## Cron Jobs (Active — Updated 2026-05-07)

| Time (PKT) | Name | Channel | Purpose |
|------------|------|---------|---------|
| Every hour | status-hourly | Last channel | Health check |
| 06:00 | token-optimization-6am | #claw-chat | Context/memory optimization |
| 11:00 | seo-coinsfera-11am | #coinsfera | Daily SEO audit |
| 14:00 | seo-tonicphysio-2pm | #tonicphysio | Daily SEO audit |
| 17:00 | seo-khanllp-5pm | #khanllp | Daily SEO audit |
| 20:00 | seo-teammotorcycle-8pm | #teammotorcycle | Daily SEO audit |
| 22:00 | seo-rankray-10pm | #rankray | Daily SEO audit |

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
| khanllp | 1272860276437422101 | KhanLLP SEO |
| tonicphysio | 1156322019072299068 | TonicPhysio SEO |
| teammotorcycle | 1475806039600271472 | TeamMotorcycle SEO |
| claw-documents | 1476561093454200923 | 📄 Restricted — never reply |

**SEO Cron Schedules (PKT) — Updated 2026-05-07:**
| Time | Site | Target Channel | Cron Name |
|------|------|----------------|-----------|
| 06:00 | Token Optimization | #claw-chat | token-optimization-6am |
| 11:00 | coinsfera | #coinsfera | seo-coinsfera-11am |
| 14:00 | tonicphysio | #tonicphysio | seo-tonicphysio-2pm |
| 17:00 | khanllp | #khanllp | seo-khanllp-5pm |
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
