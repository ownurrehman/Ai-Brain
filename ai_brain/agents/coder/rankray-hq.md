Summary: This file teaches the coder agent how to build RankRay HQ style product features without destabilizing working SEO workflows.

# RankRay HQ

## Product model

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
