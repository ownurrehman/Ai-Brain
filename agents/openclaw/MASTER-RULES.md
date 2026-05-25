# MASTER RULES — Rank Ray OpenClaw System

**Last Updated:** 2026-04-21  
**Version:** 1.1 — UNIVERSAL SEO RULES (ALL SITES)  
**Status:** ACTIVE — ALL AGENTS MUST READ BEFORE EXECUTION

---

## ⚠️ CRITICAL: THESE ARE MASTER SEO RULES (UNIVERSAL)

**This is NOT just for Rank Ray blogs.**  
**This applies to ALL 5 sites:** Rank Ray, Tonic Physio, Team Motorcycle, Khan LLP, Coinsfera  
**This applies to ALL blog posts, ALL pages, ALL content — NO EXCEPTIONS**

Every agent (main, enigma, chronos, researcher, subagents) MUST follow these rules for EVERY publishing task on EVERY site.

---

## ⚠️ MANDATORY READING RULE

**BEFORE ANY AGENT EXECUTES A TASK:**
1. Read this file (`MASTER-RULES.md`)
2. Check `TOOL-INTEGRATIONS.md` for available tools
3. Read relevant daily memory file (`memory/YYYY-MM-DD.md`)
4. THEN proceed with task

**This is not optional.** All agents (main, enigma, chronos, researcher, subagents) must synchronize with master knowledge before acting.

---

## 📡 Communication Channels

| Channel | Purpose | Agent Access |
|---------|---------|--------------|
| **WhatsApp** | User communication (Own, Tahir, Fawad) | main agent only |
| **Discord #claw-status** | Task status updates, progress logs | all agents |
| **Discord #project-rankray** | Rank Ray specific discussions | enigma, main |
| **Discord #project-tonicphysio** | Tonic Physio specific | enigma, main |
| **Discord #project-teammotorcycle** | Team Motorcycle specific | enigma, main |
| **Discord #project-khanllp** | Khan LLP specific | enigma, main |
| **Discord #project-coinsfera** | Coinsfera specific | enigma, main |

**Routing Rules:**
- User messages → WhatsApp → main agent
- Task completions → Discord #claw-status → all channels
- Project-specific updates → Respective project channel
- Tool errors → Discord #claw-status + daily memory file

---

## 🛠️ Tool Integrations (Master Level)

### Search & Scraping
- ✅ **Firecrawl** — Primary web search/scraping (integrated 2026-04-15)
- ✅ **OpenSERP** — SERP data extraction (self-hosted, /opt/openserp/)
- ❌ **Brave Search API** — DO NOT USE (connectivity issues, use Firecrawl instead)
- ✅ **Playwright** — Browser automation (for PAA, snippets, dynamic content)

### Content & SEO
- ✅ **WordPress REST API** — All sites (Rank Ray, Tonic Physio, Team Motorcycle, Khan LLP, Coinsfera)
- ✅ **Yoast SEO** — Via WordPress meta fields (REST API writable with functions.php snippet)
- ✅ **Semrush API** — Available but endpoints need verification (50,000 units with 7-day cache)

**WORDPRESS REST API AUTHENTICATION (CRITICAL — 2026-04-21 FIX):**
- ✅ **CORRECT:** `<WP_USER>:<WP_REST_API_KEY>` from `.env` (e.g., `openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j`)
- ❌ **WRONG:** `<WP_USER>:<WP_APP_PASSWORD>` (blocked by Cloudflare Turnstile)
- ✅ **ALL AGENTS MUST USE:** REST API key for `/wp-json/wp/v2/*` endpoints
- ✅ **Yoast fields writable:** Requires `functions.php` snippet (see Agent Files section)

### Image & Media
- ✅ **Unsplash API** — Free images (commercial use allowed)
- ✅ **Pexels API** — Free images (commercial use allowed)
- ✅ **Pixabay API** — Free images (commercial use allowed)
- ✅ **WordPress Media Library** — REST API upload with alt text

