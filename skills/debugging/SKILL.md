---
name: debugging
description: Use this skill when something is broken, regressed, inconsistent, or failing and the main job is to find the cause and fix it. Do not use it for greenfield feature work, pure refactoring, or non-defect planning. This skill turns symptoms into a verified root cause, a focused fix, and evidence that the issue is resolved.
---

Summary: This skill provides a disciplined path for bug reproduction, isolation, and repair.

# Purpose

Diagnose problems with evidence, isolate the root cause, and apply the smallest reliable fix.

## Use when

- A bug, regression, failing test, or broken workflow is reported.
- The cause is unclear.
- Confidence depends on reproduction and evidence.

## Avoid when

- The task is new feature work.
- The goal is structural cleanup without a defect.
- The issue is really a product decision, not a malfunction.

## Required inputs

- Symptom or failing behavior.
- Affected area if known.
- Reproduction clues, logs, or error text if available.

## Workflow

1. Reproduce or narrow the failure.
2. Identify the smallest plausible cause set.
3. Confirm the actual root cause with evidence.
4. Apply the narrowest fix that resolves the issue.
5. Re-run the relevant check or reproduction path.

## Expected outputs

- Root cause statement.
- Code or config fix.
- Verification evidence.
- Remaining uncertainty, if any.

## Checks before done

- Cause and symptom are linked clearly.
- The fix addresses the cause, not only the symptom.
- A reproduction path or test now passes, or the gap is stated.

## Common failure modes

- Guessing before reproducing.
- Fixing the symptom while the cause remains.
- Making a wide cleanup during a bug fix.

## Deep playbooks (Antigravity Awesome Skills)

This file is the **Rank Ray control layer**. For longer debugging procedures, load:

| Role | Path |
|------|------|
| Systematic bug hunting | [`../antigravity-awesome-skills/skills/bug-hunter/SKILL.md`](../antigravity-awesome-skills/skills/bug-hunter/SKILL.md) |
| Debugger patterns | [`../antigravity-awesome-skills/skills/debugger/SKILL.md`](../antigravity-awesome-skills/skills/debugger/SKILL.md) |

**Order:** Follow reproduction and evidence rules here first; add catalog depth as needed.

## Token-saving guidance

- Start here for the process.
- Load `debugging.md` only if you need tighter heuristics for isolation and proof.

## References

- [`debugging.md`](debugging.md)
