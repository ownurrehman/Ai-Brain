---
description: RankRay SEO UI skill - UX, Dashboard, and QA corrections.
---

# RankRay SEO UI

Use this skill when the task is about:

- SEO command center UX
- websites portfolio UX
- dashboard coherence
- website-scoped SEO page consistency
- manual QA-driven UX corrections

## Workflow

1. Improve framing, hierarchy, and action clarity without inventing fake SEO data.
2. Keep website context visible and consistent across SEO surfaces.

## Patterns

- No fake SEO metrics.
- Cards must show state first.
- Website-scoped context must stay visible.
- The command center should act as the launch hub for SEO work.
- Empty states must be honest and useful.
- Avoid the floating middle-menu feeling; anchor sections with stronger framing.
- Reuse existing real subsystem summaries instead of fabricating dashboard aggregates.

## Verification

Run:

- `cd rankray-hq-backend && npm exec prisma migrate status`
- `cd rankray-hq-backend && npm exec prisma generate`
- `npm run build --prefix rankray-hq-backend`
- `cd rankray-hq-backend && npm test --silent`
- `npm run build --prefix rankray-hq-frontend`
- `cd rankray-hq-frontend && npx playwright test e2e/seo-backlinks.spec.ts --workers=1`

Manual focus:

- selected website stays visible
- command center feels anchored, not floating
- cards show useful state with one obvious action
- no fake metrics or dead empty shells
