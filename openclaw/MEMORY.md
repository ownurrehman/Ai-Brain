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

## Automation Schedule (PKT)
- Daily SEO: teammotorcycle (09:00), tonicphysio (10:00), khanllp (11:00), rankray (20:00)
- Token Research: 06:00
- CRM: Google Sheet (ID: 11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4)

## Architecture
- Sequential work only (Parallel DEPRECATED)
- Daily: Fix → Target → Link → Content
- AEO Framework: All content must align with `unified-aeo-semantic-framework.md`
- Self-Correction: Mandatory Audit Phase using `self-audit-protocol.md`
- Browser: CamoFox for stealth scraping when needed

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
