# SEO User Journey

Authoritative specification for the end-to-end SEO workflow in RankRay HQ.

---

## Root Model

SEO is always website-first.

Rules:

- No SEO analysis screen opens without a selected website.
- Selected website is visible in the header/context bar on every screen.
- Every screen shows: data source, last sync time, readiness state, and an honesty note when data is limited.
- Allowed readiness states: `ready`, `needs_setup`, `sync_required`, `insufficient_data`.

---

## User Journey (End-to-End)

### 1. Add Website

User creates a website record in SEO Integrations.

- Site URL is required.
- Display name is optional (falls back to URL).
- Country, search engine, and tracking frequency are set here.

### 2. Link Website to Company

Website belongs to a company.

- Company can be selected during creation or linked afterward.
- If no company is linked, the website still works but finance/project linkage is incomplete.

### 3. Connect Providers

All provider connections happen in SEO Integrations.

| Provider | Purpose | Required? |
|----------|---------|-----------|
| Google Search Console | Organic queries, clicks, impressions, CTR, pages | Recommended |
| Google Analytics | Traffic, engagement, user behavior | Optional |
| SerpBear (or compatible) | Live keyword position tracking | Recommended for Rankings |

Connection flow:

- GSC: OAuth flow → select property → verify access.
- GA: OAuth flow → select property → verify access.
- SerpBear: API URL + token → select domain → verify access.

### 4. Verify Readiness

After provider connections, system checks:

- Is GSC connected? → enables Performance, Keywords data.
- Is GA connected? → adds traffic layer to Performance.
- Is rank tracking connected? → enables Rankings live position data.
- Are keywords tracked? → enables Rankings movement, Keyword analysis.

Screens show honest readiness state based on this.

### 5. Run Initial Crawl / Audit

User triggers a site audit from Technical screen or Overview.

- Crawl uses CheerioCrawler (real crawl, no mocking).
- Audit rules engine scores crawled pages.
- Results persist as SiteAuditRun, SiteAuditPage, SiteAuditIssue.
- Health score is calculated from real issues.

### 6. Add or Import Tracked Keywords

User adds keywords from Rankings or Keywords screen.

- Keywords are website-scoped.
- Country and device can be set per keyword.
- SerpBear connection enables live position imports.
- GSC connection enables query-based keyword discovery.

### 7. Review Rankings and Movement

Rankings screen shows:

- Current position, previous position, movement delta.
- Tracked page, device, country.
- Data source label (SerpBear / GSC / no provider).
- Empty state if no keywords are tracked or no provider is connected.

### 8. Review Technical Issues

Technical screen shows:

- Latest audit summary (health score, pages crawled, issues).
- Issue breakdown by severity (error, warning, notice).
- Issue breakdown by category (crawlability, metadata, content, internal linking, indexability, canonicals).
- Page-level issue detail.
- Ability to re-run audit.

### 9. Review Content Ideas and Opportunities

Content screen shows:

- Content ideas derived from real ranking/query/page signals.
- Keyword-to-page mapping gaps.
- Content brief generation.
- Draft generation (requires AI provider API key).
- Internal link suggestions where crawl data exists.

Honesty note: Content ideas are limited to what GSC and crawl data reveal. No external search volume or keyword difficulty is available without a paid provider.

### 10. Review AI Mentions

AI Mentions screen shows:

- Mention monitoring results from a connected provider (Firehose or equivalent).
- If no provider is connected: shows `needs_setup` state.
- Does not claim to track AI Overviews unless it actually does.

Honesty note: This feature requires a mention monitoring provider. Without one, it shows setup instructions only.

### 11. Review Backlink Intelligence

Backlinks screen shows:

- Backlink records from directly observed or imported sources.
- Referring domain summary.
- Follow/nofollow ratio.
- Authority trend only when real historical data exists.

Honesty note: Full backlink graph, toxicity scores, and broad authority metrics require a paid external index. Without one, this screen operates on imported/observed data only and labels this limitation clearly.

### 12. Review Competitor Comparison

Competitors screen shows:

- Tracked competitor domains.
- Keyword overlap from tracked rankings.
- Page/topic overlap where observable from crawl/GSC data.

Honesty note: Broad market intelligence, competitor traffic estimates, and full gap analysis require paid external datasets. Without them, comparison is limited to tracked keyword overlap.

### 13. Create Tasks / Generate Content / Publish

Publishing screen connects:

- Keyword opportunities → content brief → draft → approval → publish.
- WordPress connection for publishing.
- Publish history per website.

Tasks can be created from any insight screen (Technical issues → task, Content gap → task, Ranking drop → task).

### 14. Measure Impact Over Time

Performance screen shows:

- GSC clicks/impressions/CTR/position trends over time.
- GA traffic/engagement if connected.
- Rank tracking trends if SerpBear is connected.
- Audit health score history.

All trend data is source-labeled and shows honest empty states when insufficient.

---

## Onboarding Flow

