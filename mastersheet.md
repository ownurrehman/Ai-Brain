# 📋 Technical SEO Audit Master Sheet

> **Parent Hub:** [[INDEX|🧠 Master Ai Brain Hub]] · **Websites Directory:** [[websites/index|🌐 Websites Hub]]
> **Fleet Operations:** [[agents/FLEET-ORCHESTRATION|🤖 Agent Fleet]] · **Strategic Reports:** [[reports/INDEX|📊 Reports Hub]]

---

## 📌 Latest Live Site Audits

| Site | Strategy Dossier | Audit File | Mastersheet |
|---|---|---|---|
| **rankray.com** | [[websites/rankray.com/index|rankray.com Strategy]] | [[websites/rankray.com/audits/tech-seo-audit-2026-08-13|Audit 2026-08-13]] | [[websites/rankray.com/mastersheet|Site Mastersheet]] |
| **tonicphysio.com** | [[websites/tonicphysio.com/index|tonicphysio.com Strategy]] | [[websites/tonicphysio.com/audits/tech-seo-audit-2026-08-13|Audit 2026-08-13]] | [[websites/tonicphysio.com/mastersheet|Site Mastersheet]] |
| **coinsfera.com** | [[websites/coinsfera.com/index|coinsfera.com Strategy]] | [[websites/coinsfera.com/audits/tech-seo-audit-2026-08-13|Audit 2026-08-13]] | [[websites/coinsfera.com/mastersheet|Site Mastersheet]] |
| **teammotorcycle.com** | [[websites/teammotorcycle.com/index|teammotorcycle.com Strategy]] | [[websites/teammotorcycle.com/audits/tech-seo-audit-2026-08-13|Audit 2026-08-13]] | [[websites/teammotorcycle.com/mastersheet|Site Mastersheet]] |
| **backlinkcrypto.com** | [[websites/backlinkcrypto.com/index|backlinkcrypto.com Strategy]] | [[websites/backlinkcrypto.com/audits/tech-seo-audit-2026-08-13|Audit 2026-08-13]] | [[websites/backlinkcrypto.com/mastersheet|Site Mastersheet]] |
| **justccell.com** | [[websites/justccell.com/INDEX|justccell.com Strategy]] | [[websites/justccell.com/rules|AI Rules & Policies]] | [[websites/justccell.com/mastersheet|Site Mastersheet]] |


---

## Active Integrations

| Integration | Status | Config Location | Notes |
|-------------|--------|-----------------|-------|
| `analytics-mcp` | ✅ Installed | `~/.openclaw/openclaw.json` | Google Analytics 4 MCP server |
| `google-workspace-master` | ✅ Active | `~/.openclaw/skills/google-workspace-master/` | Full Google Workspace automation |
| `rankray-email-drafter` | ✅ Active | workspace skills dir | Daily lead email pipeline |
| `outreach-engine-v3` | ✅ Active | `system/outreach/` | 100 emails/day, self-learning, UAE prospects |
| `email-sleuth` | ✅ Installed | `~/.local/bin/es` | SMTP email verification v1.1.0 |
| `AgentMail` | ✅ Active | `sheikhown@agentmail.to` | 3,000 emails/month free tier |

---

## MCP Server: `analytics-mcp`

**Package:** `pipx run analytics-mcp` (v0.6.0)
**Credentials:** `credentials/google-oauth/ga-mcp-adc.json`
**Project ID:** `openclaw-rank-ray-automation`
**Account:** `oliverjakeseo@gmail.com`

### Available Tools
- `get_account_summaries` — List all GA accounts + properties
- `get_property_details` — Details for a specific GA4 property
- `list_google_ads_links` — Linked Google Ads accounts
- `run_report` — Run GA4 Data API reports (traffic, events, conversions)
- `run_funnel_report` — Funnel analysis
- `get_custom_dimensions_and_metrics` — Custom definitions audit
- `run_realtime_report` — Realtime user data

### GA4 Properties Accessible
| Account | Property | Property ID | Timezone |
|---------|----------|-------------|----------|
| Towel Depot | The Towel Depot | `properties/276090163` | America/Los_Angeles |

> ⚠️ **Note:** Only "Towel Depot" currently visible via ADC auth. If more GA4 properties should be accessible (rankray.com, tonicphysio.com, etc.), the ADC credentials may need re-authentication with the correct Google user.