**CRITICAL IMAGE RULE (UNIVERSAL — ALL SITES):**
- ❌ **NEVER reuse existing images** from ANY website's media library for NEW articles/pages
- ✅ **ALWAYS source fresh images** from Unsplash/Pexels/Pixabay (external sources)
- ✅ **Download, rename, upload fresh** — every new article gets unique images
- ✅ **Rationale:** SEO benefit from unique images, avoid duplicate content signals
- ✅ **Exception:** Only reuse if image is brand-specific (logo, team photos, proprietary graphics)
- ✅ **APPLIES TO:** Rank Ray, Tonic Physio, Team Motorcycle, Khan LLP, Coinsfera — **ALL SITES, FOREVER**

**IMAGE SOURCING TECHNIQUE (MASTER STANDARD):**
- ✅ **USE:** Firecrawl search for Pexels/Unsplash images (NOT hardcoded fallback URLs)
- ❌ **DO NOT USE:** Hardcoded Pexels fallback URLs (return irrelevant images - Coca-Cola, tractors, etc.)
- ❌ **DO NOT USE:** Firecrawl search with `site:` operator (fails)
- ❌ **DO NOT USE:** Brave Search API for images (connectivity issues)
- ✅ **MANDATORY VERIFICATION:** Download image FIRST, open and verify it matches topic, THEN upload
- ✅ **REJECT:** Brand logos (Coca-Cola, Nike, etc.), random objects (tractors, food), irrelevant context
- ✅ **ACCEPT:** Analytics dashboards, charts, tech/business imagery, abstract concepts
- ✅ **WORKFLOW:** 
  1. Search with Firecrawl or direct Unsplash/Pexels URLs
  2. Download to /tmp/
  3. **OPEN AND VERIFY** image matches topic (CRITICAL - NO SKIPPING)
  4. Upload to WordPress with alt text via REST API
- ✅ **ALL AGENTS MUST USE:** This verification standard for consistent results
- ✅ **REFERENCE:** `IMAGE-VERIFICATION-RULE.md` for complete workflow

### Communication
- ✅ **WhatsApp Gateway** — User messaging (ownclaw gateway)
- ✅ **Discord Bot** — Status updates, logging
- ✅ **Telegram** — Available but not actively used

### Caching & Memory
- ✅ **7-day intelligent cache** — All API calls (Semrush, SERP, images)
- ✅ **Daily memory files** — `memory/YYYY-MM-DD.md`
- ✅ **Media index** — `semantic-engine/images/media-index.json`

---

## 📋 Publishing SOP (ALL Blog Posts — ALL Sites)

**MANDATORY STEPS — NO EXCEPTIONS:**

**APPLIES TO:** Rank Ray, Tonic Physio, Team Motorcycle, Khan LLP, Coinsfera — **ALL sites, ALL blog posts, ALL pages, FOREVER**

### Before Publishing:
1. ✅ **Featured Image**
   - Source: Unsplash/Pexels/Pixabay (free, commercial use)
   - Filename: keyword-based, NO "featured image" in name
   - Upload: WordPress media library via REST API
   - Alt text: Descriptive with natural keyword variation
   - Dimensions: 1200x630px (landscape)

2. ✅ **Body Images** (1 per ~500 words)
   - For 5000 words: 10 images minimum
   - One image per main H2 heading
   - **CRITICAL: Source from Unsplash/Pexels/Pixabay (external)**
   - **NEVER reuse existing images from ANY site's media library** (Rank Ray, Tonic Physio, Team Motorcycle, Khan LLP, Coinsfera)
   - **ALWAYS download fresh images for new articles** (ALL sites)
   - Filenames: keyword-based, descriptive
   - Alt text: Mandatory for every image
   - Duplicate check: Check external sources, NOT internal library (we want unique images)
   - Upload: WordPress media library (NEVER hotlink)

