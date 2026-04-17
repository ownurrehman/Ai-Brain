# SEO rank intelligence — third-party API approach

Goal: add **Semrush/Ahrefs-class** *signals* without claiming **full parity** with those products. Aligns with capability tiers in [SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md).

## Recommended first integration: Semrush API

**Rationale**

- `SEMRUSH_API_KEY` is already listed in [keys.md](../reference/keys.md) as an expected env var.
- Broad endpoint surface (domain/keyword/backlink-ish reports) suitable for a **single adapter** behind RankRay models.
- Alternative **DataForSEO** or **Ahrefs API** can be a **second adapter** later using the same internal DTOs.

**Rule:** Pick **one** provider for v1 of “Rank intelligence”; add a second only after normalization and caching are stable.

## Internal design

### 1. Adapter layer

- `SemrushIntelAdapter` (Nest service) maps Semrush report responses → **workspace-normalized** DTOs, e.g.:
  - `KeywordMetric` (volume, difficulty, intent optional, currency, fetchedAt)
  - `DomainSummary` (organic keywords estimate, traffic estimate — labeled as provider estimates)
  - `CompetitorOverlap` (only when API supports; never invent overlap)

### 2. Persistence

- Store **raw payload hash + fetchedAt + websiteId + workspaceId** for cache and debugging.
- Store **normalized rows** in Prisma tables dedicated to intel (avoid overloading existing SerpBear/GSC tables).

### 3. UI contract

Reuse SEO screen states:

| State | When |
|-------|------|
| `needs_setup` | No `SEMRUSH_API_KEY` (or chosen provider key) |
| `sync_required` | Key present but no successful fetch for this website |
| `insufficient_data` | API returned empty or partial for query |
| `ready` | Normalized data present and fresh within TTL |

**Copy rule:** Surfaces must say **“Semrush estimates”** (or provider name), not “Ahrefs” or generic “industry standard” unless data is actually from that vendor.

### 4. Caching and cost control

- TTL per report type (e.g. keyword metrics 7–30 days depending on product decision).
- Per-workspace **monthly credit budget** optional; hard cap requests in worker.

### 5. Explicit non-goals (until separately built)

- Real-time SERP for every keyword daily at scale without budget.
- Toxicity and full backlink graph unless the chosen API returns them and you store/serve them honestly.

## Deliverables checklist (engineering)

- [ ] Env var validation and health check endpoint or admin-only status.
- [ ] Adapter + normalized schema + migration.
- [ ] One SEO screen wired (e.g. Keywords intelligence enrichment column).
- [ ] Docs update: [FEATURES.md](./FEATURES.md) + [SEO_BLUEPRINT.md](../seo/SEO_BLUEPRINT.md) capability table row.
