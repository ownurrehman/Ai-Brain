# Social publishing — MVP scope

**Status:** Planned ([ROADMAP.md](../core/ROADMAP.md), [modules/SEO_MODULE.md](../modules/SEO_MODULE.md)). Not part of v1 “Agency OS” positioning unless explicitly prioritized — see [POSITIONING_V1.md](./POSITIONING_V1.md).

## Goal of MVP

One **scheduling + publish** path for **one network** that is **traceable** inside RankRay: every post links to **workspace entities** so sales can truthfully say “social is connected to clients and websites.”

## Channel choice (pick one first)

| Channel | Pros | Cons |
|---------|------|------|
| LinkedIn | B2B fit for agency buyers | OAuth review, company vs personal pages |
| X (Twitter) | API familiarity | API tier/cost changes often |
| Facebook Pages | Reach | Meta app review complexity |

**Recommendation for MVP:** **LinkedIn** *or* **X** based on your ICP; do not implement three in parallel.

## Entity links (required)

Each `SocialPost` (or equivalent) record should support:

- `workspaceId` (tenant)
- Optional `companyId` (client)
- Optional `seoWebsiteId` (which site/campaign this promotes)
- Optional `projectId` or future `campaignId` (Marketing module alignment)
- `createdByUserId`, scheduled time, status (`draft` | `scheduled` | `published` | `failed`), provider post id, error message

## MVP feature slice

1. **OAuth** connect workspace to one network; store tokens server-side encrypted.
2. **Composer**: text + optional link + single image attachment (defer carousels/video).
3. **Queue**: publish at `scheduledAt` via background job (reuse BullMQ when Redis available; document degradation).
4. **History list** with link-out to live post when published.
5. **No** full analytics suite in MVP — optional “fetch impressions” later if API allows.

## Non-goals for MVP

- Multi-network calendar with drag-drop across five channels.
- Listening/inbox (use Inbox module roadmap separately).
- Compliance archiving for regulated industries (later).

## Documentation touchpoints when built

- [ARCHITECTURE.md](../core/ARCHITECTURE.md) and [ROADMAP.md](../core/ROADMAP.md): new entity + route ownership.
- [FEATURES.md](./FEATURES.md): update sales matrix row from **planned** to **partial** with honest limits.
- [RELEASE_GATE.md](./RELEASE_GATE.md): add OAuth + publish smoke test.