3. ✅ **Content Quality**
   - Word count: 2500-5000+ (pillar content)
   - No double dashes anywhere
   - No emojis in content
   - H1/H2/H3 proper hierarchy
   - 5-10 internal links (verified URLs from sitemap)
   - 5-7 FAQs with clear answers

4. ✅ **Yoast SEO**
   - Focus keyphrase: Set
   - Meta title: <60 characters
   - Meta description: <160 chars with keyword + LSI + brand
   - SEO analysis: Green/good status

5. ✅ **URL/Slug**
   - NO '-draft' in permalink
   - Clean, keyword-based slug
   - Proper category (if applicable)

6. ✅ **Publish Status**
   - Create as DRAFT (not live publish)
   - User review before going live

**NEVER** (ALL 5 SITES — UNIVERSAL RULE):
- Publish without all images uploaded and connected
- Hotlink images from external sources
- **Reuse existing media library images for new articles** (must source fresh from Unsplash/Pexels/Pixabay) — **ALL SITES, NO EXCEPTIONS**
- **Skip downloading and re-uploading** (every new article gets unique images) — **ALL SITES**
- Use "featured image" in filenames or alt text
- Publish live without user approval

**UNIVERSAL APPLICATION:** Rank Ray, Tonic Physio, Team Motorcycle, Khan LLP, Coinsfera — **EVERY SITE, EVERY BLOG POST, EVERY PAGE, FOREVER**

---

## 🧠 Agent Topology

| Agent | Role | Specialization | Can Spawn |
|-------|------|----------------|-----------|
| **main** (Ranki) | Coordinator | Strategy, user communication, routing | enigma, chronos, researcher |
| **enigma** | SEO Specialist | Content writing, audits, on-page SEO | junior SEO specialists (for large audits) |
| **chronos** | DevOps/Dev | WordPress, APIs, infrastructure, automation | frontend/backend specialists |
| **researcher** | Market Intel | SERP analysis, keywords, competitors | none (pure research) |

**Spawn Rules:**
- Content writing → enigma
- Technical audits → enigma (chaos merged)
- Local SEO → enigma (localseo merged)
- Development → chronos
- Research → researcher
- Coordination → main (never spawn for coordination tasks)

---

## 📊 Project Status Dashboard

### Rank Ray (rankray.com)
- **Priority:** High (agency site)
- **Focus:** Semantic SEO services, agentic SEO
- **Schedule:** 20:00 PKT daily automation
- **Status:** Pillar content generation in progress

### Tonic Physio (tonicphysio.com)
- **Priority:** High (client lead: Fawad)
- **Focus:** Service pages (34 total, 6 completed)
- **Schedule:** 10:00 PKT daily automation
- **Status:** Sequential REST API updates, ACF field mapping

### Team Motorcycle (teammotorcycle.com)
- **Priority:** Medium (ecommerce)
- **Focus:** Category pages, product optimization
- **Schedule:** 09:00 PKT daily automation
- **Status:** Pending initial audit

### Khan LLP (khanllp.com)
- **Priority:** Medium (law firm)
- **Focus:** Practice area pages, local SEO
- **Schedule:** 11:00 PKT daily automation
- **Status:** Pending initial audit

### Coinsfera (coinsfera.com)
- **Priority:** Medium (crypto OTC)
- **Focus:** Service pages, local Istanbul SEO
- **Schedule:** 11:00 PKT daily automation
- **Status:** Pending initial audit

---

## 🔐 Credentials & Access

**Stored in Environment Variables (.env):**
- WordPress app passwords (all sites)
- Semrush API key (50,000 units)
- Discord bot token
- WhatsApp gateway token

**NEVER store in:**
- MEMORY.md
- Daily memory files
- Chat messages
- Public repositories

**Access:**
- Authorized users: +923335261658 (Own), +923355973143 (Tahir), +923349570172 (Fawad)
- All 3 have full access, treat as separate individuals

---

