---
name: saas-development
description: Use for Rank Ray SaaS product builds—tenancy, auth, billing, APIs, jobs, analytics instrumentation—when the deliverable is a subscription or multi-tenant web product, not a one-off marketing site.
---

# Rank Ray — SaaS app development

**Agency:** [Rank Ray](https://www.rankray.com) — SaaS-style web products for clients (and **RankRay-HQ** as the internal agency OS reference implementation).

## Use when

- Building or extending **multi-tenant** or **team/workspace** models, **subscriptions**, **entitlements**, **webhooks**, **background jobs**.
- Stack is commonly **React + Vite** / **Next** frontends with **Node/Nest** or similar backends—follow the **active repo’s `AGENTS.md`**.
- You need **onboarding flows**, **plan upgrades**, or **usage limits**.

## Avoid when

- **Marketing website only** → **`../web-development/SKILL.md`**.
- **Only** paid ads or SEO deliverables → **`../digital-marketing/SKILL.md`** / **`../seo-services/SKILL.md`**.
- **Automation** without a product surface → **`../ai-automation/SKILL.md`**.

## Rank Ray delivery norms

- **AuthZ** must be enforced server-side; never trust the UI alone for entitlements.
- **Webhook idempotency** and **replay** handling for billing providers.
- **Observability:** structured logs for auth/billing paths; avoid PII in logs.
- Prefer **RankRay-HQ** patterns when the client stack matches (see **`RankRay-HQ/docs/`**).

## Workflow (summary)

1. Read **`RankRay-HQ/AGENTS.md`** (or client repo equivalent) + **`docs/README.md`**.
2. Model tenants, users, roles, plans, and feature flags explicitly.
3. Implement API + UI slices with contract tests or e2e where the repo supports it.
4. Document env vars and operational runbooks for the client handoff.

## Related first-party skills

| Skill | When |
|-------|------|
| [`../saas-app-foundation/SKILL.md`](../saas-app-foundation/SKILL.md) | Architecture / tenancy |
| [`../saas-auth-billing/SKILL.md`](../saas-auth-billing/SKILL.md) | Auth + Stripe lifecycle |
| [`../saas-growth-analytics/SKILL.md`](../saas-growth-analytics/SKILL.md) | Events / funnels |
| [`../saas-go-to-market/SKILL.md`](../saas-go-to-market/SKILL.md) | Positioning / launch narrative |
| [`../web-development/SKILL.md`](../web-development/SKILL.md) | Frontend implementation depth |

## Deep playbooks (Antigravity Awesome Skills)

| Role | Path |
|------|------|
| Multi-tenant SaaS | [`../antigravity-awesome-skills/skills/saas-multi-tenant/SKILL.md`](../antigravity-awesome-skills/skills/saas-multi-tenant/SKILL.md) |
| Clerk auth | [`../antigravity-awesome-skills/skills/clerk-auth/SKILL.md`](../antigravity-awesome-skills/skills/clerk-auth/SKILL.md) |
| Monetization / Stripe mindset | [`../antigravity-awesome-skills/skills/monetization/SKILL.md`](../antigravity-awesome-skills/skills/monetization/SKILL.md) |
| Payment integration | [`../antigravity-awesome-skills/skills/payment-integration/SKILL.md`](../antigravity-awesome-skills/skills/payment-integration/SKILL.md) |
| Product analytics | [`../antigravity-awesome-skills/skills/analytics-product/SKILL.md`](../antigravity-awesome-skills/skills/analytics-product/SKILL.md) |
| Next.js / React (if applicable) | [`../antigravity-awesome-skills/skills/nextjs-app-router-patterns/SKILL.md`](../antigravity-awesome-skills/skills/nextjs-app-router-patterns/SKILL.md) |

**Order:** Repo + Rank Ray SaaS skills first; Antigravity for library-scale patterns.