### When to Use
- SEO audits → traffic + conversion data
- Content performance → page-level engagement
- Technical SEO → Core Web Vitals via GA4 events
- Competitor analysis → benchmark traffic patterns
- Weekly reporting → automated report generation

---

---

## Audit History

| Date | Site | Status | Critical | Medium/High | Notes |
|------|------|--------|----------|-------------|-------|
| 2026-09-03 | justccell.com | Multi-Bot Rules | 0 | 0 | AI rules finalized: 100% backend content editability, ACF cleanup & 1:1 sync, zero samples sitewide |
| 2026-08-13 | rankray.com | Live fetch | 2 | 3 | Services/Web&Apps still `#`; case studies `#` |
| 2026-08-13 | tonicphysio.com | Live fetch | 1 | 2 | Title says Milton **CA**; fees now have prices |
| 2026-08-13 | coinsfera.com | Live fetch | 0 | 2 | `/services/usdt/` now → sell-Tether (not RU blog) |
| 2026-08-13 | teammotorcycle.com | Live fetch | 1 | 2 | Title polluted by payment alts; Product+Offer OK |
| 2026-08-13 | backlinkcrypto.com | Live fetch | 0 | 2 | Home OK; confirm product schema in catalog |
| 2026-06-01 | rankray.com | Older | 2 | 4 | Superseded |
| 2026-05-31 | tonicphysio.com | Older | 2 | 4 | Fees issue fixed 2026-08-13 |
| 2026-05-14 | coinsfera.com | Older | 1 | 2 | USDT RU-redirect fixed |
| 2026-04-25 | teammotorcycle.com | Older | 1 | 1 | Product Offer schema now present |

---

## justccell.com — 2026-09-03 (Multi-Bot Rules & Client Mandates)

**Focus:** Multi-bot coordination standards (Cursor, Grok, Hermes, Antigravity) and strict client policy enforcement.

- **100% Backend Content Editability:** Hard rule that every heading, paragraph, button text, CTA link, and media asset must be editable in wp-admin (`Pages → Edit Page` / `Products → Edit Product`) using native WordPress / WooCommerce or mapped ACF fields. Zero hardcoded copy in theme templates.
- **Mandatory ACF Hygiene & 1:1 Sync:** Prune all leftover/ghost ACF fields upon any page redesign or layout change. Ensure 1:1 sync between frontend templates and backend fields.
- **Client Mandate (Mr Nas - CCELL Mazhar):** Strictly remove all "Get Samples & Quotes", sample trays, free sample offerings, and turnaround promises sitewide. Hardware samples are not offered.
- **Authority files:** [[websites/justccell.com/rules|justccell.com rules.md]] · [[websites/justccell.com/AGENTS|AGENTS.md]] · `.cursorrules` · `.cursor/rules/justccell-page-content-editability.mdc`
- Full site mastersheet: [[websites/justccell.com/mastersheet|Justccell Project Mastersheet]]

---

## rankray.com — 2026-08-13

**Health:** Fair. 148 posts / 70 pages in XML sitemaps. Title 58c, desc 154c.

**Critical:** Header “Services” and “Web & Apps” are `href="#"`. Case-study logos still `#`.

**Also:** Footer has Facebook + Pinterest; Twitter still twitter.com; no YouTube profile on home.

Full write-up: `websites/rankray.com/audits/tech-seo-audit-2026-08-13.md`

---

## tonicphysio.com — 2026-08-13

**Health:** Fair+. `/fees/` now shows real dollar amounts (old “contact us” placeholders are gone). 74 posts / 89 pages.

**Critical:** Title tag ends in **“Milton, CA”** (should be Ontario).

**Still open:** Products / Programs nav = `#`.

Full write-up: `websites/tonicphysio.com/audits/tech-seo-audit-2026-08-13.md`

---

## coinsfera.com — 2026-08-13

**Health:** Fair/Good schema. www canonical. 141 posts / 72 pages. FAQ + FinancialService + LocalBusiness on home.

**USDT:** `/services/usdt/` now lands on `/sell-tether-in-istanbul/` (not the Russian blog). Treat as alias vs dedicated page.

Full write-up: `websites/coinsfera.com/audits/tech-seo-audit-2026-08-13.md`

---

