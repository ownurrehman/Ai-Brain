---
name: saas-auth-billing
description: Use this skill when implementing or improving SaaS authentication, authorization, subscription plans, billing states, access enforcement, and entitlement logic. Do not use it for generic UI styling, unrelated SEO tasks, or broad architecture work without auth or billing scope.
---

Summary: This skill handles one of the highest-risk SaaS surfaces: identity, subscriptions, and feature access control.

# Purpose

Build or harden SaaS auth and billing flows so access rules, payments, and account states stay consistent.

## Use when

- The task includes login, signup, team roles, plans, or subscription lifecycle.
- Entitlements must control what features users can access.
- Payment provider events must update internal account state.

## Avoid when

- The task is unrelated feature UI work.
- The request is purely marketing.
- Billing is out of scope and only content changes are needed.

## Required inputs

- Plan model and entitlement rules.
- Auth provider and payment provider assumptions.
- Required account lifecycle states.
- Failure handling expectations.

## Workflow

1. Define account, user, workspace, and role boundaries.
2. Specify entitlement model by plan and add-on.
3. Map billing lifecycle events to internal state transitions.
4. Enforce access checks in API and UI layers.
5. Add idempotent webhook handling and reconciliation rules.

## Expected outputs

- Auth and billing state model.
- Access-control implementation guidance.
- Webhook and reconciliation checklist.
- Known risk areas and test priorities.

## Checks before done

- Access checks fail closed by default.
- Billing events are idempotent and auditable.
- Downgrade and cancellation behavior is explicit.

## Common failure modes

- Trusting client-side entitlement checks.
- Race conditions around webhook delivery.
- Orphaned access after plan changes.

## Deep playbooks (Antigravity Awesome Skills)

This file is the **Rank Ray control layer**. For auth, subscriptions, and payments depth:

| Role | Path |
|------|------|
| Clerk patterns | [`../antigravity-awesome-skills/skills/clerk-auth/SKILL.md`](../antigravity-awesome-skills/skills/clerk-auth/SKILL.md) |
| Monetization / SaaS revenue | [`../antigravity-awesome-skills/skills/monetization/SKILL.md`](../antigravity-awesome-skills/skills/monetization/SKILL.md) |
| Payment integration | [`../antigravity-awesome-skills/skills/payment-integration/SKILL.md`](../antigravity-awesome-skills/skills/payment-integration/SKILL.md) |

**Order:** Entitlements and webhooks must match this file and **RankRay-HQ** reality; use catalog for provider-specific detail.

## Token-saving guidance

- Start here for scope and risks.
- Load `saas-auth-billing.md` for lifecycle and testing checklist.

## References

- [`saas-auth-billing.md`](saas-auth-billing.md)
