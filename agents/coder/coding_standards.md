Summary: These are the engineering standards for shipping SEO product features safely and readably.

# Coding Standards

## Core standards

- Prefer readable code over clever code.
- Make the smallest viable change that solves the real task.
- Preserve backward compatibility where reasonable.
- Keep behavior changes and refactors separate when possible.
- Avoid magic behavior, hidden side effects, and silent fallbacks.

## Product-facing standards

- Keep route, module, and service naming consistent.
- Keep response shapes stable and easy for clients to consume.
- Keep UI and backend patterns aligned so features stay predictable end to end.
- Prefer decision-supporting UX over decorative complexity.

## Delivery standards

- Add tests when behavior changes or regressions are likely.
- Verify builds, tests, or affected commands before claiming done.
- Avoid new dependencies unless they remove a repeated real problem.
