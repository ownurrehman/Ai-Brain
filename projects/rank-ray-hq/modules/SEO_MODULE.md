# SEO module (product contract)

**Routes, components, backend mapping, capability table:** [SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md) (update that file when IA or APIs change).

**End-to-end journeys:** [SEO_USER_JOURNEY.md](../seo/SEO_USER_JOURNEY.md).

**Roadmap status:** [ROADMAP.md](../core/ROADMAP.md) (SEO + SEO automation rows).

---

## Scope model

- **Website-first:** selected website required for analysis routes.
- **Separate from top-level Publishing:** that module is video-focused; **blog/image** generation and WP-style publishing live under **SEO** routes (see blueprint `publishing` tab).
- **Honest states only:** `ready` | `needs_setup` | `sync_required` | `insufficient_data`. No fake KPIs.
- **Integrations** (GSC/GA/etc.) use the SEO **Integrations** UI (route `/seo/settings`), not global Settings.

---

## Module status board (by sub-area)

Module: Overview  
Purpose: command center with health, blockers, opportunities, next action  
Owner Entity: SeoWebsite  
Depends On: summary + sync + issues + opportunities  
Used By: all SEO operators  
Current Status: partial  
Done Means: insights are real and each insight has an action  
Known Problems: mixed old/new UI behavior in some states  
Next Work: stabilize state handling and action consistency

Module: Performance  
Purpose: explain movement (traffic/rank deltas, confidence, sync freshness)  
Owner Entity: SeoWebsite  
Depends On: GSC/GA snapshots  
Used By: reporting and diagnostics  
Current Status: partial  
Done Means: movement explains itself with real data or honest setup state  
Known Problems: provider dependency gaps and uneven state clarity  
Next Work: harden setup/sync/error state contracts

Module: Rankings  
Purpose: tracked keywords, winners/losers, snapshot deltas  
Owner Entity: TrackedKeyword + KeywordSnapshot  
Depends On: sync providers + snapshots  
Used By: SEO tasks and triage  
Current Status: partial  
Done Means: ranking table is stable and conversion to tasks is reliable  
Known Problems: depends on provider-backed freshness and mapping quality  
Next Work: improve ranking-drop -> task workflow confidence

Module: Keywords  
Purpose: keyword opportunities, intent/grouping, content gap handoff  
Owner Entity: SeoKeywordIntelligence / opportunities  
Depends On: snapshots + intelligence enrichment  
Used By: content planning  
Current Status: partial  
Done Means: opportunity scores are populated by real sync intelligence  
Known Problems: intelligence quality varies by provider coverage  
Next Work: continue enrichment and explainability improvements

Module: Technical  
Purpose: crawl/audit issues prioritized by impact  
Owner Entity: SiteAuditRun + SiteAuditIssue  
Depends On: crawler + rules engine  
Used By: technical remediation  
Current Status: partial  
Done Means: run status, issues, and actions are deterministic and exportable  
Known Problems: needs continued UX/scale hardening  
Next Work: improve run observability and issue triage flow

Module: Publishing  
Purpose: SEO-driven content generation -> approval -> publish  
Owner Entity: content plan + publish history  
Depends On: AI providers + WordPress connection  
Used By: content ops  
Current Status: partial  
Done Means: blog and image generation workflows are reliable and auditable from inside SEO context  
Known Problems: handoff boundaries between SEO content operations and standalone publishing module need continued clarity  
Next Work: keep blog/image generation inside SEO and keep boundaries explicit in UI and docs

Module: Automation  
Purpose: scheduled SEO workflows and execution logs  
Owner Entity: SeoAutomationConfig + runs + alerts  
Depends On: queues + provider services  
Used By: operations and monitoring  
Current Status: partial  
Done Means: all configured job types execute and report deterministic outcomes  
Known Problems: recent stub-to-real wiring needs runtime validation  
Next Work: complete reliability pass and close edge-case failures

Module: Integrations (UI label; route `/seo/settings`)  
Purpose: provider setup, sync config, website list, status visibility  
Owner Entity: website integration config  
Depends On: OAuth + provider availability  
Used By: all SEO screens  
Current Status: partial  
Done Means: setup is clear, website-scoped, and non-fragile  
Known Problems: flow quality depends on callback/env correctness  
Next Work: continue UX polish and setup recovery clarity

## Feature Availability Labels

- `done`: stable and production-safe
- `partial`: implemented but not fully reliable or complete
- `planned`: not implemented yet
- `coming soon`: visible placeholder intentionally not implemented

Current practical truth:

- Google Search Console integration: partial (real, needs ongoing hardening)
- Google Analytics integration: partial (real, optional by tier/setup)
- Embedded rank provider sync: partial (backend capability with website mapping and ongoing reliability hardening)
- Keyword intelligence enrichment: partial
- WordPress publishing: partial/usable
- Image generation: partial (newly wired, requires key/config and runtime validation)
- Multi-platform social/video publishing beyond current flows: planned
- Advanced authority/backlink provider depth: planned
