---
name: conversion-rate-optimization
description: Use this skill when the task is to improve conversion performance on SaaS landing pages, signup flows, demo forms, pricing pages, or onboarding paths through hypothesis-led UX and copy changes. Do not use it for broad brand strategy, pure backend feature work, or ad account management.
---

Summary: This skill converts weak funnel performance into prioritized CRO experiments with clear expected impact.

# Purpose

Improve visitor-to-lead and visitor-to-user conversion by reducing friction, clarifying value, and validating hypotheses.

## Use when

- The task mentions low conversion rate, high bounce, drop-off, or form abandonment.
- You need to prioritize page or flow experiments.
- Marketing and product need one shared conversion improvement backlog.

## Avoid when

- The task is only traffic generation.
- The issue is mainly tracking instrumentation setup.
- The request is pure visual redesign with no funnel objective.

## Required inputs

- Target page or funnel step.
- Current baseline metric and timeframe.
- Audience segment and traffic source.
- Constraints on engineering effort or release pace.

## Workflow

1. Identify the primary conversion event and baseline.
2. Diagnose friction by intent mismatch, trust gaps, or UX complexity.
3. Create hypothesis backlog ranked by impact, confidence, and effort.
4. Implement one change group at a time.
5. Compare results against baseline and decide keep, iterate, or revert.

## Expected outputs

- CRO hypothesis list with priorities.
- Experiment definitions and success criteria.
- Recommended implementation order.
- Risks and instrumentation requirements.

## Checks before done

- Each experiment has one measurable primary metric.
- Hypotheses tie to observed friction, not opinion.
- Sample window is adequate before conclusions.

## Common failure modes

- Running too many overlapping changes at once.
- Declaring winners too early.
- Treating conversion gains without considering lead quality.

## Deep playbooks (Antigravity Awesome Skills)

This file is the **Rank Ray control layer**. For landing-page structure and funnel analytics depth:

| Role | Path |
|------|------|
| SEO/AEO landing pages | [`../antigravity-awesome-skills/skills/seo-aeo-landing-page-writer/SKILL.md`](../antigravity-awesome-skills/skills/seo-aeo-landing-page-writer/SKILL.md) |
| Product / funnel analytics | [`../antigravity-awesome-skills/skills/analytics-product/SKILL.md`](../antigravity-awesome-skills/skills/analytics-product/SKILL.md) |

**Order:** Hypothesis backlog and experiment discipline per this file; use catalog for page templates and metrics detail.

## Token-saving guidance

- Start here for workflow.
- Load `cro-checklist.md` when preparing or evaluating experiments.

## References

- [`cro-checklist.md`](cro-checklist.md)