```
1. User lands on SEO module
2. No website selected → SEO Settings shows website list + "Add Website"
3. User creates website (URL, name, country, company)
4. User connects Search Console (OAuth)
5. User connects Analytics (OAuth) — optional
6. User connects SerpBear (API token) — optional
7. User runs first site audit from Technical or Overview
8. User adds initial keywords from Rankings
9. User lands in Overview with real data
```

If only some providers are connected, the system works partially:

| Connected | What works |
|-----------|-----------|
| None | Website created, audit can run, no search/traffic data |
| GSC only | Performance (queries), Keywords (discovery), Content (opportunities) |
| GSC + GA | Performance (full), Content (full) |
| GSC + SerpBear | Performance, Rankings (live positions), Keywords |
| All three | Full capability |

---

## Final Screen Architecture (12 Screens)

| # | Screen | Tab ID | Status |
|---|--------|--------|--------|
| 1 | Overview | `overview` | Supported |
| 2 | Performance | `performance` | Supported |
| 3 | Rankings | `rankings` | Supported |
| 4 | Keywords | `keywords` | Supported |
| 5 | Technical | `technical` | Supported |
| 6 | Content | `content` | Supported (AI generation requires API key) |
| 7 | AI Mentions | `mentions` | Limited (requires mention provider) |
| 8 | Backlinks | `backlinks` | Limited (no external index; observed data only) |
| 9 | Competitors | `competitors` | Limited (tracked overlap only; no market data) |
| 10 | Publishing | `publishing` | Supported |
| 11 | Automation | `automation` | Partial (needs production hardening) |
| 12 | Integrations | `settings` | Supported |

---

## Screen Purposes

### Overview

Command center for the selected website.

Shows: health summary, provider readiness, tracked keywords count, ranking momentum, top blockers, top opportunities, next best actions.

Actions: create task, sync, run crawl, add keywords, generate brief, open publishing.

### Performance

Real performance trend using connected sources.

Uses: GSC queries/clicks/impressions/pages, GA traffic/engagement if connected, rank tracking trend if available.

Must show honest source labels and empty/setup states.

### Rankings

Track keyword positions for selected website.

Supports: add keyword, remove/archive keyword, current rank, previous rank, movement, tracked page, device/country where supported.

Must use real provider only. No faked positions.

### Keywords

Keyword workspace and page mapping.

Supports: tracked keywords, keyword grouping/tagging, map keyword to page, identify missing page coverage, identify content opportunities using real data.

Does not fake search volume or keyword difficulty.

### Technical

Technical SEO board for selected website.

Supports: latest audit, critical issues, warnings, page-level issues, crawl/indexation signals, internal link issues if available.

Must be impact-prioritized.

### Content

Convert search data into content actions.

Supports: content ideas from ranking/page/query signals, generate brief, generate draft (requires AI API key), internal link suggestions, landing page update suggestions.

### AI Mentions

Mention monitoring.

Named honestly as "AI Mentions" — does not claim AI Overview tracking unless it actually does.

Status: requires mention monitoring provider. Shows `needs_setup` when no provider is connected.

### Backlinks

Backlink intelligence from real observed data only.

If no provider/index exists: shows `limited` state with explanation.

Does not fake authority, toxicity, or domain metrics.

### Competitors

Competitor comparison from real observed data only.

Allowed: tracked competitor domains, keyword overlap from tracked rankings, page/topic overlap where observable.

Does not fake broad market intelligence.

### Publishing

Idea → draft → approval → publish workflow.

Connected to: keyword opportunities, content ideas, website context, publish history.

### Automation

Rule-based SEO workflows.

Examples: ranking drop → create task, critical issue → notify/create task, keyword gap → generate brief.

Status: partial — needs production hardening.

### Integrations

All provider/config/setup flows. Labeled "Integrations" in the UI to distinguish from global app Settings.

Includes: website management, Search Console, Analytics, rank tracking provider, crawl settings, sync controls.

---

## Capability Boundaries

### Fully Supported (no external paid index required)

- Website onboarding and management
- Google Search Console performance data
- Google Analytics performance data
- Keyword position tracking from real provider (SerpBear)
- Technical site audit via real CheerioCrawler
- Page-level SEO issue tracking
- Keyword-to-page mapping
- Content ideas from real query/page/ranking signals
- Publishing flow (WordPress)
- Automation rules (partial)
- Task creation from any insight

### Limited (real but constrained)

- Content generation (requires AI provider API key — OpenAI, Anthropic, or Gemini)
- AI Mentions (requires mention monitoring provider)
- Backlink intelligence (observed/imported sources only, no full index)
- Competitor comparison (tracked keyword overlap only, no market data)
- Automation reliability (needs production hardening)

### Not Available (would require paid external index)

- Global search volume
- Keyword difficulty scores
- Full backlink graph
- Toxicity scores
- Broad authority metrics
- Full competitor market datasets
- Competitor traffic estimates

These are labeled `planned` or `provider required` — never faked.