## teammotorcycle.com — 2026-08-13

**Health:** Mixed. Product pages now emit Product + Offer JSON-LD (old critical gap closed).

**Critical:** Homepage `<title>` appends payment-brand names (167c). 59 images missing alt. Collections still Breadcrumb-only.

Full write-up: `websites/teammotorcycle.com/audits/tech-seo-audit-2026-08-13.md`

---

## backlinkcrypto.com — 2026-08-13

**Health:** Good baseline. Title/desc/H1/FAQ schema present. Robots correctly block cart/checkout.

**Next:** Confirm listing URLs in sitemap children have Product schema.

Full write-up: `websites/backlinkcrypto.com/audits/tech-seo-audit-2026-08-13.md`

---

## Sites in Rotation

| Day | Site | Last live audit |
|-----|------|-----------------|
| Monday | rankray.com | 2026-08-13 |
| Tuesday | tonicphysio.com | 2026-08-13 |
| Wednesday | coinsfera.com | 2026-08-13 |
| Thursday | teammotorcycle.com | 2026-08-13 |
| Friday | backlinkcrypto.com | 2026-08-13 |
| Weekend | rankray.com / tonicphysio.com | 2026-08-13 |

---

## Historical notes (pre-August 2026)

The sections below are kept for trail. Prefer the 2026-08-13 files above.

---

| Date | Site | Status | Critical Issues | Medium Issues | Low Issues |
|------|------|--------|----------------|---------------|------------|
| 2026-06-01 | rankray.com | ✅ Audited | 2 | 4 | 4 |
| 2026-05-31 | tonicphysio.com | ✅ Audited | 2 | 4 | 3 |
| 2026-05-14 | coinsfera.com | ✅ Audited | 1 | 2 | 1 |
| 2026-04-25 | teammotorcycle.com | ✅ Audited | 1 | 1 | 3 |

---

## rankray.com - 2026-06-01

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK (Yoast SEO index with 5 sub-sitemaps)
- **Robots.txt:** ✅ OK (all allowed, sitemap referenced)
- **Indexation:** ✅ Good (10+ pages indexed)
- **Schema:** ⚠️ Unverified (Yoast likely generating; needs manual check)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Not measured (needs PSI)

### Critical Issues (2)
1. **Broken "Services" nav link** - Points to `#` across all pages. Hurts UX and crawlability.
2. **Social media URL mismatches** - LinkedIn, YouTube, and Pinterest footer links all redirect to the same Pinterest URL. Twitter link uses old `twitter.com` domain.

### Medium Issues (4)
1. **About Us page thin content** - Only ~5 short paragraphs; lacks team photos, history, or structured data.
2. **Duplicate Vancouver link** - Canada footer lists Vancouver twice with identical URLs.
3. **Missing dedicated Services nav target** - `/digital-marketing-services/` exists but is not linked in top nav.
4. **Case study placeholders** - On SEO services page, "Law Firm", "Real Estate Portal", and "Ecommerce Store" blocks link to `#`.

### Low Issues (4)
1. Author sitemap stale (lastmod 2026-05-12)
2. Old Twitter domain in footer (`twitter.com` vs `x.com`)
3. Some image alt text generic (client logos)
4. Title tags appeared empty in browser snapshots (verify in source)

### Recommended Fixes (Priority)
1. Fix "Services" nav link to `/digital-marketing-services/`
2. Correct LinkedIn and YouTube footer URLs to actual profiles
3. Enrich `/about-us/` with photos, timeline, and AboutPage schema
4. Fix or remove case study placeholder links
5. Remove duplicate Vancouver footer link
6. Verify schema with Google Rich Results Test
7. Run PageSpeed Insights + Mobile-Friendly Test

---

## tonicphysio.com - 2026-05-31

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK
- **Robots.txt:** ✅ OK
- **Indexation:** ✅ Good (5+ pages indexed)
- **Schema:** ⚠️ Unverified (Yoast likely generating, needs manual check)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Needs PSI test

