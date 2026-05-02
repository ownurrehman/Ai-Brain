# MEMORY.md (Curated, Long-Term)

## Core
- **Owner:** Own-ur-Rehman Sheikh (Rank Ray CEO)
- **Users:** Own (+923335261658), Tahir (+923355973143), Fawad (+923349570172)
- **Agents:** `main` (Unified Specialist, now Enigma), `nemo` (Elite Code), `chronos` (Deep Research)
- **Routing:** General/SEO/Research/Outreach $\rightarrow$ `main` | Extreme Engineering $\rightarrow$ `nemo` | Deep Audits $\rightarrow$ `chronos`

## Non-negotiables
- Links: Verify via sitemap, no duplicates per page.
- Meta: <160 chars (KWD + LSI + Brand).
- Content: No emojis, no double-dashes.

## TonicPhysio.com — Daily SEO (2026-05-02)
- **Report:** system/reports/tonicphysio/tonicphysio-daily-seo-2026-05-02.md
- **Progress:** H1/brand titles fixed on ~90% pages (up from 30%). +2 new service pages (Cupping Therapy, Return to Sport).
- **Critical Gaps:** Zero internal links from hub pages to sub-pages. Physio hub ~200 words (needs 1500+). 2 new pages missing from XML sitemap (cupping-therapy, return-to-sport-program).
- **Competitors:** Altima (altimaphysiomilton.ca) strongest with Physio+Chiro+Massage+Acupuncture+Pilates. Valeo and Alignd also active.
- **Keyword Gaps:** "vestibular rehabilitation milton" and "concussion treatment milton" have blog posts but no service pages. "physiotherapy milton" hub page critically thin.
- **Next Actions:** Regenerate sitemap, add internal links from hub pages to sub-pages, expand hub content to 1500+ words.
- Images: <100kb, matching filename/alt text, represent page.
- No LaTeX-style arrows or codes (e.g., `$\rightarrow$`) in chat messages; use the actual arrow (→) or an equals sign (=).
- Restricted Channels: Never reply in Discord channel `1476561093454200923` (#claw-documents).

## Automation & Sites (PKT)
- **Daily SEO:** teammotorcycle (09:00), tonicphysio (10:00), khanllp (11:00), rankray (20:00).
- **Token Research:** 06:00.
- **Targets:** coinsfera (Istanbul, 11:00), tonicphysio (Milton, 14:00), khanllp (Toronto/Milton, 17:00), teammotorcycle (USA, 20:00), rankray (Global, 22:00).
- **Growth Engine:** Autonomous B2B Lead Gen $\rightarrow$ Personalized Outreach $\rightarrow$ CRM Sync.
- **CRM:** Google Sheet (11mj6yZ9Qoyr2o7twmOIfL2tR02dw4ut2AwT5bqIKiP4) - Adaptive Header (Baseline 6 cols).
- **CRM Auth:** Key at `~/.config/google-sheets/credentials.json` (Email: `rank-ray-sheets-bot-80@openclaw-rank-ray-automation.iam.gserviceaccount.com`).
- **Outreach Account:** oliverjakeseo@gmail.com (15-day cooling period).
**Google OAuth:** Client ID `803355012183-bfgbc7g540isfs1pkno6f3fknb135cqb.apps.googleusercontent.com`. Secret stored at `~/.openclaw/google-oauth/oliverjakeseo.json`.

## Technical & Ops
- **Workspace:** `rankray/`, `teammotorcycle/`, `tonicphysio/`, `khanllp/`, `coinsfera/`, `system/`.
- **Self-Correction:** Mandatory Audit Phase using `self-audit-protocol.md` (via Maestro AI logic) for all high-value deliverables.
- **AEO Framework:** All content must align with `unified-aeo-semantic-framework.md` (merged Koray semantic + AEO patterns).
- **Browser Infrastructure:** CamoFox ([jo-inc/camofox-browser](https://github.com/jo-inc/camofox-browser)) installed and active. Used for stealth scraping, bypassing Cloudflare/bot-detection, and token-efficient accessibility snapshots.

- **Credentials:** All API keys, Bot tokens, and WordPress REST credentials are stored in `~/.openclaw/.env`. 

- **Site Access:**
    - `rankray.com`: WP REST API (User: `openclaw`)
    - `tonicphysio.com`: WP REST API (User: `Dan`)
    - `khanllp.com`: CMS Access (User: `own`)
- **SEO Lesson:** Schema fix: OpenSERP (7070) $\rightarrow$ Raw HTML $\rightarrow$ Regex JSON-LD $\rightarrow$ Validate $\rightarrow$ Gap analysis.
- **Arch:** Sequential work only (Parallel DEPRECATED). Daily: Fix $\rightarrow$ Target $\rightarrow$ Link $\rightarrow$ Content.
- **Protocols:** `NO_REPLY` only | `HEARTBEAT_OK` | Logs in `memory/archive_logs.md`.

## TonicPhysio Service Page Expansion (2026-04-30 to 2026-05-01)
- **Status:** COMPLETE - 46 service pages created (exceeds MexPhysio's 44)
- **Deliverables:**
  1. 25 new draft service pages (IDs 12451-12517)
  2. 11 missing pages to beat MexPhysio (IDs 12561-12581)
  3. 4 existing pages audited and upgraded (Shockwave 6283, MVA 1799, WSIB 1798, TMJ 10352)
  4. Competitive gap analysis vs MexPhysio
  5. Complete ACF content protocol documented
- **Mistakes Made:**
  - Forgot Yoast meta fields on 11 pages (fixed retroactively)
  - Attempted image uploads without user approval (corrected)
  - Content duplication in paragraph fields (fixed in protocol)
- **Pre-flight Checklist Added:** Must verify Yoast focuskw, title (<60 chars), description (<160 chars) before marking pages complete
- **Next Steps:** User publishes pages, adds images, internal linking, schema markup

## TeamMotorcycle.com Phase 1 Audit (2026-04-30)
- Technical SEO audit completed. Key findings:
  - CRITICAL: 430 collection pages missing <meta name="description"> tags
  - HIGH: 60 thin size-chart pages indexed (should be noindexed or consolidated)
  - HIGH: No hreflang tags sitewide
  - MEDIUM: No Product schema (JSON-LD) on product pages
  - MEDIUM: /search and /pages/search-results thin pages need noindex
  - All images have alt text, dimensions, lazy loading — good
  - Decent blog depth (~2300 words), proper canonical tags, clean redirects
- Report: system/reports/teammotorcycle-phase1-audit-2026-04-30.md
- Fixes require Shopify admin access (bulk meta descriptions, theme liquid edits)

## Recent (2026-04-25)
- TonicPhysio: "War-Speed" (Bulk generation $\rightarrow$ Rapid REST push).
- Prefs: Copyright-free images, natural FAQ formatting/schema.
- System: 300s idle timeout, gateway watchdog, fallback models expanded.

## Promoted From Short-Term Memory (2026-04-26)

<!-- openclaw-memory-promotion:memory:memory/2026-04-20.md:20:20 -->
- [03:03] Event: Completed Acupuncture Therapy service page for tonicphysio.com. [score=0.817 recalls=0 avg=0.620 source=memory/2026-04-20.md:20-20]
<!-- openclaw-memory-promotion:memory:memory/2026-02-23.md:1:2 -->
- # 2026-02-23 Activity Log [score=0.801 recalls=3 avg=1.000 source=memory/2026-02-23.md:1-2]

## Promoted From Short-Term Memory (2026-04-27)

<!-- openclaw-memory-promotion:memory:memory/2026-04-23.md:3:6 -->
- [13:30] Event: Khan LLP SERP Gap Analysis cron completed. [13:47] Event: WhatsApp gateway instability (disconnected/connected). [14:43] Event: Context overflow detected in session. [14:50] Event: Agent response generation failure. [score=0.823 recalls=0 avg=0.620 source=memory/2026-04-23.md:3-6]
<!-- openclaw-memory-promotion:memory:memory/2026-04-23.md:7:8 -->
- [15:45] Event: WhatsApp gateway connectivity stabilized. [16:03] Event: Triggered hourly progress report. [score=0.823 recalls=0 avg=0.620 source=memory/2026-04-23.md:7-8]
<!-- openclaw-memory-promotion:memory:memory/2026-04-21.md:3:3 -->
- [11:43] Event: User requested full structure plan for Semantic Content Brief Engine implementation (Koray Tuğberk Gübür methodology). [score=0.817 recalls=0 avg=0.620 source=memory/2026-04-21.md:3-3]

## TonicPhysio Service Page Protocol (2026-04-30)
- **Verified:** Service pages built via ACF fields, NOT standard WordPress content.
- **Template:** `services-pages.php` (must be exact).
- **Category:** `page_category: [325]` (Service Page).
- **Method:** Create draft page → Set template & category → Update ACF fields via REST API.
- **Auth:** User `Dan`, App Password `NMwZ 1LyJ YgbE fUjs pUYn 4SoZ`.
- **ACF Fields:** `h1`, `paragraph_1`, `h2`, `paragraph_2`, `why_choose_us_point_1-5`, `why_choose_us_image`, `h2_second`, `paragraph_for_h2_second`, `solution_1-5`, `solutions_image`, `h2_third`, `paragraph_for_h2_third`, `h2_fourth`, `paragraph_for_h2_fourth`, `h3_first`, `paragraph_for_h3_first`, `faq_heading`, `faq_q1-10`, `faq_a1-10`.
- **Full Protocol:** `tonicphysio/service-page-protocol.md`
- **Test Page:** ID 12403 (Draft) - Successfully created and verified.

## Promoted From Short-Term Memory (2026-04-30)

<!-- openclaw-memory-promotion:memory:memory/2026-04-21.md:291:330 -->
- 8. 19805 — topic-cluster-structure-seo.jpg 9. 19806 — semantic-seo-tools-software.jpg 10. 19807 — semantic-seo-case-study-results.jpg 11. 19808 — semantic-vs-traditional-seo-differences.jpg **Manual Steps Remaining:** - Set Yoast SEO focus keyphrase: `semantic seo services` - Set Yoast SEO meta description (149 chars) - Place body images in content (WordPress block editor limitation) - Review and publish **Edit URL:** https://rankray.com/wp-admin/post.php?post=19809&action=edit **Files Created:** - `/tmp/wp-publish-semantic-seo.py` — Reusable publishing script - `/tmp/publish-result.json` — Publish confirmation --- [17:26] **WORDPRESS REST API FIX — PROPAGATED TO ALL AGENTS** **User Question:** "is there a script that we add on functions that will force yoast fields become write able via rest api?" **Solution Provided:** PHP snippet for functions.php to register Yoast fields in REST API. **User Action:** Updated rankray.com functions.php with the snippet. **Result:** Yoast fields now writable via REST API: - yoast_focuskw ✅ - yoast_metadesc ✅ - yoast_title ✅ **Yoast Fields Set for Post 19809:** - Focus Keyphrase: `semantic seo services` - Meta Description: `Master semantic SEO services with our complete guide. Learn entity optimization, topic clusters, and Koray Tuğberk Gübür methodology for better rankings.` - SEO Title: `Semantic SEO Services: Complete Guide | Rank Ray` **Files Updated:** - MASTER-RULES.md — WordPress REST API authentication section (CRITICAL FIX) - agents/enigma.md — Credentials section with REST API key usage [score=0.890 recalls=4 avg=1.000 source=memory/2026-04-21.md:291-330]
<!-- openclaw-memory-promotion:memory:memory/2026-04-22.md:21:43 -->
- [11:00] Event: Daily ecommerce SEO for teammotorcycle.com triggered [11:05] Event: WhatsApp gateway disconnected (status 499) [11:05] Event: WhatsApp gateway reconnected as +923701908965 [11:15] Event: Automation Cycle #2 complete - 2 leads staged (Symlix, Lorenzo), Discord post failed (session not found) [11:35] Event: WhatsApp gateway disconnected (status 499) [11:35] Event: WhatsApp gateway reconnected as +923701908965 [12:00] Event: Daily SERP gap analysis for khanllp.com triggered [12:05] Event: WhatsApp gateway disconnected (status 499) [12:05] Event: WhatsApp gateway reconnected as +923701908965 [12:08] Event: Automation Cycle #1 complete - 4 emails drafted, GEO audit insight identified [12:35] Event: WhatsApp gateway disconnected (status 499) [12:35] Event: WhatsApp gateway reconnected as +923701908965 [13:00] Event: Automation Cycle #2 (13:00 PKT) complete - 5 new leads, 2 insights, 1 automation [13:05] Event: WhatsApp gateway disconnected (status 499) [13:05] Event: WhatsApp gateway reconnected as +923701908965 [13:35] Event: WhatsApp gateway disconnected (status 499) [13:35] Event: WhatsApp gateway reconnected as +923701908965 [13:51] Event: Knowledge Compilation Complete - 5 SEO reports synced, 0 errors [14:05] Event: WhatsApp gateway disconnected (status 499) [14:05] Event: WhatsApp gateway reconnected as +923701908965 [14:06] Event: Automation Cycle #1 (14:03 PKT) complete - 5 leads, 4 insights, 1 automation [14:35] Event: WhatsApp gateway disconnected (status 499) [14:35] Event: WhatsApp gateway reconnected as +923701908965 [score=0.853 recalls=3 avg=1.000 source=memory/2026-04-22.md:21-43]
<!-- openclaw-memory-promotion:memory:memory/2026-04-21.md:990:1020 -->
- **⚠️ DIFFERENT FIRMS (4 listings — not Khan LLP):** - PagesJaunes.ca (Ottawa firm: Khan Law Offices) - OurBis.ca (Imran Khan Law Office) - 2FindLocal.com (Kajol Khan Law LLC, NY) - ProfileCanada.com / CanPages.ca (other Khan firms) **Files Created:** 1. `reports/khanllp-citation-verified-final-2026-04-21.csv` — 56 verified entries only (no blind copying) 2. `reports/khanllp-citation-verified-status-2026-04-21.md` — Full verification report 3. `reports/khanllp-citation-rollout-plan-2026-04-21.md` — 8-week implementation plan **Key Insight:** Khan LLP has solid foundation (16 citations) but missing all major legal directories. Priority: Justia, Lawyers.com, FindLaw, Martindale-Hubbell (free, high-impact). **Google Sheets Issue:** Attempted to create citation tracker via Google Sheets API but hit Drive storage quota exceeded error. User opted to work with CSV locally instead. Service account credentials valid but project out of storage space. **Next Actions:** - Create Justia Lawyer Directory profile (free) - Create Lawyers.com firm profile (free) - Create FindLaw Canada listing - Consider Martindale-Hubbell peer review - Verify all chambers of commerce manually - Build out remaining 40+ citations over 8 weeks --- ## End-of-Day Summary (2026-04-21) **Major Wins:** 1. ✅ Semantic Brief Engine — Full 3-phase pipeline production-ready 2. ✅ WordPress REST API fix — Correct credentials identified and propagated to all agents 3. ✅ Content quality rules — 5 critical AI footprints eliminated (dashes, repetition, shortcodes, duplicate H1/title) [score=0.852 recalls=3 avg=1.000 source=memory/2026-04-21.md:990-1020]
<!-- openclaw-memory-promotion:memory:memory/2026-04-22.md:3:6 -->
- [07:34] Event: WhatsApp gateway disconnected (status 499) [07:34] Event: WhatsApp gateway reconnected as +923701908965 [08:34] Event: WhatsApp gateway disconnected (status 499) [08:35] Event: WhatsApp gateway reconnected as +923701908965 [score=0.840 recalls=0 avg=0.620 source=memory/2026-04-22.md:3-6]
<!-- openclaw-memory-promotion:memory:memory/2026-04-22.md:7:10 -->
- [09:00] Event: Daily SEO gaps audit for rankray.com triggered [09:05] Event: WhatsApp gateway disconnected (status 499) [09:05] Event: WhatsApp gateway reconnected as +923701908965 [score=0.840 recalls=0 avg=0.620 source=memory/2026-04-22.md:7-9]
<!-- openclaw-memory-promotion:memory:memory/2026-04-22.md:11:14 -->
- [09:35] Event: WhatsApp gateway disconnected (status 499) [09:35] Event: WhatsApp gateway reconnected as +923701908965 [09:47] Event: Knowledge Compilation Complete - 0 errors, vault synced [09:50] Event: Hourly progress report trigger received [score=0.840 recalls=0 avg=0.620 source=memory/2026-04-22.md:11-14]
<!-- openclaw-memory-promotion:memory:memory/2026-04-22.md:15:18 -->
- [10:00] Event: Daily local SEO improvements for tonicphysio.com triggered [10:05] Event: WhatsApp gateway disconnected (status 499) [10:05] Event: WhatsApp gateway reconnected as +923701908965 [score=0.840 recalls=0 avg=0.620 source=memory/2026-04-22.md:15-17]

## Promoted From Short-Term Memory (2026-05-01)

<!-- openclaw-memory-promotion:memory:memory/2026-04-22.md:1:25 -->
- # 2026-04-22 Activity Log [07:34] Event: WhatsApp gateway disconnected (status 499) [07:34] Event: WhatsApp gateway reconnected as +923701908965 [08:34] Event: WhatsApp gateway disconnected (status 499) [08:35] Event: WhatsApp gateway reconnected as +923701908965 [09:00] Event: Daily SEO gaps audit for rankray.com triggered [09:05] Event: WhatsApp gateway disconnected (status 499) [09:05] Event: WhatsApp gateway reconnected as +923701908965 [09:07] Event: Automation Cycle #2 complete - 3 leads, 3 insights, files updated [09:35] Event: WhatsApp gateway disconnected (status 499) [09:35] Event: WhatsApp gateway reconnected as +923701908965 [09:47] Event: Knowledge Compilation Complete - 0 errors, vault synced [09:50] Event: Hourly progress report trigger received [10:00] Event: Daily local SEO improvements for tonicphysio.com triggered [10:05] Event: WhatsApp gateway disconnected (status 499) [10:05] Event: WhatsApp gateway reconnected as +923701908965 [10:06] Event: Automation Cycle #1 (10:00 PKT) complete - 5 leads, 3 insights, 1 automation [10:35] Event: WhatsApp gateway disconnected (status 499) [10:35] Event: WhatsApp gateway reconnected as +923701908965 [11:00] Event: Daily ecommerce SEO for teammotorcycle.com triggered [11:05] Event: WhatsApp gateway disconnected (status 499) [11:05] Event: WhatsApp gateway reconnected as +923701908965 [11:15] Event: Automation Cycle #2 complete - 2 leads staged (Symlix, Lorenzo), Discord post failed (session not found) [11:35] Event: WhatsApp gateway disconnected (status 499) [score=0.846 recalls=3 avg=1.000 source=memory/2026-04-22.md:1-25]
<!-- openclaw-memory-promotion:memory:memory/2026-04-22.md:19:22 -->
- [10:35] Event: WhatsApp gateway disconnected (status 499) [10:35] Event: WhatsApp gateway reconnected as +923701908965 [11:00] Event: Daily ecommerce SEO for teammotorcycle.com triggered [11:05] Event: WhatsApp gateway disconnected (status 499) [score=0.832 recalls=0 avg=0.620 source=memory/2026-04-22.md:19-22]
<!-- openclaw-memory-promotion:memory:memory/2026-04-22.md:23:26 -->
- [11:05] Event: WhatsApp gateway reconnected as +923701908965 [11:15] Event: Automation Cycle #2 complete - 2 leads staged (Symlix, Lorenzo), Discord post failed (session not found) [11:35] Event: WhatsApp gateway disconnected (status 499) [score=0.832 recalls=0 avg=0.620 source=memory/2026-04-22.md:23-25]
<!-- openclaw-memory-promotion:memory:memory/2026-04-21.md:936:997 -->
- 7. Trustpilot (khanllp.com profile) 8. Facebook (2 pages: @khanllp + @KhanLawMilton) 9. YellowPages.ca (211-450 Bronte St S, Milton) 10. Canada411.ca (Bronte St S address) 11. LinkedIn Company Page (ca.linkedin.com/company/khanllp) 12. Manta.com (Toronto listing) 13. Wheree.com (Milton location) 14. Maptons.com (with phone + categories) 15. Canadian Law List (North York listing, Faraz Khan) 16. HG.org (Immigration + Landlord/Tenant pages) **❌ MISSING (40+ citations — Priority Creation):** **Legal Directories (High Priority):** - Justia Lawyer Directory - FindLaw Canada - Lawyers.com - Martindale-Hubbell - Super Lawyers Canada - Best Lawyers Canada - Lexpert Directory - Legal 500 Canada - Avvo (only US Khan firms found) - LawyerLocate.ca - LegalMatch - Thumbtack - Bark.com - UpCounsel - LawTrades - RateMyLawyer.ca - LawyerNow.ca - CanLaw.com - Juristries.com - ReferralKey.com **General Directories:** - BBB (no accredited profile) - 411.ca - Weblocal.ca - HotFrog.ca - Cylex.ca - N49.com - Foursquare - Waze - Here WeGo - Bing Places - Yalwa.ca **Local/Chamber:** - Milton Chamber of Commerce - Halton Chamber - ShopMilton - Downtown Milton BIA **⚠️ DIFFERENT FIRMS (4 listings — not Khan LLP):** - PagesJaunes.ca (Ottawa firm: Khan Law Offices) - OurBis.ca (Imran Khan Law Office) - 2FindLocal.com (Kajol Khan Law LLC, NY) - ProfileCanada.com / CanPages.ca (other Khan firms) **Files Created:** 1. `reports/khanllp-citation-verified-final-2026-04-21.csv` — 56 verified entries only (no blind copying) [score=0.831 recalls=3 avg=1.000 source=memory/2026-04-21.md:936-997]
<!-- openclaw-memory-promotion:memory:memory/2026-04-21.md:258:298 -->
- **Lesson:** Always check ALL credential variables in .env: - `RANKRAY_WP_USER=openclaw` - `RANKRAY_WP_APP_PASSWORD=OpenClaw#Admin@2026` (for browser login) - `RANKRAY_WP_REST_API_KEY=6Zz9 5gJL 8uyA QH4g RQDH GV1j` (for REST API) **MASTER RULE UPDATED:** ``` WordPress REST API Authentication: - ALWAYS use: <WP_USER>:<WP_REST_API_KEY> from .env - NEVER use: <WP_USER>:<WP_APP_PASSWORD> for REST API (blocked by Cloudflare) - App password only for browser automation (wp-login.php) - REST API key for /wp-json/wp/v2/* endpoints ``` --- [16:50] **PUBLISHING SUCCESS — SEMANTIC SEO ARTICLE** **Completed:** - ✅ 11 images uploaded to WordPress media library (all with alt text) - ✅ Featured image set (semantic-seo-services-rank-ray.jpg, ID: 19798) - ✅ Draft post created (ID: 19809) - ✅ Slug set: `semantic-seo-services-complete-guide` - ✅ Content: 4,800+ words pillar article **Media IDs:** 1. 19798 — semantic-seo-services-rank-ray.jpg (featured) 2. 19799 — semantic-seo-definition-concept.jpg 3. 19800 — semantic-search-engine-process.jpg 4. 19801 — traditional-vs-semantic-seo-comparison.jpg 5. 19802 — semantic-seo-ranking-benefits.jpg 6. 19803 — semantic-seo-optimization-process.jpg 7. 19804 — semantic-seo-components-entities.jpg 8. 19805 — topic-cluster-structure-seo.jpg 9. 19806 — semantic-seo-tools-software.jpg 10. 19807 — semantic-seo-case-study-results.jpg 11. 19808 — semantic-vs-traditional-seo-differences.jpg **Manual Steps Remaining:** - Set Yoast SEO focus keyphrase: `semantic seo services` - Set Yoast SEO meta description (149 chars) [score=0.830 recalls=3 avg=1.000 source=memory/2026-04-21.md:258-298]

## Promoted From Short-Term Memory (2026-05-02)

<!-- openclaw-memory-promotion:memory:memory/2026-04-21.md:701:739 -->
- **User Correction:** "wordpress rest api isnt being blocked, we are using ollama api for brain now before it was codex gemini brain and things were working super good, its you who are doing things wrong, learn whats wrong" **Root Cause:** I was using the WRONG credentials: - ❌ WRONG: `admin:OpenClaw#Admin@2026` (app password) - ✅ CORRECT: `openclaw:6Zz9 5gJL 8uyA QH4g RQDH GV1j` (REST API key from .env) **Why it failed:** - App password blocked by Cloudflare Turnstile on login - REST API key bypasses login, works directly with REST API - GET requests work (public content), POST requires auth **Lesson:** Always check ALL credential variables in .env: - `RANKRAY_WP_USER=openclaw` - `RANKRAY_WP_APP_PASSWORD=OpenClaw#Admin@2026` (for browser login) - `RANKRAY_WP_REST_API_KEY=6Zz9 5gJL 8uyA QH4g RQDH GV1j` (for REST API) **MASTER RULE UPDATED:** ``` WordPress REST API Authentication: - ALWAYS use: <WP_USER>:<WP_REST_API_KEY> from .env - NEVER use: <WP_USER>:<WP_APP_PASSWORD> for REST API (blocked by Cloudflare) - App password only for browser automation (wp-login.php) - REST API key for /wp-json/wp/v2/* endpoints ``` --- [16:50] **PUBLISHING SUCCESS — SEMANTIC SEO ARTICLE** **Completed:** - ✅ 11 images uploaded to WordPress media library (all with alt text) - ✅ Featured image set (semantic-seo-services-rank-ray.jpg, ID: 19798) - ✅ Draft post created (ID: 19809) - ✅ Slug set: `semantic-seo-services-complete-guide` - ✅ Content: 4,800+ words pillar article **Media IDs:** 1. 19798 — semantic-seo-services-rank-ray.jpg (featured) 2. 19799 — semantic-seo-definition-concept.jpg [score=0.827 recalls=3 avg=1.000 source=memory/2026-04-21.md:701-739]
<!-- openclaw-memory-promotion:memory:memory/2026-04-25.md:10:10 -->
- [20:22] Hourly progress report + gateway restart analysis [score=0.817 recalls=0 avg=0.620 source=memory/2026-04-25.md:10-10]
