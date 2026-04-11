---
name: saas-app-foundation
description: Use this skill when planning or implementing the core architecture of a SaaS web application, including tenant model choices, auth boundaries, API and background-job patterns, configuration strategy, and release readiness. Do not use it for isolated bug fixes, ad campaign work, or copywriting tasks.
---

Summary: This skill turns broad SaaS build requests into a stable foundation plan with clear architectural decisions.

# Purpose

Establish a reliable SaaS application foundation that supports secure growth, faster feature delivery, and operational clarity.

## Use when

- The task is to start or restructure a SaaS app foundation.
- You need decisions on tenancy, service boundaries, data model, and deployment shape.
- Multiple future features depend on early platform choices.

## Avoid when

- The task is one small UI change.
- The request is mostly marketing execution.
- The architecture already exists and only needs local bug fixes.

## Required inputs

- Product scope and expected customer size.
- Team constraints and stack preferences.
- Security and compliance requirements.
- Reliability and scale expectations.

## Workflow

1. Define domain boundaries and tenant model.
2. Choose API style, background job strategy, and data ownership rules.
3. Set authN/authZ boundaries and secret management.
4. Define environment strategy, migrations, and release process.
5. List near-term risks and hardening priorities.

## Expected outputs

- Foundation architecture decisions.
- Initial service and data model boundaries.
- Operational checklist for shipping safely.
- Explicit assumptions and tradeoffs.

## Checks before done

- Tenant isolation model is explicit.
- Data migration and rollback path is considered.
- Observability and incident basics are planned.

## Common failure modes

- Overengineering before product fit is clear.
- Blurry ownership between services.
- Skipping operational readiness in early builds.

## Deep playbooks (Antigravity Awesome Skills)

This file is the **Rank Ray control layer**. For multi-tenant SaaS architecture depth:

| Role | Path |
|------|------|
| Multi-tenant patterns | [`../antigravity-awesome-skills/skills/saas-multi-tenant/SKILL.md`](../antigravity-awesome-skills/skills/saas-multi-tenant/SKILL.md) |

**Order:** Align tenancy and boundaries with this file and **RankRay-HQ** `docs/` first; use catalog for extra patterns.

## Token-saving guidance

- Start here for foundation decisions.
- Load `saas-app-foundation.md` for practical baseline patterns.

## References

- [`saas-app-foundation.md`](saas-app-foundation.md)
