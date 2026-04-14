---
name: shipping-features
description: Use this skill when the task is to implement, finish, or safely deliver a scoped feature in code. Do not use it for pure bug triage, broad architecture discovery, or behavior-preserving refactors. This skill turns a defined requirement into a small-batch implementation with verification and clear completion criteria.
---

Summary: This skill is the default path for scoped implementation work that needs to reach a shipped state.

# Purpose

Move a defined feature from request to verified completion with minimal risk and minimal drift.

## Use when

- The user wants a feature built, completed, or wired up.
- The scope is primarily implementation rather than diagnosis.
- The work should end with code, checks, and a clear outcome.

## Avoid when

- The dominant task is reproducing a bug.
- The goal is structural cleanup with unchanged behavior.
- The request is still too vague to implement safely.

## Required inputs

- Feature goal.
- Scope boundaries.
- Relevant code area or surface.
- Acceptance cues if known.

## Workflow

1. Inspect the local code path before changing anything.
2. Define the smallest complete slice that satisfies the request.
3. Implement in a way that fits existing patterns.
4. Verify with targeted checks.
5. Report outcome, constraints, and any residual risk.

## Expected outputs

- Working code change.
- Focused verification.
- Short explanation of what changed and what remains.

## Checks before done

- The change matches the stated goal.
- A relevant verification step ran, or the missing check is called out.
- Scope did not quietly expand.

## Common failure modes

- Solving adjacent problems instead of the requested one.
- Changing too many files without need.
- Skipping verification because the code looks correct.

## Deep playbooks (Antigravity Awesome Skills)

This file is the **Rank Ray control layer**. For plan-driven and multi-step implementation depth:

| Role | Path |
|------|------|
| Execute planned tasks | [`../antigravity-awesome-skills/skills/subagent-driven-development/SKILL.md`](../antigravity-awesome-skills/skills/subagent-driven-development/SKILL.md) |
| Track / conductor style | [`../antigravity-awesome-skills/skills/conductor-implement/SKILL.md`](../antigravity-awesome-skills/skills/conductor-implement/SKILL.md) |

**Order:** Keep scope and verification aligned with this file and the target repo; use catalog for orchestration patterns.

## Token-saving guidance

- Use this as the default execution path for implementation tasks.
- Load `shipping-features.md` only if you need delivery heuristics or completion checks.

## References

- [`shipping-features.md`](shipping-features.md)
