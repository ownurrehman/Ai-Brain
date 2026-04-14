Summary: This framework helps choose the right path before loading more context or doing work.

# Decision Framework

## Choose the primary mode

- Use `skills/debugging/` when something should work but does not.
- Use `skills/shipping-features/` when behavior must be added or completed.
- Use `skills/refactor-safely/` when structure must improve without changing behavior.
- Use `skills/content-writing/` when the output is content for humans to read.
- Use `skills/seo/` when search demand, intent, or ranking opportunity matters.
- Use `skills/wordpress-publisher/` when content must be prepared or published in WordPress.
- Use `skills/saas-go-to-market/` when SaaS positioning, ICP, and launch strategy are the main problem.
- Use `skills/paid-acquisition/` when paid channels, budgets, and campaign optimization drive the outcome.
- Use `skills/conversion-rate-optimization/` when conversion lift is needed on landing pages or signup flows.
- Use `skills/saas-app-foundation/` when core SaaS web app architecture decisions are needed.
- Use `skills/saas-auth-billing/` when identity, plans, access control, or billing lifecycle behavior is in scope.
- Use `skills/saas-growth-analytics/` when event schemas, funnel metrics, or growth dashboards are the main deliverable.

## Break ties by outcome

- If the main risk is wrong diagnosis, debug first.
- If the main risk is incomplete delivery, use shipping.
- If the main risk is regression during cleanup, use refactor-safely.
- If the main risk is weak usefulness or clarity, use content-writing.
- If the main risk is poor discoverability, use SEO.
- If the main risk is formatting or publish errors, use WordPress publisher.
- If the main risk is weak positioning or segment mismatch, use SaaS go-to-market.
- If the main risk is wasting paid spend, use paid-acquisition.
- If the main risk is funnel leakage, use conversion-rate-optimization.
- If the main risk is unstable foundations, use SaaS app foundation.
- If the main risk is broken access or subscription behavior, use SaaS auth billing.
- If the main risk is metric confusion or bad instrumentation, use SaaS growth analytics.

## Prefer one lead path

Pick one primary skill. Pull in a second file only when the work genuinely crosses boundaries.
