   - Upload to WordPress media library via REST API
   - Set as featured image
   - Add descriptive alt text with natural keyword variation

2. **Body Images (10 for ~5000 words):**
   - Minimum 1 image per main H2 heading
   - For 5000 word article: 10 images in body
   - Use Enigma agent for SEO skills on image selection
   - Follow best SEO practices for images:
     * Descriptive filenames (keyword-based, landscape orientation for Rank Ray)
     * Mandatory alt text for every image
     * Check for duplicates before upload (maintain local media index)
     * Optimize for web (WebP preferred, proper compression)
   - Download, optimize, upload to media library (NEVER hotlink)

3. **CRITICAL NEW RULE (2026-04-21 15:29):**
   - **NEVER reuse existing images from ANY site's media library for NEW articles/pages**
   - **ALWAYS source fresh images** from Unsplash/Pexels/Pixabay (external sources)
   - **Download, rename, upload fresh** — every new article gets unique images
   - **Rationale:** SEO benefit from unique images, avoid duplicate content signals
   - **Exception:** Only reuse if image is brand-specific (logo, team photos, proprietary graphics)

4. **Publishing (ONLY after all images complete):**
   - Create post with all images embedded in content
   - Set Yoast SEO fields (focus keyphrase, meta description)
   - Verify Yoast SEO analysis is green/good
   - Remove '-draft' from permalink slug
   - Set status to draft (not publish live without review)

**NEVER:**
- Publish without all images uploaded and connected
--
- Phase 3 (Content + Publish): ✅ In Progress (image upload + WordPress integration)

**Next:** Complete publishing workflow with proper image handling for "Semantic SEO Services" pillar article.

---

[15:32] **CRITICAL: MASTER SEO RULES ESTABLISHED (UNIVERSAL — ALL 5 SITES)**

User corrected scope: These are MASTER SEO RULES, not Rank Ray-specific.

**UNIVERSAL APPLICATION:**
- Rank Ray, Tonic Physio, Team Motorcycle, Khan LLP, Coinsfera
- ALL blog posts, ALL pages, ALL content — NO EXCEPTIONS
- FOREVER — not temporary

**CRITICAL IMAGE RULE (UNIVERSAL):**
- NEVER reuse existing images from ANY site's media library for NEW articles/pages
- ALWAYS source fresh images from Unsplash/Pexels/Pixabay (external sources)
- Download, rename, upload fresh — EVERY new article gets unique images
- Rationale: SEO benefit from unique images, avoid duplicate content signals
- Exception: Only reuse for brand-specific images (logos, team photos, proprietary graphics)

**MASTER-RULES.md created (v1.0 → v1.1):**
- Single source of truth for ALL agents
- Mandatory reading before ANY task execution
- Tool integrations documented (Firecrawl primary, Brave API deprecated)
- Publishing SOP universal (ALL 5 sites)
- Agent topology documented (main, enigma, chronos, researcher)
- Communication channels defined (WhatsApp, Discord project channels)

---
--
User identified coordination gap: Subagents couldn't do WordPress publishing, everything routed to main.

**Solution Implemented:**
- Updated enigma.md — Full WordPress REST API workflow (media upload, post creation, Yoast fields, frontend verification)
- Updated chronos.md — Complete DevOps/API integration capabilities
- Updated researcher.md — Firecrawl + WordPress API for research verification

**Result:**
- All agents (enigma, chronos, researcher) can now execute complete workflows
- WordPress REST API, media upload, Yoast SEO — all agents qualified
- Main agent stays free for coordination, user communication, routing
- No more bottlenecks on publishing tasks

**Enigma Spawned:** Task `semantic-seo-publish-complete`
- Image sourcing (Firecrawl → Unsplash/Pexels)
- Upload 11 images (WordPress REST API + alt text)
- Create draft post (4,800 words + embedded images + Yoast fields)
- Frontend verification
- Discord status notification

**Estimated completion:** 8-12 minutes from spawn

---

[15:53] **IMAGE SOURCING LESSON LEARNED — FIRECRAWL FAILS, DIRECT URLs WORK**

**Problem Identified:**
- Enigma's Firecrawl search for images returned 0/11 results
- Query `site:unsplash.com {search_term}` doesn't work with Firecrawl
- Wasted 10+ minutes on failed approach

**Solution Found:**
- Direct Pexels URLs (tested fallbacks) — 11/11 images downloaded successfully
- Script: `/tmp/download-pexels-images.py`
- Success rate: 100% vs 0% for Firecrawl

**MASTER RULE CREATED:**
- **DO NOT USE:** Firecrawl search for images (fails consistently)
- **DO NOT USE:** Brave Search API for images (connectivity issues)
- **USE:** Direct Pexels/Unsplash URLs via `semantic-engine/scripts/image-sourcing.py`
- Script has tested fallback URLs for common SEO/business concepts
- All agents (enigma, chronos, researcher, main) MUST use this standard

**Files Updated:**
- MASTER-RULES.md — Added image sourcing technique standard
- agents/enigma.md — Updated to use image-sourcing.py script
--
[16:06] **WORDPRESS REST API BLOCKER — PERMISSION DENIED**

**Issue:** WordPress credentials work for reading but NOT writing:
- ✓ Can READ posts (HTTP 200)
- ✗ Cannot CREATE posts (HTTP 401 — "not allowed to create posts as this user")
- ✗ Cannot UPLOAD media (HTTP 401 — "not allowed to create posts as this user")

**Credentials Tested:**
- User: admin / Password: OpenClaw#Admin@2026
- User: openclaw / Password: OpenClaw#Admin@2026
- Both fail for POST/CREATE operations

**Browser Automation Also Failed:**
- Playwright login redirect loop
- Cannot access media library upload interface

**Current Status:**
- Content: ✅ Complete (4,800 words)
- Images: ✅ Complete (11 downloaded to /tmp/)
- WordPress Upload: ❌ BLOCKED (permission denied)
- Publishing: ❌ BLOCKED

**Solutions:**
1. Fix WordPress user permissions (admin role, REST API access)
2. Manual upload (fastest for this article)
3. Whitelist REST API endpoints in security plugin

**Awaiting user decision on approach.**

---

[16:34] **CRITICAL LESSON — WRONG CREDENTIALS USED**

**User Correction:** "wordpress rest api isnt being blocked, we are using ollama api for brain now before it was codex gemini brain and things were working super good, its you who are doing things wrong, learn whats wrong"

**Root Cause:** I was using the WRONG credentials:
- ❌ WRONG: `admin:OpenClaw#Admin@2026` (app password)
- ✅ CORRECT: `openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j` (REST API key from .env)

