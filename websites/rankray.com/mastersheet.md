> **Parent Site:** [[websites/rankray.com/index|🌐 rankray.com Hub]] · [[websites/index|Websites Directory]] · [[INDEX|🧠 Ai Brain]]

# rankray.com : Mastersheet

**URL:** https://rankray.com
**Type:** SEO Agency marketing website (not RankRay HQ SaaS)
**Vault path:** `websites/rankray.com/`
**Status:** Phase 5 : Content Production + Outreach Engine — Active
**Last Updated:** 2026-08-21 (16 blog drafts published, outreach engine operational)

**Env:** `RANKRAY_WP_*` in `master-env.env` — see `docs/ENV.md`

---

## Rules

| Rule | Path |
|------|------|
| Content Writing | `rules/content/semantic-seo-writer.md` |
| Quality Standards | `rules/content/content-rules.md` |
| WordPress API | `rules/access/wordpress-rest-api-setup.md` |
| Post Registry | `websites/rankray.com/post-registry.md` |
| ACF service pages | `websites/rankray.com/knowledge/ACF-SERVICE-PAGE-REFERENCE.md` |
| **No H1 in Body** | **WordPress generates H1 from post title. NEVER place H1 in body.** |
| Not HQ | RankRay HQ code lives in `projects/rankray-hq/` |

---

## Current Status (Live Site Verified 2026-06-01)

| Metric | Value | Status |
|--------|-------|--------|
|| Total Published Posts | 164 | ✅ Live (148 prev + 16 new Aug 21) |
|| Drafts | 0 | ✅ All published |
|| Total Pages | 70 | Active |
|| Location Pages | 38 | Active |
|| Outreach Prospects | 4,584 | UAE businesses scraped |
|| Outreach Emails Sent | 8 | First batch Aug 17 |
|| Outreach Replies | 0 | Monitoring every 3h |

**Domain Authority:** Growing (Brand SERP strong)
**Primary Focus:** SEO Agency Services (Global)
**Secondary Focus:** Digital Marketing, Content Strategy, AI/GEO

---

## Changes Since Last Update

| Date | Action | Result |
|------|--------|--------|
| 2026-08-21 | **16 BLOG POSTS PUBLISHED** (IDs 24048-24066) — All passed 19-point audit: 2000+ words, 5-8 internal links, 2 external links (nofollow to 90+ DA sites), Yoast meta, featured images, correct categories, 0 em-dashes, 0 wp: blocks, 0 markdown, "Rank Ray" branding correct. Categories: SEO Fundamentals, Technical SEO, AI and GEO, SEO Strategy, On-Page SEO. | All 16 live |
| 2026-08-21 | **OUTREACH ENGINE v3 BUILT** — Self-learning email prospecting system at `system/outreach/`. 4,584 UAE prospects scraped from Google Places API. 3-layer email scraper (requests + Firecrawl + curl) + email-sleuth SMTP verification. 5 skill-compliant templates (80-110 words, lowercase subjects, cold_email_check.py validation). Daily 9 AM cron sends 100 emails. Every 3h cron checks replies. Only notifies user on prospect replies. | Operational |
| 2026-08-21 | **email-sleuth INSTALLED** (v1.1.0) — SMTP email verification tool. Verifies found emails exist before sending, reducing bounces. | Installed |
| 2026-08-21 | **INTERNAL LINKS FIXED** — All 16 drafts had only 2-3 internal links. Added contextual links to relevant service pages (SEO, technical SEO, semantic SEO, content marketing, GEO, local SEO, etc.). Final: 6-8 links per post, 0 duplicate URLs. | Fixed |
| 2026-08-21 | **CATEGORIES FIXED** — All 16 drafts were assigned to wrong category (eCommerce SEO 502). Reassigned to correct categories based on topic: SEO Fundamentals (445), Technical SEO (447), AI and GEO (455), SEO Strategy (450), On-Page SEO (446). | Fixed |
| 2026-08-21 | **BRANDING RULE ENFORCED** — "RankRay"/"rankray" in blog content replaced with "Rank Ray" (with space). Hard rule saved to memory. | Fixed |
| 2026-08-21 | **MARKDOWN FORMATTING FIXED** — Post 24058 had raw markdown `[text](url)` displaying as plain text. Converted to proper HTML. Rule saved: always convert markdown to HTML before pushing. | Fixed |
|| 2026-06-03 | **DUBAI DRAFT EXPANDED** (ID 22720) — SEO Company Dubai expanded from 1313w to 2000w. Added link building, local SEO, technical SEO, and content strategy depth. 8 internal links, Key Takeaway block, clean Yoast. | Ready for review |
|| 2026-05-24 | **SLUG COLLISION FIX** — Trashed duplicate `-2` posts (22608, 22597). Created 301 redirects to canonical URLs. All `-2` slugs now clean. | Zero slug collisions |
| 2026-05-24 | **DUBAI DRAFT PUBLISHED** (ID 22720) — Expanded from 667w to 1325w, added Key Takeaway block, comparison table, 8 internal links, Pexels featured image. | Published live |
| 2026-05-24 | **H1 FIX ON 4 SERVICE PAGES** — AI Automation (was "Test"), GEO (was "GEO"), Technical SEO (was "Technical SEO"), Local SEO (was "Local SEO"). Page titles updated to sales copy. | All H1s now sell outcomes |
| 2026-05-24 | **6 REDIRECTS CREATED** for old trashed posts redirecting to relevant service pages | Zero orphaned trashed URLs |
| 2026-05-24 | **BRAIN SYNC** — Updated mastersheet (148 posts, 0 drafts, 70 pages, 11 trashed, 97 redirects) | Accurate state tracking |
| 2026-05-23 | **NYC SEO SERIES** — 3 posts: NYC Startup SEO (22601), NYC Restaurant SEO (22594), NYC Local SEO (22591) | 3 live posts |
| 2026-05-22 | **5 NEW POSTS** — Outbound vs SEO (22588), Lead Gen SEO (22585), Programmatic SEO (22582), Video Schema (22579), YouTube SEO (22576) | All live |
| 2026-05-21 | **4 NEW POSTS** — Press Release SEO (22573), Digital PR (22570), SEO Copywriting (22567), Conversion Copywriting (22564) | All live |
| 2026-05-20 | **3 NEW POSTS** — PWA vs Native (22561), ASO (22558), CRO E-commerce (22555) | All live |

