---
name: refactor-safely
description: Use this skill when the goal is to improve structure, readability, modularity, or maintainability without materially changing behavior. Do not use it for bug-first work, feature expansion, or speculative rewrites. This skill turns a messy but working code path into a cleaner one through small, reversible, behavior-preserving steps.
---

Summary: This skill guides low-risk structural changes that should preserve existing behavior.

# Purpose

Improve code structure while protecting behavior, readability, and confidence.

## Use when

- The task is cleanup, extraction, simplification, or organization.
- The existing behavior should stay the same.
- The main risk is regression from broad edits.

## Avoid when

- The user is asking for new behavior first.
- A defect must be diagnosed before cleanup.
- The only reason for change is stylistic preference with no benefit.

## Required inputs

- Code area to improve.
- Known pain points.
- Expected invariant behavior.

## Workflow

1. Define what must not change.
2. Identify the smallest useful refactor slice.
3. Restructure in small steps that are easy to review.
4. Keep naming, boundaries, and tests coherent.
5. Verify behavior still holds.

## Expected outputs

- Cleaner code path.
- No intended behavior change.
- Verification notes and any residual risk.

## Checks before done

- The refactor goal is specific.
- Behavior-preserving assumptions are explicit.
- Verification covers the touched path.

## Common failure modes

- Mixing refactor and feature work.
- Renaming or moving too much at once.
- Ending with a prettier structure but weaker clarity.

## Token-saving guidance

- Use this manifest for guardrails.
- Load `refactor-safely.md` only when sequencing or safety tradeoffs need more detail.

## References

- [`refactor-safely.md`](refactor-safely.md)
