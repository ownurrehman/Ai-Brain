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
- command center UI disjointed or metrics falsified -> `rankray-seo-ui`