### Critical Issues (2)
1. **Missing pricing on /fees/** - All fee sections show placeholder "contact us" text. No actual prices.
2. **Broken nav links** - "Products" and "Programs" menu items link to `#` (empty anchors).

### Medium Issues (4)
1. **Thin contact page** - Minimal content, missing hours/map/parking.
2. **Schema unverified** - Cannot confirm JSON-LD presence via automated fetch.
3. **Image alt text** - Needs audit across all pages.
4. **Meta descriptions** - Need verification on homepage.

### Low Issues (3)
1. FAQ schema missing on service pages
2. PriceRange not in LocalBusiness schema
3. Author sitemap stale (lastmod 2026-05-12)

### Recommended Fixes (Priority)
1. Add transparent pricing to /fees/ page
2. Fix or remove broken nav links (Products, Programs)
3. Verify schema with Google's Rich Results Test
4. Enrich /contact/ with hours, map, parking info
5. Run PageSpeed Insights + Mobile-Friendly Test
6. Audit all image alt text

---

## coinsfera.com - 2026-05-14

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK
- **Robots.txt:** ✅ OK
- **Indexation:** ✅ Good (5+ pages indexed)
- **Schema:** ✅ Good (FAQ, Service, FinancialService, and Breadcrumb schemas are all implemented correctly site-wide)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Needs PSI test

### Critical Issues (1)
1. **`/services/usdt/` Redirect Broken** - The `/services/usdt/` URL 301-redirects to a Russian comparison blog post (`/ru/блоги/usdt-против-usdc-что-выбрать/`) instead of loading the English USDT service landing page.

### Medium Issues (2)
1. **Sitemap Coverage Gap** - The `/services/usdt/` URL is missing from the XML sitemap index.
2. **Internal Link Structure** - Homepage internal links mostly point to `/wp-content/` assets. Cross-linking between service pages is thin and needs improvement.

### Low Issues (1)
1. **Blog Freshness** - Needs review of recent blog update schedules to ensure content remains fresh and authoritative.

### Recommended Fixes (Priority)
1. Correct the USDT service redirect rule in WordPress Redirections or Yoast SEO.
2. Add the proper `/services/usdt/` service landing page back to the XML sitemap.
3. Improve internal contextual linking from the homepage and between individual service pages.
4. Run Google PageSpeed Insights for Core Web Vitals.

---

## teammotorcycle.com - 2026-04-25

### Overall Health: 🟡 FAIR
- **Sitemap:** ✅ OK (native auto-generated Shopify sitemap)
- **Robots.txt:** ✅ OK (standard Shopify default)
- **Indexation:** ✅ Good (large catalog indexed, 2,149 products / 430 collections)
- **Schema:** ⚠️ Partial (ProductGroup present on product pages, but missing key pricing and offers schema)
- **Mobile:** ⚠️ Unverified
- **Page Speed:** ⚠️ Needs PSI test

### Critical Issues (1)
1. **Missing Price & Availability Schema** - Product pages use `ProductGroup` but are missing the standard `Product` type and `offers` (price, currency, availability) schemas. Rich snippets won't show prices or in-stock status in Google Search.

### Medium Issues (1)
1. **Missing CollectionPage Schema** - Shopify Collection pages only have `BreadcrumbList` and are missing the `CollectionPage` and `ItemList` schemas for products listed within the collection.

### Low Issues (3)
1. **Author/Date refinement in Blog** - Article schema is present but needs author and datePublished improvements.
2. **Missing FAQPage Schema** - No FAQ schema on `/pages/frequently-asked-questions`.
3. **Generic Alt Text** - Product images could benefit from a structured audit of alt attributes.

### Recommended Fixes (Priority)
1. Edit the Shopify theme liquid files to insert the `Product` + `Offer` structured data on product pages.
2. Add `CollectionPage` and `ItemList` schema templates to your collection liquid theme files.
3. Add FAQPage schema to your main Frequently Asked Questions page.
4. Audit product image alt text for descriptive, keyword-aligned descriptions.

---

## Sites in Rotation

| Day | Site | Last Audit | Next Audit |
|-----|------|------------|------------|
| Monday | rankray.com | 2026-06-01 | 2026-06-08 |
| Tuesday | tonicphysio.com | 2026-05-31 | 2026-06-02 |
| Wednesday | coinsfera.com | - | 2026-06-03 |
| Thursday | teammotorcycle.com | - | 2026-06-04 |
| Friday | rankray.com | 2026-06-01 | 2026-06-05 |
| Weekend | tonicphysio.com | 2026-05-31 | 2026-06-07 |

---

*This sheet is updated automatically after each audit.*
