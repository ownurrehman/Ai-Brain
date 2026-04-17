# SEO module blueprint

Technical blueprint for the **website-scoped SEO** area. Aligned with [SEO_USER_JOURNEY.md](./SEO_USER_JOURNEY.md). Product/status context: [ROADMAP.md](../core/ROADMAP.md), [modules/SEO_MODULE.md](../modules/SEO_MODULE.md).

This submodule will keep evolving; use this file for **routes, components, backend mapping, and capability tiers**.

---

## Root context

Selected website is required for all analysis screens.

Website context store: `rankray-hq-frontend/src/modules/seo/store/seoWebsiteContextStore.ts`

---

## Screen architecture (12 screens)

| Tab ID | Route | Component | Backend source |
|--------|-------|-----------|----------------|
| `overview` | `/seo/overview` | SEODashboardMain + SEOWebsiteOverview | `/seo/dashboard`, `/seo/websites/:id/overview` |
| `performance` | `/seo/performance` | SEOPerformanceCommandCenter | `/seo/performance/summary`, `/seo/performance/trends` |
| `rankings` | `/seo/rankings` | KeywordManagement (mode=tracking) | `/seo/websites/:id/keywords`, SerpBear |
| `keywords` | `/seo/keywords` | KeywordManagement (mode=intelligence) | `/seo/websites/:id/keywords/intelligence` |
| `technical` | `/seo/technical` | SEOSiteAudit | `/seo/websites/:id/site-audit/*` |
| `content` | `/seo/content` | SEOContentPlanner + SEOContentIntelligence | `/seo/websites/:id/content/*` |
| `mentions` | `/seo/mentions` | MentionMonitoring | `/seo/mentions/*` |
| `backlinks` | `/seo/backlinks` | SEOBacklinkIntelligence | `/seo/websites/:id/backlinks/*` |
| `competitors` | `/seo/competitors` | SEOCompetitorIntelligence | `/seo/websites/:id/competitors`, `/seo/websites/:id/competitor-gap/*` |
| `publishing` | `/seo/publishing` | SEOPublishing | `/seo/websites/:id/wp/*` |
| `automation` | `/seo/automation` | SEOAutomationCenter | `/seo/websites/:id/automation/*` |
| `settings` | `/seo/settings` | GSCConnection + SEOWebsites (labeled **Integrations** in UI) | `/seo/config`, `/seo/websites`, provider OAuth |

---

## Screen state contract

| State | Meaning |
|-------|---------|
| `ready` | Data exists and is fresh |
| `needs_setup` | Provider not connected or website not configured |
| `sync_required` | Provider connected but no recent data |
| `insufficient_data` | Connected and synced but not enough data for meaningful analysis |

---

## Capability tiers

| Capability | Status | Notes |
|------------|--------|-------|
| Website CRUD | Supported | |
| GSC OAuth + sync | Supported | Real Google API |
| GA OAuth + sync | Supported | Real Google API |
| SerpBear integration | Supported | |
| Site audit (Crawlee/Cheerio crawler) | Supported | Real crawl |
| Keyword tracking | Supported | Provider-backed |
| Content planning from signals | Supported | GSC + crawl derived |
| AI content generation | Limited | Needs AI API key |
| WordPress publishing | Supported | Real WP API |
| Mention monitoring | Limited | Needs provider |
| Backlink intelligence | Limited | Observed data only |
| Competitor comparison | Limited | Tracked overlap only |
| Automation | Partial | Queue hardening |
| Search volume / KD | Not available | Needs paid index API |
| Full backlink graph / toxicity / broad competitor market | Not available | Needs paid index API |

---

## Navigation rules

- **Core flow screens:** Overview, Performance, Rankings, Keywords, Technical, Publishing, Automation, Integrations (`/seo/settings` in code).
- **Additional screens:** Content, Mentions, Backlinks, Competitors — honest about limits.
- No dead nav entries; no misleading readiness.

---

## CTA flow

| Signal | Action |
|--------|--------|
| Technical issue | Create task |
| Ranking drop | Create task / investigate |
| Content gap | Generate brief |
| Keyword opportunity | Add tracking / create content |
| Audit complete | Review results |
| Sync complete | Review changes |
| Content ready | Publish to WordPress |

---

## Onboarding state machine

```
NO_WEBSITE → create website → WEBSITE_CREATED
WEBSITE_CREATED → connect GSC → GSC_CONNECTED
GSC_CONNECTED → connect GA (optional) → PROVIDERS_CONNECTED
PROVIDERS_CONNECTED → run audit → AUDITED
AUDITED → add keywords → TRACKING
TRACKING → review overview → OPERATIONAL
```

Partial paths are valid (e.g. GSC-only still supports Performance, Keywords, Content with honest states elsewhere).