---

## Semantic Category Architecture (12 Pillars)

| # | Category | WP ID | Post Count | Status |
|---|----------|-------|------------|--------|
| 1 | SEO Fundamentals | 445 | 17 | Active |
| 2 | On-Page SEO | 446 | 22 | Active |
| 3 | Technical SEO | 447 | 23 | Active |
| 4 | Off-Page SEO | 448 | 9 | Active |
| 5 | Local SEO | 449 | 13 | Active |
| 6 | SEO Strategy | 450 | 50 | Active |
| 7 | SEO Agency Guides | 451 | 4 | Active |
| 8 | Digital Marketing | 452 | 21 | Active |
| 9 | Content Marketing | 453 | 15 | Active |
| 10 | Paid Media | 454 | 4 | Active |
| 11 | AI and GEO | 455 | 15 | Active |
| 12 | SEO Tools and Resources | 456 | 15 | Active |

**Legacy Category:** Topics (ID: 1) : Count: 1 post + 8 drafts still using it (needs migration)

---

## 12 Pillar Posts - ALL PUBLISHED

| Pillar | ID | Status | Words | Focus KW |
|--------|-----|--------|-------|----------|
| GEO Content Strategy | 20463 | Published | 3,351 | geo content strategy |
| AI Overview Optimization | 20464 | Published | 3,239 | AI overview optimization |
| Entity SEO | 20465 | Published | 3,282 | entity SEO |
| SEO Analytics | 20466 | Published | 3,014 | SEO analytics |
| Topical Map SEO | 20467 | Published | 3,208 | topical map SEO |
| Information Gain | 20468 | Published | 3,266 | information gain score |
| Internal Linking | 20469 | Published | 3,225 | internal linking SEO |
| International SEO | 20470 | Published | 3,224 | international SEO |
| Ecommerce SEO | 20471 | Published | 3,232 | ecommerce SEO |
| B2B SEO | 20472 | Published | 3,259 | B2B SEO |
| SaaS SEO | 20473 | Published | 3,249 | SaaS SEO strategy |
| Schema Markup | 20474 | Published | 3,425 | schema markup |

---

## Draft Pipeline (0 Drafts - ALL PUBLISHED)

**Last 8 drafts were published on May 12-16, 2026.**
| ID | Title | Published Date | Cluster |
|----|-------|---------------|---------|
| 22012 | How Dentists Get 30+ New Patients Monthly from Google Maps | 2026-05-12 | Local SEO |
| 22015 | How Law Firms Rank #1 in the Map Pack for "Lawyer Near Me" | 2026-05-12 | Local SEO |
| 22018 | How to Rank Shopify Category Pages on Google (Without Paid Ads) | 2026-05-12 | eCommerce SEO |
| 22021 | How to Optimize Product Pages for Rich Results (Schema Guide) | 2026-05-12 | Technical SEO |
| 22024 | How to Build Internal Links for 10,000+ Product Pages (E-Commerce SEO) | 2026-05-16 | eCommerce SEO |
| 22027 | How B2B Content Marketing Generates Leads Through Thought Leadership | 2026-05-16 | Content Marketing |
| 22030 | How Brand Recognition Boosts Your Google Rankings (Branding for SEO) | 2026-05-16 | SEO Strategy |
| 22720 | SEO Company Dubai: UAE Business Rankings | 2026-05-24 | SEO Agency Guides |