## 📝 Memory System

### Hierarchy:
1. **MASTER-RULES.md** (this file) — Global rules, tool integrations, SOPs
2. **TOOL-INTEGRATIONS.md** — Detailed tool documentation
3. **MEMORY.md** — Curated long-term memory (owner info, preferences, critical decisions)
4. **memory/YYYY-MM-DD.md** — Daily activity logs (append-only)
5. **Project-specific memory** — `memory/project-{name}.md`

### Update Rules:
- **MASTER-RULES.md:** Update when new tools integrated, SOPs changed, architecture modified
- **MEMORY.md:** Update for owner preferences, critical decisions, long-term context
- **Daily files:** Append-only during daily heartbeat
- **All agents:** Must read MASTER-RULES.md before task execution

---

## 🚨 Error Handling & Escalation

### Tool Failures:
1. Retry once with exponential backoff
2. Log to Discord #claw-status
3. Log to daily memory file
4. If critical: Notify user via WhatsApp
5. If non-critical: Continue with fallback/alternative

### API Rate Limits:
- Respect Retry-After headers
- Implement 7-day caching (mandatory for all API calls)
- Batch requests when possible
- Never exceed documented limits

### User Escalation:
**Notify user immediately for:**
- Production site errors (500s, downtime)
- Data loss or corruption
- Security issues
- Budget overruns (paid APIs)
- Tasks blocked >24 hours

**Do NOT notify for:**
- Routine task completions (log to Discord)
- Minor delays (<1 hour)
- Cached data usage (working as designed)
- Subagent spawning (automatic)

---

## 📈 Performance Standards

### Response Times:
- WhatsApp messages: <30 seconds
- Discord status updates: Real-time (as tasks complete)
- Email (if enabled): <5 minutes

### Task Completion:
- Simple queries: <10 seconds
- Research tasks: <5 minutes
- Content generation: <15 minutes
- WordPress publishing: <10 minutes (including images)

### Quality Metrics:
- Cache hit rate: >70% (target 90%)
- API unit efficiency: 7-day TTL mandatory
- Content quality: Yoast green, 2500+ words, 10+ images
- Error rate: <5% of tasks

---

## 🔄 Update Protocol

**When to Update This File:**
1. New tool integration (e.g., Firecrawl added)
2. SOP changes (e.g., new publishing requirements)
3. Agent topology changes (new agents, merged capabilities)
4. Credential/access changes
5. Performance standard updates

**How to Update:**
1. Edit this file
2. Update "Last Updated" timestamp
3. Increment version number
4. Log change in daily memory file
5. Announce on Discord #claw-status
6. Notify user via WhatsApp if critical

**After Update:**
- All agents must re-read before next task
- Subagents inherit updated context automatically
- Document in next daily memory entry

---

## ✅ Acknowledgment

**All agents must acknowledge this file before execution:**

```
[AGENT_NAME] acknowledges MASTER-RULES.md v1.1 (2026-04-21) — UNIVERSAL SEO RULES
Tools verified: [list]
SOPs verified: [list]
Ready to execute: [task_name]
```

**Failure to acknowledge = Do not execute**

---

## 📞 Contact & Support

**System Owner:** Own-ur-Rehman Sheikh (+923335261658)  
**Technical Lead:** Tahir Rasheed (+923355973143)  
**Operations:** Fawad Ahmed (+923349570172)

**Discord:** https://discord.com/invite/clawd  
**Documentation:** /Users/sheikhown/.openclaw/workspace/docs  
**GitHub:** https://github.com/openclaw/openclaw

---

**END OF MASTER RULES**

*This file is the single source of truth. When in doubt, consult this file first.*
*THESE RULES APPLY TO ALL 5 SITES — RANK RAY, TONIC PHYSIO, TEAM MOTORCYCLE, KHAN LLP, COINSFERA — EVERY SITE, EVERY BLOG POST, EVERY PAGE, FOREVER*
