---
name: saas-growth-analytics
description: Use this skill when the task requires defining or improving SaaS analytics instrumentation, funnel reporting, activation and retention metrics, attribution assumptions, or growth decision dashboards. Do not use it for raw ad execution, pure feature coding, or non-metric content writing.
---

Summary: This skill aligns product and marketing around trustworthy metrics for SaaS growth decisions.

# Purpose

Create analytics definitions and instrumentation plans that make growth decisions reliable across product and marketing teams.

## Use when

- The task mentions funnel metrics, activation, retention, churn, or attribution.
- Teams disagree on metric definitions.
- You need event tracking and dashboard structure for SaaS growth.

## Avoid when

- The task is paid campaign setup only.
- The request is a UI-only change.
- No measurable product or funnel objective is defined.

## Required inputs

- Business model and target conversion path.
- Core entities such as user, workspace, and subscription.
- Current analytics stack and reporting constraints.
- Decision cadence (daily, weekly, monthly).

## Workflow

1. Define north-star, input, and guardrail metrics.
2. Map lifecycle stages: acquisition, activation, retention, revenue.
3. Specify event schema with ownership and naming rules.
4. Define dashboard views by team and decision frequency.
5. Add data quality checks and attribution assumptions.

## Expected outputs

- Shared metric dictionary.
- Event tracking plan.
- Funnel and retention reporting structure.
- Data quality and governance checklist.

## Checks before done

- Metrics have unambiguous formulas.
- Event ownership is assigned.
- Dashboard views map to real decisions, not vanity reporting.

## Common failure modes

- Multiple definitions for the same metric.
- Tracking too many events with no decision use.
- Treating attribution output as absolute truth.

## Token-saving guidance

- Start here for metric architecture.
- Load `saas-growth-analytics.md` for concrete event and dashboard patterns.

## References

- [`saas-growth-analytics.md`](saas-growth-analytics.md)