---

## Recently Published (May 2026)

| Date | ID | Title | Words | Cluster |
|------|-----|-------|-------|---------|
| 2026-05-11 | 22088 | How Real Estate Agents Dominate the Google Map Pack (Without Paid Ads) | 2873 | Local SEO |
| 2026-05-08 | 21981 | How to Rank in Dubai: A Complete SEO Strategy for UAE Markets | ~3500 | GEO/Dubai |
| 2026-05-07 | 21783 | Generative Engine Optimization: Complete Strategy for 2027 | -- | AI and GEO |
| 2026-05-06 | 21993 | How to Audit Enterprise Sites with 10,000+ Pages Without Missing Anything | ~2800 | Technical SEO |
| 2026-05-05 | 21990 | Enterprise SEO: How Large-Scale Optimization Builds Authority and Rankings | ~3000 | SEO Strategy |
| 2026-05-04 | 20492 | What Is Answer Engine Optimization: AEO Explained for Modern Search | -- | AI and GEO |
| 2026-05-04 | 21996 | Enterprise SEO Reporting Dashboards That C-Suite Actually Reads | ~2800 | SEO Strategy |
| 2026-05-04 | 20529 | 301 Redirect Mapping Guide: How to Plan URL Changes Without SEO Damage | -- | Technical SEO |
| 2026-05-03 | 20516 | ccTLD vs Subdirectory vs Subdomain: Choosing the Right International SEO Structure | -- | Technical SEO |
| 2026-05-03 | 20495 | AI-Generated Search Results: How They Choose Sources and What It Means for SEO | -- | AI and GEO |
| 2026-05-03 | 20512 | Internal Linking Strategy for SEO: Complete Guide to Link Equity Distribution | -- | SEO Strategy |
| 2026-05-02 | 20468 | Information Gain Score Ultimate Guide: Create Original Content That Ranks Higher | -- | SEO Strategy |
| 2026-05-02 | 20494 | Google AI Overview SEO Strategy: How to Rank in AI-Powered Search | -- | AI and GEO |
| 2026-05-02 | 20482 | Healthcare SEO Guide: Medical Practice SEO and YMYL Compliance | -- | Local SEO |

---

## Technical SEO Status (2026-05-25 Audit)

| Check | Status | Last Verified |
|-------|--------|---------------|
| Sitemap | Pass | 2026-05-25 |
| Robots.txt | Pass | 2026-05-25 |
| Indexation | Pass | 2026-05-25 |
| Page Speed | Needs Verification | 2026-05-25 |
| Mobile Usability | Needs Verification | 2026-05-25 |
| Schema Markup | Needs Verification | 2026-05-25 |

### Open Technical Issues
- [ ] LinkedIn/YouTube footer links point to Pinterest (incorrect URLs)
- [ ] "Digital Marekting" typo in services dropdown
- [ ] Duplicate "Vancouver" link in footer Canada section
- [ ] Core Web Vitals need PageSpeed Insights verification

---

## Priority Queue

1. **Draft Pipeline (9 drafts)** — Fix Yoast + categories on 8 drafts, expand thin affiliate post
2. **Category Migration** — Move 8 drafts from "Topics" [1] to proper semantic categories
3. **Double Dash Cleanup** — ~42 posts have `--` occurrences (batch regex fix)
4. **GSC OAuth** — Add `webmasters.readonly` scope for Search Console diagnostics
5. **Repurposing Column** — Add to Content Calendar Google Sheet (LinkedIn, email, X)
6. **Old Blog Not-Indexed Audit** — User asked for recommendation; pending decision
7. **Featured Images** — All recent posts have images; legacy gaps tracked in Visual Cluster Map

---

## Files

| File | Path | Status |
|------|------|--------|
| Technical SEO Audit | `audits/tech-audit-2026-05-25.md` | ✅ Completed 2026-05-25 |
| Full Audit | `SEMANTIC-SEO-AUDIT-2026-05-03.md` | Historical |
| Post Registry | `post-registry.md` | ✅ Rebuilt (2026-05-24, 143 posts) |
| Visual Cluster Map | Google Sheet: `Visual Cluster Map` tab | ✅ Current |
| Content Calendar | Google Sheet: `Content Calendar` tab | ✅ Current |
| Mastersheet | `mastersheet.md` | ✅ Just updated |

---

## Notes

- All posts authored by Own-ur-Rehman Sheikh (ID 21)
- All recent posts have fresh Pexels featured images with alt text
- No em-dashes across all 116 published posts + 9 drafts
- Broken body image fix completed: removed 54 broken `<img>` tags from 20 posts
- Content Calendar Row 5 (Franchise SEO Pillar) was already published before this session
- Next in content pipeline: Dentists → Law Firms → Shopify → Rich Results → E-commerce links → Content Marketing → Brand SEO
