> **Parent Hub:** [[scripts/_archived-scripts/INDEX|📦 Legacy Systems & Scripts Archive]] · [[scripts/INDEX|🛠️ Scripts Hub]] · [[INDEX|🧠 Master Ai Brain Hub]]

Summary: Quick routing guide to pick the right skill before reading deeper files.

# Task Routing

## When the task is

Building or modifying features  
-> `skills/shipping-features/`

Debugging errors, regressions, or broken output  
-> `skills/debugging/`

Improving existing code without changing behavior  
-> `skills/refactor-safely/`

Writing articles, landing pages, or structured content  
-> `skills/content-writing/`

Working on rankings, keywords, search intent, or SERP analysis  
-> `skills/seo/`

Preparing or publishing finished content in WordPress  
-> `skills/wordpress-publisher/`

Defining SaaS positioning, ICP, messaging hierarchy, or launch sequence  
-> `skills/saas-go-to-market/`

Planning or optimizing paid campaigns for demos, trials, or leads  
-> `skills/paid-acquisition/`

Improving landing page, signup, onboarding, or pricing page conversion  
-> `skills/conversion-rate-optimization/`

Designing or hardening SaaS web app architecture foundations  
-> `skills/saas-app-foundation/`

Implementing auth, entitlements, subscription logic, or billing flows  
-> `skills/saas-auth-billing/`

Defining SaaS growth metrics, event instrumentation, or funnel dashboards  
-> `skills/saas-growth-analytics/`

Working on RankRay-HQ scoped UI, Dashboards, and SEO command centers
-> `skills/rankray-seo-ui/`

## General Rules

- Start from the current user request, not from old context.
- Prefer one primary skill and at most one or two supporting files.
- Load only the files needed to act.
- Prefer existing product patterns over inventing new UI systems.
- Add notes only if future behavior should change.

## Tie-break rule

Choose the skill that matches the main risk:

- wrong behavior or unknown cause -> `debugging`
- incomplete implementation -> `shipping-features`
- regression during cleanup -> `refactor-safely`
- weak clarity or usefulness -> `content-writing`
- weak discoverability or poor targeting -> `seo`
- formatting or publish mistakes -> `wordpress-publisher`
- weak SaaS positioning or launch clarity -> `saas-go-to-market`
- wasted paid spend or unclear paid tests -> `paid-acquisition`
- funnel drop-off or poor conversion -> `conversion-rate-optimization`
- unstable SaaS architecture decisions -> `saas-app-foundation`
- risky auth, entitlement, or billing lifecycle -> `saas-auth-billing`
- unclear growth metrics or instrumentation -> `saas-growth-analytics`
- command center UI disjointed or metrics falsified -> `rankray-seo-ui`
