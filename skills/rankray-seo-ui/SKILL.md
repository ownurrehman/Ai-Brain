---
name: rankray-seo-ui
description: RankRay SEO UI skill - UX, Dashboard, and QA corrections.
---

# RankRay SEO UI

Use this skill when the task is about:

- SEO command center UX
- websites portfolio UX
- dashboard coherence
- website-scoped SEO page consistency
- manual QA-driven UX corrections

## Deep playbooks (Antigravity Awesome Skills)

This file is the **Rank Ray product layer** (SEO UI truthfulness, layout, QA). For React performance, component patterns, shadcn, and general SEO depth:

| Role | Path |
|------|------|
| React / performance | [`../antigravity-awesome-skills/skills/react-best-practices/SKILL.md`](../antigravity-awesome-skills/skills/react-best-practices/SKILL.md) |
| shadcn / UI system | [`../antigravity-awesome-skills/skills/shadcn/SKILL.md`](../antigravity-awesome-skills/skills/shadcn/SKILL.md) |
| SEO substance | [`../antigravity-awesome-skills/skills/seo/SKILL.md`](../antigravity-awesome-skills/skills/seo/SKILL.md) |

**Order:** **Never** violate Rank Ray rules here (no fake metrics, honest empty states); use catalog for implementation depth only.

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

## Product model

> Merged from `agents/coder/rankray-hq.md`

- Treat the website as the core unit of analysis and action.
- Build around website-scoped SEO modules, not disconnected utilities.
- Prefer command-center summaries that drive decisions over dashboards that only display counts.
- Keep intelligence modules additive so new insights do not break existing pages or routes.

## Common feature shape

Most RankRay HQ changes should be thought through as:

1. data or migration change
2. service or domain logic
3. controller or API layer
4. route and page wiring
5. UI state and presentation
6. verification of the full flow

If a change skips one of these layers, do it intentionally.

## Backend patterns

- Keep service boundaries clear around keyword tracking, clustering, SERP collection, scoring, and opportunity analysis.
- Prefer explicit response shapes for summary cards, tables, and detail views.
- Keep website identifiers and scoping rules consistent through the full request path.
- Do not leak one website's data or assumptions into another website flow.

## Frontend patterns

- Build feature-oriented screens around decisions: what to fix, what to publish, what to investigate, what to prioritize.
- Keep filters, tables, and summaries aligned to the backend contract.
- Favor stable route and component structure over fast one-off page logic.
- Avoid decorative widgets that do not change user action.

## SEO intelligence patterns

- Keyword tracking should preserve clear ownership of terms, clusters, pages, and websites.
- SERP intelligence should drive insights such as intent shifts, competitor gains, and content gaps.
- Opportunity views should connect directly to a next action, not stop at observation.
- Clustering logic should reduce cannibalization and page confusion.

## Safe change rules

- Protect route names, page contracts, and existing dashboard flows unless the task explicitly changes them.
- When adding a module, check where summaries, navigation, permissions, and empty states need updating.
- Keep migrations reversible when possible and avoid silent data assumptions.
- Verify the affected controller, service, route, and UI path together for end-to-end features.
