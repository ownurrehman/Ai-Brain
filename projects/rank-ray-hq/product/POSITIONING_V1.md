# Positioning — v1 decision

## Chosen default: Agency OS (SEO-led)

**RankRay HQ v1** is positioned as a **single operating system for an SEO agency or in-house growth team**, not as a full consumer-grade “every feature” SaaS. Product intent and status: [ROADMAP.md](../core/ROADMAP.md).

### Core wedge

- **Company-first** records: contacts, websites, projects, finance, assets stay **linked**.
- **SEO is website-scoped**; analysis screens require explicit website context.
- **Honest product surface**: no fake KPIs; optional providers show setup/sync/insufficient-data states.

### Publishing scope (explicit)

- **Top-level Publishing** = **video** workflows.
- **Blog / image** generation and WordPress-style publishing sit under **SEO → Publishing**, not the top-level Publishing nav.

### What v1 does not claim

- **Ahrefs/Semrush parity** (volume, KD, full backlink graph, toxicity, broad competitor market) unless [SEO_RANK_INTELLIGENCE.md](./SEO_RANK_INTELLIGENCE.md) is implemented and **labeled** accordingly.
- **Multi-platform social scheduling** as a shipped pillar — see [SOCIAL_PUBLISHING_MVP.md](./SOCIAL_PUBLISHING_MVP.md) for a minimal future slice.

## Expansion track (explicitly larger scope)

If the business chooses **“all-in-one growth suite”** for marketing:

1. Add **rank intelligence** via **one** commercial API with normalized models and UI tiers.
2. Add **social MVP** (single channel first) with entity links to company/website/campaign.
3. Run [SAAS_PACKAGING_PLAN.md](./SAAS_PACKAGING_PLAN.md) if selling **multi-tenant** subscriptions rather than per-deployment licenses.

Document any **change to default positioning** in this file with a dated revision note.
